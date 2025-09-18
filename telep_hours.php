<?php session_start();
error_reporting(0);
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include'source/db.php'; include'source/class.php'; $table='telephone';
$update_id= @$_GET['update_id'];
$query="SELECT $table.*,interpreter_reg.rpm,interpreter_reg.email as int_email FROM $table,interpreter_reg where $table.intrpName=interpreter_reg.id AND $table.id=$update_id";
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$bookinType=$row['bookinType'];$hoursWorkd=$row['hoursWorkd'];$chargInterp=$row['chargInterp'];
$rateHour=$row['rateHour']!=0?:$row['rpm'];
$calCharges=$row['calCharges'];$otherCharges=$row['otherCharges'];
$intrpName=$row['intrpName'];$total_charges_interp=$row['total_charges_interp'];
$admnchargs=$row['admnchargs'];$time_sheet=$row['time_sheet'];
$int_email=$row['int_email'];$assignDur=$row['assignDur'];
$valid_check_q=$acttObj->unique_dataAnd($table,'id','intrpName',$_SESSION['web_userId'],'id',$update_id);
$valid_check=$valid_check_q!=''?'yes':'no';
if($valid_check=='no'){
    echo '<script>window.location.href="index.php";</script>';
}
if(date('Y-m-d H:i',strtotime($row['assignDate'].' '.$row['assignTime']))>date('Y-m-d H:i')){
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
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
      <script>	
		function calcInterp() {
		var hoursWorkd = parseFloat(document.getElementById('hoursWorkd').value);	
		var rateHour = parseFloat(document.getElementById('rateHour').value);
		var chargInterp = document.getElementById('chargInterp');	
		var x = rateHour * hoursWorkd;
		chargInterp.value = x.toFixed(2);
		var calCharges = parseFloat(document.getElementById('calCharges').value);	
		var otherCharges = parseFloat(document.getElementById('otherCharges').value);
		var admnchargs = 0.50;
		total_charges_interp.value=(parseFloat(calCharges+x+otherCharges+admnchargs)).toFixed(2);
		}
        function checkDec(el){
            var ex = /^[0-9]+\.?[0-9]*$/;
            if(ex.test(el.value)==false){
                el.value = 0;
                el.select();
                calcInterp();
            }
        }
        
        </script>
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
    try {
        $to_add=$int_email;
        $from_add = "info@lsuk.org";
        $mail->SMTPDebug = 0;
        $mail->isSMTP(); 
        $mail->Host = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'xtxwzcvtdbjpftdj';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($from_add, 'LSUK Timehseet Confirmation');
        $mail->addAddress($to_add);
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = 'Confirmation of timesheet upload for telephone assignment';
        $mail->Body    = 'Dear Linguist!<br>You have successfully uploaded your timesheet for Job.<br>Thank you<br>Best Regards<br>LSUK Limited';
        if($mail->send()){
            $mail->ClearAllRecipients();
            $sent=1;
            echo "<script>alert('Expenses have been successfuly updated.');</script>";
        }else{
            echo "<script>alert('Failed to submit your record!');</script>";
        }
    }catch (Exception $e) {
        echo "<script>alert('Message could not be sent! Mailer library error.');</script>";
    }
}else{
    $sent=0;
}
if(isset($_POST['submit'])){
    if(!empty($_POST['hoursWorkd']) && $_POST['hoursWorkd']!=0){
        $date_1=$_POST['date_1'];
        $date_2=$_POST['date_2'];
        $t1 = strtotime($_POST['date_1']);
        $t2 = strtotime($_POST['date_2']);
        $diff = $t2 - $t1;
        $hours = round($diff / 3600,2);
        $data0=($hours*60)<$assignDur?$assignDur:round($hours*60);
        $data1=$data0*$rateHour;
        $data2=$_POST['calCharges'];
        $data3=$_POST['otherCharges'];
        $data4=0.50;
        $data5=number_format($data1+$data2+$data3+$data4,2);
        $acttObj->update($table,array('hoursWorkd'=>$data0,'rateHour'=>$rateHour,'chargInterp'=>$data1,'calCharges'=>$data2,
        'otherCharges'=>$data3,'admnchargs'=>$data4,'total_charges_interp'=>$data5,'st_tm'=>$date_1,'fn_tm'=>$date_2,'tm_by'=>'i',"added_via"=>2,'int_sig'=>'i_default.png'),array("id"=>$update_id));
        if($_FILES["time_sheet"]["name"]!= NULL){
            error_reporting(0);
            if($time_sheet==''){
                $picName=$acttObj->upload_file("file_folder/time_sheet_telep",$_FILES["time_sheet"]["name"],$_FILES["time_sheet"]["type"],$_FILES["time_sheet"]["tmp_name"],round(microtime(true)));
                $acttObj->update($table,array('time_sheet'=>$picName),array("id"=>$update_id));
            }else{
                if(unlink('file_folder/time_sheet_telep/'.$time_sheet)){
                    $picName=$acttObj->upload_file("file_folder/time_sheet_telep",$_FILES["time_sheet"]["name"],$_FILES["time_sheet"]["type"],$_FILES["time_sheet"]["tmp_name"],round(microtime(true)));
                    $acttObj->update($table,array('time_sheet'=>$picName),array("id"=>$update_id));
                }
            }
        }
        $acttObj->update($table,array('approved_flag' => 0, 'hrsubmited'=>'Self','interp_hr_date'=>date("Y-m-d")),array("id"=>$update_id));
        if($sent==0){
            echo "<script>alert('Expenses have been successfuly updated.');</script>";
        }
    }else{
        echo "<script>alert('Kindly update hours worked value !');</script>";
    }
} ?>
    <!-- begin page title -->
    <section id="page-title">
    	<div class="container clearfix">
            <h1>Telephone Interpreting </h1>
          <nav id="breadcrumbs">
               <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>
                    <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']);?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php'));?></a> &rsaquo;</li>
                    <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php'));?></li>
            </ul>
          </nav>
        </div>
    </section>
    <!-- begin page title -->
 
    <!-- begin content -->
    <section id="content" class="container clearfix">
			<form id="first_form" class="sky-form" action="#" method="post" enctype="multipart/form-data">
		<div align="center"><h4>Update Your Telephone Assignment Timesheet</h4></div><br>
			    <center><h4 class="alert alert-success">0.50 has been added to your pay for online timesheet submission</h4></center>
			    <div class="row">
                <div class='col-sm-4'>
                    <div class="form-group">
                        <label class="input">Assignment Start Time <span title="Expected start time for assignment has been placed. Update start time for this assignment if different." class="fa fa-question-circle"></span></label>
                        <div class='input-group date datetimepicker'>
                            <input value="<?php echo $row['assignDate'].' '.$row['assignTime']; ?>" id="date_1" name="date_1" type='text' class="form-control" required/><span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span> Click to change
                        </span>
                        </div>
                        </div>
                    </div>
                    <div class='col-sm-4'>
                        <div class="form-group">
                            <label class="input">Assignment End Time <span title="Expected end time for assignment has been placed. Update end time for this assignment if different." class="fa fa-question-circle"></span></label>
                            <div class='input-group date datetimepicker'>
                            <?php $expected_end_date=$row['assignDate'].' '.$row['assignTime'];
                                  $expected_end_date=strtotime($expected_end_date);
                                  $expected_end_date=strtotime("+".$assignDur." minute", $expected_end_date);
                                  $expected_end_date=date('Y-m-d H:i', $expected_end_date); ?>
                                <input value="<?php echo $expected_end_date; ?>" id="date_2" name="date_2" type='text' class="form-control" required/><span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span> Click to change
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-4'>
                        <div class="form-group"><h2><span class="label label-info duration_label"></span></h2></div>
                    </div>
                    <hr>
					<div class="form-group col-md-4">
						<label class="input">Worked Duration (In Minutes) <span title="Expected workout duration has been placed. Update start and end time if different." class="fa fa-question-circle"></span></label>
                        <input readonly class="form-control" name="hoursWorkd" type="text" id="hoursWorkd" value="<?php echo $assignDur ?>"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" onkeyup="checkDec(this);"/>
					</div>
                    <div class="form-group col-md-4">
                        <label class="input">Rate Per Minute</label>
                        <input readonly class="form-control" name="rateHour" type="number" min="0.1"  <?php if($row["source"]!="Sign Language (BSL)"){ ?> max="0.75" <?php } ?>  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rateHour" value="<?php echo $rateHour; ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="input">Charge for Interpreting Time</label>
                        <input class="form-control" name="chargInterp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargInterp" value="<?php echo $chargInterp!=0?:$assignDur*$rateHour; ?>" readonly style="background-color:#CCC;"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="input">Call Charges</label>
                        <input class="form-control" name="calCharges" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="calCharges" value="<?php echo $calCharges ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="input">Other Charges</label>
                        <input class="form-control" name="otherCharges" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="otherCharges" value="<?php echo $otherCharges ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/></label>
                    </div>
                </div>
                    <hr>
                <div class="row">
					<div class="form-group col-md-4">
                        <label class="input"><b>Total Charges</b></label>
                        <input class="form-control" name="total_charges_interp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_interp" value="<?php echo $total_charges_interp ?>" readonly/>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="input">Upload Time Sheet (Scanned / Picture) <i class="fa fa-question-circle" title="Acceptable Formats: gif, jpeg, jpg, png, pdf, doc, docx, xlsx"></i></label>
                        <input class="form-control" name="time_sheet" type="file" placeholder='' id="time_sheet" <?php if($time_sheet== NULL){ ?>required <?php } ?> />
                        </div>
                    <div class="form-group col-md-4">
                    <?php if($time_sheet!=''){ ?>
                        <label class="input">View your Time Sheet</label>
                        <a href="javascript:void(0)" onClick="MM_openBrWindow('timesheet_view.php?t_id=<?php echo $update_id; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=400,left=400,top=200')"><img src="lsuk_system/images/images.jpg" width="30" height="30" title="View Time Sheet"></a>
                    <?php }else{ ?>
                        <label class="text-danger">Timesheet is not uploaded!<img src="lsuk_system/images/missing.jpg" width="50" height="50" title="Time Sheet is missing for this JOB!"></label>
                    <?php } ?>
                    </div>
                </div>
				 
				
				<footer>
					<input type="submit" name="submit" class="btn btn-primary" value="Submit"/>
				</footer>
</form>
<!--Upload extra files-->
<form id="second_form" style="display:none;" class="sky-form" action="#" method="post" enctype="multipart/form-data">
    <div align="center" style=" color:#069; font-size:18px;">Upload Extra Files (if any)</div>
                <div class="form-group col-md-12" id="dvPreview"></div>
                <div class="form-group col-md-6">
                        <label class="input">Upload Extra Files (Scaned / Picture) <i class="fa fa-question-circle" title="Acceptable Formats: gif, jpeg, jpg, png, pdf, doc, docx, xlsx"></i></label>
                        <input name="interpreter_file[]" onchange="loadFiles(event)" multiple="multiple" type="file" size="60" multiple="multiple" class="form-control" accept=".docx,.xlsx,.pdf,.png,.jpeg,.jpg" id="fileupload">
                </div>
                   <?php if(isset($_POST['submit2']) && $_FILES["interpreter_file"]["name"]!= NULL){
                       error_reporting(0);
                       //UPLOADING fILES
                            for($i=0;$i<count($_FILES['interpreter_file']['tmp_name']);$i++){
                                $picName=$acttObj->upload_file("file_folder/job_files",$_FILES["interpreter_file"]["name"][$i],$_FILES["interpreter_file"]["type"][$i],$_FILES["interpreter_file"]["tmp_name"][$i],round(microtime(true)).$i);
                            	$data = array('tbl' => $table,'file_name'=>$picName,'order_id'=>$update_id,'interpreter_id'=>$_SESSION['web_userId'], 'dated'=>date('Y-m-d h:i:s') );
                            	$acttObj->insert('job_files',$data);
                            }
                            echo '<script>alert("Thank you! Your files have been uploaded.");window.location.href="time_sheet_interp.php";</script>';
            	   } ?>
                   <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
                   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
                   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
            	   <script type="text/javascript">
                   $(function () {
                        $('.datetimepicker').datetimepicker({
                            format:'YYYY-MM-DD HH:mm',
                            minDate:"<?php echo $row['assignDate'].' '.$row['assignTime']; ?>"
                        });
                         $('.datetimepicker:eq(1)').datetimepicker().on('dp.change', function (event) {
                             var dt_new_1=$('#date_1').val().split('-');
                             var tt_new_1=dt_new_1[2].split(' ');
                             dt_new_1=dt_new_1[1]+"/"+tt_new_1[0]+"/"+dt_new_1[0]+" "+tt_new_1[1];
                             
                             var dt_new_2=$('#date_2').val().split('-');
                             var tt_new_2=dt_new_2[2].split(' ');
                             dt_new_2=dt_new_2[1]+"/"+tt_new_2[0]+"/"+dt_new_2[0]+" "+tt_new_2[1];

                            //  var dt_new_1=$('#date_1').val();
                             
                            //  var dt_new_2=$('#date_2').val();

                            //  console.log(dt_new_1);
                            //  console.log(dt_new_2);
                            
                            var assignDur='<?php echo $assignDur; ?>';
                            var t1=new Date(dt_new_1);
                            var t2=new Date(dt_new_2);
                            t1=(t1.getTime()/1000)+20100;
                            t2=(t2.getTime()/1000)+20100;
                            var diff = t2 - t1;
                            var hours = diff / 3600;
                            var result=(hours*60)<assignDur?assignDur:hours*60;
                            $('#hoursWorkd').val(result);
                            var hourss;var hr;var get_dur;var mins;
                            if(result>60){
                               hourss=Math.round(result / 60);
                                if(Math.floor(hourss)>1){
                                    hr=" hours";
                                }else{
                                    hr=" hour";
                                }
                                mins=Math.round(result % 60);
                                if(mins==00){
                                    get_dur=hourss+hr;  
                                }else{
                                    get_dur=hourss+hr+" "+mins+" minutes";  
                                }
                            }else if(result==60){
                                get_dur="1 Hour";
                            }else{
                                get_dur=result+" minutes";
                            }
                            $('.duration_label').text(get_dur);
                            calcInterp();
                        });
                        calcInterp();
                    });
                    window.onload = function () {
                        var fileUpload = document.getElementById("fileupload");
                        fileUpload.onchange = function () {
                            if (typeof (FileReader) != "undefined") {
                                var dvPreview = document.getElementById("dvPreview");
                                dvPreview.innerHTML = "";
                                var regex = /^([a-zA-Z0-9\s_\\.\-:()])+(.jpg|.jpeg|.gif|.png|.pdf|.rtf|.JPG|.JPEG|.GIF|.PNG|.PDF|.RTF|.doc|.docx|.xlsx)$/;
                                var i;
                                for (i = 0; i < fileUpload.files.length; i++) {
                                    var file = fileUpload.files[i];
                                    if (regex.test(file.name.toLowerCase())) {
                                        var file_name = file.name.toLowerCase().split(".");
                                        var accepted_types = ['jpg', 'gif', 'png', 'jpeg'];
                                            //alert(ext);
                                            
                                        /*if( file_name.length === 1 || ( file_name[0] === "" && file_name.length === 2 ) ) {
                                            return "";
                                        }else{
                                            var ext=file_name.pop();
                                        }*/
                                        var reader = new FileReader();
                                        reader.onload = function (e) {
                                            //if (accepted_types.indexOf(file_name[1]) > 0) {
                                                var img = document.createElement("IMG");
                                                img.height = "100";
                                                img.width = "100";
                                                img.style.display='inline';
                                                img.style.padding='0px 2px';
                                                img.src = e.target.result;
                                            /*}else{
                                                var img = document.createElement("DIV");
                                                img.setAttribute("class", "img-thumbnail");
                                                img.setAttribute("style", "margin:1px;height:100px;width:100px;text-align:center;");
                                                img.innerHTML = "Doc "+i;
                                            }*/
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
                    </script>
                    <hr>
                    <div class="form-group col-md-6">
            	        <input type="submit" name="submit2" class="btn btn-primary" value="Upload"/>
            	        <a href="time_sheet_interp.php" class="btn btn-warning" name="close">No close</a>
                    </div>
                </form>
                <!--Upload extra ends here-->
    </section>
    <!-- end content -->  
    
        <hr>
    </section>
    <!-- end content -->  
    
    <!-- begin clients -->
       <?php include'source/our_client.php'; ?>
        <!-- end clients --> 

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
  }
}?>
</body>
</html>