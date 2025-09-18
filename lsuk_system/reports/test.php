<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<link rel="stylesheet" type="text/css" href="media/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="extensions/TableTools/css/dataTables.tableTools.css">
	<link rel="stylesheet" type="text/css" href="examples/resources/syntax/shCore.css">
	<link rel="stylesheet" type="text/css" href="examples/resources/demo.css">
	<style type="text/css" class="init">

	</style>
	<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="media/js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="extensions/TableTools/js/dataTables.tableTools.js"></script>
	<script type="text/javascript" language="javascript" src="examples/resources/syntax/shCore.js"></script>
	<script type="text/javascript" language="javascript" src="examples/resources/demo.js"></script>
	<script type="text/javascript" language="javascript" class="init">

$(document).ready(function() {
    $('#example').DataTable( {
        "dom": 'T<"clear">lfrtip',
        "tableTools": {
            "aButtons": [
                "copy",
                "print",
                {
                    "sExtends":    "collection",
                    "sButtonText": "Save",
                    "aButtons":    [ "csv", "xls", "pdf" ]
                }
            ]
        }
    } );
} );

	</script>
<body>

<?php include 'header.php'; ?>
<?php include 'navigations.php'; ?>
<div id="apDiv1">
<div style="margin-left:10px;">
<?php if(!isset($_GET['submit'])){ 
$sql = "SELECT *, 0.2*total_charges_comp as vat FROM interpreter";
$result = mysqli_query($con, $sql);
?>

<table width="99%" id="example" class="display">
<caption><img src="images/logo.png" width="408"  height="63" /></caption>
<thead align="left"> <tr>
    <th>Job Date</th>
    <th>Invoice Number</th>
    <th>Client Name</th>
    <th>Without VAT</th>
    <th>VAT</th>
    <th>Non-VAT Costs</th>
    <th>Invoice Total</th>
    <th>Payment Received Date</th>    
  </tr></thead><tbody>
<?php 
    while($row = mysqli_fetch_assoc($result)) {?>
    <tr>
    <td><?php echo date_format(date_create($row["assignDate"]), 'd-m-Y'); ?></td>
    <td><?php echo $row["invoiceNo"]; ?></td>
    <td><?php echo $row["orgName"]; ?></td>
    <td><?php echo $row["total_charges_comp"]; ?></td>
    <td><?php echo $vat=round($row["vat"],2); ?></td>
    <td><?php echo $row["id"]; ?></td>
    <td><?php echo $row["total_charges_comp"]+$vat; ?></td>
    <td><?php echo $row["id"]; ?></td>    
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
