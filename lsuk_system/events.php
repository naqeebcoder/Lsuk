<?php

if (session_id() == '' || !isset($_SESSION)) { session_start();}

if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {
    die("Event Not Found");
}
  $event_id = $_GET['event_id'];

include 'db.php';
include 'class.php';
$table="cpd_events";
$array_types=array("0"=>"No Response","1"=>"Attending","1"=>"Not Attending");
?>
<!doctype html>
<html lang="en">
<head>
<title>CPD Events</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css" />
<style>html,body{background:none !important;}
.badge-counter{border-radius: 0px!important;margin: -9px -9px!important;font-size: 10px;float: left;}
.pagination>.active>a{
    background:#337ab7;
}</style>
</head>
<?php include 'header.php';?>
<body>
<?php include 'nav2.php';?>
<style>.tablesorter thead tr {background: none;}</style>
<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12">
		<header>
		  <div class="form-group col-sm-12 mt15"><h2 class="text-center"><a href="events_list.php"><span class="label label-primary">Events</a></span></h2></div>
		<div class="tab_container" id="put_data">
		<?php $query="select $table.*,interpreter_reg.name,interpreter_reg.email,interpreter_reg.contactNo,interpreter_reg.contactNo2 FROM $table,interpreter_reg where $table.interpreter_id=interpreter_reg.id";
			  $result = $acttObj->read_all("$table.*,interpreter_reg.name,interpreter_reg.email,interpreter_reg.contactNo,interpreter_reg.contactNo2","$table,interpreter_reg","$table.interpreter_id=interpreter_reg.id and event_id=$event_id"); ?>
			<table class="tablesorter table table-bordered tbl_data" cellspacing="0" cellpadding="0">
			    <thead class="bg-info">
                <tr>
                    <td>S.No</td>
                    <td>Interpreter</td>
                    <td>Status</td>
                    <td>Remarks</td>
                    <td>Reminder Date</td>
                    <td>Replied Date</td>
   				</tr>
			</thead>
			<tbody>
        <?php if($result->num_rows==0){
                    echo '<tr>
            		  <td colspan="7"><h4 class="text-danger text-center">Sorry ! There are no records.</h4></td></tr>';
                }else{
                    while ($row = $result->fetch_assoc()) {
                    $reply=$array_types[$row['reply']];
                        $page_count++;$counter++;?>
            		<tr>
            		  <td><?php echo '<span class="w3-badge w3-blue badge-counter">'.$counter.'</span>';?></td>
            		  <td><?php echo $row["name"]."<br>".$row['email']." (".$row['contactNo'].")"; ?></td>
                      <td><?php echo $row['attend_type']=='0'?"<span class='label label-primary lbl pull-right'>In-Person</span>":"<span class='label label-info lbl pull-right'>Remotely</span>";
                      if($row['reply']=='0'){
                        echo "<br><span class='label label-warning lbl pull-right'>No Response</span>";
                        }else if($row['reply']=='1'){
                          echo "<br><span class='label label-success lbl pull-right'>Attending</span>";
                        }else{
                            echo "<br><span class='label label-danger lbl pull-right'>Not Attending</span>";
                        } ?></td>
                      <td><?php echo $row['remarks']; ?></td>
                      <td><?php echo $row["created_date"]; ?></td>
                      <td><?php echo $row["updated_date"]; ?></td>
            		</tr>
                <?php }?>
                </tbody></table>
		  </div>
		  <?php } ?>
	</section>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('.table').DataTable();
});
</script>
</body>
</html>