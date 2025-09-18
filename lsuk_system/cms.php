<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
} ?>
<?php
include 'db.php';
include 'class.php'; ?>

<!doctype html>
<html lang="en">

<head>
    <title>LSUK CMS</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style>
        .nav-tabs>li.active>a,
        .nav-tabs>li.active>a:focus,
        .nav-tabs>li.active>a:hover {
            color: #fbfbfb;
            background-color: #337ab7;
        }

        .nav-tabs_custom {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <?php include 'nav2.php'; ?>
    <!-- end of sidebar -->
    <section class="container-fluid" style="overflow-x:auto">
        <center>
            <h2 class="text-center"><span class="label label-primary">LSUK Content Management</span></h2>
        </center>
        <ul class="nav nav-tabs nav-tabs_custom nav-stacked col-md-2">
            <li id="li_tab_auto_replies" class="active"><a data-toggle="tab" href="#tab_auto_replies"><i class="fa fa-reply"></i> Auto Replies</a></li>
            <li><a href="javascript:void(0)" onClick="popupwindow('manage_bookings.php', 'title', 800, 600);"><i class="fa fa-list"></i> Booking Forms</a></li>
            <li id="li_tab_cancellation"><a data-toggle="tab" href="#tab_cancellation"><i class="fa fa-remove"></i> Cancellation</a></li>
            <li id="li_tab_amendment"><a data-toggle="tab" href="#tab_amendment"><i class="fa fa-undo"></i> Amendments</a></li>
            <li id="li_tab_delete"><a data-toggle="tab" href="#tab_delete"><i class="fa fa-trash"></i> Job Delete</a></li>
            <li id="li_tab_corona"><a data-toggle="tab" href="#tab_corona" class="text-danger"><i class="fa fa-circle-o"></i> <b>* Corona *</b></a></li>
            <li><a href="javascript:void(0)" onClick="popupwindow('timesheet_policy.php', 'title', 800, 600);"><i class="fa fa-exclamation-circle"></i> Timesheet Policies</a></li>
        </ul>

        <div class="tab-content col-md-10">
            <div id="tab_auto_replies" class="tab-pane fade in active"><br>
                <table class="table table-bordered">
                    <thead class="bg-primary">
                        <th width="5%">S.No</th>
                        <th width="25%">Type</th>
                        <th>Message</th>
                        <th width="5%">Action</th>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        $auto_replies_q = $acttObj->read_all("*", "auto_replies", NULL);
                        while ($row_auto_rep = mysqli_fetch_assoc($auto_replies_q)) {
                            if (isset($_POST['btn_auto_reply' . $i])) {
                                $auto_id = $_POST['auto_id' . $i];
                                $auto_msg = $_POST['txt_auto_reply' . $i];
                                if ($acttObj->editFun('auto_replies', $auto_id, 'message', $auto_msg)) { ?>
                                    <script>
                                        document.getElementById('<?php echo "txt_auto_reply_id" . $i; ?>').value = '<?php echo $auto_msg; ?>';
                                    </script>
                            <?php $msg = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Message has been successfully updated .</b></span><br><br>';
                                } else {
                                    $msg = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to update this message !</b></span><br><br>';
                                }
                            }
                            ?>
                            <form action="" method="post" class="register" enctype="multipart/form-data">
                                <tr>
                                    <td align="left"><?php echo $i++; ?><input type="hidden" name="<?php echo 'auto_id' . $i; ?>" value="<?php echo $row_auto_rep['id']; ?>" /> </td>
                                    <td align="left"><?php echo $row_auto_rep['type']; ?> </td>
                                    <td align="left"><textarea data-toggle="tooltip" data-placement="top" title="Place <br> in message body to keep a 'BREAK LINE'" class="w3-input w3-border w3-border-blue" style="resize:none;" name="<?php echo 'txt_auto_reply' . $i; ?>" id="<?php echo 'txt_auto_reply_id' . $i; ?>"><?php echo $row_auto_rep['message']; ?></textarea></td>
                                    <td align="left">
                                        <button data-toggle="tooltip" data-placement="top" type="submit" title="Edit this message" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue w3-hover-blue" name="<?php echo 'btn_auto_reply' . $i; ?>"><i class="fa fa-edit"></i></button>
                                    </td>
                                </tr>
                            </form>
                        <?php } ?>

                        <div class="col-md-8 col-md-offset-2">
                            <?php if (isset($msg) && !empty($msg)) {
                                echo $msg;
                            } ?><br />
                        </div>
                    </tbody>
                </table>
            </div>
            <div id="tab_delete" class="tab-pane fade"><br>
                <h2>
                    Manage Delete Reasons    
                </h2>
                <table class="table table-bordered">
                    <thead class="bg-danger">
                        <th width="5%">S.No</th>
                        <th width="20%">Type</th>
                        <th>Reason</th>
                        <th width="10%">Action</th>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        ?>
                        <?php
                        // Add New
                        $reasons_q = $acttObj->read_all("*", "reasons_job_delete", "1=1");
                        if (isset($_POST['btn_add_delete'])) {
                                $new_reason = mysqli_escape_string($con,$_POST['reason_new']);
                                $new_text_for = $_POST['text_for_new'];
                                $for_table = $_POST['for_table'];
                                $acttObj->insert("reasons_job_delete", ["detail" => $new_reason, "text_for" => $new_text_for,"for_table"=>$for_table]);
                                echo '<script>
                                                    // Save the active tab in localStorage
                                                    localStorage.setItem("activeTab", "tab_delete");
                                                    
                                                    // Reload the page
                                                    window.location.href = window.location.href;
                                                </script>';
                            }
                        while ($row = mysqli_fetch_assoc($reasons_q)) {
                            
                            if (isset($_POST['btn_update_delete' . $i])) {
                                    $id = $_POST['id' . $i];
                                    $reason = mysqli_escape_string($con,$_POST['reason' . $i]);
                                    $text_for = $_POST['text_for' . $i];
                                    $for_table = $_POST['for_table' . $i];
                                    $acttObj->update("reasons_job_delete", ["detail" => $reason, "text_for" => $text_for,"for_table"=>$for_table], "id=$id");
                                    echo '<script>
                                                    // Save the active tab in localStorage
                                                    localStorage.setItem("activeTab", "tab_delete");
                                                    
                                                    // Reload the page
                                                    window.location.href = window.location.href;
                                                </script>';
                                }
                            if (isset($_POST['btn_restore_delete' . $i])) {
                                    $id = $_POST['id' . $i];
                                    $acttObj->update("reasons_job_delete", ["deleted_flag" => 0], "id=$id");
                                    echo '<script>
                                                    // Save the active tab in localStorage
                                                    localStorage.setItem("activeTab", "tab_delete");
                                                    
                                                    // Reload the page
                                                    window.location.href = window.location.href;
                                                </script>';
                                }
                                if (isset($_POST['btn_delete_delete' . $i])) {
                                    $id = $_POST['id' . $i];
                                    $acttObj->update("reasons_job_delete", ["deleted_flag" => 1], "id=$id");
                                    echo '<script>
                                                    // Save the active tab in localStorage
                                                    localStorage.setItem("activeTab", "tab_delete");
                                                    
                                                    // Reload the page
                                                    window.location.href = window.location.href;
                                                </script>';
                                }
                                $i++;
                        } $i=1;
                            ?>l
                        <form method="post">
                            <tr>
                                <td>ADD NEW</td>
                                <td>
                                    <div class="form-group" >
                                    <select name="text_for_new" class="form-control">
                                        <option value="cl">Client</option>
                                        <option value="lsuk">LSUK</option>
                                    </select>
                                    </div>
                                    <div class="form-group">
                                    <select name="for_table" class="form-control">
                                            <option value="orders" >orders</option>
                                            <option value="interpreter_reg" >interpreter</option>
                                            <option value="comp_reg" >Company</option>
                                            <option value="expence" >Expence</option>
                                    </select>
                                    </div>
                                </td>
                                <td>
                                    <textarea name="reason_new" class="form-control" rows="2" required></textarea>
                                </td>
                                <td>
                                    <button name="btn_add_delete" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        </form>
                        <?php
                        $reasons_q = $acttObj->read_all("*", "reasons_job_delete", "1=1");
                        while ($row = mysqli_fetch_assoc($reasons_q)) {
                            
                        ?>
                            <form method="post">
                                <tr  
                                <?php if($row["deleted_flag"]) { ?>
                                style = "background:#f2dede9e;"
                                <?php
                                } ?>
                                >
                                    <td><?= $i ?></td>
                                    <td>
                                        <select name="text_for<?= $i ?>" class="form-control">
                                            <option value="cl" <?= $row['text_for'] == 'cl' ? 'selected' : '' ?>>Client</option>
                                            <option value="lsuk" <?= $row['text_for'] == 'lsuk' ? 'selected' : '' ?>>LSUK</option>
                                        </select>
                                        <select name="for_table<?= $i ?>" class="form-control">
                                            <option value="orders" <?= $row['for_table'] == 'orders' ? 'selected' : '' ?>>orders</option>
                                            <option value="interpreter_reg" <?= $row['for_table'] == 'interpreter_reg' ? 'selected' : '' ?>>interpreter</option>
                                            <option value="comp_reg" <?= $row['for_table'] == 'comp_reg' ? 'selected' : '' ?>>Company</option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea name="reason<?= $i ?>" class="form-control" rows="2"><?= $row['detail'] ?></textarea>
                                    </td>
                                    <td>
                                        <input type="hidden" name="id<?= $i ?>" value="<?= $row['id'] ?>">
                                        <button name="btn_update_delete<?= $i ?>" class="btn btn-sm btn-success"><i class="fa fa-save"></i></button>
                                        <?php 
                                            if(!$row['deleted_flag']){ ?>
                                                <button name="btn_delete_delete<?= $i ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                            <?php } else {?>
                                                <button name="btn_restore_delete<?= $i ?>" class="btn btn-sm btn-warning"><i class="fa fa-undo"></i></button>
                                                <?php }?>

                                    </td>
                                </tr>
                            </form>
                        <?php $i++;
                        } ?>
                    </tbody>
                </table>
            </div>

            <div id="tab_cancellation" class="tab-pane fade"><br>
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab_co"><i class="fa fa-circle-o"></i> Cancellation Reasons LSUK</a></li>
                    <li><a data-toggle="tab" href="#tab_cr"><i class="fa fa-circle-o"></i> Cancellation Reasons Client</a></li>
                </ul>

                <div class="tab-content col-md-12">
                    <div id="tab_co" class="tab-pane fade in active"><br>
                        <?php if (isset($_POST['btn_add_cancel'])) {
                            $value = mysqli_escape_string($con, $_POST['value']);
                            $cd_effect = $_POST['effect'];
                            $cd_effect_interp = $_POST['effect_interp'];
                            $cd_for = $_POST['cd_for'];
                            $type = $_POST['type'];
                            $chk = $acttObj->read_specific('count(cd_id) as counter', 'cancellation_drops', 'cd_title="' . $value . '" and cd_effect="' . $cd_effect . '" and cd_for="' . $cd_for . '"and effect_interp="' . $cd_effect_interp . '"'); ?>

                            <script>
                                document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                document.getElementById('tab_cancellation').classList.add('in', 'active');
                                document.getElementById('li_tab_auto_replies').classList.remove('active');
                                document.getElementById('li_tab_cancellation').classList.add('active');
                            </script>
                        <?php if ($chk['counter'] == 0) {
                                $data_cancel = array('cd_title' => $value, 'cd_effect' => $cd_effect, 'cd_effect_interp' => $cd_effect_interp, 'cd_for' => $cd_for, 'type' => $type);

                                if ($acttObj->insert('cancellation_drops', $data_cancel)) {
                                    $msg_new_cancel = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Record has been successfully added .</b></span><br><br>';
                                } else {
                                    $msg_new_cancel = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to add new record !</b></span><br><br>';
                                }
                            } else {
                                $msg_new_cancel = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Same record already exists !</b></span><br><br>';
                            }
                        }
                        ?>

                        <form action="" method="post" class=" w3-card-4 w3-light-grey col-md-4" enctype="multipart/form-data"><br>
                            <h3>Add New Option</h3>
                            <div class="form-group">
                                <input name="value" type="text" required placeholder='Enter Title' class="w3-input w3-border w3-border-blue" />
                            </div>
                            <div class="form-group">
                                <select name="effect" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Client</option>
                                    <option value="1">Chargeable</option>
                                    <option value="0">Non Chargeable</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="effect_interp" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Interepreter</option>
                                    <option value="charg">Chargeable</option>
                                    <option value="ncharg">Non Chargeable</option>
                                    <option value="pay">Payable</option>
                                    <option value="npay">Non payable</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="cd_for" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Select option for</option>
                                    <option value="cl">Client</option>
                                    <option value="ls">LSUK</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <select name="type" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Select Type</option>
                                    <option value="1">Time Based</option>
                                    <option value="2">Reason Based</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="w3-button w3-white w3-border w3-border-blue w3-hover-blue" name="btn_add_cancel"><i class="fa fa-check-circle"></i> Add Cancellation</button>
                            </div>
                        </form>
                        <div class="col-md-8 col-md-offset-2" style="margin-top: 10px;">
                            <?php if (isset($msg_new_cancel) && !empty($msg_new_cancel)) {
                                echo $msg_new_cancel;
                            } ?><br />
                        </div>
                        <?php
                        // Get cancellations of type 1 (time-based) where cd_for = 'ls'
                        $time_based_q = $acttObj->read_all("*", "cancellation_drops", "type = 1 AND cd_for = 'ls'");

                        // Get cancellations of type 2 (reason-based) where cd_for = 'ls'
                        $reason_based_q = $acttObj->read_all("*", "cancellation_drops", "type = 2 AND cd_for = 'ls'");

                        ?>
                        <!-- <div class="col-md-12" style="background-color: #439bcf;  padding: 10px;color: white; font-weight: bold;margin: 0; "> -->
                        <div class="col-md-12" style="background-color: #e9e9e9;  padding: 10px;color: #040404; font-weight: bold;margin: 0; ">
                            <h3>Time-Based Cancellations</h3>
                        </div>

                        <table class="table table-bordered">
                            <thead class="bg-primary">
                                <th width="32%">Title</th>
                                <th width="15%">Client</th>
                                <th width="15%">Interpreter</th>
                                <th width="15%">Option For</th>
                                <th width="10%">Type</th>
                                <th width="8%">Action</th>
                            </thead>
                            <tbody>
                                <?php $iam_cancel = 1;

                                while ($row_cancel = mysqli_fetch_assoc($time_based_q)) {

                                    if (isset($_POST['btn_cancel' . $iam_cancel])) {
                                        $cancel_id = $_POST['cancel_id' . $iam_cancel];
                                        $value_cancel = mysqli_escape_string($con, $_POST['value_cancel' . $iam_cancel]);
                                        $cd_for = $_POST['cd_for' . $iam_cancel];
                                        $effect_cancel = $_POST['effect_cancel' . $iam_cancel];
                                        $effect_interp_cancel = $_POST['effect_interp_cancel' . $iam_cancel];
                                        $type = $_POST['type' . $iam_cancel];
                                        $acttObj->update('cancellation_drops', array('cd_effect' => $effect_cancel, 'cd_effect_interp' => $effect_interp_cancel, 'type' => $type), array('cd_id' => $cancel_id));
                                ?>
                                        <script>
                                            document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                            document.getElementById('tab_cancellation').classList.add('in', 'active');
                                            document.getElementById('li_tab_auto_replies').classList.remove('active');
                                            document.getElementById('li_tab_cancellation').classList.add('active');
                                        </script>
                                    <?php

                                        if ($acttObj->update('cancellation_drops', array('cd_title' => $value_cancel, 'cd_for' => $cd_for), array('cd_id' => $cancel_id))) {

                                            $msg_cancel = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Record has been successfully updated .</b></span><br><br>';
                                        } else {
                                            $msg_cancel = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to update this record !</b></span><br><br>';
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td colspan='6'>
                                            <form action="" method="post" class="form-inline" enctype="multipart/form-data">
                                                <input type="hidden" name="<?php echo 'cancel_id' . $iam_cancel; ?>" value="<?php echo $row_cancel['cd_id']; ?>" />
                                                <div class="form-group col-md-4">
                                                    <input data-toggle="tooltip" data-placement="top" title="[DATE] will be replaced with system date!" name="<?php echo 'value_cancel' . $iam_cancel; ?>" id="<?php echo 'value_cancel' . $iam_cancel; ?>" type="text" placeholder='Cancellation Title' value="<?php echo $row_cancel['cd_title']; ?>" class="w3-input w3-border w3-border-blue" />
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'effect_cancel' . $iam_cancel; ?>" id="<?php echo 'effect_cancel' . $iam_cancel; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel['cd_effect']; ?>"><?php echo $row_cancel['cd_effect'] == 1 ? 'Chargable' : 'Non chargable'; ?></option>
                                                        <option disabled value="">- - - Client - - -</option>
                                                        <option value="1">Chargeable</option>
                                                        <option value="0">Non Chargeable</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'effect_interp_cancel' . $iam_cancel; ?>" id="<?php echo 'effect_interp_cancel' . $iam_cancel; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option value="" <?php echo (empty($row_cancel['cd_effect_interp'])) ? 'selected' : ''; ?>>- - - Select Interpreter - - -</option>
                                                        <option value="charg" <?php echo $row_cancel['cd_effect_interp'] == 'charg' ? 'selected' : ''; ?>>Chargeable</option>
                                                        <option value="ncharg" <?php echo $row_cancel['cd_effect_interp'] == 'ncharg' ? 'selected' : ''; ?>>Non Chargeable</option>
                                                        <option value="pay" <?php echo $row_cancel['cd_effect_interp'] == 'pay' ? 'selected' : ''; ?>>Payable</option>
                                                        <option value="npay" <?php echo $row_cancel['cd_effect_interp'] == 'npay' ? 'selected' : ''; ?>>Non payable</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'cd_for' . $iam_cancel; ?>" id="<?php echo 'cd_for' . $iam_cancel; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel['cd_for']; ?>"><?php if ($row_cancel['cd_for'] == 'cl') {
                                                                                                                            echo 'Client';
                                                                                                                        } else if ($row_cancel['cd_for'] == 'ls') {
                                                                                                                            echo 'LSUK';
                                                                                                                        } else {
                                                                                                                            echo 'Both';
                                                                                                                        } ?></option>
                                                        <option disabled value="">- - - Option For - - -</option>
                                                        <option value="cl">Client</option>
                                                        <option value="ls">LSUK</option>

                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <select name="<?php echo 'type' . $iam_cancel; ?>" id="<?php echo 'type' . $iam_cancel; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel['type']; ?>"><?php echo $row_cancel['type'] == 1 ? 'Time Based' : 'Reason Based'; ?></option>
                                                        <option disabled value="">- - - Client - - -</option>
                                                        <option value="1">Time Based</option>
                                                        <option value="2">Reason Based</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <button data-toggle="tooltip" id="update_btn" data-placement="top" type="submit" title="Edit this record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue w3-hover-blue" name="<?php echo 'btn_cancel' . $iam_cancel; ?>"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <div class="col-md-8 col-md-offset-2">
                                    <?php if (isset($msg_cancel) && !empty($msg_cancel)) {
                                        echo $msg_cancel;
                                    } ?><br />
                                </div>

                            </tbody>
                        </table>


                        <div class="col-md-12" style="background-color: #e9e9e9;  padding: 10px;color: #040404; font-weight: bold;margin: 0; ">
                            <h3>Reason-Based Cancellations</h3>
                        </div>
                        <table class="table table-bordered">
                            <thead class="bg-primary">
                                <th width="32%">Title</th>
                                <th width="15%">Client</th>
                                <th width="15%">Interpreter</th>
                                <th width="15%">Option For</th>
                                <th width="10%">Type</th>
                                <th width="8%">Action</th>
                            </thead>
                            <tbody>
                                <?php $iam_cancel_reason = 1;

                                while ($row_cancel_reason = mysqli_fetch_assoc($reason_based_q)) {

                                    if (isset($_POST['btn_cancel' . $iam_cancel_reason])) {
                                        $cancel_id = $_POST['cancel_id' . $iam_cancel_reason];
                                        $value_cancel = mysqli_escape_string($con, $_POST['value_cancel' . $iam_cancel_reason]);
                                        $cd_for = $_POST['cd_for' . $iam_cancel_reason];
                                        $effect_cancel = $_POST['effect_cancel' . $iam_cancel_reason];
                                        $effect_interp_cancel = $_POST['effect_interp_cancel' . $iam_cancel_reason];
                                        $type = $_POST['type' . $iam_cancel_reason];
                                        $acttObj->update('cancellation_drops', array('cd_effect' => $effect_cancel, 'cd_effect_interp' => $effect_interp_cancel), array('cd_id' => $cancel_id, 'type' => $type));
                                ?>
                                        <script>
                                            document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                            document.getElementById('tab_cancellation').classList.add('in', 'active');
                                            document.getElementById('li_tab_auto_replies').classList.remove('active');
                                            document.getElementById('li_tab_cancellation').classList.add('active');
                                        </script>
                                    <?php

                                        if ($acttObj->update('cancellation_drops', array('cd_title' => $value_cancel, 'cd_for' => $cd_for), array('cd_id' => $cancel_id))) {

                                            $msg_cancel = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Record has been successfully updated .</b></span><br><br>';
                                        } else {
                                            $msg_cancel = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to update this record !</b></span><br><br>';
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td colspan='6'>
                                            <form action="" method="post" class="form-inline" enctype="multipart/form-data">
                                                <input type="hidden" name="<?php echo 'cancel_id' . $iam_cancel_reason; ?>" value="<?php echo $row_cancel_reason['cd_id']; ?>" />
                                                <div class="form-group col-md-4">
                                                    <input data-toggle="tooltip" data-placement="top" title="[DATE] will be replaced with system date!" name="<?php echo 'value_cancel' . $iam_cancel; ?>" id="<?php echo 'value_cancel' . $iam_cancel_reason; ?>" type="text" placeholder='Cancellation Title' value="<?php echo $row_cancel_reason['cd_title']; ?>" class="w3-input w3-border w3-border-blue" />
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'effect_cancel' . $iam_cancel_reason; ?>" id="<?php echo 'effect_cancel' . $iam_cancel_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel_reason['cd_effect']; ?>"><?php echo $row_cancel_reason['cd_effect'] == 1 ? 'Chargable' : 'Non chargable'; ?></option>
                                                        <option disabled value="">- - - Client - - -</option>
                                                        <option value="1">Chargeable</option>
                                                        <option value="0">Non Chargeable</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'effect_interp_cancel' . $iam_cancel_reason; ?>" id="<?php echo 'effect_interp_cancel' . $iam_cancel_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option value="" <?php echo (empty($row_cancel_reason['cd_effect_interp'])) ? 'selected' : ''; ?>>- - - Select Interpreter - - -</option>
                                                        <option value="charg" <?php echo $row_cancel_reason['cd_effect_interp'] == 'charg' ? 'selected' : ''; ?>>Chargeable</option>
                                                        <option value="ncharg" <?php echo $row_cancel_reason['cd_effect_interp'] == 'ncharg' ? 'selected' : ''; ?>>Non Chargeable</option>
                                                        <option value="pay" <?php echo $row_cancel_reason['cd_effect_interp'] == 'pay' ? 'selected' : ''; ?>>Payable</option>
                                                        <option value="npay" <?php echo $row_cancel_reason['cd_effect_interp'] == 'npay' ? 'selected' : ''; ?>>Non payable</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'cd_for' . $iam_cancel_reason; ?>" id="<?php echo 'cd_for' . $iam_cancel_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel_reason['cd_for']; ?>"><?php if ($row_cancel_reason['cd_for'] == 'cl') {
                                                                                                                                    echo 'Client';
                                                                                                                                } else if ($row_cancel_reason['cd_for'] == 'ls') {
                                                                                                                                    echo 'LSUK';
                                                                                                                                } else {
                                                                                                                                    echo 'Both';
                                                                                                                                } ?></option>
                                                        <option disabled value="">- - - Option For - - -</option>
                                                        <option value="cl">Client</option>
                                                        <option value="ls">LSUK</option>

                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <select name="<?php echo 'type' . $iam_cancel_reason; ?>" id="<?php echo 'type' . $iam_cancel_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel_reason['type']; ?>"><?php echo $row_cancel_reason['type'] == 1 ? 'Time Based' : 'Reason Based'; ?></option>
                                                        <option disabled value="">- - - Type - - -</option>
                                                        <option value="1">Time Based</option>
                                                        <option value="0">Reason Based</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <button data-toggle="tooltip" id="update_btn" data-placement="top" type="submit" title="Edit this record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue w3-hover-blue" name="<?php echo 'btn_cancel' . $iam_cancel_reason; ?>"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <div class="col-md-8 col-md-offset-2">
                                    <?php if (isset($msg_cancel) && !empty($msg_cancel)) {
                                        echo $msg_cancel;
                                    } ?><br />
                                </div>

                            </tbody>
                        </table>

                        <?php

                        if (isset($_POST['btn_cancel' . $iam_cancel_reason]) || isset($_POST['btn_add_cancel'])) {
                            echo '<script>
                                localStorage.setItem("activeTab", "tab_cancellation");
                                
                                // Reload the page
                                window.location.href = window.location.href;
                            </script>';
                        }
                        ?>
                    </div>

                    <div id="tab_cr" class="tab-pane fade"><br>


                        <form action="" method="post" class=" w3-card-4 w3-light-grey col-md-4" enctype="multipart/form-data"><br>
                            <h3>Add New Option</h3>
                            <div class="form-group">
                                <input name="value" type="text" required placeholder='Enter Title' class="w3-input w3-border w3-border-blue" />
                            </div>
                            <div class="form-group">
                                <select name="effect" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Client</option>
                                    <option value="1">Chargeable</option>
                                    <option value="0">Non Chargeable</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="effect_interp" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Interepreter</option>
                                    <option value="charg">Chargeable</option>
                                    <option value="ncharg">Non Chargeable</option>
                                    <option value="pay">Payable</option>
                                    <option value="npay">Non payable</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="cd_for" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Select option for</option>
                                    <option value="cl">Client</option>
                                    <option value="ls">LSUK</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <select name="type" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Select Type</option>
                                    <option value="1">Time Based</option>
                                    <option value="2">Reason Based</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="w3-button w3-white w3-border w3-border-blue w3-hover-blue" name="btn_add_cancel"><i class="fa fa-check-circle"></i> Add Cancellation</button>
                            </div>
                        </form>

                        <?php
                        // Get cancellations of type 1 (time-based) where cd_for = 'ls'
                        $time_based_q = $acttObj->read_all("*", "cancellation_drops", "type = 1 AND cd_for = 'cl'");

                        // Get cancellations of type 2 (reason-based) where cd_for = 'ls'
                        $reason_based_q = $acttObj->read_all("*", "cancellation_drops", "type = 2 AND cd_for = 'cl'");

                        ?>
                        <!-- <div class="col-md-12" style="background-color: #439bcf;  padding: 10px;color: white; font-weight: bold;margin: 0; "> -->
                        <div class="col-md-12" style="background-color: #e9e9e9;  padding: 10px;color: #040404; font-weight: bold;margin: 0; ">
                            <h3>Time-Based Cancellations</h3>
                        </div>

                        <table class="table table-bordered">
                            <thead class="bg-primary">
                                <th width="32%">Title</th>
                                <th width="15%">Client</th>
                                <th width="15%">Interpreter</th>
                                <th width="15%">Option For</th>
                                <th width="10%">Type</th>
                                <th width="8%">Action</th>
                            </thead>
                            <tbody>
                                <?php $iam_cancel = 1;

                                while ($row_cancel = mysqli_fetch_assoc($time_based_q)) {

                                    if (isset($_POST['btn_cancel' . $iam_cancel])) {
                                        $cancel_id = $_POST['cancel_id' . $iam_cancel];
                                        $value_cancel = mysqli_escape_string($con, $_POST['value_cancel' . $iam_cancel]);
                                        $cd_for = $_POST['cd_for' . $iam_cancel];
                                        $effect_cancel = $_POST['effect_cancel' . $iam_cancel];
                                        $effect_interp_cancel = $_POST['effect_interp_cancel' . $iam_cancel];
                                        $type = $_POST['type' . $iam_cancel];
                                        $acttObj->update('cancellation_drops', array('cd_effect' => $effect_cancel, 'cd_effect_interp' => $effect_interp_cancel, 'type' => $type), array('cd_id' => $cancel_id));
                                ?>
                                        <script>
                                            document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                            document.getElementById('tab_cancellation').classList.add('in', 'active');
                                            document.getElementById('li_tab_auto_replies').classList.remove('active');
                                            document.getElementById('li_tab_cancellation').classList.add('active');
                                        </script>
                                    <?php


                                    }
                                    ?>
                                    <tr>
                                        <td colspan='6'>
                                            <form action="" method="post" class="form-inline" enctype="multipart/form-data">
                                                <input type="hidden" name="<?php echo 'cancel_id' . $iam_cancel; ?>" value="<?php echo $row_cancel['cd_id']; ?>" />
                                                <div class="form-group col-md-4">
                                                    <input data-toggle="tooltip" data-placement="top" title="[DATE] will be replaced with system date!" name="<?php echo 'value_cancel' . $iam_cancel; ?>" id="<?php echo 'value_cancel' . $iam_cancel; ?>" type="text" placeholder='Cancellation Title' value="<?php echo $row_cancel['cd_title']; ?>" class="w3-input w3-border w3-border-blue" />
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'effect_cancel' . $iam_cancel; ?>" id="<?php echo 'effect_cancel' . $iam_cancel; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel['cd_effect']; ?>"><?php echo $row_cancel['cd_effect'] == 1 ? 'Chargable' : 'Non chargable'; ?></option>
                                                        <option disabled value="">- - - Client - - -</option>
                                                        <option value="1">Chargeable</option>
                                                        <option value="0">Non Chargeable</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'effect_interp_cancel' . $iam_cancel; ?>" id="<?php echo 'effect_interp_cancel' . $iam_cancel; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option value="" <?php echo (empty($row_cancel['cd_effect_interp'])) ? 'selected' : ''; ?>>- - - Select Interpreter - - -</option>
                                                        <option value="charg" <?php echo $row_cancel['cd_effect_interp'] == 'charg' ? 'selected' : ''; ?>>Chargeable</option>
                                                        <option value="ncharg" <?php echo $row_cancel['cd_effect_interp'] == 'ncharg' ? 'selected' : ''; ?>>Non Chargeable</option>
                                                        <option value="pay" <?php echo $row_cancel['cd_effect_interp'] == 'pay' ? 'selected' : ''; ?>>Payable</option>
                                                        <option value="npay" <?php echo $row_cancel['cd_effect_interp'] == 'npay' ? 'selected' : ''; ?>>Non payable</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'cd_for' . $iam_cancel; ?>" id="<?php echo 'cd_for' . $iam_cancel; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel['cd_for']; ?>"><?php if ($row_cancel['cd_for'] == 'cl') {
                                                                                                                            echo 'Client';
                                                                                                                        } else if ($row_cancel['cd_for'] == 'ls') {
                                                                                                                            echo 'LSUK';
                                                                                                                        } else {
                                                                                                                            echo 'Both';
                                                                                                                        } ?></option>
                                                        <option disabled value="">- - - Option For - - -</option>
                                                        <option value="cl">Client</option>
                                                        <option value="ls">LSUK</option>

                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <select name="<?php echo 'type' . $iam_cancel; ?>" id="<?php echo 'type' . $iam_cancel; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel['type']; ?>"><?php echo $row_cancel['type'] == 1 ? 'Time Based' : 'Reason Based'; ?></option>
                                                        <option disabled value="">- - - Client - - -</option>
                                                        <option value="1">Time Based</option>
                                                        <option value="2">Reason Based</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <button data-toggle="tooltip" id="update_btn" data-placement="top" type="submit" title="Edit this record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue w3-hover-blue" name="<?php echo 'btn_cancel' . $iam_cancel; ?>"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>



                            </tbody>
                        </table>


                        <div class="col-md-12" style="background-color: #e9e9e9;  padding: 10px;color: #040404; font-weight: bold;margin: 0; ">
                            <h3>Reason-Based Cancellations</h3>
                        </div>
                        <table class="table table-bordered">
                            <thead class="bg-primary">
                                <th width="32%">Title</th>
                                <th width="15%">Client</th>
                                <th width="15%">Interpreter</th>
                                <th width="15%">Option For</th>
                                <th width="10%">Type</th>
                                <th width="8%">Action</th>
                            </thead>
                            <tbody>
                                <?php $iam_cancel_reason = 1;

                                while ($row_cancel_reason = mysqli_fetch_assoc($reason_based_q)) {

                                    if (isset($_POST['btn_cancel' . $iam_cancel_reason])) {
                                        $cancel_id = $_POST['cancel_id' . $iam_cancel_reason];
                                        $value_cancel = mysqli_escape_string($con, $_POST['value_cancel' . $iam_cancel_reason]);
                                        $cd_for = $_POST['cd_for' . $iam_cancel_reason];
                                        $effect_cancel = $_POST['effect_cancel' . $iam_cancel_reason];
                                        $effect_interp_cancel = $_POST['effect_interp_cancel' . $iam_cancel_reason];
                                        $type = $_POST['type' . $iam_cancel_reason];
                                        $acttObj->update('cancellation_drops', array('cd_effect' => $effect_cancel, 'cd_effect_interp' => $effect_interp_cancel), array('cd_id' => $cancel_id, 'type' => $type));
                                ?>
                                        <script>
                                            document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                            document.getElementById('tab_cancellation').classList.add('in', 'active');
                                            document.getElementById('li_tab_auto_replies').classList.remove('active');
                                            document.getElementById('li_tab_cancellation').classList.add('active');
                                        </script>
                                    <?php

                                        if ($acttObj->update('cancellation_drops', array('cd_title' => $value_cancel, 'cd_for' => $cd_for), array('cd_id' => $cancel_id))) {

                                            $msg_cancel = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Record has been successfully updated .</b></span><br><br>';
                                        } else {
                                            $msg_cancel = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to update this record !</b></span><br><br>';
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td colspan='6'>
                                            <form action="" method="post" class="form-inline" enctype="multipart/form-data">
                                                <input type="hidden" name="<?php echo 'cancel_id' . $iam_cancel_reason; ?>" value="<?php echo $row_cancel_reason['cd_id']; ?>" />
                                                <div class="form-group col-md-4">
                                                    <input data-toggle="tooltip" data-placement="top" title="[DATE] will be replaced with system date!" name="<?php echo 'value_cancel' . $iam_cancel; ?>" id="<?php echo 'value_cancel' . $iam_cancel_reason; ?>" type="text" placeholder='Cancellation Title' value="<?php echo $row_cancel_reason['cd_title']; ?>" class="w3-input w3-border w3-border-blue" />
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'effect_cancel' . $iam_cancel_reason; ?>" id="<?php echo 'effect_cancel' . $iam_cancel_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel_reason['cd_effect']; ?>"><?php echo $row_cancel_reason['cd_effect'] == 1 ? 'Chargable' : 'Non chargable'; ?></option>
                                                        <option disabled value="">- - - Client - - -</option>
                                                        <option value="1">Chargeable</option>
                                                        <option value="0">Non Chargeable</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'effect_interp_cancel' . $iam_cancel_reason; ?>" id="<?php echo 'effect_interp_cancel' . $iam_cancel_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option value="" <?php echo (empty($row_cancel_reason['cd_effect_interp'])) ? 'selected' : ''; ?>>- - - Select Interpreter - - -</option>
                                                        <option value="charg" <?php echo $row_cancel_reason['cd_effect_interp'] == 'charg' ? 'selected' : ''; ?>>Chargeable</option>
                                                        <option value="ncharg" <?php echo $row_cancel_reason['cd_effect_interp'] == 'ncharg' ? 'selected' : ''; ?>>Non Chargeable</option>
                                                        <option value="pay" <?php echo $row_cancel_reason['cd_effect_interp'] == 'pay' ? 'selected' : ''; ?>>Payable</option>
                                                        <option value="npay" <?php echo $row_cancel_reason['cd_effect_interp'] == 'npay' ? 'selected' : ''; ?>>Non payable</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <select name="<?php echo 'cd_for' . $iam_cancel_reason; ?>" id="<?php echo 'cd_for' . $iam_cancel_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel_reason['cd_for']; ?>"><?php if ($row_cancel_reason['cd_for'] == 'cl') {
                                                                                                                                    echo 'Client';
                                                                                                                                } else if ($row_cancel_reason['cd_for'] == 'ls') {
                                                                                                                                    echo 'LSUK';
                                                                                                                                } else {
                                                                                                                                    echo 'Both';
                                                                                                                                } ?></option>
                                                        <option disabled value="">- - - Option For - - -</option>
                                                        <option value="cl">Client</option>
                                                        <option value="ls">LSUK</option>

                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <select name="<?php echo 'type' . $iam_cancel_reason; ?>" id="<?php echo 'type' . $iam_cancel_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                        <option selected value="<?php echo $row_cancel_reason['type']; ?>"><?php echo $row_cancel_reason['type'] == 1 ? 'Time Based' : 'Reason Based'; ?></option>
                                                        <option disabled value="">- - - Type - - -</option>
                                                        <option value="1">Time Based</option>
                                                        <option value="0">Reason Based</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <button data-toggle="tooltip" id="update_btn" data-placement="top" type="submit" title="Edit this record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue w3-hover-blue" name="<?php echo 'btn_cancel' . $iam_cancel_reason; ?>"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>



                            </tbody>
                        </table>


                    </div>
                </div>
            </div>


            <div id="tab_amendment" class="tab-pane fade"><br>
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab_co2"><i class="fa fa-circle-o"></i> Amendments By LSUK</a></li>
                    <li><a data-toggle="tab" href="#tab_cr2"><i class="fa fa-circle-o"></i> Amendments By Client</a></li>
                </ul>

                <div class="tab-content col-md-12">
                    <div id="tab_co2" class="tab-pane fade in active"><br>
                        <?php if (isset($_POST['btn_add_amend'])) {
                            $value = mysqli_escape_string($con, $_POST['value']);
                            $effect = $_POST['effect'];
                            $effect_interp = $_POST['effect_interp'];
                            $amend_for = $_POST['amend_for'];
                            $type = $_POST['type'];
                            $chk = $acttObj->read_specific('count(id) as counter', 'amend_options', 'value="' . $value . '" and effect="' . $effect . '" and effect_interp="' . $effect_interp . '" and `amend_for`="' . $amend_for . '"');
                        ?>

                            <script>
                                document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                document.getElementById('tab_amendment').classList.add('in', 'active');
                                document.getElementById('li_tab_auto_replies').classList.remove('active');
                                document.getElementById('li_tab_amendment').classList.add('active');
                            </script>
                        <?php if ($chk['counter'] == 0) {
                                $data_amend = array('value' => $value, 'effect' => $effect, 'effect_interp' => $effect_interp, 'amend_for' => $amend_for, 'type' => $type);
                                if ($acttObj->insert('amend_options', $data_amend)) {
                                    $msg_new_amend = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Record has been successfully added .</b></span><br><br>';
                                } else {
                                    $msg_new_amend = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to add new record !</b></span><br><br>';
                                }
                            } else {
                                $msg_new_amend = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Same record already exists !</b></span><br><br>';
                            }
                        }
                        ?>

                        <form action="" method="post" class=" w3-card-4 w3-light-grey col-md-4" enctype="multipart/form-data"><br>
                            <h3 class="text-center">Add New Record</h3>
                            <div class="form-group">
                                <input name="value" type="text" required placeholder='Enter Title' class="w3-input w3-border w3-border-blue" />
                            </div>
                            <div class="form-group">
                                <select name="effect" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Client</option>
                                    <option value="0">Non Chargeable</option>
                                    <option value="1">Chargeable</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="effect_interp" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Interepreter</option>
                                    <option value="0">Not affect interpreter</option>
                                    <option value="1">Affect interpreter</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="amend_for" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Select option for</option>
                                    <option value="cl">Client</option>
                                    <option value="ls">LSUK</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="type" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Select Type</option>
                                    <option value="1">Time Based</option>
                                    <option value="2">Reason Based</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="w3-button w3-white w3-border w3-border-blue w3-hover-blue" name="btn_add_amend"><i class="fa fa-check-circle"></i> Add Amendment</button>
                            </div>
                        </form>
                        <div class="col-md-12" style="background-color: #e9e9e9;  padding: 10px;color: #040404; font-weight: bold;margin: 0; margin-top:30px;">
                            <h3>Time-Based Cancellations</h3>
                        </div>
                       
                        <table class="table table-bordered">
                            <thead class="bg-primary">
                                <th width="5%">S.No</th>
                                <th width="30%">Title</th>
                                <th width="15%">Client</th>
                                <th width="15%">Interepreter</th>
                                <th width="15%">Option For</th>
                                <th width="55%">Type</th>
                                <th width="5%">Action</th>
                            </thead>
                            <tbody>
                                <?php
                                $iam = 1;
                                $amend_q_time = $acttObj->read_all("*", "amend_options", "type = 1 AND amend_for = 'ls'");
                                
                                while ($row_amend = mysqli_fetch_assoc($amend_q_time)) {
                                    if (isset($_POST['btn_amend' . $iam])) {
                                        $amend_id = $_POST['amend_id' . $iam];
                                        $value_amend = $_POST['value_amend' . $iam];
                                        $effect_amend = $_POST['effect_amend' . $iam];
                                        $effect_interp_amend = $_POST['effect_interp_amend' . $iam];
                                        $amend_for_amend = $_POST['amend_for_amend' . $iam];
                                        $type = $_POST['type' . $iam];
                                        // $acttObj->update('amend_options', array('value' => $value_amend, 'effect' => $effect_amend, 'effect_interp' => $effect_interp_amend, 'amend_for' => $amend_for_amend), array('id' => $amend_id));
                                ?>
                                        <script>
                                            document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                            document.getElementById('tab_amendment').classList.add('in', 'active');
                                            document.getElementById('li_tab_auto_replies').classList.remove('active');
                                            document.getElementById('li_tab_amendment').classList.add('active');
                                        </script>
                                        <?php
                                        if ($acttObj->update('amend_options', array('value' => $value_amend, 'effect' => $effect_amend, 'effect_interp' => $effect_interp_amend, 'amend_for' => $amend_for_amend, 'type' => $type), array('id' => $amend_id))) {
                                        ?>
                                            <script>
                                                document.getElementById('<?php echo "value_amend" . $iam; ?>').value = '<?php echo $value_amend; ?>';
                                                document.getElementById('<?php echo "effect_amend" . $iam; ?>').value = '<?php echo $effect_amend; ?>';
                                                document.getElementById('<?php echo "effect_interp_amend" . $iam; ?>').value = '<?php echo $effect_interp_amend; ?>';
                                                document.getElementById('<?php echo "amend_for_amend" . $iam; ?>').value = '<?php echo $amend_for_amend; ?>';
                                                document.getElementById('<?php echo "type" . $iam; ?>').value = '<?php echo $type; ?>';
                                            </script>
                                    <?php $msg_amend = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Record has been successfully updated .</b></span><br><br>';
                                        } else {
                                            $msg_amend = $amend_id . '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to update this record !</b></span><br><br>';
                                        }
                                    }
                                    ?>
                                    <form action="" method="post" class="register" enctype="multipart/form-data">
                                        <tr>
                                            <td align="left"><?php echo $iam; ?>
                                                <input type="hidden" name="amend_id<?php echo $iam; ?>" value="<?php echo $row_amend['id']; ?>" />
                                            </td>
                                            <td align="left">
                                                <input name="value_amend<?php echo $iam; ?>" id="value_amend<?php echo $iam; ?>" type="text" placeholder='Amend Title' value="<?php echo $row_amend['value']; ?>" class="w3-input w3-border w3-border-blue" />
                                            </td>
                                            <td align="left">
                                                <select name="effect_amend<?php echo $iam; ?>" id="effect_amend<?php echo $iam; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option selected value="<?php echo $row_amend['effect']; ?>"><?php echo $row_amend['effect'] == '1' ? 'Chargeable' : 'Non chargable'; ?></option>
                                                    <option disabled value="">Client</option>
                                                    <option value="0">Non Chargeable</option>
                                                    <option value="1">Chargeable</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="effect_interp_amend<?php echo $iam; ?>" id="effect_interp_amend<?php echo $iam; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option selected value="<?php echo $row_amend['effect_interp']; ?>"><?php echo $row_amend['effect_interp'] == '1' ? 'Affect interpreter' : 'Not affect interpreter'; ?></option>
                                                    <option disabled value="">Interpreter</option>
                                                    <option value="1">Affect interpreter</option>
                                                    <option value="0">Not affect interpreter</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="amend_for_amend<?php echo $iam; ?>" id="amend_for_amend<?php echo $iam; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option value="" <?php echo (empty($row_amend['amend_for']) || $row_amend['amend_for'] === null) ? 'selected' : ''; ?>>- - - Option For - - -</option>
                                                    <option value="cl" <?php echo ($row_amend['amend_for'] == 'cl') ? 'selected' : ''; ?>>Client</option>
                                                    <option value="ls" <?php echo ($row_amend['amend_for'] == 'ls') ? 'selected' : ''; ?>>LSUK</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="type<?php echo $iam; ?>" id="type<?php echo $iam; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option value="" <?php echo (empty($row_amend['type']) || $row_amend['type'] === null) ? 'selected' : ''; ?>>- - - Type - - -</option>
                                                    <option value="1" <?php echo ($row_amend['type'] == '1') ? 'selected' : ''; ?>>Time Based</option>
                                                    <option value="2" <?php echo ($row_amend['type'] == '2') ? 'selected' : ''; ?>>Reason Based</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <!-- Ensure the button is linked to the correct $iam -->
                                                <button data-toggle="tooltip" data-placement="top" type="submit" title="Edit this record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue w3-hover-blue" name="btn_amend<?php echo $iam; ?>"><i class="fa fa-edit"></i></button>
                                            </td>
                                        </tr>
                                    </form>
                                <?php
                                    if (isset($_POST['btn_amend' . $iam]) || isset($_POST['btn_add_amend'])) {
                                        echo '<script>
                                        // Save the active tab in localStorage
                                        localStorage.setItem("activeTab", "tab_amendment");
                                        
                                        // Reload the page
                                        window.location.href = window.location.href;
                                    </script>';
                                    }
                                    $iam++;
                                }

                                ?>

                                <div class="col-md-8 col-md-offset-2">
                                    <?php if (isset($msg_amend) && !empty($msg_amend)) {
                                        echo $msg_amend;
                                    } ?><br />
                                </div>
                            </tbody>
                        </table>
                        <div class="col-md-12" style="background-color: #e9e9e9;  padding: 10px;color: #040404; font-weight: bold;margin: 0; ">
                            <h3>Reason-Based Cancellations</h3>
                        </div>

                        <table class="table table-bordered">
                            <thead class="bg-primary">
                                <th width="5%">S.No</th>
                                <th width="30%">Title</th>
                                <th width="15%">Client</th>
                                <th width="15%">Interepreter</th>
                                <th width="15%">Option For</th>
                                <th width="55%">Type</th>
                                <th width="5%">Action</th>
                            </thead>
                            <tbody>
                                <?php
                                $iam_reason = 1;
                                $amend_q_reason = $acttObj->read_all("*", "amend_options", "type = 2 AND amend_for = 'ls'");
                                while ($row_amend = mysqli_fetch_assoc($amend_q_reason)) {
                                    if (isset($_POST['btn_amend' . $iam_reason])) {
                                        $amend_id = $_POST['amend_id' . $iam_reason];
                                        $value_amend = $_POST['value_amend' . $iam_reason];
                                        $effect_amend = $_POST['effect_amend' . $iam_reason];
                                        $effect_interp_amend = $_POST['effect_interp_amend' . $iam_reason];
                                        $amend_for_amend = $_POST['amend_for_amend' . $iam_reason];
                                        $type = $_POST['type' . $iam_reason];
                                        // $acttObj->update('amend_options', array('value' => $value_amend, 'effect' => $effect_amend, 'effect_interp' => $effect_interp_amend, 'amend_for' => $amend_for_amend), array('id' => $amend_id));
                                ?>
                                        <script>
                                            document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                            document.getElementById('tab_amendment').classList.add('in', 'active');
                                            document.getElementById('li_tab_auto_replies').classList.remove('active');
                                            document.getElementById('li_tab_amendment').classList.add('active');
                                        </script>
                                        <?php
                                        if ($acttObj->update('amend_options', array('value' => $value_amend, 'effect' => $effect_amend, 'effect_interp' => $effect_interp_amend, 'amend_for' => $amend_for_amend, 'type' => $type), array('id' => $amend_id))) {
                                        ?>
                                            <script>
                                                document.getElementById('<?php echo "value_amend" . $iam_reason; ?>').value = '<?php echo $value_amend; ?>';
                                                document.getElementById('<?php echo "effect_amend" . $iam_reason; ?>').value = '<?php echo $effect_amend; ?>';
                                                document.getElementById('<?php echo "effect_interp_amend" . $iam_reason; ?>').value = '<?php echo $effect_interp_amend; ?>';
                                                document.getElementById('<?php echo "amend_for_amend" . $iam_reason; ?>').value = '<?php echo $amend_for_amend; ?>';
                                                document.getElementById('<?php echo "type" . $iam_reason; ?>').value = '<?php echo $type; ?>';
                                            </script>
                                    <?php $msg_amend = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Record has been successfully updated .</b></span><br><br>';
                                        } else {
                                            $msg_amend = $amend_id . '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to update this record !</b></span><br><br>';
                                        }
                                    }
                                    ?>
                                    <form action="" method="post" class="register" enctype="multipart/form-data">
                                        <tr>
                                            <td align="left"><?php echo $iam_reason; ?>
                                                <input type="hidden" name="amend_id<?php echo $iam_reason; ?>" value="<?php echo $row_amend['id']; ?>" />
                                            </td>
                                            <td align="left">
                                                <input name="value_amend<?php echo $iam_reason; ?>" id="value_amend<?php echo $iam_reason; ?>" type="text" placeholder='Amend Title' value="<?php echo $row_amend['value']; ?>" class="w3-input w3-border w3-border-blue" />
                                            </td>
                                            <td align="left">
                                                <select name="effect_amend<?php echo $iam_reason; ?>" id="effect_amend<?php echo $iam_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option selected value="<?php echo $row_amend['effect']; ?>"><?php echo $row_amend['effect'] == '1' ? 'Chargeable' : 'Non Chargeable'; ?></option>
                                                    <option disabled value="">Client</option>
                                                    <option value="0">Non Chargeable</option>
                                                    <option value="1">Chargeable</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="effect_interp_amend<?php echo $iam_reason; ?>" id="effect_interp_amend<?php echo $iam_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option selected value="<?php echo $row_amend['effect_interp']; ?>"><?php echo $row_amend['effect_interp'] == '1' ? 'Affect interpreter' : 'Not affect interpreter'; ?></option>
                                                    <option disabled value="">Interpreter</option>
                                                    <option value="1">Affect interpreter</option>
                                                    <option value="0">Not affect interpreter</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="amend_for_amend<?php echo $iam_reason; ?>" id="amend_for_amend<?php echo $iam_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option value="" <?php echo (empty($row_amend['amend_for']) || $row_amend['amend_for'] === null) ? 'selected' : ''; ?>>- - - Option For - - -</option>
                                                    <option value="cl" <?php echo ($row_amend['amend_for'] == 'cl') ? 'selected' : ''; ?>>Client</option>
                                                    <option value="ls" <?php echo ($row_amend['amend_for'] == 'ls') ? 'selected' : ''; ?>>LSUK</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="type<?php echo $iam_reason; ?>" id="type<?php echo $iam_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option value="" <?php echo (empty($row_amend['type']) || $row_amend['type'] === null) ? 'selected' : ''; ?>>- - - Type - - -</option>
                                                    <option value="1" <?php echo ($row_amend['type'] == '1') ? 'selected' : ''; ?>>Time Based</option>
                                                    <option value="2" <?php echo ($row_amend['type'] == '2') ? 'selected' : ''; ?>>Reason Based</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <!-- Ensure the button is linked to the correct $iam -->
                                                <button data-toggle="tooltip" data-placement="top" type="submit" title="Edit this record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue w3-hover-blue" name="btn_amend<?php echo $iam_reason; ?>"><i class="fa fa-edit"></i></button>
                                            </td>
                                        </tr>
                                    </form>
                                <?php
                                    if (isset($_POST['btn_amend' . $iam_reason]) || isset($_POST['btn_add_amend'])) {
                                        echo '<script>
                                localStorage.setItem("activeTab", "tab_amendment");
                                window.location.href = window.location.href;
                            </script>';
                                    }
                                    $iam_reason++;
                                }

                                ?>

                                <div class="col-md-8 col-md-offset-2">
                                    <?php if (isset($msg_amend) && !empty($msg_amend)) {
                                        echo $msg_amend;
                                    } ?><br />
                                </div>
                            </tbody>
                        </table>
                    </div>
                    <div id="tab_cr2" class="tab-pane fade"><br>

                    
                    

                        <form action="" method="post" class=" w3-card-4 w3-light-grey col-md-4" enctype="multipart/form-data"><br>
                            <h3 class="text-center">Add New Record</h3>
                            <div class="form-group">
                                <input name="value" type="text" required placeholder='Enter Title' class="w3-input w3-border w3-border-blue" />
                            </div>
                            <div class="form-group">
                                <select name="effect" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Client</option>
                                    <option value="0">Non Chargeable</option>
                                    <option value="1">Chargeable</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="effect_interp" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Interepreter</option>
                                    <option value="0">Not affect interpreter</option>
                                    <option value="1">Affect interpreter</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="amend_for" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Select option for</option>
                                    <option value="cl">Client</option>
                                    <option value="ls">LSUK</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="type" class="w3-input w3-border w3-border-blue" required>
                                    <option disabled selected value="">Select Type</option>
                                    <option value="1">Time Based</option>
                                    <option value="2">Reason Based</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="w3-button w3-white w3-border w3-border-blue w3-hover-blue" name="btn_add_amend"><i class="fa fa-check-circle"></i> Add Amendment</button>
                            </div>
                        </form>
                        <div class="col-md-12" style="background-color: #e9e9e9;  padding: 10px;color: #040404; font-weight: bold;margin: 0; margin-top:30px;">
                            <h3>Time-Based Cancellations</h3>
                        </div>
                       
                        <table class="table table-bordered">
                            <thead class="bg-primary">
                                <th width="5%">S.No</th>
                                <th width="30%">Title</th>
                                <th width="15%">Client</th>
                                <th width="15%">Interepreter</th>
                                <th width="15%">Option For</th>
                                <th width="55%">Type</th>
                                <th width="5%">Action</th>
                            </thead>
                            <tbody>
                                <?php
                                $iam = 1;
                                $amend_q_time = $acttObj->read_all("*", "amend_options", "type = 1 AND amend_for = 'cl'");
                                
                                while ($row_amend = mysqli_fetch_assoc($amend_q_time)) {
                                    if (isset($_POST['btn_amend' . $iam])) {
                                        $amend_id = $_POST['amend_id' . $iam];
                                        $value_amend = $_POST['value_amend' . $iam];
                                        $effect_amend = $_POST['effect_amend' . $iam];
                                        $effect_interp_amend = $_POST['effect_interp_amend' . $iam];
                                        $amend_for_amend = $_POST['amend_for_amend' . $iam];
                                        $type = $_POST['type' . $iam];
                                        // $acttObj->update('amend_options', array('value' => $value_amend, 'effect' => $effect_amend, 'effect_interp' => $effect_interp_amend, 'amend_for' => $amend_for_amend), array('id' => $amend_id));
                                ?>
                                        <script>
                                            document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                            document.getElementById('tab_amendment').classList.add('in', 'active');
                                            document.getElementById('li_tab_auto_replies').classList.remove('active');
                                            document.getElementById('li_tab_amendment').classList.add('active');
                                        </script>
                                        <?php
                                        if ($acttObj->update('amend_options', array('value' => $value_amend, 'effect' => $effect_amend, 'effect_interp' => $effect_interp_amend, 'amend_for' => $amend_for_amend, 'type' => $type), array('id' => $amend_id))) {
                                        ?>
                                            <script>
                                                document.getElementById('<?php echo "value_amend" . $iam; ?>').value = '<?php echo $value_amend; ?>';
                                                document.getElementById('<?php echo "effect_amend" . $iam; ?>').value = '<?php echo $effect_amend; ?>';
                                                document.getElementById('<?php echo "effect_interp_amend" . $iam; ?>').value = '<?php echo $effect_interp_amend; ?>';
                                                document.getElementById('<?php echo "amend_for_amend" . $iam; ?>').value = '<?php echo $amend_for_amend; ?>';
                                                document.getElementById('<?php echo "type" . $iam; ?>').value = '<?php echo $type; ?>';
                                            </script>
                                    <?php $msg_amend = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Record has been successfully updated .</b></span><br><br>';
                                        } else {
                                            $msg_amend = $amend_id . '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to update this record !</b></span><br><br>';
                                        }
                                    }
                                    ?>
                                    <form action="" method="post" class="register" enctype="multipart/form-data">
                                        <tr>
                                            <td align="left"><?php echo $iam; ?>
                                                <input type="hidden" name="amend_id<?php echo $iam; ?>" value="<?php echo $row_amend['id']; ?>" />
                                            </td>
                                            <td align="left">
                                                <input name="value_amend<?php echo $iam; ?>" id="value_amend<?php echo $iam; ?>" type="text" placeholder='Amend Title' value="<?php echo $row_amend['value']; ?>" class="w3-input w3-border w3-border-blue" />
                                            </td>
                                            <td align="left">
                                                <select name="effect_amend<?php echo $iam; ?>" id="effect_amend<?php echo $iam; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option selected value="<?php echo $row_amend['effect']; ?>"><?php echo $row_amend['effect'] == '1' ? 'Chargeable' : 'Non chargable'; ?></option>
                                                    <option disabled value="">Client</option>
                                                    <option value="0">Non Chargeable</option>
                                                    <option value="1">Chargeable</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="effect_interp_amend<?php echo $iam; ?>" id="effect_interp_amend<?php echo $iam; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option selected value="<?php echo $row_amend['effect_interp']; ?>"><?php echo $row_amend['effect_interp'] == '1' ? 'Affect interpreter' : 'Not affect interpreter'; ?></option>
                                                    <option disabled value="">Interpreter</option>
                                                    <option value="1">Affect interpreter</option>
                                                    <option value="0">Not affect interpreter</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="amend_for_amend<?php echo $iam; ?>" id="amend_for_amend<?php echo $iam; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option value="" <?php echo (empty($row_amend['amend_for']) || $row_amend['amend_for'] === null) ? 'selected' : ''; ?>>- - - Option For - - -</option>
                                                    <option value="cl" <?php echo ($row_amend['amend_for'] == 'cl') ? 'selected' : ''; ?>>Client</option>
                                                    <option value="ls" <?php echo ($row_amend['amend_for'] == 'ls') ? 'selected' : ''; ?>>LSUK</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="type<?php echo $iam; ?>" id="type<?php echo $iam; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option value="" <?php echo (empty($row_amend['type']) || $row_amend['type'] === null) ? 'selected' : ''; ?>>- - - Type - - -</option>
                                                    <option value="1" <?php echo ($row_amend['type'] == '1') ? 'selected' : ''; ?>>Time Based</option>
                                                    <option value="2" <?php echo ($row_amend['type'] == '2') ? 'selected' : ''; ?>>Reason Based</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <!-- Ensure the button is linked to the correct $iam -->
                                                <button data-toggle="tooltip" data-placement="top" type="submit" title="Edit this record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue w3-hover-blue" name="btn_amend<?php echo $iam; ?>"><i class="fa fa-edit"></i></button>
                                            </td>
                                        </tr>
                                    </form>
                                    <?php
                                    if (isset($_POST['btn_amend' . $iam]) || isset($_POST['btn_add_amend'])) {
                                        echo '<script>
                                        // Save the active tab in localStorage
                                        localStorage.setItem("activeTab", "tab_amendment");
                                        
                                        // Reload the page
                                        window.location.href = window.location.href;
                                    </script>';
                                    }
                                    $iam++;
                                }

                                ?>
                             <div class="col-md-8 col-md-offset-2">
                                    <?php if (isset($msg_amend) && !empty($msg_amend)) {
                                        echo $msg_amend;
                                    } ?><br />
                                </div>
                               
                            </tbody>
                        </table>
                        <div class="col-md-12" style="background-color: #e9e9e9;  padding: 10px;color: #040404; font-weight: bold;margin: 0; ">
                            <h3>Reason-Based Cancellations</h3>
                        </div>

                        <table class="table table-bordered">
                            <thead class="bg-primary">
                                <th width="5%">S.No</th>
                                <th width="30%">Title</th>
                                <th width="15%">Client</th>
                                <th width="15%">Interepreter</th>
                                <th width="15%">Option For</th>
                                <th width="55%">Type</th>
                                <th width="5%">Action</th>
                            </thead>
                            <tbody>
                                <?php
                                $iam_reason = 1;
                                $amend_q_reason = $acttObj->read_all("*", "amend_options", "type = 2 AND amend_for = 'cl'");
                                while ($row_amend = mysqli_fetch_assoc($amend_q_reason)) {
                                    if (isset($_POST['btn_amend' . $iam_reason])) {
                                        $amend_id = $_POST['amend_id' . $iam_reason];
                                        $value_amend = $_POST['value_amend' . $iam_reason];
                                        $effect_amend = $_POST['effect_amend' . $iam_reason];
                                        $effect_interp_amend = $_POST['effect_interp_amend' . $iam_reason];
                                        $amend_for_amend = $_POST['amend_for_amend' . $iam_reason];
                                        $type = $_POST['type' . $iam_reason];
                                        // $acttObj->update('amend_options', array('value' => $value_amend, 'effect' => $effect_amend, 'effect_interp' => $effect_interp_amend, 'amend_for' => $amend_for_amend), array('id' => $amend_id));
                                ?>
                                        <script>
                                            document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                            document.getElementById('tab_amendment').classList.add('in', 'active');
                                            document.getElementById('li_tab_auto_replies').classList.remove('active');
                                            document.getElementById('li_tab_amendment').classList.add('active');
                                        </script>
                                        <?php
                                        if ($acttObj->update('amend_options', array('value' => $value_amend, 'effect' => $effect_amend, 'effect_interp' => $effect_interp_amend, 'amend_for' => $amend_for_amend, 'type' => $type), array('id' => $amend_id))) {
                                        ?>
                                            <script>
                                                document.getElementById('<?php echo "value_amend" . $iam_reason; ?>').value = '<?php echo $value_amend; ?>';
                                                document.getElementById('<?php echo "effect_amend" . $iam_reason; ?>').value = '<?php echo $effect_amend; ?>';
                                                document.getElementById('<?php echo "effect_interp_amend" . $iam_reason; ?>').value = '<?php echo $effect_interp_amend; ?>';
                                                document.getElementById('<?php echo "amend_for_amend" . $iam_reason; ?>').value = '<?php echo $amend_for_amend; ?>';
                                                document.getElementById('<?php echo "type" . $iam_reason; ?>').value = '<?php echo $type; ?>';
                                            </script>
                                    <?php $msg_amend = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Record has been successfully updated .</b></span><br><br>';
                                        } else {
                                            $msg_amend = $amend_id . '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to update this record !</b></span><br><br>';
                                        }
                                    }
                                    ?>
                                    <form action="" method="post" class="register" enctype="multipart/form-data">
                                        <tr>
                                            <td align="left"><?php echo $iam_reason; ?>
                                                <input type="hidden" name="amend_id<?php echo $iam_reason; ?>" value="<?php echo $row_amend['id']; ?>" />
                                            </td>
                                            <td align="left">
                                                <input name="value_amend<?php echo $iam_reason; ?>" id="value_amend<?php echo $iam_reason; ?>" type="text" placeholder='Amend Title' value="<?php echo $row_amend['value']; ?>" class="w3-input w3-border w3-border-blue" />
                                            </td>
                                            <td align="left">
                                                <select name="effect_amend<?php echo $iam_reason; ?>" id="effect_amend<?php echo $iam_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option selected value="<?php echo $row_amend['effect']; ?>"><?php echo $row_amend['effect'] == '1' ? 'Chargeable' : 'Non Chargeable'; ?></option>
                                                    <option disabled value="">Client</option>
                                                    <option value="0">Non Chargeable</option>
                                                    <option value="1">Chargeable</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="effect_interp_amend<?php echo $iam_reason; ?>" id="effect_interp_amend<?php echo $iam_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option selected value="<?php echo $row_amend['effect_interp']; ?>"><?php echo $row_amend['effect_interp'] == '1' ? 'Affect interpreter' : 'Not affect interpreter'; ?></option>
                                                    <option disabled value="">Interpreter</option>
                                                    <option value="1">Affect interpreter</option>
                                                    <option value="0">Not affect interpreter</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="amend_for_amend<?php echo $iam_reason; ?>" id="amend_for_amend<?php echo $iam_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option value="" <?php echo (empty($row_amend['amend_for']) || $row_amend['amend_for'] === null) ? 'selected' : ''; ?>>- - - Option For - - -</option>
                                                    <option value="cl" <?php echo ($row_amend['amend_for'] == 'cl') ? 'selected' : ''; ?>>Client</option>
                                                    <option value="ls" <?php echo ($row_amend['amend_for'] == 'ls') ? 'selected' : ''; ?>>LSUK</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <select name="type<?php echo $iam_reason; ?>" id="type<?php echo $iam_reason; ?>" class="w3-input w3-border w3-border-blue" required>
                                                    <option value="" <?php echo (empty($row_amend['type']) || $row_amend['type'] === null) ? 'selected' : ''; ?>>- - - Type - - -</option>
                                                    <option value="1" <?php echo ($row_amend['type'] == '1') ? 'selected' : ''; ?>>Time Based</option>
                                                    <option value="2" <?php echo ($row_amend['type'] == '2') ? 'selected' : ''; ?>>Reason Based</option>
                                                </select>
                                            </td>
                                            <td align="left">
                                                <!-- Ensure the button is linked to the correct $iam -->
                                                <button data-toggle="tooltip" data-placement="top" type="submit" title="Edit this record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue w3-hover-blue" name="btn_amend<?php echo $iam_reason; ?>"><i class="fa fa-edit"></i></button>
                                            </td>
                                        </tr>
                                    </form>
                               
                                <?php
                                    if (isset($_POST['btn_amend' . $iam_reason]) || isset($_POST['btn_add_amend'])) {
                                        echo '<script>
                                localStorage.setItem("activeTab", "tab_amendment");
                                window.location.href = window.location.href;
                            </script>';
                                    }
                                    $iam_reason++;
                                }

                                ?>


<div class="col-md-8 col-md-offset-2">
                                    <?php if (isset($msg_amend) && !empty($msg_amend)) {
                                        echo $msg_amend;
                                    } ?><br />
                                </div>
                            </tbody>
                        </table>
                   

                     
                       


                        


                    </div>
                </div>
            </div>

            <div id="tab_corona" class="tab-pane fade"><br>
                <?php if (isset($_POST['btn_add_doc'])) {
                    $lang = mysqli_escape_string($con, $_POST['lang']);
                    $chk = $acttObj->read_specific('count(id) as counter', 'corona_safety', 'lang="' . $lang . '"'); ?>
                    <script>
                        document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                        document.getElementById('tab_corona').classList.add('in', 'active');
                        document.getElementById('li_tab_auto_replies').classList.remove('active');
                        document.getElementById('li_tab_corona').classList.add('active');
                    </script>
                <?php if ($chk['counter'] == 0) {
                        error_reporting(0);
                        $picName = $acttObj->upload_files("../file_folder/corona_safety", $_FILES['document']["name"], $_FILES['document']["type"], $_FILES['document']["tmp_name"], round(microtime(true)));
                        $data_corona = array('doc' => $picName, 'lang' => $lang);
                        if ($acttObj->insert('corona_safety', $data_corona)) {
                            $msg_new_corona = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Document has been successfully added .</b></span><br><br>';
                        } else {
                            $msg_new_corona = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to add new document !</b></span><br><br>';
                        }
                    } else {
                        $msg_new_corona = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Document already exists for this language !</b></span><br><br>';
                    }
                }
                ?>
                <form action="" method="post" class=" w3-card-4 w3-light-grey col-md-4" enctype="multipart/form-data"><br>
                    <h3 class="text-center">Add New Document</h3>
                    <div class="form-group">
                        <input name="lang" type="text" required placeholder='Enter Language Name' class="w3-input w3-border w3-border-blue w3-hover-blue" />
                    </div>
                    <div class="form-group">
                        <input name="document" type="file" required class="w3-input w3-border w3-border-blue w3-hover-blue" />
                    </div>
                    <div class="form-group">
                        <button type="submit" class="w3-button w3-white w3-border w3-border-blue w3-hover-blue" name="btn_add_doc"><i class="fa fa-check-circle"></i> Add Document</button>
                    </div>
                </form>
                <table class="table table-bordered">
                    <thead class="bg-primary">
                        <th width="5%">S.No</th>
                        <th width="25%">Document</th>
                        <th>Language</th>
                        <th>File Status</th>
                        <th width="5%">Action</th>
                    </thead>
                    <tbody>
                        <?php $ic = 1;
                        $corona_q = $acttObj->read_all("*", "corona_safety ORDER BY lang ASC", NULL);
                        while ($row_corona = mysqli_fetch_assoc($corona_q)) {
                            if (isset($_POST['btn_corona' . $ic])) {
                                $corona_id = $_POST['corona_id' . $ic];
                                $doc_name = $acttObj->unique_data('corona_safety', 'doc', 'id', $corona_id);
                                $lang = $_POST['lang' . $ic];
                                $document = $_FILES['document' . $ic]["name"];
                                if ($document != NULL) {
                                    error_reporting(0);
                                    if (unlink('../file_folder/corona_safety/' . $doc_name)) {
                                        $picName = $acttObj->upload_files("../file_folder/corona_safety", $_FILES['document' . $ic]["name"], $_FILES['document' . $ic]["type"], $_FILES['document' . $ic]["tmp_name"], round(microtime(true)));
                                        $acttObj->editFun('corona_safety', $corona_id, 'doc', $picName);
                                    }
                                }
                                if ($acttObj->editFun('corona_safety', $corona_id, 'lang', $lang)) { ?>
                                    <script>
                                        document.getElementById('<?php echo 'lang' . $ic; ?>').value = '<?php echo $lang; ?>';
                                        document.getElementById('tab_auto_replies').classList.remove('in', 'active');
                                        document.getElementById('tab_corona').classList.add('in', 'active');
                                        document.getElementById('li_tab_auto_replies').classList.remove('active');
                                        document.getElementById('li_tab_corona').classList.add('active');
                                    </script>
                            <?php $msg_corona = '<span class="alert alert-success col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Document has been successfully updated .</b></span><br><br>';
                                } else {
                                    $msg_corona = '<span class="alert alert-danger col-md-10 text-center fade in alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a><b>Failed to update this document !</b></span><br><br>';
                                }
                            }
                            ?>
                            <form action="" method="post" class="register" enctype="multipart/form-data">
                                <tr>
                                    <td align="left"><?php echo $ic++; ?><input type="hidden" name="<?php echo 'corona_id' . $ic; ?>" value="<?php echo $row_corona['id']; ?>" /> </td>
                                    <td align="left"><?php echo "<span class='label label-primary'>" . $row_corona['doc'] . "</span>"; ?> </td>
                                    <td align="left"><input name="<?php echo 'lang' . $ic; ?>" id="<?php echo 'lang' . $ic; ?>" type="text" placeholder='Language Name' value="<?php echo $row_corona['lang']; ?>" class="w3-input w3-border w3-border-blue" /> </td>
                                    <td align="left">
                                        <input name="<?php echo 'document' . $ic; ?>" type="file" <?php if ($row_corona['doc'] == '') { ?>required <?php } ?> placeholder='' id="<?php echo 'document' . $ic; ?>" class="w3-input w3-border w3-border-blue w3-hover-blue" />
                                    </td>
                                    <td align="left">
                                        <button data-toggle="tooltip" data-placement="top" type="submit" title="Edit this record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue w3-hover-blue" name="<?php echo 'btn_corona' . $ic; ?>"><i class="fa fa-pencil"></i></button>
                                    </td>
                                </tr>
                            </form>
                        <?php } ?>

                        <div class="col-md-8 col-md-offset-2">
                            <?php if (isset($msg_corona) && !empty($msg_corona)) {
                                echo $msg_corona;
                            } ?><br />
                            <?php if (isset($msg_new_corona) && !empty($msg_new_corona)) {
                                echo $msg_new_corona;
                            } ?><br />
                        </div>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <script>
        window.onload = function() {
            // Get the active tab from localStorage
            var activeTab = localStorage.getItem("activeTab");
            console.log(activeTab);
            if(activeTab == "tab_delete"){
                document.querySelector('#li_tab_delete a').click();
            }else if (activeTab === "tab_amendment") {
                document.getElementById("tab_auto_replies").classList.remove("in", "active");
                document.getElementById("tab_amendment").classList.add("in", "active");
                document.getElementById("li_tab_auto_replies").classList.remove("active");
                document.getElementById("li_tab_amendment").classList.add("active");
            } else if (activeTab === "tab_cancellation") {
                document.getElementById("tab_auto_replies").classList.remove("in", "active");
                document.getElementById("tab_cancellation").classList.add("in", "active");
                document.getElementById("li_tab_auto_replies").classList.remove("active");
                document.getElementById("li_tab_cancellation").classList.add("active");
            } else {
                // Default: Set tab_auto_replies as active
                document.getElementById("tab_auto_replies").classList.add("in", "active");
                document.getElementById("li_tab_auto_replies").classList.add("active");
            }

            // Clear the active tab from localStorage after setting it
            localStorage.removeItem("activeTab");
        };
    </script>

</body>

</html>