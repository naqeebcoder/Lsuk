<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);

if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}

include 'actions.php';
$table = 'comp_type';

if (isset($_POST['btn_insert_comp_type'])) {
  $check_existing = $obj->read_specific('id', $table, "title='".$_POST['title']."' AND company_type_id='".$_POST['company_type_id']."'")['id'];
  if (empty($check_existing)) {
    $done=$obj->insert($table, array("title" => trim($_POST['title']), "company_type_id" => $_POST['company_type_id'], "dated" => date('Y-m-d')));
    if($done){
      $msg = "<div class='alert alert-success'>New company type has been added.</div>";
    }else{
      $msg = "<div class='alert alert-danger'>Failed to add New company type!</div>";
    }
  }else{
    $msg = "<div class='alert alert-danger'>Same company type already exists!</div>";
  }
}
if (isset($_POST['btn_update_comp_type'])) {
  $ok=1;
  $check_existing = $obj->read_specific('id', $table, "title='".$_POST['title']."' AND company_type_id='".$_POST['company_type_id']."'")['id'];
  if (!empty($check_existing)) {
    if($check_existing != $_POST['id']){
      $ok=0;
      $msg = "<div class='alert alert-danger'>Same company type already exists!</div>";
    }
  }
  if($ok==1){
    $done=$obj->update($table, array("title" => trim($_POST['title']), "company_type_id" => $_POST['company_type_id'], "dated" => date('Y-m-d')), "id=".$_POST['id']);
    if($done){
      $msg = "<div class='alert alert-success'>Company type has been updated.</div>";
    }else{
      $msg = "<div class='alert alert-danger'>Failed to update this company type!</div>";
    }
  }
}
if(isset($_GET['id'])){
  $get_comp_type = $obj->read_specific("*",$table,"id=".$_GET['id']);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update Company Type Form</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />

  <script type="text/javascript">
    function MM_openBrWindow(theURL, winName, features) { //v2.0
      window.open(theURL, winName, features);
    }
  </script>
</head>

<body class="container">
  <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
    <?php if(isset($_GET['id'])){?>
      <input type="hidden" name="id" value="<?=$_GET['id']?>"/>  
    <?php } ?>
    <h3>Company Type Information</h3>
    <fieldset class="row">
      <div class="form-group col-sm-12">
        <?=!empty($msg)?$msg:''?>
      </div>
      <div class="form-group col-sm-6">
        <label>Company Type * </label>
        <input name="title" type="text" placeholder="Write title here" class="form-control" required='' id="title" value="<?=$get_comp_type['title']?>"/>
      </div>
      <div class="form-group col-sm-6">
        <label>Group Name * </label>
        <select name="company_type_id" class="form-control" required=''>
        <?php $get_templates = $obj->read_all("*","company_types","1 ORDER BY title ASC");
          while ($row_template = $get_templates->fetch_assoc()) { ?>
            <option <?=$get_comp_type['company_type_id']==$row_template['id']?'selected':''?> value="<?=$row_template['id']?>"><?=$row_template['title']?></option>
          <?php } ?>
          </select>
      </div>
      <div class="form-group col-sm-6">
        <?php if(empty($get_comp_type['id'])){?>
          <button class="btn btn-primary" type="submit" name="btn_insert_comp_type" onclick="return formSubmit(); return false">Insert Company Type &raquo;</button>
        <?php }else{?>
          <button class="btn btn-primary" type="submit" name="btn_update_comp_type" onclick="return formSubmit(); return false">Update Company Type &raquo;</button>
          <a class="btn btn-warning" href="comp_type.php">Cancel</a>
        <?php }?>
      </div>
    </fieldset>
    <fieldset class="row1">
      <h4>All Company Type List</h4>
      <table class="table table-bordered">
        <thead class="bg-info">
          <th>S.No</th>
          <th>Company Type</th>
          <th>Group Name</th>
          <th>Dated</th>
          <th>Action</th>
        </thead>
        <?php $result = $obj->read_all("$table.*,company_types.title as group_name","$table,company_types","$table.company_type_id=company_types.id ORDER BY $table.title ASC");
        $counter=1;
        while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td align="left"><?php echo $counter++; ?> </td>
            <td align="left"><?php echo $row['title']; ?> </td>
            <td align="left"><?php echo $row['group_name']; ?> </td>
            <td align="left"><?php echo $misc->dated($row['dated']); ?> </td>
            <td align="left">
              <?php if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Finance') { ?>
                <a href="?id=<?=$row['id']?>" title="Edit this company type" class="btn btn-sm btn-info"><i class="glyphicon glyphicon-edit"></i></a>
                <a href="#" title="Trash this company type" class="btn btn-sm btn-danger" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><i class="glyphicon glyphicon-remove"></i></a>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>
      </table>
    </fieldset>
  </form>
</body>

</html>