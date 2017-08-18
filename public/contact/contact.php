<?php

/*
  contact form
 */
require_once 'PHPMailer/PHPMailerAutoload.php';
header('Content-Type: application/json');
$mail = new PHPMailer();

$fname = $_POST['formfName'];
$lname = $_POST['formlName'];
$email = $_POST['formMail'];
$website = $_POST['formWebsite'];
$mess = $_POST['formMessage'];

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'p3plcpnl0361.prod.phx3.secureserver.net';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'mutasim@droitlab.com';             // SMTP username
$mail->Password = 'Muta45**sim?';                     // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;

//$mail->setFrom('mutasim@droitlab.com', 'Droitlab');
$mail->setFrom($email, $name);
$mail->addAddress('envato@droitlab.com', 'DroitLab');     // Add a recipient
$mail->addReplyTo($email, $name);
$mail->isHTML(true);

$mail->Subject = 'Inova Get in Touch Contact';
$mail->Body    = '<strong>Name : </strong>' . $fname .' '.$lname.'<br/><br/>';
$mail->Body    .= '<strong>Website: </strong>' . $website .'<br/><br/>';
$mail->Body    .= '<strong>Message: </strong>' . $mess .'<br/>';


if(!$mail->send()) {
  /*echo 'Message could not be sent.';
  echo 'Mailer Error: ' . $mail->ErrorInfo;*/
  //$result = array('message_status' => 'no', 'content' => $mail->ErrorInfo);
  $result = array('message_status' => 'no', 'content' => 'There is a problem. Please provide your valid email.');
  echo json_encode($result);
} else {
  $result = array('message_status' => 'ok', 'content' => 'Message has been send successfully!');
  echo json_encode($result);
}
