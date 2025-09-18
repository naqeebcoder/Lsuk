<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}
include 'db.php';
include 'class.php';
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$type = @$_GET['type'];	?>
<!doctype html>
<html lang="en">

<head>
	<title>Client Quarterly Booking Report</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
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

<body>
	<?php
	include "incmultiselfiles.php";
	?>
	<script type="text/javascript">
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


		function FindChildByValue(arrElem, strFind) {

			var i, nLen = arrElem.length;
			var elemChild;

			for (i = 0; i < nLen; i++) {
				elemChild = arrElem[i];
				if (elemChild.value && elemChild.value == strFind)
					return i;
			}
			return -1;
		}

		function GetChildFrom(arrElem, nFrom) {
			var i, nLen = arrElem.length;
			var elemChild;

			var strCos = "";
			for (i = nFrom; i < nLen; i++) {
				elemChild = arrElem[i];
				if (!elemChild.dataset["abrv"])
					break;

				if (strCos != "")
					strCos += ",";
				strCos += elemChild.dataset["abrv"];
			}
			return strCos;
		}

		function CompanyGrpChange(elemSel) {

			var opts = elemSel.options;
			var childs = elemSel.children;
			//var nLen=childs.length;

			var arrGrps = $(elemSel).val();
			if (arrGrps == null)
				return;

			//var arrGrps=strSel.split(",");
			var i, nCount = arrGrps.length;
			var strOrgGrp;
			var nPos;

			var strAllComp = "";
			for (i = 0; i < nCount; i++) {
				strOrgGrp = arrGrps[i];
				nPos = FindChildByValue(childs, strOrgGrp);
				if (nPos >= 0) {
					if (strAllComp != "")
						strAllComp += ",";
					strAllComp += GetChildFrom(childs, nPos + 1);
				}
			}


			//alert("CompanyGrpChange:"+strAllComp);

			var x = strAllComp;
			var y = document.getElementById("search_2").value;
			if (!y) {
				y = "<?php echo $search_2; ?>";
			}

			window.location.assign('<?php echo basename(__FILE__); ?>?search_1=' + x + '&search_2=' + y);

		}

		function myFunction_date() {
			var x = $('#search_1').val();
			var y = $('#idcompgrps').val();
			var z = $('#sup_parents').val();
			var dt = $('#search_2').val();
			var type = '';
			if (!x && y && !z) {
				x = y;
				type = 'parent';
			} else if (!x && !y && z) {
				x = z;
				type = 'super';
			} else {
				x = x;
				type = 'single';
			}
			if (Date.parse(dt)) {
				window.location.assign('<?php echo basename(__FILE__); ?>?search_1=' + x + '&type=' + type + '&search_2=' + dt);
			} else {
				alert('Kindly select Company and Date first ! Thank you');
			}
		}
	</script>
	<?php include 'nav2.php'; ?>

	<section class="container-fluid" style="overflow-x:auto">
		<div class="col-md-12">
			<header>
				<center><a href="<?php echo basename(__FILE__); ?>">
						<h2 class="col-md-4 col-md-offset-4 text-center"><span class="label label-primary">Consolidate Quarterly Report (Overall)</span></h2>
					</a></center>
				<div class="col-md-11 col-md-offset-1"><br>
					<div class="form-group col-md-2 col-sm-4">
						<select id='sup_parents' multiple="multiple" class="form-control">
							<?php include 'multiselect_super_parents.php'; ?>
						</select>
					</div>
					<div class="form-group col-md-2 col-sm-4">
						<select id='idcompgrps' multiple="multiple" class="form-control">
							<?php include 'multiselectcompgrp.php'; ?>
						</select>
					</div>
					<div class="form-group col-md-2 col-sm-4">
						<select id="search_1" name="search_1" multiple="multiple" class="form-control">
							<?php
							$sql_opt = "SELECT name,id FROM comp_reg ORDER BY name ASC";
							$result_opt = mysqli_query($con, $sql_opt);
							$options = "";
							while ($row_opt = mysqli_fetch_array($result_opt)) {
								$code = $row_opt["id"];
								$name_opt = $row_opt["name"];
								$options .= "<option value='$code'>" . $name_opt;
							}
							?>
							<?php echo $options; ?>
							</option>
						</select>
					</div>
					<div class="form-group col-md-2 col-sm-4">
						<input type="date" id="search_2" name="search_2" class="form-control" <?php if (isset($_GET['search_2']) && !empty($_GET['search_2'])) {
																									echo 'value="' . $_GET['search_2'] . '"';
																								} ?> />
					</div>
					<div class="form-group col-md-1 col-sm-4">
						<a href="javascript:void(0)" title="Click to Get Report" onclick="myFunction_date()"><span class="btn btn-sm btn-primary">Get Report</span></a>
					</div>
					<div class="form-group col-md-1 col-sm-4">
						<a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&type=<?php echo $type; ?>" title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel</span></a>
					</div>
				</div>
			</header>


			<div class="tab_container">
				<div id="tab1" class="tab_content" align="center">

					<iframe id="myFrame" class="col-xs-10 col-xs-offset-1" height="1000px" src="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&type=<?php echo $type; ?>"></iframe>

				</div><!-- end of #tab1 -->



			</div><!-- end of .tab_container -->

			</article><!-- end of content manager article --><!-- end of messages article -->

			<div class="clear"></div>

			<!-- end of post new article -->

			<div class="spacer"></div>
	</section>
</body>

</html>