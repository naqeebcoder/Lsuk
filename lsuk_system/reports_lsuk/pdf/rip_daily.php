<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];session_start();$UserName=$_SESSION['UserName'];$prv=$_SESSION['prv']; $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='interpreter';$total_charges_comp=0;$C_otherCost=0;$g_total=0;$g_vat=0;$C_otherCost=0;$non_vat=0;$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$search_1);
// if($prv=='Management'){
$query="SELECT
   interpreter.assignDate,
   interpreter.assignTime,
   interpreter.source,
   interpreter.orgName,
   interpreter.intrpName,
   interpreter.inchPerson,
   interpreter.orgContact,   
   interpreter.dated,   
    interpreter.inchNo,
    interpreter.line1,
    interpreter.line2,
    interpreter.inchRoad,
    interpreter.inchCity,
   interpreter.submited,
   interpreter.bookedVia,   
   interpreter.aloct_by,
   interpreter.aloct_date,
   interpreter_reg.name as allocated_to,
   'Interpreter' as tble   
FROM
   interpreter 
   inner join
      interpreter_reg 
      on interpreter.intrpName = interpreter_reg.id 
where

	interpreter.deleted_flag = 0 
   and interpreter.order_cancel_flag=0
   and interpreter.orgName like '$search_1%' 
   and interpreter.dated between '$search_2' and '$search_3'    
   union
SELECT
   telephone.assignDate,
   telephone.assignTime,
   telephone.source,
   telephone.orgName,
   telephone.intrpName,
   telephone.inchPerson,
   telephone.orgContact, 
   telephone.dated,   
    telephone.inchNo,
    telephone.line1,
    telephone.line2,
    telephone.inchRoad,
    telephone.inchCity, 
   telephone.submited,
   telephone.bookedVia, 
   telephone.aloct_by,
   telephone.aloct_date,
   interpreter_reg.name as allocated_to,
   'Telephone' as tble
FROM
   telephone
   inner join
      interpreter_reg 
      on telephone.intrpName = interpreter_reg.id 
where
	telephone.deleted_flag = 0 
   and telephone.order_cancel_flag=0
	and	telephone.orgName like '$search_1%' 
   	and telephone.dated between '$search_2' and '$search_3'    
union
SELECT
   translation.asignDate  as assignDate,
   'Nil' as assignTime,
   translation.source,
   translation.orgName,
   translation.intrpName,   
   'Nil' as inchPerson,
   translation.orgContact,
   translation.dated, 
    translation.inchNo,
    translation.line1,
    translation.line2,
    translation.inchRoad,
    translation.inchCity, 
   translation.submited,
   translation.bookedVia, 
   translation.aloct_by,
   translation.aloct_date,
   interpreter_reg.name as allocated_to,
   'Translation' as tble
FROM
   translation 
   inner join
      interpreter_reg 
      on translation.intrpName = interpreter_reg.id    
where
	translation.deleted_flag = 0 
   and translation.order_cancel_flag=0
   and translation.orgName like '$search_1%' 
   and translation.dated between '$search_2' and '$search_3' 
   
";
// }
// else{
// $query="SELECT
//   interpreter.assignDate,
//   interpreter.assignTime,
//   interpreter.source,
//   interpreter.orgName,
//   interpreter.intrpName,
//   interpreter.inchPerson,
//   interpreter.orgContact,   
//   interpreter.dated,   
//     interpreter.inchNo,
//     interpreter.line1,
//     interpreter.line2,
//     interpreter.inchRoad,
//     interpreter.inchCity,
//   interpreter.submited,
//   interpreter.bookedVia,   
//   interpreter.aloct_by,
//   interpreter.aloct_date,
//   interpreter_reg.name as allocated_to,
//   'Interpreter' as tble   
// FROM
//   interpreter 
//   inner join
//       interpreter_reg 
//       on interpreter.intrpName = interpreter_reg.id 
// where
// 	interpreter.deleted_flag = 0 
//   and interpreter.order_cancel_flag=0
//   and interpreter.submited='$UserName' 
//   and interpreter.orgName like '$search_1%' 
//   and interpreter.dated between '$search_2' and '$search_3'    
//   union
// SELECT
//     telephone.assignDate,
//   telephone.assignTime,
//   telephone.source,
//   telephone.orgName,
//   telephone.intrpName,
//   telephone.inchPerson,
//   telephone.orgContact, 
//   telephone.dated,   
//     telephone.inchNo,
//     telephone.line1,
//     telephone.line2,
//     telephone.inchRoad,
//     telephone.inchCity, 
//   telephone.submited,
//   telephone.bookedVia, 
//   telephone.aloct_by,
//   telephone.aloct_date,
//   interpreter_reg.name as allocated_to,
//   'Telephone' as tble
// FROM
//   telephone
//   inner join
//       interpreter_reg 
//       on telephone.intrpName = interpreter_reg.id 
// where
// 	telephone.deleted_flag = 0 
//   and telephone.order_cancel_flag=0
//   and telephone.submited='$UserName' 
// 	and	telephone.orgName like '$search_1%' 
//   	and telephone.dated between '$search_2' and '$search_3'    
// union
// SELECT
//   translation.asignDate  as assignDate,
//   'Nil' as assignTime,
//   translation.source,
//   translation.orgName,
//   translation.intrpName,   
//   'Nil' as inchPerson,
//   translation.orgContact,
//   translation.dated, 
//     translation.inchNo,
//     translation.line1,
//     translation.line2,
//     translation.inchRoad,
//     translation.inchCity, 
//   translation.submited,
//   translation.bookedVia, 
//   translation.aloct_by,
//   translation.aloct_date,
//   interpreter_reg.name as allocated_to,
//   'Translation' as tble
// FROM
//   translation 
//   inner join
//       interpreter_reg 
//       on translation.intrpName = interpreter_reg.id 
   
// where
// 	translation.deleted_flag = 0 
//   and translation.order_cancel_flag=0
//   and translation.submited='$UserName' 
//   and translation.orgName like '$search_1%' 
//   and translation.dated between '$search_2' and '$search_3' 
   
// "; }
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

$tbl.=<<<EOD
<div>
<h2 align="center"><u>Daily Booking Report for {$comp_name}</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range: Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
EOD;
$tbl.=<<<EOD
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
	<th>Mode</th>
	<th>Assgn Date</th>
    <th>Time</th>
    <th>Language</th>
    <th>Company</th>
    <th>Contact Name</th>
    <th>Contact Person</th>
    <th>Booked Via</th>
    <th>Booked By</th>
    <th>Allocated By</th>
    <th>Linguistic</th>
 </tr>

</thead>
EOD;
while($row = mysqli_fetch_assoc($result)){$submited=$row['submited'].'('.$misc->dated($row['dated']).')';$aloct_by=$row['aloct_by'].'('.$misc->dated($row['aloct_date']).')';$allocated_to=$row['allocated_to'];
$tbl.=<<<EOD
    <tr>
      	<td style="width:35px;">{$i}</td>
<td>{$row["tble"]}</td>
<td>{$misc->dated($row["assignDate"])}</td>
<td>{$row["assignTime"]}</td>
<td>{$row["source"]}</td>
<td>{$row["orgName"]}</td>
<td>{$row["inchPerson"]}</td>
<td>{$row["orgContact"]}</td>
<td>{$row["bookedVia"]}</td>
<td>{$submited}</td>
<td>{$aloct_by}</td>
<td>{$allocated_to}</td>
    </tr>
EOD;
 $i++;}
$tbl.=<<<EOD
	  
</table>

EOD;
$tbl.=<<<EOD
EOD;
	



$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output($a.'.pdf', 'I');
//============================================================+
// END OF FILE
//==========================================================EXCEL FORMAT=========================================================+
