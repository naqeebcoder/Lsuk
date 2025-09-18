<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){session_start();}
include 'actions.php';
include_once('function.php');
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">

<head>
	<title>System Users List</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<style>
		.modal-open {
            overflow: initial !important;
        }
	</style>
</head>
<?php include 'header.php'; ?>

<body>
	<?php include 'nav2.php'; ?>
	<section class="container-fluid" style="overflow-x:auto">
		<div class="col-md-12">
			<center>
				<div class="alert alert-info col-sm-3">
					<a href="<?php echo basename(__FILE__); ?>?1&user_status=1" class="alert-link">Registered System Users list</a>
				</div>
			</center>
			<div class="form-group col-md-2 col-sm-4 pull-right" style="margin-top: 15px;">
				<select id="user_status" name="user_status" class="form-control" onchange="filter_list()">
					<option value="">-- Filter by Status --</option>
					<option <?=isset($_GET['user_status']) && $_GET['user_status'] == 1 ? 'selected' : ''?> value="1">Active</option>
					<option <?=isset($_GET['user_status']) && $_GET['user_status'] == 0 ? 'selected' : ''?> value="0">Disabled</option>
				</select>
			</div>
			<div class="form-group col-md-2 col-sm-4 pull-right" style="margin-top: 15px;">
				<select id="prv" name="prv" class="form-control" onchange="filter_list()">
					<option value="">-- Filter by Role --</option>
					<?php $get_roles = $obj->read_all("*", "rolenamed", "1");
					while ($row_role = $get_roles->fetch_assoc()) {
						$selected_role = $_GET['prv'] == $row_role["named"] ? 'selected' : '';
						echo "<option " . $selected_role . " value='" . $row_role["named"] . "'>". $row_role["named"] . "</option>";
					}?>
				</select>
			</div>
			<?php $table = "login";
			if(isset($_GET['user_status'])){
				$append_status = " and login.user_status=" . $_GET['user_status'];
			}
			if($_GET['prv']){
				$append_prv = " and login.prv='" . $_GET['prv'] . "'";
			}
			$paginate_query="SELECT * FROM login WHERE 1 $append_status $append_prv LIMIT {$startpoint} , {$limit}";
			$get_users = $obj->read_all("*","login","1 $append_status $append_prv LIMIT {$startpoint} , {$limit}");
			if($get_users->num_rows > 0){ ?>
				<table class="table table-bordered" cellspacing="0" width="100%">
					<thead class="bg-primary">
						<tr>
							<th>Name</th>
							<th>Passport #</th>
							<th>Email</th>
							<th>Password</th>
							<th>Role</th>
							<th>Status</th>
							<th width="180" align="center">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php while ($row = $get_users->fetch_assoc()) { ?>
							<tr <?php if ($row['user_status'] == 0) { ?> style="background-color: #ff0000bf;color: white;" title="<?php echo $row['name'] . ' has been blocked by LSUK!'; ?>" <?php } ?>>
								<td><?php echo ucwords($row['name']); ?></td>
								<td><?php echo $row['pasport'] ?: 'Not Provided'; ?></td>
								<td><?php echo $row['email']; ?></td>
								<td><?php echo $row['pass']; ?></td>
								<td><?php echo $row['prv'];
									echo $row['is_allocation_member'] == 1 ? "<span class='label label-primary pull-right'>Allocation</span>" : ""; ?></td>
								<td><?php echo $row['Temp'] == 1 ? '<span class="label label-warning">Temporary</span>' : '<span class="label label-success">Normal</span>'; ?></td>
								<td align="center">
									<button class="btn btn-sm btn-default" href="javsscript:void(0)" onClick="popupwindow('signup_edit.php?edit_id=<?php echo $row['id']; ?>', 'title', 1100, 570);"><i class="fa fa-edit"></i></button>
									<button onclick="request_user_accessibles(this, <?= $row['id'] ?>);" class='btn btn-sm btn-info'>Actions</button>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				<div>
					<?php echo pagination($obj->con, $table, $paginate_query, $limit, $page); ?>
				</div>
			<?php }else{
				echo '<div class="row"><div class="alert alert-danger alert-dismissible show col-md-6 col-md-offset-3 text-center" role="alert">
					<strong>Sorry! </strong>There are no records in this list currently for selected filters!</td></tr>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
					</div></div>';
			} ?>
		</div>
	</section>
	<!--User accessible modal uploading model-->
	<div class="modal" id="accessible_modal">
		<div class="modal-dialog modal-lg" style="width:80%">
			<div class="modal-content">
				<!-- Modal body -->
				<div class="modal-body accessible_modal_attach">
				</div>
				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<script>
		function filter_list() {
			var append_url="<?=basename(__FILE__)?>?1";
			var user_status=$('#user_status').val();
			if(user_status){
				append_url+='&user_status='+user_status;
			}
			var prv=$('#prv').val();
			if(prv){
				append_url+='&prv='+prv;
			}
			window.location.href=append_url;
		}
		function request_user_accessibles(element, user_id) {
			$("tbody tr").removeClass("bg-primary");
			$(element).parents("tr").addClass("bg-primary");
			$('.accessible_modal_attach').html("<center><i class='fa fa-circle fa-3x'></i><i class='fa fa-circle fa-3x'></i><i class='fa fa-circle fa-3x'></i><br><h3>Loading ...<br><br>Please Wait !!!</h3></center>");
			$.ajax({
				url: 'process/role_permissions.php',
				method: 'post',
				data: {
					user_id: user_id,
					redirect_url: window.location.href,
					request_user_accessibles: 1
				},
				success: function(data) {
					$('.accessible_modal_attach').html(data);
					$('#accessible_modal').modal('show');
				},
				error: function(data) {
					alert("Error code : " + data.status + " , Error message : " + data.statusText);
				}
			});
		}

		function update_accessible_action(element, user_id, action_id, action_for) {
			var type;
			if ($(element).is(':checked')) {
				type = "insert";
			} else {
				type = "delete";
			}
			$.ajax({
				url: 'process/role_permissions.php',
				method: 'post',
				dataType: 'json',
				data: {
					user_id: user_id,
					action_id: action_id,
					type: type,
					action_for: action_for,
					update_accessible_action: 1
				},
				success: function(response) {},
				error: function(response) {
					alert("Error code : " + data.status + " , Error message : " + data.statusText);
				}
			});
		}
	</script>
</body>

</html>