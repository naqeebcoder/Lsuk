<?php if(isset($_POST['string']) && !empty($_POST['string'])){
    include 'db.php';
    include 'class.php';
    $string=$_POST['string'];
$query =
    "SELECT * from (SELECT interpreter.porder,comp_reg.po_req,'Interpreter' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter_reg.name,interpreter.source,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.submited, interpreter.aloct_by,interpreter.aloct_date,interpreter.dated,interpreter.hrsubmited,interpreter.comp_hrsubmited,interpreter.interp_hr_date, interpreter.comp_hr_date,interpreter.hoursWorkd,interpreter.C_hoursWorkd,interpreter.total_charges_comp,interpreter.cur_vat,interpreter.C_otherexpns, interpreter.credit_note,interpreter.C_admnchargs,interpreter.rAmount,interpreter.rDate,interpreter.sentemail,interpreter.printed,interpreter.printedby,interpreter.deleted_flag,interpreter.order_cancel_flag,interpreter.nameRef,interpreter.orgRef,interpreter.invoiceNo,interpreter.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv AND interpreter.multInvoicNo=0 AND interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.commit=1 and (interpreter.total_charges_comp > interpreter.rAmount or interpreter.total_charges_comp =0) and (interpreter.orgRef like '%$string%' OR interpreter.porder like '%$string%' OR interpreter.nameRef like '%$string%' OR interpreter.invoiceNo like '%$string%')
               UNION ALL SELECT telephone.porder,comp_reg.po_req,'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,interpreter_reg.name,telephone.source,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.submited, telephone.aloct_by,telephone.aloct_date,telephone.dated,telephone.hrsubmited,telephone.comp_hrsubmited,telephone.interp_hr_date,telephone.comp_hr_date, telephone.hoursWorkd,telephone.C_hoursWorkd,telephone.total_charges_comp,telephone.cur_vat,0 as C_otherexpns,telephone.credit_note,telephone.C_admnchargs, telephone.rAmount,telephone.rDate,telephone.sentemail,telephone.printed,telephone.printedby,telephone.deleted_flag,telephone.order_cancel_flag,telephone.nameRef,telephone.orgRef,telephone.invoiceNo,telephone.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv AND telephone.multInvoicNo=0 AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.commit=1 and (telephone.total_charges_comp > telephone.rAmount or telephone.total_charges_comp =0) and (telephone.orgRef like '%$string%' OR telephone.porder like '%$string%' OR telephone.nameRef like '%$string%' OR telephone.invoiceNo like '%$string%')
               UNION ALL SELECT translation.porder,comp_reg.po_req,'Translation' as type,translation.id,translation.intrpName,translation.orgName,interpreter_reg.name,translation.source,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.submited, translation.aloct_by,translation.aloct_date,translation.dated,translation.hrsubmited,translation.comp_hrsubmited,translation.interp_hr_date,translation.comp_hr_date, translation.numberUnit as hoursWorkd,translation.C_numberUnit as C_hoursWorkd,translation.total_charges_comp,translation.cur_vat,0 as C_otherexpns,translation.credit_note,translation.C_admnchargs, translation.rAmount,translation.rDate,translation.sentemail,translation.printed,translation.printedby,translation.deleted_flag,translation.order_cancel_flag,translation.nameRef,translation.orgRef,translation.invoiceNo,translation.commit,comp_reg.email,comp_reg.abrv as comp_abrv FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv AND translation.multInvoicNo=0 AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.commit=1 and (translation.total_charges_comp > translation.rAmount or translation.total_charges_comp =0) and (translation.orgRef like '%$string%' OR translation.porder like '%$string%' OR translation.nameRef like '%$string%' OR translation.invoiceNo like '%$string%')) as grp ORDER BY CONCAT(assignDate,' ',assignTime)"; ?>
<table class="tablesorter table table-bordered tbl_data" cellspacing="0" cellpadding="0">
			    <thead class="bg-info">
				<tr>
                    <td align="center"><b><?php if (!empty($type)) { echo $type.' pending jobs list'; }else{ echo 'All pending jobs list';}?></b></td>
   				</tr> 
			</thead>
			<tbody>
        <?php $result = mysqli_query($con, $query); 
                if(mysqli_num_rows($result)==0){
                    echo '<tr>
            		  <td><h4 class="text-danger text-center">Sorry ! There are no records.</h4></td></tr>';
                }else{ 
while ($row = mysqli_fetch_array($result)) {
    $counter++;?>
            		<tr>
            		  <td>
            		      <div class="col-sm-4 col-xs-12">
                            <ul class="w3-ul">
                              <li><span class="w3-large w3-right"><?php echo '<span class="label label-default w3-large w3-right">'.$row['source'].'</span>'; ?></span><?php echo '<span class="label label-default w3-large w3-right">'.$row['hoursWorkd'] == 0?'<span class="text-danger"><b>'.$row['name'].'</b></span>':'<span><b>'.$row['name'].'</b></span>'; ?></li>
                              <li><i class="fa fa-question-circle" title="Allocated By"></i><span class="w3-right"><?php echo ucwords($row['aloct_by']) . ' (' . $misc->dated($row['aloct_date']) . ')';?></span></li>
                              <li><i class="fa fa-question-circle" title="Interpreter Hours Submission"></i><span class="w3-right"><?php echo $row['hrsubmited'] . ' (' . $misc->dated($row['interp_hr_date']) . ')';?></span></li>
                              <li><i class="fa fa-question-circle" title="Company Hours Submission"></i><span class="w3-right"><?php echo $row['comp_hrsubmited'] . ' (' . $misc->dated($row['comp_hr_date']) . ')';?></span></li>
                              </ul>
                        </div>
            		      <div class="col-sm-4 col-xs-12">
                            <ul class="w3-ul">
                              <li><?php echo '<span class="label label-default w3-large w3-right">'.$row['C_hoursWorkd'] == 0?'<span class="label w3-large  w3-red">'.$row['orgName'].'</span><span class="w3-large w3-right text-right" style="font-weight:bold;margin-top:-10px;font-size:14px!important;">'.$row['assignDate'].'<br> '.$row['assignTime'].'</span>':'<span class="label w3-large  w3-blue">'.$row['orgName'].'</span><span class="w3-large w3-right text-right" style="font-weight:bold;margin-top:-10px;font-size:14px;">'.$row['assignDate'].' '.$row['assignTime'].'</span>'; ?></li>
                              <li><i class="fa fa-question-circle" title="Contact Name"></i><span class="w3-right"><?php echo $row['orgContact'];?></span></li>
                              <li><i class="fa fa-question-circle" title="Entered By"></i><span class="w3-right"><?php echo $row['submited'] . ' (' . $misc->dated($row['dated']) . ')';?></span></li>
                              <li>Printed By <span class="w3-right"><?php echo ucwords($row['printedby'])?:'Nil';?></span></li>
                            </ul>
                        </div>
            		      <div class="col-sm-4 col-xs-12">
                            <ul class="w3-ul">
                            </ul>
                        </div>

					<?php
//##gotcreditnote
    if($row['type']=='Interpreter'){
        $totalforvat = $row['total_charges_comp'];
        $vatpay = $totalforvat * $row['cur_vat'];
        $totinvnow = $totalforvat + $vatpay + $row['C_otherexpns'];
    }else if($row['type']=='Telephone'){
        $totalforvat=$row['total_charges_comp'];
		$vatpay=$totalforvat*$row['cur_vat'];
		$totinvnow=$totalforvat+$vatpay;
    }else{
        $totalforvat=$row['total_charges_comp'];
		$vatpay=$totalforvat*$row['cur_vat'];
		$totinvnow=$totalforvat+$vatpay;
    }
    
    /*$totinvnow2=$row['total_charges_comp']* $row["cur_vat"] +
    $row['total_charges_comp'] + $row['C_otherexpns'] + $row['C_admnchargs'];*/

    $gotcreditnote = false;
    if (isset($row['credit_note']) && $row['credit_note'] != "") {
        $totinvnow = 0;
        $gotcreditnote = true;
    }

    ?>
				
            		      <div class="col-sm-4 col-xs-12">
                            <ul class="w3-ul">
                              <li><i class="fa fa-question-circle" title="Emailed By"></i><span class="w3-right"><?php echo $row['email'];?></span></li>
                              <li>Invoice Amount<span class="w3-large w3-right"><?php echo $misc->numberFormat_fun($totinvnow);?></span></li>
                              <li>Received Amount<span class="w3-large w3-right"><?php echo $row['rAmount']!=0?$misc->numberFormat_fun($row['rAmount']):0;?></span></li>
                              <li>Dated<span class="w3-right"><?php echo $misc->dated($row['rDate']);?></span></li>
                            </ul>
                        </div>
                        <style>
    .action_buttons .w3-button{padding: 7px 11px;}
    .action_buttons .fa{font-size: 20px;} .w3-ul li {border-bottom: none;}
</style>
<div class="col-sm-12 text-center action_buttons">
    <a href="javascript:void(0)" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>', 'title', 1000, 1000);">
    				  <input type="image" src="images/icn_new_article.png" title="View Order">
    				  </a>

    				  <a href="javascript:void(0)" onClick="popupwindow('receive_amount.php?row_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']) ?>','title', 800,450);">
    				    <input type="image" src="images/Cash.png" title="Payment Received">
   				      </a>
   				      <?php if($row['type']=='Interpreter'){$inv_page='invoice.php';$comp_earning='comp_earning.php';$credit_page='credit_interp.php';}else if($row['type']=='Telephone'){$inv_page='telep_invoice.php';$comp_earning='comp_earning_telep.php';$credit_page='credit_telep.php';}else{$inv_page='trans_invoice.php';$comp_earning='comp_earning_trans.php';$credit_page='credit_trans.php';} ?>
<a href="javascript:void(0)" onClick="popupwindow('<?php echo $inv_page; ?>?invoice_id=<?php echo $row['id']; ?>','title', 1000, 1000);"><input type="image" src="images/invoice.png" title="Invoice"></a>
<?php if ($row['sentemail'] == 1) {
	  ?>
		<a href="javascript:void(0)" onClick="popupwindow('<?php echo $inv_page; ?>?invoice_id=<?php echo $row['id']; ?>','title', 1000, 1000);"><input type="image" src="images/email_sent_icon.jpg" title="Email Sent"></a>
<?php }else{ ?>
<a href="javascript:void(0)" onClick="popupwindow('<?php echo $inv_page; ?>?invoice_id=<?php echo $row['id']; ?>','title', 1000, 1000);"><input type="image" src="images/email_icon.jpg" title="Send Email"></a>
<?php } ?>



<?php
if ($gotcreditnote) {
        ?>
    <a href="javascript:void(0)" onClick="popupwindow('<?php echo $credit_page; ?>?invoice_id=<?php echo $row['id']; ?>','title', 1000, 1000);">
		<input style="background-color:red;" type="image" src="images/icn_categories.png" title="Credit Note">
	</a>
	<?php
} else {
        ?>
	<input type="image" src="images/icn_categories.png" title="No Credit Note">
	<?php
}
    ?>

     <a href="javascript:void(0)" onClick="popupwindow('un_commit.php?com_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>','title', 550,350);">
    				  <input type="image" src="images/icn_jump_back.png" title="Un-commit payment">
    				  </a>

                      <a href="javascript:void(0)" onClick="popupwindow('purch_update.php?purch_id=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>&orgName=<?php echo $row['orgName']; ?>','title', 550,450);"><input type="image" src="images/icn_tags.png" title="Update Purchase Order #"></a>
                      <a href="javascript:void(0)" onClick="popupwindow('<?php echo $comp_earning; ?>?view_id=<?php echo $row['id']; ?>','title', 900, 400);"><input type="image" src="images/earning.png" title="Earning"></a>

					  <a href="javascript:void(0)" class="clsActionJobNote" onclick="popupwindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo strtolower($row['type']); ?>
 	&orgName=<?php echo $row['orgName']; ?>','title', 1000, 1000);">
	 <input data-jobid="<?php echo $row['id']; ?>" type="image" src="images/post_message.png" title="Job Note"
	 	width="17" height="17"></a>
	 	<a href="javascript:void(0)"></a><?php if($row['type']=='Interpreter'){ echo "<span class='label label-success lbl'>".$row['type']."</span>"; }else if($row['type']=='Telephone'){ echo "<span class='label label-info lbl'>".$row['type']."</span>"; }else{ echo "<span class='label label-warning lbl'>".$row['type']."</span>"; } ?></a>
</div>
</td>
   			</tr>
                <?php }?>
                </tbody></table><br>
		  </div>
		  <?php } 
} ?>