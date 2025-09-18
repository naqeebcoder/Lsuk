<?php include '../../db.php';
include_once '../../class.php';
$excel = @$_GET['excel'];

$search_1 = SafeVar::GetVar('search_1', '');
$search_2 = SafeVar::GetVar('search_2', '');
$search_3 = SafeVar::GetVar('search_3', '');
$voucher_no = SafeVar::GetVar('voucher_no', '');
$invoice_no = SafeVar::GetVar('invoice_no', '');
$company = SafeVar::GetVar('company', '');
$ref_no = SafeVar::GetVar('ref_no', '');

if (empty($search_2) && empty($search_3)) {
	$str_where = 1;
	$filter_dates =  'Date Range: All';
} else {
	$str_where = "DATE(dated) BETWEEN '{$search_2}' AND '{$search_3}'";
	$filter_dates =  'From: ' . $misc->dated($search_2) . ' To: ' . $misc->dated($search_3);
}

$table = 'account_expenses';
$mak_page = @$_GET['page'];
$startpoint = @$_GET['startpoint'];
$limit = @$_GET['limit'];

$page = (int) (!isset($mak_page) ? 1 : $mak_page);
$limit = 50;
$startpoint = ($page * $limit) - $limit;

//......................................\\//\\//\\//\\//........................................................\\

$conditions = "1=1";

			if (!empty($search_2) && !empty($search_3)) {
				$conditions .= " AND DATE(ae.dated) BETWEEN '{$search_2}' AND '{$search_3}'";
			}

			if (!empty($voucher_no)) {
				$conditions .= " AND ae.voucher = '{$voucher_no}'";
			}

			if (!empty($invoice_no)) {
				$conditions .= " AND ae.invoice_no = '{$invoice_no}'";
			}

			if (!empty($company)) {
				$conditions .= " AND ae.company = '{$company}'";
			}

			if (!empty($ref_no)) {
				$conditions .= " AND e.inv_ref_num = '{$ref_no}'";
			}

			$query = "
				WITH opening_balance_cte AS (
					SELECT 
						ROUND(IFNULL(SUM(ae.debit), 0) - IFNULL(SUM(ae.credit), 0), 2) AS opening_balance
					FROM 
						account_expenses ae
					LEFT JOIN expence e ON e.invoice_no = ae.invoice_no
					WHERE 
						ae.id < (
							SELECT MIN(ae.id)
							FROM account_expenses ae
							LEFT JOIN expence e ON e.invoice_no = ae.invoice_no
							WHERE {$conditions}
						)
				),

				transaction_data AS (
					SELECT 
						ae.id,
						ae.dated,
						ae.credit,
						ae.debit,
						ae.posted_by,
						ae.posted_on,
						ae.voucher,
						ae.invoice_no,
						ae.company,
						ae.description,
						ae.balance,
						e.inv_ref_num,
						SUM(ae.debit - ae.credit) OVER (
							ORDER BY ae.id
							ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
						) AS transaction_running_balance
					FROM 
						account_expenses ae
					LEFT JOIN expence e ON e.invoice_no = ae.invoice_no
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
<h2 align="center"><u>Expense Statement Report</u></h2>
<p align="right">Report  Date: ' . $misc->sys_date() . '<br />' . $filter_dates . '</p>
</div>


	<table>
		<thead>
			<tr>
				<th>Track#</th>
				<th>Voucher</th>
				<th>Invoice/Ref#</th>
				<th>Dated</th>
				<th>Company</th>
				<th>Description</th>
				<th>Credit</th>
				<th>Debit</th>
				<th>Balance (' . $pound_symbol . ')</th>
			</tr>
		</thead>
		<tbody>
		<!--tr>
			<td colspan="7" align="right">Opening Balance</td>
			<td>' . $misc->numberFormat_fun($mak_results["opening_balance"]) . '</td>
		</tr-->';
		
		foreach ($res as $row) {
			$htmlTable .= '<tr>';
			$htmlTable .= '<td>' . $row["invoice_no"] . '</td>';
			$htmlTable .= '<td>' . $row["voucher"] . '</td>';
			$htmlTable .= '<td>' . $row["inv_ref_num"] . '</td>';	
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
