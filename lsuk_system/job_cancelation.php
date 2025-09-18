<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

?>
<?php 
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 

?>
  <link rel="stylesheet" type="text/css" href="css/layout.css" />
<title>Booking Cancelation</title>
<br /><br /><br />
<div align="center">
  <span style="font-weight:bold; color:#09F;">Record ID: <?php echo $_GET['job_id']; ?></span><br /><br />

  <form action="" method="post">
<br/>
<label>Job Cancelled By</label><br/>
<select id="order_cancelledby" name="order_cancelledby"onChange="myFunction()" required style="height:30px; width:250px;">
                   
                    <option value="">--Select--</option>
                    <option>Client</option><option>LSUK</option>
                  </select><br /><br/>
                  
                  <label>Reason / Remarks</label><br/>
    <textarea name="order_cancel_remarks" rows="5" id="order_cancel_remarks" style="width:250px;" required="required"></textarea>
<br/><br/>
<!--<label>Message for Interpreter</label><br/>
    <textarea name="order_cancel_message_interp" rows="5" id="order_cancel_message_interp" style="width:250px;" required="required"></textarea>
<br/><br/>-->
Are you sure you want to cancel this booking ?<br><br><input type="submit" name="yes" value="Yes" />&nbsp;&nbsp;<input type="submit" name="no" value="No" />
</form>
</div>

<?php 
if(isset($_POST['yes']))
{
	include 'db.php'; 
	include 'class.php';
	$table = $_GET['table'];
	$order_cancelledby=@$_POST['order_cancelledby'];
	@$order_cancel_remarks=$_POST['order_cancel_remarks'];
	$job_id = $_GET['job_id'];
    $client_email='0'; 
    $int_email='0';
	$query="SELECT intrpName FROM $table
	   where $table.id=$job_id";			
	$result = mysqli_query($con,$query);
	$row = mysqli_fetch_array($result);
	$intrpName=$row['intrpName'];
	if(empty($intrpName)){
		$acttObj->editFun($table,$job_id,'order_cancel_flag',1);
		$acttObj->editFun($table,$job_id,'order_cancelledby',$order_cancelledby);
		$acttObj->editFun($table,$job_id,'order_cancel_remarks',$order_cancel_remarks);
		$acttObj->editFun($table,$job_id,'order_cancled_bystaff',$_SESSION['UserName']);

		echo "<script>alert('Order Successfully Cancelled!');</script>";
		echo "<script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>";
		echo "<script>window.close();</script>";
	}

	if(!empty($intrpName)){
		$query="SELECT $table.*,interpreter_reg.name,interpreter_reg.email,comp_reg.name as orgzName FROM $table
	   JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
		INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv
	    where $table.id=$job_id";			

		$result = mysqli_query($con,$query);

		$row = mysqli_fetch_array($result);
		$email=$row['email'];
		$source=$row['source'];
		$orgRef=$row['orgRef'];
		if($table=='interpreter' || $table=='telephone')
		{
			$assignDate=$misc->dated($row['assignDate']); 
			$assignTime=$row['assignTime'];
			$orgzName=$row['orgzName'];
			if($table=='interpreter')
			{
				$buildingName=$row['buildingName'];
				$street=$row['street'];
				$assignCity=$row['assignCity'];
				$postCode=$row['postCode'];
			}
		}
		else
		{
			$asignDate=$row['asignDate'];
		}

		$orgContact=$row['orgContact'];
		$inchEmail=$row['inchEmail'];
		$remrks=$row['remrks'];
		$name=$row['name'];
		$acttObj->editFun($table,$job_id,'order_cancel_flag',1);
		$acttObj->editFun($table,$job_id,'order_cancelledby',$order_cancelledby);
		$acttObj->editFun($table,$job_id,'order_cancel_remarks',$order_cancel_remarks);
		$acttObj->editFun($table,$job_id,'order_cancled_bystaff',$_SESSION['UserName']);
	
		//..............data for below table.......\\
	
		 $query="SELECT $table.*,comp_reg.name as orgzName FROM $table
			INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv
	    	where $table.id=$job_id";			
		$result = mysqli_query($con,$query);
		$row = mysqli_fetch_array($result);
		$source=$row['source'];
		$aloct_by=$row['aloct_by'];$target=$row['target'];$orgRef=$row['orgRef'];
		$inchEmail=$row['inchEmail'];$orgContact=$row['orgContact'];$I_Comments=$row['I_Comments'];
		$bookinType=$row['bookinType'];
		if($table=='interpreter')
		{
			$dbs_checked=$row['dbs_checked'];
			if($dbs_checked==0)
			{
				$dbs_checked='No';
			}
			else
			{
				$dbs_checked='Yes';
			}
		}
		if($table=='interpreter' || $table=='telephone')
		{ 
			$gender =$row['gender'];$inchNo=$row['inchNo'];$line1=$row['line1'];
			$inchRoad=$row['inchRoad'];$inchCity=$row['inchCity'];$inchPcode=$row['inchPcode'];

			$assignDate=$misc->dated($row['assignDate']); 
			$assignTime=$row['assignTime'];$assignDur=$row['assignDur'];
			$orgzName=$row['orgzName'];if($table=='interpreter')
			{
				$buildingName=$row['buildingName'];$street=$row['street'];
				$assignCity=$row['assignCity'];$postCode=$row['postCode'];
			}
			$assignIssue=$row['assignIssue'];$inchPerson=$row['inchPerson'];
			$nameRef=$row['nameRef'];$remrks=$row['remrks'];
			if($table=='telephone')
			{
				$comunic=$row['comunic'];
				$ClientContact=$row['contactNo'];
				$noClient=$row['noClient'];
			}
		}
		else
		{ 
			$asignDate=$row['asignDate'];$deliveryType=$row['deliveryType'];
			$transType=$row['transType'];$deliverDate=$row['deliverDate'];$docType=$row['docType'];$trans_detail=$row['trans_detail'];
		}
	
		//.........................................................................................................\\
	
		if($table=='translation')
		{
			$from_add = "info@lsuk.org"; 
			$to_add = $inchEmail;
			$subject = "Cancellation of ". $source." translation project on ". $asignDate." - Client Ref/Name (if any) ".$orgRef."";
			$message = "<p>Dear ".$orgContact.",</p>
	
<p>We are writing to let you know that we are canceling the below Job. </p>

<style type='text/css'>
table.myTable { 
  border-collapse: collapse; 
  }
table.myTable td, 
table.myTable th { 
  border: 1px solid yellowgreen;
  padding: 5px; 
  }
</style>

<table class='myTable'>
<tr>
<td>Booking Reference Number</td>
<td>".$orgRef."</td>
</tr>
<tr>
<td>Source Language</td>
<td>".$source."</td>
</tr>
<tr>
<td>Target Language</td>
<td>".$target."</td>
</tr>
<tr>
<td>Document Type</td>
<td>" . $acttObj->read_specific("tc_title","trans_cat","tc_id=".$docType)['tc_title'] . "</td>
</tr>
<tr>
<td>Translation Type(s)</td>
<td>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR '<br>') as tt_title","trans_types","tt_id IN (".$trans_detail.")")['tt_title'] . "</td>
</tr>
<tr>
<td>Translation Category</td>
<td>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR '<br>') as td_title","trans_dropdown","td_id IN (".$transType.")")['td_title'] . "</td>
</tr>
<tr>
<td>Delivery Type</td>
<td>".$deliveryType."</td>
</tr>
<tr>
<td>Delivery Date</td>
<td>".$misc->dated($deliverDate)."</td>
</tr>
<tr>
<td>Notes (if any) </td>
<td>".$I_Comments."</td>
</tr>
<tr>
<td>Reason / Remarks</td>
<td>".$order_cancel_remarks."</td>
</tr>
</table>


<p>Kind Regards</p>

<p>Translation Admin Team
Language Services UK Limited
Acredited by Association of Translation Comoanies(ATC)
Registered in England  and Wales 7760366
Phone: 01173290610     07915177068 - 0333 7005785
Fax: 0333 800 5785
Email: INFO@LSUK.ORG</p>


<p>LSUK Limited provide professional, accurate and timely Interpreting and Certified Translation services for many languages. We are  bridging the gaps to connect words / worlds for less. Please contact us for more details. Bookings can be made Online, over the Phone, via Email or Fax.
The information in this e-mail is confidential. The contents may not be disclosed or used by anyone other than the addressee. If you are not the intended recipient, please notify the sender immediately by reply e-mail and delete this message instantly. LSUK Limited cannot accept any responsibility for the accuracy or completeness of this message as it has been transmitted over a public network. For more information on our products and services please visit WWW.LSUK.ORG</p>";
try {
        $mail->SMTPDebug = 0;
        //$mail->isSMTP(); 
        //$mailer->Host = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($from_add, 'LSUK');
        $mail->addAddress($to_add);
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        if($mail->send()){
            $mail->ClearAllRecipients();
            //$mail->addAddress('translation@lsuk.org');
            $mail->addAddress('imran.lsukltd@gmail.com');
            $mail->addReplyTo($from_add, 'LSUK');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $mail->ClearAllRecipients();
            $client_email='1';
        }else{
            $client_email='0';
        }
    } catch (Exception $e) { ?>
    <script>alert('<?php echo "Mailer Library Error!"; ?>');</script>
    <?php }
//...........................for interpreter.............................
	$to_add = $email;
	$subject = "Cancellation of ". $source." translation project on ". $asignDate." ";
	$message = "<p>Dear ".$name.",</p>
	
<p>We are writing to let you know that we are canceling the below Job. </p>
<style type='text/css'>
table.myTable { 
  border-collapse: collapse; 
  }
table.myTable td, 
table.myTable th { 
  border: 1px solid yellowgreen;
  padding: 5px; 
  }
</style>

<table class='myTable'>
<tr>
<td>Source Language</td>
<td>".$source."</td>
</tr>
<tr>
<td>Target Language</td>
<td>".$target."</td>
</tr>
<tr>
<td>Document Type</td>
<td>" . $acttObj->read_specific("tc_title","trans_cat","tc_id=".$docType)['tc_title'] . "</td>
</tr>
<tr>
<td>Translation Type(s)</td>
<td>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR '<br>') as tt_title","trans_types","tt_id IN (".$trans_detail.")")['tt_title'] . "</td>
</tr>
<tr>
<td>Translation Category</td>
<td>" . $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR '<br>') as td_title","trans_dropdown","td_id IN (".$transType.")")['td_title'] . "</td>
</tr>
<tr>
<td>Delivery Date</td>
<td>".$deliverDate."</td>
</tr>
<tr>
<td>Document Type</td>
<td>".$deliveryType."</td>
</tr>
<tr>
<td>Reason / Remarks</td>
<td>".$order_cancel_remarks."</td>
</tr>
</table>



<p>Kind Regards</p>

<p>Translation Admin Team
Language Services UK Limited
Acredited by Association of Translation Comoanies(ATC)
Registered in England  and Wales 7760366
Phone: 01173290610     07915177068 - 0333 7005785
Fax: 0333 800 5785
Email: info@lsuk.org</p>


<p>LSUK Limited provide professional, accurate and timely Interpreting and Translation services for many languages. We are  bridging the gaps to connect words / worlds for less. Please contact us for more details. Bookings can be made Online, over the Phone, via Email or Fax.
The information in this e-mail is confidential. The contents may not be disclosed or used by anyone other than the addressee. If you are not the intended recipient, please notify the sender immediately by reply e-mail and delete this message instantly. LSUK Limited cannot accept any responsibility for the accuracy or completeness of this message as it has been transmitted over a public network. For more information on our products and services please visit WWW.LSUK.ORG</p>";
try {
        $mail->SMTPDebug = 0;
        //$mail->isSMTP(); 
        //$mailer->Host = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($from_add, 'LSUK');
        $mail->addAddress($to_add);
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        if($mail->send()){
            $mail->ClearAllRecipients();
            //$mail->addAddress('translation@lsuk.org');
            $mail->addAddress('imran.lsukltd@gmail.com');
            $mail->addReplyTo($from_add, 'LSUK');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $mail->ClearAllRecipients();
            $int_email='1';
        }else{
            $int_email='0';
        }
    } catch (Exception $e) { ?>
    <script>alert('<?php echo "Mailer Library Error!"; ?>');</script>
    <?php }
    
    if($client_email=='1' && $int_email=='1'){
        echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
    }else{
        echo "<script>alert('Failed to send email to client and interpreter!');</script>";
    }
}

if($table=='telephone'){
	$from_add = "info@lsuk.org"; 
	$to_add = $inchEmail;
	$subject = "Cancellation of ".$source." telephone interpreter on ". $assignDate." at ".$assignTime." - Client Ref/Name (if any) ".$orgRef."";
	$message = "<p>Dear ".$orgContact.",</p>
<p>	We are writing to let you know that we are canceling the below Job. </p>
<style type='text/css'>
table.myTable { 
  border-collapse: collapse; 
  }
table.myTable td, 
table.myTable th { 
  border: 1px solid yellowgreen;
  padding: 5px; 
  }
</style>

<table class='myTable'>
<tr>
<td>Assignment Type</td>
<td>".$comunic."</td>
</tr>
<tr>
<td>Case Name or Reference Number (if any)</td>
<td>".$orgRef."</td>
</tr>
<tr>
<td>Source Language</td>
<td>".$source."</td>
</tr>
<td>Target Language</td>
<td>".$target."</td>
</tr>
<tr>
<td>Assignment Date</td>
<td>".$assignDate."</td>
</tr>
<tr>
<td>Assignment Time</td>
<td>".$assignTime."</td>
</tr>
<tr>
<td>Assignment Duration (in minutes) e.g. Requested</td>
<td>".$assignDur."</td>
</tr>
<tr>
<td>Booking Requested by</td>
<td>".$inchPerson."</td>
</tr>
<tr>
<td>Interpreter Contact</td>
<td>".$orgContact."</td>
</tr>
<tr>
<td>Number of the Client to be Called</td>
<td>".$ClientContact."</td>
</tr>
<tr>
<td>Client Contact Number</td>
<td>".$noClient."</td>
</tr>
<tr>
<td>Interpreter Name</td>
<td>".$name."</td>
</tr>
<tr>
<td>Interpreter Gender Requested</td>
<td>".$gender."</td>
</tr>
<tr>
<td>Booking Type</td>
<td>".$bookinType."</td>
</tr>
<tr>
<td>Notes</td>
<td>".$I_Comments."</td>
</tr>
<tr>
<td>Reason / Remarks</td>
<td>".$order_cancel_remarks."</td>
</tr>
</table>

<p>Kind Regards</p>

<p>Interpreting Admin Team
Language Services UK Limited
Registered in England  and Wales 7760366
Phone: 01173290610     07915177068 - 0333 7005785
Fax: 0333 800 5785
Email: INFO@LSUK.ORG</p>

<p>LSUK Limited provide professional, accurate and timely Interpreting and Translation services for many languages. We are  bridging the gaps to connect words / worlds for less. Please contact us for more details. Bookings can be made Online, over the Phone, via Email or Fax.
The information in this e-mail is confidential. The contents may not be disclosed or used by anyone other than the addressee. If you are not the intended recipient, please notify the sender immediately by reply e-mail and delete this message instantly. LSUK Limited cannot accept any responsibility for the accuracy or completeness of this message as it has been transmitted over a public network. For more information on our products and services please visit WWW.LSUK.ORG</p>";
try {
        $mail->SMTPDebug = 0;
        //$mail->isSMTP(); 
        //$mailer->Host = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($from_add, 'LSUK');
        $mail->addAddress($to_add);
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        if($mail->send()){
            $mail->ClearAllRecipients();
            //$mail->addAddress('telephone@lsuk.org');
            $mail->addAddress('imran.lsukltd@gmail.com');
            $mail->addReplyTo($from_add, 'LSUK');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $mail->ClearAllRecipients();
            $client_email='1';
        }else{
            $client_email='0';
        }
    } catch (Exception $e) { ?>
    <script>alert('<?php echo "Mailer Library Error!"; ?>');</script>
    <?php }
//..............................for interpreter .....................//\\\///....

	$from_add = "info@lsuk.org"; 
	$to_add = $email;
	$subject = "Cancellation of ". $source." telephone interpreting session on ".$assignDate." at ".$assignTime."";
	$message = "<p>Dear ".$name.",</p>
	
<p>We are writing to let you know that we are canceling the below Job. </p>
<style type='text/css'>
table.myTable { 
  border-collapse: collapse; 
  }
table.myTable td, 
table.myTable th { 
  border: 1px solid yellowgreen;
  padding: 5px; 
  }
</style>

<table class='myTable'>
<tr>
<td>Assignment Type</td>
<td>".$comunic."</td>
</tr>
<tr>
<td>Source Language</td>
<td>".$source."</td>
</tr>
<tr>
<td>Target Language</td>
<td>".$target."</td>
</tr>
<tr>
<td>Assignment Date</td>
<td>".$assignDate."</td>
<tr>
<td>Assignment Time</td>
<td>".$assignTime."</td>
</tr>
<tr>
<td>Assignment Duration (in minutes) e.g. Requested</td>
<td>".$assignDur."</td>
</tr>
<tr>
<td>Assignment Issue</td>
<td>".$assignIssue."</td>
</tr>
<tr>
<td>Report to</td>
<td>".$inchPerson."</td>
</tr>
<tr>
<td>Case Worker</td>
<td>".$orgContact."</td>
</tr>
<tr>
<td>Notes</td>
<td>".$remrks."</td>
</tr>
<tr>
<td>Reason / Remarks</td>
<td>".$order_cancel_remarks."</td>
</tr>

</table>


<p>Kind Regards </p>

<p>Interpreting Admin Team
Language Services UK Limited
Registered in England  and Wales 7760366
Phone: 01173290610     07915177068 - 0333 7005785
Fax: 0333 800 5785
Email: INFO@LSUK.ORG</p>

<p>LSUK Limited provide professional, accurate and timely Interpreting and Translation services for many languages. We are  bridging the gaps to connect words / worlds for less. Please contact us for more details. Bookings can be made Online, over the Phone, via Email or Fax.
The information in this e-mail is confidential. The contents may not be disclosed or used by anyone other than the addressee. If you are not the intended recipient, please notify the sender immediately by reply e-mail and delete this message instantly. LSUK Limited cannot accept any responsibility for the accuracy or completeness of this message as it has been transmitted over a public network. For more information on our products and services please visit WWW.LSUK.ORG</p>";

try {
        $mail->SMTPDebug = 0;
        //$mail->isSMTP(); 
        //$mailer->Host = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($from_add, 'LSUK');
        $mail->addAddress($to_add);
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        if($mail->send()){
            $mail->ClearAllRecipients();
            //$mail->addAddress('telephone@lsuk.org');
            $mail->addAddress('imran.lsukltd@gmail.com');
            $mail->addReplyTo($from_add, 'LSUK');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $mail->ClearAllRecipients();
            $int_email='1';
        }else{
            $int_email='0';
        }
    } catch (Exception $e) { ?>
    <script>alert('<?php echo "Mailer Library Error!"; ?>');</script>
    <?php }
    
    if($client_email=='1' && $int_email=='1'){
        echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
    }else{
        echo "<script>alert('Failed to send email to client and interpreter!');</script>";
    }
}

if($table=='interpreter'){
	$from_add = "info@lsuk.org"; 
	$to_add = $inchEmail;
	$subject = "Cancellation of Interpreting session for ".$source." language on ". $assignDate." at [".$assignTime." - Client Ref/Name (if any) ". $orgRef."";
	$message = "<p>Dear ".$orgContact.",</p>
	
<p>We are writing to let you know that we are canceling the below Job. </p>
<style type='text/css'>
table.myTable { 
  border-collapse: collapse;
  }
table.myTable td, 
table.myTable th { 
  border: 1px solid yellowgreen;
  padding: 5px; 
  }
</style>
<table class='myTable'>
<tr>
<td>Assignment Type</td>
<td>Face to Face Interpreting Assignment</td>
</tr>
<tr>
<td>Case Name or File Reference Number (if any)</td>
<td>".$orgRef."</td>
</tr>
<tr>
<td>Source Language</td>
<td>".$source."</td>
</tr>
<tr>
<td>Target Language</td>
<td>".$target."</td>
</tr>
<tr>
<td>Assignment Date</td>
<td>".$assignDate."</td>
</tr>
<tr>
<td>Assignment Time</td>
<td>".$assignTime."</td>
</tr>
<tr>
<td>DBS Interpreter Required ?</td>
<td>".$dbs_checked."</td>
</tr>
<tr>
<td>Assignment Duration (in hours) e.g. Requested</td>
<td>".$assignDur."</td>
</tr>
<tr>
<td>Assignment Location</td>
<td>".$buildingName.' '.$street.' '.$assignCity.' '.$postCode."</td>
</tr>
<tr>
<td>Interpreter Name</td>
<td>".$name."</td>
</tr>
<tr>
<td>Interpreter Gender</td>
<td>".$gender."</td>
</tr>
<tr>
<td>Interpreter Contact</td>
<td>".$orgContact."</td>
</tr>
<tr>
<td>Booking Requested By</td>
<td>".$inchPerson."</td>
</tr>
<tr>
<td>Booking Type</td>
<td>".$bookinType."</td>
</tr>
<tr>
<td>Notes (if any)</td>
<td>".$I_Comments."</td>
</tr>
<tr>
<td>Reason / Remarks</td>
<td>".$order_cancel_remarks."</td>
</tr>

</table>

<p>Kind Regards</p>

<p>Interpreting Admin Team
Language Services UK Limited
Registered in England  and Wales 7760366
Phone: 01173290610     07915177068 - 0333 7005785
Fax: 0333 800 5785
Email: info@lsuk.org</p>


<p>LSUK Limited provide professional, accurate and timely Interpreting and Translation services for many languages. We are  bridging the gaps to connect words / worlds for less. Please contact us for more details. Bookings can be made Online, over the Phone, via Email or Fax.
The information in this e-mail is confidential. The contents may not be disclosed or used by anyone other than the addressee. If you are not the intended recipient, please notify the sender immediately by reply e-mail and delete this message instantly. LSUK Limited cannot accept any responsibility for the accuracy or completeness of this message as it has been transmitted over a public network. For more information on our products and services please visit WWW.LSUK.ORG</p>";
try {
        $mail->SMTPDebug = 0;
        //$mail->isSMTP(); 
        //$mailer->Host = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($from_add, 'LSUK');
        $mail->addAddress($to_add);
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        if($mail->send()){
            $mail->ClearAllRecipients();
            //$mail->addAddress('interpreting@lsuk.org');
            $mail->addAddress('imran.lsukltd@gmail.com');
            $mail->addReplyTo($from_add, 'LSUK');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $mail->ClearAllRecipients();
            $client_email='1';
        }else{
            $client_email='0';
        }
    } catch (Exception $e) { ?>
    <script>alert('<?php echo "Mailer Library Error!"; ?>');</script>
    <?php }
//...................for interpreter ..................

	$from_add = "info@lsuk.org"; 
	$to_add = $email;
	$subject = "Cancellation of ". $source." interpreting session on ". $assignDate." at ".$assignTime."";
	$message = "<p>Dear ".$name.",</p>
	

<p>We are writing to let you know that we are canceling the below Job. </p>
<style type='text/css'>
table.myTable { 
  border-collapse: collapse; 
  }
table.myTable td, 
table.myTable th { 
  border: 1px solid yellowgreen;
  padding: 5px; 
  }
</style>

<table class='myTable'>
<tr>
<td>Source Language</td>
<td>".$source."</td>
</tr>
<tr>
<td>Target Language</td>
<td>".$target."</td>
</tr>
<tr>
<td>Assignment Date</td>
<td>".$assignDate."</td>
</tr>
<tr>
<td>Assignment Time</td>
<td>".$assignTime."</td>
</tr>
<tr>
<td>Assignment Duration (in hours)</td>
<td>".$assignDur."</td>
</tr>
<tr>
<td>Assignment Location</td>
<td>".$buildingName.' '.$street.' '.$assignCity.' '.$postCode."</td>
</tr>
<tr>
<td>Assignment Type</td>
<td>Face to Face Interpreting Assignment</td>
</tr>
<tr>
<td>Assignment Issue</td>
<td>".$assignIssue."</td>
</tr>
<tr>
<td>Report to</td>
<td>".$inchPerson."</td>
</tr>
<tr>
<td>Case Worker or Person Incharge</td>
<td>".$orgContact."</td>
</tr>
<tr>
<td>Client Name</td>
<td>".$orgRef."</td>
</tr>
<tr>
<td>Notes</td>
<td>".$remrks."</td>
</tr>
<tr>
<td>Reason / Remarks</td>
<td>".$order_cancel_remarks."</td>
</tr>

</table>


<p>Kind Regards</p>

<p>Interpreting Admin Team
Language Services UK Limited
Registered in England  and Wales 7760366
Phone: 01173290610     07915177068 - 0333 7005785
Fax: 0333 800 5785
Email: info@lsuk.org</p>


<p>LSUK Limited provide professional, accurate and timely Interpreting and Translation services for many languages. We are  bridging the gaps to connect words / worlds for less. Please contact us for more details. Bookings can be made Online, over the Phone, via Email or Fax.
The information in this e-mail is confidential. The contents may not be disclosed or used by anyone other than the addressee. If you are not the intended recipient, please notify the sender immediately by reply e-mail and delete this message instantly. LSUK Limited cannot accept any responsibility for the accuracy or completeness of this message as it has been transmitted over a public network. For more information on our products and services please visit WWW.LSUK.ORG</p>";

try {
        $mail->SMTPDebug = 0;
        //$mail->isSMTP(); 
        //$mailer->Host = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($from_add, 'LSUK');
        $mail->addAddress($to_add);
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        if($mail->send()){
            $mail->ClearAllRecipients();
            //$mail->addAddress('interpreting@lsuk.org');
            $mail->addAddress('imran.lsukltd@gmail.com');
            $mail->addReplyTo($from_add, 'LSUK');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->send();
            $mail->ClearAllRecipients();
            $int_email='1';
        }else{
            $int_email='0';
        }
    } catch (Exception $e) { ?>
    <script>alert('<?php echo "Mailer Library Error!"; ?>');</script>
    <?php }
    
    if($client_email=='1' && $int_email=='1'){
        echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
    }else{
        echo "<script>alert('Failed to send email to client and interpreter!');</script>";
    }
 }
} ?>
<script>
window.close();
window.onunload = refreshParent;
function refreshParent(){
	window.opener.location.reload();
}
</script>
<?php }
if(isset($_POST['no']))
{
echo "<script>window.close();</script>";
}
?>