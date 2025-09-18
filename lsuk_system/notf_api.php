<?php

include '../source/setup_email.php';
include 'db.php';
include 'actions.php';

// if(isset($_GET['test_notification'])){
//     // $json=(object) null;
//     // $json->sent=0;
//     $type_key="nj";
//     $job_type="Telephone";
//     $notification_id=135;
//     $subject="New ".$job_type." Interpreting Project 9545";
//     $subject2="Mark your availability for Thursday";
//     $sub_title=$job_type." Interpreting job of Arabic language on 12-11-2021 at 12:45:00 is available for you to bid.";
//     $array_tokens=explode(',',$obj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens","int_tokens","int_id=874")['tokens']);
//     $obj->update("notify_new_doc",array("new_notification"=>1),"interpreter_id=874");
//     $availability_note="Good morning!\nCan you mark your presence for the day. This doesn't guarantee a job but makes easy for LSUK to allocate a job.\nThank you";
//     if(!empty($array_tokens)){
//         foreach($array_tokens as $token){
//             if(!empty($token)){
//                 // $acttObj->notify("eSOavM79QBStpGm0KKhp74:APA91bHlmQoiugBTRN-DL8bKWnSKYfCRdJ1qQorxWFfe7DANmA1cBU4jlggFEJOtvy7HCcNOEKL9t7jzAFsQIIoU60n_OGl4dPaGfHAUzOiG7YV44J5uvkuZF_oA-3pRUp6MVnv_HlEf", "Notification Test", "Mobile app notification test", array("type_key" => "nj", "job_type" => "Telephone"));
//                 $obj->notify_test($token,"🔔 ".$subject,$availability_note,array("type_key"=>$type_key,"job_type"=>$job_type));
//                 // $json->sent=1;
//             }
//         }
//     }
//     // header('Content-Type: application/json');
//     // echo json_encode($json, JSON_UNESCAPED_UNICODE);
// }

$obj->notify_test("fvXEaHXew0PinBYODvqFMC:APA91bFEnnx0a70aYIBVUy8ea8tz5vMg61GnDwLzWIqj-fYQiH6OT8NA5whu5dNpPpdmSCB-52wvSZQGF8MofJ2phTMxQejXkz-ZIK1DdOG3-CNfveWt90pmULjiZ6u-vKouGfHBzZuf", "Notification Test", "Mobile app notification test", array("type_key" => "nj", "job_type" => "Telephone"));

// $acttObj->notify("d69vLfPnFED2sWWEALTBam:APA91bECG_evQDtHevpbz1LZAwdk_4nOpYhqZ-Og9-1_ANjuFSlJnbbyUbLfX2FZnc1YnI1Qlq3PbishAyLW6s8hwCY45f_zRTJCAXdqwf_C0G_Jepf_RgkZejBaOYkD7g1b1R3yYai9", "Notification Test", "Mobile app notification test", array("type_key" => "nj", "job_type" => "Telephone"));
