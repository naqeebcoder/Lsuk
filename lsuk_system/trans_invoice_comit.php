<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>LSUK-Invoice</title>
    <link rel="license" href="http://www.opensource.org/licenses/mit-license/">
  </head>
  <body>
<?php include'db.php'; include'class.php'; $table='translation';$invoice_id=$_GET['invoice_id'];
$query="SELECT $table.*,invoice.dated, interpreter_reg.name as intrpName,comp_reg.*,comp_reg.name as orgzName,comp_reg.abrv FROM $table
INNER JOIN invoice ON $table.invoiceNo=invoice.invoiceNo
INNER JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
INNER JOIN comp_reg ON $table.orgName=comp_reg.abrv

 where multInv_flag=0 and $table.id=$invoice_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$orgzName=$row['orgzName'];$buildingName=$row['buildingName'];$line1=$row['line1'];$line2=$row['line2'];$city=$row['city'];$postCode=$row['postCode'];$source=$row['source'];$asignDate=$row['asignDate'];$orgName=$row['orgName'];$buildingName=$row['buildingName'];$orgContact=$row['orgContact'];$invoiceNo=$row['invoiceNo'];$inchEmail=$row['inchEmail'];$C_numberUnit=$row['C_numberUnit'];$C_rpU=$row['C_rpU'];$C_otherCharg=$row['C_otherCharg'];$total_charges_comp=$row['total_charges_comp'];$dueDate=$row['dueDate'];$dated=$row['dated'];$nameRef=$row['nameRef'];$transType=$row['transType']; $intrpName=$row['intrpName'];$bookinType=$row['bookinType'];$certificationCost=$row['certificationCost'];$proofCost=$row['proofCost'];$postageCost=$row['postageCost'];$C_numberWord=$row['C_numberWord'];$C_rpW=$row['C_rpW'];$C_admnchargs=$row['C_admnchargs'];$porder=$row['porder'];$C_comments=$row['C_comments'];$orgRef=$row['orgRef'];$commit=$row['commit']; $invoic_date=@$row['invoic_date'];$abrv=@$row['abrv']; }?>

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
if(isset($_POST['submit']) && $flag_inv==0){$credit_id= $acttObj->get_id('comp_credit');$acttObj->editFun('comp_credit',$credit_id,'orgName',$abrv);$acttObj->editFun('comp_credit',$credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('comp_credit',$credit_id,'mode','translation');$acttObj->editFun('comp_credit',$credit_id,'debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('comp_credit',$credit_id,'debit_date',date("Y-m-d"));}

if(isset($_POST['submit']) && $flag_inv==1){$credit_id=$acttObj->unique_data('comp_credit','id','invoiceNo',$invoiceNo);$acttObj->editFun('comp_credit',$credit_id,'orgName',$abrv);$acttObj->editFun('comp_credit',$credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('comp_credit',$credit_id,'mode','translation');$acttObj->editFun('comp_credit',$credit_id,'debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('comp_credit',$credit_id,'debit_date',date("Y-m-d"));}
//.......................................//\\//\\//\\..Credit Note.//\\//\\//\\.................................
//....................................Business Credit Note.........................................
 $flag_inv=$acttObj->uniqueFun('bz_credit','invoiceNo',$invoiceNo);
if(isset($_POST['submit']) && $flag_inv==0){$bz_credit_id= $acttObj->get_id('bz_credit');$acttObj->editFun('bz_credit',$bz_credit_id,'orgName',$abrv);$acttObj->editFun('bz_credit',$bz_credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('bz_credit',$bz_credit_id,'mode','interpreter');$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit_date',date("Y-m-d"));}

if(isset($_POST['submit']) && $flag_inv==1){$bz_credit_id=$acttObj->unique_data('bz_credit','id','invoiceNo',$invoiceNo);$acttObj->editFun('bz_credit',$bz_credit_id,'orgName',$abrv);$acttObj->editFun('bz_credit',$bz_credit_id,'invoiceNo',$invoiceNo);$acttObj->editFun('bz_credit',$bz_credit_id,'mode','translation');$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit',@$vat+@$total5+@$C_otherexpns);$acttObj->editFun('bz_credit',$bz_credit_id,'bz_debit_date',date("Y-m-d"));}
//.......................................//\\//\\//\\..Business Credit Note.//\\//\\//\\.................................
 if(isset($_POST['submit'])){ echo "<script>window.close();</script>";}?><script>window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script>