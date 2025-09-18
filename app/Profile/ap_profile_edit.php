<?php
include '../action.php';
//profile edit
if(isset($_POST['ap_profile_edit'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $ap_user_id=$_POST['ap_user_id'];
        if(isset($_POST['ap_photo']) && !empty($_POST['ap_photo'])){
            $image_base64 = base64_decode($_POST['ap_photo']);
            $image_type = $_POST['ap_type'];
            $old_photo=$obj->read_specific("interp_pix","interpreter_reg","id=".$ap_user_id)['interp_pix'];
            $old_file='../../lsuk_system/file_folder/interp_photo/'.$old_photo;
            if(file_exists($old_file) && !empty($old_photo)){
                unlink($old_file);
            }
            $file = round(microtime(true)).$image_type;
            if(file_put_contents("../../lsuk_system/file_folder/interp_photo/".$file, $image_base64)){
                $obj->editFun("interpreter_reg",$ap_user_id,'interp_pix',$file);
				$obj->editFun("interpreter_reg",$ap_user_id,'pic_updated',1);
                $json->msg="photo_updated";
            }else{
                $json->msg="photo_failed";
            }
        }
        if(!isset($_POST['ap_photo']) && (isset($_POST['ap_name']) || isset($_POST['ap_email']) || isset($_POST['ap_contact']))){
                if(isset($_POST['ap_name'])){
                    $obj->editFun("interpreter_reg",$ap_user_id,'name',$_POST['ap_name']);
                }
                if(isset($_POST['ap_email'])){
                    $obj->editFun("interpreter_reg",$ap_user_id,'email',$_POST['ap_email']);
                }
                if(isset($_POST['ap_contact'])){
                    $obj->editFun("interpreter_reg",$ap_user_id,'contactNo',$_POST['ap_contact']);
                }
                if(isset($_POST['ap_contact2'])){
                    $obj->editFun("interpreter_reg",$ap_user_id,'contactNo2',$_POST['ap_contact2']);
                }
                $json->msg="profile_updated";
        }
        if(isset($_POST['ap_password_update'])){
            $ap_old_password=$_POST['ap_old_password'];
            $ap_new_password=$_POST['ap_new_password'];
            $db_old_password=$obj->read_specific("password","interpreter_reg","id=".$ap_user_id)['password'];
            if($db_old_password==$ap_old_password){
                    if(isset($ap_new_password) && $obj->editFun("interpreter_reg",$ap_user_id,'password',$ap_new_password)){
                        $json->status="1";
                        $json->msg="your passord has been updated successfully.";
                    }else{
                        $json->status="0";
                        $json->msg="Failed to update your passord ! Try again";
                    }
            }else{
                $json->status="0";
                $json->msg="Wrong old passord entered. try the valid one";
            }
        }
        if(isset($_POST['language_update'])){
            $languages=json_decode($_POST['languages']);
            $array_level=array('Native'=>'1','Fluent'=>'2','Intermediate'=>'3','Basic'=>'4');
            $lang_update=0;
            foreach($languages as $key){
                $language_id=$key->id;
                $level_name=trim($key->level);
                $obj->update("interp_lang",array('level'=>$array_level[$level_name],"updated_via"=>1),"id=".$language_id);
                $lang_update=1;
            }
            if($lang_update==1){
                $json->status="1";
                $json->msg="Proficiencies in selected languages have been updated.";
            }else{
                $json->status="0";
                $json->msg="Failed to update proficiencies in selected languages!";
            }
        }
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?>