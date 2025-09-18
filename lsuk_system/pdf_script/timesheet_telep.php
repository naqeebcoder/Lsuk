<?php include '../db.php';include_once ('../class.php'); $update_id=@$_GET['update_id'];$table=$_GET['table'];

$query="SELECT $table.*, interpreter_reg.name, comp_reg.name as orgName  FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
	   where $table.id=$update_id";
$result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $source=$row['source'];$assignDate=$row['assignDate'];$assignTime=$row['assignTime'];$contactNo=$row['contactNo'];$orgRef=$row['orgRef'];$orgName=$row['orgName'];$name=$row['name'];$noClient=$row['noClient'];$orgContact=$row['orgContact']; }
require('WriteHTML.php');

$pdf=new PDF_HTML();

$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 15);

$pdf->AddPage();
$pdf->Image('logo.png',18,12,23);
$pdf->SetFont('Arial','B',14);
$pdf->WriteHTML('<para><h1>            Language Services UK Limited<br>         Translation and Interpreting Service</h1><br><u>Suite 2 Davis House Lodge Causeway Trading Estate<br>Lodge Causeway - Fishponds Bristol BS163JB</u></para><br><br><br><br>                                  <u>Telephone Interpreting Timesheet</u>');

$pdf->SetFont('Arial','B',10); 
$pdf->WriteHTML('
<br><br><br><u>Linguist Name: '.$name.'  </u>   <u>Language: '.$source.'      </u>   <u>Case Worker Name: '.$orgContact.'          </u>
<br><br><u>Client Name or Ref: '.$orgRef.' </u>   <u>Date: '.$misc->dated($assignDate).'         </u>   <u>Time: '.$assignTime.'                </u>
<br><br><u>Phone Number if Known: '.$contactNo.' </u>
<br><br><u>Call Start Time:                            </u>   <u>Call Finish Time:                          </u>   <u>Total Call Duration: Hours                   </u>   <u>Minutes:               </u>   


<br><br><br><u>Company Name:  '.$orgName.' </u>
<br><br><br><u>Organisation Representative:                                                 </u>  <u>Date:                                 </u>
<br><br><br><u>Signature:                                                                                                                    </u>
<br><br><br><u>Comments:                                                                                                                                                               </u>
<br><br><br><br><br><u>Signature of the interpreter:                                             </u>  <u>Date:                                 </u>
<br><br><br><br><br><br><br><br><br><h1> Please return this form to Language Services UK Limited on completion of your interpreting assignment</h1>
<br><br><br> Language Services UK Limited 
Suite 2 Davis House Lodge Causeway Trading Estate Lodge Causeway Fishponds Bristol BS164JB'
);
$pdf->WriteHTML2("<br><br><br>");
$pdf->SetFont('Arial','B',6);
$pdf->Output(); 
?>