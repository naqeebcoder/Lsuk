<?php 
include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel']; 
$proceed=@$_GET['proceed'];
$multInvoicNo=@$_GET['multInvoicNo']; 
$search_1=@$_GET['search_1'];
$orgs=array();
if (isset($search_1) && $search_1 != "") {
    $orgs = explode(",", $search_1);
}
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];
$p_org = @$_GET['p_org'];
if (isset($p_org) && (!empty($p_org))) {
    $p_org_ad = $acttObj->read_specific("GROUP_CONCAT(CONCAT('''', comp_reg.abrv, '''' )) as ch_ids", "subsidiaries,comp_reg", " subsidiaries.child_comp=comp_reg.id AND subsidiaries.parent_comp=$p_org")['ch_ids'] ?: '';
} else {
    $p_org_ad = "";
}
$i=1;
$g_total_interp=0;$g_total_telep=0;$g_total_trans=0;$g_total_vat_interp=0;$g_total_vat_telep=0;$g_total_vat_trans=0;$non_vat=0;$non_vat_tlep=0;$non_vat_trans=0;$non_vat_interp=0;$withou_VAT_interp=0;$withou_VAT_telp=0;$withou_VAT_trans=0;$C_travelCost=0;$C_rateMile_cost=0;$C_admnchargs_interp=0;$C_admnchargs_telep=0;$C_admnchargs_trans=0;$C_otherexpns=0;$total=0;$C_chargeTravelTime=0;

$query="SELECT due_date,dated FROM mult_inv where m_inv='$multInvoicNo'";
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){
    $rep_date=$row["dated"];
    $due_date=$row["due_date"];
}
$query="SELECT  * FROM comp_reg	where id IN ".(!empty($p_org)?"($p_org)":"($search_1)");
$comps=0;
$name=$comp_abrv=$comp_abrv_q=$buildingName=$line1=$line2=$streetRoad=$postCode=$city=$vat_num=array();
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result))
{
    array_push($name,$row["name"]);
    array_push($comp_abrv,$row["abrv"]);
    array_push($comp_abrv_q,"'".$row["abrv"]."'");
    array_push($buildingName,$row["buildingName"]);
    array_push($line1,$row["line1"]);
    array_push($line2,$row["line2"]);
    array_push($streetRoad,$row["streetRoad"]);
    array_push($postCode,$row["postCode"]);
    array_push($city,$row["city"]);
    array_push($vat_num,$row["vn"]);
    $comps=$comps+1;
}

//........................................//\\//\\Invoice #//\\//\\//\\...........................................//

	//$acttObj->editFun($table,$edit_id,'invoiceNo',$invoice);
//.....................................................//\\//\\//\\//\\//\\//\\//\\//\\//\\..........................
$comp_abrv_str="";
if(!empty($comp_abrv)){
	$comp_abrv_str=implode(",",$comp_abrv_q);
}  

if(!empty($search_1) || !empty($p_org))
{
    $query="SELECT interpreter.*, interpreter_reg.name  
    FROM interpreter
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and  
       interpreter.multInvoicNo='$multInvoicNo' and interpreter.commit=0 and 
       interpreter.multInv_flag=1 and assignDate between '$search_2' and '$search_3' and 
       interpreter.orgName IN ".(!empty($p_org_ad)?"($p_org_ad)":"($comp_abrv_str)" )." order by assignDate Asc ";
	   
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
$pdf->AddPage('L');
$pdf->SetFont('helvetica', '', 8);

$invoice_to="";
for($kj=0;$kj<count($name);$kj++){
	$invoice_to .= "<p><br>Invoice To: ".$name[$kj]."<br/>".$buildingName[$kj]."<br/>".$line1[$kj]."<br/>".$line2[$kj]."<br/>".$streetRoad[$kj]."<br/>".$city[$kj]."<br/>".$postCode[$kj]."<br/></p>";
    if($comp_abrv[$kj]=='CASPA'){
        $invoice_to .= "<br><br>VAT Number: ".$vat_num[$kj]." <br>";
    }
}

// Table with rowspans and THEAD
$tbl = <<<EOD
<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
EOD;

$tbl.=<<<EOD
<div>
<span align="right"> Date: {$misc->sys_date()} </span>
<h2 style="text-decoration:underline; text-align:center">Invoice ({$multInvoicNo})</h2>
$invoice_to
<p>Date Range: {$misc->dated($search_2)} to {$misc->dated($search_3)} </p>
</div>
EOD;
$tbl.=<<<EOD
<table width="105%" cellpadding="2">
<thead>
<tr>
 	<th style="width:30px;">S.No</th>
    <th>Job Date</th>
    <th>Type</th>
    <th>Lang</th>
    <th>Client Name</th>
    <th>Org.Ref</th>
    <th>Units</th>
    <th>Unit Cost</th>
    <th>Job Cost</th>
    <th>Travel Cost</th>      
    <th>Travel Exp</th>         
    <th>Non-vatable</th>              
    <th>Admin Charge</th>  
    <th>Total Cost</th>     
    <th>Other Exp<br>Non-Vatable</th>          
    <th>Booking Type</th>
    <th>P.Order #</th>
 </tr>

</thead>
EOD;
while($row = mysqli_fetch_assoc($result)){
    $g_total_interp=$row["total_charges_comp"] + $g_total_interp;
    $g_total_vat_interp=$row["total_charges_comp"] * $row["cur_vat"] + $g_total_vat_interp;
    $C_admnchargs_interp=$row["C_admnchargs"]+$C_admnchargs_interp; 
    $C_otherexpns=$row["C_otherexpns"]+$C_otherexpns; 
    $C_chargeTravelTime=$row["C_chargeTravelTime"] + $C_chargeTravelTime;

    if($proceed=='Yes'){ 
    $acttObj->editFun('interpreter',$row['id'],'multInvoicNo',$invoice);
    $acttObj->editFun('interpreter',$row['id'],'multInv_flag',1);
    }

    if($proceed=='Cancel')
    { 
        $acttObj->editFun('interpreter',$row['id'],'multInvoicNo','');
        $acttObj->editFun('interpreter',$row['id'],'multInv_flag',0);
        $acttObj->del_comp('mult_inv','m_inv',$row['multInvoicNo']);
    }
    $withou_VAT_interp=$row["C_otherexpns"];
    $C_hoursWorkd_C_rateHour=$row["C_hoursWorkd"]* $row["C_rateHour"];

$tbl.=<<<EOD
    <tr>
      	<td style="width:30px;">{$i}</td>
<td>{$misc->dated($row["assignDate"])}</td>
<td>Interpreting</td>
<td>{$row["source"]}</td>
<td>{$row["inchPerson"]}</td>
<td>{$row["orgRef"]}</td>
<td>{$row["C_hoursWorkd"]}</td>
<td>{$row["C_rateHour"]}</td>
<td>{$C_hoursWorkd_C_rateHour}</td>
<td>{$row["C_chargeTravelTime"]}</td>
<td>{$row["C_chargeTravel"]}</td>
<td>{$withou_VAT_interp}</td>
<td>{$row["C_admnchargs"]}</td>
<td>{$misc->numberFormat_fun($row["total_charges_comp"])}</td>
<td>{$withou_VAT_interp}</td>
<td>{$row["bookinType"]}</td>
<td>{$row["porder"]}</td>
    </tr>
EOD;

 $i++;
}

$query_telep="SELECT telephone.*, interpreter_reg.name  FROM telephone inner join interpreter_reg on telephone.intrpName = interpreter_reg.id where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.multInvoicNo='$multInvoicNo' and telephone.commit=0 and telephone.multInv_flag=1 and assignDate between '$search_2' and '$search_3' and telephone.orgName IN ".(!empty($p_org_ad)?"($p_org_ad)":"($comp_abrv_str)" )." order by assignDate Asc ";
	  
$result_telep = mysqli_query($con, $query_telep);
while($row = mysqli_fetch_assoc($result_telep)){$g_total_telep=$row["total_charges_comp"] + $g_total_telep;$g_total_vat_telep=$row["total_charges_comp"] * $row["cur_vat"] + $g_total_vat_telep ;$non_vat_tlep=$row["total_charges_comp"] - $row["C_otherCharges"]+$non_vat_tlep;

if($proceed=='Yes'){ 
    $acttObj->editFun('telephone',$row['id'],'multInvoicNo',$invoice);
    $C_admnchargs_telep=$row["C_admnchargs"]+$C_admnchargs_telep;
    $acttObj->editFun('telephone',$row['id'],'multInv_flag',1);
}

if($proceed=='Cancel')
{ 
    $acttObj->editFun('telephone',$row['id'],'multInvoicNo','');
    $acttObj->editFun('telephone',$row['id'],'multInv_flag',0);
    $acttObj->del_comp('mult_inv','m_inv',$row['multInvoicNo']);
}

$withou_VAT_telp=$row["C_otherCharges"];$non_vat_tlep=$row["total_charges_comp"] - $row["C_otherCharges"];
$tbl.=<<<EOD
    <tr>
      	<td style="width:30px;">{$i}</td>
<td>{$misc->dated($row["assignDate"])}</td>
<td>Telephone</td>
<td>{$row["source"]}</td>
<td>{$row["inchPerson"]}</td>
<td>{$row["orgRef"]}</td>
<td>{$row["C_hoursWorkd"]}</td>
<td>{$row["C_rateHour"]}</td>
<td>{$non_vat_tlep}</td>
<td>N/A</td>
<td>N/A</td>
<td>{$withou_VAT_telp}</td>
<td>{$row["C_admnchargs"]}</td>
<td>{$misc->numberFormat_fun($row["total_charges_comp"])}</td>
<td>{$withou_VAT_telp}</td>
<td>{$row["bookinType"]}</td>
<td>{$row["porder"]}</td>
    </tr>
EOD;
 $i++;}
 
$query_trans="SELECT translation.*, interpreter_reg.name  FROM translation inner join interpreter_reg on translation.intrpName = interpreter_reg.id where translation.deleted_flag = 0 and  translation.order_cancel_flag=0 and  translation.multInvoicNo='$multInvoicNo' and translation.commit=0 and translation.multInv_flag=1 and asignDate between '$search_2' and '$search_3' and translation.orgName IN ".(!empty($p_org_ad)?"($p_org_ad)":"($comp_abrv_str)" )." order by asignDate  Asc ";
	  
$result_trans = mysqli_query($con, $query_trans);
while($row = mysqli_fetch_assoc($result_trans)){ $g_total_trans=$row["total_charges_comp"] + $g_total_trans ;$g_total_vat_trans=$row["total_charges_comp"] * $row["cur_vat"] + $g_total_vat_trans ;$C_admnchargs_trans=$row["C_admnchargs"]+$C_admnchargs_trans;$non_vat_trans=$row["total_charges_comp"] - $row["C_otherCharg"]+$non_vat_trans;
if($proceed=='Yes'){ 
    $acttObj->editFun('translation',$row['id'],'multInvoicNo',$invoice);
    $acttObj->editFun('translation',$row['id'],'multInv_flag',1);
}

if($proceed=='Cancel')
{ 
    $acttObj->editFun('translation',$row['id'],'multInvoicNo','');
    $acttObj->editFun('translation',$row['id'],'multInv_flag',0);
    $acttObj->del_comp('mult_inv','m_inv',$row['multInvoicNo']);
}

$withou_VAT_trans=$row["C_otherCharg"];$non_vat_trans=$row["total_charges_comp"] - $row["C_otherCharg"];
$tbl.=<<<EOD
    <tr>
      	<td style="width:30px;">{$i}</td>
<td>{$misc->dated($row["asignDate"])}</td>
<td>Translation</td>
<td>{$row["source"]}</td>
<td>{$row["nameRef"]}</td>
<td>{$row["orgRef"]}</td>
<td>{$row["numberUnit"]}</td>
<td>{$row["C_rpU"]}</td>
<td>{$non_vat_trans}</td>
<td>N/A</td>
<td>N/A</td>
<td>{$withou_VAT_trans}</td>
<td>{$row["C_admnchargs"]}</td>
<td>{$misc->numberFormat_fun($row["total_charges_comp"])}</td>
<td>{$withou_VAT_trans}</td>
<td>{$row["bookinType"]}</td>
<td>{$row["porder"]}</td>
    </tr>
EOD;
 $i++;}
 
$g_total_all=$g_total_interp  + $g_total_telep + $g_total_trans;$vat_all=($g_total_vat_interp + $g_total_vat_telep + $g_total_vat_trans);$grand_total=$C_otherexpns + ($g_total_interp  + $g_total_telep + $g_total_trans) + $vat_all;
$tbl.=<<<EOD

<tr>
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
<td></td>
<td></td>
<td><b>Total</b></td>
<td><b>{$misc->numberFormat_fun($g_total_all)}</b></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
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
<td></td>
<td></td>
<td><b>VAT @20%</b></td>
<td><b>{$misc->numberFormat_fun($vat_all)}</b></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
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
<td></td>
<td></td>
<td><b>Non VAT</b></td>
<td><b>{$misc->numberFormat_fun($C_otherexpns)}</b></td>
<td></td>
<td></td>
<td></td>
</tr>

<tr>
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
<td></td>
<td></td>
<td><b>Total Invoice</b></td>
<td><b>{$misc->numberFormat_fun($grand_total)}</b></td>
<td></td>
<td></td>
<td></td>
</tr>
</table>

EOD;
$tbl.=<<<EOD
<br><br>
Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234.
Company                                                     Registration Number 7760366 VAT Number 198427362
Thank You For Business With Us
<br><br>
Please pay your invoice within 21 days from the date of invoice. <u>Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998"</u> if no payment was made within reasonable time frame
EOD;
	

if($proceed=='Received'){ //($table,$col,$data,$comp_col,$comp_data)
$cur_date = date('Y-m-d');
$acttObj->editFun_comp('mult_inv','status','Received','m_inv',$multInvoicNo);
$acttObj->editFun_comp('mult_inv','from_date',$search_2,'m_inv',$multInvoicNo);
$acttObj->editFun_comp('mult_inv','to_date',$search_3,'m_inv',$multInvoicNo);
$acttObj->editFun_comp('mult_inv','paid_date',$cur_date,'m_inv',$multInvoicNo);
$acttObj->insert("daily_logs",array("action_id"=>25,"user_id"=>$_SESSION['userId'],"details"=>"Mult.Invoice ID: ".$maxId));
}

if($proceed=='Undo'){ //($table,$col,$data,$comp_col,$comp_data)
$acttObj->editFun_comp('mult_inv','status','','m_inv',$multInvoicNo);
$acttObj->editFun_comp('mult_inv','from_date',$search_2,'m_inv',$multInvoicNo);
$acttObj->editFun_comp('mult_inv','to_date',$search_3,'m_inv',$multInvoicNo);
$acttObj->insert("daily_logs",array("action_id"=>44,"user_id"=>$_SESSION['userId'],"details"=>"Mult.Invoice ID: ".$maxId));
}


$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output($a.'.pdf', 'I');
//============================================================+
// END OF FILE
//==========================================================EXCEL FORMAT=========================================================+
}