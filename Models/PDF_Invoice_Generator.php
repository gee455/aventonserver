<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Invoice_Generator
 *
 * @author admin3embed
 */
class PDF_Invoice_Generator {

    public $mpdf;
    public $host;
    public $appName = "PocketCabs";

    public function __construct($host) {
        $this->host = $host;
        $home_url = $host . 'images/';
        $this->mpdf = new mPDF('win-1252', 'A4', '', '', 10, 10, 38, 30, 5, 10);



        $this->mpdf->SetHtmlHeader('<img src="' . $home_url . 'header.png" style="margin-top:15px;height:90px;width:100%;" alt="image here"/>');
        $this->mpdf->SetHtmlFooter('<div id="footer" style="font-size:12px;height:70px;padding-top:5px;"></div>');

//$mpdf->DefHeaderByName('MyHeader');
//$mpdf->DefHTMLFooterByName('Footer');

        $this->mpdf->SetDisplayMode('fullpage');

        $this->mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list
    }

    public function generateInvoice($masData, $pasData, $apptData) {

        if ($apptData['status'] == '7')
            $status = 'Appointment completed';
        else if ($apptData['status'] == '4')
            $status = 'Appointment cancelled';

        if ($apptData['invoice_id'] == '') {
            $invoice_id = $this->getFileName();
            $pdf = $invoice_id . '.pdf';
            $file_name_path = 'invoice/' . $pdf;
        } else {
            $invoice_id = $apptData['invoice_id'];
            $pdf = $invoice_id . '.pdf';
            $file_name_path = 'invoice/' . $pdf;
        }
        $html = '

<div style="position: absolute;    z-index: 999999;    text-align: center;    /* Solution part I. */    display: table;">
    <div style="width: 100%;    height: 100%;   display: table-cell;    vertical-align: middle;">


        <table border="0" cellpadding="0" cellspacing="0" style="background-color:#e2e2e2;margin:20px 0 0 0;padding:0">
            <tbody><tr>
                    <td align="center" valign="top" style="border-collapse:collapse">

                        <table border="0" cellpadding="10" cellspacing="0" width="720" style="background-color:white;">
                            <tbody><tr>
                                    <td valign="top" width="50%" style="border-collapse:collapse;padding:20px 10px 20px 20px">

<table><tr style="padding-bottom:10px;"><td>
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:10px;font-weight:bold;font-size:14px">
                                            Thanks for using <span class="il">' . $this->appName . '!</span>!
                                        </div>
</td></tr><tr style="padding-bottom:10px;"><td>
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                            Billed To
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:12px">
' . ucfirst($pasData['first_name']) . ' ' . ucfirst($pasData['last_name']) . '
                                        </div>
</td></tr><tr style="padding-bottom:10px;"><td>
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                            Trip Request Date
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:12px">

                                            ' . date('F d, Y', strtotime($apptData['created_dt'])) . ' at ' . date('h:i a', strtotime($apptData['created_dt'])) . '
                                        </div>

</td></tr><tr style="padding-bottom:10px;"><td>
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                            Trip Date
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:12px">

                                            ' . date('F d, Y', strtotime($apptData['appointment_dt'])) . ' at ' . date('h:i a', strtotime($apptData['appointment_dt'])) . '
                                        </div>
</td></tr><tr style="padding-bottom:10px;"><td>
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                            Trip From Location
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:12px">
                                            ' . urldecode($apptData['address_line1']) . ' ' . urldecode($apptData['address_line2']) . '
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                            Trip To Location
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:12px">
                                            ' . urldecode($apptData['drop_addr1']) . ' ' . urldecode($apptData['drop_addr2']) . '
                                        </div>
</td></tr><tr style="padding-bottom:10px;"><td>
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                            Payment
                                        </div>
                                        <div style="padding-bottom:12px">
                                            <img src="appimages/stripe-logo-black.png" width="21" height="15" align="top" style="border:0;min-height:15px;width:21px">
                                             - <a>' . $pasData['email'] . '</a>
                                        </div>
                                   </td></tr><tr style="padding-bottom:10px;"><td>   
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                            Status
                                        </div>
                                        <div style="padding-bottom:12px">
                                            ' . $status . '
                                        </div>
</td></tr><tr style="padding-bottom:10px;"><td>
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:0px">
                                            Amount Charged
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:12px;font-weight:bold;font-size:30px">
                                            $' . $apptData['final_amount'] . '
                                        </div>
</td></tr></table>

                                    </td>

                                    <td valign="top" width="50%" style="border-collapse:collapse;padding:20px 20px 20px 10px">

                                        <img width="268" height="268" style="border:1px solid #959595;min-height:268px;width:268px">

                                        <table width="270" cellpadding="0" cellspacing="0" style="margin-bottom:12px">
                                            <tbody>
                                                <tr>
                                                    <td width="52" valign="top" style="border-collapse:collapse">
                                                        
                                                    </td>
                                                    <td width="*" valign="middle" style="border-collapse:collapse">
                                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                                            Invoice Id
                                                        </div>
                                                        <div style="font-family:helvetica,arial,sans-serif">
                                                            ' . $invoice_id . '
                                                        </div>
                                                    </td>                                            
                                                </tr>
                                                <tr>
                                                    <td width="52" valign="top" style="border-collapse:collapse">
                                                        
                                                    </td>
                                                    <td width="*" valign="middle" style="border-collapse:collapse">
                                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                                            Driver
                                                        </div>
                                                        <div style="font-family:helvetica,arial,sans-serif">
                                                            ' . ucfirst($masData['firstName']) . ' ' . ucfirst($masData['last_name']) . '
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                </tr>
                                                <tr>
                                                </tr>
                                                <tr>
                                                    <td width="52">&nbsp;</td>
                                                    <td width="*" style="font-size:11px;font-family:helvetica,arial,sans-serif">
                                                        Receipt issued on behalf of:
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="52">&nbsp;</td>
                                                    <td width="*" style="font-size:11px;font-family:helvetica,arial,sans-serif">
                                                        ' . $this->appName . '
                                                    </td>
                                                </tr>

                                            </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>

                        <table border="0" cellpadding="10" cellspacing="0" width="720" style="background-color:white">
                            <tbody><tr>
                                    <td valign="top" style="border-collapse:collapse;padding:20px 10px 20px 20px">
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:10px;font-weight:bold;font-size:14px">
                                            Fee Breakdown
                                        </div>

                                        <table width="268" cellpadding="0" cellspacing="0" border="0">
                                            <colgroup><col width="80%">
                                                <col width="*">


                                            </colgroup><tbody><tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:1px">
                                                        Charges
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        Base fare
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . round($apptData['amount'], 2) . '
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        Toll fee
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . $apptData['toll'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        Airport fee
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . $apptData['airport'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        Parking fee
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . $apptData['parking'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:3px;padding-bottom:18px;text-align:left;font-weight:bold;border-top:1px solid #959595">
                                                        Charge subtotal
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:3px;padding-bottom:18px;text-align:right;font-weight:bold;border-top:1px solid #959595">
                                                        $' . $apptData['final_amount'] . '
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:1px">
                                                        Discounts
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        No discounts available.
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        ($00.00)
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:3px;padding-bottom:18px;text-align:left;font-weight:bold;border-top:1px solid #959595">
                                                        Discount subtotal
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:3px;padding-bottom:18px;text-align:right;font-weight:bold;border-top:1px solid #959595">
                                                        ($00.00)
                                                    </td>
                                                </tr>



                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:1px">
                                                        Totals
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left;font-weight:bold">
                                                        Total Fee
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right;font-weight:bold">
                                                        $' . $apptData['final_amount'] . '
                                                    </td>
                                                </tr>
                                                
';

        /*
         * <tr>
          <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
          Meter fee
          </td>
          <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
          $' . $apptData['meter'] . '
          </td>
          </tr>
         */
        $createdPdfHtml = '
            <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:1px">
                                                        Totals
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left;font-weight:bold">
                                                        Download In PDF
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right;font-weight:bold">
                                                        <a href="' . $this->host . $file_name_path . '" target="_blank">here</a>
                                                    </td>
                                                </tr>';
        $remHtml = '
          </tbody></table>


                                    </td>
                                    
                                    <td valign="top" style="border-collapse:collapse;padding:20px 10px 20px 20px">
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:10px;font-weight:bold;font-size:14px">
                                            Trip Statistics
                                        </div>

                                        <table width="268" cellpadding="0" cellspacing="0" border="0">
                                            <colgroup><col width="80%">
                                                <col width="*">


                                            </colgroup><tbody>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        DISTANCE
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:3px;padding-bottom:18px;text-align:left;font-weight:bold;">
                                                        <b>' . round($apptData['appt_distance'], 2) . ' KM</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:1px">
                                                        DURATION
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-bottom:18px;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        <b>' . $this->convertToHoursMins(((int) $apptData['appt_duration'] == 0) ? 1 : (int) $apptData['appt_duration'], '%02d hours %02d minutes') . '</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:1px">
                                                        AVERAGE SPEED
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        <b>' . (int) $apptData['speed_in_mts'] . ' Kmph</b>
                                                    </td>
                                                </tr>
                                           </tbody></table>

                                    </td>


                                </tr>


                                <tr>
                                    <td colspan="2" style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:center">
                                        <span><font color="#888888">
                                            </font></span><span><font color="#888888">
                                            </font></span><span><font color="#888888">
                                            </font></span><span><font color="#888888"></font></span><span><font color="#888888"></font></span><span class="HOEnZb"><font color="#888888"></font></span><span class="HOEnZb"><font color="#888888"></font></span><table style="width:300px;margin:30px auto 0px;border-spacing:0px;line-height:0px">

                                            <tbody>
                                                <tr>
                                                    <td style="border-bottom-width:1px;border-bottom-color:#c0c0c8;border-bottom-style:solid">
                                                        &nbsp;
                                                    </td>
                                                    <td align="center" rowspan="2" valign="middle" style="vertical-align:middle;text-transform:uppercase;white-space:nowrap;width:10%;text-align:center;font-weight:bold;font-size:11px;color:#1fbad6;padding:0 10px">


                                                    </td><td style="border-bottom-width:1px;border-bottom-color:#c0c0c8;border-bottom-style:solid">
                                                        &nbsp;
                                                    </td>
                                                </tr><tr>
                                                    <td style="border-top-width:1px;border-top-color:#ffffff;border-top-style:solid">
                                                        &nbsp;
                                                    </td>
                                                    <td style="border-top-width:1px;border-top-color:#ffffff;border-top-style:solid">
                                                        &nbsp;
                                                    </td>
                                                </tr>
                                            </tbody></table><span class="HOEnZb"><font color="#888888"><span><font color="#888888"><span><font color="#888888">
                                                                <table style="width:295px;margin:20px auto 15px;border-spacing:0px;line-height:0px">
                                                                </table>
                                                            </font>
                                                        </span>
                                                    </font>
                                                </span>
                                            </font>
                                        </span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>

                        </table>
                    </td>
                </tr>
            </tbody>

        </table>

    </div>
</div>';

        $pdfHtml = $html . $remHtml;
        $mailHtml = $html . $createdPdfHtml . $remHtml;
        $this->mpdf->WriteHTML($pdfHtml);
        $this->mpdf->Output($file_name_path, 'F');
        return array('file' => $pdf, 'html' => $mailHtml, 'inv_id' => $invoice_id);
    }

    public function convertToHoursMins($time, $format = '%d:%d') {
        settype($time, 'integer');
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    public function getFileName() {

        $inv_id = rand(1000, 100000);
        $randFile = $inv_id . '.pdf';

        $path = 'invoice/' . $randFile;

        if (file_exists($path)) {
            return $this->getFileName();
        }
        return $inv_id;
    }

}

?>
