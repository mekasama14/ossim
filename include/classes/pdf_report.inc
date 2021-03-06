<?php
/**
* pdf_report.inc
*
* File pdf_report.inc is used to:
*   - Generate PDF file for USM Reports
*
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
* @package    ossim-framework\Reports
* @autor      AlienVault INC
* @license    http://www.gnu.org/licenses/gpl-2.0.txt
* @copyright  2003-2006 ossim.net
* @copyright  2007-2013 AlienVault
* @link       https://www.alienvault.com/
*/
define("PDF_PATH","/usr/share/ossim/www/tmp/");

class Pdf_report
{
    private $path;


    private $pathHtml;


    private $pathHtmlAux;


    private $pathDirResourcesHtml;


    private $namePdf;


    private $originalName;


    private $orientation;


    private $paperformat;


    private $name;


    private $errors;


    private $extra_data;


    private $font;


    public function __construct($name, $orientation = "P", $paperformat = "A4", $user = NULL, $data = array())
    {
        $db          = new ossim_db();
        $dbconn      = $db->connect();
        $this->path   = PDF_PATH;

        $this->errors = array();

        $this->font   = 'Helvetica';

        if (!empty($name))
        {
            $this->setName($name);
            $this->setOriginalName($name);

            $this->setNamePdf($this->getName());

            $user = (empty($user)) ? Session::get_session_user() : $user;

            $this->path .= Session::get_secure_id($user)."/".$this->getName()."/";

            if (!is_dir($this->path))
            {
                mkdir($this->path, 0755, TRUE);
            }

            if (!empty($data) && is_array($data))
            {
                if (preg_match("/ENTITY\:(\d+)/", $data['assets'], $fnd))
                {
                    $entity_name         = Acl::get_entity_name($dbconn, $fnd[1]);
                    $data['assets_text'] = "ENTITY: ".$entity_name;
                }
                else
                {
                    $data['assets_text'] = $data['assets'];
                }
                $data_s           = serialize($data);
                $this->extra_data = $data;
                $res              = file_put_contents($this->path."/data_".$this->getName().".ini", $data_s);

                if ($res === FALSE)
                {
                    $this->errors['file_error'] = _("Error! PDF data could not be created");
                }
                else
                {
                    $this->setNamePdf($this->getName().'_'.$this->sanitize($this->extra_data['assets_text']));
                }
            }
            else
            {
                if ($data == TRUE)
                {
                    $path = $this->path."/data_".$this->getName().".ini";

                    if (@file_exists($path))
                    {
                        $data_s = @file_get_contents($path);
                        $data   = @unserialize($data_s);
                        $this->extra_data = (is_array($data)) ? $data : NULL;
                    }
                }
                if (empty($data))
                {
                    $this->extra_data = NULL;
                }
                else
                {
                    $this->setNamePdf($this->getName().'_'.$this->sanitize($this->extra_data['assets_text']));
                }
            }


            $this->set_html_path($this->path.'/'.$this->getName().'.html');
            $this->set_resource_path($this->path);

            $this->orientation = $orientation;
            $this->paperformat = $paperformat;
        }
        else
        {
            $this->errors["empty_name"] = _('Error! Name is empty');
        }
    }


    public function getName()
    {
        return $this->name;
    }


    public function setName($name)
    {
        $this->name = $this->sanitize($name);
    }


    public function getOriginalName()
    {
        return $this->originalName;
    }


    public function setOriginalName($name)
    {
        $this->originalName = $name;
    }


    public function setNamePdf($name)
    {
        $this->namePdf = $this->get_current_date().'-'.$name.'.pdf';
    }


    public function getNamePdf()
    {
        return $this->namePdf;
    }


    public function getpath()
    {
        return $this->path;
    }


    public function getpathHtml()
    {
        return $this->pathHtml;
    }


    public function getExtraData($key)
    {
        return ($key == '') ? $this->extra_data : $this->extra_data[$key];
    }


    public function setExtraData($data)
    {
        return $this->extra_data = $data;
    }


    public function setHtml($html)
    {
        file_put_contents($this->pathHtml, $html);
    }




    public function getHTML() {
        if($this->html_exists()) {
            header('Content-type: text/html');
            echo $this->formathtml();
            exit();
        } else {
            Av_exception::throw_error(Av_exception::USER_ERROR, _('No data available'));
        }
    }

    private function getBackgroundUrl($html) {
        preg_match('|backimg="([^"]*)"|',$html,$match);
        return $match[1];
    }

    public function formathtml() {
            $html = file_get_contents($this->pathHtml);
            $url = $this->getBackgroundUrl($html);            
            $html = "<style>
                body{
                    margin: 0 auto;
                    width:900px;
                }
                .w100:nth-child(3) {
                    text-align: center;
                    background-image: url({$url});
                    background-repeat: no-repeat;
                    background-position: center top;
                }
            </style>".$html;
            return str_replace(
                array(
                    "Page [[page_cu]] / [[page_nb]]",
                    "[[date_y]]-[[date_m]]-[[date_d]] [[date_h]]:[[date_i]]:[[date_s]]",
                ),
                array(
                    "",
                    date("Y-m-d H:i:s"),
                ),$html);
    }


    public function getPdf($output='navigator')
    {
        if($this->html_exists())
        {
            session_write_close();
            $this->convert_entities();
            require_once "classes/html2pdf/html2pdf.class.php";

            $html2pdf = new HTML2PDF($this->orientation, $this->paperformat, 'en', TRUE, 'UTF-8', array(0,0,0,0));
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->setTestTdInOnePage(FALSE);
            $html2pdf->setTestIsImage(FALSE);

            $pdf_content = file_get_contents($this->pathHtmlAux);
            $html2pdf->setDefaultFont($this->font);

            //$html2pdf->setModeDebug();
            $html2pdf->WriteHTML($pdf_content);

            if($output == 'navigator')
            {
                $html2pdf->Output($this->getNamePdf(),'D');
            }
            elseif($output == 'server')
            {
                $html2pdf->Output($this->pathDirResourcesHtml.$this->getNamePdf(), 'F');

                return $this->pathDirResourcesHtml.$this->getNamePdf();
            }
        }
        else
        {
            Av_exception::throw_error(Av_exception::USER_ERROR, _('No data available'));
        }
    }

   public function sendPdfEmail($title, $email, $pdfName = NULL, $dirUser = NULL, $body = '') {
       $path = '/usr/share/ossim/www/tmp/scheduler/'.$dirUser.'/'.$pdfName.'.pdf';
       $filename = $this->getNamePdf();
       return $this->pdfEmail($title, $email, $pdfName, $dirUser, $body, $path, $filename);
   }

   public function sendHTMLEmail($title, $email) {
       $arr = explode("/",$this->pathHtml);
       $filename = array_pop($arr);
       $new = str_replace(".html",".html__",$this->pathHtml);
       $html = $this->formathtml();
       $url = $this->getBackgroundUrl($html);
       $url = 'data:image/png;base64,' . base64_encode(file_get_contents($url));
       preg_match_all("|src=['\"]([\.][^\"']+)['\"]|",$html,$images);
       $images = array_unique($images[1]);
       $encoded = array();
       foreach ($images as $value) {
           preg_match("/\.[a-zA-Z]+$/",$value,$res);
           $encoded[] = 'data:image/'.$res[0].';base64,' . base64_encode(file_get_contents($value));
       }
       $html = str_replace($images,$encoded,$html);
       $html = "
           <style>
               .left.w50 {
                   display: none;
               }
           </style>
           <table style='margin: 0 auto; width:900px;text-align: center;background-image: url({$url});background-repeat: no-repeat;background-position: center top;'><tr><td>
               {$html}
           </td></tr></table>";
       file_put_contents($new,$html);
       return $this->pdfEmail($title, $email, true, null, '', $new, $filename);
       unlink($new);
   }

    public function pdfEmail($title, $email, $pdfName, $dirUser, $body, $path, $file_name)
    {
        if($title == '')
        {
            $subject = _('Report (')._('No title')._(') - OSSIM');
        }
        else
        {
            $subject = $title;
        }

        if($body == "")
        {
            $body ='<html>
                        <head>
                    <title>'.$subject.'</title>
                    </head>
                    <body>
                    </body>
                </html>';
        }

        $attachments = array();

        if($pdfName == NULL)
        {
            try
            {
                // Get PDF
                $attachments[] = array(
                    "path" => $this->getPdf('server'),
                    "name" => $file_name
               );
            }
            catch (Exception $e)
            {
                echo $e->getMessage(); //Boring error messages from anything else!
            }
        }
        else
        {
            //It exists
            $attachments[] = array(
                "path" => $path,
                "name" => $file_name
           );
        }


        $db   = new ossim_db();
        $conn = $db->connect();

        $res = Util::send_email($conn, $email, $subject, $body, $attachments);
        $db->close();

        return $res;
    }


    public function getLinkPdf($extra_data = FALSE, $overhead = false)
    {
        $current_url = $_SERVER[SCRIPT_NAME].'?pdf=true&extra_data=true&'.$_SERVER[QUERY_STRING];
	$format = "email";
        $download_pdf = "";
        if ($overhead) {
            $format = "emailhtml";
            $download_pdf = "&html=true";
            $overhead_fn = '
		var loading = $("#loading_overhead");
                if (!loading.length) {
			$(\'<div id="loading_overhead">
                            <div class="overhead_content">
               	                <div class="overhead_header">'._("The PDF generation process for this report may take some time, regarding the number of assets selected. Please, choose one of the following options:").'</div>
                                    <div>
                                        <ul>
                                            <li><input type="radio" value="1" id="overhead1" checked="checked" name="overhead" /><label for="overhead1">'._('Continue report with HTML output only').'</label></li>
                                            <li><input type="radio" value="2" id="overhead2" name="overhead" /><label for="overhead2">'._('Select smaller number of assets and try again').'</label></li>
                                            <li><input type="radio" value="3" id="overhead3" name="overhead" /><label for="overhead3">'._('Schedule report to run later today (Viewable in Report history)').'</label></li>
                                        </ul>
                                   </div>
                                <div class="overhead_footer"><input id="overhead-continue" type="button" value="Continue"/> <input type="button" name="" value="Quit" onclick="overhead_quit();"/></div>
                            </div>
	                 </div>\').insertAfter("#loading");
                } else {
			loading.show();
                }
                $("body").off("click","#overhead-continue").on("click","#overhead-continue",function() {callback(); overhead_quit();});
	    ';
            $overhead_fn = str_replace("\n","",$overhead_fn);
        } else {
            $overhead_fn = 'callback(); return true;';
	}
        $html='
            <script language="javascript">
		function prelaunch(callback) {
                    '.$overhead_fn.'
		}
		function overhead_next(callback) {
			var val = $("#loading_overhead [name=overhead]:checked").val();
			if (val == 1) {
				callback();
			} else if (val == 2) {
				overhead_quit();
				$("[name=wr_options]").find("a:nth-child(2)").click();
			} else if (val == 3) {
				top.location.href = "/ossim#reports/usm_reports/scheduler";
			}
		}

		function overhead_quit() {
			$("#loading_overhead").hide();
		}

                function emailValidate(id)
                {
                    if(/([\w+\.-]*@[\w+\.-]*[\.\w{2,3}])+/.test($("#email").val()))
                    {
			prelaunch(function() {
	                        var id_form="#email_form";
                                $(id_form).attr("action","wizard_email.php?extra_data='.$extra_data.'&name='.base64_encode($this->getName()).'&format='.$format.'");
                                GB_show_post("'.Util::js_entities(("Send PDF by e-mail")).'", id_form, "350", "600");
			});
                    }
                    else
                    {
                        alert("'._("Please, add a valid e-mail").'");
                        $(id).focus();
                    }

                    return false;
                }

                function toggleDivEmail(){
                    $("#iemail").toggle();
                }

             </script>
               <table width="100%" class="transparent" align="center" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="50%" class="nobborder" style="text-align:right;" nowrap>
                        <a class="sbutton" href="#" onclick="if (!prelaunch(function() {window.open(\''.$current_url.$download_pdf.'\');})) return false;" target="_blank" title="'._("Generate PDF").'">
                            <img src="../pixmaps/pdf_button.png" align="absmiddle"> '._("Download PDF").'
                        </a>

                        </td>
                        <td  width="50%" class="nobborder" style="text-align:left;padding-left:10px;" nowrap>
                        <a class="sbutton" href="javascript:;" onclick="toggleDivEmail();" title="'._("Send PDF by e-mail").'"><img src="../pixmaps/emailButton.png" align="absmiddle"> '._("Send by e-mail").'</a>
                        </td>
                   </tr>
               </table>
               <div id="iemail" style="display:none;">
               <form method="POST" action="#" id="email_form">
                    <div style="margin-top:5px">
                        <input id="email" name="email" type="text" style="width:120px">
                        <input type="button" class="vsbutton small" onclick="emailValidate(\'#email\');" value="'._("Send").'"/>
                    </div>
                </form>
                </div>
            ';

        return $html;
    }



    /*************************************************
     ************** Private functions ****************
     *************************************************/

    private function set_html_path($url)
    {
        $this->pathHtml    = $url;
        $this->pathHtmlAux = $url."_";
    }


    private function set_resource_path($url)
    {
        $this->pathDirResourcesHtml = $url;
    }


    private function html_exists()
    {
        if (file_exists($this->pathHtml))
        {
            return TRUE;
        }

        return FALSE;
    }


    private function get_current_date()
    {
        $date = getdate();

        return $date['year'].$date['yday'].$date['hours'].$date['minutes'].$date['seconds'];
    }


    private function sanitize($data)
    {
        $str_to_replace = array(" ", ":", ".", "#", "&", "/");

        return str_replace($str_to_replace, "_", $data);
    }


    private function convert_entities()
    {
        $file = @file($this->pathHtml);

        for ($i = 0; $i < count($file); $i++)
        {
            if (preg_match('/&#(\d{4,5});/',$file[$i]) && !preg_match('/\/image/',$file[$i]))
            {
                $file[$i]   = mb_convert_encoding($file[$i],'UTF-8','HTML-ENTITIES');
                $this->font = 'ArialUnicodeMS';
            }
            else
            {
                $file[$i] = mb_convert_encoding($file[$i],'UTF-8','ISO-8859-1');
            }

            $file[$i] = str_replace("##br##","<br/>",$file[$i]);
        }

        file_put_contents($this->pathHtmlAux, $file);

        unset($file);
    }
}

/* End of file pdf_report.inc */
/* Location: ../include/classes/pdf_report.inc */
