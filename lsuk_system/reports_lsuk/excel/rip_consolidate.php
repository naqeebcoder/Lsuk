<?php include '../../db.php';
include_once ('../../class.php'); 
$excel=@$_GET['excel'];
$search_1=$_GET['search_1'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];
$counter=0;$x=0; 
$source_num=0;
$table='interpreter';$org='';

//...................................................For Multiple Selection...................................\\
$counter=0; $arr = explode(',', $search_1);
$_words = implode("' OR orgName = '", $arr);
//......................................\\//\\//\\//\\//........................................................\\
//................................................................................................................
//...................................................................................................................................../////
$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
<div>';
$htmlTable .='<h2 align="center"><u>Company Wise Consolidate Report (Overall Interpreting)</u></h2>
<p align="right">Report Date : ' .$misc->sys_date(). '<br />
Date Range : [' .$misc->dated($search_2). '] to [' .$misc->dated($search_3). ']</p>
</div>
<p>Orgnaization(s) Selected</p>
<table class="aa" border="1" cellspacing="1" style="width:700px">
  <tr>
    <td valign="top">'.$search_1.'</td>
  </tr>
</table><br/><br/>';
// face to face starts here
$htmlTable .='<h3>Face to face Jobs Summary</h3>
<table>';
$htmlTable.='<tr>';
$htmlTable.='<th width="50%" style="background-color:#003399; color:#FFF;">Source Language</th>';
foreach($arr as $orgName){
$htmlTable.='<th width="50%" style="background-color:#003399; color:#FFF;">'.$orgName.'</th>';
    $counter++;}

$htmlTable.='</tr>';

$queryf="SELECT distinct (interpreter.source) FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv 					
	   			where (interpreter.orgName = '$_words') and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.assignDate between '$search_2' and '$search_3'";

  $resultf = mysqli_query($con, $queryf); 
  $resultf = mysqli_query($con, $queryf);
  
  while($rowf = mysqli_fetch_assoc($resultf))
  { 
    $langf=$rowf['source']; 

    $strTdsf='';
    $bRowZerosf=true;
    
    foreach($arr as $orgName)
    {
      $orgName=$orgName;
       
      $xf=$counter;
      $uf=0; 
      //only once
      while($xf>$uf)
      { 
        $query_innerf="SELECT count(interpreter.source) as source_num 
          FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv  INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where interpreter.orgName = '$orgName' and interpreter.source='$langf' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0";
        $result_innerf = mysqli_query($con, $query_innerf);
        //only once
        while($row_innerf = mysqli_fetch_assoc($result_innerf))
        {
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
    }
    if ($bRowZerosf==false)
    {
      $htmlTable.='<tr>
      <td>'.$langf.'</td>';

      $htmlTable.=''.$strTdsf.'
      </tr>';
    }

    $xf++;
}
 
 
 $htmlTable.='<tr>
<td><b>Total Cancelled Face to face</b></td>';
 foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_canf="select count(rec) cancelled from (SELECT interpreter.id as rec 
          FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv  INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where interpreter.orgName = '$orgName' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.orderCancelatoin =1) tbl";
	   $result_total_canf = mysqli_query($con, $query_total_canf);
	   while($row_total_canf = mysqli_fetch_assoc($result_total_canf)){
         $row_tot_canf=$row_total_canf["cancelled"];
             } 
            $htmlTable.='<td><b> '.$row_tot_canf.'</b></td>';
        } 
	 $htmlTable.='</tr>';
     
 
 
 $htmlTable.='<tr>
 
<td><b>Total Jobs Face to face</b></td>';
 foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_innerf="select count(source_num) source_num from (SELECT interpreter.source as source_num 
          FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv  INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where interpreter.orgName = '$orgName' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0) tbl";
	   $result_total_innerf = mysqli_query($con, $query_total_innerf);
	   while($row_total_innerf = mysqli_fetch_assoc($result_total_innerf)){
        $row_totf=$row_total_innerf["source_num"];
             }
            $htmlTable.='<td><b>'.$row_totf.'</b></td>';
        }
	 $htmlTable.='</tr>';
	 $htmlTable.='<tr>
 
<td><b>Total Cost Face to face</b></td>';
 foreach($arr as $orgName){$orgName=$orgName;
  $query_total_innerf="SELECT round(sum(interpreter.total_charges_comp),2) as total_charges_comp, round(sum(interpreter.total_charges_comp * interpreter.cur_vat),2) as total_charges_comp_vat,round(sum(interpreter.C_otherexpns),2) as other_expenses FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv 
						INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo  
	   					where interpreter.orgName = '$orgName' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0";
	   $result_total_innerf = mysqli_query($con, $query_total_innerf);
	   while($row_total_innerf = mysqli_fetch_assoc($result_total_innerf)){
    $net_valuef= $row_total_innerf["total_charges_comp_vat"] + $row_total_innerf["total_charges_comp"]+ $row_total_innerf["other_expenses"]; 
        } 
       $htmlTable.='<td><b>'. $misc->numberFormat_fun($net_valuef).'</b></td>';
        } 
	 $htmlTable.='</tr>';
$htmlTable.='</table>';
// face to face ends here

// Telephone starts
$htmlTable .='<h3>Telephone Jobs Summary</h3>
<table>';
$htmlTable.='<tr>';
$htmlTable.='<th width="50%" style="background-color:#003399; color:#FFF;">Source Language</th>';
foreach($arr as $orgName){
$htmlTable.='<th width="50%" style="background-color:#003399; color:#FFF;">'.$orgName.'</th>';
    $counter++;}

$htmlTable.='</tr>';

$queryt="SELECT distinct (telephone.source) FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv 					where (telephone.orgName = '$_words') and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.assignDate between '$search_2' and '$search_3'";

  $resultt = mysqli_query($con, $queryt); 
  $resultt = mysqli_query($con, $queryt);
  
  while($rowt = mysqli_fetch_assoc($resultt))
  { 
    $langt=$rowt['source']; 

    $strTdst='';
    $bRowZerost=true;
    
    foreach($arr as $orgName)
    {
      $orgName=$orgName;
       
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
    }
    if ($bRowZerost==false)
    {
      $htmlTable.='<tr>
      <td>'.$langt.'</td>';

      $htmlTable.=''.$strTdst.'
      </tr>';
    }

    $xt++;
}
 
 
 $htmlTable.='<tr>
<td><b>Total Cancelled Telephone</b></td>';
 foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_cant="select count(rec) cancelled from (SELECT telephone.id as rec 
          FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv  INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
	   			where telephone.orgName = '$orgName' and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.orderCancelatoin =1) tbl";
	   $result_total_cant = mysqli_query($con, $query_total_cant);
	   while($row_total_cant = mysqli_fetch_assoc($result_total_cant)){
         $row_tot_cant=$row_total_cant["cancelled"];
             } 
            $htmlTable.='<td><b> '.$row_tot_cant.'</b></td>';
        } 
	 $htmlTable.='</tr>';
     
 
 
 $htmlTable.='<tr>
 
<td><b>Total Jobs Telephone</b></td>';
 foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_innert="select count(source_num) source_num from (SELECT telephone.source as source_num 
          FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv  INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
	   			where telephone.orgName = '$orgName' and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0) tbl";
	   $result_total_innert = mysqli_query($con, $query_total_innert);
	   while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){
        $row_tott=$row_total_innert["source_num"];
             }
            $htmlTable.='<td><b>'.$row_tott.'</b></td>';
        }
	 $htmlTable.='</tr>';
	 $htmlTable.='<tr>
 
<td><b>Total Cost Telephone</b></td>';
 foreach($arr as $orgName){$orgName=$orgName;
  $query_total_innert="SELECT round(sum(telephone.total_charges_comp),2) as total_charges_comp, round(sum(telephone.total_charges_comp * telephone.cur_vat),2) as total_charges_comp_vat,0 as other_expenses FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv 
						INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo  
	   					where telephone.orgName = '$orgName' and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0";
	   $result_total_innert = mysqli_query($con, $query_total_innert);
	   while($row_total_innert = mysqli_fetch_assoc($result_total_innert)){
    $net_valuet=$row_total_innert["total_charges_comp_vat"] + $row_total_innert["total_charges_comp"]+ $row_total_innert["other_expenses"]; 
        } 
       $htmlTable.='<td><b>'. $misc->numberFormat_fun($net_valuet).'</b></td>';
        } 
	 $htmlTable.='</tr>';
$htmlTable.='</table>';
// telephone ends here

// translation starts
$htmlTable .='<h3>Translation Jobs Summary</h3>
<table>';
$htmlTable.='<tr>';
$htmlTable.='<th width="50%" style="background-color:#003399; color:#FFF;">Source Language</th>';
foreach($arr as $orgName){
$htmlTable.='<th width="50%" style="background-color:#003399; color:#FFF;">'.$orgName.'</th>';
    $counter++;}

$htmlTable.='</tr>';

$querytr="SELECT distinct (translation.source) FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv 						where (translation.orgName = '$_words') and translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.asignDate between '$search_2' and '$search_3'";

  $resulttr = mysqli_query($con, $querytr); 
  $resulttr = mysqli_query($con, $querytr);
  
  while($rowtr = mysqli_fetch_assoc($resulttr))
  { 
    $langtr=$rowtr['source']; 

    $strTdstr='';
    $bRowZerostr=true;
    
    foreach($arr as $orgName)
    {
      $orgName=$orgName;
       
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
    }
    if ($bRowZerostr==false)
    {
      $htmlTable.='<tr>
      <td>'.$langtr.'</td>';

      $htmlTable.=''.$strTdstr.'
      </tr>';
    }

    $xtr++;
}
 
 
 $htmlTable.='<tr>
<td><b>Total Cancelled Translation</b></td>';
 foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_cantr="select count(rec) cancelled from (SELECT translation.id as rec 
          FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv  INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
	   			where translation.orgName = '$orgName' and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.orderCancelatoin =1) tbl";
	   $result_total_cantr = mysqli_query($con, $query_total_cantr);
	   while($row_total_cantr = mysqli_fetch_assoc($result_total_cantr)){
         $row_tot_cantr=$row_total_cantr["cancelled"];
             } 
            $htmlTable.='<td><b> '.$row_tot_cantr.'</b></td>';
        } 
	 $htmlTable.='</tr>';
     
 
 
 $htmlTable.='<tr>
 
<td><b>Total Jobs Translation</b></td>';
 foreach($arr as $orgName){
 $orgName=$orgName;	 
  $query_total_innertr="select count(source_num) source_num from (SELECT translation.source as source_num 
          FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv  INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
	   			where translation.orgName = '$orgName' and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.order_cancel_flag=0) tbl";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);
	   while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){
        $row_tottr=$row_total_innertr["source_num"];
             }
            $htmlTable.='<td><b>'.$row_tottr.'</b></td>';
        }
	 $htmlTable.='</tr>';
	 $htmlTable.='<tr>
 
<td><b>Total Cost Translation</b></td>';
 foreach($arr as $orgName){$orgName=$orgName;
  $query_total_innertr="SELECT round(sum(translation.total_charges_comp),2) as total_charges_comp, round(sum(translation.total_charges_comp * translation.cur_vat),2) as total_charges_comp_vat,0 as other_expenses FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv 
						INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo  
	   					where translation.orgName = '$orgName' and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.order_cancel_flag=0";
	   $result_total_innertr = mysqli_query($con, $query_total_innertr);
	   while($row_total_innertr = mysqli_fetch_assoc($result_total_innertr)){
    $net_valuetr=$row_total_innertr["total_charges_comp_vat"] + $row_total_innertr["total_charges_comp"]+ $row_total_innertr["other_expenses"]; 
        } 
       $htmlTable.='<td><b>'. $misc->numberFormat_fun($net_valuetr).'</b></td>';
        } 
	 $htmlTable.='</tr>';
$htmlTable.='</table>';
// translation ends here

// Total calculation
$htmlTable.='<h3>Overall Jobs Summary</h3>
     <table class="altrowstable" id="alternatecolor">
  <tr>
    <th width="50%" style="background-color:#003399; color:#FFF;">All Records</th>';
  foreach($arr as $orgName){
    $htmlTable.='<th width="50%" style="background-color:#003399; color:#FFF;">'.$orgName.'</th>';
    $counter++;} 
  $htmlTable.='</tr>';
  
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
    $lang=$row['source']; 
    $strTds='';
    $bRowZeros=true;
    //for each lang: all orgname
    foreach($arr as $orgName)
    {
      $orgName=$orgName;
       
      $x=$counter;
      $u=0; 
      //only once
      while($x>$u)
      { 
        $query_inner="SELECT count(interpreter.source) as source_num 
          FROM interpreter 
	   inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	   inner join comp_reg on interpreter.orgName = comp_reg.abrv  INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where interpreter.orgName = '$orgName' and interpreter.source='$lang' and interpreter.assignDate between '$search_2' and '$search_3' and interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0
	   			UNION ALL 
	   			SELECT count(telephone.source) as source_num 
          FROM telephone 
	   inner join interpreter_reg on telephone.intrpName = interpreter_reg.id 
	   inner join comp_reg on telephone.orgName = comp_reg.abrv  INNER JOIN invoice ON telephone.invoiceNo=invoice.invoiceNo
	   			where telephone.orgName = '$orgName' and telephone.source='$lang' and telephone.assignDate between '$search_2' and '$search_3' and telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 
	   			UNION ALL 
	   			SELECT count(translation.source) as source_num 
          FROM translation 
	   inner join interpreter_reg on translation.intrpName = interpreter_reg.id 
	   inner join comp_reg on translation.orgName = comp_reg.abrv  INNER JOIN invoice ON translation.invoiceNo=invoice.invoiceNo
	   			where translation.orgName = '$orgName' and translation.source='$lang' and translation.asignDate between '$search_2' and '$search_3' and translation.deleted_flag = 0 and translation.order_cancel_flag=0
	   			";
        $result_inner = mysqli_query($con, $query_inner);
        //only once
        while($row_inner = mysqli_fetch_assoc($result_inner))
        {
            	 
            if ($row_inner['source_num']!=0){
              $bRowZeros=false;
            }
          $u++;
        }
        break;
      }
    } 

    // if ($bRowZeros==false)
    // {
    //   $htmlTable.='<tr>
    //   </tr>';
    // }
    
    $x++;
} 

 $htmlTable.='<tr>
<td><b>Total Cancelled Jobs</b></td>';
 foreach($arr as $orgName){
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
	   while($row_total_can = mysqli_fetch_assoc($result_total_can)){
         $row_tot_can=$row_total_can["cancelled"];
             } 
            $htmlTable.='<td><b>'.$row_tot_can.'</b></td>';
        } 
	 $htmlTable.='</tr>
 <tr>
 
<td><b>Total Jobs</b></td>';
 foreach($arr as $orgName){
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
	   while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){
         $row_tot=$row_total_inner["source_num"];
             } 
            $htmlTable.='<td><b>'.$row_tot.'</b></td>';
        }
	 $htmlTable.='</tr>
 <tr>
 
<td><b>Total Cost</b></td>';
foreach($arr as $orgName){$orgName=$orgName;
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
	   while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){ 
    $net_value=$net_value+ ($row_total_inner["total_charges_comp_vat"] + $row_total_inner["total_charges_comp"]+ $row_total_inner["other_expenses"]); 
        } 
       $htmlTable.='<td><b>'. $misc->numberFormat_fun($net_value).'</b></td>';
       $net_value=0; }
	 $htmlTable.='</tr>
     
</table>';

// total calculation ends here
list($a,$b)=explode('.',basename(__FILE__));
//$new_name=$a.'_'.implode('_',$arr);
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls"); 
echo $htmlTable;
?>
