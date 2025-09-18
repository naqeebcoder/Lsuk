<?php
// include "userhaspage.php";
// SysPermiss::UserHasPage(__FILE__);
	include 'db.php';
	include 'class.php';

$table='interpreter';
$slip_id= $_GET['submit']; 
$fdate= @$_GET['fdate']; 
$tdate= @$_GET['tdate'];

$query1="SELECT * FROM interpreter_reg
  where id=$slip_id";			
$result1 = mysqli_query($con,$query1);
$row1 = mysqli_fetch_array($result1);
$email=$row1["email"]; ?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Remittance</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
</head>

<body>    

<link rel="license" href="">
<style>
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
a:link:after, a:visited:after {  
  content: normal !important;  
}
table { 
border-collapse: collapse; 
border-spacing: 0;
}
td, th { border: 1px solid #CCC; }
 #block_container {
    text-align: center;
}
#block_container > div {
    display: inline-block;
    vertical-align: middle;
}
</style>

<div>  
  <?php  

    $submitvalprint="Print Remittance Advice";
    $submitvalemail="Email Remittance Advice";

    if(isset($_POST['submit']))
    {
      if ($_POST['submit']==$submitvalprint)
      {
        ?>
        <script>window.print()</script>
        <style>.prnt{  display:none; }</style>
        <?php 
      }
    } ?>

    <form id="formone" action="" method="post">
      <!--<a href="https://lsuk.org/lsuk_system/reports_lsuk/pdf/test_rip_pay_slip.php?submit=<?php echo $slip_id; ?>&fdate=<?php echo $fdate; ?>&tdate=<?php echo $tdate; ?>">-->
      <!--    <input type="button" class='prnt' onclick="generate_slip()" name="submit" value="Generate Salary Slip" -->
      <!--  style="cursor:pointer;background-color:red; color:#FFF; border:1px solid #09F;padding: 8px;"/></a>-->
        
      <input type="submit" class='prnt' name="submit" value="<?php echo $submitvalprint;  ?>" 
        style="cursor:pointer;background-color:#06F; color:#FFF; border:1px solid #09F;padding: 8px;" />

      <a href="https://lsuk.org/lsuk_system/reports_lsuk/pdf/test_rip_pay_slip.php?submit=<?php echo $slip_id; ?>&fdate=<?php echo $fdate; ?>&tdate=<?php echo $tdate; ?>"><input type="button" class='prnt' name="submit" value="<?php echo $submitvalemail;  ?>" 
        style="cursor:pointer;background-color:#06F; color:#FFF; border:1px solid #09F;padding: 8px;"/></a>
        
        
    </form>
  </div>

  <?php  
  if(isset($_POST['submit']))
  {
    $nmbr= $acttObj->get_id('interp_salary');
    if($nmbr==NULL)
    {
      $nmbr=0;
    }
    $abrv="";
	  $new_nmbr = str_pad($nmbr, 5, "0", STR_PAD_LEFT);
    $invoice= 'LSUK'.$new_nmbr.''.$abrv;
    $maxId=$nmbr;$acttObj->editFun('interp_salary',$maxId,'invoice',$invoice);
    $acttObj->editFun('interp_salary',$maxId,'interp',$slip_id);
    $acttObj->editFun('interp_salary',$maxId,'frm',$fdate);
    $acttObj->editFun('interp_salary',$maxId,'todate',$tdate);
  }?>
    
   <div id="block_container">

    <div id="bloc1"><h1 style="background-color:#FFF; color:#000; margin-left:10px; font-weight:bold">Language Services UK Limited</h1></div>  
    <div id="bloc2"><img alt="" src="img/logo.png" height="100" width="145"></div>
    <h3 align="center">Translation | Interpreting | Transcription | Cross-Cultural Training & Development</h3>
<hr style="border-top: 1px solid #8c8b8b; width:100%">
</div>
  <div style="position:absolute; left: 5;"><span class="name"><?php echo $row1['name']; ?></span><br />
    <span class="address"><?php echo $row1['address']; ?><br /><?php echo $row1['city']; ?></span><br />
  <span style="text-decoration:underline"><?php echo $row1['email']; ?></span><br /><br />
</div>
<br /><br />
<div align="left" style="position:absolute; margin-top:44px;">
<div style="margin-left:5px; float:left;">
<table>
  <tr>
    <td width="100" bgcolor="#F4F4F4">Slip #</td>
    <td width="1">:</td>
    <td><?php //$query_slip_no=$acttObj->read_specific('CONCAT("LSUK0",max(id)+1) as num','interp_salary',NULL);?>
        <?php //echo $query_slip_no['num'];
        echo @$invoice; ?></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4"><span class="date">Salary Date</span></td>
    <td>:</td>
    <td><span class="date"><?php $date = new DateTime('now');
$date->modify('last day of this month');
echo $misc->dated($date->format('Y-m-d'));?></span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">From</td>
    <td>:</td>
    <td><span class="date"><?php echo $misc->dated($fdate); ?></span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">To</td>
    <td>:</td>
    <td><span class="date"><?php echo $misc->dated($tdate); ?></span></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">National Insurance #</td>
    <td>&nbsp;</td>
    <td><span class="date"><?php echo @$row1['ni'];; ?></span></td>
  </tr>
</table>
</div><br />
<div style=" margin-left:5px;margin-top:10px; float:left; margin-right:5px;">
<table width="100%" >
  <tr>
    <td align="center" bgcolor="#F4F4F4"><span class="desc">DESCRIPTION</span></td>
    </tr>
  <tr>
    <td><table width="100%" border="1" style="color:#FFF">
      <tr>
        <th align="center" bgcolor="#006666" scope="col" colspan="12">Interpreter Services</th>
        
        </tr>
      <tr>
        <th align="left" bgcolor="#006666" scope="col">#</th>
        <th align="left" bgcolor="#006666" scope="col">Assignment Date</th>
        <th align="left" bgcolor="#006666" scope="col">Org Name</th>
        <th align="left" bgcolor="#006666" scope="col">Hours Worked</th>
        <th align="left" bgcolor="#006666" scope="col">Charge for Interpreting Time</th>
        <th align="left" bgcolor="#006666" scope="col">Charge for Travel Cost</th>
        <th align="left" bgcolor="#006666" scope="col">Charge for Travel Time</th>
        <th align="left" bgcolor="#006666" scope="col">Travel Cost</th>
        <th align="left" bgcolor="#006666" scope="col">Other Costs (Parking , Bridge Toll)</th>
        <th align="left" bgcolor="#006666" scope="col">Additional Payment</th>
        <th align="left" bgcolor="#006666" scope="col">Deduction</th>
        <th align="left" bgcolor="#006666" scope="col">Total Charges £</th>
        </tr>
<?php $i=1;$amount1=0;$interp_total=0;$interp_ded_total=0;$con->query("SET SQL_BIG_SELECTS=1");
$query_interp=
"SELECT interpreter.id,interpreter.assignDate, interpreter.orgName, interpreter.hoursWorkd, interpreter.chargInterp, interpreter.chargeTravel, interpreter.chargeTravelTime, interpreter.travelCost, interpreter.otherCost, interpreter.admnchargs, interpreter.deduction, interpreter.total_charges_interp FROM interpreter,invoice where interpreter.invoiceNo=invoice.invoiceNo AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and 
  interpreter.intrpName=$slip_id and interpreter.intrp_salary_comit = 0 and 
  (interpreter.assignDate BETWEEN('$fdate')AND('$tdate') OR interpreter.assignDate < '$fdate') order by interpreter.assignDate";
$result_interp = mysqli_query($con,$query_interp);
while($row_interp = mysqli_fetch_array($result_interp)){?>

      <tr <?php if($row_interp['assignDate']<$fdate){?> style="color: #ffffff;font-weight: bolder;border: 3px solid #ff0707;"<?php } ?>>
        <td align="left" bgcolor="#006666"><?php echo $i++; ?>&nbsp;</td>
        <td align="left" bgcolor="#006666"><?php echo $misc->dated($row_interp['assignDate']); ?></td>
        <td align="left" bgcolor="#006666"><span class="desc"><?php echo $row_interp['orgName']; ?></span></td>
        <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['hoursWorkd']); ?></td>
        <td height="21" align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['chargInterp']); ?></td>
        <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['chargeTravel']); ?></td>
        <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['chargeTravelTime']); ?></td>
        <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['travelCost']); ?></td>
        <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['otherCost']); ?></td>
        <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['admnchargs']);?></td>
        <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['deduction']);$interp_ded_total=$row_interp['deduction'] + $interp_ded_total; ?></td>
        <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($row_interp['total_charges_interp']);$interp_total=$row_interp['total_charges_interp'] + $interp_total; ?></td>
        </tr>

        <?php
        if(isset($_POST['submit']))
        { 
          $acttObj->editFun('interpreter',$row_interp['id'],'intrp_salary_comit',1);
          $acttObj->editFun('interpreter',$row_interp['id'],'paid_date',date("Y-m-d"));
        }
        ?>

          <?php } ?>
                <tr>
        <td colspan="9" align="right" bgcolor="#006666">Total</td>
        <td align="left" bgcolor="#006666">&nbsp;</td>
        <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($interp_ded_total); ?></td>
        <td align="left" bgcolor="#006666"><?php echo $misc->numberFormat_fun($interp_total); ?></td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td><table width="100%" border="1" style="color:#FFF">
      <tr>
        
        <th align="center" bgcolor="#CC9900" scope="col" colspan="9">Telephone Interpreter Services</th>
        </tr>
      <tr>
        <th align="left" bgcolor="#CC9900" scope="col">#</th>
        <th align="left" bgcolor="#CC9900" scope="col">Assignment Date</th>
        <th align="left" bgcolor="#CC9900" scope="col">Org Name</th>
        <th align="left" bgcolor="#CC9900" scope="col">Hours Worked</th>
        <th align="left" bgcolor="#CC9900" scope="col">Call Charges</th>
        <th align="left" bgcolor="#CC9900" scope="col">Other Charges</th>
        <th align="left" bgcolor="#CC9900" scope="col">Additional Payment</th>
        <th align="left" bgcolor="#CC9900" scope="col">Deduction</th>
        <th align="left" bgcolor="#CC9900" scope="col">Total Charges</th>
        </tr>
         <?php $i=1;$telep_total=0;$telep_ded_total=0;
$query_telep=
"SELECT telephone.id,telephone.assignDate,telephone.orgName, telephone.hoursWorkd, telephone.chargInterp, telephone.otherCharges, telephone.admnchargs, telephone.deduction, telephone.total_charges_interp FROM telephone ,invoice where telephone.invoiceNo=invoice.invoiceNo AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and 
 telephone.intrpName=$slip_id and telephone.intrp_salary_comit = 0 and 
 (telephone.assignDate BETWEEN('$fdate')AND('$tdate') OR telephone.assignDate < '$fdate') order by telephone.assignDate";

$result_telep = mysqli_query($con,$query_telep);
while($row_telep = mysqli_fetch_array($result_telep)){?>
         
         <tr <?php if($row_telep['assignDate']<$fdate){?> style="color: #ffffff;font-weight: bolder;border: 3px solid #ff0707;"<?php } ?>>
        <td align="left" bgcolor="#CC9900"><?php echo $i++; ?>&nbsp;</td>
        <td align="left" bgcolor="#CC9900"><?php echo $misc->dated($row_telep['assignDate']); ?></td>
        <td align="left" bgcolor="#CC9900"><span class="desc"><?php echo $row_telep['orgName']; ?></span></td>
        <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['hoursWorkd']); ?></td>
        <td align="left" bgcolor="#CC9900"><span class="desc"><?php echo $misc->numberFormat_fun($row_telep['calCharges']); ?></span></td>
        <td align="left" bgcolor="#CC9900"><span class="desc"><?php echo $misc->numberFormat_fun($row_telep['otherCharges']); ?></span></td>
        <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['admnchargs']);?></td>
        <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['deduction']);$telep_ded_total=$misc->numberFormat_fun($row_telep['deduction'] + $telep_ded_total); ?></td>
        <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($row_telep['total_charges_interp']);$telep_total=$row_telep['total_charges_interp'] + $telep_total; ?></td>
        </tr>

          <?php  
          if(isset($_POST['submit']))
          { 
            $acttObj->editFun('telephone',$row_telep['id'],'intrp_salary_comit',1);
            $acttObj->editFun('telephone',$row_telep['id'],'paid_date',date("Y-m-d"));
          }?>

          <?php } ?>
          <tr>
        <td colspan="7" align="right" bgcolor="#CC9900">Total</td>
        <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($telep_ded_total); ?></td>
        <td align="left" bgcolor="#CC9900"><?php echo $misc->numberFormat_fun($telep_total); ?></td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td><table width="100%" border="1"style="color:#FFF">
      <tr>
        
        <th align="center" bgcolor="#FCFCFC" scope="col" colspan="9">Translation Services</th>
        </tr>
      <tr>
        <th align="left" bgcolor="#3399FF" scope="col">#</th>
        <th align="left" bgcolor="#3399FF" scope="col">Assignment Date</th>
        <th align="left" bgcolor="#3399FF" scope="col">Org Name</th>
        <th align="left" bgcolor="#3399FF" scope="col">Rate per Unit</th>
        <th align="left" bgcolor="#3399FF" scope="col">Unit</th>
        <th align="left" bgcolor="#3399FF" scope="col">Other Charges</th>
        <th align="left" bgcolor="#3399FF" scope="col">Additional Payment</th>
        <th align="left" bgcolor="#3399FF" scope="col">Deduction</th>
        <th align="left" bgcolor="#3399FF" scope="col">Total Charges</th>
        </tr>
 <?php $i=1;$trans_total=0;$trans_ded_total=0;
$query_trans=
"SELECT translation.id,translation.asignDate, translation.orgName, translation.rpU, translation.numberUnit, translation.otherCharg, translation.admnchargs, translation.deduction, translation.total_charges_interp FROM translation,invoice WHERE translation.invoiceNo=invoice.invoiceNo AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.intrpName=$slip_id and translation.intrp_salary_comit = 0 and (translation.asignDate BETWEEN('$fdate')AND('$tdate') OR translation.asignDate < '$fdate') order by translation.asignDate";				
$result_trans = mysqli_query($con,$query_trans);
while($row_trans = mysqli_fetch_array($result_trans)){?>
      
      <tr <?php if($row_trans['asignDate']<$fdate){?> style="color: #ffffff;font-weight: bolder;border: 3px solid #ff0707;"<?php } ?>>
        <td align="left" bgcolor="#3399FF"><?php echo $i++; ?>&nbsp;</td>
        <td align="left" bgcolor="#3399FF"><?php echo $misc->dated($row_trans['asignDate']); ?></td>
        <td align="left" bgcolor="#3399FF"><span class="desc"><?php echo $row_trans['orgName']; ?></span></td>
        <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['rpU']);  ?></td>
        <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['numberUnit']);  ?></td>
        <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['otherCharg']);  ?></td>
        <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['admnchargs']);?></td>
        <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['deduction']);$trans_ded_total=$row_trans['deduction'] + $trans_ded_total; ?></td>
        <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($row_trans['total_charges_interp']);$trans_total=$row_trans['total_charges_interp'] + $trans_total; ?></td>
        </tr>
        
        <?php  
        if(isset($_POST['submit']))
        { 
          $acttObj->editFun('translation',$row_trans['id'],'intrp_salary_comit',1);
          $acttObj->editFun('translation',$row_trans['id'],'paid_date',date("Y-m-d"));
        }?>

          <?php } ?>
          <tr>
        <td colspan="7" align="right" bgcolor="#3399FF">Total</td>
        <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($trans_ded_total); ?></td>
        <td align="left" bgcolor="#3399FF"><?php echo $misc->numberFormat_fun($trans_total); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td bgcolor="#F4F4F4">&nbsp;</td>
  </tr>
</table>
<div style=" margin-top:10px; float:right">
  <table>
    <tr>
      <td width="100" bgcolor="#F4F4F4">Grand Total </td>
      <td width="1">:</td>
      <td><?php echo $misc->numberFormat_fun($grand_total=$interp_total + $telep_total + $trans_total); ?></td>
    </tr>
    <tr>
      <td bgcolor="#F4F4F4">NI Deduction</td>
      <td>&nbsp;</td>
      <td>0.00</td>
    </tr>
    <tr>
      <td bgcolor="#F4F4F4">Tax Deduction</td>
      <td>&nbsp;</td>
      <td>0.00</td>
    </tr>
    <tr>
      <td bgcolor="#F4F4F4">Other Deduction</td>
      <td>:</td>
      <td><?php echo $grand_deduction=$misc->numberFormat_fun($interp_ded_total + $telep_ded_total + $trans_ded_total); ?></td>
    </tr>
    <tr>      

<?php 
$query_tax="
SELECT sum(interpreter.ni_dedu) as ni_dedu,sum(interpreter.tax_dedu) as tax_dedu FROM interpreter 
JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.intrpName=$slip_id and interpreter.intrp_salary_comit = 0 and (interpreter.assignDate BETWEEN('$fdate')AND('$tdate') OR interpreter.assignDate < '$fdate' )
union
SELECT sum(telephone.ni_dedu) as ni_dedu,sum(telephone.tax_dedu) as tax_dedu FROM telephone 
JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.intrpName=$slip_id and telephone.intrp_salary_comit = 0 and (telephone.assignDate BETWEEN('$fdate')AND('$tdate') OR telephone.assignDate < '$fdate' )
union
SELECT sum(translation.ni_dedu) as ni_dedu,sum(translation.tax_dedu) as tax_dedu FROM translation 
JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.intrpName=$slip_id and translation.intrp_salary_comit = 0 and (translation.asignDate BETWEEN('$fdate')AND('$tdate') OR translation.asignDate < '$fdate' )";			

$result_tax = mysqli_query($con,$query_tax);
while($row_tax = mysqli_fetch_array($result_tax))
{ 
  $ni_dedu=$misc->numberFormat_fun($row_tax['ni_dedu']); 
  $tax_dedu=$row_tax['tax_dedu'];
}

?>
      <td bgcolor="#F4F4F4">National Insurance  Deduction</td>
      <td>&nbsp;</td>

      <td><?php echo $misc->numberFormat_fun($ni_dedu); ?></td>
    </tr>
    <tr>
      <td bgcolor="#F4F4F4">Tax Deduction</td>
      <td>&nbsp;</td>
      <td><?php echo $misc->numberFormat_fun($tax_dedu); ?></td>
    </tr>
    <tr>
      <td bgcolor="#F4F4F4">Net Salary</td>
      <td>:</td>
      <td style="font-weight: bold;"><?php echo $misc->numberFormat_fun($grand_total - $tax_dedu - $ni_dedu); ?></td>
    </tr>
    </table>
</div>
<div style="position:absolute; margin-bottom:10px;"><h2>Thanks for business with us!</h2>
<p>Suite 3 Davis House Lodge Causeway Trading Estate Lodge<br />Causeway - FishpondsBristol BS163JB</p>

</div>
</div>
</div>

</div>
</div>
<?php  
if(isset($_POST['submit']))
{
  $acttObj->editFun('interp_salary',$maxId,'deduction',$grand_deduction);
  $acttObj->editFun('interp_salary',$maxId,'salry',$grand_total);
}
?>
<script>
    function generate_slip(){
        alert();
    }
</script>
</body>
</html>