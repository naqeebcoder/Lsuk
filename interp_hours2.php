<?php if(session_id() == '' || !isset($_SESSION)){session_start();}
error_reporting(0);
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
?> 
<?php include'source/db.php'; include'source/class.php';
 $table='interpreter';$update_id= @$_GET['update_id'];
$query="SELECT * FROM $table where id=$update_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$assignDate=$row['assignDate'];$assignTime=$row['assignTime'];$assignDur=$row['assignDur'];
$input_time = date($assignDate.'' .substr($assignTime,0,5));
$endTime = date("Y-m-d H:i",strtotime("+$assignDur minutes", strtotime($input_time)));
$first_time=$row['wt_tm']!='1001-01-01 00:00:00'?$row['wt_tm']:$row['st_tm'];
$last_time=$row['fn_tm'];
$date1 = date_create($first_time);
$date2 = date_create($last_time);
$diff = date_diff($date1,$date2);
$new_hour = round($diff->i/60,2);
$diff_time=(strtotime($last_time)-strtotime($first_time))/60;
if($diff_time>60){
    $hours=$diff_time / 60;
    if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
        $mins=$diff_time % 60;
        if($mins==00){
            $get_dur2=sprintf("%2d $hr",$hours);  
        }else{
            if($mins>0 && $mins<=15){
                $mins=15;
            $get_dur2=sprintf("%2d $hr %02d minutes",$hours,$mins);  
            }
            if($mins>15 && $mins<=30){
                $mins=30;
            $get_dur2=sprintf("%2d $hr %02d minutes",$hours,$mins);  
            }
            if($mins>30 && $mins<=45){
                $mins=45;
            $get_dur2=sprintf("%2d $hr %02d minutes",$hours,$mins);  
            }
            if($mins>45 && $mins<=60){
                $hours++;
                $hr="hours";
            $get_dur2=sprintf("%2d $hr",$hours);  
            }
        }
    }else if($diff_time==60){
        $get_dur2="1 Hour";
    }else{
        if($diff_time==0){
            $get_dur2="0 minutes";
        }
        if($diff_time>0 && $diff_time<=15){
            $get_dur2="15 minutes";
        }
        if($diff_time>15 && $diff_time<=30){
            $get_dur2="30 minutes";
        }
        if($diff_time>30 && $diff_time<=45){
            $get_dur2="45 minutes";
        }
        if($diff_time>45 && $diff_time<=60){
            $get_dur2="1 hour";
        }
    }
   //echo $get_dur2;
    
$intrpName=$row['intrpName'];$bookinType=$row['bookinType'];$hoursWorkd=$row['hoursWorkd'];$chargInterp=$row['chargInterp'];$rateHour=$row['rateHour'];$travelMile=$row['travelMile'];$rateMile=$row['rateMile'];$chargeTravel=$row['chargeTravel'];$travelCost=$row['travelCost'];$otherCost=$row['otherCost'];$travelTimeHour=$row['travelTimeHour'];$travelTimeRate=$row['travelTimeRate'];$chargeTravelTime=$row['chargeTravelTime'];$dueDate=$row['dueDate'];$tAmount=$row['tAmount'];
$admnchargs=$row['admnchargs'];$deduction=$row['deduction'];$total_charges_interp=$row['total_charges_interp'];$time_sheet=$row['time_sheet'];$wt_tm=$row['wt_tm'];$st_tm=$row['st_tm'];$fn_tm=$row['fn_tm'];
$interp_rph=$acttObj->unique_data('interpreter_reg','rph','id',$intrpName) ;
$interp_email=$acttObj->unique_data('interpreter_reg','email','id',$intrpName) ; 
$valid_check_q=$acttObj->unique_dataAnd($table,'id','intrpName',$_SESSION['web_userId'],'id',$update_id);
$valid_check=$valid_check_q!=''?'yes':'no';
if($valid_check=='no'){
    echo '<script>window.location.href="index.php";</script>';
}
if(date('Y-m-d H:i',strtotime($assignDate.' '.$assignTime))>date('Y-m-d H:i')){
    $problem_hours=1;
    $problem_msg='This job is not completed yet! Thank you';
}else if($hoursWorkd>0){
    $problem_hours=1;
    $problem_msg='Hours for this job already updated! Thank you';
}else if($row['deleted_flag']==1 || $row['order_cancel_flag']==1 || $row['orderCancelatoin']==1 || $row['intrp_salary_comit']==1){
    $problem_hours=1;
    $problem_msg='This job is in processing mode! Thank you';
}else{
    $problem_hours=0;
    $problem_msg='';
}?>
<!DOCTYPE HTML>
<html class="no-js">
<head>
<?php include'source/header.php'; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <style>
      .timer{font-weight: bold;}#html_text,.f18{font-size:18px;}
      .funkyradio div {
  clear: both;
  overflow: hidden;
  margin-top: -30px;
}

.funkyradio label {
  width: 100%;
  border-radius: 3px;
  border: 1px solid #D1D3D4;
  font-weight: normal;
}

.funkyradio input[type="radio"]:empty,
.funkyradio input[type="checkbox"]:empty {
  display: none;
}

.funkyradio input[type="radio"]:empty ~ label,
.funkyradio input[type="checkbox"]:empty ~ label {
  position: relative;
  line-height: 2.5em;
  text-indent: 3.25em;
  margin-top: 2em;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
}

.funkyradio input[type="radio"]:empty ~ label:before,
.funkyradio input[type="checkbox"]:empty ~ label:before {
  position: absolute;
  display: block;
  top: 0;
  bottom: 0;
  left: 0;
  content: '';
  width: 2.5em;
  background: #D1D3D4;
  border-radius: 3px 0 0 3px;
}

.funkyradio input[type="radio"]:hover:not(:checked) ~ label,
.funkyradio input[type="checkbox"]:hover:not(:checked) ~ label {
  color: #888;
}

.funkyradio input[type="radio"]:hover:not(:checked) ~ label:before,
.funkyradio input[type="checkbox"]:hover:not(:checked) ~ label:before {
  content: '\2714';
  text-indent: .1em;
  color: #C2C2C2;
}

.funkyradio input[type="radio"]:checked ~ label,
.funkyradio input[type="checkbox"]:checked ~ label {
  color: #777;
}

.funkyradio input[type="radio"]:checked ~ label:before,
.funkyradio input[type="checkbox"]:checked ~ label:before {
  content: '\2714';
  text-indent: .1em;
  color: #333;
  background-color: #ccc;
}

.funkyradio input[type="radio"]:focus ~ label:before,
.funkyradio input[type="checkbox"]:focus ~ label:before {
  box-shadow: 0 0 0 3px #999;
}

.funkyradio-default input[type="radio"]:checked ~ label:before,
.funkyradio-default input[type="checkbox"]:checked ~ label:before {
  color: #333;
  background-color: #ccc;
}


.funkyradio-success input[type="radio"]:checked ~ label:before,
.funkyradio-success input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #5cb85c;
}



.funkyradio-warning input[type="radio"]:checked ~ label:before,
.funkyradio-warning input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #f0ad4e;
}


  </style>
</head>

<body class="boxed">
<!-- begin container -->
<div id="wrap">
	<!-- begin header -->
<?php include'source/top_nav.php';
if($problem_hours==1){?>
    <center><br><br><h3><?php echo isset($problem_msg) && !empty($problem_msg)?$problem_msg:''; ?></h3>
						<br><br><a class="button" href="time_sheet_interp.php"><i class="glyphicon glyphicon-arrow-left"></i> Go Back</a></center>
<?php }else{
if(isset($_POST['submit']) && $time_sheet=='' && $_FILES["time_sheet"]["name"]!= NULL){
        //php mailer used at top
try {
    $to_add=$interp_email;
    //$to_add = "waqarecp1992@gmail.com";
    $from_add = "info@lsuk.org";
    $mail->SMTPDebug = 0;
    //$mail->isSMTP(); 
    //$mailer->Host = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'LangServ786';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom($from_add, 'LSUK Timehseet Confirmation');
    $mail->addAddress($to_add);
    //$mail->addAddress('waqarecp1992@gmail.com');
    $mail->addReplyTo($from_add, 'LSUK');
    $mail->isHTML(true);
    $mail->Subject = 'Confirmation of timesheet upload for interpreting assignment';
    $mail->Body    = 'Dear Linguist!<br>You have successfully uploaded your timesheet for Job.<br>Thank you<br>Best Regards<br>LSUK Limited';
    if($mail->send()){
    $mail->ClearAllRecipients();
    $sent=1; ?>
<script>alert('Expenses have been successfuly updated.');
// window.location.href="time_sheet_interp.php";
</script>
<?php }else{?>
<script>alert("Failed to submit your record!");</script>
<?php }
} catch (Exception $e) {?>
<script>alert("Message could not be sent! Mailer library error.");</script>
<?php }
}else{ 
$sent=0; 
}
if(isset($_POST['submit'])){
    if(!empty($_POST['hoursWorkd']) && $_POST['hoursWorkd']!=0){
    $data0=$_POST['hoursWorkd'];$acttObj->editFun($table,$update_id,'hoursWorkd',$data0);
    $data1=$_POST['rateHour']; $acttObj->editFun($table,$update_id,'rateHour',$data1);
    $data2=$_POST['chargInterp']; $acttObj->editFun($table,$update_id,'chargInterp',$data2);
    $data3=$_POST['travelTimeHour']; $acttObj->editFun($table,$update_id,'travelTimeHour',$data3);
    $data4=$_POST['travelTimeRate']; $acttObj->editFun($table,$update_id,'travelTimeRate',$data4);
    $data5=$_POST['chargeTravelTime']; $acttObj->editFun($table,$update_id,'chargeTravelTime',$data5);
    $data6=$_POST['travelMile']; $acttObj->editFun($table,$update_id,'travelMile',$data6);
    $data7=$_POST['rateMile']; $acttObj->editFun($table,$update_id,'rateMile',$data7);
    $data8=$_POST['chargeTravel']; $acttObj->editFun($table,$update_id,'chargeTravel',$data8);
    $data9=$_POST['travelCost']; $acttObj->editFun($table,$update_id,'travelCost',$data9);
    $data10=0.50; $acttObj->editFun($table,$update_id,'admnchargs',$data10);
    $data11=$_POST['otherCost']; $acttObj->editFun($table,$update_id,'otherCost',$data11);
    $data12=$_POST['deduction']; $acttObj->editFun($table,$update_id,'deduction',$data12);
    $data13=$_POST['totalChages']; $acttObj->editFun($table,$update_id,'total_charges_interp',$data13);
    if($_FILES["time_sheet"]["name"]!= NULL){
        error_reporting(0);
        if($time_sheet==''){
            $picName=$acttObj->upload_file("time_sheet_interp",$_FILES["time_sheet"]["name"],$_FILES["time_sheet"]["type"],$_FILES["time_sheet"]["tmp_name"],round(microtime(true)));
    	    $acttObj->editFun($table,$update_id,'time_sheet',$picName);
        }else{
            if(unlink('file_folder/time_sheet_interp/'.$time_sheet)){
                $picName=$acttObj->upload_file("time_sheet_interp",$_FILES["time_sheet"]["name"],$_FILES["time_sheet"]["type"],$_FILES["time_sheet"]["tmp_name"],round(microtime(true)));
                $acttObj->editFun($table,$update_id,'time_sheet',$picName);
            } 
        }
    }
    $acttObj->editFun($table,$update_id,'hrsubmited','Self');
    $acttObj->editFun($table,$update_id,'interp_hr_date',date("Y-m-d"));
    if($sent==0){ ?>
    <script>alert("Expenses have been successfuly updated.");
    // window.location.href="time_sheet_interp.php";
    </script>
    <?php }
}else{
    echo "<script>alert('Kindly update hours worked value !');</script>";
}
}
?>
    <!-- begin content -->
    <section id="content" class="container clearfix" style="border-top: 2px solid;">
        <h3 class="text-center f18">Upload Face to Face Assignment Expenses</h3>
        <style>#all_action .toggle-on,#all_action .toggle-off{font-size:24px;}</style>
		<center>
		    <div class="col-md-6">
		    <table class="table table-bordered">
		        <thead class="bg-info">
		            <caption><b>Expected data for this Assignment</b></caption>
		        </thead>
		        <tbody>
		            <tr>
		                <th>Assignment Start Time</th>
		                <td><?php echo $assignDate.' '.substr($assignTime,0,5); ?></td>
		            </tr>
		            <tr>
		                <th>Expected End Time</th>
		                <td><?php echo $endTime; ?></td>
		            </tr>
		            <tr>
		                <th>Expected Duration</th>
		                <td><?php if($assignDur>60){
                      $hours=$assignDur / 60;
                      if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
                      $mins=$assignDur % 60;
                        if($mins==00){
                            $get_dur=sprintf("%2d $hr",$hours);  
                        }else{
                            $get_dur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
                        }
                    }else if($assignDur==60){
                        $get_dur="1 Hour";
                    }else{
                        $get_dur=$assignDur." minutes";
                    } echo $get_dur; ?></td>
		            </tr>
		        </tbody>
		    </table>
		    </div>
			 <div class="col-md-6" id="all_action">
			     <table class="table table-bordered">
			         <thead class="bg-info"><caption><b>Working data for this Assignment</b></caption>
			         </thead>
			         <tbody class="append_data">
			             <?php if($row['wt_tm']!='1001-01-01 00:00:00'){?>
			             <tr>
			                 <td>Assignment waiting time from </td>
			                 <td><span class="timer"><?php echo $row['wt_tm']; ?></span></td>
			             </tr>
			             <?php } ?>
			             <?php if($row['st_tm']!='1001-01-01 00:00:00'){?>
			             <tr>
			                 <td>Assignment starting time from </td>
			                 <td><span class="timer"><?php echo $row['st_tm']; ?></span></td>
			                 </tr>
			             <?php } ?>
			             <?php if($row['fn_tm']!='1001-01-01 00:00:00'){?>
			             <tr>
			                 <td>Assignment finished on  </td>
			                 <td><span class="timer"><?php echo $row['fn_tm']; ?></span></td>
			                 </tr>
			             <?php } ?>
			         </tbody>
			     </table>
			 <?php if($row['wt_tm']=='1001-01-01 00:00:00' && $row['st_tm']=='1001-01-01 00:00:00' && $row['fn_tm']=='1001-01-01 00:00:00'){?>
			    <h4 class="text-success">Do you want to start this assignment now?</h4>
			    <div class="funkyradio col-md-12" style="display: inline-flex;">
                    <div class="funkyradio-success col-md-6">
                        <input type="radio" name="radio" id="radio3" onchange="if(this.checked) {$('#div_wait_start').removeClass('hidden');$('.label_waiting').removeClass('hidden');$('.label_starting').removeClass('hidden');}else{$('#div_wait_start').addClass('hidden');}"/>
                        <label for="radio3" class="label_success"><b>Yes, start now.</b></label>
                    </div>
                    <div class="funkyradio-warning col-md-6">
                        <input type="radio" name="radio" id="radio5" onchange="if(this.checked){$('#div_wait_start').addClass('hidden');}"/>
                        <label for="radio5" class="label_warning"><b>No, not yet !</b></label>
                    </div>
                </div>
			 <?php } ?>
				</div>
		<div id="div_wait_start" class="<?php if($row['wt_tm']!='1001-01-01 00:00:00' && $row['st_tm']!='1001-01-01 00:00:00'){ echo 'hidden';}?> col-md-12">
                  <h4 id="html_text" class="<?php if($row['fn_tm']!='1001-01-01 00:00:00'){ echo 'hidden';}?>">Select your assignment status</h4>
                  <?php if($row['wt_tm']=='1001-01-01 00:00:00' && $row['st_tm']=='1001-01-01 00:00:00'){?>
                  <div class="radio-inline ri label_waiting">
                      <label><input name="assign_status" type="radio" value="0" onclick="if(confirm('Are you sure To start waiting time for this assignment ?')){wait_start('<?php echo $update_id; ?>','<?php echo $table; ?>','wait_time');}"/>
                  <span class="label label-warning" style="font-size:18px;">Start Waiting Now <i class="fa fa-question"></i></span></label>
                  </div>
                  <?php } ?>
                  <?php if($row['st_tm']=='1001-01-01 00:00:00' && $row['fn_tm']=='1001-01-01 00:00:00' ){?>
                  <div class="radio-inline ri label_starting">
                      <label><input type="radio" name="assign_status" value="1" onclick="if(confirm('Are you Sure To Start Assignment ?')){wait_start('<?php echo $update_id; ?>','<?php echo $table; ?>','start_time');}"/>
                  <span class="label label-success" style="font-size:18px;">Start Assignment <i class="fa fa-check-circle"></i></span></label>
                  </div>
                  <?php } ?>
				<button type="button" name="btn_finish" class="<?php if($row['wt_tm']!='1001-01-01 00:00:00' && $row['st_tm']!='1001-01-01 00:00:00' && $row['fn_tm']!='1001-01-01 00:00:00'){ echo 'hidden';}?> btn btn-lg btn-warning btn_finish" onclick="if(confirm('Are you Sure To finish your assignment ?')){wait_start('<?php echo $update_id; ?>','<?php echo $table; ?>','finish_time');}">Finish Assignment <i class="fa fa-exclamation"></i></button>
		</div></center>
		<div id="div_all_area" class="<?php if($row['fn_tm']=='1001-01-01 00:00:00'){ echo 'hidden';}?>">
			<form id="first_form" action="#" method="post" enctype="multipart/form-data">
		<center><h4 class="col-md-12"><label style="padding:12px;" class="label label-success"><i class="fa fa-check-circle" style="font-size:1.5em;"></i> 0.50 has been added to your pay for online timesheet submission</label></h4></center>
        <div class="row">
						<div class="form-group col-md-4">
						  <label class="input">Hours Worked</label>
      <input class="form-control" name="hoursWorkd" type="text" placeholder=''  id="hoursWorkd" oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $hoursWorkd!=0?$hoursworkd:$new_hour ;?>"/>
						</div>
                  <div class="form-group col-md-4">
                         <label class="input">Rate Per Hour</label>
      <input class="form-control" name="rateHour" type="text" placeholder=''  id="rateHour" oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php if($rateHour!=0){echo $rateHour ;}else{echo $interp_rph;} ?>"/>
                    </div>
						<div class="form-group col-md-4">
							<label class="input">Charge for Interpreting Time <i class="fa fa-question-circle" title="Minimum 1 hour , additional time in incremental units example 1 hour 5 minutes is 1.25 , 1 hour 20 minutes is 1.50 hour,  1 hour 35 minutes is 1.75 and 1 hour 50 minutes is 2 hours"></i></label>
      <input class="form-control bg-info" name="chargInterp" type="text" placeholder=''  id="chargInterp" readonly value="<?php echo $chargInterp ;?>"/>
						</div>
			    </div>
					
					<div class="row">
						<div class="form-group col-md-4">
                         <label class="input">Travel Time Hours <i class="fa fa-question-circle" title="Minimum 1 hour , additional time in incremental units example 1 hour 5 minutes is 1.25 , 1 hour 20 minutes is 1.50 hour,  1 hour 35 minutes is 1.75 and 1 hour 50 minutes is 2 hours"></i></label>
      <input class="form-control" name="travelTimeHour" type="text" placeholder=''  oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelTimeHour" value="<?php echo $travelTimeHour ;?>"/>
                    </div>
						<div class="form-group col-md-4">
							<label class="input">Travel Time Rate Per Hour</label>
      <input class="form-control" name="travelTimeRate" type="text" placeholder=''  oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelTimeRate" value="<?php echo $travelTimeRate ;?>"/>
						</div>
						<div class="form-group col-md-4">
                         <label class="input">Charge for Travel Time</label>
      <input class="form-control bg-info" name="chargeTravelTime" type="text" placeholder=''  oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargeTravelTime" readonly value="<?php echo $chargeTravelTime ;?>"/>
                    </div>
			    
					</div>
                   <div class="row">
						<div class="form-group col-md-4">
							<label class="input">Travel Mileage</label>
      <input class="form-control" name="travelMile" type="text" placeholder=''  oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelMile" value="<?php echo $travelMile ;?>"/>
						</div>
						<div class="form-group col-md-4">
                         <label class="input">Rate Per Mileage</label>
      <input class="form-control" name="rateMile" type="text" placeholder=''  oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rateMile" value="<?php echo $rateMile ;?>"/>
                    </div>
						<div class="form-group col-md-4">
							<label class="input">Charge for Travel Cost</label>
      <input class="form-control bg-info" name="chargeTravel" type="text" placeholder=''  id="chargeTravel" readonly value="<?php echo $chargeTravel ;?>"/>
						</div>
						<div class="form-group col-md-4">
                         <label class="input">Travel Cost</label>
      <input class="form-control" name="travelCost" type="text" placeholder=''  oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelCost" value="<?php echo $travelCost ;?>"/>
                    </div>
						<!--<div class="form-group col-md-4">-->
						<!--	<label class="input">Additional Payment</label>-->
      <!--<input style="background: #a0e1a0;" name="admnchargs" type="text" placeholder='' readonly  step="0.01" id="admnchargs" value="0.50 has been added to your pay for online timesheet submission"/>-->
						<!--</div>-->
						<div class="form-group col-md-4">
                         <label class="input">Other Costs (Parking , Bridge Toll)</label>
      <input class="form-control" name="otherCost" type="text" placeholder=''  oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="otherCost" value="<?php echo $otherCost ;?>"/>
                    </div>
						<div class="form-group col-md-4">
							<label class="input">Deduction</label>
      <input class="form-control" name="deduction" type="text" placeholder=''  oninput="calcInterp()" onkeyup="checkDec(this);" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="deduction" value="<?php echo $deduction ;?>"/>
						</div>
						<div class="form-group col-md-4">
                         <label class="input">Total Charges</label>
      <input class="form-control bg-success" name="totalChages" type="text" placeholder='' readonly id="totalChages"  value="<?php echo @$total_charges_interp ;?>"/>
                    </div>
                  <div class="form-group col-md-4">
                         <label class="input">Upload Timesheet (Scanned / Picture) <i class="fa fa-question-circle" title="Acceptable Formats: gif, jpeg, jpg, png, pdf, doc, docx, xlsx"></i></label>
      <input class="form-control" name="time_sheet" type="file" placeholder='' id="time_sheet" <?php if($time_sheet== NULL){ ?>required <?php } ?> />
                    </div>
                    <div class="form-group col-md-4">
                    <?php if($time_sheet!=''){ ?>
                    <label class="input">View your Time Sheet</label>
      <a href="javascript:void(0)" onClick="MM_openBrWindow('timesheet_view.php?t_id=<?php echo $update_id; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=400,left=400,top=200')"><br><img src="lsuk_system/images/images.jpg" width="30" height="30" title="View Time Sheet"></a>
      <?php }else{?> <label class="text-danger">Timesheet is not uploaded!</label><br><img src="lsuk_system/images/missing.jpg" width="35" height="35" title="Time Sheet is missing for this JOB!">
      <?php } ?>
                    </div>
                    </div>
					<input type="submit" name="submit" class="btn btn-primary" value="Submit"/>
</form>
<!--Upload extra files-->
<form id="second_form" style="display:none;" action="#" method="post" enctype="multipart/form-data">
    <div align="center" style=" color:#069; font-size:18px;">Upload Extra Files (if any)</div>
                  <div class="form-group col-md-12">
                      <div id="dvPreview"></div>
                         <label class="input">Upload Timesheet (Scanned / Picture) <i class="fa fa-question-circle" title="Acceptable Formats: gif, jpeg, jpg, png, pdf, doc, docx, xlsx"></i></label>
                  <input name="interpreter_file[]" onchange="loadFiles(event)" multiple="multiple" type="file" size="60" multiple="multiple" accept=".docx,.xlsx,.pdf,.png,.jpeg,.jpg" id="fileupload">
                   <?php if(isset($_POST['submit2']) && $_FILES["interpreter_file"]["name"]!= NULL){
                       error_reporting(0);
                       //UPLOADING fILES
                            for($i=0;$i<count($_FILES['interpreter_file']['tmp_name']);$i++){
                                $picName=$acttObj->upload_file("job_files",$_FILES["interpreter_file"]["name"][$i],$_FILES["interpreter_file"]["type"][$i],$_FILES["interpreter_file"]["tmp_name"][$i],round(microtime(true)).$i);
                            	$data = array('tbl' => $table,'file_name'=>$picName,'order_id'=>$update_id,'interpreter_id'=>$_SESSION['web_userId'], 'dated'=>date('Y-m-d h:i:s') );
                            	$acttObj->insert('job_files',$data);
                            }
                            echo '<script>alert("Thank you! Your files have been uploaded.");window.location.href="time_sheet_interp.php";</script>';
            	   } ?>
            	   <script language="javascript" type="text/javascript">
                    window.onload = function () {
                        var fileUpload = document.getElementById("fileupload");
                        fileUpload.onchange = function () {
                            if (typeof (FileReader) != "undefined") {
                                var dvPreview = document.getElementById("dvPreview");
                                dvPreview.innerHTML = "";
                                var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.pdf|.rtf|.JPG|.JPEG|.GIF|.PNG|.PDF|.RTF|.doc|.docx)$/;
                                for (var i = 0; i < fileUpload.files.length; i++) {
                                    var file = fileUpload.files[i];
                                    if (regex.test(file.name.toLowerCase())) {
                                        var reader = new FileReader();
                                        reader.onload = function (e) {
                                            var img = document.createElement("IMG");
                                            img.height = "100";
                                            img.width = "100";
                                            img.style.display='inline';
                                            img.style.padding='0px 2px';
                                            img.src = e.target.result;
                                            dvPreview.appendChild(img);
                                        }
                                        reader.readAsDataURL(file);
                                    } else {
                                        alert(file.name + " is not a valid image file.");
                                        dvPreview.innerHTML = "";
                                        return false;
                                    }
                                }
                            } else {
                                alert("This browser does not support HTML5 FileReader.");
                            }
                        }
                    };
                    </script><br>
            	   <input type="submit" name="submit2" class="btn btn-primary" value="Upload Files"/>
            	   <a href="time_sheet_interp.php"><input type="button" name="close" class="btn btn-warning" value="No Close"/></a>
                                </div>
                    </form>
                    <!--Upload extra ends here-->
                    </div>
    </section>
    <!-- end content -->  
    
        <hr>
        
     	<!-- begin clients -->
       <?php// include'source/our_client.php'; ?>
        <!-- end clients -->   
    </section>
    <!-- end content -->  
    
    <!-- begin footer -->
	<?php include'source/footer.php'; ?>
	<!-- end footer -->  
</div>
<?php if(isset($_POST['submit'])){
    if(!empty($_POST['hoursWorkd']) && $_POST['hoursWorkd']!=0){ ?>
<script>
    document.getElementById('first_form').style.display='none';
    document.getElementById('second_form').style.display='inline';
</script>
<?php }else{?>
<script>
    document.getElementById('second_form').style.display='none';
    document.getElementById('first_form').style.display='inline';
</script>
<?php }
  } ?>
  <style>
		      #ajax_loader {
                visibility: hidden;
                background-color: #f8f8f8;
                position: absolute;
                width: 10%;
                height: 15%;
                right: 0%;
                top: 27%;
                overflow: hidden;
            }
            #ajax_loader img {
             position: relative;
             left: 15%;
            }
		  </style>
		  <div id="ajax_loader">
            <img src="images/ajax_loader.gif" width="100" class="img-responsive" />
        </div>
    <script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript" src="lsuk_system/js/bootstrap.js" charset="UTF-8"></script>
<?php if($hoursWorkd==0){
echo "<script>$('.label_waiting').addClass('hidden');$('.label_starting').addClass('hidden');$('.btn_finish').addClass('hidden');$('#html_text').addClass('hidden');</script>";
if($row['wt_tm']!='1001-01-01 00:00:00' && $row['st_tm']!='1001-01-01 00:00:00' && $row['fn_tm']=='1001-01-01 00:00:00'){
    echo "<script>$('#div_wait_start').removeClass('hidden');$('.btn_finish').removeClass('hidden');</script>";
}
if($row['wt_tm']!='1001-01-01 00:00:00' && $row['st_tm']=='1001-01-01 00:00:00' && $row['fn_tm']=='1001-01-01 00:00:00' ){
    echo "<script>$('#div_wait_start').removeClass('hidden');$('.label_starting').removeClass('hidden');</script>";
}
} ?>
        <script>
		function cal_hours() {
		    var wt_tm=$('#wt_tm').val();
		    var st_tm=$('#st_tm').val();
		    var fn_tm=$('#fn_tm').val();
		    var arr_wt=wt_tm.split(":");
		    var wt_tm_res=arr_wt[0]+':'+Math.ceil(arr_wt[1] / 15) * 15;
		    var arr_st=st_tm.split(":");
		    var st_tm_res=arr_st[0]+':'+Math.ceil(arr_st[1] / 15) * 15;
		    
		    function diff_hours(dt2, dt1) {
              var diff =(dt2.getTime() - dt1.getTime()) / 1000;
              diff /= (60 * 60);
              return Math.abs(Math.round(diff));
            }
            
            dt1 = new Date("August 13, 2014 08:11:00");
            dt2 = new Date("October 13, 2014 11:13:00");
            console.log(diff_hours(dt1, dt2));
		    alert();
		}
		function calcInterp() {
		var hoursWorkd = parseFloat(document.getElementById('hoursWorkd').value);	
		var rateHour = parseFloat(document.getElementById('rateHour').value);
		var chargInterp = document.getElementById('chargInterp');	
		var x = rateHour * hoursWorkd;
		chargInterp.value = x.toFixed(2);
		
		var travelMile = parseFloat(document.getElementById('travelMile').value);	
		var rateMile = parseFloat(document.getElementById('rateMile').value);
		var chargeTravel = document.getElementById('chargeTravel');	
		var y = travelMile * rateMile;
		chargeTravel.value = y.toFixed(2);
		
		var travelTimeHour = parseFloat(document.getElementById('travelTimeHour').value);	
		var travelTimeRate = parseFloat(document.getElementById('travelTimeRate').value);
		var chargeTravelTime = document.getElementById('chargeTravelTime');	
		var z = travelTimeHour * travelTimeRate;
		chargeTravelTime.value = z.toFixed(2);
		
		var otherCost = parseFloat(document.getElementById('otherCost').value);	
		var deduction = parseFloat(document.getElementById('deduction').value);	
		var admnchargs = 0.50;
		var travelCost = parseFloat(document.getElementById('travelCost').value);	
		
		totalChages.value=(parseFloat(x+y+z+travelCost+otherCost+admnchargs)-parseFloat(deduction)).toFixed(2);
		}
        function checkDec(el){
         var ex = /^[0-9]+\.?[0-9]*$/;
         if(ex.test(el.value)==false){
           el.value = 0;
           el.select();
           calcInterp();
          }
        }
        var counter=0;
        function wait_start(id,order_category,type){
            var order_id=id;
            var order_type=order_category;
            var value_type=type;
            $.ajax({
                url:'ajax_client_portal.php',
                method:'post',
                dataType: "json",
                data:{order_id:order_id,order_type:order_type,value_type:value_type},
                beforeSend: function(){
                    $('#ajax_loader').css("visibility", "visible");
                },
                success:function(data){
                    counter++;
                    if(value_type=='wait_time'){
                        $('.label_waiting').html('');
                        $('.label_waiting').addClass('hidden');
                    }
                    if(value_type=='start_time'){
                        $('.label_waiting').html('');
                        $('.label_starting').html('');
                        $('.label_waiting').addClass('hidden');
                        $('.label_starting').addClass('hidden');
                    }
                    if(value_type=='finish_time'){
                        $('#html_text').html('<label style="padding:12px;" class="label label-success"><i class="fa fa-check-circle" style="font-size:1.5em;"></i> 0.50 has been added to your pay for online timesheet submission</label>');
                        $('.btn_finish').html('');
                        $('.btn_finish').addClass('hidden');
                        $('#div_all_area').removeClass('hidden');
                    }
                    if(counter==1){
                        $('#all_action').html('<table class="table table-bordered"><thead class="bg-info"><caption><b>Working data for this Assignment</b></caption></thead><tbody class="append_data"></tbody></table>');
                    }
                    $('.append_data').append(data['msg']);
                    if(data['action']=='show_finish_button'){
                        //$('#hoursWorkd').val(data['hoursWorkd']);
                        $('.btn_finish').removeClass('hidden');
                    }
                },
                complete: function(){
                    $('#ajax_loader').css("visibility", "hidden");
                }, 
                error: function(xhr){
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                }
            });
        }
        </script>
<?php }?>
</body>
</html>