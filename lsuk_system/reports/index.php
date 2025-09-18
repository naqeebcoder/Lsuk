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
$sql = "SELECT interpreter.*, 0.2*interpreter.total_charges_comp as vat,received_amount.dated FROM interpreter
INNER JOIN received_amount ON interpreter.invoiceNo=received_amount.invoiceNo
 where interpreter.assignDate between '$frmDate' and '$toDate'";
$result = mysqli_query($con, $sql);
?>
<table width="99%" id="datatables" class="display">
<caption>Report Title</caption>
<thead align="left"> <tr>
    <th width="5">#</th>
    <th>Job Date</th>
    <th>Invoice Number</th>
    <th>Client Name</th>
    <th>Without VAT</th>
    <th>VAT</th>
    <th>Non-VAT Costs</th>
    <th>Invoice Total</th>
    <th>Payment Received Date</th>    
  </tr></thead><tbody>
<?php $i=1;
    while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
    <td><a href="#" title="Print Invoice" onClick="MM_openBrWindow('../invoice.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><?php echo $i; ?></a></td>
    <td><?php echo date_format(date_create($row["assignDate"]), 'd-m-Y'); ?></td>
    <td><?php echo $row["invoiceNo"]; ?></td>
    <td><?php echo $row["orgName"]; ?></td>
    <td><?php echo $row["total_charges_comp"]; ?></td>
    <td><?php echo $vat=round($row["vat"],2); ?></td>
    <td><?php echo $row["C_otherCost"]; ?></td>
    <td><?php echo $row["total_charges_comp"]+$vat; ?></td>
    <td><?php echo date_format(date_create($row["dated"]), 'd-m-Y'); ?></td>    
    </tr>
  <?php $i++; } ?>
 </tbody></table>
<?php } ?>
</div>
<?php
mysqli_close($con);
?></div>
</body>
</html>
