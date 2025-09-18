<?php include '../../db.php';include_once ('../../class.php'); $excel=@$_GET['excel'];session_start();$UserName=$_SESSION['UserName'];$prv=$_SESSION['prv']; $search_1=@$_GET['search_1'];$search_2=@$_GET['search_2'];$search_3=@$_GET['search_3'];
$i=1;
$current = date('Y-m-d');
if(isset($search_1) && $search_2==$current && $search_3==$current){
$append_int = "and interp_salary.interp=" . $search_1;
$query = "SELECT interp_salary.*,interpreter_reg.name FROM interp_salary,interpreter_reg where interp_salary.interp=interpreter_reg.id AND interp_salary.deleted_flag=0 $append_int ORDER BY interp_salary.dated DESC ";
}else{
$append_int = "and interp_salary.interp=" . $search_1;
$append_date = "and interp_salary.dated BETWEEN '$search_2' AND '$search_3'";
$query = "SELECT interp_salary.*,interpreter_reg.name FROM interp_salary,interpreter_reg where interp_salary.interp=interpreter_reg.id AND interp_salary.deleted_flag=0 $append_int $append_date  ORDER BY interp_salary.dated DESC "; 
}
$result = mysqli_query($con, $query);

$htmlTable='';
$htmlTable.='<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .='<span align="right"> Date: '.$misc->sys_date(). '</span>';
$date_range = ($search_2==$current && $search_3==$current ? 'All Time':'Date From ['.$misc->dated($search_2).'] Date To ['.$misc->dated($search_3).']');
$htmlTable .='<h2 style="text-decoration:underline; text-align:center">Interpreters Salary Report</h2>
<p>Date Range : ' .$date_range. '</p>
</div>

<table>
<thead>
<tr>
 	<th style="width:35px;">Sr.No</th>
	<th>Linguistic</th>
	<th>Invoice</th>
	<th>From</th>
	<th>To</th>
	<th>Deductions</th>
	<th>Additions</th>
	<th>Salary</th>
	<th>Status</th>
	<th>Paid Date</th>
 </tr>';
while($row = mysqli_fetch_assoc($result)){
$paid_status = $row['is_paid'] == 1 ? "<span class='label label-success'><i class='fa fa-check-circle'></i> Paid</span>" : "<span class='label label-warning'>Unpaid</span>";
$htmlTable .='<tr>';
$htmlTable .='<td>'.$i.'</td>';
$htmlTable .='<td>'.$row["name"].'</td>';
$htmlTable .='<td>'.$row["invoice"].'</td>';
$htmlTable .='<td>'.$misc->dated($row['frm']).'</td>';
$htmlTable .='<td>'.$misc->dated($row['todate']).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($row['ni_dedu'] + $row['tax_dedu'] + $row['payback_deduction']).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun($row['given_amount']).'</td>';
$htmlTable .='<td>'.$misc->numberFormat_fun(($row['salry'] - $row['ni_dedu'] - $row['tax_dedu'] - $row['payback_deduction']) + $row['given_amount']).'</td>';
$htmlTable .='<td>'.$paid_status.'</td>';
$htmlTable .='<td>'.$misc->dated($row['paid_date']).'</td>';
$htmlTable .='</tr>';
$i++;}
$htmlTable.='</table>';

list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
echo $htmlTable;
?>