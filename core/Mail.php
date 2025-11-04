<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mail
 *
 * @author Felipe Faciroli
 */
class Mail {
    
    function sendMail($to, $subject, $body, $path = false, $arquivo = false, $arquivo2 = false) {
   
        $from = "no-reply@dbheroes.com.br";

        $mail = new PHPMailer();
        $mail->CharSet = "UTF-8";

        $mail->IsSMTP(true); // SMTP
        $mail->SMTPAuth = true;  // SMTP authentication
        $mail->Mailer = "smtp";

        $mail->Host = "dbheroes.com.br"; // GMail SMTP
        $mail->Port = 465;  // SMTP Port
        $mail->Username = "no-reply@dbheroes.com.br";  // SMTP  Username
        $mail->Password = "dbheroes2018";  // SMTP Password
        $mail->SMTPSecure = "ssl";

        if (($path) || ($arquivo)) {
            $mail->AddAttachment($path . "/" . $arquivo);
            if (($arquivo2)) {
                $mail->AddAttachment($path . "/" . $arquivo2);
            }
        }

        $mail->SetFrom($from, 'DB Heroes Game RPG');
        $mail->AddReplyTo($from, 'no-reply');
        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        $address = $to;
        $mail->AddAddress($address, $to);

        if(!$mail->Send()){
            return false;
        } else {
            return true;
        }
    }
}
