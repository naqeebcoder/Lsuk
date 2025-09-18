<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) { session_start();}
include 'db.php';
include 'class.php';
include_once 'function.php';
$table="daily_logs";
$get_date_1=@$_GET['get_date_1'];
$get_date_2=@$_GET['get_date_2'];
$get_user=@$_GET['get_user'];
if($get_date_1){
    $append_get_date="and DATE($table.dated)='".$get_date_1."'";
}
if($get_date_2){
    $append_get_date="and DATE($table.dated)='".$get_date_2."'";
}
if($get_date_1 && $get_date_2){
    $append_get_date="and DATE($table.dated) BETWEEN ('".$get_date_1."') and ('".$get_date_2."')";
}
if($get_user){
    $append_get_user="and $table.user_id=".$get_user."";
    $append_summary_user="and $table.user_id=".$get_user."";
    $get_user_name=$acttObj->read_specific("CONCAT(name,' (',prv,')') as selected_user","login","id=".$get_user)['selected_user'];
}
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;
$page_count=$startpoint;
?>
<!doctype html>
<html lang="en">
<head>
<title>Daily Logs</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>html,body{background:none !important;}
.badge-counter{border-radius: 0px!important;margin: -9px -9px!important;font-size: 10px;float: left;}
.pagination>.active>a{
    background:#337ab7;
}</style>
<script>
function myFunction() {
   var get_date_1 = document.getElementById("get_date_1").value;if(!get_date_1){get_date_1="<?php echo $get_date_1; ?>";}
   var get_date_2 = document.getElementById("get_date_2").value;if(!get_date_2){get_date_2="<?php echo $get_date_2; ?>";}
   var get_user = document.getElementById("get_user").value;if(!get_user){get_user="<?php echo $get_user; ?>";}
   window.location.href="<?php echo basename(__FILE__);?>" + '?get_date_1=' + get_date_1 + '&get_date_2=' + get_date_2 + '&get_user=' + get_user;
}
</script>
</head>
<?php include 'header.php';?>
<body>
<?php include 'nav2.php';?>
<style>.tablesorter thead tr {background: none;}</style>
<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12">
		<header>
    <?php $get_dates=$acttObj->read_all("DISTINCT DATE(dated)","$table","1");
          $options="";
          while($row_dates=$get_dates->fetch_assoc()){
            $options.="<option value='".$row_dates['DATE(dated)']."'>".$row_dates['DATE(dated)']."</option>";
          } ?>
		    <div class="form-group col-md-2 col-sm-4 mt15" style="margin-top: 15px;">
            <select id="get_date_1" onChange="myFunction()" name="get_date_1" class="form-control">
            <?php if ($get_date_1) { ?>
                <option value="<?php echo $get_date_1; ?>" selected><?php echo $get_date_1; ?></option>
                <?php } ?>
                <option value="" disabled <?php if (empty($get_date_1)) { echo 'selected'; } ?>>Filter From Date</option>
                <?php echo $options; ?>
            </select>
        </div>
		    <div class="form-group col-md-2 col-sm-4 mt15" style="margin-top: 15px;">
            <select id="get_date_2" onChange="myFunction()" name="get_date_2" class="form-control">
            <?php if ($get_date_2) { ?>
                <option value="<?php echo $get_date_2; ?>" selected><?php echo $get_date_2; ?></option>
                <?php } ?>
                <option value="" disabled <?php if (empty($get_date_2)) { echo 'selected'; } ?>>Filter To Date</option>
                <?php echo $options; ?>
            </select>
        </div>
		    <div class="form-group col-md-2 col-sm-4 mt15" style="margin-top: 15px;">
                <select id="get_user" onChange="myFunction()" name="get_user" class="form-control">
                <?php $get_users=$acttObj->read_all("DISTINCT $table.user_id,login.name,login.prv","$table,login","$table.user_id=login.id");
				if ($get_user) {?>
                    <option value="<?php echo $get_user; ?>" selected><?php echo $get_user_name; ?></option>
                    <?php }?>
                    <option value="" disabled <?php if (empty($get_user)) { echo 'selected'; } ?>>Filter By User</option>
                    <?php while($row_users=$get_users->fetch_assoc()){ ?>
                    <option value="<?php echo $row_users['user_id']; ?>"><?php echo $row_users['name']." (".$row_users['prv'].")"; ?></option>
                    <?php } ?>
                </select>
            </div>
		  <div class="form-group col-md-4 col-md-offset-0 col-sm-4 mt15"><h2 class="text-center"><a href="<?php echo basename(__FILE__);?>"><span class="label label-primary">System Daily Logs</a></span></h2>
          </div>
		<div class="tab_container col-md-12" id="put_data">
        <?php $query="select $table.id,login.name,user_actions.title,$table.details,$table.dated FROM $table,login,user_actions where $table.user_id=login.id AND $table.action_id=user_actions.id $append_get_date $append_get_user LIMIT {$startpoint} , {$limit}";
			  $result = $acttObj->read_all("$table.id,login.name,user_actions.title,$table.details,$table.dated","$table,login,user_actions","$table.user_id=login.id AND $table.action_id=user_actions.id $append_get_date $append_get_user LIMIT {$startpoint} , {$limit}"); ?>
        <center><div class="col-md-12"><?php echo pagination($con, $table, $query, $limit, $page); ?></div></center>
			<table class="tablesorter table table-bordered tbl_data" cellspacing="0" cellpadding="0">
			    <thead class="bg-info">
                <tr>
                    <td>S.No</td>
                    <td>User Name</td>
                    <td>Action</td>
                    <td>Details</td>
                    <td>Date</td>
   				</tr>
			</thead>
			<tbody>
        <?php if($result->num_rows==0){
                    echo '<tr>
            		  <td colspan="7"><h4 class="text-danger text-center">Sorry ! There are no records currently.</h4></td></tr>';
                }else{
                    while ($row = $result->fetch_assoc()) {
                    $to_find = array("F2F", "TP", "TR");
                    $to_change = array("Face To Face","Telephone","Translation");
                    $page_count++;$counter++;?>
            		<tr>
            		  <td><?php echo '<span class="w3-badge w3-blue badge-counter">'.$page_count.'</span>';?></td>
					  <td><?php echo $row['name'];?></td>
            		  <td><?php echo $row["title"]; ?></td>
                      <td><?php echo str_replace($to_find, $to_change, $row["details"]); ?></td>
                      <td><?php echo $row['dated']; ?></td>
					  </tr>
                <?php }?>
                </tbody>
          </table>
		  <?php } ?>
    </div>
	</section>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>