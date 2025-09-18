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
$jar['bizcreditlimit']=$bizcreditlimit;
echo $return=json_encode($jar);
?>
