<?php if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}
include 'secure.php';
include 'source/db.php';
include_once('source/function.php');
include 'source/class.php';
$interp = $_SESSION['web_userId'];
$get_dated = @$_GET["get_dated"]; ?>
<?php include 'source/header.php'; ?>

<body class="boxed">
	<!-- begin container -->
	<div id="wrap">
		<!-- begin header -->
		<?php include 'source/top_nav.php'; ?>
		<!-- end header -->
		<style>
			.container {
				width: auto;
			}
		</style>
		<?php $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
		$limit = 50;
		$startpoint = ($page * $limit) - $limit;	?>
		<!doctype html>
		<html lang="en">

		<head>
			<title>Interpreters Paid Salaries Record</title>
			<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
			<script>
				function myFunction() {
					var y = document.getElementById("get_dated").value;
					if (!y) {
						y = "<?php echo $get_dated; ?>";
					}
					window.location.href = "salary_list.php" + '?get_dated=' + y;
				}
			</script>
		</head>

		<body>
			<!-- end of sidebar -->
			<style>
				.tablesorter thead tr {
					background: none;
				}
			</style>
			<section class="container-fluid" style="overflow-x:auto">
				<div class="col-md-12">
					<header>
						<center><br>
							<div class="alert alert-info col-sm-3">
								<a href="<?php echo basename(__FILE__); ?>" class="h4">My salaries records</a>
							</div>
						</center>

						<div class="form-group col-md-2 col-sm-4 pull-right" style="margin-top: 15px;">
							<?php if (isset($interp) && !empty($interp)) { ?>
								<select id="get_dated" onChange="myFunction()" class="form-control">
									<?php
									$one_year_ago = date('Y-m-d', strtotime('-1 year'));
									$sql_opt = $acttObj->read_all("dated", "interp_salary", "deleted_flag=0 and interp=" . $interp . " and DATE(dated) > '" . $one_year_ago . "'");
									$options = "";
									while ($row_opt = mysqli_fetch_array($sql_opt)) {
										$row_dated = $row_opt["dated"];
										$options .= "<OPTION value='$row_dated'>" . $row_dated;
									}
									?>
									<?php if (!empty($get_dated)) { ?>
										<option><?php echo $get_dated; ?></option>
									<?php } else { ?>
										<option value=""> Select Paid Date </option>
									<?php } ?>
									<?php echo $options; ?>
									</option>
								</select>
							<?php } else { ?>
								<input placeholder="Salry Paid Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="get_dated" id="get_dated" class="form-control" onChange="myFunction()" value="<?php echo $get_dated; ?>" />
							<?php } ?>
						</div>
						<span class="col-sm-4 pull-right"><?php if (isset($msg) && !empty($msg)) {
																echo $msg;
															} ?></span>
					</header>
					<table class="tablesorter table table-bordered" cellspacing="0" width="100%">
						<thead class="bg-primary">
							<tr>
								<th>Invoice</th>
								<th>From</th>
								<th>To</th>
								<th>Deductions</th>
								<th>Additions</th>
								<th>Salary</th>
								<th>Paid Date</th>
								<th width="230" align="center">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php $table = 'interp_salary';
							$append_date = "";
							$append_int = "";
							if (isset($get_dated) && !empty($get_dated)) {
								$append_date = "and dated='$get_dated'";
							}
							if (isset($interp) && !empty($interp)) {
								$append_int = 'and interp=' . $interp;
							}
							$query = "SELECT $table.* FROM $table where deleted_flag=0 and dated > '" . $one_year_ago . "' $append_int $append_date LIMIT {$startpoint} , {$limit}";
							$result = mysqli_query($con, $query);
							while ($row = mysqli_fetch_array($result)) { ?>

								<tr>
									<td><?php echo $row['invoice']; ?></td>
									<td><?php echo $misc->dated($row['frm']); ?></td>
									<td><?php echo $misc->dated($row['todate']); ?></td>
									<td><?php echo $misc->numberFormat_fun($row['ni_dedu'] + $row['tax_dedu'] + $row['payback_deduction']); ?></td>
									<td><?php echo $misc->numberFormat_fun($row['given_amount']); ?></td>
									<td><?php echo $misc->numberFormat_fun(($row['salry'] - $row['ni_dedu'] - $row['tax_dedu'] - $row['payback_deduction']) + $row['given_amount']); ?></td>
									<td><?php echo $misc->dated($row['dated']); ?></td>
									<td align="center"><a href="javascript:void(0)" onClick="popupwindow('lsuk_system/pay_slip_record.php?invoice_number=<?php echo $row['invoice']; ?>&interpreter_id=<?php echo $row['interp']; ?>&invoice_from=<?php echo $row['frm']; ?>&invoice_to=<?php echo $row['todate']; ?>', 'title', 1000, 550);"><button class="btn"><i class="glyphicon glyphicon-eye-open text-info" title="View Paid Salary Slip"></i></button></a>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<div><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
				</div>
			</section>
			<script src="js/jquery-1.11.3.min.js"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
		</body>

		</html>