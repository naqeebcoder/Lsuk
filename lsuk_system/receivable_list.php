<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}
include 'db.php';
include 'class.php';
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=152 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_receivable = $_SESSION['is_root'] == 1 || in_array(108, $get_actions);
$action_edit_receivable = $_SESSION['is_root'] == 1 || in_array(109, $get_actions);
$action_delete_receivable = $_SESSION['is_root'] == 1 || in_array(110, $get_actions);
$action_restore_receivable = $_SESSION['is_root'] == 1 || in_array(111, $get_actions);
$action_view_installments = $_SESSION['is_root'] == 1 || in_array(112, $get_actions);
include_once('function.php');
$table = 'receivable';
$title = @$_GET['title'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
$tp = @$_GET['tp'];
$array_tp = array('a' => 'Active', 'tr' => 'Trashed');
$page_title = $array_tp[$tp] == 'Active' ? '' : $array_tp[$tp];
$deleted_flag = $tp == 'tr' ? 'deleted_flag = 1' : 'deleted_flag = 0';
$class = $tp == 'tr' ? 'alert-danger' : 'alert-info'; ?>
<!doctype html>
<html lang="en">
<head>
<title><?php echo $page_title; ?> Company Receivable List</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 4px!important;cursor:pointer;}html, body {background: #fff !important;}.div_actions{position: absolute;margin-top: -34px;background: #ffffff;border: 1px solid lightgrey;}.alert{padding: 6px;}.div_actions .fa {font-size: 14px;}.div_actions .w3-btn,.div_actions  .w3-button {padding: 2px 4px !important;}</style>
</head>
<script>
function myFunction() {
	 var x = document.getElementById("title").value;if(!x){x="<?php echo $title; ?>";}
	 var y = document.getElementById("search_2").value;if(!y){y="<?php echo $search_2; ?>";}
	 var z = document.getElementById("search_3").value;if(!z){z="<?php echo $search_3; ?>";}
	 var tp = document.getElementById("tp").value;if(!tp){tp="<?php echo $tp; ?>";}
	 window.location.href="receivable_list.php" + '?title=' + x + '&search_2=' + y + '&search_3=' + z+ '&tp=' + tp;
	 
}
function getInvoices() {
	 var x = document.getElementById("title").value;if(!x){x="<?php echo $title; ?>";}
	 var y = document.getElementById("search_2").value;if(!y){y="<?php echo $search_2; ?>";}
	 var z = document.getElementById("search_3").value;if(!z){z="<?php echo $search_3; ?>";}
	 var tp = document.getElementById("tp").value;if(!tp){tp="<?php echo $tp; ?>";}
	 window.location.href="receivable_list.php" + '?title=' + x + '&search_2=' + y + '&search_3=' + z+ '&tp=' + tp;
	 
}
</script>
<?php include 'header.php'; ?>
<body>    
<?php include 'nav2.php';?>
<!-- end of sidebar -->
	<style>.tablesorter thead tr {background: none;}</style>
<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12">
		<header>
		    <div class="alert <?php echo $class; ?> col-md-3">
                <a href="<?php echo basename(_FILE_);?>" class="alert-link"><?php echo $page_title; ?> Receivable List</a>
            </div>
			<div class="form-group col-md-2 col-sm-3">
				<a href="reports_lsuk/excel/rip_<?php echo basename(__FILE__); ?>" title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel</span></a>
			</div>
        </div>

		</header>
		

		<div>
			<div>
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-primary"> 
				<tr>
				  <th>Company</th>
				  <th width="20%">Invoices</th>
   				  <th>Total Amount</th>
   				  <th>Credit Limit</th>
   				  <th>Over Credit</th>
				  <th>Action</th>
				</tr> 
			</thead> 
			<tbody> 

	<?php
	$get_comps = $acttObj->read_all("id,name,abrv,credit_limit","comp_reg"," comp_nature IN (1,4) AND deleted_flag=0");
	$num_comps = mysqli_num_rows($get_comps);
	$comps_data = array();
	$data_count=0;
	$semi="\"'\"";
    if($num_comps>0){
        while($row = mysqli_fetch_assoc($get_comps)){
			$p_org = $row['id'];
			$p_name = $row['name'];
			$p_abrv = $row['abrv'];
			$p_credit_limit = $row['credit_limit']?:0;

			// $get_p_org_q = $acttObj->query_extra("GROUP_CONCAT($semi,TRIM(comp_reg.abrv),$semi) as ch_ids2,GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries,comp_reg", " subsidiaries.child_comp=comp_reg.id and subsidiaries.parent_comp=$p_org","set SESSION group_concat_max_len=10000");
			// $p_org_q = $get_p_org_q['ch_ids']?:'0';

			$get_p_org_q = $acttObj->query_extra("GROUP_CONCAT(TRIM(comp_reg.abrv)) as ch_ids2,GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries,comp_reg", " subsidiaries.child_comp=comp_reg.id and subsidiaries.parent_comp=$p_org","set SESSION group_concat_max_len=10000");
			$p_org_q = $get_p_org_q['ch_ids']?:'0';

			// $p_org_q = $acttObj->read_specific("GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries", " parent_comp=$p_org")['ch_ids']?:'0';
			$p_org_ad = ($p_org_q!=0?" and comp_reg.id IN ($p_org_q) ":" and comp_reg.id IN ($p_org) ");
			$qy='SELECT SUM(num_inv) as tot_inv,ROUND(SUM(total_cost),2) AS inv_cost from (SELECT COUNT(interpreter.id) as num_inv,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,comp_reg WHERE  interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and (interpreter.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status="") OR (interpreter.multInv_flag=0 AND interpreter.invoiceNo<>"" AND  (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0) )) '.$p_org_ad.' UNION ALL SELECT COUNT(telephone.id) as num_inv,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,comp_reg WHERE telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and (telephone.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status="") OR (telephone.multInv_flag=0 AND telephone.invoiceNo<>"" and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) AND telephone.total_charges_comp > 0) ))  '.$p_org_ad.' UNION ALL SELECT COUNT(translation.id) as num_inv,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,comp_reg WHERE  translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 AND translation.disposed_of = 0 and translation.order_cancel_flag=0 and (translation.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status="") OR (translation.multInv_flag=0 AND translation.invoiceNo<>"" and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp > 0))) '.$p_org_ad.') as grp';
			// echo $qy."<br>";
			$get_data = mysqli_query($con,$qy);
			$chk_data = mysqli_num_rows($get_data);
			// echo "rows are: ".$chk_data;
			if($chk_data>0){
				while($row2 = mysqli_fetch_assoc($get_data)){
					$tot_inv = $row2['tot_inv'];
					$inv_cost = $row2['inv_cost'];
					$comps_data[$data_count]['tot_inv']=$tot_inv;
					$comps_data[$data_count]['inv_cost']=$inv_cost;
					$comps_data[$data_count]['p_name']=$p_name;
					$comps_data[$data_count]['p_credit_limit']=$p_credit_limit;
					$comps_data[$data_count]['comp_abrv']=$get_p_org_q['ch_ids2']?:$p_abrv;
					$data_count++;
					
				}
			}
		}
		// print_r($comps_data);
		arsort($comps_data);
		$comps_data = array_values($comps_data);
		// echo "<br>After Sort<br>";
		// print_r($comps_data);
		for($j=0;$j<count($comps_data);$j++){
			if($comps_data[$j]['tot_inv']>0){
				$credit_cal = $comps_data[$j]['inv_cost']-$comps_data[$j]['p_credit_limit'];
			?>
			<tr>
				<td><?php echo $comps_data[$j]['p_name']; ?></td>
				<td><?php echo $comps_data[$j]['tot_inv']; ?></td>
				<td><?php echo $comps_data[$j]['inv_cost']; ?></td>
				<td><?php echo $comps_data[$j]['p_credit_limit']; ?></td>
				<td><?php echo ($comps_data[$j]['inv_cost']>$comps_data[$j]['p_credit_limit'])?($credit_cal):0; ?></td>
				<td><a data-toggle="tooltip" data-placement="top" href="rip_pending_all.php?search_1=<?php echo urlencode($comps_data[$j]['comp_abrv']); ?>&multi=1" class="w3-button w3-small w3-circle w3-white" title="View Invoices"><i class="fa fa-eye"></i></a></td>
			</tr>
			<?php
			}
		}
	}
	?>
    <tr class="bg-primary">
		<td><b>TOTAL</td>
		<td><b><?php echo array_sum(array_column($comps_data,'tot_inv')); ?></b></td>
		<td><b><?php echo array_sum(array_column($comps_data,'inv_cost')); ?></b></td>
		<td></td>
		<td></td>
	</tr>            
	</tbody></table>                
	<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
	</div>
	</section>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
	function myFunction() {
		var x = document.getElementById("title").value;
		if (!x) {
			x = "<?php echo $title; ?>";
		}
		var y = document.getElementById("search_2").value;
		if (!y) {
			y = "<?php echo $search_2; ?>";
		}
		var z = document.getElementById("search_3").value;
		if (!z) {
			z = "<?php echo $search_3; ?>";
		}
		var tp = document.getElementById("tp").value;
		if (!tp) {
			tp = "<?php echo $tp; ?>";
		}
		window.location.href = "receivable_list.php" + '?title=' + x + '&search_2=' + y + '&search_3=' + z + '&tp=' + tp;

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
		<div class="col-md-12">
			<header>
				<div class="alert <?php echo $class; ?> col-md-3">
					<a href="<?php echo basename(__FILE__); ?>" class="alert-link"><?php echo $page_title; ?> Receivable List</a>
				</div>
				<div class="form-group col-md-2 col-sm-3">
					<a href="reports_lsuk/excel/rip_<?php echo basename(__FILE__); ?>" title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel</span></a>
				</div>
				</div>

			</header>


			<div>
				<div>
					<table class="table table-bordered table-hover" cellspacing="0" width="100%">
						<thead class="bg-primary">
							<tr>
								<th>Title</th>
								<th width="20%">Total Amount</th>
								<th>Details</th>
								<th>Given By</th>
								<th>Received date</th>
								<?php if ($tp == 'tr') { ?><th>Deleted by</th> <?php } ?>
							</tr>
						</thead>
						<tbody>

							<?php

							if (isset($_GET['del_id'])) {
								$acttObj->update("receivable", array("deleted_flag" => 1), array("id" => $_GET['del_id']));
							}
							if (isset($_GET['restore_id'])) {
								$acttObj->update("receivable", array("deleted_flag" => 0), array("id" => $_GET['restore_id']));
							}

							if ($title || $search_2 || $search_3) {

								$WHERE2 = '';
								if (!empty($search_2) and !empty($search_2)) {

									$WHERE2 = "and receivable.received_date between '$search_2' and '$search_3'";
								}


								$query = "SELECT receivable.*,receivable_types.title FROM receivable,receivable_types WHERE receivable.receivable_id=receivable_types.id  and receivable_types.title like '$title%' $WHERE2  LIMIT {$startpoint} , {$limit}";
							} else {
								$query = "SELECT receivable.*,receivable_types.title FROM receivable,receivable_types WHERE receivable.receivable_id=receivable_types.id LIMIT {$startpoint} , {$limit}";
							}

							$result = mysqli_query($con, $query);
							while ($row = mysqli_fetch_array($result)) {
							?>
								<tr class="tr_data" <?php if ($row['deleted_flag'] == 1) { ?> style="background-color: #ff0000bf;color: white;" title="This Receivable has been deleted by LSUK!" <?php } ?>>
									<td><?php echo $row['title']; ?></td>
									<td><?php echo "Total: " . $row['amount'];
										if ($row['receivable_id'] == 4) {
											echo " Balance: " . $row['balance'];
										} ?></td>
									<td><?php echo $row['details']; ?></td>
									<td><?php echo $row['given_by'] ?: 'N/A'; ?></td>
									<td><?php echo $misc->dated($row['received_date']); ?></td>
									<?php if ($tp == 'tr') { ?><td style="color:#F00"><?php echo $row['deleted_by'] . '(' . $misc->dated($row['deleted_date']) . ')'; ?></td><?php } ?>
								</tr>
								<tr class="div_actions" style="display:none">
									<td colspan="9">
										<?php if ($tp != 'tr') {
											if ($action_view_receivable) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="View Record" onClick="popupwindow('receivable_view.php?view_id=<?php echo $row['id']; ?>', 'title', 800,500);"><i class="fa fa-eye"></i></a>
											<?php }
											if ($action_edit_receivable) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edit Record" onClick="popupwindow('receivable_edit.php?edit_id=<?php echo $row['id']; ?>','_blank',800,500)"><i class="fa fa-pencil-square-o"></i></a>
											<?php }
											if ($row['deleted_flag'] == 0) {
												if ($action_delete_receivable) { ?>
													<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Trash Record" onClick="if(confirm('Are you sure to delete this record?')){window.location.href='receivable_list.php?del_id=<?php echo $row['id']; ?>';}"><i class="fa fa-trash-o"></i></a>
												<?php }
											} else {
												if ($action_restore_receivable) { ?>
													<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Restore Record" onClick="if(confirm('Are you sure to restore this record?')){window.location.href='receivable_list.php?restore_id=<?php echo $row['id']; ?>';}"><i class="fa fa-refresh"></i> </a>
												<?php }
											}
											if ($row['title'] == 'Loans') {
												if ($action_view_installments) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="View Installements" onClick="popupwindow('receivable_partail.php?row_id=<?php echo $row['id']; ?>', 'title', 800,500);"><i class="fa fa-money"></i></a>
												<?php }
											}
										} else {
											if ($action_restore_receivable) { ?>
												<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-green" title="Restore Company" onClick="popupwindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)"><i class="fa fa-refresh"></i></a>
											<?php }
										} ?>
									</td>
								</tr>
							<?php
							} ?>
						</tbody>
					</table>
					<div><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
				</div>
	</section>
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<script>
		$('.tr_data').click(function(event) {
			$('.div_actions').css('display', 'none');
			$(this).next().css('display', 'block');
		});
	</script>
</body>
</html>