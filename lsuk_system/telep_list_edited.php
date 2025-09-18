<?php if(session_id() == '' || !isset($_SESSION)){session_start();} error_reporting(0); ?> 
<?php include 'db.php';include 'class.php';include_once ('function.php');$assignDate=@$_GET['assignDate']; $interp=@$_GET['interp']; $org=@$_GET['org']; $job=@$_GET['job'];$our=@$_GET['our'];$ur=@$_GET['ur'];$inov=@$_GET['inov'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 50;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">
<head>
<title>Telephone Jobs Edited List</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<?php include 'header.php'; ?>
<body>    
<?php include 'nav2.php';?>
<!-- end of sidebar -->
	<style>.tablesorter thead tr {background: none;}</style>
<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12">
		<header>
		    <div class="alert alert-info col-md-6">
                <a href="<?php echo basename(__FILE__);?>" class="alert-link">Telephone booking list - Edited Track of <span style="color:#F00;"> (<?php echo $_GET['view_id']; ?>)</</span></a>
              </div>
		</header>
		<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-primary">
				<tr>
				  <th>Source Lang</th>
    				<th>Company Name</th> 
    				<th>Interpreter</th> 
    				<th>Contact Name</th>
    				<th>Allocated By</th> 
                    <th>Assign-Date</th>
                    <th>Assign-Time</th>
                    <th>Enterd By</th> 
                    <th>Intrp Hrz</th> 
                    <th>comp Hrz</th> 
                    <th>Job Note</th> 
                    <th>Booking Type</th>  
                    <th>Edited by</th> 
    				<th width="320" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='hist_telephone'; $view_id=$_GET['view_id'];
	  
	  switch($_SESSION['prv']){
		case 'Management':
	
		$query="SELECT $table.*,interpreter_reg.name FROM $table
		JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id	   
		where  $table.id=$view_id and   $table.deleted_flag=0 and  $table.assignDate like '$assignDate%' and $table.multInv_flag=0   and $table.commit=0 and source like '%$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
		 order by assignDate LIMIT {$startpoint} , {$limit}";	
		
		break; 
		case 'Finance':
		
		$query="SELECT $table.*,interpreter_reg.name FROM $table
		JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id	   
		where  $table.id=$view_id and   $table.deleted_flag=0 and  $table.hoursWorkd<>0 and $table.assignDate like '$assignDate%' and  $table.multInv_flag=0  and $table.commit=0  and $table.multInv_flag=0 and source like '%$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' order by assignDate and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
		LIMIT {$startpoint} , {$limit}";
		break; 
		case 'Operator':
		
		$query="SELECT $table.*,interpreter_reg.name FROM $table
		JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id	   
		where  $table.id=$view_id and   $table.deleted_flag=0 and  orderCancelatoin=0 and $table.hoursWorkd=0 and $table.assignDate like '$assignDate%' and  $table.multInv_flag=0  and $table.commit=0  and $table.multInv_flag=0 and source like '%$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' order by assignDate and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
		LIMIT {$startpoint} , {$limit}";	
		break;  
	  }
	  
	  
		
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr>
				  <td><?php echo $row['source']; ?></td>
   					<td><?php if($row['C_hoursWorkd']==0){ ?><span style="color:#F00" title="Comp Hours: <?php echo $row['C_hoursWorkd']; ?>"><?php echo $row['orgName']; ?></span><?php }else{ echo $row['orgName']; }?></td>  
    				<td><?php if($row['hoursWorkd']==0){ ?><span style="color:#F00" title="Interp Hours: <?php echo $row['hoursWorkd']; ?>"><?php echo $row['name']; ?></span><?php }else{ echo $row['name']; }?></td> 
    				<td><?php echo $row['orgContact']; ?></td>
    				<td><?php echo $row['aloct_by'].'('.$misc->dated($row['aloct_date']).')'; ?></td> 
    				<td><?php echo date_format(date_create($row['assignDate']), 'd-m-Y'); ?></td>
    				<td><?php echo $row['assignTime']; ?></td>  
    				<td><?php echo $row['submited'].'('.$misc->dated($row['dated']).')'; ?></td> 
    				<td><?php echo $row['hrsubmited'].'('.$misc->dated($row['interp_hr_date']).')'; ?></td> 
    				<td><?php echo $row['comp_hrsubmited'].'('.$misc->dated($row['comp_hr_date']).')'; ?></td> 
    				<td><?php echo $row['snote']; ?></td> 
    				<td><?php echo $row['bookinType']; ?></td>  
    				<td><?php echo $row['edited_by']; ?><br/><?php echo $row['edited_date']; ?></td>  
    				<td align="center">
                    <a href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&edited_date=<?php echo $row['edited_date']; ?>', 'title', 1000, 1000);"><input type="image" src="images/icn_new_article.png" title="View Edited Record"></a>
                    </td> 
				</tr> 
                <?php } ?>
                </tbody></table>                
			<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
		  </div>
		</section>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>