<?php include '../../db.php';
include_once('../../class.php');
$excel = @$_GET['excel'];
session_start();
$UserName = $_SESSION['UserName'];
$prv = $_SESSION['prv'];
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$is_paid = @$_GET['is_paid'];
if (isset($_GET['is_paid'])) {
    $append_is_paid = " and interp_salary.is_paid=1 ";
}
$i = 1;
if (isset($search_1) && !empty($search_1)) {
    $query = "SELECT interp_salary.is_paid,interp_salary.paid_date,interp_salary.ni_dedu,interp_salary.tax_dedu,interp_salary.payback_deduction,interp_salary.given_amount,interp_salary.interp,interp_salary.invoice,interp_salary.dated,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city,interpreter_reg.acntCode,interpreter_reg.acName,interpreter_reg.acNo,interp_salary.salry as net_total,interpreter_reg.work_type from interp_salary,interpreter_reg where interp_salary.interp=interpreter_reg.id and interp_salary.deleted_flag=0 $append_is_paid and (DATE(interp_salary.frm) BETWEEN ('$search_2') AND ('$search_3')) AND (DATE(interp_salary.todate) BETWEEN ('$search_2') AND ('$search_3')) and interp_salary.interp='$search_1' ORDER BY interpreter_reg.name ASC";
} else {
    $query = "SELECT interp_salary.is_paid,interp_salary.paid_date,interp_salary.ni_dedu,interp_salary.tax_dedu,interp_salary.payback_deduction,interp_salary.given_amount,interp_salary.interp,interp_salary.invoice,interp_salary.dated,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city,interpreter_reg.acntCode,interpreter_reg.acName,interpreter_reg.acNo,interp_salary.salry as net_total,interpreter_reg.work_type from interp_salary,interpreter_reg where interp_salary.interp=interpreter_reg.id and interp_salary.deleted_flag=0 $append_is_paid and (DATE(interp_salary.frm) BETWEEN ('$search_2') AND ('$search_3')) AND (DATE(interp_salary.todate) BETWEEN ('$search_2') AND ('$search_3')) ORDER BY interpreter_reg.name ASC";
}
$result = mysqli_query($con, $query);

$htmlTable = '';
$htmlTable .= '<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>';
$htmlTable .= '<h2 style="text-decoration:underline; text-align:center">Interpreters Paid Salary Report</h2>
<p> Date: ' . $misc->sys_date() . '</span>
<p>Date Range : ' . $misc->dated($search_2) . ' to ' . $misc->dated($search_3) . '</p>
</div>
<div>
<h3 style="text-align:center;" align="center">In House Interpreters</h3>
</div>
<table>
<thead>
<tr>
	<th style="background-color:#039;color:#FFF;">Sr.No</th>
	<th style="background-color:#039;color:#FFF;">Salary Slip No</th>
	<th style="background-color:#039;color:#FFF;">Linguistic</th>
	<th style="background-color:#039;color:#FFF;">Gender</th>
    <th style="background-color:#039;color:#FFF;">City</th>
    <th style="background-color:#039;color:#FFF;">Account Title</th>
    <th style="background-color:#039;color:#FFF;">Sort Code</th>
    <th style="background-color:#039;color:#FFF;">Account Number</th>
    <th style="background-color:#039;color:#FFF;">Paid Salary Date</th>
    <th style="background-color:#039;color:#FFF;">Payment Status</th>
    <th style="background-color:#039;color:#FFF;">Paid Date</th>
    <th style="background-color:#039;color:#FFF;">Paid Salary</th>';
while ($row = mysqli_fetch_assoc($result)) {
    if($row['work_type']=="in-house"){
        $total_salary = ($row['net_total'] - $row['ni_dedu'] - $row['tax_dedu'] - $row['payback_deduction']) + $row['given_amount'];
        $net_total += $total_salary;
        $htmlTable .= '<tr>';
        $htmlTable .= '<td>' . $i . '</td>';
        $htmlTable .= '<td>' . $row["invoice"] . '</td>';
        $htmlTable .= '<td>' . $row["name"] . '</td>';
        $htmlTable .= '<td>' . $row["gender"] . '</td>';
        $htmlTable .= '<td>' . $row["city"] . '</td>';
        $htmlTable .= '<td>' . $row["acName"] . '</td>';
        $htmlTable .= '<td>' . htmlentities($row["acntCode"]) . '</td>';
        $htmlTable .= '<td>' . $row["acNo"] . '</td>';
        $htmlTable .= '<td>' . $misc->dated($row["dated"]) . '</td>';
        $htmlTable .= '<td>' . ($row['is_paid'] == 1 ? "Paid" : "Unpaid") . '</td>';
        $htmlTable .= '<td>' . $misc->dated($row["paid_date"]) . '</td>';
        $htmlTable .= '<td>' . $total_salary . '</td>';
        $htmlTable .= '</tr>';
        $i++;
    }
}
$htmlTable .= '<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>Total</td>
    <td>' . round($net_total, 2) . '</td>
    </tr>
</table>';

$result2 = mysqli_query($con, $query);
$net_total=0;
$htmlTable .='<br><div>
<h3 style="text-align:center;" align="center">Freelance Interpreters</h3>
</div>
<table>
<thead>
<tr>
	<th style="background-color:#039;color:#FFF;">Sr.No</th>
	<th style="background-color:#039;color:#FFF;">Salary Slip No</th>
	<th style="background-color:#039;color:#FFF;">Linguistic</th>
	<th style="background-color:#039;color:#FFF;">Gender</th>
    <th style="background-color:#039;color:#FFF;">City</th>
    <th style="background-color:#039;color:#FFF;">Account Title</th>
    <th style="background-color:#039;color:#FFF;">Sort Code</th>
    <th style="background-color:#039;color:#FFF;">Account Number</th>
    <th style="background-color:#039;color:#FFF;">Paid Salary Date</th>
    <th style="background-color:#039;color:#FFF;">Payment Status</th>
    <th style="background-color:#039;color:#FFF;">Paid Date</th>
    <th style="background-color:#039;color:#FFF;">Paid Salary</th>';
while ($row2 = mysqli_fetch_assoc($result2)) {
    if($row2['work_type']!="in-house"){
        $total_salary = ($row2['net_total'] - $row2['ni_dedu'] - $row2['tax_dedu'] - $row2['payback_deduction']) + $row2['given_amount'];
        $net_total += $total_salary;
        $htmlTable .= '<tr>';
        $htmlTable .= '<td>' . $i . '</td>';
        $htmlTable .= '<td>' . $row2["invoice"] . '</td>';
        $htmlTable .= '<td>' . $row2["name"] . '</td>';
        $htmlTable .= '<td>' . $row2["gender"] . '</td>';
        $htmlTable .= '<td>' . $row2["city"] . '</td>';
        $htmlTable .= '<td>' . $row2["acName"] . '</td>';
        $htmlTable .= '<td>' . htmlentities($row2["acntCode"]) . '</td>';
        $htmlTable .= '<td>' . $row2["acNo"] . '</td>';
        $htmlTable .= '<td>' . $misc->dated($row2["dated"]) . '</td>';
        $htmlTable .= '<td>' . ($row2['is_paid'] == 1 ? "Paid" : "Unpaid") . '</td>';
        $htmlTable .= '<td>' . $misc->dated($row2["paid_date"]) . '</td>';
        $htmlTable .= '<td>' . $total_salary . '</td>';
        $htmlTable .= '</tr>';
        $i++;
    }
}
$htmlTable .= '<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>Total</td>
    <td>' . round($net_total, 2) . '</td>
    </tr>
</table>';

list($a, $b) = explode('.', basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=" . $a . ".xls");
echo $htmlTable;
