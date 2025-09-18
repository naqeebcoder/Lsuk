<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'class.php';
include 'db.php';
$order_id=$_GET['order_id'];
$updated_doc_checker=$acttObj->read_specific("*","job_files","accessible_id IN (1,2) and order_id=".$order_id." ORDER BY id DESC LIMIT 1");
$get_file=$acttObj->read_specific("job_files.*,accessibles.message","job_files,accessibles","job_files.accessible_id=accessibles.id and job_files.accessible_id=".$updated_doc_checker['accessible_id']." and job_files.order_id=".$order_id);
//Update status of uploaded files for client
$get_uploaded_file=$acttObj->read_specific("job_files.*","job_files","accessible_id IN (3,6) and order_id=".$order_id." ORDER BY id DESC");
if($get_uploaded_file['status']==1 && ($get_uploaded_file['accessible_id']==3 || $get_uploaded_file['accessible_id']==6) ){
    $acttObj->update("job_files",array("status"=>2),array("id"=>$get_uploaded_file['id'])); 
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Acess your file</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<body>
<div class="container-fluid">
<center><h4>Translation Documents for Project ID: <span class="label label-danger"><?php echo $order_id; ?></span></h4><br/>
<?php if(($get_file['accessible_id']==3 || $get_file['accessible_id']==6) && (isset($_GET['edit_id']) || isset($_GET['approve_id']) || isset($_GET['revised_id']))){ ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="col-md-12" enctype="multipart/form-data">
            <?php if(isset($_GET['approve_id'])){
                if($get_file['accessible_id']==3){ ?>
                    <h3>Approve For Final Translation</h3>
                    <div class="form-group col-md-5">
                        <textarea class="form-control" name="remarks_c" placeholder="Write your comments if any ..." rows="4"></textarea>
                    </div>
                    <div class="form-group col-md-4 col-md-offset-1">
                        <button type="submit" class="btn btn-success" name="btn_approve_for_final"><i class="glyphicon glyphicon-refresh"></i> Submit</button>
                        <a href="file.php?id=<?php echo $order_id; ?>" class="btn btn-warning"><i class="glyphicon glyphicon-remove"></i> Cancel Action</a>
                    </div>
                <?php }
                if($get_file['accessible_id']==6){ ?>
                    <h3>Approve Final Translated Copy</h3>
                    <div class="form-group col-md-5">
                        <textarea class="form-control" name="remarks_c" placeholder="Write your comments if any ..." rows="4"></textarea>
                    </div>
                    <div class="form-group col-md-4 col-md-offset-1">
                        <button type="submit" class="btn btn-success" name="btn_approve_final"><i class="glyphicon glyphicon-refresh"></i> Submit</button>
                        <a href="file.php?id=<?php echo $order_id; ?>" class="btn btn-warning"><i class="glyphicon glyphicon-remove"></i> Cancel Action</a>
                    </div>
                <?php } ?>
            <?php }else if(isset($_GET['revised_id'])){ ?>
                <h3>Upload Revised Translation Document</h3>
                <div class="form-group col-md-2 col-md-offset-2">
                    <label>Upload Revised translated copy<br><small>(Upload Revised translated version of file)</small></label>
                    <input type="file" class="form-control" name="file" required>
                </div>
                <div class="form-group col-md-5">
                    <textarea class="form-control" name="remarks" placeholder="Write your comments if any ..." rows="4"></textarea>
                </div>
                <div class="form-group col-md-4 col-md-offset-1">
                    <button type="submit" class="btn btn-info" name="btn_revised_file"><i class="fa fa-refresh"></i> Upload Revised Document</button>
                    <a href="translation_upload.php?id=<?php echo $order_id; ?>" class="btn btn-warning"><i class="fa fa-remove"></i> Cancel Upload</a>
                </div>
            <?php }else{ ?>
                <h3>Update Translation Document</h3>
                <div class="form-group col-md-2 col-md-offset-2">
                    <label>Upload translated copy file<br><small>(Upload your translated version of original file)</small></label>
                    <input type="file" class="form-control" name="file" required>
                </div>
                <div class="form-group col-md-5">
                    <textarea class="form-control" name="remarks" placeholder="Write your comments if any ..." rows="4"><?php echo $get_file['remarks'] ?></textarea>
                </div>
                <div class="form-group col-md-4 col-md-offset-1">
                    <button type="submit" class="btn btn-primary" name="btn_update_file"><i class="fa fa-refresh"></i> Update Document</button>
                    <a href="translation_upload.php?id=<?php echo $order_id; ?>" class="btn btn-warning"><i class="fa fa-remove"></i> Cancel Update</a>
                </div>
            <?php } ?>
        </form>
        <?php } ?>
<h4>History of files uploaded for this order</h4></center>
    <table class="table table-bordered">
        <thead class="bg-info">
            <th>S.No</th>
            <th>Message</th>
            <th>Action</th>
            <th>LSUK/Interpreter Remarks</th>
            <th>Client Remarks</th>
            <th>Options</th>
        </thead>
        <tbody>
    <?php $result = $acttObj->read_all("job_files.*,accessibles.message","job_files,accessibles","job_files.accessible_id=accessibles.id and job_files.id>=".$get_file['id']." and job_files.order_id=".$order_id);
    if($result->num_rows==0){
        echo '<tr><td align="centre"><h3 class="text-center"><span class="label label-danger">No history found for this ordert!</span></h3></td></tr>';
    }else{
        $td_counter=1;
        while($row = $result->fetch_assoc()){
            $row_order_id=$row['order_id']; ?>
            <tr>
            <td align="left"><?php echo $td_counter++; ?> </td>
            <td align="left"><?php echo $row['message']; ?></td>
            <td align='left'>
            <?php if($row['accessible_id']==1 || $row['accessible_id']==2){
                    if($row['status']==2){
                        echo "Interpreter accessed the file";
                    }else{
                        echo "Interpreter didn't accessed yet!";
                    }
                }
                if($row['accessible_id']==3){
                    if($row['status']==2){
                        echo "You accessed the translated copy<br>
                        <a class='btn btn-warning btn-xs' href='file.php?id=".$order_id."&revised_id'><i class='glyphicon glyphicon-refresh'></i> Request Revision</a>
                        <a class='btn btn-success btn-xs' href='file.php?id=".$order_id."&approve_id'><i class='glyphicon glyphicon-ok'></i> Approve Translated Copy</a>";
                    }else if($row['status']==3){
                        echo "You approved the file for final copy <i class='glyphicon glyphicon-thumbs-up'></i>";
                    }else if($row['status']==4){
                        echo "You requested for revision <i class='glyphicon glyphicon-refresh'></i>";
                    }else{
                        echo "You didn't accessed yet!";
                    }
                }
                if($row['accessible_id']==6){
                    if($row['status']==2){
                        echo "You accessed final translated copy<br>
                        <a class='btn btn-warning btn-xs' href='file.php?id=".$order_id."&revised_id'><i class='glyphicon glyphicon-refresh'></i> Request Revision Again</a>
                        <a class='btn btn-success btn-xs' href='file.php?id=".$order_id."&approve_id'><i class='glyphicon glyphicon-ok'></i> Approve Final Copy</a>";
                    }else if($row['status']==3){
                        echo "You approved the final copy <i class='glyphicon glyphicon-thumbs-up'></i>";
                    }else if($row['status']==4){
                        echo "You requested for revision again <i class='glyphicon glyphicon-refresh'></i>";
                    }else{
                        echo "You didn't accessed yet!";
                    }
                }
                ?>
            </td>
            <td align="left"><?php echo $row['remarks']?:'- - -'; ?></td>
            <td align="left"><?php echo $row['remarks_c']?:'- - -'; ?></td>
            <td align="left">
                <a download="<?php echo $row['file_name']; ?>" href="../file_folder/job_files/<?php echo $row['file_name']; ?>" title="Click to download this file"/>
                    <img width="140" src="images/download_doc.png">
                </a>
            </td>
        </tr>
        <?php 
        }
    } ?>
    </tbody>
  </table><br><br><br>
</div>