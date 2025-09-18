<?php $query = "SELECT interpreter.*,invoice.dated, interpreter_reg.name,
    comp_reg.name as orgzName,comp_reg.abrv,comp_reg.id as comp_id,comp_reg.email,comp_reg.invEmail,comp_reg.buildingName as c_buildingName,comp_reg.line1 as c_line1,comp_reg.line2 as c_line2,comp_reg.streetRoad as c_streetRoad,comp_reg.city as c_city,comp_reg.postCode as c_postCode
FROM interpreter
INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
INNER JOIN interpreter_reg ON interpreter.intrpName=interpreter_reg.id
INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv
where multInv_flag=0 and interpreter.id=$invoice_id";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
    $assignDate = $row['assignDate'];
    $source = $row['source'];
    $buildingName = $row['buildingName'];
    $street = $row['street'];
    $assignCity = $row['assignCity'];
    $postCode = $row['postCode'];
    $nameRef = $row['nameRef'];
    $orgzName = $row['orgzName'];
    $comp_id = $row['comp_id'];
    $inchCity = $row['inchCity'];
    $inchPcode = $row['inchPcode'];
    $invoiceNo = $row['invoiceNo'];
    $credit_note=!empty($row['credit_note'])?$acttObj->read_specific("CONCAT(DATE_FORMAT(dated,'%y%m'),'_',LPAD(id, 3, '0')) as credit_note_no","credit_notes","id=".$row['credit_note'])['credit_note_no']:"";
    $intrpName = $row['name'];
    $inchEmail = $row['inchEmail'];
    $inchRoad = $row['inchRoad'];
    $line1 = $row['line1'];
    $line2 = $row['line2'];
    $c_buildingName=$row['c_buildingName'];
    $c_line1=$row['c_line1'];
    $c_line2=$row['c_line2'];
    $c_streetRoad=$row['c_streetRoad'];
    $c_city=$row['c_city'];
    $c_postCode=$row['c_postCode'];
    $inchNo = $row['inchNo'];
    $orgContact = $row['orgContact'];
    $coEmail = $row['email'];
    $makCoEmail = $row['invEmail'];
    if($row['new_comp_id']!=0){
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
    $hoursWorkd = $row['C_hoursWorkd'];
    $chargInterp = $row['C_chargInterp'];
    $rateHour = $row['C_rateHour'];
    $travelMile = $row['C_travelMile'];
    $rateMile = $row['C_rateMile'];
    $chargeTravel = $row['C_chargeTravel'];
    $travelCost = $row['C_travelCost'];
    $otherCost = $row['C_otherCost'];
    $travelTimeHour = $row['C_travelTimeHour'];
    $travelTimeRate = $row['C_travelTimeRate'];
    $chargeTravelTime = $row['C_chargeTravelTime'];
    $C_admnchargs = $row['C_admnchargs'];
    $C_otherexpns = $row['C_otherexpns'];
    $cur_vat = @$row['cur_vat'];
    $dueDate = $row['dueDate'];
    $dated = $row['dated'];
    $company_rate_id = $row['company_rate_id'];
    $company_rate_data = !empty($row['company_rate_data']) ? (array) json_decode($row['company_rate_data']) : array();
    if (!empty($company_rate_data['title'])) {
        $extra_title_parts = explode("-", $company_rate_data['title']);
        $bookinType = trim($extra_title_parts[0]);
    } else {
        $bookinType = $row['bookinType'];
    }
    $orgRef = $row['orgRef'];
    $porder = $row['porder'];
    $C_comments = $row['C_comments'];
    $commit = $row['commit'];
    $invoic_date = @$row['invoic_date'];
    $abrv = @$row['abrv'];
    $orderCancelatoin = $row['orderCancelatoin'];
    $order_cancel_flag = $row['order_cancel_flag'];
    $cn_t_id = $row['cn_t_id'];
    $cn_r_id = $row['cn_r_id'];
    $cn_date = $row['cn_date'];
    $append_invoiceNo='';
    if(!empty($row['credit_note'])){
      $append_invoiceNo="-0".$acttObj->read_specific("count(*) as counter","credit_notes","order_id=".$invoice_id." and order_type='f2f'")['counter'];
    }
    $order_cancel_remarks = $row['order_cancel_remarks'];
    $write_cancel_type=str_replace("[DATE]", $cn_date,$acttObj->read_specific("cd_title","cancellation_drops","cd_id=".$row['cn_t_id'])['cd_title']);
    $write_cancel_remarks=$cn_t_id==6?$order_cancel_remarks:$acttObj->read_specific("cr_title","cancel_reasons","cr_id=".$cn_r_id)['cr_title'];
if($orderCancelatoin==1 || $order_cancel_flag==1){
$write_cancellation = '<span style="margin-left:10px">Cancellation Type:  '.$write_cancel_type.'</span><br><br><span style="margin-left:10px">Cancellation Reason:  '.$write_cancel_remarks.'</span><br><br>';
}
//Check if credit note is made
if(!empty($credit_note)){
  $row_credit_note=$acttObj->read_specific("credit_notes.data","credit_notes","order_id=".$invoice_id." AND order_type='f2f' AND status=1");
  $row_credit=json_decode($row_credit_note['data'], true);
  /*foreach($row as $key=>$value){
    $$key=$value;
  }*/
  $hoursWorkd = $row_credit['C_hoursWorkd'];
  $chargInterp = $row_credit['C_chargInterp'];
  $rateHour = $row_credit['C_rateHour'];
  $travelMile = $row_credit['C_travelMile'];
  $rateMile = $row_credit['C_rateMile'];
  $chargeTravel = $row_credit['C_chargeTravel'];
  $travelCost = $row_credit['C_travelCost'];
  $otherCost = $row_credit['C_otherCost'];
  $travelTimeHour = $row_credit['C_travelTimeHour'];
  $travelTimeRate = $row_credit['C_travelTimeRate'];
  $chargeTravelTime = $row_credit['C_chargeTravelTime'];
  $C_admnchargs = $row_credit['C_admnchargs'];
  $C_otherexpns = $row_credit['C_otherexpns'];
  $cur_vat = @$row_credit['cur_vat'];
  //Row data update
  $row['hoursWorkd'] = $row_credit['C_hoursWorkd'];
  $row['chargInterp'] = $row_credit['C_chargInterp'];
  $row['rateHour'] = $row_credit['C_rateHour'];
  $row['travelMile'] = $row_credit['C_travelMile'];
  $row['rateMile'] = $row_credit['C_rateMile'];
  $row['chargeTravel'] = $row_credit['C_chargeTravel'];
  $row['travelCost'] = $row_credit['C_travelCost'];
  $row['otherCost'] = $row_credit['C_otherCost'];
  $row['travelTimeHour'] = $row_credit['C_travelTimeHour'];
  $row['travelTimeRate'] = $row_credit['C_travelTimeRate'];
  $row['chargeTravelTime'] = $row_credit['C_chargeTravelTime'];
  $row['C_admnchargs'] = $row_credit['C_admnchargs'];
  $row['C_otherexpns'] = $row_credit['C_otherexpns'];
  $row['cur_vat'] = @$row_credit['cur_vat'];
}
$g_row = $row;
$g_row['intrpName'] = @$intrpName;

$g_row["_totalhoursworked"] = $misc->numberFormat_fun(@$hoursWorkd);
$g_row["_totalhourspaid"] = $misc->numberFormat_fun(@$rateHour * @$hoursWorkd);
$g_row["_rateHour"] = $misc->numberFormat_fun(@$rateHour);
$g_row["_travelMile"] = $misc->numberFormat_fun(@$travelMile);

$g_row["_C_admnchargs"] = $misc->numberFormat_fun(@$C_admnchargs);

$total2 = @$travelTimeHour * @$travelTimeRate;

$total4 = $misc->numberFormat_fun(@$rateMile * @$travelMile);
$g_row["_total4"] = $total4;

$total1 = 0;
$total1 = @$rateHour * @$hoursWorkd;
$total5 = $misc->numberFormat_fun(@$total1+@$total2+@$total4+@$C_admnchargs);
$g_row["_total5"] = $total5;
$makTotal5 = floatval(preg_replace('/[^\d.]/', '', $total5));
$vat = $misc->numberFormat_fun(@$makTotal5 * @$cur_vat);
$g_row["_vat"] = $vat;

$g_row["_travelTimeHour"] = $misc->numberFormat_fun(@$travelTimeHour);
$g_row["_travelTimeCost"] = $misc->numberFormat_fun(@$travelTimeRate * @$travelTimeHour);
$g_row["_C_otherexpns"] = $misc->numberFormat_fun(@$C_otherexpns);
$makVat = floatval(preg_replace('/[^\d.]/', '', $vat));
$g_row["_vattotalexp"] = $misc->numberFormat_fun(@$makVat+@$makTotal5+@$C_otherexpns);

if (@$invoic_date == '0000-00-00') {
    $invoiceDated = $misc->dated(date("Y-m-d"));
} else {
    $invoiceDated = $misc->dated(@$invoic_date);
}
$g_row["_invoiceDated"] = $invoiceDated;

if (@$C_comments) {
    $_comments = @$C_comments;
} else {
    $_comments = 'Nil';
}
$g_row["_comments"] = $_comments;
if($orderCancelatoin==1 || $order_cancel_flag==1){
$g_row["_write_cancel_type"] = 'Cancellation Type : '.$write_cancel_type;
$g_row["_write_cancel_remarks"] = 'Cancellation Reason : '.$write_cancel_remarks;
}else{
    $g_row["_write_cancel_type"] = '';
    $g_row["_write_cancel_remarks"] = '';
}
$g_row["_assignDate"] = $misc->dated(@$assignDate);
$g_row["_15DaysTime"] = $misc->dated(date("Y-m-d", strtotime("+15 days")));
$g_row["_otherexpns"] = $misc->numberFormat_fun(@$C_otherexpns);
$crednoted = $g_row["credit_note"];
$bCredNoted = false;
if (isset($crednoted) && $crednoted != "") {
    $bCredNoted = true;
}
$g_row['invoiceNo']=$invoiceNo.$append_invoiceNo;
$g_row['credit_note']=$credit_note;