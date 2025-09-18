<?php include 'db.php';
include 'class.php';
include 'inc_functions.php';

$id = mysqli_real_escape_string($con, $_GET['id']);
$payment_rec_type = mysqli_real_escape_string($con, $_GET['prt']); // Full OR Partial

$allowed_type_idz = "74,86,177";

//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
	$get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
	if (empty($get_page_access)) {
		die("<center><h2 class='text-center text-danger'>You do not have access to <u>Add Payment</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
	}
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Receive Manual Invoice Amount</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/bootstrap.css">

	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/fontawesome.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<style>
		.b {
			color: #fff;
		}

		a:link,
		a:visited {
			color: #337ab7;
		}

		.text-white {
			color: #fff !important;
		}
	</style>

</head>

<body>
	<?php

	if ($payment_rec_type == 'full') {
		$query = "SELECT i.*, c.company_name 
			FROM income_invoices i
			LEFT JOIN income_company c ON c.id = i.company_id 
			WHERE i.id = " . $id;
	} else {
		$query = "SELECT i.*, c.company_name,
			(SELECT SUM(total_amount) FROM paid_income_invoices WHERE is_partial_payment = 1 AND income_invoice_id = $id AND is_deleted = 0) as total_received_partial_payments
			FROM income_invoices i
			LEFT JOIN income_company c ON c.id = i.company_id 
			WHERE i.id = " . $id;
	}

	$result = mysqli_query($con, $query);
	$row = mysqli_fetch_array($result);

	$final_sum = $remAmount = $total_amount = $row['total_amount'];

	// calculating remaining amount for partial payments
	if ($payment_rec_type == 'partial') {
		$partial_received_amount = ($row['total_received_partial_payments']) ? $row['total_received_partial_payments'] : 0;
		$remAmount = ($total_amount - $partial_received_amount);
	}

	$due_date = $row['due_date'];

	if (isset($_POST['submit'])) {

		$rAmount = mysqli_real_escape_string($con, $_POST['rAmount']);
		$rDate = mysqli_real_escape_string($con, $_POST['rDate']);

		$payment_type = mysqli_real_escape_string($con, $_POST['payment_type']);
		$payment_method_id = mysqli_real_escape_string($con, $_POST['payment_through']);

		if ($payment_rec_type == 'partial') {
			$title = mysqli_real_escape_string($con, $_POST['title']);
		}

		if ($payment_rec_type == 'full') {

			if (!empty($_POST['rAmount']) && $_POST['rAmount'] > 0 && is_numeric($_POST['rAmount'])) {

				if (empty($payment_type)) {
					$msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>You must fill atleast one payment method !</b></div>';
				} else {
					if (bccomp($rAmount, $final_sum, 2) == 0) {

						//checking existing record
						$check_paid_record = $acttObj->read_specific("count(id) as count", "paid_income_invoices", "is_partial_payment = 0 AND income_invoice_id = '" . $id . "'");

						if ($check_paid_record['count'] < 1) {

							// Insertion in tbl paid_income_invoices
							$insert_data = array(
								'is_partial_payment' => 0,
								'income_invoice_id ' => $id,
								'total_amount' => $rAmount,
								'dated' => $rDate,
								'payment_type' => $payment_type,
								'payment_method' => $payment_method_id,
								'is_deleted' => 0,
								"posted_by" => $_SESSION['userId'],
								"posted_on" => date('Y-m-d H:i:s')
							);

							$res = $acttObj->insert('paid_income_invoices', $insert_data, false);

							if ($payment_type == 'cash') {
								$voucher_label = 'CPV';
								$is_bank = '0';
							} else {
								$voucher_label = 'BPV';
								$is_bank = '1';
							}

							// Getting New Voucher Counter
							$voucher_counter = getNextVoucherCount($voucher_label);

							// Updating the new Voucher Counter
							updateVoucherCounter($voucher_label, $voucher_counter);

							$voucher = $voucher_label . '-' . $voucher_counter;

							// updating main table
							$data = array(
								//"voucher" => $voucher, // this will change according to payment type from JV to BPV OR CPV
								"received_amount" => $rAmount,
								"payment_status" => "full_paid",
								"is_paid" => 1,
								"paid_by" => $_SESSION['userId'],
								"paid_on" => date('Y-m-d H:i:s')
							);

							$parameters = ['id' => $id];
							$result = $acttObj->update('income_invoices', $data, $parameters);

							$acttObj->insert('daily_logs', ['action_id' => 21, 'user_id' => $_SESSION['userId'], 'details' => "Manual Invoice No: " . $row['voucher_no']]);


							/* Insertion Query to Accounts: Receivable & account_journal_ledger Table
								- account_receivable : As Credit (balance - rAmount)
								- account_journal_ledger : As Debit (balance + rAmount)
							*/

							$description = "[Manual Invoice] Company: " . $row['company_name'] . ", Invoice No: " . $row['voucher_no'];
							$credit_amount = $rAmount;
							$current_date = date("Y-m-d");

							$parameters = " invoice_no = '" . $row['voucher_no'] . "' AND dated = '" . $current_date . "' AND company = '" . $row['company_name'] . "' AND credit = '" . $credit_amount . "'";

							// Checking if record already exists
							$chk_exist = 0; //isReceivableRecordExists($parameters);

							if ($chk_exist < 1 && $credit_amount > 0) {

								// getting balance amount
								$res = getCurrentBalances($con);

								// Insertion in tbl account_receivable
								$insert_data = array(
									'invoice_no' => $row['voucher_no'],
									'voucher' => $voucher,
									'dated' => $current_date,
									'company' => $row['company_name'],
									'description' => $description,
									'credit' => $credit_amount,
									'balance' => ($res['recv_balance'] - $credit_amount),
									'posted_by' => $_SESSION['userId'],
									'posted_on' => date('Y-m-d H:i:s'),
									'tbl' => 'income_invoices'
								);

								$re_result = insertAccountReceivable($insert_data);
								//$voucher = $re_result['voucher'];
								$new_voucher_id = $re_result['new_voucher_id'];

								// Insertion in tbl account_journal_ledger
								$insert_data_journal = array(
									'is_receivable' => 1,
									'receivable_payable_id' => $new_voucher_id,
									'voucher' => $voucher,
									'invoice_no' => $row['voucher_no'],
									'company' => $row['company_name'],
									'description' => $description,
									'is_bank' => $is_bank,
									'payment_type' => $payment_type,
									'account_id' => $payment_method_id,
									'dated' => $current_date,
									'debit' => $credit_amount,
									'balance' => ($res['journal_balance'] + $credit_amount),
									'posted_by' => $_SESSION['userId'],
									'posted_on' => date('Y-m-d H:i:s'),
									'tbl' => 'income_invoices'
								);

								insertJournalLedger($insert_data_journal);
							} // end if record not exists

						} // end if record not exists in paid_income_invoices

	?>
						<script>
							alert('Amount Successfully updated for this job. Thank you!');
							window.close();
							window.onunload = refreshParent;

							function refreshParent() {
								window.opener.location.reload();
							}
						</script>
					<?php
					} else {
					?>
						<script>
							alert('Failed: Paid Amount did not matched the invoice amount');
						</script>
					<?php
					}
				}
			} else {
				$msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Fill up valid amount value & greater then 0.</b></div>';
			}
		} else { // if payment receive type = partial

			if (!empty($_POST['rAmount']) && $_POST['rAmount'] > 0 && is_numeric($_POST['rAmount'])) {

				if (empty($payment_type)) {
					$msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>You must fill atleast one payment method !</b></div>';
				} else {

					if (bccomp($rAmount, $remAmount, 2) <= 0) {

						// Insertion in tbl paid_income_invoices
						$insert_data = array(
							'is_partial_payment' => 1,
							'income_invoice_id' => $id,
							'title' => $title,
							'total_amount' => $rAmount,
							'dated' => $rDate,
							'payment_type' => $payment_type,
							'payment_method' => $payment_method_id,
							'is_deleted' => 0,
							'posted_by' => $_SESSION['userId'],
							'posted_on' => date('Y-m-d H:i:s')
						);

						$res = $acttObj->insert('paid_income_invoices', $insert_data, false);

						if ($payment_type == 'cash') {
							$voucher_label = 'CPV';
							$is_bank = '0';
						} else {
							$voucher_label = 'BPV';
							$is_bank = '1';
						}

						// Getting New Voucher Counter
						$voucher_counter = getNextVoucherCount($voucher_label);

						// Updating the new Voucher Counter
						updateVoucherCounter($voucher_label, $voucher_counter);

						$voucher = $voucher_label . '-' . $voucher_counter;

						// getting all records from partial table
						$sum_of_total_partial_received_amount = $acttObj->read_specific("SUM(total_amount) as total_partial_received_amount", "paid_income_invoices", "is_partial_payment = 1 AND income_invoice_id = '" . $id . "' AND is_deleted = 0")['total_partial_received_amount'];

						if ($sum_of_total_partial_received_amount >= $row['total_amount']) { // if all dues cleared, update main tbl
							$data = array(
								//"voucher" => $voucher, 
								'payment_status' => 'full_partial',
								'is_paid' => 1,
								'paid_by' => $_SESSION['userId'],
								'paid_on' => date('Y-m-d H:i:s')
							);
						} else {
							$data = array(
								//"voucher" => $voucher,
								'payment_status' => 'partial',
								'received_amount' => ($row['received_amount'] + $rAmount),
							);
						}
						$parameters = ['id' => $id];
						$result = $acttObj->update('income_invoices', $data, $parameters);

						//$array_types = array("income_invoices" => "manual_invoice_paid");

						$acttObj->insert('daily_logs', ['action_id' => 50, 'user_id' => $_SESSION['userId'], 'details' => "Manual Invoice No: " . $row['voucher_no']]);


						/* Insertion Query to Accounts: Receivable & account_journal_ledger Table
							- account_receivable : As Credit (balance - rAmount)
							- account_journal_ledger : As Debit (balance + rAmount)
						*/

						$description = "[Partial Payment][Manual Invoice] Company: " . $row['company_name'] . ", Invoice No: " . $row['voucher_no'];
						$credit_amount = $rAmount;
						$current_date = date("Y-m-d");

						// Checking if record already exists
						$parameters = " invoice_no = '" . $row['voucher_no'] . "' AND dated = '" . $current_date . "' AND company = '" . $row['company_name'] . "' AND credit = '" . $credit_amount . "'";

						// Checking if record already exists
						$chk_exist = 0; //isReceivableRecordExists($parameters);

						if ($chk_exist < 1 && $credit_amount > 0) {

							// getting balance amount
							$res = getCurrentBalances($con);

							// Insertion in tbl account_receivable
							$insert_data = array(
								'invoice_no' => $row['voucher_no'],
								'voucher' => $voucher,
								'dated' => $current_date,
								'company' => $row['company_name'],
								'description' => $description,
								'credit' => $credit_amount,
								'balance' => ($res['recv_balance'] - $credit_amount),
								'posted_by' => $_SESSION['userId'],
								'posted_on' => date('Y-m-d H:i:s'),
								'tbl' => 'income_invoices'
							);

							$re_result = insertAccountReceivable($insert_data);
							//$voucher = $re_result['voucher'];
							$new_voucher_id = $re_result['new_voucher_id'];

							// Insertion in tbl account_journal_ledger
							$insert_data_journal = array(
								'is_receivable' => 1,
								'receivable_payable_id' => $new_voucher_id,
								'voucher' => $voucher,
								'invoice_no' => $row['voucher_no'],
								'company' => $row['company_name'],
								'description' => $description,
								'is_bank' => $is_bank,
								'payment_type' => $payment_type,
								'account_id' => $payment_method_id,
								'dated' => $current_date,
								'debit' => $credit_amount,
								'balance' => ($res['journal_balance'] + $credit_amount),
								'posted_by' => $_SESSION['userId'],
								'posted_on' => date('Y-m-d H:i:s'),
								'tbl' => 'income_invoices'
							);

							insertJournalLedger($insert_data_journal);
						} // end if record not exists

					?>
						<script>
							alert('Amount Successfully updated for this job. Thank you!');
							window.close();
							window.onunload = refreshParent;

							function refreshParent() {
								window.opener.location.reload();
							}
						</script>
					<?php
					} else {
					?>
						<script>
							alert('Failed: Paid Amount did not matched the invoice amount');
						</script>
			<?php
					}
				}
			} else {
				$msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Fill up valid amount value & greater then 0.</b></div>';
			}
		} // end if partial payment
	}

	/** ============ Deletion ================= **/

	// Delete Partial Payment
	if (isset($_GET['del']) && isset($_GET['id'])) {

		$query = "SELECT i.id as main_id, i.voucher_no, i.voucher, i.p_order, i.description,
			pi.id as partial_id, pi.dated as partial_dated, pi.total_amount as partial_amount, pi.payment_type, pi.payment_method,
			c.company_name
			FROM income_invoices i
			LEFT JOIN paid_income_invoices pi ON pi.income_invoice_id = i.id
			LEFT JOIN income_company c ON c.id = i.company_id 
			WHERE pi.id = " . $id;

		$result = mysqli_query($con, $query);
		$row = mysqli_fetch_array($result);

		if (count($row) > 0) {

			/* Insertion Query to Accounts: Receivable & account_journal_ledger Table
				- account_receivable : As Debit (balance + credit)
				- account_journal_ledger : As Credit (balance - credit) -- No Reversal Entry, Instead Update the record status to Deleted
			*/

			$description = "[Deleted][Manual Invoice] Company: " . $row['company_name'] . ", Invoice No: " . $row['voucher_no'];

			// Checking if record already exists
			// $chk_exist = $acttObj->read_specific_c("count(id) as counter", "account_receivable", " invoice_no = '" . $row['voucher_no'] . "' AND dated = '" . $row['partial_dated'] . "' AND company = '" . $row['company_name'] . "' AND debit = '" . $row['partial_amount'] . "'")['counter'];

			// if ($chk_exist < 1) {

			$credit_amount = $row['partial_amount'];

			if ($credit_amount > 0) {

				// getting balance amount
				$res = getCurrentBalances($con);

				if ($row['payment_type'] == 'cash') {
					$voucher_label = 'CPV';
					$is_bank = '0';
				} else {
					$voucher_label = 'BPV';
					$is_bank = '1';
				}

				// Getting New Voucher Counter
				$voucher_counter = getNextVoucherCount($voucher_label);

				// Updating the new Voucher Counter
				updateVoucherCounter($voucher_label, $voucher_counter);

				$voucher = $voucher_label . '-' . $voucher_counter;

				// Insertion in tbl account_receivable
				$insert_data = array(
					'invoice_no' => $row['voucher_no'],
					'voucher' => $voucher,
					'dated' => date('Y-m-d'),
					'company' => $row['company_name'],
					'description' => $description,
					'debit' => $credit_amount,
					'balance' => ($res['recv_balance'] + $credit_amount),
					'posted_by' => $_SESSION['userId'],
					'posted_on' => date('Y-m-d H:i:s'),
					'tbl' => 'income_invoices'
				);

				$re_result = insertAccountReceivable($insert_data);
				//$voucher = $re_result['voucher'];
				$new_voucher_id = $re_result['new_voucher_id'];

				// Getting id of journal table for the specific record to update the status
				$select_journal_rec = $acttObj->read_specific(
					"id",
					"account_journal_ledger",
					"is_receivable = 1 AND debit = '" . $credit_amount . "' 
					AND invoice_no = '" . $row['voucher_no'] . "' 
					AND dated = '" . $row['partial_dated'] . "' 
					AND payment_type = '" . $row['payment_type'] . "' 
					AND account_id = '" . $row['payment_method'] . "' 
					AND status = 'paid'"
				);

				// it will update the journal record for future, as we are not inserting any reversal record for specific parital rAmount
				updateJournalLedgerSingleRecordStatus('deleted', 'is_receivable = 1 AND id = ' . $select_journal_rec['id']);

				// Insertion in tbl account_journal_ledger --- No reversal entry in journal for paid amount.
				$insert_data_journal = array(
					'is_receivable' => 1,
					'receivable_payable_id' => $new_voucher_id,
					'voucher' => $voucher,
					'invoice_no' => $row['voucher_no'],
					'company' => $row['company_name'],
					'description' => $description,
					'is_bank' => $is_bank,
					'payment_type' => $row['payment_type'],
					'account_id' => $row['payment_method'],
					'dated' => date('Y-m-d'),
					'credit' => $credit_amount,
					'balance' => ($res['journal_balance'] - $credit_amount),
					'posted_by' => $_SESSION['userId'],
					'posted_on' => date('Y-m-d H:i:s'),
					'tbl' => 'income_invoices'
				);

				insertJournalLedger($insert_data_journal);
			}

			// this will trash the current invoice paid records if any (tbl: paid_income_invoices) 
			//$delete_invoice_payments = mysqli_query($con, "UPDATE paid_income_invoices SET is_deleted = 1 WHERE is_deleted = 0 AND income_invoice_id = " . $id);

			// Delete Main Partial Record
			$result = mysqli_query($con, "UPDATE paid_income_invoices SET is_deleted = 1 WHERE is_partial_payment = 1 AND id = " . $id);

			// Deduction of Received Amount from main table
			mysqli_query($con, "UPDATE income_invoices SET received_amount = (received_amount-$credit_amount) WHERE id = " . $row['main_id']);

			$acttObj->insert('daily_logs', ['action_id' => 51, 'user_id' => $_SESSION['userId'], 'details' => "Manual Invoice No: " . $row['voucher_no']]);

			?>

			<script>
				alert('Record successfully deleted.');
				window.location.href = "?prt=partial&id=<?php echo $row['main_id']; ?>";
				window.onunload = refreshParent;

				function refreshParent() {
					window.opener.location.reload();
				}
			</script>

	<?php
		} else {
			$msg = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Failed to deleted this record !</b></div>';
		}
	}

	?>


	<div class="container">
		<?php
		//$get_pr_st = $acttObj->read_specific("id", "paid_income_invoices", "income_invoice_id = '".$id."' AND is_partial_payment = 1 AND is_deleted = 0")['id'] ? : 0;

		if ($row['payment_status'] == 'partial' && $payment_rec_type == 'full') { ?>
			<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4">
				<b>This invoice is Partially Paid, new payment must be made through Partial Payment section. </b>
			</div>
		<?php
			die();
			exit;
		}
		?>

		<?php
		if ($payment_rec_type == 'full') {

			if ($row['is_paid'] == 1) { ?>
				<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4">
					The Invoice# <b><?php echo $row['voucher_no']; ?></b> is already Paid.
				</div>
		<?php
				die();
				exit;
			}
		} // end if url get payment receive type = full
		?>

		<form action="" method="post" class="col-md-12">
			<h3 class="text-center">
				Update Amount for
			</h3>
			<h4 class="text-center">
				Invoice No# <?php echo $row['voucher_no']; ?>
			</h4>

			<p class="text-center text-danger">
				<b>NOTE :</b> Total Amount for this invoice is :
				<?php echo '<b>' . $misc->numberFormat_fun($remAmount) . '</b>'; ?>
			</p>

			<span id="display_msg">
				<?php if (isset($msg) && !empty($msg)) {
					echo $msg;
				} ?>
			</span>

			<?php if ($payment_rec_type == 'partial') { ?>
				<div class="row">
					<div class="form-group col-sm-6 col-sm-offset-3">
						<input name="title" id="title" class="form-control" type="text" value="<?php echo $title; ?>" placeholder="Enter title of amount (optional)" />
					</div>
				</div>
			<?php } ?>
			<div class="row">
				<div class="form-group col-sm-6">
					<label>Amount Received *</label>
					<input oninput="value_amount()" name="rAmount" id="rAmount" class="form-control" type="number" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" value="<?php echo $remAmount; ?>" required />
				</div>
				<div class="form-group col-sm-6">
					<label>Date *</label>
					<input name="rDate" type="date" class="form-control" value="<?php echo !empty($rDate) && $rDate != '1001-01-01' ? $rDate : ''; ?>" required />
				</div>
				<div class="form-group col-sm-6">
					<label>Payment Type</label>
					<select class="form-control" id="payment_type" name="payment_type" required>
						<option value="">- Select -</option>
						<option value="bacs" <?php echo ($payment_type == 'bacs') ? 'selected' : ''; ?>>BACS</option>
						<option value="cheque" <?php echo ($payment_type == 'cheque') ? 'selected' : ''; ?>>Cheque</option>
						<option value="card" <?php echo ($payment_type == 'card') ? 'selected' : ''; ?>>Credit/Debit Card</option>
						<option value="cash" <?php echo ($payment_type == 'cash') ? 'selected' : ''; ?>>Cash</option>
					</select>
				</div>
				<div class="form-group col-sm-6 payment_through_wrap hide">
					<label class="pull-left">Payment Method</label>
					<label class="pull-right">
						<a href="javascript:void(0)" onclick="return addNewPaymentMode()" title="Add New Detail" data-toggle="tooltip" class="btn btn-info btn-xs text-white">
							<i class="fa fa-plus"></i> New
						</a>
					</label>
					<select class="form-control" id="payment_through" name="payment_through">
					</select>
				</div>
				<div class="form-group col-sm-12 text-right">
					<button class="btn btn-primary" type="submit" id="btn_submit" name="submit">Submit &raquo;</button>
				</div>
			</div>
		</form>
	</div>

	<?php if ($payment_rec_type == 'partial') { ?>
		<div class="container">
			<?php
			$row_part = $acttObj->read_all('*', 'paid_income_invoices', 'is_deleted = 0 AND is_partial_payment = 1 AND income_invoice_id = ' . $id);

			if (mysqli_num_rows($row_part) > 0) { ?>
				<table class="table table-bordered table-hover table-condensed table-striped">
					<thead>
						<th>Title</th>
						<th>Amount</th>
						<th>Paid Date</th>
						<th>Method</th>
						<th class="text-center">Action</th>
					</thead>
					<tbody>
						<?php while ($row_data = mysqli_fetch_assoc($row_part)) { ?>
							<tr <?php if ((isset($_GET['edit']) || isset($_GET['del'])) && ($_GET['id'] == $row_data['id'])) {
									echo 'class="bg-success"';
								} ?>>
								<td title="<?php echo $row_data['title']; ?>">
									<?php echo substr($row_data['title'], 0, 30) ?: 'NIL'; ?>
								</td>
								<td>
									<?php echo $misc->numberFormat_fun($row_data['total_amount']); ?>
								</td>
								<td>
									<?php echo $row_data['dated']; ?>
								</td>
								<td>
									<?php
									if (!empty($row_data['payment_type'])) {

										$sql = "SELECT name, account_no, sort_code, iban_no FROM account_payment_modes WHERE id = " . $row_data['payment_method'];
										$result = mysqli_query($con, $sql);
										$row = mysqli_fetch_assoc($result);

										if ($row_data['payment_type'] == 'bacs') {
											$pyment_type = strtoupper($row_data['payment_type']);
										} else {
											$pyment_type = ucwords($row_data['payment_type']);
										}

										$pm =  $pyment_type . '<br><i style="font-size: 11px;">' . $row['name'];

										if ($row['account_no']) {
											$pm .=  ' <br> A/C: ' . $row['account_no'];
										}

										$pm .= '</i>';
									} else {
										$pm = 'N/A';
									}
									echo $pm;
									?>
								</td>
								<td class="text-center">
									<a onclick='return confirm_delete();' href="<?php echo basename(__FILE__) . '?id=' . $row_data['id'] . '&del=1'; ?>" title="Trash Record" class="btn">
										<i class="fa fa-trash text-danger"></i>
									</a>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } else {
				echo '<h3 class="text-danger text-center col-sm-12"> <span class="label label-danger">No partials added yet !</span></h3>';
			} ?>
		</div>
	<?php } ?>


	<!-- Modal --- Used for Payment Methods (dropdown) -->
	<div id="myModal2" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content modal-md">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Add New Payment Method</h4>
				</div>
				<div class="modal-body">
					<div class="modal_details"></div>
				</div>
			</div>

		</div>
	</div>


</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


<script src="js/income_receive_amount.js"></script>

<script>
	<?php if ($payment_rec_type == 'full') { ?>

		function value_amount() {
			var amount_val = document.getElementById('rAmount');
			var display_msg = document.getElementById('display_msg');
			var btn_submit = document.getElementById('btn_submit');
			if (!(/^[-+]?\d*\.?\d*$/.test(amount_val.value))) {
				btn_submit.disabled = true;
				display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is not a number !</b></div>';
			} else {
				if (amount_val.value > <?php echo $final_sum; ?>) {
					btn_submit.disabled = true;
					display_msg.innerHTML = '<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4"><b>Entered value is greater  than invoice amount <?php echo $final_sum; ?></b></div>';
				} else if (amount_val.value < <?php echo $final_sum; ?>) {
					btn_submit.disabled = true;
					display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is less than invoice amount <?php echo $final_sum; ?></b></div>';
				} else {
					btn_submit.disabled = false;
					display_msg.innerHTML = '';
				}
			}
		}

	<?php } ?>


	<?php if ($payment_rec_type == 'partial') { ?>

		function value_amount() {
			var amount_val = document.getElementById('rAmount');
			var display_msg = document.getElementById('display_msg');
			var btn_submit = document.getElementById('btn_submit');
			if (!(/^[-+]?\d*\.?\d*$/.test(amount_val.value))) {
				btn_submit.disabled = true;
				display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is not a number !</b></div>';
			} else {
				if (amount_val.value > <?php echo $remAmount; ?>) {
					btn_submit.disabled = true;
					display_msg.innerHTML = '<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4"><b>Entered value is greater  than remaining amount <?php echo $remAmount; ?></b></div>';
				} else if (amount_val.value < <?php echo $remAmount; ?>) {
					btn_submit.disabled = false;
					display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is less than remaining amount <?php echo $remAmount; ?></b></div>';
				} else {
					btn_submit.disabled = false;
					display_msg.innerHTML = '';
				}
			}
		}

		function e_value_amount() {
			var e_amount_val = document.getElementById('e_rAmount');
			var e_display_msg = document.getElementById('e_display_msg');
			var e_btn_submit = document.getElementById('e_btn_submit');
			if (!(/^[-+]?\d*\.?\d*$/.test(e_amount_val.value))) {
				e_btn_submit.disabled = true;
				e_display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is not a number !</b></div>';
			} else {
				if (e_amount_val.value > <?php echo $remAmount; ?>) {
					e_btn_submit.disabled = true;
					e_display_msg.innerHTML = '<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4"><b>Entered value is greater  than remaining amount <?php echo $remAmount; ?></b></div>';
				} else if (e_amount_val.value < <?php echo $remAmount; ?>) {
					e_btn_submit.disabled = false;
					e_display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is less than remaining amount <?php echo $remAmount; ?></b></div>';
				} else {
					e_btn_submit.disabled = false;
					e_display_msg.innerHTML = '';
				}
			}
		}

		function confirm_delete() {
			var result = confirm("Are you sure to delete this record ?");
			if (result == true) {
				return true;
			} else {
				return false;
			}
		}

	<?php } ?>
</script>

</html>