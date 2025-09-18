<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){session_start();} 
?>

<?php 
include 'db.php';
include 'class.php';
include_once ('function.php');
$name=@$_GET['name']; 
$gender=@$_GET['gender']; 
$city=@$_GET['city'];

    	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    	$limit = 50;
    	$startpoint = ($page * $limit) - $limit;	?>
<!doctype html>
<html lang="en">
<head>
<title>Employees salaries list</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<body>
<script>
function myFunction() {
	 var x = document.getElementById("name").value;if(!x){x="<?php echo $name; ?>";}
	 var y = document.getElementById("gender").value;if(!y){y="<?php echo $gender; ?>";}
	 var z = document.getElementById("city").value;if(!z){z="<?php echo $city; ?>";}
	 window.location.href="emp_salary_list.php" + '?name=' + x + '&gender=' + y + '&city=' + z;
	 
}
</script>
<?php 
include 'header.php';include 'nav2.php';?>
<!-- end of sidebar -->
	<style>.tablesorter thead tr {background: none;}</style>
<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12">
<header>
		    <center><a href="<?php echo basename(__FILE__);?>"><h2 class="col-md-4 col-md-offset-4 text-center"><div class="alert bg-primary h4">EMPLOYEES SALARIES REPORT</div></h2></a></center>
   <div class="col-md-12"><br>
             <div class="form-group col-md-2 col-sm-3 col-md-offset-1">
        <select id="name" name="name" onChange="myFunction()" class="form-control">
    <?php 			

  $sql_opt=
  "SELECT name 
  FROM emp 
  where 1=1 ##emp_active##
  ORDER BY name ASC";
  $sql_opt=SqlUtils::ModfiySql($sql_opt);

$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["name"];
    $name_opt=$row_opt["name"];
    $options.="<OPTION value='$code'>".$name_opt.' ('.$code.')';}
?>
    <?php if(!empty($name)){ ?>
    <option><?php echo $name; ?></option>
    <?php } else{?>
    <option value="">Employee Name</option>
    <?php } ?>
    <?php echo $options; ?>
    </option>
  </select>
          </div>
	        <div class="form-group col-md-2 col-sm-3">
      <select name="gender" id="gender" onChange="myFunction()" class="form-control">
        <?php GetSelectGenders($gender); ?>
      </select>
          </div>
	        <div class="form-group col-md-2 col-sm-3">
      <select name="city" id="city" onChange="myFunction()" class="form-control">
        <?php GetUsedCities($city,$con); ?>
      </select>
    </div>
        </div>
		</header>
		

		<div>
			<div>
			<table class="table table-bordered table-hover" cellspacing="0" width="100%"> 
			<thead class="bg-primary"> 
				<tr>
				  <th>Name</th>
				  <th>Designation</th>
   				  <th>Job Type</th> 
				  <th>Time From</th> 
   				  <th>Time To</th>
   				  <th>Total Time</th>
   				  <th align="center">Rate Per Hour</th>
   				  <th align="center">Salary</th>
   				  <th align="center">Entry Date</th> 
   				  <th align="center">Submitted</th>

            <!-- <th>Emp ID</th>-->
            <!-- <th>rol ID</th>-->

    				<th width="210" align="center">Actions</th> 
            
				</tr> 
			</thead> 
			<tbody> 
      <?php $table='emp';

  if(!empty($name) || !empty($gender)|| !empty($city))
  {
    $query=
    "SELECT $table.*,rolcal.*,rolcal.id as rolId,$table.id as empid 
    FROM $table 
		JOIN rolcal ON $table.id=rolcal.empId	
	  where name like '$name%' and gender like '$gender%' and city like '$city%'
    order by name,rolId
    LIMIT {$startpoint} , {$limit}";	
  }
  else
  { 
    $query=
    "SELECT $table.*,rolcal.*,rolcal.id as rolId,rolcal.entry_date ,$table.id as empid 
    FROM $table 
		JOIN rolcal ON $table.id=rolcal.empId	
    order by name,rolId
    LIMIT {$startpoint} , {$limit}";	
  }

			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr>
				  <td><?php echo $row['name']; ?></td>
				  <td><?php echo $row['desig']; ?></td> 
    				<td><?php echo $row['jt']; ?></td> 
				  <td><?php echo $row['start']; ?></td> 
   					<td><?php echo $row['finish']; ?></td>
   					<td align="center"><?php echo $row['duration']; ?></td>
   					<td align="center"><?php echo $row['rph']; ?></td>
   					<td align="center"><?php echo $row['salary']; ?></td>
   					<td align="center"><?php echo $misc->dated($row['entry_date']); ?></td> 
				  	<td><?php echo $row['sbmtd_by']; ?></td> 				

            <?php
				  	/*<td><?php echo $row['empid']; ?></td> 				
            <td><?php echo $row['rolId']; ?></td> 				*/
            ?>

    				<td align="center">
              
              <a href="javascript:void(0)" onClick="popupwindow('employee_view.php?view_id=<?php echo $row['empid']; ?>&table=<?php echo $table; ?>', 'title', 900,900);">
    				    <input type="image" src="images/icn_new_article.png" title="View Details">
    				  </a>

              <a href="javascript:void(0)" onClick="popupwindow('attendance_edit.php?edit_id=<?php echo $row['rolId']; ?>','_blank',750,550)"><input type="image" src="images/icn_edit.png" title="Edit"></a>
              <a href="javascript:void(0)" onClick="popupwindow('del.php?del_id=<?php echo $row['rolId']; ?>&table=<?php echo 'rolcal'; ?>','_blank',520,350)"><input type="image" src="images/icn_trash.png" title="Delete"></a>


              <?php if($_SESSION['prv']=='Management')
              {
                ?>
                <a href="javascript:void(0)" onClick="popupwindow('empsal_list_edited.php?view_id=<?php echo $row['id']; ?>','_blank',900,800)">
                    <input type="image" src="images/feedback.png" title="Edited List"></a>
                <?php
              }   
              ?>                  
              </td> 
				</tr> 
                <?php } ?>
                </tbody></table>                
			<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
		  </div><!-- end of #tab1 -->
			
			
			
		</div><!-- end of .tab_container -->
		
		</article><!-- end of content manager article --><!-- end of messages article -->
		
    <div class="clear"></div>
		
		<!-- end of post new article -->
		
		<div class="spacer"></div>
	</section>


</body>

</html>

<?php 

function GetSelectGenders($gender)
{
  if(!empty($gender))
  { ?>
    <option><?php echo $gender; ?></option>
    <?php } else{?>
    <option value="">--Select Gender--</option>
    <?php 
  } ?>
  <option>Male</option>
  <option>Female</option>
  <?php
}

function GetUsedCities($city,$con)
{
  if(!empty($city))
  { ?>
    <option><?php echo $city; ?></option>
    <?php 
  } 
  else
  {
    ?>
    <option value="">--Select City--</option>
    <?php 
  } 

  $sql_opt="SELECT distinct city FROM `emp` ORDER BY city ASC";
  $result_opt=mysqli_query($con,$sql_opt);
  $options="";
  
  while ($row_opt=mysqli_fetch_array($result_opt)) 
  {
    $code=$row_opt["city"];
    //$name_opt=$row_opt["city"];
      
    $options.="<option>".$code."</option>";
  }
  echo $options;
}

function GetUsedCitiesSys()
{
  if(!empty($city))
  { ?>
    <option><?php echo $city; ?></option>
    <?php 
  } 
  else
  {
    ?>
    <option value="">--Select City--</option>
    <?php 
  } 
  ?>

              <optgroup label="EnglandERS">
                <option>Bath</option>
                <option>Birmingham</option>
          <option>Bradford</option>
                <option>Bridgwater</option>
                <option>Bristol</option>
                <option>Buckinghamshire</option>
                <option>Cambridge</option>
                <option>Canterbury</option>
                <option>Carlisle</option>
                <option>Chippenham</option>
                <option>Cheltenham</option>
                <option>Cheshire</option>
                <option>Coventry</option>
                <option>Derby</option>
                <option>Dorset</option>
                <option>Exeter</option>
                <option>Frome</option>
                <option>Gloucester</option>
                <option>Hereford</option>
                <option>Leeds</option>
                <option>Leicester</option>
                <option>Liverpool</option>
                <option>London</option>
                <option>Manchester</option>
                <option>Newcastle</option>
                <option>Northampton</option>
                <option>Norwich</option>
                <option>Nottingham</option>
                <option>Oxford</option>
                <option>Plymouth</option>
                <option>Pool</option>
                <option>Portsmouth</option>
                <option>Salford</option>
                <option>Shefield</option>
                <option>Somerset</option>
                <option>Southampton</option>
                <option>Swindon</option>
                <option>Suffolk</option>
                <option>Surrey</option>
                <option>Taunton</option>
                <option>Trowbridge</option>
                <option>Truro</option>
                <option>Warwick</option>
                <option>Wiltshire</option>
                <option>Winchester</option>
                <option>Wells</option>
                <option>Weston Super Mare</option>
                <option>Worcester</option>
                <option>Wolverhampton</option>
                <option>York</option>           
              </optgroup>
              <optgroup label="Scotland">
                <option>Dundee</option>
                <option>Edinburgh</option>
                <option>Glasgow</option>
              </optgroup>
              <optgroup label="Wales">
                <option>Cardiff</option>
                <option>Newport</option>
                <option>Swansea</option>
              </optgroup>                   
              
  <?php 
}
?>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>