<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include'db.php'; include'class.php';
$order_id=$_GET['id'];
$orgName=$acttObj->read_specific("orgName","translation","id=".$order_id)['orgName'];
if(isset($_POST['btn_add_file'])){
    $table="job_files";
    $done=0;
    if($_FILES["file"]["name"]!= NULL){
        $file_name=$acttObj->upload_file("file_folder/job_files",$_FILES["file"]["name"],$_FILES["file"]["type"],$_FILES["file"]["tmp_name"],round(microtime(true)));
        if($acttObj->insert($table,array("tbl"=>"translation","file_name"=>"tr_".$file_name,"order_id"=>$order_id,"interpreter_id"=>$_SESSION['web_userId'],"file_type"=>"uploaded","orgName"=>$orgName,"accessible_id"=>3,"remarks"=>$_POST['remarks']),array("id"=>$update_id))){
            $done=1;
        }
        if($done==1){
            alert("Your file has been uploaded.Thank you");
        }else{
            alert("Failed to upload your file. Kindly try again");
        }
    }
}
$q_updated_doc_checker=$acttObj->read_specific("*","job_files","accessible_id=2 and order_id=".$order_id);
$updated_doc_checker=$q_updated_doc_checker['id'];
if(empty($updated_doc_checker)){
    $accessible_id=1;
}else{
    $accessible_id=2;
}
$get_file=$acttObj->read_specific("job_files.*,accessibles.message","job_files,accessibles","job_files.accessible_id=accessibles.id and job_files.accessible_id=".$accessible_id." and job_files.order_id=".$order_id);
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Translation Document Status</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style>.multiselect {min-width: 250px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}</style>
</head>
<body>
<div class="container text-center">
    <br/>
    <?php if($accessible_id==1 && empty($get_file)){ ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="col-md-12" enctype="multipart/form-data">
            <h4>Add Translation Document</h4>
            <div class="form-group col-md-4">
                <label>Upload a file now <small>(Copy for interpreter to translate)</small></label>
                <input type="file" class="form-control" name="file">
            </div>
            <div class="form-group col-md-4"><br>
                <button type="submit" class="btn btn-primary" name="btn_add_file"><i class="fa fa-check-circle"></i> Add Document</button>
            </div>
        </form>
    <?php }
    if($accessible_id==1 && !empty($get_file) && isset($_GET['edit_id'])){ ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="col-md-12" enctype="multipart/form-data">
            <h4>Edit Translation Document</h4>
            <div class="form-group col-md-4">
                <label>Click to re-upload the original file</label>
                <input type="file" class="form-control" name="file">
            </div>
            <div class="form-group col-md-4"><br>
                <button type="submit" class="btn btn-primary" name="btn_add_file"><i class="fa fa-check-circle"></i> Update Document</button>
            </div>
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
                        echo "Client accessed & downloaded the file";
                    }else if($row['status']==3){
                        echo "Client approved the file for final copy";
                    }else if($row['status']==4){
                        echo "Client requested for revision";
                    }else{
                        echo "Client didn't accessed yet!";
                    }
                }
                if($row['accessible_id']==6){
                    if($row['status']==2){
                        echo "Client accessed & downloaded final copy";
                    }else if($row['status']==3){
                        echo "Client approved the final copy";
                    }else if($row['status']==4){
                        echo "Client requested for revision again";
                    }else{
                        echo "Client didn't accessed yet!";
                    }
                }
                ?>
            </td>
            <td align="left"><?php echo $row['remarks']?:'- - -'; ?></td>
            <td align="left"><?php echo $row['remarks_c']?:'- - -'; ?></td>
            <td align="left"><a href="javascript:void(0)" onclick="popupwindow('timesheet_view.php?t_id=<?php echo $row_order_id; ?>&table=translation', 'title', 1200, 650);"><img src="images/btn_view.jpg" width="110" title="View File"></a> </td>
        </tr>
        <?php 
        }
    } ?>
    </tbody>
  </table><br><br><br>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
function popupwindow(url, title, w, h) {
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);
  return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}
function remove_porder(elem){
    var remove_comp_id=elem.id;
    // $.ajax({
    //     url:'ajax_add_comp_data.php',
    //     method:'post',
    //     data:{remove_comp_id:remove_comp_id},
    //     success:function(data){
    //     $('#append_childs').html(data);
    //     }, error: function(xhr){
    //     alert("An error occured: " + xhr.status + " " + xhr.statusText);
    //     }
    // });
}
</script>
</body>
</html>