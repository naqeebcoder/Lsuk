<?php
session_start();
include '../../source/setup_email.php';
include '../actions.php';
include '../../source/setup_sms.php';
$setupSMS = new setupSMS;

if($_SESSION['userId']) {
    if(isset($_POST['send_text_message']) && isset($_POST['message_body']) && isset($_POST['order_id']) && isset($_POST['order_type'])) {
        if (setupSMS::IS_ALLOWED == 1) {
            $interpreter_phone = str_replace(array("-","+"," "), "", ltrim(trim($_POST['interpreter_phone']),"0"));
            $message_body = trim($_POST['message_body']);
            $message_category = isset($_POST['message_category']) && !empty($_POST['message_category']) ? $_POST['message_category'] : 1;
            $response = array("status" => 0, "message" => "No action performed!");
            $done = $obj->insert("job_messages", array("order_id" => $_POST['order_id'], "order_type" => $_POST['order_type'], "message_category" => $message_category, "interpreter_id" => $_POST['interpreter_id'], "created_by" => $_SESSION['userId'], "message_body" => $message_body, "sent_to" => $interpreter_phone));
            $inserted_id = $obj->con->insert_id;
            $sms_response = $setupSMS->send_sms($interpreter_phone, $message_body . "\nhttps://lsuk.org/co.php?i=" . $inserted_id);
            $response['status'] = $sms_response['status'];
            $response['message'] = $sms_response['message'];
            if($done){
                if ($sms_response['status'] == 0) {
                    $obj->update("job_messages", array("status" => 0), "id=" . $inserted_id);
                }
            }else{
                $response['status'] = 0;
                $response['message'] = "Failed to send message! Try again";
            }
        } else {
            $response['status'] = 0;
            $response['message'] = "SMS service is blocked yet! Try again later";
        }
        echo json_encode($response);
    }
    // Send message to client for expenses
    // if(isset($_POST['send_client_message']) && isset($_POST['message_body']) && isset($_POST['order_id']) && isset($_POST['order_type'])) {
    //     if (setupSMS::IS_ALLOWED == 1) {
    //         $client_phone = str_replace(array("-","+"," "), "", ltrim(trim($_POST['client_phone']),"0"));
    //         $message_body = trim($_POST['message_body']);
    //         $message_category = 1;
    //         $verification_password = substr(number_format(time() * rand(), 0, '', ''), 0, 4);
    //         $response = array("status" => 0, "message" => "No action performed!");
    //         $done = $obj->insert("client_messages", array("order_id" => $_POST['order_id'], "order_type" => $_POST['order_type'], "message_category" => $message_category, "interpreter_id" => $_POST['interpreter_id'], "created_by" => $_SESSION['userId'], "message_body" => $message_body, "sent_to" => $client_phone, "password" => $verification_password));
    //         $inserted_id = $obj->con->insert_id;
    //         $sms_response = $setupSMS->send_sms($client_phone, $message_body . "\nVerification password:$verification_password\nhttps://lsuk.org/cl.php?i=" . $inserted_id);
    //         $response['status'] = $sms_response['status'];
    //         $response['message'] = $sms_response['message'];
    //         if($done){
    //             if ($sms_response['status'] == 0) {
    //                 $obj->update("client_messages", array("status" => 0), "id=" . $inserted_id);
    //             } else {
    //                 $array_job_types = array(1 => "interpreter", 2 => "telephone", 3 => "translation");
    //                 $obj->update($array_job_types[$_POST['order_type']], array("request_verify" => 1), "id=" . $_POST['order_id']);
    //             }
    //         }else{
    //             $response['status'] = 0;
    //             $response['message'] = "Failed to send message to client! Try again";
    //         }
    //     } else {
    //         $response['status'] = 0;
    //         $response['message'] = "SMS service is blocked yet! Try again later";
    //     }
    //     echo json_encode($response);
    // }
    if(isset($_POST['send_client_email']) && isset($_POST['order_id']) && isset($_POST['order_type'])) {
        $array_job_types = array(1 => "Face To Face", 2 => "Telephone", 3 => "Translation");
        $array_tables = array(1 => "interpreter", 2 => "telephone", 3 => "translation");
        $client_email = trim($_POST['client_email']);
        $message_category = 1;
        $verification_password = substr(number_format(time() * rand(), 0, '', ''), 0, 4);
        $response = array("status" => 0, "message" => "No action performed!");
        $done = $obj->insert("client_messages", array("order_id" => $_POST['order_id'], "order_type" => $_POST['order_type'], "message_type" => 2, "message_category" => $message_category, "interpreter_id" => $_POST['interpreter_id'], "created_by" => $_SESSION['userId'], "sent_to" => $client_email, "password" => $verification_password));
        $inserted_id = $obj->con->insert_id;
        $link = "https://lsuk.org/cl.php?i=" . $inserted_id;
        $row = $obj->read_specific("*", $array_tables[$_POST['order_type']], "id=" . $_POST['order_id']);
        if ($row['inchEmail']) {
            $db_assignDur = $_POST['order_type'] == 1 ? $row['hoursWorkd'] * 60 : $row['hoursWorkd'];
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
            // Interpreter record
            $get_interpreter_data = $obj->read_specific("name", "interpreter_reg", "id=" . $row['intrpName']);
            $int_name = ucwords($get_interpreter_data['name']);
            $append_table = "<table>";
            if ($_POST['order_type'] == 1) {
                $append_table .= "
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Project Reference Number</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['nameRef'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Type</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Face To Face interpreting Assignment</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Case Name or File Reference Number (if any)</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['orgRef'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['source'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['target'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $misc->dated($row['assignDate']) . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['assignTime'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Claimed Duration</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignment_duration . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Location</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['buildingName'] . ' ' . $row['street'] . ' ' . $row['assignCity'] . ' ' . $row['postCode'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Requested By</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . ucwords($row['inchPerson']) . "</td>
                </tr>";
            } else if ($_POST['order_type'] == 2) {
                $append_table .= "
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Project Reference Number</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['nameRef'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Communication Type</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . ($obj->read_specific("c_title", "comunic_types", "c_id=" . $row['comunic'])['c_title']) . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Case Name or Reference Number (if any)</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['orgRef'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['source'] . "</td>
                </tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['target'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $misc->dated($row['assignDate']) . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['assignTime'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Claimed Duration</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignment_duration . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Requested by</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['inchPerson'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Number of the Client to be Called</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['contactNo'] . "</td>
                </tr>
                <tr>
                <td style='border: 1px solid yellowgreen;padding:5px;'>Client Contact Number</td>
                <td style='border: 1px solid yellowgreen;padding:5px;'>" . $row['noClient'] . "</td>
                </tr>";
            }
            $append_table .= "<tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Name</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $int_name . "</td>
            </tr>
            </table>";
            $get_email_body = $obj->read_specific("em_format", "email_format", "id=50")['em_format'];
            $data_replace   = ["[CLIENT]", "[ASSIGNMENT_DETAILS]", "[PASSWORD]", "[LINK]"];
            $to_replace  = [ucwords($row['inchPerson']), $append_table, $verification_password, $link];
            $message_client = str_replace($data_replace, $to_replace, $get_email_body);
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
                $mail->addAddress($row['inchEmail']);
                $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                $mail->isHTML(true);
                $mail->Subject = "Confirmation for claimed assignment duration";
                $mail->Body    = $message_client;
                if ($mail->send()) {
                    $mail->ClearAllRecipients();
                    $client_msg = "Email has been sent to client. Thank you";
                    $is_sent = 1;
                } else {
                    $client_msg = "Email could not be sent to client. Try again";
                    $is_sent = 0;
                }
            } catch (Exception $e) {
                $client_msg = "Email could not be sent to client due to Mailer";
                $is_sent = 0;
            }
        }
        $response['status'] = $is_sent;
        $response['message'] = $client_msg;
        if($done){
            if ($response['status'] == 0) {
                $obj->update("client_messages", array("status" => 0), "id=" . $inserted_id);
            } else {
                $obj->update($array_tables[$_POST['order_type']], array("request_verify" => 1), "id=" . $_POST['order_id']);
            }
        }else{
            $response['status'] = 0;
            $response['message'] = "Failed to send message to client! Try again";
        }
        echo json_encode($response);
    }
    //Send Mileage Enquiry to Client
if(isset($_POST['send_enquiry']) && isset($_POST['tMiles']) && isset($_POST['chargMiles'])){
    $data = array('status' => 0, 'body' => '', 'mid' => 0);
    $assignId = $_POST['assignId'];
    $assignType = $_POST['assignType'];
    $interpId = $_POST['interpId'];
    $tMiles= $_POST['tMiles'];
    $chargMiles= $_POST['chargMiles'];

    $get_details = $obj->read_specific("$assignType.nameRef,$assignType.inchEmail,$assignType.inchEmail2,$assignType.assignDur",$assignType,"$assignType.id=$assignId");
    $nameRef = explode("/",$get_details['nameRef'])[2];
    $inchEmail = $get_details['inchEmail'];
    $inchEmail2 = $get_details['inchEmail2'];
    $assignDur = $get_details['assignDur'];

    $get_comp = $obj->read_specific("comp_reg.id", "$assignType,comp_reg", "comp_reg.abrv=".$assignType.".orgName AND ".$assignType.".id=$assignId")['id'];
    $get_pr = $obj->read_specific("subsidiaries.parent_comp","subsidiaries","subsidiaries.child_comp=$get_comp")['parent_comp']?:$get_comp;
    if(!empty($get_pr)) {
        $get_pr_mileage = $obj->read_specific("comp_reg.mileage,comp_reg.travel_time","comp_reg","comp_reg.id=$get_pr");
    }
    
    $miles_package = $assignDur/60;
    $travel_package_hr = $assignDur/60;
    if($miles_package>1){
        $get_pr_mileage['mileage'] = $get_pr_mileage['mileage']*$miles_package;
        $get_pr_mileage['travel_time'] = $get_pr_mileage['travel_time']* $travel_package_hr;
    }
    $miles = $tMiles - $get_pr_mileage['mileage'];
    $travel_time = $chargMiles - $get_pr_mileage['travel_time'];
    $travel_time_number =$travel_time; 
    $travel_time_converted = $travel_time." hours";

    // $travel_package_fhr = floor($assignDur/60);
    // $travel_package_min = $assignDur%60;
    // $travel_time_converted = ($travel_package_fhr>=1?"$travel_package_fhr hour ":"").($assignDur>60?"$travel_package_min min ":"");

    if($miles>0 || $travel_time>0){
        $miles = ($miles>0)?$miles:0;
        $travel_time_converted = ($travel_time>0)?$travel_time_converted:0;
        date_default_timezone_set('Europe/London');
        $date_time = date("Y-m-d H:i:s");
        $miles_data = array(
            'mileage' => $miles,
            'mileage_cost' => $travel_time_converted,
            'order_ref' => $nameRef,
            'order_id' => $assignId,
            'order_type' => $assignType,
            'interp_id' => $interpId,
            'status' => 0,
            'date' => $date_time,
        );
        $obj->insert("mileage_enquiry", $miles_data);
        $data['status'] = 1;
        $secret_key = substr(hash('sha256', 'a1zB9eT!9Xk2D7vJ0sT9H@3', true), 0, 16);
        $encrypted_usr = openssl_encrypt($nameRef, 'aes-128-ctr', $secret_key, 0, '1234567891011121');
        $encrypted_intrp = openssl_encrypt($interpId, 'aes-128-ctr', $secret_key, 0, '1234567891011121');
        $encrypted_usr = urlencode($encrypted_usr); 
        $encrypted_intrp = urlencode($encrypted_intrp); 


        $assignData = $obj->read_specific("source,target,assignDate,assignTime,assignDur,assignCity,buildingName,street,postCode,inchPerson,nameRef,orgRef", "interpreter", "id=$assignId");
        $interpData = $obj->read_specific("name,buildingName,line1,line2,line3,city,country,postCode", "interpreter_reg", "id=$interpId");
        $row_format = $obj->read_specific("em_format", "email_format", "id=53");
        $assignLocation = (!empty(trim($assignData['buildingName']))?$assignData['buildingName']:'') .(!empty(trim($assignData['street']))?(', '.$assignData['street']):'').(!empty(trim($assignData['assignCity']))?(', '.$assignData['assignCity']):'').(!empty(trim($assignData['postCode']))?(', '.$assignData['postCode']):'');
        $intLocation = (!empty(trim($interpData['buildingName']))?$interpData['buildingName']:'') .(!empty(trim($interpData['line1']))?(', '.$interpData['line1']):'').(!empty(trim($interpData['line2']))?(', '.$interpData['line2']):'').(!empty(trim($interpData['line3']))?(', '.$interpData['line3']):'').(!empty(trim($interpData['city']))?(', '.$interpData['city']):'').(!empty(trim($interpData['postCode']))?(', '.$interpData['postCode']):'');

        // Calculate
        $mileage_total = $miles * 0.45;
        $travel_total = $travel_time_number * 16;

        // Output
        $pound_symbol = mb_convert_encoding("£", 'UTF-16LE', 'UTF-8');
        $cost_cal = "<table style='border-collapse: collapse;'>
            <tr>
                <td style='border: 1px solid #ccc; padding: 6px;'>Additional Travel Time</td>
                <td style='border: 1px solid #ccc; padding: 6px;' align='right'>" . htmlspecialchars($travel_time_number) . " hours</td>
                <td style='border: 1px solid #ccc; padding: 6px;' align='right'>" . $pound_symbol . number_format($travel_total, 2) . "</td>
            </tr>
            <tr>
                <td style='border: 1px solid #ccc; padding: 6px;'>Additional Travel Mileage</td>
                <td style='border: 1px solid #ccc; padding: 6px;' align='right'>" . htmlspecialchars($miles) . " miles</td>
                <td style='border: 1px solid #ccc; padding: 6px;' align='right'>" . $pound_symbol . number_format($mileage_total, 2) . "</td>
            </tr>
        </table>";

        $shortCode   = ["[CLIENT_NAME]", "[INTERPRETER_NAME]", "[PROJECT_REF]", "[BOOKING_PERSON]", "[ASSIGNDATE]", "[ASSIGNTIME]", "[SOURCE]", "[TARGET]", "[CLIENTREF]", "[ASSIGNLOCATION]", "[INTERPRETERLOCATION]", "[MILEAGE]", "[HOURS]", "[LINK]"];
        $to_replace  = [$assignData['inchPerson'], $interpData['name'], $assignData['nameRef'], $assignData['inchPerson'], $assignData['assignDate'], $assignData['assignTime'], $assignData['source'], $assignData['target'], $assignData['orgRef'], "$assignLocation", "$intLocation", $cost_cal, $travel_time_converted,"https://lsuk.org/client_response.php?order_id=$encrypted_usr&intrp=$encrypted_intrp"];
        // $message = "Client has <b>".($status==1?"ACCEPTED":"REJECTED")."</b> the Travel Time Approval Request for the Face to Face Job id $user_id";
        $message = str_replace($shortCode, $to_replace, $row_format['em_format']);

        $get_mileage_data=$obj->read_specific("mileage_enquiry.id as mid,interpreter_reg.name,mileage_enquiry.order_type,mileage_enquiry.order_id,mileage_enquiry.order_ref","mileage_enquiry,interpreter_reg"," mileage_enquiry.interp_id=interpreter_reg.id AND mileage_enquiry.order_ref=$nameRef AND mileage_enquiry.interp_id=$interpId");
        $obj->insert('jobnotes', array('jobNote' => "Travel Cost Request initiated by ".$_SESSION['UserName']." for the interpreter ".$get_mileage_data['name'].". <br>Link: https://lsuk.org/lsuk_system/client_response.php?order_id=$encrypted_usr&intrp=$encrypted_intrp ", 'tbl' => $get_mileage_data['order_type'], 'time' => $misc->sys_datetime_db(), 'fid' => $get_mileage_data['order_id'], 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));        

        // $row_format = $obj->read_specific("em_format", "email_format", "id=53");
        // $data   = ["[MILEAGE]", "[HOURS]", "[LINK]"];
        // $to_replace  = ["$miles", "$travel_time_converted", "https://lsuk.org/client_response.php?order_id=$encrypted_usr"];
        // $message = str_replace($data, $to_replace, $row_format['em_format']);

        $subject="Travel Cost Approval Request for your Face to Face Booking $nameRef from LSUK";
        // $message = "Please click on the Link below to view and submit your response for mileage enquiry for LSUK interpreter: https://lsuk.org/client_response.php?order_id=$encrypted_usr";
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
            // $mail->addAddress("fahadsoftech47@gmail.com");
            $mail->addAddress($inchEmail);
            $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            if ($mail->send()) {
                $mail->ClearAllRecipients();
                if($inchEmail2!=''){
                    $mail->addAddress($inchEmail2);
                    $mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->send();
                    $mail->ClearAllRecipients();
                 }
                $data['status'] = 1;
                $data['mid'] = $get_mileage_data['mid'];
            } else {
                $data['status'] = 0;
                $data['msg'] = "Something Went Wrong";
            }
        } catch (Exception $e) { 
            $data['status'] = 0;
            $data['msg'] = "Something Went Wrong";
        }
    }else{
        $data['status'] = 0;
        $data['msg'] = "Travel Cost is with in the package";
    }
    echo json_encode($data);
    exit;
}

if(isset($_POST['calculateMiles']) && isset($_POST['tMiles']) && isset($_POST['chargMiles'])){
    $data = array('status' => 0, 'body' => '');
    $assignId = $_POST['assignId'];
    $assignType = $_POST['assignType'];
    $interpId = $_POST['interpId'];
    $tMiles= $_POST['tMiles'];
    $chargMiles= $_POST['chargMiles'];

    $get_details = $obj->read_specific("$assignType.nameRef,$assignType.inchEmail,$assignType.inchEmail2,$assignType.assignDur",$assignType,"$assignType.id=$assignId");
    $nameRef = explode("/",$get_details['nameRef'])[2];
    $inchEmail = $get_details['inchEmail'];
    $inchEmail2 = $get_details['inchEmail2'];
    $assignDur = $get_details['assignDur'];

    $get_comp = $obj->read_specific("comp_reg.id", "$assignType,comp_reg", "comp_reg.abrv=".$assignType.".orgName AND ".$assignType.".id=$assignId")['id'];
    $get_pr = $obj->read_specific("subsidiaries.parent_comp","subsidiaries","subsidiaries.child_comp=$get_comp")['parent_comp']?:$get_comp;
    if(!empty($get_pr)) {
        $get_pr_mileage = $obj->read_specific("comp_reg.mileage,comp_reg.travel_time","comp_reg","comp_reg.id=$get_pr");
    }
    
    $miles_package = $assignDur/60;
    $travel_package_hr = $assignDur/60;
    if($miles_package>1){
        $get_pr_mileage['mileage'] = $get_pr_mileage['mileage']*$miles_package;
        $get_pr_mileage['travel_time'] = $get_pr_mileage['travel_time']* $travel_package_hr;
    }
    $miles = $tMiles - $get_pr_mileage['mileage'];
    $travel_time = $chargMiles - $get_pr_mileage['travel_time'];

    $travel_time_converted = $travel_time." hours";

    // $travel_package_fhr = floor($assignDur/60);
    // $travel_package_min = $assignDur%60;
    // $travel_time_converted = ($travel_package_fhr>=1?"$travel_package_fhr hour ":"").($assignDur>60?"$travel_package_min min ":"");

    if($miles>0 || $travel_time>0){
        $miles = ($miles>0)?$miles:0;
        $travel_time_converted = ($travel_time>0)?$travel_time_converted:0;
    }
    $data['status']=1;
    $data['body']="Additional Miles: $miles<br>Additional Travel Time: $travel_time_converted<br>";
    // $data['body']="Additional Miles: $miles<br>Additional Travel Time: $travel_time_converted<br><br><br>id:$assignId<br> duration:$assignDur <br> adM: $miles_package <br>adT: $travel_package_hr<br>";
    echo json_encode($data);
    exit;
}

if(isset($_POST['int_availability_changed']) && isset($_POST['mileage_id'])){
    $data = array('status' => 0, 'body' => '');
    $mileage_id = $_POST['mileage_id'];
    $get_mileage_data=$obj->read_specific("order_type,order_id,order_ref","mileage_enquiry","id=$mileage_id");
    date_default_timezone_set('Europe/London');
    $av_update_time = date("Y-m-d H:i:s");
    if($obj->update("mileage_enquiry", array("status" => 2,"av_changed_by" => $_SESSION['userId'],"av_update_time" => $av_update_time), "id=" . $mileage_id)){
    $data['status'] = 1;
    $data['msg'] = "Availability Updated Successfully";
    $obj->insert('jobnotes', array('jobNote' => "Interpreter Availability Updated by ".$_SESSION['UserName']." after Travel Cost Approval from Client", 'tbl' => $get_mileage_data['order_type'], 'time' => $misc->sys_datetime_db(), 'fid' => $get_mileage_data['order_id'], 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));

    }else{
        $data['status'] = 0;
        $data['msg'] = "Error Occured";
    }
    echo json_encode($data);
    exit;
}

if(isset($_POST['cancelTrvRequest']) && isset($_POST['mileage_id'])){
    $data = array('status' => 0, 'body' => '');
    $mileage_id = $_POST['mileage_id'];
    $get_mileage_data=$obj->read_specific("interpreter_reg.name,mileage_enquiry.order_type,mileage_enquiry.order_id,mileage_enquiry.order_ref","mileage_enquiry,interpreter_reg"," mileage_enquiry.interp_id=interpreter_reg.id AND mileage_enquiry.id=$mileage_id");
    if($obj->delete("mileage_enquiry", "id=$mileage_id")){
        $data['status'] = 1;
        $data['msg'] = "Travel Cost Request Cancelled Successfully";
        $obj->insert('jobnotes', array('jobNote' => "Travel Cost Request Cancelled by ".$_SESSION['UserName']." for the interpreter".$get_mileage_data['name'], 'tbl' => $get_mileage_data['order_type'], 'time' => $misc->sys_datetime_db(), 'fid' => $get_mileage_data['order_id'], 'submitted' => $_SESSION['UserName'], 'dated' => date('Y-m-d')));
    }else{
        $data['status'] = 0;
        $data['msg'] = "Error Occured While Cancelling the Travel Cost Request";
    }
    echo json_encode($data);
    exit;
}
}
