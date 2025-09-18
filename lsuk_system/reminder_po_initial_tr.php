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

$query_reminder = $obj->read_all("*", "(SELECT translation.porder_email,translation.porder,translation.id,translation.orgName,translation.inchEmail,translation.orgContact as inchPerson,translation.source,translation.target,translation.invoic_date,translation.asignDate as assignDate,translation.total_charges_comp,translation.cur_vat, translation.credit_note,translation.C_admnchargs,translation.nameRef,translation.orgRef,translation.invoiceNo FROM translation,comp_reg WHERE translation.orgName=comp_reg.abrv and translation.multInv_flag=0 AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.commit=1 and translation.total_charges_comp>0 and comp_reg.po_req=1 and (translation.porder='' OR translation.porder='Nil') and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) and YEAR(translation.asignDate)>'2018') as grp", "id NOT IN 
(SELECT DISTINCT po_requested.order_id from po_requested WHERE po_requested.order_type='tr') LIMIT 30");
$em_format = $obj->read_specific("em_format", "email_format", "id=39")['em_format'];
while ($row_reminder = $query_reminder->fetch_assoc()) {
    if (!empty($row_reminder['credit_note'])) {
        $row_reminder['invoiceNo'] = $row_reminder['invoiceNo'] . "-0" . $obj->read_specific("count(*) as counter", "credit_notes", "order_id=" . $row_reminder['id'] . " and order_type='tr'")['counter'];
    }
    $totalforvat = $row_reminder['total_charges_comp'];
    $vatpay = $row_reminder['total_charges_comp'] * $row_reminder['cur_vat'];
    $net_total = number_format($row_reminder['total_charges_comp'], 2);
    $invoice_amount = number_format($row_reminder['total_charges_comp'] + $vatpay, 2);
    $obj->insert('po_requested', array("order_id" => $row_reminder['id'], "order_type" => "tr", "to_email" => $row_reminder['porder_email'], "dated" => $datetime));
    $to_address = trim($row_reminder['porder_email']);
    if ($to_address) {
        $subject = "PO request for Invoice # " . $row_reminder['invoiceNo'];
        $append_table = "
        <table>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Assignment Type</td>
        <td style='border: 1px solid black;padding:5px;'>Translation Project</td>
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
        <td style='border: 1px solid black;padding:5px;'>Invoice Amount (includes VAT)</td>
        <td style='border: 1px solid black;padding:5px;'>" . $invoice_amount . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Client Reference</td>
        <td style='border: 1px solid black;padding:5px;'>" . $row_reminder['orgRef'] . "</td>
        </tr>
        </table>";
        $data   = ["[ASSIGNMENT_TYPE]", "[TABLE]"];
        $to_replace  = ["(Translation) Project", "$append_table"];
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
