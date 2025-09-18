<?php session_start(); include'db.php'; include'class.php'; $table='telephone';$update_id= @$_GET['update_id'];
$query="SELECT * FROM $table where id=$update_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$bookinType=$row['bookinType'];$hoursWorkd=$row['C_hoursWorkd'];$chargInterp=$row['C_chargInterp'];$rateHour=$row['C_rateHour'];$C_otherCharges=$row['C_otherCharges'];$bookinType=$row['bookinType'];$total_charges_comp=$row['total_charges_comp'];$C_admnchargs=$row['C_admnchargs'];$cur_vat=$row['cur_vat'];$porder=$row['porder'];$C_comments=$row['C_comments'];$orgName=$row['orgName'];$assignDate=$row['assignDate'];
  $vat_no_comp=$row['vat_no_comp'];$chk_hoursWorkd=$row['hoursWorkd']?:0;?>
<?php $interp_rpm=$acttObj->unique_data('booking_type','rate','title',$bookinType)?:0;
//Get company requirements
  $get_comp=$acttObj->read_specific("admin_ch,admin_rate","comp_reg","abrv='".$orgName."'");
  $admin_ch=$get_comp['admin_ch'];
  $admin_rate=$get_comp['admin_rate'];
if(date('Y-m-d H:i',strtotime($row['assignDate'].' '.$row['assignTime']))>date('Y-m-d H:i')){
    $problem_hours=1;
    $problem_msg='Assignment Date & Time: <b class="text-danger">'.$row['assignDate'].' '.$row['assignTime'].'</b><br><br>This job is not completed yet! Thank you';
}else if($row['deleted_flag']==1 || $row['order_cancel_flag']==1){
    $problem_hours=1;
    $problem_msg='This job is in processing mode! Thank you';
}else{
    $problem_hours=0;
    $problem_msg='';
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Client Expenses - Telephone Interpreting</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
        <style>.cls_danger{background:red;border:2px solid black;color: white;font-weight: bold;}</style>
    <script src="js/jquery-1.11.3.min.js"></script>
                    <script>	
		function calcInterp() {
		var hoursWorkd = parseFloat(document.getElementById('hoursWorkd').value);	
		var rateHour = parseFloat(document.getElementById('rateHour').value);
		var chargInterp = document.getElementById('chargInterp');	
		var x = rateHour * hoursWorkd;
		chargInterp.value = x;
			
		var C_otherCharges = parseFloat(document.getElementById('C_otherCharges').value);
				
		var C_admnchargs = parseFloat(document.getElementById('C_admnchargs').value);

		total_charges_comp.value=parseFloat(x+C_otherCharges+C_admnchargs);
		}
		function accurate_values(){
		    var actual_hours=parseFloat('<?php echo $chk_hoursWorkd; ?>');
		    if(parseFloat($('#hoursWorkd').val())<actual_hours){
		        $('#hoursWorkd').addClass('cls_danger');
		        $('#hoursWorkd').attr('title','Hours Worked value must be atleast '+actual_hours);
            $('#btn_submit_expense').attr("disabled","disabled");
		    }else{
		        $('#hoursWorkd').removeClass('cls_danger');
            $('#btn_submit_expense').removeAttr("disabled");
		    }
		}
        function checkDec(el){
         var ex = /^[0-9]+\.?[0-9]*$/;
         if(ex.test(el.value)==false){
           el.value = 0;
           el.select();
           calcInterp();
           accurate_values();
          }
        }
    function fun_vat_no(){
        var cur_vat=document.getElementById("cur_vat").value;
        var vat_no_comp=document.getElementById("vat_no_comp");
        var div_vat_no=document.getElementById("div_vat_no");
        if (!isNaN(cur_vat) && cur_vat!=0){
            div_vat_no.style.display='inline';
            vat_no_comp.setAttribute("required", "required");
        }else{
            div_vat_no.style.display='none';
            vat_no_comp.removeAttribute("required", "required");
        }
    }
        
        </script>
    </head>
<body>
<?php if($problem_hours==1){?>
    <center><br><br><h3><?php echo isset($problem_msg) && !empty($problem_msg)?$problem_msg:''; ?></h3>
						<br><br><a class="btn btn-primary" href="javascript:void(0)" onclick="window.close();"><i class="glyphicon glyphicon-arrow-left"></i> Go Back</a></center>
<?php }else{ ?>
        <form action="" method="post" class="register">
            <div class="col-xs-12 text-center"><h4>Telephone - Update Client Expenses For Invoicing: <span style="color:#F00;"><?php echo $orgName. ' ( '.$assignDate.' )'; ?></span></h4>
          </div>
          <div class="bg-info col-xs-12 form-group"><h4>Fixed Rate or Per Minute Rate (As Agreed) (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)</h4>
            </div>
            <div class="form-group col-md-4 col-sm-3">
              <p>Duration (in Minutes - Or 1 if Fixed)</p>
                    <input class="form-control" name="hoursWorkd" type="text" id="hoursWorkd" value="<?php echo $hoursWorkd ?>"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01"oninput="calcInterp();" onkeyup="checkDec(this);accurate_values();"/><?php if(isset($_POST['submit'])){$c7=$_POST['hoursWorkd'];$acttObj->editFun($table,$update_id,'C_hoursWorkd',$c7);} ?>
                  <?php if(@$_SESSION['prv']=='Management' || @$_SESSION['prv']=='Finance' ){?>  
            </div>
            <div class="form-group col-md-4 col-sm-3"><p>Rate Per Minute
           </p>
               <input class="form-control" name="rateHour" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rateHour" value="<?php if($rateHour!=0){echo $rateHour ;}else{echo $interp_rpm;} ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
              
               <?php if(isset($_POST['submit'])){$c8=$_POST['rateHour'];$acttObj->editFun($table,$update_id,'C_rateHour',$c8);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
              <p>Interpreting Time Charge
                </p>
                  <input class="form-control" name="chargInterp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargInterp" value="<?php echo $chargInterp ?>"readonly/>
              <?php if(isset($_POST['submit'])){$c9=$_POST['chargInterp'];$acttObj->editFun($table,$update_id,'C_chargInterp',$c9);} ?>
            <?php } ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
               <p>Other Charges (If Applicable)</p>
               <input class="form-control" name="C_otherCharges" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_otherCharges" value="<?php echo $C_otherCharges ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-md-4 col-sm-3">
                  <p> Admin Charges (<?php echo $admin_ch==1?'<span class="label label-danger">Must be filled</span>':'Not Applicable'; ?>)</p>
      <input <?php if($admin_ch==1){ echo 'title="Admin charge must be filled!" style="border:1px solid red;"';} ?> class="form-control" name="C_admnchargs" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="C_admnchargs" required=''value="<?php echo $C_admnchargs; ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
               <?php if(isset($_POST['submit'])){$c10=$_POST['C_otherCharges'];$acttObj->editFun($table,$update_id,'C_otherCharges',$c10);} ?>
           <?php if(isset($_POST['submit'])){$c1=$_POST['C_admnchargs']; $acttObj->editFun($table,$update_id,'C_admnchargs',$c1);}?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
                <p>Total
                </p>
                  <input class="form-control" name="total_charges_comp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_comp" value="<?php echo $total_charges_comp ?>" readonly/>
              <?php if(isset($_POST['submit'])){$c10=$_POST['total_charges_comp'];$acttObj->editFun($table,$update_id,'total_charges_comp',$c10);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
           <p style="color:#F00">Current VAT @ % </p>
                  <input class="form-control" name="cur_vat" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="cur_vat" value="<?php if($cur_vat==0){echo 0.2;}else{echo $cur_vat;}?>" placeholder='' required='' oninput="calcInterp();fun_vat_no();" onkeyup="checkDec(this);"/>
      <?php 
      if(isset($_POST['submit']))
      {
        $c20=$_POST['cur_vat'];
        $acttObj->editFun($table,$update_id,'cur_vat',$c20);
      } 
      ?>
            </div>
            <div class="form-group col-md-4 col-sm-3" <?php if(!empty($cur_vat) && $cur_vat!=0){ echo 'style="display:inline"';}else{ echo 'style="display:none"';} ?> id="div_vat_no">
           <p style="color:#F00">VAT Number (if any) </p>
                  <input class="form-control" name="vat_no_comp" type="text"  id="vat_no_comp" value="<?php echo $vat_no_comp;?>" placeholder='Write VAT Number'/>
      <?php 
      if(isset($_POST['submit']))
      {
        $vat_no_post=$_POST['vat_no_comp'];
        $acttObj->editFun($table,$update_id,'vat_no_comp',$vat_no_post);
      } 
      ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
                <p><strong><em>Purchase Order No.</em></strong></p>
                  <input class="form-control" name="porder" type="text" id="porder" value="<?php echo $porder ?>" placeholder='' readonly="readonly" />
            </div>
            <div class="form-group col-sm-6">
              <textarea placeholder="Notes if Any 1000 alphabets" class="form-control" name="C_comments" rows="3" id="C_comments"><?php echo $C_comments; ?></textarea>
                    <?php if(isset($_POST['submit'])){$data=$_POST['C_comments'];$acttObj->editFun($table,$update_id,'C_comments',$data);} ?>
            </div>
            <div class="form-group col-md-3 col-sm-3">
                   	    <button class="btn btn-info" style="border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit" id="btn_submit_expense">Submit &raquo;</button></p></div>
        </form>
<?php if(isset($_POST['submit'])){
    if($_SESSION['Temp']==1){
        $acttObj->editFun($table,$update_id,'is_temp',1);
    }
$acttObj->editFun($table,$update_id,'comp_hrsubmited',ucwords($_SESSION['UserName']));$acttObj->editFun($table,$update_id,'comp_hr_date',$misc->sys_date_db()); ?>
<script>
alert("Job's company expenses has been added uccessfully!");
window.onunload = refreshParent;
function refreshParent() {window.opener.location.reload();}</script>
<?php }
} ?>
</body>
</html>