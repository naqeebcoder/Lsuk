<?php
// include "userhaspage.php";
// SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}

include 'db.php';
include 'class.php';
include_once 'function.php';
$assignDate = @$_GET['assignDate'];
$interp = @$_GET['interp'];
$org = @$_GET['org'];
$job = @$_GET['job'];
$inov = @$_GET['inov'];
$type = @$_GET['type'];
$tp = @$_GET['tp'];
$aT = $_GET['aT'];
$string=$_GET['str'];
if(!empty($assignDate) && empty($aT)){
    $bg_aD=' style="background: #ffff0075;"';
    $bg_aT='';
    $bg_both='';
}else if(empty($assignDate) && !empty($aT)){
    $bg_aT=' style="background: #ffff0075;"';
    $bg_aD='';
    $bg_both='';
}else if(!empty($assignDate) && !empty($aT)){
    $bg_both=' style="background: #ffff0075;"';
    $bg_aD='';
    $bg_aT='';
}else{
    $bg_aD='';
    $bg_aT='';
    $bg_both='';
}
// Set query attributes according to job types
$deleted_flag=$tp=='tr'?'deleted_flag = 1':'deleted_flag = 0';
$order_cancel_flag=$tp=='c'?'order_cancel_flag = 1':'order_cancel_flag = 0';
$multInv_flag=$tp=='ml'?'multInv_flag=1':'multInv_flag=0';
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;
$page_count=$startpoint;
$array_tp=array('a'=>'Active','tr'=>'Trashed','c'=>'Cancelled','ml'=>'Multi Invoice');
// Set page title according to job types
$title=$array_tp[$tp]=='Active'?'':$array_tp[$tp];
$title_type=$type=='Interpreter'?'Face To Face':$type;
if($tp=='tr'){ $class='label-danger'; }else if($tp=='c'){ $class='label-warning'; }else{ $class='label-primary'; }
//If Operator is logged in
if($_SESSION['prv']=='Operator' || $_SESSION['prv']=='Finance'){
    if($_SESSION['prv']=='Finance'){
        $f2f_append='';
        $tp_append='';
        $tr_append='';
        // $f2f_append='and interpreter.hoursWorkd<>0';
        // $tp_append='and telephone.hoursWorkd<>0';
        // $tr_append='and translation.numberUnit<>0';
    }else{
        $f2f_append='and interpreter.orderCancelatoin=0 and interpreter.hoursWorkd=0';
        $tp_append='and telephone.orderCancelatoin=0 and telephone.hoursWorkd=0';
        $tr_append='and translation.orderCancelatoin=0 and translation.numberUnit=0';
    }
    $deleted_flag='deleted_flag = 0';
    $order_cancel_flag='order_cancel_flag = 0';
    $multInv_flag='multInv_flag=0';
    $title='';$class='label-primary';
    $tp='a';
}
?>
<!doctype html>
<html lang="en">
<head>
<title><?php echo empty($type)?'All '.$title_type.' '.$title.' jobs list':$title_type.' '.$title.' list' ?></title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>html,body{background:white !important;}.action_buttons .w3-button{padding: 7px 11px;}.dropdown_actions2  .dropdown-menu{left: auto;
    right: 0;}.action_buttons .fa,.action_buttons2 .fa{font-size: 16px;} .w3-ul li {border-bottom: none;}.dropdown_actions a,.dropdown_actions2 a{padding:2px 4px!important}.dropdown_actions .dropdown-menu{width: max-content;padding: 7px 7px 0px 2px;bottom: -4px !important;top: auto;right: 64px !important;left: auto;}.dropdown_actions,.dropdown_actions2{display: inline-block;}.lbl{border-radius: 0px!important;margin: -9px -8px!important;font-size: 12px;bottom: 0;right: 0;position: absolute;}.p3{padding:3px;}.w3-ul li {margin: -6px -25px;}.multiselect{min-width: 190px;}.multiselect-container{max-height: 400px;overflow-y: auto;max-width: 380px;}
.tab_container{min-height:700px;}.w3-small{padding: 1px 5px!important;margin-top: -6px!important;}.badge-counter{border-radius: 0px!important;margin: -9px -9px!important;font-size: 10px;float: left;}.tablesorter thead tr {background: none;}.mt15{margin-top: 15px;}.w3-hoverable tbody tr:hover{background-color: #2196f30d!important;}</style>
</head>
<?php include 'header.php';?>
<body>
<script>
function myFunction() {
   var o = document.getElementById("inov").value;if(!o){o="<?php echo $inov; ?>";}
   var w = document.getElementById("assignDate").value;if(!w){w="<?php echo $assignDate; ?>";}
   var aT = document.getElementById("aT").value;if(!aT){aT="<?php echo $aT; ?>";}
   var x = document.getElementById("interp").value;if(!x){x="<?php echo $interp; ?>";}
   var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
   var z = document.getElementById("job").value;if(!z){z="<?php echo $job; ?>";}
   var type = document.getElementById("type").value;if(!type){type="<?php echo $type; ?>";}
   var tp = document.getElementById("tp").value;if(!tp){tp="<?php echo $tp; ?>";}
   window.location.href="<?php echo basename(__FILE__);?>" + '?interp=' + x + '&org=' + y + '&job=' + z + '&assignDate=' + w + '&aT=' + aT  + '&inov=' + o+ '&type=' + type+ '&tp=' + tp;
}
function runtime_search() {
   var string = document.getElementById("search").value;
   if(!search){search="<?php echo $string; ?>";}
   window.location.href="<?php echo basename(__FILE__);?>" + '?str=' + string;
}
</script>
<?php include 'nav2.php';?>
<section class="container-fluid" style="overflow-x:auto">
<div class="">
    <header>
      <h2 class="col-md-6 text-center">
          <div class="label <?php echo $class;?>"><a class="w3-text-white" href="<?php echo basename(__FILE__);?>"><?php echo empty($type)?'All '.$title_type.' '.$title.' jobs list':$title_type.' '.$title.' list' ?></a></div></h2>
          <?php if (!empty($type) && $type=="Telephone") {?>
          <div class="form-group col-md-2 col-sm-4 mt15">
        <?php if (!empty($type) && $type=='Telephone') {
                    $sql_opt_aT = "SELECT distinct telephone.assignTime FROM telephone WHERE telephone.assignDate LIKE '$assignDate%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag ORDER BY assignTime";
                }?>
  <select name="aT" id="aT" onChange="myFunction()" class="form-control">
    <?php $result_opt_aT = mysqli_query($con, $sql_opt_aT);
$options_aT = "";
while ($row_opt_aT = mysqli_fetch_array($result_opt_aT)) {
    $name_opt_aT = $row_opt_aT["assignTime"];
    $options_aT .= "<option>" . $name_opt_aT.'</option>';}
?>
    <?php if (!empty($aT)) {?>
    <option><?php echo $aT; ?></option>
    <?php } else {?>
    <option value="" selected>Select Time</option>
    <?php }?>
    <?php echo $options_aT; ?>
  </select>
            </div>
            <?php }else{ ?>
          <div class="form-group col-md-2 col-sm-4 pull-right <?php if($_SESSION['prv']=="Management"){echo 'mt15';} ?>" <?php if($_SESSION['prv']=="Management"){echo 'style="height: 34px;"';} ?>>
            <input type="hidden" id="aT" name="aT" value="">
            </div>
            <?php } ?>
          <div class="form-group col-md-2 col-sm-4 mt15">
        <select id="type" onChange="myFunction()" name="type" class="form-control">
                <?php if (!empty($type)) {?>
                    <option value="<?php echo $type; ?>" selected><?php echo $type; ?></option>
                    <?php }?>
                    <option value="" disabled <?php if (empty($type)) { echo 'selected'; } ?>>Filter Job Type</option>
                <option value="Interpreter">Interpreter</option>
                <option value="Telephone">Telephone</option>
                <option value="Translation">Translation</option>
            </select>
            </div>
          <div class="form-group col-md-2 col-sm-4 <?php if($_SESSION['prv']=="Management"){echo 'mt15';} ?>">
              <?php if($_SESSION['prv']=='Management'){ ?>
        <select id="tp" onChange="myFunction()" name="tp" class="form-control">
                <?php
                if (!empty($tp)) {?>
                    <option value="<?php echo key($array_tp[$tp]);?>" selected><?php echo $array_tp[$tp]; ?></option>
                    <?php }?>
                    <option value="" disabled <?php if (empty($tp)) { echo 'selected' ; }?>>Filter by Type</option>
                <option value="a">Active</option>
                <option value="tr">Trashed</option>
                <option value="c">Cancelled</option>
                <option value="ml">Multi Invoice</option>
            </select>
            <?php }else{ ?>
            <input type="hidden" value='' id="tp" onChange="myFunction()" name="tp" class="form-control"/>
            <?php } ?>
            </div>
          <div class="form-group col-md-2 col-sm-4">
          <input type="text" name="search" id="search" class="form-control" placeholder="Search ..." onChange="runtime_search()" value="<?php echo $string; ?>"/> 
          </div>
             <div class="form-group col-md-2 col-sm-4">
          <input type="text" name="inov" id="inov" class="form-control" placeholder="Invoice No" onChange="myFunction()" value="<?php echo $inov; ?>"/>
          </div>
          <div class="form-group col-md-2 col-sm-4">
              
<?php 

if (!empty($type) && $type=='Interpreter') {
$sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag,
interpreter.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,interpreter,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$deleted_flag and interpreter.$order_cancel_flag and interpreter.$multInv_flag and interpreter.commit=0 and interpreter.assignDate like '$assignDate%' and (interpreter.orgName like '%$_words%')) as grp  ORDER BY name ASC";    
}else if (!empty($type) && $type=='Telephone') {
$sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag,
telephone.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,telephone,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$deleted_flag and telephone.$order_cancel_flag and telephone.$multInv_flag and telephone.commit=0 and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%' and (telephone.orgName like '%$_words%')) as grp  ORDER BY name ASC";
}else if (!empty($type) && $type=='Translation') {
$sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag,
translation.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,translation,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$deleted_flag and translation.$order_cancel_flag and translation.$multInv_flag and translation.commit=0 and translation.asignDate like '$assignDate%' and (translation.orgName like '%$_words%')) as grp  ORDER BY name ASC";
}else{
 $sql_opt = "SELECT DISTINCT name,id,gender,city from (SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag,
interpreter.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,interpreter,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$deleted_flag and interpreter.$order_cancel_flag and interpreter.$multInv_flag and interpreter.commit=0 and interpreter.assignDate like '$assignDate%' and (interpreter.orgName like '%$_words%')
               UNION ALL SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag,
telephone.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,telephone,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$deleted_flag and telephone.$order_cancel_flag and telephone.$multInv_flag and telephone.commit=0 and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%' and (telephone.orgName like '%$_words%')
               UNION ALL SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag,
translation.porder,comp_reg.po_req FROM interpreter_reg,interp_lang,translation,comp_reg WHERE interpreter_reg.code=interp_lang.code
AND translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$deleted_flag and translation.$order_cancel_flag and translation.$multInv_flag and translation.commit=0 and translation.asignDate like '$assignDate%' and (translation.orgName like '%$_words%')) as grp  ORDER BY name ASC";   
}
?>
        <select id="interp" onChange="myFunction()" name="interp" class="form-control searchable">
          <?php
$result_opt = mysqli_query($con, $sql_opt);
$options_int = "";
while ($row_opt = mysqli_fetch_array($result_opt)) {
    $code = $row_opt["name"];
    $name_opt = $row_opt["name"];
    $city_opt = $row_opt["city"];
    $gender = $row_opt["gender"];
    $options_int .= "<OPTION value='$code'>" . $name_opt . ' (' . $gender . ')' . ' (' . $city_opt . ')';}
?>
          <?php if (!empty($interp)) {?>
          <option><?php echo $interp; ?></option>
          <?php } else {?>
          <option value="">Select Interpreter</option>
          <?php }?>
          <?php echo $options_int; ?>
          </option>
          </select>
          </div>
          <div class="form-group col-md-2 col-sm-4">
              <?php 
                if (!empty($type) && $type=='Interpreter') {
                    $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter WHERE interpreter.orgName=comp_reg.abrv AND interpreter.$deleted_flag AND interpreter.$order_cancel_flag AND interpreter.$multInv_flag and interpreter.commit=0 and interpreter.assignDate like '$assignDate%' ) as grp 
                                ORDER BY name ASC";
                }else if (!empty($type) && $type=='Telephone') {
                    $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone WHERE telephone.orgName=comp_reg.abrv AND telephone.$deleted_flag AND telephone.$order_cancel_flag AND telephone.$multInv_flag and telephone.commit=0 and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%' ) as grp 
                                ORDER BY name ASC";
                }else if (!empty($type) && $type=='Translation') {
                    $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation WHERE translation.orgName=comp_reg.abrv AND translation.$deleted_flag AND translation.$order_cancel_flag AND translation.$multInv_flag and translation.commit=0 and translation.asignDate like '$assignDate%' ) as grp 
                                ORDER BY name ASC";
                }else{
                    $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter WHERE interpreter.orgName=comp_reg.abrv AND interpreter.$deleted_flag AND interpreter.$order_cancel_flag AND interpreter.$multInv_flag and interpreter.commit=0 and interpreter.assignDate like '$assignDate%' 
                                UNION SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone WHERE telephone.orgName=comp_reg.abrv AND telephone.$deleted_flag AND telephone.$order_cancel_flag AND telephone.$multInv_flag and telephone.commit=0 and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%'  
                                UNION SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation WHERE translation.orgName=comp_reg.abrv AND translation.$deleted_flag AND translation.$order_cancel_flag AND translation.$multInv_flag and translation.commit=0 and translation.asignDate like '$assignDate%'  ) as grp 
                                ORDER BY name ASC";
                };
              ?>
  <select id="org" name="org" onChange="myFunction()" class="form-control searchable">
    <?php $result_opt = mysqli_query($con, $sql_opt);
$options = "";
while ($row_opt = mysqli_fetch_array($result_opt)) {
    $code = $row_opt["abrv"];
    $name_opt = $row_opt["name"];
    $options .= "<OPTION value='$code'>" . $name_opt . ' (' . $code . ')';}
?>
    <?php if (!empty($org)) {?>
    <option><?php echo $org; ?></option>
    <?php } else {?>
    <option value="">Select Company</option>
    <?php }?>
    <?php echo $options; ?>
    </option>
  </select>
          </div>
          <div class="form-group col-md-2 col-sm-4">
              <?php if (!empty($type) && $type=='Interpreter') {
                    $sql_opt = "SELECT distinct lang.lang FROM lang,interpreter WHERE interpreter.source=lang.lang and interpreter.assignDate LIKE '$assignDate%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $f2f_append ORDER BY lang ASC";
                }else if (!empty($type) && $type=='Telephone') {
                    $sql_opt = "SELECT distinct lang.lang FROM lang,telephone WHERE telephone.source=lang.lang and telephone.assignDate LIKE '$assignDate%' and telephone.assignTime like '$aT%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $tp_append ORDER BY lang ASC";
                }else if (!empty($type) && $type=='Translation') {
                    $sql_opt = "SELECT distinct lang.lang FROM lang,translation WHERE translation.source=lang.lang and translation.asignDate LIKE '$assignDate%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $tr_append ORDER BY lang ASC";
                }else{
                  $sql_opt = "SELECT DISTINCT lang from (SELECT distinct lang.lang,interpreter.multInv_flag,interpreter.commit,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.assignDate,interpreter.assignTime FROM lang,interpreter WHERE interpreter.source=lang.lang and interpreter.assignDate LIKE '$assignDate%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $f2f_append
               UNION ALL SELECT distinct lang.lang,telephone.multInv_flag,telephone.commit,telephone.deleted_flag,telephone.order_cancel_flag,telephone.assignDate,telephone.assignTime FROM lang,telephone WHERE telephone.source=lang.lang and telephone.assignDate LIKE '$assignDate%' and telephone.assignTime like '$aT%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $tp_append
               UNION ALL SELECT distinct lang.lang,translation.multInv_flag,translation.commit,translation.deleted_flag,translation.order_cancel_flag,translation.asignDate as assignDate,'00:00:00' as 'assignTime' FROM lang,translation WHERE translation.source=lang.lang and translation.asignDate LIKE '$assignDate%' and $multInv_flag and commit=0 and $deleted_flag and $order_cancel_flag $tr_append) as grp  ORDER BY lang ASC";  
                } ?>
  <select name="job" id="job" onChange="myFunction()" class="form-control searchable">
    <?php $result_opt = mysqli_query($con, $sql_opt);
$options = "";
while ($row_opt = mysqli_fetch_array($result_opt)) {
    $code = $row_opt["lang"];
    $name_opt = $row_opt["lang"];
    $options .= "<OPTION value='$code'>" . $name_opt;}
?>
    <?php if (!empty($job)) {?>
    <option><?php echo $job; ?></option>
    <?php } else {?>
    <option value="">Language</option>
    <?php }?>
    <?php echo $options; ?>
    </option>
  </select>
          </div>
          <div class="form-group col-md-2 col-sm-4">
  <input type="date" name="assignDate" id="assignDate" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $assignDate; ?>"/>
          </div>
    </header>
<?php $arr = explode(',', $org);
$_words = implode("' OR orgName like '", $arr);
$arr_intrp = explode(',', $interp);
$_words_intrp = implode("' OR name like '", $arr_intrp);?>
      <?php $table = '';
if (!empty($type) && $type=='Interpreter') {
                    $query =
    "SELECT * from (SELECT '' as comunic,interpreter.orderCancelatoin,interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.bookinType,interpreter.time_sheet,interpreter.jobDisp,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter_reg.id as int_id,interpreter.source,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$multInv_flag AND interpreter.$deleted_flag and interpreter.$order_cancel_flag and interpreter.commit=0  $f2f_append and interpreter.assignDate like '$assignDate%' and interpreter.source like '%$job%' and interpreter_reg.name like '%$interp%' and interpreter.orgName like '%$org%' and interpreter.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                }else if (!empty($type) && $type=='Telephone') {
                    $query =
    "SELECT * from (SELECT telephone.comunic,telephone.orderCancelatoin,telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.bookinType,telephone.time_sheet,telephone.jobDisp,telephone.intrpName,telephone.orgName,interpreter_reg.name,interpreter_reg.id as int_id,telephone.source,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$multInv_flag AND telephone.$deleted_flag and telephone.$order_cancel_flag and telephone.commit=0 $tp_append  and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%' and telephone.source like '%$job%' and interpreter_reg.name like '%$interp%' and telephone.orgName like '%$org%' and telephone.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                }else if (!empty($type) && $type=='Translation') {
                    $query =
    "SELECT * from (SELECT '' as comunic,translation.orderCancelatoin,translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.bookinType,translation.time_sheet,translation.jobDisp,translation.intrpName,translation.orgName,interpreter_reg.name,interpreter_reg.id as int_id,translation.source,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$multInv_flag AND translation.$deleted_flag and translation.$order_cancel_flag and translation.commit=0 $tr_append  and translation.asignDate like '$assignDate%' and translation.source like '%$job%' and interpreter_reg.name like '%$interp%' and translation.orgName like '%$org%' and translation.invoiceNo like '%$inov%') as grp WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                }else{
                    if(isset($string) && !empty($string)){
                        $query =
    "SELECT * from (SELECT '' as comunic,interpreter.orderCancelatoin,interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.bookinType,interpreter.time_sheet,interpreter.jobDisp,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter_reg.id as int_id,interpreter.source,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$multInv_flag AND interpreter.$deleted_flag and interpreter.$order_cancel_flag and interpreter.commit=0 $f2f_append and (interpreter.orgRef like '%$string%' OR interpreter.porder like '%$string%' OR interpreter.nameRef like '%$string%' OR interpreter.invoiceNo like '%$string%')
               UNION ALL SELECT telephone.comunic,telephone.orderCancelatoin,telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.bookinType,telephone.time_sheet,telephone.jobDisp,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,interpreter_reg.id as int_id,telephone.source,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$multInv_flag AND telephone.$deleted_flag and telephone.$order_cancel_flag and telephone.commit=0 $tp_append and (telephone.orgRef like '%$string%' OR telephone.porder like '%$string%' OR telephone.nameRef like '%$string%' OR telephone.invoiceNo like '%$string%')
               UNION ALL SELECT '' as comunic,translation.orderCancelatoin,translation.porder,comp_reg.po_req,'Translation' as type,translation.bookinType,translation.time_sheet,translation.jobDisp,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,interpreter_reg.id as int_id,translation.source,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$multInv_flag AND translation.$deleted_flag and translation.$order_cancel_flag and translation.commit=0 $tr_append and (translation.orgRef like '%$string%' OR translation.porder like '%$string%' OR translation.nameRef like '%$string%' OR translation.invoiceNo like '%$string%')) as grp ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                    }else{
                        $query =
    "SELECT * from (SELECT '' as comunic,interpreter.orderCancelatoin,interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.bookinType,interpreter.time_sheet,interpreter.jobDisp,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter_reg.id as int_id,interpreter.source,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.$multInv_flag AND interpreter.$deleted_flag and interpreter.$order_cancel_flag and interpreter.commit=0  $f2f_append and interpreter.assignDate like '$assignDate%' and interpreter.source like '%$job%' and interpreter_reg.name like '%$interp%' and interpreter.orgName like '%$org%' and interpreter.invoiceNo like '%$inov%'
               UNION ALL SELECT telephone.comunic,telephone.orderCancelatoin,telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.bookinType,telephone.time_sheet,telephone.jobDisp,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,interpreter_reg.id as int_id,telephone.source,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.$multInv_flag AND telephone.$deleted_flag and telephone.$order_cancel_flag and telephone.commit=0 $tp_append  and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%' and telephone.source like '%$job%' and interpreter_reg.name like '%$interp%' and telephone.orgName like '%$org%' and telephone.invoiceNo like '%$inov%'
               UNION ALL SELECT '' as comunic,translation.orderCancelatoin,translation.porder,comp_reg.po_req,'Translation' as type,translation.bookinType,translation.time_sheet,translation.jobDisp,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,interpreter_reg.id as int_id,translation.source,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.$multInv_flag AND translation.$deleted_flag and translation.$order_cancel_flag and translation.commit=0  $tr_append and translation.asignDate like '$assignDate%' and translation.source like '%$job%' and interpreter_reg.name like '%$interp%' and translation.orgName like '%$org%' and translation.invoiceNo like '%$inov%') as grp 
               
               WHERE type like '%$type%' ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT {$startpoint} , {$limit}";
                    }
                    
                }
            ?>
    <div class="tab_container" id="put_data">
       <center><div class="col-md-12"><?php echo pagination($con, $table, $query, $limit, $page); ?></div></center>
      <table class="table table-bordered table-hover tbl_data" cellspacing="0" cellpadding="0">
          <thead class="bg-primary">
        <tr>
            <td>Interpreter</td>
            <td>Language</td>
            <td>Assign-Date</td>
            <td>Company</td>
            <td>Contact Name</td>
            <td>Ref Name</td>
            <td>Booking Type</td>
            <td>Details</td>
            <td>Actions</td>
          </tr> 
      </thead>
      <tbody>
        <?php $result = mysqli_query($con, $query);
                if(mysqli_num_rows($result)==0){
                    echo '<tr>
                  <td colspan="9"><h4 class="text-danger text-center"><b>Sorry ! There are no records.</b></h4></td></tr>';
                }else{ 
while ($row = mysqli_fetch_array($result)) {
    if($row['type']=='Interpreter'){
        $edit_page='interp_edit.php';$exp_page='update_expenses2.php';$c_exp_page='co_update_expanses.php';
        $inv_page='invoice.php';$comp_earning='comp_earning.php';$credit_page='credit_interp.php';
        $mk_credit_page='comp_interp_credit_note.php';$history_page='interp_list_edited.php';
    }else if($row['type']=='Telephone'){
        $edit_page='telep_edit.php';$exp_page='telep_expanses.php';$c_exp_page='co_telep_expanses.php';
        $inv_page='telep_invoice.php';$comp_earning='comp_earning_telep.php';$credit_page='credit_telep.php';
        $mk_credit_page='comp_telep_credit_note.php';$history_page='telep_list_edited.php';
    }else{
        $edit_page='trans_edit.php';$exp_page='trans_update_expanses.php';$c_exp_page='co_trans_update_expanses.php';
        $inv_page='trans_invoice.php';$comp_earning='comp_earning_trans.php';$credit_page='credit_trans.php';
        $mk_credit_page='comp_trans_credit_note.php';$history_page='trans_list_edited.php';
    }
    $page_count++;?>
                <tr>
                  <td><?php echo '<span class="w3-badge w3-blue badge-counter">'.$page_count.'</span>'; ?><span style="cursor:pointer;" onClick="MM_openBrWindow('full_view_interpreter.php?view_id=<?php echo $row['int_id']; ?>','_blank','scrollbars=yes,resizable=yes,width=850,height=900,left=432,top=38')" class="w3-small"> <?php if($row['hoursWorkd'] == 0){  ?><span class="w3-text-red"><?php echo $row['name']; ?></span><?php }else{ ?><span class="w3-text-black" title="Interpreter Hours: <?php echo $row['hrsubmited'] . ' (' . $misc->dated($row['interp_hr_date']) . ')';?>"> <?php echo $row['name']; ?></span><?php } ?></span></td>
                  <td><span class="w3-medium"><?php echo '<span class="w3-medium  p3">'.$row['source'].'</span>'; ?></span></td>
                  <td><span><b <?php echo $bg_both; ?>><?php echo '<span '.$bg_aD.'>'.$row['assignDate'].'</span>'.' <span '.$bg_aT.'>'.$row['assignTime'].'</span>';?></b></span></td>
                  <td><?php if($row['C_hoursWorkd'] == 0){ ?><span class="w3-text-red"><?php echo $row['orgName']; ?></span><span class="w3-medium" style="font-weight:bold;margin-top:-10px;font-size:14px!important;"></span>
                              <?php }else{ ?><span class="w3-text-black" title="<?php echo $row['comp_hrsubmited'] . ' (' . $misc->dated($row['comp_hr_date']) . ')';?>"><?php echo $row['orgName']; ?></span><?php } ?>
                              </td>
                    <td><span><?php echo $row['orgContact'];?></span></td>
                    <td><span><?php echo $row['orgRef'];?></span></td>
                  <td><span><?php echo ucwords($row['bookinType'])?:'Nil';?></span></td>
                  <td><span><?php $get_type=$acttObj->read_specific("c_title,c_image","comunic_types","c_id=".$row['comunic']); echo $row['type']=="Telephone"?'<img data-toggle="popover" data-trigger="hover" data-placement="left" data-content="'.$get_type['c_title'].'" src="images/comunic_types/'.$get_type['c_image'].'" width="36"/> ':"";
                  if($row['po_req']==1 && $row['porder']!=''){ 
                  echo '<b title="Pur.Order is updated">'.$row['porder'].'</b>';
                  }else if($row['po_req']==1 && $row['porder']==''){
                  echo '<span class="w3-badge w3-red" data-content="Purchase Order No missing !" data-toggle="popover" data-trigger="hover" data-placement="left"><i class="fa fa-remove"></i></span>';
                  }else{
                  echo '';
                  }?></span>
                  <span title="<b>JOB SUBMISSIONS</b>" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" 
                      data-content="<?php echo 'Job submitted by:<br><b>'.$row['submited'] . ' (' . $misc->dated($row['dated']) . ')</b><br>Job allocated By:<br><b>'.ucwords($row['aloct_by']) . ' (' . $misc->dated($row['aloct_date']) . ')<b>';?>" class="w3-badge w3-blue">?</span>
                      <span title="<b>HOURS SUBMISSIONS</b>" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" 
                      data-content="<?php echo 'Interp Hrz submitted by:<br><b>'.$row['hrsubmited'] . ' (' . $misc->dated($row['interp_hr_date']) . ')</b><br>Comp Hrz submitted by:<br><b>'.$row['comp_hrsubmited'] . ' (' . $misc->dated($row['comp_hr_date']) . ')<b>';?>"><i class="fa fa-clock-o" style="font-size: 20px;"></i></span>
                      </td>

<?php
//##gotcreditnote
    if($row['type']=='Interpreter'){
        $totalforvat = $row['total_charges_comp'];
        $vatpay = $totalforvat * $row['cur_vat'];
        $totinvnow = $totalforvat + $vatpay + $row['C_otherexpns'];
    }else if($row['type']=='Telephone'){
        $totalforvat=$row['total_charges_comp'];
    $vatpay=$totalforvat*$row['cur_vat'];
    $totinvnow=$totalforvat+$vatpay;
    }else{
        $totalforvat=$row['total_charges_comp'];
    $vatpay=$totalforvat*$row['cur_vat'];
    $totinvnow=$totalforvat+$vatpay;
    }

    $gotcreditnote = false;
    if (isset($row['credit_note']) && $row['credit_note'] != "") {
        $totinvnow = 0;
        $gotcreditnote = true;
    }?>
    <td <?php if($tp=='tr'||$tp=='c'){echo "width='11%'";} if($_SESSION['prv']=='Operator'){echo "width='20%'";} ?>>
<div class="col-sm-12 action_buttons">
    <?php $get_ac=$acttObj->read_all("actions.id","actions,ac_permissions,routes","actions.page_id=routes.id and ac_permissions.action_id=actions.id and ac_permissions.role_id=1830");
    $actions=array();
    while($row_ac=$get_ac->fetch_assoc()){
    array_push($actions,'ac_'.$row_ac['id']);
    }
    foreach($actions as $k=>$v){ ?>
    <script>var id='<?php echo $k; ?>'
    $('.ac_'+id).addClass('hidden');</script>
    <?php } ?>
        <?php if($tp=='tr'){ ?>
<?php if($_SESSION['prv']=='Management'){?>
<a class="w3-button w3-small w3-circle w3-white w3-border w3-border-green" title="Restore Order" href="javascript:void(0)" onClick="popupwindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','_blank',520,350)"><i class="fa fa-undo"></i></a>
<a class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" title="Permanent Delete" href="javascript:void(0)" onClick="popupwindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','_blank',520,350)"><i class="fa fa-trash"></i></a>
<?php if($row['type']=='Interpreter'){ echo "<span class='label label-success lbl'>F2F</span>"; }else if($row['type']=='Telephone'){ echo "<span class='label label-info lbl'>TP</span>"; }else{ echo "<span class='label label-warning lbl'>TR</span>"; } ?>
<?php } ?>
        <?php }else if($tp=='c'){ ?>
<?php if($_SESSION['prv']=='Management'){?>
<a class="w3-button w3-small w3-circle w3-white w3-border w3-border-green" title="Restore Order" href="javascript:void(0)" onClick="popupwindow('email_resume.php?email_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','_blank',620,450)"><i class="fa fa-undo"></i></a>
<a class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" title="Permanent Delete" href="javascript:void(0)" onClick="popupwindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','_blank',520,350)"><i class="fa fa-trash"></i></a>
<?php if($row['type']=='Interpreter'){ echo "<span class='label label-success lbl'>F2F</span>"; }else if($row['type']=='Telephone'){ echo "<span class='label label-info lbl'>TP</span>"; }else{ echo "<span class='label label-warning lbl'>TR</span>"; } ?>
<?php } ?>
        <?php }else if($tp=='ml'){ ?>
<a href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>', 'title', 1000, 1000);"><input type="image" src="images/icn_new_article.png" title="View Order"></a>
<a href="javascript:void(0)" onClick="popupwindow('<?php echo $edit_page; ?>?edit_id=<?php echo $row['id']; ?>&duplicate=<?php echo 'yes'; ?>', 'title', 1200, 700);"><input type="image" src="images/commit.png" title="Create Duplicate"></a>
<?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Operator'){?>
<?php if($_SESSION['prv']=='Management'){?>
<a href="javascript:void(0)" onClick="popupwindow('<?php echo $edit_page; ?>?edit_id=<?php echo $row['id']; ?>', 'title', 1200, 700);"><input type="image" src="images/icn_edit.png" title="Edit"></a>
<?php }if($_SESSION['prv']=='Management'){?>
<a href="javascript:void(0)" onClick="MM_openBrWindow('interp_assign.php?assign_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&srcLang=<?php echo $row['source']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/icn_add_user.png" title="Edit Assign Interpreter"></a>
<?php } ?>
<?php }if($_SESSION['prv']=='Management'){?>
<a href="#" onClick="MM_openBrWindow('email_emend.php?email_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&col=intrpName','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/icn_jump_back.png" title="Go Home Screen"></a>
<a href="javascript:void(0)" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/icn_trash.png" title="Trash"></a> 
<?php }if($_SESSION['prv']=='Management'  || $_SESSION['prv']!='Finance'){ ?>
<a href="javascript:void(0)" onClick="popupwindow('<?php echo $exp_page; ?>?update_id=<?php echo $row['id']; ?>', 'title', 1000, 550);"><input type="image" src="images/Update.png" title="Update Expanses"></a>
<?php }if(($row['hoursWorkd']!=0)  && $_SESSION['prv']=='Finance'){ ?>
<a href="javascript:void(0)" onClick="popupwindow('<?php echo $exp_page; ?>?update_id=<?php echo $row['id']; ?>', 'title', 1000, 550);"><input type="image" src="images/Update.png" title="Update Expanses"></a>
<?php }if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance'){?>
<a href="javascript:void(0)" onClick="popupwindow('<?php echo $inv_page; ?>?invoice_id=<?php echo $row['id']; ?>','title', 1000, 1000);"><input type="image" src="images/invoice.png" title="Invoice"></a>
<a href="javascript:void(0)" onClick="MM_openBrWindow('<?php echo $mk_credit_page; ?>?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/icn_settings.png".png" title="Make Credit Note"></a>
<?php if($row['credit_note']){ ?>
<a href="javascript:void(0)" onClick="MM_openBrWindow('<?php echo $credit_page; ?>?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/icn_categories.png" title="Credit Note"></a>
<?php }}if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ){?>
<?php if($row['orderCancelatoin']==0){ ?>
<a href="javascript:void(0)" onClick="popupwindow('cancel_order.php?job_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','_blank',620,450)"><input type="image" src="images/top_icon.png" title="Order Cancelation"></a>
<?php }else{ ?>
<a href="javascript:void(0)" onClick="popupwindow('email_resume.php?email_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','_blank',620,450)"><input type="image" src="images/icn_alert_error.png" title="Order Canceled"></a>
<?php }
} ?>
<?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ){?>
<a href="javascript:void(0)" onClick="popupwindow('<?php echo $c_exp_page; ?>?update_id=<?php echo $row['id']; ?>', 'title', 1000, 550);"><input type="image" src="images/company-icon.jpg" title="Company Update Expanses"></a>
<?php if(($row['po_req']==1 && $row['porder']!='') || ($row['po_req']==1 && $row['porder']=='')){ ?>
<a href="javascript:void(0)" onClick="popupwindow('purch_update.php?purch_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','_blank',600,550)"><input type="image" src="images/icn_tags.png" title="Update Purchase Order #"></a>
<?php } ?>
<a href="javascript:void(0)" onClick="popupwindow('<?php echo $comp_earning; ?>?view_id=<?php echo $row['id']; ?>', 'title', 800, 600);"><input type="image" src="images/earning.png" title="Earning">
</a>                    
<?php } ?>
<?php if($row['type']=='Interpreter'){ echo "<span class='label label-success lbl'>F2F</span>"; }else if($row['type']=='Telephone'){ echo "<span class='label label-info lbl'>TP</span>"; }else{ echo "<span class='label label-warning lbl'>TR</span>"; } ?>
        <?php }else{ ?>
        <div class="dropdown dropdown_actions">
    <button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">Action
    <span class="caret"></span></button>
        <ul class="dropdown-menu list-inline" role="menu" aria-labelledby="menu1">
      <li><a class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue ac_1" title="View Order" href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>', 'title', 1200, 650);"><i class="fa fa-eye"></i></a></li>
      <li><a class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue ac_2" title="Create Duplicate" href="javascript:void(0)" onClick="popupwindow('<?php echo $edit_page; ?>?edit_id=<?php echo $row['id']; ?>&duplicate=<?php echo 'yes'; ?>', 'title', 1200, 700);"><i class="fa fa-copy"></i></a></li>
<?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Operator'){?>
<?php if($_SESSION['prv']=='Management' || $_SESSION['userId']=='21'){?>
<li><a class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edit" href="javascript:void(0)" onClick="popupwindow('<?php echo $edit_page; ?>?edit_id=<?php echo $row['id']; ?>', 'title', 1200, 700);"><i class="fa fa-pencil"></i></a></li>
<?php } ?>
<li><a title="Amend this Job" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-yellow" onClick="popupwindow('email_emend.php?email_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&col=intrpName','_blank', 600, 420)"><i class="fa fa-undo"></i></a></li>
<?php }if($_SESSION['prv']=='Management'){?>
<li title="Trash Record"><a href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" onClick="popupwindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','title', 500,350);"><i class="fa fa-trash"></i></a></li>
<?php }?>
</ul></div>
<?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ){ ?>
<div class="dropdown dropdown_actions2">
    <button class="btn btn-default btn-xs dropdown-toggle" type="button" id="menu2" data-toggle="dropdown">Update
    <span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu" aria-labelledby="menu2">
<li><a title="Update Expenses" href="javascript:void(0)" onClick="popupwindow('<?php echo $exp_page; ?>?update_id=<?php echo $row['id']; ?>', 'title', 1000, 550);"><i class="fa fa-refresh"></i> Update Expenses</a></li>
<li><a title="Update Company Expanses" href="javascript:void(0)" onClick="popupwindow('<?php echo $c_exp_page; ?>?update_id=<?php echo $row['id']; ?>', 'title', 1000, 550);"><i class="fa fa-building"></i> Update Company Expanses</a></li>
<?php if(($row['po_req']==1 && $row['porder']!='') || ($row['po_req']==1 && $row['porder']=='')){ ?>
<li><a title="Update Purchase Order #" href="javascript:void(0)" onClick="popupwindow('purch_update.php?purch_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>&porder=<?php echo $row['porder']; ?>','_blank',600,550)"><i class="fa fa-barcode"></i> Update Purchase Order #</a></li>
<?php } ?>
<li><a title="View Invoice" href="javascript:void(0)" onClick="MM_openBrWindow('<?php echo $inv_page; ?>?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=1050,height=620,left=170,top=35')"><i class="fa fa-file-o"></i> View Invoice</a></li>
<li><a title="Make Credit Note" href="javascript:void(0)" onClick="MM_openBrWindow('<?php echo $mk_credit_page; ?>?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><i class="fa fa-sliders"></i> Make Credit Note</a></li>
</ul></div>
<?php } ?>
<?php if($_SESSION['prv']=='Operator'){ ?>
<a title="Update Expenses" href="javascript:void(0)" onClick="popupwindow('<?php echo $exp_page; ?>?update_id=<?php echo $row['id']; ?>', 'title', 1000, 550);"><i class="fa fa-refresh text-danger"></i></a>
<?php }
if($_SESSION['prv']=='Management'  || $_SESSION['prv']=='Finance' || $_SESSION['prv']=='Operator'){
$arr_n=$acttObj->read_specific("(select count(id)","jobnotes","tbl='".strtolower($row['type'])."' and fid=".$row['id']." and (readcount is null or readcount=0)) as unread ,(select count(id) from jobnotes where tbl='".strtolower($row['type'])."' and fid=".$row['id']." and (readcount is not null and readcount!=0)) as yes_read"); ?>
<a href="javascript:void(0)" title="JOB NOTES" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="<?php echo '<b>'.$arr_n['unread'].'</b> unread <b>'.$arr_n['yes_read'].'</b> read job notes';?>" onclick="popupwindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','_blank',900,800)" <?php echo $arr_n['unread']>0?'class="w3-button w3-small w3-circle w3-blue"':'class="w3-button w3-small w3-circle w3-grey"'; ?>><?php echo $arr_n['unread']>0?$arr_n['unread']:$arr_n['yes_read']; ?></a>
<?php }
if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance'){
if($row['credit_note']){ ?>
<a href="javascript:void(0)" onClick="MM_openBrWindow('<?php echo $credit_page; ?>?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><input type="image" src="images/icn_categories.png" title="Credit Note"></a>
<?php }
}
if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance'  || $_SESSION['prv']=='Operator'){
if($row['orderCancelatoin']==0){ ?>
<a href="javascript:void(0)" onClick="popupwindow('cancel_order.php?job_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','_blank',620,450)"><input type="image" src="images/top_icon.png" title="Order Cancelation"></a>
  <?php }else{ ?>
<a href="javascript:void(0)" title="Order Cancelled" data-toggle="popover" data-trigger="hover" data-placement="top" style="text-decoration:none;" data-content="Click to resmue this job" onClick="popupwindow('email_resume.php?email_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','_blank',620,450)"><input type="image" src="images/icn_alert_error.png"></a>
<?php }
} ?>
<?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ){?>
<a href="javascript:void(0)" onClick="popupwindow('<?php echo $comp_earning; ?>?view_id=<?php echo $row['id']; ?>', 'title', 800, 600);"><input type="image" src="images/earning.png" title="Earning"></a>                   
<a href="<?php echo $history_page; ?>?view_id=<?php echo $row['id']; ?>"><input type="image" src="images/feedback.png" title="Edited List"></a>
<?php if($row['time_sheet']){ ?>
<a href="javascript:void(0)" onClick="popupwindow('timesheet_view.php?t_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','_blank',620,450)"><input type="image" src="images/images.jpg" title="View Time Sheet"></a>
<?php } ?>
<?php $job_counter=$acttObj->read_specific('count(id) as counter','job_files','status=1 and tbl="'.strtolower($row['type']).'" and file_type="timesheet" and order_id='.$row['id']);
if($job_counter['counter']>0){ ?>
<a href="javascript:void(0)" onClick="popupwindow('extra_file_view.php?order_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','_blank',620,450)"  title="View Extra Files"><i class="fa fa-plus fa-2x"></i></a>
<?php } ?>
<?php }
if ($_SESSION['prv']=='Management' && $row['jobDisp'] == 1) {?>
<a href="javascript:void(0)" onClick="MM_openBrWindow('../no_of_applicants.php?tracking=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
<input type="image" src="images/aplcnts.png" title="<?php echo $acttObj->unique_dataAnd('bid','count(*)','job',$row['id'],'tabName',strtolower($row['type'])); ?> Applicants"></a>
<?php } ?>
<?php if($row['type']=='Translation'){
    $dox_counter=$acttObj->read_specific('count(id) as file_counter','job_files','status=1 and tbl="'.strtolower($row['type']).'" and file_type="c_portal" and order_id='.$row['id']);
if($dox_counter['file_counter']>0){ ?>
<a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" onClick="popupwindow('trans_dox_view.php?order_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','_blank',620,450)" title="View Translation Document(s)"><i class="fa fa-file"></i></a>
<?php }
} ?>
<?php if($row['type']=='Interpreter'){ echo "<span class='label label-success lbl'>F2F</span>"; }else if($row['type']=='Telephone'){ echo "<span class='label label-info lbl'>TP</span>"; }else{ echo "<span class='label label-warning lbl'>TR</span>"; } ?>
<?php }?>
</div>
</td>
        </tr>
                <?php }?>
                </tbody></table><br>
      </div>
      <?php } ?>
  </section>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
    $(function() {
      $('.searchable').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
      
        $('[data-toggle="popover"]').popover({html:true});
        $('[data-toggle="tooltip"]').tooltip();
    });
    </script>
</body>
</html>