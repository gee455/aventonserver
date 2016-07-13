<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'Models/config.php';
require_once 'Models/ConDB.php';
require_once 'Models/MPDF56/mpdf.php';

function getOut() {
    echo "<h1>You are on a wrong page, please get back.</h1>";
    exit();
}

function convertToHoursMins($time, $format = '%d:%d') {
    settype($time, 'integer');
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}



if (isset($_REQUEST['apntId'])) {

    $db = new ConDB();

    $getApptDetQry = "select a.distance_in_mts,a.discount,a.coupon_code,a.cc_fee,a.tip_amount,a.parking_fee as parking,a.airport_fee as airport,a.toll_fee as toll,a.meter_fee as meter,a.status,a.appt_lat,a.duration,a.complete_dt,a.appt_long,a.payment_type,a.drop_lat,a.appointment_id,a.drop_long,a.address_line1,a.address_line2,a.drop_addr1,a.drop_addr2,a.created_dt,a.arrive_dt,a.start_dt,a.appointment_dt,a.amount,a.appointment_id,a.last_modified_dt,a.user_device,";
    $getApptDetQry .= "p.first_name as p_fname,p.profile_pic as p_pic,p.last_name as p_lname,p.email as p_email,d.profile_pic as d_pic,d.first_name d_fname,d.last_name d_lname,";
    $getApptDetQry .= "(select wt.price_per_km from workplace_types wt,workplace w,master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as price_per_km,";
    $getApptDetQry .= "(select wt.price_per_min from workplace_types wt,workplace w,master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as price_per_min,";
    $getApptDetQry .= "(select wt.basefare from workplace_types wt,workplace w,master d where w.type_id = wt.type_id and w.workplace_id = d.workplace_id and d.mas_id = a.mas_id) as base_fare,";
    $getApptDetQry .= "(select avg(star_rating) from master_ratings where mas_id = a.mas_id) as avg_rating from appointment a,slave p,master d where a.slave_id = p.slave_id and a.mas_id = d.mas_id and a.appointment_id = '" . $_REQUEST['apntId'] . "'";
//echo $getApptDetQry;
//return false;
    $apptDet = mysql_fetch_assoc(mysql_query($getApptDetQry, $db->conn));
//print_r($apptDet);
//return;
    if ($apptDet['status'] != '9')
        getOut();

    $finalAmount = round((double) $apptDet['meter'] + (float) $apptDet['toll'] + (float) $apptDet['airport'] + (float) $apptDet['parking'], 2);

    $apptDet['tip'] = $apptDet['tip_amount'];

    $duration_in_mts_old = round(abs(strtotime($apptDet['complete_dt']) - strtotime($apptDet['start_dt'])) / 60, 2);

    $duration_in_mts = ((int) $duration_in_mts_old == 0) ? 1 : $duration_in_mts_old;

    $distance_in_mts = $apptDet['duration']; //0

    $dis_in_km = (float) ($distance_in_mts / 1000);

    $speed_in_mts = $dis_in_km / ($duration_in_mts / 60);

    $apptDet['speed_in_mts'] = $speed_in_mts;

    $apptDet['appt_duration'] = $duration_in_mts;

    $apptDet['appt_distance'] = $dis_in_km;

    $apptDet['getStatus'] = 1;

    $pasData = array('first_name' => $apptDet['p_fname'], 'last_name' => $apptDet['p_lname'], 'email' => $apptDet['p_email']);
    $masData = array('firstName' => $apptDet['d_fname'], 'last_name' => $apptDet['d_lname']);

   // $home_url = 'http://107.170.66.211/apps/freetaxi/images/';
    $mpdf = new mPDF('win-1252', 'A4', '', '', 10, 10, 38, 30, 5, 10);



    $mpdf->SetHtmlHeader('<img src="' . $home_url . 'header.png" style="margin-top:15px;height:90px;width:100%;" alt="image here"/>');
    $mpdf->SetHtmlFooter('<div id="footer" style="font-size:12px;height:70px;padding-top:5px;"></div>');

//$mpdf->DefHeaderByName('MyHeader');
//$mpdf->DefHTMLFooterByName('Footer');

    $mpdf->SetDisplayMode('fullpage');

    $mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list


    if ($apptDet['status'] == '9')
        $status = 'Booking completed';
    else if ($apptDet['status'] == '4')
        $status = 'Booking cancelled';


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
                                            Thanks for using <span class="il">Roadyo!</span>!
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

                                            ' . date('F d, Y', strtotime($apptDet['created_dt'])) . ' at ' . date('h:i a', strtotime($apptDet['created_dt'])) . '
                                        </div>

</td></tr><tr style="padding-bottom:10px;"><td>
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                            Trip Date
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:12px">

                                            ' . date('F d, Y', strtotime($apptDet['appointment_dt'])) . ' at ' . date('h:i a', strtotime($apptDet['appointment_dt'])) . '
                                        </div>
</td></tr><tr style="padding-bottom:10px;"><td>
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                            Trip From Location
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:12px">
                                            ' . urldecode($apptDet['address_line1']) . ' ' . urldecode($apptDet['address_line2']) . '
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:2px">
                                            Trip To Location
                                        </div>
                                        <div style="font-family:helvetica,arial,sans-serif;padding-bottom:12px">
                                            ' . urldecode($apptDet['drop_addr1']) . ' ' . urldecode($apptDet['drop_addr2']) . '
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
                                            $' . $apptDet['amount'] . '
                                        </div>
</td></tr></table>

                                    </td>

                                    <td valign="top" width="50%" style="border-collapse:collapse;padding:20px 20px 20px 10px">

                                        

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
                                                            ' . $apptDet['appointment_id'] . '
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
                                                        Roadyo
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
                                                       Meter
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . round((float) $apptDet['meter'], 2) . '
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        Toll fee
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . (float) $apptDet['toll'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        Airport fee
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . (float) $apptDet['airport'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        Parking fee
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . (float) $apptDet['parking'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        Tip
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . (float) $apptDet['tip'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        CC Fee
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . (float) $apptDet['cc_fee'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:3px;padding-bottom:18px;text-align:left;font-weight:bold;border-top:1px solid #959595">
                                                        Charge subtotal
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:3px;padding-bottom:18px;text-align:right;font-weight:bold;border-top:1px solid #959595">
                                                        $' . ((float) $apptDet['meter'] + $apptDet['toll'] + $apptDet['airport'] + $apptDet['parking'] + $apptDet['tip']) . '
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:1px">
                                                        Discounts
                                                    </td>
                                                </tr>
                                                <tr>';
   

    if ($apptDet['coupon_code'] == '') {

        $html .= '                                                <tr>
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
                                                </tr>';
    } else {
        $html .= '                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        Coupon : ' . $apptDet['coupon_code'] . '
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right">
                                                        $' . (float) $apptDet['discount'] . '
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:3px;padding-bottom:18px;text-align:left;font-weight:bold;border-top:1px solid #959595">
                                                        Discount subtotal 
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:3px;padding-bottom:18px;text-align:right;font-weight:bold;border-top:1px solid #959595">
                                                        $'.(float) $apptDet['discount'] .'
                                                    </td>
                                                </tr>';
    }

    $html .= '                                  <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:1px">
                                                        Totals
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left;font-weight:bold">
                                                        Total Fee
                                                    </td>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:right;font-weight:bold">
                                                        $' . ($apptDet['meter'] + $apptDet['toll'] + $apptDet['airport'] + $apptDet['parking'] + $apptDet['tip'] - $apptDet['discount']) . '
                                                    </td>
                                                </tr>
                                                
';
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
                                                        <b>'. bcdiv($apptDet['distance_in_mts'],'1609.344',2) .' Miles</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:1px">
                                                        DURATION
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-bottom:18px;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        <b>' . convertToHoursMins(((int) $apptDet['appt_duration'] == 0) ? 1 : (int) $apptDet['appt_duration'], '%02d hours %02d minutes') . '</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;text-transform:uppercase;font-size:10px;color:#636669;font-weight:normal;padding-bottom:1px">
                                                        AVERAGE SPEED
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-family:helvetica,arial,sans-serif;border-collapse:collapse;padding-top:2px;padding-bottom:2px;text-align:left">
                                                        <b>' . (int) $apptDet['speed_in_mts'] . ' MPH</b>
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
    $mpdf->WriteHTML($pdfHtml);

    $mpdf->Output();

//    echo $html;
} else {
    getOut();
}
?>
