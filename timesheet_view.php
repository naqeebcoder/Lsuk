<?php if(session_id() == '' || !isset($_SESSION)){session_start();} 
$t_id=$_GET['t_id'];
include 'source/class.php';
include 'source/db.php';
if(isset($_SESSION['web_userId'])){
    $valid_check_q=$acttObj->unique_dataAnd($_GET['table'],'id','intrpName',$_SESSION['web_userId'],'id',$t_id);
$valid_check=$valid_check_q!=''?'yes':'no';
}
?> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>View Uploaded Timesheet</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<body>
<div class="container-fluid">
<center><h4>Timesheet for Record ID: <span class="label label-danger"><?php echo $_GET['t_id']; ?></span></h4><br/>
<?php 
if($valid_check=='yes' || ($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' )){
$query_t="select time_sheet from ".$_GET['table']." where id=".$t_id;
$result_t=mysqli_query($con,$query_t);
$row_t=mysqli_fetch_array($result_t);
if($_GET['table']=='interpreter'){
$url_t="file_folder/time_sheet_interp/".$row_t['time_sheet'];
}elseif($_GET['table']=='telephone'){
    $url_t="file_folder/time_sheet_telep/".$row_t['time_sheet'];
}else{
    $url_t="file_folder/time_sheet_trans/".$row_t['time_sheet'];
}
?>
<a download="<?php echo $row_t['time_sheet']; ?>" href="<?php echo $url_t; ?>" title="Click to download Timesheet"/><img width="200" src="lsuk_system/images/download_doc.png"></a>
        <a target="_blank" href="<?php echo $url_t; ?>" title="Click to view Timesheet"/><img width="160" src="lsuk_system/images/btn_view.jpg"></a>
<?php }else{
echo "<center><h1 style='color:red;'>Sorry! You are not allowed to access this file. Thank you</h1></center>";} ?></center>
</div>