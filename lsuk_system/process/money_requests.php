<?php session_start();
include '../../source/setup_email.php';
include '../actions.php';

//admin view leave request
if (isset($_POST['request_id']) & $_POST['action'] == "check_request") {
    $array = array("1" => "Requested", "2" => "Accepted", "3" => "Rejected");
    $array_types = array(1 => "Face To Face", 2 => "Telephone", 3 => "translation");
    $array_colors = array("1" => "label-warning", "2" => "label-success", "3" => "label-danger");
    // Query for requested loan
    $row = $obj->read_specific("interpreter_reg.name,interpreter_reg.email,interpreter_reg.contactNo,interpreter_reg.interp_pix,loan_requests.*,loan_requests.id as request_id,loan_requests.status as loan_status,loan_dropdowns.title,loan_dropdowns.is_payable", "interpreter_reg,loan_requests,loan_dropdowns", "loan_requests.type_id=loan_dropdowns.id AND interpreter_reg.id=loan_requests.interpreter_id AND loan_requests.id=" . $_POST['request_id']);
    $interp_pix = $row['interp_pix'] ?: "profile.png";
?>
    <center>
        <h4><label class="label label-primary"><?php echo "Interpreter ID # <b>" . $row['interpreter_id'] . "</b>"; ?></label></h4>
    </center>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <td colspan="4" align="center">
                    <div class="row">
                        <div class="col-md-3">
                            <img class="img-thumbnail" width="100" src="file_folder/interp_photo/<?= $interp_pix; ?>" />
                        </div>
                        <div class="col-md-9 text-left">
                            <b><?php echo ucwords($row['name']); ?></b>
                            <br><?php echo "Email: " . $row['email'] . "<br>Contact No: " . $row['contactNo']; ?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="bg-info">
                <td width="25%"><b>Requested Amount</b></td>
                <td><b><?= number_format($row['loan_amount'], 2) ?></b></td>
                <td><b>Payable Month</b></td>
                <td><b><?= date('F Y', strtotime($row['payable_date'])) ?></b></td>
            </tr>
            <tr>
                <td>Request Type</td>
                <td><?= $row['title'] ?></td>
                <td>Request Status</td>
                <td>
                    <h3 style="margin:0px;"><span class="label <?php echo $array_colors[$row['loan_status']]; ?>"><?php echo $array[$row['loan_status']]; ?></span></h3>
                </td>
            </tr>
            <tr>
                <td>Requested Added By</td>
                <td><?php echo $row['created_by'] ? "By LSUK Admin" : "By Interpreter"; ?></td>
                <td>Receipt</td>
                <td><?= !$row['uploaded_file'] ? '<small>No receipt added!</small>' : '<a href="../file_folder/money_requests/' . $row['uploaded_file'] . '" class="btn btn-info btn-xs" target="_blank">View Uploaded Receipt</a>' ?></td>
            </tr>
            <tr>
                <td>Reason</td>
                <td colspan="3" class="text-danger"><small><?php echo $row['reason'] ?: "Not mentioned!"; ?></small></td>
            </tr>
            <tr>
                <td>Job ID</td>
                <td colspan="3"><?php echo $row['job_id'] ? $array_types[$row['job_type']] . " Job ID # " . $row['job_id']: "Not applicable!"; ?></td>
            </tr>
            <tr class="bg-info">
                <td colspan="4"><b>Money Request Updated Details</b></td>
            </tr>
            <?php if ($row['loan_status'] == 3) {
                $get_user = $obj->read_specific("name", "login", "id=" . $row['rejected_by'])['name'];
                echo "<tr>
                        <td><span class='text-danger'>Loan Rejected!</span></td>
                        <td>User: " . ucwords($get_user) . "</td>
                        <td colspan='2'>Reason: " . ($row['reject_reason'] ?: "Not mentioned!") . "</td>
                    </tr>";
            } else if ($row['loan_status'] == 2) {
                $get_user = $obj->read_specific("name", "login", "id=" . $row['accepted_by'])['name'];
                echo "<tr class='text-success'>
                            <td>Accepted By</td>
                            <td>" . ucwords($get_user) . "</td>
                            <td>Given Amount</td>
                            <td>" . number_format($row['given_amount'], 2) . "</td>
                        </tr>";
                if ($row['is_payable'] == 1) {
                    echo "<tr class='text-success'>
                            <td>Number of " . ($row['is_payable'] == 1 ? "Installments" : "Receivables") . "</td>
                            <td>" . ($row['duration'] == 1 ? "<span class='label label-primary'>ALL AT ONCE</span>" : $row['duration']) . "</td>
                            <td>Per " . ($row['is_payable'] == 1 ? "Installment" : "Receivable") . " Amount</td>
                            <td>" . number_format($row['given_amount'] / $row['duration'], 2) . "</td>";
                    echo "</tr>";
                }
                if ($row['is_payable'] == 1) {
                    $get_paybacks = $obj->read_all("*", "request_paybacks", "deleted_flag=0 AND request_id=" . $_POST['request_id']);
                    if ($get_paybacks->num_rows > 0) {
                        $total_paid = 0;
                        $paid_counter = 1;
                        echo "<tr class='bg-info'><td>Deduction Type</td><td>Paid Date</td><td>Paid Amount</td><td>Installment No#</td></tr>";
                        while ($row_pay = $get_paybacks->fetch_assoc()) {
                            $total_paid += $row_pay['paid_amount'];
                            $paid_by_user = !empty($row_pay['created_by']) ? "<small class='pull-right'>By " . $obj->read_specific("name", "login", "id=" . $row_pay['created_by'])['name'] . "</small>" : "";
                            echo "<tr>
                                <td>" . ($row_pay['paid_type'] == 1 ? "Salary Deducted" : "Manually Deducted") . "</td>
                                <td>" . $misc->dated($row_pay['paid_date']) . "</td>
                                <td>" . number_format($row_pay['paid_amount'], 2) . $paid_by_user . "</td>
                                <td>" . $paid_counter++ . "</td>
                            </tr>";
                        }
                        echo "<tr><td colspan='2' align='right'><b>Total Paid Back:</b></td><td colspan='2'><b>" . number_format($total_paid, 2) . "</b></td></tr>";
                        if ($row['given_amount'] > $total_paid) {
                            echo "<tr><td colspan='2' align='right' class='text-danger'><b>Remaining To Pay:</b></td><td colspan='2' class='text-danger'><b>" . number_format($row['given_amount'] - $total_paid, 2) . "</b><button onclick='show_deduction_div()' type='button' class='btn btn-danger pull-right hidden'> Deduct New Amount</button></td></tr>";
                        }
                    } else { ?>
                        <tr><td colspan='4' align='center' class='text-danger'><b><i class='fa fa-exclamation-circle'></i> There is no installment paid by Interpreter against this money request yet!</b></td></tr>
                        <tr>
                            <td colspan="4">
                                <form action="process/money_requests.php" autocomplete="off" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="request_id" value="<?php echo $_POST['request_id']; ?>" />
                                    <input type="hidden" name="loan_amount" id="loan_amount" value="<?= $row['loan_amount'] ?>" />
                                    <input type="hidden" name="interpreter_id" value="<?php echo $row['interpreter_id']; ?>" />
                                    <input type="hidden" name="email" value="<?php echo $row['email']; ?>" />
                                    <input type="hidden" name="redirect_url" value="<?= $_POST['redirect_url'] ?>" />
                                    <div class="form-group">
                                        <label class="btn btn-success">
                                            <input type="radio" name="status" value="2" data-is_payable="<?= $row['is_payable'] ?>" onchange="toggle_update_request(this)" /> Accept Request
                                        </label>
                                        <label class="btn btn-danger">
                                            <input type="radio" name="status" value="3" onchange="toggle_update_request(this)" /> Reject Request
                                        </label>
                                    </div>
                                    <div class="div_accept hidden">
                                        <div class="form-group col-md-4">
                                            <label>Approve Requested Amount</label>
                                            <input style="width: 80%;" class="form-control" type="text" name="given_amount" id="given_amount" value="<?= $row['given_amount'] ?: $row['loan_amount'] ?>" oninput="calculate_duration()" />
                                        </div>
                                        <div class="form-group col-md-8">
                                            <label>Select Payable Month <small class='text-danger'>(Payslip deduction will start from this month)</small></label>
                                            <input min="<?= $row['payable_date'] ? substr($row['payable_date'], 0, 7) : date('Y-m') ?>" value="<?= $row['payable_date'] ? substr($row['payable_date'], 0, 7) : date('Y-m') ?>" style="width: 30%;" class="form-control" type="month" name="payable_date" id="payable_date" />
                                        </div>
                                        <div class="row"></div>
                                        <div class="form-group col-md-4">
                                            <label>Deduction Percentage</label>
                                            <select class="form-control" id="percentage" name="percentage" onchange="calculate_duration()">
                                                <option value="">- Select percentage -</option>
                                                <option <?=$row['percentage'] == 5 ? 'selected' : ''?> value="5">5 Percent</option>
                                                <option <?=$row['percentage'] == 10 ? 'selected' : ''?> value="10">10 Percent</option>
                                                <option <?=$row['percentage'] == 15 ? 'selected' : ''?> value="15">15 Percent</option>
                                                <option <?=$row['percentage'] == 20 ? 'selected' : ''?> value="20">20 Percent</option>
                                                <option <?=$row['percentage'] == 25 ? 'selected' : ''?> value="25">25 Percent</option>
                                                <option <?=$row['percentage'] == 30 ? 'selected' : ''?> value="30">30 Percent</option>
                                                <option <?=$row['percentage'] == 35 ? 'selected' : ''?> value="35">35 Percent</option>
                                                <option <?=$row['percentage'] == 40 ? 'selected' : ''?> value="40">40 Percent</option>
                                                <option <?=$row['percentage'] == 45 ? 'selected' : ''?> value="45">45 Percent</option>
                                                <option <?=$row['percentage'] == 50 ? 'selected' : ''?> value="50">50 Percent</option>
                                                <option <?=$row['percentage'] == 100 ? 'selected' : ''?> value="100" style="color:red">FULL AMOUNT AT ONCE</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Total Payable Installments</label>
                                            <input type="hidden" name="duration" id="duration" />
                                            <br>
                                            <h3 style="display: inline;"><span class="label label-info text_duration">No Calculations</span></h3>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Per Installment Amount</label>
                                            <br>
                                            <h3 style="display: inline;"><span class="label label-info text_installment_amount">No Calculations</span></h3>
                                        </div>
                                    </div>
                                    <div class="div_reject hidden">
                                        <div class="form-group">
                                            <label>Reason of Rejection</label>
                                            <textarea rows="4" id="reject_reason" name="reject_reason" class="form-control" placeholder="Enter rejection reason here ..."><?= $row['reject_reason'] ?></textarea>
                                        </div>
                                    </div>
                                    <div class="row"></div>
                                    <div class="form-group">
                                        <label class="btn btn-default btn-sm" for="notify_Interpreter"><input type="checkbox" value="1" name="notify_Interpreter" id="notify_Interpreter" <?= $row['loan_status'] > 1 ? 'readonly disabled' : '' ?> <?= $row['is_notified'] == 1 ? 'checked' : '' ?>> Notify Interpreter on email about money request</label>
                                        <br><br><button type="submit" name="btn_update_loan_status" class="btn btn-primary"><i class="fa fa-check-circle"></i> Update Request Status</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <script>
                            $(document).ready(function() {
                                calculate_duration();
                            });
                        </script>
                    <?php }
                }
            } else { ?>
                <tr>
                    <td colspan="4">
                        <form action="process/money_requests.php" autocomplete="off" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="request_id" value="<?php echo $_POST['request_id']; ?>" />
                            <input type="hidden" name="loan_amount" id="loan_amount" value="<?= $row['loan_amount'] ?>" />
                            <input type="hidden" name="interpreter_id" value="<?php echo $row['interpreter_id']; ?>" />
                            <input type="hidden" name="email" value="<?php echo $row['email']; ?>" />
                            <input type="hidden" name="redirect_url" value="<?= $_POST['redirect_url'] ?>" />
                            <div class="form-group">
                                <label class="btn btn-success">
                                    <input type="radio" name="status" value="2" data-is_payable="<?= $row['is_payable'] ?>" onchange="toggle_update_request(this)" /> Accept Request
                                </label>
                                <label class="btn btn-danger">
                                    <input type="radio" name="status" value="3" onchange="toggle_update_request(this)" /> Reject Request
                                </label>
                            </div>
                            <div class="div_accept hidden">
                                <div class="form-group col-md-4">
                                    <label>Approve Requested Amount</label>
                                    <input style="width: 80%;" class="form-control" type="text" name="given_amount" id="given_amount" value="<?= $row['given_amount'] ?: $row['loan_amount'] ?>" oninput="calculate_duration()" />
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Select Payable Month <small class='text-danger'>(Payslip deduction will start from this month)</small></label>
                                    <input min="<?= $row['payable_date'] ? substr($row['payable_date'], 0, 7) : date('Y-m') ?>" value="<?= $row['payable_date'] ? substr($row['payable_date'], 0, 7) : date('Y-m') ?>" style="width: 30%;" class="form-control" type="month" name="payable_date" id="payable_date" />
                                </div>
                                <div class="row"></div>
                                <div class="form-group col-md-4">
                                    <label>Deduction Percentage</label>
                                    <select class="form-control" id="percentage" name="percentage" onchange="calculate_duration()">
                                        <option value="">- Select percentage -</option>
                                        <option value="5">5 Percent</option>
                                        <option value="10">10 Percent</option>
                                        <option value="15">15 Percent</option>
                                        <option value="20">20 Percent</option>
                                        <option value="25">25 Percent</option>
                                        <option value="30">30 Percent</option>
                                        <option value="35">35 Percent</option>
                                        <option value="40">40 Percent</option>
                                        <option value="45">45 Percent</option>
                                        <option value="50">50 Percent</option>
                                        <option value="100" style="color:red">FULL AMOUNT AT ONCE</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Total Payable Installments</label>
                                    <input type="hidden" name="duration" id="duration" />
                                    <br>
                                    <h3 style="display: inline;"><span class="label label-info text_duration">No Calculations</span></h3>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Per Installment Amount</label>
                                    <br>
                                    <h3 style="display: inline;"><span class="label label-info text_installment_amount">No Calculations</span></h3>
                                </div>
                            </div>
                            <div class="div_reject hidden">
                                <div class="form-group">
                                    <label>Reason of Rejection</label>
                                    <textarea rows="4" id="reject_reason" name="reject_reason" class="form-control" placeholder="Enter rejection reason here ..."><?= $row['reject_reason'] ?></textarea>
                                </div>
                            </div>
                            <div class="row"></div>
                            <div class="form-group">
                                <label class="btn btn-default btn-sm" for="notify_Interpreter"><input type="checkbox" value="1" name="notify_Interpreter" id="notify_Interpreter" <?= $row['loan_status'] > 1 ? 'readonly disabled' : '' ?> <?= $row['is_notified'] == 1 ? 'checked' : '' ?>> Notify Interpreter on email about money request</label>
                                <?php if ($row['loan_status'] == 1) { ?>
                                    <br><br><button type="submit" name="btn_update_loan_status" class="btn btn-primary"><i class="fa fa-check-circle"></i> Update Request Status</button>
                                <?php } ?>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php }
            // Total summary payable info
            if ($row['is_payable'] == 1) {
                $total_given_amount = $obj->read_specific("IFNULL(SUM(round(loan_requests.given_amount,2)),0) as given_amount", "loan_requests,loan_dropdowns", "loan_requests.type_id=loan_dropdowns.id AND loan_requests.interpreter_id=" . $row['interpreter_id'] . " AND loan_requests.status=2 AND loan_dropdowns.is_payable=1")['given_amount'];
                $total_repaid_amount = $obj->read_specific("IFNULL(SUM(round(request_paybacks.paid_amount,2)),0) as paid_amount", "request_paybacks,loan_requests", "request_paybacks.request_id=loan_requests.id AND request_paybacks.deleted_flag=0 AND loan_requests.interpreter_id=" . $row['interpreter_id'])['paid_amount']; ?>
                <tr class="bg-danger">
                    <td colspan="4">
                        <div class="col-md-4">
                            <label>Total Amount Given :<br>
                                <b><?= number_format($total_given_amount, 2) ?></b></label>
                        </div>
                        <div class="col-md-4">
                            <label>Total Repaid Amount :<br>
                                <b><?= number_format($total_repaid_amount, 2) ?></b></label>
                        </div>
                        <div class="col-md-4">
                            <label>Total Remaining Payable :
                                <h3 style="display:inline;"><?= $total_given_amount - $total_repaid_amount > 0 ? "<span class='label label-danger'>" . number_format(($total_given_amount - $total_repaid_amount), 2) : "<span class='label label-success'>" . number_format(($total_given_amount - $total_repaid_amount), 2); ?></span></h3>
                            </label>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php }
if (isset($_POST['btn_update_loan_status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];
    $datetime = date('Y-m-d H:i:s');
    if (!empty($request_id)) {
        $payable_date = $_POST['payable_date'] ? $_POST['payable_date'] : date("Y-m-d");
        $array = array("payable_date" => date('Y-m-01', strtotime($payable_date)), "status" => $status, "updated_by" => $_SESSION['userId'], "updated_date" => $datetime);
        if (isset($_POST['notify_Interpreter'])) {
            $array['is_notified'] = 1;
        }
        if ($status == 3) {
            if (!empty($_POST['reject_reason'])) {
                $array['reject_reason'] = $obj->con->real_escape_string(trim($_POST['reject_reason']));
                $reject_reason = $array['reject_reason'];
                $array['rejected_by'] = $_SESSION['userId'];
                $array['rejected_date'] = $datetime;
            }
        } else {
            $array['given_amount'] = $_POST['given_amount'];
            $array['duration'] = $_POST['duration'] ?: 1;
            $array['percentage'] = $_POST['percentage'] ?: 100;
            $array['accepted_by'] = $_SESSION['userId'];
            $array['accepted_date'] = $datetime;
        }
        $result = $obj->update("loan_requests", $array, "id=" . $request_id);
        if ($result) {
            if (isset($_POST['notify_Interpreter'])) {
                $subject = "Your money request has been updated";
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
                    $mail->addAddress($_POST['email']);
                    $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $reject_reason ?: "Hello and good day,<br>Status of your money request has been updated by admin.<br>Thank you";
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
            $msg = "<div class='alert alert-success alert-dismissible'><a href='javascript:void(0)' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Money request status has been updated. $email_failed Thank you</div>";
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible'><a href='javascript:void(0)' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Failed to update this money request for Interpreter ! Kindly try again ...</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger alert-dismissible'><a href='javascript:void(0)' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Fill all the required fields and try again !</div>";
    }
    $_SESSION['returned_message'] = $msg;
    header('Location: ' . $_POST['redirect_url']);
}
// Add new complaint
if ($_POST['interpreter_id'] && isset($_POST['btn_add_money_request'])) {
    extract($_POST);
    $payable_date = $_POST['payable_date'] ? $_POST['payable_date'] : date("Y-m-d");
    $data = array(
        "interpreter_id" => $interpreter_id,
        "type_id" => $type_id,
        "loan_amount" => $loan_amount,
        "payable_date" => date('Y-m-01', strtotime($payable_date)),
        "is_notified" => $is_notified,
        "reason" => $obj->con->real_escape_string($reason),
        "created_by" => $_SESSION['userId'],
        "created_date" => date("Y-m-d H:i:s")
    );
    if ($job_type) {
        $data['job_type'] = $job_type;
    }
    if ($job_id) {
        $data['job_id'] = $job_id;
    }

    $done = $obj->insert("loan_requests", $data);
    if ($done) {
        $_SESSION['returned_message'] = "<div class='alert alert-success alert-dismissible'><a href='javascript:void(0)' class='close' data-dismiss='alert' aria-label='close'>&times;</a>New money request has been created successfully. Thank you</div>";
    } else {
        $_SESSION['returned_message'] = "<div class='alert alert-danger alert-dismissible'><a href='javascript:void(0)' class='close' data-dismiss='alert' aria-label='close'>&times;</a>Sorry, Could not create an invalid money request! Fill all the necessary fields and try again</div>";
    }
    header('Location: ' . $_POST['redirect_url']);
}