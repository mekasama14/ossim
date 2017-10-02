<?php
/**
 * snap_load.php
 *
 * File snap_load.php is used to:
 * - Response ajax call from index.php
 * - Fill the data into asset details Snapshot section
 *
 * License:
 *
 * Copyright (c) 2003-2006 ossim.net
 * Copyright (c) 2007-2013 AlienVault
 * All rights reserved.
 *
 * This package is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 dated June, 1991.
 * You may not use, modify or distribute this program under any other version
 * of the GNU General Public License.
 *
 * This package is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this package; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
 * MA  02110-1301  USA
 *
 *
 * On Debian GNU/Linux systems, the complete text of the GNU General
 * Public License can be found in `/usr/share/common-licenses/GPL-2'.
 *
 * Otherwise you can read it here: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package    ossim-framework\Assets
 * @autor      AlienVault INC
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt
 * @copyright  2003-2006 ossim.net
 * @copyright  2007-2013 AlienVault
 * @link       https://www.alienvault.com/
 */


require_once 'av_init.php';

//Checking active session
Session::useractive();

//Checking permissions
if (!Session::am_i_admin())
{
    echo _('You do not have permissions to see this section');

    die();
}

/************************************************************************************************/
/************************************************************************************************/
/***  This file is includen in step_loader.php hence the wizard object is defined in $wizard  ***/
/***                         database connection is stored in $conn                           ***/
/************************************************************************************************/
/************************************************************************************************/

if(!$wizard instanceof Welcome_wizard)
{
    throw new Exception("There was an error, the Welcome_wizard object doesn't exist");
}

$interfaces = array();

try
{
    $interfaces = Welcome_wizard::get_interfaces();
}
catch(Exception $e)
{
    $config_nt = array(
        'content' => $e->getMessage(),
        'options' => array (
            'type'          => 'nf_error',
            'cancel_button' => true
        ),
        'style'   => 'margin:10px auto;width:50%;text-align:center;padding:0 10px;z-index:999'
    );

    $nt = new Notification('nt_notif', $config_nt);
    $nt->show();
}

$v_short    = (Session::is_pro() ? "USM" : "OSSIM");

$text_descr = _("
اینترفیس شبکه در AlienVault OSSIM  را برای اجرای نظارت بر شبکه یا به عنوان جمع آوری و اسکن لاگ ها می توان پیکربندی کرد  پس از اینکه اینترفیس ها را پیکربندی کردید، باید اطمینان حاصل کنید که شبکه به طور مناسب برای هر رابط کاربری پیکربندی شده است، به طوری که  AlienVault OSSIM یا داده ها را به صورت غیرمستقیم دریافت می کند یا قادر به دسترسی به شبکه مورد نظر است .");

$text_descr = sprintf($text_descr, $v_short, $v_short);

?>

<script type='text/javascript'>

    var __nic ,__n_role ,__n_ip, __n_mask = null;
    var __nic_state = false;

    function load_js_step()
    {
        load_handler_step_interfaces();

        <?php
        if (count($interfaces) > 0)
        {
        ?>
        get_interfaces_activity();

        <?php
        }
        ?>
    }

</script>


<div id='step_interfaces' class='step_container' dir="rtl">

    <div class='wizard_title' style="font-family: 'B Titr'; font-weight: bold">
        <?php echo _("پیکربندی اینترفیس شبکه") ?>
    </div>

    <div class='wizard_subtitle' style="font-family: 'B Mehr'">
        <?php echo $text_descr ?>
    </div>


    <table class='wizard_table table_data'  align="left">
        <tr>
            <th class='left'><?php echo _('NIC') ?></th>
            <th><?php echo _('Purpose') ?></th>
            <th><?php echo _('IP Address') ?></th>
            <th><?php echo _('Status') ?></th>
        </tr>

        <?php
        foreach ($interfaces as $id => $interface)
        {
            $ip     = ($interface['ip'] == '') ? _('N/A') : $interface['ip'];
            $role   = $interface['role'];

            //Classes
            $pencil = ''; //Show the pencil
            $led_y  = 'hide'; //Hide Led
            $led_n  = ''; //Show a dash instead of a Led

            if ($role != 'log_management')
            {
                $interface['ip']      = '';
                $interface['netmask'] = '';
                $pencil = 'hide';  //Hide pencil if it is not Log collection
            }

            if ($role == 'monitoring')
            {
                $led_y  = ''; //Only show led when it is promisc interface
                $led_n  = 'hide';
            }
            ?>

            <tr id='nic_<?php echo $id ?>' class='nic_item' data-nic='<?php echo $id ?>'>

                <td class='left'>
                    <?php echo $id ?>
                </td>

                <td>

                    <?php
                    if ($role == 'admin')
                    {
                        ?>
                        <select class="select_purpose" disabled="disabled">
                            <option value=""><?php echo _('مدیریت') ?></option>
                        </select>

                        <?php
                    }
                    else
                    {
                        ?>
                        <select class="nic_roles select_purpose">
                            <option <?php echo ($role == 'disabled') ? 'selected' : '' ?> value="disabled">
                                <?php echo _('بلا استفاده') ?>
                            </option>
                            <option <?php echo ($role == 'monitoring') ? 'selected' : '' ?> value="monitoring">
                                <?php echo _('نظارت بر شبکه') ?>
                            </option>
                            <option <?php echo ($role == 'log_management') ? 'selected' : '' ?> value="log_management">
                                <?php echo _('جمع آوری و اسکن لاگ') ?>
                            </option>
                        </select>

                    <?php } ?>

                </td>

                <td style='position:relative;'>

                    <span id='nic_ip' data-ip='<?php echo $interface['ip'] ?>' data-mask='<?php echo $interface['netmask'] ?>'>
                        <?php echo $ip ?>
                    </span>

                    <img class="edit_ip_nic <?php echo $pencil ?>" src="/ossim/dashboard/pixmaps/edit.png" />

                </td>

                <td>
                    <div id='indicator_yes' class="<?php echo $led_y ?>">
                        <span class='led_container'>
                            <span class='wizard_led led_gray'>&nbsp;</span>
                            <img  class='wizard_led_loader' src='/ossim/pixmaps/loader.gif'/>
                        </span>
                    </div>
                    <div id='indicator_no' class="<?php echo $led_n ?>">
                        -
                    </div>
                </td>

            </tr>

            <?php
        }
        ?>

    </table ">

    <div id='nic_legend'  dir="rtl">

        <div id='legend_title'><?php echo _('اطلاعات:') ?></div>
        <ul>

            <li>
                <strong><?php echo _('مدیریت:') ?> </strong>
                <?php
                $text = _('
رابط مدیریت در کنسول پیکربندی شده و به شما اجازه می دهد تا به رابط کاربری وب متصل شوید. این رابط را نمی توان از رابط کاربری وب تغییر داد.');

                $text = sprintf($text, $v_short);

                echo $text;

                ?>
            </li>

            <li>
                <strong><?php echo _('نظارت بر شبکه:') ?> </strong>
                <?php
                $msg = _('
به طور تلویحی به ترافیک شبکه گوش دهید رابط به حالت پیش فرض تنظیم می شود.در صورت نیاز به انفصال یا اتصال به شبکه  به دستورالعمل هایی درباره چگونگی راه اندازی یاتصال یا انفصال شبکه مراجعه کنید.');

                $s_1 = "<a href='https://www.alienvault.com/help/product/gsw' class='av_l_main' target='_blank'>";
                $s_2 = "</a>";

                echo sprintf($msg, $s_1, $s_2);
                ?>

            </li>

            <li>
                <strong><?php echo _('جمع آوری و اسکن لاگ:') ?> </strong>
                <?php echo _('
جمع آوری یا دریافت لاگ های مربوط از دارایی های خود، اسکن دارایی ها، یا اعزام نماینده HIDS. که لازمه ی آن  دسترسی قابل رویت به شبکه شما است') ?>
            </li>

            <li>
                <strong><?php echo _('بلا استفاده:') ?> </strong>
                <?php echo _('
اگر شما نمی خواهید از یکی از اینترفیس های شبکه استفاده کنید، از این گزینه استفاده کنید.
') ?>
            </li>

        </ul>

    </div>


    <div class='clear_layer'></div>

</div>

<a id='dialog_link' href='#ip_dialog'></a>
<div id='ip_dialog_wrapper'>
    <div id='ip_dialog'>

        <div id='dialog_title' class='wizard_title'>
            <?php echo _('IP Address & Netmask') ?>
        </div>

        <div id='dialog_message'></div>
        <input id='insert_ip' type='text' value='' placeholder="<?php echo _('IP Address') ?>">
        <br/>
        <input id='insert_mask' type='text' value='' placeholder="<?php echo _('Netmask') ?>">

        <div id='opts'>
            <input id='cancel' class='av_b_secondary' type='button' value="<?php echo _('Cancel') ?>">
            <input id='ok'type='button' value="<?php echo _('Ok') ?>" >
        </div>

    </div>
</div>


<div class='wizard_button_list'>

    <button id='next_step' class="fright"><?php echo _('بعدی') ?></button>

</div>
