<?php
include '../action.php';
if(isset($_POST['missing_documents'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $get_docs=$obj->read_specific("CONCAT(CASE WHEN (applicationForm='') THEN 'applicationForm,' ELSE '' END ,CASE WHEN (agreement='') THEN 'agreement,' ELSE '' END,CASE WHEN (dbs_file='') THEN 'dbs,' ELSE '' END,CASE WHEN (id_doc_file='') THEN 'id_doc,' ELSE '' END,CASE WHEN (acNo='') THEN 'bank_details,' ELSE '' END ) as missed","interpreter_reg","id=".$_POST['ap_user_id'])['missed'];
        $array=explode(',',$get_docs);
        $items_array=array();
        foreach($array as $item){
            if(!empty($item)){
                array_push($items_array,$item);
            }
        }
        $json=$items_array;
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>