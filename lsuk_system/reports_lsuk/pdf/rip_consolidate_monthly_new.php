 <?php  if(isset($_POST['submit'])){ ?><script>window.print()</script><style>.prnt{  display:none; }</style><?php } ?>
 <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<?php include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$all_abrv=$acttObj->read_specific("GROUP_CONCAT(comp_reg.abrv) as all_abrv","comp_reg","id IN (".$_GET['search_1'].")");
$search_1=$all_abrv['all_abrv'];
$search_2=@$_GET['search_2'];
$x=0;
$arr_langs=array();
error_reporting(0);
$counter=0; 
$arr = explode(',', $search_1);
?>


<center><br><form action="" method="post"><input type="submit" class='btn btn-primary btn-sm prnt' name="submit" value="PRINT THIE REPORT" onclick="printpage()"/></form></center>
<div class="container">
<div class="text-center"><h3>Client Consolidated Report (Detailed Bookings Quarterly)</h3></div><br />
<div class="text-right">Report Date: <?php echo date('d-m-Y'); ?><br>Year: (<?php echo $search_2; ?>)</div>
<p>Companies Selected</p>
<table class="table table-bordered table-hover">
  <tr>
    <td>
    <?php echo $search_1; ?>
     
     </td>
  </tr>
</table>
<?php while($counter<count($arr)){ ?>
<br>
     <h3 class="text-center">Quarterly Report for <?php echo '<span class="label label-primary">'.$arr[$counter].'</span>'; ?></h3>
     <table class="table table-bordered table-hover">
     <tbody>
  <thead class="bg-primary">
    <th>Language</th>
   <?php 
   $array_quaters=array('Quarter 1'=>'1,2,3','Quarter 2'=>'4,5,6','Quarter 3'=>'7,8,9','Quarter 4'=>'10,11,12');
   $array_2nd=array('Total Jobs','Total Cost','Total Jobs','Total Cost','Total Jobs','Total Cost','Total Jobs','Total Cost');
   $i_q=0;
   foreach($array_quaters as $x => $val) {?>
    <th colspan="2"><?php echo $x; ?></th>
    <?php $i_q++;} ?>
  </tr>
  <tr><th> </th>
<?php $i_2nd=0;
   foreach($array_2nd as $second) {?>
    <th><?php echo $second; ?></th>
    <?php $i_2nd++;} ?>
  </thead>
  <?php
	   $result = $acttObj->read_all('distinct(source)','(SELECT distinct (interpreter.source) FROM interpreter,interpreter_reg,comp_reg',"interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv 
AND year(interpreter.assignDate) = '$search_2' and (interpreter.orgName = '$arr[$counter]') and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0
UNION ALL
SELECT distinct (telephone.source) FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND year(telephone.assignDate) = '$search_2' and (telephone.orgName = '$arr[$counter]') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0
UNION ALL
SELECT distinct (translation.source) FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND year(translation.asignDate) = '$search_2' and (translation.orgName = '$arr[$counter]') and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as source ORDER BY source");
while($row = mysqli_fetch_assoc($result)){ $lang=$row['source']; array_push($arr_langs,$lang); ?>
   <tr class="<?php echo 'cls_'.$arr[$counter]; ?>"><td><?php echo $lang; ?></td>
   
 <?php foreach($array_quaters as $quarter){?>
  
   <?php	 $x=$i_q;$u=0; while($x>$u){
	   $result_inner = $acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id 
AND interpreter.orgName = comp_reg.abrv 
AND interpreter.invoiceNo=invoice.invoiceNo 
AND year(interpreter.assignDate) = '$search_2' and month(interpreter.assignDate) IN ($quarter) and (interpreter.orgName = '$arr[$counter]') and interpreter.source='$lang' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0
UNION ALL
SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice
WHERE telephone.intrpName = interpreter_reg.id 
AND telephone.orgName = comp_reg.abrv 
AND telephone.invoiceNo=invoice.invoiceNo 
AND year(telephone.assignDate) = '$search_2' and month(telephone.assignDate) IN ($quarter) and (telephone.orgName = '$arr[$counter]') and telephone.source='$lang' and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0
UNION ALL
SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice
WHERE translation.intrpName = interpreter_reg.id 
AND translation.orgName = comp_reg.abrv 
AND translation.invoiceNo=invoice.invoiceNo 
AND year(translation.asignDate) = '$search_2' and month(translation.asignDate) IN ($quarter) and (translation.orgName = '$arr[$counter]') and translation.source='$lang' and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");
	   while($row_inner = mysqli_fetch_assoc($result_inner)){?>
    
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    
   <?php	 $u++;}
         break;
        }
   }  ?>
	 </tr>
     
<?php $x++;} ?>

 <tr>
 
<td style="background-color: #c5c5c5ad;color: black;"><b>Total</b></td>
 <?php $i_sum=0;$sum_jobs=0;
$sum_cost=0; foreach($array_quaters as $quarter){
 $result_total = $acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id 
AND interpreter.orgName = comp_reg.abrv 
AND interpreter.invoiceNo=invoice.invoiceNo 
AND year(interpreter.assignDate) = '$search_2' and month(interpreter.assignDate) IN ($quarter) and (interpreter.orgName = '$arr[$counter]') and interpreter.source IN ".'("'.implode('","',$arr_langs).'")'." and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0
UNION ALL
SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice
WHERE telephone.intrpName = interpreter_reg.id 
AND telephone.orgName = comp_reg.abrv 
AND telephone.invoiceNo=invoice.invoiceNo 
AND year(telephone.assignDate) = '$search_2' and month(telephone.assignDate) IN ($quarter) and (telephone.orgName = '$arr[$counter]') and telephone.source IN ".'("'.implode('","',$arr_langs).'")'." and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0
UNION ALL
SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice
WHERE translation.intrpName = interpreter_reg.id 
AND translation.orgName = comp_reg.abrv 
AND translation.invoiceNo=invoice.invoiceNo 
AND year(translation.asignDate) = '$search_2' and month(translation.asignDate) IN ($quarter) and (translation.orgName = '$arr[$counter]') and translation.source IN ".'("'.implode('","',$arr_langs).'")'." and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");
$row_total = mysqli_fetch_assoc($result_total); ?>
    <td  style="background-color: #c5c5c5ad;color: black;"><?php echo $row_total['total_jobs'];$sum_jobs+=$row_total["total_jobs"]; ?></td>
    <td  style="background-color: #c5c5c5ad;color: black;"><?php echo $row_total['total_cost'];$sum_cost+=$row_total["total_cost"]; ?></td>
    
   <?php  $i_sum++;} ?>
	 </tr>
	 <tr>
	 <td colspan="5"><h4>TOTAL JOBS : <span class="label label-default"><?php echo $sum_jobs; ?></span></h4></td>
	 <td colspan="6"><h4>TOTAL COST : <span class="label label-default"><?php echo $sum_cost; ?></span></h4></td>
	 </tr>
	 </tbody>
</table>
<?php $counter++; } ?>
</div>