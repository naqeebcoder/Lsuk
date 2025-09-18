<?php if(session_id() == '' || !isset($_SESSION)){session_start();}
include 'class.php';
include 'db.php';
$id=$_GET['t_id'];
$table=$_GET['table'];
if(isset($_SESSION['web_userId'])){
    $valid_check_q=$acttObj->unique_dataAnd($table,'id','intrpName',$_SESSION['web_userId'],'id',$id);
    $valid_check=$valid_check_q!=''?'yes':'no';
}

if($valid_check=='yes' || ($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' )){
$result=$acttObj->read_all('id,time_sheet',$table,'id='.$id);
$row=$result->fetch_assoc();
if($table=='interpreter'){
    $filepath="../file_folder/time_sheet_interp/".$row['time_sheet'];
}elseif($table=='telephone'){
    $filepath="../file_folder/time_sheet_telep/".$row['time_sheet'];
}else{
    $filepath="../file_folder/time_sheet_trans/".$row['time_sheet'];
}
if (isset($_GET['file_id'])) {
    $file_id = $_GET['file_id'];
    // $file_data = $acttObj->read_specific('*',"job_files","id=".$file_id);
    if (file_exists($filepath)) {
        // $download_counter = ($file_data['downloads']+1);
        // $acttObj->update("job_files",array("downloads"=>$download_counter),array("id"=>$file_id));
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . uniqid().'.'.end(explode('.',$row['time_sheet'])));
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
<title>View Uploaded Timesheet</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<body>
<div class="container-fluid">
<center><h4>Timesheet for Record ID: <span class="label label-danger"><?php echo $id; ?></span></h4><br/>
<?php if (file_exists($filepath)) { ?>
<iframe src="<?php echo $filepath; ?>" frameborder='2' width="100%" height="100%"></iframe>
<?php } ?>
<a href="<?php echo $_SERVER['PHP_SELF'].'?t_id='.$id.'&table='.$table.'&file_id='.$row['id']; ?>" title="Click to download Timesheet"/><img width="200" src="images/download_doc.png"></a>
<!--<a target="_blank" href="<?php echo $filepath; ?>" title="Click to view Timesheet"/><img width="160" src="images/btn_view.jpg"></a>-->
</center>
</div>
<?php }else{
echo "<center><h1 class='text-danger'>Sorry! You are not allowed to access this file. Thank you</h1></center>";} ?>