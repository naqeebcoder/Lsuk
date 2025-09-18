<?php
include '../action.php';
//get interpreter documents request
if(isset($_POST['show_all_documents']) && !isset($_POST['ap_doc_id'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $obj->update("notify_new_doc",array("status"=>1),"interpreter_id=".$_POST['ap_user_id']);
        $result = $obj->read_all("post_format.id,post_format.em_type as title,post_format.em_date as date,notify_new_doc_data.cities,notify_new_doc_data.languages,notify_new_doc_data.interpreters","post_format,notify_new_doc_data","post_format.id=notify_new_doc_data.post_id AND post_format.status='Active' ORDER by post_format.id DESC");
        $json=array();
        while($row = $result->fetch_assoc()){
            if(empty($row['interpreters']) ||(!empty($row['interpreters']) && in_array($_POST['ap_user_id'], explode(',',$row['interpreters'])))){
                unset($row['interpreters']);
                unset($row['cities']);
                unset($row['languages']);
                array_push($json,$row);
            }
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>