<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}
include 'db.php';
include 'class.php';
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$year = 2014;	?>

<!doctype html>
<html lang="en">
<!--............................................For Multi-Selection.......................................................................--> <?php include 'header.php'; ?>

<?php
include "incmultiselfiles.php";
?>

<script type="text/javascript">
	$(function() {
		$('#search_1').multiselect({
			includeSelectAllOption: true,
			numberDisplayed: 1,
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true
		});
		$('#idcompgrps').multiselect({
			includeSelectAllOption: true,
			numberDisplayed: 1,
			enableFiltering: true,
			enableCaseInsensitiveFiltering: true
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



		window.location.href = "<?php echo basename(__FILE__); ?>" + '?search_1=' + x + '&search_2=' + y;
		window.location.assign('<?php echo basename(__FILE__); ?>?search_1=' + x + '&search_2=' + y);

	}


	function myFunction() {
		var x = $('#search_1').val();
		if (!x) {
			x = "<?php echo $search_1; ?>";
		}
		var y = document.getElementById("search_2").value;
		if (!y) {
			y = "<?php echo $search_2; ?>";
		}

		window.location.href = "<?php echo basename(__FILE__); ?>" + '?search_1=' + x + '&search_2=' + y;

		window.location.assign('<?php echo basename(__FILE__); ?>?search_1=' + x + '&search_2=' + y);
	}
	window.addEventListener('click', function(e) {
		if ($('#search_1').val() != null) {
			if (document.getElementById('search_1').contains(e.target)) {
				console.log('inside');
			} else {
				myFunction();
			}
		}
	});
</script>
<!--................................//\\//\\//\\//\\//\\........................................................................................-->

<body>
	<?php include 'horz_nav.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
	<!-- end of sidebar -->
	<style>
		.open>.dropdown-menu {
			max-height: 550px !important;
			max-width: 410px;
			overflow-y: auto !important;
		}

		.multiselect-container>li {
			text-align: left;
		}
	</style>
	<section id="main" class="column"><!-- end of stats article -->
		<article class="module width_full">
			<header>
				<h3 class="tabs_involved" style="width:300px;"><a href="<?php echo basename(__FILE__); ?>">Consolidate Month Wise Report (F2F)</a></h3>
				<div align="center" style=" width:60%; margin-left:400px;margin-top:10px;">

					<span>
						<select onchange='CompanyGrpChange(this);' id='idcompgrps' multiple="multiple">
							<?php include 'multiselectcompgrp.php'; ?>
						</select>

					</span>


					<select id="search_1" name="search_1" multiple="multiple">
						<?php
						$sql_opt = "SELECT name,abrv FROM comp_reg ORDER BY name ASC";
						$result_opt = mysqli_query($con, $sql_opt);
						$options = "";
						while ($row_opt = mysqli_fetch_array($result_opt)) {
							$code = $row_opt["abrv"];
							$name_opt = $row_opt["name"];
							$options .= "<OPTION value='$code'>" . $name_opt;
						}
						?>
						<?php echo $options; ?>
						</option>
					</select>
					|
					<select id="search_2" name="search_2" onChange="myFunction()">
						<option><?php if ($search_2) {
									echo $search_2;
								} else {
									echo '..Select Year..';
								} ?></option>
						<?php while ($year <= 2020) { ?>
							<option><?php echo $year; ?></option>

						<?php $year++;
						} ?>
					</select>
					<a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>" title="Download Excel Report">Excel</a>




				</div>
			</header>


			<div class="tab_container">
				<div id="tab1" class="tab_content" align="center">

					<iframe height="1000px" width="950px" src="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>"></iframe>

				</div><!-- end of #tab1 -->



			</div><!-- end of .tab_container -->

		</article><!-- end of content manager article --><!-- end of messages article -->

		<div class="clear"></div>

		<!-- end of post new article -->

		<div class="spacer"></div>
	</section>


</body>

</html>