<?php 
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 

include 'db.php';
include 'class.php';
include_once ('function.php');
$table='expence';
$title=@$_GET['title'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
$tp = @$_GET['tp'];
$array_tp=array('a'=>'Active','tr'=>'Trashed');
$page_title=$array_tp[$tp]=='Active'?'':$array_tp[$tp];
$deleted_flag=$tp=='tr'?'deleted_flag = 1':'deleted_flag = 0';
$class=$tp=='tr'?'alert-danger':'alert-info'; ?>
<!doctype html>
<html lang="en">
<head>
<title>Fix Supplier Names</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 4px!important;cursor:pointer;}html, body {background: #fff !important;}.div_actions{position: absolute;margin-top: -48px;background: #ffffff;border: 1px solid lightgrey;}.alert{padding: 6px;}.div_actions .fa {font-size: 14px;}.w3-btn, .w3-button {padding: 8px 10px!important;}</style>
</head>
<script>
function myFunction() {
	 var x = document.getElementById("title").value;if(!x){x="<?php echo $title; ?>";}
	//  var y = document.getElementById("search_2").value;if(!y){y="<?php echo $search_2; ?>";}
	//  var z = document.getElementById("search_3").value;if(!z){z="<?php echo $search_3; ?>";}
	//  var tp = document.getElementById("tp").value;if(!tp){tp="<?php echo $tp; ?>";}
	 window.location.href="fix_sup.php" + '?title=' + encodeURIComponent(x) ;
	 
}
</script>
<?php
if(isset($_GET['submit'])){
    $title=$_GET['title'];
    $new_sup_name= mysqli_real_escape_string($con, $_GET['new_sup_name']);
    $qu = mysqli_query($con,'UPDATE expence SET comp="'.$new_sup_name.'" WHERE comp="'.$title.'"');
	if($qu){
		header('Location:fix_sup.php');
	}else{
		echo "<h1>Error Updating the Record</h1>";
	}

}
?>
<?php include 'header.php'; ?>
<body>    
<?php include 'nav2.php';?>
<!-- end of sidebar -->
	<style>.tablesorter thead tr {background: none;}</style>
<section class="container-fluid" >
<div class="col-md-12">
		<header>
		    <div class="alert <?php echo $class; ?> col-md-3">
                <a href="<?php echo basename(__FILE__);?>" class="alert-link"><?php echo $page_title; ?> Expenses List</a>
              </div>
         <div class="col-md-9">
             <div class="form-group col-md-3 col-sm-4">
                <form action="" method="GET">
        <select id="title" name="title" onChange="myFunction()" class="form-control searchable">
    <?php 			
$sql_opt="SELECT DISTINCT comp as title FROM expence WHERE deleted_flag=0 ORDER BY comp ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["title"];
    $name_opt=$row_opt["title"];
	// $title=urldecode($title);
    $options.='<OPTION value="'.$code.'" '.((!empty($title) && $title==$code)?'selected':'').'>'.((!empty($name_opt))?$name_opt:'Empty');}
?>
    
    <option value="">Select Supplier</option>
    <?php echo $options; ?>
    </option>
  </select>
	        </div>
            <div class="form-group col-md-3 col-sm-4">
                <input type="text" class="form-control" name="new_sup_name" placeholder="Enter new name of this Supplier Here ..">
            </div>
            <button type="submit" class="btn btn-primary" value="submit" name="submit">Submit</button>
            </form>
		</header>
		

		<div>
			<div>
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-primary"> 
				<tr>
				  <th>Title <?php echo $title; ?></th>
				  <th>Net Amount</th>
				  <th>VAT Amount</th>
				  <th>Non-VAT Amount</th>
				  <th>Total Amount</th>
   				  <th>Details </th>
   				  <th>Voucher #</th>
   				  <th>Payment By</th>
   				  <th>Company</th> 
				  <th>Bill Date</th>
				  <th>Receipt</th>
				  <?php if($tp=='tr'){ ?><th>Deleted by</th> <?php } ?>
				</tr> 
			</thead> 
			<tbody> 

	  <?php
	if($title || $search_2 || $search_3)
	{
	   $tl_qy = (!empty($title)?'and expence.comp="'.$title.'"':'');
	   $sr_qy = ((!empty($search_2) && !empty($search_3))?' and expence.billDate between "'.$search_2.'" and "'.$search_3.'" ':'');
	   $query="SELECT expence.*,expence_list.title FROM expence,expence_list WHERE expence.type_id=expence_list.id and expence.$deleted_flag $tl_qy $sr_qy  ORDER BY billDate ASC LIMIT {$startpoint} , {$limit}";
	//    echo $query;
	//    die();exit();
	}
	else
	{
		$query="SELECT expence.*,expence_list.title FROM expence,expence_list WHERE expence.type_id=expence_list.id and expence.$deleted_flag ORDER BY billDate ASC LIMIT {$startpoint} , {$limit}";
	}
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result))
	{
		$e_id= $row['id'];
		?>            
		<tr class="tr_data" title="Click on row to see actions">
			<td><?php echo $row['title']; ?></td>
			<td><?php echo $row['netamount']; ?></td> 
			<td><?php echo $row['vat']; ?></td> 
			<td><?php echo $row['nonvat']; ?></td> 
			<td><?php echo $row['amoun']; ?></td> 
    		<td><?php echo $row['details']; ?></td>
    		<td><?php echo $row['voucher']; ?></td>
    		<td><?php echo $row['pay_by']?:'N/A'; ?></td>
    		<td><?php echo $row['comp']; ?></td> 
			<td><?php echo $misc->dated($row['billDate']); ?></td>	
			<td align="center"><?php if($row['exp_receipt']!=''){ ?>
				<a class='btn btn-primary view_attach' id="<?php echo $row['id']; ?>"><i class="fa fa-eye"> </i> View</a>
				<?php } ?></td>	
			<?php if($tp=='tr'){ ?><td style="color:#F00"><?php echo $row['deleted_by'].'('.$misc->dated($row['deleted_date']).')'; ?></td><?php } ?>
			</tr>
    		<tr class="div_actions" style="display:none">
			<td colspan="9">
			    <?php if($tp!='tr'){ ?>
			    
			    <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="View Expense" onClick="popupwindow('expence_view.php?view_id=<?php echo $row['id']; ?>', 'title', 800,500);">
					<i class="fa fa-eye"></i></a>
              	<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edit Expense" onClick="popupwindow('expence_edit.php?edit_id=<?php echo $row['id']; ?>','_blank',800,500)">
					  	<i class="fa fa-pencil-square-o"></i></a>
                <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Trash Expense" onClick="popupwindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)">
						<i class="fa fa-trash-o"></i></a>
                	<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edited List" onClick="popupwindow('expence_list_edited.php?view_id=<?php echo $row['id']; ?>','_blank',900,800)">
						<i class="fa fa-list-alt"></i></a>
                     <?php }else{ ?>
                     <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance'){?>
                <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-green" title="Restore Company" onClick="popupwindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)">
						<i class="fa fa-refresh"></i></a>
                	<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" title="Delete Company" onClick="popupwindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)">
						<i class="fa fa-trash-o"></i></a>
                    <?php } ?>
                     <?php } ?>
            </td> 

		</tr> 
		<?php 
	} ?>
                
	</tbody></table>                
	<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
	</div>
	</section>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
    $(function() {
	    $('.searchable').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
    });
$('.tr_data').click(function(event){
    $('.div_actions').css('display','none');
    $(this).next().css('display','block');
  });
  $(document).on('click','.view_attach',function(e){
      e.preventDefault();
      window.open('exp_receipt_view.php?v_id='+this.id, "popupWindow", "width=600,height=600,scrollbars=yes");
    });
</script>
</body>
</html>