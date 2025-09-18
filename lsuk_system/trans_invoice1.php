<?php include'db.php'; include'class.php'; $table='translation';$invoice_id= @$_GET['invoice_id'];
$query="SELECT $table.*,invoice.dated FROM $table
INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo

 where $table.id=$invoice_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$orgName=$row['orgName'];$orgContact=$row['orgContact'];$invoiceNo=$row['invoiceNo'];$inchEmail=$row['inchEmail'];$numberUnit=$row['numberUnit'];$C_rpU=$row['C_rpU'];$C_otherCharg=$row['C_otherCharg'];$total_charges_comp=$row['total_charges_comp'];$dueDate=$row['dueDate'];$dated=date_format(date_create($row['dated']), 'd-m-Y');}?>

<?php  if(isset($_POST['submit'])){ $acttObj->editFun($table,$invoice_id,'commit',1);?><script>window.print()</script><style>.prnt{  display:none; }</style><?php } ?>
 <div><form action="" method="post"><input type="submit" class='prnt' name="submit" value="Press to Print" style="background-color:#06F; color:#FFF; border:1px solid #09F"onclick="printpage()"/></form></div>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>LSUK-Invoice</title>
    <link rel="stylesheet" href="css/style.css" media="all" />
  </head>
  <body>
    <header class="clearfix">
      <div id="logo"><img src="img/logo.png" width="100" height="150"></div>
      <div id="company">
        <h2 class="name">Language Service UK Limited</h2>
        <div>45 Brockworth Crescent, Bristol, UK</div>
        <div>+44 07915177068</div>
        <div><a href="#">INFO@LSUK.ORG</a></div>
      </div>
      </div>
    </header>
    <main>
      <div id="details" class="clearfix">
        <div id="client">
          <div class="to">INVOICE TO:</div>
          <h2 class="name"><?php echo $orgName; ?></h2>
          <div class="address"><?php echo $orgContact; ?></div>
          <div class="email"><a href="#"><?php echo $inchEmail; ?></a></div>
        </div>
        <div id="invoice">
          <h1>Invoice#:<?php echo $invoiceNo; ?></h1>
          <div class="date"><?php echo $inchEmail; ?></div>
          <div class="date">Invoice Date: <?php echo $dated; ?></div>
          <!--<div class="date">Payment Due Date: <?php //echo $dueDate; ?></div>-->
        </div>
      </div>
      <table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="no">#</th>
            <th class="desc">DESCRIPTION</th>
            <th class="total">TOTAL &pound;</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="no">01</td>
            <td class="desc"><h3>Interpreting Service</h3>              
              Professional interpreting service provider, offers numerous modes of interpretation in and around Bristol</td>
            <td class="total">-----</td>
          </tr>
          <tr>
            <td class="no">02</td>
            <td class="desc">Number of Unit </td>
            <td class="total"><?php echo $numberUnit; ?></td>
          </tr>
          <tr>
            <td class="no">03</td>
            <td class="desc">Rate Per Unit</td>
            <td class="total"><?php echo $C_rpU; ?></td>
          </tr>
          <tr>
            <td class="no">04</td>
            <td class="desc">Other Charges</td>
            <td class="total"><?php echo $C_otherCharg; ?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2">Job Totals</td>
            <td><?php echo $subtotal=$total_charges_comp; ?></td>
          </tr>
          <tr>
            <td colspan="2">VAT @ 20% </td>
             <td><?php echo $vat=$subtotal * 0.2; ?></td>
          </tr>
          <tr>
            <td colspan="2">Invoice Total</td>
            <td><span style="color:#000"><?php echo $subtotal+$vat; ?></span></td>
          </tr>
        </tfoot>
      </table>
      <div id="thanks">Thank you!</div>
      <!--<div id="notices">
        <div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
      </div>-->
    </main>
   
  </body>
</html>