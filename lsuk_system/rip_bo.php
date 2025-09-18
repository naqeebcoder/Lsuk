<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}
include 'db.php';
include 'class.php';
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$search_4 = @$_GET['search_4'];
if (empty($search_2)) {
	$search_2 = date("Y-m-d");
}
if (empty($search_3)) {
	$search_3 = date("Y-m-d");
}	?>
<!doctype html>
<html lang="en">
<script>
	function myFunction() {
		var x = document.getElementById("search_1").value;
		if (!x) {
			x = "<?php echo $search_1; ?>";
		}
		var y = document.getElementById("search_2").value;
		if (!y) {
			y = "<?php echo $search_2; ?>";
		}
		var z = document.getElementById("search_3").value;
		if (!z) {
			z = "<?php echo $search_3; ?>";
		}
		var p = document.getElementById("search_4").value;
		if (!p) {
			p = "<?php echo $search_4; ?>";
		}
		window.location.href = "<?php echo basename(__FILE__); ?>" + '?search_1=' + x + '&search_2=' + y + '&search_3=' + z + '&search_4=' + p;

	}
</script>
<?php include 'header.php'; ?>

<body>
	<?php include 'horz_nav.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
	<!-- end of sidebar -->

	<section id="main" class="column"><!-- end of stats article -->
		<article class="module width_full">
			<header>
				<h3 class="tabs_involved" style="width:200px;"><a href="<?php echo basename(__FILE__); ?>"> Business Credit Report</a></h3>
				<div align="center" style=" width:80%; margin-left:250px;margin-top:10px;">
					<select id="search_1" onChange="myFunction()" name="search_1" style="width:125px;height:25px;">
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
						<?php if (!empty($search_1)) { ?>
							<option><?php echo $search_1; ?></option>
						<?php } else { ?>
							<option value="">--Select Org--</option>
						<?php } ?>
						<?php echo $options; ?>
						</option>
					</select>
					|<select id="search_4" onChange="myFunction()" name="search_4" style="width:125px;height:25px;">
						<?php
						$sql_opt = "SELECT distinct bz_credit.creditId from bz_credit
	where bz_credit.orgName='$search_1' and bz_credit.creditId<>''";
						$result_opt = mysqli_query($con, $sql_opt);
						$options = "";
						while ($row_opt = mysqli_fetch_array($result_opt)) {
							$code = $row_opt["creditId"];
							$name_opt = $row_opt["creditId"];
							$options .= "<OPTION value='$code'>" . $name_opt;
						}
						?>
						<?php if (!empty($search_4)) { ?>
							<option><?php echo $search_4; ?></option>
						<?php } else { ?>
							<option value="">--Select BO--</option>
						<?php } ?>
						<?php echo $options; ?>
						</option>
					</select>
					|
					<input type="date" name="search_2" id="search_2" placeholder='' style="border-radius: 5px;" onChange="myFunction()" value="<?php echo $search_2; ?>" /> |
					<input type="date" name="search_3" id="search_3" placeholder='' style="border-radius: 5px;" onChange="myFunction()" value="<?php echo $search_3; ?>" />
					<a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&search_4=<?php echo $search_4; ?>" title="Download Excel Report">Excel</a>



				</div>
			</header>


			<div class="tab_container">
				<div id="tab1" class="tab_content" align="center">

					<iframe height="1000px" width="950px" src="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&search_4=<?php echo $search_4; ?>"></iframe>

				</div><!-- end of #tab1 -->



			</div><!-- end of .tab_container -->

		</article><!-- end of content manager article --><!-- end of messages article -->

		<div class="clear"></div>

		<!-- end of post new article -->

		<div class="spacer"></div>
	</section>


</body>

</html>