<?php include'db.php'; include'class.php'; $table='interpreter';$slip_id= @$_GET['slip_id'];
$query="SELECT * FROM $table where invoiceNo=$slip_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$hoursWorkd=$row['hoursWorkd'];$chargInterp=$row['chargInterp'];$rateHour=$row['rateHour'];$travelMile=$row['travelMile'];$rateMile=$row['rateMile'];$chargeTravel=$row['chargeTravel'];$travelCost=$row['travelCost'];$otherCost=$row['otherCost'];$travelTimeHour=$row['travelTimeHour'];$travelTimeRate=$row['travelTimeRate'];$chargeTravelTime=$row['chargeTravelTime'];$dueDate=$row['dueDate'];}?>


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
          <h2 class="name">Bilal Kaka Khail</h2>
          <div class="address">Khayaban Sir Syed, Rawalpindi</div>
          <div class="email"><a href="#">Bilal@gmail.com</a></div>
        </div>
        <div id="invoice">
          <h1>INVOICE #: ABC-1234-BCD</h1>
          <div class="date">Date of Invoice: 01/06/2014</div>
          <div class="date">Payment Due Date: <?php echo $dueDate; ?></div>
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
            <td class="desc">Hours Worked </td>
            <td class="total"><?php echo $hoursWorkd; ?></td>
          </tr>
          <tr>
            <td class="no">03</td>
            <td class="desc">Rate Per Hour</td>
            <td class="total"><?php echo $rateHour; ?></td>
          </tr>
          <tr>
            <td class="no">04</td>
            <td class="desc">Charge for Interpreting Time </td>
            <td class="total"><?php echo $chargInterp; ?></td>
          </tr>
          <tr>
            <td class="no">05</td>
            <td class="desc">Travel Time Hours</td>
            <td class="total"><?php echo $travelTimeHour; ?></td>
          </tr>
          <tr>
            <td class="no">06</td>
            <td class="desc">Travel Time Rate Per Hour</td>
            <td class="total"><?php echo $travelTimeRate; ?></td>
          </tr>
          <tr>
            <td class="no">07</td>
            <td class="desc">Charge for Travel Time</td>
            <td class="total"><?php echo $chargeTravelTime; ?></td>
          </tr>
          <tr>
            <td class="no">08</td>
            <td class="desc">Travel Mileage </td>
            <td class="total"><?php echo $travelMile; ?></td>
          </tr>
          <tr>
            <td class="no">09</td>
            <td class="desc">Rate Per Mileage </td>
            <td class="total"><?php echo $rateMile; ?></td>
          </tr>
          <tr>
            <td class="no">10</td>
            <td class="desc">Charge for Travel Cost </td>
            <td class="total"><?php echo $chargeTravel; ?></td>
          </tr>
          <tr>
            <td class="no">11</td>
            <td class="desc">Travel Cost </td>
            <td class="total"><?php echo $travelCost; ?></td>
          </tr>
          <tr>
            <td class="no">12</td>
            <td class="desc">Other Costs (Parking , Bridge Toll) </td>
            <td class="total"><?php echo $otherCost; ?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2">Job Totals</td>
            <td>$5,200.00</td>
          </tr>
          <tr>
            <td colspan="2">VAT @ 20% </td>
            <td>$1,300.00</td>
          </tr>
           
          <tr>
            <td colspan="2">Invoice Total</td>
            <td><span style="color:#000">$6,500.00</span></td>
          </tr>
        </tfoot>
      </table>
      <div id="thanks">Thank you!</div>
      <div id="notices">
        <div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
      </div>
    </main>
   
  </body>
</html>