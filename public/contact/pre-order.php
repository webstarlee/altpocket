<?php

/*
  contact form
 */
require_once 'PHPMailer/PHPMailerAutoload.php';
header('Content-Type: application/json');
$mail = new PHPMailer();

$formColor = $_POST['formColor'];
$formQuantity = $_POST['formQuantity'];
$formOs1 = $_POST['formOs1'];
$formFName = $_POST['formFName'];
$formLName = $_POST['formLName'];
$formEmail = $_POST['formEmail'];
$formAddOne = $_POST['formAddOne'];
$formAddTwo = $_POST['formAddTwo'];
$formState = $_POST['formState'];
$formZip = $_POST['formZip'];
$formCountry = $_POST['formCountry'];
$productName = 'Apple Smart Watch';
$productModel = 'Model - BE270';
$productPrice = '$199';

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'p3plcpnl0361.prod.phx3.secureserver.net';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'mutasim@droitlab.com';             // SMTP username
$mail->Password = 'Muta45**sim?';                     // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;

//$mail->setFrom('mutasim@droitlab.com', 'Droitlab');
$mail->setFrom($formEmail, $formFName);
$mail->addAddress('envato@droitlab.com', 'DroitLab');     // Add a recipient
$mail->addReplyTo($formEmail, $formFName);
$mail->isHTML(true);


$mail->Subject = 'Inova Product Pre-Order Query';
$mail->Body    = '<strong>Product Name : </strong>' . $productName .'<br/>';
$mail->Body    .= '<strong>Product Model : </strong>' . $productModel .'<br/>';
$mail->Body    .= '<strong>Product Price : </strong>' . $productPrice .'<br/>';
$mail->Body    .= '<br/><br/><br/>';
$mail->Body    .= '<strong>Client Name : </strong>' . $formFName .' '.$formLName.'<br/>';
$mail->Body    .= '<strong>Client Email : </strong>' . $formEmail .'<br/>';
$mail->Body    .= '<strong>Address One : </strong>' . $formAddOne .'<br/>';
$mail->Body    .= '<strong>Address Two : </strong>' . $formAddTwo .'<br/>';
$mail->Body    .= '<strong>State : </strong>' . $formState .'<br/>';
$mail->Body    .= '<strong>Zip Code : </strong>' . $formZip .'<br/>';
$mail->Body    .= '<strong>Country : </strong>' . $formCountry .'<br/>';
$mail->Body    .= '<br/><br/><br/>';
$mail->Body    .= '<strong>Product Color : </strong>' . $formColor .'<br/>';
$mail->Body    .= '<strong>Product Quantity : </strong>' . $formQuantity .'<br/>';
$mail->Body    .= '<strong>Product Size : </strong>' . $formOs1 .'<br/>';


if(!$mail->send()) {
  $result = array('message_status' => 'no', 'content' => 'There is a problem. Please provide your valid email.');
  echo json_encode($result);
} else {
  $result = array('message_status' => 'ok', 'content' => 'Message has been send successfully!');
  echo json_encode($result);
}
