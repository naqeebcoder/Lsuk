<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'source/db.php';
include 'source/class.php';
$table = 'translation';
$invoice_id = $_GET['invoice_id'];
$allowed_type_idz = "76,88,120,179";
//Check if user has current action allowed
// if ($_SESSION['is_root'] == 0) {
//     $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
//     if (empty($get_page_access)) {
//         die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Invoice</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
//     }
// }
if (!isset($_SESSION['cust_userId'])) {
  die("<center><h2 class='text-danger'>Access Denied!</h2></center>");
}

// 2. Validate invoice_id (sanitize as integer)
$invoice_id = filter_input(INPUT_GET, 'invoice_id', FILTER_SANITIZE_NUMBER_INT);
if (!$invoice_id || $invoice_id < 1) {
  die("Invalid invoice ID!");
}

// 3. Get token from URL (no sanitization needed - validate instead)
$token = $_GET['token'] ?? '';

// 4. Validate token format (64-character hex for SHA256)
if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
  die("Invalid token format!");
}

// 5. Compare with session token
if (
  !isset($_SESSION['invoice_token'][$invoice_id]) ||
  !hash_equals($_SESSION['invoice_token'][$invoice_id], $token)
) {
  die("<center><h2 class='text-danger'>Access Denied!</h2></center>");
}
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>LSUK-Invoice</title>
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
  <?php
  include "loadinvoicedbtrans.php";
  $pay_terms = "+".@$c_payment_terms." days";
  if (isset($_POST['submit'])) {
    if ($commit == 0 || @$invoic_date == '0000-00-00') {
      $acttObj->editFun($table, $invoice_id, 'commit', 1);
      $acttObj->editFun($table, $invoice_id, 'invoic_date', date("Y-m-d"));
      $acttObj->editFun($table, $invoice_id, 'dueDate', date("Y-m-d", strtotime($pay_terms)));
      $acttObj->insert("daily_logs", array("action_id" => 16, "user_id" => $_SESSION['cust_userId'], "details" => "TR Job ID: " . $invoice_id));
    }
    $acttObj->editFun($table, $invoice_id, 'printed', 1);
    $acttObj->editFun($table, $invoice_id, 'printedby', $_SESSION['cust_UserName']);
  ?>
    <script>
      window.print()
    </script>
    <style>
      .prnt {
        display: none;
      }
    </style>
  <?php
  }
  if (isset($_POST['email'])) {
    $makCompanyEmail = $_POST['comp_email'];
    if ($commit == 0 || @$invoic_date == '0000-00-00') {
      $acttObj->editFun($table, $invoice_id, 'commit', 1);
      $acttObj->editFun($table, $invoice_id, 'invoic_date', date("Y-m-d"));
      $acttObj->editFun($table, $invoice_id, 'dueDate', date("Y-m-d", strtotime($pay_terms)));
      $acttObj->insert("daily_logs", array("action_id" => 16, "user_id" => $_SESSION['cust_userId'], "details" => "TR Job ID: " . $invoice_id));
    }
    $acttObj->editFun($table, $invoice_id, 'sentemail', 1);
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
      window.location.href = "./reports_lsuk/pdf/sendinvoicemail.php?loaddb=loadinvoicedbtrans.php&htm=invoicereporttrans.htm&invoice_id=<?php echo $invoice_id; ?>&company_email=<?php echo $makCompanyEmail; ?>&table=translation";
    </script>
  <?php } ?>
  <br>
  <form action="" method="post" class="form-inline">
    <div class="form-group"><input type="submit" class='prnt btn btn-primary' name="submit" value="Press to Print" style="margin-left: 20px;"/>
      <!-- <input placeholder="Kindly enter invoice email" type="text" class='form-control hd prnt' name="comp_email" value="<?php echo $makCoEmail; ?>" />
      <input type="submit" class='prnt btn btn-info' name="email" value="Confirm Email ID & Send Email" /> -->
    </div>
  </form>

  <div class="container-fluid">
    <header>
      <div id="block_container">

        <div id="bloc1">
          <h3 style="background-color:#FFF; color:#000; margin-left:165px;">Language Services UK Limited</h1>
        </div>
        <div id="bloc2"><img alt="" src="lsuk_system/img/logo.png" height="60" width="120"></div>
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
          <th class="bg-info">INVOICE #</th>
          <td><?php echo $invoiceNo . $append_invoiceNo; ?></td>
        </tr>
        <tr>
          <th class="bg-info"><span>DATE</span></th>
          <td><span class="date"><?php if ($invoic_date == '0000-00-00') {
                                    $misc->dated(date("Y-m-d"));
                                  } else {
                                    echo $misc->dated($invoic_date);
                                  } ?></span></td>
        </tr>
        <tr>
          <th class="bg-info"><span>BOOKING REF/NAME </span></th>
          <td><span id="prefix"><?php echo $nameRef; ?></span></td>
        </tr>
        <tr>
          <th class="bg-info">PURCHASE ORDER NO.</th>
          <td><?php echo $porder; ?></td>
        </tr>
      </table>
    </div>
    <table class="table table-bordered">
      <thead class="bg-info">
        <tr>
          <th>ASSIGNMENT DATE</th>
          <th>JOB</th>
          <th>JOB TYPE</th>
          <th>INVOICE DUE DATE</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a class="cut">-</a><span class="total"><?php echo $misc->dated($asignDate); ?></span></td>
          <td>Translation</td>
          <td><?php echo $acttObj->read_specific("GROUP_CONCAT(CONCAT(td_title)  SEPARATOR '<br>') as td_title", "trans_dropdown", "td_id IN (" . $transType . ")")['td_title']; ?></td>
          <td><span class="total"><?php echo ($dueDate == '' || $dueDate == '0000-00-00') ? $misc->dated(date("Y-m-d", strtotime($pay_terms))) : $misc->dated($dueDate); ?></span></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered">
      <thead class="bg-info">
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
          <td><span class="total"><?php echo @$source . " to " . @$target; ?></span></td>
          <td><span class="total"><?php echo @$orgContact; ?></span></td>
          <td><span class="total"><?php echo @$orgRef; ?></span></td>
          <td><?php echo @$bookinType; ?></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered" style="text-transform: uppercase;">
      <thead class="bg-info">
        <tr>
          <th>Rate per <?php echo $trans_single_label; ?></th>
          <th><?php echo $trans_multi_label; ?></th>
          <th>minimum Translation Cost (units)(£) </th>
          <!--<th>Cost Per Word</th>
              <th>Word Count</th>
              <th>Translation Cost (words)(£)</th>-->
          <th>Certification Cost(£)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a class="cut">-</a><span class="total"><?php echo $misc->numberFormat_fun($C_rpU); ?></span></td>
          <td><span class="total"><?php echo $misc->numberFormat_fun($C_numberUnit); ?></span></td>
          <td><?php echo $unitCost = $misc->numberFormat_fun($C_numberUnit * $C_rpU); ?></td>
          <!--<td><span class="total"><?php echo $misc->numberFormat_fun($C_rpW); ?></span></td>
              <td><span class="total"><?php echo $misc->numberFormat_fun($C_numberWord); ?></span></td>
              <td><?php echo $wordCost = $misc->numberFormat_fun($C_numberWord * $C_rpW); ?></td>-->
          <td><span class="total"><?php echo $misc->numberFormat_fun($certificationCost); ?></span></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered" style="text-transform: uppercase;">
      <thead class="bg-info">
        <tr>
          <th>Proof reading Cost(£)</th>
          <th>Postage Cost(£)</th>
          <th>Other Expenses(£)</th>
          <th>ADMIN CHARGES</th>
          <th>job total(£)</th>
          <th>vat @ 20%(£)</th>
          <th>invoIce total(£)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a class="cut">-</a><span class="total"><?php echo $misc->numberFormat_fun($proofCost); ?></span></td>
          <td><span class="total"><?php echo $misc->numberFormat_fun($postageCost); ?></span></td>
          <td><span class="total"><?php echo $misc->numberFormat_fun($C_otherCharg); ?></span></td>
          <td><span class="total"><?php echo $misc->numberFormat_fun($C_admnchargs); ?></span></td>
          <?php //Code Added by Solworx to reverse the number format function 
          $makWordCost = floatval(preg_replace('/[^\d.]/', '', $wordCost));
          $makunitCost = floatval(preg_replace('/[^\d.]/', '', $unitCost));
          ?>
          <td><?php echo $total = $misc->numberFormat_fun($makunitCost + $makWordCost + $certificationCost + $postageCost + $proofCost + $C_otherCharg + $C_admnchargs); ?></td>
          <?php //Code Added by Solworx to reverse the number format function;
          $makTotal = floatval(preg_replace('/[^\d.]/', '', $total));
          ?>
          <td><span class="total"><?php echo $vat = $misc->numberFormat_fun($makTotal * $cur_vat);
                                  //Code Added by Solworx to reverse the number format function;
                                  $makVat = floatval(preg_replace('/[^\d.]/', '', $vat));

                                  ?></span></td>
          <td><span class="total"><?php echo $misc->numberFormat_fun($makVat + $makTotal); ?></span></td>
        </tr>
      </tbody>
    </table>
    <aside><br>
    <?php  echo $write_cancellation; ?>
      <span style="margin-left:10px">Comments: 
      <?php 
      $solc=array(5,30,438,751);
      if ($C_comments) {
        echo @$C_comments;
      } else {
        if(in_array($comp_id,$solc)){
          echo "The interpreter holds basic interpreting qualification which meets the minimum interpreting qualification criteria as set by LAA";
        }else{
          echo 'Nil';
        }
      } ?></span>
      <br><br>
      <div style="width:95%" align="center">
        <p style=" font-size:14px;" align="center">Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234.
          Company Registration Number 7760366 VAT Number 198427362
          Thank You For Business With Us<br /><br />

          Please pay your invoice within <?php echo @$c_payment_terms; ?> days from the date of invoice.<span style="color:#F00"> Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998" </span>if no payment was made within reasonable time frame<br /><br />

          Language Services UK Limited
          Translation and Interpreting Service
          Suite 3 Davis House Lodge Causeway Trading Estate
          Lodge Causeway - Fishponds Bristol BS163JB
        </p>
      </div>
    </aside>
    </aside>
    </article>
  </div>
</body>

</html>
<?php
//....................................Credit Note.........................................
$flag_inv = $acttObj->uniqueFun('comp_credit', 'invoiceNo', $invoiceNo);
if (isset($_POST['submit']) && $flag_inv == 0) {
  $credit_id = $acttObj->get_id('comp_credit');
  $acttObj->editFun('comp_credit', $credit_id, 'orgName', $abrv);
  $acttObj->editFun('comp_credit', $credit_id, 'invoiceNo', $invoiceNo);
  $acttObj->editFun('comp_credit', $credit_id, 'mode', 'translation');
  $acttObj->editFun('comp_credit', $credit_id, 'debit', @$vat + @$total5 + @$C_otherexpns);
  $acttObj->editFun('comp_credit', $credit_id, 'debit_date', date("Y-m-d"));
}

if (isset($_POST['submit']) && $flag_inv == 1) {
  $credit_id = $acttObj->unique_data('comp_credit', 'id', 'invoiceNo', $invoiceNo);
  $acttObj->editFun('comp_credit', $credit_id, 'orgName', $abrv);
  $acttObj->editFun('comp_credit', $credit_id, 'invoiceNo', $invoiceNo);
  $acttObj->editFun('comp_credit', $credit_id, 'mode', 'translation');
  $acttObj->editFun('comp_credit', $credit_id, 'debit', @$vat + @$total5 + @$C_otherexpns);
  $acttObj->editFun('comp_credit', $credit_id, 'debit_date', date("Y-m-d"));
}
//.......................................//\\//\\//\\..Credit Note.//\\//\\//\\.................................
//....................................Business Credit Note.........................................
$flag_inv = $acttObj->uniqueFun('bz_credit', 'invoiceNo', $invoiceNo);
if (isset($_POST['submit']) && $flag_inv == 0) {
  $bz_credit_id = $acttObj->get_id('bz_credit');
  $acttObj->editFun('bz_credit', $bz_credit_id, 'orgName', $abrv);
  $acttObj->editFun('bz_credit', $bz_credit_id, 'invoiceNo', $invoiceNo);
  $acttObj->editFun('bz_credit', $bz_credit_id, 'mode', 'interpreter');
  $acttObj->editFun('bz_credit', $bz_credit_id, 'bz_debit', @$vat + @$total5 + @$C_otherexpns);
  $acttObj->editFun('bz_credit', $bz_credit_id, 'bz_debit_date', date("Y-m-d"));
}

if (isset($_POST['submit']) && $flag_inv == 1) {
  $bz_credit_id = $acttObj->unique_data('bz_credit', 'id', 'invoiceNo', $invoiceNo);
  $acttObj->editFun('bz_credit', $bz_credit_id, 'orgName', $abrv);
  $acttObj->editFun('bz_credit', $bz_credit_id, 'invoiceNo', $invoiceNo);
  $acttObj->editFun('bz_credit', $bz_credit_id, 'mode', 'translation');
  $acttObj->editFun('bz_credit', $bz_credit_id, 'bz_debit', @$vat + @$total5 + @$C_otherexpns);
  $acttObj->editFun('bz_credit', $bz_credit_id, 'bz_debit_date', date("Y-m-d"));
}
//.......................................//\\//\\//\\..Business Credit Note.//\\//\\//\\.................................
?>