<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php include'db.php'; include'class.php'; $table=@$_GET['table'];$view_id= @$_GET['view_id'];
$query="SELECT * FROM $table where id=$view_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$name=$row['name'];$email=$row['email'];$contactNo=$row['contactNo'];$rph=$row['rph'];$interp=$row['interp'];$telep=$row['telep'];$trans=$row['trans'];$gender=$row['gender'];$city=$row['city'];$address=$row['address'];$code=$row['code'];$applicationForm=$row['applicationForm'];$agreement=$row['agreement'];$crbDbs=$row['crbDbs'];$identityDocument=$row['identityDocument'];$nin=$row['nin'];$cv=$row['cv'];$dps=$row['dps'];$anyOther=$row['anyOther'];$anyCertificate=$row['anyCertificate'];$cpd=$row['cpd'];$int_qualification=$row['int_qualification'];$rpm=$row['rpm'];$rpu=$row['rpu'];$ni=$row['ni'];$buildingName=$row['buildingName'];$line1=$row['line1'];$line2=$row['line2'];$line3=$row['line3'];$postCode=$row['postCode'];$bnakName=$row['bnakName'];$acName=$row['acName'];$acntCode=$row['acntCode'];$acNo=$row['acNo'];$dob=$row['dob'];$reg_date=$row['reg_date'];$interp=$row['interp'];$telep=$row['telep'];$trans=$row['trans'];}?>
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
     <legend>Interpreter's Documents
     </legend>
            
     <table width="50%" border="1">
      <?php $table='interpreter_reg';$allowedExtns = array("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "xlsx");
	   $query="SELECT * FROM $table where code='$code'";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){$applicationForm= $row['applicationForm'];$agreement= $row['agreement'];$crbDbs= $row['crbDbs'];$identityDocument= $row['identityDocument'];$nin= $row['nin'];$cv= $row['cv'];$dps= $row['dps'];$anyOther= $row['anyOther'];$anyCertificate= $row['anyCertificate']; ?>
  
    <tr>
    <td align="left"> Application Form </td>
    <td align="left"><?php echo $row['applicationForm']; ?></td>
   <td align="left"><?php if ($row['applicationForm']=='Soft Copy' || $row['applicationForm']=='Hard Copy'){ ?>
<a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'Application Form'; ?>&col=<?php echo 'applicationForm'; ?>&data=<?php echo $applicationForm; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a>


<?php }else{ ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'Application Form'; ?>&col=<?php echo 'applicationForm'; ?>&data=<?php echo $applicationForm; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td><?php } ?>
    </tr>
    <tr>
    <td align="left">Agreement</td>
    <td align="left"><?php echo $row['agreement']; ?></td>
    <td align="left"><?php if ($row['agreement']=='Soft Copy' || $row['agreement']=='Hard Copy'){ ?>
      <a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'Agreement'; ?>&col=<?php echo 'agreement'; ?>&data=<?php echo $agreement; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a>
<?php }else{ ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'Agreement'; ?>&col=<?php echo 'agreement'; ?>&data=<?php echo $agreement; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td><?php } ?>
    </tr>
    <tr>
    <td align="left">CRB/DBS</td>
    <td align="left"><?php echo $row['crbDbs']; ?></td>
    <td align="left"><?php if ($row['crbDbs']=='Soft Copy' || $row['crbDbs']=='Hard Copy'){ ?>
<a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'CRB/DBS'; ?>&col=<?php echo 'crbDbs'; ?>&data=<?php echo $crbDbs; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a><?php }else{ ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'CRB/DBS'; ?>&col=<?php echo 'crbDbs'; ?>&data=<?php echo $crbDbs; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td><?php } ?>
    </tr>
    <tr>
    <td align="left">Identity Document</td>
    <td align="left"><?php echo $row['identityDocument']; ?></td>
    <td align="left"><?php if ($row['identityDocument']=='Soft Copy' || $row['identityDocument']=='Hard Copy'){ ?>
<a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'Identity Document'; ?>&col=<?php echo 'identityDocument'; ?>&data=<?php echo $identityDocument; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a><?php }else{ ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'Identity Document'; ?>&col=<?php echo 'identityDocument'; ?>&data=<?php echo $identityDocument; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td><?php } ?>
    </tr>
    <tr>
    <td align="left">National Insurance Number</td>
    <td align="left"><?php echo $row['nin']; ?></td>
    <td align="left"><?php if ($row['nin']=='Soft Copy' || $row['nin']=='Hard Copy'){ ?>
<a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'National Insurance Number'; ?>&col=<?php echo 'nin'; ?>&data=<?php echo $nin; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a><?php }else{ ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'National Insurance Number'; ?>&col=<?php echo 'nin'; ?>&data=<?php echo $nin; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td><?php } ?>
    </tr>
    <tr>
    <td align="left">CV</td>
    <td align="left"><?php echo $row['cv']; ?></td>
    <td align="left"><?php if ($row['cv']=='Soft Copy' || $row['cv']=='Hard Copy'){ ?>
<a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'CV'; ?>&col=<?php echo 'cv'; ?>&data=<?php echo $cv; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a><?php }else{ ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'CV'; ?>&col=<?php echo 'cv'; ?>&data=<?php echo $cv; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td><?php } ?>
    </tr>
    <tr>
    <td align="left">DPSI</td>
    <td align="left"><?php echo $row['dps']; ?></td>
    <td align="left"><?php if ($row['dps']=='Soft Copy' || $row['dps']=='Hard Copy'){ ?>
<a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'DPS'; ?>&col=<?php echo 'dps'; ?>&data=<?php echo $dps; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a><?php }else{ ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'DPS'; ?>&col=<?php echo 'dps'; ?>&data=<?php echo $dps; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td><?php } ?>
    </tr>
    <tr>
      <td align="left">Any Certificate</td>
      <td align="left"><?php echo $row['anyCertificate']; ?></td>
    <td align="left"><?php if ($row['anyCertificate']=='Soft Copy' || $row['anyCertificate']=='Hard Copy'){ ?>
        <a href="#" onclick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&amp;table=<?php echo $table; ?>&amp;text=<?php echo 'Any Certificate'; ?>&amp;col=<?php echo 'anyCertificate'; ?>&amp;data=<?php echo $anyCertificate; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a>
        <?php }else{ ?>
        <a href="#" onclick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&amp;table=<?php echo $table; ?>&amp;text=<?php echo 'Any Certificate'; ?>&amp;col=<?php echo 'anyCertificate'; ?>&amp;data=<?php echo $anyCertificate; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td>
      <?php } ?>
    </tr>
    <tr>
    <td align="left">Any Other Document</td>
    <td align="left"><?php echo $row['anyOther']; ?></td>
    <td align="left"><?php if ($row['anyOther']=='Soft Copy' || $row['anyOther']=='Hard Copy'){ ?>
<a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'Any Other Document'; ?>&col=<?php echo 'anyOther'; ?>&data=<?php echo $anyOther; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a><?php }else{ ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'Any Other Document'; ?>&col=<?php echo 'anyOther'; ?>&data=<?php echo $anyOther; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td><?php } ?>
    </tr>
    <tr>
    <td align="left">CPD Document</td>
    <td align="left"><?php echo $row['cpd']; ?></td>
    <td align="left"><?php if ($row['cpd']=='Soft Copy' || $row['cpd']=='Hard Copy'){ ?>
<a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'CPD Document'; ?>&col=<?php echo 'cpd'; ?>&data=<?php echo $cpd; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a><?php }else{ ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'CPD Document'; ?>&col=<?php echo 'cpd'; ?>&data=<?php echo $cpd; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td><?php } ?>
    </tr>
    <tr>
    <td align="left">Interpreting Qualification Document</td>
    <td align="left"><?php echo $row['int_qualification']; ?></td>
    <td align="left"><?php if ($row['int_qualification']=='Soft Copy' || $row['int_qualification']=='Hard Copy'){ ?>
<a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'Interpreting Qualification Document'; ?>&col=<?php echo 'int_qualification'; ?>&data=<?php echo $int_qualification; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_success.png" title="Edit" height="14" width="16" /></a><?php }else{ ?><a href="#" onClick="MM_openBrWindow('interp_doc_updater.php?edit_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&text=<?php echo 'Interpreting Qualification Document'; ?>&col=<?php echo 'int_qualification'; ?>&data=<?php echo $int_qualification; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')"><img src="images/icn_alert_error.png" title="Edit" height="14" width="16" /></a></td><?php } ?>
    </tr>
    <?php } ?>
  </table>
           
     </fieldset>
          </form>
   

       
</body>
</html>





