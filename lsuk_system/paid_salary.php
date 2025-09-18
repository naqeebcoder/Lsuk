<?php include'db.php'; include'class.php'; $table='interpreter';$slip_id= $_GET['submit']; $fdate= $_GET['fdate']; $tdate= $_GET['tdate'];?>
 <?php
$query1="SELECT * FROM interpreter_reg
where id=$slip_id";			
$result1 = mysqli_query($con,$query1);
$row1 = mysqli_fetch_array($result1);?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>LSUK-Pay Slip</title>
    <link rel="stylesheet" href="css/style.css" media="all" />
    <style>      @media print {
        * { -webkit-print-color-adjust: exact; }
        html { background: none; padding: 0; }
        body { box-shadow: none; margin: 0; }
        span:empty { display: none; }
        .add, .cut { display: none; }
      }

      @page { margin: 0; }</style>
      <?php  if(isset($_POST['submit'])){?><script>window.print()</script><style>.prnt{  display:none; }</style><?php } ?>
  </head>
  <body>
   <div><form action="" method="post"><input type="submit" class='prnt' name="submit" id="submit" value="Press to Print" style="background-color:#06F; color:#FFF; border:1px solid #09F" onclick="printpage();"/> | <input type="submit" class='prnt' name="undo" id="undo" value="Undo Remittance Advice" style="background-color:#F00; color:#FFF; border:1px solid #F00;"/></form></div>

   
    
    <main>
     <header class="clearfix">
      <div id="logo"><img src="img/logo.png" width="100" height="150"></div>
      <div id="company">
        <h2 class="name">Language Service UK Limited</h2>
        <div>45 Brockworth Crescent, Bristol, UK</div>
        <div>+44 07915177068</div>
        <div><a href="#">INFO@LSUK.ORG</a></div>
      </div>
      </div></header>
      <div id="details" class="clearfix">
        <div id="client">
          <div class="to">Salary To:</div>
          <h2 class="name"><?php echo $row1['name']; ?></h2>
          <div class="address"><?php echo $row1['address']; ?></div>
          <div class="email"><a href="#"><?php echo $row1['email']; ?></a></div>
        </div>
        <div id="invoice">
          <!--<h1>Slip #: ABC-1234-BCD</h1>-->
          <div class="date">Salary Date: <?php echo $tdate; ?></div>
          <div class="date"></div>
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
            <td class="no"></td>
            <td class="desc"><h3>Interpreter Services</h3></td>
             <td class="total"></td>
          </tr>
        <?php $i=1;$amount1=0;$amount2=0;$amount3=0;
$query="SELECT * FROM interpreter 
where intrpName=$slip_id and intrp_salary_comit = 1 and
 assignDate  
BETWEEN('$fdate')AND('$tdate') order by assignDate";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){if(isset($_POST['submit'])){$acttObj->editFun('interpreter',$row['id'],'intrp_salary_comit',0);}?>
          <tr>
            <td class="no"><?php echo $i++; ?></td>
            <td class="desc"><h3><?php echo $row['assignDate']; ?></h3>
              <?php echo $row['orgName']; ?></td>
            <td class="total"><?php echo round($row['total_charges_interp']); $amount1=round($row['total_charges_interp']) + $amount1;?></td>
          </tr>
           <?php  if(isset($_POST['undo'])){ $acttObj->editFun('interpreter',$row['id'],'intrp_salary_comit',0);}?> 
          <?php } ?>
          
                   <tr>
            <td class="no"></td>
            <td class="desc"><h3>Telephone Interpreter Services</h3></td>
             <td class="total"></td>
          </tr>
        <?php $i=1;
$query="SELECT * FROM telephone where intrpName=$slip_id and intrp_salary_comit = 1 and
 assignDate  
BETWEEN('$fdate')AND('$tdate') order by assignDate";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){if(isset($_POST['submit'])){$acttObj->editFun('telephone',$row['id'],'intrp_salary_comit',0);}?>
          <tr>
            <td class="no"><?php echo $i++; ?></td>
            <td class="desc"><h3><?php echo $row['assignDate']; ?></h3>
              <?php echo $row['orgName']; ?></td>
            <td class="total"><?php echo round($row['total_charges_interp']); $amount2=round($row['total_charges_interp']) + $amount2;  ?></td>
          </tr>
           <?php  if(isset($_POST['undo'])){ $acttObj->editFun('telephone',$row['id'],'intrp_salary_comit',0);}?> 
          <?php } ?>
          
                   <tr>
            <td class="no"></td>
            <td class="desc"><h3>Translation Services</h3></td>
             <td class="total"></td>
          </tr>
        <?php $i=1;
$query="SELECT * FROM translation where intrpName=$slip_id  and intrp_salary_comit = 1 and
asignDate  
BETWEEN('$fdate')AND('$tdate') order by asignDate";					
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){if(isset($_POST['submit'])){$acttObj->editFun('translation',$row['id'],'intrp_salary_comit',0);}?>
          <tr>
            <td class="no"><?php echo $i++; ?></td>
            <td class="desc"><h3><?php echo $row['dated']; ?></h3>
              <?php echo $row['orgName']; ?></td>
            <td class="total"><?php echo round($row['total_charges_interp']);$amount3=round($row['total_charges_interp']) + $amount3; ?></td>
          </tr>
          <?php  if(isset($_POST['undo'])){ $acttObj->editFun('translation',$row['id'],'intrp_salary_comit',0);}?> 
          <?php } ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"> <strong>Total</strong></td>
            <td><span style="color:#000"><?php echo $amount1 + $amount2 + $amount3; ?></span></td>
          </tr>
        </tfoot>
      </table>
      <div id="thanks">Thank you!</div>
      <div id="notices">
        <!--<div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>-->
      </div>
    </main>
   
  </body>
</html>