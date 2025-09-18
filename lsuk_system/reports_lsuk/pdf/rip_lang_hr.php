<?php include '../../db.php';include_once ('../../class.php');
$excel=@$_GET['excel'];session_start();
$UserName=$_SESSION['UserName'];
$prv=$_SESSION['prv'];
$search_1=@$_GET['search_1'];
$interpreter_name=$acttObj->read_specific("name","interpreter_reg","id=".$search_1)['name'];
$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;
if($search_2 && $search_3){
$query="SELECT DISTINCT source from (SELECT DISTINCT source FROM interpreter WHERE intrpName='$search_1' and deleted_flag=0 and order_cancel_flag=0 and hoursWorkd>0 and assignDate BETWEEN('$search_2')AND('$search_3') GROUP BY source UNION ALL
SELECT DISTINCT source FROM telephone WHERE intrpName='$search_1' and deleted_flag=0 and order_cancel_flag=0 and hoursWorkd>0 and assignDate BETWEEN('$search_2')AND('$search_3') GROUP BY source UNION ALL
SELECT DISTINCT source FROM translation WHERE intrpName='$search_1' and deleted_flag=0 and order_cancel_flag=0 and numberUnit>0 and asignDate BETWEEN('$search_2')AND('$search_3') GROUP BY source) as grp where 1 ORDER BY source";
}else{
 $query="SELECT DISTINCT source from (SELECT DISTINCT source FROM interpreter WHERE intrpName='$search_1' and deleted_flag=0 and order_cancel_flag=0 and hoursWorkd>0 GROUP BY source UNION ALL
SELECT DISTINCT source FROM telephone WHERE intrpName='$search_1' and deleted_flag=0 and order_cancel_flag=0 and hoursWorkd>0 GROUP BY source UNION ALL
SELECT DISTINCT source FROM translation WHERE intrpName='$search_1' and deleted_flag=0 and order_cancel_flag=0 and numberUnit>0 GROUP BY source) as grp where 1 ORDER BY source";  
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
$tbl='<style>
table {border-collapse: collapse; width:100%;}
th {border: 1px solid #999; padding: 0.5rem;text-align left; background-color:#039; color:#FFF;font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left; word-wrap: break-word;}
</style>
<div>
<h2 align="center"><u>Language wise hours report</u></h2>
<p align="left">Interpreter Name: <b>'.$interpreter_name.'</b></p>
<p align="right">Report Date: '.$misc->sys_date().'<br/>
Date Range: Date From ['.$misc->dated($search_2).'] Date To ['.$misc->dated($search_3).']</p>
</div>
<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
	<th>Language</th>
	<th>Interpreting (hours)</th>
	<th>Telephone (hours)</th>
	<th>Translation (units)</th>
 </tr>
</thead>';

while($row = mysqli_fetch_assoc($result)){
    if($search_2 && $search_3){
        $int=$acttObj->read_specific("IFNULL(round(SUM(hoursWorkd),2),0) as hours","interpreter","source='".$row["source"]."' and intrpName='".$search_1."' and deleted_flag=0 and order_cancel_flag=0 and hoursWorkd>0 and assignDate BETWEEN('".$search_2."')AND('".$search_3."')")['hours'];
        $tp=$acttObj->read_specific("IFNULL(round(SUM(hoursWorkd/60),2),0) as hours","telephone","source='".$row["source"]."' and intrpName='".$search_1."' and deleted_flag=0 and order_cancel_flag=0 and hoursWorkd>0 and assignDate BETWEEN('".$search_2."')AND('".$search_3."')")['hours'];
        $tr=$acttObj->read_specific("IFNULL(round(SUM(numberUnit),2),0) as hours","translation","source='".$row["source"]."' and intrpName='".$search_1."' and deleted_flag=0 and order_cancel_flag=0 and numberUnit>0 and asignDate BETWEEN('".$search_2."')AND('".$search_3."')")['hours'];
    }else{
        $int=$acttObj->read_specific("IFNULL(round(SUM(hoursWorkd),2),0) as hours","interpreter","source='".$row["source"]."' and intrpName='".$search_1."' and deleted_flag=0 and order_cancel_flag=0 and hoursWorkd>0")['hours'];
        $tp=$acttObj->read_specific("IFNULL(round(SUM(hoursWorkd/60),2),0) as hours","telephone","source='".$row["source"]."' and intrpName='".$search_1."' and deleted_flag=0 and order_cancel_flag=0 and hoursWorkd>0")['hours'];
        $tr=$acttObj->read_specific("IFNULL(round(SUM(numberUnit),2),0) as hours","translation","source='".$row["source"]."' and intrpName='".$search_1."' and deleted_flag=0 and order_cancel_flag=0 and numberUnit>0")['hours'];
    }
$tbl.='<tr>
      	<td style="width:35px;">'.$i.'</td>
        <td>'.$row["source"].'</td>
        <td>'.$int.'</td>
        <td>'.$tp.'</td>
        <td>'.$tr.'</td>
    </tr>';
$i++;
}

$tbl.='</table>';
	



$pdf->writeHTML($tbl, true, false, false, false, '');


//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output($a.'.pdf', 'I');

