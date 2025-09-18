<?php
include('db.php');

$table=$_GET['table'];
$val=$_GET['val'];
$comp=$_GET['comp'];
$porder=NULL;
$credit=0;
$creditId=NULL;
$bz_credit=0;
$bizcreditlimit=1000;

$query_order="SELECT comp_credit.porder , credit
	FROM   comp_credit 
	WHERE  comp_credit.orgname LIKE '$val' 
       AND porder <> '' 
	ORDER  BY comp_credit.id DESC 
	LIMIT  1";			
		
$result_order = mysqli_query($con,$query_order);
while($row_order = mysqli_fetch_assoc($result_order))
{
	$porder=$row_order['porder'];
	$bizcreditlimit=$row_order['credit'];
}	

$query_order_bz="SELECT bz_credit.creditId 
	FROM   bz_credit 
	WHERE  bz_credit.orgName LIKE '$val' 
       AND creditid <> '' 
	ORDER  BY bz_credit.id DESC 
	LIMIT  1";			
	
$result_order_bz = mysqli_query($con,$query_order_bz);
while($row_order_bz = mysqli_fetch_assoc($result_order_bz))
{
	$creditId=$row_order_bz['creditId'];
}	
			
$query="SELECT
   $table.contactNo1,
   $table.po_req,
   $table.po_email,
   $table.contactPerson,
   $table.email,
   $table.city,
   $table.buildingName,
   $table.streetRoad,
   $table.postCode,
   $table.line1,
   $table.line2,
   $table.bookingType,
   bz_credit.orgName 
FROM
   $table 
  
   LEFT JOIN
      bz_credit 
      ON $table.abrv = bz_credit.orgName 
WHERE
   $table. $comp like '$val' limit 1";						

$result = mysqli_query($con,$query);
while($row_selected	=mysqli_fetch_assoc($result)) 
{ 
	$orgName=$row_selected['orgName'];
								
	$query_c="SELECT Sum(comp_credit.credit) AS credit 
		FROM   comp_credit 
		WHERE  orgname LIKE '$val' 
       	AND porder <> '' 
		LIMIT  1";

	$result_c = mysqli_query($con,$query_c);
	while($row_c = mysqli_fetch_assoc($result_c))
	{
		$credit=$row_c['credit'];
	}
			
	$query_bz2="SELECT Sum(bz_credit.bz_credit) AS bz_credit 
		FROM   bz_credit 
		WHERE  orgname LIKE '$val' 
       		AND creditid <> '' 
		LIMIT  1";			

	$result_bz2 = mysqli_query($con,$query_bz2);
	while($row_bz2 = mysqli_fetch_assoc($result_bz2))
	{
		$bz_credit=$row_bz2['bz_credit'];
	}		
			
	$query_bz1="SELECT Sum(bz_credit.bz_credit) AS bz_credit 
		FROM   bz_credit 
		WHERE  orgname LIKE '$val' 
       		AND creditid <> '' 
		LIMIT  1";			

	$result_bz1 = mysqli_query($con,$query_bz1);
	while($row_bz1 = mysqli_fetch_assoc($result_bz1))
	{
		$bz_credit=$row_bz1['bz_credit'];
	}	
			
	$query_d="SELECT Sum(comp_credit.debit) AS debit 
			FROM   comp_credit 
			WHERE  orgName LIKE '$val' 
			LIMIT  1";			
	$result_d = mysqli_query($con,$query_d);
	while($row_d = mysqli_fetch_assoc($result_d))
	{
		$debit=$row_d['debit'];
	}	
			
	$query_bz="SELECT Sum(bz_credit.bz_debit) AS bz_debit 
			FROM   bz_credit 
			WHERE  orgName LIKE '$val' and creditId='$creditId'
			LIMIT  1";			

	$result_bz = mysqli_query($con,$query_bz);
	while($row_bz = mysqli_fetch_assoc($result_bz))
	{
		$bz_debit=$row_bz['bz_debit'];
	}

	if(empty($porder))
	{
		$porder='Nil';
		$credit=0;
	}
	
	$bz_total=$bz_credit-$bz_debit;
	
	if(empty($creditId) || $bz_credit==0 || $bz_total <= 0)
	{
		$creditId='Nil';
		$bz_total=0;
	}				

	$jar['contactNo']=$row_selected['contactNo1'];
	$jar['po_req']=$row_selected['po_req'];
	$jar['contactPerson']=$row_selected['contactPerson'];
	$jar['email']=$row_selected['email'];
    $jar['po_email']=$row_selected['po_email'];
	$jar['city']=$row_selected['city'];
	$jar['inchRoad']=$row_selected['streetRoad'];
	$jar['inchNo']=$row_selected['buildingName'];
	$jar['inchPcode']=$row_selected['postCode'];
	$jar['line1']=$row_selected['line1'];
	$jar['line2']=$row_selected['line2'];
	$jar['bookinType']=$row_selected['bookingType'];
	//$jar['porder']=$porder;
	//$jar['credit']=round($credit-$debit,2);
	$jar['creditId']=$creditId;
	$jar['bz_credit']=round($bz_total,2);
}
$check_comp_nature =  mysqli_fetch_assoc(mysqli_query($con,"SELECT id,comp_nature FROM comp_reg WHERE abrv='$val'"));
if($check_comp_nature['comp_nature']==3){
	$getp_org=mysqli_fetch_assoc(mysqli_query($con,"SELECT parent_comp FROM subsidiaries WHERE child_comp=".$check_comp_nature['id'].""));
	$p_org = $getp_org['parent_comp'];
}elseif(in_array($check_comp_nature['comp_nature'],[1,4])){
	$p_org=$check_comp_nature['id'];
}
$credit_limit = mysqli_fetch_assoc(mysqli_query($con,"SELECT credit_limit FROM comp_reg WHERE id=$p_org"))["credit_limit"];
$p_org_q = mysqli_fetch_assoc(mysqli_query($con,"SELECT GROUP_CONCAT(child_comp) as ch_ids FROM subsidiaries WHERE parent_comp=$p_org"))['ch_ids']?:'0';
$p_org_ad = ($p_org_q!=0?" and comp_reg.id IN ($p_org_q) ":" and comp_reg.id IN ($p_org) ");
$qy='SELECT SUM(num_inv) as tot_inv,SUM(total_cost) AS inv_cost from (SELECT COUNT(interpreter.id) as num_inv,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter,comp_reg WHERE  interpreter.orgName=comp_reg.abrv AND interpreter.deleted_flag = 0 AND interpreter.disposed_of = 0 and interpreter.order_cancel_flag=0 and (interpreter.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status="") OR (interpreter.multInv_flag=0 AND interpreter.invoiceNo<>"" AND  (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0) )) '.$p_org_ad.' UNION ALL SELECT COUNT(telephone.id) as num_inv,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,comp_reg WHERE telephone.orgName=comp_reg.abrv AND telephone.deleted_flag = 0 AND telephone.disposed_of = 0 and telephone.order_cancel_flag=0 and (telephone.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status="") OR (telephone.multInv_flag=0 AND telephone.invoiceNo<>"" and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) AND telephone.total_charges_comp > 0) ))  '.$p_org_ad.' UNION ALL SELECT COUNT(translation.id) as num_inv,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,comp_reg WHERE  translation.orgName=comp_reg.abrv AND translation.deleted_flag = 0 AND translation.disposed_of = 0 and translation.order_cancel_flag=0 and (translation.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status="") OR (translation.multInv_flag=0 AND translation.invoiceNo<>"" and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp > 0))) '.$p_org_ad.') as grp';
$get_qy = mysqli_fetch_assoc(mysqli_query($con,$qy));
$inv_cost = $get_qy['inv_cost'];
$jar['inv_cost']=round($inv_cost);
$jar['credit_limit']=round($credit_limit);
$jar['bizcreditlimit']=$bizcreditlimit;
echo $return=json_encode($jar);
?>
