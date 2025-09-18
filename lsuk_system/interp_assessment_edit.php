<?php include'db.php'; include'class.php';session_start();$table='interp_assess';$code_qs=$_GET['code_qs'];$name=$_GET['name']; $edit_id= @$_GET['edit_id'];?>
  <?php 
			
       	$query="SELECT * FROM interp_assess
		JOIN interpreter_reg ON interp_assess.interpName=interpreter_reg.code	 
		
	   	where interp_assess.id=$edit_id";	
			$result = mysqli_query($con,$query);
			$row = mysqli_fetch_array($result);$orgName=$row['orgName'];$punctuality=$row['punctuality'];$appearance=$row['appearance'];$professionalism=$row['professionalism'];$confidentiality=$row['confidentiality'];$impartiality=$row['impartiality'];$accuracy=$row['accuracy'];$rapport=$row['rapport'];$communication=$row['communication'];$p_reason=$row['p_reason'];$n_reason=$row['n_reason'];$get_feedback=$row['get_feedback'];$p_feedbackby=$row['p_feedbackby'];$order_id=$row['order_id'];?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Interpreter Assessment</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" href="css/bootstrap.css">
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
</head>
<body>    
        <form action="#" method="post" id="frm_review">
          <h1 class="text-center"> Interpreter Assessment Form for <span class="label label-primary"> <?php echo ucwords($name); ?></span></h1>
          <div class="form-group col-md-3 col-sm-6">
          <label class="control-label" for="email">Invoice/Order Number *</label>
              <input class="form-control" type="text" name="order_id" id="order_id" value="<?php  echo $order_id; ?>">
              <?php if(isset($_POST['submit'])){$c100=$_POST['order_id'];$acttObj->editFun($table,$edit_id,'order_id',$c100);} ?>
            </div>
          <div class="form-group col-md-3 col-sm-6">
              <label class="control-label" for="email">Client Name *</label>
                <select class="form-control" id="orgName" name="orgName" required=''>
                            <?php 			
        $sql_opt="SELECT name,abrv,status FROM comp_reg ORDER BY name ASC";
        $result_opt=mysqli_query($con,$sql_opt);
        $options="";
        while ($row_opt=mysqli_fetch_array($result_opt)) {
            $code=$row_opt["abrv"];
            $status=$row_opt["status"];
            $name_opt=$row_opt["name"];
        	
        	if($orgName==$code){$fulname=$name_opt;$abrivation=$code;}
            $options.="<OPTION value='$code'>".$name_opt. '<span style="color:#F00;">('.$status.')</span>';}
        ?>
                            <option value="<?php echo $abrivation ?>"><?php echo $fulname ?></option>
                            <option value="">--Select--</option>
                            <?php echo $options; ?>
                            </option>
                          </select>
        </div>
          <?php if(isset($_POST['submit'])){$c1=$_POST['orgName']; $acttObj->editFun($table,$edit_id,'orgName',$c1);} ?>
         <div class="form-group col-md-3 col-sm-6">
                  <label class="control-label" for="email">Feedback Method</label>
                  <select class="form-control" name="get_feedback" id="get_feedback" required=''>
                    <option><?php echo $get_feedback ?></option>
                  <option value="">--Select--</option>
                  <option>Email</option>
                  <option>Timesheet</option>
                  <option>Phone</option>
                  <option>Others</option>
      <option>Online</option>
      <option>App</option>
                  </select>
          <?php if(isset($_POST['submit'])){$c7=$_POST['get_feedback'];$acttObj->editFun($table,$edit_id,'get_feedback',$c7);} ?>
          </div>
          <div class="form-group col-md-3 col-sm-6">
          <label class="control-label" for="email">Person Giving Feedback</label>
              <input class="form-control" name="p_feedbackby" id="p_feedbackby" required='' value="<?php  echo $p_feedbackby; ?>">
              <?php if(isset($_POST['submit'])){$c66=$_POST['p_feedbackby'];$acttObj->editFun($table,$edit_id,'p_feedbackby',$c66);} ?>
        </div>
  <div class="form-group col-sm-12">
      <table width="100%" align="center" class="table table-bordered">
      <thead class="bg-primary">
        <th>About</th>
        <th>Poor</th>
        <th>Average</th>
        <th>Fair</th>
        <th>Good</th>
        <th>Excellent</th>
        </thead>
        <tbody>
      <tr>
        <td>Punctuality  * </td>
        <td><input type="radio" name="punctuality" id="punctuality" value="-5"  required='' <?php if($punctuality=='-5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="punctuality" id="punctuality" value="1"  required='' <?php if($punctuality=='1'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="punctuality" id="punctuality" value="5"  required='' <?php if($punctuality=='5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="punctuality" id="punctuality" value="10"  required='' <?php if($punctuality=='10'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="punctuality" id="punctuality" value="15"  required='' <?php if($punctuality=='15'){?> checked="checked"<?php } ?>/></td>
        <?php if(isset($_POST['submit'])){$c2=$_POST['punctuality']; $acttObj->editFun($table,$edit_id,'punctuality',$c2);} ?>
        </tr>
      <tr>
        <td>Appearance  * </td>
        <td><input type="radio" name="appearance" id="appearance" value="-5"  required='' <?php if($appearance=='-5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="appearance" id="appearance" value="1"  required='' <?php if($appearance=='1'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="appearance" id="appearance" value="5"  required='' <?php if($appearance=='5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="appearance" id="appearance" value="10"  required='' <?php if($appearance=='10'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="appearance" id="appearance" value="15"  required='' <?php if($appearance=='15'){?> checked="checked"<?php } ?>/></td>
        <?php if(isset($_POST['submit'])){$c2=$_POST['appearance']; $acttObj->editFun($table,$edit_id,'appearance',$c2);} ?>
        </tr>
      <tr>
        <td>Professionalism  * </td>
        <td><input type="radio" name="professionalism" id="professionalism" value="-5"  required='' <?php if($professionalism=='-5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="professionalism" id="professionalism" value="1"  required='' <?php if($professionalism=='1'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="professionalism" id="professionalism" value="5"  required='' <?php if($professionalism=='5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="professionalism" id="professionalism" value="10"  required='' <?php if($professionalism=='10'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="professionalism" id="professionalism" value="15"  required='' <?php if($professionalism=='15'){?> checked="checked"<?php } ?>/></td>
      <?php if(isset($_POST['submit'])){$c2=$_POST['professionalism']; $acttObj->editFun($table,$edit_id,'professionalism',$c2);} ?>
        </tr>
      <tr>
        <td>Confidentiality  * </td>
        <td><input type="radio" name="confidentiality" id="confidentiality" value="-5"  required='' <?php if($confidentiality=='-5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="confidentiality" id="confidentiality" value="1"  required='' <?php if($confidentiality=='1'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="confidentiality" id="confidentiality" value="5"  required='' <?php if($confidentiality=='5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="confidentiality" id="confidentiality" value="10"  required='' <?php if($confidentiality=='10'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="confidentiality" id="confidentiality" value="15"  required='' <?php if($confidentiality=='15'){?> checked="checked"<?php } ?>/></td>
       <?php if(isset($_POST['submit'])){$c2=$_POST['confidentiality']; $acttObj->editFun($table,$edit_id,'confidentiality',$c2);} ?>
        </tr>
      <tr>
        <td>Impartiality  * </td>
        <td><input type="radio" name="impartiality" id="impartiality" value="-5"  required='' <?php if($impartiality=='-5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="impartiality" id="impartiality" value="1"  required='' <?php if($impartiality=='1'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="impartiality" id="impartiality" value="5"  required='' <?php if($impartiality=='5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="impartiality" id="impartiality" value="10"  required='' <?php if($impartiality=='10'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="impartiality" id="impartiality" value="15"  required='' <?php if($impartiality=='15'){?> checked="checked"<?php } ?>/></td>
        <?php if(isset($_POST['submit'])){$c2=$_POST['impartiality']; $acttObj->editFun($table,$edit_id,'impartiality',$c2);} ?>
        </tr>
      <tr>
        <td>Accuracy  * </td>
        <td><input type="radio" name="accuracy" id="accuracy" value="-5"  required='' <?php if($accuracy=='-5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="accuracy" id="accuracy" value="1"  required='' <?php if($accuracy=='1'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="accuracy" id="accuracy" value="5"  required='' <?php if($accuracy=='5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="accuracy" id="accuracy" value="10"  required='' <?php if($accuracy=='10'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="accuracy" id="accuracy" value="15"  required='' <?php if($accuracy=='15'){?> checked="checked"<?php } ?>/></td>
        <?php if(isset($_POST['submit'])){$c2=$_POST['accuracy']; $acttObj->editFun($table,$edit_id,'accuracy',$c2);} ?>
        </tr>
      <tr>
        <td>Rapport  * </td>
        <td><input type="radio" name="rapport" id="rapport" value="-5"  required='' <?php if($rapport=='-5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="rapport" id="rapport" value="1"  required='' <?php if($rapport=='1'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="rapport" id="rapport" value="5"  required='' <?php if($rapport=='5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="rapport" id="rapport" value="10"  required='' <?php if($rapport=='10'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="rapport" id="rapport" value="15"  required='' <?php if($rapport=='15'){?> checked="checked"<?php } ?>/></td>
        <?php if(isset($_POST['submit'])){$c2=$_POST['rapport']; $acttObj->editFun($table,$edit_id,'rapport',$c2);} ?>
        </tr>
      <tr>
        <td>Communication  * </td>
        <td><input type="radio" name="communication" id="communication" value="-5"  required='' <?php if($communication=='-5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="communication" id="communication" value="1"  required='' <?php if($communication=='1'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="communication" id="communication" value="5"  required='' <?php if($communication=='5'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="communication" id="communication" value="10"  required='' <?php if($communication=='10'){?> checked="checked"<?php } ?>/></td>
        <td><input type="radio" name="communication" id="communication" value="15"  required='' <?php if($communication=='15'){?> checked="checked"<?php } ?>/></td>
        <?php if(isset($_POST['submit'])){$c2=$_POST['communication']; $acttObj->editFun($table,$edit_id,'communication',$c2);} ?>
        </tr>
        </tbody>
    </table>
</div>

          <div class="form-group col-sm-6">
          <label class="control-label" for="email">Positive Feedback</label>
              <textarea class="form-control" rows="4" name="p_reason" id="p_reason" required=''><?php  echo $p_reason; ?></textarea>
              <?php if(isset($_POST['submit'])){$c7=$_POST['p_reason'];$acttObj->editFun($table,$edit_id,'p_reason',$c7);} ?>
        </div>
          <div class="form-group col-sm-6">
          <label class="control-label" for="email">Negative Feedback </label>
              <textarea class="form-control" rows="4" name="n_reason" id="n_reason"  required=''><?php  echo $n_reason; ?></textarea>
               <?php if(isset($_POST['submit'])){$c8=$_POST['n_reason'];$acttObj->editFun($table,$edit_id,'n_reason',$c8);} ?>
        </div>
            <div class="form-group col-sm-12">
                <button class="btn btn-lg btn-primary" type="submit" name="submit">Submit &raquo;</button>
                </div>
        </form>
      
</body>
</html>

<?php if(isset($_POST['submit'])){echo "<script>alert('Successful!');</script>";
 $acttObj->editFun($table,$edit_id,'submittedBy',$_SESSION['UserName']);$acttObj->editFun($table,$edit_id,'interpName',$code_qs);}?>
<script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>


