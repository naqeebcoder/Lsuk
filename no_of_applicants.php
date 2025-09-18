<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
?>
<div style="width:100%;height:100%;text-align:center;position:fixed;z-index:111111111111111;background-color: #ffffff;display:none;" id="load-img">
<div >
<img src="lsuk_system/images/loading.gif" style="padding:2rem;width:15rem;"  alt="Loading...">
<h2 style="color: #f44336;">Please Wait Until the Emails are Sent ..</h2>
</div>
</div>
<?php
$adminuser = $_SESSION['email'];
$array_bid_via = array(1 => '<button class="btn btn-xs btn-warning" type="button">Portal</button>', 2 => '<button class="btn btn-xs btn-success" type="button">LSUK App</button>', 3 => '<button class="btn btn-xs btn-primary" type="button">Android App</button>', 4 => '<button class="btn btn-xs btn-danger" type="button">iOS App</button>');
if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Finance' || $_SESSION['prv'] == 'Operator' || $_SESSION['interp_code'] == 'id-13') {
    include 'source/db.php';
    include 'source/class.php';
    include_once('source/function.php');
    include 'source/setup_sms.php';
    $setupSMS = new setupSMS;
    $page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    $limit = 20;
    $startpoint = ($page * $limit) - $limit;
    $table = @$_GET['table'];
    $tracking = @$_GET['tracking'];
    $get_job_details = $acttObj->read_specific("*", $table, "id=" . $tracking);
    $srcLang = $get_job_details['source'];
    if ($table == 'interpreter' || $table == 'telephone') {
        $db_assignDur=$get_job_details['assignDur'];
        if($db_assignDur>60){
            $hours=$db_assignDur / 60;
            if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
            $mins=$db_assignDur % 60;
            if($mins==00){
                $assignDur=sprintf("%2d $hr",$hours);  
            }else{
                $assignDur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
            }
        }else if($db_assignDur==60){
            $assignDur="1 Hour";
        }else{
            $assignDur=$db_assignDur." minutes";
        }
    }
    if ($table == 'interpreter') {
        $assignTime = $get_job_details['assignTime'];
        $assignDate = $get_job_details['assignDate'];
        $find_string = "You bided on a " . trim($assignDur) . " Face To Face interpreting at " . trim($get_job_details['postCode']) . " on " . $misc->dated($assignDate) . " at " . $assignTime .  ". Can you re confirm your availability?";
        $order_type = 1;
    }
    if ($table == 'telephone') {
        $assignTime = $get_job_details['assignTime'];
        $assignDate = $get_job_details['assignDate'];
        $find_string = "You bided on a " . trim($assignDur) . " Telephone assignment on " . $misc->dated($assignDate) . " at " . $assignTime .  ". Can you re confirm your availability?";
        $order_type = 2;
    }
    if ($table == 'translation') {
        $assignDate = $get_job_details['asignDate'];
        $find_string = "You bided on a Translation assignment on " . $misc->dated($assignDate) . ". Can you re confirm it?";
        $order_type = 3;
    }
?>
    <html class="no-js">

    <head>
        <?php include 'source/header.php'; ?>
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    </head>

    <body class="boxed">
        <div id="wrap">
            <?php include 'source/top_nav.php'; ?>
            <!-- begin page title -->
            <section id="page-title">
                <div class="container clearfix">
                    <h1>Applicants List</h1>
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="index.php">Home</a> &rsaquo;</li>
                        </ul>
                    </nav>
                </div>
            </section>
            <!-- begin page title -->

            <!-- begin content -->
            <section id="content" class="container-fluid clearfix">
                <section>
                    <h2>No of Applicants for <?php if (@$_GET['table'] == 'interpreter') {
                                                    echo 'Face to Face';
                                                }
                                                if (@$_GET['table'] == 'telephone') {
                                                    echo 'Voice Over';
                                                } ?></h2>
                    <?php $check_book = $acttObj->read_specific("interpreter_reg.name,$table.aloct_by", "$table,interpreter_reg", "$table.intrpName=interpreter_reg.id AND $table.id=" . $_GET['tracking']);
                    if ($check_book['name'] != '') {
                        $via = $check_book['aloct_by'] == 'Auto Allocated' ? ' Via system auto allocation' : ' by ' . $check_book['aloct_by'];
                        echo "<center><h2 style='color:red;'>This job is already assigned to " . $check_book['name'] . $via . "!</h4></center>";
                    } ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-primary">
                                    <th>Interpreter</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Date</th>
                                    <th>Gender</th>
                                    <th>Bid Via</th>
                                    <th width="14%">Message</th>
                                    <th align="center" width="125" align="center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $query = "SELECT bid.id,bid.job,bid.tabName,bid.dated,bid.allocated,bid.interpreter_id as int_id,bid.bid_type,bid.bid_via,interpreter_reg.name,interpreter_reg.email as int_email,interpreter_reg.contactNo,interpreter_reg.country,interpreter_reg.code,interpreter_reg.gender,CONCAT(interpreter_reg.buildingName,' ',interpreter_reg.line1,' ',interpreter_reg.line2,' ',interpreter_reg.line3,' ',interpreter_reg.city,' ',interpreter_reg.postCode) as address,bid.gender_status,bid.alternate_date FROM bid,interpreter_reg where bid.interpreter_id=interpreter_reg.id AND bid.tabName='$table' AND bid.job=$tracking LIMIT {$startpoint} , {$limit}";
                                $result = mysqli_query($con, $query);
                                $array_gender = array(1 => "Male", 2 => "Female", 3 => "No Preference");
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $country = $row['country'];
                                    $contactNo = $setupSMS->format_phone($row['contactNo'], $country);
                                    $interpreter_email = $row['int_email'];
                                    $interpreter_id = $row['int_id'];
                                    //count week jobs
                                    $today_date_app = date('Y-m-d');
                                    $firstday_app = date('Y-m-d', strtotime("this week"));

                                    $jobDate = $get_job_details['assignDate'];
                                    $jobDur = $get_job_details['assignDur'];
                                    $jobStart = date('H:i:s', strtotime($get_job_details['assignTime']));
                                    $getJobEnd = strtotime("+".$jobDur." minutes", strtotime($jobStart));
                                    $jobEnd = date('H:i:s', $getJobEnd);

                                    $check_jobs = $acttObj->read_all("assignDate,assignTime,assignDur", " (SELECT interpreter.assignTime,interpreter.assignDate,interpreter.assignDur FROM interpreter", " interpreter.intrpName=$interpreter_id AND interpreter.assignDate='$jobDate' AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.orderCancelatoin=0 UNION ALL SELECT telephone.assignTime,telephone.assignDate,telephone.assignDur FROM telephone WHERE telephone.intrpName=$interpreter_id AND telephone.assignDate='$jobDate' AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.orderCancelatoin=0) AS grp");
                                    $intrp_jobOverlapCount = 0;
                                    if($check_jobs->num_rows>0){
                                        while ($job_row = $check_jobs->fetch_assoc()) {
                                        $intrp_jobDate = $job_row['assignDate'];
                                        $intrp_jobDur = $job_row['assignDur'];
                                        $intrp_jobStart = date('H:i:s',strtotime($job_row['assignTime']));
                                        $intrp_getJobEnd = strtotime("+".$intrp_jobDur." minutes", strtotime($intrp_jobStart));
                                        $intrp_jobEnd = date('H:i:s', $intrp_getJobEnd);
                                        ?>
                                        <!-- <pre><?php echo $jobStart."<br>".$jobEnd; ?></pre>
                                        <pre><?php echo $intrp_jobStart."<br>".$intrp_jobEnd; ?></pre> -->
                                        <?php
                                        if(($jobStart >= $intrp_jobStart && $jobStart <= $intrp_jobEnd) || ($jobEnd >= $intrp_jobStart && $jobEnd <= $intrp_jobEnd) || ($intrp_jobStart >= $jobStart && $intrp_jobEnd <= $jobEnd)){
                                            $intrp_jobOverlapCount = $intrp_jobOverlapCount + 1;
                                        }
                                        
                                        }
                                    }

                                    if ($table != 'translation') {
                                        $count_jobs_app = "SELECT count(*) as jobs_done FROM $table WHERE assignDate BETWEEN '" . $firstday_app . "' AND '" . $today_date_app . "' and intrpName='$row[int_id]' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0 and intrp_salary_comit=0";
                                    } else {
                                        $count_jobs_app = "SELECT count(*) as jobs_done FROM $table WHERE asignDate BETWEEN '" . $firstday_app . "' AND '" . $today_date_app . "' and intrpName='$row[int_id]' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0 and intrp_salary_comit=0";
                                    }
                                    $res_count_jobs_app = mysqli_query($con, $count_jobs_app);
                                    $row_count_app = mysqli_fetch_assoc($res_count_jobs_app);
                                    $week_jobs_app = $row_count_app['jobs_done'];
                                    // count jobs week ends here
                                ?>
                                    <tr <?php echo $row['allocated'] == '1' ? 'style="border: 2px solid green"' : ''; ?>>
                                        <th><?php echo $row['name'] . '<br>' . $contactNo; ?></th>
                                        <td><?php echo $row['int_email'] . '<br>Gender: ' . $row['gender']; ?></td>
                                        <td><?php echo $row['address']; ?></td>
                                        <td><?php echo $misc->dated($row['dated']); ?></td>
                                        <td><?php echo !is_null($row['gender_status']) ? $array_gender[$row['gender_status']] : "Nil"; ?></td>
                                        <td><?=$array_bid_via[$row['bid_via']]?></td>
                                        <td><?php $get_sent_message = $acttObj->read_specific("*", "job_messages", "order_type=" . $order_type . " AND order_id=" . $tracking . " AND interpreter_id=" . $interpreter_id . " AND message_category=6");
                                            if (!empty($get_sent_message['id'])) {
                                                $job_message_response = !is_null($get_sent_message['response_date']) ? $misc->dated($get_sent_message['response_date']) : "";
                                                $array_message_status = array(0 => "<i title='Message not delivered to interpreter' class='fa fa-remove fa-2x text-danger'></i>", 1 => "<i title='Message delivered successfully' class='fa fa-check fa-2x text-success'></i>", 2 => "<i title='Interprerter responded back " . $job_message_response . "' class='fa fa-refresh fa-2x text-primary'></i>");
                                                $job_message_status = $array_message_status[$get_sent_message['status']];
                                                if ($get_sent_message['status'] == 2) {
                                                    $job_message_can_do = $get_sent_message['can_do'] == 1 ? "<br><small class='label label-success'>Available</small>" : "<br><small class='label label-danger'>Not Available</small>";
                                                } else {
                                                    $job_message_can_do = "";
                                                }
                                                echo "<i class='fa fa-check-circle fa-2x text-success' title='Message initiated to this interpreter on " . date("d-m-Y H:i:s", strtotime($get_sent_message['created_date'])) . "'></i> " . $job_message_status . $job_message_can_do;
                                                echo "<br><b class='text-primary'>" . date("d-m-Y H:i:s", strtotime($get_sent_message['created_date'])) . "</b>";
                                            } else {
                                                if ($row['bid_type'] == 2) {
                                                    echo "<span class='label label-danger'>Declined Bid</span>";
                                                }elseif ($row['bid_type'] == 3) {
                                                    echo "<span class='label label-warning'>Alternate Availability</span><br><span>".$row['alternate_date']."</span>";
                                                } else {
                                                    echo "<a data-name='" . $row['name'] . "' data-phone='" . $contactNo . "' data-country='" . $country . "' data-email='" . $interpreter_email . "' onclick='update_data(this,$tracking ,$interpreter_id);' href='#' title='Send message to this interpreter' data-toggle='modal' data-target='#send_msg_modal' class='btn btn-primary btn-xs'><i class='fa fa-send' style='color: white;padding: 4px;'></i></a>";
                                                }
                                            } ?>
                                        </td>
                                        <td align="center" <?php echo $row['allocated'] == '1' ? 'style="background-color:green"' : ''; ?>>
                                            <?php if ($row['allocated'] != '1') {
                                                if ($check_book['name'] == '') {
                                                    if ($week_jobs_app >= '5' && $_SESSION['prv'] != 'Management') { ?>
                                                        <span>Already has <?php echo $week_jobs_app; ?> jobs this week</span>
                                                    <?php } else {
                                                            if ($row['bid_type'] == 2) {
                                                                echo "<span class='label label-danger'>Declined Bid</span>";
                                                            }elseif ($row['bid_type'] == 3) {
                                                                echo "<span class='label label-warning'>Alternate Availability</span><br><span>".$row['alternate_date']."</span>";
                                                            } else { ?>
                                                                <a href="javascript:void(0)" <?php if ($row['allocated'] != '1' && $_SESSION['prv'] == 'Management') { ?> id="assignBtn" data-id="<?php echo $interpreter_id; ?>" data-email="<?php echo  $interpreter_email; ?>" <?php } ?>>
                                                                <input width="25" type="image" src="lsuk_system/images/icn_add_user.png" title="Assign Interpreter"></a>
                                                                <?php echo ($intrp_jobOverlapCount>0?"<div class='alert alert-danger' role='alert'>Time Conflict Found</div>":"") ?>
                                                        <?php }
                                                        } ?>
                                                <?php } else { ?>
                                                    <span>Job is booked</span>
                                                <?php }
                                            } else { ?>
                                                <span style="color:white">Auto Allocated</span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8"><?php echo pagination($con, $table, $query, $limit, $page); ?></td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </section>
                <!-- end table -->


                <hr>
            </section>
            <!-- end content -->
            <!--Send message modal-->
            <div id="send_msg_modal" class="modal" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;">
                <div class="modal-dialog modal-md">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header alert-info">
                            <button type="button" class="close" data-dismiss="modal">×</button>
                            <h4 class="modal-title">Send Message to Interpreter</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Interpreter Name : <span id="write_interpreter_name"></span></label><br>
                                <label>Interpreter Country : <span id="write_interpreter_country"></span></label><br>
                                <label>Contact Number : <input id="write_interpreter_phone" type="text" placeholder="Contact Number"></label><br>
                                <label>Email Address : <span id="write_interpreter_email"></span></label>
                            </div>
                            <div class="form-group">
                                <label>Write message details</label><b class="character_count pull-right"></b>
                                <input type="hidden" id="write_interpreter_id" />
                                <textarea id="message_body" rows="5" class="form-control" placeholder="Write message here ..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="send_text_message(this)" class="btn btn-info" id="btn_send_text_message">Send Message</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>

                </div>
            </div>
            <!--Send message modal ends -->
        </div>
        <!-- end container -->
    </body>

    <!-- Mirrored from ixtendo.com/themes/exquiso-html/about-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Jan 2016 09:49:13 GMT -->

    </html>
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script>
        function MM_openBrWindow(theURL, winName, features) {
            window.open(theURL, winName, features);
        }

        function refreshParent() {
            window.opener.location.reload();
        }
        $(document).on('click','#assignBtn',function(){
            if (confirm('Are you sure to assign this job?')){
                $('#load-img').show();
                var inter_id = $(this).attr("data-id");
                var formURL = "/lsuk_system/ajaxsendassignemails.php?jobid=<?php echo $tracking; ?>&table=<?php echo $table; ?>&int_id=" + inter_id;
                $.ajax({
                    url: formURL,
                    type: "GET",
                    success: function(strData, textStatus, jqXHR) {
                        if (strData) {
                            alert("Job has been successfully allocated.");
                            // MM_openBrWindow("lsuk_system/reports_lsuk/pdf/new_timesheet.php?update_id=<?php echo $tracking; ?>&table=<?php echo $table; ?>&down&emailto=" + int_email+"&send_sms=1", '_blank', 'scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10');
                            window.close();
                            window.onunload =  window.opener.location.reload();
                        } else {
                            alert("SendAssignEmails: no data OK");
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert("SendAssignEmails()- Something wrong with Jquery");
                    }
                });
                $('#load-img').hide();
            }
        });
        // function SendAssignEmails(inter_id, int_email) {
        //     formURL = "/lsuk_system/ajaxsendassignemails.php?jobid=<?php echo $tracking; ?>&table=<?php echo $table; ?>&int_id=" + inter_id;

        //     $.ajax({
        //         url: formURL,
        //         type: "GET",

        //         success: function(strData, textStatus, jqXHR) {
        //             if (strData) {
        //                 alert("Job has been successfully allocated.");
        //                 // MM_openBrWindow("lsuk_system/reports_lsuk/pdf/new_timesheet.php?update_id=<?php echo $tracking; ?>&table=<?php echo $table; ?>&down&emailto=" + int_email+"&send_sms=1", '_blank', 'scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10');
        //                 window.close();
        //                 window.onunload = refreshParent;
        //             } else {
        //                 alert("SendAssignEmails: no data OK");
        //             }
        //         },
        //         error: function(jqXHR, textStatus, errorThrown) {
        //             alert("SendAssignEmails()- Something wrong with Jquery");
        //         }
        //     });

        //     function refreshParent() {
        //         window.opener.location.reload();
        //     }
        // }

        function update_data(element, order_id, interpreter_id) {
            $("#btn_send_text_message").removeClass("hidden");
            var MESSAGE_BODY = "<?=$find_string?>";
            $('#write_interpreter_id').val(interpreter_id);
            $('#write_interpreter_name').text($(element).attr('data-name'));
            $('#write_interpreter_phone').val($(element).attr('data-phone'));
            $('#write_interpreter_country').text($(element).attr('data-country'));
            $('#write_interpreter_email').text($(element).attr('data-email'));
            $('#message_body').val(MESSAGE_BODY);
            $(".character_count").text("Characters: " + $("#message_body").val().length + "/120");
        }

        function send_text_message(element) {
            $(element).addClass("hidden");
            $.ajax({
                url: 'lsuk_system/process/third_party_apis.php',
                method: 'post',
                dataType: 'json',
                data: {
                    order_id: "<?= $tracking ?>",
                    order_type: "<?= $order_type ?>",
                    interpreter_id: $('#write_interpreter_id').val(),
                    interpreter_phone: $('#write_interpreter_phone').val(),
                    interpreter_country: $('#write_interpreter_country').text(),
                    interpreter_email: $('#write_interpreter_email').text(),
                    message_body: $('#message_body').val() + "\nMore Details",
                    message_category: 6,//bided job
                    send_text_message: 1
                },
                success: function(data) {
                    if (data['status'] == 1) {
                        $('#write_interpreter_id').val("");
                        $('#write_interpreter_name').text("");
                        $('#write_interpreter_phone').val("");
                        $('#write_interpreter_country').text("");
                        $('#message_body').val("");
                        location.reload();
                    } else {
                        alert(data['message']);
                    }
                },
                error: function(data) {
                    alert("Error code : " + data.status + " , Error message : " + data.statusText);
                }
            });
        }
        
        $(document).ready(function() {
            var textarea = $("#message_body");
            var characterCount = $(".character_count");
            var initialCharCount = textarea.val().length;
            characterCount.text("Characters: " + initialCharCount + "/120");
            textarea.on("input", function() {
                var inputText = textarea.val();
                if (inputText.length > 120) {
                    $(".character_count").addClass("text-danger");
                    inputText = inputText.substring(0, 120);
                    textarea.val(inputText);
                } else {
                    $(".character_count").removeClass("text-danger");
                }
                var currentCharCount = inputText.length;
                characterCount.text("Characters: " + currentCharCount + "/120");
            });
        });
    </script>


<?php
} else {
    echo 'Sorry your are not allowed!';
}
?>