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
		$table='interpreter';
	   $query="SELECT *, total_charges_comp * 0.2 as vat, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   where total_charges_comp <> rAmount or rAmount is null or rAmount =0 and assignDate between '$frmDate' and '$toDate'";	$result = mysqli_query($con,$query);?>
<table width="99%" id="datatables" class="display">
<thead align="left"> <tr>
    <th>Invoice Number</th>
    <th>Job Date</th>
    <th>Language</th>
    <th>Client Name</th>
    <th>Interpreter Name</th>
    <th>Without VAT</th>
    <th>VAT</th>
    <th>Non-VAT Costs</th>
    <th>Invoice Total</th> 
  </tr></thead><tbody>
<?php 
    while($row = mysqli_fetch_assoc($result)) {?>
    <tr>
    <td><?php echo $row["invoiceNo"]; ?></td>
    <td><?php echo date_format(date_create($row["assignDate"]), 'd-m-Y'); ?></td>
    <td><?php echo $row['source']; ?></td>
    <td><?php echo $row["orgName"]; ?></td>
    <td><?php echo $row["name"]; ?></td>
    <td><?php echo $row["total_charges_comp"]; ?></td>
    <td><?php echo $vat=round($row["vat"],2); ?></td>
    <td><?php echo $row["C_otherCost"]; ?></td>    
    <td><?php echo $row["total_charges_comp"]+$vat; ?></td>
    </tr>
  <?php  } ?>
 </tbody></table>
<?php } ?>
</div>
<?php
mysqli_close($con);
?></div>
</body>
</html>
