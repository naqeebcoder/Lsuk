<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}
$table = 'interpreter_reg';
include 'actions.php';
include_once('function.php');
//Access actions
$get_actions = explode(",", $obj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=25 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_interpreter_profile = $_SESSION['is_root'] == 1 || in_array(140, $get_actions);
$action_generate_pay_slip = $_SESSION['is_root'] == 1 || in_array(141, $get_actions);
$name = @$_GET['name'];
$month1 = date('m');
$to = @$_GET['to'];
$from = @$_GET['from'];
$salary_date = isset($_GET['salary_date']) ? $_GET['salary_date'] : date('Y-m-d', strtotime('last day of this month'));
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">
<head>
	<title>Interpreters Salaries Record</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<script>
	function myFunction() {
		var x = document.getElementById("name").value;
		if (!x) {
			x = "<?php echo $name; ?>";
		}
		var y = document.getElementById("from").value;
		if (!y) {
			y = "<?php echo $from; ?>";
		}
		var z = document.getElementById("to").value;
		if (!z) {
			z = "<?php echo $to; ?>";
		}
		var salary_date = document.getElementById("salary_date").value;
		if (!salary_date) {
			salary_date = "<?php echo $salary_date; ?>";
		}
		window.location.href = "interp_work_list.php" + '?name=' + x + '&from=' + y + '&to=' + z + '&salary_date=' + salary_date;
	}
</script>

<?php include 'header.php';?>

<body>
	<?php include 'nav2.php';?>
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
						<h2 class="col-md-3 col-md-offset-4 text-center"><span class="label label-primary">INTERPRETERS SALARIES LIST</span></h2>
					</a></center><br>
				<div class="col-md-12"><br>
					<div class="form-group col-lg-2 col-md-4 col-sm-4">
						<select id="name" onChange="myFunction()" name="name" class="form-control">
							<?php
							if (!empty($name) || !empty($from) || !empty($to)) {
								$result_opt = $obj->read_all("interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email,interpreter_reg.gender, interpreter_reg.city", "$table JOIN interpreter ON $table.id=interpreter.intrpName",
								"interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.pay_int=1 and interpreter.intrp_salary_comit=0 and interpreter.hoursWorkd>0 and $table.name like '$name%' and interpreter.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email,interpreter_reg.gender, interpreter_reg.city FROM $table 
								JOIN telephone ON $table.id=telephone.intrpName
								where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.pay_int=1 and telephone.intrp_salary_comit=0 and telephone.hoursWorkd>0 and $table.name like '$name%' and telephone.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email,interpreter_reg.gender, interpreter_reg.city FROM $table 
								JOIN translation ON $table.id=translation.intrpName
								where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.pay_int=1 and translation.intrp_salary_comit=0 and translation.numberUnit>0 and $table.name like '$name%' and translation.asignDate between '$from' and '$to' ORDER BY name ASC");
							} else {
								$from = date('Y-m-d', strtotime('first day of last month'));
								$from = date('Y-m-d', strtotime($from . ' + 10 days'));
								$to = date('Y-m-d', strtotime('first day of this month'));
								$to = date('Y-m-d', strtotime($to . ' + 9 days'));
								$result_opt = $obj->read_all("interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email,interpreter_reg.gender, interpreter_reg.city", "$table JOIN interpreter ON $table.id=interpreter.intrpName",
								"interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.pay_int=1 and interpreter.intrp_salary_comit=0 and interpreter.hoursWorkd>0 and interpreter.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email,interpreter_reg.gender, interpreter_reg.city FROM $table 
								JOIN telephone ON $table.id=telephone.intrpName
								where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.pay_int=1 and telephone.intrp_salary_comit=0 and telephone.hoursWorkd>0 and telephone.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email,interpreter_reg.gender, interpreter_reg.city FROM $table 
								JOIN translation ON $table.id=translation.intrpName
								where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.pay_int=1 and translation.intrp_salary_comit=0 and translation.numberUnit>0 and translation.asignDate between '$from' and '$to' ORDER BY name ASC");
							}
							$options = "";
							while ($row_opt = $result_opt->fetch_assoc()) {
								$code = $row_opt["name"];
								$name_opt = $row_opt["name"];
								$city_opt = $row_opt["city"];
								$gender = $row_opt["gender"];
								$options .= "<OPTION value='$code'>" . $name_opt . ' (' . $gender . ')' . ' (' . $city_opt . ')';
							}
							?>
							<?php if (!empty($name)) { ?>
								<option><?php echo $name; ?></option>
							<?php } else { ?>
								<option value="">Select Interpreter</option>
							<?php } ?>
							<?php echo $options; ?>
							</option>
						</select>
					</div>
					<div class="form-group col-lg-2 col-md-3 col-sm-4">
						<input type="date" name="from" id="from" placeholder='From' class="form-control" value="<?php echo $from; ?>" />
					</div>
					<div class="form-group col-lg-2 col-md-3 col-sm-4">
						<input type="date" name="to" id="to" placeholder='To' class="form-control" value="<?php echo $to; ?>" />
					</div>
					<div class="form-group col-lg-1 col-md-3 col-sm-2">
						<label>Month :</label>
					</div>
					<div class="form-group col-lg-2 col-md-3 col-sm-4">
						<input onchange="myFunction()" title="Select month of salary to pay" type="date" name="salary_date" id="salary_date" value="<?php echo $salary_date; ?>" class="form-control" />
					</div>
					<div class="form-group col-lg-2 col-md-3 col-sm-4">
						<input type="button" name="btn_get" id="btn_get" class="btn btn-primary btn-sm" onclick="myFunction()" value="Submit" />
					</div>


			</header>


			<div class="tab_container">
				<div id="tab1" class="tab_content">
					<table class="tablesorter table table-bordered" cellspacing="0" width="100%">
						<thead class="bg-primary">
							<tr>
								<th>Name</th>
								<th>Gender</th>
								<th>Interpreter</th>
								<th>Ph Interp</th>
								<th>Translation</th>
								<th>City</th>
								<th>Contact No</th>
								<th>Email</th>
								<th width="120" align="center">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php $table = 'interpreter_reg';
							if (!empty($name) || !empty($from) || !empty($to)) {
								$paginate_query = "SELECT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email FROM $table JOIN interpreter ON $table.id=interpreter.intrpName
								WHERE interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.pay_int=1 and interpreter.intrp_salary_comit=0 and interpreter.hoursWorkd>0 and $table.name like '$name%' and interpreter.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email FROM $table 
								JOIN telephone ON $table.id=telephone.intrpName
								where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.pay_int=1 and telephone.intrp_salary_comit=0 and telephone.hoursWorkd>0 and $table.name like '$name%' and telephone.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email FROM $table 
								JOIN translation ON $table.id=translation.intrpName
								where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.pay_int=1 and translation.intrp_salary_comit=0 and translation.numberUnit>0 and $table.name like '$name%' and translation.asignDate between '$from' and '$to' LIMIT {$startpoint} , {$limit}";
								$result = $obj->read_all("interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email", "$table JOIN interpreter ON $table.id=interpreter.intrpName",
								"interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.pay_int=1 and interpreter.intrp_salary_comit=0 and interpreter.hoursWorkd>0 and $table.name like '$name%' and interpreter.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email FROM $table 
								JOIN telephone ON $table.id=telephone.intrpName
								where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.pay_int=1 and telephone.intrp_salary_comit=0 and telephone.hoursWorkd>0 and $table.name like '$name%' and telephone.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email FROM $table 
								JOIN translation ON $table.id=translation.intrpName
								where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.pay_int=1 and translation.intrp_salary_comit=0 and translation.numberUnit>0 and $table.name like '$name%' and translation.asignDate between '$from' and '$to' LIMIT {$startpoint} , {$limit}");
							} else {
								$from = date('Y-m-d', strtotime('first day of last month'));
								$from = date('Y-m-d', strtotime($from . ' + 9 days'));
								$to = date('Y-m-d', strtotime('first day of this month'));
								$to = date('Y-m-d', strtotime($to . ' + 10 days'));
								$paginate_query = "SELECT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email FROM $table JOIN interpreter ON $table.id=interpreter.intrpName 
								WHERE interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.pay_int=1 and interpreter.intrp_salary_comit=0 and interpreter.hoursWorkd>0 and interpreter.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email FROM $table 
								JOIN telephone ON $table.id=telephone.intrpName
								where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.pay_int=1 and telephone.intrp_salary_comit=0 and telephone.hoursWorkd>0 and telephone.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email FROM $table 
								JOIN translation ON $table.id=translation.intrpName
								where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.pay_int=1 and translation.intrp_salary_comit=0 and translation.numberUnit>0 and translation.asignDate between '$from' and '$to' LIMIT {$startpoint} , {$limit}";
								$result = $obj->read_all("interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email", "$table JOIN interpreter ON $table.id=interpreter.intrpName",
								"interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.pay_int=1 and interpreter.intrp_salary_comit=0 and interpreter.hoursWorkd>0 and interpreter.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email FROM $table 
								JOIN telephone ON $table.id=telephone.intrpName
								where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.pay_int=1 and telephone.intrp_salary_comit=0 and telephone.hoursWorkd>0 and telephone.assignDate between '$from' and '$to'
								union
								SELECT  interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.interp,interpreter_reg.telep,interpreter_reg.trans,interpreter_reg.city,interpreter_reg.contactNo,interpreter_reg.email FROM $table 
								JOIN translation ON $table.id=translation.intrpName
								where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.pay_int=1 and translation.intrp_salary_comit=0 and translation.numberUnit>0 and translation.asignDate between '$from' and '$to' LIMIT {$startpoint} , {$limit}");
							}
							while ($row = $result->fetch_assoc()) { ?>
								<tr>
									<td><?php echo $row['name']; ?></td>
									<td><?php echo $row['gender']; ?></td>
									<td><?php echo $row['interp']; ?></td>
									<td><?php echo $row['telep']; ?></td>
									<td><?php echo $row['trans']; ?></td>
									<td><?php echo $row['city']; ?></td>
									<td><?php echo $row['contactNo']; ?></td>
									<td><?php echo $row['email']; ?></td>
									<td align="center">
										<?php if ($action_view_interpreter_profile) { ?>
										<button type="button" class="btn btn-default" onClick="MM_openBrWindow('full_view_interpreter.php?view_id=<?php echo $row['id']; ?>','View interpreter profile pay','scrollbars=yes,resizable=yes,width=850,height=900,left=432,top=38')">
											<i class="fa fa-eye" title="View Interpreter Profile"></i>
										</button>
										<?php }
										if ($action_generate_pay_slip) { ?>
											<button type="button" class="btn btn-primary" onClick="popupwindow('pay_slip.php?fdate=<?php echo $from; ?>&tdate=<?php echo $to; ?>&salary_date=<?php echo $salary_date; ?>&submit=<?php echo $row['id']; ?>','Make pay slip', 1000,800);">
												<i class="fa fa-money" title="Generate Pay Slip"></i>
											</button>
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<div><br><?php echo pagination($obj->con, $table, $paginate_query, $limit, $page); ?></div>
				</div>
	</section>
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>