<?php if(session_id() == '' || !isset($_SESSION)){session_start();} error_reporting(0); ?> 
<?php include 'db.php';include 'class.php';include_once ('function.php');$assignDate=@$_GET['assignDate']; $interp=@$_GET['interp']; $org=@$_GET['org']; $job=@$_GET['job'];$our=@$_GET['our'];$ur=@$_GET['ur'];$inov=@$_GET['inov'];
    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 50;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">
<head>
<title>Interpreting Jobs Edited List</title>
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
                <a href="<?php echo basename(__FILE__);?>" class="alert-link">Interpreter booking list - Edited Track of <span style="color:#F00;"> (<?php echo $_GET['view_id']; ?>)</</span></a>
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
      <?php $table='hist_interpreter'; $view_id=$_GET['view_id'];
	  
	  switch($_SESSION['prv']){
		case 'Management':
		
	   $query="SELECT $table.*,interpreter_reg.name FROM $table
	   JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
	   where $table.id=$view_id and $table.deleted_flag=0 and  $table.assignDate like '$assignDate%' and $table.multInv_flag=0 and $table.commit=0 and source like '$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
	   order by assignDate LIMIT {$startpoint} , {$limit}";
	
		break; 
		case 'Finance':
		
	   $query="SELECT $table.*,interpreter_reg.name FROM $table
	   JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
	   where  $table.id=$view_id and   $table.deleted_flag=0 and  $table.hoursWorkd<>0 and $table.assignDate like '$assignDate%' and  $table.multInv_flag=0  and $table.commit=0  and source like '$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
	   order by assignDate LIMIT {$startpoint} , {$limit}";	
		break; 
		case 'Operator':
		
	   $query="SELECT $table.*,interpreter_reg.name FROM $table
	   JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
	   where  $table.id=$view_id and   $table.deleted_flag=0 and  orderCancelatoin=0 and $table.hoursWorkd=0 and $table.assignDate like '$assignDate%' and  $table.multInv_flag=0  and $table.commit=0  and source like '$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
	   order by assignDate LIMIT {$startpoint} , {$limit}";
		break;  
	  }
	  
	  
		  //$mapLast
		//$mapCols=array();
		//$mapCols["source";
		//$arrCols[]="source";
		$mapCols["id"]=null;
		$result = mysqli_query($con,$query);
		while($row = mysqli_fetch_array($result))
		{?>            
			<tr title="<?php echo "Tracking ID:". $row['id']; ?>">
			  <td><?php echo $row['source']; ?></td>

				<?php 
					if($row['C_hoursWorkd']==0)
					  	$htm='<span style="color:#F00" title="Comp Hours: '.$row['C_hoursWorkd'].'">'.
						  $row['orgName'].'</span>';
					else
					  	$htm=$row['orgName'];
					EchoColData($mapCols,'C_hoursWorkd',$htm);
					$mapCols["C_hoursWorkd"]=$htm;
				?>
				<?php 
					if($row['hoursWorkd']==0)
					  	$htm='<span style="color:#F00" title="Interp Hours: '.$row['hoursWorkd'].'">'.
						  $row['orgName'].'</span>';
					else
					  	$htm=$row['name'];
					EchoColData($mapCols,'hoursWorkd',$htm);
					$mapCols["hoursWorkd"]=$htm;
				?>

				<?php 
				  	$htm=$row['orgContact'];
					EchoColData($mapCols,'orgContact',$htm); 
					$mapCols["orgContact"]=$htm;
				?>
				<?php 
				  	$htm=$row['aloct_by'].'('.$misc->dated($row['aloct_date']).')';
					EchoColData($mapCols,'aloct_by',$htm); 
					$mapCols["aloct_by"]=$htm;
				?>
				<?php 
				  	$htm=$misc->dated($row['assignDate']);
					EchoColData($mapCols,'assignDate',$htm); 
					$mapCols["assignDate"]=$htm;
				?>
				<?php 
				  	$htm=$row['assignTime'];
					EchoColData($mapCols,'assignTime',$htm); 
					$mapCols["assignTime"]=$htm;
				?>
				<?php 
				  	$htm=$row['submited'].'('.$misc->dated($row['dated']).')';
					EchoColData($mapCols,'submited',$htm); 
					$mapCols["submited"]=$htm;
				?>
				<?php 
				  	$htm=$row['hrsubmited'].'('.$misc->dated($row['interp_hr_date']).')';
					EchoColData($mapCols,'hrsubmited',$htm); 
					$mapCols["hrsubmited"]=$htm;
				?>
				<?php 
				  	$htm=$row['comp_hrsubmited'].'('.$misc->dated($row['comp_hr_date']).')';
					EchoColData($mapCols,'comp_hrsubmited',$htm); 
					$mapCols["comp_hrsubmited"]=$htm;
				?>
				<?php 
				  	$htm=$row['snote'];
					EchoColData($mapCols,'snote',$htm); 
					$mapCols["snote"]=$htm;
				?>
				<?php 
				  	$htm=$row['bookinType'];
					EchoColData($mapCols,'bookinType',$htm); 
					$mapCols["bookinType"]=$htm;
				?>
				<?php 
				  	$htm=$row['edited_by'].'<br/>'.$row['edited_date'];
					EchoColData($mapCols,'edited_by',$htm); 
					$mapCols["edited_by"]=$htm;
				?>

    			<td align="center">
					<a href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&edited_date=<?php echo $row['edited_date']; ?>', 'title', 1000, 1000);"><input type="image" src="images/icn_new_article.png" title="View Edited Record"></a>
                </td> 
				</tr> 
                <?php } ?>
                </tbody></table>                
			<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
		  </div>
		</section>
<?php 
function EchoColData($mapref,$strCol,$htm)
{
	$ec="<td";
	if (isset($mapref[$strCol]) && $mapref[$strCol]!=$htm)
	{
		//$ec.=" style='color:#ff5536;background-color:yellow;'";
		$ec.=" style='background-color:yellow;'";
	}
	$ec.=">".$htm."</td>";
	//$mapref[$strCol]=$htm;

	//return $ec;
	echo $ec;
} ?>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>