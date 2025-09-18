<?php 
session_start();
include'db.php'; 
include'class.php'; 
$table='interpreter';
$update_id= @$_GET['update_id'];

$query="SELECT * FROM $table where id=$update_id";			
$result = mysqli_query($con,$query);
while($row = mysqli_fetch_array($result))
{
  $hoursWorkd=$row['C_hoursWorkd'];
  $chargInterp=$row['C_chargInterp'];
  $rateHour=$row['C_rateHour'];
  $travelMile=$row['C_travelMile'];
  $rateMile=$row['C_rateMile'];
  $chargeTravel=$row['C_chargeTravel'];
  $travelCost=$row['C_travelCost'];$otherCost=$row['C_otherCost'];
  $travelTimeHour=$row['C_travelTimeHour'];$travelTimeRate=$row['C_travelTimeRate'];
  $chargeTravelTime=$row['C_chargeTravelTime'];$C_deduction=$row['C_deduction'];
  $bookinType=$row['bookinType'];$bookinType=$row['bookinType'];$C_admnchargs=$row['C_admnchargs'];
  $C_otherexpns=$row['C_otherexpns'];$total_charges_comp=$row['total_charges_comp'];
  $porder=$row['porder'];$C_comments=$row['C_comments'];$credit_note=$row['credit_note'];
}?>

<?php $interp_rph=$acttObj->unique_data('booking_type','rate','title',$bookinType) ; ?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Order Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
                <script>	

function calcInterp() {var C_admnchargs = parseFloat(document.getElementById('C_admnchargs').value);	
		var hoursWorkd = parseFloat(document.getElementById('hoursWorkd').value);	
		var rateHour = parseFloat(document.getElementById('rateHour').value);
		var chargInterp = document.getElementById('chargInterp');	
		var x = rateHour * hoursWorkd;
		chargInterp.value = x;
		
		var travelMile = parseFloat(document.getElementById('travelMile').value);	
		var rateMile = parseFloat(document.getElementById('rateMile').value);
		var chargeTravel = document.getElementById('chargeTravel');	
		var y = travelMile * rateMile;
		chargeTravel.value = y;
		
		var travelTimeHour = parseFloat(document.getElementById('travelTimeHour').value);	
		var travelTimeRate = parseFloat(document.getElementById('travelTimeRate').value);
		var chargeTravelTime = document.getElementById('chargeTravelTime');	
		var travelCost = parseFloat(document.getElementById('travelCost').value);
		var z = travelTimeHour * travelTimeRate;
		chargeTravelTime.value = z;		
		
		var otherCost = parseFloat(document.getElementById('otherCost').value);	
		var deduction = parseFloat(document.getElementById('deduction').value);	
		
		
			
		C_otherexpns.value=parseFloat(otherCost+travelCost);
		totalChages.value=parseFloat(x+y+z)-parseFloat(deduction)+C_admnchargs;
		}
        
        </script>
    </head>
<body>    
        <form action="" method="post" class="register">
          <h1>Credit Note (Interpreter)</h1>
          <fieldset class="row1">
            <legend>Interpreting Time (Booking Type: <span style="color:#900;"><?php echo $bookinType; ?></span>)
          </legend>
            <p>
              <label >Hours Worked
  </label>
                    <input name="hoursWorkd" type="text" id="hoursWorkd"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $hoursWorkd ?>" readonly="readonly"oninput="calcInterp()"/> 
                    <?php if(isset($_POST['submit']))
                    {
                      $c7=$_POST['hoursWorkd'];
                      $acttObj->editFun($table,$update_id,'C_hoursWorkd',$c7);} 
                    ?>
                   
                      <?php if(@$_SESSION['prv']=='Management' || @$_SESSION['prv']=='Finance' ){?>
               <label >        Rate Per Hour
           </label>
               <input name="rateHour" type="text" id="rateHour"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php if($rateHour!=0){echo $rateHour;}else{echo $interp_rph;} ?>" readonly="readonly"oninput="calcInterp()"/>
             
               <?php if(isset($_POST['submit']))
               {
                 $c8=$_POST['rateHour'];
                 $acttObj->editFun($table,$update_id,'C_rateHour',$c8);} 
               ?>
            </p>
            <p>
              <label >Charge for Interpreting Time
                </label>
                  <input name="chargInterp" type="text" id="chargInterp"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $chargInterp ?>" readonly="readonly"/>
                  <!--
                  <label class="obinfo">* obligatory fields-->
                  </label>
              <?php 
              if(isset($_POST['submit']))
              {
                $c9=$_POST['chargInterp'];
                $acttObj->editFun($table,$update_id,'C_chargInterp',$c9);
              } 
              ?>
            </p>
          </fieldset>
          <fieldset class="row2">
              <legend>Travel Costs
</legend>
      <p>
            <label >Travel Mileage</label>
            <input name="travelMile" type="text" class="long" id="travelMile"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelMile ?>" readonly="readonly" oninput="calcInterp()"/>
        <?php if(isset($_POST['submit']))
        {
          $c11=$_POST['travelMile'];
          $acttObj->editFun($table,$update_id,'C_travelMile',$c11);
        } 
        ?>
      </p>
        <p>
<label >Rate Per Mileage &pound;</label>
                    <input name="rateMile" type="text" class="long" id="rateMile"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $rateMile ?>" readonly="readonly"oninput="calcInterp()"/>
        <?php if(isset($_POST['submit']))
        {
          $c12=$_POST['rateMile'];
          $acttObj->editFun($table,$update_id,'C_rateMile',$c12);
        } 
        ?>
                </p>
                <p>
                  <label > Mileage Cost &pound;</label>
                    <input name="chargeTravel" type="text" id="chargeTravel" placeholder='' style="border:1px solid #CCC"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $chargeTravel ?>" readonly="readonly" />
        <?php if(isset($_POST['submit']))
        {
          $c13=$_POST['chargeTravel'];
          $acttObj->editFun($table,$update_id,'C_chargeTravel',$c13);
        } 
        ?>
    </p>
              <p>
                <label >Public Transport Cost
    </label>
                    <input name="travelCost" type="text" id="travelCost" placeholder=''  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelCost ?>" readonly="readonly"oninput="calcInterp();" />
        <?php if(isset($_POST['submit']))
        {
          $c14=$_POST['travelCost'];
          $acttObj->editFun($table,$update_id,'C_travelCost',$c14);
        } 
        ?>
    </p>
     <p>
                  <label> Admin Charges </label>
       <input name="C_admnchargs" type="text" id="C_admnchargs" placeholder='' style="border:1px solid #CCC"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_admnchargs ?>" readonly="readonly" oninput="calcInterp();" />
    <?php if(isset($_POST['submit']))
    {
      $c1=$_POST['C_admnchargs']; 
      $acttObj->editFun($table,$update_id,'C_admnchargs',$c1);
    }?>
    </p>
    <p>
                <label >Other Costs (Parking , Bridge Toll)
</label>
                    <input name="otherCost" type="text" id="otherCost" placeholder=''  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $otherCost ?>" readonly="readonly" oninput="calcInterp();"/>
    <?php if(isset($_POST['submit']))
    {
      $c15=$_POST['otherCost'];
      $acttObj->editFun($table,$update_id,'C_otherCost',$c15);
    } 
    ?>
    </p>
    
      <p>
      <label >Deduction
</label>
    <input name="deduction" type="text" id="deduction" placeholder=''  
      pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_deduction ?>" readonly="readonly" oninput="calcInterp();"/>

    <?php if(isset($_POST['submit']))
    {
      $c15=$_POST['deduction'];
      $acttObj->editFun($table,$update_id,'C_deduction',$c15);
    } 
    ?>

    </p>
    <p>
      <label >Other Expenses-Total
</label>
                    <input name="C_otherexpns" type="text" id="C_otherexpns"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $C_otherexpns ?>" placeholder=''readonly />
    <?php if(isset($_POST['submit']))
    {
      $data=$_POST['C_otherexpns'];
      $acttObj->editFun($table,$update_id,'C_otherexpns',$data);
    } 
    ?>

    </p>
    <p>
      <label >Total Charges
</label>
                    <input name="totalChages" type="text" id="totalChages"  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $total_charges_comp ?>" placeholder=''readonly />
              
    </p>
</fieldset>

            <fieldset class="row3">
                <legend>Travel Time
  </legend>
                <p>
                  <label> Travel Time Hours </label>
                  <input name="travelTimeHour" type="text" id="travelTimeHour" placeholder=''  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelTimeHour ?>" readonly="readonly" oninput="calcInterp()"/>
    
    <?php if(isset($_POST['submit']))
    {
      $c18=$_POST['travelTimeHour'];
      $acttObj->editFun($table,$update_id,'C_travelTimeHour',$c18);
    } 
    ?>
                </p>
              <p>
                <label >Travel Time Rate Per Hour</label>
                <input name="travelTimeRate" type="text" id="travelTimeRate" placeholder=''  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $travelTimeRate ?>" readonly="readonly" oninput="calcInterp()"/>
    
    <?php if(isset($_POST['submit']))
    {
      $c19=$_POST['travelTimeRate'];
      $acttObj->editFun($table,$update_id,'C_travelTimeRate',$c19);
    } 
    ?>
              </p>
            <p>
          <label>Charge for Travel Time </label>
                  <input name="chargeTravelTime" type="text" id="chargeTravelTime" placeholder=''  pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $chargeTravelTime ?>" readonly="readonly" />
                
      <?php if(isset($_POST['submit']))
      {
        $c20=$_POST['chargeTravelTime'];
        $acttObj->editFun($table,$update_id,'C_chargeTravelTime',$c20);
      } 
      ?>
            </p>
            <p> <label><strong><em>Purchase Order No.</em></strong></label>
                  <input name="porder" type="text" id="porder" placeholder='' value="<?php echo $porder ?>" readonly="readonly" />
                
      <?php if(isset($_POST['submit']))
      {
        $data=$_POST['porder'];
        $acttObj->editFun($table,$update_id,'porder',$data);
      } 
      ?>
      
      </p>
            <div class="infobox"><h4>Notes if Any 1000 alphabets</h4>
                  <p>
                    <textarea name="C_comments" cols="51" rows="5" readonly="readonly"><?php echo $C_comments; ?></textarea>
                    
      <?php if(isset($_POST['submit']))
      {
        $data=$_POST['C_comments'];
        $acttObj->editFun($table,$update_id,'C_comments',$data);
      } 
      ?>
                  </p>
              </div><div>
              <?php echo $credit_note; if(empty($credit_note)){ ?>
              <button class="button" type="submit" name="submit">Submit &raquo;</button><?php } ?></div>
            </fieldset>
			<?php 

if(isset($_POST['submit']))
{ 
  $total1=$c7 * $c8;$total2=$c18 * $c19; $total3=$c11 * $c12; 
  $acttObj->editFun($table,$update_id,'total_charges_comp',$total1+$total2+$total3+$c15+$C_admnchargs);
  $acttObj->editFun($table,$update_id,'comp_hrsubmited',ucwords($_SESSION['UserName']));
  $acttObj->editFun($table,$update_id,'comp_hr_date',$misc->sys_date_db());
}

?> <?php } ?>
            
          
        </form>
</body>
</html>
<?php 
//........................................//\\//\\Credit Note #//\\//\\//\\...........................................//
if(isset($_POST['submit']) && empty($credit_note))
{
  $nmbr=$update_id;if($nmbr==NULL)
  {
    $nmbr=0;}$abrv='CREDIT';
	$new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
	//$month=date('M');
	//$month=substr($month,0,3);  
	$invoice_crdt= 'LSUK'.$new_nmbr.''.$abrv;
	//$maxId=$nmbr;$acttObj->editFun('invoice',$maxId,'invoiceNo',$invoice);
  $acttObj->editFun($table,$update_id,'credit_note',$invoice_crdt);
  
  $total1=$c7 * $c8;
  //paid work
  $total2=$c18 * $c19; 
  //paid travel
  $total3=$c11 * $c12; 
  //paid travel expenses
  //$c15 = other cost
  $acttObj->editFun($table,$update_id,'total_charges_comp',0);
  //$acttObj->editFun($table,$update_id,'comp_hrsubmited',ucwords($_SESSION['UserName']));
  //$acttObj->editFun($table,$update_id,'comp_hr_date',$misc->sys_date_db());
}

//.....................................................//\\//\\//\\//\\//\\//\\//\\//\\//\\..........................
if(isset($_POST['submit']))
{
  echo "<script>alert('Successful!');</script>";
}

?>


<script>
  window.onunload = refreshParent;
 function refreshParent() {
    window.opener.location.reload();
}</script>

