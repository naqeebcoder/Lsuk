<?php include '../../db.php';
include_once '../../class.php';
$excel = @$_GET['excel'];
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$voucher_no = SafeVar::GetVar('voucher_no', '');
$invoice_no = SafeVar::GetVar('invoice_no', '');
$company = SafeVar::GetVar('company', '');

if (empty($search_2) && empty($search_3)) {
	$str_where = 1;
	$filter_dates =  'Date Range: All';
} else {
	$str_where = "DATE(dated) BETWEEN '{$search_2}' AND '{$search_3}'";
	$filter_dates =  'From: ' . $misc->dated($search_2) . ' To: ' . $misc->dated($search_3);
}



$table = 'account_receivable';
$mak_page = @$_GET['page'];
$startpoint = @$_GET['startpoint'];
$limit = @$_GET['limit'];

$page = (int) (!isset($mak_page) ? 1 : $mak_page);
$limit = 50;
$startpoint = ($page * $limit) - $limit;

$conditions = "1=1 AND ";

$opening_balance_conditions = $conditions; // base condition for opening balance

if (empty($search_2) && empty($search_3)) {
	$conditions .= 1;
} else {
	$conditions .= "DATE(dated) BETWEEN '{$search_2}' AND '{$search_3}'";
}

if (!empty($voucher_no)) {
	$conditions .= " AND voucher = '{$voucher_no}'";
}
if (!empty($invoice_no)) {
	$conditions .= " AND invoice_no = '{$invoice_no}'";
}
if (!empty($company)) {
	$conditions .= " AND company = '{$company}'";
}

//......................................\\//\\//\\//\\//........................................................\\
$query = "WITH opening_balance_cte AS (
			SELECT 
				ROUND(IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0), 2) AS opening_balance
			FROM 
				account_receivable
			WHERE 
				id < (
					SELECT MIN(id) 
					FROM account_receivable 
					WHERE {$conditions}
				)
		),
		
		ordered_transactions AS (
			SELECT 
				*,
				ROW_NUMBER() OVER (ORDER BY id) AS row_num
			FROM 
				account_receivable
			WHERE 
				{$conditions}
		),
		
		running_balance_cte AS (
			SELECT 
				t.*,
				SUM(debit - credit) OVER (ORDER BY id ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS transaction_running_balance
			FROM 
				ordered_transactions t
		)
		
		SELECT 
			t.*,
			ob.opening_balance,
			ROUND(ob.opening_balance + t.transaction_running_balance, 2) AS running_balance
		FROM 
			running_balance_cte t
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
<h2 align="center"><u>Receivable Statement Report</u></h2>
<p align="right">Report  Date: ' . $misc->sys_date() . '<br />' . $filter_dates . '</p>
</div>

<table>
		<thead>
			<tr>
				<th>Voucher</th>
				<th>Invoice No.</th>
				<th>Dated</th>
				<th>Company</th>
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
	$htmlTable .= '<tr>';
	$htmlTable .= '<td>' . $row["voucher"] . '</td>';
	$htmlTable .= '<td>' . $row["invoice_no"] . '</td>';
	$htmlTable .= '<td>' . $misc->dated($row['dated']) . '</td>';
	$htmlTable .= '<td>' . $row["company"] . '</td>';
	$htmlTable .= '<td>' . $row["description"] . '</td>';
	$htmlTable .= '<td>' . $misc->numberFormat_fun($row["credit"]) . '</td>';
	$htmlTable .= '<td>' . $misc->numberFormat_fun($row["debit"]) . '</td>';
	$htmlTable .= '<td>' . $misc->numberFormat_fun($row["running_balance"]) . '</td>';
	$htmlTable .= '</tr>';
}

$htmlTable .= '</tbody>
</table>';

list($a, $b) = explode('.', basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=" . $a . ".xls");
echo $htmlTable;
