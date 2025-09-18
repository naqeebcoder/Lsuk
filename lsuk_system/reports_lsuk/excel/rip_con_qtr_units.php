<?php include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$type=$_GET['type'];$semi="\"'\"";
$orgz=$_GET['search_1'];
if($type=='super'){
   $data1=$acttObj->read_specific("DISTINCT GROUP_CONCAT(parent_companies.sup_child_comp) as data1","parent_companies","parent_companies.sup_parent_comp IN (".$orgz.")");
   $all_abrv=$acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv","comp_reg","id IN (".$data1['data1'].")","set SESSION group_concat_max_len=10000");
   $all_cz=$acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz","child_companies","child_companies.parent_comp IN (".$data1['data1'].")","set SESSION group_concat_max_len=10000");
}else if($type=='parent'){
    $data1=$acttObj->read_specific("GROUP_CONCAT(comp_reg.id) as data1","comp_reg","id IN (".$orgz.")");
    $all_abrv=$acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv","comp_reg","id IN (".$data1['data1'].")","set SESSION group_concat_max_len=10000");
   $all_cz=$acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz","child_companies","child_companies.parent_comp IN (".$data1['data1'].")","set SESSION group_concat_max_len=10000");
}else{
    $all_abrv=$acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv","comp_reg","id IN (".$orgz.")","set SESSION group_concat_max_len=10000");
    $all_cz=$acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,comp_reg.abrv,$semi) as all_cz","comp_reg","comp_reg.id IN ($orgz)","set SESSION group_concat_max_len=10000");
}
$display_org=$acttObj->read_specific("GROUP_CONCAT(comp_reg.name) as orgName","comp_reg","id IN (".$orgz.")");
$search_1=$all_abrv['all_abrv'];
$x=0;
$arr_langs=array();
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
$array_quaters=array('Qtr 1 ('.form_date($q1_start).' - '.form_date($q1_finish).')'=>'1,2,3','Qtr 2 ('.form_date($q2_start).' - '.form_date($q2_finish).')'=>'4,5,6','Qtr 3 ('.form_date($q3_start).' - '.form_date($q3_finish).')'=>'7,8,9','Qtr 4 ('.form_date($q4_start).' - '.form_date($q4_finish).')'=>'10,11,12','Full Year ('.form_date($q1_start).' - '.form_date($q4_finish).')'=>'13,14,15');
$array_qtr=array("between ('$q1_start') AND ('$q1_finish')"=>'1,2,3',"between ('$q2_start') AND ('$q2_finish')"=>'4,5,6',"between ('$q3_start') AND ('$q3_finish')"=>'7,8,9',"between ('$q4_start') AND ('$q4_finish')"=>'10,11,12',"between ('$q1_start') AND ('$q4_finish')"=>'13,14,15');
$array_2nd=array("Total Jobs","Total Cost","Total Jobs","Total Cost","Total Jobs","Total Cost","Total Jobs","Total Cost","Total Jobs","Total Cost");

//For next year
$search_2_next=date('Y-m-d', strtotime('+ 1 year', strtotime($search_2)));
$q1_start_next=$search_2_next;
$last_date_next = date('Y-m-d', strtotime("+12 months", strtotime($search_2_next)));
$q4_finish_next=date('Y-m-d', strtotime('-1 day', strtotime($last_date_next)));
$q4_start_next = date('Y-m-d', strtotime("+9 months", strtotime($search_2_next)));
$q3_finish_next=date('Y-m-d', strtotime('-1 day', strtotime($q4_start_next)));
$q3_start_next = date('Y-m-d', strtotime("+6 months", strtotime($search_2_next)));
$q2_finish_next=date('Y-m-d', strtotime('-1 day', strtotime($q3_start_next)));
$q2_start_next = date('Y-m-d', strtotime("+3 months", strtotime($search_2_next)));
$q1_finish_next=date('Y-m-d', strtotime('-1 day', strtotime($q2_start_next)));
$array_quaters_next=array('Qtr 1 ('.form_date($q1_start_next).' - '.form_date($q1_finish_next).')'=>'1,2,3','Qtr 2 ('.form_date($q2_start_next).' - '.form_date($q2_finish_next).')'=>'4,5,6','Qtr 3 ('.form_date($q3_start_next).' - '.form_date($q3_finish_next).')'=>'7,8,9','Qtr 4 ('.form_date($q4_start_next).' - '.form_date($q4_finish_next).')'=>'10,11,12','Full Year ('.form_date($q1_start_next).' - '.form_date($q4_finish_next).')'=>'13,14,15');
$array_qtr_next=array("between ('$q1_start_next') AND ('$q1_finish_next')"=>'1,2,3',"between ('$q2_start_next') AND ('$q2_finish_next')"=>'4,5,6',"between ('$q3_start_next') AND ('$q3_finish_next')"=>'7,8,9',"between ('$q4_start_next') AND ('$q4_finish_next')"=>'10,11,12',"between ('$q1_start_next') AND ('$q4_finish_next')"=>'13,14,15');

function form_date($dt){
    $timestamp = strtotime($dt);
    $new_date = date("d.m.Y", $timestamp);
    return $new_date;  
}
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
.bg-primary{background-color: #337ab7;}
.bg-info{background-color: #d9edf7;}
</style>
<div>
<div style="text-align:center;"><h3>Client Consolidated Report (Detailed Bookings Quarterly)</h3></div>
<div><b>Report Date: </b>'.date('d-m-Y').'</div>
<div><b>Selected Date: </b>'.$search_2.'</div><br>
<table>
  <tr>
    <td><b>Companies Selected: </b></td><td colspan="10"> '.$display_org['orgName'].' </td>
  </tr>
</table><br>
<table>
  <thead>
      <th class="bg-primary" colspan="11" style="text-align:center;color:white;"><b><u>BOOKINGS BY DELIVERY UNITS</u></b></th></thead>
</table><br>
<table>
  <tr><td colspan="11" align="center"><b>FACE TO FACE</b></td></tr>
</table>
<table>
     <tbody>
  <thead>
    <th class="bg-primary">Unit Name</th>';
     
   $i_q=0;$count_comp=0; 
   foreach($array_quaters as $x => $val) { 
    $htmlTable.='<th colspan="2" class="bg-primary">'.  $x .'</th>';
      $i_q++;}  
  $htmlTable.='</thead></table>
  <table>
  <thead>
    <th class="bg-info"></th>';
     
   $i_q2nd=0; 
   foreach($array_2nd as $x2nd) { 
    $htmlTable.='<th class="bg-info">  '.$x2nd .'</th>';
      $i_q2nd++;}  
  $htmlTable.='</thead>';
    $arr_comps=array(); while($x<count($arr)){
  $arr[$count_comp]; 
  array_push($arr_comps,$arr[$count_comp]);
    while($count_comp<count($arr)){  
   $htmlTable.='<tr><td width="30%">';
   $name_of_comp=$acttObj->read_specific("name","comp_reg","abrv = '".$arr[$count_comp]."'");
   $htmlTable.=$name_of_comp['name'].'</td>';
   
   if($type!='single'){
     $child_orgz=$acttObj->read_specific("GROUP_CONCAT($semi,comp_reg.abrv,$semi) as child_orgz","parent_companies,child_companies,comp_reg","parent_companies.sup_child_comp=child_companies.parent_comp AND child_companies.child_comp=comp_reg.id and child_companies.parent_comp =(SELECT id from comp_reg WHERE abrv='".$arr[$count_comp]."')");
 }
 
 foreach($array_qtr as $qtr=>$val){
   $x=$i_q;$u=0; while($x>$u){
     $result_inner =$type!='single'?$acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id 
AND interpreter.orgName = comp_reg.abrv 
AND interpreter.invoiceNo=invoice.invoiceNo 
AND interpreter.assignDate $qtr and (interpreter.orgName IN (".$child_orgz['child_orgz'].")) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526)) as grp"):$acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id 
AND interpreter.orgName = comp_reg.abrv 
AND interpreter.invoiceNo=invoice.invoiceNo 
AND interpreter.assignDate $qtr and (interpreter.orgName='$arr[$count_comp]') and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526)) as grp");
     while($row_inner = mysqli_fetch_assoc($result_inner)){ 
    
    $htmlTable.='<td>  '.$row_inner["total_jobs"] .'</td>
    <td>  '.$row_inner["total_cost"] .'</td>';
    
       $u++;}
         break;
        }
   }   
   $htmlTable.='</tr>';
     
  $count_comp++;}  
  $x++;}
  $htmlTable.='<tr class="bg-info">
    <th>Total</th>';
    foreach($array_qtr as $qtr=>$val){
    $res_int =$type!='single'?$acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id 
AND interpreter.orgName = comp_reg.abrv 
AND interpreter.invoiceNo=invoice.invoiceNo 
AND interpreter.assignDate $qtr and (interpreter.orgName IN (".$all_cz['all_cz'].")) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526)) as grp"):$acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id 
AND interpreter.orgName = comp_reg.abrv 
AND interpreter.invoiceNo=invoice.invoiceNo 
AND interpreter.assignDate $qtr and (interpreter.orgName IN (".$all_cz['all_cz'].")) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526)) as grp");
    $htmlTable.='<td><b>'.$res_int['total_jobs'].'</b></td>
    <td><b>'.$res_int['total_cost'].'</b></td>';
    }
$htmlTable.='</tr>
</tbody>
</table><br>
<table>
  <tr><td colspan="11" align="center"><b>TELEPHONE</b></td></tr>
</table>
<table>
     <tbody>
  <thead>
    <th class="bg-primary">Unit Name</th>';
     
   $i_q_telep=0;$count_comp=0; 
   foreach($array_quaters as $x => $val) { 
    $htmlTable.='<th colspan="2" class="bg-primary">  '.$x .'</th>';
      $i_q_telep++;}  
  $htmlTable.='</tr>
  </thead></table>
  <table>
  <thead>
    <th class="bg-info"></th>';
     
   $i_q2nd=0; 
   foreach($array_2nd as $x2nd) { 
    $htmlTable.='<th class="bg-info">  '.$x2nd .'</th>';
      $i_q2nd++;}  
  $htmlTable.='</thead>';
    $arr_comps=array(); while($x<count($arr)){ $arr[$count_comp]; array_push($arr_comps,$arr[$count_comp]);
    while($count_comp<count($arr)){  
   $htmlTable.='<tr><td width="30%">';
   $name_of_comp=$acttObj->read_specific("name","comp_reg","abrv = '".$arr[$count_comp]."'");
   $htmlTable.=$name_of_comp['name'].'</td>';
   
   if($type!='single'){
     $child_orgz=$acttObj->read_specific("GROUP_CONCAT($semi,comp_reg.abrv,$semi) as child_orgz","parent_companies,child_companies,comp_reg","parent_companies.sup_child_comp=child_companies.parent_comp AND child_companies.child_comp=comp_reg.id and child_companies.parent_comp =(SELECT id from comp_reg WHERE abrv='".$arr[$count_comp]."')");
 }
     foreach($array_qtr as $qtr=>$val){
   $x=$i_q_telep;$u=0; while($x>$u){
     $result_inner =$type!='single'? $acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice
"," telephone.intrpName = interpreter_reg.id 
AND telephone.orgName = comp_reg.abrv 
AND telephone.invoiceNo=invoice.invoiceNo 
AND telephone.assignDate $qtr and (telephone.orgName IN (".$child_orgz['child_orgz'].")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp"):$acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice
"," telephone.intrpName = interpreter_reg.id 
AND telephone.orgName = comp_reg.abrv 
AND telephone.invoiceNo=invoice.invoiceNo 
AND telephone.assignDate $qtr and (telephone.orgName='$arr[$count_comp]') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp");
     while($row_inner = mysqli_fetch_assoc($result_inner)){ 
    
    $htmlTable.='<td>  '.$row_inner["total_jobs"] .'</td>
    <td>  '.$row_inner["total_cost"] .'</td>';
    
       $u++;}
         break;
        }
   }   
   $htmlTable.='</tr>';
     
  $count_comp++;}  
  $x++;}
  $htmlTable.='<tr class="bg-info">
    <th>Total</th>';
    foreach($array_qtr as $qtr=>$val){
    $res_tp =$type!='single'?$acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice
"," telephone.intrpName = interpreter_reg.id 
AND telephone.orgName = comp_reg.abrv 
AND telephone.invoiceNo=invoice.invoiceNo 
AND telephone.assignDate $qtr and (telephone.orgName IN (".$all_cz['all_cz'].")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp"):$acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice
"," telephone.intrpName = interpreter_reg.id 
AND telephone.orgName = comp_reg.abrv 
AND telephone.invoiceNo=invoice.invoiceNo 
AND telephone.assignDate $qtr and (telephone.orgName IN (".$all_cz['all_cz'].")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp");
    $htmlTable.='<td><b>'.$res_tp['total_jobs'].'</b></td>
    <td><b>'.$res_tp['total_cost'].'</b></td>';
    }
$htmlTable.='</tr>
</tbody>
</table><br>';







$htmlTable.='</tr>
</tbody>
</table><br>
<table>
  <tr><td colspan="2" align="center"><b>BREAKDOWN FOR TELEPHONE JOBS</b></td></tr>
</table>
<table>
  <thead>
    <th class="bg-primary">Communication Type</th>
    <th class="bg-primary">Total Jobs</th>
  </thead>
  <tbody>';
$breakdownQ = $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost,SUM(telephone_jobs) as telephone_jobs,SUM(skype_jobs) as skype_jobs,SUM(zoom_jobs) as zoom_jobs,SUM(skypeAudio_jobs) as skypeAudio_jobs,SUM(skypeVideo_jobs) as skypeVideo_jobs,SUM(whatsappVideo_jobs) as whatsappVideo_jobs,SUM(teams_jobs) as teams_jobs,SUM(telephoneConferenceClient_IP_jobs) as telephoneConferenceClient_IP_jobs,SUM(telephoneConferenceClient_IP_SU_jobs) as telephoneConferenceClient_IP_SU_jobs,SUM(telephoneConferenceInternational_jobs) as telephoneConferenceInternational_jobs,SUM(other_jobs) as other_jobs,SUM(attendAnywhere_jobs) as attendAnywhere_jobs,SUM(whatsappAudio_jobs) as whatsappAudio_jobs,SUM(telephoneConferenceInternational2_jobs) as telephoneConferenceInternational2_jobs","(SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,COUNT((CASE WHEN telephone.comunic=1 THEN telephone.id END)) AS telephone_jobs,COUNT((CASE WHEN telephone.comunic=2 THEN telephone.id END)) AS skype_jobs, COUNT((CASE WHEN telephone.comunic=3 THEN telephone.id END)) AS zoom_jobs, COUNT((CASE WHEN telephone.comunic=4 THEN telephone.id END)) AS skypeAudio_jobs, COUNT((CASE WHEN telephone.comunic=5 THEN telephone.id END)) AS skypeVideo_jobs, COUNT((CASE WHEN telephone.comunic=6 THEN telephone.id END)) AS whatsappVideo_jobs, COUNT((CASE WHEN telephone.comunic=7 THEN telephone.id END)) AS teams_jobs, COUNT((CASE WHEN telephone.comunic=8 THEN telephone.id END)) AS telephoneConferenceClient_IP_jobs, COUNT((CASE WHEN telephone.comunic=9 THEN telephone.id END)) AS telephoneConferenceClient_IP_SU_jobs, COUNT((CASE WHEN telephone.comunic=10 THEN telephone.id END)) AS telephoneConferenceInternational_jobs, COUNT((CASE WHEN telephone.comunic=11 THEN telephone.id END)) AS other_jobs, COUNT((CASE WHEN telephone.comunic=12 THEN telephone.id END)) AS attendAnywhere_jobs, COUNT((CASE WHEN telephone.comunic=13 THEN telephone.id END)) AS whatsappAudio_jobs, COUNT((CASE WHEN telephone.comunic=14 THEN telephone.id END)) AS telephoneConferenceInternational2_jobs FROM telephone,interpreter_reg,comp_reg,invoice","telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate $qtr and (telephone.orgName IN (".$all_cz['all_cz'].")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp");

$htmlTable.='
<tr><td>Telephone</td><td>'.$breakdownQ['telephone_jobs'].'</td></tr>
<tr><td>Skype</td><td>'.$breakdownQ['skype_jobs'].'</td></tr>
<tr><td>Zoom</td><td>'.$breakdownQ['zoom_jobs'].'</td></tr>
<tr><td>Skype Audio</td><td>'.$breakdownQ['skypeAudio_jobs'].'</td></tr>
<tr><td>Skype Video</td><td>'.$breakdownQ['skypeVideo_jobs'].'</td></tr>
<tr><td>WhatsApp Video</td><td>'.$breakdownQ['whatsappVideo_jobs'].'</td></tr>
<tr><td>Microsoft Team</td><td>'.$breakdownQ['teams_jobs'].'</td></tr>
<tr><td>Telephone Conference (Client + IP)</td><td>'.$breakdownQ['telephoneConferenceClient_IP_jobs'].'</td></tr>
<tr><td>Telephone Conferences (Client+IP+SU)</td><td>'.$breakdownQ['telephoneConferenceClient_IP_SU_jobs'].'</td></tr>
<tr><td>Telephone Conference (International Destination)</td><td>'.$breakdownQ['telephoneConferenceInternational_jobs'].'</td></tr>
<tr><td>Other</td><td>'.$breakdownQ['other_jobs'].'</td></tr>
<tr><td>Attend Anywhere</td><td>'.$breakdownQ['attendAnywhere_jobs'].'</td></tr>
<tr><td>WhatsApp Audio</td><td>'.$breakdownQ['whatsappAudio_jobs'].'</td></tr>
<tr><td>Telephone Conference (International Destination)</td><td>'.$breakdownQ['telephoneConferenceInternational2_jobs'].'</td></tr>
<tr class="bg-info"><td>Total Jobs</td><td>'.($breakdownQ['telephone_jobs']+$breakdownQ['skype_jobs']+$breakdownQ['zoom_jobs']+$breakdownQ['skypeAudio_jobs']+$breakdownQ['skypeVideo_jobs']+$breakdownQ['whatsappVideo_jobs']+$breakdownQ['teams_jobs']+$breakdownQ['telephoneConferenceClient_IP_jobs']+$breakdownQ['telephoneConferenceClient_IP_SU_jobs']+$breakdownQ['telephoneConferenceInternational_jobs']+$breakdownQ['other_jobs']+$breakdownQ['attendAnywhere_jobs']+$breakdownQ['whatsappAudio_jobs']+$breakdownQ['telephoneConferenceInternational2_jobs']).'</td></tr>

</tbody>
  </table>
  <table>
<br>











<table>
  <tr><td colspan="11" align="center"><b>TRANSLATION</b></td></tr>
</table>
<table>
     <tbody>
  <thead>
    <th class="bg-primary">Unit Name</th>';
     
   $i_q_trans=0;$count_comp=0; 
   foreach($array_quaters as $x => $val) { 
    $htmlTable.='<th colspan="2" class="bg-primary">  '.$x .'</th>';
      $i_q_trans++;}  
  $htmlTable.='</tr>
  </thead></table>
  <table>
  <thead>
    <th class="bg-info"></th>';
     
   $i_q2nd=0; 
   foreach($array_2nd as $x2nd) { 
    $htmlTable.='<th class="bg-info">  '.$x2nd .'</th>';
      $i_q2nd++;}  
  $htmlTable.='</thead>';
    $arr_comps=array(); while($x<count($arr)){ $arr[$count_comp]; array_push($arr_comps,$arr[$count_comp]);
    while($count_comp<count($arr)){  
   $htmlTable.='<tr><td width="30%">';
   $name_of_comp=$acttObj->read_specific("name","comp_reg","abrv = '".$arr[$count_comp]."'");
   $htmlTable.=$name_of_comp['name'].'</td>';
   
   if($type!='single'){
     $child_orgz=$acttObj->read_specific("GROUP_CONCAT($semi,comp_reg.abrv,$semi) as child_orgz","parent_companies,child_companies,comp_reg","parent_companies.sup_child_comp=child_companies.parent_comp AND child_companies.child_comp=comp_reg.id and child_companies.parent_comp =(SELECT id from comp_reg WHERE abrv='".$arr[$count_comp]."')");
 }
 foreach($array_qtr as $qtr=>$val){
   $x=$i_q_trans;$u=0; while($x>$u){
     $result_inner =$type!='single'? $acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice
"," translation.intrpName = interpreter_reg.id 
AND translation.orgName = comp_reg.abrv 
AND translation.invoiceNo=invoice.invoiceNo 
AND translation.asignDate $qtr and (translation.orgName IN (".$child_orgz['child_orgz'].")) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp"):$acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice
"," translation.intrpName = interpreter_reg.id 
AND translation.orgName = comp_reg.abrv 
AND translation.invoiceNo=invoice.invoiceNo 
AND translation.asignDate $qtr and (translation.orgName='$arr[$count_comp]') and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");
     while($row_inner = mysqli_fetch_assoc($result_inner)){ 
    
    $htmlTable.='<td>  '.$row_inner["total_jobs"] .'</td>
    <td>  '.$row_inner["total_cost"] .'</td>';
    
       $u++;}
         break;
        }
   }   
   $htmlTable.='</tr>';
     
  $count_comp++;}  
  $x++;}
  $htmlTable.='<tr class="bg-info">
    <th>Total</th>';
    foreach($array_qtr as $qtr=>$val){
    $res_tr =$type!='single'?$acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice
"," translation.intrpName = interpreter_reg.id 
AND translation.orgName = comp_reg.abrv 
AND translation.invoiceNo=invoice.invoiceNo 
AND translation.asignDate $qtr and (translation.orgName IN (".$all_cz['all_cz'].")) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp"):$acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice
"," translation.intrpName = interpreter_reg.id 
AND translation.orgName = comp_reg.abrv 
AND translation.invoiceNo=invoice.invoiceNo 
AND translation.asignDate $qtr and (translation.orgName IN (".$all_cz['all_cz'].")) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");
    $htmlTable.='<td><b>'.$res_tr['total_jobs'].'</b></td>
    <td><b>'.$res_tr['total_cost'].'</b></td>';
    }
$htmlTable.='</tr>
</tbody>
</table>
<table>
  <tr><td colspan="3"><b>OVERALL SUMMARY</b></td></tr>
</table>
<table class="table table-bordered table-hover">
     <tbody>
  <thead class="bg-primary">
    <th class="bg-primary">Unit Name</th>';
   $i_q_tot=0;$count_comp=0; 
   foreach($array_quaters as $x => $val) {
   if($i_q_tot<1){
    $htmlTable.='<th class="bg-primary" colspan="5" style="font-size:12px;">Full Year ('.$q1_start.' - '.$q4_finish.')</th>';
    $i_q_tot++; } }
  $htmlTable.='</tr>
  </thead>
  <thead class="bg-info">
    <th class="bg-info"></th>
    <th class="bg-info" style="font-size:12px;">Total Jobs</th>
    <th class="bg-info" style="font-size:12px;">Total Net</th>
    <th class="bg-info" style="font-size:12px;">Total VAT</th>
    <th class="bg-info" style="font-size:12px;">Total Non-VAT</th>
    <th class="bg-info" style="font-size:12px;">Total Cost</th>
  </thead>';
  $arr_comps=array(); while($x<count($arr)){ $arr[$count_comp]; 
  array_push($arr_comps,$arr[$count_comp]);
  while($count_comp<count($arr)){
   $htmlTable.='<tr><td width="30%">';
   $name_of_comp=$acttObj->read_specific("name","comp_reg","abrv = '".$arr[$count_comp]."'");
   $htmlTable.=$name_of_comp['name'].'</td>';
   
 if($type!='single'){
     $child_orgz=$acttObj->read_specific("GROUP_CONCAT($semi,comp_reg.abrv,$semi) as child_orgz","parent_companies,child_companies,comp_reg","parent_companies.sup_child_comp=child_companies.parent_comp AND child_companies.child_comp=comp_reg.id and child_companies.parent_comp =(SELECT id from comp_reg WHERE abrv='".$arr[$count_comp]."')");
 }
   $x=$i_q_tot;$u=0; while($x>$u){
     $result_inner =$type!='single'? $acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost,SUM(total_net) as total_net,SUM(total_vat) as total_vat,SUM(total_non_vat) as total_non_vat","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost,round(IFNULL(sum(interpreter.total_charges_comp),0),2) as total_net,round(IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_vat,round(IFNULL(sum(interpreter.C_otherexpns),0),2) as total_non_vat FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id 
AND interpreter.orgName = comp_reg.abrv 
AND interpreter.invoiceNo=invoice.invoiceNo 
AND interpreter.assignDate between ('$q1_start') and ('$q4_finish') and (interpreter.orgName IN (".$child_orgz['child_orgz'].")) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526) UNION ALL SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,round(IFNULL(sum(telephone.total_charges_comp),0),2) as total_net,round(IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_vat,0 as total_non_vat FROM telephone,interpreter_reg,comp_reg,invoice
WHERE telephone.intrpName = interpreter_reg.id 
AND telephone.orgName = comp_reg.abrv 
AND telephone.invoiceNo=invoice.invoiceNo 
AND telephone.assignDate between ('$q1_start') and ('$q4_finish') and (telephone.orgName IN (".$child_orgz['child_orgz'].")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 UNION ALL SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost,round(IFNULL(sum(translation.total_charges_comp),0),2) as total_net,round(IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_vat,0 as total_non_vat FROM translation,interpreter_reg,comp_reg,invoice
WHERE translation.intrpName = interpreter_reg.id 
AND translation.orgName = comp_reg.abrv 
AND translation.invoiceNo=invoice.invoiceNo 
AND translation.asignDate between ('$q1_start') and ('$q4_finish') and (translation.orgName IN (".$child_orgz['child_orgz'].")) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp"):
$acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost,SUM(total_net) as total_net,SUM(total_vat) as total_vat,SUM(total_non_vat) as total_non_vat","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost,round(IFNULL(sum(interpreter.total_charges_comp),0),2) as total_net,round(IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_vat,round(IFNULL(sum(interpreter.C_otherexpns),0),2) as total_non_vat FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id 
AND interpreter.orgName = comp_reg.abrv 
AND interpreter.invoiceNo=invoice.invoiceNo 
AND interpreter.assignDate between ('$q1_start') and ('$q4_finish') and (interpreter.orgName='$arr[$count_comp]') and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526) UNION ALL SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,round(IFNULL(sum(telephone.total_charges_comp),0),2) as total_net,round(IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_vat,0 as total_non_vat FROM telephone,interpreter_reg,comp_reg,invoice
WHERE telephone.intrpName = interpreter_reg.id 
AND telephone.orgName = comp_reg.abrv 
AND telephone.invoiceNo=invoice.invoiceNo 
AND telephone.assignDate between ('$q1_start') and ('$q4_finish') and (telephone.orgName='$arr[$count_comp]') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 UNION ALL SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost,round(IFNULL(sum(translation.total_charges_comp),0),2) as total_net,round(IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_vat,0 as total_non_vat FROM translation,interpreter_reg,comp_reg,invoice
WHERE translation.intrpName = interpreter_reg.id 
AND translation.orgName = comp_reg.abrv 
AND translation.invoiceNo=invoice.invoiceNo 
AND translation.asignDate between ('$q1_start') and ('$q4_finish') and (translation.orgName='$arr[$count_comp]') and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");
     while($row_inner = mysqli_fetch_assoc($result_inner)){
      $htmlTable.='<td>'.$row_inner["total_jobs"].'</td>
      <td>'.$row_inner["total_net"].'</td>
      <td>'.$row_inner["total_vat"].'</td>
      <td>'.$row_inner["total_non_vat"].'</td>
      <td>'.$row_inner["total_cost"].'</td>';
      $u++;
      }
      break;
  }
   $htmlTable.='</tr>';
     $count_comp++;}
     $x++;}
$htmlTable.='<tr class="bg-info">
    <th>Total</th>';
    $res_tr =$type!='single'?$acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost,SUM(total_net) as total_net,SUM(total_vat) as total_vat,SUM(total_non_vat) as total_non_vat","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost,round(IFNULL(sum(interpreter.total_charges_comp),0),2) as total_net,round(IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_vat,round(IFNULL(sum(interpreter.C_otherexpns),0),2) as total_non_vat FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q1_start') and ('$q4_finish') and (interpreter.orgName IN (".$all_cz['all_cz'].")) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526)
UNION ALL
SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,round(IFNULL(sum(telephone.total_charges_comp),0),2) as total_net,round(IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_vat,0 as total_non_vat FROM telephone,interpreter_reg,comp_reg,invoice WHERE telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q1_start') and ('$q4_finish') and (telephone.orgName IN (".$all_cz['all_cz'].")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0
UNION ALL
SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost,round(IFNULL(sum(translation.total_charges_comp),0),2) as total_net,round(IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_vat,0 as total_non_vat FROM translation,interpreter_reg,comp_reg,invoice WHERE translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q1_start') and ('$q4_finish') and (translation.orgName IN (".$all_cz['all_cz'].")) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp"):
$acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost,SUM(total_net) as total_net,SUM(total_vat) as total_vat,SUM(total_non_vat) as total_non_vat","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_cost,round(IFNULL(sum(interpreter.total_charges_comp),0),2) as total_net,round(IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_vat,round(IFNULL(sum(interpreter.C_otherexpns),0),2) as total_non_vat FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q1_start') and ('$q4_finish') and (interpreter.orgName IN (".$all_cz['all_cz'].")) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526)
UNION ALL
SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,round(IFNULL(sum(telephone.total_charges_comp),0),2) as total_net,round(IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_vat,0 as total_non_vat FROM telephone,interpreter_reg,comp_reg,invoice WHERE telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q1_start') and ('$q4_finish') and (telephone.orgName IN (".$all_cz['all_cz'].")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0
UNION ALL
SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost,round(IFNULL(sum(translation.total_charges_comp),0),2) as total_net,round(IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_vat,0 as total_non_vat FROM translation,interpreter_reg,comp_reg,invoice WHERE translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q1_start') and ('$q4_finish') and (translation.orgName IN (".$all_cz['all_cz'].")) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");
    $htmlTable.='<td><b>'. $res_tr['total_jobs'].'</b></td>
    <td><b>'. $res_tr['total_net'].'</b></td>
    <td><b>'. $res_tr['total_vat'].'</b></td>
    <td><b>'. $res_tr['total_non_vat'].'</b></td>
    <td><b>'. $res_tr['total_cost'].'</b></td>
    </tr>
   </tbody>
</table><br>
<table class="table table-bordered table-hover">
     <tbody>
  <thead class="bg-primary">
    <th class="bg-primary">Unit Name</th>';
   $i_q_tot=0;$count_comp=0; 
   foreach($array_quaters_next as $x => $val) {
   if($i_q_tot<1){
    $htmlTable.='<th class="bg-primary" colspan="5" style="font-size:12px;">Full Year ('.$q1_start_next.' - '.$q4_finish_next.')</th>';
    $i_q_tot++; } }
  $htmlTable.='</tr>
  </thead>
  <thead class="bg-info">
    <th class="bg-info"></th>
    <th class="bg-info" style="font-size:12px;">Total Jobs</th>
    <th class="bg-info" style="font-size:12px;">Total Net</th>
    <th class="bg-info" style="font-size:12px;">Total VAT</th>
    <th class="bg-info" style="font-size:12px;">Total Non-VAT</th>
    <th class="bg-info" style="font-size:12px;">Total Cost</th>
  </thead>';
  $arr_comps=array(); while($x<count($arr)){ $arr[$count_comp]; array_push($arr_comps,$arr[$count_comp]);
  while($count_comp<count($arr)){
   $htmlTable.='<tr><td width="30%">';
   $name_of_comp=$acttObj->read_specific("name","comp_reg","abrv = '".$arr[$count_comp]."'");
   $htmlTable.=$name_of_comp['name'].'</td>';
   
 if($type!='single'){
     $child_orgz=$acttObj->read_specific("GROUP_CONCAT($semi,comp_reg.abrv,$semi) as child_orgz","parent_companies,child_companies,comp_reg","parent_companies.sup_child_comp=child_companies.parent_comp AND child_companies.child_comp=comp_reg.id and child_companies.parent_comp =(SELECT id from comp_reg WHERE abrv='".$arr[$count_comp]."')");
 }
   $x=$i_q_tot;$u=0; while($x>$u){
       $result_inner =$type!='single'? $acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost,SUM(total_net) as total_net,SUM(total_vat) as total_vat,SUM(total_non_vat) as total_non_vat","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost,round(IFNULL(sum(interpreter.total_charges_comp),0),2) as total_net,round(IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_vat,round(IFNULL(sum(interpreter.C_otherexpns),0),2) as total_non_vat FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id 
AND interpreter.orgName = comp_reg.abrv 
AND interpreter.invoiceNo=invoice.invoiceNo 
AND interpreter.assignDate between ('$q1_start_next') and ('$q4_finish_next') and (interpreter.orgName IN (".$child_orgz['child_orgz'].")) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526) UNION ALL SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,round(IFNULL(sum(telephone.total_charges_comp),0),2) as total_net,round(IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_vat,0 as total_non_vat FROM telephone,interpreter_reg,comp_reg,invoice
WHERE telephone.intrpName = interpreter_reg.id 
AND telephone.orgName = comp_reg.abrv 
AND telephone.invoiceNo=invoice.invoiceNo 
AND telephone.assignDate between ('$q1_start_next') and ('$q4_finish_next') and (telephone.orgName IN (".$child_orgz['child_orgz'].")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 UNION ALL SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost,round(IFNULL(sum(translation.total_charges_comp),0),2) as total_net,round(IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_vat,0 as total_non_vat FROM translation,interpreter_reg,comp_reg,invoice
WHERE translation.intrpName = interpreter_reg.id 
AND translation.orgName = comp_reg.abrv 
AND translation.invoiceNo=invoice.invoiceNo 
AND translation.asignDate between ('$q1_start_next') and ('$q4_finish_next') and (translation.orgName IN (".$child_orgz['child_orgz'].")) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp"):
$acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost,SUM(total_net) as total_net,SUM(total_vat) as total_vat,SUM(total_non_vat) as total_non_vat","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost,round(IFNULL(sum(interpreter.total_charges_comp),0),2) as total_net,round(IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_vat,round(IFNULL(sum(interpreter.C_otherexpns),0),2) as total_non_vat FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id 
AND interpreter.orgName = comp_reg.abrv 
AND interpreter.invoiceNo=invoice.invoiceNo 
AND interpreter.assignDate between ('$q1_start_next') and ('$q4_finish_next') and (interpreter.orgName='$arr[$count_comp]') and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526) UNION ALL SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,round(IFNULL(sum(telephone.total_charges_comp),0),2) as total_net,round(IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_vat,0 as total_non_vat FROM telephone,interpreter_reg,comp_reg,invoice
WHERE telephone.intrpName = interpreter_reg.id 
AND telephone.orgName = comp_reg.abrv 
AND telephone.invoiceNo=invoice.invoiceNo 
AND telephone.assignDate between ('$q1_start_next') and ('$q4_finish_next') and (telephone.orgName='$arr[$count_comp]') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 UNION ALL SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost,round(IFNULL(sum(translation.total_charges_comp),0),2) as total_net,round(IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_vat,0 as total_non_vat FROM translation,interpreter_reg,comp_reg,invoice
WHERE translation.intrpName = interpreter_reg.id 
AND translation.orgName = comp_reg.abrv 
AND translation.invoiceNo=invoice.invoiceNo 
AND translation.asignDate between ('$q1_start_next') and ('$q4_finish_next') and (translation.orgName='$arr[$count_comp]') and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");
       while($row_inner = mysqli_fetch_assoc($result_inner)){
      $htmlTable.='<td>'.$row_inner["total_jobs"].'</td>
      <td>'.$row_inner["total_net"].'</td>
      <td>'.$row_inner["total_vat"].'</td>
      <td>'.$row_inner["total_non_vat"].'</td>
      <td>'.$row_inner["total_cost"].'</td>';
      $u++;
      }
      break;
  }
   $htmlTable.='</tr>';
     $count_comp++;}
     $x++;}
$htmlTable.='<tr class="bg-info">
    <th>Total</th>';
    $res_tr =$type!='single'?$acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost,SUM(total_net) as total_net,SUM(total_vat) as total_vat,SUM(total_non_vat) as total_non_vat","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost,round(IFNULL(sum(interpreter.total_charges_comp),0),2) as total_net,round(IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_vat,round(IFNULL(sum(interpreter.C_otherexpns),0),2) as total_non_vat FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q1_start_next') and ('$q4_finish_next') and (interpreter.orgName IN (".$all_cz['all_cz'].")) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526)
UNION ALL
SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,round(IFNULL(sum(telephone.total_charges_comp),0),2) as total_net,round(IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_vat,0 as total_non_vat FROM telephone,interpreter_reg,comp_reg,invoice WHERE telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q1_start_next') and ('$q4_finish_next') and (telephone.orgName IN (".$all_cz['all_cz'].")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0
UNION ALL
SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost,round(IFNULL(sum(translation.total_charges_comp),0),2) as total_net,round(IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_vat,0 as total_non_vat FROM translation,interpreter_reg,comp_reg,invoice WHERE translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q1_start_next') and ('$q4_finish_next') and (translation.orgName IN (".$all_cz['all_cz'].")) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp"):
$acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost","(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_cost,round(IFNULL(sum(interpreter.total_charges_comp),0),2) as total_net,round(IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_vat,round(IFNULL(sum(interpreter.C_otherexpns),0),2) as total_non_vat FROM interpreter,interpreter_reg,comp_reg,invoice","interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('$q1_start_next') and ('$q4_finish_next') and (interpreter.orgName IN (".$all_cz['all_cz'].")) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526)
UNION ALL
SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,round(IFNULL(sum(telephone.total_charges_comp),0),2) as total_net,round(IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_vat,0 as total_non_vat FROM telephone,interpreter_reg,comp_reg,invoice WHERE telephone.intrpName = interpreter_reg.id AND telephone.orgName = comp_reg.abrv AND telephone.invoiceNo=invoice.invoiceNo AND telephone.assignDate between ('$q1_start_next') and ('$q4_finish_next') and (telephone.orgName IN (".$all_cz['all_cz'].")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0
UNION ALL
SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost,round(IFNULL(sum(translation.total_charges_comp),0),2) as total_net,round(IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_vat,0 as total_non_vat FROM translation,interpreter_reg,comp_reg,invoice WHERE translation.intrpName = interpreter_reg.id AND translation.orgName = comp_reg.abrv AND translation.invoiceNo=invoice.invoiceNo AND translation.asignDate between ('$q1_start_next') and ('$q4_finish_next') and (translation.orgName IN (".$all_cz['all_cz'].")) and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp");
    $htmlTable.='<td><b>'. $res_tr['total_jobs'].'</b></td>
    <td><b>'. $res_tr['total_net'].'</b></td>
    <td><b>'. $res_tr['total_vat'].'</b></td>
    <td><b>'. $res_tr['total_non_vat'].'</b></td>
    <td><b>'. $res_tr['total_cost'].'</b></td>
    </tr>
   </tbody>
</table>
</div>';

$htmlTable.='
<br><br><table>
  <tr><td colspan="2" align="center"><b>JOBS SHIFTED TO ALTERNATE MODE DETAILS</b></td></tr>
</table>
<table>
  <thead>
    <th class="bg-primary">Conversion Type</th>
    <th class="bg-primary">Total Jobs</th>
  </thead>
  <tbody>';
$shifted_jobs_interp = $acttObj->read_specific("COUNT(id) as total_shifted_jobs", "interpreter", " interpreter.assignDate $qtr and (interpreter.orgName IN (" . $all_cz["all_cz"] . ")) and interpreter.is_shifted = 1 ");

$shifted_jobs_telep = $acttObj->read_specific("COUNT(id) as total_shifted_jobs", "telephone", " telephone.assignDate $qtr and (telephone.orgName IN (" . $all_cz['all_cz'] . ")) and telephone.is_shifted = 1 ");

$htmlTable.='
<tr><td>F2F->Telephone</td><td>'.$shifted_jobs_interp['total_shifted_jobs'].'</td></tr>
<tr><td>Telephone->F2F</td><td>'.$shifted_jobs_telep['total_shifted_jobs'].'</td></tr>
<tr class="bg-info"><td>Total Jobs</td><td>'.($shifted_jobs_interp['total_shifted_jobs']+$shifted_jobs_telep['total_shifted_jobs']).'</td></tr>

</tbody>
  </table>
  <table>
<br>';


$htmlTable.='
<br><br><table>
  <tr><td colspan="4" align="center"><b>Cancelled Jobs details</b></td></tr>
</table>
<table>
  <thead>
    <th class="bg-primary">Total Cancelled Jobs</th>
    <th class="bg-primary">Cost ofCancelled Jobs</th>
    <th class="bg-primary">Cancelled By LSUK</th>
    <th class="bg-primary">Cancelled By Client</th>
  </thead>
  <tbody>';

  $canc_interp = $acttObj->read_specific("COUNT(interpreter.id) as total_cancelled_jobs,round(IFNULL(sum(total_charges_comp),0)+ IFNULL(sum(total_charges_comp * cur_vat),0) +IFNULL(sum(C_otherexpns),0),2) as total_cost,SUM(order_cancelledby='LSUK') as lsuk_count,SUM(order_cancelledby='Client') as client_count", "interpreter,comp_reg", " interpreter.orgName = comp_reg.abrv AND interpreter.assignDate $qtr and (interpreter.orgName IN (" . $all_cz['all_cz'] . ")) and interpreter.deleted_flag = 0 AND (interpreter.order_cancel_flag=1 OR interpreter.orderCancelatoin=1) AND interpreter.cn_r_id NOT IN (7,16) AND interpreter.id NOT IN (12027,14298,16772,14526)");

  $canc_telephone = $acttObj->read_specific("COUNT(telephone.id) as total_cancelled_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,SUM(order_cancelledby='LSUK') as lsuk_count,SUM(order_cancelledby='Client') as client_count", "telephone,comp_reg", " telephone.orgName = comp_reg.abrv AND telephone.assignDate $qtr and (telephone.orgName IN (" . $all_cz['all_cz'] . ")) and telephone.deleted_flag = 0 and (telephone.order_cancel_flag=1 OR telephone.orderCancelatoin=1) AND telephone.cn_r_id NOT IN (7,16)");

  $canc_trans = $acttObj->read_specific("COUNT(translation.id) as total_cancelled_jobs,round(IFNULL(sum(total_charges_comp),0)+ IFNULL(sum(total_charges_comp * cur_vat),0),2) as total_cost,SUM(order_cancelledby='LSUK') as lsuk_count,SUM(order_cancelledby='Client') as client_count", "translation,comp_reg", " translation.orgName = comp_reg.abrv AND translation.asignDate $qtr and (translation.orgName IN (" . $all_cz['all_cz'] . ")) and translation.deleted_flag = 0 and (translation.order_cancel_flag=1 OR translation.orderCancelatoin=1) AND translation.cn_r_id NOT IN (7,16)");

  $htmlTable.='
  <tr><td>'.($canc_interp["total_cancelled_jobs"]+$canc_telephone["total_cancelled_jobs"]+$canc_trans["total_cancelled_jobs"]).'<td>'.($canc_interp["total_cost"]+$canc_telephone["total_cost"]+$canc_trans["total_cost"]).'<td>'.($canc_interp["lsuk_count"]+$canc_telephone["lsuk_count"]+$canc_trans["lsuk_count"]).'<td>'.($canc_interp["client_count"]+$canc_telephone["client_count"]+$canc_trans["client_count"]).'</tr>

  </tbody>
    </table>
    <table>
  <br>';


  $htmlTable.='
  <br><br><table>
    <tr><td colspan="2" align="center"><b>Un-processed Jobs details</b></td></tr>
  </table>
  <table>
    <thead>
      <th class="bg-primary">Description</th>
      <th class="bg-primary">Total Jobs</th>
    </thead>
    <tbody>';
  
    $get_del = mysqli_query($con, "SELECT 
    SUM(total_deleted_jobs) as total_deleted_jobs,
    ROUND(SUM(total_cost), 2) as total_cost,SUM(lsuk_count) as total_lsuk_count,SUM(client_count) as total_client_count
FROM (
    SELECT 
        COUNT(id) as total_deleted_jobs,
        ROUND(
            IFNULL(SUM(total_charges_comp), 0) + 
            IFNULL(SUM(total_charges_comp * cur_vat), 0) + 
            IFNULL(SUM(C_otherexpns), 0), 
            2
        ) as total_cost,
        COUNT(CASE WHEN interpreter.deleted_reason LIKE '%LSUK%' AND interpreter.deleted_reason LIKE '%provide the service%' THEN 1 END) AS lsuk_count,
        COUNT(CASE WHEN interpreter.deleted_reason LIKE '%Customer no longer wish to proceed%' THEN 1 END) AS client_count  
    FROM interpreter 
    WHERE 
        interpreter.assignDate $qtr  
        AND interpreter.orgName IN (" . $all_cz["all_cz"] . ") 
        AND interpreter.is_shifted = 0 
        AND interpreter.deleted_flag = 1 
        AND interpreter.intrpName = '' 
        AND (interpreter.deleted_reason LIKE '%Customer no longer wish to proceed%' OR (interpreter.deleted_reason LIKE '%LSUK%' AND interpreter.deleted_reason LIKE '%provide the service%'))  
        AND interpreter.id NOT IN (12027, 14298, 16772, 14526)

    UNION ALL

    SELECT 
        COUNT(id) as total_deleted_jobs,
        ROUND(
            IFNULL(SUM(telephone.total_charges_comp), 0) + 
            IFNULL(SUM(telephone.total_charges_comp * telephone.cur_vat), 0), 
            2
        ) as total_cost,COUNT(CASE WHEN telephone.deleted_reason LIKE '%LSUK%' AND telephone.deleted_reason LIKE '%provide the service%' THEN 1 END) AS lsuk_count,COUNT(CASE WHEN telephone.deleted_reason LIKE '%Customer no longer wish to proceed%' THEN 1 END) AS client_count  
    FROM telephone 
    WHERE 
        telephone.assignDate $qtr 
        AND telephone.orgName IN (" . $all_cz['all_cz'] . ") 
        AND telephone.is_shifted = 0 
        AND telephone.deleted_flag = 1 
        AND telephone.intrpName = '' 
        AND (telephone.deleted_reason LIKE '%Customer no longer wish to proceed%' OR (telephone.deleted_reason LIKE '%LSUK%' AND telephone.deleted_reason LIKE '%provide the service%'))

    UNION ALL

    SELECT 
        COUNT(id) as total_deleted_jobs,
        ROUND(
            IFNULL(SUM(total_charges_comp), 0) + 
            IFNULL(SUM(total_charges_comp * cur_vat), 0), 
            2
        ) as total_cost, COUNT(CASE WHEN translation.deleted_reason LIKE '%LSUK%' AND translation.deleted_reason LIKE '%provide the service%' THEN 1 END) AS lsuk_count,COUNT(CASE WHEN translation.deleted_reason LIKE '%Customer no longer wish to proceed%' THEN 1 END) AS client_count  
    FROM translation 
    WHERE 
        translation.asignDate $qtr 
        AND translation.orgName IN (" . $all_cz['all_cz'] . ") 
        AND translation.deleted_flag = 1 
        AND translation.intrpName = '' 
        AND (translation.deleted_reason LIKE '%Customer no longer wish to proceed%' OR (translation.deleted_reason LIKE '%LSUK%' AND translation.deleted_reason LIKE '%provide the service%'))
) AS CombinedResults;");


$get_deleted = mysqli_fetch_assoc($get_del);

    $reasons = mysqli_query($con, "WITH LatestJobNotes AS (
      SELECT 
          jobnotes.fid, 
          jobnotes.jobNote, 
          COUNT(CASE WHEN jobnotes.jobNote LIKE '%Customer no longer wish to proceed%' THEN 1 END) AS customer_cancel,
          COUNT(CASE WHEN jobnotes.jobNote LIKE '%t provide the service%' THEN 1 END) AS lsuk_cancel,
          COUNT(CASE WHEN jobnotes.jobNote LIKE '%wrong bookings%' OR jobnotes.jobNote LIKE '%Duplicate Booking%' THEN 1 END) AS duplicate_booking,
          ROW_NUMBER() OVER (PARTITION BY jobnotes.fid ORDER BY jobnotes.id DESC) as rn
      FROM 
          jobnotes
      WHERE
          jobnotes.jobNote LIKE '%Customer no longer wish to proceed%' 
          OR jobnotes.jobNote LIKE '%t provide the service%' 
          OR jobnotes.jobNote LIKE '%wrong bookings%' 
          OR jobnotes.jobNote LIKE '%Duplicate Booking%'
      GROUP BY 
          jobnotes.fid, jobnotes.jobNote
  ),
  AllAssignments AS (
      SELECT 
          id, 
          assignDate, 
          orgName, 
          is_shifted, 
          deleted_flag, 
          intrpName
      FROM 
          interpreter
      WHERE
          assignDate $qtr
          AND orgName IN (" . $all_cz['all_cz'] . ")
          AND is_shifted = 0
          AND deleted_flag = 1
          AND intrpName = ''
      
      UNION ALL
      
      SELECT 
          id, 
          assignDate, 
          orgName, 
          is_shifted, 
          deleted_flag, 
          intrpName
      FROM 
          telephone
      WHERE
          assignDate $qtr
          AND orgName IN (" . $all_cz['all_cz'] . ")
          AND is_shifted = 0
          AND deleted_flag = 1
          AND intrpName = ''
      
      UNION ALL
      
      SELECT 
          id, 
          asignDate as assignDate, 
          orgName, 
          deleted_flag,
        approved_flag,
          intrpName
      FROM 
          translation
      WHERE
          asignDate $qtr
          AND orgName IN (" . $all_cz['all_cz'] . ")
          AND deleted_flag = 1
          AND intrpName = ''
  )
  SELECT 
      SUM(LatestJobNotes.customer_cancel) as customer_cancel,
      SUM(LatestJobNotes.lsuk_cancel) as lsuk_cancel,
      SUM(LatestJobNotes.duplicate_booking) as duplicate_booking
  FROM 
      AllAssignments
  INNER JOIN 
      LatestJobNotes ON AllAssignments.id = LatestJobNotes.fid
  WHERE 
      LatestJobNotes.rn = 1;");

    $get_reasons = mysqli_fetch_assoc($reasons);

    $htmlTable.='
    <tr><td>Client Requested Not to Proceed</td><td>'.$get_deleted['total_client_count'].'</td></tr>
    <tr><td>LSUK could not Provide the service</td><td>'.$get_deleted['total_lsuk_count'].'</td></tr>
    <tr class="bg-info"><td>Total Un-processed Jobs</td><td>'.$get_deleted['total_deleted_jobs'].'</td></tr>

  
    </tbody>
      </table>
      <table>
    <br>';


list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls"); 
echo $htmlTable;
?>

