<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='translation';$rpuXnumb=0;$otherCharg=0;$total_charges_comp=0;

//...................................................For Multiple Selection...................................\\
$counter=0; $arr = explode(',', $search_1);$_words = implode("' OR orgName like '", $arr);
//......................................\\//\\//\\//\\//........................................................\\
if(!empty($search_1)){
$query="SELECT  * FROM comp_reg	   
	   where abrv='$search_1'";	  
$result = mysqli_query($con, $query);
while($row = mysqli_fetch_assoc($result)){$name=$row["name"];$buildingName=$row["buildingName"];$line1=$row["line1"];$line2=$row["line2"];$streetRoad=$row["streetRoad"];$postCode=$row["postCode"];$city=$row["city"];}}
if(!empty($search_1)){
$query="SELECT $table.*, interpreter_reg.name, comp_reg.name as orgNam  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3' and (orgName like '%$_words%') 
	   order by orgNam";
	   }
	   else{$query="SELECT $table.*, interpreter_reg.name, comp_reg.name as orgNam  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3' 
	   order by orgNam";}
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
th {border: 1px solid #999; padding: 0.1rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.1rem;text-align: left;word-wrap: break-word;}
</style>
EOD;

$tbl.=<<<EOD
<div>
<h2 align="center"><u>Interpreter General Report – Translation Interpreting</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
<p>Orgnaization(s) Selected</p>
<table class="aa" border="1" cellspacing="0" cellpadding="0" style="width:250px">
  <tr>
    <td width="200" valign="top">{$search_1}</td>
  </tr>
</table><br/><br/>
EOD;
$tbl.=<<<EOD
<table>
<thead>
<tr>
 	<th style="width:45px;">Sr. No.</th>
    <th>Language</th>
    <th>Company Name</th>
    <th>Number of Unit</th>
    <th>Rate per Unit</th>
    <th>Interpreting Time Paid</th>
    <th>Other Expenses</th>
    <th>Total payment</th>
    <th>Payment Status Paid / Unpaid</th>
  </tr>

</thead>
EOD;

$runcompany="";
$nowcompany="";
$mapCoTotals=array();
ZeroCompTotal($mapCoTotals);

$loop=0;
while($row = mysqli_fetch_assoc($result))
{
	$rpuXnumb=$row["rpU"]*$row["numberUnit"] + $rpuXnumb;
	$otherCharg=$row["otherCharg"] + $otherCharg;
	$total_charges_comp=$row["total_charges_comp"] + $total_charges_comp;
	$rpu_numberUnit=$row["rpU"]*$row["numberUnit"];
	if($row["rAmount"]>0)
	{
		$invstst='Paid';
	}
	else
	{
		$invstst='Un-Paid';
	}

	$nowcompany=$row["orgName"];
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
  

$tbl.=<<<EOD
    <tr>
		<td style="width:45px;">{$i}</td>
		<td>{$row["source"]}</td>
		<td>{$row["orgNam"]}</td>
		<td>{$row["C_rpU"]}</td>
		<td>{$row["C_numberUnit"]}</td>
		<td>{$rpu_numberUnit}</td>
		<td>{$row["C_otherCharg"]}</td>		
		<td>{$row["total_charges_comp"]}</td>	
		<td>{$invstst}</td>
    </tr>
EOD;
 $i++;
}
ShowCompTotal($mapCoTotals,$tbl);


$tbl.=<<<EOD
<tr>

<td></td>
<td></td>
<td></td>
<td></td>
<td>Total</td>
<td>{$misc->numberFormat_fun($rpuXnumb)}</td>
<td>{$misc->numberFormat_fun($otherCharg)}</td>
<td>{$misc->numberFormat_fun($total_charges_comp)}</td>
<td></td>
</tr>
	  
</table>

EOD;
	

$pdf->writeHTML($tbl, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
list($a,$b)=explode('.',basename(__FILE__));
$pdf->Output($a.'.pdf', 'I');
//============================================================+
// END OF FILE
//==========================================================EXCEL FORMAT=========================================================+

function ZeroCompTotal(&$map)
{
	$map["rpuXnumb"]=0;
	$map["otherCharg"]=0;
	$map["total_charges_comp"]=0;
}

function UpdateCompTotal(&$map,&$row)
{
	$rpuXnumb=$row["rpU"]*$row["numberUnit"] + $map["rpuXnumb"];
	$map["rpuXnumb"]=$rpuXnumb;
	$map["otherCharg"]+=$row["otherCharg"];
	$map["total_charges_comp"]+=$row["total_charges_comp"];
}

function ShowCompTotal(&$map,&$tbl)
{
	global $misc;	

	$tbl.=<<<EOD
	<tr>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td>Comp Total</td>
	<td>{$misc->numberFormat_fun($map["rpuXnumb"])}</td>
	<td>{$misc->numberFormat_fun($map["otherCharg"])}</td>		
	<td>{$misc->numberFormat_fun($map["total_charges_comp"])}</td>
	<td></td>
	</tr>				
EOD;
}
