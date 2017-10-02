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


$tables             = ', host_properties hp';
$filters_w['where'] = ' hp.host_id=host.id AND hp.property_ref=3 AND (hp.value LIKE "windows%" OR hp.value LIKE "microsoft%")';
$filters_w['limit'] = '1';

$filter_l           = $filters_w;
$filters_l['where'] = ' hp.host_id=host.id AND hp.property_ref=3 AND (hp.value LIKE "%linux%" OR hp.value LIKE "%alienvault%")';


try
{
    list($asset_w, $total_w) = Asset_host::get_list($conn, $tables, $filters_w, TRUE);

    list($asset_l, $total_l) = Asset_host::get_list($conn, $tables, $filters_l, TRUE);

}
catch(Exception $e)
{
    die($e->getMessage());
}

$step         = intval($wizard->get_step_data('deploy_step'));

$deploy_tabs  = array();

if ($total_w > 0)
{
    $deploy_tabs['windows'] = _('ویندوز')  . ' (' . $total_w . ')';
}

if ($total_l > 0)
{
    $deploy_tabs['linux'] = _('یونیکس/ لینوکس')  . ' (' . $total_l . ')';
}


$flag_empty   = ($total_l + $total_w > 0) ? FALSE : TRUE;

$button_class = 'disabled';

$first_tab    = key($deploy_tabs);

?>


<script type='text/javascript'>

    var __os   = '<?php echo $first_tab ?>';
    var __tree = null;
    var __host_selected = {};


    function load_js_step()
    {
        load_handler_step_deploy();

        <?php
        if ($step > 0)
        {
            echo "launch_deploy_window();";
        }
        ?>

    }

</script>


<div id='step_hids' class='step_container' dir="rtl">

    <div class='wizard_title' style="font-family: 'B Titr'; font-weight: bold">
        <?php echo _('اضافه کردن HIDS به سرور') ?>
    </div>

    <div class='wizard_subtitle'>
        <?php echo _('
برای این ابزار ها توصیه می کنیم که HIDS را به منظور انجام نظارت بر یکپارچگی فایل، تشخیص روت کیت و جمع آوری گزارش های رویداد بکار بگیرید. برای دستگاه های ویندوز، عامل HIDS به صورت محلی نصب خواهد شد، برای محیط های یونیکس / لینوکس نظارت HIDS از راه دور پیکربندی خواهد شد.
') ?>
    </div>

    <?php
    if (!$flag_empty)
    {
        ?>

        <div id='tab-list'>
            <ul>
                <?php
                foreach ($deploy_tabs as $type => $tab)
                {
                    echo "
                        <li>
                            <a data-os='$type' href='#tab-container'>
                                $tab
                            </a>
                        </li>
                ";

                }
                ?>
            </ul>

            <div id='tab-container'>

                <div id='desc_hids_windows' class='wizard_subtitle'>
                    <?php echo _('
برای نصب عامل HIDS، حساب کاربری دامنه را وارد کنید. نام کاربری و گذرواژه ای که ارائه می کنید به طور دائمی ذخیره نخواهد شد و برای اعمال یک عامل به دارایی های انتخاب شده مورد استفاده قرار می گیرد.
') ?>
                </div>

                <div id='desc_hids_linux' class='wizard_subtitle'>
                    <?php echo _('
میزبان یونیکس از راه دور نظارت خواهد شد. نام کاربری و رمز عبور ذخیره و استفاده می شود تا به صورت دوره ای به دارایی های انتخاب شده دسترسی پیدا کند.') ?>
                </div>

                <br/>

                <div class='fleft half_width' dir="rtl">

                    <div class='form_elem'>
                        <label for="username" id='l_username'><?php echo _('نام کاربری')?></label><br/>
                        <input type="text" id="username" name="username" value=""/>
                    </div>

                    <div class='form_elem'>
                        <label for="password" id='l_password'><?php echo _('رمز عبور')?></label><br/>
                        <input type="password" id="password" name="password" autocomplete="off"/>
                    </div>

                    <div class='form_elem' id='form_domain'> <!-- #form_domain Visible or not -->
                        <?php echo _('دامنه').' ('._('اختیاری').')' ?><br/>
                        <input type="text" id="domain" name="domain" value=""/>
                    </div>

                    <button id='deploy' <?php echo $button_class ?> class='av_b_secondary'><?php echo _('توسعه') ?></button>

                </div>

                <div class='fright half_width'>

                    <div class='form_elem'>
                        <?php echo _('توسعه به host های زیر') ?>:
                    </div>

                    <div class='form_elem'>

                        <div id='tree'></div>

                    </div>
                </div>

            </div>

        </div>

        <?php
    }
    else
    {
        ?>
        <div id='empty_devices'>
            <?php echo _('There are no servers on your network. Return to the asset discovery page by clicking back to scan your network or by adding servers manually.') ?>
        </div>
        <?php
    }
    ?>

    <div style='clear:both'></div>

</div>


<div class='wizard_button_list'>

    <a href='javascript:;' id='prev_step' class='av_l_main'><?php echo _('قبلی') ?></a>

    <button id='next_step' class="fright"><?php echo _('بعدی') ?></button>

</div>

<a href='/ossim/wizard/extra/deploy.php' id='deploy_url'></a>

