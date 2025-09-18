<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include 'db.php';
include_once('function.php');
include 'class.php';
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=175 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_supplier = $_SESSION['is_root'] == 1 || in_array(102, $get_actions);
$action_edit_supplier = $_SESSION['is_root'] == 1 || in_array(103, $get_actions);
$action_delete_supplier = $_SESSION['is_root'] == 1 || in_array(104, $get_actions);
$action_restore_supplier = $_SESSION['is_root'] == 1 || in_array(105, $get_actions);
$action_supplier_history = $_SESSION['is_root'] == 1 || in_array(106, $get_actions);

if ($action_delete_supplier && isset($_GET['delSup'])) {
	$delSup = $_GET['delSup'];
	$pg = '';
	$acttObj->delete('sup_reg', " id=$delSup ");
	if (isset($_GET['page'])) {
		$pg = $_GET['page'];
		$page = 'reg_sup_list.php?page=' . $pg;
		header("Location:$page");
	} else {
		header("Location:reg_sup_list.php");
	}
}
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
$title = @$_GET['title'];
$array_tp = array('a' => 'Active', 'tr' => 'Trashed');
$page_title = $array_tp[$tp] == 'Active' ? '' : $array_tp[$tp];
$deleted_flag = 'deleted_flag = 0';
$class = $tp == 'tr' ? 'alert-danger' : 'alert-info';
?>
<html lang="en">
<head>
	<title><?php echo $page_title; ?> Registered Suppliers List</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<style>
		.multiselect {
			min-width: 300px;
		}
		.multiselect-container {
			max-height: 400px;
			overflow-y: auto;
			max-width: 380px;
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
			margin-top: -44px;
			background: #ffffff;
			border: 1px solid lightgrey;
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
	</style>
</head>
<script>
	function myFunction() {
		var title = document.getElementById("title").value;
		if (!title) {
			title = "<?php echo $title; ?>";
		}
		window.location.href = "reg_sup_list.php" + '?title=' + encodeURIComponent(title);

	}
</script>
<?php include 'header.php'; ?>

<body>
	<?php include 'nav2.php'; ?>
	<!-- end of sidebar -->
	<style>
		.tablesorter thead tr {
			background: none;
		}
	</style>
	<section class="container-fluid">
		<div class="col-md-12">
			<header>
				<div class="alert <?php echo $class; ?> col-md-3">
					<a href="<?php echo basename(__FILE__); ?>" class="alert-link"><?php echo $page_title; ?> Suppliers List</a>
				</div>
				<div class="form-group col-md-3 col-sm-4">
					<select id="title" name="title" onChange="myFunction()" class="form-control searchable">
						<?php
						$sql_opt = "SELECT DISTINCT comp as title FROM expence WHERE deleted_flag=0 ORDER BY comp ASC";
						$result_opt = mysqli_query($con, $sql_opt);
						$options = "";
						while ($row_opt = mysqli_fetch_array($result_opt)) {
							$code = $row_opt["title"];
							$name_opt = $row_opt["title"];
							// $title=urldecode($title);
							$options .= '<OPTION value="' . $code . '" ' . ((!empty($title) && $title == $code) ? 'selected' : '') . '>' . ((!empty($name_opt)) ? $name_opt : 'Empty');
						}
						?>
						<option value="">Select Supplier</option>
						<?php echo $options; ?>
						</option>
					</select>
				</div>
			</header>
			<div>
				<div>
					<table class="table table-bordered table-hover" cellspacing="0" width="100%">
						<thead class="bg-primary">
							<tr>
								<th>Supplier Name</th>
								<th>Contact Person</th>
								<th>Supplier Type</th>
								<th>Phone#</th>
								<th>Email</th>
								<th>City</th>
								<th>Address</th>
								<th>Submitted By</th>
								<?php if ($tp == 'tr') { ?><th>Deleted by</th> <?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php $table = 'sup_reg';
							$tl = mysqli_real_escape_string($con, trim(strtolower($title)));
							$query = "SELECT distinct *  FROM $table
								where $table.$deleted_flag " . (!empty($title) ? " and TRIM(LOWER($table.sp_name))= '$tl'" : "") . "
									LIMIT {$startpoint} , {$limit}";
							// echo $query;die();exit();
							$result = mysqli_query($con, $query);
							while ($row = mysqli_fetch_array($result)) { ?>
								<tr <?php if ($row['is_temp'] == 1) { ?>title="This Supplier is registered by Temporary Role. Kindly confirm to process." style="background-color:#cbda78;" <?php } ?> class="tr_data" title="Click on row to see actions">
									<td><?php echo $row['sp_name']; ?></td>
									<td><?php echo $row['sp_cpName']; ?></td>
									<td><?php echo $row['sp_type']; ?></td>
									<td><?php echo $row['sp_contact']; ?></td>
									<td><?php echo $row['sp_email']; ?></td>
									<td><?php echo $row['sp_city']; ?></td>
									<td><?php echo $row['sp_buildingName'] . $row['sp_streetRoad'] . $row['sp_line1']; ?></td>
									<td><?php echo $row['sbmtd_by']; ?></td>
									<?php if ($tp == 'tr') { ?><td style="color:#F00"><?php echo $row['deleted_by'] . '(' . $misc->dated($row['deleted_date']) . ')'; ?></td><?php } ?>
									<!-- <td><?php echo $row['status']; ?></td> -->
								</tr>
								<tr class="div_actions" style="display:none">
									<td colspan="9">
										<?php if ($tp != 'tr') {
											if ($action_view_supplier) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="View Supplier" onClick="popupwindow('sup_reg_view.php?edit_id=<?php echo $row['id']; ?>', 'View Supplier', 1100,820);"><i class="fa fa-eye"></i></a>
											<?php }
											if ($action_edit_supplier) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edit Supplier" onClick="popupwindow('sup_reg_edit.php?edit_id=<?php echo $row['id']; ?>','Edit Supplier record',1100, 820)"><i class="fa fa-pencil-square-o"></i></a>
											<?php }
											if ($action_delete_supplier) { ?>
												<a data-placement="top" href="javascript:void(0)" id="delSup_<?php echo $row['id']; ?>" class="delSup w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Trash Supplier" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-trash-o"></i></a>
											<?php }
										} ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<div><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
				</div>

				<!-- Modal -->
				<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLabel">Delete Supplier</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<h4>Are you sure you want to <span style='color:red;'>DELETE</span> this supplier?</h4>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
								<button type="button" class="btn btn-primary" id="delConfirm">Yes</button>
							</div>
						</div>
					</div>
				</div>
	</section>

	<script src="js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
	<script>
		$(function() {
			$('.searchable').multiselect({
				includeSelectAllOption: true,
				numberDisplayed: 1,
				enableFiltering: true,
				enableCaseInsensitiveFiltering: true
			});
		});
		$(document).ready(function() {
			var delSup = '';
			$(document).on('click', '.delSup', function() {
				delSup = (this.id).split('_')[1];
			});
			$(document).on('click', '#delConfirm', function() {
				var pg = window.location.search.substring(1);
				if (pg && pg != '') {
					window.location.href = "reg_sup_list.php" + '?delSup=' + delSup + pg;
				} else {
					window.location.href = "reg_sup_list.php" + '?delSup=' + delSup;
				}

			});
		});
		$(function() {
			$('.multi_class').multiselect({
				includeSelectAllOption: true,
				numberDisplayed: 1,
				enableFiltering: true,
				enableCaseInsensitiveFiltering: true
			});
		});
		$('.tr_data').click(function(event) {
			$('.div_actions').css('display', 'none');
			$(this).next().css('display', 'block');
		});
	</script>
</body>
</html>