<?php
ob_start();
?>
<?php 
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 

include 'db.php';
include 'class.php';
include_once ('function.php');
$table='com_reg';
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
<title>Pending Payment Reminders</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css" />    
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
    $p_title=$_GET['p_title'];
    $chk_exs = $acttObj->read_specific("id","pinv_reminders"," comp_id=$p_title ")['id'];
    if(empty($chk_exs)){
        $invEmail = $acttObj->read_specific("invEmail","comp_reg"," id=$p_title ")['invEmail'];
        $mk_sb = mysqli_query($con,"INSERT INTO pinv_reminders(comp_id,invEmail) VALUES('{$p_title}','{$invEmail}') ");
    }
    header('Location:pinv_reminders.php');
}

if(isset($_GET['opt']) && isset($_GET['id'])){
    $opt=$_GET['opt'];
    $edit_id=$_GET['id'];
    $table='pinv_reminders';
    // $chk_exs = $acttObj->read_specific("id","pinv_reminders"," comp_id=$p_title ")['id'];
    $ed_opt = $acttObj->editFun($table, $edit_id, 'frequency', $opt);
    header('Location:pinv_reminders.php');
}

if(isset($_GET['del_id'])){
    $del_id=$_GET['del_id'];
    $table='pinv_reminders';
    $dl_rec = $acttObj->delFun($table, $del_id);
    header('Location:pinv_reminders.php');
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
		    <div class="alert <?php echo $class; ?> col-md-2">
                <a href="<?php echo basename(__FILE__);?>" class="alert-link"><?php echo $page_title; ?> Pending Invoices Reminders</a>
              </div>
</div>
    <div class="col-md-12">
        <h4>Add Companies to reminder list from the dropdown below: </h4>
    </div>
<div class="row">
<form action="" method="GET">

            
         <div class="col-md-8">
             <div class="form-group col-md-3 col-sm-4">
        <select id="p_title" name="p_title"  class="form-control searchable">
    <?php 			
$sql_opt="SELECT id,name as title  FROM comp_reg WHERE deleted_flag=0 AND comp_nature IN (1,4) ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["title"];
    $id=$row_opt["id"];
    $name_opt=$row_opt["title"];
	// $title=urldecode($title);
    $options.='<OPTION value="'.$id.'" '.((isset($_GET['p_title']) && $_GET['p_title']==$id)?'selected':'').'>'.((!empty($name_opt))?$name_opt:'Empty');
}
?>
    
    <option value="">Select Parent Comapny</option>
    <?php echo $options; ?>
    </option>
  </select>
	        </div>
            
            <div class="form-group col-md-3 col-sm-4">
            <button type="submit" class="btn btn-primary" value="submit" name="submit">Submit</button>
            </div>
            </form>
		</header>
		
		             
	<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
	</div>
	</section>
    <section>
        <table class="table table-bordered" cellspacing="0" cellpadding="0" id="pinv_rem">
            <thead class="bg-primary">
                <tr>
                    <th>#</th>
                    <th>Company Name</th>
                    <th>Reminder Frequency</th>
                    <th>Invoicing Email</th>
                    <th>Total Reminders Sent</th>
                    <th>Last Reminder Sent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $chk_exs_pinv = $acttObj->read_all("pinv_reminders.id,pinv_reminders.comp_id,pinv_reminders.frequency,pinv_reminders.invEmail,pinv_reminders.total_reminders_sent,pinv_reminders.last_reminder_sent,comp_reg.name","pinv_reminders,comp_reg"," pinv_reminders.comp_id = comp_reg.id");
                    $r_pinv = mysqli_num_rows($chk_exs_pinv);
                    if($r_pinv>0){
                        $count = 1;
                        while($row = mysqli_fetch_assoc($chk_exs_pinv)){
                            ?>
                            <tr class='text-center'>
                                <td><?php echo $count; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><select id="remfrq_<?php echo $row['id']; ?>" name="rem_frq"  class="form-control rem_frq" style="height:auto;">
                                    <option id='1_<?php echo $row['id']; ?>'  value="1" <?php if($row['frequency']==1){ echo 'selected'; } ?>>Weekly</option>
                                    <option id='2_<?php echo $row['id']; ?>' value="2" <?php if($row['frequency']==2){ echo 'selected'; } ?>>Monthly</option>
                                </select></td>
                                <td><?php echo $row['invEmail']; ?></td>
                                <td><?php echo $row['total_reminders_sent']; ?></td>
                                <td><?php echo $row['last_reminder_sent']; ?></td>
                                <td><button class='btn btn-primary pinvDel' id='pinvDel_<?php echo $row['id']; ?>'> Delete</button></td>
                            </tr>
                            <?php
                        }
                    }
                ?>
            </tbody>
        </table>
    </section>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
    $(function() {
	    $('.searchable').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
    });
    $("#pinv_rem").DataTable();
    $(document).on('change','.rem_frq',function(){
        var get_id = $(this).attr('id');
        var opt = $('#'+get_id+' option:selected').attr('id').split('_');
        console.log(opt);
        // var opts = "pinv_reminders.php" + '?opt=' + encodeURIComponent(opt[0])+'&id='+ encodeURIComponent(opt[1]);
        // console.log(opts);
        window.location.href="pinv_reminders.php" + '?opt=' + encodeURIComponent(opt[0])+'&id='+ encodeURIComponent(opt[1]);
    });
    $(document).on('click','.pinvDel',function(){
        var del_opt = $(this).attr('id').split('_');
        console.log(del_opt);
        window.location.href="pinv_reminders.php" + '?del_id=' + encodeURIComponent(del_opt[1]);
        // window.location.href="fix_sup.php" + '?title=' + encodeURIComponent(x) ;
    });
    // $(document).on('change','#p_title',function(e){
    //   e.preventDefault();
    //   var p_id = this.value;
    //   window.location.href="pinv_reminders.php" + '?p_title=' + encodeURIComponent(p_id) ;
    // });
</script>
</body>
</html>