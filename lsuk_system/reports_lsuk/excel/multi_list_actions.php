<?php include '../../db.php';
include_once '../../class.php';
$excel = @$_GET['excel'];

$org = SafeVar::GetVar('org', '');
$inov = SafeVar::GetVar('inov', '');
$pstatus = SafeVar::GetVar('pstatus', '');
$status = SafeVar::GetVar('status', '');
$from_date = SafeVar::GetVar('from_date', '');
$to_date = SafeVar::GetVar('to_date', '');

$mak_page = @$_GET['page'];
$startpoint = @$_GET['startpoint'];
$limit = @$_GET['limit'];

    $page = (int) (!isset($mak_page) ? 1 : $mak_page);
    $limit = 50;
    $startpoint = ($page * $limit) - $limit;


if (!empty($org)) {
    mysqli_query($con, "SET SQL_BIG_SELECTS=1");
}

$strSqlFilt = $strSqlFilt2 = "";

if (!empty($org)) {
    $strSqlFilt = " AND comp_reg.abrv LIKE '$org%' ";
    $strSqlFilt2 = " AND mi.comp_abrv LIKE '$org%' ";
}
if (!empty($inov)) {
    $strSqlFilt .= " AND mi.m_inv like '%$inov%' ";
    $strSqlFilt2 .= " AND mi.m_inv LIKE '%$inov%' ";
}

//......................................\\//\\//\\//\\//........................................................\\
$str_where = '';

if (empty($pstatus)) {
    $str_where .= '';
} else if ($pstatus == 'pending') {
    $str_where .= 'AND mi.status = ""';
} else {
    $str_where .= 'AND mi.status = "' . ucwords(str_replace("_", " ", $pstatus)) . '" ';
}

if ($status == 'all') {
    $str_where .= '';
} else if (empty($status) || $status == 'active') {
    $str_where .= 'AND (mi.credit_note_id IS NULL OR mi.credit_note_id = 0) AND mi.is_deleted = 0 AND mi.commit = 1';
} else if ($status == 'cancelled') {
    $str_where .= 'AND mi.is_deleted = 1';
} else if ($status == 'credit_note') {
    $str_where .= 'AND (mi.credit_note_id <> NULL OR mi.credit_note_id <> 0) AND mi.is_deleted = 0';
} else if ($status == 'undo') {
    $str_where .= 'AND mi.is_undo_payment = 1 AND mi.is_deleted = 0';
}

if (!empty($from_date) && !empty($to_date)) {
    $str_where .= " AND mi.from_date BETWEEN '" . $from_date . "' AND '" . $to_date . "'";
}

$query = "SELECT mi.*
            FROM mult_inv mi
            WHERE mi.comp_id <> '' " . $str_where . "
            $strSqlFilt2
            ORDER BY DATE(mi.dated) DESC";

//$result = mysqli_query($con, $query);
$res = $acttObj->full_fetch_array($query);
//debug($res); exit;
//echo $query; exit;
//...................................................................................................................................../////
$htmlTable = '';
$htmlTable .= '<style>
table {border-collapse: collapse; width:100%;word-wrap: break-word;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF;font-weight:bold;word-wrap: break-word;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;word-wrap: break-word;}
</style>
<div>
<h2 align="center"><u>Collective Invoices List</u></h2>
<p align="right">Report  Date: ' . $misc->sys_date() . '<br />' . (($inov) ? 'Invoice# ' . $inov : '') .  (($org) ? ' Organization: ' . $org : '') . ' <br>';

if (!empty($from_date) && !empty($to_date)) {
    $htmlTable .= 'Date Range: ' . $misc->dated($from_date) . ' to ' . $misc->dated($to_date);
}

$htmlTable .= '</p>
</div>

	<table>
		<thead>
			<tr>
				<th>Invoice #</th>
                <th>Company Name</th>
                <th>Amount</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Payment Status</th>
                <th>Paid date</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Due Date</th>
                <th>Dated</th>
			 </tr>
		</thead>
		<tbody>';
foreach ($res as $row) {

    if ($row['commit'] == 1) {
        if ($row['status']) {
            $status = $row['status'];
        } else {
            $status = 'Pending';
        }
    } else {
        $status = 'Credit Note';
    }

    $htmlTable .= '<tr>';
    $htmlTable .= '<td>' . $row["m_inv"] . '</td>';
    $htmlTable .= '<td>' . $row["comp_name"] . '</td>';
    $htmlTable .= '<td>' . $row['mult_amount'] . '</td>';
    $htmlTable .= '<td>' . $row["rAmount"] . '</td>';
    $htmlTable .= '<td>' . (round($row['mult_amount'], 2) - round($row['rAmount'], 2)) . '</td>';
    $htmlTable .= '<td>' . $status . '</td>';
    $htmlTable .= '<td>' . (($row['paid_date'] != '1001-01-01' && $row['paid_date'] != '0000-00-00') ? $misc->dated($row['paid_date']) : 'Date Not Found') . '</td>';
    $htmlTable .= '<td>' . $misc->dated($row['from_date']) . '</td>';
    $htmlTable .= '<td>' . $misc->dated($row['to_date']) . '</td>';
    $htmlTable .= '<td>' . $misc->dated($row['due_date']) . '</td>';
    $htmlTable .= '<td>' . $misc->dated($row['dated']) . '</td>';
    $htmlTable .= '</tr>';
}

$htmlTable .= '</tbody>
</table>';

list($a, $b) = explode('.', basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=" . $a . ".xls");
echo $htmlTable;
