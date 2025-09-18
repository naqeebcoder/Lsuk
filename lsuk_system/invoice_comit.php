<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>LSUK-Invoice</title>
    <link rel="license" href="http://www.scholarshipgalaxy.com">
      </head>
  <body>
  <?php include'db.php'; include'class.php'; $table='interpreter';$invoice_id= $_GET['invoice_id'];
$query="SELECT interpreter.*,invoice.dated, interpreter_reg.name,comp_reg.name as orgzName,comp_reg.abrv FROM interpreter
INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
INNER JOIN interpreter_reg ON interpreter.intrpName=interpreter_reg.id
INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv

where multInv_flag=0 and interpreter.id=$invoice_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$assignDate=$row['assignDate'];$source=$row['source'];$buildingName=$row['buildingName'];$street=$row['street'];$assignCity=$row['assignCity'];$nameRef=$row['nameRef'];$orgzName=$row['orgzName'];$inchCity=$row['inchCity'];$inchPcode=$row['inchPcode'];$invoiceNo=$row['invoiceNo'];$intrpName=$row['name'];$inchEmail=$row['inchEmail'];$inchRoad=$row['inchRoad'];$line1=$row['line1'];$line2=$row['line2'];$inchNo=$row['inchNo'];$hoursWorkd=$row['C_hoursWorkd'];$chargInterp=$row['C_chargInterp'];$rateHour=$row['C_rateHour'];$travelMile=$row['C_travelMile'];$rateMile=$row['C_rateMile'];$chargeTravel=$row['C_chargeTravel'];$travelCost=$row['C_travelCost'];$otherCost=$row['C_otherCost'];$travelTimeHour=$row['C_travelTimeHour'];$travelTimeRate=$row['C_travelTimeRate'];$chargeTravelTime=$row['C_chargeTravelTime'];$dueDate=$row['dueDate']; $dated=$row['dated'];$bookinType=$row['bookinType'];$orgRef=$row['orgRef'];$C_admnchargs=$row['C_admnchargs'];$C_otherexpns=$row['C_otherexpns'];$porder=$row['porder'];$C_comments=$row['C_comments'];$orgContact=$row['orgContact'];$commit=$row['commit']; $invoic_date=@$row['invoic_date'];$abrv=@$row['abrv'];}?>

<?php  if(isset($_POST['submit'])){ 
if($commit==0 || @$invoic_date=='0000-00-00'){$acttObj->editFun($table,$invoice_id,'commit',1);



$acttObj->editFun($table,$invoice_id,'invoic_date',date("Y-m-d"));$acttObj->editFun($table,$invoice_id,'dueDate',date("Y-m-d", strtotime("+15 days")));}?><?php } ?>
 <div align="center">
 
 <p style="color:#F00; font-weight:bold">Are your sure you want to commit this Invoice <?php echo $invoiceNo; ?></p>
 <form action="" method="post"><input type="submit" class='prnt' name="submit" value="Press to Commit" style="background-color:#06F; color:#FFF; border:1px solid #09F"/></form></div>
   
  </body>
</html>
<?php 
//....................................Credit Note.........................................
 $flag_inv=$acttObj->uniqueFun('comp_credit','invoiceNo',$invoiceNo);
if(isset($_POST['submit']) && $flag_inv==0){$credit_id= $acttObj->get_id('comp_credit');$acttObj->editFun('comp_credit',$credit_id,'orgName',$abrv);$acttObj->editFun('comp_credit',$credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('comp_credit',$credit_id,'mode','interpreter');$acttObj->editFun('comp_credit',$credit_id,'debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('comp_credit',$credit_id,'debit_date',date("Y-m-d"));}

if(isset($_POST['submit']) && $flag_inv==1){$credit_id=$acttObj->unique_data('comp_credit','id','invoiceNo',$invoiceNo);$acttObj->editFun('comp_credit',$credit_id,'orgName',$abrv);$acttObj->editFun('comp_credit',$credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('comp_credit',$credit_id,'mode','interpreter');$acttObj->editFun('comp_credit',$credit_id,'debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('comp_credit',$credit_id,'debit_date',date("Y-m-d"));}
//.......................................//\\//\\//\\..Credit Note.//\\//\\//\\.................................

//....................................Business Credit Note.........................................
 $flag_inv=$acttObj->uniqueFun('bz_credit','invoiceNo',$invoiceNo);
if(isset($_POST['submit']) && $flag_inv==0){$bz_credit_id= $acttObj->get_id('bz_credit');$acttObj->editFun('bz_credit',$bz_credit_id,'orgName',$abrv);$acttObj->editFun('bz_credit',$bz_credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('bz_credit',$bz_credit_id,'mode','interpreter');$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit_date',date("Y-m-d"));}

if(isset($_POST['submit']) && $flag_inv==1){$bz_credit_id=$acttObj->unique_data('bz_credit','id','invoiceNo',$invoiceNo);$acttObj->editFun('bz_credit',$bz_credit_id,'orgName',$abrv);$acttObj->editFun('bz_credit',$bz_credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('bz_credit',$bz_credit_id,'mode','interpreter');$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit_date',date("Y-m-d"));}
//.......................................//\\//\\//\\..Business Credit Note.//\\//\\//\\.................................
 if(isset($_POST['submit'])){ echo "<script>window.close();</script>";}?><script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>