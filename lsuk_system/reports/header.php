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
       <script type="text/javascript">
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);}</script>
<?php
include'../db.php';if (!$con) {die("Connection failed: " . mysqli_connect_error());}?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>LSUK-Reporting</title>
<style>
body{font: 11px Arial, Helvetica, sans-serif;}
#nav ul {
margin:0;
padding:0;
list-style-type:none;
}
#nav ul li {
display: inline; /*IE 6*/
}
#nav ul li a {
display:block;
background:#FFF;
width:202px;
margin-left:3px;
text-decoration:none;
padding:4px/*padding for top, bottom*/ 7px /*padding for left, right*/;
border:1px solid #eeeeee;
color:#333333;}
#nav ul li a:hover {
border-left-color:#eeeeee;
color:#FFF;
font-weight:bold;
background:#666;
}
</style>
<style type="text/css">
#apDiv1 {
	position:absolute;
	width:1136px;
	height::auto;
	z-index:1;
	left: 211px;
	top: 50px;
	border:1px solid #CCC;
}
#apDiv2 {
	position:absolute;
	width:199px;
	height:700px;
	z-index:2;
	left: 0px;
	top: 50px;
}
#apDiv3 {
	position:absolute;
	width:195px;
	height:694px;
	z-index:1;
	top: 2px;
	left: -10px;
	background-color: #FFFFFF;
}
#apDiv4 {
	position:absolute;
	background-image:url(images/hi-drawing00.png);
	width:100%;
	height:49px;
	z-index:3;
	left: 0px;
	top: 0px;
	color:#FFF;
	text-align:center;
	font-size:26px;
	
}
</style>
</head>

<body>
<div id="apDiv4">
<div></div></div>
</body>
</html>
