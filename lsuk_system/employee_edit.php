<?php include'db.php'; session_start(); include'class.php'; $table='emp';$edit_id= @$_GET['edit_id'];
$query="SELECT * FROM $table where id=$edit_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$name=$row['name'];$passp=$row['passp'];$ni=$row['ni'];$pr=$row['pr'];$driving=$row['driving'];$desig=$row['desig'];$jt=$row['jt'];$phs=$row['phs'];$lss=$row['lss'];$duty=$row['duty'];$remrks=$row['remrks'];$contact=$row['contact'];$email=$row['email'];$buildNo=$row['buildNo'];$line1=$row['line1'];$line2=$row['line2'];$city=$row['city'];$pcode=$row['pcode'];$gender=$row['gender'];$elgible=$row['elgible'];?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>Edit Employee Form</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
		<script src="js/jquery-1.11.3.min.js"></script>
</head>
<body>
<div class="container-fluid">
        <form action="" method="post" class="register">
          <div class="bg-info col-xs-12 form-group"><h4>Employee Personal Details</h4></div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Name</label>
                    <input placeholder="Name *" class="form-control valid" type="text" name="name" required='' value='<?php echo $name ;?>' id="name"/>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Passport/NIC #</label>
               <input placeholder="Passport/NIC # *" class="form-control" name="passp" type="text"  required='' id="passp" value="<?php echo $passp ;?>"/>
              <?php if(isset($_POST['submit'])){$c3=$_POST['name'];$acttObj->editFun($table,$edit_id,'name',$c3);} ?>
               <?php if(isset($_POST['submit'])){$c4=$_POST['passp'];$acttObj->editFun($table,$edit_id,'passp',$c4);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>National insurance #</label>
                  <input placeholder="National insurance #" class="form-control" name="ni" type="text" value="<?php echo $ni ;?>" id="ni" />
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Payroll  #</label>
                  <input placeholder="Payroll  #" class="form-control" name="pr" type="text" value="<?php echo $pr ;?>" id="pr" />
               <?php if(isset($_POST['submit'])){$c6=$_POST['ni'];$acttObj->editFun($table,$edit_id,'ni',$c6);} ?>
               <?php if(isset($_POST['submit'])){$c6=$_POST['pr'];$acttObj->editFun($table,$edit_id,'pr',$c6);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Driving License #</label>
                  <input placeholder="Driving License #" class="form-control" name="driving" type="text"  id="driving" value="<?php echo $driving ;?>"/>
                <?php if(isset($_POST['submit'])){$c5=$_POST['driving'];$acttObj->editFun($table,$edit_id,'driving',$c5);} ?>
            </div>
            <div class="bg-info col-xs-12 form-group"><h4>Job Description</h4></div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Designation</label>
                    <input placeholder="Designation" class="form-control" name="desig" type="text" id="desig" value="<?php echo $desig ;?>"/>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Select Job Type</label>
                    <select class="form-control" id="jt" name="jt" required>
                      <option selected><?php echo $jt ;?></option>
                      <option disabled>Select Job Type</option>
                      <option>Permanent</option>
                      <option>Contract</option>
                      <option>Daily Wages</option>
                    </select>
                 <?php if(isset($_POST['submit'])){$c7=$_POST['desig'];$acttObj->editFun($table,$edit_id,'desig',$c7);} ?>
               <?php if(isset($_POST['submit'])){$c8=$_POST['jt'];$acttObj->editFun($table,$edit_id,'jt',$c8);} ?>
            </div>
            <div class="form-group col-md-2 col-sm-6">
                <label>Per Hour Salary</label>
                  <input placeholder="Per Hour Salary" class="form-control" name="phs" type="text" id="phs" value="<?php echo $phs ;?>"/>
            </div>
            <div class="form-group col-md-2 col-sm-6">
                <label>Lump-Sum Salary</label>
                  <input placeholder="Lump-Sum Salary" class="form-control" name="lss" type="text" id="lss" value="<?php echo $lss; ?>"/>
              <?php if(isset($_POST['submit'])){$c9=$_POST['phs'];$acttObj->editFun($table,$edit_id,'phs',$c9);} ?>
                  <?php if(isset($_POST['submit'])){$c10=$_POST['lss'];$acttObj->editFun($table,$edit_id,'lss',$c10);} ?>
            </div>
    <div class="bg-info col-xs-12 form-group"><h4>Contact Information</h4></div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Contact Number</label>
                    <input placeholder="Contact Number" class="form-control" name="contact" id="contact" type="text" value="<?php echo $contact ;?>"/>
                    <?php if(isset($_POST['submit'])){$c12=$_POST['contact'];$acttObj->editFun($table,$edit_id,'contact',$c12);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Email Address</label>
                    <input placeholder="Email Address" class="form-control" name="email" id="email" type="text" value="<?php echo $email ;?>"/>
                  <?php if(isset($_POST['submit'])){$c13=$_POST['email'];$acttObj->editFun($table,$edit_id,'email',$c13);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Building Number / Name</label>
                    <input placeholder="Building Number / Name" class="form-control" name="buildNo" id="buildNo" type="text" value="<?php echo $buildNo ;?>"/>
                <?php if(isset($_POST['submit'])){$c14=$_POST['buildNo'];$acttObj->editFun($table,$edit_id,'buildNo',$c14);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Address Line 1</label>
                    <input placeholder="Address Line 1" class="form-control" name="line1" id="line1" type="text" value="<?php echo $line1 ;?>"/>
                <?php if(isset($_POST['submit'])){$c14=$_POST['line1'];$acttObj->editFun($table,$edit_id,'line1',$c14);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Address Line 2</label>
                    <input placeholder="Address Line 2" class="form-control" name="line2" id="line2" type="text" value="<?php echo $line2 ;?>"/>
              <?php if(isset($_POST['submit'])){$c15=$_POST['line2'];$acttObj->editFun($table,$edit_id,'line2',$c15);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Employee City</label>
                <select class="form-control" name="city" id="city">
                <option selected><?php echo $city ;?></option>
                <option value="" disabled>Select City</option>
                    <optgroup label="England">
                      <option>Bath</option>
                      <option>Birmingham</option>
		      		  <option>Bradford</option>
                      <option>Bridgwater</option>
                      <option>Bristol</option>
                      <option>Buckinghamshire</option>
                      <option>Cambridge</option>
                      <option>Canterbury</option>
                      <option>Carlisle</option>
                      <option>Chippenham</option>
                      <option>Cheltenham</option>
                      <option>Cheshire</option>
                      <option>Coventry</option>
                      <option>Derby</option>
                      <option>Dorset</option>
                      <option>Exeter</option>
                      <option>Frome</option>
                      <option>Gloucester</option>
                      <option>Hereford</option>
                      <option>Leeds</option>
                      <option>Leicester</option>
                      <option>Liverpool</option>
                      <option>London</option>
                      <option>Manchester</option>
                      <option>Newcastle</option>
                      <option>Northampton</option>
                      <option>Norwich</option>
                      <option>Nottingham</option>
                      <option>Oxford</option>
                      <option>Plymouth</option>
                      <option>Pool</option>
                      <option>Portsmouth</option>
                      <option>Salford</option>
                      <option>Shefield</option>
                      <option>Somerset</option>
                      <option>Southampton</option>
                      <option>Swindon</option>
                      <option>Suffolk</option>
                      <option>Surrey</option>
                      <option>Taunton</option>
                      <option>Trowbridge</option>
                      <option>Truro</option>
                      <option>Warwick</option>
                      <option>Wiltshire</option>
                      <option>Winchester</option>
                      <option>Wells</option>
                      <option>Weston Super Mare</option>
                      <option>Worcester</option>
                      <option>Wolverhampton</option>
                      <option>York</option>           
                    </optgroup>
                    <optgroup label="Scotland">
                      <option>Dundee</option>
                      <option>Edinburgh</option>
                      <option>Glasgow</option>
                    </optgroup>
                    <optgroup label="Wales">
                      <option>Cardiff</option>
                      <option>Newport</option>
                      <option>Swansea</option>
                    </optgroup>                   
                  </select>
                <?php if(isset($_POST['submit'])){$c16=$_POST['city'];$acttObj->editFun($table,$edit_id,'city',$c16);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Post Code</label>
                  <input placeholder="Post Code" class="form-control" name="pcode" id="pcode" type="text" value="<?php echo $pcode ;?>"/>
              <?php if(isset($_POST['submit'])){$c17=$_POST['pcode'];$acttObj->editFun($table,$edit_id,'pcode',$c17);} ?>
            </div>
              <div class="bg-info col-xs-12 form-group"><h4>Other Details</h4></div>
            <div class="form-group col-sm-6">
                <label>Duties / Assignment</label>
                  <textarea class="form-control" placeholder="Duties / Assignment" name="duty" rows="3" id="duty"><?php echo $duty ;?></textarea>
                  <?php if(isset($_POST['submit'])){$c18=$_POST['duty'];$acttObj->editFun($table,$edit_id,'duty',$c18);} ?>
            </div>
            <div class="form-group col-sm-6">
                <label>Notes if Any 1000 alphabets</label>
                    <textarea class="form-control" placeholder="Notes if Any 1000 alphabets" name="remrks" rows="3"><?php echo $remrks ;?></textarea>
                    <?php if(isset($_POST['submit'])){$c21=$_POST['remrks'];$acttObj->editFun($table,$edit_id,'remrks',$c21);} ?>
              </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>Gender</label>
                  <select class="form-control" id="gender" name="gender" required>
                      <option selected ><?php echo $gender ;?></option>
                      <option value="" disabled>Select Gender</option>
                      <option>Male</option>
                      <option>Female</option>
                    </select>
                  <?php if(isset($_POST['submit'])){$c22=$_POST['gender'];$acttObj->editFun($table,$edit_id,'gender',$c22);} ?>
            </div>
            <div class="form-group col-md-4 col-sm-6">
                <label>UK Work Eligibility</label>
                  <select class="form-control" id="elgible" name="elgible" required>
                      <option value="<?php echo $elgible; ?>" selected ><?php echo $elgible==0?'Eligible':'Not Eligible' ;?></option>
                      <option value="" disabled>Select UK Work Eligibility</option>
                      <option value="0" class="text-success">Eligible</option>
                      <option value="1" class="text-danger">Not Eligible</option>
                    </select>
                <?php if(isset($_POST['submit'])){$data=$_POST['elgible'];$acttObj->editFun($table,$edit_id,'elgible',$data);} ?>
            </div>
            <div class="form-group col-sm-12">
            <button class="btn btn-info" style="border-color: #000000;color: black;font-size: 20px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit">UPDATE NOW &raquo;</button>
            </div>
        </form>
        </div>
<?php
if(isset($_POST['submit'])){
    session_start();
    $acttObj->editFun($table,$edit_id,'submited',$_SESSION['userId']);
}
if(isset($_POST['submit'])){
    echo "<script>alert('Record of employee successfully updated.');</script>";
$acttObj->editFun($table,$edit_id,'edited_by',$_SESSION['UserName']);
$acttObj->editFun($table,$edit_id,'edited_date',date("Y-m-d H:i:s"));
$acttObj->new_old_table('hist_'.$table,$table,$edit_id);?>
<script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}
</script>
<?php }?>
<script>
$(".valid").bind('keypress paste',function (e) {
  var regex = new RegExp(/[a-z A-Z 0-9 ()]/);
  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
  if (!regex.test(str)) {
    e.preventDefault();
    return false;
  }
});
</script>
</body>
</html>