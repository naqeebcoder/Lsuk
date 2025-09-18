<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];session_start();$UserName=$_SESSION['UserName'];$prv=$_SESSION['prv']; $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;
$current = date('Y-m-d');
if(isset($search_1) && $search_2==$current && $search_3==$current){
$append_int = "and interp_salary.interp=" . $search_1;
$query = "SELECT interp_salary.*,interpreter_reg.name FROM interp_salary,interpreter_reg where interp_salary.interp=interpreter_reg.id AND interp_salary.deleted_flag=0 $append_int ORDER BY interp_salary.dated DESC ";

}else{
$append_int = "and interp_salary.interp=" . $search_1;
$append_date = "and interp_salary.dated BETWEEN '$search_2' AND '$search_3'";
$query = "SELECT interp_salary.*,interpreter_reg.name FROM interp_salary,interpreter_reg where interp_salary.interp=interpreter_reg.id AND interp_salary.deleted_flag=0 $append_int $append_date  ORDER BY interp_salary.dated DESC ";
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
include'rip_header.php';
include'rip_footer.php';// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

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
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
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

$date_range = ($search_2==$current && $search_3==$current ? 'All Time':'Date From ['.$misc->dated($search_2).'] Date To ['.$misc->dated($search_3).']');
$tbl.=<<<EOD
<div>
<h2 align="center"><u>Interpreters Salary Report</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date Range: $date_range</p>
</div>
EOD;
$tbl.=<<<EOD
<table>
<thead>

 <tr>
 	<th style="width:35px;">Sr.No</th>
	<th>Linguistic</th>
	<th>Invoice</th>
	<th>From</th>
	<th>To</th>
	<th>Deductions</th>
	<th>Additions</th>
	<th>Salary</th>
	<th>Status</th>
	<th>Paid Date</th>
 </tr>

</thead>
EOD;

while($row = mysqli_fetch_assoc($result)){
$paid_status = $row['is_paid'] == 1 ? "<span class='label label-success'><i class='fa fa-check-circle'></i> Paid</span>" : "<span class='label label-warning'>Unpaid</span>";
$tbl.=<<<EOD
    
	<tr>
      	<td style="width:35px;">{$i}</td>
		<td>{$row["name"]}</td>
		<td>{$row['invoice']}</td>
		<td>{$misc->dated($row['frm'])}</td>
		<td>{$misc->dated($row['todate'])}</td>
		<td>{$misc->numberFormat_fun($row['ni_dedu'] + $row['tax_dedu'] + $row['payback_deduction'])}</td>
		<td>{$misc->numberFormat_fun($row['given_amount'])}</td>
		<td>{$misc->numberFormat_fun(($row['salry'] - $row['ni_dedu'] - $row['tax_dedu'] - $row['payback_deduction']) + $row['given_amount'])}</td>
		<td>{$paid_status}</td>
		<td>{$misc->dated($row['paid_date'])}</td>
    </tr>
EOD;
 $i++;}

$tbl.=<<<EOD
	  
</table>

EOD;
$tbl.=<<<EOD
EOD;
	



$pdf->writeHTML($tbl, true, false, false, false, '');


//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output($a.'.pdf', 'I');

