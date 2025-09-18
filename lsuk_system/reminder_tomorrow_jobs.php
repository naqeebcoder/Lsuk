<?php set_include_path('/home/customer/www/lsuk.org/public_html/');
echo "Server time: " . date("Y-m-d H:i:s");
include 'lsuk_system/actions.php';

if (date("H:i") == "15:00") {
    echo " - LSUK time: " . date("Y-m-d H:i:s");
    //Add SMS config
    include 'source/setup_sms.php';
    $setupSMS = new setupSMS;

    $array_order_types = array("interpreter" => 1, "telephone" => 2, "translation" => 3);
    //Non-BSL jobs 1 day before check
    $get_jobs=$obj->read_all("interpreter.id,interpreter.intrpName,interpreter.assignDate,interpreter.assignTime,interpreter.postCode,interpreter.source,'' as comunic,interpreter_reg.name as interpreter_name,interpreter_reg.contactNo as interpreter_phone,interpreter_reg.country as interpreter_country,'interpreter' as table_type", "interpreter,interpreter_reg", "interpreter.intrpName=interpreter_reg.id and interpreter.intrpName!='' and interpreter.aloct_by!='' and interpreter.hoursWorkd=0 and interpreter.deleted_flag=0 and interpreter.orderCancelatoin=0 and interpreter.order_cancel_flag=0 and interpreter.source !='Sign Language (BSL)' and DATE(interpreter.assignDate) = DATE_ADD(CURDATE(), INTERVAL 1 DAY) UNION 
    SELECT telephone.id,telephone.intrpName,telephone.assignDate,telephone.assignTime,'' as postCode,telephone.source,telephone.comunic,interpreter_reg.name as interpreter_name,interpreter_reg.contactNo as interpreter_phone,interpreter_reg.country as interpreter_country,'telephone' as table_type FROM telephone,interpreter_reg WHERE telephone.intrpName=interpreter_reg.id and telephone.intrpName!='' and telephone.aloct_by!='' and telephone.hoursWorkd=0 and telephone.deleted_flag=0 and telephone.orderCancelatoin=0 and telephone.order_cancel_flag=0 and telephone.source !='Sign Language (BSL)' and DATE(telephone.assignDate) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)");

    if ($get_jobs->num_rows > 0) {
        echo " - Rows: " . $get_jobs->num_rows;
        while($row_reminder=$get_jobs->fetch_assoc()){
            if ($row_reminder['table_type'] == 'interpreter') {
                $job_type = "Face to Face";
                $order_type = 1;
                $job_postcode = trim(str_replace(" ", "", $row_reminder['postCode']));
            } else {
                $order_type = 2;
                $job_type = $row_reminder['comunic'] == 7 ? "MS Teams" : "Telephone";
            }
            $message_body = $row_reminder['table_type'] == 'interpreter' ? "Hi\nWe are reminding you for $job_type session at " . substr($row_reminder['assignTime'], 0, 5) . ' on '.$misc->dated($row_reminder['assignDate']) . ' at ' . $job_postcode : "Hi\nWe are reminding you for $job_type session at " . substr($row_reminder['assignTime'], 0, 5) . ' on ' . $misc->dated($row_reminder['assignDate']);
            if ($row_reminder['table_type'] == 'telephone' && $row_reminder['comunic'] == 7) {
                $message_body .= "\nMake sure you have Teams Link";
            }
            $message_body .= "\nMissing or lateness will result in deductions.\nCheck your Portal/App for details";

            if (setupSMS::IS_ALLOWED == 1) {
                $interpreter_phone = $setupSMS->format_phone($row_reminder['interpreter_phone'], $row_reminder['interpreter_country']);
                $obj->insert("job_messages", array("order_id" => $row_reminder['id'], "order_type" => $array_order_types[$row_reminder['table_type']], "message_category" => 5, "interpreter_id" => $row_reminder['intrpName'], "created_by" => 1, "message_body" => $message_body, "sent_to" => $interpreter_phone));
                $inserted_id = $obj->con->insert_id;
                if ($inserted_id) {
                    $sms_response = $setupSMS->send_sms($interpreter_phone, $message_body);
                    if ($sms_response['status'] == 0) {
                        $obj->update("job_messages", array("status" => 0), "id=" . $inserted_id);
                    }
                }
            }
        }
    }
    //BSL jobs 7 days before check
    $get_jobs_bsl=$obj->read_all("interpreter.id,interpreter.intrpName,interpreter.assignDate,interpreter.assignTime,interpreter.postCode,interpreter.source,'' as comunic,interpreter_reg.name as interpreter_name,interpreter_reg.contactNo as interpreter_phone,interpreter_reg.country as interpreter_country,'interpreter' as table_type", "interpreter,interpreter_reg", "interpreter.intrpName=interpreter_reg.id and interpreter.intrpName!='' and interpreter.aloct_by!='' and interpreter.hoursWorkd=0 and interpreter.deleted_flag=0 and interpreter.orderCancelatoin=0 and interpreter.order_cancel_flag=0 and interpreter.source ='Sign Language (BSL)' and DATE(interpreter.assignDate) = DATE_ADD(CURDATE(), INTERVAL 7 DAY) UNION 
    SELECT telephone.id,telephone.intrpName,telephone.assignDate,telephone.assignTime,'' as postCode,telephone.source,telephone.comunic,interpreter_reg.name as interpreter_name,interpreter_reg.contactNo as interpreter_phone,interpreter_reg.country as interpreter_country,'telephone' as table_type FROM telephone,interpreter_reg WHERE telephone.intrpName=interpreter_reg.id and telephone.intrpName!='' and telephone.aloct_by!='' and telephone.hoursWorkd=0 and telephone.deleted_flag=0 and telephone.orderCancelatoin=0 and telephone.order_cancel_flag=0 and telephone.source ='Sign Language (BSL)' and DATE(telephone.assignDate) = DATE_ADD(CURDATE(), INTERVAL 7 DAY)");

    if ($get_jobs_bsl->num_rows > 0) {
        echo " - BSL Rows: " . $get_jobs_bsl->num_rows;
        while($row_reminder_bsl=$get_jobs_bsl->fetch_assoc()){
            if ($row_reminder_bsl['table_type'] == 'interpreter') {
                $job_type = "Face to Face";
                $order_type = 1;
                $job_postcode = trim(str_replace(" ", "", $row_reminder_bsl['postCode']));
            } else {
                $order_type = 2;
                $job_type = $row_reminder_bsl['comunic'] == 7 ? "MS Teams" : "Telephone";
            }
            $message_body = $row_reminder_bsl['table_type'] == 'interpreter' ? "Hi\nWe are reminding you for $job_type session at " . substr($row_reminder_bsl['assignTime'], 0, 5) . ' on '.$misc->dated($row_reminder_bsl['assignDate']) . ' at ' . $job_postcode : "Hi\nWe are reminding you for $job_type session at " . substr($row_reminder_bsl['assignTime'], 0, 5) . ' on ' . $misc->dated($row_reminder_bsl['assignDate']);
            if ($row_reminder_bsl['table_type'] == 'telephone' && $row_reminder_bsl['comunic'] == 7) {
                $message_body .= "\nMake sure you have Teams Link";
            }
            $message_body .= "\nMissing or lateness will result in deductions.\nCheck your Portal/App for details";

            if (setupSMS::IS_ALLOWED == 1) {
                $interpreter_phone = $setupSMS->format_phone($row_reminder_bsl['interpreter_phone'], $row_reminder_bsl['interpreter_country']);
                $obj->insert("job_messages", array("order_id" => $row_reminder_bsl['id'], "order_type" => $array_order_types[$row_reminder_bsl['table_type']], "message_category" => 5, "interpreter_id" => $row_reminder_bsl['intrpName'], "created_by" => 1, "message_body" => $message_body, "sent_to" => $interpreter_phone));
                $inserted_id = $obj->con->insert_id;
                if ($inserted_id) {
                    $sms_response = $setupSMS->send_sms($interpreter_phone, $message_body);
                    if ($sms_response['status'] == 0) {
                        $obj->update("job_messages", array("status" => 0), "id=" . $inserted_id);
                    }
                }
            }
        }
    }
} else {
    echo " - Not allowed Server time: " . date("Y-m-d H:i:s");
}