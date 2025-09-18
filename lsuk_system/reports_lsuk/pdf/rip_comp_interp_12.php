<?php include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$search_1=$_GET['search_1'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];
if(!empty($search_1)){
$arr = explode(',', $search_1);
    $_words = " AND orgName IN ('".implode("','", $arr)."')";
}else{
    $_words='';
}
$x=0;
error_reporting(0);
$query="SELECT interpreter.orderCancelatoin,interpreter.porder,interpreter.nameRef,interpreter.rDate,interpreter.inchPerson,interpreter.inchEmail,interpreter.bookeddate,interpreter.bookedVia,interpreter.bookedtime,interpreter.orgRef,interpreter.id,interpreter.invoiceNo,interpreter.assignDate,interpreter.source,round(interpreter.C_otherexpns,2) as other_expenses,interpreter.rAmount,interpreter.postCode as postCode,round(interpreter.total_charges_comp*cur_vat,2) as cur_vatt,interpreter.total_charges_comp,round(((interpreter.total_charges_comp*cur_vat)+interpreter.total_charges_comp+interpreter.C_otherexpns),2) as invoice_cost, interpreter_reg.name, comp_reg.name as orgNam,'Face to Face' as type FROM interpreter
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv
	   where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and 
	   interpreter.assignDate between '$search_2' and '$search_3' $_words
	   UNION ALL 
	   SELECT telephone.orderCancelatoin,telephone.porder,telephone.nameRef,telephone.rDate,telephone.inchPerson,telephone.inchEmail,telephone.bookeddate,telephone.bookedVia,telephone.bookedtime,telephone.orgRef,telephone.id,telephone.invoiceNo,telephone.assignDate,telephone.source,'0' as other_expenses,telephone.rAmount,'' as postCode,round(telephone.total_charges_comp*cur_vat,2) as cur_vatt,telephone.total_charges_comp,round(((telephone.total_charges_comp*cur_vat)+telephone.total_charges_comp),2) as invoice_cost, interpreter_reg.name, comp_reg.name as orgNam,'Telephone' as type FROM telephone
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv
	   where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and 
	   telephone.assignDate between '$search_2' and '$search_3' $_words
	   UNION ALL 
	   SELECT translation.orderCancelatoin,translation.porder,translation.nameRef,translation.rDate,translation.inchContact,translation.inchEmail,translation.bookeddate,translation.bookedVia,translation.bookedtime,translation.orgRef,translation.id,translation.invoiceNo,translation.asignDate,translation.source,'0' as other_expenses,translation.rAmount,'' as postCode,round(translation.total_charges_comp*cur_vat,2) as cur_vatt,translation.total_charges_comp,round(((translation.total_charges_comp*cur_vat)+translation.total_charges_comp),2) as invoice_cost, interpreter_reg.name, comp_reg.name as orgNam,'Translation' as type FROM translation
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv
	   where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and 
	   translation.asignDate between '$search_2' and '$search_3' $_words";
$result = mysqli_query($con, $query);

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetSubject('TCPDF Tutorial');

// set default header data
include'rip_header_lndscp.php';
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
$pdf->AddPage('L', 'A4');
$pdf->SetFont('helvetica', '', 8);

// Table with rowspans and THEAD
$tbl = <<<EOD
<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;height:20px;}
</style>
EOD;

$tbl.=<<<EOD
<div>
<h2 align="center"><u>Client General Report – Overall Interpreting</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range: Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
<p>Orgnaization(s) Selected</p>
<table class="aa" border="1" cellspacing="1" style="width:700px">
  <tr>
    <td valign="top">{$search_1}</td>
  </tr>
</table><br/><br/>

EOD;
$tbl.=<<<EOD
<table>
<thead>
  <tr>
 	<th style="width:45px;">Sr. No.</th>
    <th>Assignment Date</th>
    <th>Job Type</th>
    <th>Language</th>
	<th>Job Ref</th>
	<th>Client Ref</th>
    <th>Post Code</th>
    <th>Linguistic</th>
    <th width="10%">Invoice No</th>
    <th>Name</th>
    <th>Email</th>
	<th>B Date</th>
	<th>B Time</th>
    <th>Via</th>
    <th>Invoice Cost</th>
    <th>Purch.Order</th>
	<th>Pay Date</th>
  </tr>

</thead>
EOD;

$runcompany="";
$nowcompany="";

$mapCoTotals=array();
ZeroCompTotal($mapCoTotals);

$f2f_count=0;
$tel_count=0;
$tr_count=0;
$f2f_cancel_count=0;
$tel_cancel_count=0;
$tr_cancel_count=0;
$loop=0;
$cancel_row='';
while($row = mysqli_fetch_assoc($result))
{
	$counter++;
	if($row['type']=='Face to Face'){
	    $f2f_count++;
	}else if($row['type']=='Telephone'){
	    $tel_count++;
	}else{
	    $tr_count++;
	}
	if($row['type']=='Face to Face' && $row["orderCancelatoin"]==1){
	    $f2f_cancel_count++;
	}
	if($row['type']=='Telephone' && $row["orderCancelatoin"]==1){
	    $tel_cancel_count++;
	}
	if($row['type']=='Translation' && $row["orderCancelatoin"]==1){
	    $tr_cancel_count++;
	}
	$other_expenses=$row["other_expenses"] + $other_expenses;
	$total_charges_comp=$row["total_charges_comp"] + $total_charges_comp;
	$cur_vatt=$row["cur_vatt"] + $cur_vatt;
	$invoice_cost=$row["invoice_cost"] + $invoice_cost;
	$porder=$row["porder"];
	$pay_date = '';
	if($row["rAmount"]>0)
	{
		$invstst='Paid';
		//echo $row['id'].' = '.$row['type'].' = '.$row["rDate"].'<br>';
		$pay_date = $misc->dated($row["rDate"]);
		//$pay_date = date('d-m-y',strtotime($row['rDate']));
	}
	else
	{
		$invstst='Un-Paid';
	}
	
	$nowcompany=$row["orgName"];
	//if ($loop==0)
	//	$runcompany=$nowcompany;
	if ($loop==0)
		OrgOutput::WriteTR($nowcompany,$runcompany,$tbl);

	$loop++;
	if ($runcompany!=$nowcompany)
	{
		ShowCompTotal($mapCoTotals,$tbl);
		ZeroCompTotal($mapCoTotals);
	}
	OrgOutput::WriteTR($nowcompany,$runcompany,$tbl);

	UpdateCompTotal($mapCoTotals,$row);
if($row["orderCancelatoin"]==1){
        $cancel_row=' style="background-color:#ff9898;"';
    }else{
        $cancel_row=' ';
    }
	$b_time = date('H:i a',strtotime($row['bookedtime']));
$tbl.=<<<EOD
    <tr {$cancel_row}>
      <td style="width:45px;">{$i}</td>
		<td>{$misc->dated($row["assignDate"])}</td>	
		<td>{$row["type"]}</td>	
		<td>{$row["source"]}</td>
		<td>{$row["nameRef"]}</td>
		<td>{$row["orgRef"]}</td>
		<td>{$row["postCode"]}</td>
		<td>{$row["name"]}</td>
		<td width="10%">{$row["invoiceNo"]}</td>
		<td>{$row["inchPerson"]}</td>
		<td>{$row["inchEmail"]}</td>
		<td>{$misc->dated($row["bookeddate"])}</td>
		<td>{$b_time}</td>
		<td>{$row["bookedVia"]}</td>
		<td>{$row["invoice_cost"]}</td>
		<td>{$porder}</td>
		<td>{$pay_date}</td>
    </tr>
EOD;
 $i++;
}
ShowCompTotal($mapCoTotals,$tbl);
$total_jobs=$f2f_count+$tel_count+$tr_count;
$total_cencelled=$f2f_cancel_count+$tel_cancel_count+$tr_cancel_count;
$tbl.=<<<EOD
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td><b>{$misc->numberFormat_fun($total_charges_comp)}</b></td>
<td><b>{$misc->numberFormat_fun($cur_vatt)}</b></td>
<td><b>{$misc->numberFormat_fun($other_expenses)}</b></td>
<td><b>{$misc->numberFormat_fun($invoice_cost)}</b></td>
<td></td>
<td></td>
</tr>
	  
</table><br><br>
<table class="aa" border="1">
  <tr>
    <td width="125">Total Face to Face Jobs</td>
    <td width="78">{$f2f_count}</td>
    <td width="160" border="0"></td>
    <td width="170">Total Face to Face Cancelled Jobs</td>
    <td width="80">{$f2f_cancel_count}</td>
  </tr>
  <tr>
    <td width="125">Total Telephone Jobs</td>
    <td width="78">{$tel_count}</td>
    <td width="160" border="0"></td>
    <td width="170">Total Telephone Cancelled Jobs</td>
    <td width="80">{$tel_cancel_count}</td>
  </tr>
  <tr>
    <td width="125">Total Translation Jobs</td>
    <td width="78">{$tr_count}</td>    
    <td width="160" border="0"></td>
    <td width="170">Total Translation Cancelled Jobs</td>
    <td width="80">{$tr_cancel_count}</td>
  </tr><tr>
    <td width="125">Total Jobs</td>
    <td width="78"><b>{$total_jobs}</b></td>
    <td width="160" border="0"></td>
    <td width="170">Total Cancelled Jobs</td>
    <td width="80"><b>{$total_cencelled}</b></td>
  </tr>
</table>

EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');


//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output($a.'.pdf', 'I');

function ZeroCompTotal(&$map)
{
	$map["invoice_cost"] = 0;
	$map["other_expenses"] =0;
	$map["total_charges_comp"] =0;
	$map["cur_vatt"] =0;
}

function UpdateCompTotal(&$map,&$row)
{
	$map["invoice_cost"]+=$row["invoice_cost"];
	$map["other_expenses"]+=$row["other_expenses"];
	$map["total_charges_comp"]+=$row["total_charges_comp"];
	$map["cur_vatt"]+=$row["cur_vatt"];
}

function ShowCompTotal(&$map,&$tbl)
{
	global $misc;

	$tbl.=<<<EOD
	<tr>
	</tr>				
EOD;

}
