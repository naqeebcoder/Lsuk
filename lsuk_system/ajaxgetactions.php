<?php 
  include("db.php"); 
  //include'class.php';
?>

<?php

include "permissmenuitem.php";
if(session_id() == '' || !isset($_SESSION))
{
    session_start();
    
} 

//        data: {jobid:strJobId, jobtbl:strJobTbl,strfidis: strfid, counted:nCountIs ,colName: "test"},
//jobids:strJobIds

$strUrls="";
if(isset($_POST['urls']))
	$strUrls=$_POST['urls'];

$tbl="";
if(isset($_POST['jobtbl']))
    $tbl=$_POST['jobtbl'];

$pieces = explode(",",$strUrls);
$nSize=count($pieces);

$jobsreadarr="{";
$allow;
$allowNum;

for ($i=0;$i<$nSize;$i++)
{
  $url=$pieces[$i];

  //$allow=MenuPermiss::CheckUrl($url);
  $allow=MenuPermiss::CheckUrlDb($url);
  $allowNum=$allow?1:0;

  if ($i>0)
    $jobsreadarr.=',';

  $jobsreadarr.='"'.$url.'":'.$allowNum;
}

$jobsreadarr.='}';
echo $jobsreadarr;

?>
