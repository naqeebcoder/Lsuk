<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'class.php';
include 'db.php';
$order_id=$_GET['order_id'];
$tbl=$_GET['table'];
?> 
<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>View Extra Files</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<body>
<div class="container-fluid">
<center><h4>Extra Files for Record ID: <span class="label label-danger"><?php echo $order_id; ?></span></h4></center><br/>
<table class="table table-bordered">
    <thead class="bg-primary">
        <th>File Number</th>
        <th>Job ID</th>
        <th>Action</th>
    </thead>
    <tbody>
<?php 
$job_file_q=$acttObj->read_all('file_name,order_id','job_files','status=1 and tbl="'.$tbl.'" and file_type="timesheet" and order_id='.$order_id);
$i=1;
while($row_files=mysqli_fetch_array($job_file_q)){
$url_file="../file_folder/job_files/".$row_files['file_name'];
?>
<tr>
    <td>Document No <?php echo $i; ?></td>
    <td>Job <?php echo $row_files['order_id']; ?></td>
    <td>
        <a download="<?php echo $row_files['file_name']; ?>" href="<?php echo $url_file; ?>" title="Click to download this file"/><img width="130" src="images/download_doc.png"></a>
        <a target="_blank" href="<?php echo $url_file; ?>" title="Click to view this file"/><img width="40" src="images/view_file.png"></a>
        </td>
</tr>
<?php $i++; } ?>
</body>
</table>
</div>