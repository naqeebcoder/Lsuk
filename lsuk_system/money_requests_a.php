<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'actions.php';
include_once('function.php');
//Access actions
$get_actions = explode(",", $obj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=210 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_create = $_SESSION['is_root'] == 1 || in_array(198, $get_actions);
$action_check_request = $_SESSION['is_root'] == 1 || in_array(199, $get_actions);

$array_job_types = array(1 => "Face To Face", 2 => "Telephone", 3 => "Translation");
$array_types = array(0 => "Interpreter not to pay", 1 => "Interpreter to pay", 2 => "Deduction");
$array_types_colors = array(0 => "<span class='label label-success pull-right'>Interpreter not to pay</span>", 1 => "<span class='label label-warning pull-right'>Interpreter to pay</span>", 2 => "<span class='label label-danger pull-right'>Deduction</span>");
$array_statuses = array("1" => "Requested", "2" => "Accepted", "3" => "Rejected");
$array_colors = array("1" => "label-warning", "2" => "label-success", "3" => "label-danger");
$table = "loan_requests";
if (isset($_GET['del_id'])) {
  $obj->update($table, array("deleted_flag" => 1), "id=" . $_GET['del_id']);
}
if (isset($_GET['restore_id'])) {
  $obj->update($table, array("deleted_flag" => 0), "id=" . $_GET['restore_id']);
}
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
$get_types = $obj->read_all("*", "loan_dropdowns", "is_payable=0 AND deleted_flag=0 ORDER BY title ASC");
while ($row_type = $get_types->fetch_assoc()) {
  $array_request_types[] = $row_type;
}
$q_interpreters = $obj->read_all("id,name", "interpreter_reg", "deleted_flag=0 and is_temp=0 and interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) ORDER BY name ASC");
while ($row_int = $q_interpreters->fetch_assoc()) {
  $array_interpreters[] = $row_int;
} ?>
<!doctype html>
<html lang="en">

<head>
  <title>Advance Requests List</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<?php include 'header.php'; ?>

<body>
  <?php include 'nav2.php'; ?>
  <section class="container-fluid" style="overflow-x:auto">
    <div class="col-md-12">
      <header>
        <!-- <center>
          <div class="alert alert-info col-sm-3">
            <a href="<?php echo basename(__FILE__); ?>" class="alert-link">Advance Requests list</a>
          </div>
        </center> -->
        <div class="row">
          <div class="form-group col-sm-2">
            <small>From Date</small>
            <input type="date" name="date_from" id="date_from" placeholder='From' class="form-control form-control-sm" value="<?php echo $_GET['date_from']; ?>" />
          </div>
          <div class="form-group col-sm-2">
            <small>To Date</small>
            <input type="date" name="date_to" id="date_to" placeholder='To' class="form-control form-control-sm" value="<?php echo $_GET['date_to']; ?>" />
          </div>
          <div class="form-group col-sm-2">
            <small>Filter By Interpreter</small>
            <select class="form-control" required id="int_id" name="int_id">
              <option disabled selected>--- Select an Interpreter ---</option>
              <?php $q_db_interpreters = $obj->read_all("DISTINCT interpreter_reg.id,interpreter_reg.name", "loan_requests,interpreter_reg", "loan_requests.interpreter_id=interpreter_reg.id AND interpreter_reg.deleted_flag=0 ORDER BY interpreter_reg.name ASC");
                while ($row_db_interpreters = $q_db_interpreters->fetch_assoc()) {
                $selected = $_GET['int_id'] == $row_db_interpreters['id'] ? "selected" : ""; ?>
                <option <?= $selected ?> value="<?php echo $row_db_interpreters['id']; ?>"><?php echo ucwords($row_db_interpreters['name']) . " # " . $row_db_interpreters['id']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group col-sm-2">
            <small>Filter Request Type</small>
            <select class="form-control" name="type_id" id="type_id">
              <option value="">-- Select Request Type --</option>
              <?php foreach ($array_request_types as $row_type) {
                $is_red = $row_type['is_payable'] == 1 ? "style='color:red'" : "";
                $selected = $_GET['type_id'] == $row_type['id'] ? " selected" : "";
                echo "<option " . $is_red . $selected . " value='" . $row_type['id'] . "'>" . $row_type['title'] . "</option>";
              } ?>
            </select>
          </div>
          <div class="form-group col-md-2"><br>
            <a href="javascript:void(0)" title="Click to Get Results" onclick="filter_list()"><span class="btn btn-success">Filter List</span></a>
            <a href="money_requests_a.php" title="Click to reset filters"><span class="btn btn-warning">Clear</span></a>
          </div>
          <?php if ($action_create) { ?>
            <a style="color:white;margin: 5px 15px;" class="btn btn-success" href="#" data-toggle="modal" data-target="#money_request_modal">Add New Advance Request</a>
          <?php } ?>
          <div class="form-group col-md-2 col-sm-4 pull-right hidden">
            <select id="deleted" name="deleted" class="form-control pull-left" onchange="filter_list()">
              <option <?= !isset($_GET['deleted']) ? 'selected' : '' ?> value="">-- Show All Requests --</option>
              <option <?= (isset($_GET['deleted']) && $_GET['deleted'] == 0) ? 'selected' : '' ?> value="0">Show Active only</option>
              <option <?= isset($_GET['deleted']) && $_GET['deleted'] == 1 ? 'selected' : '' ?> value="1">Show Deleted Only</option>
            </select>
          </div>
        </div>
        <?php if ($_SESSION['returned_message']) {
          echo $_SESSION['returned_message'];
          unset($_SESSION['returned_message']);
        }
        if ($_GET['date_from'] && $_GET['date_to']) {
          $append_date_range = " AND DATE(loan_requests.created_date) BETWEEN '" . $_GET['date_from'] . "' AND '" . $_GET['date_to'] . "'";
        }
        if (isset($_GET['int_id']) && is_numeric($_GET['int_id'])) {
          $append_interpreter_id = " and loan_requests.interpreter_id=" . $_GET['int_id'];
        }
        if (isset($_GET['type_id']) && is_numeric($_GET['type_id'])) {
          $append_type = " and loan_requests.type_id=" . $_GET['type_id'];
        }
        $query = "SELECT $table.*,interpreter_reg.name,loan_dropdowns.title,loan_dropdowns.is_payable FROM $table,loan_dropdowns,interpreter_reg WHERE $table.type_id=loan_dropdowns.id AND $table.interpreter_id=interpreter_reg.id AND loan_dropdowns.is_payable=0 $append_type $append_date_range $append_interpreter_id ORDER BY $table.id DESC LIMIT {$startpoint} , {$limit}";
        $result = $obj->read_all("$table.*,interpreter_reg.name,loan_dropdowns.title,loan_dropdowns.is_payable", "$table,loan_dropdowns,interpreter_reg", "$table.type_id=loan_dropdowns.id AND $table.interpreter_id=interpreter_reg.id AND loan_dropdowns.is_payable=0 $append_type $append_date_range $append_interpreter_id ORDER BY $table.id DESC LIMIT {$startpoint} , {$limit}");
        if ($result->num_rows > 0) {
          echo pagination($obj->con, $table, $query, $limit, $page); ?>
          <table class="table table-bordered" cellspacing="0" width="100%">
            <thead class="bg-success">
              <tr>
                <th>S.No</th>
                <th>Interpreter</th>
                <th>Requested Amount</th>
                <th>Approved</th>
                <th>Paid Amount</th>
                <th>Payable Date</th>
                <th>Request Type</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              while ($row = $result->fetch_assoc()) {
                $total_paid = 0;
                $total_paid = $obj->read_specific("IFNULL(SUM(round(request_paybacks.paid_amount,2)),0) as paid_amount", "request_paybacks,loan_requests", "request_paybacks.deleted_flag=0 AND request_paybacks.request_id=loan_requests.id AND loan_requests.id=" . $row['id'])['paid_amount'];
                $count++; ?>
                <tr title="<?php echo 'Last updated at ' . $misc->dated($row['updated_date']); ?>">
                  <td><?php echo $count; ?></td>
                  <td><?php echo $row['name'] . " #" . $row['interpreter_id'] . "<br><small>Created:" . $misc->dated($row['created_date']) . "</small>"; ?></td>
                  <td><?php echo number_format($row['loan_amount'], 2); ?></td>
                  <td class="text-danger"><?php echo number_format($row['given_amount'], 2); ?></td>
                  <td>
                    <?php //echo ($row['given_amount'] - $total_paid > 0) ? "<span class='text-danger'>" . number_format($total_paid, 2) . "</span>" : number_format($total_paid, 2) . " <i class='text-success fa fa-check-circle'></i>";
                        if (empty($row['given_amount']) || $row['given_amount'] == 0) {
                            echo "<span class='text-danger'>" . number_format($total_paid, 2) . "</span>";
                            echo "<br><small class='text-danger'>Not Approved</small>";
                        } else if (($row['given_amount'] - $total_paid) > 0) {
                            echo "<span class='text-danger'>" . number_format($total_paid, 2) . "</span>";
                            echo "<br><small class='text-danger'>Unpaid</small>";
                        } else {
                            echo number_format($total_paid, 2) . " <i class='text-success fa fa-check-circle'></i>";
                            echo "<br><small class='text-success'>Completed</small>";
                        } 
                        ?></td>
                  <td><?php echo $misc->dated($row['payable_date']); ?></td>
                  <td><?php echo $row['title'] ?></td>
                  <td><small><?= $row['reason'] ?: "Not mentioned"; ?></small></td>
                  <td align="center"><span class="label <?php echo $array_colors[$row['status']]; ?>"><?php echo $array_statuses[$row['status']]; ?></span>
                  <td width="14%">
                    <?php if ($action_check_request) { ?>
                      <button type="button" onclick="check_request(<?php echo $row['id']; ?>)" class="btn btn-sm btn-info">Check Request</button>
                    <?php } ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php } else { ?>
          <h4 class="text-muted text-center">There are no Advance Requests available currently in list!</h4>
        <?php } ?>
        <div>
        </div>
  </section>
  <!-- Modal to display record -->
  <div class="modal modal-info fade col-md-12" data-toggle="modal" data-target=".bs-example-modal-lg" id="money_request_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Add New Advance Request</h4>
        </div>
        <div class="modal-body text-center" id="money_request_modal_data">
          <div class="row">
            <form action="process/money_requests.php" method="post">
              <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
              <div class="form-group col-md-4 text-left">
                <label>Select Request Type</label>
                <select class="form-control" required name="type_id">
                  <option value="">-- Select Request Type --</option>
                  <?php foreach ($array_request_types as $row_type) {
                    $is_red = $row_type['is_payable'] == 1 ? "style='color:red'" : "";
                    echo "<option " . $is_red . " value='" . $row_type['id'] . "'>" . $row_type['title'] . "</option>";
                  } ?>
                </select>
              </div>
              <div class="form-group col-md-4 text-left">
                <label>Select an Interpreter</label>
                <select class="form-control" required id="interpreter_id" name="interpreter_id">
                  <option disabled selected>--- Select an Interpreter ---</option>
                  <?php foreach ($array_interpreters as $row_interpreters) { ?>
                    <option value="<?php echo $row_interpreters['id']; ?>"><?php echo $row_interpreters['name'] . " # " . $row_interpreters['id']; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group col-md-4 text-left">
                <label>Enter Amount Here</label>
                <input type="text" id="loan_amount" name="loan_amount" class="form-control" placeholder="Enter Loan Amount" required />
              </div>
              <div class="form-group col-md-3 text-left">
                <label>Select Job Type</label>
                <select class="form-control" id="job_type" name="job_type">
                  <option disabled selected>--- Select Job Type ---</option>
                  <option value="1">Face To Face</option>
                  <option value="2">Telephone</option>
                  <option value="3">Translation</option>
                </select>
              </div>
              <div class="form-group col-md-3 text-left">
                <label>Enter Job ID <small>Optional</small></label>
                <input type="number" id="job_id" name="job_id" class="form-control" placeholder="Enter Job ID (optional)" />
              </div>
              <div class="form-group col-md-6 text-left">
                  <label>Select Payable Month <small class='text-danger'>(Payslip addition will start from this month)</small></label>
                  <input min="<?=date('Y-m')?>" value="<?=date('Y-m')?>" style="width: 40%;" class="form-control" type="month" name="payable_date" id="payable_date" required/>
              </div>
              <div class="form-group col-md-12">
                <textarea id="reason" rows="12" name="reason" class="form-control" placeholder="Enter Advance Request reason here ..."></textarea>
              </div>
              <div class="form-group col-md-4 hidden">
                <label class="btn btn-default pull-left" for="is_notified"><input id="is_notified" name="is_notified" type="checkbox" value="1" /> Notify Interpreter via Email</label>
              </div>
              <div class="form-group col-md-8">
                <button type="submit" name="btn_add_money_request" class="btn btn-success pull-left">Create Advance Request</button>
              </div>
            </form>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-right" data-dismiss="modal"> Close</button>
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
          <h4 class="modal-title" id="myModalLabel">Advance Request Information</h4>
        </div>
        <div class="modal-body" id="view_modal_data"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-right" data-dismiss="modal"> Close</button>
        </div>
      </div>
    </div>
  </div>
  <!--End of modal-->
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
    function check_request($request_id) {
      $.ajax({
        url: 'process/money_requests.php',
        method: 'post',
        data: {
          request_id: $request_id,
          redirect_url: window.location.href,
          action: "check_request"
        },
        success: function(data) {
          $('#view_modal_data').html(data);
          $('#view_modal').find('.modal-dialog').css('width', '60%');
          $('#view_modal').modal('show');
        },
        error: function(data) {
          alert("Error code : " + data.status + " , Error message : " + data.statusText);
        }
      });
    }

    function filter_list() {
      var append_url = "money_requests_a.php?";
      var date_from = $('#date_from').val();
      var date_to = $('#date_to').val();
      if (date_from && date_to) {
        append_url += '&date_from=' + date_from + '&date_to=' + date_to;
      }
      var int_id = $('#int_id').val();
      if (int_id) {
        append_url += '&int_id=' + int_id;
      }
      var type_id = $('#type_id').val();
      if (type_id) {
        append_url += '&type_id=' + type_id;
      }
      window.location.href = append_url;
    }

    function toggle_update_request(element) {
      var checked_value = $(element).val();
      if (checked_value == 2) {
        if ($(element).attr("data-is_payable") == 1) {
          $('.div_accept').removeClass("hidden");
          $("#percentage").attr("required", "required");
        } else {
          $('.div_accept').addClass("hidden");
          $("#percentage").removeAttr("required");
        }
        $('.div_reject').addClass("hidden");
        $('#reject_reason').removeAttr("required");
      } else {
        $('.div_accept').addClass("hidden");
        $('.div_reject').removeClass("hidden");
        $('#reject_reason').attr("required", "required");
      }
    }

    function calculate_duration() {
      var percentage = $("#percentage").val() ? $("#percentage").val() : 100;
      var loan_amount = $("#given_amount").val();
      var total_installments = !isNaN(Math.round(loan_amount / ((loan_amount * (percentage / 100))))) ? Math.round(loan_amount / ((loan_amount * (percentage / 100)))) : 0;
      var installment_amount = !isNaN(Math.round(loan_amount / total_installments)) ? Math.round(loan_amount / total_installments) : 0;
      $(".text_duration").text(total_installments);
      $("#duration").val(total_installments);
      $(".text_installment_amount").text(installment_amount);
    }

    function show_deduction_div() {
      if ($(".deduction_div").hasClass("hidden")) {
        $(".deduction_div").removeClass("hidden");
      }
    }
  </script>
</body>

</html>