 <?php  
 if(isset($_POST['submit']))
 { ?>
   <script>window.print()</script>
   <style>.prnt{  display:none; }</style>
   <?php 
 } ?>
 <div><form action="" method="post">
 <input type="submit" class='prnt' name="submit" value="Press to Print" 
  style="background-color:#06F; color:#FFF; border:1px solid #09F"onclick="printpage()"/></form></div>

<?php include '../../db.php';
include_once ('../../class.php'); 

$excel=@$_GET['excel'];
$search_1=$_GET['search_1'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];
$counter=0;
$x=0; 
$source_num=0;
$table='interpreter';
$org='';

//...................................................For Multiple Selection...................................\\
$counter=0; 
$arr = explode(',', $search_1);
$_words = implode("' OR orgName = '", $arr);
//......................................\\//\\//\\//\\//........................................................\\
//................................................................................................................?>

<style>
table {border-collapse: collapse; width:100%;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>

<div style="width:100%; text-align:center"><h3>Client Consolidated Report [<?php echo date('Y',strtotime($search_2)); ?>]</h3></div><br />
<div style="width:100%; text-align:right">Report Date: <?php echo $misc->sys_date(); ?></div>
<div style="width:100%; text-align:right">Date  Range:  Date From [<?php echo $misc->dated($search_2); ?>] Date To [<?php echo $misc->dated($search_3)?>]</div>
<p>Organziation(s) Selected</p>
<table class="aa" border="1" cellspacing="1" style="width:700px"">
  <tr>
    <td valign="top">
    <?php if(empty($search_1)){echo "";}else{ echo $search_1;} ?>
     
     </td>
  </tr>
</table><br/>
<!--Face to Face-->
<h3>Face to face Jobs Summary</h3>
<table class="altrowstable" id="alternatecolor">
  <tr>
    <th width="50%">Source Language</th>
  <?php   foreach($arr as $orgName){?>
    <th width="50%"><?php echo $orgName; ?></th>
    <?php $counter++;} ?>
  </tr>
  <?php 
  //all languages
  $queryf="SELECT distinct (interpreter.source) FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv 
  				where (interpreter.orgName = '$_words') and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.assignDate between '$search_2' and '$search_3'";

  $resultf = mysqli_query($con, $queryf); 
  $resultf = mysqli_query($con, $queryf);
  
  while($rowf = mysqli_fetch_assoc($resultf))
  { 
    $langf=$rowf['source']; ?>

    <?php 

    $strTdsf='';
    $bRowZerosf=true;
    //for each lang: all orgname
    foreach($arr as $orgName)
    {
      $orgName=$orgName;
      ?>
  
      <?php	 
      $xf=$counter;
      $uf=0; 
      //only once
      while($xf>$uf)
      { 
        $query_innerf="SELECT count(interpreter.source) as source_num 
          FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv 
  				 INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where interpreter.orgName = '$orgName' and interpreter.source='$langf' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0";
        $result_innerf = mysqli_query($con, $query_innerf);
        //only once
        while($row_innerf = mysqli_fetch_assoc($result_innerf))
        {
            ?>

            <?php	 
            if ($row_innerf['source_num']!=0){
              $bRowZerosf=false;
              $strTdsf.="<td>".$row_innerf['source_num']."</td>";
            }
            if ($row_innerf['source_num']==0){
              $bRowZerosf=false;
              $strTdsf.="<td>0</td>";
            }
          $uf++;
        }
        break;
      }
      
    } ?>

    <?php
    if ($bRowZerosf==false)
    {
      ?>
      <tr>
      <td><?php echo $langf; ?></td>
      <?php
      echo $strTdsf;
      ?>
      </tr>
      <?php 
    }
    ?>

    <?php 
    $xf++;
} 
?>
 
 <tr>
<td><b>Total Cancelled Face to face</b></td>
 <?php  foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_canf="select count(rec) cancelled from (SELECT interpreter.id as rec 
          FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv 
  				 INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where interpreter.orgName = '$orgName' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.orderCancelatoin =1) tbl";
	   $result_total_canf = mysqli_query($con, $query_total_canf);
	   while($row_total_canf = mysqli_fetch_assoc($result_total_canf)){?>
        <?php $row_tot_canf=$row_total_canf["cancelled"];?>
            <?php } ?>
            <td><b><?php echo $row_tot_canf;?></b></td>
       <?php } ?>
	 </tr>
 <tr>
 
<td><b>Total Jobs Face to face</b></td>
 <?php  foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_innerf="select count(source_num) source_num from (SELECT interpreter.source as source_num 
          FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv 
  				 INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where interpreter.orgName = '$orgName' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) tbl";
	   $result_total_innerf = mysqli_query($con, $query_total_innerf);
	   while($row_total_innerf = mysqli_fetch_assoc($result_total_innerf)){?>
        <?php $row_totf=$row_total_innerf["source_num"];?>
            <?php } ?>
            <td><b><?php echo $row_totf;?></b></td>
       <?php } ?>
	 </tr>
 <tr>
 
<td><b>Total Cost Face to face</b></td>
 <?php foreach($arr as $orgName){$orgName=$orgName;?>
   <?php	 
  $query_total_innerf="SELECT round(sum(interpreter.total_charges_comp),2) as total_charges_comp, round(sum(interpreter.total_charges_comp * interpreter.cur_vat),2) as total_charges_comp_vat,round(sum(interpreter.C_otherexpns),2) as other_expenses FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv 
  				
						INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo  
	   					where interpreter.orgName = '$orgName' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0";
	   $result_total_innerf = mysqli_query($con, $query_total_innerf);
	   while($row_total_innerf = mysqli_fetch_assoc($result_total_innerf)){ ?>
    <?php $net_valuef=$row_total_innerf["total_charges_comp_vat"] + $row_total_innerf["total_charges_comp"]+ $row_total_innerf["other_expenses"]; ?>
       <?php } ?>
       <td><b><?php echo $misc->numberFormat_fun($net_valuef); ?></b></td>
       <?php } ?>
	 </tr>
     
</table>
<br>
<!--face to face ends here-->

<!--Telephone-->
<h3>Telephone Jobs Summary</h3>
<table class="altrowstable" id="alternatecolor">
  <tr>
    <th width="50%">Source Language</th>
  <?php   foreach($arr as $orgName){?>
    <th width="50%"><?php echo $orgName; ?></th>
    <?php $counter++;} ?>
  </tr>
  <?php 
  //all languages
  $queryt="SELECT distinct (telephone.source) FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv where (telephone.orgName = '$_words') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.assignDate between '$search_2' and '$search_3'";

  $resultt = mysqli_query($con, $queryt); 
  $resultt = mysqli_query($con, $queryt);
  
  while($rowt = mysqli_fetch_assoc($resultt))
  { 
    $langt=$rowt['source']; ?>

    <?php 

    $strTdst='';
    $bRowZerost=true;
    //for each lang: all orgname
    foreach($arr as $orgName)
    {
      $orgName=$orgName;
      ?>
  
      <?php	 
      $xt=$counter;
      $ut=0; 
      //only once
      while($xt>$ut)
      { 
        $query_innert="SELECT count(telephone.source) as source_num 
          FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv  INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
	   			where telephone.orgName = '$orgName' and telephone.source='$langt' and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0";
        $result_innert = mysqli_query($con, $query_innert);
        //only once
        while($row_innert = mysqli_fetch_assoc($result_innert))
        {
            ?>

            <?php	 
            if ($row_innert['source_num']!=0){
              $bRowZerost=false;
              $strTdst.="<td>".$row_innert['source_num']."</td>";
            }
            if ($row_innert['source_num']==0){
              $bRowZerost=false;
              $strTdst.="<td>0</td>";
            }
          $ut++;
        }
        break;
      }
    } ?>

    <?php
    if ($bRowZerost==false)
    {
      ?>
      <tr>
      <td><?php echo $langt; ?></td>
      <?php
      echo $strTdst;
      ?>
      </tr>
      <?php 
    }
    ?>

    <?php 
    $xt++;
} 
?>
 
 <tr>
<td><b>Total Cancelled Telephone</b></td>
 <?php  foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_cant="select count(rec) cancelled from (SELECT telephone.id as rec 
          FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
	   			where telephone.orgName = '$orgName' and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.orderCancelatoin =1) tbl";
	   $result_total_cant = mysqli_query($con, $query_total_cant);
	   while($row_total_cant = mysqli_fetch_assoc($result_total_cant)){?>
        <?php $row_tot_cant=$row_total_cant["cancelled"];?>
            <?php } ?>
            <td><b><?php echo $row_tot_cant;?></b></td>
       <?php } ?>
	 </tr>
 <tr>
 
<td><b>Total Jobs Telephone</b></td>
 <?php  foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_innert="select count(source_num) source_num from (SELECT telephone.source as source_num 
          FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv  INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
	   			where telephone.orgName = '$orgName' and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) tbl";
	   $result_total_innert = mysqli_query($con, $query_total_innert);
	   while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){?>
        <?php $row_tott=$row_total_innert["source_num"];?>
            <?php } ?>
            <td><b><?php echo $row_tott;?></b></td>
       <?php } ?>
	 </tr>
 <tr>
 
<td><b>Total Cost Telephone</b></td>
 <?php foreach($arr as $orgName){$orgName=$orgName;?>
   <?php	 
  $query_total_innert="SELECT round(sum(telephone.total_charges_comp),2) as total_charges_comp, round(sum(telephone.total_charges_comp * telephone.cur_vat),2) as total_charges_comp_vat,0 as other_expenses FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv 
						INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo  
	   					where telephone.orgName = '$orgName' and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);
	   while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){ ?>
    <?php $net_valuet=$row_total_innert["total_charges_comp_vat"] + $row_total_innert["total_charges_comp"]+ $row_total_innert["other_expenses"]; ?>
       <?php } ?>
       <td><b><?php echo $misc->numberFormat_fun($net_valuet); ?></b></td>
       <?php } ?>
	 </tr>
     
</table>
<br>
<!--Telephone ends here-->

<!--Translation-->
<h3>Translation Jobs Summary</h3>
<table class="altrowstable" id="alternatecolor">
  <tr>
    <th width="50%">Source Language</th>
  <?php   foreach($arr as $orgName){?>
    <th width="50%"><?php echo $orgName; ?></th>
    <?php $counter++;} ?>
  </tr>
  <?php 
  //all languages
  $querytr="SELECT distinct (translation.source) FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv where (translation.orgName = '$_words') and translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.asignDate between '$search_2' and '$search_3'";

  $resulttr = mysqli_query($con, $querytr); 
  $resulttr = mysqli_query($con, $querytr);
  
  while($rowtr = mysqli_fetch_assoc($resulttr))
  { 
    $langtr=$rowtr['source']; ?>

    <?php 

    $strTdstr='';
    $bRowZerostr=true;
    //for each lang: all orgname
    foreach($arr as $orgName)
    {
      $orgName=$orgName;
      ?>
  
      <?php	 
      $xtr=$counter;
      $utr=0; 
      //only once
      while($xtr>$utr)
      { 
        $query_innertr="SELECT count(translation.source) as source_num 
          FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv  INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
	   			where translation.orgName = '$orgName' and translation.source='$langtr' and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.order_cancel_flag=0";
        $result_innertr = mysqli_query($con, $query_innertr);
        //only once
        while($row_innertr = mysqli_fetch_assoc($result_innertr))
        {
            ?>

            <?php	 
            if ($row_innertr['source_num']!=0){
              $bRowZerostr=false;
              $strTdstr.="<td>".$row_innertr['source_num']."</td>";
            }
            if ($row_innertr['source_num']==0){
              $bRowZerostr=false;
              $strTdstr.="<td>0</td>";
            }
          $utr++;
        }
        break;
      }
    } ?>

    <?php
    if ($bRowZerostr==false)
    {
      ?>
      <tr>
      <td><?php echo $langtr; ?></td>
      <?php
      echo $strTdstr;
      ?>
      </tr>
      <?php 
    }
    ?>

    <?php 
    $xtr++;
} 
?>
 
 <tr>
<td><b>Total Cancelled Translation</b></td>
 <?php  foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_cantr="select count(rec) cancelled from (SELECT translation.id as rec 
          FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv  INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
	   			where translation.orgName = '$orgName' and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.orderCancelatoin =1) tbl";
	   $result_total_cantr = mysqli_query($con, $query_total_cantr);
	   while($row_total_cantr = mysqli_fetch_assoc($result_total_cantr)){?>
        <?php $row_tot_cantr=$row_total_cantr["cancelled"];?>
            <?php } ?>
            <td><b><?php echo $row_tot_cantr;?></b></td>
       <?php } ?>
	 </tr>
 <tr>
 
<td><b>Total Jobs Translation</b></td>
 <?php  foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_innertr="select count(source_num) source_num from (SELECT translation.source as source_num 
          FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv  INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
	   			where translation.orgName = '$orgName' and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.order_cancel_flag=0) tbl";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);
	   while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){?>
        <?php $row_tottr=$row_total_innertr["source_num"];?>
            <?php } ?>
            <td><b><?php echo $row_tottr;?></b></td>
       <?php } ?>
	 </tr>
 <tr>
 
<td><b>Total Cost Translation</b></td>
 <?php foreach($arr as $orgName){$orgName=$orgName;?>
   <?php	 
  $query_total_innertr="SELECT round(sum(translation.total_charges_comp),2) as total_charges_comp, round(sum(translation.total_charges_comp * translation.cur_vat),2) as total_charges_comp_vat,0 as other_expenses FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv 
						INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo  
	   					where translation.orgName = '$orgName' and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);
	   while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){ ?>
    <?php $net_valuetr= $row_total_innertr["total_charges_comp_vat"] + $row_total_innertr["total_charges_comp"]+ $row_total_innertr["other_expenses"]; ?>
       <?php } ?>
       <td><b><?php echo $misc->numberFormat_fun($net_valuetr); ?></b></td>
       <?php } ?>
	 </tr>
     
</table>
<br>
<!--Translation ends here-->
<!--Total calculation-->
<h3>Overall Jobs Summary</h3>
     <table class="altrowstable" id="alternatecolor">
  <tr>
    <th width="50%">All Records</th>
  <?php   foreach($arr as $orgName){?>
    <th width="50%"><?php echo $orgName; ?></th>
    <?php $counter++;} ?>
  </tr>
  <?php 
  //all languages
  $query="SELECT distinct (interpreter.source) FROM interpreter	 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv 				
	   			where (interpreter.orgName = '$_words') and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.assignDate between '$search_2' and '$search_3'
	   UNION ALL 
	   SELECT distinct (telephone.source) FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv 					
	   			where (telephone.orgName = '$_words') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.assignDate between '$search_2' and '$search_3'
	   UNION ALL 
	   SELECT distinct (translation.source) FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv 				
	   			where (translation.orgName = '$_words') and translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.asignDate between '$search_2' and '$search_3'
	   ";

  $result = mysqli_query($con, $query); 
  $result = mysqli_query($con, $query);
  
  while($row = mysqli_fetch_assoc($result))
  {
    $lang=$row['source']; ?>

    <?php 

    $strTds='';
    $bRowZeros=true;
    //for each lang: all orgname
    foreach($arr as $orgName)
    {
      $orgName=$orgName;
      ?>
  
      <?php	 
      $x=$counter;
      $u=0; 
      //only once
      while($x>$u)
      { 
        $query_inner="SELECT count(interpreter.source) as source_num 
          FROM interpreter	 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv 				
	   			 INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where interpreter.orgName = '$orgName' and interpreter.source='$lang' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0
	   			UNION ALL 
	   			SELECT count(telephone.source) as source_num 
          FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv 					
	   			 INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
	   			where telephone.orgName = '$orgName' and telephone.source='$lang' and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 
	   			UNION ALL 
	   			SELECT count(translation.source) as source_num 
          FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv 					
	   			 INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
	   			where translation.orgName = '$orgName' and translation.source='$lang' and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.order_cancel_flag=0
	   			";
        $result_inner = mysqli_query($con, $query_inner);
        //only once
        while($row_inner = mysqli_fetch_assoc($result_inner))
        {
            ?>

            <?php	 
            if ($row_inner['source_num']!=0){
              $bRowZeros=false;
            }
          $u++;
        }
        break;
      }
    } ?>

    <?php
    if ($bRowZeros==false)
    {
      ?>
      <tr>
      </tr>
      <?php 
    }
    ?>

    <?php 
    $x++;
} 
?>
 
 <tr>
<td><b>Total Cancelled Jobs</b></td>
 <?php  foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_can="select count(rec) cancelled from (SELECT interpreter.id as rec 
          FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv  INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where interpreter.orgName = '$orgName' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.orderCancelatoin =1
	   			UNION ALL 
	   			SELECT telephone.id as rec 
          FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv  INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
	   			where telephone.orgName = '$orgName'  and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.orderCancelatoin =1
	   			UNION ALL 
	   			SELECT translation.id as rec 
          FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv  INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
	   			where translation.orgName = '$orgName'  and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.orderCancelatoin =1) tbl";
	   $result_total_can = mysqli_query($con, $query_total_can);
	   while($row_total_can = mysqli_fetch_assoc($result_total_can)){?>
        <?php $row_tot_can=$row_total_can["cancelled"];?>
            <?php } ?>
            <td><b><?php echo $row_tot_can;?></b></td>
       <?php } ?>
	 </tr>
 <tr>
 
<td><b>Total Jobs</b></td>
 <?php  foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_inner="select count(source_num) source_num from (SELECT interpreter.source as source_num 
          FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv  INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where interpreter.orgName = '$orgName' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0
	   			UNION ALL 
	   			SELECT telephone.source as source_num 
          FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv  INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
	   			where telephone.orgName = '$orgName'  and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 
	   			UNION ALL 
	   			SELECT translation.source as source_num 
          FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv  INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
	   			where translation.orgName = '$orgName'  and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.order_cancel_flag=0) tbl";
	   $result_total_inner = mysqli_query($con, $query_total_inner);
	   while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){?>
        <?php $row_tot=$row_total_inner["source_num"];?>
            <?php } ?>
            <td><b><?php echo $row_tot;?></b></td>
       <?php } ?>
	 </tr>
 <tr>
 
<td><b>Total Cost</b></td>
 <?php foreach($arr as $orgName){
 $orgName=$orgName;?>
   <?php	 
  $query_total_inner="SELECT round(sum(interpreter.total_charges_comp),2) as total_charges_comp, round(sum(interpreter.total_charges_comp * interpreter.cur_vat),2) as total_charges_comp_vat,round(sum(interpreter.C_otherexpns),2) as other_expenses FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv 
						INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo  
	   					where interpreter.orgName = '$orgName' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0
	   					UNION ALL
	   					SELECT round(sum(telephone.total_charges_comp),2) as total_charges_comp, round(sum(telephone.total_charges_comp * telephone.cur_vat),2) as total_charges_comp_vat,'0' as other_expenses FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv 
						INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo  
	   					where telephone.orgName = '$orgName' and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0
	   					UNION ALL
	   					SELECT round(sum(translation.total_charges_comp),2) as total_charges_comp, round(sum(translation.total_charges_comp * translation.cur_vat),2) as total_charges_comp_vat,'0' as other_expenses FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv 
						INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo  
	   					where translation.orgName = '$orgName' and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.order_cancel_flag=0
	   					";
	   $result_total_inner = mysqli_query($con, $query_total_inner);
	   while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){ ?>
    <?php $net_value=$net_value+ ($row_total_inner["total_charges_comp_vat"] + $row_total_inner["total_charges_comp"]+ $row_total_inner["other_expenses"]); ?>
       <?php } ?>
       <td><b><?php echo $misc->numberFormat_fun($net_value); ?></b></td>
       <?php $net_value=0; } ?>
	 </tr>
     
</table>
