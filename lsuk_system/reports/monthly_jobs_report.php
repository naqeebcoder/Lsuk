<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<body>
<?php include 'header.php'; ?>
<?php include 'navigations.php'; ?>
<div id="apDiv1">
<div style="margin-left:10px;">
<?php if(!isset($_GET['submit'])){ ?>
<form action="" method="get">

<table width="30%" >
  <tr>
    <td align="right">From Date</td>
    <td><input type="date" name="frmDate" id="frmDate" /></td>
  </tr>
  <tr>
    <td align="right">To Date</td>
    <td><input type="date" name="toDate" id="toDate" /></td>
  </tr>
    <td></td>
    <td><input type="submit" name="submit" value="Submit" /></td><tr>    
  </tr>
</table>
</form>
<?php }else{ $frmDate=$_GET['frmDate'];$toDate=$_GET['toDate'];
$sql = "SELECT *, 0.2*total_charges_comp as vat FROM interpreter where assignDate between '$frmDate' and '$toDate'";
$result = mysqli_query($con, $sql);
?>
<table width="99%" id="datatables" class="display">
<thead align="left"> <tr>
    <th width="5">#</th>
    <th>Job Date</th>
    <th>Languagee</th>
    <th>Client Name</th>
    <th>Invoice Number</th>
    <th>Amount Paid to the Interrpeter</th>
    <th>Interpreter Payment Date</th>
    <th>Without VAT</th>
    <th>VAT</th>
    <th>Non-VAT Costs</th>
    <th>Invoice Total</th>   
  </tr></thead><tbody>
<?php $i=1;
    while($row = mysqli_fetch_assoc($result)) {?>
    <tr>
    <td><?php echo $i; ?></td>
    <td><?php echo date_format(date_create($row["assignDate"]), 'd-m-Y'); ?></td>
    <td><?php echo $row["invoiceNo"]; ?></td>
    <td><?php echo $row["orgName"]; ?></td>
    <td><?php echo $row["total_charges_comp"]; ?></td>
    <td><?php echo $vat=round($row["vat"],2); ?></td>
    <td><?php echo $row["id"]; ?></td>
    <td><?php echo $row["total_charges_comp"]+$vat; ?></td>
    <td><?php echo $row["id"]; ?></td>    
    </tr>
  <?php  $i++;} ?>
 </tbody></table>
<?php } ?>
</div>
<?php
mysqli_close($con);
?></div>
</body>
</html>
