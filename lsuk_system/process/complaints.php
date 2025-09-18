<?php
session_start();
include '../../source/setup_email.php';
// View complaint details
if (isset($_POST['complaint_id']) && $_POST['action'] == "view_complaint") {
    include '../actions.php';
    $array_job_types = array(1 => "Face To Face", 2 => "Telephone", 3 => "Translation");
    $priority_array = array(1 => '<label class="label label-danger">High priority</label>', '2' => '<label class="label label-warning">Medium priority</label>', '3' => '<label class="label label-info">Low priority</label>');
    $status_array = array('0' => 'Pending', '1' => 'Resolved', '2' => 'Training Suggested', '3' => 'Removed', '4' => 'In Progress', '5' => 'Concluded');
    $get_complaint = $obj->read_specific("complaints.*,interpreter_reg.name", "complaints,interpreter_reg", "complaints.interpreter_id=interpreter_reg.id AND complaints.id=" . $_POST['complaint_id']); ?>
    <link rel="stylesheet" href="css/chat.css?v=1" />
    <div class="col-sm-12">
        <span class="pull-left">Complaint ID : <b><?php echo "#" . $_POST['complaint_id']; ?></b> for <b><?=$array_job_types[$get_complaint['job_type']]?> Job ID # <?=$get_complaint['job_id']?></b></span>
    </div>
    <div class="chat">
        <div class="chat-header clearfix">
            <div class="row">
                <div class="col-sm-12">
                    <span class="pull-right"><b>Date :</b> <?php echo date("d-m-Y H:i:s", strtotime($get_complaint['dated'])); ?></span>
                    <h4 class="pull-left" style="margin-top: 0px;">
                        <?php if ($get_complaint['status'] == "0") { ?>
                        <?php echo "<span class='label label-warning'>" . $status_array[$get_complaint['status']] . "</span> " . $priority_array[$get_complaint['complaint_priority']]; ?>
                        <?php } else { ?>
                            <?php echo "<span class='label label-success'>" . $status_array[$get_complaint['status']] . "</span> " . $priority_array[$get_complaint['complaint_priority']]; ?>
                        <?php } ?>
                    </h4>
                </div><br>
            </div>
        </div>
        <div class="content-container col-md-12">
            <div class="full-content text-danger hidden">
                <a href="javascript:void(0)" style="color: blue;" class="read-less-link pull-right" onclick="toggle_content(0)">Read Less</a>
                <?= nl2br($get_complaint['details']) ?>
                <a href="javascript:void(0)" style="color: blue;" class="read-less-link" onclick="toggle_content(0)">Read Less</a>
            </div>
            <div class="short-content text-danger">
                <?php if (strlen($get_complaint['details']) > 190) { 
                        echo substr($get_complaint['details'], 0, 190) . ' ... <a href="javascript:void(0)" style="color: blue;" class="read-more-link" onclick="toggle_content(1)">Read More</a>';
                    } else {
                        echo $get_complaint['details'];
                    } ?>
            </div>
        </div>
        <?php $select_complaint = $obj->read_all('*', 'complaint_reply', "complaint_id=" . $_POST['complaint_id']);
        if ($select_complaint->num_rows > 0) { ?>
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="chat-history" style="max-height: 500px;overflow-y: auto;">
                            <ul class="m-b-0 ul_chat">
                                <?php while ($fetch_complaint = $select_complaint->fetch_assoc()) { ?>
                                    <li class="clearfix">
                                        <div class="message-data <?= $fetch_complaint['reply_by'] == 2 ? '' : 'text-right'; ?>">
                                            <span class="message-data-time" <?=($fetch_complaint['reply_by'] == 2 ? 'style="color:#003977;font-weight:bold;"' : '')?>><?= date("d-m-Y H:i:s", strtotime($fetch_complaint['dated'])) ?></span>
                                            <?php if ($fetch_complaint['reply_by'] == 0) { ?>
                                                <i style="background: #fbc23e;padding: 5px 5px;border-radius: 5%;color: white;font-size: 11px;font-weight: 600;">Client</i>
                                            <?php } else { ?>
                                                <i style="background: <?=($fetch_complaint['reply_by'] == 1 ? '#5bc0de' : '#004477')?>;padding: 5px 5px;border-radius: 5%;color: white;font-size: 11px;font-weight: 600;"><?=($fetch_complaint['reply_by'] == 1 ? "Interpreter" : "LSUK Admin")?></i>
                                            <?php } ?>
                                        </div>
                                        <div class="message <?= $fetch_complaint['reply_by'] == 2 ? 'my-message' : 'other-message pull-right'; ?>"> <?= nl2br($fetch_complaint['message']) ?> </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?><br>
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="messages"><br>
                            <div id="text_no_founded">
                                <center>
                                    <h4 class="text-danger"><i>No conversation history for this complaint yet!</i></h4>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <form action="" autocomplete="off" method="post" novalidate="novalidate" enctype="multipart/form-data">
            <?php if (in_array($get_complaint['status'], array(0, 4))) { ?>
                <div class="form-group">
                    <input type="hidden" id="reply_complaint_id" value="<?php echo $_POST['complaint_id']; ?>">
                    <textarea rows="2" id="reply_message_client" class="form-control" placeholder="Write client message ..."></textarea><br>
                    <textarea rows="2" id="reply_message_interpreter" class="form-control" placeholder="Write interpreter reply ..."></textarea><br>
                    <textarea style="border:1px solid #7baaf4;" rows="2" id="reply_message_lsuk" class="form-control" placeholder="Write LSUK reply ..."></textarea>
                </div>
                <button onclick="send_reply();" type="button" class="btn btn-primary"><i class="fa fa-send"></i> Send Reply</button>
            <?php } else { ?>
                <center>
                    <h4><i>This complaint has already been updated. Thank you!</i></h4>
                </center>
            <?php } ?>
    </div>
    </div>
    </form>
<?php }
// Add new complaint
if (isset($_POST['interpreter_id']) && (isset($_POST['details'])) && $_POST['action'] == "new_complaint") {
    include '../actions.php';
    $array_job_types = array(1 => "Face To Face", 2 => "Telephone", 3 => "Translation");
    $response = array("status" => 0, "message" => "Could not create new complaint!");
    $is_client_sent = $is_interpreter_sent = null;
    extract($_POST);

    $check_existing = $obj->read_specific("id", "complaints", "interpreter_id=$interpreter_id AND job_id='$job_id' AND job_type=$job_type")['id'];
    if (empty($check_existing)) {
        $complaint_type = $obj->read_specific("title", "complaint_types", "id=" . $type_id)['title'];
        $get_interpreter = $obj->read_specific("name,email", "interpreter_reg", "id=" . $interpreter_id);

        $data = array(
            "interpreter_id" => $interpreter_id,
            "complaint_priority" => $complaint_priority,
            "job_type" => $job_type,
            "job_id" => $job_id,
            "type_id" => $type_id,
            "complaint_by" => $complaint_by,
            "complaint_email" => $complaint_email,
            "email_int" => $email_int,
            "email_client" => $email_client,
            "details" => $obj->con->real_escape_string($details),
            "received_via" => $received_via,
            "assigned_to" => $assigned_to,
            "created_by" => $_SESSION['userId']
        );

        $new_complaint_id = $obj->insert("complaints", $data, true);
        if ($new_complaint_id) {
            $response['status'] = 1;
            $response['message'] = "New complaint has been created successfully.";
            if ($email_int) {
                $row_format_ack = $obj->read_specific("em_format", "email_format", "id=36");
                $data_replace   = ["[INTERPRETER]", "[NATURE]", "[DETAILS]"];
                $to_replace  = [ucwords($get_interpreter['name']), $complaint_type, $details];
                $message = str_replace($data_replace, $to_replace, $row_format_ack['em_format']);
                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host = setupEmail::EMAIL_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = setupEmail::INFO_EMAIL;
                    $mail->Password   = setupEmail::INFO_PASSWORD;
                    $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                    $mail->Port       = setupEmail::SENDING_PORT;
                    $mail->setFrom(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                    $mail->addAddress($get_interpreter['email']);
                    $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = "Complaint regarding " . $complaint_type;
                    $mail->Body    = $message;
                    if ($mail->send()) {
                        $mail->ClearAllRecipients();
                        $is_interpreter_sent = " Email has been sent to interpreter";
                    } else {
                        $is_interpreter_sent = " Email could not be sent to interpreter";
                    }
                } catch (Exception $e) {
                    $is_interpreter_sent = " Email could not be sent to interpreter due to Mailer";
                }
            }
            if ($email_client) {
                $row_format_ack_client = $obj->read_specific("em_format", "email_format", "id=45");
                $data_replace   = ["[CLIENT]", "[JOB_INFORMATION]", "[REFERENCE_ID]", "[DETAILS]"];
                $job_details = $array_job_types[$job_type] . " Job ID # " . $job_id;
                $to_replace  = [ucwords($complaint_by), $job_details, "LSUK#" . $new_complaint_id, $details];
                $message_client = str_replace($data_replace, $to_replace, $row_format_ack_client['em_format']);
                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host = setupEmail::EMAIL_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = setupEmail::INFO_EMAIL;
                    $mail->Password   = setupEmail::INFO_PASSWORD;
                    $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                    $mail->Port       = setupEmail::SENDING_PORT;
                    $mail->setFrom(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                    $mail->addAddress($complaint_email);
                    $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = "Complaint Reference ID LSUK#" . $new_complaint_id . " has been registered";
                    $mail->Body    = $message_client;
                    if ($mail->send()) {
                        $mail->ClearAllRecipients();
                        $is_client_sent = " Email has been sent to client";
                    } else {
                        $is_client_sent = " Email could not be sent to client";
                    }
                } catch (Exception $e) {
                    $is_client_sent = " Email could not be sent to client due to Mailer";
                }
            }
        } else {
            $response['message'] = "Sorry, Could not create invalid complaint!";
        }
        $response['message'] .=  $is_interpreter_sent . $is_client_sent;
    } else {
        $response['message'] = "This complaint is already registered!";
    }
    echo json_encode($response);
}

if (isset($_POST['reply_complaint_id']) && (isset($_POST['reply_message_client']) || isset($_POST['reply_message_interpreter']) || isset($_POST['reply_message_lsuk'])) && $_POST['action'] == "complaint_reply") {
    $insert_array = array("complaint_id" => $_POST['reply_complaint_id']);
    include '../actions.php';
    $client_done = $interpreter_done = true;
    $response = array("status" => 0);
    if (!empty($_POST['reply_message_client'])) {
        $insert_array["reply_by"] = 0;
        $insert_array["message"] = $_POST['reply_message_client'];
        $client_done = $obj->insert("complaint_reply", $insert_array);
    }
    if (!empty($_POST['reply_message_interpreter'])) {
        $insert_array["reply_by"] = 1;
        $insert_array["message"] = $_POST['reply_message_interpreter'];
        $interpreter_done = $obj->insert("complaint_reply", $insert_array);
    }
    if (!empty($_POST['reply_message_lsuk'])) {
        $insert_array["reply_by"] = 2;
        $insert_array["message"] = $_POST['reply_message_lsuk'];
        $lsuk_done = $obj->insert("complaint_reply", $insert_array);
    }
    if ($client_done || $interpreter_done || $lsuk_done) {
        $response['status'] = 1;
    }
    echo json_encode($response);
}



if (isset($_POST['complaint_id']) && $_POST['action'] == "update_complaint") {
    include '../actions.php';
    $get_complaint = $obj->read_specific("complaints.*,interpreter_reg.name", "complaints,interpreter_reg", "complaints.interpreter_id=interpreter_reg.id and complaints.id=" . $_POST['complaint_id']);
    $status_array = array('0' => 'Pending', '1' => 'Resolved', '2' => 'Training Suggested', '3' => 'Removed', '4' => 'In Progress', '5' => 'Concluded'); ?>
    <h4>Complaint ID : <span class="label label-primary"><?php echo "#" . $_POST['complaint_id']; ?></span></h4>
    <p class="pull-right">
    <h4><span class="label label-default pull-right"> <?php echo $status_array[$get_complaint['status']]; ?></span></h4>
    </p>
    <hr>
    <p class="pull-left"><b>Interpreter Name :</b> <?php echo $get_complaint['name']; ?></p>
    <p class="pull-right"><b>Complaint Date :</b> <?php echo $get_complaint['dated']; ?></p>
    <br>
    <form method="POST" action="process/complaints.php">
        <input type="hidden" name="action" value="update_complaint_status">
        <input type="hidden" name="id" value="<?=$_POST['complaint_id']?>">
        <input type="hidden" name="redirect_url" value="<?=$_POST['redirect_url']?>">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td align="left">Update complaint status</td>
                    <td align="left">
                        <input type="hidden" name="order_id" value="<?php echo $_POST['order_id']; ?>">
                        <input type="hidden" name="interpreter_id" value="<?php echo $_POST['interpreter_id']; ?>">
                        <select required class="form-control" name="status">
                            <option value="" selected disabled>--- Choose an action ---</option>
                            <option value="1">Resolved</option>
                            <option value="2">Training Suggested</option>
                            <option value="3">Removed</option>
                            <option value="4">In Progress</option>
                            <option value="5">Concluded</option>
                        </select>
                        <div class="form-group"><br>
                            <label class="btn btn-default pull-left" for="email_client_update"><input id="email_client_update" name="email_client_update" type="checkbox" value="1" /> Notify Client via Email</label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="left"><button class="btn btn-primary" type="submit" name="btn_update_status">Update Complaint</button></td>
                </tr>
            </tbody>
        </table>
    </form>
<?php }
if (isset($_POST['btn_update_status']) && $_POST['action'] == 'update_complaint_status') {
    include '../actions.php';
    $is_client_updated = null;
    $done = $obj->update("complaints", array("status" => $_POST['status']), "id=" . $_POST['id']);
    $status_array = array('0' => 'Pending', '1' => 'Resolved', '2' => 'Training Suggested', '3' => 'Removed', '4' => 'In Progress', '5' => 'Concluded');
    $get_complaint = $obj->read_specific("complaints.*", "complaints", "complaints.id=" . $_POST['id']);
    if ($done) {
        if (isset($_POST['email_client_update'])) {
            try {
                $row_format_update = $obj->read_specific("em_format", "email_format", "id=46");
                $data_replace   = ["[CLIENT]", "[JOB_INFORMATION]", "[REFERENCE_ID]", "[COMPLAINT_STATUS]", "[DETAILS]"];
                $job_details = $array_job_types[$job_type] . " Job ID # " . $job_id;
                $to_replace  = [ucwords($get_complaint['complaint_by']), $get_complaint['job_details'], "LSUK#" . $get_complaint['id'], $status_array[$get_complaint['status']], $get_complaint['details']];
                $message_client_update = str_replace($data_replace, $to_replace, $row_format_update['em_format']);
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = setupEmail::EMAIL_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = setupEmail::INFO_EMAIL;
                $mail->Password   = setupEmail::INFO_PASSWORD;
                $mail->SMTPSecure = setupEmail::SECURE_TYPE;
                $mail->Port       = setupEmail::SENDING_PORT;
                $mail->setFrom(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                $mail->addAddress($get_complaint['complaint_email']);
                $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                $mail->isHTML(true);
                $mail->Subject = "Complaint Reference ID LSUK#" . $get_complaint['id'] . " is updated";
                $mail->Body    = $message_client_update;
                if ($mail->send()) {
                    $mail->ClearAllRecipients();
                    $is_client_updated = " Email has been sent to client";
                } else {
                    $is_client_updated = " Email could not be sent to client";
                }
            } catch (Exception $e) {
                $is_client_updated = " Email could not be sent to client due to Mailer";
            }
        }
        $_SESSION['returned_message'] = '<center><div class="alert alert-success alert-dismissible show col-md-8 col-md-offset-2" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Success!</strong> Complaint status has been updated successfully! ' . $is_client_updated . '
            </div></center>';
    } else {
        $_SESSION['returned_message'] = '<center><div class="alert alert-danger alert-dismissible show col-md-8 col-md-offset-2" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Sorry!</strong> Failed to update the status of this complaint! Please try again
            </div></center>';
    }
    header('Location: ' . $_POST['redirect_url']);
}