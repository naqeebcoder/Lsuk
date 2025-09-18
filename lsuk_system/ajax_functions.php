<?php
include("db.php");
include 'class.php';

if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}

if (isset($_GET['action']) && $_GET['action'] == 'get_interpreter_jobs') {

	function formatDuration($minutes) {
		$hours = floor($minutes / 60);
		$mins  = $minutes % 60;
		if ($hours > 0 && $mins > 0) {
			return $hours . ' hour ' . $mins . ' min';
		} elseif ($hours > 0) {
			return $hours . ' hour';
		} else {
			return $mins . ' min';
		}
	}

	$jobType  = $_GET['jobType'];
	$interpId = $_GET['interpId'];

	if ($jobType == "face_to_face_jobs") {
		$sql = "SELECT 'Face to Face' AS job_type, source, target, 
					SUM(hoursWorkd) AS total_hours
				FROM interpreter
				WHERE intrpName = '$interpId'
				AND deleted_flag = 0 
				AND order_cancel_flag = 0 
				AND orderCancelatoin = 0
				GROUP BY source, target";
		} elseif ($jobType == "telephone_jobs") {
			$sql = "SELECT 'Telephone' AS job_type, source, target, 
						SUM(hoursWorkd) AS total_hours
					FROM telephone
					WHERE intrpName = '$interpId'
					AND deleted_flag = 0 
					AND order_cancel_flag = 0 
					AND orderCancelatoin = 0
					GROUP BY source, target";
		} elseif ($jobType == "translation_jobs") {
			$sql = "SELECT 'Translation' AS job_type, source, target, 
						SUM(numberUnit) AS total_hours
					FROM translation
					WHERE intrpName = '$interpId'
					AND deleted_flag = 0 
					AND order_cancel_flag = 0 
					AND orderCancelatoin = 0
					GROUP BY source, target";
		} else {
			echo "Invalid job type";
			exit;
	}
	
	$result = mysqli_query($con, $sql);

	if (mysqli_num_rows($result) > 0) {
		echo "<table class='table table-bordered table-striped table-hover table-condensed'>
				<thead>
				<tr>
					<th width='40%'>Source</th>
					<th width='40%'>Target</th>
					<th width='20%'>Total Hours/Units</th>
				</tr>
				</thead>
				<tbody>";
				$total_duration = 0;
				while($row = mysqli_fetch_assoc($result)) {
					
					// $total_duration += $row['total_hours'];

					if($jobType == "translation_jobs") {
						$total_hours = $row['total_hours'] . ' units';
					} else {
						$total_hours = formatDuration($row['total_hours']);
					}

					echo "<tr>
						<td>".$row['source']."</td>
						<td>".$row['target']."</td>
						<td>".$total_hours."</td>
					</tr>";
				}
		echo "</tbody>
		</table>";
	} else {
		echo "<p>No records found</p>";
	}
}



if (isset($_GET['voucher_type']) && $_GET['action'] == 'getNextVoucherCount') {
	$voucher_type = $_GET['voucher_type'];

	// Defined in class.php
	echo $acttObj->getNextVoucherCount($voucher_type);
}

if (isset($_GET['action']) && $_GET['action'] == 'get_payment_types') {

	$table = $_GET['table'];
	$edit_id = $_GET['row_id'];

	if ($_GET['type'] && $_GET['type'] == 'cash') {
		$is_bank = 0;
	} else {
		$is_bank = 1;  // BACs, Banks, Card Payment, Cheque
	}
	$result = $acttObj->read_all("id, name, account_no", "account_payment_modes", " is_bank = " . $is_bank . " AND is_deleted = 0 AND status = 1 ORDER BY name");

	$db_payment_method_id = $acttObj->read_specific("payment_method_id", $table, " id= $edit_id")['payment_method_id'];

	if (mysqli_num_rows($result) > 0) {
		echo '<option value="">- Select -</option>';
		while ($row = mysqli_fetch_assoc($result)) {

			$selected = (($db_payment_method_id == $row['id']) ? 'selected' : (($row['id'] == 1) ? 'selected' : ''));

			if ($_GET['type'] == 'cash') {
				echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . ' </option>';
			} else {
				echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . ' [A/C: ' . $row['account_no'] . '] </option>';
			}
		}
	} else {
		echo '<option value="">No record found.</option>';
	}
}

if (isset($_GET['action']) && $_GET['action'] == 'add_payment_method') {

	if ($_GET['type'] == 'cash') {
		$cls = '12';
		$is_cash = 1;
	} else {
		$cls = '6';
		$is_cash = 0;
	}

	echo '<div class="row">';
	echo '<form action="" method="post" id="frmSavePaymentMethod" name="frmSavePaymentMethod">
				<input type="hidden" name="MM_is_cash" id="MM_is_cash" value="' . $is_cash . '">
				<input type="hidden" name="MM_is_validated" id="MM_is_validated" value="0">
				<div class="form-group col-sm-' . $cls . '">
					<label>Name / Title</label>
					<input type="text" name="bank_title" id="bank_title" class="form-control" value="" placeholder="Name / Title" required>
				</div>';

	if ($_GET['type'] != 'cash') {

		echo '<script>$(document).ready(function(){$("#sort_code").mask("00-000000");});</script>';

		echo '<div class="form-group col-sm-6">
						  <label>Account No.</label>
						  <input type="text" name="account_no" id="account_no" class="form-control" value="" placeholder="Account No." required>
					</div>
					<div class="form-group col-sm-6">
						  <label>Sort Code</label>
						  <input type="text" name="sort_code" id="sort_code" class="form-control" value="" placeholder="99-999999">
					</div>
					<div class="form-group col-sm-6">
						  <label>IBAN No.</label>
						  <input type="text" name="iban_no" id="iban_no" class="form-control" value="" placeholder="IBAN No.">
					</div>';
	}

	echo '<div class="form-group col-sm-12 text-right">
					<button class="btn btn-primary" type="button" id="btn_submit" name="submit" onclick="return savePaymentMethod()">Submit</button>
				</div>
			</form>';
	echo '</div>';
}

	if (isset($_POST['MM_is_validated']) && $_POST['MM_is_validated'] == 1) {

		// Sanitize and prepare variables
		$is_cash   = (int) $_POST['MM_is_cash'];
		$title     = mysqli_real_escape_string($con, trim($_POST['bank_title']));
		$is_update = isset($_POST['MM_update']) && $_POST['MM_update'] == 1;
		$record_id = isset($_POST['record_id']) ? (int) $_POST['record_id'] : 0;
		$user_id   = (int) $_SESSION['userId'];
		$now       = date('Y-m-d H:i:s');

		$is_bank = ($is_cash === 0) ? 1 : 0;

		// Set bank fields
		$account_no = $sort_code = $iban_no = $str_where = 'NULL';
		if ($is_bank) {
			$account_no = mysqli_real_escape_string($con, $_POST['account_no']);
			$sort_code  = mysqli_real_escape_string($con, $_POST['sort_code']);
			$iban_no    = mysqli_real_escape_string($con, $_POST['iban_no']);
			$str_where  = "AND account_no = '$account_no'";
		}

		// Duplicate check query
		$where_clause = "is_bank = $is_bank AND name = '$title' $str_where AND is_deleted = 0";
		if ($is_update && $record_id > 0) {
			$where_clause .= " AND id != $record_id";
		}

		$exists = $acttObj->read_specific("COUNT(id) AS total", "account_payment_modes", $where_clause . " LIMIT 1")['total'] ?? 0;

		if ($exists > 0) {
			echo 1001; // Duplicate record
			exit;
		}

		// Update block
		if ($is_update && $record_id > 0) {
			$status = (int) $_POST['status'];

			$update_query = "
				UPDATE account_payment_modes SET
					name = '$title',
					account_no = " . ($account_no ? "'$account_no'" : "NULL") . ",
					sort_code  = " . ($sort_code  ? "'$sort_code'"  : "NULL") . ",
					iban_no    = " . ($iban_no    ? "'$iban_no'"    : "NULL") . ",
					is_bank    = $is_bank,
					status     = $status,
					modified_by = $user_id,
					modified_on = '$now'
				WHERE id = $record_id
			";

			echo (mysqli_query($con, $update_query)) ? 'updated' : 'error';
			exit;
		}

		// Insert block
		$insert_query = "
			INSERT INTO account_payment_modes 
			(name, account_no, sort_code, iban_no, is_bank, created_by, created_on)
			VALUES (
				'$title',
				" . ($account_no ? "'$account_no'" : "NULL") . ",
				" . ($sort_code  ? "'$sort_code'"  : "NULL") . ",
				" . ($iban_no    ? "'$iban_no'"    : "NULL") . ",
				$is_bank,
				$user_id,
				'$now'
			)
		";

		if (mysqli_query($con, $insert_query)) {
			// Fetch and return updated list
			$result = $acttObj->read_all("id, name, account_no", "account_payment_modes", "is_bank = $is_bank AND is_deleted = 0 AND status = 1 ORDER BY name");

			if (mysqli_num_rows($result) > 0) {
				echo '<option value="">- Select -</option>';
				while ($row = mysqli_fetch_assoc($result)) {
					$id   = $row['id'];
					$name = htmlspecialchars($row['name']);
					$acc  = htmlspecialchars($row['account_no']);

					$label = ($is_cash === 1) ? $name : "$name [A/C: $acc]";
					echo "<option value=\"$id\">$label</option>";
				}
			} else {
				echo '<option value="">No record found.</option>';
			}
		} else {
			echo 'error';
		}
	}


if ($_POST['action'] == 'get_bank_by_id' && isset($_POST['id'])) {
	$id = (int)$_POST['id'];

	$result = $acttObj->read_all("*", "account_payment_modes", "id = $id LIMIT 1");
	if (mysqli_num_rows($result) > 0) {
		echo json_encode(mysqli_fetch_assoc($result));
	} else {
		echo json_encode([]);
	}
	exit;
}

if ($_POST['action'] == 'delete_bank' && isset($_POST['id'])) {
	$id = (int) $_POST['id'];
	$update = mysqli_query($con, "UPDATE account_payment_modes SET is_deleted = 1 WHERE id = $id");
	echo $update ? 'deleted' : 'error';
	exit;
}

if ($_POST['action'] == 'restore_bank' && isset($_POST['id'])) {
	$id = (int)$_POST['id'];
	$sql = "UPDATE account_payment_modes SET is_deleted = 0, modified_on = NOW(), modified_by = " . $_SESSION['userId'] . " WHERE id = $id";
	if (mysqli_query($con, $sql)) {
		echo 'restored';
	} else {
		echo 'error';
	}
	exit;
}


if (isset($_GET['action']) && $_GET['action'] === 'get_prepayment_payment_type') {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $prepaymentId = intval($_GET['id']);

        $payment_type = $acttObj->read_specific("payment_type", "pre_payments", " invoice_no = ".$prepaymentId)['payment_type'];

        if ($payment_type) {
            echo $payment_type;
        }
    } 
}