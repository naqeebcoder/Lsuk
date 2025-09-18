<?php if(session_id() == '' || !isset($_SESSION)){session_start();}

if(empty($_SESSION['cust_UserName'])){

    echo '<script>window.location="index.php";</script>';

}

if(isset($_SESSION['operator'])){

    echo '<script>window.location="customer_area.php";</script>';

}

include 'source/db.php';

include 'source/class.php';

$semi="\"'\"";

$logged_id=$_SESSION['cust_userId'];

$logged_company_id=$acttObj->read_specific("id","comp_reg","abrv='".$_SESSION['cust_UserName']."'")['id'];
if($_SESSION['role']=='admin'){
    $result = $acttObj->read_all("company_login.*","company_login","1");

}else{

    if($_SESSION['comp_nature']==1){

    $result = $acttObj->read_all("  company_login.*", "company_login,subsidiaries", " company_login.company_id=subsidiaries.child_comp AND subsidiaries.parent_comp=$logged_company_id");

    // $all_companies=$acttObj->read_specific("DISTINCT GROUP_CONCAT(parent_companies.sup_child_comp) as all_companies","parent_companies","parent_companies.sup_parent_comp IN (".$logged_company_id.")")['all_companies'];

    // $result = $acttObj->read_all("company_login.*","company_login,child_companies","company_login.company_id=child_companies.child_comp AND child_companies.parent_comp IN (".$all_companies.")");

    }else if($_SESSION['comp_type']==2){

        $result = $acttObj->read_all("company_login.*","company_login,child_companies","company_login.company_id=child_companies.child_comp AND child_companies.parent_comp=".$_SESSION['company_id']);

    }else{

        $result = $acttObj->read_all("company_login.*","company_login","company_login.id=".$logged_id);

    }

}

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);

$limit = 20;

$startpoint = ($page * $limit) - $limit;    ?>

<!DOCTYPE HTML>

<html class="no-js">

<head>

<?php include'source/header.php'; ?>

<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css" />

<style>

.dataTables_wrapper .row{margin:0px !important;}

select.input-sm {line-height: 22px;}

.glyphicon{color:#fff;}

input, textarea, select {-webkit-appearance: button;}

.glyphicon{color:#fff;}

#breadcrumbs {line-height: 30px;}

</style>

</head>



<body class="boxed">

<!-- begin container -->

<div id="wrap">

    <!-- begin header -->

<?php include'source/top_nav.php'; ?>

<section id="page-title">

        <div class="container clearfix">

        <h1>Registered Companies List<span style="color:#F00; font-weight:bold"></h1><br><br>

          <nav id="breadcrumbs">

               <ul>

                    <li><a href="customer_area.php">Home</a> &rsaquo;</li>

                </ul>

          </nav>

        </div>

    </section>



    <section id="content" class="container-fluid clearfix">

            <div class="col-md-10 col-md-offset-1">

            <?php if($_SESSION['role']=='admin'){ ?>

              <a class="btn btn-primary" href="javascript:void(0)" onclick="popupwindow('manage_company_login.php?id=<?php echo $logged_id;?>&action=add', 'title', 1100, 570);">Add New Company</a><br><br>

            <?php } ?>

                <table class="table table-bordered table-hover">

                    <thead class="bg-primary">

                        <tr>

                            <th width="40%">Organization</th>

                            <th>Login Email</th>

                            <th>Action</th>

                            <!--<?php if($_SESSION['comp_type']!=3){ ?><th>Company Name</th><?php } ?>-->

                        </tr>

                    </thead>

                      <tbody>

                    <?php while($row = $result->fetch_assoc()){ ?>                       

                        <tr>

                            <td><?php echo $acttObj->read_specific("name","comp_reg","abrv='".$row['orgName']."'")['name'].'<br><b>'.$row['orgName']."</b>";

                            if($row['deleted_flag']==1){echo ' <span class="label label-danger">Trashed</span>';} ?></td>

                            <td><?php echo $row['id']."-"; ?><?php echo substr($row['email'], 0,8)."... ";

                            $count_operators=$acttObj->read_specific("count(*) as count_operators","company_operators","company_id=".$row['id']);

                            echo $count_operators['count_operators']>0?"<br><b>".$count_operators['count_operators']."</b> operators":''; ?></td>

                            <td>

                            <a class="btn btn-warning btn-sm" href="javascript:void(0)" onclick="popupwindow('manage_company_login.php?id=<?php echo $row['id']; ?>&action=edit', 'title', 1100, 570);">Edit</a>

                            <?php if($row['deleted_flag']==0){ ?>

                                <a class="btn btn-danger btn-sm" href="javascript:void(0)" onclick="popupwindow('manage_company_login.php?id=<?php echo $row['id']; ?>&action=delete', 'title', 1100, 570);">Delete</a>

                            <?php }else{ ?>

                                <a class="btn btn-success btn-sm" href="javascript:void(0)" onclick="popupwindow('manage_company_login.php?id=<?php echo $row['id']; ?>&action=undo', 'title', 1100, 570);">Restore</a>

                            <?php } ?>

                            <a class="btn btn-info btn-sm" href="javascript:void(0)" onclick="popupwindow('manage_company_operators.php?id=<?php echo $row['id']; ?>&action=manage', 'title', 1100, 570);">Manage Operators</a>

                            </td>

                        </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

   <hr>

        

        <!-- begin clients -->

       <?php include'source/our_client.php'; ?>

        <!-- end clients -->   

    </section>

    <!-- end content -->  

    

    <!-- begin footer -->

    <?php include'source/footer.php'; ?>

    <!-- end footer -->  

</div>

<!-- end container -->

</body>

<script src="lsuk_system/js/jquery-1.11.3.min.js"></script>

<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script>

$(document).ready(function() {

    $('.table').DataTable();

});

</script>

</html>