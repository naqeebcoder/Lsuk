<?php include '../../db.php';
include_once('../../class.php');
$excel = @$_GET['excel'];
session_start();
$UserName = $_SESSION['UserName'];
$prv = $_SESSION['prv'];
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$is_paid = @$_GET['is_paid'];
if (isset($_GET['is_paid'])) {
    $append_is_paid = " and interp_salary.is_paid=1 ";
}
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
$i = 1;
if (isset($search_1) && !empty($search_1)) {
    $query = "SELECT interp_salary.is_paid,interp_salary.paid_date,interp_salary.ni_dedu,interp_salary.tax_dedu,interp_salary.payback_deduction,interp_salary.given_amount,interp_salary.interp,interp_salary.invoice,interp_salary.dated,interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city,interpreter_reg.acntCode,interpreter_reg.acName,interpreter_reg.acNo,interp_salary.salry as net_total,interpreter_reg.work_type from interp_salary,interpreter_reg where interp_salary.interp=interpreter_reg.id and interp_salary.deleted_flag=0 and (DATE(interp_salary.frm) BETWEEN ('$search_2') AND ('$search_3')) AND (DATE(interp_salary.todate) BETWEEN ('$search_2') AND ('$search_3')) and interp_salary.interp='$search_1' and  interp_salary.deleted_flag=0 $append_is_paid ORDER BY interpreter_reg.name ASC";
} else {
    $query = "SELECT interp_salary.is_paid,interp_salary.paid_date,interp_salary.ni_dedu,interp_salary.tax_dedu,interp_salary.payback_deduction,interp_salary.given_amount,interp_salary.interp,interp_salary.invoice,interp_salary.dated,interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city,interpreter_reg.acntCode,interpreter_reg.acName,interpreter_reg.acNo,interp_salary.salry as net_total,interpreter_reg.work_type from interp_salary,interpreter_reg where interp_salary.interp=interpreter_reg.id and interp_salary.deleted_flag=0 and  interp_salary.deleted_flag=0 $append_is_paid and (DATE(interp_salary.frm) BETWEEN ('$search_2') AND ('$search_3')) AND (DATE(interp_salary.todate) BETWEEN ('$search_2') AND ('$search_3')) ORDER BY interpreter_reg.name ASC";
}
$result = mysqli_query($con, $query);

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
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
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'B', 12);

// add a page
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);

// Table with rowspans and THEAD
$tbl = <<<EOD
<style>
table {border-collapse: collapse; width:100%;}
th {border: 1px solid #999; padding: 0.5rem;text-align left; background-color:#039; color:#FFF;font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left; word-wrap: break-word;}
</style>
EOD;

$tbl .= <<<EOD
<div>
<h2 align="center"><u>Interpreters Paid Salary Report</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range: Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
<div>
<h3 align="center">In House Interpreters</h3>
</div>
EOD;

$tbl .= <<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
	<th>Salary Slip No</th>
	<th>Linguistic</th>
	<th>Gender</th>
	<th>City</th>
    <th>Account Title</th>
    <th>Sort Code</th>
    <th>Account Number</th>
	<th>Salary Date</th>
    <th>Payment Status</th>
    <th>Paid Date</th>
	<th>Paid Salary</th>
 </tr>

</thead>
EOD;

while ($row = mysqli_fetch_assoc($result)) {
    if($row['work_type']=="in-house"){
        $total_salary = ($row['net_total'] - $row['ni_dedu'] - $row['tax_dedu'] - $row['payback_deduction']) + $row['given_amount'];
        $net_total += $total_salary;
        $tbl .= '
        <tr>
              <td style="width:35px;">' . $i . '</td>
            <td>' . $row["invoice"] . '</td>
            <td>' . $row["name"] . '</td>
            <td>' . $row["gender"] . '</td>
            <td>' . $row["city"] . '</td>
            <td>' . $row["acName"] . '</td>
            <td>' . htmlentities($row["acntCode"]) . '</td>
            <td>' . $row["acNo"] . '</td>
            <td>' . $misc->dated($row["dated"]) . '</td>
            <td>' . ($row['is_paid'] == 1 ? "Paid" : "Unpaid") . '</td>
            <td>' . $misc->dated($row['paid_date']) . '</td>
            <td>' . $total_salary . '</td>
        </tr>';
        $i++;    
    }
}

$tbl .= "<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>Total</td>
    <td>" . round($net_total, 2) . "</td>
    </tr>  
</table>";

$tbl .= <<<EOD
<div>
<h3 align="center">Freelance Interpreters</h3>
</div>
EOD;

$tbl .= <<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
	<th>Salary Slip No</th>
	<th>Linguistic</th>
	<th>Gender</th>
	<th>City</th>
    <th>Account Title</th>
    <th>Sort Code</th>
    <th>Account Number</th>
	<th>Salary Date</th>
    <th>Payment Status</th>
    <th>Paid Date</th>
	<th>Paid Salary</th>
 </tr>

</thead>
EOD;

$result2 = mysqli_query($con, $query);
$net_total=0;
while ($row2 = mysqli_fetch_assoc($result2)) {
    if($row2['work_type']!="in-house"){
    $total_salary = ($row2['net_total'] - $row2['ni_dedu'] - $row2['tax_dedu'] - $row2['payback_deduction']) + $row2['given_amount'];
    $net_total += $total_salary;
    $tbl .= '
    <tr>
      	<td style="width:35px;">' . $i . '</td>
        <td>' . $row2["invoice"] . '</td>
        <td>' . $row2["name"] . '</td>
        <td>' . $row2["gender"] . '</td>
        <td>' . $row2["city"] . '</td>
        <td>' . $row2["acName"] . '</td>
        <td>' . htmlentities($row2["acntCode"]) . '</td>
        <td>' . $row2["acNo"] . '</td>
        <td>' . $misc->dated($row2["dated"]) . '</td>
        <td>' . ($row2['is_paid'] == 1 ? "Paid" : "Unpaid") . '</td>
        <td>' . $misc->dated($row2['paid_date']) . '</td>
        <td>' . $total_salary . '</td>
    </tr>';
    $i++;
    }
}

$tbl .= "<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>Total</td>
    <td>" . round($net_total, 2) . "</td>
    </tr>  
</table>";

$pdf->writeHTML($tbl, true, false, false, false, '');


//Close and output PDF document
list($a, $b) = explode('.', basename(__FILE__));
$pdf->Output($a . '.pdf', 'I');
