<?php include '../source/setup_email.php';
//get job id details and company
$table = $_GET['table'];
if ($table != 'translation') {
    $feedback_link = "https://lsuk.org/feedback_confirmation.php?id=" . base64_encode($assign_id) . "&tbl=" . base64_encode($table);
    if ($obj->read_specific("count(*) as counter", "feedback_requests", "order_id=" . $assign_id . " AND table_name='" . $table . "'")['counter'] == 0) {
        $obj->insert("feedback_requests", array(
            "order_id" => $assign_id,
            "table_name" => $table,
            "user_id" => $_SESSION['userId'],
            "send_by" => "l"
        ));
    }
}
$row = $obj->read_specific("$table.*,comp_reg.name as orgzName,comp_reg.type_id", "$table,comp_reg", "$table.orgName=comp_reg.abrv AND $table.id=" . $assign_id);

$get_interpreter_data = $obj->read_specific("name,contactNo,email,country,postCode,city", "interpreter_reg", "id=" . $row['intrpName']);
$int_name = ucwords($get_interpreter_data['name']);
$int_contact = $get_interpreter_data['contactNo'];
$int_city = $get_interpreter_data['city'];
$int_postcode = $get_interpreter_data['postCode'];
$int_postcode= explode(' ', $int_postcode)[0];
    //get job id details and company
$row = $obj->read_specific("$table.*,comp_reg.name as orgzName,comp_reg.type_id", "$table,comp_reg", "$table.orgName=comp_reg.abrv AND $table.id=" . $assign_id);
    //check if it is law farm lla funded
    //enable the below line if need for all lla funded company_types replace the condition to enable disable lla_funcded currenly working for 15 only
    //$type = $obj->read_specific("company_types.id", "company_types, comp_type", "comp_type.id =". $row['type_id'] ."AND comp_type.company_type_id = company_types.id");
if($row['type_id']==15){
    $lla_funded=true;
}else{
    $lla_funded=false;
}
$rowData = $row;
$porder = $row['porder'];
$source = $row['source'];
$aloct_by = $row['aloct_by'];
$target = $row['target'];
$orgRef = $row['orgRef'];
$inchEmail = $row['inchEmail'];
$inchEmail2 = $row['inchEmail2'];
$orgContact = $row['orgContact'];
$I_Comments = $row['I_Comments'] ?: 'Nil';
$company_rate_id = $row['company_rate_id'];
$company_rate_data = !empty($row['company_rate_data']) ? (array) json_decode($row['company_rate_data']) : array();
if (!empty($company_rate_data['title'])) {
    $extra_title_parts = explode("-", $company_rate_data['title']);
    $bookinType = trim($extra_title_parts[0]);
} else {
    $bookinType = $row['bookinType'];
}
$nameRef = $row['nameRef'];
$comunic = isset($row['comunic']) ? $row['comunic'] : '';
$orgzName = $row['orgzName'];

if ($table == 'interpreter' || $table == 'telephone') {
    $from_add = setupEmail::INFO_EMAIL;
    $from_password = setupEmail::INFO_PASSWORD;
    $gender = $row['gender'];
    $inchNo = $row['inchNo'];
    $line1 = $row['line1'];
    $inchRoad = $row['inchRoad'];
    $inchCity = $row['inchCity'];
    $inchPcode = $row['inchPcode'];
    $assignDate = $misc->dated($row['assignDate']);
    $assignTime = $row['assignTime'];
    $db_assignDur = (int)$row['assignDur']; // total minutes
    $hours   = intdiv($db_assignDur, 60);
    $minutes = $db_assignDur % 60;

    if ($hours > 0) {
        $assignDur = $hours . ' hour' . ($hours > 1 ? 's' : '');
        if ($minutes > 0) {
            $assignDur .= ' ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }
    } else {
        $assignDur = $minutes . ' minute' . ($minutes !== 1 ? 's' : '');
    }
    $orgzName = $row['orgzName'];
    $extra_assignment_details = "";
    if ($table == 'interpreter') {
        $dbs_checked = $row['dbs_checked'];
        if ($dbs_checked == 0) {
            $dbs_checked = 'Yes';
        } else {
            $dbs_checked = 'No';
        }
        $buildingName = $row['buildingName'];
        $street = $row['street'];
        $assignCity = $row['assignCity'];
        $postCode = $row['postCode'];
        $order_type = 1;
        $assignment_type = " Face To Face interpreting";
        $project_label = "Face To Face";
        $advance_message_body = "";
        $extra_assignment_details = "location $buildingName $street $assignCity $postCode";
    }

    $assignIssue = $row['assignIssue'];
    $inchPerson = $row['inchPerson'];
    $remrks = $row['remrks'] ?: 'Nil';

    if ($table == 'telephone') {
        $comunic = $row['comunic'];
        $write_comunic = $obj->read_specific("c_title", "comunic_types", "c_id=" . $comunic)['c_title'];
        $ClientContact = $row['contactNo'];
        $noClient = $row['noClient'];
        $order_type = 2;
        $assignment_type = " Telephone interpreting";
        $project_label = "Telephone";
        $advance_message_body = "";
        $communication_type = empty($comunic) || $comunic == 11 ? $assignment_type : " " . $write_comunic;
    }
}

if ($table == 'translation') {
    $from_add = setupEmail::TRANSLATION_EMAIL;
    $from_password = setupEmail::TRANSLATION_PASSWORD;
    $asignDate = $misc->dated($asignDate);
    $deliveryType = $row['deliveryType'];
    $transType = $row['transType'];
    $deliverDate = $misc->dated($row['deliverDate']);
    $docType = $row['docType'];
    $trans_detail = $row['trans_detail'];
    $order_type = 3;
    $assignment_type = " Translation";
    $project_label = "Translation";
} else {
    //Add advance notification if assignment date and allocation date are same
    $row_format = $obj->read_specific("em_format", "email_format", "id=44");
    $data   = ["[INTERPRETER_NAME]", "[ASSIGNMENT_DETAILS]"];
    $to_replace  = [$int_name, $source . "$assignment_type Reference ID " . $row['reference_no'] . " at ($assignTime) on ($assignDate) $extra_assignment_details"];
    $advance_message_body = str_replace($data, $to_replace, $row_format['em_format']);

    $assignment_time = $row['assignDate'] . ' ' . $row['assignTime'];
    if ($row['assignDate'] == date('Y-m-d') && $row['assignTime'] > date('H:i:s')) {
        $sms_message_body = $order_type == 1 ? "Hi\nWe are reminding you for Face To Face session at " . substr($row['assignTime'], 0, 5) . ' today. Missing or lateness will result in deductions.' : "Hi\nWe are reminding you for " . ($row['comunic'] == 7 ? "Teams" : trim($communication_type)) . " session at " . substr($row['assignTime'], 0, 5) . " today. Missing or lateness will result in deductions.";
        if ($order_type == 2 && $row['comunic'] == 7) {
            $sms_message_body .= "\nMake sure you have Teams Link";
        }
        $obj->insert(
            'cron_emails_important',
            array(
                "order_id" => $assign_id,
                "order_type" => $order_type,
                "user_id" => $row['intrpName'],
                "send_from" => $from_add,
                "send_password" => $from_password,
                "send_to" => $get_interpreter_data['email'],
                "subject" => "Advance reminder for " . $source . $assignment_type . " Reference ID " . $row['reference_no'] . " today",
                "template_type" => 5,
                "template_data" => '{"specific_interval":"1","assignment_time":"' . $assignment_time . '","send_to_phone":"' . $int_contact . '","send_to_country":"' . $row['country'] . '","sms_message_body":"' . $sms_message_body . '"}',
                "template_body" => $obj->con->real_escape_string($advance_message_body),
                "created_date" => date("Y-m-d H:i:s")
            )
        );
    }
}
$purOrder_email="";
$check_po = $obj->read_specific("po_req", "comp_reg", "abrv='" . $row['orgName']."'")['po_req'];
if ($check_po) {
    $purOrderFormatted = (isset($porder) && !empty($porder)) ? $porder : '<span style="color:red;">Missing</span>';
    if(empty($porder))
    $purOrder_email = "<tr><td style='border: 1px solid yellowgreen;padding:5px;'>Purchase Order Request Email:</td><td style='border: 1px solid yellowgreen;padding:5px;'>" .
                (!empty($row['porder_email']) ? htmlspecialchars($row['porder_email']) : 'Missing') . 
            "</td></tr>";
} else {
    $purOrderFormatted = 'N/A';
}
//adding interp city and postcode for lla_funded law firms only 
if($lla_funded){
    $lla_funded_tr="
    <tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Location</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $int_city . "</td>
    </tr><tr>
    <td style='border: 1px solid yellowgreen;padding:5px;'>Post Code</td>
    <td style='border: 1px solid yellowgreen;padding:5px;'>" . $int_postcode. "</td>
    </tr>";
}else{
    $lla_funded_tr="";
}
//to inchEmail,inchEmail2 (client #1): translation
//$deliverDate
if ($table == 'translation') {
    //Get format from database
    $row_format_ack = $obj->read_specific("em_format", "email_format", "id=6");
    $ack_body = $row_format_ack['em_format'];
    $to_add = $inchEmail;
    $languagePair = "$source to $target";
    $subject = "Booking Confirmation - $languagePair - " . $nameRef;
    $append_table = "<table>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Project Reference Number</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $nameRef . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Reference Number</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . (trim($orgRef ?? '') !== '' ? $orgRef : "<span style='color:red;'>Missing</span>") . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Purchase Order Number</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $purOrderFormatted . "</td>
        </tr>
        ".$purOrder_email." 
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Document Type</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $obj->read_specific("tc_title", "trans_cat", "tc_id=" . $docType)['tc_title'] . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Category</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $obj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR '<br>') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title'] . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Translation Type(s)</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $obj->read_specific("GROUP_CONCAT(CONCAT(tt_title)  SEPARATOR '<br>') as tt_title", "trans_types", "tt_id IN (" . $trans_detail . ")")['tt_title'] . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Type</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliveryType . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid yellowgreen;padding:5px;'>Delivery Date</td>
        <td style='border: 1px solid yellowgreen;padding:5px;'>" . $deliverDate . "</td>
        </tr>
        <tr>
        "
        .$lla_funded_tr.
        "
        <td style='border: 1px solid yellowgreen;padding:5px;'>Notes (if any) </td>
        <td style='border: 1px solid yellowgreen;padding:5px;color:red;'>" . $I_Comments . "</td>
        </tr>";
    $append_table .= "<tr>";
    $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
    $append_table .= "Interpreter Name";
    $append_table .= "</td>";
    $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
    $append_table .= $int_name;
    $append_table .= "</td>";
    $append_table .= "</tr>";

    if ($row['orgName'] != '' && substr($row['orgzName'], 0, 3) == 'VHS') {

        $append_table .= "<tr>";
        $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
        $append_table .= "Interpreter Contact";
        $append_table .= "</td>";
        $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
        $append_table .= $int_contact;
        $append_table .= "</td>";
        $append_table .= "</tr>";
    }
    $append_table .= "</table>";
    $data = ["[ORGCONTCAT]", "[APPENDTABLE]", "[INTERPRETING_ADMIN_TEAM]"];
    $to_replace = ["$orgContact", "$append_table", $_SESSION['UserName']];
    $message = str_replace($data, $to_replace, $ack_body);
    //to inchEmail (client #1)
    //php mailer used at top
    try {
        if ($to_add) {
            $obj->insert(
                'cron_emails',
                array(
                    "order_id" => $assign_id,
                    "order_type" => $order_type,
                    "user_id" => $row['order_company_id'],
                    "user_type" => 2,
                    "send_from" => $from_add,
                    "send_password" => $from_password,
                    "send_to" => $to_add,
                    "subject" => $subject,
                    "template_type" => 6,
                    "template_data" => '{"interpreter_email":"' . $get_interpreter_data['email'] . '"}',
                    "template_body" => $obj->con->real_escape_string($message),
                    "created_date" => date("Y-m-d H:i:s")
                )
            );
        }
        if ($inchEmail2) {
            $obj->insert(
                'cron_emails',
                array(
                    "order_id" => $assign_id,
                    "order_type" => $order_type,
                    "user_id" => $row['order_company_id'],
                    "user_type" => 2,
                    "send_from" => $from_add,
                    "send_password" => $from_password,
                    "send_to" => $inchEmail2,
                    "subject" => $subject,
                    "template_type" => 6,
                    "template_body" => $obj->con->real_escape_string($message),
                    "created_date" => date("Y-m-d H:i:s")
                )
            );
        }
        echo "<script>alert('Job has been Allocated Successfully !');</script>";
    } catch (Exception $e) {
        echo "<script>alert('" . $e->getMessage() . "');</script>"; ?>
        <!-- <script>alert("Emails could not be sent. Try to send manual emails to client and interpreter!");</script> -->
    <?php
    }
}

//to inchEmail,inchEmail2 (client #2): interpreter
if ($table == 'interpreter') {
    //Get format from database
    $row_format_ack = $obj->read_specific("em_format", "email_format", "id=4");
    $ack_body = $row_format_ack['em_format'];
    $to_add = $inchEmail;
    $languagePair = "$source to $target";
    $subject = "Booking Confirmation - $languagePair - " . $nameRef;
    $append_table = "<table>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Project Reference Number</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $nameRef . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Type</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignment_type . " Assignment</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Case Name or File Reference Number (if any)</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . (trim($orgRef ?? '') !== '' ? $orgRef : "<span style='color:red;'>Missing</span>") . "</td>
            </tr>
            <tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Purchase Order Number</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $purOrderFormatted . "</td>
            </tr> 
            ".$purOrder_email." 
            <tr>           
            <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignTime . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>DBS Interpreter Required ?</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $dbs_checked . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDur . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Location</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . (!empty(trim($buildingName))?htmlspecialchars($buildingName,ENT_QUOTES, 'UTF-8'):'') . (!empty(trim($street))?(', '.$street):'') . (!empty(trim($assignCity))?(', '.$assignCity):'') . (!empty(trim($postCode))?(', '.$postCode):'') . "</td>
            </tr>

            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Gender</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender . "</td>
            </tr>

            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Requested By</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchPerson . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Type</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $bookinType . "</td>
            </tr>
            "
            .$lla_funded_tr.
            "
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Notes (if any) </td>
            <td style='border: 1px solid yellowgreen;padding:5px;color:red;'>" . $I_Comments . "</td>
            </tr>";
    $append_table .= "<tr>";
    $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
    $append_table .= "Interpreter Name";
    $append_table .= "</td>";
    $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
    $append_table .= $int_name;
    $append_table .= "</td>";
    $append_table .= "</tr>";

    if ($row['orgName'] != '' && substr($row['orgzName'], 0, 3) == 'VHS') {

        $append_table .= "<tr>";
        $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
        $append_table .= "Interpreter Contact";
        $append_table .= "</td>";
        $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
        $append_table .= $int_contact;
        $append_table .= "</td>";
        $append_table .= "</tr>";
    }
    $append_table .= "</table>";
    $data = ["[INCHPERSON]", "[APPENDTABLE]", "[FEEDBACK_LINK]", "[INTERPRETING_ADMIN_TEAM]"];
    $to_replace = ["$inchPerson", "$append_table", "$feedback_link", $_SESSION['UserName']];
    $message = str_replace($data, $to_replace, $ack_body);
    //to inchEmail (client #2)
    //php mailer used at top
    try {
        if ($to_add) {
            $obj->insert(
                'cron_emails',
                array(
                    "order_id" => $assign_id,
                    "order_type" => $order_type,
                    "user_id" => $row['order_company_id'],
                    "user_type" => 2,
                    "send_from" => $from_add,
                    "send_password" => $from_password,
                    "send_to" => $to_add,
                    "subject" => $subject,
                    "template_type" => 6,
                    "template_data" => '{"interpreter_email":"' . $get_interpreter_data['email'] . '"}',
                    "template_body" => $obj->con->real_escape_string($message),
                    "created_date" => date("Y-m-d H:i:s")
                )
            );
        }
        if ($inchEmail2) {
            $obj->insert(
                'cron_emails',
                array(
                    "order_id" => $assign_id,
                    "order_type" => $order_type,
                    "user_id" => $row['order_company_id'],
                    "user_type" => 2,
                    "send_from" => $from_add,
                    "send_password" => $from_password,
                    "send_to" => $inchEmail2,
                    "subject" => $subject,
                    "template_type" => 6,
                    "template_body" => $obj->con->real_escape_string($message),
                    "created_date" => date("Y-m-d H:i:s")
                )
            );
        }
        echo "<script>alert('Job has been Allocated Successfully !');</script>";
    } catch (Exception $e) {
        echo "<script>alert('" . $e->getMessage() . "');</script>"; ?>
        <!-- <script>alert("Emails could not be sent. Try to send manual emails to client and interpreter!");</script> -->
    <?php
    }
}

//to inchEmail,inchEmail2 (client #3): telephone
if ($table == 'telephone') {
    //Get format from database
    $row_format_ack = $obj->read_specific("em_format", "email_format", "id=5");
    $ack_body = $row_format_ack['em_format'];
    $to_add = $inchEmail;
    $languagePair = "$source to $target";
    $subject = "Booking Confirmation - $languagePair - " . $nameRef;
    $append_table = "<table>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Project Reference Number</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $nameRef . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Communication Type</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $write_comunic . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Case Name or Reference Number (if any)</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . (trim($orgRef ?? '') !== '' ? $orgRef : "<span style='color:red;'>Missing</span>") . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Purchase Order Number</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $purOrderFormatted . "</td>
            </tr> 
            ".$purOrder_email." 
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Source Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $source . "</td>
            </tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Target Language</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $target . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Date</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDate . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Time</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignTime . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment Duration</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $assignDur . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Requested by</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $inchPerson . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Service User's Number</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $ClientContact . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Assignment In-Charge's Number</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $noClient . "</td>
            </tr>

            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Interpreter Gender Requested</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $gender . "</td>
            </tr>
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Booking Type</td>
            <td style='border: 1px solid yellowgreen;padding:5px;'>" . $bookinType . "</td>
            </tr>
            "
            .$lla_funded_tr.
            "
            <tr>
            <td style='border: 1px solid yellowgreen;padding:5px;'>Notes (if any) </td>
            <td style='border: 1px solid yellowgreen;padding:5px;color:red;'>" . $I_Comments . "</td>
            </tr>";
    $append_table .= "<tr>";
    $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
    $append_table .= "Interpreter Name";
    $append_table .= "</td>";
    $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
    $append_table .= $int_name;
    $append_table .= "</td>";
    $append_table .= "</tr>";
    //Send email address also incase of zoom, video and microsoft teams
    $append_email = '';
    if ($comunic == 7 || $comunic == 3 || $comunic == 5) {
        $append_email .= "<tr>";
        $append_email .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
        $append_email .= "Interpreter Email";
        $append_email .= "</td>";
        $append_email .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
        $append_email .= $get_interpreter_data['email'];
        $append_email .= "</td>";
        $append_email .= "</tr>";
    }

    if ($row['orgName'] != '' && substr($row['orgzName'], 0, 3) == 'VHS') {

        $append_table .= "<tr>";
        $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
        $append_table .= "Interpreter Contact";
        $append_table .= "</td>";
        $append_table .= "<td style='border: 1px solid yellowgreen;padding:5px;'>";
        $append_table .= $int_contact;
        $append_table .= "</td>";
        $append_table .= "</tr>";
        $append_table .= $append_email;
    }
    $append_table .= "</table>";
    $data = ["[ORGCONTCAT]", "[APPENDTABLE]", "[FEEDBACK_LINK]", "[INTERPRETING_ADMIN_TEAM]"];
    $to_replace = ["$orgContact", "$append_table", "$feedback_link", $_SESSION['UserName']];
    $message = str_replace($data, $to_replace, $ack_body);

    //to client #3
    //php mailer used at top
    try {
        if ($to_add) {
            $obj->insert(
                'cron_emails',
                array(
                    "order_id" => $assign_id,
                    "order_type" => $order_type,
                    "user_id" => $row['order_company_id'],
                    "user_type" => 2,
                    "send_from" => $from_add,
                    "send_password" => $from_password,
                    "send_to" => $to_add,
                    "subject" => $subject,
                    "template_type" => 6,
                    "template_data" => '{"interpreter_email":"' . $get_interpreter_data['email'] . '"}',
                    "template_body" => $obj->con->real_escape_string($message),
                    "created_date" => date("Y-m-d H:i:s")
                )
            );
        }
        if ($inchEmail2) {
            $obj->insert(
                'cron_emails',
                array(
                    "order_id" => $assign_id,
                    "order_type" => $order_type,
                    "user_id" => $row['order_company_id'],
                    "user_type" => 2,
                    "send_from" => $from_add,
                    "send_password" => $from_password,
                    "send_to" => $inchEmail2,
                    "subject" => $subject,
                    "template_type" => 6,
                    "template_body" => $obj->con->real_escape_string($message),
                    "created_date" => date("Y-m-d H:i:s")
                )
            );
        }
        echo "<script>alert('Job has been Allocated Successfully !');</script>";
    } catch (Exception $e) {
        echo $e->getMessage();
    ?>
        <!-- <script>alert("Emails could not be sent. Try to send manual emails to client and interpreter!");</script> -->

<?php
    }
}
$um = $email; ?>