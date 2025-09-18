<?php

//include "stdcode.php";

$postdata = file_get_contents("php://input");

//header

//myHdrsIn.Add("x-named", strName);
//myHdrsIn.Add("x-dir", "lsuk_system");

//"HTTP_X_NAMED"

$strNamed=$_SERVER["HTTP_X_NAMED"];
$strDir=$_SERVER["HTTP_X_DIR"];

//$strNamedSys=MapPath($strDir,$strNamed);

file_put_contents($strNamed,$postdata);

echo "ok";
?>

