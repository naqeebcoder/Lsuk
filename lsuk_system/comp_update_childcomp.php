<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$allowed_type_idz = "65";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Update Subsidiary Companies</u> action!<br>Kindly contact admin for further process.</h2></center>");
  }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Update Child Companies</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    .multiselect {
      min-width: 250px;
    }

    .multiselect {
      min-width: 250px;
    }

    .multiselect-container {
      max-height: 400px;
      overflow-y: auto;
      max-width: 380px;
    }
  </style>
</head>

<body>
  <?php
  $table = 'child_companies';
  $parent_id = $_GET['parent_id'];
  $parent_name = $acttObj->read_specific('name', 'comp_reg', 'id=' . $parent_id);
  ?>
  <div class="container text-center">
    <h1>Record for : <span class="label label-primary"><?php echo $parent_name['name']; ?></span></h1><br />
    <form action="" method="post" class="col-md-6">
      <p>Add New child Company</p>
      <select class="form-group form-control col-md-6 child_class" name="child_comp" required multiple="multiple">
        <?php $result_opt = $acttObj->read_all("distinct comp_reg.id,comp_reg.name,comp_reg.abrv", "comp_reg", "comp_reg.id NOT IN (SELECT $table.child_comp FROM $table where $table.parent_comp='$parent_id') ORDER BY comp_reg.name ASC");
        while ($row_opt = mysqli_fetch_assoc($result_opt)) { ?>
          <option value="<?php echo $row_opt['id']; ?>"><?php echo $row_opt['name']; ?></option>
        <?php } ?>
      </select> &nbsp;&nbsp;&nbsp;<span onclick="upd_childs()" class="btn btn-primary"><i class="fa fa-check-circle"></i> Submit</span>
    </form>
    <br><br>
    <h4>Child companies list for <span class="label label-primary"><?php echo $parent_name['name']; ?></span> </h4>

    <table class="table table-bordered table-hover">
      <tbody id="append_childs">
        <?php $result = $acttObj->read_all("$table.id,comp_reg.name", "$table,comp_reg", "$table.child_comp=comp_reg.id and $table.parent_comp='$parent_id' ORDER BY comp_reg.name ASC");
        if (mysqli_num_rows($result) == 0) {
          echo '<tr><td align="centre"><h3 class="text-center"><span class="label label-danger">No child companies exists!</span></h3></td></tr>';
        } else {
          while ($row = mysqli_fetch_array($result)) {
        ?>
            <tr>
              <td align="left"><?php echo $row['name']; ?> </td>
              <td align="left">
                <a href="javascript:void(0)" id="<?php echo $row['id']; ?>" onclick="remove_child(this)">
                  <img src="images/icn_trash.png" title="Trash" height="14" width="16" />
                </a>
              </td>
            </tr>
        <?php
          }
        } ?>
      </tbody>
    </table>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
  <script>
    $(function() {
      $('.child_class').multiselect({
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true
      });
    });

    function upd_childs() {
      var parent_id = '<?php echo $parent_id; ?>';
      var child_id = $('.child_class').val();
      $.ajax({
        url: 'ajax_add_comp_data.php',
        method: 'post',
        data: {
          parent_id: parent_id,
          child_id: child_id
        },
        success: function(data) {
          $('#append_childs').html(data);
        },
        error: function(xhr) {
          alert("An error occured: " + xhr.status + " " + xhr.statusText);
        }
      });
    }

    function remove_child(elem) {
      var remove_child_id = elem.id;
      $.ajax({
        url: 'ajax_add_comp_data.php',
        method: 'post',
        data: {
          remove_child_id: remove_child_id
        },
        success: function(data) {
          $('#append_childs').html(data);
        },
        error: function(xhr) {
          alert("An error occured: " + xhr.status + " " + xhr.statusText);
        }
      });
    }
  </script>
</body>

</html>