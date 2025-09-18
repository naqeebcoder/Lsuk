<?php include 'db.php';
include 'class.php';
include('inc_functions.php');

$table = 'interpreter';
$allow_Gen = true;
//Check if user has current action allowed
$allowed_type_idz = "141";
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Generate pay Slip</u> action for interpreters!<br>Kindly contact admin for further process.</h2></center>");
  }
}
$slip_id = $_GET['submit'];
$fdate = @$_GET['fdate'];
$tdate = @$_GET['tdate'];
$salary_date = @$_GET['salary_date'];
if (isset($salary_date)) {
  $exp_date = date("Y-m-d", strtotime($salary_date));
} else {
  $exp_date = date("Y-m-d", strtotime('last day of this month'));
}
$day_date = date('Y-m-d', strtotime($exp_date . " + 7 days"));
$day_name = date('D', strtotime($exp_date . " + 7 days"));
if ($day_name == 'Sat') {
  $day_date = date('Y-m-d', strtotime($day_date . " + 2 days"));
}
if ($day_name == 'Sun') {
  $day_date = date('Y-m-d', strtotime($day_date . " + 1 days"));
}

$query1 = "SELECT *,CONCAT('****',SUBSTRING(IF(acNo IS NULL or acNo = '', '00000000', acNo),5,8)) as acNo,CONCAT(LEFT(acntCode,2),'**',RIGHT(acntCode,2)) as acntCode,CONCAT(buildingName,' ',line1,' ',line2,' ',line3) as address FROM interpreter_reg where id=$slip_id";
$result1 = mysqli_query($con, $query1);
$row1 = mysqli_fetch_assoc($result1);
$email = $row1["email"];
if (isset($_POST['generate_slip'])) {
  $nmbr = $acttObj->get_id('interp_salary');
  if ($nmbr == NULL) {
    $nmbr = 0;
  }
  $abrv = "";
  $new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
  $invoice = 'LSUK' . $new_nmbr . '' . $abrv;
  $maxId = $nmbr;
  $update_salary = array('invoice' => $invoice, 'interp' => $slip_id, 'frm' => $fdate, 'todate' => $tdate, 'salary_date' => $day_date);
  $acttObj->update('interp_salary', $update_salary, "id=" . $maxId);
  $acttObj->insert("daily_logs", array("action_id" => 30, "user_id" => $_SESSION['userId'], "details" => "Salary ID: " . $maxId));
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Remittance</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="css/default.css" />
</head>
<script>
  function popupwindow(url, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
  }
</script>

<body>
  <form id="formone" action="" method="post">
    <style>
      * {
        border: 0;
        box-sizing: content-box;
        color: inherit;
        font-family: inherit;
        font-size: inherit;
        font-style: inherit;
        font-weight: inherit;
        line-height: inherit;
        list-style: none;
        padding: 0;
        text-decoration: none;
        vertical-align: top;
      }

      a:link:after,
      a:visited:after {
        content: normal !important;
      }

      table {
        border-collapse: collapse;
        border-spacing: 0;
      }

      td,
      th {
        border: 1px solid #CCC;
      }

      #block_container {
        text-align: center;
      }

      #block_container>div {
        display: inline-block;
        vertical-align: middle;
      }

      .table-money-requests td {
        padding: 2px;
      }

      .btn-action {
        padding: 0px 4px;
        background: red;
        color: white;
        border-radius: 2px;
        cursor: pointer;
      }

      .btn-edit {
        background: blue;
      }

      .editable_amount {
        border: 1px solid grey;
        width: 80px;
        float: right;
      }
    </style>
    <div>
      <?php
      $submitvalsalary = "Generate Salary Slip";
      $submitvalprint = "Print Remittance Advice";
      $submitvalemail = "Email Remittance Advice"; ?>
      <?php if (isset($_POST['generate_slip'])) { ?>
        <input type="button" class='prnt' onclick="window.print()" name="print_slip" value="<?php echo $submitvalprint;  ?>" style="cursor:pointer;background-color:#06F; color:#FFF; border:1px solid #09F;padding: 8px;" />

        <!--<a href="https://lsuk.org/lsuk_system/reports_lsuk/pdf/rip_pay_slip.php?submit=<?php echo $slip_id; ?>&fdate=<?php echo $fdate; ?>&tdate=<?php echo $tdate; ?>">-->
        <!--    <input type="button" class='prnt' name="submit" value="<?php echo $submitvalemail;  ?>" -->
        <!--style="cursor:pointer;background-color:#06F; color:#FFF; border:1px solid #09F;padding: 8px;"/></a>-->
        <a href="javascript:void(0)" onclick="popupwindow('reports_lsuk/pdf/rip_pay_slip.php?submit=<?php echo $invoice; ?>', 'Email Remittance Slip', 1000, 800);">
          <input type="button" class='prnt' name="submit" value="<?php echo $submitvalemail;  ?>" style="cursor:pointer;background-color:#06F; color:#FFF; border:1px solid #09F;padding: 8px;" /></a>

      <?php } else { ?>
        <input type="submit" onclick="return confirm('Are you sure to Generate the payslip?')" class='prnt' id="btn_generate_slip" name="generate_slip" value="<?php echo $submitvalsalary;  ?>" style="cursor:pointer;background-color:#06F; color:#FFF; border:1px solid #09F;padding: 8px;" />
      <?php } ?>
      <style>
        @media print {
          .prnt {
            display: none;
          }
        }
      </style>
    </div>

    <div id="block_container">

      <div id="bloc1">
        <h1 style="background-color:#FFF; color:#000; margin-left:10px; font-weight:bold">Language Services UK Limited</h1>
      </div>
      <div id="bloc2"><img alt="" src="img/logo.png" height="100" width="145"></div>
      <h3 align="center">Translation | Interpreting | Transcription | Cross-Cultural Training & Development</h3>
      <hr style="border-top: 1px solid #8c8b8b; width:100%">
    </div>
    <div style="position:absolute; left: 5;"><span class="name"><?php echo $row1['name']; ?></span><br />
      <span class="address"><?php echo $row1['address'] . ','; ?><br /><?php echo $row1['postCode'] . ', ' . $row1['city']; ?></span><br />
      <span style="text-decoration:underline"><?php echo $row1['email']; ?></span><br /><br />
    </div>
    <br /><br />
    <div align="left" style="position:absolute; margin-top:44px;">
      <div style="margin-left:5px; float:left;">
        <table>
          <tr>
            <td width="100" bgcolor="#F4F4F4">Slip #</td>
            <td><?php echo @$invoice; ?></td>
          </tr>
          <tr>
            <td bgcolor="#F4F4F4"><span class="date">Slip Date</span></td>
            <td><span class="date"><?php echo $misc->dated(date("Y-m-d")); ?></span></td>
          </tr>
          <tr>
            <td bgcolor="#F4F4F4"><span class="date">Salary Date</span></td>
            <td><span class="date">
                <?php echo $misc->dated($day_date); ?>
              </span>
            </td>
          </tr>
          <tr>
            <td bgcolor="#F4F4F4">From</td>
            <td><span class="date"><?php echo $misc->dated($fdate); ?></span></td>
          </tr>
          <tr>
            <td bgcolor="#F4F4F4">To</td>
            <td><span class="date"><?php echo $misc->dated($tdate); ?></span></td>
          </tr>
          <tr>
            <td bgcolor="#F4F4F4">NI / UTR #</td>
            <td><span class="date"><?php echo @$row1['ni']; ?></span></td>
          </tr>
          <tr>
            <td bgcolor="#F4F4F4">Bank Details </td>
            <td><span class="date">Sort Code # <?php echo @$row1['acntCode'] . '<br>Account No # ' . @$row1['acNo']; ?></span></td>
          </tr>
        </table>
      </div><br />
      <div style=" margin-left:5px;margin-top:10px; float:left; margin-right:5px;">
        <table width="100%">
          <tr>
            <td align="center" bgcolor="#F4F4F4"><span class="desc">DETAILS</span></td>
          </tr>
          <tr>
            <td>
              <table width="100%" border="1" style="color:#FFF">
                <tr style="background-color:#006666">
                  <td align="center"  scope="col" colspan="15">Interpreter Services</td>
                </tr>
                <tr style="background-color:#006666">
                  <th align="left"  scope="col">#</th>
                  <th align="left"  scope="col">Job Reference</th>
                  <th align="left"  scope="col">Assignment Date</th>
                  <th align="left"  scope="col">Company</th>
                  <th align="left"  scope="col">Language</th>
                  <th align="left"  scope="col">Hours Worked</th>
                  <th align="left"  scope="col">Interpreting Charge</th>
                  <th align="left"  scope="col">Charge for Travel Cost</th>
                  <th align="left"  scope="col">Travel Time Charge</th>
                  <th align="left"  scope="col">Travel Cost</th>
                  <th align="left"  scope="col">Other Costs (Parking , Bridge Toll)</th>
                  <th align="left"  scope="col">Additional Pay</th>
                  <th align="left"  scope="col">VAT</th>
                  <th align="left"  scope="col">Deduction</th>
                  <th align="left"  scope="col">Total Charges</th>
                </tr>
                <?php $i = 1;
                $amount1 = 0;
                $interp_total = 0;
                $interp_ded_total = $interp_ni_dedu = $interp_tax_dedu = 0;
                $interp_vat = 0;
                $con->query("SET SQL_BIG_SELECTS=1");
                $query_interp =
                  "SELECT interpreter.id,interpreter.nameRef,interpreter.approved_flag,interpreter.ni_dedu,interpreter.tax_dedu,interpreter.source,interpreter.assignDate, interpreter.orgName, interpreter.hoursWorkd, interpreter.chargInterp, interpreter.chargeTravel, interpreter.chargeTravelTime, interpreter.travelCost, interpreter.otherCost, interpreter.admnchargs, interpreter.deduction, interpreter.total_charges_interp, (interpreter.total_charges_interp*interpreter.int_vat) as vat_f2f FROM interpreter where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.pay_int=1 and interpreter.intrpName=$slip_id and interpreter.intrp_salary_comit = 0 and interpreter.hoursWorkd>0 and (interpreter.assignDate BETWEEN('$fdate')AND('$tdate') OR interpreter.assignDate < '$fdate') order by interpreter.assignDate";
                $result_interp = mysqli_query($con, $query_interp);
                while ($row_interp = mysqli_fetch_assoc($result_interp)) {
                  $interp_ded_total += $row_interp['deduction'];
                  $interp_ni_dedu += $row_interp['ni_dedu'];
                  $interp_tax_dedu += $row_interp['tax_dedu'];
                  $interp_vat = $row_interp['vat_f2f'] + $interp_vat;
                  $interp_total = $row_interp['total_charges_interp'] + $row_interp['vat_f2f'] + $interp_total; ?>

                    <tr style="background-color: #006666; <?php 
                        if ($row_interp['approved_flag'] == 0) {
                            echo 'background-color: #ff0000;';
                            $allow_Gen = false;
                        }
                        if ($row_interp['assignDate'] < $fdate) {
                            echo 'color: #ffffff; font-weight: bolder; border: 3px solid #ff0707;';
                        }
                    ?>">


                    <td align="left" ><?php echo $i++; ?>&nbsp;</td>
                    <td align="left" ><span class="desc"><?php echo explode("/",$row_interp['nameRef'])[2]; ?></span></td>
                    <td align="left" ><?php echo $misc->dated($row_interp['assignDate']); ?></td>
                    <td align="left" ><span class="desc"><?php echo $row_interp['orgName']; ?></span></td>
                    <td align="left" ><span class="desc"><?php echo $row_interp['source']; ?></span></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_interp['hoursWorkd']); ?></td>
                    <td height="21" align="left" ><?php echo $misc->numberFormat_fun($row_interp['chargInterp']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_interp['chargeTravel']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_interp['chargeTravelTime']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_interp['travelCost']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_interp['otherCost']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_interp['admnchargs']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_interp['vat_f2f']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_interp['deduction']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_interp['total_charges_interp']); ?></td>
                  </tr>
                <?php
                  if (isset($_POST['generate_slip'])) {
                    $update_f2f_data = array('intrp_salary_comit' => 1, 'paid_date' => date('Y-m-d'), 'salary_id' => $maxId);
                    $acttObj->update('interpreter', $update_f2f_data, "id=" . $row_interp['id']);
                  }
                } ?>
                <tr style="background-color:#006666">
                  <td colspan="12" align="right" >Total</td>
                  <td align="left" ><?php echo $misc->numberFormat_fun($interp_vat); ?></td>
                  <td align="left" ><?php echo $misc->numberFormat_fun($interp_ded_total); ?></td>
                  <td align="left" ><?php echo $misc->numberFormat_fun($interp_total); ?></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <table width="100%" border="1" style="color:#FFF">
                <tr style="background-color:#CC9900">
                  <td align="center"   scope="col" colspan="14">Telephone Interpreter Services</td>
                </tr>
                <tr style="background-color:#CC9900">
                  <th align="left"   scope="col">#</th>
                  <th align="left"   scope="col">Job Reference</th>
                  <th align="left"   scope="col">Assignment Date</th>
                  <th align="left"   scope="col">Company</th>
                  <th align="left"   scope="col">Language</th>
                  <th align="left"   scope="col">Minutes Worked</th>
                  <th align="left"   scope="col">Rate/Hour</th>
                  <th align="left"   scope="col">Interpretering Charge</th>
                  <th align="left"   scope="col">Call Charges</th>
                  <th align="left"   scope="col">Other Charges</th>
                  <th align="left"   scope="col">Additional Pay</th>
                  <th align="left"   scope="col">VAT</th>
                  <th align="left"   scope="col">Deduction</th>
                  <th align="left"   scope="col">Total Charges</th>
                </tr>
                <?php $i = 1;
                $telep_total = 0;
                $telep_ded_total = $telep_ni_dedu = $telep_tax_dedu = 0;
                $telep_vat = 0;
                $query_telep = "SELECT telephone.id,telephone.nameRef,telephone.approved_flag,telephone.ni_dedu,telephone.tax_dedu,telephone.source,telephone.assignDate,telephone.orgName, telephone.hoursWorkd, telephone.rateHour, telephone.chargInterp, telephone.otherCharges, telephone.admnchargs, telephone.deduction, telephone.total_charges_interp, (telephone.total_charges_interp*telephone.int_vat) as vat_tp FROM telephone where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.pay_int=1 and telephone.intrpName=$slip_id and telephone.intrp_salary_comit = 0 and telephone.hoursWorkd>0 and (telephone.assignDate BETWEEN('$fdate')AND('$tdate') OR telephone.assignDate < '$fdate') order by telephone.assignDate";
                $result_telep = mysqli_query($con, $query_telep);
                while ($row_telep = mysqli_fetch_assoc($result_telep)) {
                  $telep_ded_total += $row_telep['deduction'];
                  $telep_ni_dedu += $row_telep['ni_dedu'];
                  $telep_tax_dedu += $row_telep['tax_dedu'];
                  $telep_vat = $row_telep['vat_tp'] + $telep_vat;
                  $telep_total = $row_telep['total_charges_interp'] + $row_telep['vat_tp'] + $telep_total; ?>
                  <tr style="background-color: #CC9900; <?php 
                        if ($row_telep['approved_flag'] == 0) {
                            echo 'background-color: #ff0000;';
                            $allow_Gen = false;
                        }
                        if ($row_telep['assignDate'] < $fdate) {
                            echo 'color: #ffffff; font-weight: bolder; border: 3px solid #ff0707;';
                        }
                    ?>">
                    <td align="left"  ><?php echo $i++; ?>&nbsp;</td>
                    <td align="left"  ><span class="desc"><?php echo explode("/",$row_telep['nameRef'])[2]; ?></span></td>
                    <td align="left"  ><?php echo $misc->dated($row_telep['assignDate']); ?></td>
                    <td align="left"  ><span class="desc"><?php echo $row_telep['orgName']; ?></span></td>
                    <td align="left"  ><span class="desc"><?php echo $row_telep['source']; ?></span></td>
                    <td align="left"  ><?php echo $misc->numberFormat_fun($row_telep['hoursWorkd']); ?></td>
                    <td align="left"  ><?php echo $misc->numberFormat_fun($row_telep['rateHour']); ?></td>
                    <td align="left"  ><?php echo $misc->numberFormat_fun($row_telep['chargInterp']); ?></td>
                    <td align="left"  ><span class="desc"><?php echo $misc->numberFormat_fun($row_telep['calCharges']); ?></span></td>
                    <td align="left"  ><span class="desc"><?php echo $misc->numberFormat_fun($row_telep['otherCharges']); ?></span></td>
                    <td align="left"  ><?php echo $misc->numberFormat_fun($row_telep['admnchargs']); ?></td>
                    <td align="left"  ><?php echo $misc->numberFormat_fun($row_telep['vat_tp']); ?></td>
                    <td align="left"  ><?php echo $misc->numberFormat_fun($row_telep['deduction']); ?></td>
                    <td align="left"  ><?php echo $misc->numberFormat_fun($row_telep['total_charges_interp']); ?></td>
                  </tr>
                <?php
                  if (isset($_POST['generate_slip'])) {
                    $update_tp_data = array('intrp_salary_comit' => 1, 'paid_date' => date('Y-m-d'), 'salary_id' => $maxId);
                    $acttObj->update('telephone', $update_tp_data, "id=" . $row_telep['id']);
                  }
                } ?>
                <tr style="background-color:#CC9900">
                  <td colspan="11" align="right"  >Total</td>
                  <td align="left"  ><?php echo $misc->numberFormat_fun($telep_vat); ?></td>
                  <td align="left"  ><?php echo $misc->numberFormat_fun($telep_ded_total); ?></td>
                  <td align="left"  ><?php echo $misc->numberFormat_fun($telep_total); ?></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <table width="100%" border="1" style="color:#FFF">
                <tr style="background-color:#3399FF">
                  <td align="center"  scope="col" colspan="13">Translation Services</td>
                </tr>
                <tr style="background-color:#3399FF">
                  <th align="left"  scope="col">#</th>
                  <th align="left"  scope="col">Job Reference</th>
                  <th align="left"  scope="col">Assignment Date</th>
                  <th align="left"  scope="col">Company</th>
                  <th align="left"  scope="col">Language</th>
                  <th align="left"  scope="col">Units</th>
                  <th align="left"  scope="col">Rate/Unit</th>
                  <th align="left"  scope="col">Translation Charges</th>
                  <th align="left"  scope="col">Other Charges</th>
                  <th align="left"  scope="col">Additional Pay</th>
                  <th align="left"  scope="col">VAT</th>
                  <th align="left"  scope="col">Deduction</th>
                  <th align="left"  scope="col">Total Charges</th>
                </tr>
                <?php $i = 1;
                $trans_total = 0;
                $trans_ded_total = $trans_ni_dedu = $trans_tax_dedu = 0;
                $trans_vat = 0;
                $query_trans = "SELECT translation.id,translation.nameRef,translation.approved_flag,translation.ni_dedu,translation.tax_dedu,translation.source,translation.asignDate, translation.orgName, translation.rpU, translation.numberUnit, translation.otherCharg, translation.admnchargs, translation.deduction, translation.total_charges_interp, (translation.total_charges_interp*translation.int_vat) as vat_tr FROM translation WHERE translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.pay_int=1 and translation.intrpName=$slip_id and translation.intrp_salary_comit = 0 and translation.numberUnit>0 and (translation.asignDate BETWEEN('$fdate')AND('$tdate') OR translation.asignDate < '$fdate') order by translation.asignDate";
                $result_trans = mysqli_query($con, $query_trans);
                while ($row_trans = mysqli_fetch_assoc($result_trans)) {
                  $trans_ded_total += $row_trans['deduction'];
                  $trans_ni_dedu += $row_trans['ni_dedu'];
                  $trans_tax_dedu += $row_trans['tax_dedu'];
                  $trans_vat = $row_trans['vat_tr'] + $trans_vat;
                  $trans_total = ($row_trans['total_charges_interp'] + $row_trans['admnchargs']) + $row_trans['vat_tr'] + $trans_total; ?>
                  <tr style="background-color: #3399FF; <?php 
                        if ($row_trans['approved_flag'] == 0) {
                            echo 'background-color: #ff0000;';
                            $allow_Gen = false;
                        }
                        if ($row_trans['assignDate'] < $fdate) {
                            echo 'color: #ffffff; font-weight: bolder; border: 3px solid #ff0707;';
                        }
                    ?>">
                    <td align="left" ><?php echo $i++; ?>&nbsp;</td>
                    <td align="left"  ><span class="desc"><?php echo explode("/",$row_trans['nameRef'])[2]; ?></span></td>
                    <td align="left" ><?php echo $misc->dated($row_trans['asignDate']); ?></td>
                    <td align="left" ><span class="desc"><?php echo $row_trans['orgName']; ?></span></td>
                    <td align="left" ><span class="desc"><?php echo $row_trans['source']; ?></span></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_trans['numberUnit']);  ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_trans['rpU']);  ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_trans['numberUnit'] * $row_trans['rpU']);  ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_trans['otherCharg']);  ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_trans['admnchargs']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_trans['vat_tr']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_trans['deduction']); ?></td>
                    <td align="left" ><?php echo $misc->numberFormat_fun($row_trans['total_charges_interp'] + $row_trans['admnchargs']); ?></td>
                  </tr>

                <?php
                  if (isset($_POST['generate_slip'])) {
                    $update_tr_data = array('intrp_salary_comit' => 1, 'paid_date' => date('Y-m-d'), 'salary_id' => $maxId);
                    $acttObj->update('translation', $update_tr_data, "id=" . $row_trans['id']);
                  }
                } ?>
                <tr style="background-color:#3399FF">
                  <td colspan="10" align="right" >Total</td>
                  <td align="left" ><?php echo $misc->numberFormat_fun($trans_vat); ?></td>
                  <td align="left" ><?php echo $misc->numberFormat_fun($trans_ded_total); ?></td>
                  <td align="left" ><?php echo $misc->numberFormat_fun($trans_total); ?></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td bgcolor="#F4F4F4">&nbsp;</td>
          </tr>
        </table>
        <?php
        $net_deductable_amount = $total_given_amount = $total_payable_amount = $salary_payable_deduction = 0;
        $net_non_deductable_amount = $total_extra_given_amount = $total_non_payable_amount = $salary_non_payable_deduction = 0;
        $unpaid_requests = $acttObj->read_all("loan_requests.id,loan_requests.job_id,loan_requests.job_type,loan_requests.given_amount,loan_requests.duration,loan_requests.percentage,loan_dropdowns.title,loan_dropdowns.is_payable", "loan_requests,loan_dropdowns", "loan_requests.type_id=loan_dropdowns.id AND loan_requests.interpreter_id=" . $slip_id . " AND loan_requests.status=2 AND loan_requests.payable_date<='" . $exp_date . "'");
        $array_request_ids = $array_extra_request_ids = array();
        if ($unpaid_requests->num_rows > 0) {
          while ($row_unpaid = $unpaid_requests->fetch_assoc()) {
            $get_paid = $acttObj->read_specific("IFNULL(SUM(round(request_paybacks.paid_amount,2)),0) as paid_amount", "request_paybacks,loan_requests", "request_paybacks.request_id=loan_requests.id AND request_paybacks.deleted_flag=0 AND loan_requests.id=" . $row_unpaid['id']);
            if ($row_unpaid['is_payable'] == 1) {
              $installment_amount = 0;
              $total_given_amount += $row_unpaid['given_amount'];
              $total_payable_amount += $get_paid['paid_amount'];
              // If balance pending, deduct it form salary
              $remaining_balance = $row_unpaid['given_amount'] - $get_paid['paid_amount'];
              if ($remaining_balance > 0) {
                if (!in_array($row_unpaid['id'], $array_request_id)) {
                  $installment_amount = round($row_unpaid['given_amount'] / $row_unpaid['duration'], 2);
                  array_push($array_request_ids, array("request_id" => $row_unpaid['id'], "job_id" => $row_unpaid['job_id'], "job_type" => $row_unpaid['job_type'], "initial_amount" => $row_unpaid['given_amount'], "paid_amount" => $get_paid['paid_amount'], "installment_amount" => $installment_amount, "remaining_amount" => abs($remaining_balance - $installment_amount), "loan_type" => $row_unpaid['title'], "date_taken" => $row_unpaid['accepted_date']));
                  $salary_payable_deduction += $installment_amount;
                }
              }
            } else {
              $extra_installment_amount = 0;
              $total_extra_given_amount += $row_unpaid['given_amount'];
              $total_non_payable_amount += $get_paid['paid_amount'];
              // If balance pending, deduct it form salary
              $remaining_extra_balance = $row_unpaid['given_amount'] - $get_paid['paid_amount'];
              if ($remaining_extra_balance > 0) {
                if (!in_array($row_unpaid['id'], $array_extra_request_ids)) {
                  $extra_installment_amount = round($row_unpaid['given_amount'] / $row_unpaid['duration'], 2);
                  array_push($array_extra_request_ids, array("request_id" => $row_unpaid['id'], "initial_amount" => $row_unpaid['given_amount'], "paid_amount" => $get_paid['paid_amount'], "installment_amount" => $extra_installment_amount, "remaining_amount" => abs($remaining_extra_balance - $extra_installment_amount), "loan_type" => $row_unpaid['title'], "date_taken" => $row_unpaid['accepted_date']));
                  $salary_non_payable_deduction += $extra_installment_amount;
                }
              }
            }
          }
        }
        $net_deductable_amount = $total_given_amount - $total_payable_amount ?: 0;
        $net_non_deductable_amount = $total_extra_given_amount - $total_non_payable_amount ?: 0;
        if ($salary_payable_deduction > 0) {
          echo '<table class="table-money-requests" style="margin-top: 3px;" width="100%">
            <tr><td colspan="7" align="center"><span style="font-weight: bold;">Installment Repayments <input class="btn-action btn_close_editing" style="display:none;float: right;" type="button" onclick="close_editing()" value="Close Editing"/></span></td></tr>
            <tr><td align="center" bgcolor="#F4F4F4">Request Title</td><td align="center" bgcolor="#F4F4F4">Initial Amount</td><td align="center" bgcolor="#F4F4F4">Paid Amount</td><td align="center" bgcolor="#F4F4F4">Date Taken</td><td align="center" bgcolor="#F4F4F4">Installment Amount</td><td align="center" bgcolor="#F4F4F4">Remaining</td>' . (!isset($_POST['generate_slip']) ? '<td class="prnt" align="center" bgcolor="#F4F4F4">Action</td>' : '') . '</tr>';
          foreach ($array_request_ids as $key_payable => $val_payable) {
            echo '<tr class="tr_installment">
                <td align="center">
                  <input type="hidden" name="request_id[]" value="' . $val_payable['request_id'] . '" />
                  <input type="hidden" name="loan_type[]" value="' . $val_payable['loan_type'] . '" />
                  <small>' . $val_payable['loan_type'] . ($val_payable['job_id'] ? "<br>" . $array_job_type[$val_payable['job_type']] . " Job ID: " . $val_payable['job_id'] : "") . '</small>
                </td>
                <td align="center">
                  ' . number_format($val_payable['initial_amount'], 2) . '
                  <input type="hidden" name="initial_amount[]" value="' . $val_payable['initial_amount'] . '" />
                </td>
                <td align="center">
                  ' . number_format($val_payable['paid_amount'], 2) . '
                  <input type="hidden" name="paid_amount[]" value="' . $val_payable['paid_amount'] . '" />
                </td>
                <td align="center">
                  ' . $misc->dated($val_payable['date_taken']) . '
                  <input type="hidden" name="date_taken[]" value="' . $val_payable['date_taken'] . '" />
                </td>
                <td align="center" class="td_installment_amount">
                  <span class="text_installment">' . number_format($val_payable['installment_amount'], 2) . '</span>
                  <input min="0" max="' . $val_payable['installment_amount'] . '" oninput="update_prices(this)" class="editable_amount" type="hidden" name="installment_amount[]" value="' . $val_payable['installment_amount'] . '" />
                </td>
                <td align="center" class="td_remaining_amount">
                  <span class="text_remaining">' . number_format($val_payable['remaining_amount'], 2) . '</span>
                  <input type="hidden" name="remaining_amount[]" value="' . $val_payable['remaining_amount'] . '" />
                </td>' .
              (!isset($_POST['generate_slip']) ? '<td class="prnt" align="center"><button onclick="skip_row(this)" type="button" class="btn-action">Skip</button> <button onclick="edit_row(this)" type="button" class="btn-action btn-edit">Edit</button></td>' : '') .
              '</tr>';
          }
          echo "</table>";
        }
        if ($salary_non_payable_deduction > 0) {
          echo '<table class="table-money-requests" style="margin-top: 3px;" width="100%">
            <tr><td colspan="6" align="center"><span style="font-weight: bold;">Additional Payments</span></td></tr>
            <tr>
              <td align="center" bgcolor="#F4F4F4">Payment Title</td>
              <td align="center" bgcolor="#F4F4F4">Total Amount</td>
              <td align="center" bgcolor="#F4F4F4">Given Amount</td>
              <td align="center" bgcolor="#F4F4F4">Given Date</td>
              <td align="center" bgcolor="#F4F4F4">Receivable Amount</td>
              <td align="center" bgcolor="#F4F4F4">Remaining Receivables</td>
            </tr>';
          foreach ($array_extra_request_ids as $key_extra_payable => $val_extra_payable) {
            echo '<tr>
                <td align="center">
                  <input type="hidden" name="extra_request_id[]" value="' . $val_extra_payable['request_id'] . '" />
                  <input type="hidden" name="extra_initial_amount[]" value="' . $val_extra_payable['initial_amount'] . '" />
                  <input type="hidden" name="extra_paid_amount[]" value="' . $val_extra_payable['paid_amount'] . '" />
                  <input type="hidden" name="extra_installment_amount[]" value="' . $val_extra_payable['installment_amount'] . '" />
                  <input type="hidden" name="extra_remaining_amount[]" value="' . $val_extra_payable['remaining_amount'] . '" />
                  <input type="hidden" name="extra_loan_type[]" value="' . $val_extra_payable['loan_type'] . '" />
                  <input type="hidden" name="extra_date_taken[]" value="' . $val_extra_payable['date_taken'] . '" />
                  <small>' . $val_extra_payable['loan_type'] . '</small>
                </td>
                <td align="center">' . number_format($val_extra_payable['initial_amount'], 2) . '</td>
                <td align="center">' . number_format($val_extra_payable['paid_amount'], 2) . '</td>
                <td align="center">' . $misc->dated($val_extra_payable['date_taken']) . '</td>
                <td align="center">' . number_format($val_extra_payable['installment_amount'], 2) . '</td>
                <td align="center">' . number_format($val_extra_payable['remaining_amount'], 2) . '</td>
              </tr>';
          }
          echo "</table>";
        }
        $grand_total = $interp_total + $telep_total + $trans_total;
        $skip_payable_deduction = false;
        $overall_salary = $grand_total - $ni_dedu - $tax_dedu;
        if ($overall_salary - $salary_payable_deduction < 0) {
          // $calculated_salary = $overall_salary; // We can use it to convert to 0 when salary is less then 0
          // $skip_payable_deduction = true;
          $calculated_salary = $overall_salary - $salary_payable_deduction;
        } else {
          $calculated_salary = $overall_salary - $salary_payable_deduction;
        }
        ?>
        <br>
        <table width="30%">
          <tr>
            <td width="50%" bgcolor="#F4F4F4">Grand Total</td>
            <td align="center">
              <input type='hidden' id='overall_salary' value='<?=$overall_salary?>'/>
              <?php echo $misc->numberFormat_fun($grand_total); ?>
            </td>
          </tr>
          <tr>
            <td bgcolor="#F4F4F4">Total VAT</td>
            <td align="center">
              <?php 
                  $grand_total_vat = $interp_vat + $telep_vat + $trans_vat;
                  echo $misc->numberFormat_fun($grand_total_vat); 
                ?>
            </td>
          </tr>
          <tr>
            <td bgcolor="#F4F4F4">NI Deduction</td>
            <td align="center">
              <?php echo $ni_dedu = $misc->numberFormat_fun($interp_ni_dedu + $telep_ni_dedu + $trans_ni_dedu); ?>
              <input type="hidden" id="ni_deduction" value="<?=$ni_dedu?>"/>
            </td>
          </tr>
          <tr>
            <td bgcolor="#F4F4F4">Tax Deduction</td>
            <td align="center">
              <?php echo $tax_dedu = $misc->numberFormat_fun($interp_tax_dedu + $telep_tax_dedu + $trans_tax_dedu); ?>
              <input type="hidden" id="tax_deduction" value="<?=$tax_dedu?>"/>
            </td>
          </tr>
          <?php if ($salary_payable_deduction > 0) { ?>
            <tr class="tr_payable_deduction" <?=$skip_payable_deduction ? 'style="display:none"' : ''?>>
              <td bgcolor="#F4F4F4" style="color:red">Payback Deduction</td>
              <td align="center" class="td_payback_deduction"><?php echo number_format($salary_payable_deduction, 2); ?></td>
            </tr>
          <?php }
          if ($salary_non_payable_deduction > 0) { ?>
            <tr>
              <td bgcolor="#F4F4F4" style="color:darkgreen">Additional Payment</td>
              <td align="center" class="td_additional_payment"><?php echo number_format($salary_non_payable_deduction, 2); ?></td>
            </tr>
          <?php } ?>
          <tr>
            <td bgcolor="#F4F4F4">Net Salary</td>
            <td align="center" style="font-weight: bold;" class="td_net_salary">
              <?php echo $misc->numberFormat_fun($calculated_salary + $salary_non_payable_deduction); ?>
            </td>
          </tr>
        </table>
        <div>
          <h2>Thanks for business with us!</h2>
          <p>Suite 3 Davis House Lodge Causeway Trading Estate Lodge<br />Causeway - FishpondsBristol BS163JB</p>
        </div>
      </div>
    </div>
  </form>
</body>

</html>

<?php
$grand_deduction = $interp_ded_total + $telep_ded_total + $trans_ded_total;
if (isset($_POST['generate_slip'])) {
  //Add deductional payments
  $salary_payable_deduction = 0;
  if ($_POST['request_id']) {
    foreach ($_POST['request_id'] as $key => $request_id) {
      $acttObj->insert('request_paybacks', array("request_id" => $request_id, "paid_amount" => $_POST['installment_amount'][$key], "paid_date" => date('Y-m-d H:i:s'), "created_date" => date('Y-m-d H:i:s'), "salary_id" => $maxId, "all_initial_amount" => $_POST['initial_amount'][$key], "all_paid_amount" => $_POST['paid_amount'][$key], "all_remaining_amount" => $_POST['remaining_amount'][$key]));
      $salary_payable_deduction += $_POST['installment_amount'][$key];
    }
  }
  //Add extra additional payments
  if ($_POST['extra_request_id']) {
    foreach ($_POST['extra_request_id'] as $e_key => $e_request_id) {
      $acttObj->insert('request_paybacks', array("request_id" => $e_request_id, "paid_amount" => $_POST['extra_installment_amount'][$e_key], "paid_date" => date('Y-m-d H:i:s'), "created_date" => date('Y-m-d H:i:s'), "salary_id" => $maxId, "all_initial_amount" => $_POST['extra_initial_amount'][$e_key], "all_paid_amount" => $_POST['extra_paid_amount'][$e_key], "all_remaining_amount" => $_POST['extra_remaining_amount'][$e_key]));
    }
  }
  $update_total_data = array('deduction' => $grand_deduction, 'ni_dedu' => $ni_dedu, 'tax_dedu' => $tax_dedu, 'payback_deduction' => round($salary_payable_deduction, 2), 'given_amount' => round($salary_non_payable_deduction, 2), 'salry' => $grand_total);
  $acttObj->update('interp_salary', $update_total_data, "id=" . $maxId);

  
  /* ******************* COST OF SALES ************************* */

  // Getting New Voucher Counter
  $voucher_counter = getNextVoucherCount('JV');
  $voucher = 'JV-' . $voucher_counter;

  $current_date = date("Y-m-d");
  $description = '[Salary Slip] ' . $row1['name'] . '<br> Salary Slip Generated';
  $credit_amount = ($calculated_salary + $salary_non_payable_deduction);

  /* ********************** Account Payables ********************** */
  
  // getting balance amount
  $res = getCurrentBalances($con);

  // Insertion in tbl account_receivable
  $insert_data_rec = array(
    'voucher' => $voucher,
    'invoice_no' => $invoice,
    'dated' => $current_date,
    'company' => $row1['name'],
    'description' => $description,
    'credit' => $credit_amount,
    'balance' => ($res['payable_balance'] + $credit_amount),
    'posted_by' => $_SESSION['userId'],
    'tbl' => 'interp_salary'
  );

  insertAccountPayables($insert_data_rec);

  // Updating the new Voucher Counter
  updateVoucherCounter('JV', $voucher_counter);


  // Redirect to payslip view page
  echo "<script>window.location.href='pay_slip_record.php?invoice_number=$invoice&interpreter_id=$slip_id&invoice_form=$fdate&invoice_to=$tdate';</script>";
}
?>
<script>
  //window.onunload = refreshParent;
  function refreshParent() {
    window.opener.location.reload();
  }

  function skip_row(button, type = 0) {
    if (confirm("Are you sure to remove this row?")) {
      var row = button.parentNode.parentNode;
      var net_salary_element = document.querySelector('.td_net_salary');

      if (type == 0) {
        var installmentAmount = parseFloat(row.querySelector('[name="installment_amount[]"]').value);
        var paybackDeductionElement = document.querySelector('.td_payback_deduction');
      } else {
        var installmentAmount = parseFloat(row.querySelector('[name="extra_installment_amount[]"]').value);
        var paybackDeductionElement = document.querySelector('.td_additional_payment');
      }

      var currentPaybackDeduction = parseFloat(paybackDeductionElement.textContent);
      var currentNetSalary = parseFloat(net_salary_element.textContent);

      paybackDeductionElement.textContent = (currentPaybackDeduction - installmentAmount).toFixed(2);
      net_salary_element.textContent = (currentNetSalary + installmentAmount).toFixed(2);

      row.parentNode.removeChild(row);
    }
  }

  function edit_row(button, type = 0) {
    var row = button.closest("tr");
    var inputElement = row.querySelector("input[name='installment_amount[]']");

    if (inputElement) {
      inputElement.type = "text";
      inputElement.focus();
      document.querySelector(".btn_close_editing").style.display = "inline";
      document.querySelector("#btn_generate_slip").style.display = "none";
    }
  }

  function close_editing() {
    var inputElements = document.querySelectorAll("input[name='installment_amount[]']");
    inputElements.forEach(function (element) {
        element.type = "hidden";
    });
    document.querySelector('.btn_close_editing').style.display = "none";
    document.querySelector("#btn_generate_slip").style.display = "inline";
  }

  function update_prices(element) {
    var td_element = element.closest('.td_installment_amount');
    var net_salary_element = document.querySelector('.td_net_salary');
    var current_installment_amount = td_element.querySelector('.text_installment');
    var max_allowed_value = parseFloat(element.getAttribute('max'));
    var min_allowed_value = parseFloat(element.getAttribute('min'));

    // Ensure the entered value is not more than the max attribute
    if (!element.value || element.value < min_allowed_value || parseFloat(element.value) > max_allowed_value) {
      element.value = max_allowed_value;
    }

    current_installment_amount.textContent = element.value;

    var tr_element = element.closest('.tr_installment');
    var initial_amount = parseFloat(tr_element.querySelector('input[name="initial_amount[]"]').value);
    var paid_amount = parseFloat(tr_element.querySelector('input[name="paid_amount[]"]').value);
    var remaining_amount_input = tr_element.querySelector('input[name="remaining_amount[]"]');
    var remaining_amount_text = tr_element.querySelector('.text_remaining');
    var remaining_amount = Math.max(initial_amount - paid_amount - parseFloat(element.value), 0).toFixed(2);

    remaining_amount_input.value = remaining_amount;
    remaining_amount_text.textContent = remaining_amount;

    // Calculate the total payback deduction dynamically
    var total_payback_deduction = 0;
    var installment_amount_elements = document.querySelectorAll('.td_installment_amount input[name="installment_amount[]"]');
    installment_amount_elements.forEach(function(installment_element) {
      total_payback_deduction += parseFloat(installment_element.value);
    });
    // Calculate the total extra additionals dynamically
    var total_extra_additionals = 0;
    var extra_installment_amount_elements = document.querySelectorAll('input[name="extra_installment_amount[]"]');
    extra_installment_amount_elements.forEach(function(extra_additional_element) {
      total_extra_additionals += parseFloat(extra_additional_element.value);
    });
    
    var ni_deduction = parseFloat(document.querySelector('#ni_deduction').value);
    var tax_deduction = parseFloat(document.querySelector('#tax_deduction').value);
    var overall_salary = document.querySelector('#overall_salary').value;
    if (total_payback_deduction > 0) {
      document.querySelector('.td_payback_deduction').textContent = total_payback_deduction.toFixed(2);
      document.querySelector('.tr_payable_deduction').style.display = "contents";
    } else {
      document.querySelector('.td_payback_deduction').textContent = '0.00';
      document.querySelector('.tr_payable_deduction').style.display = "none";
    }
    net_salary_element.textContent = ((overall_salary - total_payback_deduction - ni_deduction - tax_deduction) + total_extra_additionals).toFixed(2);
  }

  
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formone');

    <?php if (!$allow_Gen): ?>
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            alert('Please approve pending approval orders highlighted as red below to generate the salary slip.');
        });
    }
    <?php endif; ?>
});
</script>
