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
 $query="SELECT $table.*, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   where  $table.intrp_salary_comit = 1 and  $table.assignDate between '$frmDate' and '$toDate'";
$result = mysqli_query($con, $query);
?>
<table width="99%" id="datatables" class="display">
<thead align="left"> <tr>
    <th width="5">#</th>
    <th>Invoice No.</th>
    <th>Job Date</th>
    <th>Interpreter Name</th>
    <th>Language</th>
    <th>Amount Paid to the Interrpeter</th>
    <th>Interpreter Payment Date</th>
  </tr></thead><tbody>
<?php $i=1;
    while($row = mysqli_fetch_assoc($result)) {?>
    <tr>
    <td><?php echo $i; ?></td>
    <td><?php echo $row["invoiceNo"]; ?></td>
    <td><?php echo date_format(date_create($row["assignDate"]), 'd-m-Y'); ?></td>
    <td><?php echo $row["name"]; ?></td>
    <td><?php echo $row["source"]; ?></td>
    <td><?php echo $row["total_charges_interp"]; ?></td>
    <td><?php echo date_format(date_create($row["paid_date"]), 'd-m-Y'); ?></td>
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
