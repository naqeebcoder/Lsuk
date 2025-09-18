<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include'db.php'; include'class.php';
$order_id=$_GET['id'];
$get_file=$acttObuj->read_specific("job_files.*,accessibles.text","job_files,accessibles","job_files.accessible_id=accessibles.id and job_files.order_id=".$order_id);
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Translation Document Status</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style>.multiselect {min-width: 250px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}</style>
</head>
<body>
<?php if(isset($_POST['btn_add_file'])){
    $table="job_files";
    $counter=0;$done=0;
    $comp_id=explode(',',$items);
        while($counter<count($comp_id)){
            $chk_exist = $acttObj->read_specific("po_req","$table","id=".$comp_id[$counter]);
            if($chk_exist['po_req']=='0'){
                $acttObj->editFun($table,$comp_id[$counter],'po_req','1');
                if($counter==count($comp_id)-1){$done=1;}
            }else{
                $done=1;
            }
            $counter++;
        }
        if($done==1){
            echo "<script>alert('Purchase Order record updated for selected companies.');</script>";
        }
}
?>
<div class="container text-center">
    <?php if(isset($_GET['edit_id'])){
        echo "<h4>Edit Translation Document</h4>";
    }else{
        echo "<h4>Add Translation Document</h4>";
    } ?>
    <br/>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="col-md-6" enctype="multipart/form-data">
    <?php if($_SESSION['prv']=='Management'){
        if(!empty($get_file)){ ?>
            <label class="input">View uploaded file for translation</label>
            <a href="javascript:coid(0)" onClick="MM_openBrWindow('timesheet_view.php?t_id=<?php echo $order_id; ?>&table=translation','_blank','scrollbars=yes,resizable=yes,width=600,height=400,left=400,top=200')"><img src="images/images.jpg" width="30" height="30" title="View Uploaded File"></a>
            <div class="form-group col-md-2 col-sm-4">
                <label>Clickto re-upload new file</label>
                <input type="file" class="form-control" name="file">
            </div>
        <?php }else{ ?>
            <label class="text-danger">No file uploaded for translation!<img src="images/missing.jpg" width="50" height="50">
            <div class="form-group col-md-2 col-sm-4">
                <label>Upload a file now</label>
                <input type="file" class="form-control" name="file">
            </div>
        <?php } ?>
        <button type="submit" class="btn btn-primary" name="btn_add_file"><i class="fa fa-check-circle"></i> Submit</button>
    <?php } ?>
    </form>
    <br><br>
    <h4>History of files uploaded for this order</h4>
           
    <table class="table table-bordered table-hover">
        <tbody>
    <?php $result = $acttObj->read_all("job_files.*,accessibles.text","job_files,accessibles","job_files.accessible_id=accessibles.id and job_files.order_id=".$order_id);
    if(mysqli_num_rows($result)==0){
        echo '<tr><td align="centre"><h3 class="text-center"><span class="label label-danger">No history found for this ordert!</span></h3></td></tr>';
    }else{
        $td_counter=1;
        while($row = $result->fetch_assoc())){
            $row_order_id=$row['order_id']; ?>
            <tr>
            <td align="left"><?php echo $td_counter++; ?> </td>
            <td align="left"><a href="javascript:coid(0)" onClick="MM_openBrWindow('timesheet_view.php?t_id=<?php echo $row_order_id; ?>&table=translation','_blank','scrollbars=yes,resizable=yes,width=600,height=400,left=400,top=200')"><img src="images/btn_view.jpg" width="30" height="30" title="View File"></a> </td>
            <td align="left"><?php echo $row['text']; ?></td>
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