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
  <?php 
  if($_GET['table']=='interp_types'){
      $cat_table='interp_cat';
      $cat_title='ic_title';
      $cat_id='ic_id';
      $types_id='it_id';
      $types_title='it_title';
      $cat_show='Face To Face';
  }else if($_GET['table']=='telep_types'){
      $cat_table='telep_cat';
      $cat_title='tpc_title';
      $cat_id='tpc_id';
      $types_id='tpt_id';
      $types_title='tpt_title';
      $cat_show='Telephone';
  }else{
      $cat_table='trans_cat';
      $cat_title='tc_title';
      $cat_id='tc_id';
      $types_id='tt_id';
      $types_title='tt_title';
      $cat_show='Translation';
  }
  // trash records
  if(isset($_GET['del']) && isset($_GET['table'])){
    $del_id=$_GET['del'];
    $del_table=$_GET['table'];
    if($del_table=='interp_types'){
      $del_status='it_status';
      $id='it_id';
    }else if($del_table=='telep_types'){
      $del_status='tpt_status';
      $id='tpt_id';
    }else{
      $del_status='tt_status';
      $id='tt_id';
    }
    if($acttObj->update($del_table,array($del_status=>'0'),array($id=>$del_id))){echo '<script>alert("Record successfully trashed.")</script>';
  } else {echo '<script>alert("Failed to trash this record!")</script>';
}
}
// activate records
if(isset($_GET['act']) && isset($_GET['table'])){
  $act_id=$_GET['act'];
  $act_table=$_GET['table'];
  if($act_table=='interp_types'){
    $act_status='it_status';
    $id='it_id';
  }else if($act_table=='telep_types'){
    $act_status='tpt_status';
    $id='tpt_id';
  }else{
    $act_status='tt_status';
    $id='tt_id';
  }
  if($acttObj->update($act_table,array($act_status=>'1'),array($id=>$act_id))){echo '<script>alert("Record successfully activated.")</script>';
} else {echo '<script>alert("Failed to activate this record!")</script>';
}
}
?>
</head>
<body>
  <section class="container-fluid" style="overflow-x:auto">
    <center><h2 class="text-center"><span class="label label-primary"><?php echo $cat_show; ?> Sub Types For <?php echo $acttObj->read_specific($cat_title,$cat_table,"$cat_id=".$_GET['id'])[$cat_title]; ?></span></h2></center>
    <a class="btn btn-warning" href="manage_bookings.php"><i class="glyphicon glyphicon-arrow-left"></i> Go back</a>
    <a class="btn btn-info" href="view_sub_types.php?id=<?php echo $_GET['id']; ?>&table=<?php echo $_GET['table']; ?>&add"><i class="glyphicon glyphicon-plus"></i> Add new option</a><br>
      <?php if(isset($_GET['add']) && isset($_GET['id']) && isset($_GET['table'])){ ?>
        <form action="" method="post" id="form_add">
      <div class="row">
      <div class="form-group col-sm-8 col-sm-offset-2">
          <label>Field Title</label>
	    <input class="form-control" type="text" name="title" required/>
       </div>
	    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>"/>
	    <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>"/>
    <div class="form-group col-sm-8 col-sm-offset-2">
    <button type="submit" name="btn_add" class="btn btn-primary">Add &raquo;</button>
    <a href="javascript:void(0)" onclick="$('#form_add').css('display','none');" name="btn_cancel" class="btn btn-warning">Cancel</a>
  </div>
       </div>
</form>
<?php if(isset($_POST['btn_add'])){
        $post_id=$_POST['id'];
        $post_table=$_POST['table'];
        $post_title=mysqli_escape_string($con,$_POST['title']);
        $dated=date('Y-m-d');
        if($post_table=='interp_types'){
            $ic_id='ic_id';
            $add_title='it_title';
            $add_date='it_date';
        }else if($post_table=='telep_types'){
            $ic_id='tpc_id';
            $add_title='tpt_title';
            $add_date='tpt_date';
        }else{
            $ic_id='tc_id';
            $add_title='tt_title';
            $add_date='tt_date';
        }
        if($acttObj->insert($post_table,array($add_title=>$post_title,$add_date=>$dated,$ic_id=>$post_id))){?>
         <script>alert('New record successfully added.');
         $('#form_add').css('display','none');</script>   
        <?php }else{
         echo "<script>alert('Failed to add new record!');</script>";
        }
    } ?>
    <?php } ?><br>
    <?php if(isset($_GET['edit_id'])){ 
    $edit_data=$acttObj->read_specific("*",$_GET['table'],"$types_id=".$_GET['edit_id']);?>
        <form action="" method="post" id="form_edit">
            <div class="row">
      <div class="form-group col-sm-8 col-sm-offset-2">
          <label>Field Title</label>
	    <input class="form-control" type="text" name="title" value="<?php echo utf8_encode($edit_data[$types_title]); ?>"/>
       </div>
    <div class="form-group col-sm-8 col-sm-offset-2">
    <button type="submit" name="btn_edit" class="btn btn-primary">Update &raquo;</button>
    <a href="javascript:void(0)" onclick="$('#form_edit').css('display','none');" name="btn_cancel" class="btn btn-warning">Cancel</a>
  </div>
  </div>
</form>
<?php if(isset($_POST['btn_edit'])){
        $post_title=mysqli_escape_string($con,$_POST['title']); 
        if($acttObj->update($_GET['table'],array($types_title=>$post_title),array($types_id=>$_GET['edit_id']))){?>
         <script>alert('Record successfully updated.');
         $('#form_edit').css('display','none');</script>   
        <?php }else{
         echo "<script>alert('Failed to update this record!');</script>";}
    } ?>
    <?php } ?>
<?php if($_GET['table']=='interp_types'){ ?>
        <table class="table table-bordered">
          <thead class="bg-primary">
            <th>S.No</th>
            <th>Title</th>
            <th>Dated</th>
            <th>Action</th>
          </thead>
          <tbody>
            <?php $counter=1;
            $q_data=$acttObj->read_all("*","interp_types","ic_id=".$_GET['id']);
            while($row=$q_data->fetch_assoc()){ ?>
              <tr <?php if(isset($_GET['edit_id']) && $_GET['edit_id']==$row['it_id']){ ?> style='background-color:#1b631b47;' <?php } ?>>
                <td><?php echo $counter++; ?></td>
                <td><?php echo utf8_encode($row['it_title']); ?></td>
                <td><?php echo $row['it_date']; ?></td>
                <td width="20%">
                  <div class="col-sm-12 action_buttons">
                    <a class="btn btn-xs btn-warning" title="View Sub Types" href="view_sub_types.php?id=<?php echo $_GET['id']; ?>&table=interp_types&edit_id=<?php echo $row['it_id']; ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                    <?php if($row['it_status']=='1'){ ?>
                      <a onclick="return deleted('option');" class="btn btn-xs btn-danger" title="Remove this option" href="view_sub_types.php?id=<?php echo $_GET['id']; ?>&table=interp_types&del=<?php echo $row['it_id']; ?>"><i class="glyphicon glyphicon-trash"></i></a>
                    <?php }else{ ?>
                      <a onclick="return activate('option');" class="btn btn-xs btn-success" title="Activate this option" href="view_sub_types.php?id=<?php echo $_GET['id']; ?>&table=interp_types&act=<?php echo $row['it_id']; ?>"><i class="glyphicon glyphicon-refresh"></i></a>
                    <?php } ?>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <?php }else if($_GET['table']=='telep_types'){ ?>
        <table class="table table-bordered">
          <thead class="bg-primary">
            <th>S.No</th>
            <th>Title</th>
            <th>Dated</th>
            <th>Action</th>
          </thead>
          <tbody>
            <?php $counter=1;
            $q_data=$acttObj->read_all("*","telep_types","tpc_id=".$_GET['id']);
            while($row=$q_data->fetch_assoc()){ ?>
              <tr <?php if(isset($_GET['edit_id']) && $_GET['edit_id']==$row['tpt_id']){ ?> style='background-color:#1b631b47;' <?php } ?>>
                <td><?php echo $counter++; ?></td>
                <td><?php echo utf8_encode($row['tpt_title']); ?></td>
                <td><?php echo $row['tpt_date']; ?></td>
                <td width="20%">
                  <div class="col-sm-12 action_buttons">
                    <a class="btn btn-xs btn-warning" title="View Sub Types" href="view_sub_types.php?id=<?php echo $_GET['id']; ?>&table=telep_types&edit_id=<?php echo $row['tpt_id']; ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                    <?php if($row['tpt_status']=='1'){ ?>
                      <a onclick="return deleted('option');" class="btn btn-xs btn-danger" title="Remove this option" href="view_sub_types.php?id=<?php echo $_GET['id']; ?>&table=telep_types&del=<?php echo $row['tpt_id']; ?>"><i class="glyphicon glyphicon-trash"></i></a>
                    <?php }else{ ?>
                      <a onclick="return activate('option');" class="btn btn-xs btn-success" title="Activate this option" href="view_sub_types.php?id=<?php echo $_GET['id']; ?>&table=trans_types&act=<?php echo $row['tpt_id']; ?>"><i class="glyphicon glyphicon-refresh"></i></a>
                    <?php } ?>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <?php }else{ ?>
        <table class="table table-bordered">
          <thead class="bg-primary">
            <th>S.No</th>
            <th>Title</th>
            <th>Dated</th>
            <th>Action</th>
          </thead>
          <tbody>
            <?php $counter=1;
            $q_data=$acttObj->read_all("*","trans_types","tc_id=".$_GET['id']);
            while($row=$q_data->fetch_assoc()){ ?>
              <tr <?php if(isset($_GET['edit_id']) && $_GET['edit_id']==$row['tt_id']){ ?> style='background-color:#1b631b47;' <?php } ?>>
                <td><?php echo $counter++; ?></td>
                <td><?php echo utf8_encode($row['tt_title']); ?></td>
                <td><?php echo $row['tt_date']; ?></td>
                <td width="20%">
                  <div class="col-sm-12 action_buttons">
                    <a class="btn btn-xs btn-warning" title="View Sub Types" href="view_sub_types.php?id=<?php echo $_GET['id']; ?>&table=trans_types&edit_id=<?php echo $row['tt_id']; ?>"><i class="glyphicon glyphicon-pencil"></i></a>
                    <?php if($row['tt_status']=='1'){ ?>
                      <a onclick="return deleted('option');" class="btn btn-xs btn-danger" title="Remove this option" href="view_sub_types.php?id=<?php echo $_GET['id']; ?>&table=trans_types&del=<?php echo $row['tt_id']; ?>"><i class="glyphicon glyphicon-trash"></i></a>
                    <?php }else{ ?>
                      <a onclick="return activate('option');" class="btn btn-xs btn-success" title="Activate this option" href="view_sub_types.php?id=<?php echo $_GET['id']; ?>&table=trans_types&act=<?php echo $row['tt_id']; ?>"><i class="glyphicon glyphicon-refresh"></i></a>
                    <?php } ?>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <?php } ?>
        <br><br><br>
  </section>
</body>
</html>