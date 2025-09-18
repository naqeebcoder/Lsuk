<?php $query="SELECT telephone.*,invoice.dated, interpreter_reg.name,comp_reg.name as orgzName,comp_reg.abrv,comp_reg.id as comp_id,comp_reg.email ,comp_reg.invEmail,comp_reg.buildingName as c_buildingName,comp_reg.line1 as c_line1,comp_reg.line2 as c_line2,comp_reg.streetRoad as c_streetRoad,comp_reg.city as c_city,comp_reg.postCode as c_postCode,comp_reg.payment_terms as c_payment_terms 
FROM telephone
INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
INNER JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv
 where multInv_flag=0 and $table.id=$invoice_id";
$result = mysqli_query($con,$query);
$row = mysqli_fetch_assoc($result);
  $assignDate=$row['assignDate'];
  $assignTime=$row['assignTime'];
  $source=$row['source'];
  $target = $row['target'];
  $comunic = $row['comunic'];
  $communication_type = $acttObj->read_specific("c_title", "comunic_types", "c_id=" . $comunic)['c_title'];
  $communication_type && $communication_type != "Other" ? $communication_type : "Telephone";
  $orgzName=$row['orgzName'];
  $comp_id = $row['comp_id'];
  $assignCity=$row['assignCity'];
  $street=$row['street'];
  $inchCity=$row['inchCity'];
  $intrpName=$row['name'];
  $buildingName=$row['buildingName'];
  $inchRoad=$row['inchRoad'];
  $invoiceNo=$row['invoiceNo'];
  $credit_note=!empty($row['credit_note'])?"credit_note_".$row['credit_note']:"";
  $line1=$row['line1'];
  $line2=$row['line2'];
  $c_buildingName=$row['c_buildingName'];
  $c_line1=$row['c_line1'];
  $c_line2=$row['c_line2'];
  $c_streetRoad=$row['c_streetRoad'];
  $c_city=$row['c_city'];
  $c_postCode=$row['c_postCode'];
  $c_payment_terms=$row['c_payment_terms'];
  $inchNo=$row['inchNo'];
  $nameRef=$row['nameRef'];
  $inchEmail=$row['inchEmail'];
  $orgContact=$row['orgContact'];
  $coEmail=$row['email'];
  $makCoEmail = $row['invEmail'];
  if($row['new_comp_id']!=0 && $row['order_company_id'] == 410){
      $private_company=$acttObj->read_specific("*","private_company","id=".$row['new_comp_id']);
      $orgzName = $private_company['name'];
      $orgContact = $private_company['orgContact'];
      $inchPerson = $private_company['inchPerson'];
      $inchContact = $private_company['inchContact'];
      $c_buildingName=$private_company['inchNo'];
      $c_line1=$private_company['line1'];
      $c_line2=$private_company['line2'];
      $c_streetRoad=$private_company['inchRoad'];
      $c_city=$private_company['inchCity'];
      $inchCity = $private_company['inchCity'];
      $c_postCode=$private_company['inchPcode'];
      $inchNo = $private_company['inchNo'];
      $inchEmail = $private_company['inchEmail'];
      $inchEmail2 = $private_company['inchEmail2'];
      $coEmail = $private_company['inchEmail'];
      $makCoEmail = $private_company['inchEmail'];
  }
  $hoursWorkd=$row['C_hoursWorkd'];
  $calCharges=$row['calCharges'];
  $C_otherCharges=$row['C_otherCharges'];
  $chargInterp=$row['C_chargInterp'];
  $rateHour=$row['C_rateHour'];
  $dueDate=$row['dueDate'];
  $dated=date_format(date_create($row['dated']), 'd-m-Y');
  $inchPcode=$row['inchPcode'];
  $company_rate_id = $row['company_rate_id'];
  $company_rate_data = !empty($row['company_rate_data']) ? (array) json_decode($row['company_rate_data']) : array();
  if (!empty($company_rate_data['title'])) {
      $extra_title_parts = explode("-", $company_rate_data['title']);
      $bookinType = trim($extra_title_parts[0]);
  } else {
      $bookinType = $row['bookinType'];
  }
  $orgRef=$row['orgRef'];
  $C_admnchargs=$row['C_admnchargs'];
  $porder=$row['porder'];
  $C_comments=$row['C_comments'];
  $commit=$row['commit']; 
  $invoic_date=@$row['invoic_date'];
  $abrv=@$row['abrv'];
  $cur_vat=@$row['cur_vat'];
  $orderCancelatoin = $row['orderCancelatoin'];
  $order_cancel_flag = $row['order_cancel_flag'];
  $cn_t_id = $row['cn_t_id'];
  $cn_r_id = $row['cn_r_id'];
  $cn_date = $row['cn_date'];
  $append_invoiceNo='';
  if(!empty($row['credit_note'])){
    $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$invoice_id." and order_type='tp'")['counter'];
  }
  $order_cancel_remarks = $row['order_cancel_remarks'];
  //$write_cancel_type=str_replace("[DATE]", $cn_date,$acttObj->read_specific("cd_title","cancellation_drops","cd_id=".$row['cn_t_id'])['cd_title']);
  //$write_cancel_remarks=$cn_t_id==6?$order_cancel_remarks:$acttObj->read_specific("cr_title","cancel_reasons","cr_id=".$cn_r_id)['cr_title'];
if($orderCancelatoin==1 || $order_cancel_flag==1){
$write_cancellation = '<span style="margin-left:10px">Cancellation Type:  '.$write_cancel_type.'</span><br><br><span style="margin-left:10px">Cancellation Reason:  '.$write_cancel_remarks.'</span><br><br>';
}
  $g_row=$row;

$g_row['intrpName']=$intrpName;

$g_row["_totalhoursworked"]=$misc->numberFormat_fun(@$hoursWorkd);
$g_row["_totalhourspaid"]=$misc->numberFormat_fun(@$rateHour * @$hoursWorkd);
$g_row["_rateHour"]=$misc->numberFormat_fun(@$rateHour);
$g_row["_travelMile"]=$misc->numberFormat_fun(@$travelMile);


$total2=@$travelTimeHour*@$travelTimeRate;

$total4=$misc->numberFormat_fun(@$rateMile * @$travelMile);
$g_row["_total4"]=$total4;

$total1=0;
$total5=$misc->numberFormat_fun(@$total1+@$total2+@$total4+@$C_admnchargs);
$g_row["_total5"]=$total5;

$vat=$misc->numberFormat_fun(@$total5 * $cur_vat);
$g_row["_vat"]=$vat;

$g_row["_travelTimeHour"]=$misc->numberFormat_fun(@$travelTimeHour);
$g_row["_travelTimeCost"]=$misc->numberFormat_fun(@$travelTimeRate * @$travelTimeHour);
$g_row["_C_otherexpns"]=$misc->numberFormat_fun(@$C_otherexpns);
$g_row["_vattotalexp"]=$misc->numberFormat_fun(@$vat+@$total5+@$C_otherexpns);

if(@$invoic_date=='0000-00-00')
{ 
  $invoiceDated=$misc->dated(date("Y-m-d"));
}
else{ 
  $invoiceDated=$misc->dated(@$invoic_date);
}
$g_row["_invoiceDated"]=$invoiceDated;


if($C_comments)
{
  $_comments=@$C_comments;
}else
{
  $_comments='Nil';
}
$g_row["_comments"]=$_comments;

if($orderCancelatoin==1 || $order_cancel_flag==1){
$g_row["_write_cancel_type"] = 'Cancellation Type : '.$write_cancel_type;
$g_row["_write_cancel_remarks"] = 'Cancellation Reason : '.$write_cancel_remarks;
}else{
    $g_row["_write_cancel_type"] = '';
    $g_row["_write_cancel_remarks"] = '';
}

$g_row["_assignDate"]=$misc->dated(@$assignDate);
$pay_terms = "+".@$c_payment_terms." days";
$g_row["_15DaysTime"]=$misc->dated(date("Y-m-d", strtotime($pay_terms)));
$g_row["_otherexpns"]=$misc->numberFormat_fun(@$C_otherexpns);
$g_row["_dueDate"]=$misc->dated($dueDate);
//@@
$g_row["_rateHour"]=$misc->numberFormat_fun(@$rateHour);
$g_row["_hoursWorkd"]=$misc->numberFormat_fun($hoursWorkd);
$g_row["_calCharges"]=$misc->numberFormat_fun($calCharges);
$g_row["_hourpayable"]=$misc->numberFormat_fun($rateHour * $hoursWorkd);
$g_row["_C_otherCharges"]=$misc->numberFormat_fun($C_otherCharges);
//$g_row["_C_admnchargs"]$misc->numberFormat_fun($C_admnchargs);
$g_row["_C_admnchargs"]=$misc->numberFormat_fun(@$C_admnchargs);
//Code Added by Solworx to reverse the number format function 
$makCalCharges = floatval(preg_replace('/[^\d.]/', '',$calCharges));
$makOtherCharges = floatval(preg_replace('/[^\d.]/', '',$C_otherCharges));
$makrateHour = floatval(preg_replace('/[^\d.]/', '',$rateHour));
$makHoursWorkd = floatval(preg_replace('/[^\d.]/', '',$hoursWorkd));

$g_row["_totplusadmin"]=$sub_total=$misc->numberFormat_fun($makCalCharges + $makOtherCharges + ($makrateHour * $makHoursWorkd)+$C_admnchargs);
//Code Added by Solworx to reverse the number format function 
$makSubTotal = floatval(preg_replace('/[^\d.]/', '',$sub_total));
$g_row["_vatsubtotal"]=$vat=$misc->numberFormat_fun($makSubTotal * $cur_vat);
//Code Added by Solworx to reverse the number format function 
$makVat = floatval(preg_replace('/[^\d.]/', '',$vat));
$g_row["_vattotalnow"]=$misc->numberFormat_fun( $makSubTotal + $makVat);
$crednoted = $g_row["credit_note"];
$bCredNoted = false;
if (isset($crednoted) && $crednoted != "") {
    $bCredNoted = true;
}
$g_row['invoiceNo']=$invoiceNo.$append_invoiceNo;
$g_row['credit_note']=$credit_note;