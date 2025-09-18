<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == "" || !isset($_SESSION)) {
  session_start();
}
include "db.php";
include "class.php";
$p_org = '';
$search_1 = @$_GET["search_1"];
$search_2 = @$_GET["search_2"];
$search_3 = @$_GET["search_3"];
$type = @$_GET["type"];
$recordArray = [];
$reason_lsuk = array();
$reason_client = array();
$cn_lsuk_cost = $cn_client_cost = 0;
if (isset($_GET['p_org'])) {
  $p_org = $_GET['p_org'];
  $p_org_q = $acttObj->read_specific("GROUP_CONCAT(child_comp) as ch_ids", "subsidiaries", " parent_comp=$p_org")['ch_ids'] ?: '0';
  $p_org_ad = ($p_org_q != 0 ? " and comp_reg.id IN ($p_org_q) " : "");
} else {
  $p_org_ad = $p_org;
}
?>
<!doctype html>
<html lang="en">

<head>
  <title>Client Quarterly Booking Report</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <style>
    .multiselect {
      min-width: 190px;
    }

    .multiselect-container {
      max-height: 400px;
      overflow-y: auto;
      max-width: 380px;
    }

    .form-group {
      margin-left: 16px;
    }
  </style>
</head>

<body>
  <?php include "incmultiselfiles.php"; ?>
  <script type="text/javascript">
    $(function() {
      $('#search_1').multiselect({
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Company'
      });
      $('#idcompgrps').multiselect({
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Child Company'
      });
      $('#sup_parents').multiselect({
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Super Parent'
      });
    });


    function FindChildByValue(arrElem, strFind) {

      var i, nLen = arrElem.length;
      var elemChild;

      for (i = 0; i < nLen; i++) {
        elemChild = arrElem[i];
        if (elemChild.value && elemChild.value == strFind)
          return i;
      }
      return -1;
    }

    function GetChildFrom(arrElem, nFrom) {
      var i, nLen = arrElem.length;
      var elemChild;

      var strCos = "";
      for (i = nFrom; i < nLen; i++) {
        elemChild = arrElem[i];
        if (!elemChild.dataset["abrv"])
          break;

        if (strCos != "")
          strCos += ",";
        strCos += elemChild.dataset["abrv"];
      }
      return strCos;
    }

    function CompanyGrpChange(elemSel) {

      var opts = elemSel.options;
      var childs = elemSel.children;
      //var nLen=childs.length;

      var arrGrps = $(elemSel).val();
      if (arrGrps == null)
        return;

      //var arrGrps=strSel.split(",");
      var i, nCount = arrGrps.length;
      var strOrgGrp;
      var nPos;

      var strAllComp = "";
      for (i = 0; i < nCount; i++) {
        strOrgGrp = arrGrps[i];
        nPos = FindChildByValue(childs, strOrgGrp);
        if (nPos >= 0) {
          if (strAllComp != "")
            strAllComp += ",";
          strAllComp += GetChildFrom(childs, nPos + 1);
        }
      }


      //alert("CompanyGrpChange:"+strAllComp);

      var x = strAllComp;
      var y = document.getElementById("search_2").value;
      var zd = document.getElementById("search_3").value;
      if (!y || !zd) {
        y = "<?php echo $search_2; ?>";
        zd = "<?php echo $search_3; ?>";
      }

      window.location.assign('<?php echo basename(__FILE__); ?>?search_1=' + x + '&search_2=' + y + '&search_3=' + zd);

    }

    function myFunction_date() {
      var x = $('#search_1').val();
      var y = $('#idcompgrps').val();
      var z = $('#p_org').val();
      var dt = $('#search_2').val();
      var dt2 = $('#search_3').val();
      var type = '';
      if (!x && y && !z) {
        x = y;
        type = 'parent';
      } else if (!x && !y && z) {
        x = z;
        type = 'super';
      } else {
        x = x;
        type = 'single';
      }
      if (Date.parse(dt)) {
        window.location.assign('<?php echo basename(__FILE__); ?>?search_1=' + x + '&type=' + type + '&p_org=' + z + '&search_2=' + dt + '&search_3=' + dt2);
      } else {
        alert('Kindly select Company and Date first ! Thank you');
      }
    }
  </script>
  <?php include "nav2.php"; ?>

  <section class="container-fluid">
    <div class="col-md-12">
      <header>
        <center>
          <h2 class="col-md-4 col-md-offset-4 text-center"><span class="label label-primary">Company Wise Job List Bar Graph</span></h2>
        </center>
        <div class="col-md-11 col-md-offset-1"><br>
          <!-- <div class="form-group col-md-2 col-sm-3">
            <select id='sup_parents' multiple="multiple" class="form-control">
              <?php include "multiselect_super_parents.php"; ?>
            </select>
          </div> -->
          <div class="form-group col-md-2 col-sm-4">
              <?php
              $sql_opt = "SELECT DISTINCT id,name,abrv from comp_reg WHERE comp_nature=1 ORDER BY name ASC"; ?>
              <select id="p_org" name="p_org" onChange="myFunction()" class="form-control searchable">
                  <?php $result_opt = mysqli_query($con, $sql_opt);
                  $options = "";
                  while ($row_opt = mysqli_fetch_array($result_opt)) {
                      $comp_id = $row_opt["id"];
                      $code = $row_opt["abrv"];
                      $name_opt = $row_opt["name"];
                      $options .= "<OPTION value='$comp_id' " . ($comp_id == $p_org ? 'selected' : '') . ">" . $name_opt . ' (' . $code . ')';
                  }
                  ?>
                  <option value="">Select Parent/Head Units</option>
                  <?php echo $options; ?>
                  </option>
              </select>
          </div>
          <div class="form-group col-md-2 col-sm-3">
            <select id='idcompgrps' multiple="multiple" class="form-control">
              <?php include "multiselectcompgrp.php"; ?>
            </select>
          </div>

          <div class="form-group col-md-2 col-sm-3">
            <select id="search_1" name="search_1" multiple="multiple" class="form-control">
              <?php
              $sql_opt = "SELECT name,id FROM comp_reg ORDER BY name ASC";
              $result_opt = mysqli_query($con, $sql_opt);
              $options = "";
              while ($row_opt = mysqli_fetch_array($result_opt)) {
                $code = $row_opt["id"];
                $name_opt = $row_opt["name"];
                $options .= "<option value='$code'>" . $name_opt . "</option>";
              }
              ?>
              <?php echo $options; ?>
            </select>
          </div>
          <div class="form-group col-md-2 col-sm-3">
            <input type="date" id="search_2" name="search_2" class="form-control" <?php if (isset($_GET["search_2"]) && !empty($_GET["search_2"])) { echo 'value="' . $_GET["search_2"] . '"'; } ?> />
          </div>
          <div class="form-group col-md-2 col-sm-3">
            <input type="date" id="search_3" name="search_3" class="form-control" <?php if (isset($_GET["search_3"]) && !empty($_GET["search_3"])) { echo 'value="' . $_GET["search_3"] . '"'; } ?> />
          </div>

          <div class="form-group col-md-1 col-sm-4">
            <a href="javascript:void(0)" title="Click to Get Report" onclick="myFunction_date()"><span class="btn btn-sm btn-primary">Get Report</span></a>
          </div>

        </div>
      </header>
  </section>
  <section>

    <?php
    $excel = @$_GET["excel"];
    $type = $_GET["type"];
    $semi = "\"'\"";
    $orgz = $_GET["search_1"];
    if ($type == "super") {
      $data1 = $acttObj->read_specific(
        "DISTINCT GROUP_CONCAT(parent_companies.sup_child_comp) as data1",
        "parent_companies",
        "parent_companies.sup_parent_comp IN (" . $orgz . ")"
      );
      $all_abrv = $acttObj->query_extra(
        "GROUP_CONCAT(comp_reg.abrv) as all_abrv",
        "comp_reg",
        "id IN (" . $data1["data1"] . ")",
        "set SESSION group_concat_max_len=10000"
      );
      $all_cz = $acttObj->query_extra(
        "DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz",
        "child_companies",
        "child_companies.parent_comp IN (" . $data1["data1"] . ")",
        "set SESSION group_concat_max_len=10000"
      );
    } elseif ($type == "parent") {
      $data1 = $acttObj->read_specific(
        "GROUP_CONCAT(comp_reg.id) as data1",
        "comp_reg",
        "id IN (" . $orgz . ")"
      );
      $all_abrv = $acttObj->query_extra(
        "GROUP_CONCAT(comp_reg.abrv) as all_abrv",
        "comp_reg",
        "id IN (" . $data1["data1"] . ")",
        "set SESSION group_concat_max_len=10000"
      );
      $all_cz = $acttObj->query_extra(
        "DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz",
        "child_companies",
        "child_companies.parent_comp IN (" . $data1["data1"] . ")",
        "set SESSION group_concat_max_len=10000"
      );
    } else {
      $all_abrv = $acttObj->query_extra(
        "GROUP_CONCAT(comp_reg.abrv) as all_abrv",
        "comp_reg",
        "id IN (" . $orgz . ")",
        "set SESSION group_concat_max_len=10000"
      );
      $all_cz = $acttObj->query_extra(
        "DISTINCT GROUP_CONCAT($semi,comp_reg.abrv,$semi) as all_cz",
        "comp_reg",
        "comp_reg.id IN ($orgz)",
        "set SESSION group_concat_max_len=10000"
      );
    }
    $display_org = $acttObj->read_specific(
      "GROUP_CONCAT(comp_reg.name) as orgName",
      "comp_reg",
      "id IN (" . $orgz . ")"
    );
//     print_r($all_cz['all_cz']);
// die();exit();
    $search_1 = $all_abrv["all_abrv"];

    $x = 0;
    $arr_langs = [];
    $arr = explode(",", $search_1);
    $org_names = "'" . implode("','", $arr) . "'";
    $search_2 = @$_GET["search_2"];
    $search_3 = @$_GET["search_3"];
    $q1_start = $search_2;
    $endDate = $search_3;
    //  $endDate = date("Y-m-d", strtotime("+9 months", strtotime($search_2)));



    function form_date($dt)
    {
      $timestamp = strtotime($dt);
      $new_date = date("d.m.Y", $timestamp);
      return $new_date;
    }
    function getMonths($fromDate, $toDate)
{
    $startDate = new DateTime($fromDate);
    $endDate = new DateTime($toDate);

    $months = array();

    // Start with the given interval
    $currentDate = new DateTime($fromDate);

    $i = 0;

    while ($currentDate <= $endDate) {
        // For the first month, set start as $fromDate
        $startOfMonth = ($i === 0) ? $startDate->format('Y-m-d') : $currentDate->format('Y-m-01');
        
        // For the last month, set end as $toDate
        $endOfMonth = ($currentDate->format('Y-m') === $endDate->format('Y-m')) 
                      ? $endDate->format('Y-m-d') 
                      : $currentDate->format('Y-m-t');

        $months[$i]['month'] = $currentDate->format('F Y');
        $months[$i]['start'] = $startOfMonth;
        $months[$i]['end'] = $endOfMonth;

        // Move to the next month
        $currentDate->modify('first day of next month');
        $i++;
    }

    return $months;
}

    $months = getMonths($search_2, $search_3);
    // print_r($months);
    if ($type != "single") {
      $child_orgz = $acttObj->read_specific(
        "GROUP_CONCAT($semi,comp_reg.abrv,$semi) as child_orgz",
        "parent_companies,child_companies,comp_reg",
        "parent_companies.sup_child_comp=child_companies.parent_comp AND child_companies.child_comp=comp_reg.id and child_companies.parent_comp =(SELECT id from comp_reg WHERE abrv='" . $arr[$count_comp] . "')"
      );
    }

    ?>

    <?php
    $i_q_tot = 0;
    $count_comp = 0;
    $arr_comps = [];
    while ($x < count($arr)) {

      $arr[$count_comp];
      array_push($arr_comps, $arr[$count_comp]);

      while ($count_comp < count($arr)) {
        $name_of_comp = $acttObj->read_specific(
          "name",
          "comp_reg",
          "abrv = '" . $arr[$count_comp] . "'"
        );
        //  echo $name_of_comp["name"];

        if ($type != "single") {
          $child_orgz = $acttObj->read_specific(
            "GROUP_CONCAT($semi,comp_reg.abrv,$semi) as child_orgz",
            "parent_companies,child_companies,comp_reg",
            "parent_companies.sup_child_comp=child_companies.parent_comp AND child_companies.child_comp=comp_reg.id and child_companies.parent_comp =(SELECT id from comp_reg WHERE abrv='" .
              $arr[$count_comp] .
              "')"
          );
        }
        $x = $i_q_tot;
        $u = 0;
        $count_comp++;
      }
      $x++;
    }
    foreach ($months as $key => $value) {
      $interp = $type != 'single' ?  $acttObj->read_specific(
        "SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost,SUM(total_net) as total_net,SUM(total_vat) as total_vat,SUM(total_non_vat) as total_non_vat",
        "(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost,round(IFNULL(sum(interpreter.total_charges_comp),0),2) as total_net,round(IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_vat,round(IFNULL(sum(interpreter.C_otherexpns),0),2) as total_non_vat FROM interpreter,interpreter_reg,comp_reg,invoice",
        "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('" . $value['start'] . "') and ('" . $value['end'] . "') $p_org_ad and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526)
         ) as grp"
      )
        : $acttObj->read_specific(
          "SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost,SUM(total_net) as total_net,SUM(total_vat) as total_vat,SUM(total_non_vat) as total_non_vat",
          "(SELECT count(interpreter.source) as total_jobs,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_cost,round(IFNULL(sum(interpreter.total_charges_comp),0),2) as total_net,round(IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0),2) as total_vat,round(IFNULL(sum(interpreter.C_otherexpns),0),2) as total_non_vat FROM interpreter,interpreter_reg,comp_reg,invoice",
          "interpreter.intrpName = interpreter_reg.id AND interpreter.orgName = comp_reg.abrv AND interpreter.invoiceNo=invoice.invoiceNo AND interpreter.assignDate between ('" . $value['start'] . "') and ('" . $value['end'] . "') and (interpreter.orgName IN (" .
            $all_cz["all_cz"] .
            ")) and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 AND interpreter.id NOT IN (12027,14298,16772,14526)
          ) as grp"
        );
      $telephone = $type != 'single' ? $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost", "(SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice", "telephone.intrpName = interpreter_reg.id 
      AND telephone.orgName = comp_reg.abrv 
      AND telephone.invoiceNo=invoice.invoiceNo 
      AND telephone.assignDate BETWEEN '" . $value['start'] . "' and '" . $value['end'] . "' $p_org_ad and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp")
        : $acttObj->read_specific("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost", "(SELECT count(telephone.source) as total_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone,interpreter_reg,comp_reg,invoice", "telephone.intrpName = interpreter_reg.id 
      AND telephone.orgName = comp_reg.abrv 
      AND telephone.invoiceNo=invoice.invoiceNo 
      AND telephone.assignDate BETWEEN '" . $value['start'] . "' and '" . $value['end'] . "' and (telephone.orgName IN (" . $all_cz['all_cz'] . ")) and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) as grp");

      $trans = $type != 'single' ? $acttObj->read_all("SUM(total_jobs) as total_jobs,SUM(total_cost) as total_cost", "(SELECT count(translation.source) as total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation,interpreter_reg,comp_reg,invoice", "translation.intrpName = interpreter_reg.id 
      AND translation.orgName = comp_reg.abrv 
      AND translation.invoiceNo=invoice.invoiceNo 
      AND translation.asignDate BETWEEN '" . $value['start'] . "' and '" . $value['end'] . "' $p_org_ad and translation.deleted_flag = 0 and translation.order_cancel_flag=0) as grp")
        : $acttObj->read_all("COUNT(translation.source) AS total_jobs,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost", "translation", " translation.asignDate BETWEEN '" . $value['start'] . "' and '" . $value['end'] . "' and translation.orgName IN(" . $all_cz['all_cz'] . ") and translation.deleted_flag = 0 and translation.order_cancel_flag=0");
      $trans = mysqli_fetch_all($trans, MYSQLI_ASSOC);


      //Cancellation Jobs Count
      // $qw = mysqli_query($con,"SET SESSION group_concat_max_len = 10000000000000000;");
      $canc_interp = $acttObj->read_specific("COUNT(interpreter.id) as total_cancelled_jobs,round(IFNULL(sum(total_charges_comp),0)+ IFNULL(sum(total_charges_comp * cur_vat),0) +IFNULL(sum(C_otherexpns),0),2) as total_cost,SUM(order_cancelledby='LSUK') as lsuk_count,SUM(order_cancelledby='Client') as client_count, GROUP_CONCAT(CASE WHEN order_cancelledby='LSUK' THEN CONCAT (interpreter.id,',',nameRef,',',paid_date,',','Face 2 Face',',',total_charges_comp,',',cur_vat,',',invoiceNo) END SEPARATOR '|') as lsuk_nameref,GROUP_CONCAT(CASE WHEN order_cancelledby='Client' THEN CONCAT(interpreter.id,',',nameRef,',',paid_date,',','Face 2 Face',',',total_charges_comp,',',cur_vat,',',invoiceNo) END SEPARATOR '|') as client_nameref, COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))<=24 AND order_cancelledby='LSUK' THEN interpreter.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))<=24 AND order_cancelledby='LSUK' THEN interpreter.id END)) as LSUK_cancelled_within_24hrs,COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))<=24 AND order_cancelledby='Client' THEN interpreter.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))<=24 AND order_cancelledby='Client' THEN interpreter.id END)) as Client_cancelled_within_24hrs,COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))>24 AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))<=48 AND order_cancelledby='LSUK' THEN interpreter.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))>24 AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))<=48 AND order_cancelledby='LSUK' THEN interpreter.id END)) as LSUK_cancelled_within_48hrs,COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))>24 AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))<=48 AND order_cancelledby='Client' THEN interpreter.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))>24 AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))<=48 AND order_cancelledby='Client' THEN interpreter.id END)) as Client_cancelled_within_48hrs, COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))>48 AND order_cancelledby='LSUK' THEN interpreter.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))>48 AND order_cancelledby='LSUK' THEN interpreter.id END)) as LSUK_cancelled_AFTER_48hrs, COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))>48 AND order_cancelledby='Client' THEN interpreter.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))>48 AND order_cancelledby='Client' THEN interpreter.id END)) as Client_cancelled_AFTER_48hrs, COUNT((CASE WHEN cn_time IS NULL THEN interpreter.id END)) AS null_cancellation_time", "interpreter,comp_reg", " interpreter.orgName = comp_reg.abrv AND interpreter.assignDate between ('" . $value['start'] . "') and ('" . $value['end'] . "') ".($p_org_ad!=''?"$p_org_ad":' and (interpreter.orgName IN (' . $all_cz["all_cz"] . '))')." and interpreter.deleted_flag = 0 AND (interpreter.order_cancel_flag=1 OR interpreter.orderCancelatoin=1) AND interpreter.id NOT IN (12027,14298,16772,14526)");
      
      // $test_interp = "COUNT(id) as total_cancelled_jobs,round(IFNULL(sum(total_charges_comp),0)+ IFNULL(sum(total_charges_comp * cur_vat),0),2) as total_cost,SUM(order_cancelledby='LSUK') as lsuk_count,SUM(order_cancelledby='Client') as client_count, GROUP_CONCAT(CASE WHEN order_cancelledby='LSUK' THEN CONCAT (id,',',nameRef,',',paid_date,',','translation',',',total_charges_comp,',',cur_vat,',',invoiceNo) END SEPARATOR '|') as lsuk_nameref,GROUP_CONCAT(CASE WHEN order_cancelledby='Client' THEN CONCAT(id,',',nameRef,',',paid_date,',','translation',',',total_charges_comp,',',cur_vat,',',invoiceNo) END SEPARATOR '|') as client_nameref, COUNT((CASE WHEN datediff(asignDate ,cn_date )<=1 AND order_cancelledby='LSUK' THEN id END)) as LSUK_cancelled_within_24hrs,COUNT((CASE WHEN datediff(asignDate ,cn_date )<=1 AND order_cancelledby='Client' THEN id END)) as Client_cancelled_within_24hrs, COUNT((CASE WHEN datediff(asignDate ,cn_date )>1 AND  datediff(asignDate ,cn_date )<=2 AND order_cancelledby='LSUK' THEN id END)) as LSUK_cancelled_within_48hrs,COUNT((CASE WHEN datediff(asignDate ,cn_date )>1 AND  datediff(asignDate ,cn_date )<=2 AND order_cancelledby='Client' THEN id END)) as Client_cancelled_within_48hrs, COUNT((CASE WHEN datediff(asignDate ,cn_date )>2 AND order_cancelledby='LSUK' THEN id END)) as LSUK_cancelled_AFTER_48hrs,COUNT((CASE WHEN datediff(asignDate ,cn_date )>2 AND order_cancelledby='Client' THEN id END)) as Client_cancelled_AFTER_48hrs, COUNT((CASE WHEN cn_date IS NULL THEN id END)) AS null_cancellation_time  FROM translation,comp_reg where translation.orgName = comp_reg.abrv AND translation.asignDate BETWEEN '" . $value['start'] . "' and '" . $value['end'] . "' ".($p_org_ad!=''?"$p_org_ad":' and (translation.orgName IN (' . $all_cz["all_cz"] . '))')." and translation.deleted_flag = 0 and (translation.order_cancel_flag=1 OR translation.orderCancelatoin=1)";
      // echo $test_interp;
      // die();exit();

      $canc_telephone = $acttObj->read_specific("COUNT(telephone.id) as total_cancelled_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost,SUM(order_cancelledby='LSUK') as lsuk_count,SUM(order_cancelledby='Client') as client_count,GROUP_CONCAT(CASE WHEN order_cancelledby='LSUK' THEN CONCAT (telephone.id,',',nameRef,',',paid_date,',','telephone',',',total_charges_comp,',',cur_vat,',',invoiceNo) END SEPARATOR '|') as lsuk_nameref,GROUP_CONCAT(CASE WHEN order_cancelledby='Client' THEN CONCAT(telephone.id,',',nameRef,',',paid_date,',','telephone',',',total_charges_comp,',',cur_vat,',',invoiceNo) END SEPARATOR '|') as client_nameref, COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))<=24 AND order_cancelledby='LSUK' THEN telephone.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))<=24 AND order_cancelledby='LSUK' THEN telephone.id END)) as LSUK_cancelled_within_24hrs,COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))<=24 AND order_cancelledby='Client' THEN telephone.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))<=24 AND order_cancelledby='Client' THEN telephone.id END)) as Client_cancelled_within_24hrs,COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))>24 AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))<=48 AND order_cancelledby='LSUK' THEN telephone.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))>24 AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))<=48 AND order_cancelledby='LSUK' THEN telephone.id END)) as LSUK_cancelled_within_48hrs,COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))>24 AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))<=48 AND order_cancelledby='Client' THEN telephone.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))>24 AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))<=48 AND order_cancelledby='Client' THEN telephone.id END)) as Client_cancelled_within_48hrs, COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))>48 AND order_cancelledby='LSUK' THEN telephone.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))>48 AND order_cancelledby='LSUK' THEN telephone.id END)) as LSUK_cancelled_AFTER_48hrs, COUNT((CASE WHEN cn_time IS NOT NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', cn_time) ))>48 AND order_cancelledby='Client' THEN telephone.id WHEN cn_time IS NULL AND HOUR(timediff(CONCAT(assignDate, ' ', assignTime) ,CONCAT(cn_date, ' ', '09:00:00') ))>48 AND order_cancelledby='Client' THEN telephone.id END)) as Client_cancelled_AFTER_48hrs, COUNT((CASE WHEN cn_time IS NULL THEN telephone.id END)) AS null_cancellation_time", "telephone,comp_reg", " telephone.orgName = comp_reg.abrv AND telephone.assignDate BETWEEN '" . $value['start'] . "' and '" . $value['end'] . "' ".($p_org_ad!=''?"$p_org_ad":' and (telephone.orgName IN (' . $all_cz["all_cz"] . '))')." and telephone.deleted_flag = 0 and (telephone.order_cancel_flag=1 OR telephone.orderCancelatoin=1)");
      $canc_trans = $acttObj->read_specific("COUNT(translation.id) as total_cancelled_jobs,round(IFNULL(sum(total_charges_comp),0)+ IFNULL(sum(total_charges_comp * cur_vat),0),2) as total_cost,SUM(order_cancelledby='LSUK') as lsuk_count,SUM(order_cancelledby='Client') as client_count, GROUP_CONCAT(CASE WHEN order_cancelledby='LSUK' THEN CONCAT(translation.id,',',nameRef,',',paid_date,',','translation',',',total_charges_comp,',',cur_vat,',',invoiceNo) END SEPARATOR '|') as lsuk_nameref,GROUP_CONCAT(CASE WHEN order_cancelledby='Client' THEN CONCAT(translation.id,',',nameRef,',',paid_date,',','translation',',',total_charges_comp,',',cur_vat,',',invoiceNo) END SEPARATOR '|') as client_nameref, COUNT((CASE WHEN datediff(asignDate ,cn_date )<=1 AND order_cancelledby='LSUK' THEN translation.id END)) as LSUK_cancelled_within_24hrs,COUNT((CASE WHEN datediff(asignDate ,cn_date )<=1 AND order_cancelledby='Client' THEN translation.id END)) as Client_cancelled_within_24hrs, COUNT((CASE WHEN datediff(asignDate ,cn_date )>1 AND datediff(asignDate ,cn_date )<=2 AND order_cancelledby='LSUK' THEN translation.id END)) as LSUK_cancelled_within_48hrs,COUNT((CASE WHEN datediff(asignDate ,cn_date )>1 AND datediff(asignDate ,cn_date )<=2 AND order_cancelledby='Client' THEN translation.id END)) as Client_cancelled_within_48hrs, COUNT((CASE WHEN datediff(asignDate ,cn_date )>2 AND order_cancelledby='LSUK' THEN translation.id END)) as LSUK_cancelled_AFTER_48hrs,COUNT((CASE WHEN datediff(asignDate ,cn_date )>2 AND order_cancelledby='Client' THEN translation.id END)) as Client_cancelled_AFTER_48hrs, COUNT((CASE WHEN cn_date IS NULL THEN translation.id END)) AS null_cancellation_time", "translation,comp_reg", " translation.orgName = comp_reg.abrv AND translation.asignDate BETWEEN '" . $value['start'] . "' and '" . $value['end'] . "' ".($p_org_ad!=''?"$p_org_ad":' and (translation.orgName IN (' . $all_cz["all_cz"] . '))')." and translation.deleted_flag = 0 and (translation.order_cancel_flag=1 OR translation.orderCancelatoin=1)");

      $deleted_jobs_interp = $acttObj->read_specific("COUNT(id) as total_deleted_jobs,round(IFNULL(sum(total_charges_comp),0)+ IFNULL(sum(total_charges_comp * cur_vat),0) +IFNULL(sum(C_otherexpns),0),2) as total_cost", "interpreter", " interpreter.assignDate between ('" . $value['start'] . "') and ('" . $value['end'] . "') and (interpreter.orgName IN (" . $all_cz["all_cz"] . ")) and interpreter.is_shifted = 0 and interpreter.deleted_flag = 1 AND interpreter.intrpName='' AND interpreter.id NOT IN (12027,14298,16772,14526)");

      $deleted_jobs_telep = $acttObj->read_specific("COUNT(id) as total_deleted_jobs,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost", "telephone", " telephone.assignDate BETWEEN '" . $value['start'] . "' and '" . $value['end'] . "' and (telephone.orgName IN (" . $all_cz['all_cz'] . ")) and telephone.is_shifted = 0 and telephone.deleted_flag = 1 AND telephone.intrpName=''");
        
      $deleted_jobs_trans = $acttObj->read_specific("COUNT(id) as total_deleted_jobs,round(IFNULL(sum(total_charges_comp),0)+ IFNULL(sum(total_charges_comp * cur_vat),0),2) as total_cost", "translation", " translation.asignDate BETWEEN '" . $value['start'] . "' and '" . $value['end'] . "' and (translation.orgName IN (" . $all_cz['all_cz'] . ")) and translation.deleted_flag = 1 AND translation.intrpName=''");

      $shifted_jobs_interp = $acttObj->read_specific("COUNT(id) as total_shifted_jobs", "interpreter", " interpreter.assignDate between ('" . $value['start'] . "') and ('" . $value['end'] . "') and (interpreter.orgName IN (" . $all_cz["all_cz"] . ")) and interpreter.is_shifted = 1 ");

      $shifted_jobs_telep = $acttObj->read_specific("COUNT(id) as total_shifted_jobs", "telephone", " telephone.assignDate BETWEEN '" . $value['start'] . "' and '" . $value['end'] . "' and (telephone.orgName IN (" . $all_cz['all_cz'] . ")) and telephone.is_shifted = 1 ");

      $arr = [
        // 'total' => $res_tr['total_jobs'],
        'interp' => $interp['total_jobs'],
        'telep' => $telephone['total_jobs'],
        'trans' => array_sum(array_column($trans, 'total_jobs')),
        'total' => array_sum(array_column($trans, 'total_jobs')) +  $interp['total_jobs'] + $telephone['total_jobs'],
      ];
      $del_arr = [
        'del_interp' =>$deleted_jobs_interp['total_deleted_jobs'],
        'del_telep' => $deleted_jobs_telep['total_deleted_jobs'],
        'del_trans' => $deleted_jobs_trans['total_deleted_jobs'],
        'del_total' => $deleted_jobs_interp['total_deleted_jobs'] +  $deleted_jobs_telep['total_deleted_jobs'] + $deleted_jobs_trans['total_deleted_jobs'],
        'del_total_cost' => $deleted_jobs_interp['total_cost'] +  $deleted_jobs_telep['total_cost'] + $deleted_jobs_trans['total_cost'],
      ];
      $shift_arr = [
        'shift_interp' =>$shifted_jobs_interp['total_shifted_jobs'],
        'shift_telep' => $shifted_jobs_telep['total_shifted_jobs'],
        'shift_total' => $shifted_jobs_interp['total_shifted_jobs'] +  $shifted_jobs_telep['total_shifted_jobs'],
      ];
      $cost_arr = [
        // 'total' => $res_tr['total_jobs'],
        'cost_interp' => $interp['total_cost'],
        'cost_telep' => $telephone['total_cost'],
        'cost_trans' => array_sum(array_column($trans, 'total_cost')),
        'cost_total' => array_sum(array_column($trans, 'total_cost')) +  $interp['total_cost'] + $telephone['total_cost'],
      ];
      $canc_arr = [
        // 'total' => $res_tr['total_jobs'],
        'canc_interp' => $canc_interp['total_cancelled_jobs'],
        'cost_canc_interp' => $canc_interp['total_cost'],
        'canc_by_lsuk_interp' => $canc_interp['lsuk_count'],
        'canc_by_client_interp' => $canc_interp['client_count'],
        'canc_by_lsuk24_interp' => $canc_interp['LSUK_cancelled_within_24hrs'],
        'canc_by_client24_interp' => $canc_interp['Client_cancelled_within_24hrs'],
        'canc_by_lsuk48_interp' => $canc_interp['LSUK_cancelled_within_48hrs'],
        'canc_by_client48_interp' => $canc_interp['Client_cancelled_within_48hrs'],
        'canc_by_lsuk48m_interp' => $canc_interp['LSUK_cancelled_AFTER_48hrs'],
        'canc_by_client48m_interp' => $canc_interp['Client_cancelled_AFTER_48hrs'],
        'nct_interp' => $canc_interp['null_cancellation_time'],
        'interp_lsuk_nameref' => $canc_interp['lsuk_nameref'],
        'interp_client_nameref' => $canc_interp['client_nameref'],

        'canc_telep' => $canc_telephone['total_cancelled_jobs'],
        'cost_canc_telephone' => $canc_telephone['total_cost'],
        'canc_by_lsuk_telep' => $canc_telephone['lsuk_count'],
        'canc_by_client_telep' => $canc_telephone['client_count'],
        'canc_by_lsuk24_telephone' => $canc_telephone['LSUK_cancelled_within_24hrs'],
        'canc_by_client24_telephone' => $canc_telephone['Client_cancelled_within_24hrs'],
        'canc_by_lsuk48_telephone' => $canc_telephone['LSUK_cancelled_within_48hrs'],
        'canc_by_client48_telephone' => $canc_telephone['Client_cancelled_within_48hrs'],
        'canc_by_lsuk48m_telephone' => $canc_telephone['LSUK_cancelled_AFTER_48hrs'],
        'canc_by_client48m_telephone' => $canc_telephone['Client_cancelled_AFTER_48hrs'],
        'nct_telephone' => $canc_telephone['null_cancellation_time'],
        'telephone_lsuk_nameref' => $canc_telephone['lsuk_nameref'],
        'telephone_client_nameref' => $canc_telephone['client_nameref'],

        'canc_trans' => $canc_trans, ['total_cancelled_jobs'],
        'cost_canc_trans' => $canc_trans['total_cost'],
        'canc_by_lsuk_trans' => $canc_trans['lsuk_count'],
        'canc_by_client_trans' => $canc_trans['client_count'],
        'canc_by_lsuk24_trans' => $canc_trans['LSUK_cancelled_within_24hrs'],
        'canc_by_client24_trans' => $canc_trans['Client_cancelled_within_24hrs'],
        'canc_by_lsuk48_trans' => $canc_trans['LSUK_cancelled_within_48hrs'],
        'canc_by_client48_trans' => $canc_trans['Client_cancelled_within_48hrs'],
        'canc_by_lsuk48m_trans' => $canc_trans['LSUK_cancelled_AFTER_48hrs'],
        'canc_by_client48m_trans' => $canc_trans['Client_cancelled_AFTER_48hrs'],
        'nct_trans' => $canc_trans['null_cancellation_time'],
        'trans_lsuk_nameref' => $canc_trans['lsuk_nameref'],
        'trans_client_nameref' => $canc_trans['client_nameref'],

        'canc_total' => $canc_trans['total_cancelled_jobs'] +  $canc_interp['total_cancelled_jobs'] + $canc_telephone['total_cancelled_jobs'],
        'cost_canc_total' => $canc_trans['total_cost'] +  $canc_interp['total_cost'] + $canc_telephone['total_cost'],
        'canc_by_lsuk_total' => $canc_trans['lsuk_count'] +  $canc_interp['lsuk_count'] + $canc_telephone['lsuk_count'],
        'canc_by_client_total' =>  $canc_trans['client_count'] +  $canc_interp['client_count'] + $canc_telephone['client_count'],
        'nct_total' => $canc_trans['null_cancellation_time'] + $canc_interp['null_cancellation_time'] + $canc_telephone['null_cancellation_time'],
        'total_lsuk_nameref' => $canc_interp['lsuk_nameref'] . '|' . $canc_telephone['lsuk_nameref'] . '|' . $canc_trans['lsuk_nameref'],
        'total_client_nameref' => $canc_interp['client_nameref'] . '|' . $canc_telephone['client_nameref'] . '|' . $canc_trans['client_nameref'],

        'sum_lsuk24_total' => $canc_trans['LSUK_cancelled_within_24hrs'] + $canc_interp['LSUK_cancelled_within_24hrs'] + $canc_telephone['LSUK_cancelled_within_24hrs'],
        'sum_client24_total' => $canc_trans['Client_cancelled_within_24hrs'] + $canc_interp['Client_cancelled_within_24hrs'] + $canc_telephone['Client_cancelled_within_24hrs'],
        'sum_lsuk48_total' => $canc_trans['LSUK_cancelled_within_48hrs'] + $canc_interp['LSUK_cancelled_within_48hrs'] + $canc_telephone['LSUK_cancelled_within_48hrs'],
        'sum_client48_total' => $canc_trans['Client_cancelled_within_48hrs'] + $canc_interp['Client_cancelled_within_48hrs'] + $canc_telephone['Client_cancelled_within_48hrs'],
        'sum_lsuk48m_total' => $canc_trans['LSUK_cancelled_AFTER_48hrs'] + $canc_interp['LSUK_cancelled_AFTER_48hrs'] + $canc_telephone['LSUK_cancelled_AFTER_48hrs'],
        'sum_client48m_total' => $canc_trans['Client_cancelled_AFTER_48hrs'] + $canc_interp['Client_cancelled_AFTER_48hrs'] + $canc_telephone['Client_cancelled_AFTER_48hrs'],



      ];
      $months[$key]['record'] = $arr;
      $months[$key]['cost'] = $cost_arr;
      $months[$key]['cancel'] = $canc_arr;
      $months[$key]['deleted'] = $del_arr;
      $months[$key]['shifted'] = $shift_arr;

    }
    $gpcn_data = "SELECT interpreter.id,interpreter.nameRef,interpreter.paid_date,interpreter.invoiceNo,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.c_otherexpns as other_exp,'int_lsuk_nameref' as type,'f2f' as job_type,interpreter.cn_r_id,interpreter.cn_t_id FROM interpreter WHERE interpreter.assignDate BETWEEN '" . $search_2 . "' AND '" . $search_3 . "' AND (interpreter.orgName IN (" . $all_cz['all_cz'] . ")) AND interpreter.deleted_flag=0 AND  (interpreter.orderCancelatoin=1 OR interpreter.order_cancel_flag=1) AND order_cancelledby='LSUK' UNION ALL SELECT interpreter.id,interpreter.nameRef,interpreter.paid_date,interpreter.invoiceNo,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.c_otherexpns as other_exp,'int_client_nameref' as type,'f2f' as job_type,interpreter.cn_r_id,interpreter.cn_t_id FROM interpreter WHERE interpreter.assignDate BETWEEN '" . $search_2 . "' AND '" . $search_3 . "' AND (interpreter.orgName IN (" . $all_cz['all_cz'] . ")) AND interpreter.deleted_flag=0 AND  (interpreter.orderCancelatoin=1 OR interpreter.order_cancel_flag=1) AND order_cancelledby='Client' UNION ALL SELECT telephone.id,telephone.nameRef,telephone.paid_date,telephone.invoiceNo,telephone.total_charges_comp,telephone.cur_vat,telephone.c_otherCharges as other_exp,'telep_lsuk_nameref' as type,'telephone' as job_type,telephone.cn_r_id,telephone.cn_t_id FROM telephone WHERE telephone.assignDate BETWEEN '" . $search_2 . "' AND '" . $search_3 . "' AND (telephone.orgName IN (" . $all_cz['all_cz'] . ")) AND telephone.deleted_flag=0 AND  (telephone.orderCancelatoin=1 OR telephone.order_cancel_flag=1) AND order_cancelledby='LSUK' UNION ALL SELECT telephone.id,telephone.nameRef,telephone.paid_date,telephone.invoiceNo,telephone.total_charges_comp,telephone.cur_vat,telephone.c_otherCharges as other_exp,'telep_client_nameref' as type,'telep' as job_type,telephone.cn_r_id,telephone.cn_t_id FROM telephone WHERE telephone.assignDate BETWEEN '" . $search_2 . "' AND '" . $search_3 . "' and (telephone.orgName IN (" . $all_cz['all_cz'] . ")) AND telephone.deleted_flag=0 AND  (telephone.orderCancelatoin=1 OR telephone.order_cancel_flag=1) AND order_cancelledby='Client' UNION ALL SELECT translation.id,translation.nameRef,translation.paid_date,translation.invoiceNo,translation.total_charges_comp,translation.cur_vat,translation.c_otherCharg as other_exp,'trans_lsuk_nameref' as type,'translation' as job_type,translation.cn_r_id,translation.cn_t_id FROM translation WHERE translation.asignDate BETWEEN '" . $search_2 . "' AND '" . $search_3 . "' AND (translation.orgName IN (" . $all_cz['all_cz'] . ")) AND translation.deleted_flag=0 AND  (translation.orderCancelatoin=1 OR translation.order_cancel_flag=1) AND order_cancelledby='LSUK' UNION ALL SELECT translation.id,translation.nameRef,translation.paid_date,translation.invoiceNo,translation.total_charges_comp,translation.cur_vat,translation.c_otherCharg as other_exp,'trans_client_nameref' as type,'translation' as job_type,translation.cn_r_id,translation.cn_t_id FROM translation WHERE translation.asignDate BETWEEN '" . $search_2 . "' AND '" . $search_3 . "' AND (translation.orgName IN (" . $all_cz['all_cz'] . ")) AND translation.deleted_flag=0 AND  (translation.orderCancelatoin=1 OR translation.order_cancel_flag=1) AND order_cancelledby='Client'";

    //  $gpcn_data = "SELECT interpreter.id,interpreter.nameRef,interpreter.paid_date,interpreter.invoiceNo,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.c_otherexpns as other_exp,'int_lsuk_nameref' as type,'f2f' as job_type FROM interpreter WHERE interpreter.assignDate BETWEEN '".$search_2."' AND '".$search_3."' AND (interpreter.orgName IN (".$all_cz['all_cz'].")) AND interpreter.deleted_flag=0 AND  (interpreter.orderCancelatoin=1 OR interpreter.order_cancel_flag=1) AND order_cancelledby='LSUK' UNION ALL SELECT interpreter.id,interpreter.nameRef,interpreter.paid_date,interpreter.invoiceNo,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.c_otherexpns as other_exp,'int_client_nameref' as type,'f2f' as job_type FROM interpreter WHERE interpreter.assignDate BETWEEN '".$search_2."' AND '".$search_3."' AND (interpreter.orgName IN (".$all_cz['all_cz'].")) AND interpreter.deleted_flag=0 AND  (interpreter.orderCancelatoin=1 OR interpreter.order_cancel_flag=1) AND order_cancelledby='Client' UNION ALL SELECT telephone.id,telephone.nameRef,telephone.paid_date,telephone.invoiceNo,telephone.total_charges_comp,telephone.cur_vat,telephone.c_otherCharges as other_exp,'telep_lsuk_nameref' as type,'telephone' as job_type FROM telephone WHERE telephone.assignDate BETWEEN '".$search_2."' AND '".$search_3."' AND (telephone.orgName IN (".$all_cz['all_cz'].")) AND telephone.deleted_flag=0 AND  (telephone.orderCancelatoin=1 OR telephone.order_cancel_flag=1) AND order_cancelledby='LSUK' UNION ALL SELECT telephone.id,telephone.nameRef,telephone.paid_date,telephone.invoiceNo,telephone.total_charges_comp,telephone.cur_vat,telephone.c_otherCharges as other_exp,'telep_client_nameref' as type,'telep' as job_type FROM telephone WHERE telephone.assignDate BETWEEN '".$search_2."' AND '".$search_3."' and (telephone.orgName IN (".$all_cz['all_cz'].")) AND telephone.deleted_flag=0 AND  (telephone.orderCancelatoin=1 OR telephone.order_cancel_flag=1) AND order_cancelledby='Client' UNION ALL SELECT translation.id,translation.nameRef,translation.paid_date,translation.invoiceNo,translation.total_charges_comp,translation.cur_vat,translation.c_otherCharg as other_exp,'trans_lsuk_nameref' as type,'translation' as job_type FROM translation WHERE translation.asignDate BETWEEN '".$search_2."' AND '".$search_3."' AND (translation.orgName IN (".$all_cz['all_cz'].")) AND translation.deleted_flag=0 AND  (translation.orderCancelatoin=1 OR translation.order_cancel_flag=1) AND order_cancelledby='LSUK' UNION ALL SELECT translation.id,translation.nameRef,translation.paid_date,translation.invoiceNo,translation.total_charges_comp,translation.cur_vat,translation.c_otherCharg as other_exp,'trans_client_nameref' as type,'translation' as job_type FROM translation WHERE translation.asignDate BETWEEN '".$search_2."' AND '".$search_3."' AND (translation.orgName IN (".$all_cz['all_cz'].")) AND translation.deleted_flag=0 AND  (translation.orderCancelatoin=1 OR translation.order_cancel_flag=1) AND order_cancelledby='Client'";
    $exe_gpcn = mysqli_query($con, $gpcn_data);
    $exe_gpcn2 = mysqli_query($con, $gpcn_data);
    //  while($row = mysqli_fetch_assoc($exe_gpcn)){
    //    print_r($row);
    //    echo "<br><br><br>";
    //  }
    // echo mysqli_num_rows($exe_gpcn);
    //  die();
    //  exit();

    $lne = $cne = array();
    $ls_nm = $cn_nm = "";
    for ($i = 0; $i < count($months); $i++) :
      if (trim($months[$i]['cancel']['total_lsuk_nameref']) != "") {
        if ($ls_nm != "") {
          $ls_nm .= "|" . $months[$i]['cancel']['total_lsuk_nameref'];
        } else {
          $ls_nm .= $months[$i]['cancel']['total_lsuk_nameref'];
        }
      }
      if (trim($months[$i]['cancel']['total_client_nameref']) != "") {
        if ($cn_nm != "") {
          $cn_nm .= "|" . $months[$i]['cancel']['total_client_nameref'];
        } else {
          $cn_nm .= $months[$i]['cancel']['total_client_nameref'];
        }
      }


    endfor;

    if ($ls_nm != "") {
      $lne = explode("|", $ls_nm);
    }
    if ($cn_nm != "") {
      $cne = explode("|", $cn_nm);
    }


    // if($ls_nm!=""){
    //   $lne=explode("|",$ls_nm);
    //   echo "<h1>LSUK ".count($lne)."</h1>";
    //   foreach($lne as $ln){
    //     echo $ln."<br>";
    //   }
    // }else{
    //   echo "<h1>LSUK 0</h1>";
    // }

    // echo "<br><br><br>";
    // if($cn_nm!=""){
    //   $cne=explode("|",$cn_nm);
    //   echo "<h1>Client ".count($cne)."</h1>";
    //   foreach($cne as $cn){
    //     echo $cn."<br>";
    //   }
    // }else{
    //   echo "<h1>Client 0</h1>";
    // }


    // die();
    // exit();



    //  echo "<pre>"; print_r($months);
    ?>
  </section>
  <div class="modals_cancelled">
    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#lsuk_cancelled_detail">
          Launch demo modal
        </button> -->

    <div class="modal fade" id="lsuk_cancelled_detail" tabindex="-1" role="dialog" aria-labelledby="lsuk_cancelled_detailLabel" aria-hidden="true">
      <div class="modal-dialog" style='width:80%;' role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="lsuk_cancelled_detailLabel">Cancelled By LSUK</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Table id</th>
                  <th scope="col">Reference</th>
                  <th scope="col">InvoiceNo</th>
                  <th scope="col">Paid Date</th>
                  <th scope="col">Cost</th>
                  <th scope="col">Type</th>
                  <th scope="col">Reason</th>
                  <th scope="col">Shift to Cancelled by Client</th>

                  <!-- <th scope="col">Detail</th> -->
                </tr>
              </thead>
              <tbody>
                <tr>
                  <?php
                  $count_ls_can = 0;
                  $tcl = 0;
                  while ($row = mysqli_fetch_assoc($exe_gpcn)) {
                    if ($row['type'] == "int_lsuk_nameref" || $row['type'] == "telep_lsuk_nameref" || $row['type'] == "trans_lsuk_nameref") {
                      $count_ls_can = $count_ls_can + 1;
                      $pd_status = "";
                      if ($row['job_type'] == "f2f") {
                        $tcl = $row['total_charges_comp'] + ($row['total_charges_comp'] * $row['cur_vat']);
                        +$row['other_exp'];
                      } else {
                        $tcl = $row['total_charges_comp'] + ($row['total_charges_comp'] * $row['cur_vat']);
                      }

                      if (trim($row['paid_date']) == "1001-01-01") {
                        $pd_status = "Unpaid";
                      } else {
                        $pd_status = $row['paid_date'];
                      }
                      // $cancellation_type=$row["cn_t_id"]?$acttObj->read_specific("cd_title","cancellation_drops","cd_id=".$row["cn_t_id"])['cd_title']:"Other";
                      // $cancellation_type=$cancellation_type?str_replace("[DATE]",$row["cn_date"],$cancellation_type):$cancellation_type;
                      $cancellation_reason = $row["cn_r_id"] ? $acttObj->read_specific("cr_title", "cancel_reasons", "cr_id=" . $row["cn_r_id"])['cr_title'] : "Other";
                      array_push($reason_lsuk, $cancellation_reason);
                      $cn_lsuk_cost = $tcl + $cn_lsuk_cost;
                      echo "<tr>";
                      echo "<td>$count_ls_can</td>";
                      echo "<td>" . $row['id'] . "</td>";
                      echo "<td>" . $row['nameRef'] . "</td>";
                      echo "<td>" . $row['invoiceNo'] . "</td>";
                      echo "<td>" . $pd_status . "</td>";
                      echo "<td>£ " . $tcl . "</td>";
                      echo "<td>" . $row['job_type'] . "</td>";
                      echo "<td>" . $cancellation_reason . "</td>";
                      echo "<td><button type='button' class='btn btn-primary shift_cancel' id='".$row['job_type']."_".$row['id']."'>Shift</button></td>";
                      // echo "<td>".$cancellation_type."</td>";
                      echo "</tr>";
                    }
                  }
                  // foreach($lne as $lsuk_record){
                  //   if($lsuk_record!=""){
                  //     $count_ls_can= $count_ls_can+1;
                  //     $row = explode(",",$lsuk_record);
                  //     $tcl = $row[4]+($row[4]*$row[5]);
                  //     $cn_lsuk_cost=$tcl+$cn_lsuk_cost;
                  //     echo "<tr>";
                  //     echo "<td>$count_ls_can</td>";
                  //     echo "<td>".$row[0]."</td>";
                  //     echo "<td>".$row[1]."</td>";
                  //     echo "<td>".$row[6]."</td>";
                  //     echo "<td>".$row[2]."</td>";
                  //     echo "<td>£ ".$tcl."</td>";
                  //     echo "<td>".$row[3]."</td>";
                  //     echo "</tr>";

                  //   }
                  // } 
                  ?>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="client_cancelled_detail" tabindex="-1" role="dialog" aria-labelledby="client_cancelled_detailLabel" aria-hidden="true">
      <div class="modal-dialog" style='width:80%;' role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="client_cancelled_detailLabel">Cancelled By Client</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Table id</th>
                  <th scope="col">Reference</th>
                  <th scope="col">InvoiceNo</th>
                  <th scope="col">Paid Date</th>
                  <th scope="col">Cost</th>
                  <th scope="col">Type</th>
                  <th scope="col">Reason</th>
                  <!-- <th scope="col">Detail</th> -->
                </tr>
              </thead>
              <tbody>
                <tr>
                  <?php
                  $count_cl_can = 0;
                  $tcc = 0;
                  while ($row2 = mysqli_fetch_assoc($exe_gpcn2)) {
                    if ($row2['type'] == "int_client_nameref" || $row2['type'] == "telep_client_nameref" || $row2['type'] == "trans_client_nameref") {
                      $count_cl_can = $count_cl_can + 1;
                      $pd_status2 = "";
                      if ($row2['job_type'] == "f2f") {
                        $tcc =  $row2['total_charges_comp'] + ($row2['total_charges_comp'] * $row2['cur_vat']) + $row2['other_exp'];
                      } else {
                        $tcc = $row2['total_charges_comp'] + ($row2['total_charges_comp'] * $row2['cur_vat']);
                      }
                      if (trim($row2['paid_date']) == "1001-01-01") {
                        $pd_status2 = "Unpaid";
                      } else {
                        $pd_status2 = $row2['paid_date'];
                      }
                      // $cancellation_type2=$row2["cn_t_id"]?$acttObj->read_specific("cd_title","cancellation_drops","cd_id=".$row2["cn_t_id"])['cd_title']:"Other";
                      // $cancellation_type2=$cancellation_type2?str_replace("[DATE]",$row2["cn_date"],$cancellation_type2):$cancellation_type2;
                      $cancellation_reason2 = $row2["cn_r_id"] ? $acttObj->read_specific("cr_title", "cancel_reasons", "cr_id=" . $row2["cn_r_id"])['cr_title'] : "Other";
                      array_push($reason_client, $cancellation_reason2);
                      $cn_client_cost = $tcc + $cn_client_cost;
                      echo "<tr>";
                      echo "<td>$count_cl_can</td>";
                      echo "<td>" . $row2['id'] . "</td>";
                      echo "<td>" . $row2['nameRef'] . "</td>";
                      echo "<td>" . $row2['invoiceNo'] . "</td>";
                      echo "<td>" . $pd_status2 . "</td>";
                      echo "<td>£ " . $tcc . "</td>";
                      echo "<td>" . $row2['job_type'] . "</td>";
                      echo "<td>" . $cancellation_reason2 . "</td>";
                      // echo "<td>".$cancellation_type2."</td>";
                      echo "</tr>";
                    }
                  }
                  // foreach($cne as $client_record){
                  //   if($client_record!=""){
                  //     $count_cl_can= $count_cl_can+1;
                  //     $row = explode(",",$client_record);
                  //     $tcc = $row[4]+($row[4]*$row[5]);
                  //     $cn_client_cost=$tcc+$cn_client_cost;
                  //     echo "<tr>";
                  //     echo "<td>$count_cl_can</td>";
                  //     echo "<td>".$row[0]."</td>";
                  //     echo "<td>".$row[1]."</td>";
                  //     echo "<td>".$row[6]."</td>";
                  //     echo "<td>".$row[2]."</td>";
                  //     echo "<td>£ ".$tcc."</td>";
                  //     echo "<td>".$row[3]."</td>";
                  //     echo "</tr>";

                  //   }
                  // } 
                  ?>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <section>
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <h3 style="margin-top:5rem;">TOTAL JOBS GRAPH</h3>

          <canvas id="bar-chart"></canvas>
        </div>
      </div>
    </div>
  </section>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

  <script>
    var ctx = document.getElementById("bar-chart").getContext("2d");

    var mybarChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [<?php
                  $monthslabel = "";
                  foreach ($months as $m) :
                    $monthslabel .= "'" . $m['month'] . "',";

                  endforeach;
                  echo rtrim($monthslabel);
                  ?>],
        datasets: [{
            label: 'Total',
            backgroundColor: "red",
            data: [<?php
                    $f2f = "";
                    for ($i = 0; $i < count($months); $i++) :
                      $f2f .= "'" . $months[$i]['record']['total'] . "',";
                    endfor;
                    echo rtrim($f2f, ',');
                    ?>]
          },
          {
            label: 'F2F',
            backgroundColor: "#5969ff",
            data: [<?php
                    $f2f = "";
                    for ($i = 0; $i < count($months); $i++) :
                      $f2f .= "'" . $months[$i]['record']['interp'] . "',";
                    endfor;
                    echo rtrim($f2f, ',');
                    ?>]
          },
          {
            label: 'Telephone',
            backgroundColor: "#ff407b",
            data: [<?php
                    $telp = "";
                    for ($i = 0; $i < count($months); $i++) :
                      $telp .= "'" . $months[$i]['record']['telep'] . "',";
                    endfor;
                    echo rtrim($telp, ',');
                    ?>]
          },
          {
            label: 'Translation',
            backgroundColor: "#2ec551",
            data: [<?php
                    $trans = "";
                    for ($i = 0; $i < count($months); $i++) :
                      $trans .= "'" . $months[$i]['record']['trans'] . "',";
                    endfor;
                    echo rtrim($trans, ',');
                    ?>]
          }
        ]
      },

      options: {
        legend: {
          display: true,
          position: 'top',
          labels: {
            fontColor: "#71748d",
          }
        },
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }
    });
  </script>
  <div class="container" style="margin-top:5rem;">
    <div class="table1">
      <?php
      $cost_of_jobs = $no_of_jobs = $canc_jobs = "";
      $canc_by_lsuk = $canc_by_client = 0;
      $no_of_jobs_interp = $no_of_jobs_telep = $no_of_jobs_trans = 0;
      $canc_by_lsuk_trans =  $canc_by_lsuk_interp = $canc_by_lsuk_telep = 0;
      $canc_by_client_trans =  $canc_by_client_interp = $canc_by_client_telep = 0;

      for ($i = 0; $i < count($months); $i++) :
        $cost_of_jobs += $months[$i]['cost']['cost_total'];
        $no_of_jobs += $months[$i]['record']['total'];
        $no_of_jobs_interp += $months[$i]['record']['interp'];
        $no_of_jobs_telep += $months[$i]['record']['telep'];
        $no_of_jobs_trans += $months[$i]['record']['trans'];

        $canc_jobs += $months[$i]['cancel']['canc_total'];
        $canc_by_lsuk += $months[$i]['cancel']['canc_by_lsuk_total'];
        $canc_by_client += $months[$i]['cancel']['canc_by_client_total'];

        $canc_by_lsuk_interp += $months[$i]['cancel']['canc_by_lsuk_interp'];
        $canc_by_client_interp += $months[$i]['cancel']['canc_by_client_interp'];
        $canc_by_lsuk_telep += $months[$i]['cancel']['canc_by_lsuk_telep'];
        $canc_by_client_telep += $months[$i]['cancel']['canc_by_client_telep'];
        $canc_by_lsuk_trans += $months[$i]['cancel']['canc_by_lsuk_trans'];
        $canc_by_client_trans += $months[$i]['cancel']['canc_by_client_trans'];

      endfor;
      ?>
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th scope="col">Total Jobs</th>
            <th scope="col">Cost of Jobs</th>
            <th scope="col">F2F</th>
            <th scope="col">Telephone</th>
            <th scope="col">Translation</th>
            <!-- <th scope="col">Cancelled Jobs</th>
      <th scope="col">Cancelled By LSUK</th>
      <th scope="col">Cancelled By Client</th> -->

          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo $no_of_jobs; ?></td>
            <td><?php echo $cost_of_jobs; ?></td>
            <td><?php echo $no_of_jobs_interp; ?></td>
            <td><?php echo $no_of_jobs_telep; ?></td>
            <td><?php echo $no_of_jobs_trans; ?></td>
            <!-- <td><?php echo $canc_jobs; ?></td>
      <td><?php echo $canc_by_lsuk; ?></td>
      <td><?php echo $canc_by_client; ?></td> -->
            <!-- <td><?php echo "(f2f:$canc_by_lsuk_interp+telep:$canc_by_lsuk_telep+trans:$canc_by_lsuk_trans)=" . $canc_by_lsuk; ?></td>
      <td><?php echo "(f2f:$canc_by_client_interp+telep:$canc_by_client_telep+trans:$canc_by_client_trans)=" . $canc_by_client; ?></td> -->
          </tr>
        </tbody>
      </table>
    </div>






    <section>
      <div class="container">
        <div class="row">
          <div class="col-sm-12">
            <h3 style="margin-top:5rem;">TOTAL CANCELLATION GRAPH</h3>

            <canvas id="bar-chart-cancellation"></canvas>
          </div>
        </div>
      </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <script>
      var ctx = document.getElementById("bar-chart-cancellation").getContext("2d");

      var mybarChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: [<?php
                    $monthslabel = "";
                    foreach ($months as $m) :
                      $monthslabel .= "'" . $m['month'] . "',";

                    endforeach;
                    echo rtrim($monthslabel);
                    ?>],
          datasets: [{
              label: 'Total',
              backgroundColor: "red",
              data: [<?php
                      $f2f = "";
                      for ($i = 0; $i < count($months); $i++) :
                        $f2f .= "'" . $months[$i]['cancel']['canc_total'] . "',";
                      endfor;
                      echo rtrim($f2f, ',');
                      ?>]
            },
            {
              label: 'F2F',
              backgroundColor: "#5969ff",
              data: [<?php
                      $f2f = "";
                      for ($i = 0; $i < count($months); $i++) :
                        $f2f .= "'" . $months[$i]['cancel']['canc_interp'] . "',";
                      endfor;
                      echo rtrim($f2f, ',');
                      ?>]
            },
            {
              label: 'Telephone',
              backgroundColor: "#ff407b",
              data: [<?php
                      $telp = "";
                      for ($i = 0; $i < count($months); $i++) :
                        $telp .= "'" . $months[$i]['cancel']['canc_telep'] . "',";
                      endfor;
                      echo rtrim($telp, ',');
                      ?>]
            },
            {
              label: 'Translation',
              backgroundColor: "#2ec551",
              data: [<?php
                      $trans = "";
                      for ($i = 0; $i < count($months); $i++) :
                        $trans .= "'" . $months[$i]['cancel']['canc_trans'] . "',";
                      endfor;
                      echo rtrim($trans, ',');
                      ?>]
            }
          ]
        },

        options: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              fontColor: "#71748d",
            }
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true
              }
            }]
          }
        }
      });
    </script>

    <div class="table1" style="margin-top:5rem;">
      <?php
      $cost_of_jobs = $no_of_jobs = $canc_jobs = "";
      $canc_by_lsuk = $canc_by_client = 0;
      $canc_by_lsuk_trans =  $canc_by_lsuk_interp = $canc_by_lsuk_telep = 0;
      $canc_by_client_trans =  $canc_by_client_interp = $canc_by_client_telep = 0;

      for ($i = 0; $i < count($months); $i++) :
        $cost_of_jobs += $months[$i]['cost']['cost_total'];
        $no_of_jobs += $months[$i]['record']['total'];
        $canc_jobs += $months[$i]['cancel']['canc_total'];
        $cost_canc_total += $months[$i]['cancel']['cost_canc_total'];
        $canc_by_lsuk += $months[$i]['cancel']['canc_by_lsuk_total'];
        $canc_by_client += $months[$i]['cancel']['canc_by_client_total'];

        $canc_by_lsuk_interp += $months[$i]['cancel']['canc_by_lsuk_interp'];
        $canc_by_client_interp += $months[$i]['cancel']['canc_by_client_interp'];
        $canc_by_lsuk_telep += $months[$i]['cancel']['canc_by_lsuk_telep'];
        $canc_by_client_telep += $months[$i]['cancel']['canc_by_client_telep'];
        $canc_by_lsuk_trans += $months[$i]['cancel']['canc_by_lsuk_trans'];
        $canc_by_client_trans += $months[$i]['cancel']['canc_by_client_trans'];

      endfor;
      ?>
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th scope="col">Total Cancelled Jobs</th>
            <th scope="col">Cost of Cancelled Jobs</th>
            <th scope="col">Cancelled By LSUK</th>
            <th scope="col">Cancelled By Client</th>

          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo $canc_jobs; ?></td>
            <td><?php echo "<b>£ $cost_canc_total</b>" . " (LSUK= £ $cn_lsuk_cost+Client= £ $cn_client_cost)"; ?></td>
            <td class="get_detail" id='show_lsuk_cancelled_detail' data-toggle="modal" data-target="#lsuk_cancelled_detail"><?php echo $canc_by_lsuk; ?><a href="#"> (Show)</a> </td>
            <td class="get_detail" id='show_client_cancelled_detail' data-toggle="modal" data-target="#client_cancelled_detail"><?php echo $canc_by_client; ?><a href="#"> (Show)</a></td>
          </tr>
        </tbody>
      </table>
      <?php
      $del_interp =  $del_telep = $del_trans = $del_total_cost = $del_total = 0;
      for ($i = 0; $i < count($months); $i++) :
        $del_interp += $months[$i]['deleted']['del_interp'];
        $del_telep += $months[$i]['deleted']['del_telep'];
        $del_trans += $months[$i]['deleted']['del_trans'];
        $del_total_cost += $months[$i]['deleted']['del_total_cost'];
        $del_total += $months[$i]['deleted']['del_total'];
      endfor;
      ?>
      <table class="table table-striped table-hover mt-5">
        <thead>
          <tr>
            <th scope="col">Total Un-Processed Jobs</th>
            <th scope="col">Cost of Un-Processed Jobs</th>
            <th scope="col">BreakDown</th>
            <!-- <th scope="col">Cancelled By LSUK</th>
            <th scope="col">Cancelled By Client</th> -->

          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo $del_total; ?></td>
            <td><?php echo $del_total_cost; ?></td>
            <td><?php echo "<b>F2F: $del_interp | TP : $del_telep | TR : $del_trans</b>"; ?></td>
          </tr>
        </tbody>
      </table>

      <?php
      $shift_interp =  $shift_telep = $shift_total = 0;
      for ($i = 0; $i < count($months); $i++) :
        $shift_interp += $months[$i]['shifted']['shift_interp'];
        $shift_telep += $months[$i]['shifted']['shift_telep'];
        $shift_total += $months[$i]['shifted']['shift_total'];
      endfor;
      ?>
      <table class="table table-striped table-hover mt-5">
        <thead>
          <tr>
            <th scope="col">Total Jobs Shifted to Alternate Mode</th>
            <th scope="col">BreakDown</th>
            <!-- <th scope="col">Cancelled By LSUK</th>
            <th scope="col">Cancelled By Client</th> -->

          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo $shift_total; ?></td>
            <td><?php echo "<b>F2F: $shift_interp | TP : $shift_telep </b>"; ?></td>
          </tr>
        </tbody>
      </table>
    </div>





    <div class="table2" style="margin-top:5rem;">
      <h3>Jobs Cancelled BY LSUK and Clients Breakdown</h3>
      <?php
      $cost_of_jobs = $no_of_jobs = $canc_jobs = "";
      $canc_by_lsuk = $canc_by_client = 0;
      $canc_by_lsuk_trans =  $canc_by_lsuk_interp = $canc_by_lsuk_telep = $cost_canc_total = 0;
      $canc_by_client_trans =  $canc_by_client_interp = $canc_by_client_telep = 0;

      for ($i = 0; $i < count($months); $i++) :
        // $canc_by_lsuk24_interp += $months[$i]['cost']['cost_total'];
        // $no_of_jobs += $months[$i]['record']['total'];
        // $canc_jobs += $months[$i]['cancel']['canc_total'];
        $cost_canc_total += $months[$i]['cancel']['cost_canc_total'];
        $cost_canc_interp += $months[$i]['cancel']['cost_canc_interp'];
        $cost_canc_telephone += $months[$i]['cancel']['cost_canc_telephone'];
        $cost_canc_trans += $months[$i]['cancel']['cost_canc_trans'];

        $canc_by_lsuk += $months[$i]['cancel']['canc_by_lsuk_total'];
        $canc_by_client += $months[$i]['cancel']['canc_by_client_total'];

        $canc_by_lsuk_interp += $months[$i]['cancel']['canc_by_lsuk_interp'];
        $canc_by_client_interp += $months[$i]['cancel']['canc_by_client_interp'];
        $canc_by_lsuk_telep += $months[$i]['cancel']['canc_by_lsuk_telep'];
        $canc_by_client_telep += $months[$i]['cancel']['canc_by_client_telep'];
        $canc_by_lsuk_trans += $months[$i]['cancel']['canc_by_lsuk_trans'];
        $canc_by_client_trans += $months[$i]['cancel']['canc_by_client_trans'];

        $canc_by_lsuk24_interp += $months[$i]['cancel']['canc_by_lsuk24_interp'];
        $canc_by_client24_interp += $months[$i]['cancel']['canc_by_client24_interp'];
        $canc_by_lsuk48_interp += $months[$i]['cancel']['canc_by_lsuk48_interp'];
        $canc_by_client48_interp += $months[$i]['cancel']['canc_by_client48_interp'];
        $canc_by_lsuk48m_interp += $months[$i]['cancel']['canc_by_lsuk48m_interp'];
        $canc_by_client48m_interp += $months[$i]['cancel']['canc_by_client48m_interp'];
        $nct_interp += $months[$i]['cancel']['nct_interp'];

        $canc_by_lsuk24_telephone += $months[$i]['cancel']['canc_by_lsuk24_telephone'];
        $canc_by_client24_telephone += $months[$i]['cancel']['canc_by_client24_telephone'];
        $canc_by_lsuk48_telephone += $months[$i]['cancel']['canc_by_lsuk48_telephone'];
        $canc_by_client48_telephone += $months[$i]['cancel']['canc_by_client48_telephone'];
        $canc_by_lsuk48m_telephone += $months[$i]['cancel']['canc_by_lsuk48m_telephone'];
        $canc_by_client48m_telephone += $months[$i]['cancel']['canc_by_client48m_telephone'];
        $nct_telephone += $months[$i]['cancel']['nct_telephone'];


        $canc_by_lsuk24_trans += $months[$i]['cancel']['canc_by_lsuk24_trans'];
        $canc_by_client24_trans += $months[$i]['cancel']['canc_by_client24_trans'];
        $canc_by_lsuk48_trans += $months[$i]['cancel']['canc_by_lsuk48_trans'];
        $canc_by_client48_trans += $months[$i]['cancel']['canc_by_client48_trans'];
        $canc_by_lsuk48m_trans += $months[$i]['cancel']['canc_by_lsuk48m_trans'];
        $canc_by_client48m_trans += $months[$i]['cancel']['canc_by_client48m_trans'];
        $nct_trans += $months[$i]['cancel']['nct_trans'];

        $sum_lsuk24_total += $months[$i]['cancel']['sum_lsuk24_total'];
        $sum_client24_total += $months[$i]['cancel']['sum_client24_total'];
        $sum_lsuk48_total += $months[$i]['cancel']['sum_lsuk48_total'];
        $sum_client48_total += $months[$i]['cancel']['sum_client48_total'];
        $sum_lsuk48m_total += $months[$i]['cancel']['sum_lsuk48m_total'];
        $sum_client48m_total += $months[$i]['cancel']['sum_client48m_total'];
        $nct_total += $months[$i]['cancel']['nct_total'];

      endfor;
      ?>
      <h3 style="text-decoration:underline;margin-top:5rem;">Face to Face:</h3>

      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th scope='col'>Type</th>
            <th scope="col">24 Hours</th>
            <th scope="col">48 Hours</th>
            <th scope="col">More than 48</th>
            <th scope="col">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>LSUK</th>
            <td><?php echo $canc_by_lsuk24_interp; ?></td>
            <td><?php echo $canc_by_lsuk48_interp; ?></td>
            <td><?php echo $canc_by_lsuk48m_interp; ?></td>
            <td><?php echo $canc_by_lsuk_interp; ?></td>
          </tr>
          <tr>
            <th>Client</th>
            <td><?php echo $canc_by_client24_interp; ?></td>
            <td><?php echo $canc_by_client48_interp; ?></td>
            <td><?php echo $canc_by_client48m_interp; ?></td>
            <td><?php echo $canc_by_client_interp; ?></td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <th><?php echo $canc_by_lsuk_interp + $canc_by_client_interp . " (Cost = $cost_canc_interp)"; ?></th>
          </tr>
        </tbody>
      </table>



      <h3 style="text-decoration:underline;margin-top:5rem;">Telephone:</h3>

      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th scope='col'>Type</th>
            <th scope="col">24 Hours</th>
            <th scope="col">48 Hours</th>
            <th scope="col">More than 48</th>
            <th scope="col">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>LSUK</th>
            <td><?php echo $canc_by_lsuk24_telephone; ?></td>
            <td><?php echo $canc_by_lsuk48_telephone; ?></td>
            <td><?php echo $canc_by_lsuk48m_telephone; ?></td>
            <td><?php echo $canc_by_lsuk_telep; ?></td>
          </tr>
          <tr>
            <th>Client</th>
            <td><?php echo $canc_by_client24_telephone; ?></td>
            <td><?php echo $canc_by_client48_telephone; ?></td>
            <td><?php echo $canc_by_client48m_telephone; ?></td>
            <td><?php echo $canc_by_client_telep; ?></td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <th><?php echo $canc_by_lsuk_telep + $canc_by_client_telep . " (Cost = $cost_canc_telephone)"; ?></th>
          </tr>
        </tbody>
      </table>




      <h3 style="text-decoration:underline;margin-top:5rem;">Translation:</h3>

      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th scope='col'>Type</th>
            <th scope="col">24 Hours</th>
            <th scope="col">48 Hours</th>
            <th scope="col">More than 48</th>
            <th scope="col">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>LSUK</th>
            <td><?php echo $canc_by_lsuk24_trans; ?></td>
            <td><?php echo $canc_by_lsuk48_trans; ?></td>
            <td><?php echo $canc_by_lsuk48m_trans; ?></td>
            <td><?php echo $canc_by_lsuk_trans; ?></td>
          </tr>
          <tr>
            <th>Client</th>
            <td><?php echo $canc_by_client24_trans; ?></td>
            <td><?php echo $canc_by_client48_trans; ?></td>
            <td><?php echo $canc_by_client48m_trans; ?></td>
            <td><?php echo $canc_by_client_trans; ?></td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <th><?php echo $canc_by_lsuk_trans + $canc_by_client_trans . " (Cost = $cost_canc_trans)"; ?></th>
          </tr>
        </tbody>
      </table>

      <h3 style="text-decoration:underline;margin-top:5rem;">Summary:</h3>

      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th scope='col'>Type</th>
            <th scope="col">24 Hours</th>
            <th scope="col">48 Hours</th>
            <th scope="col">More than 48</th>
            <th scope="col">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>LSUK</th>
            <td><?php echo $sum_lsuk24_total; ?></td>
            <td><?php echo $sum_lsuk48_total; ?></td>
            <td><?php echo $sum_lsuk48m_total; ?></td>
            <td><?php echo $canc_by_lsuk; ?></td>
          </tr>
          <tr>
            <th>Client</th>
            <td><?php echo $sum_client24_total; ?></td>
            <td><?php echo $sum_client48_total; ?></td>
            <td><?php echo $sum_client48m_total; ?></td>
            <td><?php echo $canc_by_client; ?></td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <th><?php echo $canc_by_lsuk + $canc_by_client . " (Cost = $cost_canc_total)"; ?></th>
          </tr>
        </tbody>
      </table>






      <h3 style="text-decoration:underline;margin-top:5rem;">Cancellation Reasons Summary:</h3>

      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th scope='col'>Reason</th>
            <th scope="col">LSUK</th>
            <th scope="col">Client</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // print_r($reason_lsuk);
          // print_r($reason_client);

          $count_reason_lsuk =  array_count_values($reason_lsuk);
          $count_reason_client =  array_count_values($reason_client);
          ?>
          <tr>
            <td>Interpreter Family emergency </td>
            <td><?php echo $count_reason_lsuk["Interpreter  no longer available due to a family emergency "]; ?></td>
            <td><?php echo $count_reason_client["Interpreter  no longer available due to a family emergency "]; ?></td>
          </tr>
          <tr>
            <td>Interpreter forgot to attend</td>
            <td><?php echo $count_reason_lsuk["Interpreter forgot to attend"]; ?></td>
            <td><?php echo $count_reason_client["Interpreter forgot to attend"]; ?></td>
          </tr>
          <tr>
            <td>Interpreter couldn't find the location</td>
            <td><?php echo $count_reason_lsuk["Interpreter couldn't find the location"]; ?></td>
            <td><?php echo $count_reason_client["Interpreter couldn't find the location"]; ?></td>
          </tr>

          <tr>
            <td>Service user can't make it</td>
            <td><?php echo $count_reason_lsuk["Service user can't make it"]; ?></td>
            <td><?php echo $count_reason_client["Service user can't make it"]; ?></td>
          </tr>
          <tr>
            <td>Professional can't make it</td>
            <td><?php echo $count_reason_lsuk["Professional can't make it"]; ?></td>
            <td><?php echo $count_reason_client["Professional can't make it"]; ?></td>
          </tr>
          <tr>
            <td>Venue no longer available</td>
            <td><?php echo $count_reason_lsuk["Venue no longer available"]; ?></td>
            <td><?php echo $count_reason_client["Venue no longer available"]; ?></td>
          </tr>
          <tr>
            <td>Duplicate Booking</td>
            <td><?php echo $count_reason_lsuk["Duplicate Booking"]; ?></td>
            <td><?php echo $count_reason_client["Duplicate Booking"]; ?></td>
          </tr>
          <tr>
            <td>Wrong Booking</td>
            <td><?php echo $count_reason_lsuk["Wrong Booking"]; ?></td>
            <td><?php echo $count_reason_client["Wrong Booking"]; ?></td>
          </tr>
          <tr>
            <td>Need to rearrange</td>
            <td><?php echo $count_reason_lsuk["Need to rearrange"]; ?></td>
            <td><?php echo $count_reason_client["Need to rearrange"]; ?></td>
          </tr>
          <tr>
            <td>Service No Longer Required</td>
            <td><?php echo $count_reason_lsuk["Service No Longer Required"]; ?></td>
            <td><?php echo $count_reason_client["Service No Longer Required"]; ?></td>
          </tr>
          <tr>
            <td>Reason Not Mentioned</td>
            <td><?php echo $count_reason_lsuk["Reason Not Mentioned"]; ?></td>
            <td><?php echo $count_reason_client["Reason Not Mentioned"]; ?></td>
          </tr>
          <tr>
            <td>Interpreter was not fluent </td>
            <td><?php echo $count_reason_lsuk["Interpreter was not fluent"]; ?></td>
            <td><?php echo $count_reason_client["Interpreter was not fluent"]; ?></td>
          </tr>
          <tr>
            <td>Poorly Healthy Condition</td>
            <td><?php echo $count_reason_lsuk["Poorly Healthy Condition"]; ?></td>
            <td><?php echo $count_reason_client["Poorly Healthy Condition"]; ?></td>
          </tr>
          <tr>
            <td>BSL Cancellation </td>
            <td><?php echo $count_reason_lsuk["BSL Cancellation "]; ?></td>
            <td><?php echo $count_reason_client["BSL Cancellation "]; ?></td>
          </tr>
          <tr>
            <td>Other, Please specify</td>
            <td><?php echo $count_reason_lsuk["Other, Please specify"]; ?></td>
            <td><?php echo $count_reason_client["Other, Please specify"]; ?></td>
          </tr>
          <tr>
            <td>Other</td>
            <td><?php echo $count_reason_lsuk["Other"]; ?></td>
            <td><?php echo $count_reason_client["Other"]; ?></td>
          </tr>
        </tbody>
      </table>




    </div>
  </div>
</body>

</html>

<script>
  $(document).ready(function() {
    $(document).on("click", ".get_detail", function() {
      var get_id = this.id;
      console.log(get_id);
    });
  });
  $(document).on("click",".shift_cancel",function(){
    var shift_id = this.id;
    var shift_type= shift_id.split("_")[0];
    var tbd= shift_id.split("_")[1];

    $.ajax({
        url: 'ajaxporder.php',
        method: 'post',
        data: {
            'shift_id': shift_id,
            'shift_type': shift_type,
            'tbd': tbd
        },
        success: function(data) {
            window.location.reload();
        },
        error: function(xhr) {
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        }
    });

    // console.log(shift_id);
    // console.log(type);
    // console.log(tbd);

  });
</script>