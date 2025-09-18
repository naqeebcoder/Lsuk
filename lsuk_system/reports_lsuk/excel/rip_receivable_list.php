<?php 
ob_start();
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 
?> 

<?php 
include '../../db.php';
include_once('../../class.php');
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=152 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_receivable = $_SESSION['is_root'] == 1 || in_array(108, $get_actions);
$action_edit_receivable = $_SESSION['is_root'] == 1 || in_array(109, $get_actions);
$action_delete_receivable = $_SESSION['is_root'] == 1 || in_array(110, $get_actions);
$action_restore_receivable = $_SESSION['is_root'] == 1 || in_array(111, $get_actions);
$action_view_installments = $_SESSION['is_root'] == 1 || in_array(112, $get_actions); 

$htmlTable = "";
$htmlTable .= "<table> 
			<thead> 
				<tr>
				  <th>Company</th>
				  <th width='20%'>Invoices</th>
   				  <th>Total Amount</th>
   				  <th>Credit Limit</th>
   				  <th>Over Credit</th>
				</tr> 
			</thead> 
			<tbody> ";

$get_comps = $acttObj->read_all("id,name,abrv,credit_limit","comp_reg"," comp_nature IN (1,4) AND deleted_flag=0");
$num_comps = mysqli_num_rows($get_comps);
$comps_data = array();
$data_count=0;
$semi="\"'\"";
if($num_comps>0){
    while($row = mysqli_fetch_assoc($get_comps)){
        $p_org = $row['id'];
        $p_name = $row['name'];
        $p_abrv = $row['abrv'];
        $p_credit_limit = $row['credit_limit']?:0;

        $get_p_org_q = $acttObj->query_extra("GROUP_CONCAT(TRIM(comp_reg.abrv)) as ch_ids2,GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries,comp_reg", " subsidiaries.child_comp=comp_reg.id and subsidiaries.parent_comp=$p_org","set SESSION group_concat_max_len=10000");
        $p_org_q = $get_p_org_q['ch_ids']?:'0';
        $p_org_ad = ($p_org_q!=0?" and comp_reg.id IN ($p_org_q) ":" and comp_reg.id IN ($p_org) ");
        $qy='SELECT SUM(num_inv) as tot_inv,ROUND(SUM(total_cost),2) AS inv_cost from (SELECT COUNT(interpreter.id) as num_inv,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,comp_reg WHERE  interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and (interpreter.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status="") OR (interpreter.multInv_flag=0 AND interpreter.invoiceNo<>"" AND  (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0) )) '.$p_org_ad.' UNION ALL SELECT COUNT(telephone.id) as num_inv,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,comp_reg WHERE telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and (telephone.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status="") OR (telephone.multInv_flag=0 AND telephone.invoiceNo<>"" and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) AND telephone.total_charges_comp > 0) ))  '.$p_org_ad.' UNION ALL SELECT COUNT(translation.id) as num_inv,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,comp_reg WHERE  translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 AND translation.disposed_of = 0 and translation.order_cancel_flag=0 and (translation.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status="") OR (translation.multInv_flag=0 AND translation.invoiceNo<>"" and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp > 0))) '.$p_org_ad.') as grp';
        $get_data = mysqli_query($con,$qy);
        $chk_data = mysqli_num_rows($get_data);
        if($chk_data>0){
            while($row2 = mysqli_fetch_assoc($get_data)){
                $tot_inv = $row2['tot_inv'];
                $inv_cost = $row2['inv_cost'];
                $comps_data[$data_count]['tot_inv']=$tot_inv;
                $comps_data[$data_count]['inv_cost']=$inv_cost;
                $comps_data[$data_count]['p_name']=$p_name;
                $comps_data[$data_count]['p_credit_limit']=$p_credit_limit;
                $comps_data[$data_count]['comp_abrv']=$get_p_org_q['ch_ids2']?:$p_abrv;
                $data_count++;
                
            }
        }
    }
    arsort($comps_data);
    $comps_data = array_values($comps_data);
    for($j=0;$j<count($comps_data);$j++){
        if($comps_data[$j]['tot_inv']>0){
            $credit_cal = $comps_data[$j]['inv_cost']-$comps_data[$j]['p_credit_limit'];
        $htmlTable .= "
        <tr>
            <td>".$comps_data[$j]['p_name']."</td>
            <td>".$comps_data[$j]['tot_inv']."</td>
            <td>".$comps_data[$j]['inv_cost']."</td>
            <td>".$comps_data[$j]['p_credit_limit']."</td>
            <td>".(($comps_data[$j]['inv_cost']>$comps_data[$j]['p_credit_limit'])?($credit_cal):0)."</td>
        </tr>";
        }
    }
}
$htmlTable .= "<tr class='bg-primary'>
		<td><b>TOTAL</td>
		<td><b>".array_sum(array_column($comps_data,'tot_inv'))."</b></td>
		<td><b>".array_sum(array_column($comps_data,'inv_cost'))."</b></td>
		<td></td>
		<td></td>
	</tr></tbody></table>";
list($a, $b) = explode('.', basename(__FILE__));
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=" . $a . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $htmlTable;
ob_end_flush();
exit;
				?>