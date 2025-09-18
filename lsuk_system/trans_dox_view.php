<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
$allowed_type_idz = "164";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Client Attached Document</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$id=$_GET['order_id'];
$table=$_GET['table'];
// Downloads files
if (isset($_GET['file_id'])) {
    $file_id = $_GET['file_id'];
    $file_data = $acttObj->read_specific('*',"job_files","id=".$file_id);
    $filepath="../file_folder/trans_dox/".$file_data['file_name'];
    if (file_exists($filepath)) {
        $download_counter = ($file_data['downloads']+1);
        $acttObj->update("job_files",array("downloads"=>$download_counter),array("id"=>$file_id));
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . uniqid().'.'.end(explode('.',$file_data['file_name'])));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    }
}
?> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>View Files</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<body>
<div class="container-fluid">
<center><h4>Translation Documents for Record ID: <span class="label label-danger"><?php echo $id; ?></span></h4></center><br/>
<?php $get_file_data=$acttObj->read_specific('*','job_files','status=1 AND downloads<1 and tbl="'.$table.'" and file_type="c_portal" and order_id='.$id);
if(empty($get_file_data['id'])){
    // $get_file_path="../file_folder/trans_dox/".$get_file_data['file_name'];
    // if (file_exists($get_file_path)) {
    //     unlink($get_file_path);
    // } ?>
<div class="alert alert-danger">
  <strong>Note!</strong><br>This file is no more available!<br>It is already downloaded and automatically removed after. Thank you
</div>
<?php }else{ ?>
<div class="alert alert-danger">
  <strong>Warning!</strong><br>Files in this window can be downloaded only once and will be removed after closing this window.<br>
  Save this file before you leave this window. Thank you
</div>
<table class="table table-bordered">
    <thead class="bg-primary">
        <th>File Number</th>
        <th>Job ID</th>
        <!--<th>Downloads</th>-->
        <th>Action</th>
    </thead>
    <tbody>
<?php 
$job_file=$acttObj->read_all('*','job_files','status=1 and tbl="'.$table.'" and file_type="c_portal" and order_id='.$id);
$i=1;
while($row=$job_file->fetch_assoc()){?>
<tr>
    <td>Document No <?php echo $i; ?></td>
    <td>Job <?php echo $row['order_id']; ?></td>
    <!--<td><?php echo $row['downloads']; ?></td>-->
    <td>
        <a href="<?php echo $_SERVER['PHP_SELF'].'?order_id='.$id.'&table='.$table.'&file_id='.$row['id']; ?>" title="Click to download this file"/><img width="130" src="images/download_doc.png"></a>
        <!--<a target="_blank" href="<?php echo $filepath; ?>" title="Click to view this file"/><img width="110" src="images/btn_view.jpg"></a>-->
        </td>
</tr>
<?php $i++; } ?>
</body>
</table>
<?php } ?>
</div>