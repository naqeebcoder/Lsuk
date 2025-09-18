<?php
include 'class.php';
include 'function.php';
include 'db.php';

if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}

$allowed_type_idz = "235";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
	$get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
	if (empty($get_page_access)) {
		die("<center><h2 class='text-center text-danger'>You do not have access to <u>Add Payment</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
	}
}


$table = 'account_receivable';
$search_1 = SafeVar::GetVar('search_1', '');
$search_2 = SafeVar::GetVar('search_2', '');
$search_3 = SafeVar::GetVar('search_3', '');
$voucher_no = SafeVar::GetVar('voucher_no', '');
$invoice_no = SafeVar::GetVar('invoice_no', '');
$company = SafeVar::GetVar('company', '');

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;

?>
<!doctype html>
<html lang="en">
<head>
	<title>Receivable Statement - Accounts</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="css1/grey.css" />
	<link rel="stylesheet" type="text/css" href="css1/pagination.css" />
	<link rel="stylesheet" type="text/css" href="css/util.css" />
	<link rel="icon" type="image/png" href="img/logo.png">
	
</head>

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
						<a href="<?php echo basename(__FILE__); ?>">Receivable Statement</a>
					</div>
				</h2>
			</center>
		</div>

		<div class="row">
			<div class="col-md-12 m-b-30">
				<div class="form-group col-md-2 col-sm-3">
					<label>Invoice No/Track#</label>
					<input type="text" name="invoice_no" id="invoice_no" placeholder='Invoice No/Track#' class="form-control" onChange="myFunction()" value="<?php echo $invoice_no; ?>" />
				</div>
				<div class="form-group col-md-2 col-sm-3">
					<label>Voucher No</label>
					<input type="text" name="voucher_no" id="voucher_no" placeholder='Voucher No' class="form-control" onChange="myFunction()" value="<?php echo $voucher_no; ?>" />
				</div>
				<div class="form-group col-md-2 col-sm-3">
					<label>Company</label>
					<input type="text" name="company" id="company" placeholder='Company' class="form-control" onChange="myFunction()" value="<?php echo $company; ?>" />
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
				<!-- <div class="form-group col-md-1 col-sm-2 text-right">
				<a href="javascript:void(0)" title="Click to Get Report" onclick="myFunction()">
					<span class="btn btn-sm btn-primary">Get Report</span>
				</a>
			</div> -->
				<div class="form-group col-md-1 col-sm-2 text-left">
					<a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&voucher_no=<?php echo $voucher_no; ?>&invoice_no=<?php echo $invoice_no; ?>&company=<?php echo $company; ?>"
						title="Download Excel Report" class="btn btn-md btn-success m-t-28">
						Export To Excel
					</a>
				</div>
			</div>
		</div>

		<div class="col-md-12">
			<?php

			//$conditions = "is_receivable = 1 AND is_bank = 1";
			$conditions = "1=1 AND ";

			$opening_balance_conditions = $conditions; // base condition for opening balance

			if (empty($search_2) && empty($search_3)) {
				$conditions .= 1;
			} else {
				$conditions .= "DATE(dated) BETWEEN '{$search_2}' AND '{$search_3}'";
			}

			if (!empty($voucher_no)) {
				$conditions .= " AND voucher = '{$voucher_no}'";
			}
			if (!empty($invoice_no)) {
				$conditions .= " AND invoice_no = '{$invoice_no}'";
			}
			if (!empty($company)) {
				$conditions .= " AND company = '{$company}'";
			}

			$query = "WITH opening_balance_cte AS (
				SELECT 
					ROUND(IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0), 2) AS opening_balance
				FROM 
					account_receivable
				WHERE 
					id < (
						SELECT MIN(id) 
						FROM account_receivable 
						WHERE {$conditions}
					)
			),
			
			ordered_transactions AS (
				SELECT 
					*,
					ROW_NUMBER() OVER (ORDER BY id) AS row_num
				FROM 
					account_receivable
				WHERE 
					{$conditions}
			),
			
			running_balance_cte AS (
				SELECT 
					t.*,
					SUM(debit - credit) OVER (ORDER BY id ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS transaction_running_balance
				FROM 
					ordered_transactions t
			)
			
			SELECT 
				t.*,
				ob.opening_balance,
				ROUND(ob.opening_balance + t.transaction_running_balance, 2) AS running_balance
			FROM 
				running_balance_cte t
			CROSS JOIN 
				opening_balance_cte ob
			ORDER BY 
				t.id
			LIMIT {$startpoint}, {$limit}";

			//debug($query);
			//echo $query;

			$total_records = $acttObj->read_specific("COUNT(*) AS total", "account_receivable", $conditions)['total'];

			?>

			<div class="col-sm-12 text-right">
				<?php echo pagination($con, $table, $query, $limit, $page); ?>
			</div>

			<?php
			$result = $con->query($query);
			$res = $result->fetch_all(MYSQLI_ASSOC);
			?>
			<table class="table table-striped table-bordered table-hover table-condensed" id="dt_receivable">
				<thead>
					<tr>
						<th>Invoice No/Track#</th>
						<th>Voucher</th>
						<th>Dated</th>
						<th>Company</th>
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
							<tr>
								<td><?= $row["invoice_no"]; ?></td>
								<td><?= $row["voucher"]; ?></td>
								<td><?= $misc->dated($row['dated']); ?></td>
								<td><?= $row["company"]; ?></td>
								<td><?= $row["description"]; ?></td>
								<td><?= ($row["credit"]) ? $misc->numberFormat_fun($row["credit"]) : ''; ?></td>
								<td><?= ($row["debit"]) ? $misc->numberFormat_fun($row["debit"]) : ''; ?></td>
								<td><?= $misc->numberFormat_fun($row["running_balance"]); ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="8">
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

			var strLoc = '<?php echo basename(__FILE__); ?>' + '?search_2=' + y + '&search_3=' + z + '&voucher_no=' + v + '&invoice_no=' + i + '&company=' + c;
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