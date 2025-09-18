<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){session_start();}
include 'db.php';
include 'class.php';
include_once ('function.php'); 
$org=@$_GET['org'];
$p_order=@$_GET['p_order'];
$table='comp_credit';

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 100;
    	$startpoint = ($page * $limit) - $limit;	?>

<!doctype html>
<!doctype html>
<html lang="en">
<head>
<title><?php echo $page_title; ?> Credit List</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
<style>.multiselect {min-width: 250px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}</style>
</head>
<script>
function myFunction() {
	 var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
	 var z = document.getElementById("p_order").value;if(!z){z="<?php echo $p_order; ?>";}
	 window.location.href="credit_list.php" + '?org=' + y + '&p_order=' + z;
	 
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
                <a href="<?php echo basename(__FILE__);?>" class="alert-link"> Credit List</a>
              </div>
         <div class="col-md-9">
             <div class="form-group col-md-3 col-sm-4">
                <select id="p_order" name="p_order" onChange="myFunction()" class="form-control multi_class">
                    <?php $sql_opt="SELECT  distinct $table.porder FROM $table LEFT JOIN comp_reg ON $table.orgName=comp_reg.abrv where $table.porder<>'' and $table.deleted_flag=0 and $table.orgName like '$org%' and $table.porder like '$porder%'";
                        $result_opt=mysqli_query($con,$sql_opt);
                        $option="";
                        while ($row_opt=mysqli_fetch_array($result_opt)) {
                            $code=$row_opt["porder"];
                            $name_opt=$row_opt["porder"];
                            $option.="<option value='$code'>".$name_opt."</option>";}
                        ?>
		      <?php if(!empty($p_order)){ ?>
		      <option><?php echo $p_order; ?></option>
		      <?php } else{?>
		      <option value="">Select Purch-Order</option>
		      <?php } ?>
                    <?php echo $option; ?>
                  </select>
	        </div>
	        <div class="form-group col-md-3 col-sm-4">
	            <select id="org" name="org" onChange="myFunction()" class="form-control multi_class">
                    <?php 			
$sql_opt="SELECT name,abrv FROM comp_reg where deleted_flag=0 and po_req=1 ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $name_opt=$row_opt["name"];
    $options.="<option value='$code'>".$name_opt.'</option>';}
?>
		      <?php if(!empty($org)){ ?>
		      <option><?php echo $org; ?></option>
		      <?php } else{?>
		      <option value="">Select Company</option>
		      <?php } ?>
                    <?php echo $options; ?>
                  </select> 
                  </div>
            </div>
		</header>
		<div class="">
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-primary"> 
				<tr>
				  	<th>Purchase Order #</th>
				  	<th>Comp Name</th>
				  	<th>Credit</th> 
      				<th>Credit Date</th> 
    				<th>Debit</th> 
   				  	<th>Debit Date</th> 
                    <th>Remaining</th> 
                  	<th>Dated</th> 
   				</tr> 
			</thead> 
			<tbody> 
		<?php 
	   	$query="
		SELECT $table.id,$table.orgName,$table.porder,
			sum($table.credit) as credit,$table.credit_date,sum($table.debit) debit,$table.debit_date,$table.dated 
	    FROM $table
	  	LEFT JOIN comp_reg ON $table.orgName=comp_reg.abrv
	   	where $table.orgName like '$org%' and $table.porder like '$p_order%' and porder <> '' 
	   	group by $table.orgName
		LIMIT {$startpoint} , {$limit}";			
		
		$result = mysqli_query($con,$query);
		while($row = mysqli_fetch_array($result))
		{ 
			$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$row['orgName']);
			$orgName=$row['orgName'];

			$query_r="SELECT  sum($table.credit) - sum($table.debit) as remaining,sum($table.debit) debit,$table.debit_date 
				FROM $table
	   			where $table.orgName like '$orgName%'
	   			order by $table.id desc
				LIMIT 1";			
				
			$result_r = mysqli_query($con,$query_r);
			while($row_r = mysqli_fetch_array($result_r))
			{
				$remaining=$row_r['remaining'];
				$debit=$row_r['debit'];
				$debit_date=$row_r['debit_date'];
			}	

			if(!empty($p_order))
			{				
				$query_p="SELECT  $table.porder FROM $table
	   			where $table.orgName like '$orgName%' and porder <> '' and $table.porder like '$p_order%'
	   			order by $table.id desc
				LIMIT 1";	
			}
			else
			{
				$query_p="SELECT  $table.porder FROM $table
	   			where $table.orgName like '$orgName%' and porder <> ''
	   			order by $table.id desc
				LIMIT 1";
			}		

			$result_p = mysqli_query($con,$query_p);
			while($row_p = mysqli_fetch_array($result_p))
			{
				$porder=$row_p['porder'];
			}				
			?>            
			<tr>
			  	<td><?php echo $porder; ?></td>
			  	<td><?php echo $comp_name; ?></td>
			  	<td><?php echo round($row['credit'],2); ?></td> 
   				<td><?php echo $misc->dated($row['credit_date']); ?></td> 
   				<td><?php echo round($debit,2); ?></td> 
    			<td><?php echo $misc->dated($debit_date); ?></td> 
    			<td><?php echo round($remaining,2); ?></td> 
    			<td><?php echo $misc->dated($row['dated']); ?></td> 
   			</tr> 
			<?php 
		} 
		?>
        </tbody>
		</table>                
		<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
		</div>
	</section>
<script src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
$(function() {
	    $('.multi_class').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
    });
</script>
</body>
</html>