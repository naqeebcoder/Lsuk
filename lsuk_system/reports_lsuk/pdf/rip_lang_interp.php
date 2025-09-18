<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel']; $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='interpreter';
//...................................................For Multiple Selection...................................\\
 $arr_source = explode(',', $search_1);$_words_source = implode("' OR source like '", $arr_source);
//......................................\\//\\//\\//\\//........................................................\\

if(!empty($search_1)){
$query="SELECT *, total_charges_comp * 0.2 as vat, interpreter_reg.name,interpreter_reg.city FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3' 
	   and ($table.source like '%$_words_source%')
	   order by source";
	   }
	   else{$query="SELECT *, total_charges_comp * 0.2 as vat, interpreter_reg.name,interpreter_reg.city  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and assignDate between '$search_2' and '$search_3'
	   order by source";
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
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
EOD;

$tbl.=<<<EOD
<div>
<h2 align="center"><u>Language Report from "Face to Face" Interpreter</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
<p>Language(s) Selected</p>
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
 	<th style="width:35px;">Sr.No</th>
	<th>Job Date </th>
    <th>Job Location</th>
    <th>Language</th>
    <th>Client Name</th>
    <th>Interpreter Name</th>
    <th>Interpreter City</th>
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
	$nowcompany=$row["source"];
	if ($loop==0)
	OrgOutput::WriteTR($nowcompany,$runcompany,$tbl);
  
  $loop++;
  if ($runcompany!=$nowcompany)
  {
	ShowCompTotal($mapCoTotals,$tbl);
	ZeroCompTotal($mapCoTotals);
  }
  OrgOutput::WriteTR($nowcompany,$runcompany,$tbl);
  
  UpdateCompTotal($mapCoTotals,$row,$i);
  

	$tbl.=<<<EOD
    <tr>
      	<td style="width:35px;">{$i}</td>
      	<td>{$misc->dated($row['assignDate'])}</td>
		<td>{$row["assignCity"]}</td>
		<td>{$row["source"]}</td>
		<td>{$row["orgName"]}</td>
		<td>{$row["name"]}</td>
		<td>{$row["city"]}</td>
    </tr>
EOD;
 $i++;
}
if ($loop!=0)
  ShowCompTotal($mapCoTotals,$tbl);

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

function ZeroCompTotal(&$map)
{
	$map["i"]=0;
}

function UpdateCompTotal(&$map,&$row,$i)
{
	$map["i"]++;
}

function ShowCompTotal(&$map,&$tbl)
{
	global $misc;	

	$tbl.=<<<EOD
	<tr>
  
  <td style="font-weight:bold;" colspan="4" align="right"> Interp. Tot:</td>
  <td>{$map["i"]}</td>
  <td></td>
  </tr>				
EOD;
}
