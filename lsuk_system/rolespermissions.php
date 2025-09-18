<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include 'actions.php';
$role_id = -1;
$rolenamed = "NONE";
// Function to recursively generate collapsible panels
function generateCollapsiblePanel($data, $role_id, $rolenamed, $active_tab="", $action_type = "added")
{
  if ($action_type == "added") {
    $action_value = 1;
    $action_icon = "remove";
    $action_class = "danger";
    $action_param = "del_id";
  } else {
    $action_value = 2;
    $action_icon = "plus";
    $action_class = "success";
    $action_param = "add_id";
  }
  foreach ($data as $key => $value) {
    if (is_numeric($key)) {
      echo '<li class="list-group-item" style="margin: 4px 0px;">';
      echo !empty($value['label']) ? $value['label'] : $value['name'];
      echo !empty($value['info']) ? " <small class='text-muted'>(" . $value['info'] . ")</small>" : "";
      echo '<a class="btn btn-action btn-xs btn-'.$action_class.' pull-right" onclick="return action('.$action_value.');" href="rolespermissions.php?'.$action_param.'=' . $value['id'] . '&role_id=' . $role_id . '&route_heading=' . strtolower(str_replace(" ", "", $value['route_heading'])) . '" title="Update route for ' . $rolenamed . '">
              <i class="glyphicon glyphicon-'.$action_icon.'"></i>
          </a></li>';
    } else {

      echo '<div class="panel panel-default">';
      echo '<div class="panel-heading">';
      echo '<h4 class="panel-title">';
      echo '<a data-toggle="collapse" href="#' . strtolower(str_replace(" ", "", $key))."_".$action_type . '">' . $key . '</a>';
      echo '</h4>';
      echo '</div>';

      echo '<div id="' . strtolower(str_replace(" ", "", $key))."_".$action_type . '" class="panel-collapse collapse '.(strtolower(str_replace(" ", "", $key)) == $active_tab ? 'in' : '').'">';
      echo '<div class="panel-body">';

      if (is_array($value)) {

        // Nested collapsible panel
        if (isAssoc($value)) {
          generateCollapsiblePanel($value, $role_id, $rolenamed, $active_tab, $action_type);
        } else {
          // List group for direct indexes
          echo '<ul class="list-group">';
          foreach ($value as $item) {
            echo '<li class="list-group-item" style="margin: 4px 0px;">';
            echo !empty($item['label']) ? $item['label'] : $item['name'];
            echo !empty($item['info']) ? " <small class='text-muted'>(" . $item['info'] . ")</small>" : "";
            echo '<a class="btn btn-action btn-xs btn-'.$action_class.' pull-right" onclick="return action('.$action_value.');" href="rolespermissions.php?'.$action_param.'=' . $item['id'] . '&role_id=' . $role_id . '&route_heading=' . strtolower(str_replace(" ", "", $item['route_heading'])) . '" title="Update route for ' . $rolenamed . '">
                            <i class="glyphicon glyphicon-'.$action_icon.'"></i>
                        </a>';
            echo '</li>';
            // }
          }
          echo '</ul>';
        }
      }

      echo '</div>'; // Close panel-body
      echo '</div>'; // Close panel-collapse
      echo '</div>'; // Close panel
    }
  }
}

// Function to check if an array is associative or not
function isAssoc($arr)
{
  return array_keys($arr) !== range(0, count($arr) - 1);
}
//Getting role name for role id
if (isset($_GET['role_id'])) {
  $role_id = @$_GET['role_id'];
  $sql_named = $obj->read_specific("named", "rolenamed", "id=$role_id");
  $rolenamed = $sql_named["named"];
}
//Adding new role
if (isset($_POST['new_role'])) {
  $role_name = trim($_POST['role_name']);
  $chk = $obj->read_specific("count(named) as counter", "rolenamed", "named='" . $role_name . "'")['counter'];
  if ($chk == 0) {
    $is_role_added = $obj->insert("rolenamed",  array('named' => $role_name, 'dated' => date('Y-m-d')));
    $new_role_id = $obj->con->insert_id;
    if ($is_role_added) {
      //Add default routes [Home Screens, Booking Forms, Booking List] to newly added role
      $obj->insert("route_permissions", array('role_id' => $new_role_id, 'perm_id' => 1, 'dated' => date('Y-m-d')));
      $obj->insert("route_permissions", array('role_id' => $new_role_id, 'perm_id' => 2, 'dated' => date('Y-m-d')));
      $obj->insert("route_permissions", array('role_id' => $new_role_id, 'perm_id' => 3, 'dated' => date('Y-m-d')));
      $obj->insert("route_permissions", array('role_id' => $new_role_id, 'perm_id' => 4, 'dated' => date('Y-m-d')));
      $obj->insert("route_permissions", array('role_id' => $new_role_id, 'perm_id' => 5, 'dated' => date('Y-m-d')));
      $obj->insert("route_permissions", array('role_id' => $new_role_id, 'perm_id' => 6, 'dated' => date('Y-m-d')));
      $obj->insert("route_permissions", array('role_id' => $new_role_id, 'perm_id' => 7, 'dated' => date('Y-m-d')));
      $obj->insert("route_permissions", array('role_id' => $new_role_id, 'perm_id' => 135, 'dated' => date('Y-m-d')));
      echo "<script>alert('New Role " . $role_name . " sucessfully added.');
      window.location.href = '" . basename(__FILE__) . "?role_id=$new_role_id';</script>";
    } else {
      echo "<script>alert('Failed to add new Role! try again');</script>";
    }
  }
}
//Adding existing non-assigned route for a role
if (isset($_GET['add_id'])) {
  $perm_id = @$_GET['add_id'];
  $role_id = @$_GET['role_id'];
  $route_heading = @$_GET['route_heading'];
  $obj->insert("route_permissions", array('role_id' => $role_id, 'perm_id' => $perm_id, 'dated' => date('Y-m-d'))); ?>
  <script>
    window.location.href = "<?php echo basename(__FILE__); ?>" + '?role_id=<?= $role_id ?>&route_heading=<?= $route_heading ?>';
  </script>
<?php }
//Deleting existing assigned route for a role
if (isset($_GET['del_id'])) {
  $del_id = @$_GET['del_id'];
  $route_heading = @$_GET['route_heading'];
  $obj->delete("route_permissions","id=$del_id"); ?>
  <script>
    window.location.href = "<?php echo basename(__FILE__); ?>" + '?role_id=<?= $role_id ?>&route_heading=<?= $route_heading ?>';
  </script>
<?php } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Roles & Permission Management</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <script type="text/javascript">
    function refreshParent() {
      window.opener.location.reload();
    }

    function popupwindow(url, title, w, h) {
      var left = (screen.width / 2) - (w / 2);
      var top = (screen.height / 2) - (h / 2);
      return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
    }

    function action(action = 1) {
      return true;
      // var msg = action == 1 ? "Revoke this route" : "Add this route";
      // var result = confirm("Are you sure to " + msg + "?");
      // if (result == true) {
      //   return true;
      // } else {
      //   return false;
      // }
    }

    function myFunction() {
      var x = $('#sysroles').val();
      window.location.href = "<?php echo basename(__FILE__); ?>" + '?role_id=' + x;
    }

    function validate_new_role() {
      var role_name = $("#role_name").val();
      if (!role_name) {
        $("#role_name").focus();
        return false;
      } else {
        if (confirm("Are you sure to add " + role_name + " Role?")) {
          return true;
        } else {
          return false;
        }
      }
    }

    function OnRoleChanged(ev, elem) {
      var roleind = elem.selectedIndex;
      var opt = elem.options[roleind];
      role_id = opt.value;
      myFunction();
    }
  </script>
  <style>
    .list-group-item {
      padding: 7px 15px;
    }

    .list-group-item .btn-sm {
      margin: -5px 0px;
    }

    .btn-action {
      padding: 3px 4px 0px 4px;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <form action="" method="post" class="register" id="signup_form" name="signup_form">
      <div class="bg-info col-xs-12 form-group">
        <h4>Roles Permissions</h4>
      </div>
      <div class="form-group col-md-4 col-sm-6">
        <label class="optional">Add New Role</label>
        <div class="input-group">
          <input id="role_name" required class="form-control" name="role_name" type="text" placeholder="Add New Role">
          <div class="input-group-btn">
            <button type="submit" name="new_role" onclick="return validate_new_role();" class="btn btn-primary">Add Role</button>
          </div>
        </div>
      </div>
    </form>
    <div class="bg-info col-xs-12 form-group">
      <h4>Roles Access Management</h4>
    </div>
    <form action="" method="post" class="register" id="signup_form" name="signup_form">
      <div class="form-group col-md-3 col-sm-6">
        <label>Check Access For Role</label>
        <select onchange="OnRoleChanged(event,this);" class="form-control" name="sysroles" id="sysroles" required>
          <option value="" <?=!$_GET['role_id']?'selected':'disabled'?>>--- Select a Role ---</option>
          <?php $get_roles = $obj->read_all("*", "rolenamed", "is_root=0");
          while ($row_role = $get_roles->fetch_assoc()) {
            $selected_role = $_GET['role_id'] == $row_role['id'] ? "selected" : "";
            echo "<option " . $selected_role . " value='" . $row_role['id'] . "'>" . $row_role['named'] . "</option>";
          }?>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <br><a href="<?=basename(__FILE__)?>" class="btn btn-primary">View Default Page</a>
      </div>
    </form>
    <div class="bg-success text-center col-xs-12 form-group">
      <h4>Permissions details for: <span class="text-danger"><?php echo $rolenamed; ?><span></h4>
    </div>
    <?php if (!empty($rolenamed) && $rolenamed != 'NONE') {
      $get_routes = $obj->read_all("routes.*,route_permissions.id as primary_id", "routes,route_permissions", "routes.id=route_permissions.perm_id AND route_deleted=0 AND visibility=1 AND route_permissions.role_id=$role_id ORDER BY FIELD(routes.route_heading,'HOME','HR','FINANCE','REPORTS','BOOKING','PAYROLL','MANAGEMENT',NULL)");
      if ($get_routes->num_rows > 0) {
        $get_route_headings = array();
        $get_route_parents = array();
        $get_route_childs = array();
        while ($row_route = $get_routes->fetch_assoc()) {
          $row_route['route_heading'] = empty($row_route['route_heading']) ? "EXTRAS" : $row_route['route_heading'];
          $array_to_use = array("id" => $row_route['primary_id'], "name" => $row_route['name'], "label" => $row_route['label'], "info" => $row_route['info'], "route_heading" => $row_route['route_heading']);
          if (empty($row_route['route_parent']) && empty($row_route['route_child'])) {
            $get_route_headings[$row_route['route_heading']][] = $array_to_use;
          } else {
            if (!empty($row_route['route_parent']) && empty($row_route['route_child'])) {
              $get_route_headings[$row_route['route_heading']][$row_route['route_parent']][] = $array_to_use;
            } else {
              $get_route_headings[$row_route['route_heading']][$row_route['route_parent']][$row_route['route_child']][] = $array_to_use;
            }
          }
        }
      }
      $get_routes_excluded = $obj->read_all("routes.*", "routes", "route_deleted=0 AND visibility=1 AND routes.id NOT IN (SELECT route_permissions.perm_id from route_permissions WHERE route_permissions.role_id=$role_id) ORDER BY FIELD(routes.route_heading,'HOME','HR','FINANCE','REPORTS','BOOKING','PAYROLL','MANAGEMENT',NULL)");
      if ($get_routes_excluded->num_rows > 0) {
        $get_route_headings_ex = array();
        while($row_route_ex = $get_routes_excluded->fetch_assoc()) {
          $row_route_ex['route_heading'] = empty($row_route_ex['route_heading']) ? "EXTRAS" : $row_route_ex['route_heading'];
          if (empty($row_route_ex['route_parent']) && empty($row_route_ex['route_child'])) {
            $get_route_headings_ex[$row_route_ex['route_heading']][] = $row_route_ex;
          } else {
            if (!empty($row_route_ex['route_parent']) && empty($row_route_ex['route_child'])) {
              $get_route_headings_ex[$row_route_ex['route_heading']][$row_route_ex['route_parent']][] = $row_route_ex;
            } else {
              $get_route_headings_ex[$row_route_ex['route_heading']][$row_route_ex['route_parent']][$row_route_ex['route_child']][] = $row_route_ex;
            }
          }          
        }
      }
      $query = $obj->read_all("routes.id,routes.name,routes.label,routes.route_heading", "routes", "route_deleted=0 AND visibility=1 AND routes.route_heading='$heading' AND routes.id NOT IN (SELECT route_permissions.perm_id from route_permissions WHERE route_permissions.role_id=$role_id) ORDER BY routes.label ASC");
    ?>
      <div class="col-sm-6">
        <div class="bg-info col-xs-12 form-group">
          <h4>Permissions currently set for: <span class="text-danger"><?php echo $rolenamed; ?><span></h4>
        </div><br><br><br>
        <?php // Start generating collapsible panels
        echo '<div class="panel-group">';
        generateCollapsiblePanel($get_route_headings, $role_id, $rolenamed, $_GET['route_heading']);
        echo '</div>';
        ?>
      </div>

      <div class="col-sm-6">
        <div class="bg-danger col-xs-12 form-group">
          <h4>Excluded permissions for: <span class="text-danger"><?php echo $rolenamed; ?><span></h4>
        </div><br><br><br>
        <?php // Start generating collapsible panels
        echo '<div class="panel-group">';
        generateCollapsiblePanel($get_route_headings_ex, $role_id, $rolenamed, $_GET['route_heading'], 'excluded');
        echo '</div>';
        ?>
      </div>
    <?php } ?>
  </div>
  <?php if (isset($_POST['submit']) && $bSubmitOk == true) {
    echo "<script>alert('Successful!');</script>"; ?>
    <script>
      window.onunload = refreshParent;
    </script>
  <?php } ?>
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      var route_heading = "<?= $_GET['route_heading'] ?>";
      if (route_heading) {
        $('.panel-collapse').removeClass('in');
        $('#' + route_heading + '_added').addClass('in');
        $('#' + route_heading + '_excluded').addClass('in');
        var targetDiv = $('#' + route_heading + '_excluded');
        if (targetDiv) {
          var position = targetDiv.offset().top - 40;
          $("html, body").animate({
            scrollTop: position
          }, "slow");
        }
      }
    });
  </script>
</body>

</html>