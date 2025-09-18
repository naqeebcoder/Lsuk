<?php
include 'db.php';
include 'class_new.php';
$today=date('Y-m-d');
$yesterday = (new DateTime())->modify('-1 day')->format('Y-m-d');
$obj_new->delete("app_notifications","dated<'".$today."' AND (type_key='nj' OR (type_key='ja' AND read_ids='') OR (type_key='jc' AND read_ids=''))");
$obj_new->update_custom("notify_new_doc",array("new_notification"=>0),"1");
$query=$obj_new->read_all("*","app_notifications","1");
while($row = $query->fetch_assoc()){
    $read_ids=explode(',',$row['read_ids']);
    if(!in_array($row['int_ids'],$read_ids)){
        $row['read']='1';
    }else{
        $row['read']='0';
        $existing_notification=$obj_new->read_specific("new_notification","notify_new_doc","interpreter_id=".$row['int_ids'])['new_notification'];
        $obj_new->update("notify_new_doc",array("new_notification"=>$existing_notification+1),array("interpreter_id"=>$row['int_ids']));
    }
}
// Add celebration flag for lsuk system users
$get_allocation_users = $obj_new->read_all("*", "login", "user_status=1 AND prv='Operator' AND is_allocation_member=1");
while ($row_user = $get_allocation_users->fetch_assoc()) {
    if ($row_user['celebrate'] == 1) {
        continue;
    }
    $get_assigned_jobs = $obj_new->read_specific("COUNT(*) AS jobs", "assigned_jobs_users", "user_id = " . $row_user['id'] . " AND DATE(assigned_date) = '" . $yesterday . "'")['jobs'];
    $get_allocated_jobs = $obj_new->read_specific("COUNT(*) AS allocated", "daily_logs", "action_id=5 AND DATE(dated) = '" . $yesterday . "' AND user_id=" . $row_user['id'])['allocated'];
    if ($get_assigned_jobs > 0) {
        if ($get_assigned_jobs <= $get_allocated_jobs) {
            $obj_new->update_custom("login", array("celebrate" => 1), "id=" . $row_user['id']);
        }
    }
}
?>