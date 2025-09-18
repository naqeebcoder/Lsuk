 <?php  if(isset($_POST['submit'])){ ?><script>window.print()</script><style>.prnt{  display:none; }</style><?php } ?>
 <div><form action="" method="post"><input type="submit" class='prnt' name="submit" value="Press to Print" style="background-color:#06F; color:#FFF; border:1px solid #09F"onclick="printpage()"/></form></div>

<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];$search_1=$_GET['search_1'];$search_2=$_GET['search_2'];$counter=0;$x=0; $source_num=0;$table='telephone';$org='';
//...................................................For Multiple Selection...................................\\
$counter=0; $arr = explode(',', $search_1);$_words = implode("' OR orgName = '", $arr);$arr_Month = array();
//......................................\\//\\//\\//\\//........................................................\\
//................................................................................................................?>

<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>

<div style="width:100%; text-align:center"><h3>Client Consolidated Report [Cancellations Monthly]</h3></div><br />
<div style="width:100%; text-align:right">Report Date: <?php echo $misc->sys_date(); ?></div>
<div style="width:100%; text-align:right">Year: [<?php echo $search_2; ?>]</div>
<p>Companies Selected</p>
<table class="aa" border="1" cellspacing="0" cellpadding="0" style="width:250px">
  <tr>
    <td width="200" valign="top">
    <?php if(empty($search_1)){echo "";}else{ echo $search_1;} ?>
     
     </td>
  </tr>
</table><br/>
     
         <table width="100%" border="1">
  <tr>
    <th>Cancelled By</th>
   <?php $query="SELECT distinct(month(assignDate)) as assignDate FROM $table					
	   			where order_cancel_flag=1 and year(assignDate) = '$search_2' and (orgName = '$_words') order by $table.assignDate";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $assignDate=$row['assignDate']; $arr_Month[]=$assignDate; ?>
    <th><?php echo date('F', mktime(0, 0, 0, $assignDate, 10)); ?></th>
    <?php $counter++;} ?>
  </tr>
  <?php  $arr_Month=array_unique($arr_Month);$query="SELECT distinct ($table.order_cancelledby) FROM $table					
	   			where order_cancel_flag=1 and year(assignDate) = '$search_2' and (orgName = '$_words')";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $order_cancelledby=$row['order_cancelledby']; ?>
   <tr><td><?php echo $order_cancelledby; ?></td>
   
 <?php foreach($arr_Month as $month){?>
  
   <?php	 $x=$counter;$u=0; while($x>$u){ 
   $query_inner="SELECT count(order_cancelledby) as order_cancelledby_num FROM $table
  				INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
	   			where order_cancel_flag=1 and year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words') and order_cancelledby='$order_cancelledby'";
	   $result_inner = mysqli_query($con, $query_inner);while($row_inner = mysqli_fetch_assoc($result_inner)){?>
    
    <td><?php echo $row_inner["order_cancelledby_num"]; //$source_num=$row_inner["source_num"] + $source_num; ?></td>
    
   <?php	 $u++;}break;}}  ?>
	 </tr>
     
<?php $x++;} ?>
 
 
 <tr>
 
<td>Total</td>
 <?php  foreach($arr_Month as $month){ ?>
	  
  
   <?php	 
  $query_total_inner="SELECT count(order_cancelledby) as order_cancelledby_num FROM $table
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where order_cancel_flag=1 and year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words')";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){?>
    
    <td><?php echo $row_total_inner["order_cancelledby_num"]; //$source_num=$row_inner["source_num"] + $source_num; ?></td>
    
   <?php	 }} ?>
	 </tr>
     
 
 
 <tr>
 
<td>Total Cost</td>
 <?php foreach($arr_Month as $month){ ?>
	  
 
   <?php	 
  $query_total_inner="SELECT sum($table.total_charges_comp) as total_charges_comp, sum($table.total_charges_comp * $table.cur_vat) as total_charges_comp_vat FROM $table
						INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo  
	   					where order_cancel_flag=1 and year(assignDate) = '$search_2' and month(assignDate)= '$month' and (orgName = '$_words')";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){?>
    
    <td><?php echo round ($row_total_inner["total_charges_comp_vat"] + $row_total_inner["total_charges_comp"], 2); //$source_num=$row_inner["source_num"] + $source_num; ?></td>
    
   <?php	 }} ?>
	 </tr>
     
</table>
