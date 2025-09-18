<?php session_start();

if(isset($_POST['order_id']) && isset($_POST['order_type']) && isset($_POST['view_order_details'])){
    $view_id=$_POST['order_id'];
    $table=$_POST['order_type'];
    include 'source/db.php';
    include 'source/class.php';
    $query=$_POST['order_type']!='telephone'?"SELECT ".$_POST['order_type'].".*,comp_reg.buildingName as c_buildingName,comp_reg.line1 as c_line1,comp_reg.line2 as c_line2,comp_reg.streetRoad as c_streetRoad,comp_reg.city as c_city,comp_reg.postCode as c_postCode FROM ".$_POST['order_type'].",comp_reg where ".$_POST['order_type'].".orgName=comp_reg.abrv AND ".$_POST['order_type'].".id=$view_id":"SELECT ".$_POST['order_type'].".*,comunic_types.*,comp_reg.buildingName as c_buildingName,comp_reg.line1 as c_line1,comp_reg.line2 as c_line2,comp_reg.streetRoad as c_streetRoad,comp_reg.city as c_city,comp_reg.postCode as c_postCode FROM telephone,comp_reg,comunic_types WHERE ".$_POST['order_type'].".orgName=comp_reg.abrv AND ".$_POST['order_type'].".comunic=comunic_types.c_id AND ".$_POST['order_type'].".id=$view_id";
    $result = mysqli_query($con,$query);
    $row = mysqli_fetch_array($result);
    $source=$row['source'];$target=$row['target'];
    $assignDate=$_POST['order_type']!='translation'?$row['assignDate']:$row['asignDate'];
    $assignTime=$row['assignTime'];$assignDur=$row['assignDur'];
    $nameRef=$row['nameRef'];$noClient=$row['noClient'];$contactNo=$row['contactNo'];
    $inchPerson=$row['inchPerson'];$inchContact=$row['inchContact'];$inchEmail=$row['inchEmail'];$inchEmail2=$row['inchEmail2'];
    $inchNo=$row['inchNo'];$line1=$row['line1'];$line2=$row['line2'];$inchRoad=$row['inchRoad'];
    $inchCity=$row['inchCity'];$inchPcode=$row['inchPcode'];$orgName=$row['orgName'];
    $orgRef=$row['orgRef'];$orgContact=$row['orgContact'];$remrks=$row['remrks'];
    $gender=$row['gender'];$jobStatus=$row['jobStatus'];
    $bookinType=$row['bookinType'];$I_Comments=$row['I_Comments'];$comunic=$row['comunic'];$c_title=$row['c_title'];$c_image=$row['c_image'];
    $assignIssue=$row['assignIssue'];$jobDisp=$row['jobDisp'];
    $invoiceNo=$row['invoiceNo'];$bookedVia=$row['bookedVia'];
    $docType=$row['docType'];$transType=$row['transType'];$trans_detail=$row['trans_detail'];$deliverDate=$row['deliverDate'];$deliverDate_int=$row['deliverDate2'];$deliveryType=$row['deliveryType'];
    $dbs_checked=$row['dbs_checked'];$buildingName=$row['buildingName'];
    $street=$row['street'];$assignCity=$row['assignCity'];$postCode=$row['postCode'];  $c_buildingName=$row['c_buildingName'];$c_line1=$row['c_line1'];
    $c_line2=$row['c_line2'];$c_streetRoad=$row['c_streetRoad'];
    $c_city=$row['c_city'];$c_postCode=$row['c_postCode'];
    $disp_org=$acttObj->read_specific("name","comp_reg","abrv='".$orgName."'");
    $tbl.='<table class="table table-bordered table-hover">
            <tbody><tr><td colspan="2" class="text-center bg-info"><b>ASSIGNMENT DETAILS</b></td></tr>
                <tr>
                    <td width="45%">Source Language</td>
                    <td class="text-right"><span class="label label-default" style="font-size:16px;">'.$source.'</span></td>
                </tr>
                <tr>
                    <td>Target Language</td>
                    <td class="text-right"><span class="label label-success" style="font-size:16px;">'.$target.'</span></td>
                </tr>
                <tr>
                    <td>Assignment Date</td>
                    <td class="text-right">'.$assignDate.'</td>
                </tr>';
                if($_POST['order_type']!='translation'){
                $tbl.='<tr>
                    <td>Assignment Time</td>
                    <td class="text-right">'.$assignTime.'</td>
                </tr>
                <tr>
                    <td>Assignment Duration</td>
                    <td class="text-right">'; 
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
                      $tbl.=$get_dur;
                    $tbl.='</td>
                </tr>';
                }
                if($_POST['order_type']=='translation'){
                $tbl.='
                <tr>
                    <td>Document Type</td>
                    <td class="text-right">'.$acttObj->read_specific("tc_title","trans_cat","tc_id=".$docType)['tc_title'].'</td>
                </tr>
                <tr>
                    <td>Translation Type</td>
                    <td class="text-right">'.$acttObj->read_specific("CONCAT(GROUP_CONCAT(CONCAT('{',td_title)  SEPARATOR '} '),'}') as td_title","trans_dropdown","td_id IN (".$transType.")")['td_title'].'</td>
                </tr>';
                if(!empty($trans_detail)){
                $tbl.='<tr>
                    <td>Translation Details</td>
                    <td class="text-right">'.$acttObj->read_specific("CONCAT(GROUP_CONCAT(CONCAT('{',tt_title)  SEPARATOR '} '),'}') as tt_title","trans_types","tt_id IN (".$trans_detail.")")['tt_title'].'</td>
                </tr>';
                }
                $tbl.='<tr>
                    <td>Delivery Type</td>
                    <td class="text-right">'.$deliveryType?:'NIL'.'</td>
                </tr>';
                }
                $tbl.='<tr>
                    <td>Our Reference</td>
                    <td class="text-right">'.$nameRef?:'NIL'.'</td>
                </tr>';
                if($_POST['order_type']!='translation'){
                       if($_POST['order_type']=='telephone'){
                        $tbl.='<tr>
                            <td>Type</td>
                            <td class="text-right"><img style="display: inline-block;" width="30" src="lsuk_system/images/comunic_types/'.$c_image.'"/> '.$c_title.'</td>
                        </tr>';
                        }
                        $category=$_POST['order_type']=='telephone'?$acttObj->read_specific("tpc_title","telep_cat","tpc_id=".$row['telep_cat'])['tpc_title']:$acttObj->read_specific("ic_title","interp_cat","ic_id=".$row['interp_cat'])['ic_title'];
                $tbl.='<tr>
                    <td>Assignment Category</td>
                    <td class="text-right text-danger">'.$category.'</td>
                </tr>
                <tr>
                    <td>Assignment Details</td>
                    <td class="text-right text-danger">';
                    if($_POST['order_type']=='telephone'){
                        $str=$row['telep_cat']=='11'?$assignIssue:$acttObj->read_specific("GROUP_CONCAT(CONCAT(tpt_title)  SEPARATOR ' <b> & </b> ') as tpt_title","telep_types","tpt_id IN (".$row['telep_type'].")")['tpt_title'];
                    }else{
                        $str=$row['interp_cat']=='12'?$assignIssue:$acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title","interp_types","it_id IN (".$row['interp_type'].")")['it_title'];
                    }
                    $tbl.=$str;
                    $tbl.='</td>
                </tr>';
                }
                if($_POST['order_type']=='interpreter'){
                $tbl.='<tr>
                    <td class="text-center" colspan="2" title="Building No / Name"><i class="fa fa-map-marker"></i> <b>'.$buildingName?:'NIL'.'</td>
                </tr>
                <tr>
                    <td class="text-left" colspan="2" title="Street / Road Address"><i class="fa fa-road"></i> <b>Street/Road:</b>'.$street.'</td>
                </tr>
                <tr><td class="text-left" colspan="2" title="City Name"><i class="fa fa-road"></i> <b>City:</b>'.$assignCity.'</td>
                </tr>
                <tr>
                    <td class="text-left" colspan="2" title="Post Code"><i class="fa fa-map-pin"></i> <b>Post Code:</b>'.$postCode.'</td>
                </tr>';
                }
            $tbl.='</tbody>
        </table>';
        echo $tbl;
}
if(isset($_POST['order_id']) && isset($_POST['order_type']) && isset($_POST['job_action'])){
    $view_id=$_POST['order_id'];
    $job_table=$_POST['order_type'];
    $job_action=$_POST['job_action'];
    include 'source/db.php';
    include 'source/class.php';
    $query=$job_action=="delete"?"UPDATE $job_table SET deleted_flag=1,deleted_by='Online - Company Manager' WHERE id=$view_id":"UPDATE $job_table SET approve_portal_mngt=1 WHERE id=$view_id";
    $result = mysqli_query($con,$query);
    if(!$result){
        echo "not updated";
    }else{
        echo "updated";
    }
    exit;
}
if(isset($_POST['order_id']) && isset($_POST['value_type']) && isset($_POST['order_type'])){
    $order_id=$_POST['order_id'];
    $order_type=$_POST['order_type'];
    $value_type=$_POST['value_type'];
    include 'source/db.php';
    include 'source/class.php';
    $time=date('Y-m-d H:i');
    if($value_type=='start_time'){
        $acttObj->update("$order_type",array('st_tm'=>$time,'tm_by'=>'i'),array('id'=>$order_id));
        $data['msg']='<tr><td>Assignment starting time from </td><td><span class="timer">'.$time.'</span></td></tr>';
        $data['action']='show_finish_button';
    }else if($value_type=='wait_time'){
        $acttObj->update("$order_type",array('wt_tm'=>$time,'tm_by'=>'i'),array('id'=>$order_id));
        $data['msg']='<tr><td>Assignment waiting time from </td> <td><span class="timer">'.$time.'</span></td></tr>';
        $data['action']='show_start_button';
    }else{
        $acttObj->update("$order_type",array('fn_tm'=>$time,'tm_by'=>'i'),array('id'=>$order_id));
        $get_wt_st=$acttObj->read_specific("wt_tm,wt_tm,fn_tm","$order_type","id=".$order_id);
        $first_time=$get_wt_st['wt_tm']!='1001-01-01 00:00:00'?$get_wt_st['wt_tm']:$get_wt_st['st_tm'];
        $last_time=$get_wt_st['fn_tm'];
        $date1 = date_create($first_time);
        $date2 = date_create($last_time);
        $diff = date_diff($date1,$date2);
        $new_hour = round($diff->i/60,2);
        $data['hoursWorkd']=$new_hour;
        $data['msg']='<tr><td class="text-danger">Assignment finishing is <span class="timer">'.$time.'</span> & duration : </td><td class="text-danger">'.$new_hour.' hour(s)</td></tr>';
        $data['action']='show_form';
    }
    echo json_encode($data);
}
if(isset($_POST['datetime']) && isset($_POST['duration']) && $_POST['val']=='dur_finder'){
$dt=$_POST['datetime'];
$dur=$_POST['duration'];
    function get_endtime($datetime,$duration){
        $input_time = date($datetime);
        list($a, $b) = explode(':', $duration);
        $minutes = $a * 60 + $b;
        $newTime = date("m/d/Y H:i",strtotime("+$minutes minutes", strtotime($input_time)));
        return  $newTime;
    }
    echo get_endtime($dt,$dur);
}
if(isset($_POST['tc_id']) && !empty($_POST['tc_id'])){
    include 'source/db.php'; 
    include 'source/class.php';
    $tc_id=$_POST['tc_id'];
        $q_tt=$acttObj->read_all('tt_id,tt_title','trans_types',"tc_id='$tc_id' and tc_id!=8 and tt_status=1  ORDER BY tt_title ASC");
        $q_td=$acttObj->read_all('td_id,td_title','vw_translation',"tc_id='$tc_id' and tc_id!=8 ORDER BY td_title ASC");
        $res_tt.='<label class="control-label">Select Translation Type(s)</label>';
			if($q_tt->num_rows==0){
			    $res_tt.='<select name="trans_detail[]" id="trans_detail" class="form-control">
                <option value="8">Select Translation Type</option>
            </select>';
            }else{
                $res_tt.='<select name="trans_detail[]"  multiple="multiple" id="trans_detail" class="form-control multi_class" required>';
                    while($row_tt=$q_tt->fetch_assoc()){
                    $res_tt.='<option value="'.$row_tt['tt_id'].'">'.utf8_encode($row_tt['tt_title']).'</option>';
                        }
                $res_tt.='</select>';
            }
        $res_td.='<label class="control-label">Select Translation Category</label>';
			if($q_td->num_rows==0){
			    $res_td.='<select name="transType[]" id="transType" class="form-control">
                <option value="8">Select Translation Category</option>
            </select>';
            }else{
                $res_td.='<select name="transType[]"  multiple="multiple" id="transType" required class="form-control multi_class">';
                while($row_td=$q_td->fetch_assoc()){
                    $res_td.='<option value="'.$row_td['td_id'].'">'.utf8_encode($row_td['td_title']).'</option>';
                        }
                $res_td.='</select>';
            }
    $data[0] = $res_tt;
    $data[1] = $res_td;
    echo json_encode($data);
}
//Code for interpreting job types ajax
if(isset($_POST['ic_id']) && !empty($_POST['ic_id'])){
    include 'source/db.php'; 
    include 'source/class.php';
    $ic_id=$_POST['ic_id'];
    $res_it='';
    if($ic_id!='12'){
        $q_it=$acttObj->read_all('it_id,it_title','interp_types',"ic_id=$ic_id and it_status=1 ORDER BY it_title ASC");
        
        $res_it.='<label class="control-label">Select Assignment Type(s)</label>';
			if($q_it->num_rows==0){
			    $res_it.='<select name="interp_type[]" id="interp_type" class="form-control">
                <option value="">Select Assignment Type(s)</option>
            </select>';
            }else{
                $res_it.='<select name="interp_type[]"  multiple="multiple" id="interp_type" class="form-control multi_class" required>';
                    while($row_it=$q_it->fetch_assoc()){
                    $res_it.='<option value="'.$row_it['it_id'].'">'.utf8_encode($row_it['it_title']).'</option>';
                        }
                $res_it.='</select>';
            }
    }else{
    $res_it='';
    }
    echo $res_it;
}
//Code for telephone job types ajax
if(isset($_POST['tpc_id']) && !empty($_POST['tpc_id'])){
    include 'source/db.php'; 
    include 'source/class.php';
    $tpc_id=$_POST['tpc_id'];
    $res_tpt='';
    if($tpc_id!='11'){
        $q_tpt=$acttObj->read_all('tpt_id,tpt_title','telep_types',"tpc_id=$tpc_id and tpt_status=1 ORDER BY tpt_title ASC");
        
        $res_tpt.='<label class="control-label">Select Telephone Details</label>';
			if($q_tpt->num_rows==0){
			    $res_tpt.='<select name="telep_type[]" id="telep_type" class="form-control">
                <option value="">Select Telephone Details</option>
            </select>';
            }else{
                $res_tpt.='<select name="telep_type[]"  multiple="multiple" id="telep_type" class="form-control multi_class" required>';
                    while($row_tpt=$q_tpt->fetch_assoc()){
                    $res_tpt.='<option value="'.$row_tpt['tpt_id'].'">'.utf8_encode($row_tpt['tpt_title']).'</option>';
                        }
                $res_tpt.='</select>';
            }
    }else{
    $res_tpt='';
    }
    echo $res_tpt;
}
//Code for audio,video and both types ajax
if(isset($_POST['telep_checker']) && !empty($_POST['val'])){
    include 'source/db.php'; 
    include 'source/class.php';
    $val=$_POST['val'];
    $put_var=$val!='b'?"and c_cat='$val'":"";
    $res_tp_checker='';
        $q_tp_checker=$acttObj->read_all("c_id,c_title,c_image","comunic_types","c_status=1 $put_var ORDER BY c_title ASC");
        $res_tp_checker.='<label class="control-label">Select Communication Type</label>
                <select class="form-control" name="comunic" id="comunic" required="">';
                    $res_tp_checker.='<option value="">Select Type</option>';
                    while($row_tp_checker=$q_tp_checker->fetch_assoc()){
                    $res_tp_checker.='<option value="'.$row_tp_checker['c_id'].'">'.utf8_encode($row_tp_checker['c_title']).'</option>';
                        }
                $res_tp_checker.='</select>';
    echo $res_tp_checker;
}
if(isset($_REQUEST["term"]) && isset($_REQUEST["orgName"])){
    include 'source/db.php'; 
    include 'source/class.php';
    if(!empty($_REQUEST["orgName"])){
        $append_orgName="AND company='".$_REQUEST["orgName"]."'";
    }else{
        $append_orgName="";
    }
    $result=$acttObj->read_all("reference","comp_ref","reference LIKE '".$_REQUEST["term"]."%' ".$append_orgName);
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo "<p class='click'>" . $row["reference"] . "</p>";
        }
    } else{
        echo "<p>No matches found</p>";
    }
}
if(isset($_REQUEST["feedback_interpreter_name"]) && !empty($_REQUEST["feedback_interpreter_name"])){
    include 'source/db.php'; 
    include 'source/class.php';
    $name=$_REQUEST["feedback_interpreter_name"];
    $result=$acttObj->read_all("id,name","interpreter_reg","name LIKE '".$name."%' AND 
    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) 
    and deleted_flag=0 AND is_temp=0 AND id!=941 ORDER BY name ASC");
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo "<p id='".$row["id"]."' class='click'>" . $row["name"] . "</p>";
        }
    } else{
        echo "<p>No matches found</p>";
    }
}
if(isset($_POST["nm"]) && isset($_POST["em"]) && isset($_POST["dob"]) && isset($_POST["action"]) && $_POST["action"]=="check_em"){
    include 'source/db.php'; 
    include 'source/class.php';
    $nm=trim($_POST["nm"]);
    $em=trim($_POST["em"]);
    $dob=$_POST["dob"];
    $data=array();
    $check_1=$acttObj->read_specific("id,is_temp,dated","interpreter_reg",'email="'.$em.'" AND deleted_flag=0');
    if(!empty($check_1['id'])){
        $data['status']="exist";
        $data['is_temp']=$check_1['is_temp'];
        if($check_1['is_temp']==1){
            $data['msg']="Your account is already in pending for approval registered on ".$check_1['dated'];
        }else{
            $data['msg']="This email is already registered. Use a different one!";
        }
    }else{
        $check_2=$acttObj->read_specific("id,is_temp,dated","interpreter_reg","name LIKE '%".$nm."' AND dob='".$dob."' AND deleted_flag=0");
        if(!empty($check_2['id'])){
            $data['status']="same_exist";
            $data['is_temp']=$check_2['is_temp'];
            $data['msg']="Looks like we have already have a same record!";
        }else{
            $data['status']="not_exist";
            $data['is_temp']=$check_2['is_temp'];
            $data['msg']="";
        }
    }
    echo json_encode($data);
}
//code to get cities list for a specific country
if(isset($_POST['country_name']) && $_POST['type']=="get_cities_of_country"){
//   $ch = curl_init();
//   $postData = [
//       "country"=>$_POST['country_name']
//   ];
//   curl_setopt($ch, CURLOPT_URL,"https://countriesnow.space/api/v0.1/countries/cities");
//   curl_setopt($ch, CURLOPT_POST, 1);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
//   $cities_array=json_decode(curl_exec ($ch));
//   $cities_array=$cities_array->data;
//   $select_cities="<select onchange='other_city(this)' name='selected_city' id='selected_city' class='form-control mt' required>
//   <option value='' disabled selected>--- Select a city ---</option>";
//   if(count($cities_array)>0){
//     foreach($cities_array as $key=>$val){
//       $select_cities.="<option value='".$val."'>".$val."</option>";
//     }
//     $select_cities.="<option value='Not in List'>Not in List</option>";
//   }else{
//     $select_cities.="<option value='Not in List'>No City Found</option>";
//   }
//   $select_cities.="<select>";
//   $data['cities']=$select_cities;
//   echo json_encode($data);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://countriesnow.space/api/v0.1/countries/cities');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['country' => $_POST['country_name']]);
$response = curl_exec($ch);
curl_close($ch);
$cities_array = json_decode($response)->data;

$select_cities="<select onchange='other_city(this)' name='selected_city' id='selected_city' class='form-control mt' required>
  <option value='' disabled selected>--- Select a city ---</option>";
  if(count($cities_array)>0){
    foreach($cities_array as $key=>$val){
      $select_cities.="<option value='".$val."'>".$val."</option>";
    }
    $select_cities.="<option value='Not in List'>Not in List</option>";
  }else{
    $select_cities.="<option value='Not in List'>No City Found</option>";
  }
  $select_cities.="<select>";
  $data['cities']=$select_cities;
  echo json_encode($data);

}
?>