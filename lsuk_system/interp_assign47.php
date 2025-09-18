<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'actions.php';
?>
<script src="js/jquery-1.11.3.min.js"></script>
<div style="width:100%;height:100%;text-align:center;position:fixed;z-index:111111111111111;background-color: #ffffff;display:none;" id="load-img">
<div >
<img src="images/loading.gif" style="padding:2rem;width:15rem;"  alt="Loading...">
<h2 style="color: #f44336;">Please Wait Until the Emails are Sent ..</h2>
</div>
</div>
<?php
if (isset($interp_id) && !empty($interp_id)) {
  echo "<script> let requestInProgress = true; $('#load-img').show(); </script>";
}
?>
<script>
    let requestInProgress = true;
    $(window).on('beforeunload', function() {
        if (requestInProgress) {
            return 'Please wait while the emails are being sent..';
        }
    });
</script>
<?php
$table = $_GET['table'];
if ($table == 'interpreter') {
  $find_string = "Face To Face";
  $order_type = 1;
  $allowed_type_idz = "7,143";
}
if ($table == 'telephone') {
  $find_string = "Telephone";
  $order_type = 2;
  $allowed_type_idz = "21,145";
}
if ($table == 'translation') {
  $find_string = "Translation";
  $order_type = 3;
  $allowed_type_idz = "34,147";
  if ($get_job_details['docType'] == 7) { //Transcription:7, selected_language_type:3 means BSL
    $order_type = 4;
  }
  if ($get_job_details['docType'] == 7 && $find_language_type == 3) { //Transcription & BSL
    $order_type = 5;
  }
}
//Check if user has assign interpreter action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Assign Interpreter</u> action for " . $find_string . " jobs!<br>Kindly contact admin for further process.</h2></center>");
  }
}
include '../source/setup_sms.php';
$setupSMS = new setupSMS;
//Access actions
$get_actions = explode(",", $obj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=58 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_text_messages = $_SESSION['is_root'] == 1 || in_array(136, $get_actions);
$action_assign_job = $_SESSION['is_root'] == 1 || in_array(137, $get_actions);
$action_view_budget_rates = $_SESSION['is_root'] == 1 || in_array(138, $get_actions);
$action_view_interpreter_profile = $_SESSION['is_root'] == 1 || in_array(139, $get_actions);

$gender = $_GET['gender'];
$dbs_checked = $_GET['dbs_checked'];
$assign_id = $_GET['assign_id'];
$srcLang = $_GET['srcLang'];
$interp_id = $_GET['interp_id'];
$assignDate = $_GET['assignDate'];
$get_job_details = $obj->read_specific("*", $table, "id=" . $assign_id);
$srcLang = $get_job_details['source'];
$orgNameForJob = $get_job_details['orgName'];
$genderForJob = $get_job_details['gender'];
$submited='';
$submited = $get_job_details['submited'];
$JobCity="";
$order_types_array = array("interpreter" => 1, "telephone" => 2, "translation" => 3);

$order_company_id = $get_job_details['order_company_id'];
if (empty($order_company_id)) {
  $get_company_data = $obj->read_specific("comp_reg.id,comp_type.company_type_id as type_id", "comp_reg,comp_type", "comp_reg.type_id=comp_type.id AND comp_reg.abrv='" . $orgNameForJob . "'");
  $order_company_id = $get_company_data['id'];
  $find_company_type = $get_company_data['type_id'];
} else {
  if (!empty($get_job_details['company_rate_data'])) {
    $extracted_data = (array) json_decode($get_job_details['company_rate_data']);
    $find_company_type = $extracted_data['company_type_id'];
  } else {
    $get_company_data = $obj->read_specific("comp_reg.id,comp_type.company_type_id as type_id", "comp_reg,comp_type", "comp_reg.type_id=comp_type.id AND comp_reg.id=" . $order_company_id);
    $find_company_type = $get_company_data['type_id'];
  }
}

$find_language_type = $obj->read_specific("language_type", "lang", "lang='" . $get_job_details['source'] . "'")['language_type'];
$input_duration = $get_job_details['assignDur'] / 60;
$job_budget = 0;
if ($table != 'translation') {
  if (empty($get_job_details['company_rate_data'])) {
    $find_company_type = $get_company_data['type_id'];
    $ch = curl_init();
    $postData = [
      "find_company_rates"  => 1,
      "find_order_type"     => $order_type,
      "find_company_id"     => $order_company_id,
      "find_company_type"     => $find_company_type,
      "find_assignment_time"     => $get_job_details['assignTime'],
      "find_assignment_date"     => $get_job_details['assignDate'],
      "find_language_type"     => $find_language_type,
      "find_booked_time"     => $get_job_details['bookedtime'],
      "find_booked_date"     => $get_job_details['bookeddate']
    ];
    curl_setopt($ch, CURLOPT_URL, actionsClass::URL . "/lsuk_system/ajax_add_interp_data.php");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response_data = curl_exec($ch);
    curl_close($ch);
    $json_data = json_decode($response_data, true);
    $extracted_data = !empty($json_data['company_rates'][0]) ? $json_data['company_rates'][0] : array();
  }
  //Client calculation
  if (!empty($extracted_data)) {
    if ($table == 'interpreter') {
      $input_hours = $input_duration < $extracted_data['minimum_charge_interpreting'] ? $extracted_data['minimum_charge_interpreting'] : $input_duration;
      $client_minimum_duration = $misc->calculate_client_hours($input_hours, $extracted_data['incremental_charge_f2f']);
      $input_hours = $client_minimum_duration * 60;
    } else {
      $input_hours = $input_duration < $extracted_data['minimum_charge_telephone'] ? $extracted_data['minimum_charge_telephone'] : $input_duration;
      $client_minimum_duration = $misc->calculate_client_hours($input_hours, $extracted_data['incremental_charge_tp'], 'minutes');
      $input_hours = round($client_minimum_duration) < $input_duration ? $client_minimum_duration : round($client_minimum_duration);
    }
    if ($input_hours > 60) {
      $returned_value = $input_hours / 60;
      if (floor($returned_value) > 1) {
        $label_time = "hours";
      } else {
        $label_time = "hour";
      }
      $returned_minutes = $input_hours % 60;
      if ($returned_minutes == 00) {
        $assignment_duration = sprintf("%2d $label_time", $returned_value);
      } else {
        $assignment_duration = sprintf("%2d $label_time %02d minutes", $returned_value, $returned_minutes);
      }
    } else if ($input_hours == 60) {
      $assignment_duration = "1 Hour";
    } else {
      $assignment_duration = round($input_hours) . " minutes";
    }
    $desired_company_rate = $table == 'interpreter' ? $extracted_data['rate_value_f2f'] : $extracted_data['rate_value_tp'];
    $desired_company_admin_charge =  $extracted_data['admin_charge'] == 1 ? $extracted_data['admin_charge_rate'] : 0;
    $job_budget = ($client_minimum_duration * $desired_company_rate) + $desired_company_admin_charge;
  }
}
//Interpreter duration will be original duration

if ($get_job_details['assignDur'] > 60) {
  $int_returned_value = $get_job_details['assignDur'] / 60;
  if (floor($int_returned_value) > 1) {
    $int_label_time = "hours";
  } else {
    $int_label_time = "hour";
  }
  $int_returned_minutes = $get_job_details['assignDur'] % 60;
  if ($int_returned_minutes == 00) {
    $actual_duration = sprintf("%2d $int_label_time", $int_returned_value);
  } else {
    $actual_duration = sprintf("%2d $int_label_time %02d minutes", $int_returned_value, $int_returned_minutes);
  }
} else if ($get_job_details['assignDur'] == 60) {
  $actual_duration = "1 Hour";
} else {
  $actual_duration = $get_job_details['assignDur'] . " minutes";
}

$firstday = date('Y-m-d', strtotime("this week"));
$today_plus_7 = date('Y-m-d', strtotime("+7 day"));
$fields = '';

if ($table == 'interpreter') {
  $assignDate = $get_job_details['assignDate'];
  $assignpostcode = $get_job_details['postCode'];
  $chek_col = 'interp';
  $fields = 'assignDate,assignTime,assignDur,postCode';
}
if ($table == 'telephone') {
  $assignDate = $get_job_details['assignDate'];
  $assignpostcode = $get_job_details['inchPcode'];
  $chek_col = 'telep';
  $fields = 'assignDate,assignTime,assignDur';
}
if ($table == 'translation') {
  $assignDate = $get_job_details['asignDate'];
  $chek_col = 'trans';
  $fields = 'asignDate';
}

if ($table != 'translation') {
  $assignTime = @$_GET['assignTime'];
  $assignDur = $get_job_details['assignDur'];
  $JobCity = $get_job_details['assignCity'];
}
$post_assign_time = $assignTime ? $assignTime : "09:00:00";
if (isset($interp_id) && !empty($interp_id)) {
  $is_already_assigned = $obj->read_specific("intrpName", "$table", "id=" . $assign_id)['intrpName'];
  if (!empty($is_already_assigned)) {
    echo "<script> alert('This job is already assigned to another interpreter! Thank you');window.close(); </script>";exit;
  } else {
    echo "<script> $('#load-img').show(); </script>";
    //Add assign interpreter ID logic to here
    if (isset($_GET['int_rate_id']) && !empty($_GET['int_rate_id'])) {
      $get_interpreter_rate = $obj->read_specific("interpreter_rates.*,rate_categories.is_bsl,rate_categories.is_rare,(CASE WHEN rate_categories.is_bsl = 1 THEN 'BSL' WHEN rate_categories.is_rare = 1 THEN 'Rare' ELSE 'Standard' END) as extra_title, rate_categories.title", "interpreter_rates,rate_categories", "interpreter_rates.rate_category_id=rate_categories.id AND interpreter_rates.id=" . $_GET['int_rate_id']);
      $get_interpreter_rate['title'] = $get_interpreter_rate['title'] . " - (" . $get_interpreter_rate['extra_title'] . ")";
    }
    $update_data_array = array(
      'pay_int' => '1',
      'aloct_by' => $_SESSION['UserName'],
      'aloct_date' => date("Y-m-d"),
      'intrpName' => $interp_id,
      'edited_by' => $_SESSION['UserName'],
      'edited_date' => date("Y-m-d H:i:s")
    );
    if (!empty($get_interpreter_rate['id'])) {
      $update_data_array['interpreter_rate_id'] = $get_interpreter_rate['id'];
      $update_data_array['interpreter_rate_data'] = json_encode($get_interpreter_rate);
    }
    $obj->update($table, $update_data_array, "id=" . $assign_id);

    // Log Assign data combinely excluding rate data
    $index_mapping = array('Allocated To' => 'intrpName', 'Allocated By' => 'aloct_by', 'Allocated Date' => 'aloct_date');
    $old_values = array();
    $new_values = array();
    $get_new_data = $obj->read_specific("*", "$table", "id=" . $assign_id);
    foreach ($index_mapping as $key => $value) {
        if (isset($get_new_data[$value])) {
            $old_values[$key] = $row[$value];
            $new_values[$key] = $get_new_data[$value];
        }
    }
    $obj->log_changes(json_encode($old_values), json_encode($new_values), $assign_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], "assign_interpreter");
    // Log Rate data individually
    $index_mapping = array('Interpreter Rate ID' => 'interpreter_rate_id', 'Interpreter Rate Data' => 'interpreter_rate_data');
    $old_values = array();
    $new_values = array();
    foreach ($index_mapping as $key => $value) {
        if (isset($get_new_data[$value])) {
            $old_values[$key] = $get_job_details[$value];
            $new_values[$key] = $get_new_data[$value];
        }
    }
    $obj->log_changes($old_values, $new_values, $assign_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], "assign_interpreter");

    $array_table = array("interpreter" => "F2F", "telephone" => "TP", "translation" => "TR");
    $obj->insert("daily_logs", array("action_id" => 5, "user_id" => $_SESSION['userId'], "details" => $array_table[$table] . " Job ID: " . $assign_id));
    $check_role = $obj->read_specific('prv', "login", 'name="' . $submited.'"')['prv'];
    if($check_role!="Test"){
      include 'sendassignemails.php';
      $get_removals = $obj->read_all("*", "app_notifications", "title LIKE '%" . $assign_id . "%' and type_key='nj' AND LOCATE('" . $find_string . "',title)>0");
      if ($get_removals->num_rows > 0) {
        while ($row_removals = $get_removals->fetch_assoc()) {
          //Update notification counter on APP
          $check_int_id = $obj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $row_removals['int_ids'])['id'];
          if (!empty($check_int_id) && $check_int_id > 0) {
            $existing_notification = $obj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $row_removals['int_ids'])['new_notification'];
            $obj->update('notify_new_doc', array("new_notification" => $existing_notification - 1), "interpreter_id=" . $row_removals['int_ids']);
          }
          $obj->delete("app_notifications", "id=" . $row_removals['id']);
        }
      } 
      $title = "You have got a new job";
      $sub_title = $srcLang . $assignment_type . " assignment at " . $assignDate . " has been assigned to you";
      $type_key = "ja";
      //Send notification on APP
      $check_exist_int_id = $obj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $interp_id)['id'];
      if (empty($check_exist_int_id)) {
        $obj->insert('notify_new_doc', array("interpreter_id" => $interp_id, "status" => '1'));
      } else {
        $existing_notification = $obj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $interp_id)['new_notification'];
        $obj->update('notify_new_doc', array("new_notification" => $existing_notification + 1), "interpreter_id" . $interp_id);
      }
      $array_tokens = explode(',', $obj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $interp_id)['tokens']);
      if (!empty($array_tokens)) {
        $obj->insert('app_notifications', array("title" => $title, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $interp_id, "read_ids" => $interp_id, "type_key" => $type_key));
        foreach ($array_tokens as $token) {
          if (!empty($token)) {
            $obj->notify($token, "📝 " . $title, $sub_title, array("type_key" => $type_key));
          }
        }
      }
    }
    //Removed update interpreter ID from here
    echo "<script> 
    $('#load-img').hide(); 
    window.close(); 
    </script>";
    exit;
  }
}
?>
<script>
    requestInProgress = false;
</script>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" style="background:white !important;">

<head>
  <title>Assign Interpreter</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet" type='text/css'>
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  <style>
    .bg-adhoc {
      background: #e9de24;
    }
    .fa-star {
      color: yellow;
      text-shadow: 0px 0px 3px black;
    }

    tr.bg-success {
      background-color: #dff0d8 !important;
    }

    tr.bg-info {
      background-color: #d9edf7 !important;
    }

    tr.bg-danger {
      background-color: #f2dede !important;
    }

    tr.bg-primary {
      background-color: #337ab7 !important;
    }

    .nav-tabs>li.active>a,
    .nav-tabs>li.active>a:focus,
    .nav-tabs>li.active>a:hover {
      background-color: #f4eded;
      font-weight: bold;
    }
  </style>
  <script>
    function myFunction() {
      //onchange on combo
      var x = document.getElementById("mySelect").value;
      //  document.getElementById("demo").innerHTML = "You selected: " + x;
      window.location.href = "interp_assign.php?interp_id=" + x +
        "&table=<?php echo $table; ?>" + "&assign_id=<?php echo $assign_id; ?>" +
        "&srcLang=<?php echo $srcLang; ?>" + "&gender=<?php echo $gender; ?>" +
        "&dbs_checked=<?php echo $dbs_checked; ?>" + "&assignDate=<?php echo @$assignDate; ?>" +
        "&assignTime=<?php echo @$assignTime; ?>";
    }
    $(document).ready(function() {
      $('table.datatable').DataTable({
        "order": [],
        "pageLength": 100
      });
    });
  </script>

</head>

<body>
  <form action="" method="post">
    <fieldset class="container-fluid">
      <p>
        <?php include 'loadinterpcanassign.php';
        $arrGrp0 = "";
        $arrGrp1 = "";
        $arrGrp2 = "";
        $arrGrp3 = "";
        //if job already booked or not deleted/cancelled
        $check_booked = $obj->read_specific("interpreter_reg.name,interpreter_reg.contactNo,interpreter_reg.city,$table.aloct_by", "$table,interpreter_reg", "$table.intrpName=interpreter_reg.id AND $table.id=" . $assign_id);
        if ($check_booked['name'] != '') {
          $via = $check_booked['aloct_by'] == 'Auto Allocated' ? ' Via system auto allocation' : ' by ' . $check_booked['aloct_by'];
          $get_msg_db = $obj->read_specific('message', 'auto_replies', 'id=4');
          $msg = "<div class='alert alert-warning col-md-10 col-md-offset-1 text-center h4'>This job is already assigned to <b>" . $check_booked['name'] . "</b>" . $via . " !</b></div>";
        } else if ($get_job_details['deleted_flag'] == 1 || $get_job_details['order_cancel_flag'] == 1) {
          $msg = "<div class='alert alert-warning col-md-10 col-md-offset-1 text-center h4'><b>Sorry ! This job is no longer available.<br>Thank you</b></div>";
        } else {
          $msg = "";
        }
        if (isset($msg) && !empty($msg)) {
          echo "<br><br><br>" . $msg;
        } else {
          $options = "";
          while ($row_opt = $sql_opt->fetch_assoc()) {
            $code = $row_opt["id"];
            $jobDate = $get_job_details['assignDate'];
            $jobDur = $get_job_details['assignDur'];
            $jobStart = date('H:i:s', strtotime($get_job_details['assignTime']));

            $getJobEnd = strtotime("+".$jobDur." minutes", strtotime($jobStart));
            $jobEnd = date('H:i:s', $getJobEnd);

            $check_jobs = $obj->read_all("assignDate,assignTime,assignDur", " (SELECT interpreter.assignTime,interpreter.assignDate,interpreter.assignDur FROM interpreter", " interpreter.intrpName=$code AND interpreter.assignDate='$jobDate' AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.orderCancelatoin=0 UNION ALL SELECT telephone.assignTime,telephone.assignDate,telephone.assignDur FROM telephone WHERE telephone.intrpName=$code AND telephone.assignDate='$jobDate' AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.orderCancelatoin=0) AS grp");
            $intrp_jobOverlapCount = 0;
            if($check_jobs->num_rows>0){
                while ($job_row = $check_jobs->fetch_assoc()) {
                  $intrp_jobDate = $job_row['assignDate'];
                  $intrp_jobDur = $job_row['assignDur'];
                  $intrp_jobStart = date('H:i:s',strtotime($job_row['assignTime']));
                  $intrp_getJobEnd = strtotime("+".$intrp_jobDur." minutes", strtotime($intrp_jobStart));
                  $intrp_jobEnd = date('H:i:s', $intrp_getJobEnd);
                  if(($jobStart >= $intrp_jobStart && $jobStart <= $intrp_jobEnd) || ($jobEnd >= $intrp_jobStart && $jobEnd <= $intrp_jobEnd)){
                    $intrp_jobOverlapCount = $intrp_jobOverlapCount + 1;
                  }
                  
                }
            }

            $codeid = "id-$code";
            $ids = $row_opt['id'];
            //Interpreter calculation starts
            $ch = curl_init();
            $postData = [
              "find_interpreter_rates"  => 1,
              "find_order_type"     => $order_type,
              "find_interpreter_id"     => $ids,
              "find_assignment_time"     => $post_assign_time,
              "find_assignment_date"     => $assignDate,
              "find_language_type"     => $find_language_type,
              "find_booked_time"     => $get_job_details['bookedtime'],
              "find_booked_date"     => $get_job_details['bookeddate']
            ];
            curl_setopt($ch, CURLOPT_URL, actionsClass::URL . "/lsuk_system/ajax_add_interp_data.php");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response_data = curl_exec($ch);
            curl_close($ch);
            $json_data = json_decode($response_data, true);
            $extracted_data_int = !empty($json_data['interpreter_rates'][0]) ? $json_data['interpreter_rates'][0] : array();
            if (!empty($extracted_data_int)) {
              // echo "<pre>";
              // print_r($extracted_data_int);

              if ($table != 'translation') {
                if ($table == 'interpreter') {
                  $admin_charge_allowed = $extracted_data_int['admin_charge'] == 1 ? "<i class='fa fa-money text-danger pull-right' title='Admin Charges Payable'></i>" : "";
                  $is_travel_allowed = $extracted_data_int['travel_time_charges'] == 1 ? "<i class='fa fa-car text-danger pull-right' title='Travel Time Chargeable'></i>" : "";
                  $is_mileage_allowed = $extracted_data_int['mileage_charge'] == 1 ? "<i class='fa fa-map-marker text-danger pull-right' title='Mileage Chargeable'></i>" : "";
                  $is_parking_allowed = $extracted_data_int['parking_charges'] == 1 ? "<i class='fa fa-ban text-danger pull-right' title='Parking Chargeable'></i>" : "";
                  $what_is_allowed = $admin_charge_allowed . $is_travel_allowed . $is_mileage_allowed . $is_parking_allowed;
                  $input_hours_int = $input_duration < $extracted_data_int['minimum_charge_interpreting'] ? $extracted_data_int['minimum_charge_interpreting'] : $input_duration;
                  $int_minimum_duration = $misc->calculate_client_hours($input_hours_int, $extracted_data_int['incremental_charge_f2f']);
                  $input_hours_int = $int_minimum_duration * 60;
                } else {
                  $admin_charge_allowed = $extracted_data_int['admin_charge'] == 1 ? "<i class='fa fa-money text-danger pull-right' title='Admin Charges Payable'></i>" : "";
                  $what_is_allowed = $admin_charge_allowed;
                  $input_hours_int = $input_duration < $extracted_data_int['minimum_charge_telephone'] ? $extracted_data_int['minimum_charge_telephone'] : $input_duration;
                  $int_minimum_duration = $misc->calculate_client_hours($input_hours_int, $extracted_data_int['incremental_charge_tp'], 'minutes');
                  $input_hours_int = round($int_minimum_duration) < $input_duration ? $int_minimum_duration : round($int_minimum_duration);
                }
                if ($input_hours_int > 60) {
                  $returned_value_int = $input_hours_int / 60;
                  if (floor($returned_value_int) > 1) {
                    $label_time_int = "hours";
                  } else {
                    $label_time_int = "hour";
                  }
                  $returned_minutes_int = $input_hours_int % 60;
                  if ($returned_minutes_int == 00) {
                    $assignment_duration_int = sprintf("%2d $label_time_int", $returned_value_int);
                  } else {
                    $assignment_duration_int = sprintf("%2d $label_time_int %02d minutes", $returned_value_int, $returned_minutes_int);
                  }
                } else if ($input_hours_int == 60) {
                  $assignment_duration_int = "1 Hour";
                } else {
                  $assignment_duration_int = round($input_hours_int) . " minutes";
                }
                $desired_int_rate = $table == 'interpreter' ? $extracted_data_int['rate_value_f2f'] : $extracted_data_int['rate_value_tp'];
                $desired_int_admin_charge =  $extracted_data_int['admin_charge'] == 1 ? $extracted_data_int['admin_charge_rate'] : 0;
                $job_budget_int = ($int_minimum_duration * $desired_int_rate) + $desired_int_admin_charge;
              }
            }
            //Interpreter calculation ends

            $bWantIt = true;
            $black_list = $obj->read_specific('count(*) as black_list', 'interp_blacklist', "interpName='$codeid' AND orgName='$orgNameForJob' and deleted_flag=0")['black_list'];
            if ($black_list > 0) {
              $bWantIt = false;
            } else {
              //check schedule
              $name_of_day = strtolower(date('l', strtotime($assignDate)));
              if ($row_opt[$name_of_day] == "No") {
                $bWantIt = false;
              }
            }
            //echo $row_opt['name'].' - '.$row_opt[$name_of_day].' - '.$bWantIt.'<br>';
            if ($bWantIt) {
              $name_opt = $row_opt["name"];
              $interpreter_rate = $table == 'interpreter' ? $row_opt["rph"] : $row_opt["rpm"];
              $city_opt = $row_opt["city"];
              $gender = $row_opt["gender"];
              $contactNo = $setupSMS->format_phone($row_opt['contactNo'], $row_opt['country']);
              $country = $row_opt['country'];
              $interpreter_email = $row_opt['email'];
              $interpCity = $row_opt['city'];
              //$options.="<OPTION value='$code'>".$name_opt.' ('. $gender.')'.' ('. $city_opt.')';

              $dob = $row_opt['dob'];
              $buildingName = $row_opt['buildingName'];
              $line1 = $row_opt['line1'];
              $city = $row_opt['city'];
              $ni = $row_opt['ni'];

              $interp_idonly = $row_opt['id'];
              $interp_code = "id-" . $row_opt['id'];

              $isred = false;
              if (
                empty($dob) || $dob == '0000-00-00' || empty($buildingName) ||
                empty($buildingName) || empty($city) || empty($line1) || empty($ni)
              ) {
                $isred = true;
              }

              $isAdhoc = false;
              if ($row_opt['isAdhoc'] == 1) {
                $isAdhoc = true;
              }

              $isblue = false;
              if (
                empty($row_opt['applicationForm']) || empty($row_opt['agreement']) || empty($row_opt['crbDbs']) ||
                empty($row_opt['identityDocument']) || $row_opt['identityDocument'] == 'Not Provided' ||
                $row_opt['applicationForm'] == 'Not Provided' || $row_opt['agreement'] == 'Not Provided' ||
                $row_opt['crbDbs'] == 'Not Provided'
              ) {
                $isblue = false;
              }

              $tableis = 'interp_skill';
              $code = 'id-' . $interp_id;

              $strSkills = "";
              $result = $obj->read_all("*", "$tableis", "code='$code'");
              while ($row = $result->fetch_assoc()) {
                $strSkills .= $row['skill'] . "\n";
              }

              $strRateStar = "";

              $result = $obj->read_all(
                "(sum(punctuality) + sum(appearance) + sum(professionalism) + 
                sum(confidentiality) + sum(impartiality) + sum(accuracy) + sum(rapport) + 
                sum(communication)) as sm,COUNT(interp_assess.id) as diviser", 
                "interp_assess,interpreter_reg", 
                "interp_assess.interpName=interpreter_reg.code AND interp_assess.interpName='$interp_code'");
              while ($row = mysqli_fetch_assoc($result)) {
                $diviser = $row['diviser'];
                if ($diviser <= 0) {
                  $diviser = 1;
                }
                $assess_num = $row['sm'] * 100 / ($diviser * 120);
              }

              $badfeed = false;
              //echo $assess_num;
              if ($assess_num < 0) {
                $strRateStar = '<span style="color: white;font-size: 14px;">Negative Feedback</span>';
                $badfeed = true;
              }
              if ($assess_num >= 0 && $assess_num <= 5) {
                $strRateStar = '<span class="btn btn-default btn-xs">No Feedback</span>';
                $badfeed = true;
              }
              if ($assess_num > 6 && $assess_num <= 20) {
                $strRateStar = '<i class="fa fa-star"></i>';
              }
              if ($assess_num > 20 && $assess_num <= 40) {
                $strRateStar = '<i class="fa fa-star"></i> <i class="fa fa-star"></i>';
              }
              if ($assess_num > 40 && $assess_num <= 60) {
                $strRateStar = '<i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i>';
              }
              if ($assess_num > 60 && $assess_num <= 80) {
                $strRateStar = '<i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i>';
              }
              if ($assess_num > 80 && $assess_num <= 100) {
                $strRateStar = '<i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i>';
              }
              //code for 5 jobs

              if ($table == 'translation') {
                //$q_count_jobs = "SELECT count(*) as jobs_done FROM $table WHERE asignDate BETWEEN '" . $firstday . "' AND '" . $today_plus_7 . "' AND intrpName='$ids' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0";
                $row_count_jobs = $obj->read_specific("count(*) as jobs_done", $table, "asignDate >='" . date('Y-m-d') . "' AND intrpName='$ids' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0");
              } else {
                //$q_count_jobs = "SELECT count(*) as jobs_done FROM $table WHERE assignDate BETWEEN '" . $firstday . "' AND '" . $today_plus_7 . "' AND intrpName='$ids' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0";
                $row_count_jobs = $obj->read_specific("sum(jobs) as jobs_done", "(SELECT COUNT(*) as jobs FROM interpreter", "interpreter.assignDate >='" . date('Y-m-d') . "' AND interpreter.intrpName='$ids' AND interpreter.deleted_flag=0 AND interpreter.order_cancel_flag=0 and interpreter.orderCancelatoin=0 UNION ALL SELECT COUNT(*) as jobs FROM telephone WHERE telephone.assignDate >='" . date('Y-m-d') . "' AND telephone.intrpName='$ids' AND telephone.deleted_flag=0 AND telephone.order_cancel_flag=0 and telephone.orderCancelatoin=0) as grp");
              }
              $week_jobs = $row_count_jobs['jobs_done'];
              $count_jobs_badge = $week_jobs >= 5 ? '<span style="font-size: 16px;" class="label label-danger" title="More than 5 future jobs!">' . $week_jobs . '</i>' : '<span style="font-size: 16px;" class="label label-primary">' . $week_jobs . '</i>';
              //code for 5 jobs ends

              //code for badges
              $badge = "";
              if ($table != 'translation') {
                $dur_in_hr = $assignDur / 60;
                $assignTime_req = substr($assignTime, 0, 5);
                $replaced_time = str_replace(':', '.', $assignTime_req);
                // $result_booked = $obj->read_all("id,assignDate,assignTime,assignDur,REPLACE(substr(assignTime,1,5),':','.') as new_time", "$table", "intrpName='$ids' and assignDate='$assignDate' and (REPLACE(substr(assignTime,1,5),':','.')=($replaced_time) OR REPLACE(substr(assignTime,1,5),':','.')=($replaced_time+$dur_in_hr)) AND deleted_flag=0 and order_cancel_flag=0");
                $result_booked = $obj->read_all("id,assignDate,assignTime,assignDur,new_time","(SELECT interpreter.id,interpreter.assignDate,interpreter.assignTime,interpreter.assignDur,REPLACE(substr(interpreter.assignTime,1,5),':','.') as new_time from interpreter","interpreter.intrpName='$ids' and interpreter.assignDate='$assignDate' and (REPLACE(substr(interpreter.assignTime,1,5),':','.')=($replaced_time) OR REPLACE(substr(interpreter.assignTime,1,5),':','.')=($replaced_time+$dur_in_hr)) AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 UNION ALL SELECT telephone.id,telephone.assignDate,telephone.assignTime,telephone.assignDur,REPLACE(substr(telephone.assignTime,1,5),':','.') as new_time from telephone WHERE telephone.intrpName='$ids' and telephone.assignDate='$assignDate' and (REPLACE(substr(telephone.assignTime,1,5),':','.')=($replaced_time) OR REPLACE(substr(telephone.assignTime,1,5),':','.')=($replaced_time+$dur_in_hr)) AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0) as grp");

                if ($result_booked->num_rows > 0) {
                  $allot = 'no';
                } else {
                  // $result_booked = $obj->read_all("id,assignDate,assignTime,assignDur/60 as assignDur,REPLACE(substr(assignTime,1,5),':','.') as new_time", "$table", "intrpName='$ids' and assignDate='$assignDate' AND deleted_flag=0 and order_cancel_flag=0 and orderCancelatoin=0");
                  $result_booked = $obj->read_all("id,assignDate,assignTime,assignDur,new_time","(SELECT interpreter.id,interpreter.assignDate,interpreter.assignTime,interpreter.assignDur/60 as assignDur,REPLACE(substr(interpreter.assignTime,1,5),':','.') as new_time FROM interpreter", "interpreter.intrpName='$ids' and interpreter.assignDate='$assignDate' AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.orderCancelatoin=0 UNION ALL SELECT telephone.id,telephone.assignDate,telephone.assignTime,telephone.assignDur/60 as assignDur,REPLACE(substr(telephone.assignTime,1,5),':','.') as new_time FROM telephone WHERE telephone.intrpName='$ids' and telephone.assignDate='$assignDate' AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.orderCancelatoin=0) as grp");
                  if ($result_booked->num_rows == 0) {
                    $allot = 'yes';
                  } else {
                    $allot_array = array();
                    while ($row_booked = $result_booked->fetch_assoc()) {
                      if ($replaced_time > $row_booked['new_time']) {
                        $get_dur = $replaced_time - ($row_booked['new_time'] + $row_booked['assignDur']);
                        if ($get_dur >= 0.30) {
                          array_push($allot_array, "yes");
                        } else {
                          array_push($allot_array, "no");
                        }
                      } else {
                        $get_dur = $row_booked['new_time'] - ($replaced_time + $dur_in_hr);
                        if ($get_dur >= 0.30) {
                          array_push($allot_array, "yes");
                        } else {
                          array_push($allot_array, "no");
                        }
                      }
                    }
                    if (in_array("no", $allot_array) && !in_array("yes", $allot_array)) {
                      $allot = "no";
                    } else if (!in_array("no", $allot_array) && in_array("yes", $allot_array)) {
                      $allot = "yes";
                    } else if (!in_array("no", $allot_array) && !in_array("yes", $allot_array)) {
                      $allot = "yes";
                    } else if (in_array("no", $allot_array) && in_array("yes", $allot_array)) {
                      $allot = "no and yes";
                    } else {
                      $allot = "yes";
                    }
                  }
                }
              } else {
                $allot = 'yes';
              }

              $allot_badge = $allot == 'yes' ? '' : '<img src="../images/badge_busy.png" width="25" title="Busy on ' . $assignDate . ' with other job"/>';
              //end badges code here

              $milesaway = "?";
              $postcodeinterp = $row_opt["postCode"];
              //echo $postcodeinterp;exit;
              //  include'getmilesbetween.php';  // temporary disable
              if ($table == 'interpreter') {
                $strHref_assign = "interp_assign.php?assign_id=$assign_id&table=$table&srcLang=$srcLang&gender=$genderForJob&dbs_checked=$dbs_checked&assignDate=$assignDate&assignTime=$assignTime&interp_id=$interp_idonly&int_rate_id=" . $extracted_data_int['id'];
              } else if ($table == 'telephone') {
                $strHref_assign = "interp_assign.php?assign_id=$assign_id&table=$table&srcLang=$srcLang&gender=$genderForJob&assignDate=$assignDate&assignTime=$assignTime&interp_id=$interp_idonly&int_rate_id=" . $extracted_data_int['id'];
              } else {
                $strHref_assign = "interp_assign.php?assign_id=$assign_id&table=$table&srcLang=$srcLang&assignDate=$assignDate&interp_id=$interp_idonly&int_rate_id=" . $extracted_data_int['id'];
              }

              // $button_link_assign = $week_jobs >= 5 ? "href='javascript:void(0)' disabled " : "data-id='$strHref_assign'";
              $button_link_assign = "data-id='$strHref_assign'";
              $display_interpreter_budget = $action_view_budget_rates ? "<br><b>Rate: £" . round($job_budget_int, 2) . "</b>" . $what_is_allowed : "";
              $budget_label = $table == 'translation' ? $gender : "Chargeable: " . $assignment_duration_int . $display_interpreter_budget;
              $strAncAttribs = "<td>" . $name_opt . "<br>" . $strRateStar . "</td>
              <td>" . $contactNo . "<br>" . $gender . "<br></td>
              <td width='20%'>$budget_label</td>";
              $append_city_tick = $table == "interpreter" && strpos(strtolower($JobCity), strtolower($interpCity)) > -1 ? " <i class='fa fa-check-circle text-success'></i>" : "";
              $strAncAttribs .= "<td>" . $interpCity . $append_city_tick . "</td>";
              $btn_view_messages = $action_view_text_messages ? '<br><button style="margin-top:6px;" data-job-type="' . $order_type . '" data-job-id="' . $assign_id . '" data-interpreter-id="' . $interp_idonly . '" data-interpreter-name="' . $name_opt . '" data-contact-no="' . $contactNo . '" data-country-name="' . $country . '" onclick="view_text_messages(this)" type="button" class="btn btn-xs btn-primary">Send New Message</button>' : '';
              $get_sent_message = $obj->read_specific("*", "job_messages", "order_type=" . $order_type . " AND order_id=" . $assign_id . " AND interpreter_id=" . $interp_idonly . " AND message_category=1");
              if (!empty($get_sent_message['id'])) {
                $job_message_response = !is_null($get_sent_message['response_date']) ? $misc->dated($get_sent_message['response_date']) : "";
                $array_message_status = array(0 => "<i title='Message not delivered to interpreter' class='fa fa-remove fa-2x text-danger'></i>", 1 => "<i title='Message delivered successfully' class='fa fa-check fa-2x text-success'></i>", 2 => "<i title='Interprerter responded back " . $job_message_response . "' class='fa fa-refresh fa-2x text-primary'></i>");
                $job_message_status = $array_message_status[$get_sent_message['status']];
                if ($get_sent_message['status'] == 2) {
                  $job_message_can_do = $get_sent_message['can_do'] == 1 ? "<br><small class='label label-success'>Available</small>" : "<br><small class='label label-danger'>Not Available</small>";
                } else {
                  $job_message_can_do = "";
                }
                $strAncAttribs .= "<td><i class='fa fa-check-circle fa-2x text-success' title='Message initiated to this interpreter on " . $misc->dated($get_sent_message['created_date']) . "'></i> " . $job_message_status . $job_message_can_do . $btn_view_messages . "</td>";
              } else {
                $strAncAttribs .= "<td>" . ($action_view_text_messages ? "<a style='margin-top:6px;' data-job-type='" . $order_type . "' data-job-id='" . $assign_id . "' data-interpreter-id='" . $interp_idonly . "' data-interpreter-name='" . $name_opt . "' data-contact-no='" . $contactNo . "' data-country-name='" . $country . "' onclick='view_text_messages(this)' href='javascript:void(0)' title='Send message to this interpreter' class='btn btn-primary btn-xs'>Send New Message</a>" : "<span class='text-danger'>No Action</span>" ) . "</td>";
              }
              $strAncAttribs .= "<td align='center'>$allot_badge $count_jobs_badge</td>";
              if ($action_view_interpreter_profile || $action_assign_job) {
                $strAncAttribs .= "<td width='20%'>" . ($action_view_interpreter_profile ? "<a data-id='$interp_idonly' href='javascript:void(0)' class='btn btn-default btn-sm btn_view_interpreter'>View</a> &nbsp; " : "") . ($action_assign_job ? "<a href='javascript:void(0)' class='btn ".($intrp_jobOverlapCount>0?"btn-danger":"btn-info")." btn-sm btn_assign_interpreter' data-overlap='$intrp_jobOverlapCount' $button_link_assign>Assign Job ".($intrp_jobOverlapCount>0?"<br><small>Time Conflict</small>":"")."</a>" : "" ) ."</td>";
              } else {
                $strAncAttribs .= "<td width='20%'>No action allowed!</td>";
              }
              $strAncAttribs .= "</tr>";
              if (($isred || $isblue)) {
                $strAnc = "<tr class='bg-danger'> " . $strAncAttribs;
                $arrGrp2 .= $strAnc;
              } else {
                if ($isAdhoc) {
                  $strAnc = "<tr class='bg-adhoc'> " . $strAncAttribs;
                  $arrGrp3 .= $strAnc;
                } else {
                  if ($badfeed) {
                    $strAnc = "<tr class='bg-info'> " . $strAncAttribs;
                    $arrGrp1 .= $strAnc;
                  } else {
                    $strAnc = "<tr class='bg-success'> " . $strAncAttribs;
                    $arrGrp0 .= $strAnc;
                  }
                }
              }
            }
          } ?>
      </p>

      <fieldset>
        <?php $append_rate_title = $table != 'transaltion' ? "<b class='pull-right'>" . $extracted_data['title'] . "</b>" : ""; ?>
        <legend>Job Details : <?= $find_string . " # " . $get_job_details['id'] . $append_rate_title ?></legend>
        <table class="table table-bordered">
          <tr>
            <th class="bg-info">Company</th>
            <td><?= $get_job_details['orgName'] ?></td>
            <?php if ($table != "translation") {
              echo $action_view_budget_rates ? "<th class='bg-info'><h3 style='margin: auto;'><b>£" . number_format($job_budget, 2) . "</b></h3></th>" : ""; ?>
              <td><b>Actual Duration:<span class="pull-right"><?= $actual_duration ?></span></b><br>
                <?= $job_budget != 0 ? "<b class='text-success'>Chargable Duration: <span class='pull-right'>" . $assignment_duration . "</span></b>" : "<b class='text-danger'>Not Calculated</b>" ?></td>
            <?php }
            if ($table == "interpreter") { ?>
              <th class="bg-info">City</th>
              <td><?= $get_job_details['assignCity'] ?: "---" ?></td>
            <?php } ?>
            <th class="bg-info">Assignment Date</th>
            <td><?= $assignDate ? $assignDate . ' ' . $assignTime : "---" ?></td>
          </tr>
        </table>

        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#recommended_interpreters">Recommended Interpreters</a></li>
          <li><a data-toggle="tab" href="#can_do">Can Do - Completed</a></li>
          <li><a data-toggle="tab" href="#newly_registered">Newly Registered</a></li>
          <li><a data-toggle="tab" href="#adhoc_registered">Adhoc Interpreters</a></li>
        </ul>
        <div class="tab-content">
          <div id="recommended_interpreters" class="tab-pane fade in active">
            <br>
            <table class="table table-bordered datatable">
              <thead>
                <th>Interpreter Name</th>
                <th>Contact</th>
                <th><?= $table == 'translation' ? "Gender" : "Rate Charges" ?></th>
                <!-- <?php //if($table != 'telephone'):
                      ?>
                <th>Miles</th>
                <?php //else:
                ?>
                <th>City</th>
                <?php //endif;
                ?> -->
                <th>City</th>
                <th>Message</th>
                <th>Badges</th>
                <th>Action</th>
              </thead>
              <tbody>
                <?php echo $arrGrp0; ?>
              </tbody>
            </table>
          </div>
          <div id="can_do" class="tab-pane fade">
            <br>
            <table class="table table-bordered datatable">
              <thead>
                <th>Interpreter Name</th>
                <th>Contact</th>
                <th><?= $table == 'translation' ? "Gender" : "Rate Charges" ?></th>
                <!-- <?php //if($table != 'telephone'):
                      ?>
                <th>Miles</th>
                <?php //else:
                ?>
                <th>City</th>
                <?php //endif;
                ?> -->
                <th>City</th>
                <th>Message</th>
                <th>Badges</th>
                <th>Action</th>
              </thead>
              <tbody>
                <?php echo $arrGrp1; ?>
              </tbody>
            </table>
          </div>
          <div id="newly_registered" class="tab-pane fade">
            <br>
            <table class="table table-bordered datatable">
              <thead>
                <th>Interpreter Name</th>
                <th>Contact</th>
                <th><?= $table == 'translation' ? "Gender" : "Rate Charges" ?></th>
                <!-- <?php //if($table != 'telephone'):
                      ?>
                <th>Miles</th>
                <?php //else:
                ?>
                <th>City</th>
                <?php //endif;
                ?> -->
                <th>City</th>
                <th>Message</th>
                <th>Badges</th>
                <th>Action</th>
              </thead>
              <tbody>
                <?php echo $arrGrp2; ?>
              </tbody>
            </table>
          </div>
          <div id="adhoc_registered" class="tab-pane fade">
            <br>
            <table class="table table-bordered datatable">
              <thead class="bg-success">
                <th>Interpreter Name</th>
                <th>Contact</th>
                <th><?= $table == 'translation' ? "Gender" : "Rate Charges" ?></th>
                <!-- <?php //if($table != 'telephone'):
                      ?>
                <th>Miles</th>
                <?php //else:
                ?>
                <th>City</th>
                <?php //endif;
                ?> -->
                <th>City</th>
                <th>Message</th>
                <th>Badges</th>
                <th>Action</th>
              </thead>
              <tbody>
                <?php echo $arrGrp3; ?>
              </tbody>
            </table>
          </div>
        </div>

      </fieldset>

      <p style="display:none" id="demo"></p>
      <br><br>

      <?php
          if (!empty($interp_id)) {
            $bad_result = '0';
            if ($table != 'translation') {
              $assignDate = @$_GET['assignDate'];
              $assignTime = @$_GET['assignTime'];
              //check if already assigned
              $result = $obj->read_all("$fields", "$table", "intrpName='$interp_id' and assignDate='$assignDate' and assignTime='$assignTime' AND deleted_flag=0 AND order_cancel_flag=0 and orderCancelatoin=0");
              if ($result->fetch_assoc() > 0) {
                $bad_result = '1';
              }
            }
            if ($bad_result == '1') { ?>

          <script>
            alert('Sorry! This interpreter is already booked on this Date.');
          </script>
        <?php
              return;
            } else { ?>
          <fieldset class="row1">
            <legend>
              <h2>Assign Interpreter</h2>
            </legend>
            <?php include 'viewinterpassess.php'; ?>
          </fieldset>
      <?php }
          }
      ?>
  </form>
</body>

</html>

<script>
  //$(document).ready(function(){
  //$("#exampleModal").modal();
  //});
</script>
<!-- Modal -->
<div class="modal fade" id="exampleModal" style="margin-top: 70px;" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Notice</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Sorry! This interpreter is already booked on this Date & Time.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!--Ajax processing modal-->
<div class="modal" id="process_modal">
  <div class="modal-dialog modal-lg" style="width: 100%;">
    <div class="modal-content">
      <div class="modal-body process_modal_attach">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<script>
  function popupwindow(url, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
  }
  $('.btn_view_interpreter').on('click', function() {
    var clicked_id = $(this).attr('data-id');
    popupwindow('full_view_interpreter.php?view_id=' + clicked_id, 'View profile of interpreter', 1000, 650);
  });
  $('.btn_assign_interpreter').on('click', function() {
    var clicked_link = $(this).attr('data-id');
    var overlap_info = $(this).attr('data-overlap');
    if(overlap_info>0){
      if (confirm('This interpreter is already booked in this time for another job, are you sure you still want to assign the job to this interpreter?')) {
        if (confirm('Are you sure to assign this interpreter for this job?')) {
          window.location.href = clicked_link;
          $('#load-img').show();
        }
      }
    }else{
      if (confirm('Are you sure to assign this interpreter for this job?')) {
        window.location.href = clicked_link;
        $('#load-img').show();
      }
    }
    
  });

  function view_text_messages(element) {
    var job_type = $(element).attr("data-job-type");
    var job_id = $(element).attr("data-job-id");
    var interpreter_id = $(element).attr("data-interpreter-id");
    var interpreter_name = $(element).attr("data-interpreter-name");
    var contact_no = $(element).attr("data-contact-no");
    var country = $(element).attr("data-country-name");
    if (job_type && job_id && interpreter_id) {
      $('.process_modal_attach').html("<center><i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i><br><h3>Loading ...<br><br>Please Wait !!!</h3></center>");
      $('#process_modal').modal('show');
      $('body').removeClass('modal-open');
      $.ajax({
        url: 'ajax_add_interp_data.php',
        method: 'post',
        dataType: 'json',
        data: {
          job_id: job_id,
          job_type: job_type,
          interpreter_id: interpreter_id,
          interpreter_name: interpreter_name,
          contact_no: contact_no,
          country: country,
          screen: "interp_assign",
          view_text_messages: 1
        },
        success: function(data) {
          if (data['status'] == 1) {
            $('.process_modal_attach').html(data['body']);
            // Write message content
            var MESSAGE_BODY = "Let us know about your availability for:\n<?= $srcLang . ' ' . $find_string ?> assignment on (<?= $misc->dated($assignDate) ?>).\nMore Details";
            $('#message_body').val(MESSAGE_BODY);
            $(".character_count").text("Characters: " + $("#message_body").val().length + "/120");
          } else {
            alert("Cannot load job response. Please try again!");
          }
        },
        error: function(data) {
          console.log("Error code : " + data.status + " , Error message : " + data.statusText);
        }
      });
    } else {
      alert("Error: Please select valid job details or refresh the page! Thank you");
    }
  }

  function send_text_message(element) {
    if ($('#write_interpreter_phone').val() && $('#message_body').val()) {
      $(element).addClass("hidden");
      $.ajax({
        url: 'process/third_party_apis.php',
        method: 'post',
        dataType: 'json',
        data: {
          order_id: $('#write_order_id').val(),
          order_type: $('#write_order_type').val(),
          interpreter_id: $('#write_interpreter_id').val(),
          interpreter_phone: $('#write_interpreter_phone').val(),
          interpreter_country: $('#write_interpreter_country').text(),
          interpreter_email: "",
          message_body: $('#message_body').val(),
          send_text_message: 1
        },
        success: function(data) {
          if (data['status'] == 1) {
            alert(data['message']);
            $('#process_modal').modal("hide");
          } else {
            alert(data['message']);
            $(element).removeClass("hidden");
          }
        },
        error: function(data) {
          alert("Error code : " + data.status + " , Error message : " + data.statusText);
        }
      });
    } else {
      if (!$('#write_interpreter_phone').val()) {
        $('#write_interpreter_phone').focus();
      } else {
        $('#message_body').focus();
      }
    }
  }
</script> 