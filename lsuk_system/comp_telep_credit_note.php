<?php session_start(); include'db.php'; include'class.php'; $table='telephone';$update_id= @$_GET['update_id'];
$query="SELECT * FROM $table where id=$update_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result)){$bookinType=$row['bookinType'];$hoursWorkd=$row['C_hoursWorkd'];$chargInterp=$row['C_chargInterp'];$rateHour=$row['C_rateHour'];$C_otherCharges=$row['C_otherCharges'];$bookinType=$row['bookinType'];$total_charges_comp=$row['total_charges_comp'];$C_admnchargs=$row['C_admnchargs'];$porder=$row['porder'];$C_comments=$row['C_comments'];$credit_note=$row['credit_note'];}?>
<?php $interp_rpm=$acttObj->unique_data('booking_type','rate','title',$bookinType) ; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
        <title>Order Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
                    <script>	
		function calcInterp() {
		var hoursWorkd = parseFloat(document.getElementById('hoursWorkd').value);	
		var rateHour = parseFloat(document.getElementById('rateHour').value);
		var chargInterp = document.getElementById('chargInterp');	
		var x = rateHour * hoursWorkd;
		chargInterp.value = x;
			
		var C_otherCharges = parseFloat(document.getElementById('C_otherCharges').value);
				
		var C_admnchargs = parseFloat(document.getElementById('C_admnchargs').value);

		total_charges_comp.value=parseFloat(x+C_otherCharges+C_admnchargs);
		}
        
        </script>
    </head>
<body>    
        <form action="" method="post" class="register">
          <h1>Credit Note (Telephone)</h1>
          <fieldset class="row1">
            <legend>Interpreting Time (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)
          </legend>
            <p>
              <label >Hours Worked
  </label>
                    <input name="hoursWorkd" type="text" id="hoursWorkd"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $hoursWorkd ?>" readonly="readonly"oninput="calcInterp()"/><?php if(isset($_POST['submit'])){$c7=$_POST['hoursWorkd'];$acttObj->editFun($table,$update_id,'C_hoursWorkd',$c7);} ?>
                  <?php if(@$_SESSION['prv']=='Management' || @$_SESSION['prv']=='Finance' ){?>  <label >Rate Per Min
           </label>
               <input name="rateHour" type="text" id="rateHour"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php if($rateHour!=0){echo $rateHour ;}else{echo $interp_rpm;} ?>" readonly="readonly"oninput="calcInterp()"/>
              
               <?php if(isset($_POST['submit'])){$c8=$_POST['rateHour'];$acttObj->editFun($table,$update_id,'C_rateHour',$c8);} ?>
            </p>
            <p>
              <label >Charge for Interpreting Time
                </label>
                  <input name="chargInterp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="chargInterp" value="<?php echo $chargInterp ?>"readonly/>
                  <!--
                  <label class="obinfo">* obligatory fields-->
                  </label>
              <?php if(isset($_POST['submit'])){$c9=$_POST['chargInterp'];$acttObj->editFun($table,$update_id,'C_chargInterp',$c9);} ?>
            </p><?php } ?>
              <p>
               <label >Other Charges</label>
               <input name="C_otherCharges" type="text" id="C_otherCharges"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_otherCharges ?>" readonly="readonly"oninput="calcInterp()"/>
                  <label> Admin Charges </label>
      <input name="C_admnchargs" type="text" required='' id="C_admnchargs" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01"value="<?php echo $C_admnchargs; ?>" readonly="readonly" oninput="calcInterp()" />
               <?php if(isset($_POST['submit'])){$c10=$_POST['C_otherCharges'];$acttObj->editFun($table,$update_id,'C_otherCharges',$c10);} ?>
           <?php if(isset($_POST['submit'])){$c1=$_POST['C_admnchargs']; $acttObj->editFun($table,$update_id,'C_admnchargs',$c1);}?>
            </p>
            
             <p> <label >Total
                </label>
                  <input name="total_charges_comp" type="text"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="total_charges_comp" value="<?php echo $total_charges_comp ?>"readonly/>
              <?php if(isset($_POST['submit'])){$c10=$_POST['total_charges_comp'];$acttObj->editFun($table,$update_id,'total_charges_comp',$c10);} ?></p>
              <p> <label><strong><em>Purchase Order No.</em></strong></label>
                  <input name="porder" type="text" id="porder" placeholder='' value="<?php echo $porder ?>" readonly="readonly" />
                <?php if(isset($_POST['submit'])){$data=$_POST['porder'];$acttObj->editFun($table,$update_id,'porder',$data);} ?></p>
              <div class="infobox"><h4>Notes if Any 1000 alphabets</h4>
                  <p>
                    <textarea name="C_comments" cols="51" rows="5" readonly="readonly"><?php echo $C_comments; ?></textarea>
                    <?php if(isset($_POST['submit'])){$data=$_POST['C_comments'];$acttObj->editFun($table,$update_id,'C_comments',$data);} ?>
                  </p> <div>
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

