<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php 
include'db.php'; 
include'class.php';

if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 

$table='interp_assess';
$code_qs=$_GET['code_qs'];
$name=$_GET['name']; 

if(isset($_POST['submit']))
{
  $edit_id= $acttObj->get_id($table);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Order Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
<!--        for auto fil text boxez...........................-->
  <script src="js/jquery-1.11.3.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	$('#orgName').on('change', function (e) {
		var	orgName=	$('#orgName').val();		
		var stream='orgName='+orgName;
		$.ajax({			
			type:'POST',
			url:'get_values_autofil_textbox.php?table=comp_reg&comp=abrv&val='+orgName,
			data:stream,
			success:function(ajaxresult){
				obj = JSON.parse(ajaxresult);
				$('#orgContact').val(obj.contactPerson);
				$('#inchPerson').val(obj.contactPerson);
				$('#inchEmail').val(obj.email);				
				$('#inchContact').val(obj.contactNo);	
				$('#inchCity').val(obj.city);						
				$('#inchRoad').val(obj.inchRoad);						
				$('#inchNo').val(obj.inchNo);							
				$('#inchPcode').val(obj.inchPcode);							
				$('#line1').val(obj.line1);									
				$('#line2').val(obj.line2);							
				$('#rem_credit').val(obj.credit);						
				$('#porder').val(obj.porder);						
				$('#rem_bz_credit').val(obj.bz_credit);						
				$('#creditId').val(obj.creditId);									
			}
		});
	});
});
</script>
<!--     ////////   for auto fil text boxez...........................-->
    </head>
<body>    
        <form style="height:100%;" action="" method="post" class="register">
          <h1> Interpreter Assessment Form for <span style="color:#F00;"> <?php echo $name; ?></span></h1>
          <fieldset class="row1">
            <div><p><legend>Details
          </legend>
          
      <strong>
      <label> Client Name* </label>
      </strong>
      
      <select id="orgName" name="orgName" required=''>

        <?php 			
  $sql_opt="SELECT name,abrv,status FROM comp_reg 
  where status <> 'Company Seized trading in this name or Company closed' or 
  status <> 'Company Blacklisted'
  ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $status=$row_opt["status"];
    $name_opt=$row_opt["name"];
    $options.="<OPTION value='$code'>".$name_opt. '<span style="color:#F00;">('.$status.')</span>';}
?>
        <option value="">--Select--</option>
        <?php echo $options; ?>
        </option>
      </select>
      <?php if(isset($_POST['submit'])){$c1=$_POST['orgName']; $acttObj->editFun($table,$edit_id,'orgName',$c1);} ?></p>
       <p><label class="optional">Feedback Method</label>
      <select name="get_feedback" id="get_feedback" required=''>
      <option value="">--Select--</option>
      <option>Email</option>
      <option>Timesheet</option>
      <option>Phone</option>
      <option>Others</option>
      <option>Online</option>
      <option>App</option>
      </select>
         <?php 
         if(isset($_POST['submit']))
         {
           $c7=$_POST['get_feedback'];
           $acttObj->editFun($table,$edit_id,'get_feedback',$c7);
         } 
         ?>
        </p>
  </div>
  <div>
          <table width="100%" align="center" class="table table-hover">
  <tr>
    <td><strong>About</strong></td>
    <td><strong>Poor</strong></td>
    <td><strong>Average</strong></td>
    <td><strong>Fair</strong></td>
    <td><strong>Good</strong></td>
    <td><strong>Excellent</strong></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>Punctuality  * </td>
    <td><input type="radio" name="punctuality" id="punctuality" value="-5"  required=''/></td>
    <td><input type="radio" name="punctuality" id="punctuality" value="1"  required=''/></td>
    <td><input type="radio" name="punctuality" id="punctuality" value="5"  required=''/></td>
    <td><input type="radio" name="punctuality" id="punctuality" value="10"  required=''/></td>
    <td><input type="radio" name="punctuality" id="punctuality" value="15"  required=''/></td>
    <td><?php
     if(isset($_POST['submit']))
     {
       $c2=$_POST['punctuality']; 
       $acttObj->editFun($table,$edit_id,'punctuality',$c2);
       } ?></td>
    </tr>
  <tr>
    <td>Appearance  * </td>
    <td><input type="radio" name="appearance" id="appearance" value="-5"  required=''/></td>
    <td><input type="radio" name="appearance" id="appearance" value="1"  required=''/></td>
    <td><input type="radio" name="appearance" id="appearance" value="5"  required=''/></td>
    <td><input type="radio" name="appearance" id="appearance" value="10"  required=''/></td>
    <td><input type="radio" name="appearance" id="appearance" value="15"  required=''/></td>
    <td>
      <?php 
      if(isset($_POST['submit']))
      {
        $c2=$_POST['appearance']; 
        $acttObj->editFun($table,$edit_id,'appearance',$c2);
      } ?></td>
    </tr>
  <tr>
    <td>Professionalism  * </td>
    <td><input type="radio" name="professionalism" id="professionalism" value="-5"  required=''/></td>
    <td><input type="radio" name="professionalism" id="professionalism" value="1"  required=''/></td>
    <td><input type="radio" name="professionalism" id="professionalism" value="5"  required=''/></td>
    <td><input type="radio" name="professionalism" id="professionalism" value="10"  required=''/></td>
    <td><input type="radio" name="professionalism" id="professionalism" value="15"  required=''/></td>
    <td><?php if(isset($_POST['submit'])){$c2=$_POST['professionalism']; $acttObj->editFun($table,$edit_id,'professionalism',$c2);} ?></td>
    </tr>
  <tr>
    <td>Confidentiality  * </td>
    <td><input type="radio" name="confidentiality" id="confidentiality" value="-5"  required=''/></td>
    <td><input type="radio" name="confidentiality" id="confidentiality" value="1"  required=''/></td>
    <td><input type="radio" name="confidentiality" id="confidentiality" value="5"  required=''/></td>
    <td><input type="radio" name="confidentiality" id="confidentiality" value="10"  required=''/></td>
    <td><input type="radio" name="confidentiality" id="confidentiality" value="15"  required=''/></td>
    <td><?php if(isset($_POST['submit'])){$c2=$_POST['confidentiality']; $acttObj->editFun($table,$edit_id,'confidentiality',$c2);} ?></td>
    </tr>
  <tr>
    <td>Impartiality  * </td>
    <td><input type="radio" name="impartiality" id="impartiality" value="-5"  required=''/></td>
    <td><input type="radio" name="impartiality" id="impartiality" value="1"  required=''/></td>
    <td><input type="radio" name="impartiality" id="impartiality" value="5"  required=''/></td>
    <td><input type="radio" name="impartiality" id="impartiality" value="10"  required=''/></td>
    <td><input type="radio" name="impartiality" id="impartiality" value="15"  required=''/></td>
    <td><?php if(isset($_POST['submit'])){$c2=$_POST['impartiality']; $acttObj->editFun($table,$edit_id,'impartiality',$c2);} ?></td>
    </tr>
  <tr>
    <td>Accuracy  * </td>
    <td><input type="radio" name="accuracy" id="accuracy" value="-5"  required=''/></td>
    <td><input type="radio" name="accuracy" id="accuracy" value="1"  required=''/></td>
    <td><input type="radio" name="accuracy" id="accuracy" value="5"  required=''/></td>
    <td><input type="radio" name="accuracy" id="accuracy" value="10"  required=''/></td>
    <td><input type="radio" name="accuracy" id="accuracy" value="15"  required=''/></td>
    <td><?php if(isset($_POST['submit'])){$c2=$_POST['accuracy']; $acttObj->editFun($table,$edit_id,'accuracy',$c2);} ?></td>
    </tr>
  <tr>
    <td>Rapport  * </td>
    <td><input type="radio" name="rapport" id="rapport" value="-5"  required=''/></td>
    <td><input type="radio" name="rapport" id="rapport" value="1"  required=''/></td>
    <td><input type="radio" name="rapport" id="rapport" value="5"  required=''/></td>
    <td><input type="radio" name="rapport" id="rapport" value="10"  required=''/></td>
    <td><input type="radio" name="rapport" id="rapport" value="15"  required=''/></td>
    <td><?php if(isset($_POST['submit'])){$c2=$_POST['rapport']; $acttObj->editFun($table,$edit_id,'rapport',$c2);} ?></td>
    </tr>
  <tr>
    <td>Communication  * </td>
    <td><input type="radio" name="communication" id="communication" value="-5"  required=''/></td>
    <td><input type="radio" name="communication" id="communication" value="1"  required=''/></td>
    <td><input type="radio" name="communication" id="communication" value="5"  required=''/></td>
    <td><input type="radio" name="communication" id="communication" value="10"  required=''/></td>
    <td><input type="radio" name="communication" id="communication" value="15"  required=''/></td>
    <td><?php if(isset($_POST['submit'])){$c2=$_POST['communication']; $acttObj->editFun($table,$edit_id,'communication',$c2);} ?></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
          </table>
     
</div>
            <p>&nbsp;</p>
          </fieldset>

          <fieldset class="row1">
          <legend>Person Giving Feedback
          </legend>
            <p>
              <label class="optional">Name 
              </label>
              <textarea style="width:350px;" name="p_feedbackby" id="p_feedbackby" required=''></textarea>
              
              <?php 
              if(isset($_POST['submit']))
              {
                $c66=$_POST['p_feedbackby'];
                $acttObj->editFun($table,$edit_id,'p_feedbackby',$c66);
              } 
              ?>

            </p>
          </fieldset>
	  <fieldset class="row1">
          <legend>Invoice/Order Number *
          </legend>
            <p>
              <label class="optional">Invoice Number
              </label>
              <input type="text" style="width:350px;" class="form-control" name="order_id" id="order_id">
              <?php if(isset($_POST['submit'])){$c100=$_POST['order_id'];$acttObj->editFun($table,$edit_id,'order_id',$c100);} ?>
            </p>
          </fieldset>
          <fieldset class="row1">
            <legend>Positive Feedback 
          </legend>
            <p>
              <label class="optional"> Reason
              </label>
              <textarea style="width:350px;height:80px;" name="p_reason" id="p_reason" required='' oninput="handleReasonChange('p')"></textarea>
              <?php if(isset($_POST['submit']))
              {
                $c7=$_POST['p_reason'];
                $acttObj->editFun($table,$edit_id,'p_reason',$c7);
              } ?>
            </p>
          </fieldset>

          <fieldset class="row1">
            <legend>Negative Feedback 
          </legend>
            <p>
              <label class="optional"> Reason
              </label>
              <textarea style="width:350px;height:80px;" name="n_reason" id="n_reason"  required='' oninput="handleReasonChange('n')"></textarea>
               <?php if(isset($_POST['submit']))
               {
                 $c8=$_POST['n_reason'];
                 $acttObj->editFun($table,$edit_id,'n_reason',$c8);
               } 
               ?>
            </p>
               <p>
             <h1 style="color:#F00"> <label class="optional" style="color:#069; font-size:15px"> Calculated Stars: 
              </label>
             <?php 
    
    //show assessment rating
      $query="SELECT (sum(punctuality) + sum(appearance) + sum(professionalism) + 
        sum(confidentiality) + sum(impartiality) + sum(accuracy) + sum(rapport) + 
        sum(communication)) as sm,COUNT(interp_assess.id) as diviser 
      FROM interp_assess
		  JOIN interpreter_reg ON interp_assess.interpName=interpreter_reg.code	 
	   	where interp_assess.interpName='$code_qs'";	

      $result = mysqli_query($con,$query);
      while($row = mysqli_fetch_array($result))
      {
        $diviser=$row['diviser'];
        if($diviser<=0)
        {
          $diviser=1;
        } 
        $assess_num=$row['sm']*100/($diviser*120); 
      }
			//echo $assess_num;
			if($assess_num<0){echo 'Negative Feedback';}
			if($assess_num>=0 && $assess_num<=5){echo 'No Feedback Received';}
			if($assess_num>6 && $assess_num<=20){echo '* ';}
			if($assess_num>20 && $assess_num<=40){echo '** ';}
			if($assess_num>40 && $assess_num<=60){echo '*** ';}
			if($assess_num>60 && $assess_num<=80){echo '**** ';}
			if($assess_num>80 && $assess_num<=100){echo '***** ';}
			?>
            </h1>
            </p>
            <div style="margin-top:10px;">
              <button class="button" type="submit" name="submit">Submit &raquo;</button></div>
          </fieldset>
        </form>
<style>
  textarea.disabled {
      background-color: #e9ecef;
      cursor: not-allowed;
      opacity: 0.7;
  }
</style>      
</body>
</html>

<?php 
if(isset($_POST['submit']))
{
  echo "<script>alert('Successful!');</script>";
  $acttObj->editFun($table,$edit_id,'submittedBy',$_SESSION['UserName']);
  $acttObj->editFun($table,$edit_id,'interpName',$code_qs);
}
?>

<script>window.onunload = refreshParent;
function refreshParent() 
{
  window.opener.location.reload();
}
</script>
<script>
function handleReasonChange(source) {
    const pReason = document.getElementById('p_reason');
    const nReason = document.getElementById('n_reason');

    if (source === 'p') {
        if (pReason.value.trim() !== '') {
            nReason.value = '';
            nReason.readOnly = true;
            nReason.classList.add('disabled');
        } else {
            nReason.readOnly = false;
            nReason.classList.remove('disabled');
        }
    }

    if (source === 'n') {
        if (nReason.value.trim() !== '') {
            pReason.value = '';
            pReason.readOnly = true;
            pReason.classList.add('disabled');
        } else {
            pReason.readOnly = false;
            pReason.classList.remove('disabled');
        }
    }
}
</script>