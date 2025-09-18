 <?php  if(isset($_POST['submit'])){ ?><script>window.print()</script><style>.prnt{  display:none; }</style><?php } ?>
 <div><form action="" method="post"><input type="submit" class='prnt' name="submit" value="Press to Print" style="background-color:#06F; color:#FFF; border:1px solid #09F"onclick="printpage()"/></form></div>

<?php include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$search_1=$_GET['search_1'];
$search_2=@$_GET['search_2'];
$counter=0;$x=0; 
$countert=0;$xt=0; 
$countertr=0;$xtr=0; 
$source_num=0;
$tot_jobs=0;
$tot_jobsf=0;
$tot_jobst=0;
$tot_jobstr=0;
$table='interpreter';
$org='';
error_reporting(0);

//...................................................For Multiple Selection...................................\\
$counter=0; $arr = explode(',', $search_1);$_words = implode("' OR orgName = '", $arr);$arr_Month = array();
//......................................\\//\\//\\//\\//........................................................\\
//................................................................................................................?>

<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>

<div style="width:100%; text-align:center"><h3>Client Consolidated Report [Detailed Bookings Monthly]</h3></div><br />
<div style="width:100%; text-align:right">Report Date: <?php echo $misc->sys_date(); ?></div>
<div style="width:100%; text-align:right">Year: [<?php echo $search_2; ?>]</div>
<p>Companies Selected</p>
<table class="aa" border="1" cellspacing="1" style="width:700px"">
  <tr>
    <td valign="top">
    <?php if(empty($search_1)){echo "";}else{ echo $search_1;} ?>
     
     </td>
  </tr>
</table><br/>

     <h3>Face to Face Summary</h3>
     <table width="100%" border="1">
  <tr>
    <th>Source Language</th>
   <?php $query="SELECT distinct(month(assignDate)) as assignDate FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv where year(assignDate) = '$search_2' and (orgName = '$_words')   and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.assignDate";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $assignDate=$row['assignDate']; $arr_Month[]=$assignDate; ?>
    <th><?php echo date('F', mktime(0, 0, 0, $assignDate, 10)); ?></th>
    <?php $counter++;} ?>
  </tr>
  <?php  $arr_Month=array_unique($arr_Month);$query="SELECT distinct ($table.source) FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 					
	   			where year(assignDate) = '$search_2' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.source";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $lang=$row['source']; ?>
   <tr><td><?php echo $lang; ?></td>
   
 <?php foreach($arr_Month as $month){?>
  
   <?php	 $x=$counter;$u=0; while($x>$u){ 
   $query_inner="SELECT count(source) as source_num FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
  				INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
	   			where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and source='$lang' and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_inner = mysqli_query($con, $query_inner);while($row_inner = mysqli_fetch_assoc($result_inner)){?>
    
    <td><?php echo $row_inner["source_num"]; //$source_num=$row_inner["source_num"] + $source_num; ?></td>
    
   <?php	 $u++;}break;}}  ?>
	 </tr>
     
<?php $x++;} ?>
 
 
 <tr>
 
<td>Total Jobs</td>
 <?php  foreach($arr_Month as $month){ ?>
	  
  
   <?php	 
  $query_total_inner="SELECT count($table.source) as source_num FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){?>
    
    <td><?php $tot_jobsf+=$row_total_inner["source_num"];
    echo $row_total_inner["source_num"]; ?></td>
    
   <?php	 }} ?>
	 </tr>
     <tr>
 
<td>Total VAT</td>
 <?php foreach($arr_Month as $month){ ?>
	  
 
   <?php	 
  $query_total_inner="SELECT sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){?>
    
    <td><?php echo round ($row_total_inner["total_charges_comp_vat"] , 2); ?></td>
    
   <?php	 }} ?>
	 </tr>
	 <tr>
 
<td>Total Non-VAT</td>
 <?php foreach($arr_Month as $month){ ?>
	  
 
   <?php
  $query_total_inner="SELECT sum($table.C_otherexpns) as other_expenses FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){?>
    
    <td><?php echo round ($row_total_inner["other_expenses"] , 2); ?></td>
    
   <?php	 }} ?>
	 </tr>
 <tr>
 
<td>Total Job Cost</td>
 <?php foreach($arr_Month as $month){ ?>
	  
 
   <?php	 
  $query_total_inner="SELECT sum($table.total_charges_comp) as total_charges_comp FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){?>
    
    <td><?php echo round ($row_total_inner["total_charges_comp"], 2); ?></td>
    
   <?php	 }} ?>
	 </tr>
 
 <tr>
 
<td style="background-color: #c5c5c5ad;color: black;">Total Invoice Cost</td>
 <?php foreach($arr_Month as $month){ ?>
	  
 
   <?php	 
  $query_total_inner="SELECT sum($table.total_charges_comp) as total_charges_comp, sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat,sum($table.C_otherexpns) as other_expenses FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){?>
    
    <td style="background-color: #c5c5c5ad;color: black;"><?php echo round ($row_total_inner["total_charges_comp_vat"] + $row_total_inner["total_charges_comp"]+ $row_total_inner["other_expenses"], 2); ?></td>
    
   <?php	 }} ?>
	 </tr>
     
</table><br>
<table>
   <?php	 
  $query_total_inner="SELECT round(sum($table.total_charges_comp),2) as total_charges_comp, round(sum($table.total_charges_comp * $table.cur_vat),2) as total_charges_comp_vat,round(sum($table.C_otherexpns),2) as other_expenses, 
  round(sum($table.total_charges_comp) +sum($table.total_charges_comp * $table.cur_vat)+ sum($table.C_otherexpns),2) as total_amount FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv  where year(assignDate) = '$search_2' and (orgName = '$_words') 
  and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_inner = mysqli_query($con, $query_total_inner);
	   $row_total_inner = mysqli_fetch_assoc($result_total_inner); ?>
	   <tr>
        <td>Full Invoice VAT</td>
        <td><?php echo $row_total_inner["total_charges_comp_vat"]; ?></td>
	 </tr>
	 <tr>
        <td>Full Invoice Non-VAT</td>
        <td><?php echo $row_total_inner["other_expenses"]; ?></td>
	 </tr>
	 <tr>
        <td>Full Job Cost</td>
        <td><?php echo $row_total_inner["total_charges_comp"]; ?></td>
	 </tr>
	 <tr>
        <td>Full Invoice Amount</td>
        <td><?php echo $row_total_inner["total_amount"]; ?></td>
	 </tr>
</table>
<!------------------------------------------------------------------------------>
 <h3>Telephone Summary</h3>
     <table width="100%" border="1">
  <tr>
    <th>Source Language</th>
   <?php $table='telephone';
   $queryt="SELECT distinct(month(assignDate)) as assignDate FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 					
	   			where year(assignDate) = '$search_2' and (orgName = '$_words')   and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.assignDate";
	   $resultt = mysqli_query($con, $queryt); $resultt = mysqli_query($con, $queryt);
	   while($rowt = mysqli_fetch_assoc($resultt)){ $assignDatet=$rowt['assignDate']; $arr_Montht[]=$assignDatet; ?>
    <th><?php echo date('F', mktime(0, 0, 0, $assignDatet, 10)); ?></th>
    <?php $countert++;} ?>
  </tr>
  <?php  $arr_Montht=array_unique($arr_Montht);
  $queryt="SELECT distinct ($table.source) FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 					
	   			where year(assignDate) = '$search_2' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.source";
	   $resultt = mysqli_query($con, $queryt); $resultt = mysqli_query($con, $queryt);while($rowt = mysqli_fetch_assoc($resultt)){ $langt=$rowt['source']; ?>
   <tr><td><?php echo $langt; ?></td>
   
 <?php foreach($arr_Montht as $montht){?>
  
   <?php	 $xt=$countert;$ut=0; while($xt>$ut){ 
   $query_innert="SELECT count(source) as source_num FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
  				INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
	   			where year(assignDate) = '$search_2' and month(assignDate)= '$montht' and (orgName = '$_words') and source='$langt' and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_innert = mysqli_query($con, $query_innert);while($row_innert = mysqli_fetch_assoc($result_innert)){?>
    
    <td><?php echo $row_innert["source_num"]; //$source_num=$row_inner["source_num"] + $source_num; ?></td>
    
   <?php	 $ut++;}break;}}  ?>
	 </tr>
     
<?php $xt++;} ?>
 
 
 <tr>
 
<td>Total Jobs</td>
 <?php  foreach($arr_Montht as $montht){ ?>
	  
  
   <?php	 
  $query_total_innert="SELECT count(source) as source_num FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$montht' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){?>
    
    <td><?php $tot_jobst+=$row_total_innert["source_num"];
    echo $row_total_innert["source_num"];  ?></td>
    
   <?php	 }} ?>
	 </tr>
     <tr>
 
<td>Total VAT</td>
 <?php foreach($arr_Montht as $montht){ ?>
	  
 
   <?php	 
  $query_total_innert="SELECT sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$montht' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){?>
    
    <td><?php echo round ($row_total_innert["total_charges_comp_vat"] , 2); ?></td>
    
   <?php	 }} ?>
	 </tr>
 <tr>
 
<td>Total Job Cost</td>
 <?php foreach($arr_Montht as $montht){ ?>
	  
 
   <?php	 
  $query_total_innert="SELECT sum($table.total_charges_comp) as total_charges_comp FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$montht' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innert= mysqli_query($con, $query_total_innert);while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){?>
    
    <td><?php echo round ($row_total_innert["total_charges_comp"], 2); ?></td>
    
   <?php	 }} ?>
	 </tr>
 
 <tr>
 
<td style="background-color: #c5c5c5ad;color: black;">Total Invoice Cost</td>
 <?php foreach($arr_Montht as $montht){ ?>
	  
 
   <?php	 
  $query_total_innert="SELECT sum($table.total_charges_comp) as total_charges_comp, sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(assignDate) = '$search_2' and month(assignDate)= '$montht' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){?>
    
    <td style="background-color: #c5c5c5ad;color: black;"><?php echo round ($row_total_innert["total_charges_comp_vat"] + $row_total_innert["total_charges_comp"], 2); ?></td>
    
   <?php	 }} ?>
	 </tr>
     
</table><br>
<table>
   <?php	 
  $query_total_innert="SELECT round(sum($table.total_charges_comp),2) as total_charges_comp, round(sum($table.total_charges_comp * $table.cur_vat),2) as total_charges_comp_vat, 
  round(sum($table.total_charges_comp) +sum($table.total_charges_comp * $table.cur_vat),2) as total_amount FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv  where year(assignDate) = '$search_2' and (orgName = '$_words') 
  and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);
	   $row_total_innert = mysqli_fetch_assoc($result_total_innert); ?>
	   <tr>
        <td>Full Invoice VAT</td>
        <td><?php echo $row_total_innert["total_charges_comp_vat"]; ?></td>
	 </tr>
	 <tr>
        <td>Full Job Cost</td>
        <td><?php echo $row_total_innert["total_charges_comp"]; ?></td>
	 </tr>
	 <tr>
        <td>Full Invoice Amount</td>
        <td><?php echo $row_total_innert["total_amount"]; ?></td>
	 </tr>
</table>

<!------------------------------------------------------------------------------>
 <h3>Translation Summary</h3>
     <table width="100%" border="1">
  <tr>
    <th>Source Language</th>
   <?php $table='translation';
   $querytr="SELECT distinct(month(asignDate)) as asignDate FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 					
	   			where year(asignDate) = '$search_2' and (orgName = '$_words')   and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.asignDate";
	   $resulttr = mysqli_query($con, $querytr); $resulttr = mysqli_query($con, $querytr);
	   while($rowtr = mysqli_fetch_assoc($resulttr)){ $assignDatetr=$rowtr['asignDate']; $arr_Monthtr[]=$assignDatetr; ?>
    <th><?php echo date('F', mktime(0, 0, 0, $assignDatetr, 10)); ?></th>
    <?php $countertr++;} ?>
  </tr>
  <?php  $arr_Monthtr=array_unique($arr_Monthtr);
  $querytr="SELECT distinct ($table.source) FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 					
	   			where year(asignDate) = '$search_2' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0 order by $table.source";
	   $resulttr = mysqli_query($con, $querytr); $resulttr = mysqli_query($con, $querytr);
	   while($rowtr = mysqli_fetch_assoc($resulttr)){ $langtr=$rowtr['source']; ?>
   <tr><td><?php echo $langtr; ?></td>
   
 <?php foreach($arr_Monthtr as $monthtr){?>
  
   <?php	 $xtr=$countertr;$utr=0; while($xtr>$utr){ 
   $query_innertr="SELECT count(source) as source_num FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
  				INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
	   			where year(asignDate) = '$search_2' and month(asignDate)= '$monthtr' and (orgName = '$_words') and source='$langtr' and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_innertr = mysqli_query($con, $query_innertr);while($row_innertr = mysqli_fetch_assoc($result_innertr)){?>
    
    <td><?php echo $row_innertr["source_num"]; //$source_num=$row_inner["source_num"] + $source_num; ?></td>
    
   <?php	 $utr++;}break;}}  ?>
	 </tr>
     
<?php $xtr++;} ?>
 
 
 <tr>
 
<td>Total Jobs</td>
 <?php  foreach($arr_Monthtr as $monthtr){ ?>
	  
  
   <?php	 
  $query_total_innertr="SELECT count(source) as source_num FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$monthtr' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);
	   while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){?>
    
    <td><?php $tot_jobstr+=$row_total_innertr["source_num"];
    echo $row_total_innertr["source_num"];  ?></td>
    
   <?php	 }} ?>
	 </tr>
     <tr>
 
<td>Total VAT</td>
 <?php foreach($arr_Monthtr as $monthtr){ ?>
	  
 
   <?php	 
  $query_total_innertr="SELECT sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$monthtr' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){?>
    
    <td><?php echo round ($row_total_innertr["total_charges_comp_vat"] , 2); ?></td>
    
   <?php	 }} ?>
	 </tr>
 <tr>
 
<td>Total Job Cost</td>
 <?php foreach($arr_Monthtr as $monthtr){ ?>
	  
 
   <?php	 
  $query_total_innertr="SELECT sum($table.total_charges_comp) as total_charges_comp FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$monthtr' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){?>
    
    <td><?php echo round ($row_total_innertr["total_charges_comp"], 2); ?></td>
    
   <?php	 }} ?>
	 </tr>
 
 <tr>
 
<td style="background-color: #c5c5c5ad;color: black;">Total Invoice Cost</td>
 <?php foreach($arr_Monthtr as $monthtr){ ?>
	  
 
   <?php	 
  $query_total_innertr="SELECT sum($table.total_charges_comp) as total_charges_comp, sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv 
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where year(asignDate) = '$search_2' and month(asignDate)= '$monthtr' and (orgName = '$_words') and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){?>
    
    <td style="background-color: #c5c5c5ad;color: black;"><?php echo round ($row_total_innertr["total_charges_comp_vat"] + $row_total_innertr["total_charges_comp"], 2); ?></td>
    
   <?php	 }} ?>
	 </tr>
     
</table><br>
<table>
   <?php	 
  $query_total_innertr="SELECT round(sum($table.total_charges_comp),2) as total_charges_comp, round(sum($table.total_charges_comp * $table.cur_vat),2) as total_charges_comp_vat, 
  round(sum($table.total_charges_comp) +sum($table.total_charges_comp * $table.cur_vat),2) as total_amount FROM $table 
	   inner join interpreter_reg on $table.intrpName = interpreter_reg.id 
	   inner join comp_reg on $table.orgName = comp_reg.abrv  where year(asignDate) = '$search_2' and (orgName = '$_words') 
  and $table.deleted_flag = 0 and $table.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);
	   $row_total_innertr = mysqli_fetch_assoc($result_total_innertr); ?>
	   <tr>
        <td>Full Invoice VAT</td>
        <td><?php echo $row_total_innertr["total_charges_comp_vat"]; ?></td>
	 </tr>
	 <tr>
        <td>Full Job Cost</td>
        <td><?php echo $row_total_innertr["total_charges_comp"]; ?></td>
	 </tr>
	 <tr>
        <td>Full Invoice Amount</td>
        <td><?php echo $row_total_innertr["total_amount"]; ?></td>
	 </tr>
</table>

<!------------------------------------------------------------------------------>
 <h3>Overall Summary</h3>
     
<table>
   <tr>
        <td>Total Jobs</td>
        <td><?php echo $tot_jobs=$tot_jobs+$tot_jobsf+$tot_jobst+$tot_jobstr; ?></td>
	 </tr>
   <tr>
        <td>Full Invoice VAT</td>
        <td><?php echo $row_total_inner["total_charges_comp_vat"]+$row_total_innert["total_charges_comp_vat"]+$row_total_innertr["total_charges_comp_vat"]; ?></td>
	 </tr>
	 <tr>
        <td>Full Invoice Non-VAT</td>
        <td><?php echo $row_total_inner["other_expenses"]; ?></td>
	 </tr>
	 <tr>
        <td>Full Job Cost</td>
        <td><?php echo $row_total_inner["total_charges_comp"]+$row_total_innert["total_charges_comp"]+$row_total_innertr["total_charges_comp"]; ?></td>
	 </tr>
	 <tr>
        <td>Full Invoice Amount</td>
        <td><?php echo $row_total_inner["total_amount"]+$row_total_innert["total_amount"]+$row_total_innertr["total_amount"]; ?></td>
	 </tr>
</table>