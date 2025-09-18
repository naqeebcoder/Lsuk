<?php
include '../action.php';
//View all tickets
if(isset($_POST['view_tickets'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $query_tickets=$obj->read_all("title,dated,(CASE WHEN status=0 THEN 'Pending' ELSE 'Resolved' END) as status","tickets","interpreter_id=".$_POST['ap_user_id']);
        $json=array();
        while($row = $query_tickets->fetch_assoc()){
            array_push($json,$row);
        }
        if(count($json)==0){
            $json->msg="no_tickets";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>