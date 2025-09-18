<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);

if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}

include 'db.php';
include 'class.php';
include_once('function.php');
include_once('inc_functions.php');

$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=223 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);

$action_view_invoice = $_SESSION['is_root'] == 1 || in_array(229, $get_actions);
$action_export_to_excel = $_SESSION['is_root'] == 1 || in_array(229, $get_actions);
$action_receive_payment = $_SESSION['is_root'] == 1 || in_array(230, $get_actions);
$action_receive_partial_payment = $_SESSION['is_root'] == 1 || in_array(231, $get_actions);
$action_delete_invoice = $_SESSION['is_root'] == 1 || in_array(232, $get_actions);
$action_make_credit_note = $_SESSION['is_root'] == 1 || in_array(233, $get_actions);
$action_undo_payments = $_SESSION['is_root'] == 1 || in_array(234, $get_actions);


$from_date = SafeVar::GetVar('from_date', '');
$to_date = SafeVar::GetVar('to_date', '');

$pstatus = SafeVar::GetVar('pstatus', '');
$status = SafeVar::GetVar('status', '');

$org = SafeVar::GetVar('org', '');
$inov = SafeVar::GetVar('inov', '');

$multInvoicNo = $_GET['multInvoiceNo'];
$proceed = $_GET['proceed'];

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;


if (isset($_GET['action']) && $_GET['action'] == 'CancelCollectiveInvoice') {

	// $allowed_type_idz = "232";
	// if ($_SESSION['is_root'] == 0) {
	// 	$get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
	// 	if (empty($get_page_access)) {
	// 		die("<center><h2 class='text-center text-danger'>You do not have access to <u>Add Payment</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
	// 	}
	// }

	$multInvoicNo = $_GET['multInvoiceNo'];

	$row = $acttObj->read_specific("*", "mult_inv", "m_inv = '$multInvoicNo' ");

	/* Insertion Query to Accounts: Income & Receivable Table
		- account_income : As Debit (balance - DueAmount)
		- account_receivable : As Credit (balance - DueAmount)
	*/

	$current_date = date("Y-m-d");
	$credit_amount = $row['mult_amount'];
	$description = '[Cancelled][Collective Invoice] Company: ' . $row['comp_abrv'] . ", Invoice# " . $row['m_inv'];

	// Checking if record already exists
	$parameters = " invoice_no = '" . $row['m_inv'] . "' AND dated = '" . $current_date . "' AND company = '" . $row['comp_abrv'] . "' AND debit = '" . $row['mult_amount'] . "'";
	$chk_exist = 0; //isIncomeRecordExists($parameters);

	if ($chk_exist < 1 && $credit_amount > 0) {

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
			'invoice_no' => $row['m_inv'],
			'dated' => $current_date,
			'company' => $row['comp_abrv'],
			'description' => $description,
			'debit' => $credit_amount,
			'balance' => ($res['balance'] - $credit_amount),
			'posted_by' => $_SESSION['userId'],
			'tbl' => 'mult_inv'
		);

		$jv_voucher = insertAccountIncome($insert_data);

		if ($row['rAmount'] == 0 || $row['rAmount'] < 1) {
			// Insertion in tbl account_receivable
			$insert_data_rec = array(
				'voucher' => $voucher,
				'invoice_no' => $row['m_inv'],
				'dated' => $current_date,
				'company' => $row['comp_abrv'],
				'description' => $description,
				'credit' => $credit_amount,
				'balance' => ($res['recv_balance'] - $credit_amount),
				'posted_by' => $_SESSION['userId'],
				'tbl' => 'mult_inv'
			);
			$re_result = insertAccountReceivable($insert_data_rec);
			$new_voucher_id = $re_result['new_voucher_id'];
		}

		// check if invoice is already paid
		if ($row['rAmount'] > 0) {
			//$chk_payment_type = $row['payment_type'];
			updateJournalLedgerStatus('deleted', 1, $row['m_inv']); // status, is_receviable, invoice_no

		}
	} // end if record exists

	$check_exist_records = $acttObj->read_specific(
		'COUNT(id) as counter',
		'partial_amounts',
		'order_id = "' . $multInvoicNo . '" AND tbl = "mult_inv" AND status = 1'
	)['counter'];

	if ($check_exist_records > 0) {
		$acttObj->db_query("UPDATE partial_amounts SET status = 0 WHERE order_id = '" . $multInvoicNo . "' AND tbl = 'mult_inv' AND status = 1");
	}

	$update_int = $acttObj->db_query("UPDATE mult_inv SET rAmount = 0, status = '', is_deleted = 1, deleted_by = '" . $_SESSION['UserName'] . "', deleted_on = '" . date('Y-m-d H:i:s') . "' WHERE m_inv = '" . $multInvoicNo . "'");

	if ($update_int) {

		$update_int = $acttObj->db_query("UPDATE interpreter SET multInvoicNo = '', multInv_flag = 0 WHERE multInvoicNo = '" . $multInvoicNo . "'");
		$update_tele = $acttObj->db_query("UPDATE telephone SET multInvoicNo = '', multInv_flag = 0 WHERE multInvoicNo = '" . $multInvoicNo . "'");
		$update_trans = $acttObj->db_query("UPDATE translation SET multInvoicNo = '', multInv_flag = 0 WHERE multInvoicNo = '" . $multInvoicNo . "'");

		$acttObj->db_query("UPDATE comp_credit SET deleted_flag = 1, deleted_by = '" . $_SESSION['UserName'] . "', deleted_date = '" . date('Y-m-d') . "' WHERE invoiceNo = '" . $multInvoicNo . "' AND mult_inv_flag = 1");

		$acttObj->db_query("UPDATE bz_credit SET deleted_flag = 1, deleted_by = '" . $_SESSION['UserName'] . "', deleted_date = '" . date('Y-m-d') . "' WHERE invoiceNo = '" . $multInvoicNo . "' AND mult_inv_flag = 1");

		$acttObj->insert("daily_logs", array("action_id" => 52, "user_id" => $_SESSION['userId'], "details" => "Mult. Invoice#: " . $multInvoicNo));

		echo 1; // success message
		exit;
	} else {
		echo 0; // error message
		exit;
	}
}
?>

<?php

if (isset($_GET['action']) && $_GET['action'] == 'UndoInvoicePayment') {

	// $allowed_type_idz = "234";
	// if ($_SESSION['is_root'] == 0) {
	// 	$get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
	// 	if (empty($get_page_access)) {
	// 		die("<center><h2 class='text-center text-danger'>You do not have access to <u>Add Payment</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
	// 	}
	// }

	$multInvoicNo = $_GET['multInvoiceNo'];

	$row = $acttObj->read_specific("*", "mult_inv", "m_inv = '$multInvoicNo' ");
	
	/* Insertion Query to Accounts: Income & Receivable Table
		- account_income : As Debit (balance - DueAmount)
		- account_receivable : As Credit (balance - DueAmount)
	*/

	$current_date = date("Y-m-d");
	$credit_amount = $row['rAmount'];
	$description = '[Undo Payment][Collective Invoice] Company: ' . $row['comp_abrv'] . ", Invoice# " . $row['m_inv'];

	// Checking if record already exists
	$parameters = " invoice_no = '" . $row['m_inv'] . "' AND dated = '" . $current_date . "' AND company = '" . $row['comp_abrv'] . "' AND debit = '" . $row['mult_amount'] . "'";
	$chk_exist = 0; //isReceivableRecordExists($parameters);

	if ($chk_exist < 1 && $credit_amount > 0) {

		// getting balance amount
		$res = getCurrentBalances($con);

		$payment_type = $row['payment_type'];
		$payment_method_id = $row['payment_method_id'];

		if ($payment_type == 'cash') {
			$voucher_label = 'CPV';
			$is_bank = '0';
		} else {
			$voucher_label = 'BPV';
			$is_bank = '1';
		}

		// Getting New Voucher Counter
		$voucher_counter = getNextVoucherCount($voucher_label);

		// Updating the new Voucher Counter
		updateVoucherCounter($voucher_label, $voucher_counter);

		$voucher = $voucher_label . '-' . $voucher_counter;

		// Insertion in tbl account_receivable
		$insert_data_rec = array(
			'voucher' => $voucher,
			'invoice_no' => $row['m_inv'],
			'dated' => $current_date,
			'company' => $row['comp_abrv'],
			'description' => $description,
			'debit' => $credit_amount,
			'balance' => ($res['recv_balance'] + $credit_amount),
			'posted_by' => $_SESSION['userId'],
			'tbl' => 'mult_inv'
		);

		$re_result = insertAccountReceivable($insert_data_rec);
		//$voucher = $re_result['voucher'];
		$new_voucher_id = $re_result['new_voucher_id'];

		// This will update the status of paid record in Journal Table
		if ($row['rAmount'] > 0) {
			updateJournalLedgerStatus('undo', 1, $row['m_inv']);

			$insert_data_journal = array(
				'is_receivable' => 1,
				'receivable_payable_id' => $new_voucher_id,
				'voucher' => $voucher,
				'invoice_no' => $row['m_inv'],
				'company' => $row['comp_abrv'],
				'description' => $description,
				'is_bank' => $is_bank,
				'payment_type' => $payment_type,
				'account_id' => $payment_method_id,
				'dated' => $current_date,
				'credit' => $credit_amount,
				'balance' => ($res['journal_balance'] - $credit_amount),
				'posted_by' => $_SESSION['userId'],
				'posted_on' => date('Y-m-d H:i:s'),
				'tbl' => 'mult_inv'
			);

			insertJournalLedger($insert_data_journal);
		}
	} // end if record exists

	$update_mult_invoice = $acttObj->db_query("UPDATE mult_inv SET status = '', rAmount = 0, paid_date = NULL, paid_by = NULL, paid_on = NULL, is_undo_payment = 1, 
	undo_by = '".$_SESSION['UserName']."', undo_on = '".date('Y-m-d H:i:s')."' WHERE id = '" . $row['id'] . "'");

	$acttObj->db_query("UPDATE partial_amounts SET status = 0 WHERE order_id = '" . $multInvoicNo . "' AND tbl = 'mult_inv' AND status = 1");

	if ($update_mult_invoice) {

		$acttObj->insert("daily_logs", array("action_id" => 44, "user_id" => $_SESSION['userId'], "details" => "Mult. Invoice#: " . $multInvoicNo));

		echo 1;
		exit;
	} else {
		echo 0;
		exit;
	}
}

?>

<!doctype html>
<html lang="en">

<head>
	<title>Collective Invoices Actions List</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="css/util.css" />
	<style>
		.table-condensed>tbody>tr>td,
		.table-condensed>tbody>tr>th,
		.table-condensed>thead>tr>td,
		.table-condensed>thead>tr>th {
			font-size: 13px;
		}

		.dropdown-menu {
			min-width: auto !important;
		}

		.dropdown-menu .divider {
			margin: 5px 0;
		}

		.multiselect_orgs .btn-group {
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

		.multiselect_orgs .btn .caret {
			margin: 8px;
			position: absolute;
			right: 0;
		}

		.multiselect-container {
			max-height: 400px;
			overflow-y: auto;
			max-width: 400px;
		}

		.progress {
			background-color: #efebeb;
		}

		.progress-bar {
			color: #fff;
			font-weight: bold;
			text-align: center;
			line-height: 20px;
			font-size: 8px;
		}
	</style>

	<script>
		function myFunction() {
			var p = document.getElementById("inov").value;
			if (!p) {
				p = p;
			}
			var fd = document.getElementById("from_date").value;
			if (!fd) {
				fd = fd;
			}
			var td = document.getElementById("to_date").value;
			if (!td) {
				td = td;
			}
			var org = document.getElementById("org").value;
			if (org == 'All') {
				org = '';
			} else {
				org = org;
			}
			var pstatus = document.getElementById("pstatus").value;
			if (pstatus == 'all') {
				pstatus = '';
			} else {
				pstatus = pstatus;
			}
			var s = document.getElementById("status").value;
			if (s == '') {
				s = 'active';
			} else if (s == 'all') {
				s = 'all';
			} else {
				s = s;
			}
			window.location.href = "multi_list_actions.php" + '?inov=' + p + '&org=' + org + "&pstatus=" + pstatus + "&from_date=" + fd + "&to_date=" + td + "&status=" + s;

		}
	</script>

	<?php include 'header.php'; ?>

<body>
	<?php include 'nav2.php'; ?>
	<!-- end of sidebar -->
	<style>
		.tablesorter thead tr {
			background: none;
		}

		a.btn-default {
			color: #000;
		}

		.card {
			margin-bottom: 1.875rem;
			background-color: #fff;
			transition: all .5s ease-in-out;
			position: relative;
			border: 0px solid transparent;
			border-radius: 0.25rem;
			box-shadow: 0px 0px 13px 0px rgba(82, 63, 105, 0.05);
		}

		.stat-widget-two {
			text-align: center;
		}

		.stat-widget-two .stat-text {
			font-size: 16px;
			margin-bottom: 5px;
			color: #868e96;
		}

		.stat-widget-two .stat-digit {
			font-size: 1.75rem;
			font-weight: 500;
			color: #373757;
			font-family: 'Roboto', sans-serif;
		}

		.stat-widget-two .stat-digit i {
			font-size: 18px;
			margin-right: 5px;
		}

		.stat-widget-two .progress {
			height: 8px;
			margin-bottom: 0;
			margin-top: 20px;
			box-shadow: none;
		}

		.stat-widget-two .progress-bar {
			box-shadow: none;
		}

		.progress-bar {
			border-radius: 4px;
		}

		.w-85 {
			width: 85% !important;
		}

		.progress-bar {
			display: flex;
			flex-direction: column;
			justify-content: center;
			color: #fff;
			text-align: center;
			white-space: nowrap;
			transition: width 0.6s ease;
		}

		.card-body {
			padding: 1.25rem;
			flex: 1 1 auto;
			padding: 1.25rem;
		}

		.progress-bar-success {
			background-color: #7ED321 !important;
		}

		.progress-bar-primary {
			background-color: #593bdb !important;
		}

		.progress-bar-warning {
			background-color: #FFAA16 !important;
		}

		.progress-bar-danger {
			background-color: #FF1616 !important;
		}
	</style>

	<?php
	$strSqlFilt = $strSqlFilt2 = "";
	if (!empty($org)) {
		$strSqlFilt = " AND comp_reg.abrv LIKE '$org%' ";
		$strSqlFilt2 = " AND mi.comp_abrv LIKE '$org%' ";
	}
	if (!empty($inov)) {
		$strSqlFilt .= " AND mi.m_inv like '%$inov%' ";
		$strSqlFilt2 .= " AND mi.m_inv LIKE '%$inov%' ";
	}

	/*$query = "SELECT mi.*,
		(SELECT count(id) FROM mult_inv WHERE status = '') as total_pending_invoices,
		(SELECT count(id) FROM mult_inv WHERE status = 'Partially Received') as total_partial_invoices,
		(SELECT count(id) FROM mult_inv WHERE status = 'Received') as total_paid_invoices,
		(SELECT count(id) FROM mult_inv) as total_invoices,
		(SELECT ROUND(SUM(rAmount)) FROM mult_inv WHERE 1) as total_received_amount,
		(SELECT ROUND(SUM(mult_amount) - SUM(rAmount)) FROM mult_inv WHERE 1) as total_balance_amount,
		CASE 
			WHEN EXISTS (
				SELECT 1 FROM interpreter i WHERE i.orgName = mi.comp_abrv
					OR FIND_IN_SET(i.orgName, mi.comp_abrv)
			) THEN 'interpreter'
			WHEN EXISTS (
				SELECT 1 FROM telephone t WHERE t.orgName = mi.comp_abrv
					OR FIND_IN_SET(t.orgName, mi.comp_abrv)
			) THEN 'telephone'
			WHEN EXISTS (
				SELECT 1 FROM translation tr WHERE tr.orgName = mi.comp_abrv
					OR FIND_IN_SET(tr.orgName, mi.comp_abrv)
			) THEN 'translation'
			ELSE 'unknown'
		END AS source_table
	FROM mult_inv mi
	WHERE mi.comp_name != ''
		$strSqlFilt2
	ORDER BY mi.dated DESC
	LIMIT {$startpoint}, {$limit}";*/

	$query = "SELECT DISTINCT 
		(SELECT ROUND(SUM(mult_amount)) FROM mult_inv WHERE comp_id <> '' AND is_deleted = 0 AND is_undo_payment = 0 AND credit_note_id IS NULL) as total_invoices_amount,
		(SELECT ROUND(SUM(rAmount)) FROM mult_inv WHERE comp_id <> '' AND is_deleted = 0 AND is_undo_payment = 0 AND credit_note_id IS NULL) as total_received_amount,
		(SELECT ROUND(SUM(mult_amount) - SUM(rAmount)) FROM mult_inv WHERE comp_id <> '' AND is_deleted = 0 AND is_undo_payment = 0 AND credit_note_id IS NULL) as total_balance_amount,

		(SELECT count(id) FROM mult_inv WHERE comp_id <> '' AND is_deleted = 0 AND is_undo_payment = 0 AND credit_note_id IS NULL) as total_invoices,
		(SELECT count(id) FROM mult_inv WHERE comp_id <> '' AND status = 'Received' AND is_deleted = 0 AND is_undo_payment = 0 AND credit_note_id IS NULL) as total_paid_invoices,
		(SELECT count(id) FROM mult_inv WHERE comp_id <> '' AND status = '' AND is_deleted = 0 AND is_undo_payment = 0 AND credit_note_id IS NULL) as total_pending_invoices,

		(SELECT count(id) FROM mult_inv WHERE comp_id <> '' AND status = 'Partially Received' AND is_deleted = 0 AND is_undo_payment = 0 AND credit_note_id IS NULL) as total_partial_invoices
		
	FROM mult_inv mi
	WHERE mi.comp_id <> ''";

	$result = mysqli_query($con, $query);
	$counter_data = $acttObj->full_fetch_assoc($result);

	//$total_invoices_amount   = $counter_data['total_invoices_amount'];      // total amount of all invoices
	$total_invoices_amount   = ($counter_data['total_received_amount'] + $counter_data['total_balance_amount']);      // total amount of all invoices
	$total_received_amount   = $counter_data['total_received_amount'];      // total amount received
	$total_balance_amount    = $counter_data['total_balance_amount'];       // total remaining balance

	$total_amount_progress = 0;
	$received_amount_progress = 0;
	$balance_amount_progress = 0;

	if ($total_invoices_amount > 0) {
		$total_amount_progress = ($total_invoices_amount > 0) ? round(($total_received_amount / $total_invoices_amount) * 100, 2) : 0; // always 100% as it's the full amount
		$received_amount_progress = round(($total_received_amount / $total_invoices_amount) * 100, 2);
		$balance_amount_progress = round(($total_balance_amount / $total_invoices_amount) * 100, 2);
	}

	$total_invoices          = $counter_data['total_invoices'];             // total number of invoices
	$total_paid_invoices     = $counter_data['total_paid_invoices'];        // count with status = 'Received'
	$total_pending_invoices  = $counter_data['total_pending_invoices'];     // status = ''
	$total_partial_invoices  = $counter_data['total_partial_invoices'];     // status = 'Partially Received'


	// Invoice status progress
	$paid_invoice_progress = ($total_invoices > 0)
		? round(($total_paid_invoices / $total_invoices) * 100, 2)
		: 0;

	$pending_invoice_progress = ($total_invoices > 0)
		? round(($total_pending_invoices / $total_invoices) * 100, 2)
		: 0;

	$partial_invoice_progress = ($total_invoices > 0)
		? round(($total_partial_invoices / $total_invoices) * 100, 2)
		: 0;

	?>

	<section class="container-fluid" style="overflow-x:auto;min-height: 800px;">
		<div class="col-md-12">

			<div class="row m-b-50">
				<a href="<?php echo basename(__FILE__); ?>">
					<h2 class="col-md-3 col-md-offset-4 text-center"><span class="label label-primary">COLLECTIVE INVOICES ACTION LIST</span></h2>
				</a>
			</div>

			<div class="row">
				<div class="col-lg-2 col-sm-6">
					<div class="card">
						<div class="stat-widget-two card-body">
							<div class="stat-content">
								<div class="stat-text">Total Amount</div>
								<div class="stat-digit"> <?php echo number_format($total_invoices_amount); ?></div>
							</div>
							<div class="progress">
								<div class="progress-bar progress-bar-default"
									style="width: <?php echo $total_amount_progress; ?>%;"
									role="progressbar"
									aria-valuenow="<?php echo $total_amount_progress; ?>"
									aria-valuemin="0"
									aria-valuemax="100">
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="card">
						<div class="stat-widget-two card-body">
							<div class="stat-content">
								<div class="stat-text">Received Amount</div>
								<div class="stat-digit"> <?php echo number_format($total_received_amount); ?></div>
							</div>
							<div class="progress">
								<div class="progress-bar progress-bar-success" style="width: <?php echo $received_amount_progress; ?>%;" role="progressbar" aria-valuenow="<?php echo $received_amount_progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="card">
						<div class="stat-widget-two card-body">
							<div class="stat-content">
								<div class="stat-text">Balance Amount</div>
								<div class="stat-digit"> <?php echo number_format($total_balance_amount); ?></div>
							</div>
							<div class="progress">
								<div class="progress-bar progress-bar-warning" style="width: <?php echo $balance_amount_progress; ?>%;" role="progressbar" aria-valuenow="<?php echo $balance_amount_progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="card">
						<div class="stat-widget-two card-body">
							<div class="stat-content">
								<div class="stat-text">Paid Invoices</div>
								<div class="stat-digit"> <?php echo number_format($total_paid_invoices); ?></div>
							</div>
							<div class="progress">
								<div class="progress-bar progress-bar-success" style="width: <?php echo $paid_invoice_progress; ?>%" role="progressbar" aria-valuenow="<?php echo $paid_invoice_progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="card">
						<div class="stat-widget-two card-body">
							<div class="stat-content">
								<div class="stat-text">Partial Invoices</div>
								<div class="stat-digit"> <?php echo number_format($total_partial_invoices); ?></div>
							</div>
							<div class="progress">
								<div class="progress-bar progress-bar-warning" style="width:<?php echo $partial_invoice_progress; ?>%" role="progressbar" aria-valuenow="<?php echo $partial_invoice_progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-2 col-sm-6">
					<div class="card">
						<div class="stat-widget-two card-body">
							<div class="stat-content">
								<div class="stat-text">Pending Invoices</div>
								<div class="stat-digit"> <?php echo number_format($total_pending_invoices); ?></div>
							</div>
							<div class="progress">
								<div class="progress-bar progress-bar-danger" style="width:<?php echo $pending_invoice_progress; ?>%" role="progressbar" aria-valuenow="<?php echo $pending_invoice_progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					</div>
					<!-- /# card -->
				</div>
				<!-- /# column -->
			</div>

			<div class="row m-b-20">
				<div class="form-group col-md-2 col-sm-2">
					<label>Invoice# </label>
					<input type="text" name="inov" id="inov" class="form-control" placeholder="Invoice #" onChange="myFunction()" value="<?php echo $inov; ?>" />
				</div>
				<div class="form-group col-md-2 col-sm-2 multiselect_orgs">
					<label>Company</label>
					<select id="org" name="org" onChange="myFunction()" class="form-control">
						<?php /*if (!empty($org)) {
							mysqli_query($con, "SET SQL_BIG_SELECTS=1");
						}

						if (!empty($type) && $type == 'Interpreter') {
							$sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter WHERE interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag=0 AND interpreter.order_cancel_flag=0 AND interpreter.multInv_flag=1 and interpreter.commit=0 ) as grp 
								ORDER BY name ASC";
						} else if (!empty($type) && $type == 'Telephone') {
							$sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone WHERE telephone.orgName=comp_reg.abrv AND telephone.deleted_flag=0 AND telephone.order_cancel_flag=0 AND telephone.multInv_flag=1 and telephone.commit=0 ) as grp 
								ORDER BY name ASC";
						} else if (!empty($type) && $type == 'Translation') {
							$table = 'translation';
							$sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation WHERE translation.orgName=comp_reg.abrv AND translation.deleted_flag=0 AND translation.order_cancel_flag=0 AND translation.multInv_flag=1 and translation.commit=0 ) as grp 
								ORDER BY name ASC";
						} else {
							$sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter WHERE interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag=0 AND interpreter.order_cancel_flag=0 AND interpreter.multInv_flag=1 and interpreter.commit=0 
								UNION SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone WHERE telephone.orgName=comp_reg.abrv AND telephone.deleted_flag=0 AND telephone.order_cancel_flag=0 AND telephone.multInv_flag=1 and telephone.commit=0  
								UNION SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation WHERE translation.orgName=comp_reg.abrv AND translation.deleted_flag=0 AND translation.order_cancel_flag=0 AND translation.multInv_flag=1 and translation.commit=0 ) as grp 
								ORDER BY name ASC";
						}*/
						$sql_opt = "SELECT DISTINCT cr.id, 
							cr.name, 
							cr.abrv
						FROM (
							SELECT 
								TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(mi.comp_id, ',', n.n), ',', -1)) AS comp_id_part
							FROM mult_inv mi
							JOIN (
								SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL 
								SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL 
								SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
							) AS n
							ON CHAR_LENGTH(mi.comp_id) - CHAR_LENGTH(REPLACE(mi.comp_id, ',', '')) >= n.n - 1
						) AS derived
						JOIN comp_reg cr ON cr.id = derived.comp_id_part
						WHERE 
							cr.deleted_flag = 0
						ORDER BY cr.name ASC";
						$result_opt = mysqli_query($con, $sql_opt);
						$options = "";
						echo '<option value="" disabled>Select Organization</option>';
						echo '<option value="All" selected>All</option>';
						while ($row_opt = mysqli_fetch_array($result_opt)) {
							$code = $row_opt["abrv"];
							$name_opt = $row_opt["name"];
							$selected = ($code == $_GET['org']) ? "selected" : "";
						?>
							<option value="<?php echo $code; ?>" <?php echo $selected; ?>>
								<?php echo $name_opt . ' (' . $code . ')'; ?>
							</option>
						<?php }	?>
					</select>
				</div>
				<div class="form-group col-md-2 col-sm-2">
					<label>From Date</label>
					<input type="date" name="from_date" id="from_date" class="form-control" placeholder="From Date" onChange="myFunction()" value="<?php echo $from_date; ?>" />
				</div>
				<div class="form-group col-md-2 col-sm-2">
					<label>To Date</label>
					<input type="date" name="to_date" id="to_date" class="form-control" placeholder="To Date" onChange="myFunction()" value="<?php echo $to_date; ?>" />
				</div>
				<div class="form-group col-md-2 col-sm-2">
					<label>Payment Status</label>
					<select id="pstatus" name="pstatus" onChange="myFunction()" class="form-control">
						<option value="" disabled>- Payment Status -</option>';
						<option value="all">All</option>
						<option value="pending" <?php echo ($pstatus == 'pending') ? 'selected' : ''; ?>>Pending</option>
						<option value="received" <?php echo ($pstatus == 'received') ? 'selected' : ''; ?>>Received</option>
						<option value="partially_received" <?php echo ($pstatus == 'partially_received') ? 'selected' : ''; ?>>Partially Received</option>
					</select>
				</div>
				<div class="form-group col-md-1 col-sm-1">
					<label>Status</label>
					<select id="status" name="status" onChange="myFunction()" class="form-control">
						<option value="" disabled>- Status -</option>';
						<option value="all" <?php echo ($status == 'all') ? 'selected' : ''; ?>>All</option>
						<option value="active" <?php echo (empty($status) || $status == 'active') ? 'selected' : ''; ?>>Active</option>
						<option value="cancelled" <?php echo ($status == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
						<option value="credit_note" <?php echo ($status == 'credit_note') ? 'selected' : ''; ?>>Credit Note</option>
						<option value="undo" <?php echo ($status == 'undo') ? 'selected' : ''; ?>>Undo Invoices</option>
					</select>
				</div>

				<div class="form-group col-md-1 col-sm-2 text-left pull-right m-t-30">
					<a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?inov=<?php echo $inov; ?>&org=<?php echo $org; ?>&pstatus=<?php echo $pstatus; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>&status=<?php echo $status; ?>"
						title="Download Excel Report">
						<span class="btn btn-sm btn-success">Export To Excel</span>
					</a>
				</div>
			</div>

			<?php
			$str_where = '';

			if (empty($pstatus)) {
				$str_where .= '';
			} else if ($pstatus == 'pending') {
				$str_where .= 'AND mi.status = ""';
			} else {
				$str_where .= 'AND mi.status = "' . ucwords(str_replace("_", " ", $pstatus)) . '" ';
			}

			if ($status == 'all') {
				$str_where .= '';
			} else if (empty($status) || $status == 'active') {
				$str_where .= 'AND (mi.credit_note_id IS NULL OR mi.credit_note_id = 0) AND mi.is_deleted = 0 AND mi.commit = 1';
			} else if ($status == 'cancelled') {
				$str_where .= 'AND mi.is_deleted = 1';
			} else if ($status == 'credit_note') {
				$str_where .= 'AND (mi.credit_note_id <> NULL OR mi.credit_note_id <> 0) AND mi.is_deleted = 0';
			} else if ($status == 'undo') {
				$str_where .= 'AND mi.is_undo_payment = 1 AND mi.is_deleted = 0';
			}

			if (!empty($from_date) && !empty($to_date)) {
				$str_where .= " AND mi.from_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'";
			}

			$query2 = "SELECT mi.*
				FROM mult_inv mi
				WHERE mi.comp_id <> '' " . $str_where . "
				$strSqlFilt2
				ORDER BY DATE(mi.dated) DESC
				LIMIT {$startpoint}, {$limit}";

			$result2 = $acttObj->full_fetch_array($query2);
			?>

			<div class="tab_container m-b-100">
				<div id="tab1" class="tab_content">
					<div class="col-md-12 m-b-15 text-right">
						<?php echo pagination($con, 'mult_inv', $query2, $limit, $page); ?>
					</div>
					<table class="table table-bordered table-striped table-hover table-condensed" id="tbl_multi_inv">
						<thead class="bg-primary">
							<tr>
								<th width="10%">Invoice #</th>
								<th width="15%">Company Name</th>
								<th>Amount</th>
								<th>Paid</th>
								<th>Balance</th>
								<th>Status</th>
								<th>Paid date</th>
								<th>From Date</th>
								<th>To Date</th>
								<th>Due Date</th>
								<th>Dated</th>
								<th align="center">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if (count($result2) > 0) {
								foreach ($result2 as $row) { ?>
									<tr style="<?php echo ($row['is_deleted'] == 1) ? 'background-color: #f2dede;' : ''; ?> 
									<?php echo ($row['commit'] == 0) ? 'background-color: #dfa4212b;' : ''; ?>"
										title="<?php echo ($row['is_deleted'] == 1) ? 'This job is deleted' : ''; ?>
									<?php echo ($row['commit'] == 0) ? 'The job is cancelled/credit note' : ''; ?>
									"
										id="inv_<?php echo $row['m_inv']; ?>">
										<td><?php echo $row['m_inv']; ?></td>
										<td><?php echo $row['comp_name']; ?></td>
										<td>
											<?php echo $misc->numberFormat_fun($row['mult_amount']); ?>
										</td>
										<td>
											<?php echo $misc->numberFormat_fun($row['rAmount']) ?: 0; ?>
										</td>
										<td>
											<?php echo $misc->numberFormat_fun(round($row['mult_amount'], 2) - round($row['rAmount'], 2)); ?>
										</td>
										<?php
										if ($row['commit'] == 1) {
											if ($row['status']) { ?>
												<td style=" color:#066; font-weight:bold"><?php echo $row['status']; ?></td>
											<?php } else { ?>
												<td style="color:#F00; font-weight:bold"><?php echo 'Pending'; ?></td>
											<?php }
										} else { ?>
											<td style="color:#F00; font-weight:bold">
												Credit Note
											</td>
										<?php } ?>
										<td>
											<?php echo ($row['paid_date'] != '1001-01-01' && $row['paid_date'] != '0000-00-00')
												? $misc->dated($row['paid_date'])
												: 'Date Not Found';
											?>
										</td>
										<td><?php echo $misc->dated($row['from_date']); ?></td>
										<td><?php echo $misc->dated($row['to_date']); ?></td>
										<td><?php echo $misc->dated($row['due_date']); ?></td>
										<td><?php echo $misc->dated($row['dated']); ?></td>
										<td align="center">

											<div class="dropdown">
												<button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="menu2" data-toggle="dropdown">Actions <span class="caret"></span></button>
												<ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="menu2">
													<?php if ($action_view_invoice) { ?>
														<li>
															<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="" title="View Invoice" onClick="popupwindow('view_multiple_invoice.php?multInvoiceNo=<?php echo $row['m_inv']; ?>', 'View Order', 1400, 800);">
																<i class="fa fa-eye"></i> View Invoice
															</a>
														</li>
													<?php } ?>
													<?php if ($action_receive_payment && empty($row['status']) && $row['commit'] == 1 && $row['is_deleted'] == 0) { ?>
														<li>
															<a href="javascript:void(0)" class="" onClick="popupwindow('multi_receive_amount.php?row_id=<?php echo $row['id']; ?>&table=mult_inv','Update Multi Invoice Payment', 800,450);">
																<i class="fa fa-dollar" title="Receive Payment"></i> Receive Payment
															</a>
														</li>
													<?php } ?>
													<?php if ($action_receive_partial_payment && (empty($row['status']) || $row['status'] == 'Partially Received') && $row['commit'] == 1 && $row['is_deleted'] == 0) { ?>
														<li>
															<a href="javascript:void(0)" class="" onClick="popupwindow('multi_receive_part.php?row_id=<?php echo $row['id']; ?>&table=mult_inv','Partial Payments', 800,450);">
																<i class="fa fa-money" title="Receive Partial Payment"></i> Partial Payment
															</a>
														</li>
													<?php } ?>
													<?php /*if ($action_receive_payment && empty($row['status'])) { ?>
														<li>
															<a href="javascript:void(0)" class="" title="Update Invoice" onClick="popupwindow('view_multiple_invoice.php?multInvoiceNo=<?php echo $row['m_inv']; ?>', 'View Order', 1400, 800);">
																<i class="fa fa-retweet" title="Update Payment"></i> Update Payment
															</a>
														</li>
													<?php }*/ ?>
													<li>
														<a href="javascript:void(0)" onClick="popupwindow('reports_lsuk/pdf/rip_multiple_inv_pdf.php?multInvoiceNo=<?php echo $row['m_inv']; ?>', 'Print Invoice', 1200, 800);" title="Print" class="">
															<i class="fa fa-print"></i> Print
														</a>
													</li>
													<?php if ($action_export_to_excel) { ?>
														<li>
															<a href="reports_lsuk/excel/rip_multiple_inv_export.php?multInvoiceNo=<?php echo $row['m_inv']; ?>" title="Download Excel Report">
																<i class="fa fa-download"></i> Export To Excel
															</a>
														</li>
													<?php } ?>
													<li class="divider"></li>
													<?php if ($action_undo_payments && $row['rAmount'] > 0) { ?>
														<li>
															<a href="javascript:void(0)" onclick="undoInvoicePayment('<?php echo $row['m_inv']; ?>', <?php echo $row['rAmount']; ?>)" title="Undo Payment">
																<i class="fa fa-undo text-danger" aria-hidden="true"></i> Undo Payment
															</a>
														</li>
													<?php } ?>
													<?php if ($action_delete_invoice && $row['status'] == '' && $row['commit'] == 1 && $row['is_deleted'] == 0) { ?>
														<li>
															<a href="javascript:void(0)" onclick="cancelMultiInvoice('<?php echo $row['m_inv']; ?>', <?php echo $row['rAmount']; ?>)" title="Cancel Invoice">
																<i class="fa fa-trash text-danger"></i> Cancel Invoice
															</a>
														</li>

													<?php } ?>
													<?php if ($action_make_credit_note && $row['is_deleted'] == 0) { ?>
														<li>
															<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" onClick="popupwindow('multiple_invoice_credit_note.php?multInvoiceNo=<?php echo $row['m_inv']; ?>', 'Credit Note', 1400, 800);" title="Credit Note">
																<i class="fa fa-exclamation-circle text-<?php echo ($row['commit'] == 1) ? 'primary' : 'danger' ?>"></i> Credit Note
															</a>
														</li>
													<?php } ?>
												</ul>
											</div>
										</td>
									</tr>
								<?php } // end foreach 
								?>
							<?php } else { // end if 
							?>
								<tr>
									<td colspan="11">
										No record found.
									</td>
								</tr>
							<?php }  // end else 
							?>
						</tbody>
					</table>
				</div>
	</section>
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>

	<script>
		$(function() {
			$('#org').multiselect({
				includeSelectAllOption: true,
				numberDisplayed: 1,
				enableFiltering: true,
				enableCaseInsensitiveFiltering: true,
				nonSelectedText: 'Select Company'
			});
		});

		function cancelMultiInvoice(invoiceNo, rAmount) {
			if (!invoiceNo) return;

			if (!confirm("Are you sure to Cancel Invoice# " + invoiceNo)) return;

			if (rAmount > 0 && !confirm("The selected Invoice has some Received Payment(s). Are you sure to Cancel?")) {
				return;
			}

			$.ajax({
				type: "GET",
				url: "?action=CancelCollectiveInvoice&multInvoiceNo=" + invoiceNo,
				success: function(response) {
					if ($.trim(response) == 1) {
						alert(invoiceNo + ' successfully Cancelled.');
						var row = $('#inv_' + invoiceNo);
						row.find('td').css('background-color', '#f2dede');
						$(row).remove();
						// setTimeout(function() {
						// 	row.fadeOut(500, function() {
						// 		$(this).remove();
						// 	});
						// }, 3000);
					} else {
						alert("Sorry! Failed to Cancel Invoice# " + invoiceNo + ", Please try again.");
					}
				}
			});
		}

		function undoInvoicePayment(invoiceNo, rAmount) {
			if (!invoiceNo) return;

			if (!confirm("Are you sure to Undo Payment for Invoice# " + invoiceNo)) return;

			if (rAmount > 0 && !confirm("The selected Invoice has some Received Payment(s). Are you sure?")) {
				return;
			}

			$.ajax({
				type: "GET",
				url: "?action=UndoInvoicePayment&multInvoiceNo=" + invoiceNo,
				success: function(response) {
					if (response == 1) {
						alert('Payment Undo successfully for Invoice# ' + invoiceNo);
						location.reload();
					} else {
						alert("Sorry! Failed to Undo Payment for Invoice# " + invoiceNo + ", Please try again.");
					}
				}
			});
		}
	</script>
</body>

</html>