<?php include '../../db.php';
include_once '../../class.php';
$excel = @$_GET['excel'];

$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];

$voucher_no = @$_GET['voucher_no'];
$invoice_no = @$_GET['invoice_no'];
$company = @$_GET['company'];
$bank_id = @$_GET['bank_id'];
$category = SafeVar::GetVar('cat', '');
$status = SafeVar::GetVar('status', '');

$table = 'account_receivable';
$mak_page = @$_GET['page'];
$startpoint = @$_GET['startpoint'];
$limit = @$_GET['limit'];

$page = (int) (!isset($mak_page) ? 1 : $mak_page);
$limit = 50;
$startpoint = ($page * $limit) - $limit;

//......................................\\//\\//\\//\\//........................................................\\
$conditions = " is_bank = 0";

$opening_balance_conditions = $conditions; // base condition for opening balance

if (!empty($search_2) && !empty($search_3)) {
	$conditions .= " AND DATE(dated) BETWEEN '{$search_2}' AND '{$search_3}'";
}

if (!empty($voucher_no)) {
	$conditions .= " AND voucher LIKE '%{$voucher_no}%'";
}
if (!empty($invoice_no)) {
	$conditions .= " AND invoice_no LIKE '%{$invoice_no}%'";
}
if (!empty($company)) {
	$conditions .= " AND company LIKE '%{$company}%'";
}
if (!empty($bank_id)) {
	$conditions .= " AND account_id = {$bank_id}";
	$opening_balance_conditions .= " AND account_id = {$bank_id}"; // apply same to opening balance
}
if (isset($category)) {
	if ($category === 'all') {
		$conditions .= " AND (is_receivable = 0 OR is_receivable = 1)";
	} elseif ($category === 'income') {
		$conditions .= " AND is_receivable = 1";
	} elseif ($category === 'expense') {
		$conditions .= " AND is_receivable = 0";
	}
}
if (!empty($status)) {
	if ($status == 'all') {
		$conditions .= "";
	} else {
		$conditions .= " AND status = '{$status}'";
	}
}


$query = "
		WITH opening_balance_cte AS (
			SELECT 
				ROUND(IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0), 2) AS opening_balance
			FROM 
				account_journal_ledger
			WHERE 
				{$opening_balance_conditions}
				AND id < (
					SELECT IFNULL(MIN(id), 0)
					FROM account_journal_ledger
					WHERE {$conditions}
				)
		),
	
		transaction_data AS (
			SELECT 
				id,
				dated,
				credit,
				debit,
				posted_by,
				posted_on,
				voucher,
				invoice_no,
				company,
				is_bank,
				payment_type,
				account_id,
				description,
				balance,
				SUM(debit - credit) OVER (
					ORDER BY id 
					ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
				) AS transaction_running_balance
			FROM 
				account_journal_ledger
			WHERE 
				{$conditions}
		)
	
		SELECT 
			t.*,
			ob.opening_balance,
			ROUND(ob.opening_balance + t.transaction_running_balance, 2) AS running_balance
		FROM 
			transaction_data t
		CROSS JOIN 
			opening_balance_cte ob
		ORDER BY 
			t.id
		LIMIT {$startpoint}, {$limit}";


//$result = mysqli_query($con, $query);
$res = $acttObj->full_fetch_array($query);
//debug($res); exit;
//echo $query; exit;

$pound_symbol = mb_convert_encoding("£", 'UTF-16LE', 'UTF-8');

//...................................................................................................................................../////
$htmlTable = '';
$htmlTable .= '<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
<div>
<h2 align="center"><u>Journal Ledger Bank Statement Report</u></h2>
<p align="right">Report  Date: ' . $misc->sys_date() . '<br />
  From: ' . $misc->dated($search_2) . ' To: ' . $misc->dated($search_3) . '</p>
</div>

<table>
		<thead>
			<tr>
				<th>Voucher</th>
				<th>Invoice No.</th>
				<th>Dated</th>
				<th>Company</th>
				<th>Recv. By</th>
				<th>Description</th>
				<th>Credit</th>
				<th>Debit</th>
				<th>Balance ('.$pound_symbol.')</th>
			 </tr>
		</thead>
		<tbody>
		<!--tr>
			<td colspan="7" align="right">Opening Balance</td>
			<td>' . $misc->numberFormat_fun($mak_results["opening_balance"]) . '</td>
		</tr-->';
foreach ($res as $row) {

	$bank_info = $acttObj->read_specific("name as bank_name, account_no, sort_code, iban_no", "account_payment_modes", " is_bank = 0 AND id = " . $row['account_id']);

	$htmlTable .= '<tr>';
	$htmlTable .= '<td>' . $row["voucher"] . '</td>';
	$htmlTable .= '<td>' . $row["invoice_no"] . '</td>';
	$htmlTable .= '<td>' . $misc->dated($row['dated']) . '</td>';
	$htmlTable .= '<td>' . $row["company"] . '</td>';
	$htmlTable .= '<td>' . ucwords($row["payment_type"]) .
		'<p style="font-size: 11px; margin-bottom:0;">
				By: ' . ucwords($bank_info['bank_name']) . ' </p> </td>';
	$htmlTable .= '<td>' . $row["description"] . '</td>';
	$htmlTable .= '<td>' . $misc->numberFormat_fun($row["credit"]) . '</td>';
	$htmlTable .= '<td>' . $misc->numberFormat_fun($row["debit"]) . '</td>';
	$htmlTable .= '<td>' . $misc->numberFormat_fun($row["running_balance"]) . '</td>';
	$htmlTable .= '</tr>';
}

$htmlTable .= '</tbody>
</table>';

//echo $htmlTable; exit;

list($a, $b) = explode('.', basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=" . $a . ".xls");
echo $htmlTable;
