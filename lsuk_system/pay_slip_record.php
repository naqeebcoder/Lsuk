<?php session_start();
$slip_id = $_GET['interpreter_id'];
$invoice = $_GET['invoice_number'];
if (isset($_SESSION['web_userId']) && $_SESSION['web_userId'] == $slip_id) {
  include '../source/db.php';
  include '../source/class.php';
} else {
  include 'db.php';
  include 'class.php';
}
$array_job_type = array(1 => "F2F", 2 => "Telephone", 3 => "Translation");
$get_salary_info = $acttObj->read_specific("*", "interp_salary", "invoice='$invoice'");
$fdate = $get_salary_info['frm'];
$tdate = $get_salary_info['todate'];
if (isset($_SESSION['web_userId']) && ($_SESSION['web_userId'] != $slip_id || $get_salary_info['interp'] != $_SESSION['web_userId'])) {
  echo "<br><br><div class='alert alert-danger col-sm-4 col-sm-offset-4'><h3>Sorry ! We coudn't found this record.</h3></div>";
} else {
  $table = 'interpreter';
  $row1 = $acttObj->read_specific("*,CONCAT('****',SUBSTRING(IF(acNo IS NULL or acNo = '', '00000000', acNo),5,8)) as acNo,CONCAT(LEFT(acntCode,2),'**',RIGHT(acntCode,2)) as acntCode,CONCAT(buildingName,' ',line1,' ',line2,' ',line3) as address", "interpreter_reg", "id=" . $slip_id);
  //$check_date="assignDate BETWEEN('$fdate') AND ('$tdate')";
  //$check_date_tr="asignDate BETWEEN('$fdate') AND ('$tdate')";
  $check_date = "paid_date='" . $get_salary_info['dated'] . "'";
  $check_date_tr = "paid_date='" . $get_salary_info['dated'] . "'";
  $check_salary_id = "salary_id=" . $get_salary_info['id'];
?>
  <title>Pay Slip Record</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
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

    @media print {
      .no {
        display: none;
      }
    }

    .print_btn {
      position: absolute;
      margin: 7px;
    }
  </style>
  <script>
    function popupwindow(url, title, w, h) {
      var left = (screen.width / 2) - (w / 2);
      var top = (screen.height / 2) - (h / 2);
      return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
    }
  </script>
  <button onclick="window.print()" type="button" class='btn btn-info no print_btn' name="submit" title="Press to Print"><i class="glyphicon glyphicon-print"></i></button>
  <a style="margin: 7px 55px;" class='btn btn-primary no' href="javascript:void(0)" onclick="popupwindow('reports_lsuk/pdf/rip_pay_slip.php?submit=<?php echo $invoice; ?>', 'Email Remittance Slip', 1000, 800);"><i class="glyphicon glyphicon-envelope"></i></a>
  <div id="block_container">
    <div id="bloc1">
      <h3 style="background-color:#FFF; color:#000; margin-left:10px;">Language Services UK Limited</h3>
    </div>
    <div id="bloc2"><img alt="" src="img/logo.png" height="100" width="145"></div>
    <h4 align="center">Translation | Interpreting | Transcription | Cross-Cultural Training & Development</h4>
    <hr style="border-top: 1px solid #8c8b8b; width:100%">
  </div>
  <div style="position:absolute; left: 5;"><span class="name"><?php echo $row1['name']; ?></span><br />
    <span class="address"><?php echo $row1['address']; ?><br /><?php echo $row1['city']; ?></span><br />
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
          <td><span class="date"><?php echo $misc->dated($get_salary_info['dated']); ?></span></td>
        </tr>
        <tr>
          <td bgcolor="#F4F4F4"><span class="date">Salary Date</span></td>
          <td><span class="date"><?php echo $misc->dated($get_salary_info['salary_date']); ?></span></td>
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
              <tr>
                <td align="center" bgcolor="#006666" scope="col" colspan="15">Interpreter Services</td>
              </tr>
              <tr>
                <th align="left" bgcolor="#006666" scope="col">#</th>
                <th align="left" bgcolor="#006666" scope="col">Job Reference</th>
                <th align="left" bgcolor="#006666" scope="col">Assignment Date</th>
                <th align="left" bgcolor="#006666" scope="col">Company</th>
                <th align="left" bgcolor="#006666" scope="col">Language</th>
                <th align="left" bgcolor="#006666" scope="col">Hours Worked</th>
                <th align="left" bgcolor="#006666" scope="col">Interpreting Charge</th>
                <th align="left" bgcolor="#006666" scope="col">Charge for Travel Cost</th>
                <th align="left" bgcolor="#006666" scope="col">Travel Time Charge</th>
                <th align="left" bgcolor="#006666" scope="col">Travel Cost</th>
                <th align="left" bgcolor="#006666" scope="col">Other Costs (Parking , Bridge Toll)</th>
                <th align="left" bgcolor="#006666" scope="col">Additional Pay</th>
                <th align="left" bgcolor="#006666" scope="col">VAT</th>
                <th align="left" bgcolor="#006666" scope="col">Deduction</th>
                <th align="left" bgcolor="#006666" scope="col">Total Charges</th>
              </tr>
              <?php $i = 1;
              $amount1 = 0;
              $interp_total = 0;
              $interp_ded_total = $interp_ni_dedu = $interp_tax_dedu = 0;

              if($get_salary_info['deleted_flag'] == 0){
                $str_where = "interpreter.deleted_flag = 0 and intrpName=$slip_id and intrp_salary_comit = 1 and $check_salary_id";
              }else {
                $get_interp_job_ids = $acttObj->read_specific("GROUP_CONCAT(job_id) as job_id", "hist_interp_salary", " tbl = 'inte' AND salary_id = ".$get_salary_info['id'])['job_id'];
                if($get_interp_job_ids){
                  $str_where = "interpreter.id IN (".$get_interp_job_ids.")";
                }else {
                  $str_where = "interpreter.deleted_flag = 1 and intrpName=$slip_id and intrp_salary_comit = 0 and $check_salary_id";
                }
              }

              $query_interp = "SELECT interpreter.id,interpreter.nameRef,interpreter.ni_dedu,interpreter.tax_dedu,interpreter.source,interpreter.assignDate, interpreter.orgName, interpreter.hoursWorkd, interpreter.chargInterp, interpreter.chargeTravel, interpreter.chargeTravelTime, interpreter.travelCost, interpreter.otherCost, interpreter.admnchargs, interpreter.deduction, interpreter.total_charges_interp, (interpreter.total_charges_interp*interpreter.int_vat) as vat_f2f 
              FROM interpreter
              LEFT JOIN invoice ON invoice.invoiceNo = interpreter.invoiceNo
              LEFT JOIN interpreter_reg ON interpreter_reg.id = interpreter.intrpName
              WHERE ".$str_where." order by assignDate";
              
              $result_interp = mysqli_query($con, $query_interp);
              while ($row_interp = mysqli_fetch_assoc($result_interp)) {
                $interp_ded_total += $row_interp['deduction'];
                $interp_ni_dedu += $row_interp['ni_dedu'];
                $interp_tax_dedu += $row_interp['tax_dedu'];
                $interp_vat = $row_interp['vat_f2f'] + $interp_vat;
                $interp_total = $row_interp['total_charges_interp'] + $row_interp['vat_f2f'] + $interp_total; ?>
                <tr>
                  <td align="left" bgcolor="#006666"><?php echo $i++; ?>&nbsp;</td>
                  <td align="left" bgcolor="#006666"><span class="desc"><?php echo explode("/",$row_interp['nameRef'])[2]; ?></span></td>
                  <td align="left" bgcolor="#006666"><?php echo $misc->dated($row_interp['assignDate']); ?></td>
                  <td align="left" bgcolor="#006666"><span class="desc"><?php echo $row_interp['orgName']; ?></span></td>
                  <td align="left" bgcolor="#006666"><span class="desc"><?php echo $row_interp['source']; ?></span></td>
                  <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['hoursWorkd']); ?></td>
                  <td height="21" align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['chargInterp']); ?></td>
                  <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['chargeTravel']); ?></td>
                  <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['chargeTravelTime']); ?></td>
                  <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['travelCost']); ?></td>
                  <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['otherCost']); ?></td>
                  <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['admnchargs']); ?></td>
                  <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['vat_f2f']); ?></td>
                  <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['deduction']); ?></td>
                  <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['total_charges_interp']); ?></td>
                </tr>
              <?php } ?>
              <tr>
                <td colspan="12" align="right" bgcolor="#006666">Total</td>
                <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($interp_vat); ?></td>
                <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($interp_ded_total); ?></td>
                <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($interp_total); ?></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <table width="100%" border="1" style="color:#FFF">
              <tr>
                <td align="center" bgcolor="#CC9900" scope="col" colspan="14">Telephone Interpreter Services</td>
              </tr>
              <tr>
                <th align="left" bgcolor="#CC9900" scope="col">#</th>
                <th align="left" bgcolor="#CC9900" scope="col">Job Reference</th>
                <th align="left" bgcolor="#CC9900" scope="col">Assignment Date</th>
                <th align="left" bgcolor="#CC9900" scope="col">Company</th>
                <th align="left" bgcolor="#CC9900" scope="col">Language</th>
                <th align="left" bgcolor="#CC9900" scope="col">Minutes Worked</th>
                <th align="left" bgcolor="#CC9900" scope="col">Rate/Hour</th>
                <th align="left" bgcolor="#CC9900" scope="col">Interpreting Charges</th>
                <th align="left" bgcolor="#CC9900" scope="col">Call Charges</th>
                <th align="left" bgcolor="#CC9900" scope="col">Other Charges</th>
                <th align="left" bgcolor="#CC9900" scope="col">Additional Pay</th>
                <th align="left" bgcolor="#CC9900" scope="col">VAT</th>
                <th align="left" bgcolor="#CC9900" scope="col">Deduction</th>
                <th align="left" bgcolor="#CC9900" scope="col">Total Charges</th>
              </tr>
              <?php $i = 1;
              $telep_total = 0;
              $telep_ded_total = $telep_ni_dedu = $telep_tax_dedu = 0;

              if($get_salary_info['deleted_flag'] == 0){
                $str_where_tele = "telephone.invoiceNo=invoice.invoiceNo and telephone.intrpName=interpreter_reg.id and telephone.deleted_flag = 0 and intrpName=$slip_id and intrp_salary_comit = 1 and $check_salary_id";
              }else {
                $get_interp_job_ids = $acttObj->read_specific("GROUP_CONCAT(job_id) as job_id", "hist_interp_salary", " tbl = 'tele' AND salary_id = ".$get_salary_info['id'])['job_id'];
                if($get_interp_job_ids){
                  $str_where_tele = "telephone.id IN (".$get_interp_job_ids.")";
                }else {
                  $str_where_tele = "telephone.invoiceNo=invoice.invoiceNo and telephone.intrpName=interpreter_reg.id and telephone.deleted_flag = 1 and intrpName=$slip_id and intrp_salary_comit = 0 and $check_salary_id";
                }
              }

              $query_telep = "SELECT telephone.id,telephone.nameRef,telephone.ni_dedu,telephone.tax_dedu,telephone.source,telephone.assignDate,telephone.orgName, telephone.hoursWorkd, telephone.rateHour, telephone.chargInterp, telephone.otherCharges, telephone.admnchargs, telephone.deduction, telephone.total_charges_interp, (telephone.total_charges_interp*telephone.int_vat) as vat_tp 
              FROM telephone
              LEFT JOIN invoice ON invoice.invoiceNo = telephone.invoiceNo
              LEFT JOIN interpreter_reg ON interpreter_reg.id = telephone.intrpName
              WHERE ".$str_where_tele."
              order by assignDate";
              $result_telep = mysqli_query($con, $query_telep);
              while ($row_telep = mysqli_fetch_assoc($result_telep)) {
                $telep_ded_total += $row_telep['deduction'];
                $telep_ni_dedu += $row_telep['ni_dedu'];
                $telep_tax_dedu += $row_telep['tax_dedu'];
                $telep_vat = $row_telep['vat_tp'] + $telep_vat;
                $telep_total = $row_telep['total_charges_interp'] + $row_telep['vat_tp'] + $telep_total; ?>
                <tr>
                  <td align="left" bgcolor="#CC9900"><?php echo $i++; ?>&nbsp;</td>
                  <td align="left" bgcolor="#CC9900"><span class="desc"><?php echo explode("/",$row_telep['nameRef'])[2]; ?></span></td>
                  <td align="left" bgcolor="#CC9900"><?php echo $misc->dated($row_telep['assignDate']); ?></td>
                  <td align="left" bgcolor="#CC9900"><span class="desc"><?php echo $row_telep['orgName']; ?></span></td>
                  <td align="left" bgcolor="#CC9900"><span class="desc"><?php echo $row_telep['source']; ?></span></td>
                  <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['hoursWorkd']); ?></td>
                  <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['rateHour']); ?></td>
                  <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['chargInterp']); ?></td>
                  <td align="left" bgcolor="#CC9900"><span class="desc"><?php echo $misc->numberFormat_fun($row_telep['calCharges']); ?></span></td>
                  <td align="left" bgcolor="#CC9900"><span class="desc"><?php echo $misc->numberFormat_fun($row_telep['otherCharges']); ?></span></td>
                  <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['admnchargs']); ?></td>
                  <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['vat_tp']); ?></td>
                  <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['deduction']); ?></td>
                  <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['total_charges_interp']); ?></td>
                </tr>
              <?php } ?>
              <tr>
                <td colspan="11" align="right" bgcolor="#CC9900">Total</td>
                <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($telep_vat); ?></td>
                <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($telep_ded_total); ?></td>
                <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($telep_total); ?></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <table width="100%" border="1" style="color:#FFF">
              <tr>
                <td align="center" bgcolor="#3399FF" scope="col" colspan="13">Translation Services</td>
              </tr>
              <tr>
                <th align="left" bgcolor="#3399FF" scope="col">#</th>
                <th align="left" bgcolor="#3399FF" scope="col">Job Reference</th>
                <th align="left" bgcolor="#3399FF" scope="col">Assignment Date</th>
                <th align="left" bgcolor="#3399FF" scope="col">Company</th>
                <th align="left" bgcolor="#3399FF" scope="col">Language</th>
                <th align="left" bgcolor="#3399FF" scope="col">Units</th>
                <th align="left" bgcolor="#3399FF" scope="col">Rate/Unit</th>
                <th align="left" bgcolor="#3399FF" scope="col">Translation Charges</th>
                <th align="left" bgcolor="#3399FF" scope="col">Other Charges</th>
                <th align="left" bgcolor="#3399FF" scope="col">Additional Pay</th>
                <th align="left" bgcolor="#3399FF" scope="col">VAT</th>
                <th align="left" bgcolor="#3399FF" scope="col">Deduction</th>
                <th align="left" bgcolor="#3399FF" scope="col">Total Charges</th>
              </tr>
              <?php $i = 1;
              $trans_total = 0;
              $trans_ded_total = $trans_ni_dedu = $trans_tax_dedu = 0;

              if($get_salary_info['deleted_flag'] == 0){
                $str_where_trans = "translation.invoiceNo=invoice.invoiceNo AND translation.intrpName=interpreter_reg.id AND translation.deleted_flag = 0 AND intrpName=$slip_id AND intrp_salary_comit = 1 AND $check_salary_id";
              }else {
                $get_interp_job_ids = $acttObj->read_specific("GROUP_CONCAT(job_id) as job_id", "hist_interp_salary", " tbl = 'tran' AND salary_id = ".$get_salary_info['id'])['job_id'];
                if($get_interp_job_ids){
                  $str_where_trans = "telephone.id IN (".$get_interp_job_ids.")";
                }else {
                  $str_where_trans = "translation.invoiceNo=invoice.invoiceNo AND translation.intrpName=interpreter_reg.id AND translation.deleted_flag = 1 AND intrpName=$slip_id AND intrp_salary_comit = 0 AND $check_salary_id";
                }
              }

              $query_trans = "SELECT translation.id,translation.nameRef,translation.ni_dedu,translation.tax_dedu,translation.source,translation.asignDate, translation.orgName, translation.rpU, translation.numberUnit, translation.otherCharg, translation.admnchargs, translation.deduction, translation.total_charges_interp, (translation.total_charges_interp*translation.int_vat) as vat_tr 
              FROM translation
              LEFT JOIN invoice ON invoice.invoiceNo = translation.invoiceNo
              LEFT JOIN interpreter_reg ON interpreter_reg.id = translation.intrpName
              WHERE ".$str_where_trans." 
              order by asignDate";

              $result_trans = mysqli_query($con, $query_trans);
              while ($row_trans = mysqli_fetch_assoc($result_trans)) {
                $trans_ded_total += $row_trans['deduction'];
                $trans_ni_dedu += $row_trans['ni_dedu'];
                $trans_tax_dedu += $row_trans['tax_dedu'];
                $trans_vat = $row_trans['vat_tr'] + $trans_vat;
                $trans_total = ($row_trans['total_charges_interp'] + $row_trans['admnchargs']) + $row_trans['vat_tr'] + $trans_total; ?>
                <tr>
                  <td align="left" bgcolor="#3399FF"><?php echo $i++; ?>&nbsp;</td>
                  <td align="left" bgcolor="#3399FF"><span class="desc"><?php echo explode("/",$row_trans['nameRef'])[2]; ?></span></td>
                  <td align="left" bgcolor="#3399FF"><?php echo $misc->dated($row_trans['asignDate']); ?></td>
                  <td align="left" bgcolor="#3399FF"><span class="desc"><?php echo $row_trans['orgName']; ?></span></td>
                  <td align="left" bgcolor="#3399FF"><span class="desc"><?php echo $row_trans['source']; ?></span></td>
                  <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['numberUnit']);  ?></td>
                  <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['rpU']);  ?></td>
                  <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['numberUnit'] * $row_trans['rpU']);  ?></td>
                  <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['otherCharg']);  ?></td>
                  <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['admnchargs']); ?></td>
                  <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_telep['vat_tr']); ?></td>
                  <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['deduction']); ?></td>
                  <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['total_charges_interp'] + $row_trans['admnchargs']); ?></td>
                </tr>
              <?php } ?>
              <tr>
                <td colspan="10" align="right" bgcolor="#3399FF">Total</td>
                <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($trans_vat); ?></td>
                <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($trans_ded_total); ?></td>
                <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($trans_total); ?></td>
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
      $paid_requests = $acttObj->read_all("loan_requests.id,loan_requests.job_id,loan_requests.job_type,loan_requests.given_amount,loan_requests.duration,loan_requests.percentage,loan_dropdowns.title,loan_dropdowns.is_payable,request_paybacks.*", "request_paybacks,loan_requests,loan_dropdowns", "request_paybacks.request_id=loan_requests.id AND loan_requests.type_id=loan_dropdowns.id AND request_paybacks.deleted_flag=0 AND request_paybacks.salary_id=" . $get_salary_info['id']);
      $array_request_ids = $array_extra_request_ids = array();
      if ($paid_requests->num_rows > 0) {
        while ($row_paid = $paid_requests->fetch_assoc()) {
          $installment_amount = $extra_installment_amount = 0;
          if ($row_paid['is_payable'] == 1) {
            $total_given_amount += $row_paid['all_initial_amount'];
            $total_payable_amount += $row_paid['all_paid_amount'];
            $installment_amount = round($row_paid['paid_amount'], 2);
            array_push($array_request_ids, array("request_id" => $row_paid['id'], "job_id" => $row_paid['job_id'], "job_type" => $row_paid['job_type'], "initial_amount" => $row_paid['all_initial_amount'], "paid_amount" => $row_paid['all_paid_amount'], "installment_amount" => $installment_amount, "remaining_amount" => $row_paid['all_remaining_amount'], "loan_type" => $row_paid['title'], "date_taken" => $row_paid['paid_date']));
            $salary_payable_deduction += $installment_amount;
          } else {
            $total_extra_given_amount += $row_paid['all_initial_amount'];
            $total_non_payable_amount += $row_paid['all_paid_amount'];
            $extra_installment_amount = round($row_paid['paid_amount'], 2);
            array_push($array_extra_request_ids, array("request_id" => $row_paid['id'], "initial_amount" => $row_paid['all_initial_amount'], "paid_amount" => $row_paid['all_paid_amount'], "installment_amount" => $extra_installment_amount, "remaining_amount" => $row_paid['all_remaining_amount'], "loan_type" => $row_paid['title'], "date_taken" => $row_paid['paid_date']));
            $salary_non_payable_deduction += $extra_installment_amount;
          }
        }
      }
      $net_deductable_amount = $total_given_amount - $total_payable_amount ?: 0;
      $net_non_deductable_amount = $total_extra_given_amount - $total_non_payable_amount ?: 0;
      if ($salary_payable_deduction > 0) {
        echo '<table class="table-money-requests" style="margin-top: 3px;" width="100%">
            <tr><td colspan="6" align="center"><span style="font-weight: bold;">Installment Repayments</span></td></tr>
            <tr><td align="center" bgcolor="#F4F4F4">Request Title</td><td align="center" bgcolor="#F4F4F4">Initial Amount</td><td align="center" bgcolor="#F4F4F4">Paid Amount</td><td align="center" bgcolor="#F4F4F4">Date Taken</td><td align="center" bgcolor="#F4F4F4">Installment Amount</td><td align="center" bgcolor="#F4F4F4">Remaining</td></tr>';
        foreach ($array_request_ids as $key_payable => $val_payable) {
          echo '<tr>
                <td align="center">
                  <small>' . $val_payable['loan_type'] . ($val_payable['job_id'] ? "<br>" . $array_job_type[$val_payable['job_type']] . " Job ID: " . $val_payable['job_id'] : "") . '</small>
                </td>
                <td align="center">' . number_format($val_payable['initial_amount'], 2) . '</td>
                <td align="center">' . number_format($val_payable['paid_amount'], 2) . '</td>
                <td align="center">' . $misc->dated($val_payable['date_taken']) . '</td>
                <td align="center">' . number_format($val_payable['installment_amount'], 2) . '</td>
                <td align="center">' . number_format($val_payable['remaining_amount'], 2) . '</td>
              </tr>';
        }
        echo "</table>";
      }
      if ($salary_non_payable_deduction > 0) {
        echo '<table class="table-money-requests" style="margin-top: 3px;" width="100%">
            <tr><td colspan="6" align="center"><span style="font-weight: bold;">Additional Payments</span></td></tr>
            <tr><td align="center" bgcolor="#F4F4F4">Payment Title</td><td align="center" bgcolor="#F4F4F4">Total Amount</td><td align="center" bgcolor="#F4F4F4">Given Amount</td><td align="center" bgcolor="#F4F4F4">Given Date</td><td align="center" bgcolor="#F4F4F4">Receivable Amount</td><td align="center" bgcolor="#F4F4F4">Remaining Receivables</td></tr>';
        foreach ($array_extra_request_ids as $key_extra_payable => $val_extra_payable) {
          echo '<tr>
                <td align="center">
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
      if ($salary_payable_deduction == 0 && $salary_non_payable_deduction == 0) {
        $margin_total_table = '-110px';
      } else if (($salary_payable_deduction == 0 && $salary_non_payable_deduction > 0) || ($salary_payable_deduction > 0 && $salary_non_payable_deduction == 0)) {
        $margin_total_table = '-198px';
      } else if ($salary_payable_deduction > 0 && $salary_non_payable_deduction > 0) {
        $margin_total_table = '-265px';
      }
      $total_ni_deduction = ($get_salary_info['ni_dedu'] != 0 ? $get_salary_info['ni_dedu'] : $interp_ni_dedu + $telep_ni_dedu + $trans_ni_dedu );
      $total_tax_deduction = ($get_salary_info['tax_dedu'] != 0 ? $get_salary_info['tax_dedu'] : $interp_tax_dedu + $telep_tax_dedu + $trans_tax_dedu );
      ?>
      <br>
      <table width="30%">
        <tr>
          <td width="50%" bgcolor="#F4F4F4">Grand Total </td>
          <td align="center"><?php echo $misc->numberFormat_fun($get_salary_info['salry']); ?></td>
        </tr>
        <tr>
          <td bgcolor="#F4F4F4">Total VAT</td>
          <td align="center"><?php echo $misc->numberFormat_fun($interp_vat + $telep_vat + $trans_vat); ?></td>
        </tr>
        <tr>
          <td bgcolor="#F4F4F4">NI Deduction</td>
          <td align="center"><?php echo $misc->numberFormat_fun($total_ni_deduction); ?></td>
        </tr>
        <tr>
          <td bgcolor="#F4F4F4">Tax Deduction</td>
          <td align="center"><?php echo $misc->numberFormat_fun($total_tax_deduction); ?></td>
        </tr>
        <?php if ($get_salary_info['payback_deduction'] > 0) { ?>
          <tr>
            <td bgcolor="#F4F4F4" style="color:red">Payback Deduction</td>
            <td align="center"><?php echo number_format($get_salary_info['payback_deduction'], 2); ?></td>
          </tr>
        <?php }
        if ($get_salary_info['given_amount'] > 0) { ?>
          <tr>
            <td bgcolor="#F4F4F4" style="color:darkgreen">Additional Payment</td>
            <td align="center"><?php echo number_format($get_salary_info['given_amount'], 2); ?></td>
          </tr>
        <?php } ?>
        <tr>
          <td bgcolor="#F4F4F4"><b>Net Salary</b></td>
          <td align="center"><b><?php echo $misc->numberFormat_fun(($get_salary_info['salry'] - $total_ni_deduction - $total_tax_deduction - $salary_payable_deduction) + $salary_non_payable_deduction); ?></b></td>
        </tr>
      </table>
      <div>
        <h2>Thanks for business with us!</h2>
        <p>Suite 3 Davis House Lodge Causeway Trading Estate Lodge<br />Causeway - FishpondsBristol BS163JB</p>
      </div>
    </div>
  </div>
<?php } ?>