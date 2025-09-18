<?php use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require '../../lsuk_system/phpmailer/vendor/autoload.php';
include '../action.php';
include '../db.php';
// error_reporting(E_ALL);

//app token creation
if(isset($_POST['device_id']) && isset($_POST['add_token']) && isset($_POST['user_id'])){
    $json=(object) null;
    if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
        $token_done=0;
		$idz=$obj->read_specific("GROUP_CONCAT(id) as idz","int_tokens","device_id='".$_POST['device_id']."' OR token='".$_POST['add_token']."' OR (int_id=".$_POST['user_id']." and dated < DATE_SUB(NOW(), INTERVAL 30 DAY))")["idz"];
        if(!is_null($idz)){
			$obj->delete("int_tokens","id IN (".$idz.")");
		}
        $obj->insert("int_tokens",array("device_id"=>$_POST['device_id'],"int_id"=>$_POST['user_id'],"token"=>$_POST['add_token'],"dated"=>date('Y-m-d h:i:s')));
        $token_done=1;
        if($token_done==1){
            $json->msg="token_success";
        }else{
            $json->msg="token_failed";
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//app update token
if(isset($_POST['update_token']) && isset($_POST['token']) && isset($_POST['device_id'])){
    $json=(object) null;
    if($obj->update("int_tokens",array("token"=>$_POST['token'],"dated"=>date('Y-m-d h:i:s')),"device_id='".$_POST['device_id']."'")){
        $json->msg="token_updated";
    }else{
        $json->msg="token_not_updated";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//app delete token
if(isset($_POST['delete_token']) && isset($_POST['device_id'])){
    $json=(object) null;
    
    if($obj->delete("int_tokens","device_id='".$_POST['device_id']."'")){
        $json->msg="token_deleted";
    }else{
        $json->msg="token_not_deleted";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
//get active jobs request
if(isset($_POST['show_jobs']) && isset($_POST['role'])){
    $json=(object) null;
    if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
        $user_id=trim($_POST['user_id']);
        $table = "telephone";
        $put_id="AND $table.id IN (3288,33404,33405,33406)";
        $query_details="$table.assignDate,$table.assignTime,$table.assignDur,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as meeting_title,telephone.fn_tm";
        $result = $obj->read_all("$table.id as job_id,$table.nameRef as meeting_id,$table.source,$table.target,$query_details","$table,interpreter_reg","$table.intrpName=interpreter_reg.id AND $table.deleted_flag=0 and $table.order_cancel_flag=0 and $table.jobStatus= 1 and $table.intrpName= '$user_id' and $table.salary_id=0 and $table.hrsubmited='' and $table.fn_tm = '1001-01-01 00:00:00' $put_id ORDER BY assignDate ASC");
        $json=array();
        while($row = $result->fetch_assoc()){
            $row['meeting_id'] = strtoupper(str_replace("/", "", $row['meeting_id']));
            $assignDate = $row['assignDate'];
            $assignTime = $row['assignTime'];
            $assignDur=$row['assignDur'];
            $assignDurDouble = ($assignDur * 2);
            if($assignDur>60){
                $hours=$assignDur / 60;
                if(floor($hours)>1){
                    $hr="hours";
                }else{
                    $hr="hour";
                }
                $mins=$assignDur % 60;
                if($mins==0){
                    $get_dur=sprintf("%2d $hr",$hours);  
                }else{
                    $get_dur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
                }
            }else if($assignDur==60){
                $get_dur="1 Hour";
            }else{
                $get_dur=$assignDur." minutes";
            }
            $expected_start = date($assignDate.' '.substr($assignTime,0,5));
            $expected_end = date("Y-m-d H:i",strtotime("+$assignDur minutes", strtotime($expected_start)));
            $expected_end_double = date("Y-m-d H:i", strtotime("+$assignDurDouble minutes", strtotime($expected_start)));
            $row['expected_start']=date("d-m-Y H:i", strtotime($expected_start));
            $row['expected_end']=date("d-m-Y H:i", strtotime($expected_end));
            $row['assignDur']=$get_dur;
            $row['assignDate']=$misc->dated($row['assignDate']);
            if ($expected_end_double < date("Y-m-d H:i:s")) {
                continue;
            } else {
                unset($row['fn_tm']);
                array_push($json,$row);
            }
        }
    }else{
        $json->status="failed";
        $json->msg="User ID must be provided";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
