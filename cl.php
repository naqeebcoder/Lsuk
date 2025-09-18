<?php
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
error_reporting(0);
include 'source/db.php';
include 'source/class.php';
include_once('source/function.php');
$is_valid = 0;
$array_order_types = array(1 => "Face To Face", 2 => "Telephone", 3 => "Translation");
$apply_id = @$_GET['i'];
$can_access = false;
$submitted = false;
if (isset($_POST['verify_access'])) {
    $submitted = true;
    $password = trim($_POST['verification_password']);
    if ($password) {
        $row = $acttObj->read_specific("password", "client_messages", "id='" . $apply_id . "' AND password='" . $password . "' AND status=1");
        if (empty($row['password']) || ($row['password'] != $password)) {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">This timesheet verification cannot be accessed. Please try again with valid password!</div>';
        } else {
            $can_access = true;
        }
    }
}

if (empty($apply_id)) {
    $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">You must use a valid URL with Job ID. Please use links only sent to your registered number!</div>';
} else {
    if ($can_access) {
        $get_application = $acttObj->read_specific("*", "client_messages", "id=" . $apply_id . " AND message_category IN (1)");
        if (!empty($get_application['id'])) {
            if (!is_null($get_application['response_date'])) {
                $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">Sorry! You have already responded against this Job on ' . $misc->date_time($get_application['response_date']) . '. Thank you</div>';
            } else {
                $is_valid = 1;
                $array_table = array(1 => "interpreter", 2 => "telephone", 3 => "translation");
                $get_job_details = $acttObj->read_specific("*", $array_table[$get_application['order_type']], "id=" . $get_application['order_id']);
                if ($get_application['order_type'] == 1) {
                    $assignment_type = $get_job_details['interp_cat'] == 12 ? $get_job_details['assignIssue'] : $acttObj->read_specific("ic_title","interp_cat","ic_id=" . $get_job_details['interp_cat'])['ic_title'];
                }
                if ($get_application['order_type'] == 2) {
                    $assignment_type = $acttObj->read_specific("c_title", "comunic_types", "c_id=" . $get_job_details['comunic'])['c_title'];
                }
                if ($get_application['order_type'] == 3) {
                    $assignment_type = $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $get_job_details['docType'])['tc_title'];
                    $get_job_details['assignDate'] = $get_job_details['asignDate'];
                } else {
                    $db_assignDur = $get_application['order_type'] == 1 ? $get_job_details['hoursWorkd'] * 60 : $get_job_details['hoursWorkd'];
                    if ($db_assignDur > 60) {
                        $hours = $db_assignDur / 60;
                        if (floor($hours) > 1) {
                            $hr = "hours";
                        } else {
                            $hr = "hour";
                        }
                        $mins = $db_assignDur % 60;
                        if ($mins == 00) {
                            $assignment_duration = sprintf("%2d $hr", $hours);
                        } else {
                            $assignment_duration = sprintf("%2d $hr %02d minutes", $hours, $mins);
                        }
                    } else if ($db_assignDur == 60) {
                        $assignment_duration = "1 Hour";
                    } else {
                        $assignment_duration = $db_assignDur . " hour(s)";
                    }
                }
            }
        } else {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">This job is no longer available! Thank you</div>';
        }
    } else {
        $append_try_again = $submitted ? '<br><br><a class="btn btn-warning" href="cl.php?i=' . $apply_id . '">Try Again</a>' : '';
        $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">You must use a valid link with verification password to perform this action! Thank you' . $append_try_again . '</div>';
    }
}

if (isset($_POST['btn_response'])) {
    $is_verified = $_POST['is_verified'];
    if ($_POST['verification_password']) {
        $is_valid = 0;
        $get_application = $acttObj->read_specific("*", "client_messages", "id=" . $apply_id . " AND message_category IN (1)");
        if (!empty($get_application['id'])) {
            if (!is_null($get_application['response_date'])) {
                $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">Sorry! You have already responded against this Job on ' . $misc->date_time($get_application['response_date']) . '. Thank you</div>';
            } else {
                $success_msg =  'Your response against this job has been received. Thank you';
                if ($get_application['password'] == trim($_POST['verification_password'])) {
                    $update_array = array("status" => 2, "is_verified" => $is_verified, "response_date" => date("Y-m-d H:i:s"));
                    if ($_POST['response_message']) {
                        $update_array['response_message'] = trim($_POST['response_message']);
                    }
                    $acttObj->update("client_messages", $update_array, array("id" => $apply_id));
                    $msg = '<div class="alert alert-success col-md-6 col-md-offset-3 text-center">' . $success_msg . '</div>';
                } else {
                    $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">You must use your verification password to perform this action! Thank you</div>';
                }
            }
        } else {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">This job is no longer available! Thank you</div>';
        }
    } else {
        $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">You must use your verification password to perform this action! Thank you</div>';
    }
}
$original_message = $get_application['password'];
?>

<head>
    <?php include 'source/header.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>

<body class="boxed">
    <div id="wrap">
        <?php include 'source/top_nav.php'; ?>

        <!-- begin content -->
        <section id="content" class="container-fluid clearfix">
            <!-- begin table -->
            <section style="overflow-x: auto;">
                <div class="col-md-12">
                    <h4 class="text-center"><b>Confirm <?=$array_order_types[$get_application['order_type']]?:""?> Job Timesheet claimed duration</b></h4>
                    <?php echo !empty($msg) && isset($msg) ? $msg : "";?>
                </div>
                <?php if ($_POST['verification_password']) {
                    if ($is_valid == 1) { ?>
                        <div class="col-md-6">
                        <h4>Confirm interpreter's claimed timesheet duration for <?=$array_order_types[$get_application['order_type']]?:"this"?> Job.</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Job ID #</th>
                                    <td><?= $get_job_details['id'] ?></td>
                                </tr>
                                <tr>
                                    <th><?=$get_application['order_type']==3?'Document':'Assignment'?> Type</th>
                                    <td><?= $assignment_type ?></td>
                                </tr>
                                <tr>
                                    <th>Assignment Date Time</th>
                                    <td><?= $get_job_details['assignDate'] ? $misc->dated($get_job_details['assignDate']) . ' ' . $get_job_details['assignTime'] : "---" ?></td>
                                </tr>
                                <?php if ($get_application['order_type'] != 3) { ?>
                                    <tr>
                                        <th>Assignment Duration</th>
                                        <td><?=$assignment_duration?></td>
                                    </tr>
                                <?php }
                                if ($get_application['order_type'] == 1) { ?>
                                    <tr>
                                        <th>Assignment PostCode</th>
                                        <td><?=$get_job_details['postCode']?:"Nil"?></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <form method="post" action="#">
                                <input type="hidden" name="verification_password" value="<?=$_POST['verification_password']?>"/>
                                <div class="form-group"><br>
                                    <p class="text-muted"><b>Verification password:</b><br><?=$original_message?></p><hr>
                                    <p class="text-danger">
                                        This is only to verify the interpreter's requested timesheet.<br>Please add your response to ensure that the requested interpreter timesheet hours/minutes are correct.
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label onclick="show_reply(0);" for="yes" class="btn btn-success"><input id="yes" name="is_verified" type="radio" value="1" checked /> Correct Timesheet</label>
                                    <label onclick="show_reply(1);" for="no" class="btn btn-danger"><input id="no" name="is_verified" type="radio" value="0" /> Incorrect Timesheet</label>
                                </div>
                                <div class="form-group div_alternative hidden">
                                    <textarea class="form-control" name="response_message" id="response_message" rows="3" placeholder="Write a message regarding correct timesheet (if any)"></textarea>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="btn_response" value="Update Timesheet Verification" class="btn btn-primary" />
                                </div>
                            </form>
                        </div>
                    <?php }
                } else { ?>
                    <center>
                        <h4>Please use your verification password to proceed</h4>
                    </center>
                    <div class="col-md-4 col-md-offset-4">
                        <form id="login" method="post" action="#">
                            <div class="form-group">
                                <input type="password" name="verification_password" class="form-control" id="verification_password" placeholder="Enter your verification password" required />
                            </div>
                            <div class="form-group">
                                <input type="submit" name="verify_access" value="Verify Access" class="btn btn-primary" />
                            </div>
                        </form>
                    </div>
                <?php } ?>
            </section>
        </section>
        <!-- end content -->

        <!-- begin footer -->
        <?php include 'source/footer.php'; ?>
        <!-- end footer -->
    </div>
    <!-- end container -->
</body>
<script>
    function show_reply(type) {
        if (type == 1) {
            $('.div_alternative').removeClass("hidden");
        } else {
            $('.div_alternative').addClass("hidden");
        }
    }
</script>

</html>