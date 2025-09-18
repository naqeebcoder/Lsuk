<?php
//php mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../../phpmailer/vendor/autoload.php';
$mail = new PHPMailer(true);

include '../../db.php';
include_once '../../class.php';

$slip_id= $_GET['submit'];
$query1="SELECT * FROM interpreter_reg where id=$slip_id";			
$result1 = mysqli_query($con,$query1);
$row1 = mysqli_fetch_array($result1);
$int_email=$row1["email"];
$fdate= @$_GET['fdate']; 
$tdate= @$_GET['tdate'];
$i=1;$amount1=0;$interp_total=0;$interp_ded_total=0;$con->query("SET SQL_BIG_SELECTS=1");
$query_interp=
"SELECT interpreter.id,interpreter.assignDate, interpreter.orgName, interpreter.hoursWorkd, interpreter.chargInterp, interpreter.chargeTravel, interpreter.chargeTravelTime, interpreter.travelCost, interpreter.otherCost, interpreter.admnchargs, interpreter.deduction, interpreter.total_charges_interp FROM interpreter,invoice where interpreter.invoiceNo=invoice.invoiceNo AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and 
  interpreter.intrpName=$slip_id and interpreter.intrp_salary_comit = 0 and 
  (interpreter.assignDate BETWEEN('$fdate')AND('$tdate') OR interpreter.assignDate < '$fdate') order by interpreter.assignDate";
$result_interp = mysqli_query($con,$query_interp);?>
<?php 
// Include the main TCPDF library (search for installation path).
require_once 'tcpdf_include.php';
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetSubject('TCPDF Tutorial');

// set default header data
include 'rip_header.php';
include 'rip_footer.php'; // set header and footer fonts
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

// set font
$pdf->SetFont('helvetica', 'B', 12);

// add a page
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);

$tbl = '<div style="position:absolute; left: 5;">
<span class="name">'.$row1['name'].'</span><br />
    <span class="address">'.$row1['address'].'<br/>'.$row1['city'].'</span><br />
  <span style="text-decoration:underline">'.$row1['email'].'</span><br /><br />
</div>
<table width="50%" border="1" cellpadding="2">
      <tr>
        <td align="left" bgcolor="#F4F4F4" scope="col">Slip # </td>
        <td align="left" bgcolor="#F4F4F4" scope="col"> - - - </td>
        </tr>
  <tr>
    <td bgcolor="#F4F4F4"><span class="date">Salary Date # </span></td>
    <td><span class="date">';
    $date = new DateTime('now');
$date->modify('last day of this month');
$tbl .=''. $misc->dated($date->format('Y-m-d')).'</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">From # </td>
    <td><span class="date">'.$misc->dated($fdate).'</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">To # </td>
    <td><span class="date">'.$misc->dated($tdate).'</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">National Insurance # </td>
    <td><span class="date">'.@$row1['ni'].'</span></td>
  </tr>
</table>
<br/><br/>
<table width="100%" border="1" style="color:#FFF">
      <tr>
        <th align="center" bgcolor="#F4F4F4" scope="col" colspan="12"><span style="color:black;">DESCRIPTION</span></th>
        </tr><tr>
        <th align="center" bgcolor="#006666" scope="col" colspan="12">Interpreter Services</th>
        </tr>
      <tr>
        <th align="left" bgcolor="#006666" scope="col">#</th>
        <th align="left" bgcolor="#006666" scope="col">Assignment Date</th>
        <th align="left" bgcolor="#006666" scope="col">Org Name</th>
        <th align="left" bgcolor="#006666" scope="col">Hours Worked</th>
        <th align="left" bgcolor="#006666" scope="col">Charge for Interpreting Time</th>
        <th align="left" bgcolor="#006666" scope="col">Charge for Travel Cost</th>
        <th align="left" bgcolor="#006666" scope="col">Charge for Travel Time</th>
        <th align="left" bgcolor="#006666" scope="col">Travel Cost</th>
        <th align="left" bgcolor="#006666" scope="col">Other Costs (Parking , Bridge Toll)</th>
        <th align="left" bgcolor="#006666" scope="col">Additional Payment</th>
        <th align="left" bgcolor="#006666" scope="col">Deduction</th>
        <th align="left" bgcolor="#006666" scope="col">Total Charges £</th>
        </tr>';
        while($row_interp = mysqli_fetch_array($result_interp)){
        if($row_interp['assignDate']<$fdate){
            $style= 'style="color: #ffffff;font-weight: bolder;border: 3px solid #ff0707;"';
        }else{
            $style= ' ';
        }
$tbl .= '<tr '.$style.'>
        <td align="left" bgcolor="#006666">'.$i++.'&nbsp;</td>
        <td align="left" bgcolor="#006666">'.$misc->dated($row_interp['assignDate']).'</td>
        <td align="left" bgcolor="#006666"><span class="desc">'.$row_interp['orgName'].'</span></td>
        <td align="left" bgcolor="#006666">'.$misc->numberFormat_fun($row_interp['hoursWorkd']).'</td>
        <td height="21" align="left" bgcolor="#006666">'.$misc->numberFormat_fun($row_interp['chargInterp']).'</td>
        <td align="left" bgcolor="#006666">'.$misc->numberFormat_fun($row_interp['chargeTravel']).'</td>
        <td align="left" bgcolor="#006666">'.$misc->numberFormat_fun($row_interp['chargeTravelTime']).'</td>
        <td align="left" bgcolor="#006666">'.$misc->numberFormat_fun($row_interp['travelCost']).'</td>
        <td align="left" bgcolor="#006666">'.$misc->numberFormat_fun($row_interp['otherCost']).'</td>
        <td align="left" bgcolor="#006666">'.$misc->numberFormat_fun($row_interp['admnchargs']).'</td>
        <td align="left" bgcolor="#006666">'.$misc->numberFormat_fun($row_interp['deduction']).'</td>
        <td align="left" bgcolor="#006666">'.$misc->numberFormat_fun($row_interp['total_charges_interp']).'</td>
        </tr>';
        $interp_ded_total=$row_interp['deduction'] + $interp_ded_total;
        $interp_total=$row_interp['total_charges_interp'] + $interp_total; 
        }
         $tbl .= '<tr>
        <td colspan="9" align="right" bgcolor="#006666">Total</td>
        <td align="left" bgcolor="#006666">&nbsp;</td>
        <td align="left" bgcolor="#006666">'.$misc->numberFormat_fun($interp_ded_total).'</td>
        <td align="left" bgcolor="#006666">'.$misc->numberFormat_fun($interp_total).'</td>
      </tr>
  <tr>
    <td colspan="9" bgcolor="#F4F4F4">&nbsp;</td>
  </tr>
    </table>
    <table width="100%" border="1" style="color:#FFF">
      <tr>
        <th align="center" bgcolor="#CC9900" scope="col" colspan="9">Telephone Interpreter Services</th>
        </tr>
      <tr>
        <th align="left" bgcolor="#CC9900" scope="col">#</th>
        <th align="left" bgcolor="#CC9900" scope="col">Assignment Date</th>
        <th align="left" bgcolor="#CC9900" scope="col">Org Name</th>
        <th align="left" bgcolor="#CC9900" scope="col">Hours Worked</th>
        <th align="left" bgcolor="#CC9900" scope="col">Call Charges</th>
        <th align="left" bgcolor="#CC9900" scope="col">Other Charges</th>
        <th align="left" bgcolor="#CC9900" scope="col">Additional Payment</th>
        <th align="left" bgcolor="#CC9900" scope="col">Deduction</th>
        <th align="left" bgcolor="#CC9900" scope="col">Total Charges</th>
        </tr>';
        $i=1;$telep_total=0;$telep_ded_total=0;

$query_telep=
"SELECT telephone.id,telephone.assignDate,telephone.orgName, telephone.hoursWorkd, telephone.chargInterp, telephone.otherCharges, telephone.admnchargs, telephone.deduction, telephone.total_charges_interp FROM telephone ,invoice where telephone.invoiceNo=invoice.invoiceNo AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and 
 telephone.intrpName=$slip_id and telephone.intrp_salary_comit = 0 and 
 (telephone.assignDate BETWEEN('$fdate')AND('$tdate') OR telephone.assignDate < '$fdate') order by telephone.assignDate";

$result_telep = mysqli_query($con,$query_telep);
while($row_telep = mysqli_fetch_array($result_telep)){
        if($row_telep['assignDate']<$fdate){
            $style= 'style="color: #ffffff;font-weight: bolder;border: 3px solid #ff0707;"';
        }else{
            $style= ' ';
        }
         $tbl .= '<tr '.$style.'>
        <td align="left" bgcolor="#CC9900">'.$i++.'&nbsp;</td>
        <td align="left" bgcolor="#CC9900">'.$misc->dated($row_telep['assignDate']).'</td>
        <td align="left" bgcolor="#CC9900"><span class="desc">'.$row_telep['orgName'].'</span></td>
        <td align="left" bgcolor="#CC9900">'.$misc->numberFormat_fun($row_telep['hoursWorkd']).'</td>
        <td align="left" bgcolor="#CC9900"><span class="desc">'.$misc->numberFormat_fun($row_telep['calCharges']).'</span></td>
        <td align="left" bgcolor="#CC9900"><span class="desc">'.$misc->numberFormat_fun($row_telep['otherCharges']).'</span></td>
        <td align="left" bgcolor="#CC9900">'.$misc->numberFormat_fun($row_telep['admnchargs']).'</td>
        <td align="left" bgcolor="#CC9900">'.$misc->numberFormat_fun($row_telep['deduction']) .'</td>
        <td align="left" bgcolor="#CC9900">'.$misc->numberFormat_fun($row_telep['total_charges_interp']) .'</td>
        </tr>';
        $telep_ded_total=$misc->numberFormat_fun($row_telep['deduction'] + $telep_ded_total);
        $telep_total=$row_telep['total_charges_interp'] + $telep_total;
}
        $tbl .= '<tr>
        <td colspan="7" align="right" bgcolor="#CC9900">Total</td>
        <td align="left" bgcolor="#CC9900">'.$misc->numberFormat_fun($telep_ded_total).'</td>
        <td align="left" bgcolor="#CC9900">'.$misc->numberFormat_fun($telep_total).'</td>
      </tr>
  <tr>
    <td colspan="9" bgcolor="#F4F4F4">&nbsp;</td>
  </tr>
    </table>
    <table width="100%" border="1"style="color:#FFF">
      <tr>
        <th align="center" bgcolor="#FCFCFC" scope="col" colspan="9">Translation Services</th>
        </tr>
      <tr>
        <th align="left" bgcolor="#3399FF" scope="col">#</th>
        <th align="left" bgcolor="#3399FF" scope="col">Assignment Date</th>
        <th align="left" bgcolor="#3399FF" scope="col">Org Name</th>
        <th align="left" bgcolor="#3399FF" scope="col">Rate per Unit</th>
        <th align="left" bgcolor="#3399FF" scope="col">Unit</th>
        <th align="left" bgcolor="#3399FF" scope="col">Other Charges</th>
        <th align="left" bgcolor="#3399FF" scope="col">Additional Payment</th>
        <th align="left" bgcolor="#3399FF" scope="col">Deduction</th>
        <th align="left" bgcolor="#3399FF" scope="col">Total Charges</th>
        </tr>';
 $i=1;$trans_total=0;$trans_ded_total=0;
$query_trans=
"SELECT translation.id,translation.asignDate, translation.orgName, translation.rpU, translation.numberUnit, translation.otherCharg, translation.admnchargs, translation.deduction, translation.total_charges_interp FROM translation,invoice WHERE translation.invoiceNo=invoice.invoiceNo AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.intrpName=$slip_id and translation.intrp_salary_comit = 0 and (translation.asignDate BETWEEN('$fdate')AND('$tdate') OR translation.asignDate < '$fdate') order by translation.asignDate";				
$result_trans = mysqli_query($con,$query_trans);
while($row_trans = mysqli_fetch_array($result_trans)){
        if($row_trans['asignDate']<$fdate){
            $style= 'style="color: #ffffff;font-weight: bolder;border: 3px solid #ff0707;"';
        }else{
            $style= ' ';
        }
      $tbl .= '<tr '.$style.'>
        <td align="left" bgcolor="#3399FF">'.$i++.'&nbsp;</td>
        <td align="left" bgcolor="#3399FF">'. $misc->dated($row_trans['asignDate']).'</td>
        <td align="left" bgcolor="#3399FF"><span class="desc">'.$row_trans['orgName'].'</span></td>
        <td align="left" bgcolor="#3399FF">'.$misc->numberFormat_fun($row_trans['rpU']).'</td>
        <td align="left" bgcolor="#3399FF">'.$misc->numberFormat_fun($row_trans['numberUnit']).'</td>
        <td align="left" bgcolor="#3399FF">'. $misc->numberFormat_fun($row_trans['otherCharg']).'</td>
        <td align="left" bgcolor="#3399FF">'. $misc->numberFormat_fun($row_trans['admnchargs']).'</td>
        <td align="left" bgcolor="#3399FF">'.$misc->numberFormat_fun($row_trans['deduction']).'</td>
        <td align="left" bgcolor="#3399FF">'. $misc->numberFormat_fun($row_trans['total_charges_interp']).'</td>
        </tr>';
         $trans_ded_total=$row_trans['deduction'] + $trans_ded_total;
        $trans_total=$row_trans['total_charges_interp'] + $trans_total;
}
        $tbl .= '<tr>
        <td colspan="7" align="right" bgcolor="#3399FF">Total</td>
        <td align="left" bgcolor="#3399FF">'. $misc->numberFormat_fun($trans_ded_total).'</td>
        <td align="left" bgcolor="#3399FF">'.$misc->numberFormat_fun($trans_total).'</td>
      </tr>
  <tr>
    <td colspan="9" bgcolor="#F4F4F4">&nbsp;</td>
  </tr>
</table>
<br><br>
<table width="50%" border="1" cellpadding="2">
      <tr>
        <td align="left" bgcolor="#F4F4F4" scope="col">Grand Total </td>
        <td align="left" bgcolor="#F4F4F4" scope="col">'.$misc->numberFormat_fun($interp_total + $telep_total + $trans_total) .'</td>
        </tr>
  <tr>
    <td bgcolor="#F4F4F4"><span class="date">NI Deduction</span></td>
    <td><span class="date">0.00</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4"><span class="date">Tax Deduction</span></td>
    <td><span class="date">0.00</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4"><span class="date">Other Deduction</span></td>
    <td><span class="date">'.$misc->numberFormat_fun($interp_ded_total + $telep_ded_total + $trans_ded_total).'</span></td>
  </tr>';
  $grand_total=$misc->numberFormat_fun($interp_total + $telep_total + $trans_total);
    $grand_deduction=$misc->numberFormat_fun($interp_ded_total + $telep_ded_total + $trans_ded_total);
$query_tax="
SELECT sum(interpreter.ni_dedu) as ni_dedu,sum(interpreter.tax_dedu) as tax_dedu FROM interpreter 
JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.intrpName=$slip_id and interpreter.intrp_salary_comit = 0 and (interpreter.assignDate BETWEEN('$fdate')AND('$tdate') OR interpreter.assignDate < '$fdate' )
union
SELECT sum(telephone.ni_dedu) as ni_dedu,sum(telephone.tax_dedu) as tax_dedu FROM telephone 
JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.intrpName=$slip_id and telephone.intrp_salary_comit = 0 and (telephone.assignDate BETWEEN('$fdate')AND('$tdate') OR telephone.assignDate < '$fdate' )
union
SELECT sum(translation.ni_dedu) as ni_dedu,sum(translation.tax_dedu) as tax_dedu FROM translation 
JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.intrpName=$slip_id and translation.intrp_salary_comit = 0 and (translation.asignDate BETWEEN('$fdate')AND('$tdate') OR translation.asignDate < '$fdate' )";			

$result_tax = mysqli_query($con,$query_tax);
while($row_tax = mysqli_fetch_array($result_tax))
{
  $ni_dedu=$misc->numberFormat_fun($row_tax['ni_dedu']); 
  $tax_dedu=$row_tax['tax_dedu'];
}
 $tbl.='<tr>
    <td bgcolor="#F4F4F4">National Insurance  Deduction</td>
    <td><span class="date">'.$misc->numberFormat_fun($ni_dedu).'</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">Tax Deduction</td>
    <td><span class="date">'. $misc->numberFormat_fun($tax_dedu).'</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">Net Salary</td>
    <td><span class="date"><b>'.$misc->numberFormat_fun($grand_total - $tax_dedu - $ni_dedu).'</b></span></td>
  </tr>
</table>';

$pdf->writeHTML($tbl, true, false, false, false, '');
$pdfhere = $pdf->Output('', 'S');

$query_format="SELECT em_format FROM email_format where id='7'";			
$result_format = mysqli_query($con,$query_format);
$row_format = mysqli_fetch_array($result_format);
//Get format from database
    $remittance_body  = $row_format['em_format'];
    $to_add = $int_email;
    $from_add = "info@lsuk.org";
    $strMsg = $remittance_body;
    $dated=date('Y-m-d');
    $name_file="Remittance $dated.pdf";

try {
    $mail->SMTPDebug = 0;
    //$mail->isSMTP(); 
    //$mailer->Host = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@lsuk.org';
    $mail->Password   = 'LangServ786';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom($from_add, 'LSUK Remittance Slip');
    $mail->addAddress($to_add);
    $mail->addReplyTo('info@lsuk.org', 'LSUK Remittance Slip');
    $mail->addStringAttachment($pdfhere, $name_file);
    $mail->isHTML(true);
    $mail->Subject = 'Remittance Slip From LSUK';
    $mail->Body    = $strMsg;
    if($mail->send()){
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();
    }
    list($a, $b) = explode('.', basename(__FILE__));
    $pdf->Output($a . '.pdf', 'I');
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error:";
} ?>