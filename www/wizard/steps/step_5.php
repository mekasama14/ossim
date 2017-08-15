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

$config  = new Config();
$otx_key = $config->get_conf("open_threat_exchange_key");

$v_tag   = (Session::is_pro() ? "USM" : "OSSIM");
?>

<script type='text/javascript'>

    function load_js_step()
    {

        load_handler_step_otx();

    }

</script>

<div id='step_6' class='step_container'>


    <div id='w_otx_step_1'>

        <div class='wizard_title' style='padding-left:2px;'  >
            <?php echo _('به تبادل تهدید -هوش تهدید که توسط جامعه طراحی شده بپیوندید') ?>
        </div>

        <div id='left_column' dir="rtl" >

            <span class='bold'>
                <?php echo _('OTX چیست؟') ?>
            </span>

            <p  style="direction:rtl;text-align:right;">
                <?php echo _("
AlienVault Open Threat Exchange (OTX ™) نخستین کشف امنیت اطلاعات در جهان است. OTX شما را قادر می سازد امنیت دفاع شبکه خود را با امنیت اطلاعات محرمانه، دقیق و مربوطه تهیه کنید. با استفاده از AlienVault OTX، می توانید سریعا به تغییرات چشم انداز تهدید با دریافت اطلاعات زمان واقعی تهدید دقیق از جامعه پاسخ دهید.") ?>
            </p>


            <span class='bold'>
                <?php echo _('چرا بهتر است به OTX بپیوندیم؟') ?>
            </span>
            <p  style="direction:rtl;text-align:right;">
                <?php echo _('ابزارهای خودکار USM ، OTX و OSSIM شما را با تهدید عملیاتی هوش که توسط "پالس ها" در جامعه تولید می شوند ، توسعه می دهند.پالس ها یک گروهی از شاخص سازش (locs) می باشند که به عنوان یک عامل فعال شناسایی شده اند. این پالسها اطلاعات خاص و قابل اجرایی را ارائه می دهند که به شما کمک می کند تا آخرین تهدیدات را در محیط شناسایی کنید.') ?>
            </p>


            <span class='bold'>
                <?php echo _('OTX چگونه کار می کند؟') ?>
            </span>
            <p  style="direction:rtl;text-align:right;">
                <?php
                $_txt = _('فعال کردن OTX در نصب شما، پالس های OTX  را که حاوی آخرین اطلاعات تهدید، از جمله شاخص های سازش (IoC) در نصب شما است، را به شما متصل می کند. هنگامی که IOCs از پالس با دارایی های محیط خود ارتباط برقرار می کنند، یک رویداد امنیتی ایجاد می شود. این رویدادها در همبستگی مورد استفاده قرار می گیرند تا بینش عمیق تر نسبت به فعالیت هایی که در شبکه شما اتفاق می افتد، به شما ارائه دهد. علاوه بر این، با ارسال اطلاعات تهدید ناشناس به OTX می توانید به جامعه کمک کنید. ');

                echo sprintf($_txt, $v_tag);

                ?>
                &nbsp;<a id='otx_data_link' href='javascript:;'><?php echo _('
ببینید چه اطلاعاتی به OTX ارسال می شود') ?></a>
            </p>


            <p id='otx_enable_p'  style="direction:rtl;text-align:right;">
                <?php echo _('برای دریافت تهدید هوش طراحی شده توسط جامعه از OTX به نصب خود، برای یک حساب  OTXثبت نام کنید. هنگامی که آدرس ایمیل شما تأیید شد، کلید OTX را برای اتصال دریافت خواهید کرد.') ?>
            </p>

            <button id='b_get_otx_token' class='av_b_secondary'><?php echo _('ثبت نام کنید') ?></button>

            <p  style="direction:rtl;text-align:right;">
                <?php echo _("کلید OTX زیر را وارد کنید تا به حسابتان وارد شوید") ?>
            </p>

            <input type='text' id='w_otx_token' placeholder="<?php echo _("کلید OTX را وارد کنید") ?>" value="<?php echo $otx_key ?>">

        </div>

        <div id='right_column'>
            <img id='otx_register_img' src='img/otx_register.png' />
        </div>


        <div class='clear_layer'> </div>


    </div>

    <div id='w_otx_step_2'>

        <div id='w_otx_2_skip'>

            <div class='wizard_title'>
                <?php echo _("Don't Worry, You Can Join the AlienVault Open Threat Exchange at Any Time!") ?>
            </div>

            <div class='wizard_subtitle'>
                <?php
                $_txt = _("You've chosen not to join the Open Threat Exchange at this time. You can join the AlienVault OTX community through your AlienVault %s web interface at any time.");

                echo sprintf($_txt, $v_tag);
                ?>
            </div>

            <br/><br/>

        </div>

        <div id='w_otx_2_register'>

            <div class='wizard_title'>
                <?php echo _('Thank you for joining the Open Threat Exchange!') ?>
            </div>

        </div>

        <div class='wizard_subtitle'>
            <?php echo sprintf(_('Click "Finish" to start using AlienVault %s'), $v_tag) ?>
        </div>

    </div>

</div>


<!-- THE BUTTONS HERE -->
<div class='wizard_button_list'>

    <a href='javascript:;' id='prev_step' class='av_l_main'><?php echo _('قبلی') ?></a>
    <a href='javascript:;' id='otx_back'  class='av_l_main'><?php echo _('قبلی') ?></a>

    <button id='next_step'  class="fright finish_wizard"><?php echo _('پایان') ?></button>

    <button id='w_otx_next' class="fright" <?php echo ($otx_key != '') ? '' : 'disabled' ?> ><?php echo _('بعدی') ?></button>
    <button id='w_otx_skip' class="fright av_b_secondary" data-type='skip'><?php echo _('رد کردن این مرحله') ?></button>

</div>
