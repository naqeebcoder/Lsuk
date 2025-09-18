<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php include'db.php'; include'class.php'; $table=@$_GET['table'];$view_id= @$_GET['view_id'];
$query="SELECT * FROM $table where id=$view_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$name=$row['name'];
$dbs_file=$row['dbs_file'];
$id_doc_file=$row['id_doc_file'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

        <title>Interpreter Info</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="css/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
       
          <center><h1>Linguist's  Information</h1></center>
     <fieldset class="row1">
     <legend>Interpreter's Documents
     </legend>
            
     <table width="50%" border="1" class="w3-table-all w3-card-4">
    <thead>
        <tr class="w3-blue">
            <td>File Name</td>
            <td>File Document</td>
            <td>Number</td>
            <td>Issue Date</td>
            <td>Expiry Date</td>
            <td>Status</td>
        </tr>
    </thead>
    <tr>
    <td align="left">DBS Document</td>
    <td align="left">
        <?php if($dbs_file!=''){ ?>
        <label class="w3-text-black">
        <a href="#" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $view_id; ?>&col=<?php echo 'dbs'; ?>&text=<?php echo 'DBS Document'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
        <span class="w3-badge w3-large w3-padding w3-blue"><i class="fa fa-eye" title="View <?php echo 'DBS Document'; ?>"></i></span></a>
        </label>
        <?php }else{?> 
      <label class="w3-text-red"><b><?php echo 'DBS Document'; ?> is not uploaded!</label></b>
      <?php } ?>
      </td>
    <td align="left"><?php echo $row['dbs_no']?:'Not Provided'; ?></td>
    <td align="left"><?php echo $row['dbs_issue_date']=='1001-01-01'?'Not Provided':$row['dbs_issue_date']; ?></td>
    <td align="left"><?php echo $row['dbs_expiry_date']=='1001-01-01'?'Not Provided':$row['dbs_expiry_date']; ?></td>
    <td align="left">
        <a href="#" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'dbs'; ?>&text=<?php echo 'DBS Document'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')">
        <?php if ($row['dbs_file']!=''){ ?>
<img src="images/icn_alert_success.png" title="Edit" height="14" width="16" />
<?php }else{ ?>
<img src="images/icn_alert_error.png" title="Edit" height="14" width="16" />
<?php } ?></a>
</td>
    </tr>
    
    <tr>
    <td align="left">Identity Document</td>
    <td align="left">
        <?php if($id_doc_file!=''){ ?>
        <label class="w3-text-black">
        <a href="#" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $view_id; ?>&col=<?php echo 'id_doc'; ?>&text=<?php echo 'Identity Document'; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
        <span class="w3-badge w3-large w3-padding w3-blue"><i class="fa fa-eye" title="View <?php echo 'Identity Document'; ?>"></i></span></a>
        </label>
        <?php }else{?> 
      <label class="w3-text-red"><b><?php echo 'Identity Document'; ?> is not uploaded!</label></b>
      <?php } ?>
      </td>
    <td align="left"><?php echo $row['id_doc_no']?:'Not Provided'; ?></td>
    <td align="left"><?php echo $row['id_doc_issue_date']=='1001-01-01'?'Not Provided':$row['id_doc_issue_date']; ?></td>
    <td align="left"><?php echo $row['id_doc_expiry_date']=='1001-01-01'?'Not Provided':$row['id_doc_expiry_date']; ?></td>
    <td align="left">
        <a href="#" onClick="MM_openBrWindow('update_expiry_date_updater.php?edit_id=<?php echo $row['id']; ?>&col=<?php echo 'id_doc'; ?>&text=<?php echo 'Identity Document'; ?>','_blank','scrollbars=yes,resizable=yes,width=700,height=500,left=550,top=100')">
            <?php if ($row['id_doc_file']!=''){ ?>
<img src="images/icn_alert_success.png" title="Edit" height="14" width="16" />
<?php }else{ ?>
<img src="images/icn_alert_error.png" title="Edit" height="14" width="16" />
<?php } ?></a></td>
    </tr>
  </table>
           
     </fieldset>
          </form>
   

       
</body>
</html>





