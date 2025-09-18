<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){session_start();}
include 'db.php';
include'class.php';?>
<!doctype html>
<html lang="en">
<head>
  <title>LSUK Booking Forms management</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <style>.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {color: #fbfbfb;background-color: #337ab7;}</style>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
    function popupwindow(url, title, w, h) {
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
    }
    function deleted(val){
      var result = confirm("Are you sure to trash this "+val+"?");
      if (result == true){
        return true;
      }else{
        return false;
      }
    }
    function activate(val){
      var result = confirm("Are you sure to activate this "+val+"?");
      if (result == true){
        return true;
      }else{
        return false;
      }
    }
  </script>
  <?php if(empty($_GET['table'])){
    $interp_class="class='active'";$telep_class='';$trans_class='';
    $interp_cls="in active";$telep_cls='';$trans_cls='';
  }else{
    if($_GET['table']=='interp_cat'){
      $interp_class="class='active'";$telep_class='';$trans_class='';
      $interp_cls="in active";$telep_cls='';$trans_cls='';
    }if($_GET['table']=='telep_cat'){
      $interp_class="";$telep_class="class='active'";$trans_class='';
      $telep_cls="in active";$interp_cls="";$trans_cls='';
    }
    if($_GET['table']=='trans_cat'){
      $interp_class="";$telep_class='';$trans_class="class='active'";
      $interp_cls="";$telep_cls='';$trans_cls="in active";
    }
  }
// trash records
  if(isset($_GET['del']) && isset($_GET['table'])){
    $del_id=$_GET['id'];
    $del_table=$_GET['table'];
    if($del_table=='interp_cat'){
      $del_status='ic_status';
      $id='ic_id';
    }else if($del_table=='telep_cat'){
      $del_status='tpc_status';
      $id='tpc_id';
    }else{
      $del_status='tc_status';
      $id='tc_id';
    }
    if($acttObj->update($del_table,array($del_status=>'0'),array($id=>$del_id))){echo '<script>alert("Record successfully trashed.")</script>';
  } else {echo '<script>alert("Failed to trash this record!")</script>';
}
}
// activate records
if(isset($_GET['act']) && isset($_GET['table'])){
  $act_id=$_GET['id'];
  $act_table=$_GET['table'];
  if($act_table=='interp_cat'){
    $act_status='ic_status';
    $id='ic_id';
  }else if($act_table=='telep_cat'){
    $act_status='tpc_status';
    $id='tpc_id';
  }else{
    $act_status='tc_status';
    $id='tc_id';
  }
  if($acttObj->update($act_table,array($act_status=>'1'),array($id=>$act_id))){echo '<script>alert("Record successfully activated.")</script>';
} else {echo '<script>alert("Failed to activate this record!")</script>';
}
}
?>
</head>
<body>
  <section class="container-fluid" style="overflow-x:auto">
    <center><h2 class="text-center"><span class="label label-primary">Booking Form Dropdowns</span></h2></center>
    <ul class="nav nav-tabs">
      <li <?php echo $interp_class; ?>><a data-toggle="tab" href="#tab_f2f"><i class="glyphicon glyphicon-user"></i> Face To Face</a></li>
      <li <?php echo $telep_class; ?>><a data-toggle="tab" href="#tab_tp"><i class="glyphicon glyphicon-phone"></i> Telephone</a></li>
      <li <?php echo $trans_class; ?>><a data-toggle="tab" href="#tab_tr"><i class="glyphicon glyphicon-globe"></i> Translation</a></li>
    </ul>
    <br>
      <a class="btn btn-info" href="manage_bookings.php?table=interp_cat&add"><i class="glyphicon glyphicon-plus"></i> Add new option</a><br>
      <?php if(isset($_GET['add'])){ ?>
        <form action="" method="post" id="form_add">
      <div class="row">
      <div class="form-group col-sm-8 col-sm-offset-2">
          <label>Field Title</label>
	    <input class="form-control" type="text" name="title" required/>
       </div>
      <div class="form-group col-sm-8 col-sm-offset-2">
          <label>Option For</label>
	    <select class="form-control" name="option_for" required>
	        <option disabled selected value="">Select option for</option>
	        <option value="f2f">Face To Face</option>
	        <option value="tp">Telephone</option>
	        <option value="tr">Translation</option>
	    </select>
       </div>
    <div class="form-group col-sm-8 col-sm-offset-2">
    <button type="submit" name="btn_add" class="btn btn-primary">Add &raquo;</button>
    <a href="javascript:void(0)" onclick="$('#form_add').css('display','none');" name="btn_cancel" class="btn btn-warning">Cancel</a>
  </div>
       </div>
</form>
<?php if(isset($_POST['btn_add'])){
        $post_title=mysqli_escape_string($con,$_POST['title']);
        $dated=date('Y-m-d');
        if($_POST['option_for']=='f2f'){
            $add_table='interp_cat';
            $add_field='ic_title';
            $add_date='ic_date';
        }else if($_POST['option_for']=='tp'){
            $add_table='telep_cat';
            $add_field='tpc_title';
            $add_date='tpc_date';
        }else{
            $add_table='trans_cat';
            $add_field='tc_title';
            $add_date='tc_date';
        }
        $option_for=$_POST['option_for'];
        if($acttObj->insert($add_table,array($add_field=>$post_title,$add_date=>$dated))){?>
         <script>alert('New record successfully added.');
         $('#form_add').css('display','none');</script>   
        <?php }else{
         echo "<script>alert('Failed to add new record!');</script>";
        }
    } ?>
    <?php } ?>
    <?php if(isset($_GET['edit']) && isset($_GET['id'])){
        if($_GET['table']=='interp_cat'){
      $cat_table='interp_cat';
      $cat_title='ic_title';
      $cat_id='ic_id';
      $cat_show='Face To Face';
  }else if($_GET['table']=='telep_cat'){
      $cat_table='telep_cat';
      $cat_title='tpc_title';
      $cat_id='tpc_id';
  }else{
      $cat_table='trans_cat';
      $cat_title='tc_title';
      $cat_id='tc_id';
  }
    $edit_data=$acttObj->read_specific("*",$cat_table,"$cat_id=".$_GET['id']);?>
        <form action="" method="post" id="form_edit">
            <div class="row">
      <div class="form-group col-sm-8 col-sm-offset-2">
          <label>Field Title</label>
	    <input class="form-control" type="text" name="title" value="<?php echo utf8_encode($edit_data[$cat_title]); ?>"/>
       </div>
    <div class="form-group col-sm-8 col-sm-offset-2">
    <button type="submit" name="btn_edit" class="btn btn-primary">Update &raquo;</button>
    <a href="javascript:void(0)" onclick="$('#form_edit').css('display','none');" name="btn_cancel" class="btn btn-warning">Cancel</a>
  </div>
  </div>
</form>
<?php if(isset($_POST['btn_edit'])){
        $post_title=mysqli_escape_string($con,$_POST['title']); 
        if($acttObj->update($_GET['table'],array($cat_title=>$post_title),array($cat_id=>$_GET['id']))){?>
         <script>alert('Record successfully updated.');
         $('#form_edit').css('display','none');</script>   
        <?php }else{
         echo "<script>alert('Failed to update this record!');</script>";}
    } ?>
    <?php } ?>
    <div class="tab-content col-md-10">
      <div id="tab_f2f" class="tab-pane fade <?php echo $interp_cls; ?>"><br>
        <table class="table table-bordered">
          <thead class="bg-primary">
            <th>S.No</th>
            <th>Title</th>
            <th>Dated</th>
            <th>Action</th>
          </thead>
          <tbody>
            <?php $counter=1;
            $q_data=$acttObj->read_all("*","interp_cat","");
            while($row=$q_data->fetch_assoc()){ ?>
              <tr <?php if(isset($_GET['edit']) && $_GET['id']==$row['ic_id']){ ?> style='background-color:#1b631b47;' <?php } ?>>
                <td><?php echo $counter++; ?></td>
                <td><?php echo $row['ic_title']; ?></td>
                <td><?php echo $row['ic_date']; ?></td>
                <td width="20%">
                  <div class="col-sm-12 action_buttons">
                      <a class="btn btn-xs btn-warning" title="Edit Category Title" href="manage_bookings.php?id=<?php echo $row['ic_id']; ?>&table=interp_cat&edit"><i class="glyphicon glyphicon-pencil"></i></a>
                    <a class="btn btn-xs btn-primary" title="View Sub Types"  href="javascript:void(0)" onClick="popupwindow('view_sub_types.php?id=<?php echo $row['ic_id']; ?>&table=interp_types', 'title', 800, 600);"><i class="glyphicon glyphicon-eye-open"></i></a>
                    <?php if($row['ic_status']=='1'){ ?>
                      <a onclick="return deleted('option');" class="btn btn-xs btn-danger" title="Remove this option" href="manage_bookings.php?id=<?php echo $row['ic_id']; ?>&table=interp_cat&del"><i class="glyphicon glyphicon-trash"></i></a>
                    <?php }else{ ?>
                      <a onclick="return activate('option');" class="btn btn-xs btn-success" title="Activate this option" href="manage_bookings.php?id=<?php echo $row['ic_id']; ?>&table=interp_cat&act"><i class="glyphicon glyphicon-refresh"></i></a>
                    <?php } ?>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div id="tab_tp" class="tab-pane fade <?php echo $telep_cls; ?>"><br>
        <table class="table table-bordered">
          <thead class="bg-primary">
            <th>S.No</th>
            <th>Title</th>
            <th>Dated</th>
            <th>Action</th>
          </thead>
          <tbody>
            <?php $counter=1;
            $q_data=$acttObj->read_all("*","telep_cat","");
            while($row=$q_data->fetch_assoc()){ ?>
              <tr <?php if(isset($_GET['edit']) && $_GET['id']==$row['tpc_id']){ ?> style='background-color:#1b631b47;' <?php } ?>>
                <td><?php echo $counter++; ?></td>
                <td><?php echo $row['tpc_title']; ?></td>
                <td><?php echo $row['tpc_date']; ?></td>
                <td width="20%">
                  <div class="col-sm-12 action_buttons">
                      <a class="btn btn-xs btn-warning" title="Edit Category Title" href="manage_bookings.php?id=<?php echo $row['tpc_id']; ?>&table=telep_cat&edit"><i class="glyphicon glyphicon-pencil"></i></a>
                    <a class="btn btn-xs btn-primary" title="View Sub Types"  href="javascript:void(0)" onClick="popupwindow('view_sub_types.php?id=<?php echo $row['tpc_id']; ?>&table=telep_types', 'title', 800, 600);"><i class="glyphicon glyphicon-eye-open"></i></a>
                    <?php if($row['tpc_status']=='1'){ ?>
                      <a onclick="return deleted('option');" class="btn btn-xs btn-danger" title="Remove this option" href="manage_bookings.php?id=<?php echo $row['tpc_id']; ?>&table=telep_cat&del"><i class="glyphicon glyphicon-trash"></i></a>
                    <?php }else{ ?>
                      <a onclick="return activate('option');" class="btn btn-xs btn-success" title="Activate this option" href="manage_bookings.php?id=<?php echo $row['tpc_id']; ?>&table=telep_cat&act"><i class="glyphicon glyphicon-refresh"></i></a>
                    <?php } ?>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div id="tab_tr" class="tab-pane fade <?php echo $trans_cls; ?>"><br>
        <table class="table table-bordered">
          <thead class="bg-primary">
            <th>S.No</th>
            <th>Title</th>
            <th>Dated</th>
            <th>Action</th>
          </thead>
          <tbody>
            <?php $counter=1;
            $q_data=$acttObj->read_all("*","trans_cat","");
            while($row=$q_data->fetch_assoc()){ ?>
              <tr <?php if(isset($_GET['edit']) && $_GET['id']==$row['tc_id']){ ?> style='background-color:#1b631b47;' <?php } ?>>
                <td><?php echo $counter++; ?></td>
                <td><?php echo $row['tc_title']; ?></td>
                <td><?php echo $row['tc_date']; ?></td>
                <td width="20%">
                  <div class="col-sm-12 action_buttons">
                      <a class="btn btn-xs btn-warning" title="Edit Category Title" href="manage_bookings.php?id=<?php echo $row['tc_id']; ?>&table=trans_cat&edit"><i class="glyphicon glyphicon-pencil"></i></a>
                    <a class="btn btn-xs btn-primary" title="View Sub Types"  href="javascript:void(0)" onClick="popupwindow('view_sub_types.php?id=<?php echo $row['tc_id']; ?>&table=trans_types', 'title', 800, 600);"><i class="glyphicon glyphicon-eye-open"></i></a>
                    <?php if($row['tc_status']=='1'){ ?>
                      <a onclick="return deleted('option');" class="btn btn-xs btn-danger" title="Remove this option" href="manage_bookings.php?id=<?php echo $row['tc_id']; ?>&table=trans_cat&del"><i class="glyphicon glyphicon-trash"></i></a>
                    <?php }else{ ?>
                      <a onclick="return activate('option');" class="btn btn-xs btn-success" title="Activate this option" href="manage_bookings.php?id=<?php echo $row['tc_id']; ?>&table=trans_cat&act"><i class="glyphicon glyphicon-refresh"></i></a>
                    <?php } ?>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div><br><br><br>
  </section>
</body>
</html>