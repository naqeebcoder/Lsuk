<?php 
list($a,$b)=explode('.',basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=".$a.".xls");  
include '../../db.php';
include_once ('../../class.php'); 
?>

<?php 
    $org = @$_GET['org'];
    $search_2=@$_GET['search_2']; 
    $search_3=@$_GET['search_3']; 
    if(isset($org) && !empty($org)){
        $append_orgName_int=" and interpreter.orgName = '".$org."'";
        $append_orgName_tp=" and telephone.orgName = '".$org."'";
        $append_orgName_tr=" and translation.orgName = '".$org."'";
        $append_orgName_all=" and orgName = '".$org."'";
    }else{
        $append_orgName_int=" and comp_reg.name LIKE 'VHS%' ";
        $append_orgName_tp=" and comp_reg.name LIKE 'VHS%' ";
        $append_orgName_tr=" and comp_reg.name LIKE 'VHS%' ";
        $append_orgName_all=" and comp_reg.name LIKE 'VHS%' ";
    }
    if((isset($search_2) && !empty($search_2)) && (isset($search_3) && !empty($search_3))){
        $append_dates=" assignDate BETWEEN '$search_2' AND '$search_3' ";
    }else{
        $append_dates=" DATE(assignDate)=DATE(NOW() + INTERVAL 1 DAY) ";
    }

    $bkr = "SELECT * from (SELECT 'Face 2 Face' as type,'none' as communication_type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,interpreter.source,interpreter.target,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.dated FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_orgName_int AND interpreter.multInv_flag=0 AND interpreter.deleted_flag=0  and interpreter.order_cancel_flag=0 and interpreter.commit=0  and interpreter.orderCancelatoin=0  
    UNION ALL SELECT 'Telephone' as type,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type,telephone.id,telephone.intrpName,telephone.orgName,telephone.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.dated FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_orgName_tp AND telephone.multInv_flag=0 AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.commit=0 and telephone.orderCancelatoin=0
    UNION ALL SELECT 'Translation' as type,'none' as communication_type,translation.id,translation.intrpName,translation.orgName,translation.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,translation.source,translation.target,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.dated FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_orgName_tr AND translation.multInv_flag=0 AND translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.commit=0  and translation.orderCancelatoin=0 ) as grp WHERE $append_dates ORDER BY CONCAT(assignDate,' ',assignTime)";

        // $bkr = "SELECT * from (SELECT 'Face 2 Face' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,interpreter.source,interpreter.target,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.dated FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_orgName_int AND interpreter.multInv_flag=0 AND interpreter.deleted_flag=0  and interpreter.order_cancel_flag=0 and interpreter.commit=0  and interpreter.orderCancelatoin=0 
        // UNION ALL SELECT 'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.dated FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_orgName_tp AND telephone.multInv_flag=0 AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.commit=0 and telephone.orderCancelatoin=0
        // UNION ALL SELECT 'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,translation.source,translation.target,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.dated FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_orgName_tr AND translation.multInv_flag=0 AND translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.commit=0  and translation.orderCancelatoin=0 ) as grp ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT 10";
    // $bkr = "select id from interpreter LIMIT 10";

    $htmlTable ='<style>
        table {border-collapse: collapse; width:670px;}
        th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
        td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
        .bg-primary{background-color: #337ab7;color:white;}
        .bg-info{background-color: #d9edf7;}
        </style>
        <div>
        <h2 align="center"><u>Next Day Booking Report</u></h2>
        </div>

        <table>
        <thead>
        <tr>
            <th scope="col">Interp Name - Contact</th>
            <th scope="col">Type</th>
            <th scope="col">Source- Target</th>
            <th scope="col">Assignment Date and Time</th>
            <th scope="col">Organization</th>
            <th scope="col">Client Name</th>
            <th scope="col">Reference</th>
        </tr>
    </thead>
        <tbody>';
    $ex_bkr = mysqli_query($con,$bkr);
    while($bk_row = mysqli_fetch_assoc($ex_bkr)){
    $htmlTable.="<tr><td>".$bk_row['int_name']."<br>".$bk_row['int_cont']."<br>".$bk_row['int_email']."</td><td>".($bk_row['communication_type']!="none"?"Remote - ".$bk_row['communication_type']:$bk_row['type'])."</td><td>".$bk_row['source']." to ".$bk_row['target']."</td><td>".$bk_row['assignDate']." ".$bk_row['assignTime']."</td><td>".$bk_row['orgName']."</td><td>".$bk_row['orgContact']."</td><td>".$bk_row['orgRef']."</td></tr>";
    }
    $htmlTable.='</tbody></table>';

    echo $htmlTable;
                ?>