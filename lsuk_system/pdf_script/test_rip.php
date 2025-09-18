
<?php
require('pdf_design/New folder/mc_table.php');
require('pdf_design/New folder/html_table.php');

$pdf=new PDF();
$pdf->AddPage();
$pdf->Image('logo.png',18,12,20);
$pdf->SetFont('Arial','',6);

define('EURO', chr(128));



$html='<br><br><br><br><br><br><table  border="1">
<tr>

<td width="30" bgcolor="#D0D0FF">#</td>
<td width="60" bgcolor="#D0D0FF">Job Date</td>
<td width="40" bgcolor="#D0D0FF">Type</td>
<td width="50" bgcolor="#D0D0FF">Language</td>
<td width="100" bgcolor="#D0D0FF">Client Name</td>
<td width="30" bgcolor="#D0D0FF">Units</td>
<td width="60" bgcolor="#D0D0FF">Unit Cost</td>
<td width="30" bgcolor="#D0D0FF">Job Cost</td>
<td width="40" bgcolor="#D0D0FF">Travel Cost</td>
<td width="50" bgcolor="#D0D0FF">Travel Expenses</td>
<td width="30" bgcolor="#D0D0FF">Non vatable</td>
<td width="30" bgcolor="#D0D0FF">Admn Charges</td>
<td width="130" bgcolor="#D0D0FF">Job Notes</td>

</tr>


</table>';

$pdf->WriteHTML($html);

$pdf->Output();
?>