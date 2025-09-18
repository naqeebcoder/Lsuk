<?php
if (isset($_POST['get_inv'])) {
    include 'actions.php';
    $porder=$_POST['get_inv'];
    $fetch_match = $obj->read_all("'Face To Face' as job_type,interpreter.nameRef,interpreter.invoiceNo,round(IFNULL(interpreter.total_charges_comp,0)+ IFNULL(interpreter.total_charges_comp * interpreter.cur_vat,0) +IFNULL(interpreter.C_otherexpns,0),2) as total_cost FROM interpreter where interpreter.porder='$porder' AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 AND interpreter.commit=1 and interpreter.invoic_date!='1001-01-01'  UNION ALL SELECT 'Telephone' as job_type,telephone.nameRef,telephone.invoiceNo,round(IFNULL(telephone.total_charges_comp,0)+ IFNULL(telephone.total_charges_comp * telephone.cur_vat,0),2) as total_cost FROM telephone WHERE telephone.porder='$porder' AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 AND telephone.commit=1 and telephone.invoic_date!='1001-01-01' UNION ALL SELECT 'Translation' as job_type,translation.nameRef,translation.invoiceNo,round(IFNULL(translation.total_charges_comp,0)+ IFNULL(translation.total_charges_comp * translation.cur_vat,0),2)  as total_cost","translation","translation.porder='$porder' AND translation.deleted_flag=0 and translation.order_cancel_flag=0 AND translation.commit=1 and translation.invoic_date!='1001-01-01'");
    $count=1;
    $tbl="";
    $tbl .= '<table class="table table-bordered tbl_data" cellspacing="0" cellpadding="0">
        <thead class="bg-primary">
            <tr>
                <td>Sr.No</td>
                <td>Job Type</td>
                <td>Job Ref</td>
                <td>Invoice Number</td>
                <td>Total Amount</td>
            </tr>
        </thead>
        <tbody>';
    if(mysqli_num_rows($fetch_match)>0){
        $tbl.="<h1 class='text-center'> Invoices Under Purchaser Order # $porder </h1>";
        while($row = mysqli_fetch_assoc($fetch_match)){
            $tbl.="<tr>
            <td>$count</td>
            <td>".$row['job_type']." </td>
            <td>".$row['nameRef']." </td>
            <td>".$row['invoiceNo']." </td>
            <td>".$row['total_cost']." </td>
            </tr>";
            $count++;
        }
    }else{
        $tbl.="<h1 class='text-center' style='color:#156c00;'> No invoices Found </h1>";
        $tbl.="<tr class='text-center'> <td colspan='10'> No Records </td></tr>";
    } 
    $tbl.='</tbody>
    </table>';
    $data=array();
    $data['body']=$tbl;
    $data['matches']=$count-1;
    echo json_encode($data);
    exit;
}
if(isset($_POST['shift_id'])){
    include 'actions.php';
    $shift_type = $_POST['shift_type'];
    $tbd = $_POST['tbd'];
    $table='';
    if($shift_type=="f2f"){
        $table="interpreter";
    }elseif($shift_type=="telephone"){
        $table="telephone";
    }elseif($shift_type=="translation"){
        $table="translation";
    }
    $change = $obj->editFun($table, $tbd, 'order_cancelledby', 'Client');
    echo "done";
    exit;
}