<?php
session_start();
include '../source/db.php'; 
include '../source/class.php';

if (isset($_POST['update_availability'])) {
    if ($_SESSION['web_userId']) {
        $row = $acttObj->read_specific("actnow,actnow_time,actnow_to", "interpreter_reg", "id=" . $_SESSION['web_userId']);
        $update_array = array("actnow_time" => $_POST['actnow_time'], "actnow_to" => $_POST['actnow_to']);
        if (!isset($_POST['set_available']) && $_POST['actnow_time'] && $_POST['actnow_to']) {
            $update_array['actnow'] = "Inactive";
        } else {
            $update_array['actnow'] = "Active";
            $update_array['actnow_time'] = "1001-01-01";
            $update_array['actnow_to'] = "1001-01-01";
        }
        $acttObj->update("interpreter_reg", $update_array, "id=" . $_SESSION['web_userId']);
        $index_mapping = array('Availability.Status' => 'actnow', 'Date.From' => 'actnow_time', 'Date.To' => 'actnow_to');
    
        $old_values = array();
        $new_values = array();
        $get_new_data = $acttObj->read_specific("actnow,actnow_time,actnow_to", "interpreter_reg", "id=" . $_SESSION['web_userId']);
    
        foreach ($index_mapping as $key => $value) {
            if (isset($get_new_data[$value])) {
                $old_values[$key] = $row[$value];
                $new_values[$key] = $get_new_data[$value];
            }
        }
        $acttObj->log_changes(json_encode($old_values), json_encode($new_values), $_SESSION['web_userId'], "interpreter_reg", "update", $_SESSION['web_userId'], "By Interpreter", "interpreter_availability");
        $_SESSION['returned_message'] = '<center><div class="alert alert-success alert-dismissible show col-md-12" role="alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> Your availability schedule has been updated successfully. Thank you
            </div></center>';
    } else {
        $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-12" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Failed!</strong> Failed to update your availability schedule. Please try again
        </div></center>';
    }
    header('Location: ' . $_POST['redirect_url']);
}