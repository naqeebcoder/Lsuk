<?php  if(isset($_POST['submit'])){ ?><script>window.print()</script><style>.prnt{  display:none; }</style><?php } ?>
<?php include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$type=$_GET['type'];
$orgz=$_GET['search_1'];
if($type=='super'){
   $data1=$acttObj->read_specific("DISTINCT GROUP_CONCAT(parent_companies.sup_child_comp) as data1","parent_companies","parent_companies.sup_parent_comp IN (".$orgz.")");
   $data2=$acttObj->read_specific("DISTINCT GROUP_CONCAT(child_companies.child_comp) as data2","child_companies","child_companies.parent_comp IN (".$data1['data1'].")");
   $all_abrv=$acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv","comp_reg","id IN (".$data2['data2'].")","set SESSION group_concat_max_len=10000");
}else if($type=='parent'){
    $data1=$acttObj->read_specific("GROUP_CONCAT(comp_reg.id) as data1","comp_reg","id IN (".$orgz.")");
    $data2=$acttObj->read_specific("DISTINCT GROUP_CONCAT(child_companies.child_comp) as data2","child_companies","child_companies.parent_comp IN (".$orgz.")");
    $all_abrv=$acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv","comp_reg","id IN (".$data2['data2'].")","set SESSION group_concat_max_len=10000");
}else{
    $all_abrv=$acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv","comp_reg","id IN (".$orgz.")","set SESSION group_concat_max_len=10000");
}
$display_org=$acttObj->read_specific("GROUP_CONCAT(comp_reg.name) as orgName","comp_reg","id IN (".$orgz.")");
$search_1=$all_abrv['all_abrv'];
error_reporting(0);
$arr = explode(',', $search_1);
$org_names="'".implode("','",$arr)."'";
$search_2=@$_GET['search_2'];
$q1_start=$search_2;
$last_date = date('Y-m-d', strtotime("+12 months", strtotime($search_2)));
$q4_finish=date('Y-m-d', strtotime('-1 day', strtotime($last_date)));
$q4_start = date('Y-m-d', strtotime("+9 months", strtotime($search_2)));
$q3_finish=date('Y-m-d', strtotime('-1 day', strtotime($q4_start)));
$q3_start = date('Y-m-d', strtotime("+6 months", strtotime($search_2)));
$q2_finish=date('Y-m-d', strtotime('-1 day', strtotime($q3_start)));
$q2_start = date('Y-m-d', strtotime("+3 months", strtotime($search_2)));
$q1_finish=date('Y-m-d', strtotime('-1 day', strtotime($q2_start)));
$array_quaters=array('Qtr 1 ('.form_date($q1_start).' - '.form_date($q1_finish).')'=>'1,2,3','Qtr 2 ('.form_date($q2_start).' - '.form_date($q2_finish).')'=>'4,5,6','Qtr 3 ('.form_date($q3_start).' - '.form_date($q3_finish).')'=>'7,8,9','Qtr 4 ('.form_date($q4_start).' - '.form_date($q4_finish).')'=>'10,11,12');
function form_date($dt){
    $timestamp = strtotime($dt);
    $new_date = date("d.m.Y", $timestamp);
    return $new_date;  
}
?>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<center><br><form action="" method="post"><input type="submit" class='btn btn-primary btn-sm prnt' name="submit" value="PRINT THIE REPORT" onclick="printpage()"/></form></center>
<div class="container">
<div class="text-center"><h3>Client Consolidated Report (Detailed Bookings Quarterly)</h3></div><br />
<div class="text-right">Report Date: <?php echo date('d-m-Y'); ?><br>Selected Date: (<?php echo $search_2; ?>)</div>
<p>Companies Selected</p>
<table class="table table-bordered table-hover">
  <tr>
    <td><?php echo $display_org['orgName']; ?></td>
  </tr>
</table>
<table class="table table-bordered">
  <thead class="bg-primary">
      <th colspan="3" class="text-center"><b><u>QUARTERLY REPORTS</u></b></th></thead>
</table><br>
<table>
  <tr><td colspan="3"><b>FACE TO FACE</b></td></tr>
</table>
     <table class="table table-bordered table-hover">
     <thead class="bg-info">
    <th>Quarter</th>
    <th>Total number of Interpreting Bookings</th>
    <th>Total cost of Interpreting Bookings</th>
    </thead>
    <tbody>
        <?php $result_total = $acttObj->read_all("concat('Qtr 1 (',DATE_FORMAT('$q1_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q1_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate BETWEEN('$q1_start') AND ('$q1_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as grp
            UNION ALL
            SELECT concat('Qtr 2 (',DATE_FORMAT('$q2_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q2_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost FROM (SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice WHERE interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate BETWEEN('$q2_start') AND ('$q2_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as grp
            UNION ALL
            SELECT concat('Qtr 3 (',DATE_FORMAT('$q3_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q3_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost FROM (SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice WHERE interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate BETWEEN('$q3_start') AND ('$q3_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as grp
            UNION ALL
            SELECT concat('Qtr 4 (',DATE_FORMAT('$q4_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q4_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost FROM (SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice WHERE interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate BETWEEN('$q4_start') AND ('$q4_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as grp");
        while($row_total = mysqli_fetch_assoc($result_total)){ ?>
        <tr>
            <td><?php echo $row_total['quarter'];?></td>
            <td><?php echo $row_total['total_jobs'];?></td>
            <td><?php echo $row_total['total_cost'];?></td>
           </tr>
           <?php } ?>
	 </tbody>
</table>
<br>
<table>
  <tr><td colspan="3"><b>TELEPHONE</b></td></tr>
</table>
     <table class="table table-bordered table-hover">
     <thead class="bg-info">
    <th>Quarter</th>
    <th>Total number of Telephone Bookings</th>
    <th>Total cost of Telephone Bookings</th>
    </thead>
    <tbody>
        <?php $result_total_telep = $acttObj->read_all("concat('Qtr 1 (',DATE_FORMAT('$q1_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q1_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice","telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate BETWEEN('$q1_start') AND ('$q1_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp
            UNION ALL
            SELECT concat('Qtr 2 (',DATE_FORMAT('$q2_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q2_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost FROM (SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice WHERE telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate BETWEEN('$q2_start') AND ('$q2_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp
            UNION ALL
            SELECT concat('Qtr 3 (',DATE_FORMAT('$q3_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q3_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost FROM (SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice WHERE telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate BETWEEN('$q3_start') AND ('$q3_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp
            UNION ALL
            SELECT concat('Qtr 4 (',DATE_FORMAT('$q4_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q4_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost FROM (SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice WHERE telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate BETWEEN('$q4_start') AND ('$q4_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp");
        while($row_total_telep = mysqli_fetch_assoc($result_total_telep)){ ?>
        <tr>
            <td><?php echo $row_total_telep['quarter'];?></td>
            <td><?php echo $row_total_telep['total_jobs'];?></td>
            <td><?php echo $row_total_telep['total_cost'];?></td>
           </tr>
           <?php } ?>
	 </tbody>
</table>
<br>
<table>
  <tr><td colspan="3"><b>TRANSLATION</b></td></tr>
</table>
     <table class="table table-bordered table-hover">
     <thead class="bg-info">
    <th>Quarter</th>
    <th>Total number of Translation Bookings</th>
    <th>Total cost of Translation Bookings</th>
    </thead>
    <tbody>
        <?php $result_total_trans = $acttObj->read_all("concat('Qtr 1 (',DATE_FORMAT('$q1_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q1_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice","translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate BETWEEN('$q1_start') AND ('$q1_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp
            UNION ALL
            SELECT concat('Qtr 2 (',DATE_FORMAT('$q2_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q2_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost FROM (SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice WHERE translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate BETWEEN('$q2_start') AND ('$q2_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp
            UNION ALL
            SELECT concat('Qtr 3 (',DATE_FORMAT('$q3_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q3_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost FROM (SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice WHERE translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate BETWEEN('$q3_start') AND ('$q3_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp
            UNION ALL
            SELECT concat('Qtr 4 (',DATE_FORMAT('$q4_start','%d.%m.%Y'),' - ',DATE_FORMAT('$q4_finish','%d.%m.%Y'),')') as quarter,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost FROM (SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice WHERE translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate BETWEEN('$q4_start') AND ('$q4_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");
        while($row_total_trans = mysqli_fetch_assoc($result_total_trans)){ ?>
        <tr>
            <td><?php echo $row_total_trans['quarter'];?></td>
            <td><?php echo $row_total_trans['total_jobs'];?></td>
            <td><?php echo $row_total_trans['total_cost'];?></td>
           </tr>
           <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered">
  <thead class="bg-primary">
      <th colspan="3" class="text-center"><b><u>BOOKINGS BY LANGUAGE</u></b></th></thead>
</table><br>
<table>
  <tr><td colspan="3"><b>FACE TO FACE INTERPRETING</b></td></tr>
</table>
<table class="table table-bordered table-hover">
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[0]); ?></th>
  </tr>
  <?php /*$result_langs = $acttObj->query_extra('GROUP_CONCAT("'."'".'",source,"'."'".'") as result_langs','(SELECT distinct (interpreter.source) FROM interpreter,interpreter_reg,comp_reg',"interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.assignDate between ('$q1_start') AND ('$q4_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as source","set SESSION group_concat_max_len=10000");
	   $res_langs=$result_langs['result_langs'];*/
	 
	   $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice",
	   "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q1_start') AND ('$q1_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
   <tbody>
  <?php 
	   while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT interpreter.source,count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice",
	   "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q1_start') AND ('$q1_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[1]); ?></th>
  </tr>
  <?php $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice",
	   "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q2_start') AND ('$q2_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT interpreter.source,count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice",
	   "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q2_start') AND ('$q2_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[2]); ?></th>
  </tr>
  <?php $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice",
	   "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q3_start') AND ('$q3_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT interpreter.source,count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice",
	   "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q3_start') AND ('$q3_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[3]); ?></th>
  </tr>
  <?php $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice",
	   "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q4_start') AND ('$q4_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT interpreter.source,count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice",
	   "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q4_start') AND ('$q4_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table>
  <tr><td colspan="3"><b>TELEPHONE</b></td></tr>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[0]); ?></th>
  </tr>
  <?php /*$result_langs = $acttObj->query_extra('GROUP_CONCAT("'."'".'",source,"'."'".'") as result_langs','(SELECT distinct (telephone.source) FROM telephone,interpreter_reg,comp_reg',"telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.assignDate between ('$q1_start') AND ('$q4_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as source","set SESSION group_concat_max_len=10000");
	   $res_langs=$result_langs['result_langs'];*/
	 
	   $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice",
	   "telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q1_start') AND ('$q1_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT telephone.source,count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice",
	   "telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q1_start') AND ('$q1_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[1]); ?></th>
  </tr>
  <?php $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice",
	   "telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q2_start') AND ('$q2_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT telephone.source,count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice",
	   "telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q2_start') AND ('$q2_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[2]); ?></th>
  </tr>
  <?php $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice",
	   "telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q3_start') AND ('$q3_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT telephone.source,count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice",
	   "telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q3_start') AND ('$q3_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[3]); ?></th>
  </tr>
  <?php $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice",
	   "telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q4_start') AND ('$q4_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT telephone.source,count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice",
	   "telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q4_start') AND ('$q4_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table>
  <tr><td colspan="3"><b>TRANSLATION</b></td></tr>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[0]); ?></th>
  </tr>
  <?php /*$result_langs = $acttObj->query_extra('GROUP_CONCAT("'."'".'",source,"'."'".'") as result_langs','(SELECT distinct (translation.source) FROM translation,interpreter_reg,comp_reg',"translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.asignDate between ('$q1_start') AND ('$q4_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as source","set SESSION group_concat_max_len=10000");
	   $res_langs=$result_langs['result_langs'];*/
	 
	   $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice",
	   "translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q1_start') AND ('$q1_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT translation.source,count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice",
	   "translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q1_start') AND ('$q1_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[1]); ?></th>
  </tr>
  <?php $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice",
	   "translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q2_start') AND ('$q2_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT translation.source,count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice",
	   "translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q2_start') AND ('$q2_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[2]); ?></th>
  </tr>
  <?php $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice",
	   "translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q3_start') AND ('$q3_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT translation.source,count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice",
	   "translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q3_start') AND ('$q3_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2"><?php print_r(array_keys($array_quaters)[3]); ?></th>
  </tr>
  <?php $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice",
	   "translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q4_start') AND ('$q4_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT translation.source,count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice",
	   "translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q4_start') AND ('$q4_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table class="table table-bordered">
  <thead style="background-color:#8eca75">
      <th colspan="3" class="text-center"><b><u>FULL YEAR SUMMARY</u></b></th></thead>
</table><br>
<table>
  <tr><td colspan="3"><b>FACE TO FACE INTERPRETING</b></td></tr>
</table>
<table class="table table-bordered table-hover">
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2">Full year</th>
  </tr>
  <?php /*$result_langs = $acttObj->query_extra('GROUP_CONCAT("'."'".'",source,"'."'".'") as result_langs','(SELECT distinct (interpreter.source) FROM interpreter,interpreter_reg,comp_reg',"interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.assignDate between ('$q1_start') AND ('$q4_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as source","set SESSION group_concat_max_len=10000");
	   $res_langs=$result_langs['result_langs'];*/
	 
	   $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice",
	   "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q1_start') AND ('$q4_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
   <tbody>
  <?php 
	   while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT interpreter.source,count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice",
	   "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q1_start') AND ('$q4_finish') and (interpreter.orgName IN ($org_names)) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table>
  <tr><td colspan="3"><b>TELEPHONE</b></td></tr>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2">Full year</th>
  </tr>
  <?php /*$result_langs = $acttObj->query_extra('GROUP_CONCAT("'."'".'",source,"'."'".'") as result_langs','(SELECT distinct (telephone.source) FROM telephone,interpreter_reg,comp_reg',"telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.assignDate between ('$q1_start') AND ('$q4_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as source","set SESSION group_concat_max_len=10000");
	   $res_langs=$result_langs['result_langs'];*/
	 
	   $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice",
	   "telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q1_start') AND ('$q4_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT telephone.source,count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice",
	   "telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q1_start') AND ('$q4_finish') and (telephone.orgName IN ($org_names)) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
<table>
  <tr><td colspan="3"><b>TRANSLATION</b></td></tr>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead>
    <th class="bg-info" width="30%">Language</th>
    <th class="bg-info" colspan="2">Full year</th>
  </tr>
  <?php $result_inner = $acttObj->read_all("source,SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT (CASE WHEN source='English' THEN target ELSE source END) as source,count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice",
	   "translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q1_start') AND ('$q4_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0 GROUP by (CASE WHEN source='English' THEN target ELSE source END)) as grp GROUP by source ORDER BY total_jobs DESC");
	   if(mysqli_num_rows($result_inner)==0){
	       echo "<td align='center' colspan='2'>No records in this quarter !</td>";
	   }else{ ?>
  <tr>
      <th></th>
    <th>Total Jobs</th>
    <th>Total Cost</th>
  </thead>
  <?php while($row_inner = mysqli_fetch_assoc($result_inner)){?>
     <tr>
    <td><?php echo $row_inner["source"]; ?></td>
    <td><?php echo $row_inner["total_jobs"]; ?></td>
    <td><?php echo $row_inner["total_cost"];?></td>
    </tr>
   <?php } ?>
	 </tr>

 <tr>
<td style="background-color: #c5c5c552;color: black;"><b>Total</b></td>
 <?php
 $result_total = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost",
	   "(SELECT translation.source,count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice",
	   "translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q1_start') AND ('$q4_finish') and (translation.orgName IN ($org_names)) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");?>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_jobs'];?></td>
    <td  style="background-color: #c5c5c552;color: black;"><?php echo $result_total['total_cost']; ?></td>
	 </tr>
	 <?php } ?>
	 </tbody>
</table>
</div>