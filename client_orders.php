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
$get_company_data = $acttObj->read_specific("id,po_req", "comp_reg", "abrv='" . $_SESSION['cust_UserName'] . "'");
$logged_company_id = $get_company_data['id'];
if ($_SESSION['comp_nature']==1) {

    // $data1 = $acttObj->read_specific("DISTINCT GROUP_CONCAT(parent_companies.sup_child_comp) as data1", "parent_companies", "parent_companies.sup_parent_comp IN (" . $logged_company_id . ")");
    // $all_abrv = $acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv", "comp_reg", "id IN (" . $data1['data1'] . ")", "set SESSION group_concat_max_len=10000");
    // $all_cz = $acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz", "child_companies", "child_companies.parent_comp IN (" . $data1['data1'] . ")", "set SESSION group_concat_max_len=10000")['all_cz'];
    // $append_string = "orgName IN (" . $all_cz . ")";

    if(isset($_SESSION['operator'])){
        $all_cz = "'" . $_SESSION['cust_UserName'] . "'";
        $append_string = "orgName=" . $all_cz;
    }else{
        $all_cz = $acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,comp_reg.abrv,$semi) as all_cz", "comp_reg,subsidiaries", "comp_reg.id=subsidiaries.child_comp AND subsidiaries.parent_comp=$logged_company_id", "set SESSION group_concat_max_len=10000")['all_cz'];
        $append_string = "orgName IN (" . $all_cz . ")";
    }
} else if ($_SESSION['comp_type'] == 2) {
    if(isset($_SESSION['operator'])){
        $all_cz = "'" . $_SESSION['cust_UserName'] . "'";
        $append_string = "orgName=" . $all_cz;
    }else{
        $data1 = $acttObj->read_specific("GROUP_CONCAT(comp_reg.id) as data1", "comp_reg", "id IN (" . $logged_company_id . ")");
        $all_abrv = $acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv", "comp_reg", "id IN (" . $data1['data1'] . ")", "set SESSION group_concat_max_len=10000");
        $all_cz = $acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz", "child_companies", "child_companies.parent_comp IN (" . $data1['data1'] . ")", "set SESSION group_concat_max_len=10000")['all_cz'];
        $append_string = "orgName IN (" . $all_cz . ")";    
    }
} else {
    $all_cz = "'" . $_SESSION['cust_UserName'] . "'";
    $append_string = "orgName=" . $all_cz;
}
if (!$_GET['order_type'] || $_GET['order_type'] == 1) {
    $page_name = "Active Orders";
} else {
    $page_name = $_GET['order_type'] == 2 ? "Paid Invoices" : "Pending Invoices";
}
$edit_pages_array = array('interpreter' => 'edit_f2f.php', 'telephone' => 'edit_telephone.php', 'translation' => 'edit_translation.php');
$type_array = array('interpreter' => 1, 'telephone' => 2, 'translation' => 3);
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
                <h1><?=$page_name?> List <a href="logout.php?r=comp" class="btn btn-warning" title="Click here to logout">LOG OUT</a></h1><br><br>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="customer_area.php">Home</a> &rsaquo;</li>
                    </ul>
                </nav>
            </div>
        </section>
        <div class="container-fluid">
            <div style="overflow-x:auto;">
                <span class="col-sm-12">
                    <?php if ($_SESSION['returned_message']) {
                    echo $_SESSION['returned_message'];
                    unset($_SESSION['returned_message']);
                    } ?>
                </span>
                <div class="col-md-12">
                    <div class="form-group col-sm-3">
                        <label>Filter By Order/Invoice Status</label>
                        <select id="order_type" class="form-control">
                            <option <?=!$_GET['order_type'] ? 'selected' : ''?> value="">--- Not Selected ---</option>
                            <option <?=$_GET['order_type'] == 1 ? 'selected' : ''?> value="1">Active Orders</option>
                            <option <?=$_GET['order_type'] == 2 ? 'selected' : ''?> value="2">Paid Invoices</option>
                            <option <?=$_GET['order_type'] == 3 ? 'selected' : ''?> value="3">Pending Invoices</option>
						</select>
					</div>
                    <?php if ($get_company_data['po_req'] == 1) { ?>
                        <div class="form-group col-sm-3">
                            <label>Filter By Purchase Order Status</label>
                            <select id="po_status" class="form-control">
                                <option <?=!$_GET['po_status'] ? 'selected' : ''?> value="">--- Not Selected ---</option>
                                <option <?=$_GET['po_status'] == 1 ? 'selected' : ''?> value="1">Waiting for Purchase Order</option>
                                <option <?=$_GET['po_status'] == 2 ? 'selected' : ''?> value="2">Purchase Order Added</option>
                            </select>
                        </div>
                    <?php } ?>
                    <div class="form-group col-sm-2">
                        <label>Filter By Cancellation</label>
                        <select id="cancellation_status" class="form-control">
                            <option <?=!$_GET['cancellation_status'] ? 'selected' : ''?> value="">--- Not Selected ---</option>
                            <option <?=$_GET['cancellation_status'] == 1 ? 'selected' : ''?> value="1">Chargeable Cancelled</option>
                            <option <?=$_GET['cancellation_status'] == 2 ? 'selected' : ''?> value="2">Non-Chargeable Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label>Filter Assignment Date</label>
                        <input title="Select an assignment date" type="date" name="assignDate" id="assignDate" class="form-control" value="<?=$_GET['assignDate']?>" />
					</div>
                    <div class="form-group col-md-2">
                        <br>
                        <a style="margin-top:4px;" class="btn btn-primary" href="javascript:void(0)" title="Click to filter list" onclick="myFunction()">Search Results</a>
                        <a style="margin-top:4px;" class="btn btn-warning" href="<?php echo basename(__FILE__); ?>" title="Click to reset filters">Clear</a>
                    </div>
                </div>
                <?php 
                $order_type_f2f_query = " and interpreter.commit=0 and interpreter.intrp_salary_comit=0 and interpreter.is_temp=0 AND interpreter.assignDate>=CURRENT_DATE()";
                $order_type_tp_query = " and telephone.commit=0 and telephone.intrp_salary_comit=0 and telephone.is_temp=0 AND telephone.assignDate>=CURRENT_DATE()";
                $order_type_tr_query = " and translation.commit=0 and translation.intrp_salary_comit=0 and translation.is_temp=0 AND translation.asignDate>=CURRENT_DATE()";
                if ($_GET['order_type']) {
                    if ($_GET['order_type'] == 1) { // Active Orders
                        $order_type_f2f_query = $order_type_f2f_query;
                        $order_type_tp_query = $order_type_tp_query;
                        $order_type_tr_query = $order_type_tr_query;
                    } elseif ($_GET['order_type'] == 2) { // Paid Orders
                        $order_type_f2f_query = " and interpreter.assignDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) and interpreter.commit=1 and (round(interpreter.rAmount,2) >= round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) and interpreter.rAmount>0)";
                        $order_type_tp_query = " and telephone.assignDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) and telephone.commit=1 and (round(telephone.rAmount,2) >= round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.rAmount>0)";
                        $order_type_tr_query = " and translation.asignDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) and translation.commit=1 and (round(translation.rAmount,2) >= round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) and translation.rAmount>0)";
                    } else { // Un-paid/Pending Orders
                        $order_type_f2f_query = " and interpreter.assignDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) and interpreter.commit=1 and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0)";
                        $order_type_tp_query = " and telephone.assignDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) and telephone.commit=1 and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0)";
                        $order_type_tr_query = " and translation.asignDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) and translation.commit=1 and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0)";
                    }
                }
                if ($get_company_data['po_req'] == 1) {
                    if ($_GET['po_status']) {
                        if ($_GET['po_status'] == 1) {
                            $po_string_f2f = " and (interpreter.porder='' OR interpreter.porder='Nil')";
                            $po_string_tp = " and (telephone.porder='' OR telephone.porder='Nil')";
                            $po_string_tr = " and (translation.porder='' OR translation.porder='Nil')";
                        } else {
                            $po_string_f2f = " and interpreter.porder!=''";
                            $po_string_tp = " and telephone.porder!=''";
                            $po_string_tr = " and translation.porder!=''";
                        }
                    }
                }
                if ($_GET['cancellation_status']) {
                    if ($_GET['cancellation_status'] == 1) {
                        $cancel_string_f2f = " and interpreter.orderCancelatoin=1 and interpreter.order_cancel_flag=0";
                        $cancel_string_tp = " and telephone.orderCancelatoin=1 and telephone.order_cancel_flag=0";
                        $cancel_string_tr = " and translation.orderCancelatoin=1 and translation.order_cancel_flag=0";
                    } else {
                        $cancel_string_f2f = " and interpreter.orderCancelatoin=0 and interpreter.order_cancel_flag=1";
                        $cancel_string_tp = " and telephone.orderCancelatoin=0 and telephone.order_cancel_flag=1";
                        $cancel_string_tr = " and translation.orderCancelatoin=0 and translation.order_cancel_flag=1";
                    }
                } else {
                    $cancel_string_f2f = "";
                    $cancel_string_tp = "";
                    $cancel_string_tr = "";
                }
                if ($_GET['assignDate']) {
                    $assignDate_f2f = " and interpreter.assignDate='" . $_GET['assignDate'] . "'";
                    $assignDate_tp = " and telephone.assignDate='" . $_GET['assignDate'] . "'";
                    $assignDate_tr = " and translation.asignDate='" . $_GET['assignDate'] . "'";
                }
                $q_jobs = $acttObj->read_all("*", "(SELECT 'interpreter' as 'type',interpreter.orderCancelatoin,interpreter.order_cancel_flag,interpreter.porder,interpreter.id,interpreter.intrpName,interpreter.source,interpreter.target, interpreter.assignDate, interpreter.assignTime,comp_reg.name,'' as comunic  FROM interpreter,comp_reg", "interpreter.orgName = comp_reg.abrv and interpreter.deleted_flag=0 and interpreter.$append_string $po_string_f2f $cancel_string_f2f $order_type_f2f_query $assignDate_f2f
                UNION
                SELECT 'telephone' as 'type',telephone.orderCancelatoin,telephone.order_cancel_flag,telephone.porder,telephone.id,telephone.intrpName,telephone.source,telephone.target, telephone.assignDate, telephone.assignTime,comp_reg.name,comunic  FROM telephone,comp_reg 
                WHERE telephone.orgName = comp_reg.abrv and telephone.deleted_flag=0 and telephone.$append_string $po_string_tp $cancel_string_tp $order_type_tp_query $assignDate_tp
                UNION
                SELECT 'translation' as 'type',translation.orderCancelatoin,translation.order_cancel_flag,translation.porder,translation.id,translation.intrpName,translation.source,translation.target, translation.asignDate as 'assignDate', '00:00:00' as 'assignTime',comp_reg.name,'' as comunic  FROM translation,comp_reg 
                WHERE translation.orgName = comp_reg.abrv and translation.deleted_flag=0 and translation.$append_string $po_string_tr $cancel_string_tr $order_type_tr_query $assignDate_tr) as grp order by assignDate DESC");
                if ($q_jobs->num_rows > 0) { ?>
                    <table class="table table-bordered table-hover">
                        <thead class="bg-primary">
                            <tr>
                                <th>Source Language</th>
                                <th>Target Language</th>
                                <?php if ($_SESSION['comp_type'] != 3) { ?><th>Company Name</th><?php } ?>
                                <th>Assignment Date</th>
                                <th>Job Type</th>
                                <th>Invoice Status</th>
                                <th>Booking Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $q_jobs->fetch_assoc()) {
                                if ($row['type'] == "interpreter") {
                                    $inv_page = 'invoice.php';
                                    $page = "order_f2f_multi_dup.php";
                                } else if ($row['type'] == "telephone") {
                                    $inv_page = 'telep_invoice.php';
                                    $page = "order_tp_multi_dup.php";
                                    $get_channel = $acttObj->read_specific("c_title,c_image", "comunic_types", "c_id=" . $row['comunic']);
                                    $communication_type = empty($row['comunic']) || $row['comunic'] == 11 ? " Telephone" : " " . $get_channel['c_title'];
                                    $channel_img = file_exists('lsuk_system/images/comunic_types/' . $get_channel['c_image']) ? '<img src="lsuk_system/images/comunic_types/' . $get_channel['c_image'] . '" width="36" style="display: inline-block;"/>' : '';
                                } else {
                                    $inv_page = 'trans_invoice.php';
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
                                        <?php if ($row['type'] == 'interpreter') {
                                                echo '<h5><span class="label label-success"><i class="glyphicon glyphicon-user"></i> Face To Face</span></h5>';
                                            } else if ($row['type'] == 'telephone') {
                                                echo $channel_img . " <h5 style='display:inline-block'>" . $communication_type . "</h5>";
                                            } else {
                                                echo '<h4><span class="label label-warning"><i class="glyphicon glyphicon-globe"></i> Translation</span></h4>';
                                            } ?>
                                    </td>
                                    <td><?php 
                                        if ($row['orderCancelatoin'] == 1) {
                                            echo "<span class='label label-danger'>Chargeable Cancelled</span><br>";
                                        } else {
                                            echo $row['order_cancel_flag'] == 1 ? "<span class='label label-warning'>Non-Chargeable Cancelled</span><br>" : "";
                                        }
                                        echo $_GET['order_type'] == 2 ? "<span class='label label-success'><i class='fa fa-check-circle'></i> Invoice Paid<span>" : "<span class='label label-warning'><i class='fa fa-exclamation-circle text-danger'></i> Un-paid Invoice<span>" ?></td>
                                    <td>
                                        <?php 
                                            if (!empty($row['intrpName'])) { ?>
                                                <?php echo $acttObj->read_specific('name', 'interpreter_reg', 'id=' . $row['intrpName'])['name']; 
                                            } else { ?>
                                               Interpreter not assigned yet for this assignment
                                            <?php }
                                        ?>
                                    </td>
                                    <td>
                                        <a data-order-type="<?=$row['type']?>" onclick="view_order_details(this, <?=$row['id'];?>)" class="btn btn-primary btn-sm" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to view order details"><i class="glyphicon glyphicon-eye-open"></i></a>
                                        <?php if ($row['order_cancel_flag'] == 0 && $row['orderCancelatoin'] == 0 && $row['assignDate'] >= date('Y-m-d')) { ?>
                                            <a data-id="<?=$row['id']?>" data-date="<?=$row['assignDate']?>" data-time="<?=$row['assignTime']?>" data-type="<?=$type_array[$row['type']]?>" class="btn btn-danger btn-sm" href="javascript:void(0)" onclick="amend_order(this)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to amend this job if required"><i class="glyphicon glyphicon-refresh"></i></a>
                                        <?php } ?>
                                        <a href="<?php echo $page . '?id=' . base64_encode($row['id']); ?>" class="btn btn-info btn-sm" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Create duplicate for this job"><i class="glyphicon glyphicon-duplicate"></i></a>
                                        <?php if ($row['orderCancelatoin'] == 0 && $row['order_cancel_flag'] == 0 && $row['assignDate'] >= date('Y-m-d')) { ?>
                                            <button data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to cancel this order if required" type="button" title="Cancel Order" href="javascript:void(0)" onClick="popupwindow('cancel_order.php?job_id=<?php echo base64_encode($row['id']); ?>&table=<?php echo strtolower($row['type']); ?>','Cancel order booking',900, 600)" class="btn btn-sm btn-danger"><i class="fa fa-remove"></i></button>
                                        <?php }
                                        /*
                                        if (!empty($row['intrpName'])) { ?>
                                            <a class="btn btn-sm" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?php echo $acttObj->read_specific('name', 'interpreter_reg', 'id=' . $row['intrpName'])['name']; ?> has been assigned for this assignment."><i class="fa-2x fa fa-user text-success"></i></a>
                                        <?php } else { ?>
                                            <a class="btn btn-sm" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Interpreter not assigned yet for this assignment!"><i class="fa-2x fa fa-exclamation text-warning"></i></a>
                                        <?php }
                                        */
                                        ?>
                                         <?php
                                            
                                            $invoice_id = $row['id'];
                                            
                                            $token = hash_hmac('sha256', $invoice_id, '78691');
                                            $_SESSION['invoice_token'][$invoice_id] = $token; 
                                        ?>
                                       
                                       <a href="javascript:void(0)" 
                                        onclick="popupwindow('<?= htmlspecialchars($inv_page) ?>?invoice_id=<?= urlencode($invoice_id) ?>&token=<?= urlencode($token) ?>', 'View Invoice', 1000, 1000);"
                                        style="display: inline-block; vertical-align: middle;" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Click to View Invoice">
                                        <img style="width:25px;" src="lsuk_system/images/inv.png" title="Invoice">
                                        </a>
                                         <!-- <a href="javascript:void(0)" onClick="popupwindow('<?php echo $inv_page; ?>?invoice_id=<?php echo $row['id']; ?>','View Invoice', 1000, 1000);"><input type="image" style="width: 25px;" src="lsuk_system/images/inv.png" title="Invoice"></a> -->
                                        <?php
                                        if ($get_company_data['po_req'] == 1) {
                                            if ($row['porder'] == "" || $row['porder'] == "Nil") {
                                                echo "<span class='label label-danger'>Missing Purchase Order</span>";
                                            } else {
                                                echo "<span class='label label-success'>Purchase Order Added</span>";
                                            }
                                        } ?>
                                        <!--<a class="btn btn-warning btn-sm" onclick="popupwindow('lsuk_system/reports_lsuk/pdf/timesheet.php?update_id=<?php echo $row['id']; ?>&table=<?php echo $row['type']; ?>&emailto=<?php echo $_SESSION['email']; ?>', 'title', 1000, 1000);" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Email Timesheet"><i class="glyphicon glyphicon-envelope"></i> Email</a>-->
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <div class="alert alert-warning text-center h3 col-sm-8 col-sm-offset-2">Sorry ! There are no <?=$page_name?> available in this list for selected filters. Thank you
                                <br><br><a class="btn btn-info" href="customer_area.php"><i class="glyphicon glyphicon-home"></i> Go to Dashboard Page</a>
                            </div>
                        <?php } ?>
                        </tbody>
                    </table>
            </div>
        </div>
        <?php include 'source/footer.php'; ?>
    </div>

    <!--Start Amend Modal-->
    <div class="modal" id="amend_modal">
        <div class="modal-dialog">
        <div class="modal-content">
            <form action="process/amend_order.php" method="post">
            <input type="hidden" name="amend_id" id="amend_id" required>
            <input type="hidden" name="amend_type" id="amend_type" required>
            <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
            <div class="modal-header alert-danger">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title"><b>Request to amend this order</b></h4>
            </div>
            <div class="modal-body amend_modal_attach">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="amend_date">Original Order Date</label>
                        <input type="date" disabled title="Select new assignment date" class="form-control" name="amend_date" id="amend_date" required/>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="amend_time">Original Order Time</label>
                        <input type="time" disabled title="Select new assignment time" class="form-control" name="amend_time" id="amend_time" required/>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="a_date">New Order Date</label>
                        <input type="date" title="Select new assignment date" class="form-control" name="a_date" id="a_date" />
                    </div>
                    <div class="form-group col-md-12">
                        <label for="a_time">New Order Time</label>
                        <input type="time" title="Select new assignment time" class="form-control" name="a_time" id="a_time" />
                    </div>
                    <div class="form-group col-md-12">
                        <label for="amend_reason">Write Reason of Amending this order</label>
                        <textarea rows="3" maxlength="250" placeholder="Write amendment reason ..." class="form-control" name="amend_reason" id="amend_reason" required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                <button onclick='return confirm("Are you sure to AMEND this order?")' type="submit" name="btn_amend_order" class="btn btn-danger">Amend Order</button>
            </div>
            </form>
        </div>
        </div>
    </div>
    <!--End Amend Modal-->

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
    <!-- <script>
    // Set current date and time on page load
    window.onload = function() {
        // Get current date
        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = ("0" + (currentDate.getMonth() + 1)).slice(-2); // Add leading zero
        var day = ("0" + currentDate.getDate()).slice(-2); // Add leading zero
        var formattedDate = year + "-" + month + "-" + day;
        
        // Set the current date to the "a_date" field
        document.getElementById('a_date').value = formattedDate;
        
        // Get current time (HH:MM)
        var hours = ("0" + currentDate.getHours()).slice(-2); // Add leading zero
        var minutes = ("0" + currentDate.getMinutes()).slice(-2); // Add leading zero
        var formattedTime = hours + ":" + minutes;
        
        // Set the current time to the "a_time" field
        document.getElementById('a_time').value = formattedTime;
    };
</script> -->

    <!--End of modal-->
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

        function view_order_details(element, order_id) {
            var order_type = $(element).attr("data-order-type");
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
        function myFunction(e) {
            var append_url = "<?php echo basename(__FILE__) . "?1"; ?>";
            var assignDate = $("#assignDate").val();
            if (assignDate) {
                append_url += '&assignDate=' + assignDate;
            }
            var order_type = $("#order_type").val();
            if (order_type) {
                append_url += '&order_type=' + order_type;
            }
            <?php if ($get_company_data['po_req'] == 1) { ?>
                var po_status = $("#po_status").val();
                if (po_status) {
                    append_url += '&po_status=' + po_status;
                }
            <?php } ?>
            var cancellation_status = $("#cancellation_status").val();
            if (cancellation_status) {
                append_url += '&cancellation_status=' + cancellation_status;
            }
            window.location.href = append_url;
        }

        function amend_order(element) {
            $("#amend_id").val($(element).attr("data-id"));
            $("#amend_type").val($(element).attr("data-type"));
            $("#amend_date").val($(element).attr("data-date"));
            $("#amend_time").val($(element).attr("data-time"));
            $('#amend_modal').modal('show');
        }
    </script>
</body>

</html>