<?php 


$milesaway="?";

if (!isset($assignpostcode))
    return;

if (!isset($postcodeinterp))
    return;

//$strCodeTo="bs107na";
//$strCodeFrom="bs24 7hs";
$strCodeFrom=$postcodeinterp;
$strCodeTo=$assignpostcode;

$strCodeTo = str_replace(' ', '', $strCodeTo);
$strCodeFrom = str_replace(' ', '', $strCodeFrom);

//lose spaces
//urlencode
$strUrlFrom='http://api.getthedata.com/postcode/'.$strCodeFrom;
$rsp=file_get_contents($strUrlFrom);
$obj=json_decode($rsp,TRUE);
if (!isset($obj["status"]) || $obj["status"]=="no_match")
    return;

$data=$obj["data"];
$strLong=$data["longitude"];
$strLat=$data["latitude"];
$strSrc="$strLat,$strLong";

$strUrlTo='http://api.getthedata.com/postcode/'.$strCodeTo;
$rsp=file_get_contents($strUrlTo);
$obj=json_decode($rsp,TRUE);
if (!isset($obj["status"]) || $obj["status"]=="no_match")
    return;
$data=$obj["data"];
$strLong=$data["longitude"];
$strLat=$data["latitude"];
$strDest="$strLat,$strLong";

//$strSrc="51.512074,-2.630160";
//$strDest="51.513329,-2.596399";

$strKey="AIzaSyA7nA7Vg3gzVhxFyHliCA2nWJkQRWC7GrU";

$strDistMatrixUrl='https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins='.
  $strSrc.'&destinations='.$strDest.'&key='.$strKey;

//strDistMatrixUrl+="&jsoncallback=?";
//var strUrl=encodeURI(strDistMatrixUrl);


$rsp=file_get_contents($strDistMatrixUrl);

//var_dump($rsp);
$obj=json_decode($rsp,TRUE);

//if($arr['success']){$captcha_flag=1;}else{echo 'Spam';}

$ok=$obj["status"];

$rows=$obj["rows"];
$data=$rows[0];

$elem=$data["elements"];
//$d=$elem["distance"];

$calc=$elem[0];
$odist=$calc["distance"];
$txt=$odist["text"];
$val=$odist["value"];

$fltMiles = floatval($txt);
$milesaway=$fltMiles;

$odur=$calc["duration"];

?>  

