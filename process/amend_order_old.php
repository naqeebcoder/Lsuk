<?php
session_start();
include '../lsuk_system/actions.php'; 

if (isset($_POST['btn_amend_order'])) {
    if ($_SESSION['cust_userId']) {
        $array_tables = array("1" => "interpreter", "2" => "telephone", "3" => "translation");
        $row = $obj->read_specific("*", "amendment_requests", "order_type='" . $_POST['amend_type'] . "' AND order_id=" . $_POST['amend_id']);
        if ($row['id']) {
            $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-12" role="alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Alert!</strong> There is already an amendment request submitted for this order on ' . $misc->dated($row['created_date']) . '. Contact LSUK support or wait for status update. Thank you
            </div></center>';
        } else {
            $insert_array = array(
                "order_id" => $_POST['amend_id'], "order_type" => $_POST['amend_type'], "create_user_name" => $_SESSION['cust_UserName'], "company_id" => $_SESSION['company_id'], "created_date" => date('Y-m-d H:i:s'), 
                "amend_reason" => trim($_POST['amend_reason']), "amend_date" => $_POST['amend_date'], "amend_time" => $_POST['amend_time']
            );
            $done = $obj->insert("amendment_requests", $insert_array);
            if ($done) {
                // Insert job note for admin
                $obj->insert('jobnotes', array('jobNote' => 'Client requested amendment at ' . date("Y-m-d H:i:s") . "<br>" . trim($_POST['amend_reason']) . " & new booking date-time is " . $misc->dated($_POST['amend_date']) . " " . $_POST['amend_time'], 'tbl' => $array_tables[$_POST['amend_type']], 'time' => $misc->sys_datetime_db(), 'fid' => $_POST['amend_id'], 'submitted' => $_SESSION['cust_UserName'], 'dated' => date('Y-m-d')));
                $_SESSION['returned_message'] = '<center><div class="alert alert-success alert-dismissible show col-md-12" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success!</strong> Your amendment request for this order has been submitted successfully. Thank you
                </div></center>';
            } else {
                $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-12" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Failed!</strong> Failed to update your amendment request for this order. Please try again
                </div></center>';
            }
        }
    } else {
        $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-12" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Failed!</strong> Failed to update your amendment request for this order. Please try again
        </div></center>';
    }
    header('Location: ' . $_POST['redirect_url']);
}