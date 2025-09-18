<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'actions.php';
//Access actions
$get_actions = explode(",", $obj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=212 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_request = $_SESSION['is_root'] == 1 || in_array(214, $get_actions); // This action is unused for now - no need but can be used if in future we have lot of information in amendment to check
$action_update_request = $_SESSION['is_root'] == 1 || in_array(215, $get_actions);
$order_type = @$_GET['order_type'];
if (isset($order_type)) {
    $append_order_type = "and order_type='" . $order_type . "'";
}
$table = "amendment_requests";
$status_array = array("1" => "<span class='label label-warning lbl'>Requested</span>", "2" => "<span class='label label-success lbl'>Approved</span>", "3" => "<span class='label label-danger lbl'>Declined</span>");
$array_types = array("1" => "Face To Face", "2" => "Telephone", "3" => "Translation");
$array_tables = array("1" => "interpreter", "2" => "telephone", "3" => "translation");
?>
<!doctype html>
<html lang="en">

<head>
    <title>Amendment Requests</title>
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
                        if (!empty($order_type)) { ?>
                            <option value="<?php echo $order_type; ?>" selected><?php echo $array_types[$order_type]; ?></option>
                        <?php } ?>
                        <option value="" disabled <?php if (empty($order_type)) {
                                                        echo 'selected';
                                                    } ?>>Filter Job Type</option>
                        <option value="1">Face To Face</option>
                        <option value="2">Telephone</option>
                        <option value="3">Translation</option>
                    </select>
                </div>
                <div class="form-group col-md-6 col-md-offset-0 col-sm-4 mt15">
                    <h2 class="text-center"><a href="<?php echo basename(__FILE__); ?>"><span class="label label-primary">Client Amendment Requests</a></span></h2>
                </div>
                <div class="tab_container" id="put_data">
                    <?php
                    $result = $obj->read_all("$table.*", "$table", "1 $append_order_type ORDER BY $table.id ASC"); ?>
                    <table class="tablesorter table table-bordered tbl_data" cellspacing="0" cellpadding="0">
                        <thead class="bg-info">
                            <tr>
                                <td>S.No</td>
                                <td>Order ID</td>
                                <td>Company</td>
                                <td>Name.Ref</td>
                                <td>Action By</td>
                                <td>Action Date-Time</td>
                                <td>Amendment Date-Time</td>
                                <td>Request Status</td>
                                <td>Chargeable Status</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows == 0) {
                                echo '<tr><td colspan="9"><h4 class="text-danger text-center">No Data Available! There are no records in this list. Thank you</h4></td></tr>';
                            } else {
                                while ($row = $result->fetch_assoc()) {
                                    $get_table = $array_tables[$row['order_type']];
                                    if ($row['order_type'] == 1) {
                                        $data = $obj->read_specific("$get_table.orgName,$get_table.nameRef,$get_table.source,$get_table.assignDate,$get_table.assignTime", $get_table, "$get_table.id=" . $row['order_id']);
                                    } else if ($row['order_type'] == 2) {
                                        $data = $obj->read_specific("$get_table.orgName,$get_table.nameRef,$get_table.source,$get_table.assignDate,$get_table.assignTime", $get_table, "$get_table.id=" . $row['order_id']);
                                    } else {
                                        $data = $obj->read_specific("$get_table.orgName,$get_table.nameRef,$get_table.source,$get_table.asignDate as assignDate,$get_table.assignTime", $get_table, "$get_table.id=" . $row['order_id']);
                                    }
                                    $cancelled_at = $row["created_date"];
                                    $cancelled_date = date('Y-m-d', strtotime($cancelled_at));
                                    $cancelled_time = date('H:i:s', strtotime($cancelled_at));
                                    $assignment_date = date('Y-m-d', strtotime($data['assignDate']));
                                    $assignment_time = date('H:i:s', strtotime($data['assignTime']));
                                    $date1 = new DateTime($cancelled_at);
                                    $date2 = new DateTime($assignment_date . " " . $assignment_time);
                                    $diff = $date2->diff($date1);
                                    $working_days = 0;
                                    $pay_int = 0;
                                    if ($date2 > $date1) {
                                        list($date2, $date1) = [$date1, $date2];
                                    }
                                    while ($date2 < $date1) {
                                        if ($date2->format("N") < 6) {
                                            $working_days++;
                                        }
                                        $date2->modify('+1 day');
                                    }
                                    $diff_hours = ($working_days * 24);
                                    $hours = $diff_hours;
                                    if ($data['source'] == 'Sign Language' || $data['source'] == 'Sign Language (BSL)') {
                                        // For BSL:24 hours=24x7:168,48 hours=24x14:336,greater 48 hours=greater then 336
                                        if ($working_days <= 7) {
                                            $pay_int = 1;
                                            $put_cancelled_hours = " AND cancelled_hours=1";
                                        } else if ($working_days > 7 && $working_days <= 14) {
                                            $put_cancelled_hours = " AND cancelled_hours=2";
                                        } else {
                                            $put_cancelled_hours = " AND cancelled_hours=3";
                                        }
                                    } else {
                                        if ($working_days <= 1) {
                                            $pay_int = 1;
                                            $put_cancelled_hours = " AND cancelled_hours=1";
                                        } else if ($working_days > 1 && $working_days <= 2) {
                                            $put_cancelled_hours = " AND cancelled_hours=2";
                                        } else {
                                            $put_cancelled_hours = " AND cancelled_hours=3";
                                        }
                                    }

                                    $put_bsl = $data['source'] == 'Sign Language' || $data['source'] == 'Sign Language (BSL)' ? " and is_bsl = 1" : " and is_bsl = 0";
                                    $row_dropdown = $obj->read_specific("cd_id,cd_title,cd_effect", "cancellation_drops", "cd_for='cl' $put_bsl $put_cancelled_hours AND deleted_flag=0 ORDER BY cd_title ASC");
                                    $chargeable_status = $row_dropdown['cd_effect'] == '1' ? "Chargeable" : "Non-Chargeable";
                                    $client_chargeable = $row_dropdown['cd_effect'];
                                    $page_count++;
                                    $counter++; ?>
                                    <tr>
                                        <td><?php echo '<span class="w3-badge w3-blue badge-counter">' . $page_count . '</span>'; ?></td>
                                        <td><?php echo $row['order_id'];
                                            if ($row['order_type'] == 1) {
                                                echo "<span class='label label-success lbl pull-right'>Face To Face</span>";
                                            } else if ($row['order_type'] == 2) {
                                                echo "<span class='label label-info lbl pull-right'>Telephone</span>";
                                            } else {
                                                echo "<span class='label label-warning lbl pull-right'>Translation</span>";
                                            } ?></td>
                                        <td><?php echo $data["orgName"]; ?></td>
                                        <td><?php echo $data["nameRef"]; ?></td>
                                        <td><?php echo $row["create_user_name"]; ?></td>
                                        <td><?php echo date('d-m-Y H:i:s', strtotime($row["created_date"])) . " <span class='label label-primary pull-right'>" . $working_days . " days</span>"; ?></td>
                                        <td><?php echo date('d-m-Y H:i:s', strtotime($row["amend_date"] . " " . $row["amend_time"])); ?></td>
                                        <td><?php echo $status_array[$row["status"]] . " <span class='pull-right fa fa-question-circle' title='" . $row['amend_reason'] . "'></span>"; ?></td>
                                        <td><?php echo "<span class='btn btn-" . ($row_dropdown['cd_effect'] == 1 ? "danger" : "success") . "'>" . $chargeable_status . "</span>"; ?></td>
                                        <td width="10%">
                                            <?php if ($action_update_request) { ?>
                                                <a data-client-chargeable="<?=$client_chargeable?>" data-interpreter-chargeable="<?=$pay_int?>" data-id="<?=$row['order_id']?>" data-id="<?=$row['order_id']?>" data-type="<?=$array_tables[$row['order_type']]?>" href="javascript:void(0)" onclick="update_amendment(this)" class="btn btn-primary btn-sm" style="color:white" title="Update amendment request"><i class="fa fa-edit"></i></a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
    </section>
    <!--Start Update Modal-->
    <div class="modal" id="update_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="process/update_amend_request.php" method="post">
                    <input type="hidden" name="amend_order_id" id="amend_order_id" required>
                    <input type="hidden" name="amend_order_type" id="amend_order_type" required>
                    <input type="hidden" name="amend_client_chargeable" id="amend_client_chargeable" required>
                    <input type="hidden" name="amend_interpreter_chargeable" id="amend_interpreter_chargeable" required>
                    <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
                    <div class="modal-header alert-info">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h4 class="modal-title"><b>Update client's order amendment request</b></h4>
                    </div>
                    <div class="modal-body update_modal_attach">
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="update_status">Select a status</label>
                                <select class="form-control" name="update_status" id="update_status" required>
                                    <option value="">--- Not Selected ---</option>
                                    <option value="2">Approve Request</option>
                                    <option value="3">Decline Request</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                        <button onclick='return confirm("Are you sure to update this amendment request?")' type="submit" name="btn_update_amend" class="btn btn-primary">Update Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--End Update Modal-->

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

        function update_amendment(element) {
            $("#amend_order_id").val($(element).attr("data-id"));
            $("#amend_order_type").val($(element).attr("data-type"));
            $("#amend_client_chargeable").val($(element).attr("data-client-chargeable"));
            $("#amend_interpreter_chargeable").val($(element).attr("data-interpreter-chargeable"));
            $('#update_modal').modal('show');
        }
    </script>
</body>

</html>