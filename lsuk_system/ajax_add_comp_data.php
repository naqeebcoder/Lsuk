<?php
//session_start();
//code to add child company
if(isset($_POST['parent_id']) && isset($_POST['child_id'])){
include'db.php'; 
include'class.php'; 
$table='child_companies';
$counter=0;$done=0;
$parent_id=$_POST['parent_id'];
$child_array=implode(',',$_POST['child_id']);
$child_id=explode(',',$child_array);
$dated=date('Y-m-d');
        while($counter<count($child_id)){
            $chk_exist = $acttObj->read_specific("count(id) as counter","$table","parent_comp=$parent_id AND child_comp=".$child_id[$counter]);
            if($chk_exist['counter']==0){
                $data=array('parent_comp'=>$parent_id,'child_comp'=>$child_id[$counter],'dated'=>$dated);
                $acttObj->insert($table,$data);
                if($counter==count($child_id)-1){$done=1;}
            }else{
                $done=1;
            }
            $counter++;
        }
        if($done==1){
           $result =$acttObj->read_all("$table.id,comp_reg.name","$table,comp_reg","$table.child_comp=comp_reg.id and $table.parent_comp='$parent_id' ORDER BY comp_reg.name ASC");
            if(mysqli_num_rows($result)==0){
                echo '<tr><td align="centre"><h3 class="text-center"><span class="label label-danger">No child companies exists!</span></h3></td></tr>';
            }else{
                while($row = mysqli_fetch_assoc($result))
                {
                  ?>
                  <tr>
                  <td align="left"><?php echo $row['name']; ?> </td>
                  <td align="left"> 
                    <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ) { ?>
                      <a href="javascript:void(0)" id="<?php echo $row['id']; ?>" onclick="remove_child(this)">
                        <img src="images/icn_trash.png" title="Trash" height="14" width="16" />
                      </a>
                      <?php 
                    } ?>
                  </td>
                </tr>
                <?php 
                } 
            }
        }
}
//code to remove child company
if(isset($_POST['remove_child_id'])){
include'db.php'; 
include'class.php'; 
$table='child_companies';
$remove_id=$_POST['remove_child_id'];
$parent_id=$acttObj->read_specific('parent_comp',"$table",'id='.$remove_id);
$data_remove=mysqli_query($con,"DELETE from $table where id=".$remove_id);
    if($data_remove){
        $result = $acttObj->read_all("$table.id,comp_reg.name","$table,comp_reg","$table.child_comp=comp_reg.id and $table.parent_comp=".$parent_id['parent_comp']." ORDER BY comp_reg.name ASC");
        if(mysqli_num_rows($result)==0){
            echo '<tr><td align="centre"><h3 class="text-center"><span class="label label-danger">No child companies exists!</span></h3></td></tr>';
        }else{
            while($row = mysqli_fetch_assoc($result))
            {
              ?>
              <tr>
              <td align="left"><?php echo $row['name']; ?> </td>
              <td align="left"> 
                <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' )
                {
                  ?>
                  <a href="javascript:void(0)" id="<?php echo $row['id']; ?>" onclick="remove_child(this)">
                    <img src="images/icn_trash.png" title="Trash" height="14" width="16" />
                  </a>
                  <?php 
                } 
                ?>
              </td>
            </tr>
            <?php 
            } 
        }
    }
}
//code to update porder attribute of company to 0 (no porder usage) 
if(isset($_POST['remove_comp_id']) && $_POST['action']=="remove_porder"){
    include'db.php'; 
    include'class.php'; 
    $table='comp_reg';
    $comp_id=$_POST['remove_comp_id'];
    $data_remove=$acttObj->editFun($table,$comp_id,'po_req','0');
    $acttObj->editFun($table,$comp_id,'po_email','');
    //Remove po reminders sent from list
    $f2f_ids=$acttObj->read_specific("GROUP_CONCAT(po_requested.id) as f2f_ids","po_requested,interpreter,comp_reg","interpreter.orgName=comp_reg.abrv AND po_requested.order_id=interpreter.id and po_requested.order_type='f2f' AND comp_reg.id=".$comp_id)['f2f_ids'];
    $tp_ids=$acttObj->read_specific("GROUP_CONCAT(po_requested.id) as tp_ids","po_requested,telephone,comp_reg","telephone.orgName=comp_reg.abrv AND po_requested.order_id=telephone.id and po_requested.order_type='tp' AND comp_reg.id=".$comp_id)['tp_ids'];
    $tr_ids=$acttObj->read_specific("GROUP_CONCAT(po_requested.id) as tr_ids","po_requested,translation,comp_reg","translation.orgName=comp_reg.abrv AND po_requested.order_id=translation.id and po_requested.order_type='tr' AND comp_reg.id=".$comp_id)['tr_ids'];
    if($f2f_ids){
        $acttObj->delete("po_requested","id IN ($f2f_ids)");
    }
    if($tp_ids){
        $acttObj->delete("po_requested","id IN ($tp_ids)");
    }
    if($tr_ids){
        $acttObj->delete("po_requested","id IN ($tr_ids)");
    }
    if($data_remove){
        echo "1";
    }else{
        echo "0";
    }
}
//code to update company emails like emial invEmail and porder email
if(isset($_POST['update_comp_id']) && $_POST['type']=="update_comp_emails"){
    include'db.php'; 
    include'class.php'; 
    $table='comp_reg';
    $update_comp_id=$_POST['update_comp_id'];
    if($_POST['email']){
        if($acttObj->update($table,array('email'=>$_POST['email']),array("id"=>$update_comp_id))){
            $data['result']=1;
        }
    }
    if($_POST['invEmail']){
        if($acttObj->update($table,array('invEmail'=>$_POST['invEmail']),array("id"=>$update_comp_id))){
            $data['result']=1;
        }
    }
    if($_POST['po_email']){
        if($acttObj->update($table,array('po_email'=>$_POST['po_email']),array("id"=>$update_comp_id))){
            $data['result']=1;
        }
    }
    echo json_encode($data);
}

if (isset($_POST['cd_for']) && isset($_POST['lang']) && isset($_POST['cancel']) && $_POST['cancel'] == 'yes') {
    include '../source/class.php';
    $cd_for = 'cl';
    $lang = $_POST['lang'];
    $cancelled_at = date('Y-m-d H:i:s');
    $cancelled_date = date('Y-m-d', strtotime($cancelled_at));
    $cancelled_time = date('H:i:s', strtotime($cancelled_at));
    $assignment_date = date('Y-m-d', strtotime($_POST['assign_date']));
    $assignment_time = date('H:i:s', strtotime($_POST['assign_time']));

    $date1 = new DateTime($cancelled_at);
    $date2 = new DateTime($assignment_date . " " . $assignment_time);
    $diff = $date2->diff($date1);

    $working_days = 0;
    if ($date2 > $date1) {
        list($date2, $date1) = [$date1, $date2];
    }
    while ($date2 < $date1) {
        if ($date2->format("N") < 6) {
            $working_days++;
        }
        $date2->modify('+1 day');
    }
    $diff_hours = ($working_days * 24);
    $hours = $diff_hours;

    $pay_int = 0;
    $past_cancellation_label = $date1 > $date2 ? '<span class="label label-danger">Past Cancellation</span>' : '';
    if ($lang == 'Sign Language' || $lang == 'Sign Language (BSL)') {
        // For BSL:24 hours=24x7:168,48 hours=24x14:336,greater 48 hours=greater then 336
        if ($working_days <= 7) {
            $pay_int = 1;
            $put_cancelled_hours = " AND cancelled_hours=1";
        } else if ($working_days > 7 && $working_days <= 14) {
            $put_cancelled_hours = " AND cancelled_hours=2";
        } else {
            $put_cancelled_hours = " AND cancelled_hours=3";
        }
    } else {
        if ($working_days <= 1) {
            $pay_int = 1;
            $put_cancelled_hours = " AND cancelled_hours=1";
        } else if ($working_days > 1 && $working_days <= 2) {
            $put_cancelled_hours = " AND cancelled_hours=2";
        } else {
            $put_cancelled_hours = " AND cancelled_hours=3";
        }
    }
    $pay_int_text = $pay_int == 1 ? "Interpreter Payable" : "Interpreter Non-payable";
    $pay_int_class = $pay_int == 1 ? "danger" : "success";

    $put_bsl = $lang == 'Sign Language' || $lang == 'Sign Language (BSL)' ? " and is_bsl = 1" : " and is_bsl = 0";
    $put_var = "cd_for='" . $cd_for . "'";
    $row_dropdown = $acttObj->read_specific("cd_id,cd_title,cd_effect", "cancellation_drops", "$put_var $put_bsl $put_cancelled_hours AND deleted_flag=0 ORDER BY cd_title ASC");
    $charge_client = $row_dropdown['cd_effect'] == '1' ? "You will be Charged!" : "You will not be Charged";
    $charge_client_class = $row_dropdown['cd_effect'] == '1' ? "danger" : "success";
    $response = '<div class="form-group col-xs-3" id="div_cancel_details" style="margin-top: 5px;">
        <br><button type="button" class="btn btn-block btn-' . $charge_client_class . '">' . $charge_client . '</button>
    </div>
    <div class="form-group col-xs-6" id="div_cd">';
    $response .= '<label class="control-label">Select Cancellation Type [Days : ' . $working_days . '] ' . $past_cancellation_label . '</label>
    <select onchange="get_reasons(this);" class="form-control" name="cn_t_id" id="cn_t_id" required>';
    $response .= '<option value="' . $row_dropdown['cd_id'] . '">' . utf8_encode(str_replace("[DATE]", date('d-m-Y'), $row_dropdown['cd_title'])) . '</option>';
    $response .= '</select></div>';

        //Cancellation reasons dropdown    
    $put_var = $cd_for == 'ls' && ($lang == 'Sign Language' || $lang == 'Sign Language (BSL)') ? "1" : "cr_for='" . $cd_for . "'";
    $response .= '<div class="form-group col-xs-5" id="div_reason">';
    $query_reasons = $acttObj->read_all("cr_id,cr_title", "cancel_reasons", "$put_var $put_bsl OR cr_id=15 ORDER BY cr_title ASC");
    $response .= '<label class="control-label">Select Cancellation Reason</label>
    <select onchange="get_buttons(this);" class="form-control" name="cn_r_id" id="cn_r_id" required=""><option selected disabled value="">Select Cancellation Reason</option>';
    while ($row_reasons = $query_reasons->fetch_assoc()) {
        $other_colored = $row_reasons['cr_id'] == 15 ? "style='color:red'" : "";
        $response .= '<option ' . $other_colored . ' value="' . $row_reasons['cr_id'] . '">' . utf8_encode($row_reasons['cr_title']) . '</option>';
    }
    $response .= '</select></div>';
    echo json_encode(["data" => $response, "charge_client" => $row_dropdown['cd_effect'], "pay_int" => $pay_int]);
}
//Code for cancellation reasons ajax
if (isset($_POST['cd_id']) && isset($_POST['reason']) && $_POST['reason'] == 'yes') {
    include '../source/class.php';
    $cd_id = $_POST['cd_id'];
    $cd_effect = $acttObj->read_specific("cd_effect", "cancellation_drops", "cd_id=" . $cd_id)['cd_effect'];
    if ($cd_effect == 1) {
        $hidden_pay = "<input type='hidden' id='hidden_pay' value='1'/>";
    } else {
        $hidden_pay = "<input type='hidden' id='hidden_pay' value='0'/>";
    }
    if ($cd_id != '6') {
        $cr_for = $acttObj->read_specific("cd_for", "cancellation_drops", "cd_id='$cd_id'")['cd_for'];
        $put_var = $cr_for == 'cl' ? "cr_for='$cr_for' OR cr_for='all'" : "cr_for='ls' OR cr_for='all'";
        $res_cr_chk = '';
        $q_cr_chk = $acttObj->read_all("cr_id,cr_title", "cancel_reasons", "$put_var ORDER BY cr_title ASC");
        $res_cr_chk .= '<label class="control-label">Select Cancellation Reason</label>' . $hidden_pay . '
            <select onchange="get_buttons();" class="form-control" name="cn_r_id" id="cn_r_id" required=""><option selected disabled value="">Select Cancellation Reason</option>';
        while ($row_cr_chk = $q_cr_chk->fetch_assoc()) {
            $res_cr_chk .= '<option value="' . $row_cr_chk['cr_id'] . '">' . utf8_encode($row_cr_chk['cr_title']) . '</option>';
        }
        $res_cr_chk .= '</select>';
        echo $res_cr_chk;
    } else {
        echo '';
    }
}
?>