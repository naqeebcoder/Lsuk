
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>        
</head>
<body>
</body>
</html>
<?php
$ary=unserialize($_REQUEST['email']);
for($i=0;$i<4;$i++){
$x= $ary[$i];
   //print_r (unserialize($_REQUEST['email']));
	$from_add ='Ali.Jutt@gmail.com';
	$to_add = $x; //<-- put your yahoo/gmail email address here
	$subject = "Promotion Email of Travel ";
	$message =$_GET['msg'];

$headers = "From: $from_add \r\n";
$headers .= "Reply-To: $from_add \r\n";
$headers .= "Return-Path: $from_add\r\n";
$headers .= "X-Mailer: PHP \r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";	

if(mail($to_add,$subject,$message,$headers)){}
    
    
}echo "<script>window.close();</script>";
?>



