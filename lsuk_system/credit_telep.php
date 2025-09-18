<?php
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
include 'inc_functions.php';

$table = 'telephone';
$invoice_id = $_GET['invoice_id'];
$allowed_type_idz = "77,89,163,180";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Credit Note</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
  }
}
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>LSUK Credit Note</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <style type="text/css">
    .table th,
    .table td {
      border: 1px solid #b8bcbd !important;
    }

    header span,
    header img {
      display: block;
      float: right;
    }

    header span {
      margin: 0 0 1em 1em;
      max-height: 25%;
      max-width: 60%;
      position: relative;
    }

    header img {
      max-height: 100%;
      max-width: 50%;
    }

    /* article */

    article,
    article address,
    table.meta,
    table.inventory {
      margin: 0 0 3em;
    }

    article:after {
      clear: both;
      content: "";
      display: table;
    }

    article h1 {
      clip: rect(0 0 0 0);
      position: absolute;
    }

    article address {
      float: left;
      font-weight: bold;
    }

    aside h1 {
      border: none;
      border-width: 0 0 1px;
      margin: 0 0 1em;
    }

    aside h1 {
      border-color: #999;
      border-bottom-style: solid;
    }

    .cutw {
      position: relative;
    }

    /* javascript */

    .add,
    .cut {
      border-width: 1px;
      display: block;
      font-size: .8em;
      padding: 0.25em;
      float: left;
      text-align: center;
      width: 0.8em;
    }

    .cut {
      font-size: 1em;
    }

    .add,
    .cut {
      background: #9AF;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
      background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
      background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
      border-color: #0076A3;
      color: #FFF;
      cursor: pointer;
      font-weight: bold;
      text-shadow: 0 -1px 2px rgba(0, 0, 0, 0.333);
    }

    .add {
      margin: -2.5em 0 0;
    }

    .add:hover {
      background: #00ADEE;
    }

    .cut {
      opacity: 0;
      position: absolute;
      top: 0;
      left: -1.5em;
    }

    tr:hover .cut {
      opacity: 1;
    }

    @media print {
      * {
        -webkit-print-color-adjust: exact;
      }

      html {
        background: none;
        padding: 0;
      }

      body {
        box-shadow: none;
        margin: 0;
      }

      span:empty {
        display: none;
      }

      .add,
      .cut {
        display: none;
      }

      table.inventory {
        margin-top: -25px;
      }

      .hd {
        display: none !important;
      }
    }

    @page {
      margin: 0;
    }

    .total {
      word-wrap: break-word;
    }

    #block_container {
      text-align: center;
    }

    #block_container>div {
      display: inline-block;
      vertical-align: middle;
    }

    .table_address td {
      line-height: 13px !important;
      border-top: none !important;
      font-weight: bold;
    }
  </style>

</head>

<body>
  <?php include "loadinvoicedbtelep.php";
  if (isset($_POST['submit'])) {
    $data = $acttObj->read_specific("*", "telephone", "id=" . $invoice_id);
    unset(
      $data['remrks'],
      $data['I_Comments'],
      $data['aloct_by'],
      $data['aloct_date'],
      $data['assignIssue'],
      $data['C_comments'],
      $data['amend_note'],
      $data['int_sig'],
      $data['noty'],
      $data['noty_reason'],
      $data['order_cancelledby'],
      $data['order_cancel_remarks'],
      $data['order_cancled_bystaff'],
      $data['edited_date'],
      $data['printed'],
      $data['sentemail'],
      $data['printedby'],
      $data['bookinType'],
      $data['buildingName'],
      $data['street'],
      $data['assignCity'],
      $data['postCode'],
      $data['inchPerson'],
      $data['inchContact'],
      $data['inchEmail'],
      $data['inchNo'],
      $data['line1'],
      $data['inchRoad'],
      $data['line2'],
      $data['inchCity'],
      $data['inchPcode'],
      $data['orgName'],
      $data['orgRef'],
      $data['orgContact'],
      $data['gender'],
      $data['rem_credit'],
      $data['jobDisp'],
      $data['dbs_checked'],
      $data['snote'],
      $data['aprove_flag'],
      $data['time_sheet'],
      $data['bookedVia'],
      $data['credit_note'],
      $data['exp_remrks'],
      $data['deleted_by'],
      $data['bacs'],
      $data['cheque'],
      $data['rDate'],
      $data['paid_date'],
      $data['namedbooked'],
      $data['edited_by'],
      $data['inchEmail2'],
      $data['cash_payment'],
      $data['amend_id'],
      $data['porder_email'],
      $data['int_vat'],
      $data['vat_no_int'],
      $data['vat_no_comp'],
      $data['is_temp'],
      $data['cn_t_id'],
      $data['cn_r_id'],
      $data['cn_date'],
      $data['pay_int'],
      $data['wt_tm'],
      $data['st_tm'],
      $data['fn_tm'],
      $data['tm_by'],
      $data['cl_sig'],
      $data['company_rate_data'],
      $data['interpreter_rate_data']
    );
    $data = json_encode($data);
    $check_existing = $acttObj->read_specific("CONCAT(id) as ids", "credit_notes", "order_id=" . $invoice_id . " AND order_type='tp' AND status=1")['ids'];
    if (!empty($check_existing)) {
      $acttObj->update_custom("credit_notes", array("status" => 0), "id IN (" . $check_existing . ")");
    }
    $acttObj->update_custom("credit_notes", array("status" => 0), "id IN (" . $check_existing . ")");
    $is_inserted = $acttObj->insert("credit_notes", array("order_id" => $row['id'], "order_type" => "tp", "data" => $data, "inserted_by" => $_SESSION['userId']));
    if ($is_inserted) {
      $insert_id = $acttObj->read_specific("id,CONCAT(DATE_FORMAT(dated,'%y%m'),'_',LPAD(id, 3, '0')) as credit_note_no", "credit_notes", "order_id=" . $invoice_id . " AND order_type='tp' AND status=1");

      $calCharges = $row['calCharges'];
      $C_otherCharges = $row['C_otherCharges'];
      $rateHour = $row['C_rateHour'];
      $hoursWorkd = $row['C_hoursWorkd'];
      $C_admnchargs = $row['C_admnchargs'];

      $sub_total = $misc->numberFormat_fun($calCharges + $C_otherCharges + ($rateHour * $hoursWorkd) + $C_admnchargs);
      $vat = $misc->numberFormat_fun($sub_total * $row['cur_vat']);

      if (@$commit == 1) {

        /* Insertion Query to Accounts: Income & Receivable Table
        - account_income : As Debit (balance - DueAmount)
        - account_receivable : As Credit (balance - DueAmount)
    */

        $current_date = date("Y-m-d");
        $description = '[Credit Note][Remote] ' . $row['source'] . " to " . $row['target'] . " on " . $misc->dated($assignDate);
        $credit_amount = $misc->numberFormat_fun($sub_total + $vat);

        // Check if record already exists
        $parameters = " invoice_no = '" . @$invoiceNo . $append_invoiceNo . "' AND dated = '" . $current_date . "' AND company = '" . @$abrv . "' AND credit = '" . $credit_amount . "'";

        $chk_exist = 0; //isIncomeRecordExists($parameters);

        if ($chk_exist < 1 && $credit_amount > 0) {

          // getting balance amount
          $res = getCurrentBalances($con);

          // Getting New Voucher Counter
          $voucher_counter = getNextVoucherCount('JV');

          // Updating the new Voucher Counter
          updateVoucherCounter('JV', $voucher_counter);

          $voucher = 'JV-' . $voucher_counter;

          // Insertion in tbl account_income
          $insert_data = array(
            'voucher' => $voucher,
            'invoice_no' => @$invoiceNo . $append_invoiceNo,
            'dated' => $current_date,
            'company' => @$abrv,
            'description' => $description,
            'debit' => $credit_amount,
            'balance' => ($res['balance'] - $credit_amount),
            'posted_by' => $_SESSION['userId'],
            'tbl' => $table
          );

          $jv_voucher = insertAccountIncome($insert_data);

          // Insertion in tbl account_receivable
          $insert_data_rec = array(
            'voucher' => $voucher,
            'invoice_no' => @$invoiceNo . $append_invoiceNo,
            'dated' => $current_date,
            'company' => @$abrv,
            'description' => $description,
            'credit' => $credit_amount,
            'balance' => ($res['recv_balance'] - $credit_amount),
            'posted_by' => $_SESSION['userId'],
            'tbl' => $table
          );

          $re_result = insertAccountReceivable($insert_data_rec);
          //$voucher = $re_result['voucher'];
          $new_voucher_id = $re_result['new_voucher_id'];

          if ($row['rAmount'] > 0) {
            // it will update the journal ledger table record for future use, as we are not inserting any reversal entry for rAmount
            updateJournalLedgerStatus('credit_note', 1, @$invoiceNo . $append_invoiceNo);
          }
        }
      }

      $acttObj->update("telephone", array("credit_note" => $insert_id['id'], "commit" => 0, "rAmount" => 0), array("id" => $invoice_id));
      $acttObj->update('partial_amounts', array("status" => 0), array("order_id" => $invoice_id, "status" => 1, "tbl" => "tp"));
      $acttObj->insert('daily_logs', ['action_id' => 17, 'user_id' => $_SESSION['userId'], 'details' => "Invoice No: " . @$invoiceNo]);
    }
  }

  $old_credit_note = $acttObj->read_specific("id,CONCAT(DATE_FORMAT(dated,'%y%m'),'_',LPAD(id, 3, '0')) as credit_note_no", "credit_notes", "order_id=" . $invoice_id . " AND order_type='tp' AND status=1");
  if ((isset($insert_id['id']) && !empty($insert_id['id']))) {
    $credit_note_number = $insert_id['credit_note_no'];
  } else if ((!isset($insert_id['id']) && !empty($old_credit_note['id']))) {
    $credit_note_number = $old_credit_note['credit_note_no'];
  } else {
    $credit_note_number = "Not created yet";
  }

  if (isset($_POST['company_email'])) {
    $makCompanyEmail = $_POST['comp_email'];
    if ($row['new_comp_id'] == 0) {
      $get_invEmail = $acttObj->read_specific("invEmail", "comp_reg", "id=" . $comp_id)['invEmail'];
      if (empty($get_invEmail)) {
        $acttObj->editFun('comp_reg', $comp_id, 'invEmail', $makCompanyEmail);
      }
    } else {
      $get_invEmail = $acttObj->read_specific("inchEmail", "private_company", "id=" . $row['new_comp_id'])['inchEmail'];
      if (empty($get_invEmail)) {
        $acttObj->editFun('private_company', $row['new_comp_id'], 'inchEmail', $makCompanyEmail);
      }
    } ?>
    <script>
      window.location.href = "./reports_lsuk/pdf/sendcreditnotemail.php?loaddb=load_credit_tp.php&htm=creditnote_tp.htm&invoice_id=<?php echo $invoice_id . '&company_email=' . $makCompanyEmail . '&table=telephone'; ?>";
    </script>
  <?php }
  if (!empty($credit_note)) {
    $append_credit_invoiceNo = $invoiceNo . "-0" . $acttObj->read_specific("count(*)-1 as counter", "credit_notes", "order_id=" . $invoice_id . " and order_type='tp'")['counter'];
  } ?>
  <br>
  <form action="" method="post" class="form-inline">

    <div class="form-group">
      <?php if (!isset($_POST['submit']) && $commit == 1) { ?>
        <input type="submit" class='prnt btn btn-primary hd' name="submit" value="Create Credit Note" />
      <?php }
      if (isset($_POST['submit']) || !empty($credit_note)) { ?>
        <input placeholder="Kindly enter invoice email" type="text" class='form-control hd prnt' name="comp_email" value="<?php echo $makCoEmail; ?>" />
        <input type="submit" class='prnt btn btn-primary hd' name="company_email" value="Send in Email" />
        <a class='prnt btn btn-info hd' name="btn_print" href="javascript:void(0)" onclick="window.print();">Print</a>
    </div>
  <?php } ?>
  </form>

  <div class="container-fluid">
    <header>
      <div id="block_container">

        <div id="bloc1">
          <h3 style="background-color:#FFF; color:#000; margin-left:165px;">Language Services UK Limited</h1>
        </div>
        <div id="bloc2"><img alt="" src="img/logo.png" height="60" width="120"></div>
        <h4 align="center">Translation | Interpreting | Transcription | Cross-Cultural Training & Development</h4>
        <hr style="border-top: 1px solid #8c8b8b; width:100%">
      </div>
    </header>

    <div class="col-sm-6 pull-left">
      <address style="margin-left:10px; font-weight:bold;">

        <p><?php echo @$orgzName; ?></p>
        <p><?php echo $c_buildingName; ?></p>
        <p><?php echo $c_line1; ?></p>
        <p><?php echo $c_line2; ?></p>
        <p><?php echo $c_streetRoad; ?></p>
        <p><?php echo $c_city; ?></p>
        <p><?php echo $c_postCode; ?></p>
      </address>
    </div>
    <div class="col-sm-6 pull-right">
      <table class="table table-bordered">
        <tr>
          <th class="bg-danger">CREDIT NOTE #</th>
          <td><?php echo $credit_note_number; ?></td>
        </tr>
        <tr>
          <th class="bg-danger">Invoice No #</th>
          <td><?php echo $invoiceNo; //$append_credit_invoiceNo; 
              ?></td>
        </tr>
        <tr>
          <th class="bg-danger"><span>DATE</span></th>
          <td><span class="date"><?php if (@$invoic_date == '0000-00-00') {
                                    $misc->dated(date("Y-m-d"));
                                  } else {
                                    echo $misc->dated(@$invoic_date);
                                  } ?></span></td>
        </tr>
        <tr>
          <th class="bg-danger"><span>BOOKING REF/NAME </span></th>
          <td><span id="prefix"><?php echo @$nameRef; ?></span></td>
        </tr>
        <tr>
          <th class="bg-danger">PURCHASE ORDER NO.</th>
          <td><?php echo @$porder; ?></td>
        </tr>
      </table>
    </div>
    <table class="table table-bordered">
      <thead class="bg-danger">
        <tr>
          <th>ASSIGNMENT DATE</th>
          <th>JOB</th>
          <th>JOB TYPE</th>
          <th>INVOICE DUE DATE</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a class="cut">-</a><span class="total"><?php echo $misc->dated($assignDate); ?></span></td>
          <td>Interpreting</td>
          <td>Telephone </td>
          <td><span class="total"><?php echo ($dueDate == '' || $dueDate == '0000-00-00') ? $misc->dated(date("Y-m-d", strtotime("+15 days"))) : $misc->dated($dueDate); ?></span></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered">
      <thead class="bg-danger">
        <tr>
          <th>JOB ADDRESS</th>
          <th>LINGUIST</th>
          <th>LANGUAGE</th>
          <th>CASE WORKER NAME</th>
          <th>FILE REFERENCE (CLIENT REFERENCE)</th>
          <th>BOOKING TYPE</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a class="cut">-</a><span class="total">N/A</span></td>
          <td><span class="total"><?php echo @$intrpName; ?></span></td>
          <td><span class="total"><?php echo @$source; ?></span></td>
          <td><span class="total"><?php echo @$orgContact; ?></span></td>
          <td><span class="total"><?php echo @$orgRef; ?></span></td>
          <td><?php echo @$bookinType; ?></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered" style="text-transform: uppercase;">
      <thead class="bg-danger">
        <tr>
          <th>Per Minute price</th>
          <th>Minutes</th>
          <th>Call Length Cost (£)</th>
          <th>Minimum Charge minutes / hours</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a class="cut">-</a><span class="total"><?php echo $misc->numberFormat_fun($rateHour); ?></span></td>
          <td><span class="total"><?php echo $misc->numberFormat_fun($hoursWorkd); ?></span></td>
          <td><?php echo $misc->numberFormat_fun($calCharges); ?></td>
          <?php
          //Code Added by Solworx to reverse the number format function 
          $makHoursWord = floatval(preg_replace('/[^\d.]/', '', $hoursWorkd));
          $makRateHour = floatval(preg_replace('/[^\d.]/', '', $rateHour));

          ?>
          <td><span class="total"><?php echo $misc->numberFormat_fun($makRateHour * $makHoursWord); ?></span></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered" style="text-transform: uppercase;">
      <thead class="bg-danger">
        <tr>
          <th>Other Expenses(£)</th>
          <th>ADMIN CHARGES</th>
          <th>job total (£)</th>
          <th>vat @ 20% (£)</th>
          <th>Invoice total (£)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a class="cut">-</a><span class="total"><?php echo $misc->numberFormat_fun($C_otherCharges); ?></span></td>
          <td><span class="total"><?php echo $misc->numberFormat_fun($C_admnchargs); ?></span></td>

          <?php $makCalCharges = floatval(preg_replace('/[^\d.]/', '', $calCharges)); ?>
          <td><span class="total">
              <?php echo $sub_total = $misc->numberFormat_fun($makCalCharges + $C_otherCharges + ($makRateHour * $makHoursWord) + $C_admnchargs); ?></span></td>
          <?php $makSub_total = floatval(preg_replace('/[^\d.]/', '', $sub_total));

          ?>
          <td><span class="total">
              <?php echo $vat = $misc->numberFormat_fun($makSub_total * $cur_vat); ?></span></td>
          <?php $makVat = floatval(preg_replace('/[^\d.]/', '', $vat));

          ?>
          <td><span class="total">
              <?php echo $misc->numberFormat_fun($makSub_total + $makVat); ?></span></td>
        </tr>
      </tbody>
    </table>
    <aside><br>
      <span style="margin-left:10px">Comments: <?php if ($C_comments) {
                                                  echo @$C_comments;
                                                } else {
                                                  echo 'Nil';
                                                } ?></span>
      <br><br>
      <?php echo $write_cancellation; ?>
      <div style="width:95%" align="center">
        <p style=" font-size:14px;" align="center">Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234.
          Company Registration Number 7760366 VAT Number 198427362
          Thank You For Business With Us<br /><br />

          Please pay your invoice within 21 days from the date of invoice.<span style="color:#F00"> Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998" </span>if no payment was made within reasonable time frame<br /><br />

          Language Services UK Limited
          Translation and Interpreting Service
          Suite 3 Davis House Lodge Causeway Trading Estate
          Lodge Causeway - Fishponds Bristol BS163JB


        </p>
      </div>
    </aside>
    </aside>
    <aside> </aside>
    </article>

  </div>
</body>

</html>