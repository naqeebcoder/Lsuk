<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$allowed_type_idz = "51";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Blacklist Interpreter</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
if ($_SESSION['prv'] == 'Management') {
  $managment = 1;
} else {
  $managment = 0;
}
$table = 'interp_blacklist';
$code_qs = $_GET['code_qs'];
$name = $_GET['name'];
$interpreter_id = $_GET['edit_id'];
if (isset($_GET['activate_id']) && !empty($_GET['activate_id'])) {
  $id = mysqli_escape_string($con, $_GET['activate_id']);
  $by = $_SESSION['UserName'];
  $dated = date('Y-m-d');
  $query_activate = "update interp_blacklist set deleted_flag='0',edited_by='$by',edited_date='$dated' where id=" . $id;
  if (mysqli_query($con, $query_activate)) {
    echo '<script>alert("Removed from blacklisted successfully !");</script>';
  }
}
if (isset($_GET['del_id']) && !empty($_GET['del_id'])) {
  $id = mysqli_escape_string($con, $_GET['del_id']);
  $reason = mysqli_escape_string($con, $_GET['edited_reason']);
  $by = $_SESSION['UserName'];
  $dated = date('Y-m-d H:i:s');

  $query_del = "UPDATE interp_blacklist 
                SET deleted_flag='1', 
                    edited_by='$by', 
                    edited_date='$dated', 
                    deleted_by='$by', 
                    deleted_date='$dated',
                    edited_reason='$reason' 
                WHERE id=$id";
$redirectUrl = $_GET['redirect_url'];
// Remove activate_id from URL
$redirectUrl = preg_replace('/(&|\?)activate_id=\d+/', '', $redirectUrl);

// If first param was removed, replace first & with ?
$redirectUrl = preg_replace('/\?&/', '?', $redirectUrl);

  if (mysqli_query($con, $query_del)) {
    echo '<script>
        alert("Record has been trashed successfully!");
        setTimeout(function() {
            window.location.href = "' . $redirectUrl . '";
        }, 500);
    </script>';
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Blacklist an Interpreter</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <?php include 'ajax_uniq_fun.php'; ?>
  <script>
    function refreshParent() {
      window.opener.location.reload();
    }

    function popupwindow(url, title, w, h) {
      var left = (screen.width / 2) - (w / 2);
      var top = (screen.height / 2) - (h / 2);
      return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
    }
  </script>
</head>

<body>
  <div class="container-fluid">
<form action="" method="post">
  <div class="bg-info col-xs-12 form-group">
    <h4>Interpreter Blacklist Form for <span style="color:#F00;"> <?php echo $name; ?></span></h4>
  </div>

  <!-- Block By Company -->
  <div class="form-group col-md-3 col-sm-6">
    <label>Block By Company *</label>
    <select id="block_by_id" class="form-control" name="block_by_id" required>
      <option value="">Select Company</option>
      <?php
      $sql_opt = "SELECT id, name FROM comp_reg WHERE status NOT IN ('Company Seized trading in this name or Company closed', 'Company Blacklisted') ORDER BY name ASC";
      $result_opt = mysqli_query($con, $sql_opt);
      while ($row = mysqli_fetch_assoc($result_opt)) {
        echo "<option value='{$row['id']}'>{$row['name']}</option>";
      }
      ?>
    </select>
  </div>

  <!-- Block By Type -->
  <div class="form-group col-md-3 col-sm-6">
    <label>Block Type *</label>
    <select name="block_by_type" id="block_by_type" class="form-control" required>
      <option value="parent">All child</option>
      <option value="child">self</option>
    </select>
  </div>

  <!-- Block Scope -->
  <div class="form-group col-md-3 col-sm-6">
    <label>Block Scope *</label>
    <select name="block_scope" id="block_scope" class="form-control" required>
      <option value="all">All Jobs</option>
      <option value="specific_user">Specific User</option>
      <option value="specific_reference">Specific Reference</option>
    </select>
  </div>

  <!-- Optional: Service User ID -->
  <div class="form-group col-md-3 col-sm-6 block-conditional block-user" style="display:none;">
    <label>Service User Names(comma separated if multiple)</label>
    <input type="text" name="service_user_id" class="form-control" placeholder="user1,user2,user3....">
  </div>

  <!-- Optional: Reference ID -->
  <div class="form-group col-md-3 col-sm-6 block-conditional block-ref" style="display:none;">
    <label>Reference</label>
    <input type="text" name="reference_id" class="form-control" placeholder="Enter  Ref">
  </div>

  <!-- Feedback Method -->
  <div class="form-group col-md-3 col-sm-6">
    <label>How we get Feedback</label>
    <select name="get_feedback" class="form-control">
      <option value="">Select Feedback Method</option>
      <option>Email</option>
      <option>Timesheet</option>
      <option>Phone</option>
      <option>Others</option>
    </select>
  </div>

  <!-- Blocked For -->
  <div class="form-group col-md-3 col-sm-6">
    <label>Block For</label>
    <div class="d-flex gap-4 align-items-center">
      <label class="d-flex align-items-center gap-1 m-0">
        <input type="checkbox" name="blocked_for[]" value="1" checked> Face to Face
      </label>
      <label class="d-flex align-items-center gap-1 m-0">
        <input type="checkbox" name="blocked_for[]" value="3" checked> Remote
      </label>
      <label class="d-flex align-items-center gap-1 m-0">
        <input type="checkbox" name="blocked_for[]" value="2" checked> Translation
      </label>
    </div>
  </div>





  <div class="form-group  col-sm-12">
    <textarea name="block_reason" class="form-control" placeholder="Block reason" required></textarea>
  </div>

  <!-- Submit -->
  <div class="form-group col-md-3 col-sm-6">
    <br>
    <button class="btn btn-primary" type="submit" name="submit">Submit &raquo;</button>
  </div>
</form>
<script>
  $('#block_scope').on('change', function() {
  $('.block-conditional').hide();
  if (this.value === 'specific_user') {
    $('.block-user').show();
  } else if (this.value === 'specific_reference') {
    $('.block-ref').show();
  }
});

</script>
    <?php
if (isset($_POST['submit']) && !empty($_POST['block_by_id'])) {
  $edit_id = $acttObj->get_id($table);

  // Required fields
  $block_by_id = $_POST['block_by_id'];
  $block_by_type = $_POST['block_by_type'];
  $block_scope = $_POST['block_scope'];
  $get_feedback = $_POST['get_feedback'];
  $blocked_for = $_POST['blocked_for'];

  // Optional fields
  $service_user_id = !empty($_POST['service_user_id']) ? $_POST['service_user_id'] : null;
  $reference_id = !empty($_POST['reference_id']) ? $_POST['reference_id'] : null;

  // Save new fields
  $acttObj->editFun($table, $edit_id, 'block_by_id', $block_by_id);
  $acttObj->editFun($table, $edit_id, 'block_by_type', $block_by_type);
  $acttObj->editFun($table, $edit_id, 'block_scope', $block_scope);
  $acttObj->editFun($table, $edit_id, 'service_user_id', $service_user_id);
  $acttObj->editFun($table, $edit_id, 'reference_id', $reference_id);

  // Existing fields
  $acttObj->editFun($table, $edit_id, 'orgName', $block_by_id); // You can skip this if orgName is deprecated
  $acttObj->editFun($table, $edit_id, 'get_feedback', $get_feedback);
  $blocked_for = isset($_POST['blocked_for']) ? implode(',', $_POST['blocked_for']) : '';
  $acttObj->editFun($table, $edit_id, 'blocked_for', $blocked_for);
  $acttObj->editFun($table, $edit_id, 'submittedBy', $_SESSION['UserName']);
  $acttObj->editFun($table, $edit_id, 'interpName', $code_qs);
  $acttObj->editFun($table, $edit_id, 'blocked_by', $_SESSION['UserName']); // New field for audit
  $acttObj->editFun($table, $edit_id, 'block_reason', $_POST['block_reason']);
  $acttObj->editFun($table, $edit_id, 'dated', date('Y-m-d H:i:s'));

  // Log
  $acttObj->insert("daily_logs", array(
    "action_id" => 28,
    "user_id" => $_SESSION['userId'],
    "details" => "Interpreter ID: " . str_replace("id-", "", $code_qs)
  ));

  echo "<script>alert('Interpreter has been blacklisted successfully!');</script>";

  if ($managment == 0) { ?>
    <script>
      window.onunload = refreshParent;
    </script>
  <?php }
}
?>

<table class="table table-bordered">
  <tr class="bg-info">
    <th>ID</th>
    <th>Company</th>
    <th>Block Type</th>
    <th>Scope</th>
    <th>Feedback</th>
    <th>Blocked For</th>
    <th>Date</th>
    <th>Reason</th>
    <th>By</th>
    <th>Action</th>
  </tr>
  <?php
  $block_array = ['1' => 'Face To Face', '2' => 'Translation','3' => 'Remote'];
  $query = "SELECT b.*, c.name as company_name 
            FROM $table b 
            LEFT JOIN comp_reg c ON b.block_by_id = c.id 
            WHERE b.interpName = '$code_qs' 
            ORDER BY b.id DESC";
  $result = mysqli_query($con, $query);
  if (mysqli_num_rows($result) > 0) {
    $pos = 1;
    while ($row = mysqli_fetch_assoc($result)) {
      ?>
      <tr>
            <td><?php echo $pos++; ?></td>

            <!-- Company Name -->
            <td><?php echo htmlspecialchars($row['company_name']); ?></td>

            <!-- Block Type -->
            <td><?php echo ucfirst($row['block_by_type']); ?></td>

            <!-- Scope -->
            <td>
                <?php
                echo ucfirst(str_replace('_', ' ', $row['block_scope']));
                if ($row['block_scope'] === 'specific_user') {
                    echo "<br>Service User: " . htmlspecialchars($row['service_user_id']) . "";
                } elseif ($row['block_scope'] === 'specific_reference') {
                    echo "<br>Ref ID: " . htmlspecialchars($row['reference_id']) . "";
                }
                ?>
            </td>

            <!-- Feedback -->
            <td><?php echo htmlspecialchars($row['get_feedback']); ?></td>

            <!-- Blocked For -->
            <td><?php
              $blockedFor = explode(',', $row['blocked_for'] ?? '');
              $labels = array_map(function ($val) use ($block_array) {
                  return $block_array[trim($val)] ?? $val;
              }, $blockedFor);
              echo implode(', ', $labels) ?: '—';
              ?>
            </td>

            <!-- Status Info -->

                  <?php if ($row['deleted_flag'] == 0) { ?>
                    <td>
                      <?php echo $misc->date_time($row['dated']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($row['block_reason']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($row['blocked_by']); ?>
                    </td>
                    
                <?php } else { ?>
                <td>
                  <?php echo $misc->date_time($row['edited_date']); ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($row['edited_reason']); ?>
                </td>
                <td>
                     <?php echo htmlspecialchars($row['edited_by']); ?>
                </td>  
                <?php } ?>

            <!-- Actions -->
            <td>
                <?php if ($row['deleted_flag'] == 0) { ?>
                    <a class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteReasonModal"
                        data-id="<?php echo $row['id']; ?>"
                        data-url="interp_reg_blacklist.php?edit_id=<?php echo $interpreter_id; ?>&code_qs=<?php echo $code_qs; ?>&name=<?php echo $name; ?>"
                        title="Trash this record">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>

                <?php } else { ?>
                    <a class="btn btn-success btn-xs"
                    href="interp_reg_blacklist.php?edit_id=<?php echo $interpreter_id; ?>&code_qs=<?php echo $code_qs; ?>&name=<?php echo $name; ?>&activate_id=<?php echo $row['id']; ?>"
                    title="Restore to blacklist">
                        <i class="glyphicon glyphicon-refresh"></i>
                    </a>
                <?php } ?>
            </td>
        </tr>

    <?php }
  }else{
        echo '<center><h4 class="text-danger">Not blacklisted by company yet!</h4></center>';
      } 
  ?>
</table>
  </div>
<div class="modal fade" id="deleteReasonModal" tabindex="-1" role="dialog" aria-labelledby="deleteReasonLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="GET" id="deleteReasonForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Reason for Deletion</h5>
          <input type="hidden" name="redirect_url" id="modalRedirectUrl">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="del_id" id="modalDelId">
          <div class="form-group">
            <label for="edited_reason">Enter reason:</label>
            <textarea name="edited_reason" id="editedReasonInput" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Confirm Delete</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script>
$('#deleteReasonModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const id = button.data('id');
    const url = button.data('url');

    $('#modalDelId').val(id);
    $('#deleteReasonForm').attr('action', url);
    $('#modalRedirectUrl').val(window.location.href); // Store current URL
});
</script>

</body>

</html>

<!--  ALTER TABLE `interp_blacklist` CHANGE `blocked_by` `blocked_by` VARCHAR(200) NULL DEFAULT NULL; -->

<!-- ALTER TABLE `interp_blacklist` ADD `block_reason` TEXT NOT NULL AFTER `blocked_by`, ADD `edited_reason` TEXT NULL AFTER `block_reason`; -->