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
$intrpName=$row['intrpName'];$bookinType=$row['bookinType'];$hoursWorkd=$row['hoursWorkd'];$chargInterp=$row['chargInterp'];$rateHour=$row['rateHour'];$travelMile=$row['travelMile'];$rateMile=$row['rateMile'];$chargeTravel=$row['chargeTravel'];$travelCost=$row['travelCost'];$otherCost=$row['otherCost'];$travelTimeHour=$row['travelTimeHour'];$travelTimeRate=$row['travelTimeRate'];$chargeTravelTime=$row['chargeTravelTime'];$dueDate=$row['dueDate'];$tAmount=$row['tAmount'];
$admnchargs=$row['admnchargs'];$deduction=$row['deduction'];$total_charges_interp=$row['total_charges_interp'];$time_sheet=$row['time_sheet'];
?>
<?php $intrpName; 
$interp_rph=$acttObj->unique_data('interpreter_reg','rph','id',$intrpName) ;
$interp_email=$acttObj->unique_data('interpreter_reg','email','id',$intrpName) ; 
$valid_check_q=$acttObj->unique_dataAnd($table,'id','intrpName',$_SESSION['web_userId'],'id',$update_id);
$valid_check=$valid_check_q!=''?'yes':'no';
if($valid_check=='no'){
    echo '<script>window.location.href="index.php";</script>';
}
?>


<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->

<!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:48:33 GMT -->
<head>
<?php include'source/header.php'; ?>
        <script>	
		function calcInterp() {
		var hoursWorkd = parseFloat(document.getElementById('hoursWorkd').value);	
		var rateHour = parseFloat(document.getElementById('rateHour').value);
		var chargInterp = document.getElementById('chargInterp');	
		var x = rateHour * hoursWorkd;
		chargInterp.value = x;
		
		var travelMile = parseFloat(document.getElementById('travelMile').value);	
		var rateMile = parseFloat(document.getElementById('rateMile').value);
		var chargeTravel = document.getElementById('chargeTravel');	
		var y = travelMile * rateMile;
		chargeTravel.value = y;
		
		var travelTimeHour = parseFloat(document.getElementById('travelTimeHour').value);	
		var travelTimeRate = parseFloat(document.getElementById('travelTimeRate').value);
		var chargeTravelTime = document.getElementById('chargeTravelTime');	
		var z = travelTimeHour * travelTimeRate;
		chargeTravelTime.value = z;
		
		var otherCost = parseFloat(document.getElementById('otherCost').value);	
		var deduction = parseFloat(document.getElementById('deduction').value);	
		var admnchargs = parseFloat(document.getElementById('admnchargs').value);
		var travelCost = parseFloat(document.getElementById('travelCost').value);	
		
		totalChages.value=parseFloat(x+y+z+travelCost+otherCost+admnchargs)-parseFloat(deduction);
		}
        
        </script>
</head>

<body class="boxed">
<!-- begin container -->
<div id="wrap">
	<!-- begin header -->
<?php include'source/top_nav.php'; ?>
    <!-- end header -->
			 <?php 
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
    $mail->Body    = 'Dear Interpreter!<br>You have successfully uploaded your timesheet for Job.<br>Thank you<br>Best Regards<br>LSUK Limited';
    if($mail->send()){
    $mail->ClearAllRecipients();
    $sent=1; ?>
<script>alert('Expenses have been successfuly updated!');
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
    <script>alert("Expenses have been successfuly updated!");
    // window.location.href="time_sheet_interp.php";
    </script>
    <?php }
}
?>	
    <!-- begin page title -->
    <section id="page-title">
    	<div class="container clearfix">
            <h1>Face to Face </h1>
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
    	<!-- begin our company -->
 
    <!-- begin content -->
    <section id="content" class="container clearfix">
  <link rel="stylesheet" type="text/css" href="style_form.css"/>
		
			<form id="first_form" class="sky-form" action="#" method="post" enctype="multipart/form-data">
			    <div align="center" style=" color:#069; font-size:18px;">Update Your Expenses</div>
              <fieldset> <legend style="font-size:14px; color:#069;">Assignment Cost </legend>					
			    <div class="row">
						<section class="col col-6">
						  <label class="input">Hours Worked
      <input name="hoursWorkd" type="text" placeholder=''  id="hoursWorkd" oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $hoursWorkd ;?>"/>
						  </label>
						</section>
                  <section class="col col-6">
                         <label class="input">Rate Per Hour  
      <input name="rateHour" type="text" placeholder=''  id="rateHour" oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php if($rateHour!=0){echo $rateHour ;}else{echo $interp_rph;} ?>"/>
      </label>
                    </section>
			    </div>
					
					<div class="row">
						<section class="col col-6">
							<label class="input">Charge for Interpreting Time <span style="font-size:9px">(Minimum 1 hour , additional time in incremental units example 1 hour 5 minutes is 1.25 , 1 hour 20 minutes is 1.50 hour,  1 hour 35 minutes is 1.75 and 1 hour 50 minutes is 2 hours)</span>
      <input name="chargInterp" type="text" placeholder=''  id="chargInterp" readonly style="background-color:#CCC;" value="<?php echo $chargInterp ;?>"/>
							</label>
						</section>
						<section class="col col-6">
                         <label class="input">Travel Time Hours <span style="font-size:9px">(Minimum 1 hour , additional time in incremental units example 1 hour 5 minutes is 1.25 , 1 hour 20 minutes is 1.50 hour,  1 hour 35 minutes is 1.75 and 1 hour 50 minutes is 2 hours)</span>
      <input name="travelTimeHour" type="text" placeholder=''  oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelTimeHour" value="<?php echo $travelTimeHour ;?>"/>
      </label>
                    </section>
			    
					</div>
                   <div class="row">
						<section class="col col-6">
							<label class="input">Travel Time Rate Per Hour
      <input name="travelTimeRate" type="text" placeholder=''  oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelTimeRate" value="<?php echo $travelTimeRate ;?>"/>
							</label>
						</section>
						<section class="col col-6">
                         <label class="input">Charge for Travel Time 
      <input name="chargeTravelTime" type="text" placeholder=''  oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargeTravelTime" readonly style="background-color:#CCC;" value="<?php echo $chargeTravelTime ;?>"/>
      </label>
                    </section>
			    
					</div>
                   <div class="row">
						<section class="col col-6">
							<label class="input">Travel Mileage
      <input name="travelMile" type="text" placeholder=''  oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelMile" value="<?php echo $travelMile ;?>"/>
							</label>
						</section>
						<section class="col col-6">
                         <label class="input">Rate Per Mileage 
      <input name="rateMile" type="text" placeholder=''  oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rateMile" value="<?php echo $rateMile ;?>"/>
      </label>
                    </section>
			    
					</div>
                   <div class="row">
						<section class="col col-6">
							<label class="input">Charge for Travel Cost
      <input name="chargeTravel" type="text" placeholder=''  id="chargeTravel" readonly style="background-color:#CCC;" value="<?php echo $chargeTravel ;?>"/>
							</label>
						</section>
						<section class="col col-6">
                         <label class="input">Travel Cost
      <input name="travelCost" type="text" placeholder=''  oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="travelCost" value="<?php echo $travelCost ;?>"/>
      </label>
                    </section>
			    
					</div>
                   <div class="row">
						<section class="col col-6">
							<label class="input">Additional Payment
      <input style="background: #a0e1a0;" name="admnchargs" type="text" placeholder='' readonly  oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="admnchargs" value="0.50 has been added to your pay for online timesheet submission"/>
							</label>
						</section>
						<section class="col col-6">
                         <label class="input">Other Costs (Parking , Bridge Toll)
      <input name="otherCost" type="text" placeholder=''  oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="otherCost" value="<?php echo $otherCost ;?>"/>
      </label>
                    </section>
			    
					</div>
                   <div class="row">
						<section class="col col-6">
							<label class="input">Deduction
      <input name="deduction" type="text" placeholder=''  oninput="calcInterp()" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="deduction" value="<?php echo $deduction ;?>"/>
							</label>
						</section>
						<section class="col col-6">
                         <label class="input">Total Charges
      <input name="totalChages" type="text" placeholder='' readonly style="background-color:#99C;" id="totalChages"  value="<?php echo @$total_charges_interp ;?>"/>
      </label>
                    </section>
			    
					</div>
                  <div class="row">
                  <section class="col col-6">
                         <label class="input">Upload your Time Sheet (Scaned / Picture) <br/>(acceptable formats: "gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx")
      <input name="time_sheet" type="file" placeholder='' id="time_sheet" <?php if($time_sheet== NULL){ ?>required <?php } ?> />
      </label>
                    </section>
                    <section class="col col-6">
                    <?php if($time_sheet!=''){ ?>
                    <label class="input">View your Time Sheet
      <a href="#" onClick="MM_openBrWindow('lsuk_system/timesheet_view.php?t_id=<?php echo $update_id; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')"><br><br><img src="lsuk_system/images/images.jpg" width="30" height="30" title="View Time Sheet"></a>
      </label><?php }else{?> <label class="text-danger">Timesheet is not uploaded!<br><br><img src="lsuk_system/images/missing.jpg" width="50" height="50" title="Time Sheet is missing for this JOB!">
      </label><?php } ?>
                    </section>
                    </div>
			  </fieldset>
				 
				
				<footer>
					<input type="submit" name="submit" class="button" value="Submit"/>
				</footer>
</form>
<!--Upload extra files-->
<form id="second_form" style="display:none;" class="sky-form" action="#" method="post" enctype="multipart/form-data">
    <div align="center" style=" color:#069; font-size:18px;">Upload Extra Files (if any)</div>
                  <section class="col col-6">
                      <div id="dvPreview"></div>
                         <label class="input">Upload Extra Files (Scaned / Picture) <br/>(acceptable formats: "gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx")
                  <input name="interpreter_file[]" onchange="loadFiles(event)" multiple="multiple" type="file" size="60" multiple="multiple" class="form-control" accept=".docx,.xlsx,.pdf,.png,.jpeg,.jpg" id="fileupload">
                  </label>
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
                                var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.pdf|.rtf|.JPG|.JPEG|.GIF|.PNG|.PDF|.RTF)$/;
                                for (var i = 0; i < fileUpload.files.length; i++) {
                                    var file = fileUpload.files[i];
                                    if (regex.test(file.name.toLowerCase())) {
                                        var reader = new FileReader();
                                        reader.onload = function (e) {
                                            var img = document.createElement("IMG");
                                            img.height = "100";
                                            img.width = "100";
                                            img.style.display='inline';
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
                    </script>
            	   <input type="submit" name="submit2" class="button" value="Upload"/>
            	   <a href="time_sheet_interp.php"><input type="button" name="close" class="button" value="No Close" style="background-color: #dabd2d;color: black !important;"/></a>
                                </section>
                    </form>
                    <!--Upload extra ends here-->
    </section>
    <!-- end content -->  
    
        <hr>
        
     	<!-- begin clients -->
       <?php include'source/our_client.php'; ?>
        <!-- end clients -->   
    </section>
    <!-- end content -->  
    
    <!-- begin footer -->
	<?php include'source/footer.php'; ?>
	<!-- end footer -->  
</div>
<?php if(isset($_POST['submit'])){ ?>
<script>
    document.getElementById('first_form').style.display='none';
    document.getElementById('second_form').style.display='inline';
</script>
<?php } ?>
<!-- end container -->
</body>
</html>
