<?php session_start();
include '../../db.php';
include_once ('../../class.php');
$semi="\"'\"";
$excel=@$_GET['excel'];
$type=@$_GET['type'];
$search_1=@$_GET['search_1'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];
$orgz=$_GET['search_1'];

if($type=='super'){
   $data1=$acttObj->read_specific("DISTINCT GROUP_CONCAT(parent_companies.sup_child_comp) as data1","parent_companies","parent_companies.sup_parent_comp IN (".$orgz.")");
   $all_abrv=$acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv","comp_reg","id IN (".$data1['data1'].")","set SESSION group_concat_max_len=10000");
   $all_cz=$acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz","child_companies","child_companies.parent_comp IN (".$data1['data1'].")","set SESSION group_concat_max_len=10000");
}else if($type=='parent'){
    $data1=$acttObj->read_specific("GROUP_CONCAT(comp_reg.id) as data1","comp_reg","id IN (".$orgz.")");
    $all_abrv=$acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv","comp_reg","id IN (".$data1['data1'].")","set SESSION group_concat_max_len=10000");
   $all_cz=$acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz","child_companies","child_companies.parent_comp IN (".$data1['data1'].")","set SESSION group_concat_max_len=10000");
}else{
    $all_abrv=$acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv","comp_reg","id IN (".$orgz.")","set SESSION group_concat_max_len=10000");
    $all_cz=$acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,comp_reg.abrv,$semi) as all_cz","comp_reg","comp_reg.id IN ($orgz)","set SESSION group_concat_max_len=10000");
}
$display_org=$acttObj->read_specific("GROUP_CONCAT(comp_reg.name) as orgName","comp_reg","id IN (".$orgz.")");
$search_1=$all_cz['all_cz'];
$i=1;
$table='interpreter';
$total_charges_comp=0;
$C_otherCost=0;
$g_total=0;
$g_vat=0;
$C_otherCost=0;
$non_vat=0;
$comp_name=$acttObj->unique_data('comp_reg','name','abrv',$search_1);
$append_companies="orgName IN (".$all_cz['all_cz'].")";
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
   interpreter.order_cancel_flag,
   interpreter.orderCancelatoin,
   interpreter.order_cancelledby,
   interpreter.cn_date,
   interpreter.cn_time,
   interpreter.cn_t_id,
   interpreter.cn_r_id,
   interpreter.pay_int,
   interpreter_reg.name as allocated_to,
   'Interpreter' as tbl   
FROM
   interpreter ,interpreter_reg  
where
interpreter.intrpName = interpreter_reg.id
and interpreter.deleted_flag = 0 
and (interpreter.order_cancel_flag=1 OR interpreter.orderCancelatoin=1)
and interpreter.$append_companies 
and interpreter.assignDate between '$search_2' and '$search_3'    
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
   telephone.order_cancel_flag,
   telephone.orderCancelatoin,
   telephone.order_cancelledby,
   telephone.cn_date,
   telephone.cn_time,
   telephone.cn_t_id,
   telephone.cn_r_id,
   telephone.pay_int,
   interpreter_reg.name as allocated_to,
   'Telephone' as tbl
FROM
   telephone,interpreter_reg  
where
telephone.intrpName = interpreter_reg.id
and telephone.deleted_flag = 0 
and (telephone.order_cancel_flag=1 OR telephone.orderCancelatoin=1)
and	telephone.$append_companies 
and telephone.assignDate between '$search_2' and '$search_3'    
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
   translation.order_cancel_flag,
   translation.orderCancelatoin,
   translation.order_cancelledby,
   translation.cn_date,
   translation.cn_time,
   translation.cn_t_id,
   translation.cn_r_id,
   translation.pay_int,
   interpreter_reg.name as allocated_to,
   'Translation' as tbl
FROM
   translation,interpreter_reg     
	where translation.intrpName = interpreter_reg.id
and translation.deleted_flag = 0 
and (translation.order_cancel_flag=1 OR translation.orderCancelatoin=1)
and translation.$append_companies 
and translation.asignDate between '$search_2' and '$search_3'";
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
$tbl = "<style>
table {border-collapse: collapse; width:100%;}
th {border: 1px solid #999; padding: 0.5rem;text-align left; background-color:#039; color:#FFF;font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left; word-wrap: break-word;}
</style>
<div>
<h2 align='center'><u>Report for ".$display_org['orgName']."</u></h2>
<p align='right'>Report  Date: ".$misc->sys_date()."<br />
  Date  Range: Date From [".$misc->dated($search_2)."] Date To [".$misc->dated($search_3)."]</p>
</div><table>
<thead>
<tr>
 	<th>Sr.No</th>
	<th>Mode</th>
    <th>Language</th>
    <th>Company</th>
    <th>Chargeable Status</th>
    <th>Cancelled By</th>
	<th>Assignment Date/Time</th>
    <th>Cancelled Date</th>
    <th>Reason</th>
 </tr>
</thead>";
while($row = mysqli_fetch_assoc($result)){
	$cancellation_type=$row["cn_t_id"]?$acttObj->read_specific("cd_title","cancellation_drops","cd_id=".$row["cn_t_id"])['cd_title']:"Other";
	$cancellation_type=$cancellation_type?str_replace("[DATE]",$row["cn_date"],$cancellation_type):$cancellation_type;
	$cancellation_reason=$row["cn_r_id"]?$acttObj->read_specific("cr_title","cancel_reasons","cr_id=".$row["cn_r_id"])['cr_title']:"Other";
	$chargeable_status=$row["order_cancel_flag"]==1?'Not Chargeable':'Chargeable';
	$submited=$row['submited'].'('.$misc->dated($row['dated']).')';
	$aloct_by=$row['aloct_by'].'('.$misc->dated($row['aloct_date']).')';
	$allocated_to=$row['allocated_to'];
$tbl.="<tr>
      	<td>".$i."</td>
		<td>".$row["tbl"]."</td>
		<td>".$row["source"]."</td>
		<td>".$row["orgName"]."</td>
		<td>".$chargeable_status."</td>
		<td>".$row["order_cancelledby"]."</td>
		<td>".$misc->dated($row["assignDate"])." ".$row["assignTime"]."</td>
		<td>".$row["cn_date"].' '.$row["cn_time"]."</td>
		<td>".$cancellation_reason."</td>
	</tr>";
 $i++;
}
$tbl.="</table>";
	



$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output('/home/customer/www/lsuk.org/public_html/lsuk_system/reports_lsuk/pdf/'.$a.'.pdf', 'I');
//============================================================+
// END OF FILE
//==========================================================EXCEL FORMAT=========================================================+