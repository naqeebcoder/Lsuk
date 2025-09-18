<?php
include 'class.php';
include 'function.php';
include 'db.php';

if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}

$allowed_type_idz = "237";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
	$get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
	if (empty($get_page_access)) {
		die("<center><h2 class='text-center text-danger'>You do not have access to <u>Add Payment</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
	}
}


$table = 'account_journal_ledger';
$search_2 = SafeVar::GetVar('search_2', '');
$search_3 = SafeVar::GetVar('search_3', '');
$voucher_no = SafeVar::GetVar('voucher_no', '');
$invoice_no = SafeVar::GetVar('invoice_no', '');
$company = SafeVar::GetVar('company', '');
$bank_id = SafeVar::GetVar('bank_id', '');
$category = SafeVar::GetVar('cat', '');
$status = SafeVar::GetVar('status', '');

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;

?>
<!doctype html>
<html lang="en">

<head>
	<title>Journal Ledger Cash Statement</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="css1/grey.css" />
	<link rel="stylesheet" type="text/css" href="css1/pagination.css" />
	<link rel="stylesheet" type="text/css" href="css/util.css" />
	<link rel="icon" type="image/png" href="img/logo.png">
</head>

<?php //include "incmultiselfiles.php";
?>


<body>
	<?php include 'nav2.php'; ?>
	<!-- end of sidebar -->
	<style>
		.tablesorter thead tr {
			background: none;
		}

		.table-condensed>tbody>tr>td,
		.table-condensed>tbody>tr>th,
		.table-condensed>thead>tr>td,
		.table-condensed>thead>tr>th {
			font-size: 13px;
		}
	</style>
	<section class="container-fluid" style="overflow-x:auto">
		<div class="col-md-12">
			<center>
				<h2 class="col-md-4 col-md-offset-4 text-center m-b-50">
					<div class="label label-primary">
						<a href="<?php echo basename(__FILE__); ?>">Journal Ledger Cash</a>
					</div>
				</h2>
			</center>
		</div>

		<div class="col-md-12 m-b-30">
			<div class="row">
				<div class="form-group col-md-2 col-sm-3">
					<label>Invoice No/Track#</label>
					<input type="text" name="invoice_no" id="invoice_no" placeholder='Invoice/Track#' class="form-control" value="<?php echo $invoice_no; ?>" />
				</div>
				<div class="form-group col-md-2 col-sm-3">
					<label>Voucher No</label>
					<input type="text" name="voucher_no" id="voucher_no" placeholder='Voucher No' class="form-control" value="<?php echo $voucher_no; ?>" />
				</div>
				<div class="form-group col-md-2 col-sm-3">
					<label>Date (from)</label>
					<input type="date" name="search_2" id="search_2" placeholder='' class="form-control"
						onChange="myFunction()" value="<?php echo $search_2; ?>" />
				</div>
				<div class="form-group col-md-2 col-sm-3">
					<label>Date (to)</label>
					<input type="date" name="search_3" id="search_3" placeholder='' class="form-control"
						onChange="myFunction()" value="<?php echo $search_3; ?>" />
				</div>
				<div class="form-group col-md-2 col-sm-3">
					<label>Company</label>
					<input type="text" name="company" id="company" placeholder='Company' class="form-control" value="<?php echo $company; ?>" />
				</div>
				<div class="form-group col-md-2 col-sm-3">
					<label>Category</label>
					<select name="category" id="category" class="form-control" onChange="myFunction()">
						<option value="all" <?php echo ($category == 'all' || empty($category)) ? "selected" : ""; ?>>All</option>
						<option value="income" <?php echo ($category == 'income') ? "selected" : ""; ?>>Income</option>
						<option value="expense" <?php echo ($category == 'expense') ? "selected" : ""; ?>>Expense</option>
						<option value="prepayment" <?php echo ($category == 'prepayment') ? "selected" : ""; ?>>Prepayment</option>
						?>
					</select>
				</div>
				<div class="form-group col-md-2 col-sm-3">
					<label>Recv. By</label>
					<?php
					$bank_infos = $acttObj->full_fetch_array("SELECT id, name as bank_name, account_no, sort_code, iban_no FROM account_payment_modes WHERE is_bank = 0 AND status = 1 ORDER BY name");
					?>
					<select name="bank_id" id="bank_id" class="form-control">
						<option value="">- Select -</option>
						<?php foreach ($bank_infos as $info) { ?>
							<option value="<?php echo $info['id']; ?>" <?php echo ($bank_id == $info['id']) ? 'selected' : ''; ?>>
								<?php echo $info['bank_name']; ?>
							</option>
						<?php } // end foreach 
						?>
					</select>
				</div>
				<div class="form-group col-md-2 col-sm-3">
					<label>Status</label>
					<?php
					$journal_status = $acttObj->full_fetch_array("SELECT DISTINCT status FROM account_journal_ledger WHERE is_bank = 0 ORDER BY status");
					?>
					<select name="status" id="status" class="form-control" onChange="myFunction()">
						<option value="all">All</option>
						<?php foreach ($journal_status as $j_status) { ?>
							<option value="<?php echo $j_status['status']; ?>" <?php echo ($j_status['status'] == $status) ? 'selected' : ''; ?>>
								<?php echo ucwords(str_replace('_', ' ', $j_status['status'])); ?>
							</option>
						<?php } // end foreach 
						?>
					</select>
				</div>
			</div>
			<div class="row display-inline">
				<div class="col-lg-12">
					<?php
					// Check if query string exists
					if ($_SERVER['QUERY_STRING']) {
						// Check if all search fields are empty
						$allEmpty = empty($search_2) && empty($search_3) && empty($voucher_no) && empty($invoice_no) && empty($company) && empty($bank_id);
					?>
						<div class="pull-left alert alert-danger p-3 p-l-10 p-r-10 m-b-0">
							<strong>Search Result:</strong>
							<?php if ($allEmpty): ?>
								All
							<?php else: ?>
								<?php echo ($search_2) ? $misc->dated($search_2) : '';  ?>
								<?php echo ($search_3) ? ' - ' . $misc->dated($search_3) : '';  ?>
								<?php echo ($voucher_no) ? ' / Voucher# ' . $voucher_no : '';  ?>
								<?php echo ($invoice_no) ? ' / Invoice# ' . $invoice_no : '';  ?>
								<?php echo ($company) ? ' / Company: ' . $company : '';  ?>
								<?php echo ($bank_id) ? ' / Bank: ' . $acttObj->read_specific("name", "account_payment_modes", "is_bank = 1 AND status = 1 AND id = " . $bank_id)['name'] : '';  ?>
							<?php endif; ?>
						</div>
					<?php } ?>
					<div class=" text-right pull-right">
						<a href="javascript:void(0)" title="Click to Get Report" onclick="myFunction()">
							<span class="btn btn-sm btn-primary">Get Report</span>
						</a>

						<a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&voucher_no=<?php echo $voucher_no; ?>&invoice_no=<?php echo $invoice_no; ?>&company=<?php echo $company; ?>&bank_id=<?php echo $bank_id; ?>"
							title="Download Excel Report">
							<span class="btn btn-sm btn-success">Export To Excel</span>
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-12">
			<?php

			$conditions = " is_bank = 0";

			$opening_balance_conditions = $conditions; // base condition for opening balance

			if (!empty($search_2) && !empty($search_3)) {
				$conditions .= " AND DATE(dated) BETWEEN '{$search_2}' AND '{$search_3}'";
			}

			if (!empty($voucher_no)) {
				$conditions .= " AND voucher LIKE '%{$voucher_no}%'";
			}
			if (!empty($invoice_no)) {
				$conditions .= " AND invoice_no LIKE '%{$invoice_no}%'";
			}
			if (!empty($company)) {
				$conditions .= " AND company LIKE '%{$company}%'";
			}
			if (!empty($bank_id)) {
				$conditions .= " AND account_id = {$bank_id}";
				$opening_balance_conditions .= " AND account_id = {$bank_id}"; // apply same to opening balance
			}

			if (isset($category)) {
				if ($category === 'all') {
					$conditions .= " AND (is_receivable = 0 OR is_receivable = 1 OR is_receivable = 2)";
				} elseif ($category === 'expense') {
					$conditions .= " AND is_receivable = 0";
				} elseif ($category === 'income') {
					$conditions .= " AND is_receivable = 1";
				} elseif ($category === 'prepayment') {
					$conditions .= " AND is_receivable = 2";
				}
			}

			if (!empty($status)) {
				if ($status == 'all') {
					$conditions .= "";
				} else {
					$conditions .= " AND status = '{$status}'";
				}
			}


			$query = "
				WITH opening_balance_cte AS (
					SELECT 
						ROUND(IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0), 2) AS opening_balance
					FROM 
						account_journal_ledger
					WHERE 
						{$opening_balance_conditions}
						AND id < (
							SELECT IFNULL(MIN(id), 0)
							FROM account_journal_ledger
							WHERE {$conditions}
						)
				),
			
				transaction_data AS (
					SELECT 
						id,
						is_receivable,
						dated,
						credit,
						debit,
						posted_by,
						posted_on,
						voucher,
						invoice_no,
						company,
						is_bank,
						payment_type,
						account_id,
						description,
						balance,
						status,
						SUM(debit - credit) OVER (
							ORDER BY id 
							ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
						) AS transaction_running_balance
					FROM 
						account_journal_ledger
					WHERE 
						{$conditions}
				)
			
				SELECT 
					t.*,
					ob.opening_balance,
					ROUND(ob.opening_balance + t.transaction_running_balance, 2) AS running_balance
				FROM 
					transaction_data t
				CROSS JOIN 
					opening_balance_cte ob
				ORDER BY 
					t.id
				LIMIT {$startpoint}, {$limit}";


			//debug($query);
			//echo $query;
			$total_records = $acttObj->read_specific("COUNT(*) AS total", "account_journal_ledger", $conditions)['total'];
			?>

			<div class="col-sm-12 text-right">
				<?php echo pagination($con, $table, $query, $limit, $page); ?>
			</div>

			<?php
			$result = $con->query($query);
			$res = $result->fetch_all(MYSQLI_ASSOC);
			?>
			<table class="table table-striped table-bordered table-condensed table-hover" id="dt_receivable">
				<thead>
					<tr>
						<th>Invoice/Track#</th>
						<th>Voucher</th>
						<th>Dated</th>
						<th>Company</th>
						<th>Recv. By</th>
						<th>Description</th>
						<th>Credit</th>
						<th>Debit</th>
						<th>Balance (£)</th>
					</tr>
				</thead>
				<tbody>
					<!--tr>
						<td colspan="7" align="right">Opening Balance</td>
						<td>
							<?php //echo $misc->numberFormat_fun($res[0]["opening_balance"]); 
							?>
						</td>
					</tr-->
					<?php
					if (count($res) > 0) {
						foreach ($res as $row) {
					?>
							<tr style="<?php echo ($row['status'] != 'paid') ? 'background-color: #dfa4211a' : ''; ?>"
								title="<?php echo ($row['status'] != 'paid') ? ucwords(str_replace('_', ' ', $row['status'])) : ''; ?>">
								<td>
									<?= $row["invoice_no"]; ?><br>
									<label class="label label-<?= 
										$row['is_receivable'] == 1 ? 'success' : 
										($row['is_receivable'] == 0 || $row['is_receivable'] == 3 ? 'danger' : 'warning'); 
									?>">

										<?php
										if ($row['is_receivable'] == 0) {
											echo 'Expense';
										} else if ($row['is_receivable'] == 1) {
											echo 'Income';
										} else if ($row['is_receivable'] == 2) {
											echo 'Prepayment';
										} else if ($row['is_receivable'] == 3) {
											echo 'Interpreter Salary';
										}
										?>
									</label>
								</td>
								<td><?= $row["voucher"]; ?></td>
								<td><?= $misc->dated($row['dated']); ?></td>
								<td><?= $row["company"]; ?></td>
								<td>
									<?php
									$bank_info = $acttObj->read_specific("name as bank_name, account_no, sort_code, iban_no", "account_payment_modes", " is_bank = 0 AND id = " . $row['account_id']);

									echo ucwords($row["payment_type"]);

									if ($bank_info['bank_name']) {
										echo '<p class="fs-10 m-b-0"> By: ' . ucwords($bank_info['bank_name']) . '</p>';
									}

									?>
								</td>
								<td><?= $row["description"]; ?></td>
								<td><?= ($row["credit"]) ? $misc->numberFormat_fun($row["credit"]) : ''; ?></td>
								<td><?= ($row["debit"]) ? $misc->numberFormat_fun($row["debit"]) : ''; ?></td>
								<td><?= $misc->numberFormat_fun($row["running_balance"]); ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="9">
								No record found.
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

		</div>

		<div class="clear"></div>

	</section>

	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<!--script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
	<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script-->

	<script>
		const currentPage = <?php echo isset($_GET['page']) ? (int)$_GET['page'] : 0; ?>;
		const totalPages = <?php echo max(1, ceil($total_records / $limit)); ?>;
		const url = new URL(window.location.href);

		// If no page param and total pages > 1, redirect to last page
		if (!url.searchParams.has('page') && totalPages > 1) {
			url.searchParams.set('page', totalPages);
			window.location.href = url.toString();
		}
	</script>
	
	<script type="text/javascript">
		function myFunction() {
			var y = $('#search_2').val();
			var z = $('#search_3').val();
			var v = $('#voucher_no').val();
			var i = $('#invoice_no').val();
			var c = $('#company').val();
			var b = $('#bank_id').val();
			var cat = $('#category').val();
			var status = $('#status').val();

			var strLoc = '<?php echo basename(__FILE__); ?>' + '?search_2=' + y + '&search_3=' + z + '&voucher_no=' + v + '&invoice_no=' + i + '&company=' + c + '&bank_id=' + b + '&cat=' + cat + '&status=' + status;

			// var strLoc = '<?php echo basename(__FILE__); ?>' + '?search_2=' + y + '&search_3=' + z + '&voucher_no=' + v + '&invoice_no=' + i + '&company=' + c + '&bank_id=' + b;
			window.location.assign(strLoc);
		}

		/*var table = $('#dt_receivable');

		$(document).ready(function(e){
			ajaxDatatable();
			var tableWrapper = jQuery('#dt_receivable');
			
			function ajaxDatatable() {
				table.dataTable({
				   "container": "btn-group tabletools-dropdown-on-portlet",
				  "buttons": {
					  "normal": "btn btn-sm default",
					  "disabled": "btn btn-sm default disabled"
				  },
				  "collection": {
					  "container": "DTTT_dropdown dropdown-menu tabletools-dropdown-menu"
				  },
					"processing": true,
					"serverSide": false,
				  "ajax": "account_receivable_statement_ajax.php?search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>",
				  "language": {
					  "aria": {
						  "sortAscending": ": activate to sort column ascending",
						  "sortDescending": ": activate to sort column descending"
					  },
					  "emptyTable": "No data available in table",
					  "info": "Showing _START_ to _END_ of _TOTAL_ records",
					  "infoEmpty": "No records found",
					  "infoFiltered": "(filtered1 from _MAX_ total records)",
					  "lengthMenu": "Show _MENU_ records",
					  "search": "Search:",
					  "zeroRecords": "No matching records found",
					  "paginate": {
						  "previous": "Prev",
						  "next": "Next",
						  "last": "Last",
						  "first": "First"
					  }
				  },
			  });
			}
			
		});*/
	</script>

</body>


</html>