<?php
include 'actions.php';
$get_intrp = $obj->read_all("interpreter_reg.id","interpreter_reg"," interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.deleted_flag=0");
if(mysqli_num_rows($get_intrp)>0){
    while($row_interp = mysqli_fetch_assoc($get_intrp)){
        $activeNeverInvited = 0;
        $availableNonActive = 0;
        $int_jobs = $obj->read_specific("sum(tot_jobs) as jobs","(SELECT count(interpreter.id) as tot_jobs from interpreter WHERE interpreter.intrpName=".$row_interp['id']." and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 UNION ALL SELECT count(telephone.id) as tot_jobs from telephone WHERE telephone.intrpName =".$row_interp['id']." and telephone.deleted_flag=0 and telephone.order_cancel_flag=0 UNION ALL SELECT count(translation.id) as tot_jobs from translation"," translation.intrpName =".$row_interp['id']." and translation.deleted_flag=0 and translation.order_cancel_flag=0) as grp")['jobs'];
        $reg_3months = $obj->read_specific("id","interpreter_reg"," id=".$row_interp['id']." AND TIMESTAMPDIFF(MONTH, reg_date, CURRENT_DATE())>=3")['id']?:0;
        $invite_3months = $obj->read_specific("id","job_messages"," interpreter_id=".$row_interp['id']." AND TIMESTAMPDIFF(MONTH, created_date, CURRENT_DATE())<3")['id']?:0;
        if ($int_jobs==0 && $reg_3months!=0 && $invite_3months==0) {
        $activeNeverInvited = 1;
        }

        $int_tot_jobs = $obj->read_specific("sum(tot_jobs) as jobs","(SELECT count(interpreter.id) as tot_jobs from interpreter WHERE interpreter.intrpName=".$row_interp['id']." and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 UNION ALL SELECT count(telephone.id) as tot_jobs from telephone WHERE telephone.intrpName =".$row_interp['id']." and telephone.deleted_flag=0 and telephone.order_cancel_flag=0 UNION ALL SELECT count(translation.id) as tot_jobs from translation"," translation.intrpName =".$row_interp['id']." and translation.deleted_flag=0 and translation.order_cancel_flag=0) as grp")['jobs'];
        $int_jobs = $obj->read_specific("sum(tot_jobs) as jobs","(SELECT count(interpreter.id) as tot_jobs from interpreter WHERE interpreter.intrpName=".$row_interp['id']." AND TIMESTAMPDIFF(MONTH, interpreter.assignDate, CURRENT_DATE())<3 and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 UNION ALL SELECT count(telephone.id) as tot_jobs from telephone WHERE telephone.intrpName =".$row_interp['id']." AND TIMESTAMPDIFF(MONTH, telephone.assignDate, CURRENT_DATE())<3 and telephone.deleted_flag=0 and telephone.order_cancel_flag=0 UNION ALL SELECT count(translation.id) as tot_jobs from translation"," translation.intrpName =".$row_interp['id']." AND TIMESTAMPDIFF(MONTH, translation.asignDate, CURRENT_DATE())<3 and translation.deleted_flag=0 and translation.order_cancel_flag=0) as grp")['jobs'];
        $reg_3months = $obj->read_specific("id","interpreter_reg"," id=".$row_interp['id']." AND TIMESTAMPDIFF(MONTH, reg_date, CURRENT_DATE())>=3")['id']?:0;
        $invite_3months = $obj->read_specific("id","job_messages"," interpreter_id=".$row_interp['id']." AND message_category IN (1,6) AND status=2 AND TIMESTAMPDIFF(MONTH, created_date, NOW())<3")['id']?:0;
        if ($int_tot_jobs>0 && $int_jobs==0 && $reg_3months!=0 && $invite_3months==0) {
        $availableNonActive = 1;
        }
        
        echo "intrp id : ".$row_interp['id']."<br>activeNeverInvited: $activeNeverInvited<br>availableNonActive: $availableNonActive<br>";

        $obj->update('interpreter_reg', array("is_activeNeverInvited" => $activeNeverInvited, "is_availableNonActive" => $availableNonActive), " interpreter_reg.id=" . $row_interp['id']);
    }
}
?>
