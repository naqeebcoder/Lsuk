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

$query="SELECT $table.*,interpreter_reg.name,interpreter_reg.code  FROM $table
	   JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
   where $table.id=$update_id";			
$result = mysqli_query($con,$query);
  $row = mysqli_fetch_array($result);
  $intrpName=$row['intrpName'];
  $interp_name=$row['name'];
  $code=$row['code'];
  $bookinType=$row['bookinType'];
  $hoursWorkd=$row['hoursWorkd'];
  $chargInterp=$row['chargInterp'];
  $rateHour=$row['rateHour'];
  $travelMile=$row['travelMile'];
  $rateMile=$row['rateMile'];
  $chargeTravel=$row['chargeTravel'];
  $travelCost=$row['travelCost'];
  $otherCost=$row['otherCost'];
  $travelTimeHour=$row['travelTimeHour'];
  $travelTimeRate=$row['travelTimeRate'];
  $chargeTravelTime=$row['chargeTravelTime'];
  $dueDate=$row['dueDate'];
  $tAmount=$row['tAmount'];
  $admnchargs=$row['admnchargs'];
  $deduction=$row['deduction'];
  $total_charges_interp=$row['total_charges_interp'];
  $exp_remrks=$row['exp_remrks'];
  $ni_dedu=$row['ni_dedu'];
  $tax_dedu=$row['tax_dedu'];
  $assignDate=$row['assignDate'];
  $orgName=$row['orgName'];
  $int_vat=$row['int_vat'];
  $vat_no_int=$row['vat_no_int'];
  if ($misc->IsDatedNull($dueDate))
  {
    $dateAssignStart=date_create($assignDate);
    $assignDay=date_format($dateAssignStart, 'd');
    $assignMonth=date_format($dateAssignStart, 'm');
    $assignYear=date_format($dateAssignStart, 'Y');
    if ($assignDay>=11)
    {
      $assignMonth++;
      if ($assignMonth>12)
      {
        $assignMonth=1;
        $assignYear++;        
      }
    }

    //get last day of week
    $nextMonth=$assignMonth;
    $nextYear=$assignYear;

    $nextMonth++;
    if ($nextMonth>12)
    {
      $nextMonth=1;
      $nextYear++;        
    }

    $dateNext=date_create("$nextYear-$nextMonth-1");
    $dateNextStr=date_format($dateNext, "Y-m-d");

    $dueLastStr=$misc->add_in_date($dateNextStr,-1);

    $dateLast=date_create($dueLastStr);
    $dueDayStr=date_format($dateLast, 'd');

    $dateDue=date_create("$assignYear-$assignMonth-$dueDayStr");
	$dueDate=date_format($dateDue, "Y-m-d");
  }
?>

<?php   $interp_rph=$acttObj->unique_data('interpreter_reg','rph','id',$intrpName) ; ?>
<?php   $interp_rte=$acttObj->unique_data('interpreter_reg','ratetravelexpmile','id',$intrpName) ; ?>
<?php   $interp_rtw=$acttObj->unique_data('interpreter_reg','ratetravelworkmile','id',$intrpName) ; 
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
        <title>Interpreter Expenses - Face to Face Interpreting</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<head>

        <script>	
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
		var int_vat = document.getElementById('int_vat').value;	
		var z = travelTimeHour * travelTimeRate;
		chargeTravelTime.value = z.toFixed(2);
		var otherCost = parseFloat(document.getElementById('otherCost').value);	
		var deduction = parseFloat(document.getElementById('deduction').value);	
		var admnchargs = parseFloat(document.getElementById('admnchargs').value);
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
    function fun_vat_no(){
        var int_vat=document.getElementById("int_vat").value;
        var vat_no_int=document.getElementById("vat_no_int");
        var div_vat_no=document.getElementById("div_vat_no");
        if (!isNaN(int_vat) && int_vat!=0){
            div_vat_no.style.display='inline';
            vat_no_int.setAttribute("required", "required");
        }else{
            div_vat_no.style.display='none';
            vat_no_int.removeAttribute("required", "required");
        }
    }
        
        </script>
    </head>
<body>
    <?php if($problem_hours==1){?>
    <center><br><br><h3><?php echo isset($problem_msg) && !empty($problem_msg)?$problem_msg:''; ?></h3>
						<br><br><a class="btn btn-primary" href="javascript:void(0)" onclick="window.close();"><i class="glyphicon glyphicon-arrow-left"></i> Go Back</a></center>
<?php }else{ ?>
        <form action="" method="post" class="register" id="frm_expenses">
        <input type="hidden" id="edit_idd" value="<?php echo $intrpName?>" readonly/>
        <input type="hidden" id="namee" value="<?php echo $interp_name?>" readonly/>
        <input type="hidden" id="orgName" value="<?php echo $orgName?>" readonly/>
        <input type="hidden" id="code_qss" value="<?php echo $code?>" readonly/>
          <div class="col-xs-12 text-center"><h4>Face To Face  - Update Interpreter Expenses For <span style="color:#F00;"><?php echo $interp_name. ' ( '.$assignDate.' )'; ?></span></h4>
          </div>
          <div class="bg-info col-xs-12 form-group"><h4>Fixed Rate or Per Hour Rate (As Agreed) (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)</h4>
          </div>
      <div class="form-group col-md-3 col-sm-6">
              <p>Hours Worked</p>
                    <input title="(Actual or Minimum Agreed Interpreting Time)" class="form-control" name="hoursWorkd" type="text" id="hoursWorkd" value="<?php echo $hoursWorkd ?>"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" oninput="calcInterp()" onkeyup="checkDec(this);"/> <?php if(isset($_POST['submit'])){$c7=$_POST['hoursWorkd'];$acttObj->editFun($table,$update_id,'hoursWorkd',$c7);} ?>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                   <p>Rate Per Hour</p>
               <input class="form-control" name="rateHour" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rateHour" value="<?php if($rateHour!=0){echo $rateHour ;}else{echo $interp_rph;} ?>" 
                  oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-md-3 col-sm-6">
              <p>Interpreting Time Payment</p>
                  <input class="form-control" name="chargInterp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargInterp" value="<?php echo $chargInterp ?>" readonly/>
            </div>
            <div class="form-group col-md-3 col-sm-6">
               <p>Due Date for Bill Payment</p>
               <input class="form-control" name="dueDate" type="date" id="dueDate" value="<?php echo $dueDate ?>"/>
            </div>
            <!--<div class="form-group col-md-4 col-sm-6">-->
            <!--   <p>Assignment Start Time<p>-->
            <!--    <input class="form-control" type="text" name="name0" id="text"/>-->
            <!--</div>-->
            <!--<div class="form-group col-md-4 col-sm-6">-->
            <!--   <p>Assignment End Time<p>-->
            <!--    <input class="form-control" type="text" name="name0" id="text"/>-->
            <!--</div>-->
            <div class="bg-info col-xs-12 form-group"><h4>Travel Time</h4></div>
            <div class="form-group col-md-4 col-sm-6">
                  <p> Travel Hours </p>
                  <input class="form-control" name="travelTimeHour" type="text" id="travelTimeHour" value="<?php echo $travelTimeHour ?>" placeholder=''  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <p>Rate Per Hour (Travel Time)</p>
                <input class="form-control" name="travelTimeRate" type="text" id="travelTimeRate"  
                  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" 
                  value="<?php if($travelTimeRate!=0){echo $travelTimeRate ;}else{echo $interp_rtw;} ?>" 
                  placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
          <p>Travel Time Payment </p>
                  <input class="form-control" name="chargeTravelTime" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargeTravelTime" value="<?php echo $chargeTravelTime ?>" placeholder='' readonly/>
                </div>
          <div class="bg-info col-xs-12 form-group"><h4>Travel Costs</h4>
          </div>
            </div>
            <div class="form-group col-md-4 col-sm-6">
            <p>Travel Mileage</p>
            <input class="form-control long" name="travelMile" type="text" id="travelMile"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelMile ?>" oninput="calcInterp()" onkeyup="checkDec(this);" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
<p>Rate Per Mile &pound;</p>
                    
    <input class="form-control long" name="rateMile" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" 
            id="rateMile" 
      value="<?php if($rateMile!=0){echo $rateMile ;}else{echo $interp_rte;} ?>" 
      oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                  <p> Mileage Cost &pound;</p>
                    <input class="form-control" name="chargeTravel" type="text" id="chargeTravel"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" style="border:1px solid #CCC" value="<?php echo $chargeTravel ?>" placeholder='' readonly/>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <p>Travel Cost</p>
                    <input title="(Public Transport or Fixed Travel Allowance)" class="form-control" name="travelCost" type="text" id="travelCost"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelCost ?>" placeholder=''oninput="calcInterp()" onkeyup="checkDec(this);" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <p>Additional Payment (If Applicable)
    </p>
                    <input class="form-control" name="admnchargs" type="text" id="admnchargs"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $admnchargs ?>" placeholder=''oninput="calcInterp()" onkeyup="checkDec(this);" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <p>Other Costs
</p>
                    <input title="(Parking , Bridge Toll) (If Applicable)" class="form-control" name="otherCost" type="text" id="otherCost"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $otherCost ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-md-4 col-sm-6">
      <p>Deduction</p>
                    <input title="(No or Late Attendance or DBS Fee, etc (If Applicable)" class="form-control" name="deduction" type="text" id="deduction"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $deduction ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-md-4 col-sm-6">
      <p>National Insurance Deduction (If Applicable)
</p>
                    <input class="form-control" name="ni_dedu" type="text" id="ni_dedu"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $ni_dedu ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-md-4 col-sm-6">
      <p>Tax Deduction (If Applicable)
</p>
                    <input class="form-control" name="tax_dedu" type="text" id="tax_dedu"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $tax_dedu ?>" placeholder='' oninput="calcInterp()" onkeyup="checkDec(this);"/>
            </div>
            <div class="form-group col-sm-4">
           <p style="color:#F00">Current VAT % </p>
                  <input class="form-control" name="int_vat" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="int_vat" value="<?php echo $int_vat?:0;?>" placeholder='' oninput="calcInterp();fun_vat_no();" onkeyup="checkDec(this);" required=''/>
            </div>
            <div class="form-group col-sm-4" <?php if(!empty($int_vat) && $int_vat!=0){ echo 'style="display:inline"';}else{ echo 'style="display:none"';} ?> id="div_vat_no">
           <p style="color:#F00">VAT Number (if any) </p>
                  <input class="form-control" name="vat_no_int" type="text" id="vat_no_int" value="<?php echo $vat_no_int;?>" placeholder=''/>
            </div>
            <div class="form-group col-sm-4">
      <p>Total Payment</p>
                    <input  class="form-control"name="totalChages" type="text" id="totalChages"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $total_charges_interp ?>" placeholder=''readonly />
            </div>
            <div class="form-group col-sm-7">
              <textarea placeholder="Notes (If Any) 1000 Characters, Please enter notes for future reference, details of all deductions or additional payments" class="form-control" name="exp_remrks" rows="3" id="exp_remrks"><?php echo $exp_remrks ?></textarea>
              </div>
            <div id="div_further" style="display:none;">
            <div class="form-group col-sm-5">
            <label id="lbl_feedback">Do you want to add feedback from timesheet?</label>
            <select class="form-control" onchange="check_func(this);" id="check_further" required=''>
                   <option value=""></option>
                   <option value="yes">Proceed with feedback (on timesheet)</option>
                   <option value="no">No Feedback (on timesheet)</option>
                   </select>
                   </div>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                   	    <button class="btn btn-info" style="border-color: #000000;color: black;text-transform: uppercase;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="button" name="submit" id="btn_submit_expense">Submit &raquo;</button></p></div>

   <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg" style="width: 820px;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Feedback For Interpreter</h4>
      </div>
      <div class="modal-body" id="myModalBody">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
</form>
<script>
//ajax call for interpreter data
function get_interp_data(){
  var edit_idd=$('#edit_idd').val();
  var namee=$('#namee').val();
  var orgName=$('#orgName').val();
  var code_qss=$('#code_qss').val();
  var order_id='<?php echo $update_id ?>';
  $.ajax({
    url:'get_interp_data.php',
    method:'post',
    data:{edit_idd:edit_idd,code_qss:code_qss,namee:namee,orgName:orgName,order_id:order_id},
    success:function(data){
      $('#myModalBody').html(data);
      $('#myModal').modal("show");
    }, error: function(xhr){
      alert("An error occured: " + xhr.status + " " + xhr.statusText);
    }
  });
}
//window function
function MM_openBrWindow(theURL,winName,features) {
  window.open(theURL,winName,features);}
  function refreshParent() 
{
  window.opener.location.reload();
}
//end of ajax call
function check_func(element){
if(element.value==""){
document.getElementById('btn_submit_expense').disabled='true';
}else if(element.value=="yes"){
get_interp_data();
document.getElementById('btn_submit_expense').disabled='false';
}else if(element.value=="no"){
$("#lbl_feedback").hide();
$("#check_further").before( "<label id='lbl_feedback'>Do you want to add future job?</label>" );
$("#check_further").empty();
$("#check_further").append("<option value=''></option>");
$("#check_further").append("<option value='no_future'>Not for Future Job (on timesheet)</option>");
$("#check_further").append("<option value='future'>Future Job (on timesheet)</option>");
document.getElementById('btn_submit_expense').disabled='false';
}else if(element.value=="future"){
self.close();
MM_openBrWindow('interp_edit.php?edit_id=<?php echo $update_id; ?>&duplicate=<?php echo 'yes'; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650');
document.getElementById('btn_submit_expense').disabled='false';
}else if(element.value=="no_future"){
self.close();
document.getElementById('btn_submit_expense').disabled='false';
}else{
document.getElementById('btn_submit_expense').disabled='true';
}
}
// end of function

  $("#btn_submit_expense").click(function(){
var form_elements = document.getElementById('frm_expenses').elements;
var hoursWorkd= form_elements['hoursWorkd'].value;
var rateHour= form_elements['rateHour'].value;
var chargInterp= form_elements['chargInterp'].value;
var dueDate= form_elements['dueDate'].value;
var travelMile= form_elements['travelMile'].value;
var rateMile= form_elements['rateMile'].value;
var chargeTravel= form_elements['chargeTravel'].value;
var travelCost= form_elements['travelCost'].value;
var admnchargs= form_elements['admnchargs'].value;
var otherCost= form_elements['otherCost'].value;
var deduction= form_elements['deduction'].value;
var ni_dedu= form_elements['ni_dedu'].value;
var tax_dedu= form_elements['tax_dedu'].value;
var totalChages= form_elements['totalChages'].value;
var travelTimeHour= form_elements['travelTimeHour'].value;
var travelTimeRate= form_elements['travelTimeRate'].value;
var chargeTravelTime= form_elements['chargeTravelTime'].value;
var int_vat= form_elements['int_vat'].value;
var vat_no_int= form_elements['vat_no_int'].value;
var exp_remrks= form_elements['exp_remrks'].value;
var update_id= '<?php echo $update_id; ?>';
var UserName= '<?php echo $_SESSION['UserName'] ?>';
var interp_hr_date= '<?php echo $misc->sys_date_db() ?>';
if(hoursWorkd && hoursWorkd>0){
    if(int_vat=='0' || (int_vat!='0' && vat_no_int!='')){
$.ajax({
url:"store_expenses.php",
type: "POST",
data:{hoursWorkd: hoursWorkd,rateHour: rateHour,chargInterp: chargInterp,
dueDate: dueDate,travelMile: travelMile,rateMile: rateMile,chargeTravel: chargeTravel,
travelCost: travelCost,admnchargs: admnchargs,otherCost: otherCost,deduction: deduction,ni_dedu: ni_dedu,
tax_dedu: tax_dedu,totalChages: totalChages,travelTimeHour: travelTimeHour,travelTimeRate: travelTimeRate
,chargeTravelTime: chargeTravelTime,exp_remrks: exp_remrks,update_id: update_id,UserName: UserName
,interp_hr_date: interp_hr_date,int_vat: int_vat,vat_no_int: vat_no_int},
success: function (data){
if(data=='1'){
window.onunload = refreshParent;
$("#div_further").show();
$("#btn_submit_expense").hide();
}
}, error: function(xhr){
        alert("An error occured: " + xhr.status + " " + xhr.statusText);
      }
});
}else{
    alert('You must enter VAT Number for entered VAT value !');
    form_elements['vat_no_int'].focus();
}
}else{
    alert('Hours worked value must be greater than 0 ');
    form_elements['hoursWorkd'].focus();
}
});

</script>
<?php } ?>
</body>
</html>