<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php 
include 'db.php';
include 'class.php';
include_once ('function.php'); 

$org=@$_GET['org'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 100;
		$startpoint = ($page * $limit) - $limit;	
		
?>
<!doctype html>
<html lang="en">
<head>
<title><?php echo $page_title; ?>Business Credit List Full</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<script>
function myFunction() {
	 var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
	 window.location.href="bz_credit_list_full.php" + '?org=' + y ;
	 
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
                <a href="<?php echo basename(__FILE__);?>" class="alert-link"> Business Credit List Full</a>
              </div>
         <div class="col-md-9">
             <div class="form-group col-md-3 col-sm-4">
        <select id="org" name="org" onChange="myFunction()" class="form-control">
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
                  </select> </div>
		</header>
		

		<div class="">
			<div id="" class="">
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-primary"> 
				<tr>
    				<th>Mode</th> 
				  	<th>Credit Id</th>
				  	<th>Invoice No.</th>
				  	<th>Comp Name</th>
				  	<th>Credit</th> 
      				<th>Credit Date</th> 
    				<th>Debit</th> 
   				  	<th>Debit Date</th> 
   				  	<th>Remaining</th> 
                  	<th>Dated</th> 
    				<th width="80" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='bz_credit';

	$query=
	   "SELECT  $table.id,$table.creditId,$table.mode,$table.invoiceNo,$table.orgName,
	   	$table.bz_credit,$table.bz_credit_date,$table.bz_debit ,$table.bz_debit_date,$table.dated 
	   FROM $table
	   LEFT JOIN comp_reg ON $table.orgName=comp_reg.abrv
	   where $table.deleted_flag=0 and $table.orgName like '$org%'
	   LIMIT {$startpoint} , {$limit}";			
		
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result))
	{
			$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$row['orgName']);

			/*
			$orgName=$row['orgName'];

			$query_r=
			"SELECT  sum($table.bz_credit) - sum($table.bz_debit) as remaining,sum($table.bz_debit) bz_debit,$table.bz_debit_date 
			FROM $table
	   		where $table.orgName like '$orgName%'
	   		order by $table.id desc
	    	LIMIT 1";			
			
		  $result_r = mysqli_query($con,$query_r);
		  while($row_r = mysqli_fetch_array($result_r))
		  {
				$remaining=$row_r['remaining'];
				//$bz_debit=$row_r['bz_debit'];
				//$bz_debit_date=$row_r['bz_debit_date'];
		  }*/

		  $remaining=$row['bz_credit']-$row['bz_debit'];

			?>            
			<tr>
			  <td><?php echo ucwords($row['mode']); ?></td>
			  <td><?php echo $row['creditId']; ?></td>
			  <td><?php echo $row['invoiceNo']; ?></td>
			  <td><?php echo $comp_name; ?></td>
			  <td><?php echo round($row['bz_credit'],2); ?></td> 
   			  <td><?php echo $misc->dated($row['bz_credit_date']); ?></td> 
   			  <td><?php echo round($row['bz_debit'],2); ?></td> 
    		  <td><?php echo $misc->dated($row['bz_debit_date']); ?></td>

    		  <td><?php echo  round($remaining,2); ?></td>

    		  <td><?php echo $misc->dated($row['dated']); ?></td> 

    		  <td align="center">
                    <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ){?>
                     <a href="javascript:void(0)" onClick="popupwindow('bz_comp_credit_edit.php?edit_id=<?php echo $row['id']; ?>','_blank',850,500)"><input type="image" src="images/icn_edit.png" title="Edit"></a>
                     <?php if($_SESSION['prv']=='Management'){?>
                    <a href="javascript:void(0)" onClick="popupwindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)"><input type="image" src="images/icn_trash.png" title="Trash"></a>
                    <a href="bz_credit_list_full_edited.php?view_id=<?php echo $row['id']; ?>"><input type="image" src="images/feedback.png" title="Edited List"></a>
                    <?php }} ?>
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
</body>
</html>