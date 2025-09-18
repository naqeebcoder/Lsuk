	<link rel="stylesheet" type="text/css" href="media/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="extensions/TableTools/css/dataTables.tableTools.css">
	<link rel="stylesheet" type="text/css" href="examples/resources/syntax/shCore.css">
	<link rel="stylesheet" type="text/css" href="examples/resources/demo.css">
	<style type="text/css" class="init">
	#apDiv1 {
	position:relative;
	width:100%;
	height:1000px;
	z-index:1;
	left: 0px;
	top: 0px;
	background-color:#FFF;
	text-align:right;
}
    #apDiv2 {
	position:absolute;
	width:293px;
	height:134px;
	z-index:1;
	left: 18px;
	top: 12px;
	background-color: #0099FF;
}
    #apDiv3 {
	position:absolute;
	width:347px;
	height:156px;
	z-index:2;
	left: 680px;
	top: 8px;
	background-color: #990033;
}
    #apDiv4 {
	position:absolute;
	width:100%;
	height:800px;
	z-index:3;
	top: 200px;
	background-color: #FFF;
}
    </style>
	<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="media/js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="extensions/TableTools/js/dataTables.tableTools.js"></script>
	<script type="text/javascript" language="javascript" src="examples/resources/syntax/shCore.js"></script>
	<script type="text/javascript" language="javascript" src="examples/resources/demo.js"></script>
	<script type="text/javascript" language="javascript" class="init">

$(document).ready( function () {
    $('#datatables').dataTable( {
        "dom": 'T<"clear">lfrtip',
        "tableTools": {
            "sSwfPath": "extensions/TableTools/swf/copy_csv_xls_pdf.swf",
			"aButtons": [
				"copy",
				"csv",
				"xls",
				{
					"sExtends": "pdf",
					"sPdfOrientation": "landscape",
					"sPdfMessage": "Your custom message would go here."
					
				},
				"print"
			]
        }
    } );
} );

	</script>
<?php include'../db.php'; include'../class.php'; $table='interpreter';$invoice_id= @$_GET['invoice_id'];
$query="SELECT interpreter.*,invoice.dated FROM interpreter
INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo

 where interpreter.id=$invoice_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$orgName=$row['orgName'];$inchCity=$row['inchCity'];$invoiceNo=$row['invoiceNo'];$inchEmail=$row['inchEmail'];$inchRoad=$row['inchRoad'];$buildingName=$row['buildingName'];$hoursWorkd=$row['C_hoursWorkd'];$chargInterp=$row['C_chargInterp'];$rateHour=$row['C_rateHour'];$travelMile=$row['C_travelMile'];$rateMile=$row['C_rateMile'];$chargeTravel=$row['C_chargeTravel'];$travelCost=$row['C_travelCost'];$otherCost=$row['C_otherCost'];$travelTimeHour=$row['C_travelTimeHour'];$travelTimeRate=$row['C_travelTimeRate'];$chargeTravelTime=$row['C_chargeTravelTime'];$dueDate=$row['dueDate'];$dated=date_format(date_create($row['dated']), 'd-m-Y');}?>


<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
    <title>LSUK-Invoice</title>
</head>
  <body>
  <table width="80%" id="datatables" class="display"><thead align="left"> <tr><th></th></tr></thead>
  <tbody>
    <tr>
      <td height="700"><div style="margin:auto"><div id="apDiv1">
        <div id="apDiv4">
        <table width="100%" style="border-collapse: collapse;
    border: 1px solid black;">
  <tr>
    <th width="5" scope="col">#</th>
    <th width="400" scope="col">DESCRIPTION</th>
    <th scope="col">TOTAL £</th>
    </tr>
  <tr>
    <td>01</td>
    <td><h3>Interpreting Service</h3>
      Professional interpreting service provider, offers numerous modes of interpretation in and around Bristol</td>
    <td>-----</td>
    </tr>
  <tr>
    <td>02</td>
    <td>Hours Worked</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>03</td>
    <td>Rate Per Hour</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>Charge for Interpreting Time</strong></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>04</td>
    <td>Travel Time Hours</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>05</td>
    <td>Travel Time Rate Per Hour</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>Charge for Travel Time</strong></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>06</td>
    <td>Travel Mileage</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>07</td>
    <td>Rate Per Mileage</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>Charge for Travel Cost</strong></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td>08</td>
    <td>Other Costs (Parking , Bridge Toll)</td>
    <td>&nbsp;</td>
    </tr>
  </table>
</div>
        <div id="apDiv3"></div>
        <div id="apDiv2"></div>
      </div></div></td>
    </tr>
    </tbody>
  </table>
</body>
</html>