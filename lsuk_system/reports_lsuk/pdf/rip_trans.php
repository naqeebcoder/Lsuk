<?php include '../../db.php';include_once ('../../class.php'); $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;$table='translation';$total_charges_interp=0; 
//...................................................For Multiple Selection...................................\\
 $arr_intrp = explode(',', $search_1);$_words_intrp = implode("' OR interpreter_reg.name like '", $arr_intrp);
//......................................\\//\\//\\//\\//........................................................\\
if($search_1){
$query="SELECT $table.*, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3' 
     and (interpreter_reg.name like '%$_words_intrp%')
     order by name";
     }
else{$query="SELECT $table.*, interpreter_reg.name  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
     where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and asignDate between '$search_2' and '$search_3'
     order by name";
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
<h2 align="center"><u>Interpreter General Report – Translation Interpreting</u></h2>
<p align="right">Report  Date: {$misc->sys_date()}<br />
  Date  Range:  Date From [{$misc->dated($search_2)}] Date To [{$misc->dated($search_3)}]</p>
</div>
<p>Interpreter(s) Selected</p>
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
    <th>Interpreter Name</th> 
    <th>Assignment Date</th>
    <th>Amount Paid to Interpreter</th>
    <th>Payment Date </th>
    <th>Language</th>
    <th>Invoice Number</th>
    <th>Company Name</th> 
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
  $total_charges_interp=$row["total_charges_interp"] + $total_charges_interp;

	$nowcompany=$row["name"];
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
      <td style="width:45px;">{$i}</td>
      <td>{$row['name']}</td>
      <td>{$misc->dated($row['asignDate'])}</td>
      <td>{$row['total_charges_interp']}</td>
      <td>{$misc->dated($row['dueDate'])}</td>	  
      <td>{$row['source']}</td>
      <td>{$row['invoiceNo']}</td>
      <td>{$row['orgName']}</td>
    </tr>
EOD;
 $i++;
}
if ($loop!=0)
  ShowCompTotal($mapCoTotals,$tbl);

$tbl.=<<<EOD
<tr>
      
	  <td style="font-weight:bold;"  colspan="3" align="right">Total</td>
	  <td style="font-weight:bold;">{$misc->numberFormat_fun($total_charges_interp)}</td>
	  <td style="font-weight:bold;"  colspan="4"></td>
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
	$map["total_charges_interp"]=0;
}

function UpdateCompTotal(&$map,&$row,$i)
{
	$map["total_charges_interp"]+=$row["total_charges_interp"];

	$map["i"]=$i;
}

function ShowCompTotal(&$map,&$tbl)
{
	global $misc;	

	$tbl.=<<<EOD
	<tr>
  
  <td style="font-weight:bold;" colspan="4" align="right"> Interp. Tot:</td>
  <td>{$misc->numberFormat_fun($map["total_charges_interp"])}</td>
  <td></td>
  </tr>				
EOD;
}
