<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php if (session_id() == '' || !isset($_SESSION)) {
	session_start();
} ?>
<?php include 'db.php';
include 'class.php';
$orgs = array();
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$proceed = @$_GET['proceed'];
$p_org = @$_GET['p_org'];
if (empty($search_2)) {
	$search_2 = date("Y-m-d");
}
if (empty($search_3)) {
	$search_3 = date("Y-m-d");
}
if (isset($search_1) && $search_1 != "") {
	$orgs = explode(",", $search_1);
}
?>
<!doctype html>
<html lang="en">

<head>
	<title>Collective Invoice Created</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="css/util.css" />
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<link rel="icon" type="image/png" href="img/logo.png">
	<style>
		.multiselect {
			min-width: 190px;
		}

		.multiselect-container {
			max-height: 400px;
			overflow-y: auto;
			max-width: 380px;
		}

		.form-group {
			margin-left: 16px;
		}
	</style>
</head>
<?php include "incmultiselfiles.php"; ?>
<script>
	$(function() {
		$('#search_1').multiselect({
			includeSelectAllOption: true,
			numberDisplayed: 1,
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true,
			nonSelectedText: 'Select Company'
		});
		$('#idcompgrps').multiselect({
			includeSelectAllOption: true,
			numberDisplayed: 1,
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true,
			nonSelectedText: 'Select Child Company'
		});
		$('#sup_parents').multiselect({
			includeSelectAllOption: true,
			numberDisplayed: 1,
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true,
			nonSelectedText: 'Select Super Parent'
		});
	});

	function myFunction() {
		var x = $("#search_1").val();
		if (!x) {
			x = "<?php echo $search_1; ?>";
		}
		var y = $("#search_2").val();
		if (!y) {
			y = "<?php echo $search_2; ?>";
		}
		var z = $("#search_3").val();
		if (!z) {
			z = "<?php echo $search_3; ?>";
		}
		var p = $("#proceed").val();
		if (p == 'Yes') {

			if (!$('#p_org').val() && !$('#search_1').val()) {
				alert("Please select Parent/Head Units OR Companie(s) to proceed.");
				$("#proceed").val('Proceed');
				return false;
			}

			var result = confirm("Are you sure to create collective invoice?");

			if (result == true) {
				p = p;
			} else {
				return false;
			}
		} else {
			p = p;
		}

		var p_org = $("#p_org").val();
		if (!p_org) {
			p_org = "<?php echo $p_org; ?>";
		}
		//  console.log(x);

		window.location.href = "<?php echo basename(__FILE__); ?>" + '?p_org=' + p_org + '&search_1=' + x + '&search_2=' + y + '&search_3=' + z + '&proceed=' + p;

	}
</script>
<?php include 'nav2.php'; ?>

<section class="container-fluid" style="overflow-x:auto">
	<div class="col-md-12 m-b-30 text-center">
		<a href="<?php echo basename(__FILE__); ?>" style="display: inline-block; text-decoration: none; ">
			<h2 class="text-center"><span class="label label-primary">Collective Invoice Created</span></h2>
		</a>
	</div>

	<div class="col-md-12">
		<div class="form-group col-md-2 col-sm-2">
			<select name="proceed" id="proceed" onChange="myFunction()" class="form-control">
				<option value="Proceed" <?php echo ($proceed == 'Proceed') ? 'selected' : ''; ?>>Proceed</option>
				<option value="Yes" <?php echo ($proceed == 'Yes') ? 'selected' : ''; ?>>Yes</option>
				<option value="No" <?php echo ($proceed == 'No') ? 'selected' : ''; ?>>No</option>
			</select>
		</div>
		<div class="form-group col-md-3 col-sm-3 p_org_div">
			<?php
			$result_opt = $acttObj->read_all("DISTINCT id,name,abrv", "comp_reg", " comp_nature=1 AND deleted_flag=0 ORDER BY name ASC "); ?>
			<select id="p_org" name="p_org" class="form-control searchable">
				<?php
				$options = "";
				while ($row_opt = mysqli_fetch_array($result_opt)) {
					$comp_id = $row_opt["id"];
					$code = $row_opt["abrv"];
					$name_opt = $row_opt["name"];
					$options .= "<OPTION value='$comp_id' " . ($comp_id == $p_org ? 'selected' : '') . ">" . $name_opt . ' (' . $code . ')';
				}
				?>
				<option value="">Select Parent/Head Units</option>
				<?php echo $options; ?>
				</option>
			</select>
		</div>
		<div class="form-group col-md-2 col-sm-2 search_1_div">
			<select id="search_1" name="search_1" multiple class="form-control">
				<?php
				$result_opt = $acttObj->read_all("id,name,abrv", "comp_reg", " 1 ORDER BY name ASC ");
				$options = "";
				while ($row_opt = mysqli_fetch_array($result_opt)) {
					$code = $row_opt["id"];
					$name_opt = $row_opt["name"];
					$options .= "<option value='$code' " . (in_array($code, $orgs) ? 'selected' : '') . ">" . $name_opt . "</option>";
				}
				?>
				<?php echo $options; ?>
			</select>
		</div>
		<div class="form-group col-md-2 col-sm-2">
			<input type="date" name="search_2" id="search_2" placeholder='' class="form-control" value="<?php echo $search_2; ?>" />
		</div>
		<div class="form-group col-md-2 col-sm-2">
			<input type="date" name="search_3" id="search_3" placeholder='' class="form-control" value="<?php echo $search_3; ?>" />
		</div>
	</div>
	<div class="col-md-12 text-right">
		<div class="form-group col-md-2 col-sm-2 pull-right m-r-75">
			<a href="javascript:void(0)" title="Click to Get Result" onclick="myFunction()"><span class="btn btn-sm btn-primary">Get Result</span></a>

			<a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?p_org=<?php echo $p_org; ?>&search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>" title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel</span></a>
		</div>
	</div>


	<div class="tab_container">
		<div id="tab1" class="tab_content" align="center">
			<iframe id="myFrame" class="col-xs-12" height="1000px" style="padding: 0px;"
				src="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?p_org=<?php echo $p_org; ?>&search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&proceed=<?php echo $proceed; ?>"></iframe>
		</div><!-- end of #tab1 -->
	</div><!-- end of .tab_container -->

	</article><!-- end of content manager article --><!-- end of messages article -->

	<div class="clear"></div>

	<!-- end of post new article -->

	<div class="spacer"></div>
</section>


</body>

</html>

<script>
	$(document).on('change', '#p_org', function() {
		if ($(this).val()) {
			$('.search_1_div').hide();
		} else {
			$('.search_1_div').show();
		}
	});
	$(document).on('change', '#search_1', function() {
		if ($(this).val() == null) {
			$('.p_org_div').show();
		} else {
			$('.p_org_div').hide();
		}

	});
</script>