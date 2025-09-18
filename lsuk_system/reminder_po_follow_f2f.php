<?php set_include_path('/home/customer/www/lsuk.org/public_html/');
include 'source/setup_email.php';
include 'lsuk_system/actions.php';
$datetime = date('Y-m-d H:i:s');
$mail->SMTPDebug = 0;
$mail->isSMTP();
$mail->Host = setupEmail::EMAIL_HOST;
$mail->SMTPAuth   = true;
$mail->Username   = setupEmail::ACCOUNTS_EMAIL;
$mail->Password   = setupEmail::ACCOUNTS_PASSWORD;
$mail->SMTPSecure = setupEmail::SECURE_TYPE;
$mail->Port       = setupEmail::SENDING_PORT;

$query_reminder = $obj->read_all("interpreter.porder_email,interpreter.porder,interpreter.id,interpreter.orgName,interpreter.inchEmail,interpreter.inchPerson,interpreter.source,interpreter.target,interpreter.invoic_date,interpreter.assignDate,SUBSTRING(interpreter.assignTime,1,5) as assignTime,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo", "(SELECT po_requested.order_id,MAX(dated) as latest_date FROM po_requested WHERE order_type='f2f' GROUP BY order_id,order_type) as grp,interpreter,comp_reg", "interpreter.orgName=comp_reg.abrv and interpreter.multInv_flag=0 AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 AND interpreter.total_charges_comp >0 and comp_reg.po_req=1 and (interpreter.porder='' OR interpreter.porder='Nil') and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) and YEAR(interpreter.assignDate)>'2018' AND grp.order_id=interpreter.id AND DATEDIFF(now(),grp.latest_date)>=7 LIMIT 30");
$em_format = $obj->read_specific("em_format", "email_format", "id=40")['em_format'];
while ($row_reminder = $query_reminder->fetch_assoc()) {
    if (!empty($row_reminder['credit_note'])) {
        $row_reminder['invoiceNo'] = $row_reminder['invoiceNo'] . "-0" . $obj->read_specific("count(*) as counter", "credit_notes", "order_id=" . $row_reminder['id'] . " and order_type='f2f'")['counter'];
    }
    $totalforvat = $row_reminder['total_charges_comp'];
    $vatpay = $row_reminder['total_charges_comp'] * $row_reminder['cur_vat'];
    $non_vat = number_format($row_reminder['C_otherexpns'], 2);
    $net_total = number_format($row_reminder['total_charges_comp'], 2);
    $invoice_amount = number_format($row_reminder['total_charges_comp'] + $vatpay + $row_reminder['C_otherexpns'], 2);
    $obj->insert('po_requested', array("order_id" => $row_reminder['id'], "order_type" => "f2f", "to_email" => $row_reminder['porder_email'], "dated" => $datetime));
    $to_address = trim($row_reminder['porder_email']);
    if ($to_address) {
        $subject = "PO follow-up request for Invoice # " . $row_reminder['invoiceNo'];
        $append_table = "
        <table>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Assignment Type</td>
        <td style='border: 1px solid black;padding:5px;'>Face To Face Interpreting</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Project Reference Number</td>
        <td style='border: 1px solid black;padding:5px;'>" . $row_reminder['nameRef'] . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Booking Person</td>
        <td style='border: 1px solid black;padding:5px;'>" . $row_reminder['inchPerson'] . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Assignment Date</td>
        <td style='border: 1px solid black;padding:5px;'>" . $misc->dated($row_reminder['assignDate']) . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Assignment Time</td>
        <td style='border: 1px solid black;padding:5px;'>" . $row_reminder['assignTime'] . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Source Language</td>
        <td style='border: 1px solid black;padding:5px;'>" . $row_reminder['source'] . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Target Language</td>
        <td style='border: 1px solid black;padding:5px;'>" . $row_reminder['target'] . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Invoice Number</td>
        <td style='border: 1px solid black;padding:5px;'>" . $row_reminder['invoiceNo'] . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Net Total</td>
        <td style='border: 1px solid black;padding:5px;'>" . $net_total . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>VAT Amount</td>
        <td style='border: 1px solid black;padding:5px;'>" . $vatpay . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Non-VAT Amount</td>
        <td style='border: 1px solid black;padding:5px;'>" . $non_vat . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Invoice Amount (includes VAT)</td>
        <td style='border: 1px solid black;padding:5px;'>" . $invoice_amount . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Client Reference</td>
        <td style='border: 1px solid black;padding:5px;'>" . $row_reminder['orgRef'] . "</td>
        </tr>
        </table>";
        $data   = ["[ASSIGNMENT_TYPE]", "[TABLE]"];
        $to_replace  = ["(Face To Face) Interpreting session", "$append_table"];
        $message = str_replace($data, $to_replace, $em_format);
        try {
            $mail->setFrom(setupEmail::ACCOUNTS_EMAIL, setupEmail::FROM_NAME);
            $mail->addAddress($to_address);
            $mail->addReplyTo(setupEmail::ACCOUNTS_EMAIL, setupEmail::FROM_NAME);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $mail->msgHtml($message);
            if ($mail->send()) {
                $mail->ClearAllRecipients();
            }
        } catch (Exception $e) {
            echo "Message could not be sent! Mailer library error for: " . $row_reminder['id'];
        }
    }
}
