<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__); ?>
<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include_once('function.php');
include 'class.php';
$table = 'interpreter_reg';
//Access actions
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=28 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_view_profile = $_SESSION['is_root'] == 1 || in_array(41, $get_actions);
$action_approve_documents = $_SESSION['is_root'] == 1 || in_array(249, $get_actions);
$action_edit_profile = $_SESSION['is_root'] == 1 || in_array(42, $get_actions);
$action_interp_id_card = $_SESSION['is_root'] == 1 || in_array(216, $get_actions);
$action_update_availability = $_SESSION['is_root'] == 1 || in_array(43, $get_actions);
$action_update_rates = $_SESSION['is_root'] == 1 || in_array(44, $get_actions);
$action_delete = $_SESSION['is_root'] == 1 || in_array(45, $get_actions);
$action_restore = $_SESSION['is_root'] == 1 || in_array(46, $get_actions);
$action_edited_history = $_SESSION['is_root'] == 1 || in_array(47, $get_actions);
$action_change_password = $_SESSION['is_root'] == 1 || in_array(48, $get_actions);
$action_add_rating = $_SESSION['is_root'] == 1 || in_array(49, $get_actions);
$action_confirm_temporary = $_SESSION['is_root'] == 1 || in_array(50, $get_actions);
$action_blacklist = $_SESSION['is_root'] == 1 || in_array(51, $get_actions);
$action_activate_de_activate_account = $_SESSION['is_root'] == 1 || in_array(52, $get_actions);
$action_dropdown_de_activated_filter = $_SESSION['is_root'] == 1 || in_array(53, $get_actions);
$action_dropdown_trashed_filter = $_SESSION['is_root'] == 1 || in_array(54, $get_actions);
$action_dropdown_active_filter = $_SESSION['is_root'] == 1 || in_array(206, $get_actions);
$action_dropdown_newly_reg_filter = $_SESSION['is_root'] == 1 || in_array(207, $get_actions);
$action_missing_docs_filter = $_SESSION['is_root'] == 1 || in_array(208, $get_actions);
$action_export_to_excel = $_SESSION['is_root'] == 1 || in_array(55, $get_actions);
$action_view_paid_salaries = $_SESSION['is_root'] == 1 || in_array(56, $get_actions);
$action_language_assessment = $_SESSION['is_root'] == 1 || in_array(57, $get_actions);
$action_view_references = $_SESSION['is_root'] == 1 || in_array(142, $get_actions);
$action_reject = $_SESSION['is_root'] == 1 || in_array(202, $get_actions);
$action_invite = $_SESSION['is_root'] == 1 || in_array(203, $get_actions);
$name = @$_GET['name'];
$interp_email = @$_GET['interp_email'];
$InterpPhoneNumber = @$_GET['InterpPhoneNumber'];
$srchgender = @$_GET['srchgender'];
$city = @$_GET['city'];
$lang = @$_GET['lang'];
if ($lang == "all") {
  $lang = "";
}
$tp = @$_GET['tp'];
$isAdhoc = @$_GET['isAdhoc'];
$put_delete = $tp == 'tr' ? 'deleted_flag=1' : 'deleted_flag=0';
$srcdbs_checked = @$_GET['srcdbs_checked'];
$array_tp = array('tr' => 'Trashed', 'ac' => 'Active', 'da' => 'De-Activated', 'nr' => 'Newly Registered');
$class = $tp == 'tr' ? 'alert-danger' : 'alert-info';
$page_title = $array_tp[$tp] == 'Active' ? '' : $array_tp[$tp];
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;
if (isset($_GET['tp'])) {
  if ($_GET['tp'] == 'ac') {
    $put_active = "and $table.active=0";
  }
  if ($_GET['tp'] == 'da') {
    $put_active = "and $table.active=1";
  }
  if ($_GET['tp'] == 'nr') {
    $put_active = "and $table.is_temp=1";
  }
} else {
  $put_active = "and $table.active=0";
}
$missing_docs = @$_GET['missing_docs'];
if (isset($missing_docs)) {
  $put_missing_docs = "missing_docs=1";
} else {
  $put_missing_docs = "missing_docs=0";
} ?>
<!doctype html>
<html lang="en">

<head>
  <title><?php echo $page_title; ?> Interpreters List</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    html,
    body {
      background: none !important;
    }

    .modal {
      overflow-y: auto !important;
    }
  </style>
</head>
<script>
  function myFunction() {
    var append_url = "<?php echo basename(__FILE__) . "?1"; ?>";
    if ($("#missing_docs").is(':checked')) {
      append_url += '&missing_docs=1';
    }
    if ($("#pending_approvals").is(':checked')) {
      append_url += '&pending_approval=1';
    }
    var name = $("#name").val();
    if (name) {
      append_url += '&name=' + name;
    }
    var srchgender = $("#srchgender").val();
    if (srchgender) {
      append_url += '&srchgender=' + srchgender;
    }
    var city = $("#city").val();
    if (city) {
      append_url += '&city=' + city;
    }
    var lang = $("#lang").val();
    if (lang) {
      append_url += '&lang=' + lang;
    }
    var srcdbs_checked = $("#srcdbs_checked").val();
    if (srcdbs_checked) {
      append_url += '&srcdbs_checked=' + srcdbs_checked;
    }
    var tp = $("#tp").val();
    if (tp) {
      append_url += '&tp=' + tp;
    }
    var isAdhoc = $("#isAdhoc").val();
    if (isAdhoc) {
      append_url += '&isAdhoc=' + isAdhoc;
    }
    var interp_email = $("#email").val();
    if (interp_email) {
      append_url += '&interp_email=' + interp_email;
    }
    var phoneNumber = $("#phoneNumber").val();
    if (phoneNumber) {
      append_url += '&InterpPhoneNumber=' + phoneNumber;
    }
    window.location.href = append_url;
  }
</script>

<?php include 'header.php'; ?>

<body>
  <?php include 'nav2.php'; ?>
  <!-- end of sidebar -->
  <style>
    .table>tbody>tr>td,
    .table>tbody>tr>th,
    .table>tfoot>tr>td,
    .table>tfoot>tr>th,
    .table>thead>tr>td,
    .table>thead>tr>th {
      padding: 8px !important;
      cursor: pointer;
    }

    html,
    body {
      background: #fff !important;
    }

    .div_actions {
      position: absolute;
      margin-top: -37px;
      background: #ffffff;
      border: 1px solid lightgrey;
    }

    .alert {
      padding: 6px;
    }

    .div_actions .fa {
      font-size: 18px;
    }

    .w3-btn,
    .w3-button {
      padding: 4px 6px !important;
    }
  </style>
  <section class="container-fluid" style="overflow-x:auto">
    <div class="col-md-12">
      <header>
        <center>
          <div class="alert <?php echo $class; ?> col-sm-3">
            <a href="<?php echo basename(__FILE__); ?>" class="alert-link"><?php echo $page_title; ?> Interpreters List</a>
          </div>
        </center>
        <?php if ($action_dropdown_de_activated_filter || $action_dropdown_trashed_filter || $action_dropdown_active_filter || $action_dropdown_newly_reg_filter) { ?>
          <div class="form-group col-md-2 col-sm-4">
            <select id="tp" onChange="myFunction()" name="tp" class="form-control">
              <option value="" disabled <?= empty($tp) ? 'selected' : '' ?>>Filter by Type</option>
              <option <?=$tp == 'ac' ? 'selected' : ''?> <?= $action_dropdown_active_filter ? '' : 'class="hidden"' ?> value="ac">Active</option>
              <option <?=$tp == 'nr' ? 'selected' : ''?> <?= $action_dropdown_newly_reg_filter ? '' : 'class="hidden"' ?> value="nr">Newly Registered</option>
              <option <?=$tp == 'da' ? 'selected' : ''?> <?= $action_dropdown_de_activated_filter ? '' : 'class="hidden"' ?> value="da">De-Activated</option>
              <option <?=$tp == 'tr' ? 'selected' : ''?> <?= $action_dropdown_trashed_filter ? '' : 'class="hidden"' ?> value="tr">Trashed</option>
            </select>
          </div>
        <?php } else { ?>
          <input type="hidden" value='ac' id='tp' />
        <?php } ?>
          <div class="form-group col-md-2">
            <select id="isAdhoc" onChange="myFunction()" name="isAdhoc" class="form-control">
              <option value="" <?= !isset($isAdhoc) ? 'selected' : '' ?>>-- Filter By Registration --</option>
              <option <?=isset($isAdhoc) && $isAdhoc == 0 ? 'selected' : ''?> value="0">Regular Interpreters</option>
              <option style="color:green" <?=isset($isAdhoc) && $isAdhoc == 1 ? 'selected' : ''?> value="1">Adhoc Interpreters</option>
            </select>
          </div>
        <?php if ($action_missing_docs_filter) { ?>
          <div class="form-group col-md-2 col-sm-4">
            <label title="Filter interpreters with important documents missing" class="btn btn-default">
              <input <?= isset($missing_docs) ? 'checked' : '' ?> type="checkbox" id="missing_docs" onchange="myFunction()"> Missing Documents
            </label>
          </div>
        <?php } if ($action_approve_documents) { ?>
          <div class="form-group col-md-2 col-sm-4">
            <label title="Filter interpreters with important documents missing" class="btn btn-default">
              <input <?= isset($_GET['pending_approval']) ? 'checked' : '' ?> type="checkbox" id="pending_approvals" onchange="myFunction()"> Pending Review
            </label>
          </div>
        <?php }
        if ($action_export_to_excel) { ?>
          <div class="form-group col-md-1 col-sm-4">
            <a id="btn_export" href="reports_lsuk/excel/<?php echo basename(__FILE__) . '?name=' . $name . '&srchgender=' . $srchgender . '&city=' . $city . '&lang=' . $lang . '&srcdbs_checked=' . $srcdbs_checked . '&tp=' . $tp . '&missing_docs=' . $missing_docs; ?>" title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel <i class="glyphicon glyphicon-download"></i></span></a>
          </div>
        <?php } ?>
        <div class="col-md-12">
          <div class="form-group col-md-3 col-sm-4">
            <select id="name" onChange="myFunction()" name="name" class="form-control">
              <?php
              $sql_opt = "SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city,interpreter_reg.email,interpreter_reg.contactNo FROM interpreter_reg WHERE $put_delete $put_active ORDER BY interpreter_reg.name ASC";
              $result_opt = mysqli_query($con, $sql_opt);
              $options = "";

              $emailOptions = "";
              $phoneNumberOption = "";
              while ($row_opt = mysqli_fetch_array($result_opt)) {
                $code = $row_opt["name"];
                $interPemail = $row_opt["email"];
                $name_opt = $row_opt["name"] ?: "No Name";
                $city_opt = $row_opt["city"];
                $gender = $row_opt["gender"];
                $options .= "<option value='$code'>" . $name_opt . ' (' . $gender . ')' . ' (' . $city_opt . ') #' . $row_opt['id'] . '</option>';
                if ($interPemail != "") {
                  $selectedEmailOption = ($interp_email == $interPemail) ? 'selected' : '';
                  $emailOptions .= "<option value='$interPemail' $selectedEmailOption>$interPemail</option>";
                }
                $phoneNumber = $row_opt["contactNo"];
                if ($phoneNumber != "") {
                  $selectedPhoneNumberOption = ($phoneNumber == $InterpPhoneNumber) ? 'selected' : '';
                  $phoneNumberOption .= "<option value='$phoneNumber' $selectedPhoneNumberOption>$phoneNumber</option>";
                }
              }
              ?>
              <?php if (!empty($name)) { ?>
                <option><?php echo $name; ?></option>
              <?php } else { ?>
                <option value="">-- Interpreter --</option>
              <?php } ?>
              <?php echo $options; ?>
            </select>
          </div>
          <div class="form-group col-md-2 col-lg-2 col-sm-4">
            <select name="email" id="email" class="form-control" onChange="myFunction()">
              <option value="">Email</option>
              <?php echo $emailOptions; ?>
            </select>
          </div>
          <div class="form-group col-md-2 col-lg-2 col-sm-4">
            <select name="phoneNumber" id="phoneNumber" class="form-control" onChange="myFunction()">
              <option value="">Phone Number</option>
              <?php echo $phoneNumberOption; ?>
            </select>
          </div>

          <div class="form-group col-md-2 col-sm-4">
            <select name="srcdbs_checked" id="srcdbs_checked" onChange="myFunction()" class="form-control">
              <?php if ($srcdbs_checked != '') { ?>
                <option value="<?php echo $srcdbs_checked; ?>"><?= $srcdbs_checked == 0 ? 'Yes' : 'No' ?></option>
              <?php } else { ?>
                <option value="" selected> Select DBS </option>
              <?php } ?>
              <option value="0">Yes</option>
              <option value="1">No</option>
            </select>
          </div>
          <div class="form-group col-md-3 col-sm-4">
            <select name="lang" id="lang" onChange="myFunction()" class="form-control">
              <?php
              $sql_opt = "SELECT lang FROM lang ORDER BY lang ASC";
              $result_opt = mysqli_query($con, $sql_opt);
              $options = "";
              while ($row_opt = mysqli_fetch_array($result_opt)) {
                $code = $row_opt["lang"];
                $name_opt = $row_opt["lang"];
                $options .= "<option value='$code'>" . $name_opt . "</option>";
              }
              ?>
              <option value="all"> Select All </option>
              <?php if (!empty($lang)) { ?>
                <option selected><?php echo $lang; ?></option>
              <?php } ?>
              <?php echo $options; ?>
            </select>
          </div>
          <div class="form-group col-md-2 col-sm-4">
            <select name="srchgender" id="srchgender" onChange="myFunction()" class="form-control">
              <?php if (!empty($srchgender)) { ?>
                <option><?php echo $srchgender; ?></option>
              <?php } else { ?>
                <option value="">Select Gender</option>
              <?php } ?>
              <option>Male</option>
              <option>Female</option>
            </select>
          </div>
          <div class="form-group col-md-2 col-sm-4">
            <select name="city" id="city" onChange="myFunction()" class="form-control">
              <?php if (!empty($city)) { ?>
                <option><?php echo $city; ?></option>
              <?php } else { ?>
                <option value="">Select City</option>
              <?php } ?>
              <optgroup label="England">
                <option>Bath</option>
                <option>Birmingham</option>
                <option>Bradford</option>
                <option>Bridgwater</option>
                <option>Bristol</option>
                <option>Buckinghamshire</option>
                <option>Cambridge</option>
                <option>Canterbury</option>
                <option>Carlisle</option>
                <option>Chippenham</option>
                <option>Cheltenham</option>
                <option>Cheshire</option>
                <option>Coventry</option>
                <option>Derby</option>
                <option>Dorset</option>
                <option>Exeter</option>
                <option>Frome</option>
                <option>Gloucester</option>
                <option>Hereford</option>
                <option>Leeds</option>
                <option>Leicester</option>
                <option>Liverpool</option>
                <option>London</option>
                <option>Manchester</option>
                <option>Newcastle</option>
                <option>Northampton</option>
                <option>Norwich</option>
                <option>Nottingham</option>
                <option>Oxford</option>
                <option>Plymouth</option>
                <option>Pool</option>
                <option>Portsmouth</option>
                <option>Salford</option>
                <option>Shefield</option>
                <option>Somerset</option>
                <option>Southampton</option>
                <option>Swindon</option>
                <option>Suffolk</option>
                <option>Surrey</option>
                <option>Taunton</option>
                <option>Trowbridge</option>
                <option>Truro</option>
                <option>Warwick</option>
                <option>Wiltshire</option>
                <option>Winchester</option>
                <option>Wells</option>
                <option>Weston Super Mare</option>
                <option>Worcester</option>
                <option>Wolverhampton</option>
                <option>York</option>
              </optgroup>
              <optgroup label="Scotland">
                <option>Dundee</option>
                <option>Edinburgh</option>
                <option>Glasgow</option>
              </optgroup>
              <optgroup label="Wales">
                <option>Cardiff</option>
                <option>Newport</option>
                <option>Swansea</option>
              </optgroup>
            </select>

          </div>
          <div class="form-group col-md-2 col-sm-4">
            <a href="reg_interp_list.php" class="btn btn-primary text-white">Reset</a>
          </div>
      </header>

      <div>
        <div>
          <?php if ($_SESSION['returned_message']) {
            echo $_SESSION['returned_message'];
            unset($_SESSION['returned_message']);
          } ?>
          <table class="table table-bordered table-hover" cellspacing="0" width="100%">
            <thead class="bg-primary">
              <tr>
                <th>Linguist Name</th>
                <th>Gender</th>
                <th>Location (City)</th>
                <th>Mobile #</th>
                <th>Landline No</th>
                <th>Email</th>
                <th>Status</th>
                <?= ($tp == 'tr' ? '<th>Trashed Info</th>' : '') ?>
              </tr>
            </thead>
            <tbody>
              <?php
              if (isset($missing_docs)) {
                $append_missing_docs = " AND ((interpreter_reg.agreement='') OR (interpreter_reg.interp_pix = '') OR
                (interpreter_reg.crbDbs='' AND interpreter_reg.interp='Yes') OR (interpreter_reg.ni='') OR 
                (interpreter_reg.identityDocument='' AND interpreter_reg.uk_citizen=1) OR 
                (interpreter_reg.work_evid_file='' AND interpreter_reg.uk_citizen=0) OR 
                (interpreter_reg.acNo=''))";
              }
              if(isset($_GET['pending_approval'])){
                $pending_approvals = " AND pending_approvals <> ''
  AND JSON_CONTAINS(pending_approvals, '{\"action\":\"pending\"}')";
                $append_missing_docs .=$pending_approvals;
              }
              if ($name) {
                $append_name = " and name like '$name%'";
              }
              if ($interp_email) {
                $append_email = " and email like '$interp_email%'";
              }
              if ($InterpPhoneNumber) {
                $append_contact = " and contactNo like '$InterpPhoneNumber%'";
              }
              if ($srchgender) {
                $append_gender = " and gender = '$srchgender'";
              }
              if (isset($isAdhoc)) {
                $append_isAdhoc = " and isAdhoc='$isAdhoc'";
              }
              if ($city) {
                $append_city = " and city like '$city%'";
              }
              if ($srcdbs_checked) {
                $append_dbs = " and $table.dbs_checked like '$srcdbs_checked%'";
              }
              if ($_SESSION['is_root'] == 1 || $_SESSION['prv'] == 'Finance') {
                if ($adLang = 'adLang' && $lang == '') {
                  $query = "SELECT distinct $table.* FROM $table 		
                  where $table.$put_delete $append_missing_docs $append_dbs $append_name $append_email $append_contact $append_gender $append_city $append_isAdhoc $put_active	  
                  LIMIT {$startpoint} , {$limit}";
                } else {
                  $query = "SELECT distinct $table.* FROM $table
                  JOIN interp_lang ON interpreter_reg.code=interp_lang.code	 
                  where $table.$put_delete and interp_lang.lang = '$lang' 
                  $append_missing_docs $append_dbs $append_name $append_email $append_contact $append_gender $append_city $append_isAdhoc $put_active group by email  LIMIT {$startpoint} , {$limit}";
                }
              }

              if ($_SESSION['is_root'] == 0 && $_SESSION['prv'] != 'Finance') {
                if ($adLang = 'adLang' && $lang == '') {
                  $query = "SELECT distinct $table.* FROM $table where $table.$put_delete $append_missing_docs $append_dbs $append_name $append_email $append_contact $append_gender $append_city $append_isAdhoc $put_active LIMIT {$startpoint} , {$limit}";
                } else {
                  $query = "SELECT distinct $table.* FROM $table JOIN interp_lang ON interpreter_reg.code=interp_lang.code	 
                  where $table.$put_delete $append_missing_docs $append_dbs $append_name $append_email $append_contact $append_gender $append_city $append_isAdhoc $put_active and interp_lang.lang = '$lang' group by email  LIMIT {$startpoint} , {$limit}";
                }
              }

              //echo $query;exit;
              $result = mysqli_query($con, $query);
              while ($row = mysqli_fetch_array($result)) {
                $missing_doc = $acttObj->read_specific("CONCAT(CASE WHEN (applicationForm='') THEN '<b class=\"label label-danger\">* Application Form</b><br>' ELSE '' END ,
                CASE WHEN (agreement='') THEN '<b class=\"label label-danger\">* Agreement Document</b><br>' ELSE '' END,
                CASE WHEN (country_of_origin='') THEN '<b class=\"label label-danger\">* Country Of Origin</b><br>' ELSE '' END,
                CASE WHEN (interp_pix='') THEN '<b class=\"label label-danger\">* Profile Photo</b><br>' ELSE '' END,
                CASE WHEN (crbDbs='' AND interpreter_reg.interp='Yes') THEN '<b class=\"label label-danger\">* DBS Document</b><br>' ELSE '' END,
                CASE WHEN (ni='') THEN '<b class=\"label label-danger\">* National Insurance Document</b><br>' ELSE '' END,
                CASE WHEN (interpreter_reg.identityDocument='' AND interpreter_reg.uk_citizen=1) THEN '<b class=\"label label-danger\">* Identity Document</b><br>' ELSE '' END,
                CASE WHEN (interpreter_reg.work_evid_file='' AND interpreter_reg.uk_citizen=0) THEN '<b class=\"label label-danger\">* Right To Work Document</b><br>' ELSE '' END,
                CASE WHEN (acNo='') THEN '<b class=\"label label-danger\">* Bank Details</b><br>' ELSE '' END ) as missed", "interpreter_reg", "id=" . $row['id'])['missed'];
                $dob = $row['dob'];
                $buildingName = $row['buildingName'];
                $line1 = $row['line1'];
                $city = $row['city'];
                $ni = $row['ni']; ?>

                <tr <?php if ($row['is_temp'] == 1) { ?>title="This interpreter is registered by Temporary Role. Kindly confirm to process." style="background-color:#dbe5a39e;" <?php } ?> <?php if ($row['active'] == 1) { ?> class="bg-danger tr_data" title="<?php echo ucwords($row['name']); ?> is De Activated. Click on row to see actions" <?php } else { ?> title="Click on row to see actions" class="tr_data" <?php } ?>>
                  <td id="emtpyclr">
                    <?php if (empty($dob) || $dob == '0000-00-00' || empty($buildingName) || empty($buildingName) || empty($city) || empty($line1) || empty($ni)) { ?>
                      <span style="color:#F00"><?php echo ucwords($row['name']).'<a href="#" data-toggle="tooltip" data-placement="right" title="'.(!empty($row['week_remarks'])?$row['week_remarks']:"No Notes").'"><i class="fa fa-info" aria-hidden="true" style="width: 21px; padding: 2px 0; border: 1px solid #ddd; border-radius: 2rem; margin: .15rem;text-align:center;"></i></a>'; ?></span>
                    <?php } else {
                      echo $row['name'].'<a href="#" data-toggle="tooltip" data-placement="right" title="'.(!empty($row['week_remarks'])?$row['week_remarks']:"No Notes").'"><i class="fa fa-info" aria-hidden="true" style="width: 21px; padding: 2px 0; border: 1px solid #ddd; border-radius: 2rem; margin: .15rem;text-align:center;"></i></a>';
                    }
                    echo $row['isAdhoc'] == '1' ? "<span class='pull-right label label-success'>Adhoc Interpreter</span>" : ""; ?>
                  </td>
                  <td><?php echo $row['gender']; ?></td>
                  <td><?php echo $row['city'] == "Not in List" ? '<span style="color:red"><b>Not in List</b></span>' : $row['city']; ?></td>
                  <td><?php echo $row['contactNo'] ? "<span class='label label-primary'>" . $row['contactNo'] . "</span>" : ""; ?></td>
                  <td><?php echo $row['contactNo2'];
                      echo $row['other_number'] ? "<span class='label label-info'>" . $row['other_number'] . "</span>" : ""; ?></td>
                  <td><?php echo $row['email']; ?></td>
                  <td><?php if ($row['availability_option'] == 1) {
                        echo $row['is_marked'] == 1 ? "<span class='label label-success' style='margin:1px;' title='Available today'><i class='fa fa-check'></i></span>" : "<span class='label label-warning' style='margin:1px;' title='Not Available'><i class='fa fa-exclamation'></i></span>";
                      }
                      if (strlen($row['contactNo']) == 0 || strlen($row['contactNo']) > 11) {
                        echo "<span class='label label-warning'>Number not valid</span><br>";
                      }
                      if (!empty($missing_doc)) {
                        echo $missing_doc;
                      } ?></td>
                      <?php if ($tp == 'tr') {
                        echo '<td><small>By ' . ucwords($row['deleted_by']) . ' at ' . $misc->dated($row['deleted_date']) . '</small>' . ($row['deleted_reason'] ? ' <i class="fa fa-exclamation-circle" title="' . $row['deleted_reason'] . '"></i>' : '') . '</td>';
                      } ?>
                </tr>
                <tr class="div_actions" style="display:none">
                  <td colspan="9" style="padding: 0px !important;">
                    <?php if ($tp == 'tr') {
                      if ($action_restore) { ?>
                        <a title="Restore Interpreter" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-green w3-border w3-border-blue" onClick="popupwindow('trash_restore.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','Restore record interpreter',520,350)"><i class="fa fa-refresh"></i></a>
                      <?php }
                    } else {
                      if ($action_view_profile) { ?>
                        <a title="View Details" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" onclick="popupwindow('full_view_interpreter.php?view_id=<?php echo $row['id']; ?>', 'View profile of interpreter', 1100, 900);"><i class="fa fa-eye"></i></a>
                        <?php }
                      if ($row['is_temp'] == 1) {
                        if ($action_invite) { ?>
                          <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" onclick="popupwindow('send_invites.php?interpreter_id=<?php echo $row['id']; ?>', 'Send interpreter invite', 1100, 900);" class="w3-button w3-small w3-circle w3-blue w3-border w3-border-black" title="Invite This Interpreter"><i class="fa fa-envelope"></i></a>
                        <?php }
                        if ($action_confirm_temporary) { ?>
                          <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-yellow w3-border w3-border-blue" title="Confirm This Account First" onClick="popupwindow('confirm_record.php?id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'Confirm interpreter account', 520,350);"><i class="fa fa-check-circle"></i></a>
                        <?php }
                        if ($action_reject) { ?>
                          <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-red w3-border w3-border-black" title="Reject This Account" data-id="<?= $row['id'] ?>" data-name="<?= ucwords($row['name']) ?>" data-email="<?= $row['email'] ?>" data-reject-reason="<?= $row['reject_reason'] ?>" onClick="reject_interpreter(this)"><i class="fa fa-remove"></i></a>
                        <?php }
                      }
                      if ($action_view_references) {
                        if ($acttObj->read_specific("count(*) as counter", "int_references", "int_id=" . $row['id'])['counter'] > 0) { ?>
                          <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-green w3-border w3-border-blue" title="View references records" onClick="view_reference(<?php echo $row['id']; ?>);"><i class="fa fa-search"></i></a>
                        <?php }
                      }
                      if ($action_edit_profile) { ?>
                        <a title="Edit" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" onClick="popupwindow('interp_reg_edit.php?edit_id=<?php echo $row['id']; ?>', 'Edit interpreter record', 1100, 910);"><i class="fa fa-pencil"></i></a>
                        <?php }
                      if ($action_interp_id_card) { ?>
                        <a title="Interpreter Card" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" onClick="popupwindow('../print_interp_card.php?id=<?php echo $row['id']; ?>&print_from_list=1', 'Interpreter id Card', 1100, 910);"><i class="fa fa-id-card"></i></a>
                        <?php }
                      if ($row['is_temp'] == 0) {
                        if ($action_update_availability) { ?>
                          <a title="Week Schedule" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" onClick="MM_openBrWindow('interp_reg_schedul.php?edit_id=<?php echo $row['id']; ?>&name=<?php echo $row['name']; ?>&table=<?php echo $table; ?>','Attendance schedule interpreter','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><i class="fa fa-briefcase"></i></a>
                        <?php }
                      }
                      if ($action_update_rates) { ?>
                        <a title="Manage <?= $row['name'] ?> interpreting rates" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-green w3-border w3-border-blue" onClick="popupwindow('interpreter_self_rates.php?interpreter_id=<?php echo $row['id']; ?>&name=<?= $row['name'] ?>', '<?= $row['name'] ?> interpreting rates', 1100, 1000);"><i class="fa fa-list"></i></a>
                      <?php }
                      if ($action_delete) { ?>
                        <a title="Trash Record" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" onclick="popupwindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','Delete interpreter record', 500,350);"><i class="fa fa-trash"></i></a>
                        <?php }
                      if ($row['is_temp'] == 0) {
                        if ($action_view_paid_salaries) { ?>
                          <a title="Paid Salaries Record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" href="reg_interp_salary_list.php?interp=<?php echo $row['id']; ?>"><i class="fa fa-money"></i></a>
                        <?php }
                      }
                      if ($action_edited_history) { ?>
                        <a class="w3-button w3-small w3-circle w3-yellow w3-border w3-border-red" data-record-id="<?= $row['id'] ?>" onclick="view_log_changes(this)" href="javascript:void(0)" title="View Log Edited History"><i class="fa fa-list text-danger"></i></a>
                      <?php }
                      if ($action_change_password) { ?>
                        <a href="javascript:void(0)" title="Change Interpreter's Password" class="w3-button w3-small w3-circle w3-white w3-border w3-border-green" onClick="popupwindow('change_pswrd_interp.php?ref_frn_key=<?php echo $row['id']; ?>&name=<?php echo $row['name']; ?>','Update password interpreter',900,800)"><i class="fa fa-lock"></i></a>
                      <?php }
                      if ($action_add_rating) { ?>
                        <a title="Assessment" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-yellow" onClick="MM_openBrWindow('interp_assessment.php?edit_id=<?php echo $row['id']; ?>&code_qs=<?php echo $row['code']; ?>&name=<?php echo $row['name']; ?>','Interpreter assessment','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><i class="fa fa-star"></i></a>
                      <?php }
                      if ($action_language_assessment) { ?>
                        <a title="Language Assessment" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" onClick="MM_openBrWindow('lang_update.php?interpreter_id=<?php echo $row['id']; ?>&name=<?php echo $row['name']; ?>','Update languages interpreter','scrollbars=yes,resizable=yes,width=950,height=800,left=450,top=50')"><i class="fa fa-language"></i></a>
                      <?php }
                      if ($action_blacklist) { ?>
                        <a title="Blacklist" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-red w3-border w3-border-black" onclick="popupwindow('interp_reg_blacklist.php?edit_id=<?php echo $row['id']; ?>&code_qs=<?php echo $row['code']; ?>&name=<?php echo $row['name']; ?>','Blacklist interpreter',820,550)""><i class=" fa fa-ban"></i></a>
                    <?php }
                    if ($action_approve_documents) { ?>
                        <a title="Document Approval" href="javascript:void(0)" 
                            class="w3-button w3-small w3-circle w3-purple w3-border w3-border-black" 
                            onclick="popupwindow('interp_doc_approve.php?edit_id=<?php echo $row['id']; ?>',
                            'Document Approval',1300,700)">
                               <img src="checklist.png" width="20">
                          </a>
                    <?php }
                    } ?>
                  </td>
                </tr>
              <?php
              } ?>
            </tbody>
          </table>
          <div><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
        </div>
  </section>

  <!-- Modal to display record -->
  <div class="modal modal-info fade col-md-8 col-md-offset-2" data-toggle="modal" data-target=".bs-example-modal-lg" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div class="modal-dialog" role="document" style="width:auto;">
      <div class="modal-content">
        <div class="modal-header bg-default bg-light-ltr">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Record Details</h4>
        </div>
        <div class="modal-body" id="view_modal_data" style="overflow-x:auto;">
        </div>
        
        <div class="modal-footer bg-default">
          <button type="button" class="btn  btn-primary pull-right" data-dismiss="modal"> Close</button>
        </div>
      </div>
    </div>
  </div>
  <!--End of modal-->
  <!--Reject an interpreter account Modal-->
  <div class="modal" id="reject_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="process/reg_interp_list.php" method="post">
          <input type="hidden" name="interpreter_id" id="interpreter_id" required>
          <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
          <div class="modal-header alert-danger">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h4 class="modal-title"><b>Reject an Interpreter Account</b></h4>
          </div>
          <div class="modal-body reject_modal_attach">
            <div class="row">
              <div class="form-group col-md-6">
                <label for="interpreter_name">Interpreter Name</label>
                <input class="form-control" name="interpreter_name" id="interpreter_name" readonly>
              </div>
              <div class="form-group col-md-6">
                <label for="interpreter_email">Interpreter Email</label>
                <input class="form-control" name="interpreter_email" id="interpreter_email" readonly>
              </div>
              <div class="form-group col-md-12">
                <label for="deleted_reason">Write Reason of Rejection</label>
                <textarea rows="5" maxlength="250" placeholder="Write rejection reason ..." class="form-control" name="reject_reason" id="reject_reason" required></textarea>
              </div>
              <div class="form-group col-md-6">
                <label class="btn btn-default btn-sm" for="notify_interpreter"><input type="checkbox" value="1" name="notify_interpreter" id="notify_interpreter"> Notify Interpreter on email</label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            <button onclick='return confirm("Are you sure to REJECT this interpreter account?")' type="submit" name="btn_reject_interpreter" class="btn btn-primary">Reject Account</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!--Ajax processing modal-->
  <div class="modal" id="process_modal" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">×</button>
        </div>
        <div class="modal-body process_modal_attach">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <script>

  $(document).on('click', '.edit-ref-btn', function () {
    const refId = $(this).data('id');
    const row = $('#edit_row_' + refId);
    const btn = $(this);
    const icon = btn.find('i');

    // Hide all others and reset their buttons
    $('.edit-form-row').not(row).hide();
    $('.edit-ref-btn').not(btn).html('<i class="fa fa-edit"></i> Edit');

    // Toggle the target row
    row.toggle();

    // Update button text and icon
    if (row.is(':visible')) {
      btn.removeClass('btn-warning').addClass('btn-danger');
        btn.html('<i class="fa fa-times"></i> Close');
        row.find('.edit-name').val(btn.data('name'));
        row.find('.edit-relation').val(btn.data('relation'));
        row.find('.edit-company').val(btn.data('company'));
        row.find('.edit-email').val(btn.data('email'));
        row.find('.edit-phone').val(btn.data('phone'));
    } else {
        btn.removeClass('btn-danger').addClass('btn-warning');
        btn.html('<i class="fa fa-edit"></i> Edit');
    }
});



  $('#cancel_edit_ref').click(function () {
      $('#edit_ref_form_container').hide();
      $('#edit_ref_form')[0].reset();
  });

$(document).on('submit', '.edit-ref-form', function (e) {
    e.preventDefault();
    const form = $(this);
    const refId = form.find('input[name="edit_ref_id"]').val();

    $.ajax({
        url: 'ajax_add_interp_data.php',
        method: 'POST',
        data: form.serialize() + '&update_ref_id=1',
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
              alert('Reference updated successfully!');

              const name = form.find('input[name="name"]').val();
              const relation = form.find('input[name="relation"]').val();
              const company = form.find('input[name="company"]').val();
              const email = form.find('input[name="email"]').val();
              const phone = form.find('input[name="phone"]').val();

              // Update visible table row
              $('#ref_row_' + refId + ' .ref-name').text(name);
              $('#ref_row_' + refId + ' .ref-relation').text(relation);
              $('#ref_row_' + refId + ' .ref-company').text(company);
              $('#ref_row_' + refId + ' .ref-email').text(email);
              $('#ref_row_' + refId + ' .ref-phone').text(phone);

              // Update the button's data-* attributes so form fills correctly next time
              const editBtn = $('#ref_row_' + refId + ' .edit-ref-btn');
              editBtn.data('name', name);
              editBtn.data('relation', relation);
              editBtn.data('company', company);
              editBtn.data('email', email);
              editBtn.data('phone', phone);

              // Hide the edit form
              $('#edit_row_' + refId).hide();
          } else {
                alert('Update failed: ' + response.message);
            }
        },
        error: function (xhr) {
            alert('AJAX Error: ' + xhr.responseText);
        }
    });
});

</script>


  <script>
    $(document).ready(function() {
      $('#name').select2({
        width: 'resolve',
      });

      $('#email').select2({
        width: 'resolve',
      });

      $('#phoneNumber').select2({
        width: 'resolve',
      });
    });
    $('.tr_data').click(function(event) {
      $('.div_actions').css('display', 'none');
      $(this).next().css('display', 'block');
    });
    $(document).ready(function() {
      $('[data-toggle="popover"]').popover({
        html: true
      });
      $('[data-toggle="tooltip"]').tooltip();
    });

    function view_reference(id) {
      $.ajax({
        url: 'ajax_add_interp_data.php',
        method: 'post',
        data: {
          id: id,
          view_reference: '1'
        },
        success: function(data) {
          $('#view_modal_data').html(data);
          $('#view_modal').modal("show");
        },
        error: function(xhr) {
          alert("An error occured: " + xhr.status + " " + xhr.statusText);
        }
      });
    }

    function reject_interpreter(element) {
      $("#interpreter_id").val($(element).attr("data-id"));
      $("#interpreter_email").val($(element).attr("data-email"));
      $("#interpreter_name").val($(element).attr("data-name"));
      $("#reject_reason").val($(element).attr("data-reject-reason"));
      $('#reject_modal').modal('show');
    }

    function view_log_changes(element) {
      var table_name = "interpreter_reg";
      var record_id = $(element).attr("data-record-id");
      if (record_id && table_name) {
        $('.process_modal_attach').html("<center><i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i><br><h3>Loading ...<br><br>Please Wait !!!</h3></center>");
        $('#process_modal').modal('show');
        $('body').removeClass('modal-open');
        $.ajax({
          url: 'ajax_add_interp_data.php',
          method: 'post',
          dataType: 'json',
          data: {
            record_id: record_id,
            table_name: table_name,
            table_name_label: "Edit Interpreter Profile",
            record_label: "Interpreter Account",
            view_log_changes: 1
          },
          success: function(data) {
            if (data['status'] == 1) {
              $('.process_modal_attach').html(data['body']);
            } else {
              alert("Cannot load requested response. Please try again!");
            }
          },
          error: function(data) {
            alert("Error: Please select valid record for log details or refresh the page! Thank you");
          }
        });
      } else {
        alert("Error: Please select valid record for log details or refresh the page! Thank you");
      }
    }
  </script>
</body>

</html>