<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}
include 'db.php';
include 'class.php';
include('function.php');

//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=24 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_expense = $_SESSION['is_root'] == 1 || in_array(94, $get_actions);
$action_edit_expense = $_SESSION['is_root'] == 1 || in_array(95, $get_actions);
$action_delete_expense = $_SESSION['is_root'] == 1 || in_array(96, $get_actions);
$action_restore_expense = $_SESSION['is_root'] == 1 || in_array(97, $get_actions);
$action_expense_history = $_SESSION['is_root'] == 1 || in_array(98, $get_actions);
$action_dropdown_filter = $_SESSION['is_root'] == 1 || in_array(99, $get_actions);
$action_pay_expense = $_SESSION['is_root'] == 1 || in_array(238, $get_actions);
$action_receive_payment = $_SESSION['is_root'] == 1 || in_array(241, $get_actions);
$action_receive_partial_payment = $_SESSION['is_root'] == 1 || in_array(242, $get_actions);

$table = 'expence';
$title = @$_GET['title'];
$comp = @$_GET['comp'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$pmnt_by = @$_GET['pmnt_by'];
$tp = @$_GET['tp'];
$inv = @$_GET['inv'];
$vhr = @$_GET['vhr'];
$pstatus = @$_GET['pstatus'];
$ref_no = @$_GET['ref_no'];

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;

$class = $tp == 'tr' ? 'alert-danger' : 'alert-info';

?>
<!doctype html>
<html lang="en">

<head>
	<title>Company Expenses List</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<style>
		.table-condensed>tbody>tr>td,
		.table-condensed>tbody>tr>th,
		.table-condensed>thead>tr>td,
		.table-condensed>thead>tr>th {
			font-size: 13px;
		}

		.table>tbody>tr>td,
		.table>tbody>tr>th,
		.table>tfoot>tr>td,
		.table>tfoot>tr>th,
		.table>thead>tr>td,
		.table>thead>tr>th {
			padding: 4px !important;
			cursor: pointer;
		}

		html,
		body {
			background: #fff !important;
		}

		.div_actions {
			position: absolute;
			margin-top: -48px;
			background: #ffffff;
			border: 1px solid lightgrey;
			left: 50%;
		}

		.alert {
			padding: 6px;
		}

		.div_actions .fa {
			font-size: 14px;
		}

		.w3-btn,
		.w3-button {
			padding: 8px 10px !important;
		}

		.multiselect-container {
			height: 25rem;
			overflow: scroll;
		}

		div.btn-group,
		.btn-group button {
			width: 100% !important;
		}
	</style>
</head>
<script>
	function myFunction() {
		var x = document.getElementById("title").value;
		if (!x) {
			x = "<?php echo $title; ?>";
		}
		var y = document.getElementById("search_2").value;
		if (!y) {
			y = "<?php echo $search_2; ?>";
		}
		var z = document.getElementById("search_3").value;
		if (!z) {
			z = "<?php echo $search_3; ?>";
		}
		var tp = document.getElementById("tp").value;
		if (!tp) {
			tp = "<?php echo $tp; ?>";
		}
		var pstatus = document.getElementById("pstatus").value;
		if (!pstatus) {
			pstatus = "<?php echo $pstatus; ?>";
		}
		var sp = document.getElementById("comp").value;
		if (!sp) {
			sp = "<?php echo $comp; ?>";
		}
		var pmnt_by = document.getElementById("pmnt_by").value;
		if (!pmnt_by) {
			pmnt_by = "<?php echo $pmnt_by; ?>";
		}
		var pmb = '';
		if (pmnt_by != '') {
			pmb = '&pmnt_by=' + pmnt_by;
		}
		var inv = document.getElementById("invoice_no").value;
		if (inv) {
			inv = '&inv=' + inv;
		}
		var vhr = document.getElementById("voucher").value;
		if (vhr) {
			vhr = '&vhr=' + vhr;
		}
		var ref_no = document.getElementById("ref_no").value;
		if (ref_no) {
			ref_no = '&ref_no=' + ref_no;
		}
		window.location.href = "expence_list.php" + '?title=' + x + '&comp=' + encodeURIComponent(sp) + '&search_2=' + y + '&search_3=' + z + '&tp=' + tp + pmb + inv + vhr + "&pstatus=" + pstatus + ref_no;
	}
</script>
<?php include 'header.php'; ?>
<link rel="stylesheet" type="text/css" href="css/util.css" />

<body>
	<?php include 'nav2.php'; ?>
	<!-- end of sidebar -->
	<style>
		.tablesorter thead tr {
			background: none;
		}

		.text-white {
			color: #fff !important;
		}

		.div_actions a {
			color: #000000;
			text-decoration: none;
		}
		html, body {
			background: #F8F8F8 !important;
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
	<div class="container-fluid p-b-50">
		<section class="col-sm-12">
			<center>
				<div class="alert <?php echo $class; ?> col-md-3 col-md-offset-4 text-center">
					<a href="<?php echo basename(__FILE__); ?>" class="alert-link"><?php echo $page_title; ?> Expenses List</a>
				</div>
			</center>
		</section>

		<?php 
			$condition = '';
			
			if ($tp == 'tr') {
				$condition .= 'deleted_flag = 1';
			} else if ($tp == 'all') {
				$condition .= '(expence.deleted_flag = 0 OR expence.deleted_flag IS NULL) OR expence.deleted_flag = 1';
			} else {
				$condition .= '(expence.deleted_flag = 0 OR expence.deleted_flag IS NULL)';
			}

			if (empty($pstatus) || $pstatus == 'all') {
				$condition .= "";
			} else {
				$condition .= " AND expence.status = '$pstatus'";
			}

			if (!empty($inv)) {
				$condition .= " AND expence.invoice_no LIKE '$inv'";
			}

			if (!empty($vhr)) {
				$condition .= " AND expence.voucher LIKE '$vhr'";
			}

			if (!empty($title)) {
				$condition .= " AND expence_list.title LIKE '$title%'";
			}

			if (!empty($comp)) {
				$condition .= " AND expence.comp = '" . trim($comp) . "'";
			}
			
			if (!empty($ref_no)) {
				$condition .= " AND expence.inv_ref_num LIKE '%" . trim($ref_no) . "'";
			}

			// if (!empty($search_2) && !empty($search_3)) {
			// 	$condition .= " AND DATE(expence.paid_on) BETWEEN '$search_2' AND '$search_3'";
			// }

			if (!empty($pmnt_by)) {
				if ($pmnt_by == 'cash') {
					$condition .= " AND (expence.pay_by = 'CASH' OR expence.payment_type = '{$pmnt_by}')";
				}

				if ($pmnt_by == 'payable') {
					$condition .= " AND (expence.pay_by = '{$pmnt_by}' OR expence.pay_by = 'PAYABLE')";
				}

				if ($pmnt_by == 'bacs') {
					$condition .= " AND (expence.pay_by = 'BANK' OR expence.payment_type = '{$pmnt_by}')";
				}

				if ($pmnt_by == 'cheque' || $pmnt_by == 'card') {
					$condition .= " AND expence.payment_type = '{$pmnt_by}'";
				}

				if ($pmnt_by == 'prepayments') {
					$condition .= " AND expence.is_prepayment = 1";
				}

				if ($pmnt_by == 'all') {
					$condition .= "";
				}
			}

			$sumQuery = "
				SELECT 
					ROUND(SUM(expence.netamount), 2) AS total_netamount,
					ROUND(SUM(expence.vat), 2) AS total_vat,
					ROUND(SUM(expence.nonvat), 2) AS total_nonvat,
					ROUND(SUM(expence.amoun), 2) AS total_amount,
					ROUND(SUM(expence.amountPaid), 2) AS total_paid_amount,
					ROUND(SUM(expence.amoun) - SUM(expence.amountPaid), 2) AS total_balance_amount,

					-- Progress %s
					ROUND(IF(SUM(expence.amoun) > 0, (SUM(expence.netamount) / SUM(expence.amoun)) * 100, 0), 2) AS progress_netamount,
					ROUND(IF(SUM(expence.amoun) > 0, (SUM(expence.vat) / SUM(expence.amoun)) * 100, 0), 2) AS progress_vat,
					ROUND(IF(SUM(expence.amoun) > 0, (SUM(expence.nonvat) / SUM(expence.amoun)) * 100, 0), 2) AS progress_nonvat,
					ROUND(IF(SUM(expence.amoun) > 0, (SUM(expence.amountPaid) / SUM(expence.amoun)) * 100, 0), 2) AS progress_paid_amount,
					ROUND(IF(SUM(expence.amoun) > 0, ((SUM(expence.amoun) - SUM(expence.amountPaid)) / SUM(expence.amoun)) * 100, 0), 2) AS progress_balance_amount
				FROM 
					expence
				LEFT JOIN 
					expence_list ON expence.type_id = expence_list.id
				WHERE 
					{$condition}
			";

			// Add optional date range filter
			if (!empty($search_2) && !empty($search_3)) {
				$sumQuery .= " AND DATE(
					COALESCE(
						(
							SELECT payment_date 
							FROM expence_partial_payments epp 
							WHERE epp.expence_id = expence.id AND epp.deleted_flag = 0 
							ORDER BY id DESC 
							LIMIT 1
						),
						expence.paid_on
					)
				) BETWEEN '{$search_2}' AND '{$search_3}'";
			}
			
			//debug($sumQuery);

			$sumResult = mysqli_query($con, $sumQuery);
			$totals = mysqli_fetch_assoc($sumResult);

		?>

		<div class="row">
			<div class="col-lg-2 col-sm-6">
				<div class="card">
					<div class="stat-widget-two card-body">
						<div class="stat-content">
							<div class="stat-text">Net Amount</div>
							<div class="stat-digit"> £ <?php echo number_format($totals['total_netamount']); ?></div>
						</div>
						<div class="progress">
							<div class="progress-bar progress-bar-warning"
								style="width: <?php echo $totals['progress_netamount']; ?>%;"
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
							<div class="stat-text">VAT</div>
							<div class="stat-digit"> £ <?php echo number_format($totals['total_vat']); ?></div>
						</div>
						<div class="progress">
							<div class="progress-bar progress-bar-success" style="width: <?php echo $totals['progress_vat']; ?>%;" role="progressbar" aria-valuenow="<?php echo $totals['progress_vat']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-2 col-sm-6">
				<div class="card">
					<div class="stat-widget-two card-body">
						<div class="stat-content">
							<div class="stat-text">Non VAT</div>
							<div class="stat-digit"> £ <?php echo number_format($totals['total_nonvat']); ?></div>
						</div>
						<div class="progress">
							<div class="progress-bar progress-bar-warning" style="width: <?php echo $totals['progress_nonvat']; ?>%;" role="progressbar" aria-valuenow="<?php echo $totals['progress_nonvat']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-2 col-sm-6">
				<div class="card">
					<div class="stat-widget-two card-body">
						<div class="stat-content">
							<div class="stat-text">Total Amount</div>
							<div class="stat-digit"> £ <?php echo number_format($totals['total_amount']); ?></div>
						</div>
						<div class="progress">
							<div class="progress-bar progress-bar-default" style="width: 100%" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-2 col-sm-6">
				<div class="card">
					<div class="stat-widget-two card-body">
						<div class="stat-content">
							<div class="stat-text">Paid Amount</div>
							<div class="stat-digit"> £ <?php echo number_format($totals['total_paid_amount']); ?></div>
						</div>
						<div class="progress">
							<div class="progress-bar progress-bar-success" style="width:<?php echo $totals['progress_paid_amount']; ?>%" role="progressbar" aria-valuenow="<?php echo $totals['progress_paid_amount']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-2 col-sm-6">
				<div class="card">
					<div class="stat-widget-two card-body">
						<div class="stat-content">
							<div class="stat-text">Balance</div>
							<div class="stat-digit"> £ <?php echo number_format($totals['total_balance_amount']); ?></div>
						</div>
						<div class="progress">
							<div class="progress-bar progress-bar-danger" style="width:<?php echo $totals['progress_balance_amount']; ?>%" role="progressbar" aria-valuenow="<?php echo $totals['progress_balance_amount']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
				<!-- /# card -->
			</div>
			<!-- /# column -->
		</div>

		<section class="row">
			<div class="form-group col-md-2">
				<label>Track#</label>
				<input type="text" name="invoice_no" id="invoice_no" placeholder='Track#' class="form-control" onChange="myFunction()" value="<?php echo $inv; ?>" />
			</div>
			<div class="form-group col-md-2">
				<label>Voucher</label>
				<input type="text" name="voucher" id="voucher" placeholder='Voucher' class="form-control" onChange="myFunction()" value="<?php echo $vhr; ?>" />
			</div>
			<div class="form-group col-md-2">
				<label>Invoice/Ref#</label>
				<input type="text" name="ref_no" id="ref_no" placeholder='Invoice/Ref#' class="form-control" onChange="myFunction()" value="<?php echo $ref_no; ?>" />
			</div>
			<div class="form-group col-md-2 ">
				<label>Expense Type</label>
				<select id="title" name="title" onChange="myFunction()" class="form-control">
					<?php
					$sql_opt = "SELECT title FROM expence_list ORDER BY title ASC";
					$result_opt = mysqli_query($con, $sql_opt);
					$options = "";
					while ($row_opt = mysqli_fetch_array($result_opt)) {
						$code = $row_opt["title"];
						$name_opt = $row_opt["title"];
						$options .= "<OPTION value='$code'>" . $name_opt . ' (' . $code . ')';
					}
					?>
					<?php if (!empty($title)) { ?>
						<option><?php echo $title; ?></option>
					<?php } else { ?>
						<option value="">Select Expense Type</option>
					<?php } ?>
					<?php echo $options; ?>
					</option>
				</select>
			</div>
			<div class="form-group  col-md-2  ">
				<label>Payment Status</label>
				<select id="pstatus" onChange="myFunction()" name="pstatus" class="form-control">
					<option <?= $pstatus == 'all' ? 'selected' : '' ?> value="all">All</option>
					<option <?= ($pstatus == 'unpaid') ? 'selected' : '' ?> value="unpaid">Unpaid</option>
					<option <?= ($pstatus == 'partial') ? 'selected' : '' ?> value="partial">Partial</option>
					<option <?= ($pstatus == 'full_paid') ? 'selected' : '' ?> value="full_paid">Full Paid</option>
					<option <?= ($pstatus == 'full_partial') ? 'selected' : '' ?> value="full_partial">Full Partial</option>
				</select>
			</div>
			<div class="form-group  col-md-2  ">
				<label>Payment By</label>
				<select id="pmnt_by" name="pmnt_by" onChange="myFunction()" class="form-control">
					<option value="">Payment By</option>
					<option value="all" <?php echo (empty($pmnt_by) || $pmnt_by == "all") ? 'selected' : ''; ?>>All</option>
					<option value="bacs" <?php echo ($pmnt_by == "bacs") ? 'selected' : ''; ?>>BACS</option>
					<option value="cheque" <?php echo ($pmnt_by == "cheque") ? 'selected' : ''; ?>>Cheque</option>
					<option value="card" <?php echo ($pmnt_by == "card") ? 'selected' : ''; ?>>Credit/Debit Card</option>
					<option value="cash" <?php echo ($pmnt_by == "cash") ? 'selected' : ''; ?>>Cash</option>
					<option value="payable" <?php echo ($pmnt_by == "payable") ? 'selected' : ''; ?>>Payable</option>
					<option value="prepayments" <?php echo ($pmnt_by == "prepayments") ? 'selected' : ''; ?>>Prepayments</option>
				</select>
			</div>
			<div class="form-group col-md-2">
				<label>Status</label>
				<?php if ($action_dropdown_filter) { ?>
					<select id="tp" onChange="myFunction()" name="tp" class="form-control">
						<option <?= $tp == 'all' ? 'selected' : '' ?> value="all">All</option>
						<option <?= ($tp == 'a' || empty($tp)) ? 'selected' : '' ?> value="a">Active</option>
						<option <?= $tp == 'tr' ? 'selected' : '' ?> value="tr">Trashed</option>
					</select>
				<?php } else { ?>
					<input type="hidden" value='' id="tp" onChange="myFunction()" name="tp" class="form-control" />
				<?php } ?>
			</div>
			<div class="form-group col-md-4 col-sm-4">
				<label>Supplier/Company</label>
				<select id="comp" name="comp" onChange="myFunction()" class="form-control searchable">
					<?php
					$sql_opt = "SELECT DISTINCT comp as title FROM expence WHERE deleted_flag=0 ORDER BY comp ASC";
					$result_opt = mysqli_query($con, $sql_opt);
					$options = "";
					while ($row_opt = mysqli_fetch_array($result_opt)) {
						$code = $row_opt["title"];
						$name_opt = $row_opt["title"];
						// $title=urldecode($title);
						$options .= '<OPTION value="' . $code . '" ' . ((!empty($comp) && $comp == $code) ? 'selected' : '') . '>' . ((!empty($name_opt)) ? $name_opt : 'Empty');
					}
					?>
					<option value="">Select Supplier</option>
					<?php echo $options; ?>
					</option>
				</select>
			</div>
			<div class="form-group col-md-2">
				<label>Paid Date (From)</label>
				<input type="date" name="search_2" id="search_2" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $search_2; ?>" />
			</div>
			<div class="form-group col-md-2">
				<label>Paid Date (To)</label>
				<input type="date" name="search_3" id="search_3" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $search_3; ?>" />
			</div>

		</section>

		<?php
		$query = "
			SELECT 
				expence.*, 
				expence_list.title,
				DATE(
					COALESCE(
						(
							SELECT payment_date 
							FROM expence_partial_payments epp 
							WHERE epp.expence_id = expence.id AND epp.deleted_flag = 0 
							ORDER BY id DESC 
							LIMIT 1
						),
						expence.paid_on
					)
				) AS payment_date
			FROM 
				expence
			LEFT JOIN 
				expence_list ON expence.type_id = expence_list.id
			WHERE 
				{$condition}
		";

		// Inject date filter
			if (!empty($search_2) && !empty($search_3)) {
				$query .= " AND DATE(
					COALESCE(
						(
							SELECT payment_date 
							FROM expence_partial_payments epp 
							WHERE epp.expence_id = expence.id AND epp.deleted_flag = 0 
							ORDER BY id DESC 
							LIMIT 1
						),
						expence.paid_on
					)
				) BETWEEN '{$search_2}' AND '{$search_3}'";
			}

			// Add ORDER BY and LIMIT
			$query .= "
				ORDER BY DATE(
					COALESCE(
						(
							SELECT payment_date 
							FROM expence_partial_payments epp 
							WHERE epp.expence_id = expence.id AND epp.deleted_flag = 0 
							ORDER BY id DESC 
							LIMIT 1
						),
						expence.paid_on
					)
				) DESC
				LIMIT {$startpoint}, {$limit}";

		//debug($query);
		$result = mysqli_query($con, $query);
		?>
		<div class="row">
			<div class="col-sm-12 text-right p-t-10 p-b-10">
				<?php echo pagination($con, 'expence', $query, $limit, $page); ?>
			</div>
			<div class="col-sm-12">
				<table class="table table-bordered table-striped table-condensed table-hover">
					<thead class="bg-primary">
						<tr>
							<th>Track#</th>
							<th>Voucher#</th>
							<th>Exp. Title</th>
							<th>Net Amount</th>
							<th>VAT</th>
							<th>Non-VAT</th>
							<th>Total</th>
							<th>Paid</th>
							<th>Balance</th>
							<th>Details </th>
							<th>Invoice/Ref#</th>
							<th>Status</th>
							<th>Payment By</th>
							<th>Company</th>
							<th>Bill Date</th>
							<th>Paid On</th>
							<th></th>
							<?php if ($tp == 'tr') { ?><th>Deleted by</th> <?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php
						if (mysqli_num_rows($result) == 0) {
							echo '<tr><td colspan="13" class="text-left text-danger">No records found.</td></tr>';
						} else {
							$payable_array = array(
								'Payable',
								'payable',
								'PAYABLE'
							);
							foreach ($result as $row) {
								$e_id = $row['id'];

								if ($row['pay_by'] == 'bacs') {
									$payment_by = 'BACS';
								} else {
									$payment_by = $row['pay_by'] ? ucwords(strtolower($row['pay_by'])) : 'N/A';
								}

								$tr_title = '';

								$bank_details = '';
								if (in_array($row['pay_by'], $payable_array)) {
									$txt_cls = 'text-danger';
									$bg_danger = 'background-color:#f2dede4f;';
									$tr_title .= 'Payable Voucher';
								} else {

									$txt_cls = '';
									$bg_danger = '';

									if (!empty($row['payment_method_id'])) {

										// Fetch full bank/cash info including is_bank flag
										$bank_info = $acttObj->read_specific("name as bank_name, account_no, sort_code, iban_no, is_bank", "account_payment_modes", "id = " . $row['payment_method_id']);

										if (!empty($bank_info)) {
											$label = ($bank_info['is_bank'] == 1) ? 'Bank' : 'By';

											$bank_details = '<p class="fs-10 m-b-0">' . $label . ': ' . $bank_info['bank_name'];

											if ($bank_info['is_bank'] == 1) {
												$bank_details .= '<br>Account#: ' . $bank_info['account_no'];

												if (!empty($bank_info['sort_code'])) {
													$bank_details .= '<br>Sort Code: ' . $bank_info['sort_code'];
												}
												if (!empty($bank_info['iban_no'])) {
													$bank_details .= '<br>Iban No: ' . $bank_info['iban_no'];
												}
											}

											$bank_details .= '</p>';
										}
									}
								}

								if ($row['deleted_flag'] == 1) {
									$deleted_tr .= 'background-color: #edc4c4;';
									$tr_title .= ' [Deleted]';
								} else {
									$deleted_tr = '';
								}

								$status = $row['status'];
								$labelClass = '';

								if ($status === 'unpaid') {
									$labelClass = 'warning';
								} elseif ($status === 'full_paid' || $status === 'full_partial') {
									$labelClass = 'success';
								} elseif ($status === 'partial') {
									$labelClass = 'info';
								}

						?>
								<tr class="tr_data" style="<?php echo $bg_danger . $deleted_tr; ?>" title="Click on row to see actions <?php echo ($tr_title) ? '(' . $tr_title . ')' : ''; ?>">
									<td>
										<?php echo $row['invoice_no']; ?>
										<?php if($row['is_prepayment'] == 1){ ?>
											<label class="label label-primary">Prepayment</label>
										<?php } ?>
									</td>
									<td><?php echo $row['voucher']; ?></td>
									<td><?php echo $row['title']; ?></td>
									<td><?php echo $row['netamount']; ?></td>
									<td><?php echo $row['vat']; ?></td>
									<td><?php echo $row['nonvat']; ?></td>
									<td><?php echo $row['amoun']; ?></td>
									<td><?php echo $row['amountPaid']; ?></td>
									<td>
										<strong>
											<?php echo ($row['amoun'] - $row['amountPaid']); ?>
										</strong>
									</td>
									<td><?php echo $row['details']; ?></td>
									<td><?php echo $row['inv_ref_num']; ?></td>
									<td>
										<span class="label label-<?= $labelClass ?>">
											<?= ucwords(str_replace('_', ' ', $status)) ?>
										</span>
									</td>
									<td class="<?php echo $txt_cls; ?>">
										<?php echo ($payment_by == 'Payable') ? $payment_by : '<strong>' . $payment_by . '</strong>'; ?>
										<?php echo $bank_details; ?>
									</td>
									<td><?php echo $row['comp']; ?></td>
									<td><?php echo $misc->dated($row['billDate']); ?></td>
									<td>
										<?php echo $misc->dated($row['payment_date']); ?>
									</td>
									<td>
										<?php if ($row['exp_receipt']) { ?>
											<a data-toggle="tooltip" data-placement="top" href="javascript:void(0);" onClick="popupwindow('exp_receipt_view.php?v_id=<?php echo $row['id']; ?>', 'title', 1000,700);" class="btn btn-primary text-white btn-sm view_attach" title="View Receipt" id="<?php echo $row['id']; ?>">
												<i class="fa fa-paperclip"></i>
											</a>
										<?php } ?>
									</td>
									<?php if ($tp == 'tr') { ?>
										<td style="color:#F00"><?php echo $row['deleted_by'] . '(' . $misc->dated($row['deleted_date']) . ')' . ($row['deleted_reason'] ? ' 	<i class="fa fa-exclamation-circle" title="' . $row['deleted_reason'] . '"></i>' : ''); ?>
										</td>
									<?php } ?>
								</tr>
								<tr class="div_actions" style="display:none">
									<td colspan="9">
										<?php if ($action_view_expense) { ?>
											<?php if ($row['exp_receipt']) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0);" onClick="popupwindow('exp_receipt_view.php?v_id=<?php echo $row['id']; ?>', 'title', 1000,700);" class="btn btn-default btn-sm view_attach" title="View Receipt" id="<?php echo $row['id']; ?>"><i class="fa fa-paperclip"></i></a>
											<?php } ?>
											<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="btn btn-default btn-sm" title="View Expense" onClick="popupwindow('expence_view.php?view_id=<?php echo $row['id']; ?>', 'title', 1000,650);"><i class="fa fa-eye"></i></a>
										<?php } ?>

										<?php if ($tp != 'tr') {
											if ($action_receive_payment && ($row['status'] == 'unpaid' || in_array($row['pay_by'], $payable_array))) {
										?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="btn btn-success btn-sm text-white" title="Pay Expense" onClick="popupwindow('expense_pay.php?action=full&pay_id=<?php echo $row['id']; ?>','_blank',900,700)"><i class="fa fa-dollar"></i></a>
											<?php } ?>
											<?php if ($action_receive_partial_payment && ($row['status'] == 'unpaid' || $row['status'] == 'partial')) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="btn btn-info btn-sm text-white" title="Pay Partial Expense" onClick="popupwindow('expense_pay.php?action=partial&pay_id=<?php echo $row['id']; ?>','_blank',900,700)"><i class="fa fa-money"></i></a>
											<?php } ?>
											<?php if ($action_edit_expense) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="btn btn-default btn-sm" title="Edit Expense" onClick="popupwindow('expence_edit.php?edit_id=<?php echo $row['id']; ?>','_blank',1000,650)"><i class="fa fa-pencil-square-o"></i></a>
											<?php }

											if ($action_delete_expense) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="btn btn-danger btn-sm text-white" title="Trash" onClick="popupwindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)"><i class="fa fa-trash-o"></i></a>
											<?php }
											if ($action_expense_history) { ?>
												<!-- <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edited List" onClick="popupwindow('expence_list_edited.php?view_id=<?php echo $row['id']; ?>','_blank',900,800)"><i class="fa fa-list-alt"></i></a> -->
											<?php }
										} else { ?>
											<?php if ($action_restore_expense && $row['deleted_flag'] == 1) {
											?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="btn btn-danger btn-sm text-white" title="Restore Company" onClick="popupwindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)"><i class="fa fa-refresh"></i></a>
											<?php }
											?>
										<?php } ?>
									</td>
								</tr>
						<?php
							}
						}
						?>

					</tbody>
				</table>

				<div class="col-sm-12 text-right">
					<?php echo pagination($con, $table, $query, $limit, $page); ?>
				</div>

			</div>
			</section>


			<script src="js/jquery-1.11.3.min.js"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
			<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>

			<script>
				function popupwindow(url, title, w, h) {
					var left = (screen.width / 2) - (w / 2);
					var top = (screen.height / 2) - (h / 2);
					return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
				}

				$(function() {
					$('.searchable').multiselect({
						includeSelectAllOption: true,
						numberDisplayed: 1,
						enableFiltering: true,
						enableCaseInsensitiveFiltering: true
					});
				});

				$('.tr_data').click(function() {
					var $nextRow = $(this).next('.div_actions');
					// Check if the next row is visible
					if ($nextRow.is(':visible')) {
						$nextRow.hide();
					} else {
						$('.div_actions').hide(); // Hide all other action rows
						$nextRow.show(); // Show this one
					}
				});

				// $(document).on('click', '.view_attach', function(e) {
				// 	e.preventDefault();
				// 	window.open('exp_receipt_view.php?v_id=' + this.id, "popupWindow", "width=600,height=600,scrollbars=yes");
				// });
			</script>
</body>

</html>