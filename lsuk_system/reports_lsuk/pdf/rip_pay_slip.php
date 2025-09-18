<?php
include '../../../source/setup_email.php';
include '../../db.php';
include_once '../../class.php';
$slip_id = $_GET['submit'];

$array_job_type = array(1 => "F2F", 2 => "Telephone", 3 => "Translation");
$data_slip = $acttObj->read_specific("*", "interp_salary", "invoice='" . $slip_id . "'");
$row1 = $acttObj->read_specific("*,CONCAT('****',SUBSTRING(IF(acNo IS NULL or acNo = '', '00000000', acNo),5,8)) as acNo,CONCAT(LEFT(acntCode,2),'**',RIGHT(acntCode,2)) as acntCode,CONCAT(buildingName,' ',line1,' ',line2,' ',line3) as address", "interpreter_reg", "id=" . $data_slip['interp']);
$int_id = $data_slip['interp'];
$interpreter_email = $row1["email"];
$fdate = $data_slip['frm'];
$tdate = $data_slip['todate'];
$salary_date = $data_slip['salary_date'];
$slip_date = $data_slip['dated'];
$invoice_no = $data_slip['invoice'];
$check_date = "paid_date='" . $data_slip['dated'] . "'";
$check_date_tr = "paid_date='" . $data_slip['dated'] . "'";
$check_salary_id = "salary_id=" . $data_slip['id'];
$i = 1;
$amount1 = 0;
$interp_total = 0;
$interp_ded_total = $interp_ni_dedu = $interp_tax_dedu = 0;
$interp_vat = 0;
$con->query("SET SQL_BIG_SELECTS=1");
$query_interp =
  "SELECT interpreter.id,interpreter.ni_dedu,interpreter.tax_dedu,interpreter.source,interpreter.assignDate, interpreter.orgName, interpreter.hoursWorkd, interpreter.chargInterp, interpreter.chargeTravel, interpreter.chargeTravelTime, interpreter.travelCost, interpreter.otherCost, interpreter.admnchargs, interpreter.deduction, interpreter.total_charges_interp, (interpreter.total_charges_interp*interpreter.int_vat) as vat_f2f FROM interpreter,invoice where interpreter.invoiceNo=invoice.invoiceNo AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.intrp_salary_comit=1 and interpreter.intrpName='" . $int_id . "' and $check_salary_id order by interpreter.assignDate";
$result_interp = mysqli_query($con, $query_interp);
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
<span class="name">' . $row1['name'] . '</span><br />
    <span class="address">' . $row1['address'] . ',<br />' . $row1['postCode'] . ', ' . $row1['city'] . '</span><br />
  <span style="text-decoration:underline">' . $row1['email'] . '</span><br /><br />
</div>
<table width="50%" border="1" cellpadding="2">
  <tr>
    <td align="left" bgcolor="#F4F4F4" scope="col">Slip # </td>
    <td align="left" bgcolor="#F4F4F4" scope="col">' . $invoice_no . ' </td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4"><span class="date">Slip Date</span></td>
    <td><span class="date">' . $misc->dated($slip_date) . '</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4"><span class="date">Salary Date</span></td>
    <td><span class="date">' . $misc->dated($salary_date) . '</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">From</td>
    <td><span class="date">' . $misc->dated($fdate) . '</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">To</td>
    <td><span class="date">' . $misc->dated($tdate) . '</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">NI / UTR # </td>
    <td><span class="date">' . $row1['ni'] . '</span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">Bank Details </td>
    <td><span class="date">Sort Code # ' . $row1['acntCode'] . '<br>Account No # ' . $row1['acNo'] . '</span></td>
  </tr>
</table>
<br/><br/>
<table width="100%" border="1" style="color:#FFF">
      <tr>
        <th align="center" bgcolor="#F4F4F4" scope="col" colspan="15"><span style="color:black;">DETAILS</span></th>
        </tr><tr>
        <td align="center" bgcolor="#006666" scope="col" colspan="15">Interpreter Services</td>
        </tr>
      <tr>
        <th align="left" bgcolor="#006666" scope="col">#</th>
        <th align="left" bgcolor="#006666" scope="col">Job Reference</th>
        <th align="left" bgcolor="#006666" scope="col">Assignment Date</th>
        <th align="left" bgcolor="#006666" scope="col">Company</th>
        <th align="left" bgcolor="#006666" scope="col">Language</th>
        <th align="left" bgcolor="#006666" scope="col">Hours Worked</th>
        <th align="left" bgcolor="#006666" scope="col">Interpreting Charge</th>
        <th align="left" bgcolor="#006666" scope="col">Charge for Travel Cost</th>
        <th align="left" bgcolor="#006666" scope="col">Travel Time Charge</th>
        <th align="left" bgcolor="#006666" scope="col">Travel Cost</th>
        <th align="left" bgcolor="#006666" scope="col">Other Costs (Parking , Bridge Toll)</th>
        <th align="left" bgcolor="#006666" scope="col">Additional Pay</th>
        <th align="left" bgcolor="#006666" scope="col">VAT</th>
        <th align="left" bgcolor="#006666" scope="col">Deduction</th>
        <th align="left" bgcolor="#006666" scope="col">Total Charges</th>
        </tr>';
while ($row_interp = mysqli_fetch_assoc($result_interp)) {
  $interp_ded_total += $row_interp['deduction'];
  $interp_ni_dedu += $row_interp['ni_dedu'];
  $interp_tax_dedu += $row_interp['tax_dedu'];
  $interp_vat = $row_interp['vat_f2f'] + $interp_vat;
  $interp_total = $row_interp['total_charges_interp'] + $row_interp['vat_f2f'] + $interp_total;
  if ($row_interp['assignDate'] < $fdate) {
    $style = 'style="color: #ffffff;font-weight: bolder;border: 3px solid #ff0707;"';
  } else {
    $style = ' ';
  }
  $tbl .= '<tr ' . $style . '>
        <td align="left" bgcolor="#006666">' . $i++ . '&nbsp;</td>
        <td align="left" bgcolor="#006666"><span class="desc">' . $row_interp['id'] . '</span></td>
        <td align="left" bgcolor="#006666">' . $misc->dated($row_interp['assignDate']) . '</td>
        <td align="left" bgcolor="#006666"><span class="desc">' . $row_interp['orgName'] . '</span></td>
        <td align="left" bgcolor="#006666"><span class="desc">' . $row_interp['source'] . '</span></td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($row_interp['hoursWorkd']) . '</td>
        <td height="21" align="left" bgcolor="#006666">' . $misc->numberFormat_fun($row_interp['chargInterp']) . '</td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($row_interp['chargeTravel']) . '</td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($row_interp['chargeTravelTime']) . '</td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($row_interp['travelCost']) . '</td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($row_interp['otherCost']) . '</td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($row_interp['admnchargs']) . '</td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($row_interp['vat_f2f']) . '</td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($row_interp['deduction']) . '</td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($row_interp['total_charges_interp']) . '</td>
        </tr>';
}
$tbl .= '<tr>
        <td colspan="12" align="right" bgcolor="#006666">Total</td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($interp_vat) . '</td>
        <td align="left" bgcolor="#006666">' . $misc->numberFormat_fun($interp_ded_total) . '</td>
        <td colspan="2" align="left" bgcolor="#006666">' . $misc->numberFormat_fun($interp_total) . '</td>
      </tr>
  <tr>
    <td colspan="15" bgcolor="#F4F4F4">&nbsp;</td>
  </tr>
    </table>
    <table width="100%" border="1" style="color:#FFF">
      <tr>
        <td align="center" bgcolor="#CC9900" scope="col" colspan="14">Telephone Interpreter Services</td>
        </tr>
      <tr>
        <th align="left" bgcolor="#CC9900" scope="col">#</th>
        <th align="left" bgcolor="#CC9900" scope="col">Job Reference</th>
        <th align="left" bgcolor="#CC9900" scope="col">Assignment Date</th>
        <th align="left" bgcolor="#CC9900" scope="col">Company</th>
        <th align="left" bgcolor="#CC9900" scope="col">Language</th>
        <th align="left" bgcolor="#CC9900" scope="col">Minutes Worked</th>
        <th align="left" bgcolor="#CC9900" scope="col">Rate/Hour</th>
        <th align="left" bgcolor="#CC9900" scope="col">Interpreting Charges</th>
        <th align="left" bgcolor="#CC9900" scope="col">Call Charges</th>
        <th align="left" bgcolor="#CC9900" scope="col">Other Charges</th>
        <th align="left" bgcolor="#CC9900" scope="col">Additional Pay</th>
        <th align="left" bgcolor="#CC9900" scope="col">VAT</th>
        <th align="left" bgcolor="#CC9900" scope="col">Deduction</th>
        <th align="left" bgcolor="#CC9900" scope="col">Total Charges</th>
        </tr>';
$i = 1;
$telep_total = 0;
$telep_ded_total = $telep_ni_dedu = $telep_tax_dedu = 0;
$telep_vat = 0;

$query_telep =
  "SELECT telephone.id,telephone.ni_dedu,telephone.tax_dedu,telephone.source,telephone.assignDate,telephone.orgName, telephone.hoursWorkd, telephone.rateHour, telephone.chargInterp, telephone.otherCharges, telephone.admnchargs, telephone.deduction, telephone.total_charges_interp, (telephone.total_charges_interp*telephone.int_vat) as vat_tp FROM telephone ,invoice where telephone.invoiceNo=invoice.invoiceNo AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.intrp_salary_comit=1 and telephone.intrpName='" . $int_id . "' and $check_salary_id order by telephone.assignDate";
$result_telep = mysqli_query($con, $query_telep);
while ($row_telep = mysqli_fetch_assoc($result_telep)) {
  $telep_ded_total += $row_telep['deduction'];
  $telep_ni_dedu += $row_telep['ni_dedu'];
  $telep_tax_dedu += $row_telep['tax_dedu'];
  $telep_vat = $row_telep['vat_tp'] + $telep_vat;
  $telep_total = $row_telep['total_charges_interp'] + $row_telep['vat_tp'] + $telep_total;
  if ($row_telep['assignDate'] < $fdate) {
    $style = 'style="color: #ffffff;font-weight: bolder;border: 3px solid #ff0707;"';
  } else {
    $style = ' ';
  }
  $tbl .= '<tr ' . $style . '>
        <td align="left" bgcolor="#CC9900">' . $i++ . '&nbsp;</td>
        <td align="left" bgcolor="#CC9900"><span class="desc">' . $row_telep['id'] . '</span></td>
        <td align="left" bgcolor="#CC9900">' . $misc->dated($row_telep['assignDate']) . '</td>
        <td align="left" bgcolor="#CC9900"><span class="desc">' . $row_telep['orgName'] . '</span></td>
        <td align="left" bgcolor="#CC9900"><span class="desc">' . $row_telep['source'] . '</span></td>
        <td align="left" bgcolor="#CC9900">' . $misc->numberFormat_fun($row_telep['hoursWorkd']) . '</td>
        <td align="left" bgcolor="#CC9900">' . $misc->numberFormat_fun($row_telep['rateHour']) . '</td>
        <td align="left" bgcolor="#CC9900">' . $misc->numberFormat_fun($row_telep['chargInterp']) . '</td>
        <td align="left" bgcolor="#CC9900"><span class="desc">' . $misc->numberFormat_fun($row_telep['calCharges']) . '</span></td>
        <td align="left" bgcolor="#CC9900"><span class="desc">' . $misc->numberFormat_fun($row_telep['otherCharges']) . '</span></td>
        <td align="left" bgcolor="#CC9900">' . $misc->numberFormat_fun($row_telep['admnchargs']) . '</td>
        <td align="left" bgcolor="#CC9900">' . $misc->numberFormat_fun($row_telep['vat_tp']) . '</td>
        <td align="left" bgcolor="#CC9900">' . $misc->numberFormat_fun($row_telep['deduction']) . '</td>
        <td align="left" bgcolor="#CC9900">' . $misc->numberFormat_fun($row_telep['total_charges_interp']) . '</td>
        </tr>';
}
$tbl .= '<tr>
        <td colspan="11" align="right" bgcolor="#CC9900">Total</td>
        <td align="left" bgcolor="#CC9900">' . $misc->numberFormat_fun($telep_vat) . '</td>
        <td align="left" bgcolor="#CC9900">' . $misc->numberFormat_fun($telep_ded_total) . '</td>
        <td colspan="2" align="left" bgcolor="#CC9900">' . $misc->numberFormat_fun($telep_total) . '</td>
      </tr>
  <tr>
    <td colspan="14" bgcolor="#F4F4F4">&nbsp;</td>
  </tr>
    </table>
    <table width="100%" border="1" style="color:#FFF">
      <tr>
        <td align="center" bgcolor="#3399FF" scope="col" colspan="13">Translation Services</td>
        </tr>
      <tr>
        <th align="left" bgcolor="#3399FF" scope="col">#</th>
        <th align="left" bgcolor="#3399FF" scope="col">Job Reference</th>
        <th align="left" bgcolor="#3399FF" scope="col">Assignment Date</th>
        <th align="left" bgcolor="#3399FF" scope="col">Company</th>
        <th align="left" bgcolor="#3399FF" scope="col">Language</th>
        <th align="left" bgcolor="#3399FF" scope="col">Units</th>
        <th align="left" bgcolor="#3399FF" scope="col">Rate/Unit</th>
        <th align="left" bgcolor="#3399FF" scope="col">Translation Charges</th>
        <th align="left" bgcolor="#3399FF" scope="col">Other Charges</th>
        <th align="left" bgcolor="#3399FF" scope="col">Additional Pay</th>
        <th align="left" bgcolor="#3399FF" scope="col">VAT</th>
        <th align="left" bgcolor="#3399FF" scope="col">Deduction</th>
        <th align="left" bgcolor="#3399FF" scope="col">Total Charges</th>
        </tr>';
$i = 1;
$trans_total = 0;
$trans_ded_total = $trans_ni_dedu = $trans_tax_dedu = 0;
$trans_vat = 0;
$query_trans =
  "SELECT translation.id,translation.ni_dedu,translation.tax_dedu,translation.source,translation.asignDate, translation.orgName, translation.rpU, translation.numberUnit, translation.otherCharg, translation.admnchargs, translation.deduction, translation.total_charges_interp, (translation.total_charges_interp*translation.int_vat) as vat_tr FROM translation,invoice WHERE translation.invoiceNo=invoice.invoiceNo AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.intrp_salary_comit=1 and translation.intrpName='" . $int_id . "' and $check_salary_id order by translation.asignDate";
$result_trans = mysqli_query($con, $query_trans);
while ($row_trans = mysqli_fetch_assoc($result_trans)) {
  $trans_ded_total += $row_trans['deduction'];
  $trans_ni_dedu += $row_trans['ni_dedu'];
  $trans_tax_dedu += $row_trans['tax_dedu'];
  $trans_vat = $row_trans['vat_tr'] + $trans_vat;
  $trans_total = ($row_trans['total_charges_interp'] + $row_trans['admnchargs']) + $row_trans['vat_tr'] + $trans_total;
  if ($row_trans['asignDate'] < $fdate) {
    $style = 'style="color: #ffffff;font-weight: bolder;border: 3px solid #ff0707;"';
  } else {
    $style = ' ';
  }
  $tbl .= '<tr ' . $style . '>
        <td align="left" bgcolor="#3399FF">' . $i++ . '&nbsp;</td>
        <td align="left" bgcolor="#3399FF"><span class="desc">' . $row_trans['id'] . '</span></td>
        <td align="left" bgcolor="#3399FF">' . $misc->dated($row_trans['asignDate']) . '</td>
        <td align="left" bgcolor="#3399FF"><span class="desc">' . $row_trans['orgName'] . '</span></td>
        <td align="left" bgcolor="#3399FF"><span class="desc">' . $row_trans['source'] . '</span></td>
        <td align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($row_trans['numberUnit']) . '</td>
        <td align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($row_trans['rpU']) . '</td>
        <td align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($row_trans['numberUnit'] * $row_trans['rpU']) . '</td>
        <td align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($row_trans['otherCharg']) . '</td>
        <td align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($row_trans['admnchargs']) . '</td>
        <td align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($row_trans['vat_tr']) . '</td>
        <td align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($row_trans['deduction']) . '</td>
        <td align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($row_trans['total_charges_interp'] + $row_trans['admnchargs']) . '</td>
        </tr>';
}
$tbl .= '<tr>
        <td colspan="10" align="right" bgcolor="#3399FF">Total</td>
        <td align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($trans_vat) . '</td>
        <td align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($trans_ded_total) . '</td>
        <td colspan="2" align="left" bgcolor="#3399FF">' . $misc->numberFormat_fun($trans_total) . '</td>
      </tr>
  <tr>
    <td colspan="13" bgcolor="#F4F4F4">&nbsp;</td>
  </tr>
</table>';
$pdf->writeHTML($tbl, true, false, false, false, '');
$pdf->AddPage();
$tbl = "<br><br>";
$net_deductable_amount = $total_given_amount = $total_payable_amount = $salary_payable_deduction = 0;
$net_non_deductable_amount = $total_extra_given_amount = $total_non_payable_amount = $salary_non_payable_deduction = 0;
$paid_requests = $acttObj->read_all("loan_requests.id,loan_requests.job_id,loan_requests.job_type,loan_requests.given_amount,loan_requests.duration,loan_requests.percentage,loan_dropdowns.title,loan_dropdowns.is_payable,request_paybacks.*", "request_paybacks,loan_requests,loan_dropdowns", "request_paybacks.request_id=loan_requests.id AND loan_requests.type_id=loan_dropdowns.id AND request_paybacks.deleted_flag=0 AND request_paybacks.salary_id=" . $data_slip['id']);
$array_request_ids = $array_extra_request_ids = array();
if ($paid_requests->num_rows > 0) {
  while ($row_paid = $paid_requests->fetch_assoc()) {
    $installment_amount = $extra_installment_amount = 0;
    if ($row_paid['is_payable'] == 1) {
      $total_given_amount += $row_paid['all_initial_amount'];
      $total_payable_amount += $row_paid['all_paid_amount'];
      $installment_amount = round($row_paid['paid_amount'], 2);
      array_push($array_request_ids, array("request_id" => $row_paid['id'], "job_id" => $row_paid['job_id'], "job_type" => $row_paid['job_type'], "initial_amount" => $row_paid['all_initial_amount'], "paid_amount" => $row_paid['all_paid_amount'], "installment_amount" => $installment_amount, "remaining_amount" => $row_paid['all_remaining_amount'], "loan_type" => $row_paid['title'], "date_taken" => $row_paid['paid_date']));
      $salary_payable_deduction += $installment_amount;
    } else {
      $total_extra_given_amount += $row_paid['all_initial_amount'];
      $total_non_payable_amount += $row_paid['all_paid_amount'];
      $extra_installment_amount = round($row_paid['paid_amount'], 2);
      array_push($array_extra_request_ids, array("request_id" => $row_paid['id'], "initial_amount" => $row_paid['all_initial_amount'], "paid_amount" => $row_paid['all_paid_amount'], "installment_amount" => $extra_installment_amount, "remaining_amount" => $row_paid['all_remaining_amount'], "loan_type" => $row_paid['title'], "date_taken" => $row_paid['paid_date']));
      $salary_non_payable_deduction += $extra_installment_amount;
    }
  }
}
$net_deductable_amount = $total_given_amount - $total_payable_amount ?: 0;
$net_non_deductable_amount = $total_extra_given_amount - $total_non_payable_amount ?: 0;
if ($salary_payable_deduction > 0) {
  $tbl .= '<table class="table-money-requests" style="margin-top: 3px;" width="100%" border="1">
            <tr><td colspan="6" align="center"><span style="font-weight: bold;">Installment Repayments</span></td></tr>
            <tr><td align="center" bgcolor="#F4F4F4">Request Title</td><td align="center" bgcolor="#F4F4F4">Initial Amount</td><td align="center" bgcolor="#F4F4F4">Paid Amount</td><td align="center" bgcolor="#F4F4F4">Date Taken</td><td align="center" bgcolor="#F4F4F4">Installment Amount</td><td align="center" bgcolor="#F4F4F4">Remaining</td></tr>';
  foreach ($array_request_ids as $key_payable => $val_payable) {
    $tbl .= '<tr>
                <td align="center">
                  <small>' . $val_payable['loan_type'] . ($val_payable['job_id'] ? "<br>" . $array_job_type[$val_payable['job_type']] . " Job ID: " . $val_payable['job_id'] : "") . '</small>
                </td>
                <td align="center">' . number_format($val_payable['initial_amount'], 2) . '</td>
                <td align="center">' . number_format($val_payable['paid_amount'], 2) . '</td>
                <td align="center">' . $misc->dated($val_payable['date_taken']) . '</td>
                <td align="center">' . number_format($val_payable['installment_amount'], 2) . '</td>
                <td align="center">' . number_format($val_payable['remaining_amount'], 2) . '</td>
              </tr>';
  }
  $tbl .= "</table><br><br>";
}
if ($salary_non_payable_deduction > 0) {
  $tbl .= '<table class="table-money-requests" style="margin-top: 3px;" width="100%" border="1">
            <tr><td colspan="6" align="center"><span style="font-weight: bold;">Additional Payments</span></td></tr>
            <tr><td align="center" bgcolor="#F4F4F4">Payment Title</td><td align="center" bgcolor="#F4F4F4">Total Amount</td><td align="center" bgcolor="#F4F4F4">Given Amount</td><td align="center" bgcolor="#F4F4F4">Given Date</td><td align="center" bgcolor="#F4F4F4">Receivable Amount</td><td align="center" bgcolor="#F4F4F4">Remaining Receivables</td></tr>';
  foreach ($array_extra_request_ids as $key_extra_payable => $val_extra_payable) {
    $tbl .= '<tr>
                <td align="center">
                  <small>' . $val_extra_payable['loan_type'] . '</small>
                </td>
                <td align="center">' . number_format($val_extra_payable['initial_amount'], 2) . '</td>
                <td align="center">' . number_format($val_extra_payable['paid_amount'], 2) . '</td>
                <td align="center">' . $misc->dated($val_extra_payable['date_taken']) . '</td>
                <td align="center">' . number_format($val_extra_payable['installment_amount'], 2) . '</td>
                <td align="center">' . number_format($val_extra_payable['remaining_amount'], 2) . '</td>
              </tr>';
  }
  $tbl .= "</table><br><br>";
}
$total_ni_deduction = ($data_slip['ni_dedu'] != 0 ? $data_slip['ni_dedu'] : $interp_ni_dedu + $telep_ni_dedu + $trans_ni_dedu );
$total_tax_deduction = ($data_slip['tax_dedu'] != 0 ? $data_slip['tax_dedu'] : $interp_tax_dedu + $telep_tax_dedu + $trans_tax_dedu );
$tbl .= '<br>
<table width="40%" border="1" cellpadding="2">
    <tr>
      <td align="left" bgcolor="#F4F4F4" scope="col">Grand Total </td>
      <td align="left" bgcolor="#F4F4F4" scope="col">' . $misc->numberFormat_fun($data_slip['salry']) . '</td>
    </tr>
    <tr>
        <td bgcolor="#F4F4F4">Total VAT</td>
        <td>' . $misc->numberFormat_fun($interp_vat + $telep_vat + $trans_vat) . '</td>
    </tr>
    <tr>
      <td bgcolor="#F4F4F4">NI Deduction</td>
      <td><span class="date">' . $misc->numberFormat_fun($total_ni_deduction) . '</span></td>
    </tr>
    <tr>
      <td bgcolor="#F4F4F4">Tax Deduction</td>
      <td><span class="date">' . $misc->numberFormat_fun($total_tax_deduction) . '</span></td>
    </tr>';
if ($data_slip['payback_deduction'] > 0) {
  $tbl .= '<tr>
      <td bgcolor="#F4F4F4" style="color:red">Payback Deduction</td>
      <td>' . number_format($data_slip['payback_deduction'], 2) . '</td>
    </tr>';
}
if ($data_slip['given_amount'] > 0) {
  $tbl .= '<tr>
    <td bgcolor="#F4F4F4" style="color:darkgreen">Additional Payment</td>
    <td>' . number_format($data_slip['given_amount'], 2) . '</td>
  </tr>';
}
$tbl .= '<tr>
    <td bgcolor="#F4F4F4">Net Salary</td>
    <td><span class="date"><b>' . $misc->numberFormat_fun(($data_slip['salry'] - $total_ni_deduction - $total_tax_deduction - $data_slip['payback_deduction']) + $data_slip['given_amount']) . '</b></span></td>
  </tr>
</table>';

$pdf->writeHTML($tbl, true, false, false, false, '');
$pdfhere = $pdf->Output('', 'S');
$row_format = $acttObj->read_specific("em_format", "email_format", "id=7");
//Get format from database
$remittance_body  = $row_format['em_format'];
$from_add = "payroll@lsuk.org";
$fields   = ["[FIRST_DATE]", "[SECOND_DATE]", "[THIRD_DATE]"];
$replacement  = ["$fdate", "$tdate", "$salary_date"];
$strMsg = str_replace($fields, $replacement, $remittance_body);
$name_file = "Remittance $slip_id.pdf";
$subject = 'Remittance Slip From LSUK';
try {
  $mail->SMTPDebug = 0;
  $mail->isSMTP();
  $mail->Host = setupEmail::EMAIL_HOST;
  $mail->SMTPAuth   = true;
  $mail->Username   = setupEmail::PAYROLL_EMAIL;
  $mail->Password   = setupEmail::PAYROLL_PASSWORD;
  $mail->SMTPSecure = setupEmail::SECURE_TYPE;
  $mail->Port       = setupEmail::SENDING_PORT;
  $mail->setFrom(setupEmail::PAYROLL_EMAIL, 'LSUK Remittance Slip');
  $mail->addAddress($interpreter_email);
  $mail->addReplyTo(setupEmail::PAYROLL_EMAIL, 'LSUK Remittance Slip');
  $mail->addStringAttachment($pdfhere, $name_file);
  $mail->isHTML(true);
  $mail->Subject = $subject;
  $mail->Body    = $strMsg;
  if ($mail->send()) {
    $mail->ClearAllRecipients();
    $mail->ClearAttachments();
    /*$sub_title = "Your pay slip has been generated & uploaded at your profile";
      $type_key="ps";
      //Send notification on APP
      $check_id=$acttObj->read_specific('id','notify_new_doc','interpreter_id='.$int_id)['id'];
      if(empty($check_id)){
          $acttObj->insert('notify_new_doc',array("interpreter_id"=>$int_id,"status"=>'1'));
      }else{
          $acttObj->update('notify_new_doc',array("new_notification"=>'0'),array("interpreter_id"=>$int_id));
      }
      $array_tokens=explode(',',$acttObj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens","int_tokens","int_id=".$int_id)['tokens']);
      if(!empty($array_tokens)){
          foreach($array_tokens as $token){
              if(!empty($token)){
                  $acttObj->insert('app_notifications',array("title"=>$subject,"sub_title"=>$sub_title,"dated"=>date('Y-m-d'),"int_ids"=>$int_id,"read_ids"=>$int_id,"type_key"=>$type_key));
                  $acttObj->notify($token,"📎 ".$subject,$sub_title,array("type_key"=>$type_key));
              }
          }
      }*/
    list($a, $b) = explode('.', basename(__FILE__));
    $pdf->Output($a . '.pdf', 'I');
  } else {
    echo "Message could not be sent to email: " . $interpreter_email;
  }
} catch (Exception $e) {
  echo "Message could not be sent. Mailer Error:";
}