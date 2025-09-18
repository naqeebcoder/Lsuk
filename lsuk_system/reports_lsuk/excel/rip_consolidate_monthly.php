<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=$_GET['search_1'];$search_2=@$_GET['search_2'];
$counter=0;$x=0; 
$countert=0;$xt=0; 
$countertr=0;$xtr=0; 
$source_num=0;
$tot_vat=0;
$tot_non_vat=0;
$tot_cost=0;
$tot_amount=0;
$table='interpreter';$org='';
//...................................................For Multiple Selection...................................\\
$counter=0; $arr = explode(',', $search_1);$_words = implode("' OR orgName = '", $arr);$arr_Month = array();
//......................................\\//\\//\\//\\//........................................................\\
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">Company Wise Consolidate Report(Overall)</h2>
<p>Report Date : ' .$misc->sys_date().'<br/>
Date Range (Year) : ' .$search_2.'</p>
</div>
<p>Organization(s) Selected</p>
<table class="aa" border="1" cellspacing="1" style="width:700px">
  <tr>
    <td valign="top">'.$search_1.'</td>
  </tr>
</table><br/>';

$htmlTable .='<h3>Face to Face Summary</h3>
<table>';
$htmlTable.='<tr>';
$htmlTable.='<th style="background-color: #039;color: #FFF;">Source Language</th>';
 $query="SELECT distinct(month(assignDate)) as assignDate FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv where year(assignDate) = '$search_2' and (orgName = '$_words')   and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.assignDate";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $assignDate=$row['assignDate']; $arr_Month[]=$assignDate;
$htmlTable.='<th style="background-color: #039;color: #FFF;">'.date('F', mktime(0, 0, 0, $assignDate, 10)).'</th>';
$counter++;}
$htmlTable.='</tr>';
$arr_Month=array_unique($arr_Month);$query="SELECT distinct ($table.source) FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv where year(assignDate) = '$search_2' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.source";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $lang=$row['source'];
$htmlTable.='<tr><td>'.$lang.'</td>';
foreach($arr_Month as $month){
  
$x=$counter;$u=0; while($x>$u){ 
   $query_inner="SELECT count(source) as source_num FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
  				INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
	   			where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and source='$lang' and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_inner = mysqli_query($con, $query_inner);while($row_inner = mysqli_fetch_assoc($result_inner)){
    
$htmlTable.='<td>'.$row_inner["source_num"].'</td>';
    
$u++;}break;}}
$htmlTable.='</tr>';
$x++;}
 
 
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Jobs</td>';
 foreach($arr_Month as $month){ 
  $query_total_inner="SELECT count(source) as source_num FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){    
$htmlTable.='<td>'.$row_total_inner["source_num"].'</td>';
}} 
$htmlTable.='</tr>';
 
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total VAT</td>';
 foreach($arr_Month as $month){ 
	  
	 
  $query_total_inner="SELECT  sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td>'.round ($row_total_inner["total_charges_comp_vat"], 2).'</td>';
    
   }} 

     
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Non-VAT</td>';
 foreach($arr_Month as $month){ 
	  
	 
  $query_total_inner="SELECT sum($table.C_otherexpns) as other_expenses FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);
	   while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td>'.round ( $row_total_inner["other_expenses"], 2).'</td>';
    
   }} 

     
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Job Cost</td>';
 foreach($arr_Month as $month){ 
	  
	 
  $query_total_inner="SELECT sum($table.total_charges_comp) as total_charges_comp FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td>'.round ( $row_total_inner["total_charges_comp"], 2).'</td>';
    
   }} 

     
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td style="background-color: #cacaca;color: black;">Total Invoice Cost</td>';
 foreach($arr_Month as $month){ 
	  
	 
  $query_total_inner="SELECT sum($table.total_charges_comp) as total_charges_comp, sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat,sum(interpreter.C_otherexpns) as other_expenses FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
    
$htmlTable.='<td style="background-color: #cacaca;color: black;">'.round ($row_total_inner["total_charges_comp_vat"] + $row_total_inner["total_charges_comp"]+ $row_total_inner["other_expenses"], 2).'</td>';
    
   }} 

     
$htmlTable.='</tr>';
$htmlTable.='</table><br>
<table>';
  $query_total_inner="SELECT round(sum($table.total_charges_comp),2) as total_charges_comp, round(sum($table.total_charges_comp * $table.cur_vat),2) as total_charges_comp_vat,round(sum($table.C_otherexpns),2) as other_expenses, 
  round(sum($table.total_charges_comp) +sum($table.total_charges_comp * $table.cur_vat)+ sum($table.C_otherexpns),2) as total_amount FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv  where year(assignDate) = '$search_2' and (orgName = '$_words') 
  and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);
	   $row_total_inner = mysqli_fetch_assoc($result_total_inner);
	   $tot_vat+=$row_total_inner["total_charges_comp_vat"];
	   $tot_non_vat+=$row_total_inner["other_expenses"];
	   $tot_cost+=$row_total_inner["total_charges_comp"];
	   $tot_amount+=$row_total_inner["total_amount"];
	   $htmlTable.='<tr>
        <td>Full Invoice VAT</td>
        <td>'.$row_total_inner["total_charges_comp_vat"].'</td>
	 </tr>
	 <tr>
        <td>Full Invoice Non-VAT</td>
        <td>'.$row_total_inner["other_expenses"].'</td>
	 </tr>
	 <tr>
        <td>Full Job Cost</td>
        <td>'.$row_total_inner["total_charges_comp"].'</td>
	 </tr>
	 <tr>
        <td>Full Invoice Amount</td>
        <td>'.$row_total_inner["total_amount"].'</td>
	 </tr>
</table>';

$table='telephone';
$htmlTable .='<h3>Telephone Summary</h3>
<table>';
$htmlTable.='<tr>';
$htmlTable.='<th style="background-color: #039;color: #FFF;">Source Language</th>';
 $queryt="SELECT distinct(month(assignDate)) as assignDate FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv where year(assignDate) = '$search_2' and (orgName = '$_words')   and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.assignDate";
	   $resultt = mysqli_query($con, $queryt); $resultt = mysqli_query($con, $queryt);while($rowt = mysqli_fetch_assoc($resultt)){ $assignDatet=$rowt['assignDate']; $arr_Montht[]=$assignDatet;
$htmlTable.='<th style="background-color: #039;color: #FFF;">'.date('F', mktime(0, 0, 0, $assignDatet, 10)).'</th>';
$countert++;}
$htmlTable.='</tr>';
$arr_Montht=array_unique($arr_Montht);$queryt="SELECT distinct ($table.source) FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv where year(assignDate) = '$search_2' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.source";
	   $resultt = mysqli_query($con, $queryt); $resultt = mysqli_query($con, $queryt);while($rowt = mysqli_fetch_assoc($resultt)){ $langt=$rowt['source'];
$htmlTable.='<tr><td>'.$langt.'</td>';
foreach($arr_Montht as $montht){
  
$xt=$countert;$ut=0; while($xt>$ut){ 
   $query_innert="SELECT count(source) as source_num FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
  				INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
	   			where year(assignDate) = '$search_2' and month(assignDate)= '$montht' and (orgName = '$_words') and source='$langt' and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_innert = mysqli_query($con, $query_innert);while($row_innert = mysqli_fetch_assoc($result_innert)){
    
$htmlTable.='<td>'.$row_innert["source_num"].'</td>';
    
$ut++;}break;}}
$htmlTable.='</tr>';
$xt++;}
 
 
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Jobs</td>';
 foreach($arr_Montht as $montht){ 
  $query_total_innert="SELECT count(source) as source_num FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$montht' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){    
$htmlTable.='<td>'.$row_total_innert["source_num"].'</td>';
}} 
$htmlTable.='</tr>';
 
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total VAT</td>';
 foreach($arr_Montht as $montht){ 
	  
	 
  $query_total_innert="SELECT  sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$montht' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){
    
$htmlTable.='<td>'.round ($row_total_innert["total_charges_comp_vat"], 2).'</td>';
    
   }} 

     
$htmlTable.='</tr>';

$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Job Cost</td>';
 foreach($arr_Montht as $montht){ 
	  
	 
  $query_total_innert="SELECT sum($table.total_charges_comp) as total_charges_comp FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$montht' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){
    
$htmlTable.='<td>'.round ( $row_total_innert["total_charges_comp"], 2).'</td>';
    
   }} 

     
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td style="background-color: #cacaca;color: black;">Total Invoice Cost</td>';
 foreach($arr_Montht as $montht){ 
	  
	 
  $query_total_innert="SELECT sum($table.total_charges_comp) as total_charges_comp, sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$montht' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){
    
$htmlTable.='<td style="background-color: #cacaca;color: black;">'.round ($row_total_innert["total_charges_comp_vat"] + $row_total_innert["total_charges_comp"], 2).'</td>';
    
   }} 

     
$htmlTable.='</tr>';
$htmlTable.='</table><br>
<table>';	 
  $query_total_innert="SELECT round(sum($table.total_charges_comp),2) as total_charges_comp, round(sum($table.total_charges_comp * $table.cur_vat),2) as total_charges_comp_vat, 
  round(sum($table.total_charges_comp) +sum($table.total_charges_comp * $table.cur_vat),2) as total_amount FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv  where year(assignDate) = '$search_2' and (orgName = '$_words') 
  and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);
	   $row_total_innert = mysqli_fetch_assoc($result_total_innert);
	   $tot_vat+=$row_total_innert["total_charges_comp_vat"];
	   $tot_cost+=$row_total_innert["total_charges_comp"];
	   $tot_amount+=$row_total_innert["total_amount"];
	   $htmlTable.='<tr>
        <td>Full Invoice VAT</td>
        <td>'.$row_total_innert["total_charges_comp_vat"].'</td>
	 </tr>
	 <tr>
        <td>Full Job Cost</td>
        <td>'.$row_total_innert["total_charges_comp"].'</td>
	 </tr>
	 <tr>
        <td>Full Invoice Amount</td>
        <td>'.$row_total_innert["total_amount"].'</td>
	 </tr>
</table>';

$table='translation';
$htmlTable .='<h3>Translation Summary</h3>
<table>';
$htmlTable.='<tr>';
$htmlTable.='<th style="background-color: #039;color: #FFF;">Source Language</th>';
 $querytr="SELECT distinct(month(asignDate)) as asignDate FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv where year(asignDate) = '$search_2' and (orgName = '$_words')   and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.asignDate";
	   $resulttr = mysqli_query($con, $querytr); $resulttr = mysqli_query($con, $querytr);while($rowtr = mysqli_fetch_assoc($resulttr)){ $assignDatetr=$rowtr['asignDate']; $arr_Monthtr[]=$assignDatetr;
$htmlTable.='<th style="background-color: #039;color: #FFF;">'.date('F', mktime(0, 0, 0, $assignDatetr, 10)).'</th>';
$countertr++;}
$htmlTable.='</tr>';
$arr_Monthtr=array_unique($arr_Monthtr);$querytr="SELECT distinct ($table.source) FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv where year(asignDate) = '$search_2' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.source";
	   $resulttr = mysqli_query($con, $querytr); $resulttr = mysqli_query($con, $querytr);while($rowtr = mysqli_fetch_assoc($resulttr)){ $langtr=$rowtr['source'];
$htmlTable.='<tr><td>'.$langtr.'</td>';
foreach($arr_Monthtr as $monthtr){
  
$xtr=$countertr;$utr=0; while($xtr>$utr){ 
   $query_innertr="SELECT count(source) as source_num FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
  				INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
	   			where year(asignDate) = '$search_2' and month(asignDate)= '$monthtr' and (orgName = '$_words') and source='$langtr' and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_innertr= mysqli_query($con, $query_innertr);while($row_innertr = mysqli_fetch_assoc($result_innertr)){
    
$htmlTable.='<td>'.$row_innertr["source_num"].'</td>';
    
$utr++;}break;}}
$htmlTable.='</tr>';
$xtr++;}
 
 
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Jobs</td>';
 foreach($arr_Monthtr as $monthtr){ 
  $query_total_innertr="SELECT count(source) as source_num FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$monthtr' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){    
$htmlTable.='<td>'.$row_total_innertr["source_num"].'</td>';
}} 
$htmlTable.='</tr>';
 
$htmlTable.='<tr>';
 
$htmlTable.='<td>Total VAT</td>';
 foreach($arr_Monthtr as $monthtr){ 
	  
	 
  $query_total_innertr="SELECT  sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$monthtr' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){
    
$htmlTable.='<td>'.round ($row_total_innertr["total_charges_comp_vat"], 2).'</td>';
    
   }} 

     
$htmlTable.='</tr>';

$htmlTable.='<tr>';
 
$htmlTable.='<td>Total Job Cost</td>';
 foreach($arr_Monthtr as $monthtr){ 
	  
	 
  $query_total_innertr="SELECT sum($table.total_charges_comp) as total_charges_comp FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$monthtr' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){
    
$htmlTable.='<td>'.round ( $row_total_innertr["total_charges_comp"], 2).'</td>';
    
   }} 

     
$htmlTable.='</tr>';
$htmlTable.='<tr>';
 
$htmlTable.='<td style="background-color: #cacaca;color: black;">Total Invoice Cost</td>';
 foreach($arr_Monthtr as $monthtr){ 
	  
	 
  $query_total_innertr="SELECT sum($table.total_charges_comp) as total_charges_comp, sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$monthtr' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){
    
$htmlTable.='<td style="background-color: #cacaca;color: black;">'.round ($row_total_innertr["total_charges_comp_vat"] + $row_total_innertr["total_charges_comp"], 2).'</td>';
    
   }} 

     
$htmlTable.='</tr>';
$htmlTable.='</table><br>
<table>';
  $query_total_innertr="SELECT round(sum($table.total_charges_comp),2) as total_charges_comp, round(sum($table.total_charges_comp * $table.cur_vat),2) as total_charges_comp_vat, 
  round(sum($table.total_charges_comp) +sum($table.total_charges_comp * $table.cur_vat),2) as total_amount FROM $table
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv  where year(asignDate) = '$search_2' and (orgName = '$_words') 
  and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);
	   $row_total_innertr = mysqli_fetch_assoc($result_total_innertr);
	   $tot_vat+=$row_total_innertr["total_charges_comp_vat"];
	   $tot_cost+=$row_total_innertr["total_charges_comp"];
	   $tot_amount+=$row_total_innertr["total_amount"];
	   $htmlTable.='<tr>
        <td>Full Invoice VAT</td>
        <td>'.$row_total_innertr["total_charges_comp_vat"].'</td>
	 </tr>
	 <tr>
        <td>Full Job Cost</td>
        <td>'.$row_total_innertr["total_charges_comp"].'</td>
	 </tr>
	 <tr>
        <td>Full Invoice Amount</td>
        <td>'.$row_total_innertr["total_amount"].'</td>
	 </tr>
</table><br>';
$htmlTable.='<h3>Overall Summary</h3>
     
<table>
   <tr>
        <td>Full Invoice VAT</td>
        <td>'.$tot_vat.'</td>
	 </tr>
	 <tr>
        <td>Full Invoice non-VAT</td>
        <td>'.$tot_non_vat.'</td>
	 </tr>
	 <tr>
        <td>Full Job Cost</td>
        <td>'.$tot_cost.'</td>
	 </tr>
	 <tr>
        <td>Full Invoice Amount</td>
        <td>'.$tot_amount.'</td>
	 </tr>
</table>';
list($a,$b)=explode('.',basename(__FILE__));
//$new_name=$a.'_'.implode('_',$arr);
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls"); 
echo $htmlTable;
?>