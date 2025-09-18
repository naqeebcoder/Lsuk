<?php if(session_id() == '' || !isset($_SESSION)){session_start();}
error_reporting(0);
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'lsuk_system/phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);
include'source/db.php';
include'source/class.php';
$update_id=$_GET['update_id'];$table='translation';
$query="SELECT $table.*,interpreter_reg.rpu,interpreter_reg.email as int_email FROM $table,interpreter_reg where $table.intrpName=interpreter_reg.id AND $table.id=$update_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$bookinType=$row['bookinType'];$numberUnit=$row['numberUnit'];
$rpU=$row['rpU']!=0?:$row['rpu'];
$otherCharg=$row['otherCharg'];$intrpName=$row['intrpName'];
$total_charges_interp=$row['total_charges_interp'];
$admnchargs=$row['admnchargs'];$time_sheet=$row['time_sheet'];
$units_rate=$numberUnit*$rpU;
$int_email=$row['int_email'];
$docType=$row['docType'];
if($docType==7){ $trans_single_label='unit';$trans_multi_label=' Units';}else{ $trans_single_label='word';$trans_multi_label=' Words';}
$valid_check_q=$acttObj->unique_dataAnd($table,'id','intrpName',$_SESSION['web_userId'],'id',$update_id);
$valid_check=$valid_check_q!=''?'yes':'no';
if($valid_check=='no'){
    echo '<script>window.location.href="index.php";</script>';
}
if($row['asignDate']>date('Y-m-d')){
    $problem_hours=1;
    $problem_msg='This job is not completed yet! Thank you';
}else if($numberUnit>0){
    $problem_hours=1;
    $problem_msg='Units for this job already updated! Thank you';
}else if($row['deleted_flag']==1 || $row['order_cancel_flag']==1 || $row['orderCancelatoin']==1 || $row['intrp_salary_comit']==1){
    $problem_hours=1;
    $problem_msg='LSUK is currently processing this job! Thank you';
}else{
    $problem_hours=0;
    $problem_msg='';
} ?>
<!DOCTYPE HTML>
<html class="no-js">
<head>
<?php include'source/header.php'; ?>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <script src="js/jquery-1.8.2.min.js"></script>
      <script>	
		function calcInterp() {
		var hoursWorkd = parseFloat(document.getElementById('numberUnit').value);	
		var rateHour = parseFloat(document.getElementById('rpU').value);
		var x = rateHour * hoursWorkd;
		var units_rate = document.getElementById('units_rate');
		units_rate.value = x.toFixed(2);
		var admnchargs = 0.50;
		var otherCharges = parseFloat(document.getElementById('otherCharg').value);
		total_charges_interp.value=(parseFloat(x+otherCharges+admnchargs)).toFixed(2);
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
<div id="wrap">
<?php include'source/top_nav.php';
if($problem_hours==1){?>
    <center><br><br><h3><?php echo isset($problem_msg) && !empty($problem_msg)?$problem_msg:''; ?></h3>
	<br><br><a class="button" href="time_sheet_interp.php"><i class="glyphicon glyphicon-arrow-left"></i> Go Back</a></center>
<?php }else{ 
if(isset($_POST['submit']) && $time_sheet=='' && $_FILES["time_sheet"]["name"]!= NULL){
    try {
        $to_add=$int_email;
        //$to_add = "waqarecp1992@gmail.com";
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
        //$mail->addAddress('waqarecp1992@gmail.com');
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->isHTML(true);
        $mail->Subject = 'Confirmation of timesheet upload for translation assignment';
        $mail->Body    = 'Dear Linguist!<br>You have successfully uploaded your timesheet for Job.<br>Thank you<br>Best Regards<br>LSUK Limited';
        if($mail->send()){
            $mail->ClearAllRecipients();
            $sent=1;
            echo "<script>alert('Expenses have been successfuly updated.');</script>";
        }else{
            echo "<script>alert('Failed to submit your record!');</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent! Mailer library error.');</script>";
    }
}else{
    $sent=0;
}
if(isset($_POST['submit'])){
    if(!empty($_POST['numberUnit']) && $_POST['numberUnit']!=0){
        $c1=$_POST['numberUnit'];
        $acttObj->editFun($table,$update_id,'numberUnit',$c1);
        $acttObj->editFun($table,$update_id,'rpU',$rpU);
        $c3=$_POST['otherCharg'];
        $acttObj->editFun($table,$update_id,'otherCharg',$c3);
        $c4=0.50;
        $acttObj->editFun($table,$update_id,'admnchargs',$c4);
        $acttObj->editFun($table,$update_id,'tm_by','i');
        $acttObj->editFun($table,$update_id,'added_via',2);
        $acttObj->editFun($table,$update_id,'int_sig','i_default.png');
        if($_FILES["time_sheet"]["name"]!= NULL){
            error_reporting(0);
            if($time_sheet==''){
                $picName=$acttObj->upload_file("file_folder/time_sheet_trans",$_FILES["time_sheet"]["name"],$_FILES["time_sheet"]["type"],$_FILES["time_sheet"]["tmp_name"],round(microtime(true)));
                $acttObj->editFun($table,$update_id,'time_sheet',$picName);
            }else{
                if(unlink('file_folder/time_sheet_trans/'.$time_sheet)){
                    $picName=$acttObj->upload_file("file_folder/time_sheet_trans",$_FILES["time_sheet"]["name"],$_FILES["time_sheet"]["type"],$_FILES["time_sheet"]["tmp_name"],round(microtime(true)));
                    $acttObj->editFun($table,$update_id,'time_sheet',$picName);
                }
            }
        }
        $data=($c1*$rpU)+$c3;
        $acttObj->editFun($table,$update_id,'approved_flag',0);
        $acttObj->editFun($table,$update_id,'hrsubmited','Self');
        $acttObj->editFun($table,$update_id,'total_charges_interp',$data);
        $acttObj->editFun($table,$update_id,'interp_hr_date',date("Y-m-d"));
        if($sent==0){
            echo "<script>alert('Expenses have been successfuly updated.');</script>";
        }
    }else{
        echo "<script>alert('Kindly update number of units value !');</script>";
    }
}
?>
    <!-- begin page title -->
    <section id="page-title">
    	<div class="container clearfix">
            <h1>Translation Job </h1>
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
			<form id="first_form" class="sky-form" action="#" method="post" enctype="multipart/form-data">
			    <div align="center" style=" color:#069; font-size:18px;">Update Your Translation Assignment Expenses</div><br>
			    <center><h4 class="alert alert-success">0.50 has been added to your pay for online timesheet submission</h4></center>
			    <div class="row">
                    <div class="form-group col-md-4">
						<label  class="input">Total <?php echo $trans_multi_label; ?></label>
                        <input class="form-control" name="numberUnit" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="numberUnit" required='' value="<?php echo $numberUnit; ?>" oninput="calcInterp()" onkeyup="checkDec(this);" /></label>
                    </div>
					<div class="form-group col-md-4">
						<label  class="input">Rate per <?php echo $trans_single_label; ?></label>
                        <input readonly class="form-control" name="rpU" type="number" min="0.01" <?php if($row['source']!="Sign Language (BSL)"){ ?> max="0.20" <?php } ?> id="rpU" required='' value="<?php echo $rpU; ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
				    </div>
					<div class="form-group col-md-4">
						<label  class="input">Calculated Value</label>
                        <input class="form-control" name="units_rate" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="units_rate" style="background-color:#CCC;" value="<?php echo $units_rate ?>" readonly/></label>
                    </div>   
				</div>
                <div class="row">
					<div class="form-group col-md-4">
						<label  class="input">Any other Charges</label>
                        <input class="form-control" name="otherCharg" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="otherCharg" required='' value="<?php echo $otherCharg; ?>"  oninput="calcInterp()" onkeyup="checkDec(this);"/>
                  </div>
		        </div>
                  <hr>
                  <div class="row">
					<div class="form-group col-md-4">
						<label  class="input">Total Cost</label>
                        <input class="form-control" name="total_charges_interp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_interp" value="<?php echo $total_charges_interp ?>" readonly/></label>
		            </div>
                        <div class="form-group col-md-4">
                            <label class="input">Upload your Time Sheet (Scaned / Picture) <i class="fa fa-question-circle" title="Acceptable Formats: gif, jpeg, jpg, png, pdf, doc, docx, xlsx"></i></label>
                            <input class="form-control" name="time_sheet" type="file" placeholder='' id="time_sheet" <?php if($time_sheet== NULL){ ?>required <?php } ?> />
                        </div>
                        <div class="form-group col-md-4">
                            <?php if($time_sheet!=''){ ?>
                            <label class="input">View your Time Sheet</label>
                            <a href="javascript:void(0)" onClick="MM_openBrWindow('timesheet_view.php?t_id=<?php echo $update_id; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=400,left=400,top=200')"><img src="lsuk_system/images/images.jpg" width="30" height="30" title="View Time Sheet"></a>
                            <?php }else{?> <label class="text-danger">Timesheet is not uploaded!<img src="lsuk_system/images/missing.jpg" width="50" height="50" title="Time Sheet is missing for this JOB!">
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
            	   <script language="javascript" type="text/javascript">
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
    
    <!-- begin footer -->
	<?php include'source/footer.php'; ?>
	<!-- end footer -->  
</div>
<!-- end container -->
<?php if(isset($_POST['submit'])){
    if(!empty($_POST['numberUnit']) && $_POST['numberUnit']!=0){ ?>
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
} ?>
</body>
</html>