<?php
// if (session_id() == '' || !isset($_SESSION)) {
// 	session_start();
// }
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include 'db.php';
include_once('function.php');
include 'class.php';
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=30 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_profile = $_SESSION['is_root'] == 1 || in_array(58, $get_actions);
$action_edit_profile = $_SESSION['is_root'] == 1 || in_array(59, $get_actions);
$action_duplicate_profile = $_SESSION['is_root'] == 1 || in_array(225, $get_actions);
$action_update_company_status = $_SESSION['is_root'] == 1 || in_array(60, $get_actions);
$action_update_rates = $_SESSION['is_root'] == 1 || in_array(61, $get_actions);
$action_update_rates_new = $_SESSION['is_root'] == 1 || in_array(62, $get_actions);
$action_delete_company = $_SESSION['is_root'] == 1 || in_array(63, $get_actions);
$action_restore_company = $_SESSION['is_root'] == 1 || in_array(64, $get_actions);
$action_update_subsidiary_companies = $_SESSION['is_root'] == 1 || in_array(65, $get_actions);
$action_edited_history = $_SESSION['is_root'] == 1 || in_array(66, $get_actions);
$action_confirm_temporary_company = $_SESSION['is_root'] == 1 || in_array(67, $get_actions);
$action_dropdown_filter = $_SESSION['is_root'] == 1 || in_array(68, $get_actions);
$action_view_creds = $_SESSION['is_root'] == 1 || in_array(217, $get_actions);
$comptype = @$_GET['comptype'];
$org = @$_GET['org'];
$city = @$_GET['city'];
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
$tp = @$_GET['tp'];
$array_tp = array('a' => 'Active', 'tr' => 'Trashed');
$page_title = $array_tp[$tp] == 'Active' ? '' : $array_tp[$tp];
$deleted_flag = $tp == 'tr' ? 'deleted_flag = 1' : 'deleted_flag = 0';
$class = $tp == 'tr' ? 'alert-danger' : 'alert-info'; ?>
<html lang="en">

<head>
	<title><?php echo $page_title; ?> Registered Companies List</title>
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

		ul.pagination {
			height: auto !important;
			padding: 0 0 20px 0 !important;
		}
	</style>
</head>
<script>
	function myFunction() {
		var x = document.getElementById("comptype").value;
		if (!x) x = "<?php echo $comptype; ?>";

		var y = document.getElementById("org").value;
		if (!y) y = "<?php echo $org; ?>";

		var z = document.getElementById("city").value;
		if (!z) z = "<?php echo $city; ?>";

		var tp = document.getElementById("tp").value;
		if (!tp) tp = "<?php echo $tp; ?>";

		var nature = document.getElementById("comp_nature").value;
		if (!nature) nature = "<?php echo $comp_nature; ?>";

		window.location.href = "reg_comp_list.php"
			+ '?comptype=' + encodeURIComponent(x)
			+ '&org=' + encodeURIComponent(y)
			+ '&city=' + encodeURIComponent(z)
			+ '&tp=' + encodeURIComponent(tp)
			+ '&comp_nature=' + encodeURIComponent(nature);
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
	<section class="container-fluid" style="overflow-x:auto">
		<div class="col-md-12" style="min-height: 100%;">
			<header>
				<div class="alert <?php echo $class; ?> col-md-3">
					<a href="<?php echo basename(__FILE__); ?>" class="alert-link"><?php echo $page_title; ?> Companies List</a>
				</div>
				<div class="col-md-9">
					<?php
					$comp_nature = isset($_GET['comp_nature']) ? $_GET['comp_nature'] : '';
					?>

					<div class="form-group col-md-3 col-sm-4">
						<select id="comp_nature" name="comp_nature" onChange="myFunction()" class="form-control">
							<option value="">Select Company Nature</option>
							<option value="1" <?= $comp_nature === '1' ? 'selected' : '' ?>>Parent</option>
							<option value="3" <?= $comp_nature === '3' ? 'selected' : '' ?>>Child</option>
							<option value="4" <?= $comp_nature === '4' ? 'selected' : '' ?>>Individual</option>
						</select>
					</div>

					<div class="form-group col-md-3 col-sm-4">
						<select id="org" name="org" onChange="myFunction()" class="form-control multi_class">
							<?php
							$sql_opt = "SELECT name,abrv FROM comp_reg where $deleted_flag ORDER BY name ASC";
							$result_opt = mysqli_query($con, $sql_opt);
							$options = "";
							while ($row_opt = mysqli_fetch_array($result_opt)) {
								$code = $row_opt["abrv"];
								$name_opt = $row_opt["name"];
								$options .= "<OPTION value='$code'>" . $name_opt;
							}
							?>
							<?php if (!empty($org)) { ?>
								<option><?php echo $org; ?></option>
							<?php } else { ?>
								<option value="">Select Organization</option>
							<?php } ?>
							<?php echo $options; ?>
							</option>
						</select>
					</div>
					<div class="form-group col-md-3 col-sm-4">
						<select id="comptype" name="comptype" onChange="myFunction()" class="form-control">
							<?php
							$sql_opt = "SELECT title FROM comp_type ORDER BY title ASC";
							$result_opt = mysqli_query($con, $sql_opt);
							$options = "";
							while ($row_opt = mysqli_fetch_array($result_opt)) {
								$code = $row_opt["title"];
								$name_opt = $row_opt["title"];
								$options .= "<OPTION value='$code'>" . $name_opt;
							}
							?>
							<?php if (!empty($comptype)) { ?>
								<option><?php echo $comptype; ?></option>
							<?php } else { ?>
								<option value="">Select Company Type</option>
							<?php } ?>
							<?php echo $options; ?>
							</option>
						</select>
					</div>
					<div class="form-group col-md-3 col-sm-4">
						<select name="city" id="city" onChange="myFunction()" class="form-control">
							<?php
							$sql_opt = "SELECT city FROM cities ORDER BY city ASC";
							$result_opt = mysqli_query($con, $sql_opt);
							$options = "";
							while ($row_opt = mysqli_fetch_array($result_opt)) {
								$code = $row_opt["city"];
								$name_opt = $row_opt["city"];
								$options .= "<OPTION value='$code'>" . $name_opt;
							}
							?>
							<?php if (!empty($city)) { ?>
								<option><?php echo $city; ?></option>
							<?php } else { ?>
								<option value="">Select City</option>
							<?php } ?>
							<?php echo $options; ?>
							</option>
						</select>
					</div>
					<div class="form-group col-md-3 col-sm-4">
						<?php if ($action_dropdown_filter) { ?>
							<select id="tp" onChange="myFunction()" name="tp" class="form-control">
								<?php
								if (!empty($tp)) { ?>
									<option value="<?php echo key($array_tp[$tp]); ?>" selected><?php echo $array_tp[$tp]; ?></option>
								<?php } ?>
								<option value="" disabled <?= empty($tp) ? 'selected' : '' ?>>Filter by Type</option>
								<option value="a">Active</option>
								<option value="tr">Trashed</option>
							</select>
						<?php } else { ?>
							<input type="hidden" value='' id="tp" onChange="myFunction()" name="tp" class="form-control" />
						<?php } ?>
					</div>
				</div>
			</header>


			<div>
				<div>
					<table class="table table-bordered table-hover" cellspacing="0" width="100%">
						<thead class="bg-primary">
							<tr>
								<th>Comp Name</th>
								<th>Contact Person</th>
								<?php echo ($action_view_creds ? "<th>Company Login Credentials</th>" : ""); ?>
								<th>Phone#</th>
								<th>Email</th>
								<th>City</th>
								<th>Address</th>
								<th>Submitted By</th>
								<?php if ($tp == 'tr') { ?><th>Deleted by</th> <?php } ?>
								<th>Status</th>
								<th>Last Booking Date</th>
							</tr>
						</thead>
						<tbody>
							<?php $table = 'comp_reg';
							// $query = "SELECT distinct *  FROM $table where $table.$deleted_flag and  compType like '$comptype%' and abrv like '$org%' and  city like '$city%' 
							// GROUP BY name LIMIT {$startpoint} , {$limit}";
							$comp_nature = isset($_GET['comp_nature']) ? trim($_GET['comp_nature']) : '';

							$query = "SELECT 
										cr.*,
										i.orgName AS order_id,
										t.orgName AS order_id,
										tr.orgName AS order_id,
										GREATEST(
											IFNULL(i.assignDate, '0000-00-00'),
											IFNULL(t.assignDate, '0000-00-00'),
											IFNULL(tr.asignDate, '0000-00-00')
										) AS last_booking_date
									FROM 
										comp_reg cr

									-- Latest interpreter per company
									LEFT JOIN (
										SELECT i1.*
										FROM interpreter i1
										INNER JOIN (
											SELECT order_company_id, MAX(assignDate) AS latest_assign
											FROM interpreter
											WHERE order_cancel_flag = 0 AND is_temp = 0 AND deleted_flag = 0
											GROUP BY order_company_id
										) i2 ON i1.order_company_id = i2.order_company_id AND i1.assignDate = i2.latest_assign
										WHERE i1.order_cancel_flag = 0 AND i1.is_temp = 0 AND i1.deleted_flag = 0
									) i ON i.order_company_id = cr.id

									-- Latest telephone per company
									LEFT JOIN (
										SELECT t1.*
										FROM telephone t1
										INNER JOIN (
											SELECT order_company_id, MAX(assignDate) AS latest_assign
											FROM telephone
											WHERE order_cancel_flag = 0 AND is_temp = 0 AND deleted_flag = 0
											GROUP BY order_company_id
										) t2 ON t1.order_company_id = t2.order_company_id AND t1.assignDate = t2.latest_assign
										WHERE t1.order_cancel_flag = 0 AND t1.is_temp = 0 AND t1.deleted_flag = 0
									) t ON t.order_company_id = cr.id

									-- Latest translation per company
									LEFT JOIN (
										SELECT tr1.*
										FROM translation tr1
										INNER JOIN (
											SELECT order_company_id, MAX(asignDate) AS latest_assign
											FROM translation
											WHERE order_cancel_flag = 0 AND is_temp = 0 AND deleted_flag = 0
											GROUP BY order_company_id
										) tr2 ON tr1.order_company_id = tr2.order_company_id AND tr1.asignDate = tr2.latest_assign
										WHERE tr1.order_cancel_flag = 0 AND tr1.is_temp = 0 AND tr1.deleted_flag = 0
									) tr ON tr.order_company_id = cr.id

									WHERE 
										cr.deleted_flag = 0
										AND cr.compType LIKE '%$comptype'
										AND cr.abrv LIKE '%$org'
										AND cr.city LIKE '%$city'";

							if ($comp_nature !== '') {
								$query .= " AND cr.comp_nature = '$comp_nature'";
							}

							$query .= " GROUP BY cr.name LIMIT {$startpoint}, {$limit}";

							$result = mysqli_query($con, $query);

							while ($row = mysqli_fetch_array($result)) { ?>
								<tr <?php if ($row['is_temp'] == 1) { ?>title="This Company is registered by Temporary Role. Kindly confirm to process." style="background-color:#cbda78;" <?php } ?> class="tr_data" title="Click on row to see actions">
									<td><?php echo $row['name']; ?></td>
									<td><?php echo $row['contactPerson']; ?></td>
									<?php if ($action_view_creds) {
										$get_cr =  $acttObj->read_specific("email,paswrd as password", "company_login", "company_id=" . $row['id']);
										echo "<td>" . $get_cr['email'] . "<br>" . $get_cr['password'] . "</td>";
									} ?>
									<td><?php echo $row['contactNo1']; ?></td>
									<td><?php echo $row['email']; ?></td>
									<td><?php echo $row['city']; ?></td>
									<td><?php echo $row['buildingName'] . $row['line1'] . $row['streetRoad']; ?></td>
									<td><?php echo $row['sbmtd_by']; ?></td>
									<?php if ($tp == 'tr') { ?><td style="color:#F00"><?php echo $row['deleted_by'] . '(' . $misc->dated($row['deleted_date']) . ')'; ?></td><?php } ?>
									<td><?php echo $row['status'] . ($row['deleted_reason'] ? ' <i class="fa fa-exclamation-circle" title="' . $row['deleted_reason'] . '"></i>' : ''); ?></td>
									<td><?php echo ($row['last_booking_date'] && $row['last_booking_date'] != '0000-00-00') ? $misc->dated($row['last_booking_date']) : ''; ?></td>
								</tr>
								<tr class="div_actions" style="display:none">
									<td colspan="9">
										<?php if ($tp != 'tr') { ?>
											<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="View Company" onClick="popupwindow('comp_reg_view.php?edit_id=<?php echo $row['id']; ?>', 'View company', 1100,820);">
												<i class="fa fa-eye"></i></a>
											<?php if ($row['is_temp'] == 1 && $action_confirm_temporary_company) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-yellow w3-border w3-border-blue" title="Confirm This Company First" onClick="popupwindow('confirm_record.php?id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'Confirm account company', 520,350);"><i class="fa fa-check-circle"></i></a>
											<?php }
											if ($action_edit_profile) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edit Company" onClick="popupwindow('comp_reg_edit.php?edit_id=<?php echo $row['id']; ?>','Edit company record',1100, 820)"><i class="fa fa-pencil-square-o"></i></a>
											<?php }
											if ($action_duplicate_profile) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Create Duplicate" onClick="popupwindow('comp_reg_edit.php?action=duplicate&edit_id=<?php echo $row['id']; ?>','create duplicate company record',1100, 820)"><i class="fa fa-clone"></i></a>
											<?php }
											if ($action_delete_company) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Trash Company" onClick="popupwindow('del_trash.php?hist=1&del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','Delete company record',520,350)"><i class="fa fa-trash-o"></i></a>
												<?php }
											if ($row['is_temp'] == 0) {
												if ($action_update_company_status) { ?>
													<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Update Company Status" onClick="popupwindow('comp_update_status.php?edit_id=<?php echo $row['id']; ?>','Update company status',520,350)"><i class="fa fa-bookmark"></i></a>
												<?php }
												if ($action_update_rates) { ?>
													<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Update Booking Type" onClick="popupwindow('comp_update_booking_type.php?orgName=<?php echo $row['abrv']; ?>&bookingType=<?php echo $row['bookingType']; ?>','Update booking type company',800,800)"><i class="fa fa-refresh"></i></a>
												<?php }
												if ($action_update_rates_new) { ?>
													<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-green w3-border w3-border-blue" title="Update Company Booking Rates" onClick="popupwindow('manage_company_rates.php?company_id=<?= $row['id'] ?>', 'Manage Company Booking Rates', 1100, 1000);"><i class="fa fa-refresh"></i></a>
												<?php }
												if ($action_update_subsidiary_companies) { ?>
													<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Update Subsidary Companies" onClick="popupwindow('comp_update_childcomp.php?parent_id=<?php echo $row['id']; ?>','Update child companies',900,800)"><i class="fa fa-list"></i></a>
												<?php }
												if ($action_edited_history) { ?>
													<a class="w3-button w3-small w3-circle w3-yellow w3-border w3-border-red" data-record-id="<?= $row['id'] ?>" onclick="view_log_changes(this)" href="javascript:void(0)" title="View Log Edited History"><i class="fa fa-list text-danger"></i></a>
												<?php }
												?><a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" title="Update Password" onClick="popupwindow('comp_update_pass.php?edit_id=<?php echo $row['id']; ?>','Update Password',520,350)">
  <i class="fa fa-lock" style="color:red;"></i>
</a>

													<?php
											}
										} else {
											if ($action_restore_company) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-green" title="Restore Company" onClick="popupwindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','Restore company record',520,350)"><i class="fa fa-refresh"></i></a>
										<?php }
										} ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<div><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
				</div>
	</section>

	<!--Ajax processing modal-->
	<div class="modal" id="process_modal" data-backdrop="static">
		<div class="modal-dialog modal-lg" style="width: 85%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">×</button>
				</div>
				<div class="modal-body process_modal_attach">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<script src="js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
	<script>
		$(function() {
			$('.multi_class').multiselect({
				includeSelectAllOption: true,
				numberDisplayed: 1,
				enableFiltering: true,
				enableCaseInsensitiveFiltering: true
			});
		});

		// $('.tr_data').click(function(event) {
		// 	$('.div_actions').css('display', 'none');
		// 	$(this).next().css('display', 'block');
		// });

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

		function view_log_changes(element) {
			var table_name = "comp_reg";
			var record_id = $(element).attr("data-record-id");
			if (record_id && table_name) {
				$('.process_modal_attach').html("<center><i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i><br><h3>Loading ...<br><br>Please Wait !!!</h3></center>");
				$('#process_modal').modal('show');
				$('body').removeClass('modal-open');
				$.ajax({
					url: 'ajax_add_interp_data.php',
					method: 'post',
					dataType: 'json',
					data: {
						record_id: record_id,
						table_name: table_name,
						table_name_label: "Edit Company Profile",
						record_label: "Company Account",
						view_log_changes: 1
					},
					success: function(data) {
						if (data['status'] == 1) {
							$('.process_modal_attach').html(data['body']);
						} else {
							alert("Cannot load requested response. Please try again!");
						}
					},
					error: function(data) {
						alert("Error: Please select valid record for log details or refresh the page! Thank you");
					}
				});
			} else {
				alert("Error: Please select valid record for log details or refresh the page! Thank you");
			}
		}
	</script>
</body>

</html>