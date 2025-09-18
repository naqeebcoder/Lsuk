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

$query_reminder = $obj->read_all("*", "(SELECT telephone.porder_email,telephone.porder,telephone.id,telephone.orgName,telephone.inchEmail,telephone.inchPerson,telephone.source,telephone.target,telephone.invoic_date,telephone.assignDate,SUBSTRING(telephone.assignTime,1,5) as assignTime,telephone.total_charges_comp,telephone.cur_vat,telephone.credit_note,telephone.C_admnchargs,telephone.nameRef,telephone.orgRef,telephone.invoiceNo FROM telephone,comp_reg WHERE telephone.orgName=comp_reg.abrv and telephone.multInv_flag=0 AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 AND telephone.total_charges_comp >0 and comp_reg.po_req=1 and (telephone.porder='' OR telephone.porder='Nil') and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) and YEAR(telephone.assignDate)>'2018') as grp", "id NOT IN 
(SELECT DISTINCT po_requested.order_id from po_requested WHERE po_requested.order_type='tp') LIMIT 30");
$em_format = $obj->read_specific("em_format", "email_format", "id=39")['em_format'];
while ($row_reminder = $query_reminder->fetch_assoc()) {
    if (!empty($row_reminder['credit_note'])) {
        $row_reminder['invoiceNo'] = $row_reminder['invoiceNo'] . "-0" . $obj->read_specific("count(*) as counter", "credit_notes", "order_id=" . $row_reminder['id'] . " and order_type='tp'")['counter'];
    }
    $totalforvat = $row_reminder['total_charges_comp'];
    $vatpay = $row_reminder['total_charges_comp'] * $row_reminder['cur_vat'];
    $net_total = number_format($row_reminder['total_charges_comp'], 2);
    $invoice_amount = number_format($row_reminder['total_charges_comp'] + $vatpay, 2);
    $obj->insert('po_requested', array("order_id" => $row_reminder['id'], "order_type" => "tp", "to_email" => $row_reminder['porder_email'], "dated" => $datetime));
    $to_address = trim($row_reminder['porder_email']);
    if ($to_address) {
        $subject = "PO request for Invoice # " . $row_reminder['invoiceNo'];
        $append_table = "
        <table>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Assignment Type</td>
        <td style='border: 1px solid black;padding:5px;'>Telephone Interpreting</td>
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
        <td style='border: 1px solid black;padding:5px;'>Invoice Amount (includes VAT)</td>
        <td style='border: 1px solid black;padding:5px;'>" . $invoice_amount . "</td>
        </tr>
        <tr>
        <td style='border: 1px solid black;padding:5px;'>Client Reference</td>
        <td style='border: 1px solid black;padding:5px;'>" . $row_reminder['orgRef'] . "</td>
        </tr>
        </table>";
        $data   = ["[ASSIGNMENT_TYPE]", "[TABLE]"];
        $to_replace  = ["(Telephone) Interpreting session", "$append_table"];
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
