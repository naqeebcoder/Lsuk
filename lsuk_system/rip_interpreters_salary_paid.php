<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php if (session_id() == '' || !isset($_SESSION)) {
	session_start();
} ?>
<?php include 'db.php';
include 'class.php';
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$is_paid = @$_GET['is_paid'];
$append_is_paid = isset($is_paid) ? "&is_paid=1" : "";
if (empty($search_2)) {
	$search_2 = date("Y-m-d");
}
if (empty($search_3)) {
	$search_3 = date("Y-m-d");
}	?>
<!doctype html>
<html lang="en">

<head>
	<title>Interpreters Paid Salaries List</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>

<body>
	<script>
		function myFunction() {
			var append_url = "<?php echo basename(__FILE__) . "?1"; ?>";
			if ($("#is_paid").is(':checked')) {
				append_url += '&is_paid=1';
			}
			var search_1 = $("#search_1").val();
			if (search_1) {
				append_url += '&search_1=' + search_1;
			}
			var search_2 = $("#search_2").val();
			if (search_2) {
				append_url += '&search_2=' + search_2;
			}
			var search_3 = $("#search_3").val();
			if (search_3) {
				append_url += '&search_3=' + search_3;
			}
			window.location.href = append_url;
		}
	</script>
	<?php include 'nav2.php'; ?>
	<!-- end of sidebar -->
	<style>
		.tablesorter thead tr {
			background: none;
		}
	</style>
	<section class="container-fluid" style="overflow-x:auto">
		<div class="col-md-12">
			<header>
				<center><a href="<?php echo basename(__FILE__); ?>">
						<h2 class="col-md-4 col-md-offset-4 text-center">
							<div class="alert bg-primary h4">INTEPRETERS PAID SALARIES REPORT</div>
						</h2>
					</a></center>
				<div class="col-md-12"><br>
					<div class="form-group col-md-2 col-sm-3 col-md-offset-1">
						<select id="search_1" onChange="myFunction()" name="search_1" class="form-control">
							<?php
							$sql_opt = "SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city FROM interpreter_reg WHERE deleted_flag=0 ORDER BY interpreter_reg.name ASC";
							$result_opt = mysqli_query($con, $sql_opt);
							$options = "";
							while ($row_opt = mysqli_fetch_array($result_opt)) {
								$code = $row_opt["id"];
								$name_opt = $row_opt["name"];
								$city_opt = $row_opt["city"];
								$gender = $row_opt["gender"];
								$options .= "<OPTION value='$code'>" . $name_opt . ' (' . $gender . ')' . ' (' . $city_opt . ')';
							}
							?>
							<?php if (!empty($search_1)) {
								$get_name = $acttObj->unique_data('interpreter_reg', 'name', 'id', $search_1);
							?>
								<option value="<?php echo $search_1; ?>"><?php echo $get_name; ?></option>
							<?php } else { ?>
								<option value="">Select Interpreter</option>
							<?php } ?>
							<?php echo $options; ?>
							</option>
						</select>
					</div>
					<div class="form-group col-md-2 col-sm-3">
						<input type="date" name="search_2" id="search_2" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $search_2; ?>" />
					</div>
					<div class="form-group col-md-2 col-sm-3">
						<input type="date" name="search_3" id="search_3" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $search_3; ?>" />
					</div>
					<div class="form-group col-md-2 col-sm-4">
						<label title="Filter salaries by Paid Status" class="btn btn-default btn-sm">
							<input <?= isset($is_paid) ? 'checked' : '' ?> type="checkbox" id="is_paid" onchange="myFunction()"> Filter Paid Salaries Only
						</label>
					</div>
					<div class="form-group col-md-1 col-sm-3">
						<a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?1&search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3 . $append_is_paid; ?>" title="Download Excel Report"><span class="btn btn-success">Export To Excel</span></a>
					</div>
				</div>
			</header>


			<div class="tab_container">
				<div id="tab1" class="tab_content" align="center">

					<iframe id="myFrame" class="col-xs-10 col-xs-offset-1" height="1000px" src="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?1&search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3 . $append_is_paid; ?>"></iframe>

				</div><!-- end of #tab1 -->



			</div><!-- end of .tab_container -->

			</article><!-- end of content manager article --><!-- end of messages article -->

			<div class="clear"></div>

			<!-- end of post new article -->

			<div class="spacer"></div>
	</section>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</html>