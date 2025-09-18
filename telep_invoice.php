<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'source/db.php';
include 'source/class.php';
$table = 'telephone';
$invoice_id = $_GET['invoice_id'];
// $allowed_type_idz = "76,88,120,179";
// //Check if user has current action allowed
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
  include "loadinvoicedbtelep.php";
  $pay_terms = "+".@$c_payment_terms." days";
  if (isset($_POST['submit'])) {
    if ($commit == 0 || @$invoic_date == '0000-00-00') {
      $acttObj->editFun($table, $invoice_id, 'commit', 1);
      $acttObj->editFun($table, $invoice_id, 'invoic_date', date("Y-m-d"));
      $acttObj->editFun($table, $invoice_id, 'dueDate', date("Y-m-d", strtotime($pay_terms)));
      $acttObj->insert("daily_logs", array("action_id" => 16, "user_id" => $_SESSION['cust_userId'], "details" => "TP Job ID: " . $invoice_id));
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
  <?php }

  if (isset($_POST['email'])) {
    $makCompanyEmail = $_POST['comp_email'];
    if ($commit == 0 || @$invoic_date == '0000-00-00') {
      $acttObj->editFun($table, $invoice_id, 'commit', 1);
      $acttObj->editFun($table, $invoice_id, 'invoic_date', date("Y-m-d"));
      $acttObj->editFun($table, $invoice_id, 'dueDate', date("Y-m-d", strtotime($pay_terms)));
      $acttObj->insert("daily_logs", array("action_id" => 16, "user_id" => $_SESSION['cust_userId'], "details" => "TP Job ID: " . $invoice_id));
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
      window.location.href = "./reports_lsuk/pdf/sendinvoicemail.php?loaddb=loadinvoicedbtelep.php&htm=invoicereporttelep.htm&invoice_id=<?php echo $invoice_id; ?>&company_email=<?php echo $makCompanyEmail; ?>&table=telephone";
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
          <td><?php echo @$invoiceNo . $append_invoiceNo; ?></td>
        </tr>
        <tr>
          <th class="bg-info"><span>DATE</span></th>
          <td><span class="date"><?php if (@$invoic_date == '0000-00-00') {
                                    $misc->dated(date("Y-m-d"));
                                  } else {
                                    echo $misc->dated(@$invoic_date);
                                  } ?></span></td>
        </tr>
        <tr>
          <th class="bg-info"><span>BOOKING REF/NAME </span></th>
          <td><span id="prefix"><?php echo @$nameRef; ?></span></td>
        </tr>
        <tr>
          <th class="bg-info">PURCHASE ORDER NO.</th>
          <td><?php echo @$porder; ?></td>
        </tr>
      </table>
    </div>
    <table class="table table-bordered">
      <thead class="bg-info">
        <tr>
          <th>ASSIGNMENT DATE AND TIME</th>
          <th>JOB</th>
          <th>JOB TYPE</th>
          <th>INVOICE DUE DATE</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a class="cut">-</a><span class="total"><?php echo $misc->dated($assignDate) . " " . $misc->timeFormat($assignTime); ?></span></td>
          <td>Interpreting</td>
          <td><?=$communication_type;?></td>
          <td><span class="total"><?php echo ($dueDate == '' || $dueDate == '0000-00-00') ? $misc->dated(date("Y-m-d", strtotime($pay_terms))) : $misc->dated($dueDate); ?></span></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered">
      <thead class="bg-info">
        <tr>
          <th>INTERPRETER LOCATION</th>
          <th>LINGUIST</th>
          <th>LANGUAGE</th>
          <th>CASE WORKER NAME</th>
          <th>FILE REFERENCE (CLIENT REFERENCE)</th>
          <th>BOOKING TYPE</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a class="cut">-</a><span class="total"><?php echo @$intrpCity;   ?></span></td>
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
          <th>Per Minute price</th>
          <th>Minutes</th>
          <th>Call Length Cost (£)</th>
          <th>Minimum Charge minutes / hours</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><a class="cut">-</a><span class="total"><?php echo $rateHour; ?></span></td>
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
      <thead class="bg-info">
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
              <?php echo $sub_total;
              // echo $sub_total=$bCredNoted?
              //   0:$misc->numberFormat_fun($makCalCharges + $C_otherCharges + ($makRateHour * $makHoursWord)+$C_admnchargs); 
              ?></span></td>
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
    <?php  
    //echo $write_cancellation; 
    ?><br>
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
    <aside> </aside>
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
  $acttObj->editFun('comp_credit', $credit_id, 'mode', 'telephone');
  $acttObj->editFun('comp_credit', $credit_id, 'debit', @$vat + @$total5 + @$C_otherexpns);
  $acttObj->editFun('comp_credit', $credit_id, 'debit_date', date("Y-m-d"));
}

if (isset($_POST['submit']) && $flag_inv == 1) {
  $credit_id = $acttObj->unique_data('comp_credit', 'id', 'invoiceNo', $invoiceNo);
  $acttObj->editFun('comp_credit', $credit_id, 'orgName', $abrv);
  $acttObj->editFun('comp_credit', $credit_id, 'invoiceNo', $invoiceNo);
  $acttObj->editFun('comp_credit', $credit_id, 'mode', 'telephone');
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
  $acttObj->editFun('bz_credit', $bz_credit_id, 'mode', 'telephone');
  $acttObj->editFun('bz_credit', $bz_credit_id, 'bz_debit', @$vat + @$total5 + @$C_otherexpns);
  $acttObj->editFun('bz_credit', $bz_credit_id, 'bz_debit_date', date("Y-m-d"));
}
//.......................................//\\//\\//\\..Business Credit Note.//\\//\\//\\.................................
?>