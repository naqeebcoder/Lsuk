<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){session_start();}
include 'db.php';
include 'class.php';
$fdate= @$_GET['fdate'];
$tdate= @$_GET['tdate'];?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Assign Interpreter</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <!--link rel="stylesheet" type="text/css" href="css/default.css"/-->
</head>
<body>    
	
	<div class="container">
        <form action="" method="post" class="register">
        
          <h1>Total Invoices Registered Between Two Dates</h1>
          <fieldset class="row1">
            <legend>Select Dates
          </legend>
		  
		 <script>
			function myFunction() {
				var x = document.getElementById("fdate").value;var y = document.getElementById("tdate").value;
				window.location.href = "invoice_query.php?fdate=" + x + "&tdate=" + y ;
			}
		</script>
		
		<div class="row">
			<div class="col-sm-3 form-group">
				<label>From Date *</label>
				<input type="date" name="fdate" id="fdate" class="form-control" />
			</div>
			<div class="col-sm-3 form-group">
				<label>To Date *</label>
				<input type="date" name="tdate" id="tdate"  class="form-control" onchange="myFunction()" />
			</div>
			<p style="display:none" id="demo"></p>
			<br /><br />
			
		</div>
		
	</div>
<?php
	if(!empty($fdate) && !empty($tdate)){
	$query="SELECT 
		count((CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='Received') THEN interpreter.id END)) as interp_paid_i,
		(SELECT 
			count( (CASE WHEN (interpreter.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='') THEN interpreter.id END))
			FROM interpreter inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id where interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0  and assignDate between '$fdate' and '$tdate'  
			  AND (
				(round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0 )
					OR interpreter.multInv_flag = 1
				)
			AND (
					(interpreter.multInv_flag = 0 AND interpreter.invoiceNo<>'') 
					OR  (interpreter.multInv_flag = 1 ) 
				)
		) as pend_i
	FROM interpreter inner join interpreter_reg on interpreter.intrpName = interpreter_reg.id 
	WHERE interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 
	 and assignDate between '$fdate' and '$tdate'
	 AND (
			(round(interpreter.rAmount, 2) >= round((interpreter.total_charges_comp + (interpreter.total_charges_comp * interpreter.cur_vat)), 2) and interpreter.total_charges_comp >0 AND interpreter.commit=1) 
			OR interpreter.multInv_flag = 1
		)
	AND (
			(interpreter.multInv_flag = 0 AND interpreter.invoiceNo<>'') 
			OR  (interpreter.multInv_flag = 1 ) 
		)";			
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result)){
		$paid_interp = $row['interp_paid_i'];
		$pending_interp = $row['pend_i'];
		$total_interp = ($row['interp_paid_i']+$row['pend_i']);
	}

	$query="SELECT 
		count((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='Received') THEN telephone.id END)) as telep_paid_i,
		(SELECT 
			count((CASE WHEN (telephone.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='') THEN telephone.id END))
			FROM telephone inner join interpreter_reg on telephone.intrpName = interpreter_reg.id where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and assignDate between '$fdate' and '$tdate'
			AND (
				(round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) and telephone.total_charges_comp > 0 )
				OR telephone.multInv_flag = 1
			)
			AND (
				(telephone.multInv_flag = 0 AND telephone.invoiceNo<>'') 
				OR  (telephone.multInv_flag = 1 ) 
			)
		) as pend_i
	FROM telephone inner join interpreter_reg on telephone.intrpName = interpreter_reg.id where telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and assignDate between '$fdate' and '$tdate'
	 AND (
			(round(telephone.rAmount, 2) >= round((telephone.total_charges_comp + (telephone.total_charges_comp * telephone.cur_vat)), 2) and telephone.total_charges_comp > 0 AND telephone.commit=1) 
			OR telephone.multInv_flag = 1
		)
	AND (
			(telephone.multInv_flag = 0 AND telephone.invoiceNo<>'') 
			OR  (telephone.multInv_flag = 1 ) 
		)";		
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result)){
		$paid_telep = $row['telep_paid_i'];
		$pending_telep = $row['pend_i'];
		$total_telep = ($row['telep_paid_i']+$row['pend_i']);
	} 

	$query="SELECT 
		count(
			(CASE WHEN (translation.multInv_flag=0) OR 
			(SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='Received') THEN translation.id END)
		) as trans_paid_i,
		(
			SELECT count((CASE WHEN (translation.multInv_flag=0) OR (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='') THEN translation.id END))
			FROM translation inner join interpreter_reg on translation.intrpName = interpreter_reg.id where translation.deleted_flag = 0 and translation.order_cancel_flag=0 and asignDate between '$fdate' and '$tdate'
			AND (
				(round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp >0 )
				OR translation.multInv_flag = 1
			)
			AND (
				(translation.multInv_flag = 0 AND translation.invoiceNo<>'') 
				OR  (translation.multInv_flag = 1 ) 
			)
		) as pend_i
	FROM translation 
	INNER JOIN interpreter_reg ON translation.intrpName = interpreter_reg.id 
	WHERE translation.deleted_flag = 0 AND translation.order_cancel_flag=0 AND asignDate BETWEEN '$fdate' and '$tdate'
	  AND (
			(round(translation.rAmount, 2) >= round((translation.total_charges_comp + (translation.total_charges_comp * translation.cur_vat)), 2) and translation.total_charges_comp >0 AND translation.commit=1) 
			OR translation.multInv_flag = 1
		)
	AND (
			(translation.multInv_flag = 0 AND translation.invoiceNo<>'') 
			OR  (translation.multInv_flag = 1 ) 
		)";
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result)){
		$paid_trans = $row['trans_paid_i'];
		$pending_trans = $row['pend_i'];
		$total_trans = ($row['trans_paid_i']+$row['pend_i']);
	}
	
?>
        <div class="container">
            <legend>Total Record</legend>
            <table width="100%" border="1" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Category</th>
						<th>Paid</th>
						<th>Pending</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th width="200" align="left">Interpreter</th>
						<td width="250"><?php echo $paid_interp; ?></td>
						<td width="250"><?php echo $pending_interp; ?></td>
						<td width="250"><?php echo $total_interp; ?></td>
					</tr>
					<tr>
						<th width="200" align="left">Telephone Interpreter</th>
						<td width="250"><?php echo $paid_telep; ?></td>
						<td width="250"><?php echo $pending_telep; ?></td>
						<td width="250"><?php echo $total_telep; ?></td>
					</tr>
					<tr>
						<th width="200" align="left">Translation Interpreter</th>
						<td width="250"><?php echo $paid_trans; ?></td>
						<td width="250"><?php echo $pending_trans; ?></td>
						<td width="250"><?php echo $total_trans; ?></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
					<th width="200" align="left">Total Jobs </th>
					<th width="250"><?php echo $paid_interp+$paid_telep+$paid_trans; ?></th>
					<th width="250"><?php echo $pending_interp+$pending_telep+$pending_trans; ?></th>
					<th width="250"><?php echo $total_interp+$total_telep+$total_trans; ?></th>
					</tr>
				</tfoot>
            </table>
		</div>
		
          <?php } ?>
        </form>
</body>
</html>




