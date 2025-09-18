<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
include '../source/setup_email.php';
?>

<link rel="stylesheet" type="text/css" href="css/layout.css" />
<title>Booking Cancelation</title>
<br /><br /><br />
<div align="center">
    <span style="font-weight:bold; color:#09F;">Record ID: <?php echo $_GET['email_id']; ?></span><br /><br />
    <form action="" method="post">

        <br />
        <label>Job Cancelled By</label><br />
        <select id="order_cancelledby" name="order_cancelledby" onChange="myFunction()" required style="height:30px; width:250px;">

            <option value="">--Select--</option>
            <option>Client</option>
            <option>LSUK</option>
        </select><br /><br />

        <label>Reason / Remarks</label><br />
        <textarea name="order_cancel_remarks" rows="5" id="order_cancel_remarks" style="width:250px;" required="required"></textarea>
        <br /><br />

        Are you sure you want to cancel this booking ?<br><br><input type="submit" name="yes" value="Yes" />&nbsp;&nbsp;<input type="submit" name="no" value="No" />

    </form>
</div>

<?php
if (isset($_POST['yes'])) {
    if (session_id() == '' || !isset($_SESSION)) {
        session_start();
    }
    include 'db.php';
    include 'class.php';

    $table = $_GET['table'];
    $email_id = $_GET['email_id'];
    $client_email = '0';
    $int_email = '0';
    $order_cancelledby = $_POST['order_cancelledby'];
    $order_cancel_remarks = $_POST['order_cancel_remarks'];
    //update database table
    $acttObj->editFun($table, $email_id, 'order_cancelledby', $order_cancelledby);
    $acttObj->editFun($table, $email_id, 'order_cancel_remarks', $order_cancel_remarks);
    $acttObj->editFun($table, $email_id, 'order_cancled_bystaff', $_SESSION['UserName']);
    $acttObj->editFun($table, $email_id, 'orderCancelatoin', 1);
    $acttObj->editFun($table, $email_id, 'edited_by', $_SESSION['UserName']);
    $acttObj->editFun($table, $email_id, 'edited_date', date("Y-m-d H:i:s"));
    // $acttObj->new_old_table('hist_' . $table, $table, $email_id);

    $row = $acttObj->read_specific("$table.*,interpreter_reg.name,interpreter_reg.email,comp_reg.name as orgzName", "$table,interpreter_reg,comp_reg", "$table.intrpName=interpreter_reg.id AND $table.orgName=comp_reg.abrv AND $table.id=" . $email_id);

    $email = $row['email'];
    $source = $row['source'];
    $orgRef = $row['orgRef'];
    if ($table == 'interpreter' || $table == 'telephone') {
        $from_add = setupEmail::INFO_EMAIL;
        $from_password = setupEmail::INFO_PASSWORD;
        $assignDate = $misc->dated($row['assignDate']);
        $assignTime = $row['assignTime'];
        $orgzName = $row['orgzName'];
        if ($table == 'interpreter') {
            $buildingName = $row['buildingName'];
            $street = $row['street'];
            $assignCity = $row['assignCity'];
            $postCode = $row['postCode'];
        }
    } else {
        $from_add = setupEmail::TRANSLATION_EMAIL;
        $from_password = setupEmail::TRANSLATION_PASSWORD;
        $assignDate = $row['asignDate'];
    }
    $orgContact = $row['orgContact'];
    $inchEmail = $row['inchEmail'];
    $remrks = $row['remrks'];
    $name = $row['name'];

    if ($table == 'translation') {
        $to_add = $inchEmail;
        $subject = "Cancellation of " . $source . " translation request on " . $assignDate . " - Client Ref/Name (if any) " . $orgRef . "";
        $query_format = "SELECT em_format FROM email_format where id='9'";
        $result_format = mysqli_query($con, $query_format);
        $row_format = mysqli_fetch_array($result_format);
        //Get format from database
        $msg_body = $row_format['em_format'];
        $data   = ["[ORGCONTACT]", "[SOURCE]", "[ORGREF]", "[ASSIGNDATE]", "[CANCELLATION_REASON]"];
        $to_replace  = ["$orgContact", "$source", "$orgref", "$assignDate", "$order_cancel_remarks"];
        $message = str_replace($data, $to_replace, $msg_body);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP(); 
            $mail->Host = setupEmail::EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = $from_add;
            $mail->Password   = $from_password;
            $mail->SMTPSecure = setupEmail::SECURE_TYPE;
            $mail->Port       = setupEmail::SENDING_PORT;
            $mail->setFrom($from_add, setupEmail::FROM_NAME);
            $mail->addAddress($to_add);
            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            if ($mail->send()) {
                $mail->ClearAllRecipients();
                $mail->addAddress(setupEmail::LSUK_GMAIL);
                $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->send();
                $mail->ClearAllRecipients();
                $client_email = '1';
            } else {
                $client_email = '0';
            }
        } catch (Exception $e) { ?>
            <script>
                alert('<?php echo "Mailer Library Error!"; ?>');
            </script>
        <?php }
        //...........................for interpreter.............................
        $to_add = $email;
        $subject = "Cancellation of " . $source . " translation session on " . $assignDate . " ";
        $query_format = "SELECT em_format FROM email_format where id='10'";
        $result_format = mysqli_query($con, $query_format);
        $row_format = mysqli_fetch_array($result_format);
        //Get format from database
        $msg_body = $row_format['em_format'];
        $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[CANCELLATION_REASON]"];
        $to_replace  = ["$name", "$source", "$assignDate", "$order_cancel_remarks"];
        $message = str_replace($data, $to_replace, $msg_body);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP(); 
            $mail->Host = setupEmail::EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = $from_add;
            $mail->Password   = $from_password;
            $mail->SMTPSecure = setupEmail::SECURE_TYPE;
            $mail->Port       = setupEmail::SENDING_PORT;
            $mail->setFrom($from_add, setupEmail::FROM_NAME);
            $mail->addAddress($to_add);
            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            if ($mail->send()) {
                $mail->ClearAllRecipients();
                $mail->addAddress(setupEmail::LSUK_GMAIL);
                $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->send();
                $mail->ClearAllRecipients();
                $int_email = '1';
            } else {
                $int_email = '0';
            }
        } catch (Exception $e) { ?>
            <script>
                alert('<?php echo "Mailer Library Error!"; ?>');
            </script>
        <?php }

        if ($client_email == '1' && $int_email == '1') {
            echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
        } else {
            echo "<script>alert('Failed to send email to client and interpreter!');</script>";
        }
    }

    if ($table == 'telephone') {
        $to_add = $inchEmail;
        $communication_type = empty($row['comunic']) || $row['comunic'] == 11 ? " telephone interpreting" : " " . ($acttObj->read_specific("c_title", "comunic_types", "c_id=" . $row['comunic'])['c_title']);
        $subject = "Cancellation of " . $source . $communication_type . " request on " . $assignDate . " at " . $assignTime . " - Client Ref/Name (if any) " . $orgRef . "";
        $query_format = "SELECT em_format FROM email_format where id='11'";
        $result_format = mysqli_query($con, $query_format);
        $row_format = mysqli_fetch_array($result_format);
        //Get format from database
        $msg_body = $row_format['em_format'];
        $data   = ["[ORGCONTACT]", "[SOURCE]", "[ORGREF]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[CANCELLATION_REASON]"];
        $to_replace  = ["$orgContact", "$source", "$orgRef", "$assignDate", "$assignTime", "$order_cancel_remarks"];
        $message = str_replace($data, $to_replace, $msg_body);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP(); 
            $mail->Host = setupEmail::EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = $from_add;
            $mail->Password   = $from_password;
            $mail->SMTPSecure = setupEmail::SECURE_TYPE;
            $mail->Port       = setupEmail::SENDING_PORT;
            $mail->setFrom($from_add, setupEmail::FROM_NAME);
            $mail->addAddress($to_add);
            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            if ($mail->send()) {
                $mail->ClearAllRecipients();
                $mail->addAddress(setupEmail::LSUK_GMAIL);
                $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->send();
                $mail->ClearAllRecipients();
                $client_email = '1';
            } else {
                $client_email = '0';
            }
        } catch (Exception $e) { ?>
            <script>
                alert('<?php echo "Mailer Library Error!"; ?>');
            </script>
        <?php }
        //..............................for interpreter ............

        $to_add = $email;
        $subject = "Cancellation of " . $source . $communication_type . " session on " . $assignDate . " at " . $assignTime . "";
        $query_format = "SELECT em_format FROM email_format where id='12'";
        $result_format = mysqli_query($con, $query_format);
        $row_format = mysqli_fetch_array($result_format);
        //Get format from database
        $msg_body = $row_format['em_format'];
        $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[CANCELLATION_REASON]"];
        $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$order_cancel_remarks"];
        $message = str_replace($data, $to_replace, $msg_body);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP(); 
            $mail->Host = setupEmail::EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = $from_add;
            $mail->Password   = $from_password;
            $mail->SMTPSecure = setupEmail::SECURE_TYPE;
            $mail->Port       = setupEmail::SENDING_PORT;
            $mail->setFrom($from_add, setupEmail::FROM_NAME);
            $mail->addAddress($to_add);
            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            if ($mail->send()) {
                $mail->ClearAllRecipients();
                $mail->addAddress(setupEmail::LSUK_GMAIL);
                $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->send();
                $mail->ClearAllRecipients();
                $int_email = '1';
            } else {
                $int_email = '0';
            }
        } catch (Exception $e) { ?>
            <script>
                alert('<?php echo "Mailer Library Error!"; ?>');
            </script>
        <?php }

        if ($client_email == '1' && $int_email == '1') {
            echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
        } else {
            echo "<script>alert('Failed to send email to client and interpreter!');</script>";
        }
    }

    if ($table == 'interpreter') {
        $to_add = $inchEmail;
        $subject = "Cancellation of " . $source . " interpreter request on " . $assignDate . " at [" . $assignTime . " - Client Ref/Name (if any) " . $orgRef . "";
        $query_format = "SELECT em_format FROM email_format where id='13'";
        $result_format = mysqli_query($con, $query_format);
        $row_format = mysqli_fetch_array($result_format);
        //Get format from database
        $msg_body = $row_format['em_format'];
        $data   = ["[ORGCONTACT]", "[SOURCE]", "[ORGREF]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[BUILDINGNAME]", "[STREET]", "[ASSIGNCITY]", "[POSTCODE]", "[CANCELLATION_REASON]"];
        $to_replace  = ["$orgContact", "$source", "$orgRef", "$assignDate", "$assignTime", "$buildingName", "$street", "$assignCity", "$postCode", "$order_cancel_remarks"];
        $message = str_replace($data, $to_replace, $msg_body);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP(); 
            $mail->Host = setupEmail::EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = $from_add;
            $mail->Password   = $from_password;
            $mail->SMTPSecure = setupEmail::SECURE_TYPE;
            $mail->Port       = setupEmail::SENDING_PORT;
            $mail->setFrom($from_add, setupEmail::FROM_NAME);
            $mail->addAddress($to_add);
            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            if ($mail->send()) {
                $mail->ClearAllRecipients();
                $mail->addAddress(setupEmail::LSUK_GMAIL);
                $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->send();
                $mail->ClearAllRecipients();
                $client_email = '1';
            } else {
                $client_email = '0';
            }
        } catch (Exception $e) { ?>
            <script>
                alert('<?php echo "Mailer Library Error!"; ?>');
            </script>
        <?php }
        //..............................for interpreter ...........

        $to_add = $email;
        $subject = "Cancellation of " . $source . " interpreter session on " . $assignDate . " at " . $assignTime . "";
        $query_format = "SELECT em_format FROM email_format where id='14'";
        $result_format = mysqli_query($con, $query_format);
        $row_format = mysqli_fetch_array($result_format);
        //Get format from database
        $msg_body = $row_format['em_format'];
        $data   = ["[NAME]", "[SOURCE]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[BUILDINGNAME]", "[STREET]", "[ASSIGNCITY]", "[POSTCODE]", "[CANCELLATION_REASON]"];
        $to_replace  = ["$name", "$source", "$assignDate", "$assignTime", "$buildingName", "$street", "$assignCity", "$postCode", "$order_cancel_remarks"];
        $message = str_replace($data, $to_replace, $msg_body);

        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP(); 
            $mail->Host = setupEmail::EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = $from_add;
            $mail->Password   = $from_password;
            $mail->SMTPSecure = setupEmail::SECURE_TYPE;
            $mail->Port       = setupEmail::SENDING_PORT;
            $mail->setFrom($from_add, setupEmail::FROM_NAME);
            $mail->addAddress($to_add);
            $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            if ($mail->send()) {
                $mail->ClearAllRecipients();
                $mail->addAddress(setupEmail::LSUK_GMAIL);
                $mail->addReplyTo($from_add, setupEmail::FROM_NAME);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                $mail->send();
                $mail->ClearAllRecipients();
                $int_email = '1';
            } else {
                $int_email = '0';
            }
        } catch (Exception $e) { ?>
            <script>
                alert('<?php echo "Mailer Library Error!"; ?>');
            </script>
    <?php }

        if ($client_email == '1' && $int_email == '1') {
            echo "<script>alert('Email successfully sent to client and interpreter.');</script>";
        } else {
            echo "<script>alert('Failed to send email to client and interpreter!');</script>";
        }
    }
    ?>
    <script>
        window.close();
        window.onunload = refreshParent;

        function refreshParent() {
            window.opener.location.reload();
        }
    </script>
<?php }
if (isset($_POST['no'])) {
    echo "<script>window.close();</script>";
} ?>