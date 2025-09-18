<?php if(session_id() == '' || !isset($_SESSION)){session_start();} 
include 'secure.php'; ?> 
<?php include 'source/db.php';
include 'source/class.php';
$interpreter_id=$_SESSION['web_userId'];
$order_id=$_GET['id'];
$orgName=$acttObj->read_specific("orgName","translation","id=".$order_id)['orgName'];
if(isset($_POST['btn_add_file'])){
    $table="job_files";
    $done=0;
    if($_FILES["file"]["name"]!= NULL){
        $file_name=$acttObj->upload_file("file_folder/job_files",$_FILES["file"]["name"],$_FILES["file"]["type"],$_FILES["file"]["tmp_name"],round(microtime(true)));
        if($acttObj->insert($table,array("tbl"=>"translation","file_name"=>"tr_".$file_name,"order_id"=>$order_id,"interpreter_id"=>$interpreter_id,"file_type"=>"uploaded","orgName"=>$orgName,"accessible_id"=>3,"remarks"=>$_POST['remarks']))){
            $done=1;
        }
        if($done==1){
            alert("Your file has been uploaded.Thank you");
        }else{
            alert("Failed to upload your file. Kindly try again");
        }
    }
}
if(isset($_POST['btn_update_file'])){
    $table="job_files";
    $get_old_data=$acttObj->read_specific("*","job_files","accessible_id=3 and order_id=".$order_id);
    $done=0;
    if($_FILES["file"]["name"]!= NULL){
        if(unlink('file_folder/job_files/'.$get_old_data['file_name'])){
            $file_name=$acttObj->upload_file("file_folder/job_files",$_FILES["file"]["name"],$_FILES["file"]["type"],$_FILES["file"]["tmp_name"],round(microtime(true)));
            if($acttObj->update($table,array("file_name"=>"tr_".$file_name,"remarks"=>$_POST['remarks']),array("id"=>$get_old_data['id']))){
                $done=1;
            }
            if($done==1){
                alert("Your file has been updated.Thank you");
            }else{
                alert("Failed to update your file. Kindly try again");
            }
        }
    }
}
if(isset($_POST['btn_approve_file'])){
    $table="job_files";
    $done=0;
    if($_FILES["file"]["name"]!= NULL){
        $file_name=$acttObj->upload_file("file_folder/job_files",$_FILES["file"]["name"],$_FILES["file"]["type"],$_FILES["file"]["tmp_name"],round(microtime(true)));
        if($acttObj->insert($table,array("tbl"=>"translation","file_name"=>"tr_".$file_name,"order_id"=>$order_id,"interpreter_id"=>$interpreter_id,"file_type"=>"uploaded","orgName"=>$orgName,"accessible_id"=>6,"remarks"=>$_POST['remarks']))){
            $done=1;
        }
        if($done==1){
            alert("Final copy has been uploaded.Thank you");
        }else{
            alert("Failed to upload final copy. Kindly try again");
        }
    }
}
if(isset($_POST['btn_revised_file'])){
    $table="job_files";
    $done=0;
    if($_FILES["file"]["name"]!= NULL){
        $file_name=$acttObj->upload_file("file_folder/job_files",$_FILES["file"]["name"],$_FILES["file"]["type"],$_FILES["file"]["tmp_name"],round(microtime(true)));
        if($acttObj->insert($table,array("tbl"=>"translation","file_name"=>"tr_".$file_name,"order_id"=>$order_id,"interpreter_id"=>$interpreter_id,"file_type"=>"uploaded","orgName"=>$orgName,"accessible_id"=>3,"remarks"=>$_POST['remarks']))){
            $done=1;
        }
        if($done==1){
            alert("Revised file has been uploaded.Thank you");
        }else{
            alert("Failed to upload revised file. Kindly try again");
        }
    }
}
$get_file=$acttObj->read_specific("job_files.*,accessibles.message","job_files,accessibles","job_files.accessible_id=accessibles.id and job_files.order_id=".$order_id." ORDER BY job_files.id DESC");
if($get_file['status']==1 && ($get_file['accessible_id']==1 || $get_file['accessible_id']==2) ){
    $acttObj->update("job_files",array("status"=>2),array("id"=>$get_file['id'])); 
}
?>
<!DOCTYPE HTML>
<html class="no-js">
    <head>
<?php include'source/header.php'; ?>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>select.input-sm {line-height: 22px;}.glyphicon{color:#fff;}input, textarea, select {-webkit-appearance: button;}
.dataTables_wrapper .row{margin:0px !important;}</style>
</head>

<body class="boxed">
<div id="wrap">
<?php include'source/top_nav.php'; ?>
    <section id="page-title">
    	<div class="container clearfix">
        <h1>Manage translation job</h1>
          <nav id="breadcrumbs">
               <ul>
                    <li><a href="interp_profile.php">Home</a> &rsaquo;</li>
                </ul>
          </nav>
        </div>
    </section>
    
    <section id="content" class="container-fluid clearfix">
        <center><section style="overflow-x:auto;" class="col-md-12">
        <?php if(($get_file['accessible_id']==1 || $get_file['accessible_id']==2)){ ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="col-md-12" enctype="multipart/form-data">
            <h3>Add Translation Document</h3>
            <div class="form-group col-md-2 col-md-offset-2">
                <label>Upload translated copy file<br><small>(Add your translated version of original file)</small></label>
                <input type="file" class="form-control" name="file" required>
            </div>
            <div class="form-group col-md-5">
                <textarea class="form-control" name="remarks" placeholder="Write your comments if any ..." rows="3"></textarea>
            </div>
            <div class="form-group col-md-1"><br><br>
                <button type="submit" class="btn btn-primary" name="btn_add_file"><i class="fa fa-check-circle"></i> Add Document</button>
            </div>
        </form>
    <?php }
    if($get_file['accessible_id']==3 && (isset($_GET['edit_id']) || isset($_GET['approve_id']) || isset($_GET['revised_id']))){ ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="col-md-12" enctype="multipart/form-data">
            <?php if(isset($_GET['approve_id'])){ ?>
                <h3>Upload Final Translation Document</h3>
                <div class="form-group col-md-2 col-md-offset-2">
                    <label>Upload Final translated copy<br><small>(Upload final translated version of original file)</small></label>
                    <input type="file" class="form-control" name="file" required>
                </div>
                <div class="form-group col-md-5">
                    <textarea class="form-control" name="remarks" placeholder="Write your comments if any ..." rows="4"></textarea>
                </div>
                <div class="form-group col-md-4 col-md-offset-1">
                    <button type="submit" class="btn btn-success" name="btn_approve_file"><i class="fa fa-refresh"></i> Upload Final Document</button>
                    <a href="translation_upload.php?id=<?php echo $order_id; ?>" class="btn btn-warning"><i class="fa fa-remove"></i> Cancel Upload</a>
                </div>
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
    <br><br>
    <h4>History of files uploaded for this order</h4>
           
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
    <?php $result = $acttObj->read_all("job_files.*,accessibles.message","job_files,accessibles","job_files.accessible_id=accessibles.id and job_files.order_id=".$order_id);
    if(mysqli_num_rows($result)==0){
        echo '<tr><td align="centre"><h3 class="text-center"><span class="label label-danger">No history found for this order!</span></h3></td></tr>';
    }else{
        $td_counter=1;
        while($row = $result->fetch_assoc()){
            $row_order_id=$row['order_id']; ?>
            <tr>
            <td align="left"><?php echo $td_counter++; ?> </td>
            <td align="left"><?php echo str_replace("Interpreter","You",$row['message']); ?></td>
            <td align='left'>
            <?php if($row['accessible_id']==1 || $row['accessible_id']==2){
                    if($row['status']==2){
                        echo "You accessed the file";
                    }else{
                        echo "You didn't accessed yet <i class='fa fa-exclamation-circle'></i>";
                    }
                }
                if($row['accessible_id']==3){
                    if($row['status']==2){
                        echo "Client accessed & downloaded the file";
                    }else if($row['status']==3){
                        echo "Client approved the demo file <i class='fa fa-thumbs-up'></i><br>
                        <a class='btn btn-success btn-xs' href='translation_upload.php?id=".$order_id."&approve_id'>Upload Final Document</a>";
                    }else if($row['status']==4){
                        echo "Client requested for revision <i class='fa fa-refresh'></i><br>
                        <a class='btn btn-info btn-xs' href='translation_upload.php?id=".$order_id."&revised_id'>Upload Revised Document</a>";
                    }else{
                        echo "Client didn't accessed yet <i class='fa fa-exclamation-circle'></i><br>
                        <a class='btn btn-warning btn-xs' href='translation_upload.php?id=".$order_id."&edit_id'>Edit Document</a>";
                    }
                }
                if($row['accessible_id']==6){
                    if($row['status']==2){
                        echo "Client accessed & downloaded final copy";
                    }else if($row['status']==3){
                        echo "Client approved the final copy <i class='fa fa-check-circle'></i><br>
                        <span class='label label-success'>Job Completed</span>";
                    }else if($row['status']==4){
                        echo "Client requested for revision again <i class='fa fa-refresh'></i>";
                    }else{
                        echo "Client didn't accessed yet <i class='fa fa-exclamation-circle'></i>";
                    }
                }
                ?>
            </td>
            <td align="left"><?php echo $row['remarks']?:'- - -'; ?></td>
            <td align="left"><?php echo $row['remarks_c']?:'- - -'; ?></td>
            <td align="left"><a href="javascript:void(0)" onclick="popupwindow('timesheet_view.php?t_id=<?php echo $row_order_id; ?>&table=translation', 'title', 1200, 650);"><img src="lsuk_system/images/btn_view.jpg" width="110" title="View File"></a> </td>
        </tr>
        <?php 
        }
    } ?>
    </tbody>
  </table><br><br><br>
            </section></center>
   <hr>
         
    </section>
    <!-- end content -->  
    
    <!-- begin footer -->
	<?php include'source/footer.php'; ?>
	<!-- end footer -->  
</div>
<script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>