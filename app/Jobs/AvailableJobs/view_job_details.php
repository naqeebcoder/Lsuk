<?php
include '../../action.php';
//get bidding request
if(isset($_POST['view_job_details']) && isset($_POST['ap_view']) && isset($_POST['ap_value'])){
    $json=(object) null;
    if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
        $job_id=trim($_POST['ap_view'],'"');
        $table=@$_POST['ap_value'];
        if($table!='translation'){
            if($table=='interpreter'){
                $table_name="'Face To Face' as job_type";
                $query_details="$table.assignDate,substr($table.assignTime,1,5) as assignTime,$table.assignDur,$table.guess_dur,$table.assignCity,$table.buildingName,$table.street,(SELECT interp_cat.ic_title from interp_cat WHERE interp_cat.ic_id=interpreter.interp_cat) as category,interp_type as sub_category,'' as transType,assignIssue as remarks,interp_cat,$table.postCode,intrpName,postcode_data";
            }else{
                $table_name="'".ucwords($table)."' as job_type";
                $query_details="$table.assignDate,$table.assignTime,$table.assignDur,$table.guess_dur,(SELECT telep_cat.tpc_title from telep_cat WHERE telep_cat.tpc_id=telephone.telep_cat) as category,telep_type as sub_category,'' as transType,assignIssue as remarks,telep_type as interp_cat";
            }
        }else{
            $table_name="'".ucwords($table)."' as job_type";
            $query_details="$table.asignDate as assignDate,(SELECT trans_cat.tc_title from trans_cat WHERE trans_cat.tc_id=docType) as category,'' as sub_category,docType as interp_cat,transType,trans_detail,DATE_FORMAT(deliverDate2,'%d-%m-%Y') as delivery_date";
            $put=" ";
        }
        $json=array();
        $row = $obj->read_specific("$table_name,comp_reg.name as company_name,$table.id as job_id,$table.nameRef as job_key,$table.source,$table.target,$query_details,remrks","$table,comp_reg","$table.orgName=comp_reg.abrv AND $table.id=".$job_id);
        if($table=="interpreter"){
            $title_string="it_title";
            $table_string="interp_types";
            $id_string="it_id";
        }else if($table=="telephone"){
            $title_string="tpt_title";
            $table_string="telep_types";
            $id_string="tpt_id";
        }else{
            $title_string="tt_title";
            $table_string="trans_types";
            $id_string="tt_id";
        }
        if($table!="translation"){
            if(($table=="interpreter" && $row['interp_cat']!='12') || ($table=="telephone" && $row['interp_cat']!='11')){
                $row['category_details']=$obj->read_specific("GROUP_CONCAT(CONCAT(".$title_string.")  SEPARATOR ' , ') as title",$table_string,"$id_string IN (".$row['sub_category'].")")['title'];
            }else{
                $row['category_details']=$row['remarks']?:"Other";
            }
        }else{
            $row['category_details']=$obj->read_specific("GROUP_CONCAT(trans_types.tt_title SEPARATOR ', ') as category","trans_types","trans_types.tt_id IN (".$row['trans_detail'].")")['category']?:$row['remarks'];
        }
        $assignDur=$row['assignDur'];
            if($assignDur>60){
                $hours=$assignDur / 60;
                if(floor($hours)>1){$hr="hours";}else{$hr="hour";}
                $mins=$assignDur % 60;
                if($mins==00){
                    $get_dur=sprintf("%2d $hr",$hours);  
                }else{
                    $get_dur=sprintf("%2d $hr %02d minutes",$hours,$mins);  
                }
            }else if($assignDur==60){
                $get_dur="1 Hour";
            }else{
                $get_dur=$assignDur." minutes";
            }
            $guess_dur=$row['guess_dur'];
            if($assignDur!=$guess_dur && $guess_dur>0){
              if($guess_dur>60){
                  $guess_hours=$guess_dur / 60;
                  if(floor($guess_hours)>1){$guess_hr="hours";}else{$guess_hr="hour";}
                  $guess_mins=$guess_dur % 60;
                  if($guess_mins==0){
                      $get_guess_dur=sprintf("%2d $guess_hr",$guess_hours);  
                  }else{
                      $get_guess_dur=sprintf("%2d $guess_hr %02d minutes",$guess_hours,$guess_mins);  
                  }
              }else if($guess_dur==60){
                  $get_guess_dur="1 Hour";
              }else{
                  $get_guess_dur=$guess_dur." minutes";
              }
            }
            if($assignDur!=$guess_dur && $guess_dur>0){
              $row['html_notes']="This session is booked for ".$get_dur.", however it can take  up to ".$get_guess_dur." or longer.<br>
              Therefore please consider your unrestricted availability before bidding / accepting this job.
              In cases of short notice cancellation, you will be paid the booked time (".$get_dur.").<br>";
              if(!empty($row['remrks'])){
                  $row['html_notes'].=$row['remrks'];
              }
            }else{
                $row['html_notes']=!empty($row['remrks'])?$row['remrks']:"";
            }
            $get_dur=$row['job_type']=='Translation'?'no_display':$get_dur;
            $row['assignDur']=$get_dur;
            $row['bid']=$obj->read_specific("bid_type","bid","interpreter_id=".$_POST['ap_user_id']." AND job=".$job_id." AND tabName='".$table."'")["bid_type"]?:0;
            if($row['bid']==1){
                $row['bid_label'] = "Applied";
            }elseif($row['bid']==3){
                $row['bid_label'] = "Alternate Availability Given";
            }elseif($row['bid']==0){
                $row['bid_label'] = NULL;
            }
            unset($row['interp_cat']);unset($row['remarks']);
            if(!empty($row['intrpName']) && $table=='interpreter'){
                if($row['postCode'] && !empty($row['postcode_data'])){
                $postcode_data=explode(',',$row['postcode_data']);
                $row['latitude']=$postcode_data[0];
                $row['longitude']=$postcode_data[1];
            }else{
                $row['api_key']="JwJX4MXnkEihbeI4wAPTIg14351";
                $row['latitude']="";
                $row['longitude']="";
            }
            }
            $row['assignDate']=$misc->dated($row['assignDate']);
            $row['assignCity']=$table=="interpreter"?$row['assignCity']:"no_display";
            $row['address']=$table=="interpreter"?$row['buildingName'].' '.$row['street'].' '.$row['assignCity']:"no_display";
            array_push($json,$row);
    }else{
        $json->msg="not_logged_in";
    }
    header('Content-Type: application/json');
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
}
?> 