<?php
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include_once('function.php');
include 'class.php';
include('inc_functions.php');

$allowed_type_idz = "56";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Paid Salaries</u> action!<br>Kindly contact admin for further process.</h2></center>");
  }
}
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=25 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_payslip = $_SESSION['is_root'] == 1 || in_array(209, $get_actions);
$action_undo_payslip = $_SESSION['is_root'] == 1 || in_array(210, $get_actions);
$action_mark_paid = $_SESSION['is_root'] == 1 || in_array(211, $get_actions);

$interp = @$_GET["interp"];
$get_dated = @$_GET["get_dated"];
if ($interp) {
  $name = $acttObj->read_specific('name', 'interpreter_reg', 'id=' . $interp);
  $interp_name = $name['name'];
}
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;  ?>
<!doctype html>
<html lang="en">

<head>
  <title>Interpreters Paid Salaries Record</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" type="text/css" href="css/util.css" />
</head>
<?php include 'header.php'; ?>

<body>
  <?php include 'nav2.php'; ?>
  <!-- end of sidebar -->
  <style>
    .tablesorter thead tr {
      background: none;
    }

    .interp_multiselect .btn-group {
        width: 100% !important;
    }
    .dropdown-menu .divider {
			margin: 5px 0;
		}

		.interp_multiselect .btn-group {
			width: 100%;
		}

		.multiselect {
			min-width: 100%;
			display: flex;
		}

		span.multiselect-selected-text {
			text-align: left;
			float: left;
			text-wrap: auto;
		}

		.interp_multiselect .btn .caret {
			margin: 8px;
			position: absolute;
			right: 0;
		}

		.multiselect-container {
			max-height: 400px;
			overflow-y: auto;
			max-width: 400px;
		}
  </style>
  <section class="container-fluid" style="overflow-x:auto">
    <div class="col-md-12">

      <header class="row">

        <div class="form-group col-md-3 col-sm-4">
          <a href="<?php echo basename(__FILE__); ?>">
            <h2 class="col-md-3 text-center"><span class="label label-success"><?= $interp_name ? ucwords($interp_name) : "All"; ?> Salaries Record</span></h2>
          </a>
        </div>
        
        <div class="p-t-10">
          <div class="form-group col-md-2 col-sm-4 text-right">
            <label title="Filter salaries by Paid Status" class="btn btn-default btn-sm">
              <input <?= isset($_GET['is_paid']) ? 'checked' : '' ?> type="checkbox" id="is_paid" onchange="myFunction()"> Filter Paid Salaries Only
            </label>
          </div>
          <div class="form-group col-md-2 col-sm-4">
            <?php if (isset($interp) && !empty($interp)) { ?>
              <select id="get_dated" onChange="myFunction()" class="form-control">
                <?php
                $sql_opt = $acttObj->read_all("dated", "interp_salary", "deleted_flag=0 and interp=" . $interp . " ORDER BY dated DESC");
                echo '<option value="">--- Select Paid Date ---</option>';
                while ($row_opt = mysqli_fetch_array($sql_opt)) {
                  $row_dated = $row_opt["dated"];
                  echo "<option " . ($get_dated == $row_dated ? 'selected' : '') . " value='$row_dated'>" . $row_dated . "</option>";
                }
                ?>
              </select>
            <?php } else { ?>
              <input placeholder="Salary Paid Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="get_dated" id="get_dated" class="form-control" onChange="myFunction()" value="<?php echo $get_dated; ?>" />
            <?php } ?>
          </div>
          <div class="form-group col-md-3 col-sm-4 interp_multiselect">
            <select id="interp" onChange="myFunction()" class="form-control">
              <?php
              $sql_int = $acttObj->read_all("distinct interpreter_reg.name,interpreter_reg.id", "interpreter_reg,interp_salary", "interp_salary.interp=interpreter_reg.id and interp_salary.deleted_flag=0 order by interpreter_reg.name ASC");
              echo '<option value="">- Select an Interpreter -</option>';
              while ($row_int = mysqli_fetch_array($sql_int)) {
                $row_id = $row_int["id"];
                $row_name = $row_int["name"];
                echo "<option " . ($interp == $row_id ? 'selected' : '') . " value='$row_id'>" . $row_name . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group col-md-2 col-sm-2">
            <select class="form-control" name="status" id="status" onchange="myFunction()">
              <option value="all" <?php echo (!isset($_GET['status']) || $_GET['status'] == 'all') ? 'selected' : '' ?>>All</option>
              <option value="trashed" <?php echo ($_GET['status'] == 'trashed') ? 'selected' : ''; ?>>Trashed</option>
            </select>
          </div>
        </div>
      </header>

      <span class="col-sm-12">
        <?php if ($_SESSION['returned_message']) {
          echo $_SESSION['returned_message'];
          unset($_SESSION['returned_message']);
        } ?></span>

      <?php $table = 'interp_salary';
          $append_date = "";
          $append_int = "";

          if (isset($get_dated) && !empty($get_dated)) {
            $append_int .= " AND $table.dated='$get_dated'";
          }
          if (isset($_GET['is_paid'])) {
            $append_int .= " AND $table.is_paid=1";
          }
          if (isset($interp) && !empty($interp)) {
            $append_int .= " AND $table.interp=" . $interp;
          }

          if (isset($_GET['status']) && $_GET['status'] == 'trashed') {
            $append_int .= " AND $table.deleted_flag = 1";
          }else {
            $append_int .= " AND $table.deleted_flag = 0";
          }
          
          $query = "SELECT $table.*,interpreter_reg.name 
          FROM $table,interpreter_reg 
          where $table.interp=interpreter_reg.id $append_int 
          ORDER BY $table.dated DESC 
          LIMIT {$startpoint} , {$limit}";
        ?>

        <div class="col-md-12 m-b-10">
          <div class="row text-right">
            <?php echo pagination($con, $table, $query, $limit, $page); ?>
          </div>
        </div>

      <table class="tablesorter table table-bordered" cellspacing="0" width="100%">
        <thead class="bg-primary">
          <tr>
            <th>Interpreter</th>
            <th>Invoice</th>
            <th>From</th>
            <th>To</th>
            <th>Deductions</th>
            <th>Additions</th>
            <th>Salary</th>
            <th>Paid Date & Status</th>
            <th>Slip Print Date</th>
            <th width="10%" align="center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $result = mysqli_query($con, $query);
          while ($row = mysqli_fetch_array($result)) { ?>
            <tr <?= $row['deleted_flag'] == 1 ? 'class="bg-danger"' : '' ?> >
              <td><?php echo ucwords($row['name']); ?></td>
              <td><?php echo $row['invoice']; ?></td>
              <td><?php echo $misc->dated($row['frm']); ?></td>
              <td><?php echo $misc->dated($row['todate']); ?></td>
              <td><?php echo $misc->numberFormat_fun($row['ni_dedu'] + $row['tax_dedu'] + $row['payback_deduction']); ?></td>
              <td><?php echo $misc->numberFormat_fun($row['given_amount']); ?></td>
              <td><?php echo $misc->numberFormat_fun(($row['salry'] - $row['ni_dedu'] - $row['tax_dedu'] - $row['payback_deduction']) + $row['given_amount']); ?></td>
              <td><?php echo $row['is_paid'] == 1 ? "<span class='label label-success'><i class='fa fa-check-circle'></i> Paid</span><small class='pull-right'><i class='fa fa-calendar'></i> " . $misc->dated($row['paid_date']) . "</small>" : "<span class='label label-warning'>Unpaid</span>"; ?></td>
              <td><?php echo $misc->dated($row['dated']); ?></td>
              <td align="center">
                <?php if ($action_view_payslip) { ?>
                  <a href="javascript:void(0)" onClick="popupwindow('pay_slip_record.php?invoice_number=<?php echo $row['invoice']; ?>&interpreter_id=<?php echo $row['interp']; ?>&invoice_form=<?php echo $row['frm']; ?>&invoice_to=<?php echo $row['todate']; ?>','View paid salary record', 1000,800);"><button class="btn btn-info btn-sm"><i class="glyphicon glyphicon-eye-open" title="View Paid Salary Slip"></i></button></a>
                <?php }
                if ($action_undo_payslip && $row['is_paid'] == 0 && $row['deleted_flag'] == 0) { ?>
                  <a href="javascript:void(0)" data-id="<?= $row['id'] ?>" data-invoice="<?= $row['invoice'] ?>" data-salary="<?= $misc->numberFormat_fun(($row['salry'] - $row['ni_dedu'] - $row['tax_dedu'] - $row['payback_deduction']) + $row['given_amount']) ?>" onclick="manage_salary_slip(this)"><button class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-refresh" title="Undo Paid Salary Slip"></i></button></a>
                <?php }
                if ($action_mark_paid && $row['is_paid'] == 0 && $row['deleted_flag'] == 0) { ?>
                  <a href="javascript:void(0)" data-id="<?= $row['id'] ?>" data-invoice="<?= $row['invoice'] ?>" data-salary="<?= $misc->numberFormat_fun(($row['salry'] - $row['ni_dedu'] - $row['tax_dedu'] - $row['payback_deduction']) + $row['given_amount']) ?>" onclick="mark_paid_modal(this)"><button class="btn btn-success btn-sm"><i class="glyphicon glyphicon-check" title="Mark as Paid Salary Slip"></i></button></a>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <div class="col-md-12 m-t-20 m-b-20">
        <div class="row text-right">
          <?php echo pagination($con, $table, $query, $limit, $page); ?>
        </div>
      </div>

    </div>
  </section>
  <!--Start Salary undo Modal-->
  <div class="modal" id="salary_slip_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="process/reg_interp_salary_list.php" method="post">
          <input type="hidden" name="slip_id" id="slip_id" required>
          <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
          <div class="modal-header alert-danger">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h4 class="modal-title"><b>Undo / Rollback Salary Slip</b></h4>
          </div>
          <div class="modal-body salary_slip_modal_attach">
            <div class="row">
              <div class="form-group col-md-6">
                <label for="slip_number">Salary Slip Number</label>
                <input class="form-control" name="slip_number" id="slip_number" readonly>
              </div>
              <div class="form-group col-md-6">
                <label for="total_salary">Total Salary</label>
                <input class="form-control" name="total_salary" id="total_salary" readonly>
              </div>
              <div class="form-group col-md-12">
                <label for="deleted_reason">Write Reason of Undo Salary Slip</label>
                <textarea rows="3" maxlength="250" placeholder="Write payslip undo reason ..." class="form-control" name="deleted_reason" id="deleted_reason" required></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            <button onclick='return confirm("Are you sure to UNDO this salary record?")' type="submit" name="undo_salary_slip" class="btn btn-danger">Undo Salary Slip</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!--End Salary undo Modal-->
  
  <!--Start Paid undo Modal-->
  <div class="modal" id="paid_salary_slip_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="process/reg_interp_salary_list.php" method="post" id="frm_pay_salary">
                <input type="hidden" name="paid_slip_id" id="paid_slip_id" required>
                <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
                
                <div class="modal-header alert-success">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"><b>Mark as Paid Salary Slip</b></h4>
                </div>

                <div class="modal-body paid_salary_slip_modal_attach">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="paid_slip_number">Salary Slip Number</label>
                            <input class="form-control" name="paid_slip_number" id="paid_slip_number" readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="paid_total_salary">Total Salary</label>
                            <input class="form-control" name="paid_total_salary" id="paid_total_salary" readonly>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="paid_by">Salary Slip Paid By</label>
                            <select class="form-control" name="paid_by" id="paid_by">
                                <option value="" selected>--- Select Paid By User ---</option>
                                <?php 
                                $q_users = $acttObj->read_all("id,name", "login", "user_status=1 and Temp=0 ORDER BY name ASC");
                                while ($row_user = $q_users->fetch_assoc()) { ?>
                                    <option value="<?php echo $row_user['id']; ?>"><?php echo ucwords($row_user['name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="paid_date">Select Paid Date</label>
                            <input class="form-control" name="paid_date" id="paid_date" type="date">
                        </div>

                        <div class="form-group col-sm-6 payment_type_wrap">
                            <label class="pull-left">Payment Type *</label>
                            <select class="form-control" id="payment_type" name="payment_type">
                                <option value="">- Select -</option>
                                <option value="bacs" <?php echo ($payment_type == 'bacs') ? 'selected' : ''; ?>>BACS</option>
                                <option value="cheque" <?php echo ($payment_type == 'cheque') ? 'selected' : ''; ?>>Cheque</option>
                                <option value="card" <?php echo ($payment_type == 'card') ? 'selected' : ''; ?>>Credit/Debit Card</option>
                                <option value="cash" <?php echo ($payment_type == 'cash') ? 'selected' : ''; ?>>Cash</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-6 payment_through_wrap">
                            <label class="pull-left">Payment Method *</label>
                            <select class="form-control" id="payment_through" name="payment_through">
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    <button type="submit" name="paid_salary_slip" class="btn btn-success">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>
  <!--End Paid undo Modal-->
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
<script src="js/income_receive_amount.js"></script>

<!-- Validation Script -->
<script>
  document.getElementById("frm_pay_salary").addEventListener("submit", function (e) {
    let fields = [
        {id: "paid_by", name: "Paid By"},
        {id: "paid_date", name: "Paid Date"},
        {id: "payment_type", name: "Payment Type"},
        {id: "payment_through", name: "Payment Method"}
    ];

    let missingFields = [];
    let valid = true;

    fields.forEach(function(field) {
        let el = document.getElementById(field.id);
        if (!el.value.trim()) {
            el.style.border = "2px solid red";
            missingFields.push(field.name);
            valid = false;
        } else {
            el.style.border = "";
        }
    });

      if (!valid) {
          e.preventDefault();
          alert("Please fill in the following fields:\n- " + missingFields.join("\n- "));
          return; // stop here if invalid
      }

      // Confirmation prompt before submission
      if (!confirm("Are you sure to Mark this salary slip as PAID?")) {
          e.preventDefault(); // cancel submission if user clicks "Cancel"
      }
  });

  // Remove red border when user changes the field
  ["paid_by", "paid_date", "payment_type", "payment_through"].forEach(function(id) {
      document.getElementById(id).addEventListener("change", function() {
          if (this.value.trim()) {
              this.style.border = "";
          }
      });
  });

  </script>


	<script>
		$(function() {
			$('#interp').multiselect({
          includeSelectAllOption: false,
          numberDisplayed: 1,
          enableFiltering: true,
          enableCaseInsensitiveFiltering: true,
          nonSelectedText: 'Select an Interpreter',
        });

        // clean modal after close
        $('#paid_salary_slip_modal').on('hidden.bs.modal', function () {
            let form = $(this).find('#frm_pay_salary')[0];
            form.reset();
            $(form).find('input, select').css('border', '');

            $("#payment_type").val(""); // reset payment type
            $("#payment_through").val(""); // reset payment through
            $(".payment_through_wrap").addClass('hide'); // hide the dropdown wrapper

        });

      });

  function manage_salary_slip(element) {
    $("#slip_id").val($(element).attr("data-id"));
    $("#slip_number").val($(element).attr("data-invoice"));
    $("#total_salary").val($(element).attr("data-salary"));
    $('#salary_slip_modal').modal('show');
  }

  function mark_paid_modal(element) {
      let modal = $('#paid_salary_slip_modal');
      let form = modal.find('#frm_pay_salary')[0];

      // Reset the form values
      form.reset();

      // Remove any red borders from previous validation
      $(form).find('input, select').css('border', '');

      // Set new data from the clicked button
      $("#paid_slip_id").val($(element).attr("data-id"));
      $("#paid_slip_number").val($(element).attr("data-invoice"));
      $("#paid_total_salary").val($(element).attr("data-salary"));

      // Show the modal
      modal.modal('show');
  }

  function myFunction() {
    var append_url = "<?php echo basename(__FILE__) . "?1"; ?>";
    var paid_status = $("#is_paid").is(':checked') ? 1 : '';
    if (paid_status) {
        append_url += '&is_paid=' + paid_status;
    }
    var interp = $("#interp").val();
    if (interp) {
      append_url += '&interp=' + interp;
    }
    var get_dated = $("#get_dated").val();
    if (get_dated) {
      append_url += '&get_dated=' + get_dated;
    }
    var status = $("#status").val();
    if (status) {
      append_url += '&status=' + status;
    }
    window.location.href = append_url;
  }
</script>

</html>