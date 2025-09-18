<?php
//php mailer library
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}

include 'db.php';
include 'class.php';
include 'inc_functions.php';

$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=216 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_invoice = $_SESSION['is_root'] == 1 || in_array(224, $get_actions);
$action_edit_invoice = $_SESSION['is_root'] == 1 || in_array(221, $get_actions);
$action_delete_invoice = $_SESSION['is_root'] == 1 || in_array(220, $get_actions);
$action_restore_invoice = $_SESSION['is_root'] == 1 || in_array(222, $get_actions);
$action_receive_payment = $_SESSION['is_root'] == 1 || in_array(238, $get_actions);
$action_receive_partial_payment = $_SESSION['is_root'] == 1 ||  in_array(239, $get_actions);
$action_make_credit_note = $_SESSION['is_root'] == 1 ||	 in_array(240, $get_actions);
$action_create_invoice = $_SESSION['is_root'] == 1 || in_array(218, $get_actions);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Invoice Management</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.css" />
	<link rel="stylesheet" type="text/css" href="css/util.css" />
	<link rel="icon" type="image/png" href="img/logo.png">
	<style>
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
	include_once('function.php');
	$table = 'post_format';
	$company_id = $_GET['company'];

	if (isset($_GET['del_id']) && !empty($_GET['del_id'])) {

		$allowed_type_idz = "220";
		//Check if user has current action allowed
		if ($_SESSION['is_root'] == 0) {
			$get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];

			if (empty($get_page_access)) {
				die("<center><h2 class='text-center text-danger'>You do not have access to this action!<br>Kindly contact admin for further process.</h2></center>");
			}
		}
		$id = mysqli_escape_string($con, $_GET['del_id']);
		//$by = $_SESSION['UserName'];
		$dated = date('Y-m-d');

		// Getting Existing records
		$db_invoice_info = $acttObj->read_specific(
			"count(id) as total_rec, voucher_no, voucher, description, due_date, total_amount, company_id, payment_status, received_amount",
			"income_invoices",
			"id = '" . $id . "'"
		);

		if ($db_invoice_info['total_rec'] > 0) {

			/* Insertion Query to Accounts: Income & Receivable Table
				- account_income : As Debit (balance - DueAmount)
				- account_receivable : As Credit (balance - DueAmount)
			*/

			$company_name_abrv = $acttObj->read_specific("company_name", "income_company", " id = " . $db_invoice_info['company_id'])['company_name'];
			$description = "[Deleted][Manual Invoice]  Company: " . $company_name_abrv . ", Invoice No: " . $db_invoice_info['voucher_no'];

			if ($db_invoice_info['total_amount'] > 0) {

				// getting balance amount
				$res = getCurrentBalances($con);

				// Getting New Voucher Counter
				$voucher_counter = getNextVoucherCount('JV');

				// Updating the new Voucher Counter
				updateVoucherCounter('JV', $voucher_counter);

				$voucher = 'JV-' . $voucher_counter;

				// Insertion in tbl account_income
				$insert_data = array(
					'invoice_no' => $db_invoice_info['voucher_no'],
					'voucher' => $voucher,
					'dated' => date('Y-m-d'),
					'company' => $company_name_abrv,
					'description' => $description,
					'debit' => $db_invoice_info['total_amount'],
					'balance' => ($res['balance'] - $db_invoice_info['total_amount']),
					'posted_by' => $_SESSION['userId'],
					'tbl' => 'income_invoices'
				);

				$jv_voucher = insertAccountIncome($insert_data);

				// Insertion in tbl account_receivable
				$insert_data_rec = array(
					'voucher' => $voucher,
					'invoice_no' => $db_invoice_info['voucher_no'],
					'dated' => date('Y-m-d'),
					'company' => $company_name_abrv,
					'description' => $description,
					'credit' => $db_invoice_info['total_amount'],
					'balance' => ($res['recv_balance'] - $db_invoice_info['total_amount']),
					'posted_by' => $_SESSION['userId'],
					'tbl' => 'income_invoices'
				);

				$re_result = insertAccountReceivable($insert_data_rec);
				//$voucher = $re_result['voucher'];
				$new_voucher_id = $re_result['new_voucher_id'];

				//$credit_amount = $db_invoice_info['total_amount'];

				if ($db_invoice_info['payment_status'] != 'unpaid') {
					// it will update the journal record for future, as we are not inserting any reversal record for specific parital rAmount
					updateJournalLedgerStatus('deleted', 1, $db_invoice_info['voucher_no']);
				}
			} // end if record exists

			$result = $acttObj->db_query("UPDATE income_invoices SET received_amount = 0, payment_status = 'unpaid', deleted_flag = 1, deleted_by = '" . $_SESSION['UserName'] . "', deleted_date = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $id);

			$res = $acttObj->db_query("UPDATE paid_income_invoices SET is_deleted = 1 WHERE is_partial_payment = 1 AND income_invoice_id = " . $id);

			$acttObj->insert('daily_logs', ['action_id' => 50, 'user_id' => $_SESSION['userId'], 'details' => "Manual Invoice No: " . $db_invoice_info['voucher_no']]);

			if ($result) {
				echo '<script>alert("Invoice successfully trashed!");
				window.location.href="manual_invoice.php";</script>';
			} else {
				echo '<script>alert("Failed to trash this invoice!");</script>';
			}
		}
	}

	// Restore
	if (isset($_GET['activate_id'])) {
		$allowed_type_idz = "222";

		//Check if user has current action allowed
		if ($_SESSION['is_root'] == 0) {
			$get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
			if (empty($get_page_access)) {
				die("<center><h2 class='text-center text-danger'>You do not have access to this action!<br>Kindly contact admin for further process.</h2></center>");
			}
		}

		$id = (int) $_GET['activate_id'];

		// Getting Existing records
		$db_invoice_info = $acttObj->read_specific(
			"count(id) as total_rec, voucher_no, voucher, description, due_date, total_amount, company_id",
			"income_invoices",
			" id = '" . $id . "'"
		);

		if ($db_invoice_info['total_rec'] > 0) {

			/* Insertion Query to Accounts: Income & Receivable Table
				- account_income : As Credit (balance + DueAmount)
				- account_receivable : As Debit (balance + DueAmount)
				- journal_ledger : update record to credit_note
			*/

			$company_name_abrv = $acttObj->read_specific("company_name", "income_company", " id = " . $db_invoice_info['company_id'])['company_name'];
			$description = '[Restored][Manual Invoice] Company: ' . $company_name_abrv . ', Invoice No: ' . $db_invoice_info['voucher_no'];

			if ($db_invoice_info['total_amount'] > 0) {

				// getting balance amount
				$res = getCurrentBalances($con);

				// Getting New Voucher Counter
				$voucher_counter = getNextVoucherCount('JV');

				// Updating the new Voucher Counter
				updateVoucherCounter('JV', $voucher_counter);

				$voucher = 'JV-' . $voucher_counter;

				// Insertion in tbl account_income
				$insert_data = array(
					'voucher' => $voucher,
					'invoice_no' => $db_invoice_info['voucher_no'],
					'dated' => date('Y-m-d'),
					'company' => $company_name_abrv,
					'description' => $description,
					'credit' => $db_invoice_info['total_amount'],
					'balance' => ($res['balance'] + $db_invoice_info['total_amount']),
					'posted_by' => $_SESSION['userId'],
					'tbl' => 'income_invoices'
				);

				$jv_voucher = insertAccountIncome($insert_data);

				// Insertion in tbl account_receivable
				$insert_data_rec = array(
					'voucher' => $voucher,
					'invoice_no' => $db_invoice_info['voucher_no'],
					'dated' => date('Y-m-d'),
					'company' => $company_name_abrv,
					'description' => $description,
					'debit' => $db_invoice_info['total_amount'],
					'balance' => ($res['recv_balance'] + $db_invoice_info['total_amount']),
					'posted_by' => $_SESSION['userId'],
					'tbl' => 'income_invoices'
				);

				$re_result = insertAccountReceivable($insert_data_rec);
				//$voucher = $re_result['voucher'];
				$new_voucher_id = $re_result['new_voucher_id'];
			} // end if record exists

			$result = $acttObj->db_query("UPDATE income_invoices SET deleted_flag = 0, restore_flag = 1, restore_by = '" . $_SESSION['UserName'] . "', restore_time = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $id);

			$acttObj->insert('daily_logs', ['action_id' => 48, 'user_id' => $_SESSION['userId'], 'details' => "Manual Invoice No: " . $db_invoice_info['voucher_no']]);

			if ($result) {
				echo "<script>alert('Invoice restored successfully.'); 
				window.location.href = 'manual_invoice.php';</script>";
			} else {
				echo "Error: " . mysqli_error($con);
			}
		}
	}

	include 'nav2.php';

	?>
	<!-- end of sidebar -->

	<style>
		div#DataTables_Table_0_filter {
			text-align: right;
			margin-bottom: 10px;
		}

		.multiselect_orgs .btn-group {
			width: 100%;
		}

		.multiselect {
			min-width: 100%;
		}

		span.multiselect-selected-text {
			text-align: left;
			float: left;
		}

		.multiselect_orgs .btn .caret {
			margin: 8px 0;
			float: right;
		}

		.multiselect-container {
			max-height: 400px;
			overflow-y: auto;
			max-width: 100%;
		}

		.table-condensed>tbody>tr>td,
		.table-condensed>tbody>tr>th,
		.table-condensed>thead>tr>td,
		.table-condensed>thead>tr>th {
			font-size: 14px;
		}

		/* table.table-condensed>tbody>tr>th,
		table.table-condensed>tbody>tr>td {
			padding: 5px !important;
		} 
		.table-condensed>tbody>tr>td, .table-condensed>tbody>tr>th, .table-condensed>thead>tr>td, .table-condensed>thead>tr>th {
			font-size: 13px;
		}*/
	</style>
	<script>
		function myFunction() {
			var inv_status = document.getElementById("inv_status").value;
			if (inv_status == 'all') {
				inv_status = 'all';
			} else {
				inv_status = inv_status;
			}
			var p_status = document.getElementById("p_status").value;
			if (p_status == 'all') {
				p_status = '';
			} else {
				p_status = p_status;
			}
			var company = document.getElementById("company").value;
			if (company == 'all') {
				company = '';
			} else {
				company = company;
			}

			var inv = document.getElementById("inv_no").value;
			if (!inv) {
				inv = inv;
			}

			var fd = document.getElementById("from_date").value;
			if (!fd) {
				fd = fd;
			}
			var td = document.getElementById("to_date").value;
			if (!td) {
				td = td;
			}
			// var vhr = document.getElementById("voucher").value;
			// if (!vhr) {
			// 	vhr = vhr;
			// }

			window.location.href = "?inv=" + inv + "&status=" + inv_status + "&company=" + company + "&fd=" + fd + "&td=" + td + "&pstatus=" + p_status;

		}
	</script>

	<section class="container-fluid">
		<div class="row">
			<div class="col-sm-12" style="margin-top: 20px;">
				<center>
					<a href="manual_invoice.php" style="padding: 12px;text-decoration:none;" class="alert-link h4 bg-primary">Manual Invoices</a>
				</center>
				<div class="pull-right m-b-30">
					<?php if ($action_create_invoice) { ?>
						<a href="javascript:void(0)" onclick="popupwindow('create_manual_invoice.php?add_post', 'Create Invoice', 1250, 730);" class="btn btn-primary">
							<i class="glyphicon glyphicon-plus"></i> Create Invoice
						</a>
					<?php } ?>
				</div>
			</div>

			<div class="">
				<div class="form-group col-sm-2">
					<label>Invoice/Track#</label>
					<input type="text" class="form-control" name="inv_no" id="inv_no" onchange="myFunction()" placeholder="Invoice/Track#" onchange="myFunction()" value="<?php echo ($_GET['inv']) ? $_GET['inv'] : ''; ?>">
				</div>
				<!-- <div class="form-group col-sm-2">
					<label>Voucher</label>
					<input type="text" class="form-control" name="voucher" id="voucher" onchange="myFunction()" placeholder="Voucher" onchange="myFunction()" value="<?php echo ($_GET['vhr']) ? $_GET['vhr'] : ''; ?>">
				</div> -->

				<div class="form-group col-sm-2 multiselect_orgs">
					<label>Company</label>
					<?php
					$companies_list = $acttObj->full_fetch_array("SELECT id, company_name FROM income_company WHERE status = 1");
					?>
					<select class="form-control" id="company" name="company" onchange="myFunction()">
						<option value="all">All</option>
						<?php if (count($companies_list) > 0) { ?>
							<?php foreach ($companies_list as $company) { ?>
								<option value="<?php echo $company['id']; ?>" <?php echo ($company['id'] == $company_id) ? 'selected' : ''; ?>>
									<?php echo $company['company_name']; ?>
								</option>
							<?php } // end foreach 
							?>
						<?php } ?>
					</select>
				</div>
				<div class="form-group col-sm-2">
					<label>Due Date (from)</label>
					<input type="date" class="form-control" name="from_date" id="from_date" onchange="myFunction()" placeholder="From Date" onchange="myFunction()" value="<?php echo ($_GET['fd']) ? $_GET['fd'] : '' ?>">
				</div>
				<div class="form-group col-sm-2">
					<label>Due Date (to)</label>
					<input type="date" class="form-control" name="to_date" id="to_date" onchange="myFunction()" placeholder="To Date" onchange="myFunction()" value="<?php echo ($_GET['td']) ? $_GET['td'] : '' ?>">
				</div>
				<div class="form-group col-sm-2">
					<label>Status</label>
					<select class="form-control" name="inv_status" id="inv_status" onchange="myFunction()">
						<option value="all" <?php echo ($_GET['status'] == 'all') ? "selected" : ""; ?>>All</option>
						<option value="active" <?php echo (!isset($_GET['status']) || $_GET['status'] == 'active') ? "selected" : ""; ?>>Active</option>
						<option value="deleted" <?php echo ($_GET['status'] == 'deleted') ? "selected" : ""; ?>>Trashed</option>
						<option value="credit_note" <?php echo ($_GET['status'] == 'credit_note') ? "selected" : ""; ?>>Credit Note</option>
					</select>
				</div>
				<div class="form-group col-sm-2">
					<label>Payment Status</label>
					<select class="form-control" name="p_status" id="p_status" onchange="myFunction()">
						<option value="all" <?php echo ($_GET['pstatus'] == '') ? "selected" : ""; ?>>All</option>
						<option value="unpaid" <?php echo ($_GET['pstatus'] == 'unpaid') ? "selected" : ""; ?>>Unpaid</option>
						<option value="full_paid" <?php echo ($_GET['pstatus'] == 'full_paid') ? "selected" : ""; ?>>Full Paid</option>
						<option value="partial" <?php echo ($_GET['pstatus'] == 'partial') ? "selected" : ""; ?>>Partial</option>
						<option value="full_partial" <?php echo ($_GET['pstatus'] == 'full_partial') ? "selected" : ""; ?>>Full Partial</option>
					</select>
				</div>
			</div>

			<!-- Filters -->
			<div class="filters">

			</div>
			<!-- Filters End -->

		</div><br>

		<div class="row">
			<div class="col-md-12">
				<table class="table table-striped table-hover table-bordered table-condensed" id="invoiceList">
					<thead class="bg-info">
						<tr>
							<th scope="col">Invoice No/Track#</th>
							<!-- <th scope="col">Voucher</th> -->
							<th scope="col">Company Name</th>
							<th scope="col">Amount</th>
							<th scope="col">Received</th>
							<th scope="col">Balance</th>
							<th scope="col">Due Date</th>
							<th scope="col">Payment Status</th>
							<th scope="col">Dated</th>
							<th scope="col">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php

						// Modify the query to conditionally filter based on the 'deleted' flag
						$query = "SELECT i.id, i.voucher_no, i.voucher, i.due_date, i.total_amount, i.received_amount, i.deleted_flag, i.is_paid, i.commit, i.payment_status, i.created_at, c.company_name,
							(SELECT is_partial_payment FROM paid_income_invoices WHERE income_invoice_id = i.id GROUP BY income_invoice_id) as is_partial_payment,
							(SELECT SUM(total_amount) FROM paid_income_invoices WHERE is_partial_payment = 0 AND income_invoice_id = i.id AND is_deleted = 0) as full_paid_amount,
							(SELECT SUM(total_amount) FROM paid_income_invoices WHERE is_partial_payment = 1 AND income_invoice_id = i.id AND is_deleted = 0) as total_partial_amount
							FROM income_invoices i
							LEFT JOIN income_company c ON i.company_id = c.id
							WHERE 1 ";

						// Add condition for the deleted status
						$query .= (!isset($_GET['status'])) ? " AND i.deleted_flag = 0 AND i.commit = 1" : "";

						$query .= ($_GET['status'] == 'deleted') ? " AND i.deleted_flag = 1" : "";

						$query .= ($_GET['status'] == 'active') ? " AND i.deleted_flag = 0 AND i.commit = 1" : "";

						$query .= ($_GET['status'] == 'credit_note') ? " AND i.commit = 0" : "";

						$query .= ($_GET['company']) ? " AND i.company_id = " . $company_id : "";

						$query .= ($_GET['inv']) ? " AND voucher_no = '" . $_GET['inv'] . "'" : "";

						$query .= ($_GET['vhr']) ? " AND voucher = '" . $_GET['vhr'] . "'" : "";

						$query .= ($_GET['pstatus']) ? " AND i.payment_status = '" . $_GET['pstatus'] . "'" : "";

						$query .= ($_GET['fd']) ? " AND i.due_date BETWEEN '" . $_GET['fd'] . "' AND '" . $_GET['td'] . "'" : "";

						$query .= " ORDER BY i.id DESC";
						$result = mysqli_query($con, $query);

						while ($row = mysqli_fetch_assoc($result)) {
						?>
							<tr style="<?php echo ($row['deleted_flag'] == 1) ? 'background: #ff00001f;' : ''; ?>
								<?php echo ($row['commit'] == 0) ? 'background: #dfa4212b;' : ''; ?>"

								<?php echo ($row['deleted_flag'] == 1) ? 'title="This invoice is Deactivated!"' : ''; ?>
								<?php echo ($row['commit'] == 0) ? 'title="Invoice cancelled (credit note)"' : ''; ?>>

								<td><?= $row['voucher_no']; ?></td>
								<!-- <td><?= $row['voucher']; ?></td> -->
								<td><?= $row['company_name'] ?></td>
								<td><?= $misc->numberFormat_fun($row['total_amount']); ?></td>
								<td>
									<?= $misc->numberFormat_fun($row['received_amount']); ?>
								</td>
								<td><?= $misc->numberFormat_fun(($row['total_amount'] - $row['received_amount'])); ?></td>
								<td><?= $misc->dated($row['due_date']) ?></td>
								<td>
									<?php
									if ($row['payment_status'] == 'unpaid') {
										$label_class = 'danger';
									} else if ($row['payment_status'] == 'partial') {
										$label_class = 'warning';
									} else if ($row['payment_status'] == 'full_paid') {
										$label_class = 'success';
									} else if ($row['payment_status'] == 'full_partial') {
										$label_class = 'success';
									}
									?>
									<label class=" label label-<?= $label_class; ?>">
										<?= ucwords(str_replace('_', ' ', $row['payment_status'])); ?>
									</label>
								</td>
								<td><?= $misc->dated($row['created_at']); ?></td>
								<td>
									<?php if ($row['commit'] == 1) { ?>
										<?php if ($action_edit_invoice && $row['is_paid'] == 0) { ?>
											<a href="javascript:void(0)" onclick="popupwindow('edit_invoice.php?id=<?= $row['id'] ?>', 'Edit Invoice', 1250, 730);" class="btn btn-default btn-xs" title="Edit Invoice">
												<i class="fa fa-edit"></i>
											</a>
										<?php } ?>

										<?php if ($action_view_invoice) { ?>
											<a href="javascript:void(0)" onclick="popupwindow('invoice_view.php?id=<?= $row['id'] ?>', 'View Invoice', 1100, 900);" class="btn btn-default btn-xs" title="View Invoice">
												<i class="fa fa-eye"></i>
											</a>
										<?php } ?>

										<?php if ($action_make_credit_note && $row['deleted_flag'] == 0) { ?>
											<a href="javascript:void(0)" onclick="popupwindow('invoice_credit_note.php?id=<?= $row['id'] ?>', 'Create Credit Note', 1000, 1000);" class="btn btn-default btn-xs" title="Create Credit Note">
												<i class="fa fa-exclamation-circle text-primary"></i>
											</a>
										<?php } ?>

										<?php if ($action_receive_payment && $row['is_paid'] == 0 && $row['deleted_flag'] == 0 && $row['payment_status'] == 'unpaid') { ?>
											<a href="javascript:void(0)" onclick="popupwindow('manual_invoice_receive_amount.php?prt=full&id=<?= $row['id'] ?>', 'Receive Payment', 800, 450);" class="btn btn-success btn-xs" title="Receive Payment">
												<i class="fa fa-dollar" aria-hidden="true"></i>
											</a>
										<?php } ?>

										<?php if ($action_receive_partial_payment && $row['deleted_flag'] == 0 && ($row['payment_status'] == 'unpaid' || $row['payment_status'] == 'partial')) { ?>

											<a href="javascript:void(0)" onclick="popupwindow('manual_invoice_receive_amount.php?prt=partial&id=<?= $row['id'] ?>', 'Receive Partial Payment', 800, 600);" class="btn btn-info btn-xs" title="Receive Partial Payment">
												<i class="fa fa-money" aria-hidden="true"></i>
											</a>
										<?php } ?>

										<?php if ($action_delete_invoice && $row['deleted_flag'] == 0 && $row['payment_status'] == 'unpaid') { ?>
											<?php //if ($row['is_paid'] == 0) { 
											?>
											<a class="btn btn-danger btn-xs" onclick="return confirm_delete();" href="manual_invoice.php?del_id=<?= $row['id'] ?>" title="Delete">
												<i class="fa fa-trash"></i>
											</a>
										<?php } ?>
										<?php if ($action_restore_invoice && $row['deleted_flag'] == 1 && $row['payment_status'] == 'unpaid') { ?>
											<a class="btn btn-default btn-xs" onclick="return confirm_restore();" href="manual_invoice.php?activate_id=<?= $row['id'] ?>" title="Restore">
												<i class="fa fa-refresh text-success"></i>
											</a>
										<?php } ?>
									<?php } else { // end if commit = 1 
									?>
										<?php if ($action_create_invoice) { ?>
											<a href="javascript:void(0)" onclick="popupwindow('recreate_manual_invoice.php?id=<?= $row['id'] ?>', 'Create Invoice', 1250, 730);" class="btn btn-default btn-xs" title="<?php echo ($row['commit'] == 1) ? 'Create Invoice' : 'Re-create Invoice' ?>">
												<i class="fa fa-retweet"></i>
											</a>
										<?php } ?>

										<?php if ($action_make_credit_note) { ?>
											<a href="javascript:void(0)" onclick="popupwindow('invoice_credit_note.php?id=<?= $row['id'] ?>', 'Create Credit Note', 1000, 1000);" class="btn btn-default btn-xs" title="<?php echo ($row['commit'] == 1) ? 'Create Credit Note' : 'View Credit Note' ?>">
												<i class="fa fa-exclamation-circle <?php echo ($row['commit'] == 1) ? 'text-primary' : 'text-danger' ?>"></i>
											</a>
										<?php } ?>

									<?php } // end if commit = 0 
									?>

								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>

		</div>

		<style>
			.dt-button {
				padding: 0px 5px !important;
			}

			div.dt-button-collection .active:after {
				position: absolute;
				top: 50%;
				margin-top: -10px;
				right: 1em;
				display: inline-block;
				content: "\2713";
				color: inherit;
			}
		</style>

		<link href="https://cdn.datatables.net/2.3.1/css/dataTables.dataTables.min.css" rel="stylesheet">
		<link href="https://cdn.datatables.net/buttons/3.2.3/css/buttons.dataTables.min.css" rel="stylesheet">

		<script src="js/jquery-1.11.3.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

		<script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/fixedColumns.dataTables.js"></script>

		<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>

		<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
		<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js" type="text/javascript"></script>
		<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
		<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
		<script src="https://cdn.tiny.cloud/1/1cuurlhdv50ndxckpjk52wu6i868lluhxe90y7xesmawusin/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>


		<?php if (isset($_GET['add_post']) || isset($_GET['edit_id'])) { ?>

			<script type="text/javascript">
				tinymce.init({
					selector: "#mytextarea",
					height: 400,
					plugins: 'print preview   searchreplace autolink autosave save directionality  visualblocks visualchars fullscreen image link media  template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount   imagetools textpattern noneditable help  ',
					toolbar: 'undo redo | link image | code',
					image_title: true,
					automatic_uploads: true,
					file_picker_types: 'image media',
					file_picker_callback: function(cb, value, meta) {
						var input = document.createElement('input');
						input.setAttribute('type', 'file');
						input.setAttribute('accept', 'image/*');
						input.onchange = function() {
							var file = this.files[0];
							var reader = new FileReader();
							reader.onload = function() {
								var id = 'blobid' + (new Date()).getTime();
								var blobCache = tinymce.activeEditor.editorUpload.blobCache;
								var base64 = reader.result.split(',')[1];
								var blobInfo = blobCache.create(id, file, base64);
								blobCache.add(blobInfo);
								cb(blobInfo.blobUri(), {
									title: file.name
								});
							};
							reader.readAsDataURL(file);
						};
						input.click();
					}
				});

				function changable() {
					var value = document.getElementById("selector").value;
					if (value == 'all') {
						$('#div_for_all').css('display', 'contents');
						$('#div_lang').css('display', 'none');
						$('#div_city').css('display', 'none');
					} else if (value == 'sc') {
						$('#div_for_all').css('display', 'none');
						$('#div_lang').css('display', 'none');
						$('#div_city').css('display', 'contents');
					} else if (value == 'sl') {
						$('#div_for_all').css('display', 'none');
						$('#div_lang').css('display', 'contents');
						$('#div_city').css('display', 'none');
					} else {
						$('#div_for_all').css('display', 'none');
						$('#div_lang').css('display', 'contents');
						$('#div_city').css('display', 'contents');
					}
				}
			</script>
		<?php } ?>
		<script>
			$(function() {
				$('#company').multiselect({
					includeSelectAllOption: false,
					numberDisplayed: 1,
					enableFiltering: true,
					enableCaseInsensitiveFiltering: true
				});
			});

			function confirm_delete() {
				var result = confirm("Are you sure to delete this record ?");
				if (result == true) {
					return true;
				} else {
					return false;
				}
			}

			function confirm_restore() {
				var result = confirm("Are you sure to restore this record ?");
				if (result == true) {
					return true;
				} else {
					return false;
				}
			}

			$(document).ready(function() {
				$('#invoiceList').DataTable({
					paging: true,
					pageLength: 50,
					lengthMenu: [
						[10, 25, 50, 100, -1],
						[10, 25, 50, 100, "All"]
					],
					order: [
						[0, 'desc']
					],
					dom: 'Blfrtip',
					layout: {
						topStart: {
							buttons: ['colvis']
						}
					},
					buttons: [{
							extend: 'colvis',
							text: '<i class="fa fa-bars"></i>',
							titleAttr: 'Show/Hide Columns',
							className: 'myShowHideActive',
							columnText: function(dt, idx, title) {
								return (idx + 1) + ': ' + title;
							}
						},
						{
							extend: 'copyHtml5',
							text: '<i class="fa fa-files-o"></i>',
							titleAttr: 'Copy',
							exportOptions: {
								columns: ':not(:last-child)',
							}
						},
						{
							extend: 'excelHtml5',
							text: '<i class="fa fa-file-excel-o"></i>',
							titleAttr: 'Excel',
							exportOptions: {
								columns: ':not(:last-child)',
							}
						},
						{
							extend: 'csvHtml5',
							text: '<i class="fa fa-file-text-o"></i>',
							titleAttr: 'CSV',
							exportOptions: {
								columns: ':not(:last-child)',
							}
						},
						{
							extend: 'pdfHtml5',
							text: '<i class="fa fa-file-pdf-o"></i>',
							titleAttr: 'PDF',
							exportOptions: {
								columns: ':not(:last-child)',
							}
						},
						{
							extend: 'print',
							text: '<i class="fa fa-print"></i>',
							titleAttr: 'Print',
							customize: function(win) {
								$(win.document.body)
									.find('h1').css({
										position: 'relative',
										top: '0px',
										left: '38%'
									}).addClass('fs-14');

								$(win.document.body).find('#invoiceList').css('top', '0px !important');

								$(win.document.body)
									.find('th:nth-child(4), td:nth-child(4), th:nth-child(5), td:nth-child(5)')
									.css('text-align', 'center');
							},
							exportOptions: {
								columns: ':not(:last-child)',
							}
						}
					],
					initComplete: function() {
						// Move length and filter into the same row
						var length = $('#invoiceList_length');
						var filter = $('#invoiceList_filter');

						var topRow = $('<div class="row" style="margin-bottom: 10px;"></div>');
						var leftCol = $('<div class="col-sm-6"></div>').append(length);
						var rightCol = $('<div class="col-sm-6 text-right"></div>').append(filter);
						topRow.append(leftCol).append(rightCol);

						// Insert it before the DataTables wrapper
						$('.dataTables_wrapper').prepend(topRow);

						// Move info and pagination into same row below
						var info = $('#invoiceList_info');
						var paginate = $('#invoiceList_paginate');

						var bottomRow = $('<div class="row" style="margin-top: 10px;"></div>');
						var bottomLeft = $('<div class="col-sm-6"></div>').append(info);
						var bottomRight = $('<div class="col-sm-6 text-right"></div>').append(paginate);
						bottomRow.append(bottomLeft).append(bottomRight);

						// Place after the table
						$('.dataTables_wrapper').append(bottomRow);
					}
				});


			});
		</script>
</body>

</html>