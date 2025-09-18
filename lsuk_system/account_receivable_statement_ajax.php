<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);

if (session_id() == '' || !isset($_SESSION)) {session_start();}

include 'db.php';
include 'class.php';
include 'function.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$search_2 = SafeVar::GetVar('search_2', '');
$search_3 = SafeVar::GetVar('search_3', '');

if (empty($search_2)) {
    $search_2 = date("Y-m-d");
}
if (empty($search_3)) {
    $search_3 = date("Y-m-d");
}
$newtest1='';
$newtest='';
$whereClause = "";

		
		$query = "WITH opening_balance_cte AS (
			SELECT 
				ROUND(IFNULL(SUM(debit), 0) - IFNULL(SUM(credit), 0), 2) AS opening_balance
			FROM 
				account_receivable
			WHERE 
				DATE(dated) < '".$search_2."'
		),

		transaction_data AS (
			SELECT 
				*,
				SUM(debit - credit) OVER (ORDER BY DATE(dated), id ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS transaction_running_balance
			FROM 
				account_receivable
			WHERE 
				DATE(dated) BETWEEN '".$search_2."' AND '".$search_3."'
		)

		SELECT 
			*,
			ob.opening_balance,
			ROUND(ob.opening_balance + t.transaction_running_balance, 2) AS running_balance
		FROM 
			transaction_data t
		CROSS JOIN 
			opening_balance_cte ob
		ORDER BY 
			DATE(t.dated), t.id";
		//$result = $con->query($query);
		//$res = $result->fetch_all(MYSQLI_ASSOC);
		$res = $acttObj->full_fetch_array($query);
		
		$rec = count($res);
		$sno = 1;
		//debug($res);
		
		foreach($res as $row){
		
			$newtest1 .= '["'.$row['voucher'].'", "' . $row['invoice_no']. '", "' . $row['dated'] . '", "' .$row['company'] . '", "' . $row['description']. '", "' . $row['credit'] . '", "' . $row['debit'] . '", "' . $row['running_balance'] . '"],';
		}
			$newtest1 = substr($newtest1, 0, -1);
			$newtest .='{
			  "draw": 1,
			  "recordsTotal": ' . $rec . ',
			  "recordsFiltered": ' . $rec . ',
			  "data": [' . $newtest1 . ' ]
			}';
		echo $newtest;
		exit;

?>