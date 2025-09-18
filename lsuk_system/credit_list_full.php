<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php if(session_id() == '' || !isset($_SESSION)){session_start();} 

include 'db.php';
include 'class.php';
include_once ('function.php'); 

$org=@$_GET['org'];
$porder=@$_GET['porder'];
$table='comp_credit';

    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 100;
		$startpoint = ($page * $limit) - $limit;	
?>
<!doctype html>
<html lang="en">
<head>
<title><?php echo $page_title; ?> Credit List Full</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>.multiselect {min-width: 250px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}</style>
</head>
<script>
function myFunction() {
	 var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
	 var z = document.getElementById("porder").value;if(!z){z="<?php echo $porder; ?>";}
	 window.location.href="credit_list_full.php" + '?org=' + encodeURIComponent(y) + '&porder=' + encodeURIComponent(z);
	 
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
		    <div class="alert alert-info col-md-3">
                <a href="<?php echo basename(__FILE__);?>" class="alert-link"> Credit List Full</a>
              </div>
         <div class="col-md-9">
             <div class="form-group col-md-3 col-sm-4">
        <select id="porder" name="porder" onChange="myFunction()"  class="form-control multi_class">
                    <?php 			
$sql_opt="SELECT  DISTINCT $table.porder FROM $table
	   LEFT JOIN comp_reg ON $table.orgName=comp_reg.abrv
	   where $table.porder<>'' and $table.deleted_flag=0 and $table.orgName like '$org%' and $table.porder like '$porder%'";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["porder"];
    $name_opt=$row_opt["porder"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
		      <?php if(!empty($porder)){ ?>
		      <option><?php echo $porder; ?></option>
		      <?php } else{?>
		      <option value="">--Select P-Order--</option>
		      <?php } ?>
                    <?php echo $options; ?>
                    </option>
                  </select>
	        </div>
	        <div class="form-group col-md-3 col-sm-4">
        <select id="org" name="org" onChange="myFunction()" class="form-control multi_class">
                    <?php 			
$sql_opt="SELECT name,abrv FROM comp_reg ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $name_opt=$row_opt["name"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
		      <?php if(!empty($org)){ ?>
		      <option><?php echo $org; ?></option>
		      <?php } else{?>
		      <option value="">--Select Org--</option>
		      <?php } ?>
                    <?php echo $options; ?>
                    </option>
                  </select> 
                  </div>
            </div>
		</header>
		

		<div class="">
			<div id="" class="">
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-primary"> 
				<tr>
    				<th>Mode</th> 
				  	<th>Purchase Order #</th>
				  	<!-- <th>Invoice No.</th> -->
				  	<th>Comp Name</th>
					<th>Last Credit Date</th> 
				  	<th>Total Credit</th> 
					<th>Used Balance</th>
    				<!-- <th>Debit</th> 
   				  	<th>Debit Date</th> 
                  	<th>Dated</th>  -->
					<th>Remaining</th>
					<th>Invoices</th>
    				<th width="80" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php 
	   $query=
	   "SELECT  $table.id,$table.porder,$table.mode,$table.invoiceNo,$table.orgName,
	   		$table.credit,$table.credit_date,$table.debit debit,$table.debit_date,$table.dated 
		FROM $table
	   	LEFT JOIN comp_reg ON $table.orgName=comp_reg.abrv
	   	where $table.deleted_flag=0 and $table.porder!='' and $table.orgName like '$org%' and $table.porder like '$porder%'
		group by $table.porder 
		order by $table.credit_date DESC
		LIMIT {$startpoint} , {$limit}";	
		
		// $query=$acttObj->read_all("$table.id,$table.porder,$table.mode,$table.invoiceNo,$table.orgName,
		// $table.credit,$table.credit_date,$table.debit debit,$table.debit_date,$table.dated","$table LEFT JOIN comp_reg ON $table.orgName=comp_reg.abrv"," $table.deleted_flag=0 and $table.porder!='' GROUP BY $table.porder ORDER BY $table.credit_date DESC LIMIT {$startpoint} , {$limit} ");
		// // die();exit();
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result))
			{
				$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$row['orgName']);
				$porder_num = $row['porder'];
				// $porder_inv = $acttObj->read_specific(" SUM(num_inv) as no_inv,SUM(total_cost) as used_credit ","(SELECT COUNT(interpreter.id) as num_inv,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter where interpreter.porder='$porder_num' AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0  UNION ALL SELECT COUNT(telephone.id) as num_inv,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone WHERE telephone.porder='$porder_num' AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 UNION ALL SELECT COUNT(translation.id) as num_inv,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation ","translation.porder='$porder_num' AND translation.deleted_flag=0 and translation.order_cancel_flag=0 ) as grp");

				$porder_inv = $acttObj->read_specific(" SUM(num_inv) as no_inv,SUM(total_cost) as used_credit ","(SELECT COUNT(interpreter.id) as num_inv,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter where interpreter.porder='$porder_num' AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 AND interpreter.commit=1 and interpreter.invoic_date!='1001-01-01'  UNION ALL SELECT COUNT(telephone.id) as num_inv,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone WHERE telephone.porder='$porder_num' AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 AND telephone.commit=1 and telephone.invoic_date!='1001-01-01' UNION ALL SELECT COUNT(translation.id) as num_inv,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation ","translation.porder='$porder_num' AND translation.deleted_flag=0 and translation.order_cancel_flag=0 AND translation.commit=1 and translation.invoic_date!='1001-01-01') as grp");
				$no_inv = $porder_inv['no_inv'];
				$used_credit = $porder_inv['used_credit'];
				$sum_amount_qy = $acttObj->read_specific(" MAX(credit) as po_balance ","comp_credit"," porder='$porder_num' AND deleted_flag=0");
				$sum_amount=$sum_amount_qy['po_balance'];
				$remaining = round($sum_amount-$used_credit,2);
				// echo "$porder invoices are: ".$porder_inv;
				// die();exit();
				?>            
				<tr>
				  <td><?php echo ucwords($row['mode']); ?></td>
				  <td><?php echo $row['porder']; ?></td>
				  <!-- <td><?php echo $row['invoiceNo']; ?></td> -->
				  <td><?php echo $comp_name; ?></td>
				  <td><?php echo $misc->dated($row['credit_date']); ?></td> 
				  <td><?php echo round($sum_amount,2); ?> </td> 
				  <!-- <td><?php echo round($row['credit'],2); ?></td>  -->
   				  <td><?php echo round($used_credit,2); ?></td> 
   				  <!-- <td><?php echo round($row['debit'],2); ?></td> 
    			  <td><?php echo $misc->dated($row['debit_date']); ?></td>
    			  <td><?php echo $misc->dated($row['dated']); ?></td>  -->
				  <td><?php echo $remaining; ?></td>
				  <td><button class="btn btn-primary getInvoices" id="getInvoices_<?php echo $row['porder']; ?>" data-toggle="modal" data-target="#view_invoices">View (<?php echo $no_inv; ?>)</button></td>

    			  <td align="center">
                   
				    <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' )
				    {	?>
                     	<a href="javascript:void(0)" onClick="popupwindow('comp_credit_edit.php?edit_id=<?php echo $row['id']; ?>&rem=<?php echo $remaining ?>','_blank',950,700)"><input type="image" src="images/icn_edit.png" title="Edit"></a>
					 	<?php if($_SESSION['prv']=='Management')
					 	{
							?>
								<a href="javascript:void(0)" onClick="popupwindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)"><input type="image" src="images/icn_trash.png" title="Trash"></a>
        		            <a href="credit_list_full_edited.php?view_id=<?php echo $row['id']; ?>"><input type="image" src="images/feedback.png" title="Edited List"></a>
							<?php 
						}
				    } ?>
                   </td> 
				</tr> 
				<?php 
			} ?>
        </tbody></table>                

		<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
		</div>
	</section>

<!-- Invoices Modal -->
<div class="modal fade" id="view_invoices" tabindex="-1" role="dialog" aria-labelledby="view_invoicesLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="view_invoicesLabel">Invoices</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="view_invoices_body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
$(function() {
	    $('.multi_class').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
    });
	$(document).ready(function(){
		$(document).on('click','.getInvoices',function(){
			var get_inv = (this.id).split("_")[1];
			console.log(get_inv);
			$.post("ajaxporder.php", {
				get_inv: get_inv
			},function(data) {
				// console.log(data);
				var json_data = JSON.parse(data);
				console.log(json_data['matches']);
				$('#view_invoices_body').html(json_data['body']);

				// if(json_data['matches']>0){
				// 	$('#proceed_bk').html('Proceed Anyway');
				// 	$('#proceed_bk').removeClass('btn-primary');
				// 	$('#proceed_bk').addClass('btn-danger');
				// 	$("#compare_modal").modal('show');
				// }else{
				// 	alert('No Duplicates Found! Proceed to Confirm Job.');
				// 	$('#proceed_bk').html('Proceed');
				// 	$('#proceed_bk').removeClass('btn-danger');
				// 	$('#proceed_bk').addClass('btn-primary');
				// 	$('#btn_confirm').removeClass('hidden');
				// 	$('#btn_compare').addClass('hidden');
				// }
				// $("#compare_modal").modal('show');
			});
		});
	})
</script>
</body>
</html>