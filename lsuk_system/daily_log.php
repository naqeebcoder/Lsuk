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
$action_id=@$_GET['action_id'];
if($get_date_1){
    $append_get_date="and DATE($table.dated)='".$get_date_1."'";
}else{
    $yesterday=date('Y-m-d',strtotime("-1 days"));
    $append_get_date="and DATE($table.dated)='".$yesterday."'";
}
if($get_date_2){
    $append_get_date="and DATE($table.dated)='".$get_date_2."'";
}else{
    $today=date('Y-m-d');
    $append_get_date="and DATE($table.dated)='".$today."'";
}
if($get_date_1 && $get_date_2){
    $append_get_date="and DATE($table.dated) BETWEEN ('".$get_date_1."') and ('".$get_date_2."')";
}
if($action_id){
    $append_action_id="and $table.action_id=".$action_id;
}
if($get_user){
    $append_get_user="and $table.user_id=".$get_user."";
    $get_user_name=$acttObj->read_specific("CONCAT(name,' (',prv,')') as selected_user","login","id=".$get_user)['selected_user'];
}
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 100;
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
.pagination>.active>a{background:#337ab7;}
.table thead tr {background: none;}</style>
</head>
<?php include 'header.php';?>
<body>
<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12">
		<header>
		<div class="tab_container col-md-12" id="put_data">
        <?php $query="select $table.id,login.name,user_actions.title,$table.details,$table.dated FROM $table,login,user_actions where $table.user_id=login.id AND $table.action_id=user_actions.id $append_get_date $append_get_user $append_action_id LIMIT {$startpoint} , {$limit}";
			  $result = $acttObj->read_all("$table.id,login.name,user_actions.title,$table.details,DATE($table.dated) as just_date,$table.dated","$table,login,user_actions","$table.user_id=login.id AND $table.action_id=user_actions.id $append_get_date $append_get_user $append_action_id LIMIT {$startpoint} , {$limit}"); ?>
        <center><div class="col-md-12"><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
            <h3><span class="label label-primary"><?php echo $get_user_name;?></span></h3></center>
            <a onclick="popupwindow('daily_log.php?get_date_1=<?php echo $get_date_1;?>&get_date_2=<?php echo $get_date_2.'&get_user='.$get_user; ?>', 'View daily log', 1000, 1000);" href="javascript:void(0)" class="btn btn-primary" style="color:white;" title="Click to view all operations">View All Actions</a><br>
			<table style="margin-top:5px;" class="table table-bordered tbl_data" cellspacing="0" cellpadding="0">
			    <thead class="bg-info">
                <tr>
                    <td>S.No</td>
                    <td>Action</td>
                    <td>Details</td>
                    <td>Action Time</td>
   				</tr>
			</thead>
			<tbody>
        <?php if($result->num_rows==0){
                    echo '<tr>
            		  <td colspan="7"><h4 class="text-danger text-center">Sorry ! There are no records currently.</h4></td></tr>';
                }else{
                    $date_change='';
                    while ($row = $result->fetch_assoc()) {
                    if($date_change!=$row['just_date']){
                        echo "<tr class='bg-warning'><td colspan='4' align='center'><b>".$row['just_date']."</b></td></tr>";
                        $page_count=0;$counte=0;
                    }
                    $date_change=$row['just_date'];
                    $to_find = array("F2F", "TP", "TR");
                    $to_change = array("Face To Face","Telephone","Translation");
                    $page_count++;$counter++;?>
            		<tr>
            		  <td><?php echo '<span class="badge badge-info badge-counter">'.$page_count.'</span>';?></td>
            		  <td><?php echo "<b>".$row["name"]."</b> ".$row["title"]; ?></td>
                      <td><?php echo str_replace($to_find, $to_change, $row["details"]); ?></td>
                      <td><?php echo $row['dated']; ?></td>
					  </tr>
                    <?php } ?>
                </tbody>
          </table>
		  <?php } ?>
    </div>
	</section>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>