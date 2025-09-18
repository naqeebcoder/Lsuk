<?php
session_start();
include '../actions.php';
include '../db.php';
if (
    isset($_POST['actnow_time'], $_POST['actnow_to']) &&
    !isset($_POST['set_available']) && 
    $_POST['actnow_time'] && $_POST['actnow_to']
) {
    $from = $_POST['actnow_time'];
    $to = $_POST['actnow_to'];
    $interpreter_id = $_POST['interpreter_id'];

    $query = "
        SELECT id, nameRef, assignDate, source as src, target, 'interpreter' as source FROM interpreter
        WHERE deleted_flag = 0 AND order_cancel_flag = 0 AND orderCancelatoin = 0 
        AND jobStatus = 1 AND intrpName = '{$interpreter_id}' AND jobDisp = 1 AND is_temp = 0 
        AND assignDate BETWEEN '{$from}' AND '{$to}'
        UNION ALL
        SELECT id, nameRef, assignDate, source as src, target, 'telephone' as source FROM telephone
        WHERE deleted_flag = 0 AND order_cancel_flag = 0 AND orderCancelatoin = 0 
        AND jobStatus = 1 AND intrpName = '{$interpreter_id}' AND jobDisp = 1 AND is_temp = 0 
        AND assignDate BETWEEN '{$from}' AND '{$to}'
        UNION ALL
        SELECT id, nameRef, asignDate, source as src, target, 'translation' as source FROM translation
        WHERE deleted_flag = 0 AND order_cancel_flag = 0 AND orderCancelatoin = 0 
        AND jobStatus = 1 AND intrpName = '{$interpreter_id}' AND jobDisp = 1 AND is_temp = 0 
        AND asignDate BETWEEN '{$from}' AND '{$to}'
    ";
    $result = mysqli_query($con,$query);
    $conflicting_jobs = [];
    while ($row = $result->fetch_assoc()) {
        $conflicting_jobs[] = $row;
    }

    if (!empty($conflicting_jobs)) {
        $msg = "<ul>";
        foreach ($conflicting_jobs as $job) {
            $msg .= "<li>[{$job['source']}] Job ID: {$job['id']} | Ref: {$job['nameRef']}</li>";
        }
        $msg .= "</ul>";

		$_SESSION['returned_message'] = '
		<!-- Trigger Modal -->
		<script>
			$(document).ready(function() {
				$("#jobConflictModal").modal("show");
			});
		</script>

		<!-- Modal -->
		<div class="modal fade" id="jobConflictModal" tabindex="-1" role="dialog" aria-labelledby="jobConflictModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
			<div class="modal-header bg-danger text-white">
				<h5 class="modal-title" id="jobConflictModalLabel">Interpreter Job Conflict</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>The interpreter has active jobs in the selected date range:</p>
				<div class="table-responsive">
				<table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th>Job ID</th>
						<th>Job Type</th>
                        <th>Source</th>
                        <th>Target</th>
						<th>Name Ref</th>
						<th>Assign Date</th>
					</tr>
					</thead>
					<tbody>';
					foreach ($conflicting_jobs as $job) {
						$assignDate = $job['assignDate'] ?? '-';
						$deliverDate = ($job['source'] === 'translation') ? ($job['deliverDate'] ?? '-') : '-';
						$faceToFace = ($job['source'] === 'interpreter') ? ($job['face_to_face'] ?? '-') : '-';
						$_SESSION['returned_message'] .= '
						<tr>
                            <td>' . $job['id'] . '</td>
							<td>' . ($job['source'] === 'interpreter' ? 'Face to Face' : ucfirst($job['source'])) . '</td>
                            <td>' . $job['src'] . '</td>
                            <td>' . $job['target'] . '</td>
							<td>' . $job['nameRef'] . '</td>
							<td>' . $assignDate . '</td>
						</tr>';
					}
		$_SESSION['returned_message'] .= '
					</tbody>
				</table>
				</div>
			</div>
			</div>
		</div>
		</div>';

        header('Location: ' . $_POST['redirect_url']);
        exit;
    }
}

if (isset($_POST['update_availability'])) {
    if ($_POST['interpreter_id']) {
        $row = $obj->read_specific("*", "interpreter_reg", "id=" . $_POST['interpreter_id']);
        
        $update_array = array(
            "actnow_time" => $_POST['actnow_time'], "actnow_to" => $_POST['actnow_to'],
            "monday" => $_POST['monday'], "monday_time" => $_POST['monday_time'], "monday_to" => $_POST['monday_to'],
            "tuesday" => $_POST['tuesday'], "tuesday_time" => $_POST['tuesday_time'], "tuesday_to" => $_POST['tuesday_to'],
            "wednesday" => $_POST['wednesday'], "wednesday_time" => $_POST['wednesday_time'], "wednesday_to" => $_POST['wednesday_to'],
            "thursday" => $_POST['thursday'], "thursday_time" => $_POST['thursday_time'], "thursday_to" => $_POST['thursday_to'],
            "friday" => $_POST['friday'], "friday_time" => $_POST['friday_time'], "friday_to" => $_POST['friday_to'],
            "saturday" => $_POST['saturday'], "saturday_time" => $_POST['saturday_time'], "saturday_to" => $_POST['saturday_to'],
            "sunday" => $_POST['sunday'], "sunday_time" => $_POST['sunday_time'], "sunday_to" => $_POST['sunday_to'], "week_remarks" => $_POST['week_remarks']
          );
        if (!isset($_POST['set_available']) && $_POST['actnow_time'] && $_POST['actnow_to']) {
            $update_array['actnow'] = "Inactive";
        } else {
            $update_array['actnow'] = "Active";
            $update_array['actnow_time'] = "1001-01-01";
            $update_array['actnow_to'] = "1001-01-01";
        }
        $obj->update("interpreter_reg", $update_array, "id=" . $_POST['interpreter_id']);
        $index_mapping = array(
            'Availability.Status' => 'actnow', 'Date.From' => 'actnow_time', 'Date.To' => 'actnow_to', 'Remarks' => 'week_remarks',
            'Mon' => 'monday', 'Mon.From' => 'monday_time', 'Mon.To' => 'monday_to', 'Mon' => 'monday', 'Mon.From' => 'monday_time', 'Mon.To' => 'monday_to',
            'Tues' => 'tuesday', 'Tues.From' => 'tuesday_time', 'Tues.To' => 'tuesday_to', 'Wed' => 'wednesday', 'Wed.From' => 'wednesday_time', 'Wed.To' => 'wednesday_to',
            'Thur' => 'thursday', 'Thur.From' => 'thursday_time', 'Thur.To' => 'thursday_to', 'Fri' => 'friday', 'Fri.From' => 'friday_time', 'Fri.To' => 'friday_to',
            'Sat' => 'saturday', 'Sat.From' => 'saturday_time', 'Sat.To' => 'saturday_to', 'Sun' => 'sunday', 'Sun.From' => 'sunday_time', 'Sun.To' => 'sunday_to'
        );
    
        $old_values = array();
        $new_values = array();
        $get_new_data = $obj->read_specific("*", "interpreter_reg", "id=" . $_POST['interpreter_id']);
    
        foreach ($index_mapping as $key => $value) {
            if (isset($get_new_data[$value])) {
                $old_values[$key] = $row[$value];
                $new_values[$key] = $get_new_data[$value];
            }
        }
        $obj->log_changes(json_encode($old_values), json_encode($new_values), $_POST['interpreter_id'], "interpreter_reg", "update", $_SESSION['userId'], $_SESSION['UserName'], "interpreter_availability");
        $_SESSION['returned_message'] = '<center><div class="alert alert-success alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> Availability schedule has been updated successfully. Thank you
            </div></center>';
    } else {
        $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Failed!</strong> Failed to update availability schedule for this interpeter. Try again
        </div></center>';
    }
    header('Location: ' . $_POST['redirect_url']);
}
//Admin add new notes
if (isset($_POST['btn_add_notes'])) {
    $response = array("status" => 0, "message" => "No Things To Do found!");
    $row_admin = $obj->read_specific("*", "login", "id=" . $_SESSION['userId']);
    if ($_SESSION['userId'] && !empty($row_admin) && $row_admin['user_status'] == 1) {
        if ($_POST['details'] && $_POST['interpreter_id']) {
            $done = 0;
            foreach ($_POST['details'] as $note) {
                if ($note) {
                    $insert_array = array("interpreter_id" => $_POST['interpreter_id'], "details" => $obj->con->real_escape_string(trim($note)), "created_by" => $_SESSION['userId'], "created_date" => date('Y-m-d H:i:s'));
                    $obj->insert("interpreter_notes", $insert_array);
                    $done = 1;
                }
            }
            if ($done == 1) {
                $_SESSION['returned_message'] = '<div class="row"><div class="alert alert-success alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success!</strong> Notes for this interpreter has been added successfully. Thank you
                </div></div>';
            } else {
                $_SESSION['returned_message'] = '<div class="row"><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Failed!</strong> Failed to add notes for this interpeter. Try again
                </div></div>';
            }
        } else {
            $_SESSION['returned_message'] = '<div class="row"><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Failed!</strong> You must add details & Interpreter ID for adding notes! Try again
                </div></div>';
        }
    } else {
        $_SESSION['returned_message'] = '<div class="row"><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Failed!</strong> You are not allowed to perform this action! Try again later or contact admin support
                </div></div>';
    }
    header('Location: ' . $_POST['redirect_url'] . "&show_notes");
}
// Delete interpreter notes
if (isset($_POST['delete_interpreter_note']) && isset($_POST['note_id'])) {
    $row_admin = $obj->read_specific("*", "login", "id=" . $_SESSION['userId']);
    $response = array("status" => 0, "message" => "Cannot delete this note!");
    if ($_SESSION['userId'] && !empty($row_admin) && $row_admin['user_status'] == 1) {
        if ($_POST['note_id']) {
            $obj->delete("interpreter_notes", "id=" . $_POST['note_id']);
            $response['status'] = 1;
            $response['message'] = 'This note has been deleted successfully. Thank you';
        } else {
            $response['message'] = 'You must select a valid interpreter note to delete! Thank you';
        }
    } else {
        $response['message'] = 'You are not allowed to perform this action! Try again later or refresh the page!';
    }
    echo json_encode($response);
    exit;
}