<?php

/**
 * Description of sendAMail
 *
 * @author admin3embed
 *  
 */
class sendAMail {

    public $mail;
    public $host;
    public $inv_id;
    public $appName = APP_NAME;
    public $driverAdmin = "";
    public $superAdmin = "";

    public function __construct($host) {
//        $this->mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
        $this->host = APP_SERVER_HOST;

        $this->superAdmin = $host . "admin/SuperAdminLogin.php";

        $this->mandrill = new Mandrill(MANDRILL_API_KEY);
    }

    public function sendMasWelcomeMail($toMail, $toName) {

        $subject = 'Thank you for registering with ' . $this->appName;

        $body = '<div style="padding:45px 45px 15px">          
  <div style="font-size:20px;font-weight:normal;margin-bottom:30px">
    <strong>Hello ' . ucwords($toName) . '</strong>
  </div>

  <div style="font-size:24px;font-weight:normal;margin-bottom:15px;color:#1fbad6">
    Thank you for registering with ' . $this->appName . '!<br><br>
    One of our representatives will get in touch with you in the next 24 hours to setup your profile and get all the necessary documents.
  </div>

  <table style="width:460px;margin:30px auto 30px;border-spacing:0px;line-height:0px">
    <tbody><tr>
      <td style="border-bottom-width:1px;border-bottom-color:#c0c0c8;border-bottom-style:solid">&nbsp;</td>
      <td style="border-bottom-width:1px;border-bottom-color:#c0c0c8;border-bottom-style:solid">&nbsp;</td>
    </tr>
    <tr>
      <td style="border-top-width:1px;border-top-color:#ffffff;border-top-style:solid">&nbsp;</td>
      <td style="border-top-width:1px;border-top-color:#ffffff;border-top-style:solid">&nbsp;</td>
    </tr>
  </tbody></table>
  <div>Regards,  </div>
  <div>Team ' . $this->appName . '.</div>
  </div>';

        $recipients = array($toMail => $toName);

        return $this->mailFun($recipients, $subject, $body);
    }

    public function sendDiscountCoupon($toMail, $toName, $discountMail) {
        $subject = 'Thanks for registering on ' . $this->appName . '';

        $body = '<div>Hey ' . ucfirst($toName) . ',<br><br>'
                . 'Thank you for signing up with ' . $this->appName . '!<br><br>';
        if ($discountMail['code'] != '') {
            $body .= 'You got a discount coupon for your first ride on ' . $this->appName . '! <br><br>'
                    . 'Use the promo code<br><br>'
                    . '<h3 style="border: 1px solid black;display: inline;padding: 2px 5px;">' . $discountMail['code'] . '</h3>' . '<br><br><br>'
                    . 'To get ' . $discountMail['discountData']['referral_discount'] . ' ' . ($discountMail['discountData']['referral_discount_type'] == '1' ? '%' : $discountMail['discountData']['currency']) . ' discount in your next ride!' . '<br><br>'
                    . 'The promocode can be used anytime before ' . date('jS M Y', strtotime('+30 days', time())) . '<br><br>'
                    . 'Terms & Conditions:<br>'
                    . $discountMail['discountData']['message'] . '<br><br>';
        }
        $body .= 'Use your REFERRAL CODE to refer your friends and family:' . '<br><br>'
                . '<h3 style="border: 1px solid black;display: inline;padding: 2px 5px;">' . $discountMail['refCoupon'] . '</h3>' . '<br><br><br>'
                . 'and get more discounts on ' . $this->appName . '<br><br>'
                . 'Cheers, <br>Team ' . $this->appName
                . '</div>';

        $recipients = array($toMail => $toName);

        return $this->mailFun($recipients, $subject, $body);
    }

    public function discountOnFriendSignup($toMail, $toName, $details) {

        $subject = 'You got a promo code on ' . $this->appName . '';

        $body = '<div>Hey ' . ucfirst($toName) . ',<br><br>'
                . 'Your friend ' . ucfirst($details['uname']) . ' just singed up on ' . $this->appName . ' using your referral code!<br><br>'
                . 'We thank you for referring ' . $this->appName . ' to your friends and family with a discount code for your next ride on ' . $this->appName . '! <br><br>'
                . 'Use the promo code<br><br>'
                . '<h3 style="border: 1px solid black;display: inline;padding: 2px 5px;">' . $details['code'] . '</h3>' . '<br><br><br>'
                . 'To get ' . $details['discountData']['referral_discount'] . ' ' . ($details['discountData']['referral_discount_type'] == '1' ? '%' : $details['discountData']['currency']) . ' discount in your next ride!' . '<br><br>'
                . 'The promocode can be used anytime before ' . date('jS M Y', strtotime('+30 days', time())) . '<br><br>'
                . 'Terms & Conditions:<br>'
                . $details['discountData']['message'] . '<br><br>'
                . 'Use your REFERRAL CODE:' . '<br><br>'
                . '<h3 style="border: 1px solid black;display: inline;padding: 2px 5px;">' . $details['discountData']['coupon_code'] . '</h3>' . '<br><br><br>'
                . 'and get more discounts on ' . $this->appName . '<br><br>'
                . 'Cheers, <br>Team ' . $this->appName
                . '</div>';

        $recipients = array($toMail => ucfirst($toName));

        return $this->mailFun($recipients, $subject, $body);
    }

    public function sendSlvWelcomeMail($toMail, $toName, $args) {
$subject = 'Pound Cabs Registration';
//' . ucwords($toName) . '
        $body = '<div style="padding:45px 45px 15px">          
  <div style="font-size:20px;font-weight:normal;margin-bottom:30px">
    <strong>Dear Passenger,</strong>
  </div>

  <div style="font-size:14px;font-weight:normal;margin-bottom:15px;color:#1fbad6">
    Thank you for registering with Pound Cabs. <br>Your login details are below:
<br><br>
    Username: '.$args['ent_email'].'<br>
    Password: '.$args['ent_password'].'<br><br>
    If you require any further information on our services please do not hesitate to contact us.<br><br>

  <div>Best Regards</div>
  <div>The Pound Cabs Team</div><br>
  <div>Pound Cabs LTD</div>
  <div>Tel: 0161 871 7493</div>
  <div>Email: Support@poundcabs.co.uk</div>
  <div>Web: http://www.poundcabs.co.uk</div>
  <div>HQ Hamill, Clippers Quay, Salford Quays, M50 3XP</div><br><br>
  
  <div style="color: black;font-size:12px;">
    This e-mail message may contain confidential or legally privileged information and is intended only for the use of the intended recipient(s). Any unauthorized disclosure, dissemination, distribution, copying or the taking of any action in reliance on the information herein is prohibited. E-mails are not secure and cannot be guaranteed to be error free as they can be intercepted, amended, or contain viruses. Anyone who communicates with us by e-mail is deemed to have accepted these risks. Company Name is not responsible for errors or omissions in this message and denies any responsibility for any damage arising from the use of e-mail. Any opinion and other statement contained in this message and any attachment are solely those of the author and do not necessarily represent those of the company
    </div>
  </div>';

        $recipients = array($toMail => $toName);

        return $this->mailFun($recipients, $subject, $body,'registration@poundcabs.co.uk');
    }

    public function sendInvoice($masData, $pasData, $apptData) {

        $recipients = array($pasData['email'] => $pasData['first_name'], $masData['email'] => $masData['first_name']);

        $pdf = new InvoiceHtml($this->host);

        $invoice = $pdf->generateInvoice($masData, $pasData, $apptData);

        $subject = "Trip details in " . $this->appName;

        return $this->mailFun($recipients, $subject, $invoice['html'], MANDRILL_FROM_EMAIL_BOOKINGS);
    }

    public function masterSuspended($email, $name) {

        $subject = "Your profile is suspended on " . $this->appName;

        $html = "Hello " . ucwords($name) . ",<br><br>Sorry your profile has been susepnded on " . $this->appName . "!<br><br>If you face any issues, one of our representatives will get in touch with you in the next 24 hours!<br><br>Regards,<br>Team " . $this->appName;

        $recipients = array($email => $name);
        return $this->mailFun($recipients, $subject, $html);
    }

    public function masterActivated($email, $name, $password) {

        $subject = "Your profile is accepted on " . $this->appName;

        $html = "Hello " . ucwords($name) . ",<br><br>Congratulations! You are now a driver with " . $this->appName . "!";
//                . "<br><br>You can download the driver apps on Android and iOS:<br>Android @ https://play.google.com/store/apps/details?id=com.app.driverapp&hl=en<br>iOS @ https://itunes.apple.com/us/app/roadyo-driver/id918849395?mt=8<br><br>";

        $html .= "You can login to your administration panel as well @ " . $this->driverAdmin . " and your login credentials are:<br>";
        $html .= "User: " . $email;
        $html .= "Password: " . $password . "<br><br>";
//        $html .= "You can recover your password by clicking on this link @ password recovery link for drivers ";
        $html .= "If you face any issues, please mail us at ".MANDRILL_FROM_EMAIL.", one of our representatives will get in touch with you with in 24 hours!<br><br>";
        $html .= "Regards,<br>Team " . $this->appName;

        $recipients = array($email => $name);
        return $this->mailFun($recipients, $subject, $html);
    }

    public function forgotPassword($details, $randData) {

        $html = "Hi " . ucwords($details['first_name']) . ",<br><br><div>You requested a password reset. Please visit this link to enter your new password:<br><br><a href='" . $this->host . "resetData.php?data=" . $randData . "' target='_blank'>" . $this->host . "resetData.php?data=" . $randData . "</a></div><br><br>Hope to see you riding soon!<br><br>Love,<br>Team " . $this->appName . "<br>";

        $subject = "Password reset link for " . $this->appName;

        $recipients = array($details['email'] => $details['first_name']);

        return $this->mailFun($recipients, $subject, $html);
    }

    public function passwordChanged($details, $randData) {

        $html = "<div>Hello " . ucwords($details['first_name']) . ",<br><br><div>Your password is changed from admin to: <br> " . $randData . "<br><br>Regards,<br>" . $this->appName . " Team.</div>";

        $subject = $this->appName . " Adminstrator";

        $recipients = array($details['email'] => $details['first_name']);

        return $this->mailFun($recipients, $subject, $html);
    }

    public function adminPasswordChanged($details, $randData) {

        $html = "<div>Hello " . ucwords($details['first_name']) . ",<br><br><div>Your Login creds are changed from admin to: <br> Username: " . $details['first_name'] . "<br>Password: " . $randData . "<br><br>You can login here: " . $this->superAdmin . "<br><br>Regards,<br>" . $this->appName . " Team.</div>";

        $subject = $this->appName . " Adminstrator";

        $recipients = array($details['email'] => $details['first_name']);

        return $this->mailFun($recipients, $subject, $html);
    }

    public function newApptReq($details) {

        $html = "<div>Good news! You have a new ' . $this->appName . ' appointment. <a href = '" . $this->host . "/confirm_appt.php?id=" . $details['apptId'] . "' target='_blank' >Please click here to confirm the appoitment with in one business hour.</a></div><br>";
        $html .= "<table>        <tr>        <td>Passenger:</td>        <td>" . $details['patFirst'] . " " . $details['patLast'] . "</td>    </tr>
    <tr>        <td>With</td>        <td>" . $details['docFirst'] . " " . $details['docLast'] . "</td>    </tr>
    <tr>        <td>When</td>        <td>" . date('l, F d, Y', strtotime($details['apptDt'])) . ' at ' . date('h:i A', strtotime($details['apptDt'])) . "</td>    </tr>
    <tr>        <td>Where</td>        <td>" . $details['addr1'] . '<br>' . $details['addr2'] . "</td>    </tr>
        </table>
<br><br>
<div>Thanks,</div>
<div>The " . $this->appName . " Team.</div>";

        $subject = $details['docFirst'] . " " . $details['docLast'] . " has a new ' . $this->appName . ' appointment to confirm!";

        $recipients = array($details['docEmail'] => $details['docFirst']);

        return $this->mailFun($recipients, $subject, $html);
    }

    public function acceptRejectUser($details) {
        return $this->mailFun($details['users'], $details['subject'], $details['html']);
    }



    
    function mailFun($recipients, $subject, $body, $reply = MANDRILL_FROM_EMAIL) {

        $toemail = $toname = "";
        foreach ($recipients as $email => $name) {

            if ($email != '') {
                $toemail .= $email . ",";
                $toname .= $name . ",";
            }
        }
        try {

            $config = array();

            
            $config['api_key'] = "key-eb2fbb7432506149c63b2edcdd4f9185";

            $config['api_url'] = "https://api.mailgun.net/v3/roadyo.in/messages";

            $message = array();

            $message['from'] = $reply;

            $message['toname'] = rtrim($toname, ',');

            $message['to'] = rtrim($toemail, ',');

            $message['h:Reply-To'] = $reply;

            $message['subject'] = $subject;

            $message['html'] = $body; //file_get_contents("http://www.domain.com/email/html");

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $config['api_url']);

            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            curl_setopt($ch, CURLOPT_USERPWD, "api:{$config['api_key']}");

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $message);

            $result = curl_exec($ch);

            curl_close($ch);

            return $result;
        } catch (Mandrill_Error $e) {
            return array('msg' => $e->getMessage(), 'status' => 'failed', 'flag' => 1);
        }
    }
    
    
}

?>
