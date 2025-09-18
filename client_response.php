<?php if (session_id() == '' || !isset($_SESSION)) {
session_start();
}
include 'source/db.php';
include 'source/class.php';
error_reporting(1);
// $logged_id = $_SESSION['cust_userId'];
$table = "mileage_enquiry";
if (isset($_GET['order_id']) && isset($_GET['intrp'])) {
$encrypted_usr = urldecode($_GET['order_id']);
$encrypted_intrp = urldecode($_GET['intrp']);
$secret_key = substr(hash('sha256', 'a1zB9eT!9Xk2D7vJ0sT9H@3', true), 0, 16);
$user_id = openssl_decrypt($encrypted_usr, 'aes-128-ctr', $secret_key, 0, '1234567891011121');
$intrpId = openssl_decrypt($encrypted_intrp, 'aes-128-ctr', $secret_key, 0, '1234567891011121');
}
$data = $acttObj->read_specific("*", "mileage_enquiry", "order_ref='" . $user_id . "' AND interp_id=$intrpId");

if (isset($_POST['submit'])) {
    $status = $_POST['status'];
    $client_name = $_POST['client_name'];
    $client_email = $_POST['client_email'];
    $client_contact = $_POST['client_contact'];
    $user_id = $_POST['order_id'];
    $intrpId = $_POST['intrpId'];

$data = $acttObj->read_specific("mileage_enquiry.*,interpreter_reg.name as interp_name", "mileage_enquiry,interpreter_reg", " mileage_enquiry.interp_id=interpreter_reg.id AND mileage_enquiry.order_ref='" . $user_id . "' AND interp_id=$intrpId");
if ((isset($status) && !empty($status)) && (isset($client_name) && !empty($client_name)) && (isset($client_email) && !empty($client_email))) {
    
        $array = [
            'client_name' => $client_name,
            'client_email' => $client_email,
            'client_contact' => $client_contact,
            'status' => $status,
        ];
    
    
    $where = "order_ref ='" . $user_id."' AND interp_id=$intrpId";
    $acttObj->update($table, $array, $where);

    
    $acttObj->insert('jobnotes', array('jobNote' => "Client named $client_name ($client_email) ".($status==1?"Approved":"Declined")." the Travel Cost for this Booking<br>Interpreter Name: ".$data['interp_name']."<br>Addtional Miles: ".$data['mileage']."<br>Additional Travel time:".$data['mileage_cost'], 'tbl' => $data['order_type'], 'time' => $misc->sys_datetime_db(), 'fid' => $data['order_id'], 'submitted' => "Client : $client_name", 'dated' => date('Y-m-d')));
    
    // $status = strtoupper($status);
    $msg = "<div class='alert alert-success'>Thank you for your response: You selected '$status'.</div>";

    $subject="Travel Cost ".($status==1?"Approved":"Rejected")." - $user_id";

    $assignData = $acttObj->read_specific("source,target,assignDate,assignTime,assignDur,assignCity,buildingName,street,postCode,inchPerson,nameRef,orgRef", "interpreter", "id='".$data['order_id']."'");
    $interpData = $acttObj->read_specific("name,buildingName,line1,line2,line3,city,country,postCode", "interpreter_reg", "id='" . $data['interp_id'] . "'");
    $em = ($status==1?54:55);
    $row_format = $acttObj->read_specific("em_format", "email_format", "id=$em");
    $assignLocation = (!empty(trim($assignData['buildingName']))?$assignData['buildingName']:'') .(!empty(trim($assignData['street']))?(', '.$assignData['street']):'').(!empty(trim($assignData['assignCity']))?(', '.$assignData['assignCity']):'').(!empty(trim($assignData['postCode']))?(', '.$assignData['postCode']):'');
    $intLocation = (!empty(trim($interpData['buildingName']))?$interpData['buildingName']:'') .(!empty(trim($interpData['line1']))?(', '.$interpData['line1']):'').(!empty(trim($interpData['line2']))?(', '.$interpData['line2']):'').(!empty(trim($interpData['line3']))?(', '.$interpData['line3']):'').(!empty(trim($interpData['city']))?(', '.$interpData['city']):'').(!empty(trim($interpData['postCode']))?(', '.$interpData['postCode']):'');

    $shortCode   = ["[CLIENT_NAME]", "[INTERPRETER_NAME]", "[PROJECT_REF]", "[BOOKING_PERSON]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[SOURCE]", "[TARGET]", "[CLIENTREF]", "[ASSIGNLOCATION]", "[INTERPRETERLOCATION]", "[MILEAGE]", "[HOURS]"];
    $to_replace  = [$assignData['inchPerson'], $interpData['name'], $assignData['nameRef'], $assignData['inchPerson'], $assignData['assignDate'], $assignData['assignTime'], $assignData['source'], $assignData['target'], $assignData['orgRef'], "$assignLocation", "$intLocation", $data['mileage'], $data['mileage_cost']];
    // $message = "Client has <b>".($status==1?"ACCEPTED":"REJECTED")."</b> the Travel Time Approval Request for the Face to Face Job id $user_id";
    $message = str_replace($shortCode, $to_replace, $row_format['em_format']);

    try {
        $odt = array("interpreter" => 1,"telephone" => 2,"translation" => 3);
        $acttObj->insert(
            'cron_emails',
            array(
                "order_id" => $data['order_id'],
                "order_type" => $odt[$data['order_type']],
                "user_id" => $data['interp_id'],
                "send_from" => 'info@lsuk.org',
                "send_password" => 'xtxwzcvtdbjpftdj',
                "send_to" => 'info@lsuk.org',
                "subject" => $subject,
                "template_type" => 7,
                "template_body" => mysqli_real_escape_string($con,$message),
                "created_date" => date("Y-m-d H:i:s")
            )
        );
    } catch (Exception $e) { 
        echo "Error occured while informing LSUK";
    }
    header("Location: ".$_SERVER['REQUEST_URI']); exit;
} else {
    $msg = "<div class='alert alert-danger ' role='alert'>Please fill all the fields.</div>";
}
}
?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
<title>Customer Feedback</title>
<link href="images/favicon.ico" type="image/x-icon" rel="shortcut icon">
<link rel="stylesheet" type="text/css" href="landing-page/css/opensans-font.css">
<link rel="stylesheet" type="text/css" href="landing-page/fonts/line-awesome/css/line-awesome.min.css">

<link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">

<link rel="stylesheet" href="landing-page/css/style.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

</head>

<body class="form-v4">
<div class="logo-container" style=" margin-bottom: 10px; margin-top:10px; margin-left:20px;">
        <img id="logo_img" src="images/logo_lsuk.png" alt="LSUK" height="60" width="60">
    </div>

    <?php 
    if(empty($data)){
        echo "<div class='alert alert-warning col-md-12 col-md-offset-2 text-center h4'>This link has expired OR this Travel Cost Approval Request has been recalled by LSUK</div>";
        die();exit;
    }
    if (isset($msg)) echo $msg; ?>

<div class="page-content">
    <div class="form-v4-content">
        <div class="form-left">
            
            <h4>Approval Request for Interpreter Travel Cost</h4>
            <p class="text-1">We would like to inform you that while we do not have a local interpreter available, we can arrange for one to assist you. To proceed, we kindly request your approval for the additional travel cost beyond what is already covered in the hourly rate.</p>
            <h5>Booking Details:</h5>
            <?php 
            $assignData = $acttObj->read_specific("source,target,assignDate,assignTime,assignDur,assignCity,buildingName,street,postCode", "interpreter", "id='".$data['order_id']."'");
            $interpData = $acttObj->read_specific("buildingName,line1,line2,line3,city,country,postCode", "interpreter_reg", "id='" . $data['interp_id'] . "'");
            ?>
            <table class="table">
                <tbody>
                    <tr>
                        <th>Language Pair:</th>
                        <td><?php echo $assignData['source']." to ".$assignData['target']; ?></td>
                    </tr>
                    <tr>
                        <th>Date and Time:</th>
                        <td><?php echo "On ".$assignData['assignDate']." at ".$assignData['assignTime']; ?></td>
                    </tr>
                    <tr>
                        <th>Duration:</th>
                        <td><?php echo ($assignData['assignDur']/60)." hour"; ?></td>
                    </tr>
                    <tr>
                        <th>Location:</th>
                        <td><?php echo (!empty(trim($assignData['buildingName']))?$assignData['buildingName']:'') .(!empty(trim($assignData['street']))?(', '.$assignData['street']):'').(!empty(trim($assignData['assignCity']))?(', '.$assignData['assignCity']):'').(!empty(trim($assignData['postCode']))?(', '.$assignData['postCode']):''); ?></td>
                    </tr>
                    <tr>
                        <th>Proposed Interpreter's Location:</th>
                        <td><?php echo (!empty(trim($interpData['buildingName']))?$interpData['buildingName']:'') .(!empty(trim($interpData['line1']))?(', '.$interpData['line1']):'').(!empty(trim($interpData['line2']))?(', '.$interpData['line2']):'').(!empty(trim($interpData['line3']))?(', '.$interpData['line3']):'').(!empty(trim($interpData['city']))?(', '.$interpData['city']):'').(!empty(trim($interpData['postCode']))?(', '.$interpData['postCode']):''); ?></td>
                    </tr>
                </tbody>
            </table>
            <p class="text-2">Please confirm how you’d like to proceed by filling out the form and providing your response.</p>
            <div class="form-left-last">
                <a href="https://lsuk.org/contact_us.php" target="_blank" class="account">Contact Us</a>
            </div>
        </div>

        <form class="form-detail" action="" method="POST" id="myform">
            <h2>Response Form</h2>
            <input type="hidden" value="<?php echo $user_id; ?>" id="order_id" name="order_id" />
            <input type="hidden" value="<?php echo $intrpId; ?>" id="intrpId" name="intrpId" />
            <div class="form-group">
                <div class="form-row">
                    <label for="client_name">Your Full Name*:</label>
                    <input type="text" name="client_name" id="client_name" <?php if (!empty($data['client_name'])) {
                        echo "value=" . $data['client_name']." disabled";
                    }  ?> class="input-text" required>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <label for="client_email">Your Email*:</label>
                    <input type="text" name="client_email" id="client_email" <?php if (!empty($data['client_email'])) {
                        echo "value=" . $data['client_email']." disabled";
                    }  ?> class="input-text" required>
                </div>
            </div>
            <div class="form-group">
                <div class="form-row">
                    <label for="client_contact">Phone Number:</label>
                    <input type="text" name="client_contact" id="client_contact" <?php if (!empty($data['client_contact'])) {
                        echo "value=" . $data['client_contact']." disabled";
                    }  ?> class="input-text" required>
                </div>
            </div>
            <!-- Mileage Field -->
            <div class="form-group">
                <div class="form-row">
                    <label for="mileage">Additional Miles:</label>
                    <input type="text" name="mileage" id="mileage" <?php if (!empty($data['mileage'])) {
                                                                            echo "value=" . $data['mileage'];
                                                                        }  ?> class="input-text" required disabled>
                </div>
            </div>

            <!-- Cost Field -->
            <div class="form-group">
                <div class="form-row">
                    <label for="cost">Additional Travel Time:</label>
                    <input type="text" <?php if (!empty($data['mileage_cost'])) {
                                                echo "value='" . $data['mileage_cost']."'";
                                            }  ?> name="cost" id="cost" class="input-text" required disabled>
                </div>
            </div>

            <!-- Agree (Yes/No) Field -->
            <?php if (isset($data['status'])) {
                    if($data['status']==0){
                ?>
                <div class="form-group">
                <div class="form-row">
                    <label>Do you agree?</label><br>
                    <label class="radio-inline" style="margin-left: 15px;">
                        <input id="status_yes" type="radio"  name="status" value="1"> Yes
                    </label>
                    <label class="radio-inline">
                        <input id="status_no" type="radio"  name="status" value="2"> No
                    </label>
                </div>

            </div>

            <!-- Submit Button -->
            <div class="form-row-last">
                <button type="submit" name="submit" class="custom-btn btn-3"><span>Submit</span></button>
            </div>

                <?php
                    
            }elseif($data['status']==1){
                echo "Approved";
            }elseif($data['status']==2){
                echo "Rejected";
            } 
            } ?>
        </form>

    </div>
</div>
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>
    jQuery.validator.setDefaults({
        debug: true,
        success: function(label) {
            label.attr('id', 'valid');
        }
    });

    $("#FeedBack").validate({
        rules: {
            agree: {
                required: true
            }
        },
        messages: {
            agree: {
                required: "Please select an option"
            }
        },

        submitHandler: function(form) {
            form.submit();
        },

        invalidHandler: function(event, validator) {
            console.log("Form is invalid. Submission prevented.");
        }
    });
</script>

</body>

</html>