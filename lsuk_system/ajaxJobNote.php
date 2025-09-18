<?php 
  include("db.php"); 
  include'class.php';
?>

<?php

//        data: {jobid:strJobId, jobtbl:strJobTbl,strfidis: strfid, counted:nCountIs ,colName: "test"},

$idis=0;
if(isset($_POST['strfidis']))
	$idis=$_POST['strfidis'];

$nCountIs=0;
if(isset($_POST['counted']))
  $nCountIs=$_POST['counted'];
  
$strColName=0;
if(isset($_POST['colName']))
	$strColName=$_POST['colName'];

$fid=0;
if(isset($_POST['jobid']))
  $fid=$_POST['jobid'];
  
$tbl="";
if(isset($_POST['jobtbl']))
    $tbl=$_POST['jobtbl'];
    
if ($nCountIs>0)
  $acttObj->editFun("jobnotes",$idis,'readcount',0);
else
  $acttObj->editFun("jobnotes",$idis,'readcount',$nCountIs+1);

  //include("jobnotetable.php?table=".$tbl."&fid=".$fid);
  include("jobnotetable.php");

  //echo $resjson;
?>
