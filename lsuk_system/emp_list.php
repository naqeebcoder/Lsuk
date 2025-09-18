<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){session_start();
} 
?> 
<?php 
include 'db.php';
include 'class.php';
include_once ('function.php');

$name=SafeVar::GetVar('name',"");
$gender=SafeVar::GetVar('gender',"");
$city=SafeVar::GetVar('city',"");

$active=SafeVar::GetVar('active',"");
$act_id=SafeVar::GetVar('act_id',"");

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
$tp = @$_GET['tp'];
$array_tp=array('a'=>'Active','tr'=>'Trashed');
$page_title=$array_tp[$tp]=='Active'?'':$array_tp[$tp];
$deleted_flag=$tp=='tr'?'deleted_flag = 1':'deleted_flag = 0';
$class=$tp=='tr'?'label-danger':'label-primary';
      ?>
<!doctype html>
<html lang="en">
<head>
<title><?php echo $page_title; ?> Registered Employees List</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 4px!important;cursor:pointer;}html, body {background: #fff !important;}.div_actions{position: absolute;margin-top: -48px;background: #ffffff;border: 1px solid lightgrey;}.alert{padding: 6px;}.div_actions .fa {font-size: 14px;}.w3-btn, .w3-button {padding: 8px 10px!important;}</style>
</head>
<script>
function myFunction() {
	 var x = document.getElementById("name").value;if(!x){x="<?php echo $name; ?>";}
	 var y = document.getElementById("gender").value;if(!y){y="<?php echo $gender; ?>";}
	 var z = document.getElementById("city").value;if(!z){z="<?php echo $city; ?>";}
	 var tp = document.getElementById("tp").value;if(!tp){tp="<?php echo $tp; ?>";}
	 window.location.href="emp_list.php" + '?name=' + x + '&gender=' + y + '&city=' + z+ '&tp=' + tp;
	 
}
</script>
<?php include 'header.php'; ?>
<body>    
<?php include 'nav2.php';?>
<!-- end of sidebar -->
<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12">
		<header>
		    <center><a href="<?php echo basename(__FILE__);?>"><h2 class="col-md-3 text-center"><span class="label <?php echo $class; ?>"><?php echo $page_title; ?> Employees List</span></h2></a></center><br>
         <div class="col-md-9">
             <div class="form-group col-md-3 col-sm-4">
   <?php if($_SESSION['prv']=='Management'){ ?>
        <select id="name" onChange="myFunction()" name="name" class="form-control">
		      <?php 			
$sql_opt="SELECT name FROM emp where $deleted_flag ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["name"];
    $name_opt=$row_opt["name"];$city_opt=$row_opt["city"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
		      <?php if(!empty($name)){ ?>
		      <option><?php echo $name; ?></option>
		      <?php } else{?>
		      <option value="">--Select Interpreter--</option>
		      <?php } ?>
		      <?php echo $options; ?>
		      </option>
	        </select>
	        </div>
	        <div class="form-group col-md-3 col-sm-4">
         <select id="gender" onChange="myFunction()" name="gender" class="form-control">
		       <?php if(!empty($gender)){ ?>
		      <option><?php echo $gender; ?></option>
		      <?php } else{?>
		      <option value="">--Select Gender--</option>
		     
              <option>Male</option>
              <option>Female</option> <?php } ?>
	        </select>
	        </div>
	        <div class="form-group col-md-3 col-sm-4">
        <select name="city" id="city" onChange="myFunction()" class="form-control">
        		      <?php if(!empty($city)){ ?>
		      <option><?php echo $city; ?></option>
		      <?php } else{?>
		      <option value="">--Select City--</option>
		      <?php } ?>
                    <optgroup label="England">
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
                  </select><?php } ?>
	        </div>
        <div class="form-group col-md-3 col-sm-4 col-xs-6 ">
              <?php if($_SESSION['prv']=='Management'){ ?>
        <select id="tp" onChange="myFunction()" name="tp" class="form-control">
                <?php
                if (!empty($tp)) {?>
                    <option value="<?php echo key($array_tp[$tp]);?>" selected><?php echo $array_tp[$tp]; ?></option>
                    <?php }?>
                    <option value="" disabled <?php if (empty($tp)) { echo 'selected' ; }?>>Filter by Type</option>
                <option value="a">Active</option>
                <option value="tr">Trashed</option>
            </select>
            <?php }else{ ?>
            <input type="hidden" value='' id="tp" onChange="myFunction()" name="tp" class="form-control"/>
            <?php } ?>
            </div>
        </div>
	</header>
		<div>
			<div >
			<table class="table table-bordered" cellspacing="0" width="100%"> 
			<thead class="bg-primary"> 
				<tr>
				  <th>Name</th>
				  <th>Designation</th>
   				  <th>Job Type</th> 
				  <th>Gender</th> 
   				  <th>Contact No</th> 
                    <th>City</th>
				  <?php if($tp=='tr'){ ?><th>Deleted by</th> <?php } ?>
				</tr> 
			</thead> 
			<tbody><style>.bg-danger{background-color: #f2dede42;}</style>
      <?php $table='emp';  $pasport=$_SESSION['pasport'];
	  if($_SESSION['prv']=='Management'){		
	 	$query="SELECT * FROM $table 
	   where $table.$deleted_flag and name like '$name%' and gender like '$gender%' and city like'$city%'
	   LIMIT {$startpoint} , {$limit}";	}
	   else{ $query="SELECT * FROM $table 
	   where $table.$deleted_flag and passp = '$pasport' and name like '$name%' and gender like '$gender%' and city like'$city%'
	   LIMIT {$startpoint} , {$limit}";}
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>            
				<tr class="tr_data <?php echo $row['active']==0?'':'bg-danger'; ?>" title="<?php echo $row['active']==0?'':$row['name'].' is currently de-activated!'; ?> Click on row to see actions">
				  <td><?php echo $row['name']; ?></td>
				  <td><?php echo $row['desig']; ?></td> 
    				<td><?php echo $row['jt']; ?></td> 
				  <td><?php echo $row['gender']; ?></td> 
   					<td><?php echo $row['contact']; ?></td>
                    <td><?php echo $row['city']; ?></td>	
			<?php if($tp=='tr'){ ?><td style="color:#F00"><?php echo $row['deleted_by'].'('.$misc->dated($row['deleted_date']).')'; ?></td><?php } ?>
			</tr>
    		<tr class="div_actions" style="display:none">
			<td colspan="9">
			    <?php if($tp!='tr'){ ?>
                    <?php if($_SESSION['prv']=='Management'){?>
                    <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="View Employee" onClick="popupwindow('employee_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'title', 900,900);">
					<i class="fa fa-eye"></i></a>
              	<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edit Employee" onClick="popupwindow('employee_edit.php?edit_id=<?php echo $row['id']; ?>','_blank',1100, 570)">
					  	<i class="fa fa-pencil-square-o"></i></a>
                <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Trash Employee" onClick="popupwindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)">
						<i class="fa fa-trash-o"></i></a>
                    <?php } ?>
                <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Staff Attendance" onClick="popupwindow('wages.php?empId=<?php echo $row['id']; ?>','_blank',750,550)">
						<i class="fa fa-user"></i></a>
                   <?php if($_SESSION['prv']=='Management'){ ?> 
                	<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edited List" onClick="popupwindow('emp_list_edited.php?view_id=<?php echo $row['id']; ?>','_blank',800,550)">
						<i class="fa fa-list-alt"></i></a>
                    <a href="emp_list.php?active=<?php echo $row['active']; ?>&&act_id=<?php echo $row['id']; ?>" 
                      title="Status">
                    <?php 
                    $status=$row['active']; 
                    if($status==0){
                      echo '<span title="Click for De-activate" style=" color:green;background: green;width: 15px;height: 20px;-moz-border-radius: 50px; -webkit-border-radius: 50px;border-radius: 50px;">On</span>';
                    }else{
                      echo '<span title="Click for Activate" style=" color:red;background: red;width: 15px;height: 20px;-moz-border-radius: 50px;	-webkit-border-radius: 50px;border-radius: 50px;">On</span>';
                    } ?>
                    </a> 
                  <?php }
                  }else{
                  if($_SESSION['prv']=='Management'){?>
<a href="javascript:void(0)" onClick="popupwindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)"><input type="image" src="images/icn_jump_back.png" title="Restore"></a>

<a href="javascript:void(0)" onClick="popupwindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank',520,350)"><input type="image" src="images/icn_trash.png" title="Delete"></a> 
                    <?php } ?>
                     <?php } ?>
                    </td> 
				</tr> 
                <?php } ?>
                </tbody></table>                
			<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
		  </div>
	</section>
	<?php 
      if($active==0 && !empty($act_id))
      {
        $acttObj->editFun($table,$act_id,'active',1); 
        $acttObj->editFun($table,$act_id,'edited_by',$_SESSION['UserName']);
        $acttObj->editFun($table,$act_id,'edited_date',date("Y-m-d H:i:s"));
        //$acttObj->new_old_table('hist_'.$table,$table,$act_id);
        ?>
        <script>window.location.href="<?php echo basename(__FILE__);?>"</script>
        <?php 
      }
      
      if($active==1 && !empty($act_id))
      {
        $acttObj->editFun($table,$act_id,'active',0); 
        $acttObj->editFun($table,$act_id,'edited_by',$_SESSION['UserName']);
        $acttObj->editFun($table,$act_id,'edited_date',date("Y-m-d H:i:s"));
        //$acttObj->new_old_table('hist_'.$table,$table,$act_id);
        
        ?>
        <script>window.location.href="<?php echo basename(__FILE__);?>"</script>
        <?php 
      } 
      ?>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
$('.tr_data').click(function(event){
    $('.div_actions').css('display','none');
    $(this).next().css('display','block');
  });
</script>
</body>
</html>