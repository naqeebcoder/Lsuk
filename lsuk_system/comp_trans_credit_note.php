<?php session_start(); include'db.php'; include'class.php'; $update_id=$_GET['update_id']; $table='translation';
$query="SELECT * FROM $table where id=$update_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$bookinType=$row['bookinType'];$C_numberUnit=$row['C_numberUnit'];$C_rpU=$row['C_rpU'];$C_otherCharg=$row['C_otherCharg'];$bookinType=$row['bookinType'];$total_charges_comp=$row['total_charges_comp'];$certificationCost=$row['certificationCost'];$proofCost=$row['proofCost'];$postageCost=$row['postageCost'];$C_numberWord=$row['C_numberWord'];$C_rpW=$row['C_rpW'];$C_admnchargs=$row['C_admnchargs'];$proofCost=$row['proofCost'];$porder=$row['porder'];$C_comments=$row['C_comments'];$credit_note=$row['credit_note'];}?>
<?php $interp_rpu=$acttObj->unique_data('booking_type','rate','title',$bookinType) ; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Order Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
          <script>	
		function calcInterp() {
		var hoursWorkd = parseFloat(document.getElementById('C_numberUnit').value);	
		var rateHour = parseFloat(document.getElementById('C_rpU').value);
		var certificationCost = parseFloat(document.getElementById('certificationCost').value);	
		var proofCost = parseFloat(document.getElementById('proofCost').value);	
		var postageCost = parseFloat(document.getElementById('postageCost').value);
		var x = rateHour * hoursWorkd;
		var C_numberWord = parseFloat(document.getElementById('C_numberWord').value);	
		var C_rpW = parseFloat(document.getElementById('C_rpW').value);
		var y = C_rpW * C_numberWord;
		
		var otherCharges = parseFloat(document.getElementById('C_otherCharg').value);
				
		var C_admnchargs = parseFloat(document.getElementById('C_admnchargs').value);

		total_charges_comp.value=parseFloat(y+x+otherCharges+certificationCost + proofCost+ postageCost+C_admnchargs);
		}
        
        </script>
    </head>
<body>    
<form action="" method="post" class="register">
  <h1>Credit Note (Translation)</h1>
  <fieldset class="row1">
    <legend>Amount Details (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)
          </legend>
          <p><label>Number of Unit </label>
          <input name="C_numberUnit" type="text" required='' id="C_numberUnit" style="border:1px solid #CCC" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_numberUnit; ?>" readonly="readonly" oninput="calcInterp()" />
          
           <label>Rate Per Unit </label>
      <input name="C_rpU" type="text" required='' id="C_rpU" style="border:1px solid #CCC" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php if($C_rpU!=0){echo $C_rpU ;}else{echo $interp_rpu;} ?>" readonly="readonly"oninput="calcInterp()" />
      <?php if(isset($_POST['submit'])){$c1=$_POST['C_numberUnit']; $acttObj->editFun($table,$update_id,'C_numberUnit',$c1);} ?>
      <?php if(isset($_POST['submit'])){$c1=$_POST['C_rpU']; $acttObj->editFun($table,$update_id,'C_rpU',$c1);} ?>
          </p>
          <p>
            <label>Number of Words </label>
          <input name="C_numberWord" type="text" required='' id="C_numberWord" style="border:1px solid #CCC" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_numberWord; ?>" readonly="readonly" oninput="calcInterp()" />
          
           <label>Rate Per Word </label>
      <input name="C_rpW" type="text" required='' id="C_rpW" style="border:1px solid #CCC" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_rpW; ?>" readonly="readonly"oninput="calcInterp()" />
      <?php if(isset($_POST['submit'])){$c1=$_POST['C_numberWord']; $acttObj->editFun($table,$update_id,'C_numberWord',$c1);} ?>
      <?php if(isset($_POST['submit'])){$c1=$_POST['C_rpW']; $acttObj->editFun($table,$update_id,'C_rpW',$c1);} ?>
          </p>
          <p><label>CERTIFICATION COST(£) </label>
          <input name="certificationCost" type="text" required='' id="certificationCost" style="border:1px solid #CCC" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $certificationCost; ?>" readonly="readonly" oninput="calcInterp()" />
          
           <label>PROOF READING COST(£) </label>
      <input name="proofCost" type="text" required='' id="proofCost" style="border:1px solid #CCC" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $proofCost; ?>" readonly="readonly"oninput="calcInterp()" />
      <?php if(isset($_POST['submit'])){$c1=$_POST['certificationCost']; $acttObj->editFun($table,$update_id,'certificationCost',$c1);} ?>
      <?php if(isset($_POST['submit'])){$c1=$_POST['proofCost']; $acttObj->editFun($table,$update_id,'proofCost',$c1);} ?>
          </p>
    <p>
   
      <label>POSTAGE COST(£) </label>
      <input name="postageCost" type="text" required='' id="postageCost" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01"value="<?php echo $postageCost; ?>" readonly="readonly" oninput="calcInterp()" />

      <label> Any other Charges </label>
      <input name="C_otherCharg" type="text" required='' id="C_otherCharg" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01"value="<?php echo $C_otherCharg; ?>" readonly="readonly" oninput="calcInterp()" />
       
     
      <?php if(isset($_POST['submit'])){$c1=$_POST['postageCost']; $acttObj->editFun($table,$update_id,'postageCost',$c1);} ?>
      
      <?php if(isset($_POST['submit'])){$c1=$_POST['C_otherCharg']; $acttObj->editFun($table,$update_id,'C_otherCharg',$c1);} ?>
    </p>
<p>
   <label> Admin Charges </label>
      <input name="C_admnchargs" type="text" required='' id="C_admnchargs" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01"value="<?php echo $C_admnchargs; ?>" readonly="readonly" oninput="calcInterp()" />
       
     
      
   
      <!--
                  <label class="obinfo">* obligatory fields-->
    <label >Total </label>
      <input name="total_charges_comp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_comp" value="<?php echo $total_charges_comp ?>" readonly="readonly"/>
                  <?php if(isset($_POST['submit'])){$c10=$_POST['total_charges_comp'];$acttObj->editFun($table,$update_id,'total_charges_comp',$c10);} ?>
                  <?php if(isset($_POST['submit'])){$c1=$_POST['C_admnchargs']; $acttObj->editFun($table,$update_id,'C_admnchargs',$c1);} ?>
</p>
<p> <label><strong><em>Purchase Order No.</em></strong></label>
                  <input name="porder" type="text" id="porder" placeholder='' value="<?php echo $porder ?>" readonly="readonly" />
                <?php if(isset($_POST['submit'])){$data=$_POST['porder'];$acttObj->editFun($table,$update_id,'porder',$data);} ?></p>
<div class="infobox"><h4>Notes if Any 1000 alphabets</h4>
                  <p>
                    <textarea name="C_comments" cols="51" rows="5" readonly="readonly"><?php echo $C_comments; ?></textarea>
                    <?php if(isset($_POST['submit'])){$data=$_POST['C_comments'];$acttObj->editFun($table,$update_id,'C_comments',$data);} ?>
                  </p><div>
              <?php echo $credit_note; if(empty($credit_note)){ ?>
              <button class="button" type="submit" name="submit">Submit &raquo;</button><?php } ?></div>
              </div>
  </fieldset>
  
</form>
</body>
</html>
<?php
if(isset($_POST['submit'])){$acttObj->editFun($table,$update_id,'comp_hrsubmited',ucwords($_SESSION['UserName']));$acttObj->editFun($table,$update_id,'comp_hr_date',$misc->sys_date_db());

//........................................//\\//\\Credit Note #//\\//\\//\\...........................................//
if(isset($_POST['submit']) && empty($credit_note)){
	$nmbr=$update_id;if($nmbr==NULL){$nmbr=0;}$abrv='CREDIT';
	$new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
	//$month=date('M');
	//$month=substr($month,0,3);  
	$invoice_crdt= 'LSUK'.$new_nmbr.''.$abrv;
	//$maxId=$nmbr;$acttObj->editFun('invoice',$maxId,'invoiceNo',$invoice);
	$acttObj->editFun($table,$update_id,'credit_note',$invoice_crdt);}
//.....................................................//\\//\\//\\//\\//\\//\\//\\//\\//\\..........................

echo "<script>alert('Successful!');</script>";}

?>

<script>
  window.onunload = refreshParent;
 function refreshParent() {
    window.opener.location.reload();
	
}</script>





