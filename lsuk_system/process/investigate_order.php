<?php
session_start();
//Admin view order history in detail
if (isset($_POST['investigate_order']) && isset($_POST['investigate_order_id']) && isset($_POST['investigate_order_type']) && isset($_SESSION['userId'])) {
    include '../actions.php';
    $data = array('status' => 0, 'body' => '');
    if (isset($_SESSION)) {
        $where = array(1 => "F2F Job ID: " . $_POST['investigate_order_id'], 2 => "TP Job ID: " . $_POST['investigate_order_id'], 3 => "TR Job ID: " . $_POST['investigate_order_id']);
        $actions_array = array("update" => "<small class='text-primary'>Updated <i class='fa fa-edit'></i></small>", "create" => "<small class='text-success'>Created <i class='fa fa-plus'></i></small>", "delete" => "<small class='text-danger'>Deleted <i class='fa fa-trash'></i></small>");
        $table_name_array = array(1 => "interpreter", 2 => "telephone", 3 => "translation");
        $data['status'] = 1;
        $data['body'] = "<h3 class='text-center'>Log History for <b>" . $_POST['table_name'] . "</b> Order ID # " . $_POST['investigate_order_id'] . "</h3>";
        $get_logs = $obj->read_all("daily_logs.id,daily_logs.action_id,daily_logs.dated,user_actions.title,login.name", "daily_logs,user_actions,login", "daily_logs.action_id=user_actions.id AND daily_logs.user_id=login.id AND daily_logs.details='" . $where[$_POST['investigate_order_type']] . "'");

        //New Code 
        $get_audit_history = $obj->read_all("*", "audit_logs", "table_name='" . $table_name_array[$_POST['investigate_order_type']] . "' AND record_id=" . $_POST['investigate_order_id']);
        $get_audit_history_result = $get_audit_history->fetch_assoc();
        $amount = 0;
        if ($get_audit_history_result) {
            $joob = $obj->read_all("*", $table_name_array[$_POST['investigate_order_type']], "id=" . $get_audit_history_result['record_id']);
            $job_result =  $joob->fetch_assoc();
            if ($table_name_array[$_POST['investigate_order_type']] == "interpreter") {
                $amount = $job_result['otherCharges'] + $job_result['total_charges_comp'] * $job_result['cur_vat'] + $job_result['total_charges_comp'];
            } else {
                $amount = $job_result['total_charges_comp'] * $job_result['cur_vat'] + $job_result['total_charges_comp'];
            }
        }

        if ($get_logs->num_rows > 0) {
            $daily_actions_array = array();
            $data['body'] .= '<div class="panel-group" id="accordion" style="padding: 6px;">
                <div class="panel panel-info">';
            while ($row = $get_logs->fetch_assoc()) {
                $daily_actions_array[] = $row['action_id'];
                // Check if (Checked The Job:33) was done from home OR booking screen
                if ($row['action_id'] == 33) {
                    // See if (Allocated The Job:5) action is already done then booking else home screen
                    if (in_array(5, $daily_actions_array)) {
                        $screen_name = " in Booking List";
                    } else {
                        $screen_name = " in Home Screen";
                    }
                    $action_name = $row['title'] . $screen_name;
                } else {
                    $action_name = $row['title'];
                }
                $action_name = trim($action_name);
               
                $show_amount = 0;
                if (preg_match('/invoice/i', $action_name)) {
                    $show_amount = 1; 
                }
               
                $data['body'] .= '
                    <div class="panel-heading" style="margin: 4px;">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse_' . $row['id'] . '">
                                <b>' . ucwords($row['name']) . ' ' . $action_name . ' at ' . date_format(date_create($row['dated']), 'd-m-Y H:i:s');

                
                if ($show_amount ) {
                    $data['body'] .= ' (Amount: ' . number_format($amount, 2) . ')'; 
                }
                

                $data['body'] .= '</b></a>
                        </h4>
                    </div>
                    <div id="collapse_' . $row['id'] . '" class="panel-collapse collapse">
                        <div class="panel-body table-responsive">
                            <table class="table table-bordered table-history">
                                <thead><tr class="bg-info">
                                    <td width="13%">Field</td>
                                    <td width="5%">Action</td>
                                    <td width="12%">User/Date-Time</td>
                                    <td width="35%">Old Value</td>
                                    <td width="35%">New Value</td>
                                </tr></thead>
                                <tbody>';

                $get_log_history = $obj->read_all("*", "audit_logs", "table_name='" . $table_name_array[$_POST['investigate_order_type']] . "' AND record_id=" . $_POST['investigate_order_id'] . " AND created_date='" . $row['dated'] . "'");

                if ($get_log_history->num_rows > 0) {
                    while ($row_history = $get_log_history->fetch_assoc()) {
                        // print_r($row_history);
                        // die();
                        $data['body'] .= "<tr>
                                    <td>" . $row_history['field_name'] . "</td>
                                    <td>" . $actions_array[$row_history['action']] . "<br><small class='text-muted'>" . $row_history['ip_address'] . "</small></td>
                                    <td>" . $row_history['user_name'] . " (#{$row_history['user_id']})</td>
                                    <td><div style='word-wrap: break-word;max-width: 450px;'><small>" . ($row_history['old_value'] != strip_tags($row_history['old_value']) ? str_replace("<p>&nbsp;</p>", "", $row_history['old_value']) : $row_history['old_value']) . "</small></div></td>
                                    <td><div style='word-wrap: break-word;max-width: 450px;'><small class='text-success'>" . ($row_history['new_value'] != strip_tags($row_history['new_value']) ? str_replace("<p>&nbsp;</p>", "", $row_history['new_value']) : $row_history['new_value']) . "</small></div></td>
                                </tr>";
                    }
                } else {
                    $data['body'] .= "<tr><td colspan='5' align='center'>No log details found for this action!</td></tr>";
                }
                $data['body'] .= '</tbody>
                            </table>
                        </div>
                    </div>';
                    // $amount = 0;
                    $show_amount = 0;
            }
            $data['body'] .= '</div>
            </div>';
        } else {
            $data['body'] .= "<p class='text-center text-danger'>No log history found for <b>" . $_POST['table_name'] . "</b> Order ID # " . $_POST['investigate_order_id'] . "</p>";
        }
    }
    echo json_encode($data);
    exit;
}