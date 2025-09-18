<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'actions.php';
include_once('function.php');
//Access actions
$get_actions = explode(",", $obj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=149 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_create = $_SESSION['is_root'] == 1 || in_array(193, $get_actions);
$action_view = $_SESSION['is_root'] == 1 || in_array(194, $get_actions);
$action_update = $_SESSION['is_root'] == 1 || in_array(195, $get_actions);
$action_delete = $_SESSION['is_root'] == 1 || in_array(196, $get_actions);
$action_restore = $_SESSION['is_root'] == 1 || in_array(197, $get_actions);

$array_job_types = array(1 => "Face To Face", 2 => "Telephone", 3 => "Translation");
$priority_array = array(1 => '<label class="label label-danger">High priority</label>', '2' => '<label class="label label-warning">Medium priority</label>', '3' => '<label class="label label-info">Low priority</label>');
$status_array = array('0' => 'Pending', '1' => 'Resolved', '2' => 'Training Suggested', '3' => 'Removed', '4' => 'In Progress', '5' => 'Concluded');
$received_via_array = array('1' => 'Telephone', '2' => 'Email', '3' => 'Letter', '4' => 'Timesheet', '5' => 'Other');

if (isset($_GET['del_id'])) {
  $obj->update("complaints", array("deleted_flag" => 1), "id=" . $_GET['del_id']);
}
if (isset($_GET['restore_id'])) {
  $obj->update("complaints", array("deleted_flag" => 0), "id=" . $_GET['restore_id']);
}
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;  ?>
<!doctype html>
<html lang="en">
<head>
  <title>Complaints list</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<?php include 'header.php'; ?>

<body>
  <?php include 'nav2.php'; ?>
  <section class="container-fluid" style="overflow-x:auto">
    <div class="col-md-12">
      <header>
        <center>
          <div class="alert alert-info col-sm-3">
            <a href="<?php echo basename(__FILE__); ?>" class="alert-link">Registered System Complaints list</a>
          </div>
        </center>
        <?php if ($action_create) { ?>
          <a style="color:white;" class="btn btn-primary pull-right" href="#" data-toggle="modal" data-target="#complaint_modal">Create New Complaint</a>
        <?php } ?>
        <div class="form-group col-md-2 col-sm-4 pull-right">
				<select id="deleted" name="deleted" class="form-control pull-left" onchange="filter_list()">
					<option <?=!isset($_GET['deleted']) ? 'selected' : ''?> value="">-- Show All Complaints --</option>
					<option <?=(isset($_GET['deleted']) && $_GET['deleted'] == 0) ? 'selected' : ''?> value="0">Show Active only</option>
					<option <?=isset($_GET['deleted']) && $_GET['deleted'] == 1 ? 'selected' : ''?> value="1">Show Deleted Only</option>
				</select>
			</div>
        <?php if ($_SESSION['returned_message']) {
          echo $_SESSION['returned_message'];
          unset($_SESSION['returned_message']);
        } ?>
        <table class="table table-bordered" cellspacing="0" width="100%">
          <thead class="bg-primary">
            <tr>
              <th>Complaint ID</th>
              <th>Interpreter & Job ID</th>
              <th>Nature</th>
              <th>Complaint By</th>
              <th>Received VIA</th>
              <th>Assigned To</th>
              <th>Date</th>
              <th>Status</th>
              <th width="20%" align="center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $table = 'complaints';
            if(isset($_GET['deleted'])){
              $append_deleted = " and complaints.deleted_flag=" . $_GET['deleted'];
            }
            if ($_SESSION['is_root'] == 0) {
              $append_my_complaints = " and (complaints.assigned_to=" . $_SESSION['userId'] . " OR complaints.created_by=" . $_SESSION['userId'] . ") ";
            }
            $query = "SELECT complaints.*,interpreter_reg.name,login.name as user,complaint_types.title as nature FROM complaints,interpreter_reg,login,complaint_types WHERE complaints.interpreter_id=interpreter_reg.id AND complaints.assigned_to=login.id AND complaints.type_id=complaint_types.id $append_deleted $append_my_complaints LIMIT {$startpoint} , {$limit}";
            $get_complaints = $obj->read_all("complaints.*,interpreter_reg.name,interpreter_reg.id as interpreterID,login.name as user,complaint_types.title as nature", "complaints,interpreter_reg,login,complaint_types", "complaints.interpreter_id=interpreter_reg.id AND complaints.assigned_to=login.id AND complaints.type_id=complaint_types.id $append_deleted $append_my_complaints ORDER BY complaints.id DESC LIMIT {$startpoint} , {$limit}");
            if ($get_complaints->num_rows > 0) {
              while ($row = $get_complaints->fetch_assoc()) { ?>
                <tr <?php if ($row['deleted_flag'] == 1) { ?> style="background-color: #ff000096;color: white;" title="This complaint has been deleted by LSUK!" <?php } ?>>
                  <td><?php echo $row['id']; ?></td>
                  <td><?php echo ucwords($row['name']);
                      echo $row['job_id'] != 0 ? '<br>' . $array_job_types[$row['job_type']] . ' ID # ' . $row['job_id'] : ''; ?></td>
                  <td><?php echo $row['nature']; ?></td>
                  <td><?php echo $row['complaint_by']; ?></td>
                  <td><?php echo $received_via_array[$row['received_via']]; ?></td>
                  <td><?php echo $row['user']; ?></td>
                  <td><?php echo date("d-m-Y", strtotime($row['dated'])); ?></td>
                  <td><?php echo $row['status'] == 0 ? '<span class="label label-warning">Pending</span>' : '<span class="label label-success">' . $status_array[$row['status']] . '</span>';
                      echo "<br>" . $priority_array[$row['complaint_priority']] ?></td>
                  <td align="center">
                    <?php if ($action_view) { ?>
                      <a style="color:white;" href="javascript:void(0)" onclick="view_complaint(<?php echo $row['id']; ?>);" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> View</a>
                      <?php }
                    if ($row['deleted_flag'] == 0) {
                      if ($action_delete) { ?>
                        <a style="color:white;" href="<?php echo basename(__FILE__) . '?del_id=' . $row['id']; ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</a>
                      <?php }
                    } else {
                      if ($action_restore) { ?>
                        <a style="color:white;" href="<?php echo basename(__FILE__) . '?restore_id=' . $row['id']; ?>" class="btn btn-sm btn-success"><i class="fa fa-refresh"></i> Restore</a>
                      <?php }
                    }
                    if ($action_update) { ?>
                      <a style="color:white;" href="javascript:void(0)" onclick="update_complaint(<?php echo $row['id']; ?>,<?php echo $row['interpreterID']; ?>);" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                    <?php } ?>
                  </td>
                </tr>
            <?php }
            } else {
              echo "<tr><td colspan='9' align='center'><i>There are no complaints registered yet!</i></td></tr>";
            } ?>
          </tbody>
        </table>
        <div><?php echo pagination($obj->con, $table, $query, $limit, $page); ?></div>
    </div>
  </section>
  <!-- Modal to display record -->
  <div class="modal modal-info fade col-md-12" data-toggle="modal" data-target=".bs-example-modal-lg" id="complaint_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Add New Complaint</h4>
        </div>
        <div class="modal-body text-center" id="complaint_modal_data">
          <div class="row">
            <form action="" method="post" id="add_complaints">
              <input type="hidden" name="action" value="new_complaint">
              <div class="row"></div>
              <div class="form-group col-md-4">
                <select class="form-control" required="" id="type_id" name="type_id">
                  <option disabled selected>--- Nature of Complaint ---</option>
                  <?php $q_c = $obj->read_all("*", "complaint_types", "1 ORDER BY title ASC");
                  while ($row_q_c = $q_c->fetch_assoc()) { ?>
                    <option value="<?php echo $row_q_c['id']; ?>"><?php echo $row_q_c['title']; ?></option>
                  <?php } ?>

                </select>
              </div>
              <div class="form-group col-md-4">
                <select class="form-control" required id="complaint_priority" name="complaint_priority">
                  <option disabled selected>--- Select Priority ---</option>
                  <option value="1">High</option>
                  <option value="2">Medium</option>
                  <option value="3">Low</option>
                </select>
              </div>
              <div class="form-group col-md-4">
                <select class="form-control" required="" id="received_via" name="received_via">
                  <option disabled selected>--- Select Received Via ---</option>
                  <?php
                  foreach ($received_via_array as $rkey => $rvalue) {
                  ?>
                    <option value="<?php echo $rkey; ?>"><?php echo $rvalue; ?></option>
                  <?php }
                  ?>
                </select>
              </div>
              <div class="form-group col-md-4">
                <select class="form-control" required id="job_type" name="job_type">
                  <option disabled selected>--- Select Job Type ---</option>
                  <option value="1">Face To Face</option>
                  <option value="2">Telephone</option>
                  <option value="3">Translation</option>
                </select>
              </div>
              <div class="form-group col-md-4">
                <input type="text" id="job_id" name="job_id" class="form-control" placeholder="Enter Job ID OR LSUK Reference No" required />
              </div>
              <div class="form-group col-md-4">
                <select class="form-control" required id="interpreter_id" name="interpreter_id">
                  <option disabled selected>--- Select an Interpreter ---</option>
                  <?php $q_interpreters = $obj->read_all("id,name", "interpreter_reg", "deleted_flag=0 and is_temp=0 and 
                  interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) ORDER BY name ASC");
                  while ($row_interpreters = $q_interpreters->fetch_assoc()) { ?>
                    <option value="<?php echo $row_interpreters['id']; ?>"><?php echo $row_interpreters['name'] . " # " . $row_interpreters['id']; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group col-md-6">
                <input type="text" id="complaint_by" name="complaint_by" class="form-control" placeholder="Enter Complaint By (Client Name)" required />
              </div>
              <div class="form-group col-md-6">
                <input type="text" id="complaint_email" name="complaint_email" class="form-control" placeholder="Enter Client Email" required />
              </div>
              <div class="form-group col-md-12">
                <textarea id="details" rows="12" name="details" class="form-control" placeholder="Enter complaint details here ..." required=""></textarea>
              </div>

              <div class="form-group col-md-4">
                <select class="form-control" required="" name="assigned_to" id="assigned_to">
                  <option disabled selected>--- Select Assigned To ---</option>
                  <?php $q_assigned = $obj->read_all("id,name", "login", "user_status=1 and Temp=0 ORDER BY name ASC");
                  while ($row_assigned = $q_assigned->fetch_assoc()) { ?>
                    <option value="<?php echo $row_assigned['id']; ?>"><?php echo $row_assigned['name']; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label class="btn btn-default pull-left" for="email_int"><input id="email_int" name="email_int" type="checkbox" value="1" checked /> Notify Interpreter via Email</label>
              </div>
              <div class="form-group col-md-4">
                <label class="btn btn-default pull-left" for="email_client"><input id="email_client" name="email_client" type="checkbox" value="1" /> Notify Client via Email</label>
              </div>
              <div class="form-group col-md-8">
                <input onclick="submit_complaint();" type="button" id="btn_send_complaint" class="btn btn-success pull-left" value="Create Complaint">
              </div>
            </form>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn  btn-primary pull-right" data-dismiss="modal"> Close</button>
        </div>
      </div>
    </div>
  </div>
  <!--End of modal-->
  <!-- Modal to display record -->
  <div class="modal modal-info fade col-md-12" data-toggle="modal" data-target=".bs-example-modal-lg" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div class="modal-dialog" role="document" style="width: 55%;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Complaint Information</h4>
        </div>
        <div class="modal-body" id="view_modal_data"></div>
        <div class="modal-footer">
          <button type="button" class="btn  btn-primary pull-right" data-dismiss="modal"> Close</button>
        </div>
      </div>
    </div>
  </div>
  <!--End of modal-->
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<script>
		function filter_list() {
			var append_url="<?=basename(__FILE__)?>?1";
			var deleted=$('#deleted').val();
			if(deleted){
				append_url+='&deleted='+deleted;
			}
			var prv=$('#prv').val();
			if(prv){
				append_url+='&prv='+prv;
			}
			window.location.href=append_url;
		}
    function view_complaint($complaint_id) {
      $.ajax({
        url: 'process/complaints.php',
        method: 'post',
        data: {
          complaint_id: $complaint_id,
          action: "view_complaint"
        },
        success: function(data) {
          $('#view_modal_data').html(data);
          $('#view_modal').modal('show');
        },
        error: function(data) {
          alert("Error code : " + data.status + " , Error message : " + data.statusText);
        }
      });
    }

    function update_complaint($complaint_id, $interpreter_id) {
      $.ajax({
        url: 'process/complaints.php',
        method: 'post',
        data: {
          complaint_id: $complaint_id,
          interpreter_id: $interpreter_id,
          redirect_url: window.location.href,
          action: "update_complaint"
        },
        success: function(data) {
          $('#view_modal_data').html(data);
          $('#view_modal').modal('show');
        },
        error: function(data) {
          alert("Error code : " + data.status + " , Error message : " + data.statusText);
        }
      });
    }

    function pad(n) {
      return n < 10 ? '0' + n : n
    }

    function send_reply() {
      var reply_complaint_id = $("#reply_complaint_id").val();
      var reply_message_client = $("#reply_message_client").val();
      var reply_message_interpreter = $("#reply_message_interpreter").val();
      var reply_message_lsuk = $("#reply_message_lsuk").val();
      if (reply_message_client || reply_message_lsuk || reply_message_interpreter) {
        $.ajax({
          url: 'process/complaints.php',
          method: 'post',
          dataType: 'json',
          data: {
            reply_complaint_id: reply_complaint_id,
            reply_message_client: reply_message_client,
            reply_message_interpreter: reply_message_interpreter,
            reply_message_lsuk: reply_message_lsuk,
            action: "complaint_reply"
          },
          success: function(res) {

            if (res['status'] == 1) {
              view_complaint(reply_complaint_id);
            } else {
              alert("Unable to send your message!");
            }
          },
          error: function(xhr) {
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
          }
        });
      } else {
        $("#reply_message_lsuk").focus();
      }
    }

    function submit_complaint() {
      var interpreter_id = $("#interpreter_id").val();
      var job_id = $("#job_id").val();
      var complaint_by = $("#complaint_by").val();
      var details = $("#details").val();
      var data_complaints = $("#add_complaints").serializeArray();
      var received_via = $("#received_via").val();
      var assigned_to = $("#assigned_to").val();
      if (interpreter_id && details && received_via && received_via && assigned_to && assigned_to) {
        $.ajax({
          url: 'process/complaints.php',
          method: 'post',
          dataType: 'json',
          data: data_complaints,
          success: function(res) {
            alert(res['message']);
            if (res['status'] == 1) {
              window.location.href = "complaints.php";
            }
          },
          error: function(xhr) {
            alert("Failed to create complaint due to invalid processing!");
          }
        });
      } else {
        if (!interpreter_id) {
          $("#interpreter_id").focus();
        } else {
          $("#details").focus();
        }
      }
    }
    function toggle_content(val = 1) {
      if (val == 1) {
        $('.full-content').removeClass("hidden");
        $('.short-content').addClass("hidden");
      } else {
        $('.full-content').addClass("hidden");
        $('.short-content').removeClass("hidden");
      }
    }
  </script>
</body>
</html>