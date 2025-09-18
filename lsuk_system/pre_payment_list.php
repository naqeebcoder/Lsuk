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
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=225 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);

$route_add_prepayment = $_SESSION['is_root'] == 1 || in_array(243, $get_actions);
$route_edit_prepayment = $_SESSION['is_root'] == 1 || in_array(244, $get_actions);
$route_view_prepayment = $_SESSION['is_root'] == 1 || in_array(245, $get_actions);
$route_delete_prepayment = $_SESSION['is_root'] == 1 || in_array(246, $get_actions);
$route_restore_prepayment = $_SESSION['is_root'] == 1 || in_array(247, $get_actions);
$route_pay_prepayment = $_SESSION['is_root'] == 1 || in_array(248, $get_actions);

$table = 'pre_payments';
$expense_type = @$_GET['expense_type'];
$comp = @$_GET['comp'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$pmnt_by = @$_GET['pmnt_by'];
$tp = @$_GET['tp'];
$inv = @$_GET['inv'];
$vhr = @$_GET['vhr'];
$pstatus = @$_GET['pstatus'];
$pdate = @$_GET['pdate'];

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;

$class = $tp == 'tr' ? 'alert-danger' : 'alert-info';

?>
<!doctype html>
<html lang="en">

<head>
	<title>Prepayment List</title>
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
		var expense_type = document.getElementById("expense_type").value;
		if (!expense_type) {
			expense_type = "<?php echo $expense_type; ?>";
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
		var sp = document.getElementById("comp").value;
		if (!sp) {
			sp = "<?php echo $comp; ?>";
		}
		var pmnt_by = document.getElementById("pmnt_by").value;
		if (!pmnt_by) {
			pmnt_by = "<?php echo $pmnt_by; ?>";
		}
		var inv = document.getElementById("invoice_no").value;
		if (inv) {
			inv = '&inv=' + inv;
		}
		var vhr = document.getElementById("voucher").value;
		if (vhr) {
			vhr = '&vhr=' + vhr;
		}
		var pdate = document.getElementById("pdate").value;
		if (pdate) {
			pdate = '&pdate=' + pdate;
		}
		
		window.location.href = '?comp=' + encodeURIComponent(sp) + '&search_2=' + y + '&search_3=' + z + '&tp=' + tp + inv + vhr + "&pmnt_by=" + pmnt_by + "&expense_type="+expense_type + pdate;
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
					<a href="<?php echo basename(__FILE__); ?>" class="alert-link"><?php echo $page_title; ?> Prepayment List</a>
				</div>
			</center>
		</section>

		<?php 
			$condition = '';
			
			if ($tp == 'tr') {
				$condition .= $table . ".deleted_flag = 1";
			} else if ($tp == 'all') {
				$condition .= "((" . $table . ".deleted_flag = 0 OR " . $table . ".deleted_flag IS NULL) OR " . $table . ".deleted_flag = 1)";
			} else {
				$condition .= "(" . $table . ".deleted_flag = 0 OR " . $table . ".deleted_flag IS NULL)";
			}

			if (!empty($inv)) {
				$condition .= " AND {$table}.invoice_no LIKE '{$inv}'";
			}

			if (!empty($vhr)) {
				$condition .= " AND {$table}.voucher LIKE '{$vhr}'";
			}

			if (!empty($expense_type)) {
				$condition .= " AND $table.category_id = {$expense_type}";
			}

			if (!empty($comp)) {
				$condition .= " AND {$table}.receiver_id = '" . trim($comp) . "'";
			}

			if (!empty($search_2) && !empty($search_3)) {
			    $condition .= " AND DATE($table.from_date) BETWEEN '{$search_2}' AND '{$search_3}'";
			}
			
			if (!empty($pdate)) {
			    $condition .= " AND DATE($table.payment_date) = '{$pdate}'";
			}

			if (!empty($pmnt_by)) {
				if ($pmnt_by !== 'all' && $pmnt_by !== 'payable' && $pmnt_by !== 'paid') {
					$condition .= " AND {$table}.payment_type = '{$pmnt_by}'";
				}
				if ($pmnt_by == 'payable') {
					$condition .= " AND {$table}.is_payable = 1";
				}
				if ($pmnt_by == 'paid') {
					$condition .= " AND {$table}.status = 'paid'";
				}
			}

			$sumQuery = "
			SELECT 
				total_net_amount,
				total_vat,
				total_nonvat,
				total_amount,
				total_spent_amount,
				balance_amount,
				CASE WHEN grand_total > 0 THEN (total_net_amount / grand_total) * 100 ELSE 0 END AS net_amount_percentage,
				CASE WHEN grand_total > 0 THEN (total_vat / grand_total) * 100 ELSE 0 END AS vat_percentage,
				CASE WHEN grand_total > 0 THEN (total_nonvat / grand_total) * 100 ELSE 0 END AS nonvat_percentage,
				CASE WHEN grand_total > 0 THEN (total_amount / grand_total) * 100 ELSE 0 END AS total_amount_percentage,
				CASE WHEN total_amount > 0 THEN (total_spent_amount / total_amount) * 100 ELSE 0 END AS total_spent_percentage,
				CASE WHEN total_amount > 0 THEN (balance_amount / total_amount) * 100 ELSE 0 END AS balance_percentage
			FROM (
				SELECT 
					SUM({$table}.net_amount) AS total_net_amount, 
					SUM({$table}.vat) AS total_vat, 
					SUM({$table}.nonvat) AS total_nonvat, 
					SUM({$table}.total_amount) AS total_amount, 
					IFNULL(SUM(e.total_spent_amount), 0) AS total_spent_amount,
					(SUM({$table}.total_amount) - IFNULL(SUM(e.total_spent_amount), 0)) AS balance_amount,
					-- Grand total for percentage reference
					(SUM({$table}.net_amount) + SUM({$table}.vat) + SUM({$table}.nonvat)) AS grand_total
				FROM {$table}
				LEFT JOIN (
					SELECT prepayment_id, SUM(amoun) AS total_spent_amount
					FROM expence
					GROUP BY prepayment_id
				) e 
					ON e.prepayment_id = {$table}.invoice_no
				LEFT JOIN prepayment_receivers pr 
					ON pr.id = {$table}.receiver_id
				LEFT JOIN prepayment_categories pc 
					ON pc.id = {$table}.category_id
				WHERE {$condition}
			) AS t
			";

			// debug($sumQuery);

			$sumResult = mysqli_query($con, $sumQuery);
			$totals = mysqli_fetch_assoc($sumResult);

		?>

		<div class="row">
			<div class="col-lg-2 col-sm-6">
				<div class="card">
					<div class="stat-widget-two card-body">
						<div class="stat-content">
							<div class="stat-text">Net Amount</div>
							<div class="stat-digit"> £ <?php echo number_format($totals['total_net_amount']); ?></div>
						</div>
						<div class="progress">
							<div class="progress-bar progress-bar-warning"
								style="width: <?php echo $totals['net_amount_percentage']; ?>%;"
								role="progressbar"
								aria-valuenow="<?php echo $totals['net_amount_percentage']; ?>"
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
							<div class="progress-bar progress-bar-success" 
							style="width: <?php echo $totals['progress_vat']; ?>%;" 
							role="progressbar" aria-valuenow="<?php echo $totals['progress_vat']; ?>" 
							aria-valuemin="0" aria-valuemax="100">
							</div>
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
							<div class="progress-bar progress-bar-warning" 
							style="width: <?php echo $totals['vat_pernonvat_percentagecentage']; ?>%;" 
							role="progressbar" 
							aria-valuenow="<?php echo $totals['nonvat_percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
							<div class="stat-text">Spent Amount</div>
							<div class="stat-digit"> £ <?php echo number_format($totals['total_spent_amount']); ?></div>
						</div>
						<div class="progress">
							<div class="progress-bar progress-bar-success" style="width:<?php echo $totals['total_spent_percentage']; ?>%" role="progressbar" aria-valuenow="<?php echo $totals['total_spent_percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-2 col-sm-6">
				<div class="card">
					<div class="stat-widget-two card-body">
						<div class="stat-content">
							<div class="stat-text">Balance</div>
							<div class="stat-digit"> £ <?php echo number_format($totals['balance_amount']); ?></div>
						</div>
						<div class="progress">
							<div class="progress-bar progress-bar-danger" style="width:<?php echo $totals['balance_percentage']; ?>%" role="progressbar" aria-valuenow="<?php echo $totals['balance_percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
			<div class="form-group col-md-3">
				<label>Category</label>
				<select id="expense_type" name="expense_type" onChange="myFunction()" class="form-control searchable">
					<?php
					$sql_opt = "SELECT id, title FROM prepayment_categories ORDER BY title ASC";
					$result_opt = mysqli_query($con, $sql_opt);
					$options = "";
					while ($row_opt = mysqli_fetch_array($result_opt)) {
						$code = $row_opt["title"];
						$name_opt = $row_opt["title"];
						$options .= "<OPTION value='".$row_opt['id']."'>" . $name_opt;
					}
					?>
					<?php if (!empty($title)) { ?>
						<option><?php echo $title; ?></option>
					<?php } else { ?>
						<option value="">Select Category</option>
					<?php } ?>
					<?php echo $options; ?>
					</option>
				</select>
			</div>
			<div class="form-group col-md-3 col-sm-3">
				<label>Receiver</label>
				<select id="comp" name="comp" onChange="myFunction()" class="form-control searchable">
					<?php
					$sql_opt = "SELECT * FROM prepayment_receivers where deleted_flag=0 ORDER BY title ASC";
					$result_opt = mysqli_query($con, $sql_opt);
					$options = "";
					while ($row_opt = mysqli_fetch_array($result_opt)) {
						$options .= '<OPTION value="' . $row_opt['id'] . '" ' . ((!empty($comp) && $comp == $row_opt['id']) ? 'selected' : '') . '>' . $row_opt['title'];				
					}
					?>
					<option value="">Select Receiver</option>
					<?php echo $options; ?>
					</option>
				</select>
			</div>
			<div class="form-group  col-md-2  ">
				<label>Payment Status</label>
				<select id="pmnt_by" name="pmnt_by" onChange="myFunction()" class="form-control">
					<option value="">Payment By</option>
					<option value="all" <?php echo (empty($pmnt_by) || $pmnt_by == "all") ? 'selected' : ''; ?>>All</option>
					<option value="bacs" <?php echo ($pmnt_by == "bacs") ? 'selected' : ''; ?>>BACS</option>
					<option value="cheque" <?php echo ($pmnt_by == "cheque") ? 'selected' : ''; ?>>Cheque</option>
					<option value="card" <?php echo ($pmnt_by == "card") ? 'selected' : ''; ?>>Credit/Debit Card</option>
					<option value="cash" <?php echo ($pmnt_by == "cash") ? 'selected' : ''; ?>>Cash</option>
					<option value="paid" <?php echo ($pmnt_by == "paid") ? 'selected' : ''; ?>>Paid</option>
					<option value="payable" <?php echo ($pmnt_by == "payable") ? 'selected' : ''; ?>>Payable</option>

				</select>
			</div>
			<div class="form-group col-md-2">
				<label>Date (From)</label>
				<input type="date" name="search_2" id="search_2" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $search_2; ?>" />
			</div>
			<div class="form-group col-md-2">
				<label>Date (To)</label>
				<input type="date" name="search_3" id="search_3" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $search_3; ?>" />
			</div>
			<div class="form-group col-md-2">
				<label>Payment Date</label>
				<input type="date" name="pdate" id="pdate" placeholder="Payment Date" class="form-control" onChange="myFunction()" value="<?php echo $pdate; ?>" />
			</div>
			<div class="form-group col-md-2">
				<label>Status</label>
					<select id="tp" onChange="myFunction()" name="tp" class="form-control">
						<option <?= $tp == 'all' ? 'selected' : '' ?> value="all">All</option>
						<option <?= ($tp == 'a' || empty($tp)) ? 'selected' : '' ?> value="a">Active</option>
						<option <?= $tp == 'tr' ? 'selected' : '' ?> value="tr">Trashed</option>
					</select>
			</div>
			

		</section>

		<?php
		$query = "
			SELECT 
				$table.*, 
				prepayment_categories.title as category, prepayment_receivers.title as receiver,
				(SELECT COUNT(*) FROM expence e WHERE e.prepayment_id = $table.invoice_no AND e.deleted_flag = 0) as total_exp_records,
				(SELECT COALESCE(SUM(e.amoun), 0) FROM expence e WHERE e.prepayment_id = $table.invoice_no AND e.deleted_flag = 0) as total_exp_paid,
				($table.total_amount - (SELECT COALESCE(SUM(e.amoun), 0) FROM expence e WHERE e.prepayment_id = $table.invoice_no AND e.deleted_flag = 0)) as balance_amount
			FROM 
				$table
			LEFT JOIN prepayment_categories ON prepayment_categories.id = $table.category_id
			LEFT JOIN prepayment_receivers ON prepayment_receivers.id = $table.receiver_id
			WHERE 
				{$condition}
			ORDER BY id DESC
			LIMIT {$startpoint}, {$limit}";

		// debug($query);
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
							<th>Category</th>
							<th>Net Amount</th>
							<th>VAT</th>
							<th>Non-VAT</th>
							<th>Total</th>
							<th>Total Spent</th>
							<th>Balance</th>
							<th>Details </th>
							<th>Receiver</th>
							<th>No of Installment</th>
							<th>Frequency</th>
							<th>Payment Date</th>
							<th>Dated</th>
							<th>Status</th>
							<th>Payment Status</th>
							<th></th>
							<?php if ($tp == 'tr') { ?><th>Deleted by</th> <?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php
						if (mysqli_num_rows($result) == 0) {
							echo '<tr><td colspan="14" class="text-left text-danger">No records found.</td></tr>';
						} else {
							foreach ($result as $row) {
								$e_id = $row['id'];

								if ($row['payment_type'] == 'bacs') {
									$payment_by = 'BACS';
								} else {
									$payment_by = ucwords($row['payment_type']);
								}

								$tr_title = '';

								$bank_details = '';
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
								
								if ($row['deleted_flag'] == 1) {
									$deleted_tr .= 'background-color: #edc4c4;';
									$tr_title .= ' [Deleted]';
								} else {
									$deleted_tr = '';
								}

								$labelClass = '';

						?>
								<tr class="tr_data" style="<?php echo $deleted_tr; ?>" title="Click on row to see actions <?php echo ($tr_title) ? '(' . $tr_title . ')' : ''; ?>">
									<td><?php echo $row['invoice_no']; ?></td>
									<td><?php echo $row['voucher']; ?></td>
									<td><?php echo $row['category']; ?></td>
									<td><?php echo $row['net_amount']; ?></td>
									<td><?php echo $row['vat']; ?></td>
									<td><?php echo $row['nonvat']; ?></td>
									<td>
										<strong><?php echo $row['total_amount']; ?></strong>
									</td>
									<td><?php echo $row['total_exp_paid']; ?></td>
									<td>
										<strong>
											<?php echo $row['balance_amount']; ?>
										</strong>
									</td>
									<td><?php echo $row['description']; ?></td>
									<td><?php echo $row['receiver']; ?></td>
									<td><?php echo $row['no_of_installment']; ?></td>
									<td><?php echo ucwords($row['frequency']); ?></td>
									<td><?php echo $misc->dated($row['payment_date']); ?></td>
									<td><?php echo $misc->date_time($row['dated']); ?></td>
									<td style="vertical-align: middle;">
										<label class="label label-<?php echo ($row['status'] == 'paid') ? 'success' : (empty($row['status']) ? 'danger' : 'warning'); ?> fs-11">
											<?php echo (!empty($row['status'])) ? ucwords($row['status']) : 'Deleted'; ?>
										</label>
									</td>
									<td class="">
										<?php echo '<strong>' . $payment_by . '</strong>'; ?>
										<?php echo $bank_details; ?>
									</td>
									<td>
										<?php if ($route_view_prepayment) { ?>
											<?php if ($row['attachment']) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0);" onClick="popupwindow('prepayment_receipt_view.php?v_id=<?php echo $row['id']; ?>', 'title', 1000,700);" class="btn btn-primary text-white btn-sm view_attach" title="View Receipt" id="<?php echo $row['id']; ?>">
													<i class="fa fa-paperclip"></i>
												</a>
											<?php } ?>
										<?php } ?>
									</td>
									<?php if ($tp == 'tr') { ?>
										<td style="color:#F00"><?php echo $row['deleted_by'] . '(' . $misc->dated($row['deleted_date']) . ')' . ($row['deleted_reason'] ? ' 	<i class="fa fa-exclamation-circle" title="' . $row['deleted_reason'] . '"></i>' : ''); ?>
										</td>
									<?php } ?>
								</tr>
								<tr class="div_actions" style="display:none">
									<td colspan="9">
										<?php if ($route_view_prepayment) { ?>
											<?php if ($row['attachment']) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0);" onClick="popupwindow('prepayment_receipt_view.php?v_id=<?php echo $row['id']; ?>', 'title', 1000,700);" class="btn btn-default btn-sm view_attach" title="View Receipt" id="<?php echo $row['id']; ?>"><i class="fa fa-paperclip"></i></a>
											<?php } ?>
											<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="btn btn-default btn-sm" title="View Prepayment" onClick="popupwindow('prepayment_view.php?view_id=<?php echo $row['id']; ?>', 'title', 1000,650);"><i class="fa fa-eye"></i></a>
										<?php } ?>

										<?php if ($tp != 'tr') { ?>
											
											<?php if ($row['total_exp_records'] > 0) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0);" onClick="popupwindow('prepayment_view_history.php?v_id=<?php echo $row['invoice_no']; ?>', 'View History', 1200,600);" class="btn btn-default btn-sm" title="View History" id="<?php echo $row['invoice_no']; ?>">
													<i class="fa fa-bars"></i>
												</a>
										<?php } ?>

										<?php if ($route_pay_prepayment && $row['is_payable'] == 1) { ?>
											<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="btn btn-success btn-sm text-white" title="Pay Expense" onClick="popupwindow('prepayment_pay.php?action=full&pay_id=<?php echo $row['id']; ?>','_blank',900,700)"><i class="fa fa-dollar"></i></a>
										<?php } ?>
										<?php if ($route_edit_prepayment) { ?>
											<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="btn btn-default btn-sm" title="Edit Prepayment" onClick="popupwindow('prepayment_edit.php?edit_id=<?php echo $row['id']; ?>','_blank',1000,650)"><i class="fa fa-pencil-square-o"></i></a>
										<?php } ?>

											<?php if ($route_delete_prepayment) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="btn btn-danger btn-sm text-white" title="Trash" onClick="popupwindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)"><i class="fa fa-trash-o"></i></a>
											<?php } ?>

										<?php } else { ?>
											<?php if ($route_restore_prepayment && $row['deleted_flag'] == 1) {
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