<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
if (empty($_SESSION['cust_UserName'])) {
    echo '<script>window.location="index.php";</script>';
}
include 'source/db.php';
include 'source/class.php';
$semi = "\"'\"";
$logged_id = $_SESSION['cust_userId'];
$logged_company_id = $acttObj->read_specific("id", "comp_reg", "abrv='" . $_SESSION['cust_UserName'] . "'")['id'];
if ($_SESSION['comp_nature']==1) {

    // $data1 = $acttObj->read_specific("DISTINCT GROUP_CONCAT(parent_companies.sup_child_comp) as data1", "parent_companies", "parent_companies.sup_parent_comp IN (" . $logged_company_id . ")");
    // $all_abrv = $acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv", "comp_reg", "id IN (" . $data1['data1'] . ")", "set SESSION group_concat_max_len=10000");
    // $all_cz = $acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz", "child_companies", "child_companies.parent_comp IN (" . $data1['data1'] . ")", "set SESSION group_concat_max_len=10000")['all_cz'];
    // $append_string = "orgName IN (" . $all_cz . ")";


    $all_cz = $acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,comp_reg.abrv,$semi) as all_cz", "comp_reg,subsidiaries", "comp_reg.id=subsidiaries.child_comp AND subsidiaries.parent_comp=$logged_company_id", "set SESSION group_concat_max_len=10000")['all_cz'];
    $append_string = "orgName IN (" . $all_cz . ")";    

} else if ($_SESSION['comp_type'] == 2) {
    $data1 = $acttObj->read_specific("GROUP_CONCAT(comp_reg.id) as data1", "comp_reg", "id IN (" . $logged_company_id . ")");
    $all_abrv = $acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv", "comp_reg", "id IN (" . $data1['data1'] . ")", "set SESSION group_concat_max_len=10000");
    $all_cz = $acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz", "child_companies", "child_companies.parent_comp IN (" . $data1['data1'] . ")", "set SESSION group_concat_max_len=10000")['all_cz'];
    $append_string = "orgName IN (" . $all_cz . ")";
} else {
    $all_cz = "'" . $_SESSION['cust_UserName'] . "'";
    $append_string = "orgName=" . $all_cz;
}
?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
    <?php include 'source/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css" />
    <style>
        .dataTables_wrapper .row {
            margin: 0px !important;
        }

        select.input-sm {
            line-height: 22px;
        }

        .glyphicon {
            color: #fff;
        }

        input,
        textarea,
        select {
            -webkit-appearance: button;
        }

        .glyphicon {
            color: #fff;
        }

        #breadcrumbs {
            line-height: 30px;
        }
    </style>
</head>

<body class="boxed">
    <div id="wrap">
        <?php include 'source/top_nav.php'; ?>
        <section id="page-title">
            <div class="container clearfix">
                <h1>Pending Approval Jobs List <a href="logout.php?r=comp" class="btn btn-warning" title="Click here to logout">LOG OUT</a></h1><br><br>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="customer_area.php">Home</a> &rsaquo;</li>
                    </ul>
                </nav>
            </div>
        </section>
        <div class="container-fluid">
            <div style="overflow-x:auto;">
                <?php $cr_dt = date('Y-m-d');
                $q_jobs = $acttObj->read_all("*", "(SELECT 'interpreter' as 'type',interpreter.id,interpreter.intrpName,interpreter.submited,interpreter.source,interpreter.target, interpreter.assignDate, interpreter.assignTime,comp_reg.name  FROM interpreter,comp_reg", "interpreter.orgName = comp_reg.abrv AND interpreter.approve_portal_mngt=0 AND interpreter.commit=0 and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.orderCancelatoin=0 and interpreter.intrp_salary_comit=0 and interpreter.is_temp=0 and interpreter.$append_string AND interpreter.assignDate>=CURRENT_DATE()
                UNION
                SELECT 'telephone' as 'type',telephone.id,telephone.intrpName,telephone.submited,telephone.source,telephone.target, telephone.assignDate, telephone.assignTime,comp_reg.name  FROM telephone,comp_reg 
                WHERE telephone.orgName = comp_reg.abrv AND telephone.approve_portal_mngt=0 and telephone.commit=0 and telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.orderCancelatoin=0 and telephone.intrp_salary_comit=0 and telephone.is_temp=0 and telephone.$append_string AND telephone.assignDate>=CURRENT_DATE()
                UNION
                SELECT 'translation' as 'type',translation.id,translation.intrpName,translation.submited,translation.source,translation.target, translation.asignDate as 'assignDate', '00:00:00' as 'assignTime',comp_reg.name  FROM translation,comp_reg 
                WHERE translation.orgName = comp_reg.abrv AND translation.approve_portal_mngt=0 and translation.commit=0 and translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.orderCancelatoin=0 and translation.intrp_salary_comit=0 and translation.is_temp=0 and translation.$append_string AND translation.asignDate>=CURRENT_DATE()) as grp  order by assignDate");
                if ($q_jobs->num_rows > 0) { ?>
                    <table class="table table-bordered table-hover">
                        <thead class="bg-primary">
                            <tr>
                                <th>Source Language</th>
                                <th>Target Language</th>
                                <?php if ($_SESSION['comp_type'] != 3) { ?><th>Company Name</th><?php } ?>
                                <th>Assignment Date</th>
                                <th>Job Type</th>
                                <th>Booked By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $q_jobs->fetch_assoc()) {
                                if ($row['type'] == "interpreter") {
                                    $page = "order_f2f_multi_dup.php";
                                } else if ($row['type'] == "telephone") {
                                    $page = "order_tp_multi_dup.php";
                                } else {
                                    $page = "order_tr_multi_dup.php";
                                } ?>
                                <tr>
                                    <td><?php echo $row['source']; ?></td>
                                    <td><?php echo $row['target']; ?></td>
                                    <?php if ($_SESSION['comp_type'] != 3) { ?><td>
                                            <h5 <?php if (strlen($row['name']) > 30) { ?> class="h6" <?php } ?>><?php echo $row['name']; ?></h5>
                                        </td><?php } ?>
                                    <td><?php echo $misc->dated($row['assignDate']) . " " . substr($row['assignTime'], 0, 5); ?></td>
                                    <td>
                                        <h4><?php if ($row['type'] == 'interpreter') {
                                                echo '<span class="label label-success"><i class="glyphicon glyphicon-user"></i> Face To Face';
                                            } else if ($row['type'] == 'telephone') {
                                                echo '<span class="label label-info"><i class="glyphicon glyphicon-earphone"></i> Telephone';
                                            } else {
                                                echo '<span class="label label-warning"><i class="glyphicon glyphicon-globe"></i> Translation';
                                            } ?></span></h4>
                                    </td>
                                    <td><?php echo $row['submited']; ?></td>
                                    <td>
                                        <a onclick="view_order_details(<?php echo $row['id'] . ",'" . $row['type'] . "'"; ?>)" class="btn btn-primary btn-sm" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to view order details"><i class="glyphicon glyphicon-eye-open"></i></a>
                                        <!-- <a href="<?php echo $page . '?id=' . base64_encode($row['id']); ?>" class="btn btn-info btn-sm" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Create duplicate for this job"><i class="glyphicon glyphicon-duplicate"></i></a> -->
                                        <a class="btn btn-sm" onclick="order_action(<?php echo $row['id'] . ",'" . $row['type'] . "','apxn'"; ?>)" id="approve_btn" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to approve." ><i class="fa-2x fa fa-check-circle text-warning" data-toggle="modal" data-target="#approveModal"></i></a>
                                        <a class="btn btn-sm" onclick="order_action(<?php echo $row['id'] . ",'" . $row['type'] . "','dxn'"; ?>)" id="delete_btn" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to Delete"><i class="fa-2x fa fa-trash text-danger" data-toggle="modal" data-target="#deleteModal"></i></a>
                                        <!--<a class="btn btn-warning btn-sm" onclick="popupwindow('lsuk_system/reports_lsuk/pdf/timesheet.php?update_id=<?php echo $row['id']; ?>&table=<?php echo $row['type']; ?>&emailto=<?php echo $_SESSION['email']; ?>', 'title', 1000, 1000);" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Email Timesheet"><i class="glyphicon glyphicon-envelope"></i> Email</a>-->
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <div class="alert alert-warning text-center h3 col-sm-6 col-sm-offset-3">Sorry ! There are no jobs pending for approval yet.
                                <br><br><a class="btn btn-info" href="customer_area.php"><i class="glyphicon glyphicon-home"></i> Go to Dashboard Page</a>
                            </div>
                        <?php } ?>
                        </tbody>
                    </table>
            </div>
        </div>
        <?php include 'source/footer.php'; ?>
    </div>
    <!-- Modal to display record -->
    <div class="modal modal-info fade col-md-8 col-md-offset-2" data-toggle="modal" data-target=".bs-example-modal-lg" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div class="modal-dialog" role="document" style="width:auto;">
            <div class="modal-content">
                <div class="modal-header bg-default bg-light-ltr">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">View Details</h4>
                </div>
                <div class="modal-body" id="view_modal_data" style="overflow-x:auto;">

                </div>
                <div class="modal-footer bg-default">
                    <button type="button" class="btn  btn-primary pull-right" data-dismiss="modal"> Close</button>
                </div>
            </div>
        </div>
    </div>
    <!--End of modal-->

<!-- Job Approval Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approveModalLabel">Approval Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to <b>APPROVE</b> this job?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success act_btn" id="approve_job" data-id="" data-dismiss="modal">Approve</button>
      </div>
    </div>
  </div>
</div>

<!-- Job Approval Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Deletion Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to <b>DELETE</b> this job?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger act_btn" id="del_job" data-id="" data-dismiss="modal">Delete</button>
      </div>
    </div>
  </div>
</div>

    <script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                "bSort": false,
                drawCallback: function() {
                    $('[data-toggle="popover"]').popover({
                        html: true
                    });
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        });

        function view_order_details(order_id, order_type) {
            $.ajax({
                url: 'ajax_client_portal.php',
                method: 'post',
                data: {
                    order_id: order_id,
                    order_type: order_type,
                    view_order_details: '1'
                },
                success: function(data) {
                    $('#view_modal_data').html(data);
                    $('#view_modal').modal("show");
                },
                error: function(xhr) {
                    alert("An error occured: " + xhr.status + " " + xhr.statusText);
                }
            });
        }

        
        function order_action(order_id, order_type, act) {
            if(act=="dxn"){
                $("#del_job").attr("data-id",order_id+","+order_type);
            }else if(act=="apxn"){
                $("#approve_job").attr("data-id",order_id+","+order_type);
            }
        }
        $(document).on('click','.act_btn',function(){
            var action = "";
            if(this.id=="del_job"){
                action="delete";
            }else if(this.id=="approve_job"){
                action="approve";
            }
            if(action!=""){
                var order_id = $(this).attr("data-id").split(",")[0].trim();
                var order_type = $(this).attr("data-id").split(",")[1].trim();
                // console.log(order_id+ "\n"+order_type);
                $.ajax({
                    url: 'ajax_client_portal.php',
                    method: 'post',
                    data: {
                        order_id: order_id,
                        order_type: order_type,
                        job_action: action
                    },
                    success: function(data) {
                        console.log(data);
                        if(data=="updated"){
                            alert(action+" Succesfully");
                            window.location.reload();
                        }else{
                            alert("Failed to "+action+". Try reloading the page and try again.")
                        }
                    },
                    error: function(xhr) {
                        alert("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });
            }
        });
    </script>
</body>

</html>