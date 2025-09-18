<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php 
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 

include'db.php'; 
include'class.php'; 

$table='interpreter';
$update_id= @$_GET['update_id'];
$query="SELECT * FROM $table where id=$update_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
  $chk_hoursWorkd=$row['hoursWorkd']?:0;
  $hoursWorkd=$row['C_hoursWorkd']?:0;
  $chargInterp=$row['C_chargInterp'];
  $rateHour=$row['C_rateHour']?:0;
  $travelMile=$row['C_travelMile'];
  $rateMile=$row['C_rateMile'];
  $chargeTravel=$row['C_chargeTravel'];
  $travelCost=$row['C_travelCost'];
  $otherCost=$row['C_otherCost'];
  $travelTimeHour=$row['C_travelTimeHour'];
  $travelTimeRate=$row['C_travelTimeRate'];
  $chargeTravelTime=$row['C_chargeTravelTime'];
  $C_deduction=$row['C_deduction'];
  $bookinType=$row['bookinType'];
  $C_admnchargs=$row['C_admnchargs'];
  $cur_vat=$row['cur_vat'];
  $C_otherexpns=$row['C_otherexpns'];
  $total_charges_comp=$row['total_charges_comp'];
  $porder=$row['porder'];
  $C_comments=$row['C_comments'];
  $orgName=$row['orgName'];
  $assignDate=$row['assignDate'];
  $vat_no_comp=$row['vat_no_comp'];
  $interp_rph=$acttObj->unique_data('booking_type','rate','title',$bookinType)?:0; 
  //Get company requirements
  $get_comp=$acttObj->read_specific("admin_ch,admin_rate,tr_time,tr_rate,interp_time","comp_reg","abrv='".$orgName."'");
  $admin_ch=$get_comp['admin_ch'];
  $tr_time=$get_comp['tr_time'];
  $interp_time=$get_comp['interp_time'];
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
        <title>Client Expenses - Face to Face Interpreting</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
        <style>.cls_danger{background:red;border:2px solid black;color: white;font-weight: bold;}</style>
    <script src="js/jquery-1.11.3.min.js"></script>
    <script>	
		function calcInterp() 
    {
      var C_admnchargs = parseFloat(document.getElementById('C_admnchargs').value);	
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
		var travelCost = parseFloat(document.getElementById('travelCost').value);
		var z = travelTimeHour * travelTimeRate;
		chargeTravelTime.value = z;		
		
		var otherCost = parseFloat(document.getElementById('otherCost').value);	
		var deduction = parseFloat(document.getElementById('deduction').value);	
		
		C_otherexpns.value=parseFloat(otherCost+travelCost);
		totalChages.value=parseFloat(x+y+z)-parseFloat(deduction)+C_admnchargs;
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
            div_vat_no.style.display='block';
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
          <div class="col-xs-12 text-center"><h4>Face To Face - Update Client Expenses For Invoicing: <span style="color:#F00;"><?php echo $orgName. ' ( '.$assignDate.' )'; ?></span></h4>
          </div>
          <div class="bg-info col-xs-12 form-group"><h4>Fixed Rate or Hourly Charge (As Agreed) (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)</h4>
            </div>
            <div class="form-group col-md-4 col-sm-3">
              <p>Interpreting Time (<?php echo 'Atleast '.$chk_hoursWorkd.' Hours'; ?>) <i class="glyphicon glyphicon-question-sign" title="Enter Minimum or Requested Hours if less Than Actual Hours"></i></p>
                    <input <?php if($interp_time==1){ echo 'style="border:1px solid red;"';} ?> class="form-control" title="Enter Minimum or Requested Hours if less Than Actual Hours" name="hoursWorkd" type="text" id="hoursWorkd" value="<?php echo $hoursWorkd ?>"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" oninput="calcInterp();accurate_values();" onkeyup="checkDec(this);"/> 
        
        <?php if(isset($_POST['submit']))
        {
          $c7=$_POST['hoursWorkd'];
          $acttObj->editFun($table,$update_id,'C_hoursWorkd',$c7);
        } 
        ?>
                   
                      <?php if(@$_SESSION['prv']=='Management' || @$_SESSION['prv']=='Finance' ){?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
               <p>        Rate Per Hour
           </p>
               <input <?php if($interp_time==1){ echo 'title="Interpreting charge must be filled!" style="border:1px solid red;"';} ?> class="form-control" name="rateHour" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rateHour" value="<?php if($rateHour!=0){echo $rateHour;}else{echo $interp_rph;} ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
             
            <?php 
            if(isset($_POST['submit']))
            {
              $c8=$_POST['rateHour'];
              $acttObj->editFun($table,$update_id,'C_rateHour',$c8);
            } 
            ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
              <p> Interpreting Time Charge
                </p>
                  <input class="form-control" name="chargInterp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargInterp" value="<?php echo $chargInterp ?>"/>
            <?php 
            if(isset($_POST['submit']))
            {
              $c9=$_POST['chargInterp'];
              $acttObj->editFun($table,$update_id,'C_chargInterp',$c9);
            } 
            ?>
            </div>
            <div class="bg-info col-xs-12 form-group"><h4>Travel Time</h4></div>
            </div>
            <div class="form-group col-md-4 col-sm-3">
                  <p> Travel Time (<?php echo $tr_time==1?'<span class="label label-danger">Hours be filled</span>':'Not Applicable'; ?>) </p>
                  <input <?php if($tr_time==1){ echo 'style="border:1px solid red;"';} ?> class="form-control" name="travelTimeHour" type="text" id="travelTimeHour" value="<?php echo $travelTimeHour; ?>" placeholder=''  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" oninput="calcInterp()" onkeyup="checkDec(this);"/>
      <?php 
      if(isset($_POST['submit']))
      {
        $c18=$_POST['travelTimeHour'];
        $acttObj->editFun($table,$update_id,'C_travelTimeHour',$c18);
      } 
      ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
                <p>Rate Per Hour (<?php echo $tr_time==1?'<span class="label label-danger">Must be filled</span>':'Not Applicable'; ?>)</p>
                <input <?php if($tr_time==1){ echo 'title="Travel charge must be filled!" style="border:1px solid red;"';} ?> class="form-control" name="travelTimeRate" type="text" id="travelTimeRate"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelTimeRate; ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);"/>
      <?php 
      if(isset($_POST['submit']))
      {
        $c19=$_POST['travelTimeRate'];
        $acttObj->editFun($table,$update_id,'C_travelTimeRate',$c19);
      } 
      ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
          <p>Travel Time Charge</p>
                  <input class="form-control" name="chargeTravelTime" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargeTravelTime" value="<?php echo $chargeTravelTime ?>" placeholder='' />
                
      <?php 
      if(isset($_POST['submit']))
      {
        $c20=$_POST['chargeTravelTime'];
        $acttObj->editFun($table,$update_id,'C_chargeTravelTime',$c20);
      } 
      ?>
            </div>
            <div class="form-group col-md-4 col-sm-3">
                <p><strong><em>Purchase Order No.</em></strong></p>
                  <input class="form-control" name="porder" type="text" id="porder" value="<?php echo $porder ?>" placeholder='' readonly="readonly" />
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
            <div class="form-group col-md-4 col-sm-3" <?php if(!empty($cur_vat) && $cur_vat!=0){ echo 'style="display:block"';}else{ echo 'style="display:none"';} ?> id="div_vat_no">
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
          <div class="bg-info col-xs-12 form-group"><h4>Travel Costs</h4></div>
            </div>
            <div class="form-group col-md-3 col-sm-3">
            <p>Travel Mileage </p>
            <input name="travelMile" type="text" class="form-control long" id="travelMile"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelMile ?>" oninput="calcInterp()" onkeyup="checkDec(this);"/>
        <?php 
        if(isset($_POST['submit']))
        {
          $c11=$_POST['travelMile'];
          $acttObj->editFun($table,$update_id,'C_travelMile',$c11);
        } 
        ?>
            </div>
            <div class="form-group col-md-3 col-sm-3">
<p>Rate Per Mile &pound;</p>
                    <input name="rateMile" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" class="form-control long" id="rateMile" value="<?php echo $rateMile ?>"oninput="calcInterp()" onkeyup="checkDec(this);"/>
                    
        <?php 
        if(isset($_POST['submit']))
        {
          $c12=$_POST['rateMile'];
          $acttObj->editFun($table,$update_id,'C_rateMile',$c12);
        } 
        ?>
            </div>
            <div class="form-group col-md-3 col-sm-3">
                  <p> Mileage Cost &pound;</p>
                    <input class="form-control" name="chargeTravel" type="text" id="chargeTravel"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" style="border:1px solid #CCC" value="<?php echo $chargeTravel ?>" placeholder='' />
        <?php 
        if(isset($_POST['submit']))
        {
          $c13=$_POST['chargeTravel'];
          $acttObj->editFun($table,$update_id,'C_chargeTravel',$c13);
        } 
        ?>
            </div>
            <div class="form-group col-md-3 col-sm-3">
                <p>Public Transport Cost <i class="glyphicon glyphicon-question-sign" title="(If Applicable)"></i>
    </p>
                    <input class="form-control" name="travelCost" type="text" id="travelCost"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelCost ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
        <?php 
        if(isset($_POST['submit']))
        {
          $c14=$_POST['travelCost'];
          $acttObj->editFun($table,$update_id,'C_travelCost',$c14);
        } 
        ?>
            </div>
            <div class="form-group col-md-3 col-sm-3">
                  <p> Admin Charges (<?php echo $admin_ch==1?'<span class="label label-danger">Must be filled</span>':'Not Applicable'; ?>) </p>
       <input <?php if($admin_ch==1){ echo 'title="Admin charge must be filled!" style="border:1px solid red;"';} ?> name="C_admnchargs" type="text" id="C_admnchargs"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" class="form-control" value="<?php echo $admin_ch==1 && $C_admnchargs==0?$admin_rate:$C_admnchargs; ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
    <?php 
    if(isset($_POST['submit']))
    {
      $c1=$_POST['C_admnchargs']; 
      $acttObj->editFun($table,$update_id,'C_admnchargs',$c1);
    }
    ?>
            </div>
            <div class="form-group col-md-3 col-sm-3">
                <p>Other Costs</p>
                    <input title="(Parking , Bridge Toll) (If Applicable)" class="form-control" name="otherCost" type="text" id="otherCost"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $otherCost ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);"/>
              
    <?php 
    if(isset($_POST['submit']))
    {
      $c15=$_POST['otherCost'];
      $acttObj->editFun($table,$update_id,'C_otherCost',$c15);
    } 
    ?>
            </div>
            <div class="form-group col-md-3 col-sm-3">
      <p>Deduction (If Applicable)
</p>
                    <input class="form-control" name="deduction" type="text" id="deduction"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_deduction ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);"/>
    <?php 
    if(isset($_POST['submit']))
    {
      $c15=$_POST['deduction'];
      $acttObj->editFun($table,$update_id,'C_deduction',$c15);
    } 
    ?>
            </div>
            <div class="form-group col-md-3 col-sm-3">
      <p>Other Expenses-Total 
</p>
                    <input class="form-control" name="C_otherexpns" type="text" id="C_otherexpns"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_otherexpns ?>" placeholder=''readonly />
      <?php 
      if(isset($_POST['submit']))
      {
        $data=$_POST['C_otherexpns'];
        $acttObj->editFun($table,$update_id,'C_otherexpns',$data);
      } 
      ?>
            </div>
            <div class="form-group col-md-3 col-sm-3">
      <label>Job Total</label>
                    <input title="(Excluding VAT and Non-VATable charge)" class="form-control" name="totalChages" type="text" id="totalChages"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $total_charges_comp ?>" placeholder=''readonly />
            </div>
            <div class="form-group col-sm-6">
              <textarea placeholder="Notes if Any 1000 alphabets" class="form-control" name="C_comments" rows="3" id="C_comments"><?php echo $C_comments; ?></textarea>
              <?php 
      if(isset($_POST['submit']))
      {
        $data=$_POST['C_comments'];
        $acttObj->editFun($table,$update_id,'C_comments',$data);
      } 
      ?>
              </div>
            <div class="form-group col-md-3 col-sm-3">
                   	    <button class="btn btn-info" style="border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit" id="btn_submit_expense">Submit &raquo;</button></div>
			<?php 
      if(isset($_POST['submit']))
      { 
        $total1=$c7 * $c8;
        $total2=$c18 * $c19; 
        $total3=$c11 * $c12; 
        $acttObj->editFun($table,$update_id,'total_charges_comp',$total1+$total2+$total3+$c1);
        $acttObj->editFun($table,$update_id,'comp_hrsubmited',ucwords($_SESSION['UserName']));
        $acttObj->editFun($table,$update_id,'comp_hr_date',$misc->sys_date_db());
      }

?> <?php } ?>
            
          
        </form>
<?php  if(isset($_POST['submit'])){
    if($_SESSION['Temp']==1){
        $acttObj->editFun($table,$update_id,'is_temp',1);
    }
	$acttObj->editFun($table,$update_id,'edited_by',$_SESSION['UserName']);
	$acttObj->editFun($table,$update_id,'edited_date',date("Y-m-d H:i:s")); 
	$acttObj->new_old_table('hist_'.$table,$table,$update_id);?>
<script>
alert("Job's company expenses has been added successfully!");
window.onunload = refreshParent;
function refreshParent() {window.opener.location.reload();}</script>
<?php }
} ?>
</body>
</html>