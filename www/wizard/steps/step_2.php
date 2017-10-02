<?php
/**
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
    throw new Exception('There was an unexpected error');
}

//Getting the scan step to know if we have a scan running
$step = intval($wizard->get_step_data('scan_step'));

//Selected nets
$nets_selected = $wizard->get_step_data('scan_nets');
$nets_selected = (is_array($nets_selected)) ?  $nets_selected : array();

$n_ids = array_fill_keys(array_keys($nets_selected), 1);

?>

<script>

    var __dt_hosts = null;


    function GB_onclose()
    {
        try
        {
            __dt_hosts._fnAjaxUpdate();
        }
        catch(Err){}

    }

    function GB_onhide(url, params)
    {
        if (typeof params == 'undefined')
        {
            params = {}
        }

        if (params['start_scan'] == 1)
        {
            launch_scan_window();
        }

    }

    function load_js_step()
    {
        load_handler_step_discovery();

        <?php
        if ($step != '')
        {
        ?>
        launch_scan_window();
        <?php
        }
        ?>

    };

</script>

<style>

    /* Hidding the search input in the select. It has to be here and not in the css style!! */
    .select2-search

    {
        display:none !important;
    }
    .select2-container
    {
        margin-top:-4px !important;
    }
    {
        font-family: BNazanin;
    }

</style>

<div id='step_discovery' class='step_container' dir="rtl"  >

    <div class='wizard_title'>
        <?php echo _('اسکن کردن و اضافه کردن دارایی') ?>
    </div>

    <div class='wizard_subtitle'>
        <?php echo _('
برای شروع نظارت بر محیط شما باید ابتدا دارایی های خود را در شبکه خود پیدا کنید. سه راه  برای اضافه کردن دارایی ها برای نظارت وجود دارد: شما می توانید شبکه خود را با استفاده از محدوده شبکه اسکن کنید، وارد کردن CSV دارایی ها در شبکه خود، یا می توانید به صورت دستی اضافه کنید
 ') ?>
    </div>

    <div class='container'>

        <div class='wizard_subtitle'>
            <?php echo _('وارد کردن دستی دارایی') ?>
        </div>

        <div id='host_inputs'>
            <input type='text' id='host_name' value='' placeholder="<?php echo _('Hostname') ?>">
            <input type='text' id='host_ip'   value='' placeholder="<?php echo _('IP') ?>">
            <select id='host_type'>
                <option value=''></option>
                <option value='windows_'><?php echo _('ویندوز') ?></option>
                <option value='linux'><?php echo _('لینوکس') ?></option>
                <option value='_networkdevice'><?php echo _('ابزار شبکه') ?></option>
            </select>

            <button disabled="disabled" id='add_host' class='av_b_secondary'>+ <?php echo _('اضافه کردن')?></button>
        </div>

        <div id='import_host_csv'>
            <button class='av_b_secondary' id='launch_scan'><?php echo _('اسکن شبکه ') ?></button>
            <button class='av_b_secondary' id='import_csv'><?php echo _('وارد کردن فایل csv دارایی') ?></button>
        </div>


        <table id='table_host_results' class='table_data item_results'>
            <thead>
            <tr>
                <th class='left'><?php echo _('Hostname')?></th>
                <th class='left'><?php echo _('IP')?></th>
                <th><?php echo _('نوع')?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr><td></td></tr>
            </tbody>
        </table>

    </div>

</div>

<div class='wizard_button_list'>

    <a href='javascript:;' id='prev_step' class='av_l_main'><?php echo _('قبلی') ?></a>

    <button id='next_step' class="fright"><?php echo _('بعدی') ?></button>

</div>

<a href='/ossim/wizard/extra/scan.php' id='scan_url'></a>

