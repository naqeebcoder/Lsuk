<?php
include '../../action.php';
//Update Parking Starts
if(isset($_POST['update_parking'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id = $_POST['job_id'];
        $is_parking = $_POST['is_parking'];
        $parking_amount = $_POST['parking_amount']?$_POST['parking_amount']:0;
        $images = [];
        $i=0;
        if(isset($_POST['attachment']) && !empty($_POST['attachment'])){
            $decoded=is_object($_POST['attachment'])?json_decode($_POST['attachment']):$_POST['attachment'];
            foreach ($decoded as $value){
                $i++;
                $file_name = time().$i.'.png';
                $file = base64_decode($value);
                if(file_put_contents("../../../file_folder/parking_tickets/".$file_name, $file)){
                    array_push($images , $file_name);
                }
            }
            $check_existing=$obj->read_specific("parking_tickets","interpreter","id=".$job_id)["parking_tickets"];
            if(!empty($check_existing)){
                $existing_parking_tickets=json_decode($check_existing, true);
                foreach($existing_parking_tickets as $key=>$val){
                    $old_parking_file="../../../file_folder/parking_tickets/".$val;
                    if(file_exists($old_parking_file) && !empty($old_parking_file)){
                        unlink($old_parking_file);
                    }
                }
            }
            $get_data = $obj->read_specific("total_charges_interp", "interpreter", "id=".$job_id);
            $done=$obj->update('interpreter',array('otherCost' => $parking_amount,'total_charges_interp' => ($get_data['total_charges_interp']+$parking_amount),'is_parking'=>1,'parking_tickets'=>json_encode($images)),"id=".$job_id);
            if($done==1){
                $json->status="success";
                $json->msg="Parking attachments have been uploaded for this job. Thank you";
            }else{
                $json->status="failed";
                $json->msg="Failed to upload parking attachments for this job. Try again";
            }
        }else{
            $json->status="failed";
            $json->msg="You must upload parking attachments for this job. Try again";
        }
    }else{
        $json->status="failed";
        $json->msg="You must login to perform this action!";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>