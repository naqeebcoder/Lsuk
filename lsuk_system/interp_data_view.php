<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php include'db.php'; include'class.php'; $table=@$_GET['table'];$view_id= @$_GET['view_id'];
$query="SELECT * FROM $table where id=$view_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$name=$row['name'];$email=$row['email'];$contactNo=$row['contactNo'];$rph=$row['rph'];$interp=$row['interp'];$telep=$row['telep'];$trans=$row['trans'];$gender=$row['gender'];$city=$row['city'];$address=$row['address'];$code=$row['code'];$applicationForm=$row['applicationForm'];$agreement=$row['agreement'];$crbDbs=$row['crbDbs'];$identityDocument=$row['identityDocument'];$nin=$row['nin'];$cv=$row['cv'];$dps=$row['dps'];$anyOther=$row['anyOther'];$anyCertificate=$row['anyCertificate'];$rpm=$row['rpm'];$rpu=$row['rpu'];$ni=$row['ni'];$buildingName=$row['buildingName'];$line1=$row['line1'];$line2=$row['line2'];$line3=$row['line3'];$postCode=$row['postCode'];$bnakName=$row['bnakName'];$acName=$row['acName'];$acntCode=$row['acntCode'];$acNo=$row['acNo'];$dob=$row['dob'];$reg_date=$row['reg_date'];$interp=$row['interp'];$telep=$row['telep'];$trans=$row['trans'];}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

        <title>Interpreter Info</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
    <style type="text/css">
    #apDiv1 {
	position:absolute;
	width:792px;
	height:150px;
}
    </style>
<script type="text/javascript"> function MM_openBrWindow(theURL,winName,features) {window.open(theURL,winName,features);}</script>
</head>
<body>    
 <form class="register" action="javascript:void(0);">
       
          <h1>Linguist's  Information</h1>
   <fieldset class="row1">
     <legend>Work Details
          </legend>
          
            
            <table width="98%" border="4">
  <tr>
    <th width="200" align="left">Name</th>
    <td width="250" align="left"><?php echo $name; ?></td>
    <th width="200" align="left">Gender</th>
    <td width="250" align="left"><?php echo $gender; ?></td>
  </tr>
  <tr>
    <th width="200" align="left">Interpreter</th>
    <td width="250" align="left"><?php echo $interp; ?></td>
    <th width="200" align="left">Ph Interp</th>
    <td width="250" align="left"><?php echo $telep; ?></td>
  </tr>
  <tr>
    <th width="200" align="left">Translation</th>
    <td width="250" align="left"><?php echo $trans; ?></td>
    <th width="200" align="left">Rate per Hour</th>
    <td width="250" align="left"><?php echo $rph; ?></td>
  </tr>
  <tr>
    <th align="left">Rate per Minut</th>
    <td align="left"><?php echo $rpm; ?></td>
    <th align="left">Rate per Unit</th>
    <td align="left"><?php echo $rpu; ?></td>
    </tr>
  <tr>
    <th align="left">National Insurance #</th>
    <td align="left"><?php echo $ni; ?></td>
    <th align="left">date of Birth</th>
    <td align="left"><?php echo $misc->dated($dob); ?></td>
  </tr>
  <tr>
    <th align="left">Registration Date</th>
    <td align="left"><?php echo $reg_date; ?></td>
    <th align="left">Mode of Job</th>
    <td align="left"><?php echo "Interpreter: ".$interp; ?>,<?php echo "Telephone: ".$telep; ?>,<?php echo "Translation: ".$trans; ?></td>
  </tr>
            </table>
   </fieldset>
   <fieldset class="row1">
     <legend>Contact Details
          </legend>
            
            <table width="98%" border="1">
  <tr>
    <th width="200" align="left">Contact Number</th>
    <td width="250" align="left"><?php echo $contactNo; ?></td>
    <th width="200" align="left">Email Address</th>
    <td width="250" align="left"><?php echo $email; ?></td>
  </tr>
  <tr>
    <th align="left">Address</th>
    <td align="left"><?php echo 'Building No. '.$buildingName.' '.$line1.' '.$line2.' '.$line3.' '.$city.' '.$postCode; ?></td>
    <th align="left">City</th>
    <td align="left"><?php echo $city; ?></td>
  </tr>
     </table>
        
   </fieldset>
          <fieldset class="row1">
     <legend>Bank Account Details
          </legend>
            
            <table width="98%" border="1">
  <tr>
    <th width="200" align="left">Bank Name</th>
    <td width="250" align="left"><?php echo $bnakName; ?></td>
    <th width="200" align="left">Account Name</th>
    <td width="250" align="left"><?php echo $acName; ?></td>
  </tr>
  <tr>
    <th align="left">Account Sort Code</th>
    <td align="left"><?php echo $acntCode; ?></td>
    <th align="left">Account Number</th>
    <td align="left"><?php echo $acNo; ?></td>
  </tr>
     </table>
        
   </fieldset>
   <fieldset class="row1">
     <legend>Languages can interpret
     </legend>
            
     <table width="30%" border="1">
      <?php $table='interp_lang';
	   $query="SELECT * FROM $table where code='$code'";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>
  <tr>
    <td align="left"><?php echo $row['lang']; ?> </td>
    <td align="left"> <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><img src="images/icn_trash.png" title="Trash" height="14" width="16" /></a></td>
    </tr>
    <?php } ?>
  </table>
           
     </fieldset>
     <fieldset class="row1">
     <legend>Skills can interpret
     </legend>
            
     <table width="30%" border="1">
      <?php $table='interp_skill';
	   $query="SELECT * FROM $table where code='$code'";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>
  <tr>
    <td align="left"><?php echo $row['skill']; ?> </td>
    <td align="left"> <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><img src="images/icn_trash.png" title="Trash" height="14" width="16" /></a></td>
    </tr>
    <?php } ?>
  </table>
           
     </fieldset>
 </form>
   

       
</body>
</html>





