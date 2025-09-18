<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=155 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_requested_po = $_SESSION['is_root'] == 1 || in_array(171, $get_actions);
$action_purchase_order = $_SESSION['is_root'] == 1 || in_array(272, $get_actions);
$order_type = @$_GET['order_type'];
if (isset($order_type)) {
    $append_order_type = "and order_type='" . $order_type . "'";
}
$table = "po_requested";
?>
<!doctype html>
<html lang="en">

<head>
    <title>Purchase Order Requested</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css" />
    <style>
        html,
        body {
            background: none !important;
        }

        .badge-counter {
            border-radius: 0px !important;
            margin: -9px -9px !important;
            font-size: 10px;
            float: left;
        }

        .pagination>.active>a {
            background: #337ab7;
        }
    </style>
    <script>
        function myFunction() {
            var order_type = document.getElementById("order_type").value;
            if (!order_type) {
                order_type = "<?php echo $order_type; ?>";
            }
            window.location.href = "<?php echo basename(__FILE__); ?>" + '?order_type=' + order_type;
        }
    </script>
</head>
<?php include 'header.php'; ?>

<body>
    <?php include 'nav2.php'; ?>
    <style>
        .tablesorter thead tr {
            background: none;
        }
    </style>
    <section class="container-fluid" style="overflow-x:auto">
        <div class="col-md-12">
            <header>
                <div class="form-group col-md-2 col-sm-4 mt15" style="margin-top: 15px;">
                    <select id="order_type" onChange="myFunction()" name="order_type" class="form-control">
                        <?php
                        $array_types = array("f2f" => "Face To Face", "tp" => "Telephone", "tr" => "Translation");
                        if (!empty($order_type)) { ?>
                            <option value="<?php echo $order_type; ?>" selected><?php echo $array_types[$order_type]; ?></option>
                        <?php } ?>
                        <option value="" disabled <?php if (empty($order_type)) {
                                                        echo 'selected';
                                                    } ?>>Filter Job Type</option>
                        <option value="f2f">Face To Face</option>
                        <option value="tp">Telephone</option>
                        <option value="tr">Translation</option>
                    </select>
                </div>
                <div class="form-group col-md-6 col-md-offset-0 col-sm-4 mt15">
                    <h2 class="text-center"><a href="<?php echo basename(__FILE__); ?>"><span class="label label-primary">Purchase Order Requested</a></span></h2>
                </div>
                <div class="tab_container" id="put_data">
                    <?php $query = "select $table.*,count(*) as counter FROM $table where 1 $append_order_type GROUP by $table.order_id,$table.order_type ORDER BY $table.id ASC";
                    $result = $acttObj->read_all("$table.*,count(*) as counter", "$table", "1 $append_order_type GROUP by $table.order_id,$table.order_type ORDER BY $table.id ASC"); ?>
                    <table class="tablesorter table table-bordered tbl_data" cellspacing="0" cellpadding="0">
                        <thead class="bg-info">
                            <tr>
                                <td>S.No</td>
                                <td>Order ID</td>
                                <td>Company</td>
                                <td>PO# email</td>
                                <td>Invoice No</td>
                                <td>Reference</td>
                                <td>Invoice Amount</td>
                                <td>Notifications</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows == 0) {
                                echo '<tr>
            		  <td colspan="7"><h4 class="text-danger text-center">Sorry ! There are no records.</h4></td></tr>';
                            } else {
                                $array_types = array("f2f" => "interpreter", "tp" => "telephone", "tr" => "translation");
                                while ($row = $result->fetch_assoc()) {
                                    $get_table = $array_types[$row['order_type']];
                                    if ($row['order_type'] == "f2f") {
                                        $data = $acttObj->read_specific("$get_table.orgName,$get_table.porder_email,$get_table.invoiceNo,$get_table.credit_note,$get_table.nameRef,$get_table.total_charges_comp,$get_table.cur_vat,$get_table.C_otherexpns,$get_table.porder", $get_table, "$get_table.id=" . $row['order_id']);
                                    } else if ($row['order_type'] == "tp") {
                                        $data = $acttObj->read_specific("$get_table.orgName,$get_table.porder_email,$get_table.invoiceNo,$get_table.credit_note,$get_table.nameRef,$get_table.total_charges_comp,$get_table.cur_vat,$get_table.porder", $get_table, "$get_table.id=" . $row['order_id']);
                                    } else {
                                        $data = $acttObj->read_specific("$get_table.orgName,$get_table.porder_email,$get_table.invoiceNo,$get_table.credit_note,$get_table.nameRef,$get_table.total_charges_comp,$get_table.cur_vat,$get_table.porder", $get_table, "$get_table.id=" . $row['order_id']);
                                    }
                                    //Invoice number with credit note been made
                                    if (!empty($data['credit_note'])) {
                                        $data['invoiceNo'] = $data['invoiceNo'] . "-0" . $acttObj->read_specific("count(*) as counter", "credit_notes", "order_id=" . $row['order_id'] . " and order_type='" . $row['order_type'] . "'")['counter'];
                                    }
                                    if ($get_table == 'interpreter') {
                                        $totalforvat = $data['total_charges_comp'];
                                        $vatpay = $totalforvat * $data['cur_vat'];
                                        $totinvnow = $totalforvat + $vatpay + $data['C_otherexpns'];
                                    } else if ($get_table == 'telephone') {
                                        $totalforvat = $data['total_charges_comp'];
                                        $vatpay = $totalforvat * $data['cur_vat'];
                                        $totinvnow = $totalforvat + $vatpay;
                                    } else {
                                        $totalforvat = $data['total_charges_comp'];
                                        $vatpay = $totalforvat * $data['cur_vat'];
                                        $totinvnow = $totalforvat + $vatpay;
                                    }
                                    $page_count++;
                                    $counter++; ?>
                                    <tr>
                                        <td><?php echo '<span class="w3-badge w3-blue badge-counter">' . $page_count . '</span>'; ?></td>
                                        <td><?php echo $row['order_id'];
                                            if ($row['order_type'] == 'f2f') {
                                                echo "<span class='label label-success lbl pull-right'>Face To Face</span>";
                                            } else if ($row['order_type'] == 'tp') {
                                                echo "<span class='label label-info lbl pull-right'>Telephone</span>";
                                            } else {
                                                echo "<span class='label label-warning lbl pull-right'>Translation</span>";
                                            } ?></td>
                                        <td><?php echo $data["orgName"]; ?></td>
                                        <td><?php echo $data["porder_email"]; ?></td>
                                        <td><?php echo $data['invoiceNo']; ?></td>
                                        <td><?php echo $data["nameRef"]; ?></td>
                                        <td><?php echo $misc->numberFormat_fun($totinvnow); ?></td>
                                        <td><?php echo "<span class='label label-primary'>" . $row['counter'] . "</span>"; ?></td>
                                        <td width="10%">
                                            <?php if ($action_view_requested_po) { ?>
                                                <a class="btn btn-primary btn-sm" style="color:white" title="View/Update notifications record" href="javascript:void(0)" onclick="popupwindow('view_po_requested.php?order_id=<?php echo $row['order_id']; ?>&order_type=<?php echo $row['order_type']; ?>', 'title', 1200, 650);"><i class="fa fa-pencil"></i></a>
                                            <?php }
                                            if ($action_purchase_order) { ?>
                                                <a class="btn btn-info btn-sm" style="color:black" title="Update purchase order number" href="javascript:void(0)" onclick="popupwindow('purch_update.php?purch_id=<?php echo $row['order_id']; ?>&table=<?php echo $get_table; ?>&orgName=<?php echo $data['orgName']; ?>&porder=<?php echo $row['porder']; ?>','Update Purchase Order',600,550)"><i class="fa fa-barcode"></i></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
    </section>
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                'iDisplayLength': 50
            });
        });
    </script>
</body>

</html>