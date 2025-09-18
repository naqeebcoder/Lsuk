<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>LSUK-Invoice</title>
    <link rel="license" href="http://www.opensource.org/licenses/mit-license/">
       <style type="text/css">
      /* reset */

      *{
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

      /* content editable */

      *[contenteditable] { min-width: 1em; outline: 0; }

      *[contenteditable] { cursor: pointer; }

      *[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 1em 0.5em #DEF; }

      /*span[contenteditable] { display: inline-block; }*/

      /* heading */

      h1 { font: bold 100% Ubuntu, Arial, sans-serif; text-align: center; text-transform: uppercase; }

      /* table */

      table { font-size: 75%; table-layout: fixed; width: 100%; }
      table { border-collapse: separate; border-spacing: 2px; }
      th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left; }
      th, td {  border-style: solid; }
      th { background: #EEE; border-color: #BBB; }
      td { border-color: #DDD; }

      /* page */

      html { font: 16px/1 'Open Sans', sans-serif; overflow: auto; }
      html { background: #fff; cursor: default; }

      body { box-sizing: border-box; margin:0;}
      #wrapper{margin: 0 auto; margin-left:20px; width: 18.5cm;}
      body { background: #FFF;}

      /* header */

      header { margin: 0 0 3em; }
      header:after { clear: both; content: ""; display: table; }

      header h1 { background: #000; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
      header address { float: left; font-size: 75%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; }
      header address p { margin: 0 0 0.25em; }
      header span, header img { display: block; float: right; }
      header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
      header img { max-height: 100%; max-width: 50%; }
      header input { cursor: pointer; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; }

      /* article */

      article, article address, table.meta, table.inventory { margin: 0 0 3em; }
      article:after { clear: both; content: ""; display: table; }
      article h1 { clip: rect(0 0 0 0); position: absolute; }

      article address { float: left; font-size: 125%; font-weight: bold; }

      /* table meta & balance */

      table.meta, table.balance { float: right; width: 36%; margin-top:-90px; }
      table.meta:after, table.balance:after { clear: both; content: ""; display: table; }

      /* table meta */

      table.meta th { width: 40%; }
      table.meta td { width: 60%; }

      /* table items */

      table.inventory { clear: both; width: 100%; }
      table.inventory th { font-weight: bold; text-align: left; }

      table.inventory td:nth-child(1) { width: 26%; }
      table.inventory td:nth-child(2) { width: 38%; }
      table.inventory td:nth-child(3) { text-align: left; width: 12%; }
      table.inventory td:nth-child(4) { text-align: left; width: 12%; }
      table.inventory td:nth-child(5) { text-align: left; width: 12%; }

      /* table balance */

      table.balance th, table.balance td { width: 50%; }
      table.balance td { text-align: left; }

      /* aside */

      aside h1 { border: none; border-width: 0 0 1px; margin: 0 0 1em; }
      aside h1 { border-color: #999; border-bottom-style: solid; }

      .cutw{position:relative;}
      /* javascript */

      .add, .cut
      {
        border-width: 1px;
        display: block;
        font-size: .8em;
        padding: 0.25em;
        float: left;
        text-align: center;
        width:0.8em;
      }
      .cut{font-size:1em;}

      .add, .cut
      {
        background: #9AF;
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
        background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
        border-color: #0076A3;
        color: #FFF;
        cursor: pointer;
        font-weight: bold;
        text-shadow: 0 -1px 2px rgba(0,0,0,0.333);
      }

      .add { margin: -2.5em 0 0; }

      .add:hover { background: #00ADEE; }

      .cut { opacity: 0; position: absolute; top: 0; left: -1.5em; }

      tr:hover .cut { opacity: 1; }

      @media print {
        * { -webkit-print-color-adjust: exact; }
        html { background: none; padding: 0; }
        body { box-shadow: none; margin: 0; }
        span:empty { display: none; }
        .add, .cut { display: none; }
      }

      @page { margin: 0; }
	  .total{word-wrap:break-word;}
	   #block_container {
    text-align: center;
}
#block_container > div {
    display: inline-block;
    vertical-align: middle;
}
    </style>

  </head>
  <body>
<?php 
include'db.php'; 
include'class.php'; 

$table='telephone';
$invoice_id= $_GET['invoice_id'];

include "loadinvoicedbtelep.php";

if(isset($_POST['submit']))
{ 
  if($commit==0 || @$invoic_date=='0000-00-00')
  {
    $acttObj->editFun($table,$invoice_id,'commit',1);
    $acttObj->editFun($table,$invoice_id,'invoic_date',date("Y-m-d"));
    $acttObj->editFun($table,$invoice_id,'dueDate',date("Y-m-d", strtotime("+15 days")));
  }
  ?>
  <script>window.print()</script><style>.prnt{  display:none; }
  </style>
  <?php 
} 
?>

<?php
if(isset($_POST['email']))
{ 
  //email
  if($commit==0 || @$invoic_date=='0000-00-00')
  {
    $acttObj->editFun($table,$invoice_id,'commit',1);

    $acttObj->editFun($table,$invoice_id,'invoic_date',date("Y-m-d"));
    $acttObj->editFun($table,$invoice_id,'dueDate',date("Y-m-d", strtotime("+15 days")));
  }

  //include "reports_lsuk/pdf/sendinvoicemail.php";
  ?>
  <script>
  window.location.href="./reports_lsuk/pdf/sendinvoicemail.php?loaddb=loadinvoicedbtelep.php&htm=invoicereporttelep.htm&invoice_id=<?php echo $invoice_id; ?>&table=telephone";
  </script>

  <?php


} 
?>

<div>
   <div>
     <form action="" method="post">
       <input type="submit" class='prnt' name="submit" value="Press to Print" 
        style="cursor:pointer;background-color:#06F; color:#FFF; border:1px solid #09F"/>
       |
       <input type="submit" class='prnt' name="email" value="Press to Email" 
        style="cursor:pointer;background-color:#06F; color:#FFF; border:1px solid #09F"/>
     </form>
   </div>
</div>
   
      <div id="wrapper" style="width:100%;">
      <header>
<div id="block_container">

    <div id="bloc1"><h1 style="background-color:#FFF; color:#000; margin-left:10px;">Language Services UK Limited</h1></div>  
    <div id="bloc2"><img alt="" src="img/logo.png" height="100" width="210"></div>
    <h3 align="center">Translation | Interpreting | Transcription | Cross-Cultural Training & Development</h3>
<hr style="border-top: 1px solid #8c8b8b; width:100%">
</div>
        <address style="margin-left:10px; font-weight:bold;">
        
           <p><?php echo @$orgzName ; ?></p>
         <p><?php echo @$inchNo; ?></p><p><?php echo @$line1; ?></p><p><?php echo @$line2; ?></p><p><?php echo @$inchRoad; ?></p><p><?php echo @$inchCity; ?></p>
          <p><?php echo @$inchPcode; ?></p>
        </address>
        
      </header>
      
      <article>

        <table class="meta">
          <tr>
            <th>INVOICE #</th>
            <td><?php echo $invoiceNo; ?></td>
          </tr>
          <tr>
            <th><span>DATE</span></th>
            <td><span class="date"><?php if(@$invoic_date=='0000-00-00'){ $misc->dated(date("Y-m-d"));}else{ echo $misc->dated(@$invoic_date);} ?></span></td>
          </tr>
          <tr>
            <th><span>BOOKING REF/NAME</span></th>
            <td><span id="prefix"><?php echo $nameRef; ?></span><span></span></td>
          </tr>
          <tr>
            <th>PURCHASE ORDER NO.</th>
            <td><?php echo $porder; ?></td>
          </tr>
        </table>
        <table class="inventory">
          <thead>
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
              <td><span class="total"><?php echo $misc->dated($dueDate); ?></span></td>
            </tr>
          </tbody>
        </table>
        <table class="inventory">
          <thead>
            <tr>
              <th>JOB ADDRESS abc<?php echo $dueDate; ?></th>
              <th>LINGUIST</th>
              <th>LANGUAGE</th>
              <th>CASE WORKER NAME</th>
              <th>FILE REFERENCE (CLIENT REFERENCE)</th>
              <th>BOOKING TYPE</th>
            </tr>
            <tr>
              <td><a class="cut">-</a><span class="total">N/A</span></td>
              <td><span class="total"><?php echo $intrpName.":".$inchEmail; ?></span></td>
              <td><span class="total"><?php echo $source."UH". $g_row['source']; ?></span></td>
              <td><span class="total"><?php echo $orgContact; ?></span></td>
              <td><span class="total"><?php echo $orgRef; ?></span></td>
              <td><?php echo @$bookinType; ?></td>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <table class="inventory" style="text-transform: uppercase;">
          <thead>
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
              <td><span class="total"><?php echo $misc->numberFormat_fun($rateHour * $hoursWorkd); ?></span></td>
            </tr>
          </tbody>
        </table>
        <table class="inventory" style="text-transform: uppercase;">
          <thead>
            <tr>
              <th>Other Expenses(£)</th>
              <th>ADMIN CHARGES</th>
              <th>job total (£)</th>
              <th>vat @ 20% (£)</th>
              <th>inVOICe total (£)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><a class="cut">-</a><span class="total"><?php echo $misc->numberFormat_fun($C_otherCharges); ?></span></td>
              <td><span class="total"><?php echo $misc->numberFormat_fun($C_admnchargs); ?></span></td>

              <?php $crednoted=$g_row["credit_note"];$bCredNoted=false;
                if (isset($crednoted) && $crednoted!="") $bCredNoted=true;?>

              <td><span class="total">
                <?php echo $sub_total=$bCredNoted?
                  0:$misc->numberFormat_fun($calCharges + $C_otherCharges + ($rateHour * $hoursWorkd)+$C_admnchargs); ?></span></td>

              <td><span class="total">
                <?php echo $vat=$misc->numberFormat_fun($sub_total * $cur_vat); ?></span></td>
              <td><span class="total">
                <?php echo $misc->numberFormat_fun( $sub_total + $vat); ?></span></td>
            </tr>
          </tbody>
        </table>
<aside>
<span style="margin-left:10px">Comments: <?php if($C_comments){echo @$C_comments;}else{echo 'Nil';} ?></span>
<br/><br/>
<div style="width:95%" align="center">
<p style=" font-size:14px;" align="center">Please make all cheques payable to Language Services UK Limited for BACS payment Sort Code 20-13-34 Account Number 33161234.
Company Registration Number 7760366 VAT Number 198427362
Thank You For Business With Us<br/><br/>

Please pay your invoice within 21 days from the date of invoice.<span style="color:#F00"> Compensation fee and interest charges at 1.5% per day will be added to invoice total in accordance with the "Late Payment of Commercial Debts Interests Act 1998" </span>if no payment was made within reasonable time frame<br/><br/>

Language Services UK Limited
 Translation and Interpreting Service
Suite 3 Davis House Lodge Causeway Trading Estate
 Lodge Causeway - Fishponds Bristol BS163JB


</p></div>
    </aside>
</aside>
<aside> </aside>
      </article>
     
    </div>
  </body>
</html>
<?php 
//....................................Credit Note.........................................
 $flag_inv=$acttObj->uniqueFun('comp_credit','invoiceNo',$invoiceNo);
if(isset($_POST['submit']) && $flag_inv==0){$credit_id= $acttObj->get_id('comp_credit');$acttObj->editFun('comp_credit',$credit_id,'orgName',$abrv);$acttObj->editFun('comp_credit',$credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('comp_credit',$credit_id,'mode','telephone');$acttObj->editFun('comp_credit',$credit_id,'debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('comp_credit',$credit_id,'debit_date',date("Y-m-d"));}

if(isset($_POST['submit']) && $flag_inv==1){$credit_id=$acttObj->unique_data('comp_credit','id','invoiceNo',$invoiceNo);$acttObj->editFun('comp_credit',$credit_id,'orgName',$abrv);$acttObj->editFun('comp_credit',$credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('comp_credit',$credit_id,'mode','telephone');$acttObj->editFun('comp_credit',$credit_id,'debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('comp_credit',$credit_id,'debit_date',date("Y-m-d"));}
//.......................................//\\//\\//\\..Credit Note.//\\//\\//\\.................................
//....................................Business Credit Note.........................................
 $flag_inv=$acttObj->uniqueFun('bz_credit','invoiceNo',$invoiceNo);
if(isset($_POST['submit']) && $flag_inv==0){$bz_credit_id= $acttObj->get_id('bz_credit');$acttObj->editFun('bz_credit',$bz_credit_id,'orgName',$abrv);$acttObj->editFun('bz_credit',$bz_credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('bz_credit',$bz_credit_id,'mode','interpreter');$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit_date',date("Y-m-d"));}

if(isset($_POST['submit']) && $flag_inv==1){$bz_credit_id=$acttObj->unique_data('bz_credit','id','invoiceNo',$invoiceNo);$acttObj->editFun('bz_credit',$bz_credit_id,'orgName',$abrv);$acttObj->editFun('bz_credit',$bz_credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('bz_credit',$bz_credit_id,'mode','telephone');$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit_date',date("Y-m-d"));}
//.......................................//\\//\\//\\..Business Credit Note.//\\//\\//\\.................................
?>