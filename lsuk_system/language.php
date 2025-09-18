<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);

if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}

include 'actions.php';
$table = 'lang';
$array_types=array(1=>"Standard",2=>"Rare",3=>"BSL");

if (isset($_POST['btn_insert_language'])) {
  $check_existing = $obj->read_specific('id', $table, "lang='".$_POST['lang']."' AND language_type='".$_POST['language_type']."'")['id'];
  if (empty($check_existing)) {
    $parent_id = $_POST['parent_id'] ?:NULL;
    $done=$obj->insert($table, array("lang" => trim($_POST['lang']), "language_type" => $_POST['language_type'], "parent_id" => $parent_id, "dated" => date('Y-m-d')));
    if($done){
      $msg = "<div class='alert alert-success'>New language has been added.</div>";
    }else{
      $msg = "<div class='alert alert-danger'>Failed to add New language!</div>";
    }
  }else{
    $msg = "<div class='alert alert-danger'>Same language with language type already exists!</div>";
  }
}
if (isset($_POST['btn_update_language'])) {
  $ok=1;
  $parent_id = $_POST['parent_id'] ?:NULL;
  $check_existing = $obj->read_specific('id', $table, "lang='".$_POST['lang']."' AND language_type='".$_POST['language_type']."'")['id'];
  if (!empty($check_existing)) {
    if($check_existing != $_POST['id']){
      $ok=0;
      $msg = "<div class='alert alert-danger'>Same language with language type already exists!</div>";
    }
  }
  if($ok==1){
    $done=$obj->update($table, array("lang" => trim($_POST['lang']), "language_type" => $_POST['language_type'], "parent_id" => $parent_id, "dated" => date('Y-m-d')), "id=".$_POST['id']);
    if($done){
      $msg = "<div class='alert alert-success'>Language has been updated.</div>";
    }else{
      $msg = "<div class='alert alert-danger'>Failed to update this language!</div>";
    }
  }
}
if(isset($_GET['id'])){
  $get_lang = $obj->read_specific("*",$table,"id=".$_GET['id']);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update Language Form</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <style>
    .label {
      font-size: 100% !important;
    }
  </style>
  <script type="text/javascript">
    function MM_openBrWindow(theURL, winName, features) { //v2.0
      window.open(theURL, winName, features);
    }
  </script>
</head>

<body class="container-fluid">
  <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
    <?php if(isset($_GET['id'])){?>
      <input type="hidden" name="id" value="<?=$_GET['id']?>"/>  
    <?php } ?>
    <h3>Language Information</h3>
    <fieldset class="row">
      <div class="form-group col-sm-12">
        <?=!empty($msg)?$msg:''?>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label>Language * </label>
        <input name="lang" type="text" placeholder="Write language name" class="form-control" required='' id="lang" value="<?=$get_lang['lang']?>"/>
      </div>
      <div class="form-group col-md-2 col-sm-6">
        <label>Language Type * </label>
        <select name="language_type" class="form-control" required=''>
          <option <?=$get_lang['language_type']==1?'selected':''?> value="1">Standard</option>
          <option <?=$get_lang['language_type']==2?'selected':''?> value="2">Rare</option>
          <option <?=$get_lang['language_type']==3?'selected':''?> value="3">BSL</option>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Main Language ID <small class="text-danger">(If Dilect Language)</small></label>
        <input name="parent_id" type="text" placeholder="Write Parent Language ID" class="form-control" id="parent_id" value="<?=$get_lang['parent_id']?:NULL?>"/>
      </div>
      <div class="form-group col-md-3 col-sm-6"><br>
        <?php if(empty($get_lang['id'])){?>
          <button class="btn btn-primary" type="submit" name="btn_insert_language" onclick="return formSubmit(); return false">Insert Language &raquo;</button>
        <?php }else{?>
          <button class="btn btn-primary" type="submit" name="btn_update_language" onclick="return formSubmit(); return false">Update Language &raquo;</button>
          <a class="btn btn-warning" href="language.php">Cancel</a>
        <?php }?>
      </div>
    </fieldset>
    <fieldset class="row1">
      <h4>All Languages List</h4>
      <table class="table table-bordered">
        <thead class="bg-info">
          <th>Language ID</th>
          <th>Langauge</th>
          <th>Type</th>
          <th>Dated</th>
          <th>Action</th>
        </thead>
        <?php $result = $obj->read_all("*",$table,"1 ORDER BY lang ASC");
        $counter=1;
        while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td align="left" class='label_parent_language' data-id='<?=$row['id']?>' data-language='<?=$row['lang']?>'><b><?php echo $row['id']; ?></b></td>
            <td align="left"><?php echo $row['lang'] . (empty($row['parent_id']) ? "<span class='label label-danger pull-right'>Main Language</span>" : "<span class='label label-primary pull-right'>Dilect of <span class='label_dilect' data-dilect-id='" . $row['parent_id'] . "'></span></span>"); ?> </td>
            <td align="left"><?php echo $array_types[$row['language_type']]; ?> </td>
            <td align="left"><?php echo $misc->dated($row['dated']); ?> </td>
            <td align="left">
              <?php if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Finance') { ?>
                <a href="?id=<?=$row['id']?>" title="Edit this language" class="btn btn-sm btn-info"><i class="glyphicon glyphicon-edit"></i></a>
                <a href="#" title="Trash this language" class="btn btn-sm btn-danger hidden" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><i class="glyphicon glyphicon-remove"></i></a>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>
      </table>
    </fieldset>
  </form>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css" />
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
  $('.label_dilect').each(function() {
    var dilect_id = $(this).attr('data-dilect-id');
    if (dilect_id) {
      var parent_language = $('.label_parent_language[data-id="' + dilect_id +'"]').attr('data-language');
      $(this).html(parent_language);
    }
  });
  $('.table').DataTable({
    "bSort": true,
    "order": []
  });
  $('#lang').keyup(function() {
    var inputVal = $(this).val();
    inputVal = inputVal.replace(/[^a-zA-Z\s]/g, '');
    $(this).val(inputVal);
  });
});
</script>
</html>