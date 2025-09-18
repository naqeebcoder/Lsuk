<?php
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../../phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

include '../../db.php';
include_once '../../class.php';

// Include the main TCPDF library (search for installation path).
require_once 'tcpdf_include.php';

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetSubject('TCPDF Tutorial');

// set default header data
include 'rip_header.php';
include 'rip_footer.php';

// set header and footer fonts

$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once dirname(__FILE__) . '/lang/eng.php';
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'B', 12);

// add a page
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);
$invoice_id = $_GET['invoice_id'];
$to_add = $_GET['company_email'];
$table = $_GET['table'];
include "../../".$_GET['loaddb'];
$array_types=array("interpreter"=>"f2f","telephone"=>"tp","translation"=>"tr");
if(!empty($credit_note)){
    $append_credit_invoiceNo=$invoiceNo."-0".$acttObj->read_specific("count(*)-1 as counter","credit_notes","order_id=".$invoice_id." and order_type='".$array_types[$table]."'")['counter'];
    $g_row['append_credit_invoiceNo']=$append_credit_invoiceNo;
}
$htmlfile = $_GET['htm'];
$doc = new DOMDocument();
//$datahere=$doc->loadHTMLFile("../../invoicereport.htm");
//$datahere=$doc->load("../../invoicereport.php");
$datahere = $doc->loadHTMLFile("../../" . $htmlfile);

//$elemshr = $doc->getElementsByTagName('hr');
//$elemsPhp = $doc->getElementsByTagName('?');
$elemsVar = $doc->getElementsByTagName('var');
//$elemsCom = $doc->getElementsByTagName('!--');

TestCode::ModifyVarTags($elemsVar, $g_row);

$elemsVar = $doc->getElementsByTagName('var');

//!--
$datahere2 = $doc->saveHTML();

//file_put_contents("G:\\Phil\\CodedPHP\\notes\\from.htm",$datahere);
//file_put_contents("G:\\Phil\\CodedPHP\\notes\\to.htm",$datahere2);

//$tblHtml = "<div>phils html</div>";

// output the HTML content
$pdf->writeHTML($datahere2, true, false, true, false, '');
//$pdf->writeHTML($tbl, true, false, false, false, '');

// -----------------------------------------------------------------------------

//Close and output PDF document
$pdfhere = $pdf->Output('', 'S');
$from_add = "info@lsuk.org";
$strSubject = "LSUK Credit Note # ".$credit_note;
$query_format="SELECT em_format FROM email_format where id='38'";			
$result_format = mysqli_query($con,$query_format);
$row_format = mysqli_fetch_assoc($result_format);
//Get format from database
$data   = ["[CREDIT_NOTE]", "[INVOICE_NO]"];
$to_replace  = ["$credit_note", "$append_credit_invoiceNo"];
$strMsg = str_replace($data, $to_replace,$row_format['em_format']);
if(isset($to_add) && !empty($to_add)){
    try {
        $mail->SMTPDebug = 0;
        //$mail->isSMTP(); 
        //$mailer->Host = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@lsuk.org';
        $mail->Password   = 'LangServ786';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->setFrom($from_add, 'LSUK');
        //$mail->addAddress('waqarecp1992@gmail.com');
        $mail->addAddress($to_add);
        $mail->addReplyTo($from_add, 'LSUK');
        $mail->addStringAttachment($pdfhere, $credit_note.".pdf");
        $mail->isHTML(true);
        $mail->Subject = $strSubject;
        $mail->Body    = $strMsg;
        $mail->send();
        $mail->ClearAllRecipients();
    } catch (Exception $e) { 
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
list($a, $b) = explode('.', basename(__FILE__));
$pdf->Output('', 'I');
?>

<script>
alert("Successful");
</script>