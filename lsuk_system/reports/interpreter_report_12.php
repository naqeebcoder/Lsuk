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
 $query="SELECT *, total_charges_comp , interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id where assignDate between '$frmDate' and '$toDate'";
	   $result = mysqli_query($con, $query); ?>
       
       	<script type="text/javascript" language="javascript" class="init">
//...............................................................Date Range Fileter,,,,,,,,,,,,,,,,,,,,,,,,,
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = Date.parse( $('#min').val());
        var max = Date.parse( $('#max').val());
        var range = Date.parse( data[1] ) || 0; // use data for the age column
 
        if ( ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && range <= max ) ||
             ( min <= range   && isNaN( max ) ) ||
             ( min <= range   && range <= max ) )
        {
            return true;
        }
        return false;
    }
);
 
$(document).ready(function() {
    var table = $('#datatables').DataTable();
     
    // Event listener to the two range filtering inputs to redraw on input
    $('#min, #max').keyup( function() {
        table.draw();
    } );
} );
	</script>
<!--<table border="0" cellspacing="5" cellpadding="5">
    <tbody><tr>
        <td>Minimum Date:</td>
        <td><input type="text" id="min" name="min"></td>
    
        <td>Maximum Date:</td>
        <td><input type="text" id="max" name="max"></td>
    </tr></tbody></table>-->
<table width="99%" id="datatables" class="display">
<thead align="left"> <tr>
    <th width="5">#</th>
    <th>Interpreter Name</th>
    <th>Job Date</th>
    <th>Interpreting Hours Worked</th>
    <th>Rate Per Hour</th>
    <th>Job Payment</th>
    <th>Travel KM</th>
    <th>Rate per KM</th>  
    <th>Travel Costs</th>      
    <th>Other Expenses</th>          
    <th>Total Job Payment</th>     
  </tr></thead><tbody>
<?php $i=1;
    while($row = mysqli_fetch_assoc($result)) {?>
    <tr>
    <td><?php echo $i; ?></td>
    <td><?php echo $row["name"]; ?></td>
    <td><?php echo date_format(date_create($row["assignDate"]), 'd-m-Y'); ?></td>
    <td><?php echo $row["hoursWorkd"]; ?></td>
    <td><?php echo $row["rateHour"]; ?></td>
    <td><?php echo $vat=round($row["hoursWorkd"]*$row["rateHour"],2); ?></td>
    <td><?php echo $row["travelMile"]; ?></td>
    <td><?php echo $row["rateMile"]; ?></td>
    <td><?php echo $vat=round($row["travelMile"]*$row["rateMile"],2); ?></td>
    <td><?php echo $row["otherCost"]; ?></td>    
    <td><?php echo $row["total_charges_interp"]; ?></td>    
    </tr>
  <?php  $i++;} ?>
 </tbody></table>
<?php } ?>
</div>
<?php mysqli_close($con); ?></div>
</body>
</html>
