<?php
/**
 * base_header.php
 *
 * File base_header.php is used to:
 * - Be included by includes/base_output_html.inc.php as module of SIEM console
 * - Manage searches and current criterias
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
 * @package    ossim-framework\Siem
 * @autor      AlienVault INC
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt
 * @copyright  2003-2006 ossim.net
 * @copyright  2007-2013 AlienVault
 * @link       https://www.alienvault.com/
 */

require_once 'av_init.php';

Session::logcheck("analysis-menu", "EventsForensics");

// Parameters for date filters
$timetz      = $GLOBALS["timetz"];
$today_d     = gmdate("d",$timetz);
$today_m     = gmdate("m",$timetz);
$today_y     = gmdate("Y",$timetz);
$today_h     = gmdate("H",$timetz);
$yesterday_d = gmdate("d", $timetz-86400);
$yesterday_m = gmdate("m", $timetz-86400);
$yesterday_y = gmdate("Y", $timetz-86400);
$week_d      = gmdate("d", $timetz-604800);
$week_m      = gmdate("m", $timetz-604800);
$week_y      = gmdate("Y", $timetz-604800);
$month_d     = gmdate("d", strtotime("-1 month UTC",$timetz));
$month_m     = gmdate("m", strtotime("-1 month UTC",$timetz));
$month_y     = gmdate("Y", strtotime("-1 month UTC",$timetz));
$year_d      = gmdate("d", strtotime("-11 month UTC",$timetz));
$year_m      = gmdate("m", strtotime("-11 month UTC",$timetz));
$year_y      = gmdate("Y", strtotime("-11 month UTC",$timetz));

// Current signature/payload/IDM filter
$sterm = ($_GET['search_str'] != "") ? $_GET['search_str'] : ($_SESSION['search_str'] != "" ? $_SESSION['search_str'] : _("جستجو"));

?>

<!-- Solera Form -->
<form action="../conf/solera.php" method="post" id="solera_form">
<input type="hidden" name="from">
<input type="hidden" name="to">
<input type="hidden" name="src_ip">
<input type="hidden" name="dst_ip">
<input type="hidden" name="src_port">
<input type="hidden" name="dst_port">
<input type="hidden" name="proto">
</form>

<!-- MAIN HEADER TABLE -->
<table class="container">
<?php
if (count($database_servers)>0 && Session::menu_perms("configuration-menu", "PolicyServers") && Session::is_pro())
{
    // session server
    $ss = (is_array($_SESSION["server"]) && $_SESSION["server"][0]!="") ? $_SESSION["server"][0] : "local";
    $sn = (is_array($_SESSION["server"]) && $_SESSION["server"][4]!="") ? $_SESSION["server"][4] : "snort";
    ?>
    <tr>
    <td class='noborder' width="45" valign="top" style="padding:10px 0px 0px 0px">
        <table class='transparent' cellpadding="0" cellspacing="0">
            <tr>
                 <td class='noborder' align='left'>

                    <button type="button" class='av_b_gray' onclick='$("#dbs").toggle();$("#img_home").attr("src",(($("#img_home").attr("src").match(/plus/)) ? "images/home_minus.png" : "images/home_plus.png"))'>
                        <img id='img_home' src="images/home_plus.png" align="absmiddle" width='15' height='15'/>
                    </button>

                    <div style='position:relative;width:1px'>
                        <div id='dbs'>
                            <table class='noborder transparent' cellpadding='5' cellspacing='1'>

                                <?php
                                foreach ($database_servers as $_db_aux)
                                {
                                    $svar = intval($_db_aux->get_id());
                                    // 'end' tag to solve PHPIDS exception
                                    $name = ($ss==$_db_aux->get_ip() && $sn == $_db_aux->get_name()) ? "<b>".$_db_aux->get_name()."</b>" : $_db_aux->get_name();
                                    ?>
                                    <tr>
                                        <td class='left noborder' style='padding:5px'>
                                        <?php
                                        if ($_db_aux->get_icon() != '')
                                        {
                                            ?>
                                            <img id='db_icon' class='asset_icon w16' src='data:image/png;base64,<?php echo base64_encode($_db_aux->get_icon())?>'/>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <img id='db_icon' class='asset_icon w16' src='../forensics/images/server.png'/>
                                            <?php
                                        }
                                        ?>
                                        </td>
                                        <td class='left noborder' nowrap>
                                            <a href='<?php echo preg_replace("/\&server\=[^\&]+/","",$actual_url) ?>server=<?php echo $svar ?>'><?php echo $name ?></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>

                                <tr>
                                    <td class='left noborder' style='padding:5px'>
                                        <img id='db_icon' style='width: 16px; height: 16px;' src='../forensics/images/home.png' border='0' align='absbottom'/>
                                    </td>
                                    <td class='left noborder'>
                                        <a href='<?php echo preg_replace("/\&server\=[^\&]+/","",$actual_url) ?>server=local'><?php echo ($ss=="local" ? "<b>"._("محلی")."</b>" : _("محلی")) ?></a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </td>
    </tr>
    <?php
}
?>
<tr>
    <td valign="top" width="380" class="box" style="border-right:none">

    <!-- Query Main Form -->
    <form name="QueryForm" id="frm" action="base_qry_main.php" method="get" onsubmit="CheckSensor()" style="margin:0 auto">

    <input type='hidden' name="search" value="1" />
    <input type="hidden" name="hidden_menu" id="hidden_menu" value="1" />
    <input type="submit" name="bsf" id="bsf" value="Query DB" style="display:none">

<table class="container">
<tr>
    <td colspan='2'>
            <!-- Signature, Payload, IDM Search input -->

                    <div class='siem_form_search'>

                        <div class='left_float'>
                            <input type="text" name="search_str" id="search_str" class="ne pholder" placeholder="<?php echo _('Search') ?>">
                        </div>

                        <div class='left_float'>
                            <select name="submit" class="hselect" id='search_type_combo'>
                                <option value="<?php echo _("امضا") ?>"><?php echo _("امضا") ?></option>
                                <option value="Payload"><?php echo _("پیلود") ?></option>
                                <?php if ($_SESSION["_idm"]) { ?>
                                <option value="<?php echo _("IDM نام کاربری") ?>"><?php echo _("IDM نام کاربری") ?></option>
                                <option value="<?php echo _("IDM نام میزبان") ?>"><?php echo _("IDM نام میزبان") ?></option>
                                <option value="<?php echo ("IDM دامنه") ?>"><?php echo ("IDM دامنه") ?></option>
                                <?php } ?>
                                <option value="<?php echo _("Src یا Dst IP") ?>"><?php echo _("Src یا Dst IP") ?></option>
                                <option value="<?php echo _("Src IP") ?>"><?php echo _("Src IP") ?></option>
                                <option value="<?php echo _("Dst IP") ?>"><?php echo _("Dst IP") ?></option>
                                <option value="<?php echo _("Src یا Dst Host") ?>"><?php echo _("Src یا Dst Host") ?></option>
                                <option value="<?php echo _("Src Host") ?>"><?php echo _("Src Host") ?></option>
                                <option value="<?php echo _("Dst Host") ?>"><?php echo _("Dst Host") ?></option>
                            </select>
                        </div>

                        <!-- GO SUBMIT BUTTON -->
                        <div class='left_float'>
                            <input type="submit" style="margin-top: 3px;padding: 5px 5px;" value="<?php echo _('شروع') ?>" id="go_button">
                        </div>

                        <img id='help_tooltip' class='help_icon_1' src="/ossim/pixmaps/help_small.png">




                <!-- Export data in PDF/CSV -->
                <?php
                // Events
                if (preg_match("/base_qry_main\.php/", $_SERVER['SCRIPT_NAME']))
                {
                    $export_pdf_mode = ($_SESSION['current_cview'] == "IDM" || $_SESSION['current_cview'] == "default") ? "Events_Report" : "";
                    $export_csv_mode = "Events_Report";
                    $csv_report_type = 33;
                }
                // Unique events
                elseif (preg_match("/base_stat_alerts\.php/", $_SERVER['SCRIPT_NAME']))
                {
                    $export_pdf_mode = "UniqueEvents_Report";
                    $export_csv_mode = "UniqueEvents_Report";
                    $csv_report_type = 36;
                }
                // Unique sensors
                elseif (preg_match("/base_stat_sensor\.php/", $_SERVER['SCRIPT_NAME']))
                {
                    $export_pdf_mode = "Sensors_Report";
                    $export_csv_mode = "Sensors_Report";
                    $csv_report_type = 38;
                }
                // Unique data sources
                elseif (preg_match("/base_stat_plugins\.php/", $_SERVER['SCRIPT_NAME']))
                {
                    $export_pdf_mode = "UniquePlugin_Report";
                    $export_csv_mode = "UniquePlugin_Report";
                    $csv_report_type = 46;
                }
                // Unique IPs Src/Dst
                elseif (preg_match("/base_stat_uaddr|base_stat_uidm/", $_SERVER['SCRIPT_NAME']) && ($_GET['addr_type'] == "1" || $_GET['addr_type'] == "2"))
                {
                    $export_pdf_mode = "UniqueAddress_Report".intval($_GET['addr_type']);
                    $export_csv_mode = "UniqueAddress_Report".intval($_GET['addr_type']);
                    $csv_report_type = 40;
                }
                // Unique ports
                elseif (preg_match("/base_stat_ports\.php/", $_SERVER['SCRIPT_NAME']))
                {
                    $_report_type    = ($_GET['proto'] == '6') ? 1 : (($_GET['proto'] == '17') ? 2 : 0);
                    $export_pdf_mode = ($_GET['port_type'] == 1) ? "SourcePort_Report$_report_type" : "DestinationPort_Report$_report_type";
                    $export_csv_mode = ($_GET['port_type'] == 1) ? "SourcePort_Report$_report_type" : "DestinationPort_Report$_report_type";
                    $csv_report_type = ($_GET['port_type'] == 1) ? 42 : 44;
                }
                // Unique IPLinks
                elseif (preg_match("/base_stat_iplink\.php/", $_SERVER['SCRIPT_NAME']) && GET('fqdn') == 'no')
                {
                    $export_pdf_mode = "UniqueIPLinks_Report";
                    $export_csv_mode = "UniqueIPLinks_Report";
                    $csv_report_type = 37;
                }
                // Unique Countries
                elseif (preg_match("/base_stat_country\.php/", $_SERVER['SCRIPT_NAME']))
                {
                    $export_pdf_mode = "UniqueCountryEvents_Report";
                    $export_csv_mode = "UniqueCountryEvents_Report";
                    $csv_report_type = 48;
                }
                else
                {
                    $export_pdf_mode = "";
                    $export_csv_mode = "";
                }


                // Deprecated
                $cloud_instance = ($conf->get_conf("cloud_instance", FALSE) == 1) ? TRUE : FALSE;

                if (($export_pdf_mode != '' || $export_csv_mode != '') && !$cloud_instance)
                {
                    ?>
                    <div style='float:right'>
                        <a style='cursor:pointer' class='ndc' onclick="$('#export').toggle()"><img src="../pixmaps/forensic_download.png" border="0"/></a>
                        <div style="position:relative">
                            <div id="export">
                                <div>
                                    <table>

                                        <thead>
                                            <tr>
                                                <th id='title'>
                                                    <div id='c_close'>
                                                        <div id='b_close'>
                                                            <a style="cursor:pointer;" onclick="$('#export').toggle()">
                                                                <img src="../pixmaps/cross-circle-frame.png" alt="<?php echo _("بستن"); ?>" title="<?php echo _("بستن"); ?>" border="0" align='absmiddle'/>
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <span><?php echo _("نوع خروجی")?></span>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            <?php
                                            // PDF
                                            if ($export_pdf_mode != '')
                                            {
                                                ?>
                                                <tr>
                                                    <td class='left'>
                                                        <a href="javascript:;" onclick="javascript:report_launcher('<?php echo $export_pdf_mode ?>','pdf');return false">
                                                            <img src="images/pdf-icon.png" border="0" align="absmiddle" title="<?=_("دادن گزارش به شکل PDF")?>"> <?php echo _("دانلود داده به عنوان گزارش PDF") ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }

                                            // CSV
                                            if ($export_csv_mode != "")
                                            {
                                                ?>
                                                <tr>
                                                    <td class='left'>
                                                       <a href="javascript:;" onclick="javascript:report_launcher('<?php echo $export_csv_mode ?>','<?php echo $csv_report_type ?>');return false">
                                                           <img src="images/csv-icon.png" border="0" align="absmiddle" title="<?=_('گرفتن داده با فرمت CSV')?>"> <?php echo _("گرفتن داده با فرمت CSV") ?>
                                                       </a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class='clear_layer'></div>
            </div>

            <!-- Date, DS, Sensor, Risk, Taxonomy, Reputation boxes -->

            <div>

                <!-- Datetime -->

                <div class='siem_form_column'>
                    <?php
                    $urltimecriteria = Util::get_sanitize_request_uri($_SERVER['SCRIPT_NAME']);
                    $params = "";
                    // Clicked from qry_alert or clicked from Time profile must return to main
                    if (preg_match("/base_qry_alert|base_stat_time/", $urltimecriteria)) {
                        $urltimecriteria = "base_qry_main.php";
                    }
                    if ($_GET["addr_type"]  != "") $params.= "&addr_type=" .  urlencode($_GET["addr_type"]);
                    if ($_GET["sort_order"] != "") $params.= "&sort_order=" . urlencode($_GET["sort_order"]);
                    if ($_GET["proto"]      != "") $params.= "&proto=" . urlencode($_GET["proto"]);
                    if ($_GET["port_type"]  != "") $params.= "&port_type=" . urlencode($_GET["port_type"]);
                    if ($_GET["fqdn"]       != "") $params.= "&fqdn=" . urlencode($_GET["fqdn"]);
                    ?>
                    <div class='siem_form_title'><?php echo _("نمایش رویدادها") ?></div>

                        <div>

                            <div class='siem_form_daterange'>
                                <input class="margin0" type="radio" <? if ($_GET['time_range'] == "day")   echo "checked" ?> name="selected_time_range" onclick="load_link('<?php echo Util::get_sanitize_request_uri($urltimecriteria) ?>?time_range=day&time%5B0%5D%5B0%5D=+&time%5B0%5D%5B1%5D=%3E%3D&time%5B0%5D%5B2%5D=<?php echo $yesterday_m ?>&time%5B0%5D%5B3%5D=<?php echo $yesterday_d ?>&time%5B0%5D%5B4%5D=<?php echo $yesterday_y ?>&time%5B0%5D%5B5%5D=<?php echo $today_h ?>&time%5B0%5D%5B6%5D=&time%5B0%5D%5B7%5D=&time%5B0%5D%5B8%5D=+&time%5B0%5D%5B9%5D=+&submit=Query+DB&num_result_rows=-1&time_cnt=1<?php echo $params ?>')"/>
                                <?php echo _("آخرین روز") ?>
                            </div>

                            <div class='siem_form_daterange'>
                                <input class="margin0" type="radio" <? if ($_GET['time_range'] == "week")  echo "checked" ?> name="selected_time_range" onclick="load_link('<?php echo Util::get_sanitize_request_uri($urltimecriteria) ?>?time_range=week&time%5B0%5D%5B0%5D=+&time%5B0%5D%5B1%5D=%3E%3D&time%5B0%5D%5B2%5D=<?php echo $week_m ?>&time%5B0%5D%5B3%5D=<?php echo $week_d ?>&time%5B0%5D%5B4%5D=<?php echo $week_y ?>&time%5B0%5D%5B5%5D=<?php echo $today_h ?>&time%5B0%5D%5B6%5D=&time%5B0%5D%5B7%5D=&time%5B0%5D%5B8%5D=+&time%5B0%5D%5B9%5D=+&submit=Query+DB&num_result_rows=-1&time_cnt=1<?php echo $params ?>')"/>
                                <?php echo _("آخرین هفته") ?>
                            </div>

                            <div class='siem_form_daterange'>
                                <input class="margin0" type="radio" <? if ($_GET['time_range'] == "month") echo "checked" ?> name="selected_time_range" onclick="load_link('<?php echo Util::get_sanitize_request_uri($urltimecriteria) ?>?time_range=month&time%5B0%5D%5B0%5D=+&time%5B0%5D%5B1%5D=%3E%3D&time%5B0%5D%5B2%5D=<?php echo $month_m ?>&time%5B0%5D%5B3%5D=<?php echo $month_d ?>&time%5B0%5D%5B4%5D=<?php echo $month_y ?>&time%5B0%5D%5B5%5D=<?php echo $today_h ?>&time%5B0%5D%5B6%5D=&time%5B0%5D%5B7%5D=&time%5B0%5D%5B8%5D=+&time%5B0%5D%5B9%5D=+&submit=Query+DB&num_result_rows=-1&time_cnt=1<?php echo $params ?>')"/>
                                <?php echo _("آخرین ماه") ?>
                            </div>

                            <div class='siem_form_daterange'>
                                <input class="margin0" type="radio" <? if ($_GET['time_range'] == "range") echo "checked" ?> name="selected_time_range" onclick='show_calendar()'/>
                                <?php echo _("تاریخ") ?>
                            </div>


                            <div class='datepicker_range siem_form_daterange'>
                                <div class='calendar_from'>
                                    <div class='calendar'>
                                        <input id='date_from' class='date_filter' type="input" value="<?php if ($_SESSION['time_range'] == "range") echo (($_SESSION['time'][0][4] != '') ? $_SESSION['time'][0][4] : '*')."-".(($_SESSION['time'][0][2] != '') ? $_SESSION['time'][0][2] : '*')."-".(($_SESSION['time'][0][3] != '') ? $_SESSION['time'][0][3] : '*') ?>">
                                    </div>
                                </div>
                                <div class='calendar_separator'>
                                    -
                                </div>
                                <div class='calendar_to'>
                                    <div class='calendar'>
                                        <input id='date_to' class='date_filter' type="input" value="<?php if ($_SESSION['time_range'] == "range") echo (($_SESSION['time'][1][4] != '') ? $_SESSION['time'][1][4] : '*')."-".(($_SESSION['time'][1][2] != '') ? $_SESSION['time'][1][2] : '*')."-".(($_SESSION['time'][1][3] != '') ? $_SESSION['time'][1][3] : '*') ?>">
                                    </div>
                                </div>
                            </div>

                        </div>
                        
                        <div class='siem_form_ud'>
                            <select class="selectu" name="userdata[0]">
                                <option value="userdata1"<?= ($_SESSION["userdata"][0]=="userdata1") ? " selected" : ""; ?>>userdata1</option>
                                <option value="userdata2"<?= ($_SESSION["userdata"][0]=="userdata2") ? " selected" : ""; ?>>userdata2</option>
                                <option value="userdata3"<?= ($_SESSION["userdata"][0]=="userdata3") ? " selected" : ""; ?>>userdata3</option>
                                <option value="userdata4"<?= ($_SESSION["userdata"][0]=="userdata4") ? " selected" : ""; ?>>userdata4</option>
                                <option value="userdata5"<?= ($_SESSION["userdata"][0]=="userdata5") ? " selected" : ""; ?>>userdata5</option>
                                <option value="userdata6"<?= ($_SESSION["userdata"][0]=="userdata6") ? " selected" : ""; ?>>userdata6</option>
                                <option value="userdata7"<?= ($_SESSION["userdata"][0]=="userdata7") ? " selected" : ""; ?>>userdata7</option>
                                <option value="userdata8"<?= ($_SESSION["userdata"][0]=="userdata8") ? " selected" : ""; ?>>userdata8</option>
                                <option value="userdata9"<?= ($_SESSION["userdata"][0]=="userdata9") ? " selected" : ""; ?>>userdata9</option>
                                <option value="filename"<?= ($_SESSION["userdata"][0]=="filename") ? " selected" : ""; ?>>filename</option>
                                <option value="username"<?= ($_SESSION["userdata"][0]=="username") ? " selected" : ""; ?>>username</option>
                                <option value="password"<?= ($_SESSION["userdata"][0]=="password") ? " selected" : ""; ?>>password</option>
                            </select>
                            <select name="userdata[1]">
                                <option value="EQ"<?= ($_SESSION["userdata"][1]=="EQ") ? " selected" : ""; ?>>=</option>
                                <option value="NE"<?= ($_SESSION["userdata"][1]=="NE") ? " selected" : ""; ?>><></option>
                                <option value="LT"<?= ($_SESSION["userdata"][1]=="LT") ? " selected" : ""; ?>><</option>
                                <option value="LOE"<?= ($_SESSION["userdata"][1]=="LOE") ? " selected" : ""; ?>><=</option>
                                <option value="GT"<?= ($_SESSION["userdata"][1]=="GT") ? " selected" : ""; ?>>></option>
                                <option value="GOE"<?= ($_SESSION["userdata"][1]=="GOE") ? " selected" : ""; ?>>>=</option>
                                <option value="like"<?= ($_SESSION["userdata"][1]=="like") ? " selected" : ""; ?>>like</option>
                            </select>
                        </div>

                    </div>

                    <div class='siem_form_column'>

                        <!-- Data Sources -->
                        <div class='siem_form_title'><?php echo _("منابع داده ها") ?></div>
                        <div>
                                <select name="plugin" class="selectp" onchange="$('input[name=sourcetype],#category,#subcategory').val('');this.form.bsf.click()"><option value=''></option>
                                <?php
                                // Get Plugins
                                $snortsensors = GetPlugins($db);
                                uksort($snortsensors, "strnatcasecmp");

                                // Get selected
                                $plugins_selected = array();
                                if (preg_match('/\,/', $_SESSION['plugin']))
                                {
                                    $plugins_selected = explode(',', $_SESSION['plugin']);
                                }
                                elseif (preg_match('/(\d+)\-(\d+)/', $_SESSION['plugin'], $found) && $found[1] < $found[2])
                                {
                                    for ($i = $found[1]; $i <= $found[2]; $i++)
                                    {
                                        $plugins_selected[] = $i;
                                    }
                                }
                                else
                                {
                                    $plugins_selected[] = $_SESSION['plugin'];
                                }

                                // Print select box
                                foreach($snortsensors as $plugin_name => $pids)
                                {
                                    $pid       = implode(",", $pids);
                                    $intersect = array_intersect($pids, $plugins_selected);
                                    $sel       = (count($intersect) > 0) ? "selected" : "";
                                    echo "<option value='".Util::htmlentities($pid)."' $sel>".Util::htmlentities(ucfirst($plugin_name))."</option>\n";
                                }
                                ?>
                                </select>
                        </div>

                        <!-- force order -->
                        <input type="hidden" id="sort_order" name="sort_order" value="">

                        <!-- DS Group -->
                        <div class='siem_form_title'><?=_("گروه منابع داده ها")?></div>
                        <div>
                            <select name="plugingroup" class="selectp" onchange="this.form.bsf.click()"><option value=''></option>
                            <?php
                            $pg = GetPluginGroups($db);
                            foreach ($pg as $idpg => $namepg) echo "<option value='$idpg'".(($_SESSION["plugingroup"]==$idpg) ? " selected" : "").">$namepg</option>\n";
                            ?>
                            </select>
                        </div>

                        <!-- Sensors -->
                        <?php
                        $snortsensors = GetSensorSids($db);
                        $sensor_keys  = array();
                        if (Session::allowedSensors() != "")
                        {
                            $user_sensors = explode(",",Session::allowedSensors());
                            foreach ($user_sensors as $user_sensor)
                            {
                                $sensor_keys[$user_sensor]++;
                            }
                        }
                        else
                        {
                            $sensor_keys['all'] = 1;
                        }
                        // sort by sensor name
                        $sensor = ($_GET["sensor"] != "") ? $_GET["sensor"] : $_SESSION["sensor"];
                        $sstr = $exclude = '';

                        foreach ($sensors as $sid => $sdata)
                        {
                            // Only show sensors allowed && sensors with events
                            if ( ($sensor_keys['all'] || $sensor_keys[$sdata['ip']]) && array_key_exists($sid, $snortsensors))
                            {
                                $txt = Util::htmlentities($sdata['name'])." [" . $sdata['ip'] . "]";
                                $sel = ($sensor == $snortsensors[$sid] || $sensor == '!'.$snortsensors[$sid]) ? ' selected' : '';
                                $sstr .= '<option value="'.$sid.'"'.$sel.'>'.$txt.'</option>'."\n";
                                // Exclude checkbox
                                if (empty($exclude) && $sensor == '!'.$snortsensors[$sid])
                                {
                                    $exclude = ' checked';
                                }
                            }
                        }
                        ?>
                        <div class='siem_form_title_sensor'><?php echo _("حسگرها") ?>
                            <div class='exclude'>
                                <table class="tax noborder"><tr>
                                <td><input type="checkbox" id="exclude" name="exclude" onclick="SetSensor(this.form.bsf,false)" value="1"<?php echo $exclude ?>></td>
                                <td id="lexc"><?php echo _("جدا کردن") ?></td>
                                </tr></table>
                            </div>
                        </div>
                        <div>
                            <input type="hidden" id="ctx" name="ctx" value="<?php echo Util::htmlentities($ctx) ?>">
                            <select name="sensor" id="sensor" class="selectp" onchange="SetSensor(this.form.bsf,true)">
                                <option value=""></option>
                                <?php echo $sstr.$ents ?>
                            </select>
                        </div>

                        <!-- User data content -->
                        <div class='siem_form_pad'>
                             <input type="text" id='extradatafield' name="userdata[2]" placeholder="" class='search_sensor' value="<?php echo Util::htmlentities($_SESSION["userdata"][2]) ?>"/>
                             <!-- <input type="button" value="<?php echo _("انجام")?>" onclick="this.form.bsf.click()" class="small av_b_secondary"/> -->
                        </div>

                    </div>


                    <!-- Taxonomy -->

                    <div class='siem_form_column'>

                        <!-- Asset Group -->
                        <div class='siem_form_title'><?=_("گروه های دارایی")?></div>
                        <div>
                            <select name="addhomeips" class="selectp" onchange="this.form.bsf.click()"><option value='-1'></option>
                            <?php
                            $hg = GetOssimHostGroups();
                            foreach ($hg as $hgid => $namehg)
                            {
                                echo "<option value='".$hgid."'".(($_SESSION["_hostgroup"]==$hgid) ? " selected" : "").">$namehg</option>\n";
                            }
                            ?>
                            </select>
                        </div>

                        <!--  Network Group -->
                        <div class='siem_form_title'><?=_("گروه های شبکه")?></div>
                        <div>
                            <select name="networkgroup" class="selectp" onchange="this.form.bsf.click()">
                                <option value=''></option>
                                <?php
                                $ng = GetOssimNetworkGroups();
                                foreach ($ng as $ngid => $nameng) echo "<option value='$ngid'".(($_SESSION["networkgroup"]==$ngid) ? " selected" : "").">$nameng</option>\n";
                                ?>
                            </select>
                        </div>


                        <!-- Risk -->
                        <div class='siem_form_title'><?php echo _("ریسک") ?></div>
                        <div>
                            <select name="ossim_risk_a" class="selectp" onchange="$('#sort_order').val((this.value==' ') ? '' : 'oriska_d');this.form.bsf.click()"><option value=' '>
                                <option value="low"<?php if ($_SESSION['ossim_risk_a'] == "low") echo " selected" ?>><?php echo _("پایین") ?></option>
                                <option value="medium"<?php if ($_SESSION['ossim_risk_a'] == "medium") echo " selected" ?>><?php echo _("متوسط") ?></option>
                                <option value="high"<?php if ($_SESSION['ossim_risk_a'] == "high") echo " selected" ?>><?php echo _("بالا") ?></option>
                            </select>
                        </div>

                        <?php
                        if (Session::is_pro()) {
                        ?>
                        <!-- Device -->
                        <div class='siem_form_dev'>
                            <input type="text" name="device" id="device_input" value="<?php if ($_SESSION['device'] != "") echo Util::htmlentities($_SESSION['device']) ?>" placeholder="<?php echo _("Device IP") ?>" class='search_sensor'/>
                        </div>
                        <?php } ?>

                    </div>


                    <!-- Reputation -->
                    <?php global $rep_activities, $rep_severities; ?>

                    <div class='siem_form_column'>

                        <div class='siem_form_title'><?php echo _("OTX IP Reputation") ?></div>
                        <div>
                            <input type="hidden" name="rep[1]" id="severity" value="">
                            <select name="rep[0]" id="activity" class="selectp" onchange="this.form.bsf.click()"><option value=''></option>
                            <option value='0'<?php echo ($_SESSION["rep"][0]=="0") ? " selected" : "";?>><?php echo _("ANY OTX IP Reputation") ?></option>
                            <?php
                            foreach ($rep_severities as $idsev => $sev)
                            {
                                echo "<option value=\"".Util::htmlentities($idsev)."\"".(($_SESSION["rep"][0]=="" && $_SESSION["rep"][1]!="" && $_SESSION["rep"][1]==$idsev) ? " selected" : "").">"._("ANY ".$sev)."</option>\n";
                            }
                            foreach ($rep_activities as $idact => $act)
                            {
                                foreach ($rep_severities as $idsev => $sev)
                                {
                                    echo "<option value=\"".Util::htmlentities($idact).";$idsev\"".(($_SESSION["rep"][0]==$idact && $_SESSION["rep"][1]==$idsev) ? " selected" : "").">".Util::htmlentities("$act  - $sev")."</option>\n";
                                }
                            }
                            ?>
                            </select>
                        </div>

                        <div class='siem_form_title'><?php echo _("OTX پالس") ?></div>
                        <div>
                            <input type="hidden" name="otx[0]" id="otx_pulse_value" value="<?php if ($_SESSION["otx"][0]!='') echo $_SESSION["otx"][0] ?>"/>
                            <input type="text" id="otx_pulse" value="<?php if (!empty($_SESSION["otx"][0])) echo GetPulseName($_SESSION["otx"][0]); ?>" placeholder="<?php echo _("نام پالس") ?>" class='search_sensor'/>
                        </div>

                        <div class='siem_form_otx'>
                            <input type="checkbox" onchange="$('#otx_pulse_value').val('');$('#otx_pulse').val('');this.form.bsf.click()" name="otx[1]" id="otx_activity" value="1" <?php if ($_SESSION["otx"][1] != "") echo "checked" ?>/>
                            <label for="otx_activity" class='siem_form_title'><?php echo _("ففط فعالیت های پالس OTX") ?></label>
                        </div>

                    </div>
    
                    <div class='siem_form_column'>
                    <?php
                        // Current criteria box
                        PrintCriteria2();
                    ?>
                        <div class="float_left">
                            <input type="button" class="av_b_secondary" value="<?php echo _("جستجوی پیشرفته") ?>" id="adv_search_button"/>
                        </div>

                    </div>
    
                    <div class='clear_layer'></div>

            </div>

            <div id="filters_buttons_div">

                 <div class="float_right padding_right_5 task_info">
                    <span id="task" style="display:none;"><?php echo _("عدم وجود کارهای معوق") ?>.</span>
                 </div>

                 <div id="backup_info"></div>

            </div>

    </td>
</tr>

<tr>
    <td style="padding:0px">
        <table class="container">
            <tr>
                    <td style="text-align:left">
                    <div id='tab_siem'>

                        <!-- The target is actually processing by data-action_id, always reload the page -->
                        <div id='null_aux_div'></div>

                        <ul>
                            <li>
                            <?php
                            if (preg_match("/base\_qry\_main/", $_SERVER['SCRIPT_NAME']))
                            {
                            ?>
                            <a href="#null_aux_div" data-action_id='0'>
                            <?php
                            }
                            else
                            {
                            ?>
                            <a href="#null_aux_div" data-action_id='1'>
                            <?php
                            }
                            ?>
                            <?php echo _("Events") ?>
                            </a>
                            </li>
                            <li>
                                <a href="#null_aux_div" data-action_id='2'>
                            <?php echo _("گروه بندی شده"); ?>
                            </a>
                            </li>
                            <li>
                            <?php
                            if (preg_match("/base\_timeline/", $_SERVER['SCRIPT_NAME']))
                            {
                            ?>
                                <a href="#null_aux_div" data-action_id='3'>
                                <?php
                                }
                                else
                                {
                                ?>
                            <a href="#null_aux_div" data-action_id='4'>
                                <?php
                                }
                                ?>
                            <?php echo _("خط زمانی") ?>
                            </a>
                            </li>
                        </ul>
                    </div>
                    </td>
                </tr>
        </table>
    </td>
</tr>

<!-- Grouped option -->
<tr>
    <td id="grouped_option" style="padding: 10px 0px 3px 0px;display:none">
        <table class="transparent" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td class='left'>
                <table class="transparent" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="siem_title_gray"><?php echo _("Group Events by") ?>&nbsp;</td>
                    <td>
                    <?php global $addr_type ?>
                        <!-- Level 1 grouping -->
                        <select name="groupby_1" id="groupby_1" style='width:110px' onchange="group_selected(this.value)">
                            <option value=""><?php echo _("یکی را انتخاب کنید") ?></option>
                            <option value="ip"         <?php if (preg_match("/base_stat_(uaddr|iplink)/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("IP") ?></option>
                            <option value="hostname"   <?php if (preg_match("/base_stat_uidm/", $_SERVER['SCRIPT_NAME']) && preg_match("/host/", $addr_type)) echo "انتخاب شده" ?>><?php echo _("IDM نام میزبان") ?></option>
                            <option value="idmusername"   <?php if (preg_match("/base_stat_uidm/", $_SERVER['SCRIPT_NAME']) && preg_match("/user/", $addr_type)) echo "انتخاب شده" ?>><?php echo _("IDM نام کاربری") ?></option>
                            <option value="signature"  <?php if (preg_match("/base_stat_alerts\.php/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("امضا") ?></option>
                            <option value="port"       <?php if (preg_match("/base_stat_ports/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("پورت") ?></option>
                            <option value="sensor"     <?php if (preg_match("/base_stat_sensor\.php/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("حسگرها") ?></option>
                            <option value="otx"        <?php if (preg_match("/base_stat_otx/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("OTX پالس") ?></option>
                            <option value="ptypes"     <?php if (preg_match("/base_stat_ptypes/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("نوع محصول") ?></option>
                            <option value="plugins"    <?php if (preg_match("/base_stat_plugins/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("منبع داده") ?></option>
                            <option value="country"    <?php if (preg_match("/base_stat_country/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("کشور") ?></option>
                            <option value="categories" <?php if (preg_match("/base_stat_categories/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("دسته ها") ?></option>
                            <option value="username"   <?php if (preg_match("/base_stat_extra/", $_SERVER['SCRIPT_NAME']) && preg_match("/username/", $addr_type)) echo "selected" ?>><?php echo _("نام کاربری 1") ?></option>
                            <option value="userdata1"   <?php if (preg_match("/base_stat_extra/", $_SERVER['SCRIPT_NAME']) && preg_match("/userdata1/", $addr_type)) echo "selected" ?>><?php echo _("نام کاربری 2") ?></option>
                            <option value="userdata2"   <?php if (preg_match("/base_stat_extra/", $_SERVER['SCRIPT_NAME']) && preg_match("/userdata2/", $addr_type)) echo "selected" ?>><?php echo _("نام کاربری 3") ?></option>
                            <option value="userdata3"   <?php if (preg_match("/base_stat_extra/", $_SERVER['SCRIPT_NAME']) && preg_match("/userdata3/", $addr_type)) echo "selected" ?>><?php echo _("نام کاربری 4") ?></option>
                            <option value="userdata4"   <?php if (preg_match("/base_stat_extra/", $_SERVER['SCRIPT_NAME']) && preg_match("/userdata4/", $addr_type)) echo "selected" ?>><?php echo _("نام کاربری 5") ?></option>
                            <option value="userdata5"   <?php if (preg_match("/base_stat_extra/", $_SERVER['SCRIPT_NAME']) && preg_match("/userdata5/", $addr_type)) echo "selected" ?>><?php echo _("نام کاربری 6") ?></option>
                            <option value="userdata6"   <?php if (preg_match("/base_stat_extra/", $_SERVER['SCRIPT_NAME']) && preg_match("/userdata6/", $addr_type)) echo "selected" ?>><?php echo _("نام کاربری 7") ?></option>
                            <option value="userdata7"   <?php if (preg_match("/base_stat_extra/", $_SERVER['SCRIPT_NAME']) && preg_match("/userdata7/", $addr_type)) echo "selected" ?>><?php echo _("نام کاربری 8") ?></option>
                            <option value="userdata8"   <?php if (preg_match("/base_stat_extra/", $_SERVER['SCRIPT_NAME']) && preg_match("/userdata8/", $addr_type)) echo "selected" ?>><?php echo _("نام کاربری 9") ?></option>
                            <option value="userdata9"   <?php if (preg_match("/base_stat_extra/", $_SERVER['SCRIPT_NAME']) && preg_match("/userdata9/", $addr_type)) echo "selected" ?>><?php echo _("نام کاربری 10") ?></option>
                        </select>
    
                        <!-- Level 2: IP -->
                        <div id="group_ip_select" style="display:<?php echo (preg_match("/base_stat_(uaddr|iplink)/", $_SERVER['SCRIPT_NAME'])) ? "inline" : "none" ?>">
                        <select name="groupby_ip" id="groupby_ip" onchange="group_selected(this.value)">
                            <option value="ipempty"><?php echo _("یکی را انتخاب کنید") ?></option>
                            <option value="ipboth"      <?php if (preg_match("/base_stat_uaddress\.php/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("همه") ?></option>
                            <option value="ipsrc"       <?php if (preg_match("/base_stat_uaddr\.php/", $_SERVER['SCRIPT_NAME']) && $addr_type == 1) echo "selected" ?>><?php echo _("منبع") ?></option>
                            <option value="ipdst"       <?php if (preg_match("/base_stat_uaddr\.php/", $_SERVER['SCRIPT_NAME']) && $addr_type == 2) echo "selected" ?>><?php echo _("مقصد") ?></option>
                            <option value="iplink"      <?php if (preg_match("/base_stat_iplink\.php/", $_SERVER['SCRIPT_NAME']) && $_GET['fqdn'] == 'no') echo "selected" ?>><?php echo _("IP لینک") ?></option>
                            <option value="iplink_fqdn" <?php if (preg_match("/base_stat_iplink\.php/", $_SERVER['SCRIPT_NAME']) && $_GET['fqdn'] == 'yes') echo "selected" ?>><?php echo _("IP لینک [FQDN]") ?></option>
                        </select>
                        </div>
    
                        <!-- Level 2: Hostname -->
                        <div id="group_hostname_select" style="display:<?php echo (preg_match("/base_stat_uidm/", $_SERVER['SCRIPT_NAME']) && preg_match("/host/", $_GET['addr_type'])) ? "inline" : "none" ?>">
                        <select name="groupby_hostname" id="groupby_hostname" onchange="group_selected(this.value)">
                            <option value="hostnameempty"><?php echo _("یکی را انتخاب کنید") ?></option>
                            <option value="hostnameboth" <?php if (preg_match("/base_stat_uidm\.php/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("همه") ?></option>
                            <option value="hostnamesrc"  <?php if (preg_match("/base_stat_uidmsel\.php/", $_SERVER['SCRIPT_NAME']) && $addr_type == "src_hostname") echo "selected" ?>><?php echo _("منبع") ?></option>
                            <option value="hostnamedst"  <?php if (preg_match("/base_stat_uidmsel\.php/", $_SERVER['SCRIPT_NAME']) && $addr_type == "dst_hostname") echo "selected" ?>><?php echo _("مقصد") ?></option>
                        </select>
                        </div>
    
                        <!-- Level 2: Username -->
                        <div id="group_username_select" style="display:<?php echo (preg_match("/base_stat_uidm/", $_SERVER['SCRIPT_NAME']) && preg_match("/user/", $_GET['addr_type'])) ? "inline" : "none" ?>">
                        <select name="groupby_username" id="groupby_username" onchange="group_selected(this.value)">
                            <option value="usernameempty"><?php echo _("یکی را انتخاب کنید") ?></option>
                            <option value="usernameboth" <?php if (preg_match("/base_stat_uidm\.php/", $_SERVER['SCRIPT_NAME'])) echo "selected" ?>><?php echo _("همه") ?></option>
                            <option value="usernamesrc"  <?php if (preg_match("/base_stat_uidmsel\.php/", $_SERVER['SCRIPT_NAME']) && $addr_type == "src_userdomain") echo "selected" ?>><?php echo _("منبع") ?></option>
                            <option value="usernamedst"  <?php if (preg_match("/base_stat_uidmsel\.php/", $_SERVER['SCRIPT_NAME']) && $addr_type == "dst_userdomain") echo "selected" ?>><?php echo _("مقصد") ?></option>
                        </select>
                        </div>
    
                        <!-- Level 2: Port -->
                        <div id="group_port_select" style="display:<?php echo (preg_match("/base_stat_port/", $_SERVER['SCRIPT_NAME'])) ? "inline" : "none" ?>">
                        <?php global $port_type ?>
                        <select name="groupby_port" id="groupby_port" onchange="group_selected(this.value)">
                            <option value="portempty"><?php echo _("یکی را انتخاب کنید") ?></option>
                            <option value="portsrc" <?php if (preg_match("/base_stat_port/", $_SERVER['SCRIPT_NAME']) && $port_type == "1") echo "selected" ?>><?php echo _("منبع") ?></option>
                            <option value="portdst" <?php if (preg_match("/base_stat_port/", $_SERVER['SCRIPT_NAME']) && $port_type == "2") echo "selected" ?>><?php echo _("مقصد") ?></option>
                        </select>
                        </div>
    
                        <!-- Level 3: Port Protocol -->
                        <div id="group_proto_select" style="display:<?php echo (preg_match("/base_stat_port/", $_SERVER['SCRIPT_NAME'])) ? "inline" : "none" ?>">
                        <?php global $proto ?>
                        <select name="groupby_proto" id="groupby_proto" onchange="group_selected(this.value)">
                            <option value="portprotoempty"><?php echo _("یکی را انتخاب کنید") ?></option>
                            <option value="portprotoany" <?php if (preg_match("/base_stat_port/", $_SERVER['SCRIPT_NAME']) && $proto == "-1")  echo "selected" ?>><?php echo _("Any") ?></option>
                            <option value="portprototcp" <?php if (preg_match("/base_stat_port/", $_SERVER['SCRIPT_NAME']) && $proto == "6")  echo "selected" ?>><?php echo _("TCP") ?></option>
                            <option value="portprotoudp" <?php if (preg_match("/base_stat_port/", $_SERVER['SCRIPT_NAME']) && $proto == "17") echo "selected" ?>><?php echo _("UDP") ?></option>
                        </select>
                        </div>
                    </td>
                    <td style="padding-left:10px"><input id="group_button" class="small av_b_secondary" type="button" value="<?php echo _("Group") ?>" onclick="go_stats()" style="display:none"/></td>
                </tr>
                </table>
                </td>
                <?php 
                if (preg_match("/base_stat_(uaddr|iplink|alert|sensor|port)/", $_SERVER['SCRIPT_NAME']))
                {
                ?>
                <td class='right'>
                    <button id="actions_link" class="button av_b_secondary">
                        <?php echo _('فعالیت ها') ?> &nbsp;&#x25be;
                    </button>
                </td>
                <?php
                }
                ?>
            </tr>
        </table>

    </td>
</tr>

</table>

</form>

</td>
</tr>
</table>
<?php

/* End of file base_header.php */
/* Location: ./forensics/base_header.php */
