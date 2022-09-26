<?php

require_once APPPATH."libraries/PHPMailer/Exception.php";
require_once APPPATH."libraries/PHPMailer/PHPMailer.php";
require_once APPPATH."libraries/PHPMailer/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer 
{

	function __construct()
	{
		$this->CI =& get_instance();

	}

    function initMailer(){
        $mail = new PHPMailer();

		$mail->IsSMTP();
		$mail->Host = 'mail.clinical.careequity.com';
		$mail->Port = 465;
		$mail->SMTPAuth = true;
		$mail->Username = 'clinicalrss@clinical.careequity.com';
		$mail->Password = 'M?r;=[_Wq631';
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPDebug  = 1;  
		$mail->SMTPAuth   = TRUE;

        return $mail;
    }

    function sendTestMail(){
        $mail = $this->initMailer();
        
        $mail->From = 'clinicalrss@clinical.careequity.com';
		$mail->FromName = 'Care Equity Clinical tool';

        $mail->IsHTML(true);
		$mail->Subject = "Message from Care Equity Clinical tool";
    	$mail->Body    = "This is test email";
		$mail->AddAddress('kingdeveloper@yahoo.com');

		$mail->addAttachment('searchresults/Test Attachment.pdf');

		if(!$mail->Send()) {
			echo $mail->ErrorInfo;
		}

		echo "success";
    }

	//=============================================================
	function Tpl_Registration($username, $email_verification_link)
	{
        $login_link = base_url('auth/login');  

		$tpl = '<h3>Hi ' .strtoupper($username).'</h3>
            <p>Welcome to the Care Equity Clinical tool!</p>
            <p>Active your account with the link above and start your Care Equity Clinical tool:</p>  
            <p><a href="'.$email_verification_link.'">'.$email_verification_link.'</a></p>

            <br>
            <br>

            <p>Regards, <br> 
            Care Equity Clinical tool <br> 
            </p>
            ';
		return $tpl;		
	}

    function sendRegistrationMail($data){
        $mail = $this->initMailer();

        $mail->From = 'careequityclinicaltool@clinical.careequity.com';
		$mail->FromName = 'Care Equity Clinical tool';
        $mail->IsHTML(true);

		$mail->Subject = "Message from Care Equity Clinical tool";
    	$mail->Body    = '<h3>Hi ' .$data['username'].'</h3>
            <p>Welcome to the Care Equity Clinical tool!</p>
            <p>Active your account with the link above and start your Care Equity Clinical tool:</p>  
            <p><a href="'.$data['email_verification_link'].'">'.$data['email_verification_link'].'</a></p>

            <br>
            <br>

            <p>Regards, <br> 
            Care Equity Clinical tool <br> 
            </p>
        ';

        $mail->AddAddress($data['to']);

		if(!$mail->Send()) {
			return array(
                'success' => false,
                'msg' => $mail->ErrorInfo
            );
		}

        return array(
            'success' => true
        );
    }

	//=============================================================
	function Tpl_PwdResetLink($username, $reset_link)
	{
		$tpl = '<h3>Hi ' .strtoupper($username).'</h3>
            <p>Welcome to the Care Equity Clinical tool!</p>
            <p>We have received a request to reset your password. If you did not initiate this request, you can simply ignore this message and no action will be taken.</p> 
            <p>To reset your password, please click the link below:</p> 
            <p><a href="'.$reset_link.'">'.$reset_link.'</a></p>

            <br>
            <br>

            <p>&#169; 2021 Care Equity Clinical tool - All rights reserved</p>
    ';
		return $tpl;		
	}

	function sendResetPasswordMail($data){
        $mail = $this->initMailer();

        $mail->From = 'careequityclinicaltool@clinical.careequity.com';
		$mail->FromName = 'Care Equity Clinical tool';
        $mail->IsHTML(true);

		$mail->Subject = "Message from Care Equity Clinical tool";
    	$mail->Body    = '<h3>Hi ' .$data['username'].'</h3>
        <p>Welcome to the Care Equity Clinical tool!</p>
        <p>We have received a request to reset your password. If you did not initiate this request, you can simply ignore this message and no action will be taken.</p> 
        <p>To reset your password, please click the link below:</p> 
        <p><a href="'.$data['reset_link'].'">'.$data['reset_link'].'</a></p>

        <br>
        <br>

        <p>&#169; 2021 Care Equity Clinical tool - All rights reserved</p>
        ';

        $mail->AddAddress($data['to']);

		if(!$mail->Send()) {
			return array(
                'success' => false,
                'msg' => $mail->ErrorInfo
            );
		}

        return array(
            'success' => true
        );
    }

}
?>