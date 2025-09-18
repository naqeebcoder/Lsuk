<?php
include '../action.php';
//get interpreter salaries records
if(isset($_POST['ap_salary'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $ap_user_id=$_POST['ap_user_id'];
        //$get_dated=$_POST["ap_date"];
        $table='interp_salary';$append_date="";
        /*if(isset($get_dated) && !empty($get_dated)){
            $append_date="and dated='$get_dated'";
        }*/
        $one_year_ago = date('Y-m-d', strtotime('-1 year'));
        $query_salary=$obj->read_all("$table.ni_dedu,$table.tax_dedu,$table.payback_deduction,$table.given_amount,$table.invoice as 'invoice_number',$table.frm as 'invoice_from',$table.todate as 'invoice_to',$table.deduction,$table.salry as 'salary',$table.salary_date as 'paid_date'","$table","$table.dated > '" . $one_year_ago . "' and deleted_flag=0 and interp=".$ap_user_id." ORDER BY id DESC");
        $json=array();
        while($row = $query_salary->fetch_assoc()){
            $row['salary'] = $misc->numberFormat_fun(($row['salary'] - $row['ni_dedu'] - $row['tax_dedu'] - $row['payback_deduction']) + $row['given_amount']);
            $row['invoice_from'] = $misc->dated($row['invoice_from']);
            $row['invoice_to'] = $misc->dated($row['invoice_to']);
            $row['paid_date'] = $misc->dated($row['paid_date']);
            array_push($json,$row);
        }
        if(count($json)==0){
            $json->msg="no_salary_slips";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>