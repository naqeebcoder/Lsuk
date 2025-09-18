<?php 
  include("db.php"); 
  include'class.php';
?>

<?php

//        data: {jobid:strJobId, jobtbl:strJobTbl,strfidis: strfid, counted:nCountIs ,colName: "test"},

//jobids:strJobIds

$strJobIds="";
if(isset($_POST['jobids']))
	$strJobIds=$_POST['jobids'];

$tbl="";
if(isset($_POST['jobtbl']))
    $tbl=$_POST['jobtbl'];

$pieces = explode(",",$strJobIds);
$nSize=count($pieces);

$jobsreadarr="{";
//$nSize=1;

for ($i=0;$i<$nSize;$i++)
{
  $strJobIdIs=$pieces[$i];

  $sql_opt="select count(id) as unread 
    from  jobnotes
    where tbl='$tbl' and fid=$strJobIdIs and (readcount is null or readcount=0)";

  $result_opt=mysqli_query($con,$sql_opt);
  $unread=0;
  if ($result_opt==true)
  {
    while ($row_opt=mysqli_fetch_array($result_opt)) 
    {
      $unread=$row_opt["unread"];
    }
  }
    
  $sql_opt="select count(id) as readit 
    from jobnotes 
    where tbl='$tbl' and fid=$strJobIdIs and (readcount is not null and readcount<>0)";

  $result_opt=mysqli_query($con,$sql_opt);
  $read=0;
  if ($result_opt==true)
  {
    while ($row_opt=mysqli_fetch_array($result_opt)) 
    {
      $read=$row_opt["readit"];
    }
  }

  if ($i>0)
    $jobsreadarr.=',';

  //$mapResult[$strJobIdIs]="unread,";
  $jobsreadarr.='"'.$strJobIdIs.'":"'.$unread.','.$read.'"';

  //end for
}
$jobsreadarr.='}';

//echo $strJobIds."here in ajaxListJobNotes.php";
echo $jobsreadarr;

?>
