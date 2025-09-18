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
$interp_id = $_SESSION['web_userId'];

if (isset($_POST['login'])) {
    $password = $_POST['loginPass'];
    $username = $_POST['loginEmail'];
    if ($username && $password) {
        $row = $acttObj->read_specific("id,name,contactNo,email,code,gender,address", "interpreter_reg", "TRIM(email)='" . $username . "' AND BINARY TRIM(password)='" . $password . "' AND deleted_flag=0 AND is_temp=0");
        $username = $row['name'];
        $id = $row['id'];
        $email = $row['email'];
        $contactNo = $row['contactNo'];
        $gender = $row['gender'];
        $interp_code = $row['code'];
        $address = $row['address'];
        if (empty($id)) {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">Entered email or password is incorrect. Please try again!</div>';
        } else {
            $_SESSION['web_UserName'] = $username;
            $_SESSION['web_userId'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['web_contactNo'] = $contactNo;
            $_SESSION['web_address'] = $address;
            $_SESSION['gender'] = $gender;
            $_SESSION['interp_code'] = $interp_code;
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            //header('location:$url'); 
            $result_url = "co.php?i=" . $apply_id;
            if ($url != $result_url) {
                echo "<script>setTimeout(function(){ window.location.href='$result_url'; }, 3500);</script>";
            } else {
                echo "<script>window.location.href='$result_url';</script>";
            }
        }
    }
}

if (empty($apply_id)) {
    $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">You must use a valid URL with Job ID. Please use links only sent to your registered number!</div>';
} else {
    if (isset($_SESSION['web_userId'])) {
        $get_application = $acttObj->read_specific("*", "job_messages", "id=" . $apply_id . " AND message_category IN (1,6,7)");
        if (!empty($get_application['id'])) {
            if (!is_null($get_application['response_date'])) {
                $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">Sorry! You have already responded against this Job on ' . $misc->date_time($get_application['response_date']) . '. Thank you</div>';
            } else {
                if ($get_application['interpreter_id'] == $_SESSION['web_userId']) {
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
                        $db_assignDur = $get_job_details['assignDur'];
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
                            $assignment_duration = $db_assignDur . " minutes";
                        }
                    }
                } else {
                    $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">You cannot use Clickable link of other interpreters. Please use links only sent to your registered number!</div>';
                }
            }
        } else {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">No response is required to update against this job! Thank you</div>';
        }
    } else {
        $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">You must login to perform this action! Thank you</div>';
    }
}

if (isset($_POST['btn_response'])) {
    $can_do = $_POST['can_do'];
    $resp_message = $_POST['response_message'];
    
    $datee = isset($_POST['alt_date']) ? $_POST['alt_date'] : '';
    if (isset($_SESSION['web_userId'])) {
        $is_valid = 0;
        $get_application = $acttObj->read_specific("*", "job_messages", "id=" . $apply_id . " AND message_category IN (1,6,7)");
        //$response_message = $get_application['response_message'];
        if (!empty($get_application['id'])) {
            if (!is_null($get_application['response_date'])) {
                $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">Sorry! You have already responded against this Job on ' . $misc->date_time($get_application['response_date']) . '. Thank you</div>';
            } else {
                if ($get_application['message_category'] == 1) {
                    if($can_do == 1 || $can_do == 3){
                        $success_msg = 'Your response against this job has been received. We will send you the timesheet when we allocate you the job. Thank you';    
                    }else{
                        $success_msg = 'Thanks for notifying us that you are <b class="text-danger">NOT AVAILABLE</b> for this job at ' . $misc->dated($get_job_details['assignDate']) . ' ' . $get_job_details['assignTime'] . '</a>';    
                    }
                } else {
                    $success_msg =  'Your response against this job has been received. Thank you';
                }
                if ($get_application['interpreter_id'] == $_SESSION['web_userId']) {

                    $response_message = "\n" . $resp_message . "\n";
                    if ($datee) {
                        $date = new DateTime($datee);
                        $formatted_date = $date->format('d/m/Y H:i');
                        $response_message .= "\nI will be available on this alternate date\nDATE: " . $formatted_date;
                    }
                    $acttObj->update("job_messages", array("status" => 2, "can_do" => $can_do, "response_message" => $response_message,"response_date" => date("Y-m-d H:i:s")), array("id" => $apply_id));
                    $msg = '<div class="alert alert-success col-md-6 col-md-offset-3 text-center">' . $success_msg . '</div>';
                } else {
                    $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">You cannot use Clickable link of other interpreters. Please use links only sent to your registered number!</div>';
                }
            }
        } else {
            $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">No response is required to update against this job! Thank you</div>';
        }
    } else {
        $msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center">You must login to perform this action! Thank you</div>';
    }
}
$keyword = "More Details";
$original_message = strstr($get_application['message_body'], $keyword, true);
if ($original_message === false) {
    $original_message = $get_application['message_body'];
}
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
                    <h4 class="text-center"><b>Confirm <?=$array_order_types[$get_application['order_type']]?:""?> Job Availability</b></h4>
                    <?php echo !empty($msg) && isset($msg) ? $msg : "";?>
                </div>
                <?php if (isset($_SESSION['web_userId'])) {
                    if ($is_valid == 1) { ?>
                        <div class="col-md-6">
                        <h4>Confirm your availability for <?=$array_order_types[$get_application['order_type']]?:"this"?> job.</h4>
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
                                <div class="form-group">
                                    <p class="text-muted"><b>Original Message:</b><br><?=$original_message?></p><hr>
                                    <p class="text-danger">
                                        <?=$get_application['message_category'] == 1 ? 'This is only to check your availability.<br>A timesheet / email will be sent when a job is allocated.' : 'This is only to ensure you are availabile and attending this tomorrow.'?></b>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label onclick="show_danger(0);show_reply(0);show_clander(0)" for="yes" class="btn btn-default"><input id="yes" name="can_do" type="radio" value="1" checked /> <?=$get_application['message_category'] == 1 ? "Yes - Can Do" : "I will be attending";?></label>
                                    <label   onclick="show_clander(0);show_reply(1);show_danger(0)" for="no" class="btn btn-default"><input id="no" name="can_do" type="radio" value="0" /> <?=$get_application['message_category'] == 1 ? "No - Not Available" : "No, I can't Attend";?></label>
                                    <?php if (in_array($get_application['message_category'], array(1, 6))) { ?>
                                        <label onclick="show_danger(0);show_reply(1);show_clander(1)" for="alternative" class="btn btn-warning"><input id="alternative" name="can_do" type="radio" value="3" /> Alternative Availability</label>
                                    <?php } ?>
                                </div>
                                <div class="alert alert-danger form-group div_danger hidden">
                                    <p class="text-danger">
                                        <b>Note: There may be deduction for no attendance!</b>
                                    </p>
                                </div>
                                <div class="form-group div_alt_cal hidden">
                                    <!-- <input class="form-control" type="date" id="alt_date" name="alt_date" > -->
                                    <input class="form-control" type="datetime-local" id="alt_date" name="alt_date">

                                </div>
                                <div class="form-group div_alternative hidden">
                                    <textarea class="form-control" name="response_message" id="response_message" rows="3" placeholder="Write your message here" ></textarea>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="btn_response" value="Confirm Availability" class="btn btn-primary" />
                                </div>
                            </form>
                        </div>
                    <?php }
                } else { ?>
                    <center>
                        <h4>Kindly login to proceed</h4>
                    </center>
                    <div class="col-md-6 col-md-offset-3">
                        <form id="login" method="post" action="#">
                            <div class="form-group">
                                <input type="text" name="loginEmail" class="form-control" id="loginEmail" value="" placeholder="Enter your email" required />
                            </div>
                            <div class="form-group">
                                <input type="password" name="loginPass" class="form-control" id="loginPass" placeholder="Enter your password" required />
                            </div>
                            <div class="form-group">
                                <input type="submit" name="login" value="Login" class="btn btn-primary" />
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
    function show_danger(type) {
        if (type == 1) {
            $('.div_danger').removeClass("hidden");
        } else {
            $('.div_danger').addClass("hidden");
        }
    }
    function show_reply(type) {
        if (type == 1) {
            $('.div_alternative').removeClass("hidden");
        } else {
            $('.div_alternative').addClass("hidden");
        }
    }
    function show_clander(type){
        
        if (type == 1) {
            $('.div_alt_cal').removeClass("hidden");
        } else {
            $('.div_alt_cal').addClass("hidden");
        }
    }
</script>

</html>