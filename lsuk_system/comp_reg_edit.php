<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'actions.php';
$allowed_type_idz = "59";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Edit Company Profile</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$table = 'comp_reg';
$edit_id = $_GET['edit_id'];
$row = $obj->read_specific("*", $table, "id=" . $edit_id);

if (isset($_POST['submit'])) {
  $name = trim($_POST['name']);
  $contactPerson = $_POST['contactPerson'];
  $payment_terms = $_POST['payment_terms'];
  $type_id = $_POST['type_id'];
  $bod = $_POST['bod'];
  $tpostCode = $_POST['tpostCode'];
  $tbuildingName = $_POST['tbuildingName'];
  $tline1 = $_POST['tline1'];
  $tline2 = $_POST['tline2'];
  $tstreetRoad = $_POST['tstreetRoad'];
  $tcn = $_POST['tcn'];
  $rff = $_POST['rff'];
  $note = $_POST['note'];
  $crn = $_POST['crn'];
  $vn = $_POST['vn'];
  $contactNo1 = $_POST['contactNo1'];
  $contactNo2 = $_POST['contactNo2'];
  $contactNo3 = $_POST['contactNo3'];
  $web = $_POST['web'];
  $credit_limit = $_POST['credit_limit'];
  $mileage = $_POST['mileage'];
  $travel_time = $_POST['travel_time'];
  $email = $_POST['email'];
  $country = $_POST['selected_country'];
  $city = $_POST['city'];
  $postCode = $_POST['postCode'];
  $buildingName = $_POST['buildingName'];
  $line1 = $_POST['line1'];
  $line2 = $_POST['line2'];
  $streetRoad = $_POST['streetRoad'];
  $invEmail = $_POST['invEmail'];
  $taupn = $_POST['taupn'];
  $tpitc = $_POST['tpitc'];
  $tcity = $_POST['tcity'];
  $admin_rate = $_POST['admin_rate'];
  $update_array = array(
    "name" => $name, "contactPerson" => $contactPerson, "bod" => $bod, "tpostCode" => $tpostCode, "tbuildingName" => $tbuildingName, "tline1" => $tline1, "tline2" => $tline2, "tstreetRoad" => $tstreetRoad, "tcn" => $tcn, "rff" => $rff, "note" => $note, "crn" => $crn, "vn" => $vn, "contactNo1" => $contactNo1, "contactNo2" => $contactNo2, "contactNo3" => $contactNo3, "web" => $web, "email" => $email, "country" => $country, "city" => $city,"payment_terms"=>$payment_terms,"credit_limit"=>$credit_limit,"mileage"=>$mileage,"travel_time"=>$travel_time, "postCode" => $postCode, "buildingName" => $buildingName, "line1" => $line1, "line2" => $line2, "streetRoad" => $streetRoad, "invEmail" => $invEmail, "taupn" => $taupn, "tpitc" => $tpitc, "tcity" => $tcity
   );

  if(isset($_POST['p_org'])){
    $p_org = $_POST['p_org'];
    $get_pr = '';
    if(!empty($p_org)){
      $get_pr = $obj->read_specific("subsidiaries.parent_comp","subsidiaries","subsidiaries.child_comp=$edit_id")['parent_comp'];
      if(!empty($get_pr)) {
          $obj->update('subsidiaries', array("parent_comp" => $p_org), "child_comp=" . $edit_id);
      } else {
          $obj->insert('subsidiaries', array("parent_comp" => $p_org, "child_comp" => $edit_id));
          $update_array['comp_nature'] = 3;
      }
    }else {
		$obj->delete('subsidiaries', "child_comp = '$edit_id'");
        $update_array['comp_nature'] = 4;
	}
  }
  
  if (isset($_POST['po_req'])) {
    if ($_POST['po_email']) {
      $po_email = $_POST['po_email'];
    } else {
      $po_email = $_POST['email'];
    }
    $update_array['po_req'] = 1;
    $update_array['po_email'] = $po_email;
  } else {
    $update_array['po_req'] = 0;
    $update_array['po_email'] = "";
    //Remove po reminders sent from list
    $f2f_ids = $obj->read_specific("GROUP_CONCAT(po_requested.id) as f2f_ids", "po_requested,interpreter,comp_reg", "interpreter.orgName=comp_reg.abrv AND po_requested.order_id=interpreter.id and po_requested.order_type='f2f' AND comp_reg.id=" . $edit_id)['f2f_ids'];
    $tp_ids = $obj->read_specific("GROUP_CONCAT(po_requested.id) as tp_ids", "po_requested,telephone,comp_reg", "telephone.orgName=comp_reg.abrv AND po_requested.order_id=telephone.id and po_requested.order_type='tp' AND comp_reg.id=" . $edit_id)['tp_ids'];
    $tr_ids = $obj->read_specific("GROUP_CONCAT(po_requested.id) as tr_ids", "po_requested,translation,comp_reg", "translation.orgName=comp_reg.abrv AND po_requested.order_id=translation.id and po_requested.order_type='tr' AND comp_reg.id=" . $edit_id)['tr_ids'];
    if ($f2f_ids) {
      $obj->delete("po_requested", "id IN ($f2f_ids)");
    }
    if ($tp_ids) {
      $obj->delete("po_requested", "id IN ($tp_ids)");
    }
    if ($tr_ids) {
      $obj->delete("po_requested", "id IN ($tr_ids)");
    }
  }

  $update_array['admin_ch'] = (isset($_POST['admin_ch']) ? 1 : 0);
  $update_array['admin_rate'] = (isset($_POST['admin_ch']) ? $admin_rate : 0);
  $update_array['tr_time'] = (isset($_POST['tr_time']) ? 1 : 0);
  $update_array['interp_time'] = (isset($_POST['interp_time']) ? 1 : 0);
  $update_array['wait_time'] = (isset($_POST['wait_time']) ? 1 : 0);
  $update_array['remote_unit_in_hours'] = (isset($_POST['remote_unit_in_hours']) ? 1 : 0);
  
  $update_array['edited_by'] = $_SESSION['UserName'];
  $update_array['edited_date'] = date("Y-m-d H:i:s");

  // Update the record
  $obj->update($table, $update_array, "id=" . $edit_id);
  // $obj->new_old_table('hist_' . $table, $table, $edit_id); // We are not using this now
  $obj->insert("daily_logs", array("action_id" => 11, "user_id" => $_SESSION['userId'], "details" => "Company ID: " . $edit_id));

  // Log changes start
  $index_mapping = array(
    'Name' => 'name', 'Contact.Person' => 'contactPerson', 'Bod' => 'bod', 'T.PostCode' => 'tpostCode', 'T.BuildingName' => 'tbuildingName', 'T.Line1' => 'tline1', 'T.Line2' => 'tline2',
    'T.StreetRoad' => 'tstreetRoad', 'Contact.Name' => 'tcn', 'Reg.Form.Filled' => 'rff', 'Notes' => 'note', 'Reg.No' => 'crn', 'VAT Number' => 'vn', 'ContactNo1' => 'contactNo1', 
    'ContactNo2' => 'contactNo2', 'ContactNo3' => 'contactNo3', 'Website' => 'web', 'Email' => 'email', 'Country' => 'country', 'City' => 'city', 'PostCode' => 'postCode', 'Building.Name' => 'buildingName', 
    'Line 1' => 'line1', 'Line 2' => 'line2', 'Street.Road' => 'streetRoad', 'Invoice.Email' => 'invEmail', 'Authorised.Person' => 'taupn', 'A.Person.Position' => 'tpitc', 'A.Person.City' => 'tcity', 
    'Company.Nature' => 'comp_nature', 'Purchase.Order?' => 'po_req', 'Purch.Order.Email' => 'po_email', 'Admin.Charge?' => 'admin_ch', 'Admin.Charge.Rate' => 'admin_rate', 'Travel.Time?' => 'tr_time',
    'Interpreting.Time?' => 'interp_time', 'Waiting.Time?' => 'wait_time'
  );
  
  $old_values = $new_values = array();
  $get_new_data = $obj->read_specific("*", "$table", "id=" . $edit_id);
  
  foreach ($index_mapping as $key => $value) {
    if (isset($get_new_data[$value])) {
      $old_values[$key] = $row[$value];
      $new_values[$key] = $get_new_data[$value];
    }
  }
  $obj->log_changes(json_encode($old_values), json_encode($new_values), $edit_id, $table, "update", $_SESSION['userId'], $_SESSION['UserName'], "edit_company_account");
  // Log changes end
}


	// For Duplication - Company
	if(isset($_POST['MM_validator']) && $_POST['MM_validator'] == 1){
		include "userhaspage.php";
		SysPermiss::UserHasPage(__FILE__);
		
		include '../source/setup_email.php';
		
		  $name = trim($_POST['name']);
		  $abrv = $_POST['abrv'];
		  $contactPerson = $_POST['contactPerson'];
		  $payment_terms = $_POST['payment_terms'];
		  $type_id = $_POST['type_id'];
		  $bod = $_POST['bod'];
		  $tpostCode = $_POST['tpostCode'];
		  $tbuildingName = $_POST['tbuildingName'];
		  $tline1 = $_POST['tline1'];
		  $tline2 = $_POST['tline2'];
		  $tstreetRoad = $_POST['tstreetRoad'];
		  $tcn = $_POST['tcn'];
		  $rff = $_POST['rff'];
		  $note = $_POST['note'];
		  $crn = $_POST['crn'];
		  $vn = $_POST['vn'];
		  $contactNo1 = $_POST['contactNo1'];
		  $contactNo2 = $_POST['contactNo2'];
		  $contactNo3 = $_POST['contactNo3'];
		  $web = $_POST['web'];
      $credit_limit = $_POST['credit_limit'];
      $mileage = $_POST['mileage'];
      $travel_time = $_POST['travel_time'];
		  $email = $_POST['email'];
		  $country = $_POST['selected_country'];
		  $city = $_POST['city'];
		  $postCode = $_POST['postCode'];
		  $buildingName = $_POST['buildingName'];
		  $line1 = $_POST['line1'];
		  $line2 = $_POST['line2'];
		  $streetRoad = $_POST['streetRoad'];
		  $invEmail = $_POST['invEmail'];
		  $taupn = $_POST['taupn'];
		  $tpitc = $_POST['tpitc'];
		  $tcity = $_POST['tcity'];
		  $admin_rate = $_POST['admin_rate'];
		  
		  $update_array = array(
			"name" => $name, "abrv" => $abrv, "contactPerson" => $contactPerson, "bod" => $bod, "tpostCode" => $tpostCode, "tbuildingName" => $tbuildingName, 
			"tline1" => $tline1, "tline2" => $tline2, "tstreetRoad" => $tstreetRoad, "tcn" => $tcn, "rff" => $rff, "note" => $note, "crn" => $crn, 
			"vn" => $vn, "contactNo1" => $contactNo1, "contactNo2" => $contactNo2, "contactNo3" => $contactNo3, "web" => $web, "email" => $email, 
			"country" => $country, "city" => $city,"payment_terms"=>$payment_terms,"credit_limit"=>$credit_limit,"mileage"=>$mileage,"travel_time"=>$travel_time, "postCode" => $postCode, "buildingName" => $buildingName, "line1" => $line1, "line2" => $line2, "streetRoad" => $streetRoad, "invEmail" => $invEmail, "taupn" => $taupn, "tpitc" => $tpitc, "tcity" => $tcity
		  );

			if (isset($_POST['po_req'])) {
				if ($_POST['po_email']) {
					$po_email = $_POST['po_email'];
				} else {
					$po_email = $_POST['email'];
				}
				$update_array['po_req'] = 1;
				$update_array['po_email'] = $po_email;
			  } else {
				$update_array['po_req'] = 0;
				$update_array['po_email'] = "";
			}

			$update_array['admin_ch'] = (isset($_POST['admin_ch']) ? 1 : 0);
			$update_array['admin_rate'] = (isset($_POST['admin_ch']) ? $admin_rate : 0);
			$update_array['tr_time'] = (isset($_POST['tr_time']) ? 1 : 0);
			$update_array['interp_time'] = (isset($_POST['interp_time']) ? 1 : 0);
			$update_array['wait_time'] = (isset($_POST['wait_time']) ? 1 : 0);
      $update_array['remote_unit_in_hours'] = (isset($_POST['remote_unit_in_hours']) ? 1 : 0);
		  
			$update_array['edited_by'] = $_SESSION['UserName'];
			$update_array['edited_date'] = date("Y-m-d H:i:s");
			$update_array['dated'] = date("Y-m-d H:i:s");
			
			if ($_SESSION['Temp'] == 1) {
				$update_array['is_temp'] = 1;
			}
			
			$update_array['sbmtd_by'] = ucwords($_SESSION['UserName']);
			  
			$edit_id = $obj->insert($table, $update_array, true);
			
			if($edit_id){
				if(isset($_POST['p_org']) && !empty($_POST['p_org'])){
					$obj->insert('subsidiaries', array("parent_comp" => $_POST['p_org'], "child_comp" => $edit_id, "dated" => date('Y-m-d')));
					$obj->editFun($table, $edit_id, 'comp_nature', 3);  
				}
			}
					
		  echo "<script>alert('New company registered successfully.');</script>";
		  
		  $obj->insert("daily_logs", array("action_id" => 9, "user_id" => $_SESSION['userId'], "details" => "Company ID: " . $edit_id));
		  
		  $comp_id = $edit_id;
		  
		  $table = "company_login";
			
			$s = '!@#$%^&*()-_=+'; $u = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$p = substr(md5(time()), 0, 5) . $s[rand(0, strlen($s) - 1)] . $s[rand(0, strlen($s) - 1)] . $u[rand(0, 25)];
			$g_password = str_shuffle($p);
			
			$company_logins = array(
				'company_id' => $comp_id,
				'orgName' => $_POST['abrv'],
				'email' => $_POST['email'],
				'paswrd' => $g_password,
				'prvlg' => 0,
				'dated' => date('Y-m-d'),
			);
			
			$result = $obj->insert($table, $company_logins, false);
			
		  $subject = "Welcome to LSUK - Account Details";
		  $message = "<p>Hi</p><p>Please use the Below credentials to login into your LSUK portal:</p><p>Email: ".$_POST['email']."<br />Password: ".$g_password." </p><p>Best Regards,<br /><br /></p><p><strong>LSUK Limited</strong></p><p>Landline: 01173290610<br />Mobile: 07915177068<br />Office Address: Suite 3 Davis House<br />Lodge Causeway Trading estate<br />Lodge Causeway - Fishponds<br />Bristol BS16 3JB<br />Opening Hours: Monday - Friday 09AM to 5PM</p>";
		  
		  	try{
				$mail->SMTPDebug = 0;
				$mail->isSMTP();
				$mail->Host = setupEmail::EMAIL_HOST;
				$mail->SMTPAuth   = true;
				$mail->Username   = setupEmail::INFO_EMAIL;
				$mail->Password   = setupEmail::INFO_PASSWORD;
				$mail->SMTPSecure = setupEmail::SECURE_TYPE;
				$mail->Port       = setupEmail::SENDING_PORT;
				$mail->setFrom(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
				$mail->addAddress($_POST['email']); 
				//$mail->addAddress("developer866@gmail.com"); 
				$mail->addReplyTo(setupEmail::INFO_EMAIL, setupEmail::FROM_NAME);
				$mail->isHTML(true);
				$mail->Subject = $subject;
				$mail->Body    = $message;
				$mail->send();
				$mail->ClearAllRecipients();
			} catch (Exception $e) { 
		?>
			<script>
				alert("Message could not be sent! Mailer library error."
				<?php echo $e->getMessage() ?>);
			</script>
		<?php } 
		
			echo "<script>alert('Credentials Created');
				if (window.opener) {
				  window.opener.location.reload();
				}
				window.close();
			</script>";
		
		} // end if Duplication submit	?>
	
	
<?php 	
//get data from database
$parent_comp='';
$get_parent = $obj->read_specific("parent_comp", "subsidiaries", "child_comp=" . $edit_id);
$parent_comp = $get_parent['parent_comp'];

$row = $obj->read_specific("*", $table, "id=" . $edit_id);
$rowID = $row['id'];
$name = $row['name'];
$contactPerson = $row['contactPerson'];
$abrv = $row['abrv'];
$contactNo1 = $row['contactNo1'];
$contactNo2 = $row['contactNo2'];
$contactNo3 = $row['contactNo3'];
$buildingName = $row['buildingName'];
$line1 = $row['line1'];
$streetRoad = $row['streetRoad'];
$email = $row['email'];
$city = $row['city'];
$country = $row['country'];
$compType = $row['compType'];
$type_id = $row['type_id'];
$postCode = $row['postCode'];
$note = $row['note'];
$line2 = $row['line2'];
$invEmail = $row['invEmail'];
$invAddrs = $row['invAddrs'];
$bod = $row['bod'];
$crn = $row['crn'];
$vn = $row['vn'];
$web = $row['web'];
$credit_limit = $row['credit_limit'];
$mileage = $row['mileage'];
$travel_time = $row['travel_time'];
$aupn = $row['aupn'];
$pitc = $row['pitc'];
$taupn = $row['taupn'];
$tpitc = $row['tpitc'];
$tbuildingName = $row['tbuildingName'];
$tline1 = $row['tline1'];
$tline2 = $row['tline2'];
$tstreetRoad = $row['tstreetRoad'];
$tcity = $row['tcity'];
$tcn = $row['tcn'];
$tpostCode = $row['tpostCode'];
$rff = $row['rff'];
$payment_terms = $row['payment_terms'];
$po_req = $row['po_req'];
$wait_time = $row['wait_time'];
$remote_unit_in_hours = $row['remote_unit_in_hours'];
$admin_ch = $row['admin_ch'];
$admin_rate = $row['admin_rate'];
$interp_time = $row['interp_time'];
$tr_time = $row['tr_time'];
$po_email = $row['po_email'];

	

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Edit Company Record</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
  <style>
    label {
      font-weight: 500;
    }

    .multiselect {
      min-width: 230px;
    }

    .multiselect-container {
      max-height: 400px;
      overflow-y: auto;
      max-width: 380px;
    }

    .multiselect-native-select {
      display: block;
    }

    .multiselect-container li.active label.radio,
    .multiselect-container li.active label.checkbox {
      color: white;
    }
  </style>
  
  
  <?php include 'ajax_uniq_fun.php'; ?>
</head>

<body>
  <div class="container-fluid">
	
	<?php if(!isset($_GET['action'])){ // Not for creating duplicat --- for updation only ?>
    
	<form action="" method="post" class="register" enctype="multipart/form-data">
      <div style="background: #8c8c86;padding: 6px;position: fixed;z-index: 999999999999;width: 100%;color: white;">
        <b>
          <h4 style="display: inline-block;">Company Registration <span class="hidden-xs">Details</span></h4>
        </b>
        <button class="btn btn-info pull-right" style="border-color: #000000;color: black;text-transform: uppercase;margin: 7px 21px;font-size: 16px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="submit" name="submit">UPDATE COMPANY &raquo;</button>
      </div><br><br><br><br>
      <div class="form-group col-md-3 col-sm-6">
        <label>Company Name *</label>
        <input placeholder="Company Name *" name="name" class="form-control" type="text" required='' id="name" onBlur="uniqueFun(this.value,'comp_reg','name',$(this).attr('id') );" value="<?php echo $name; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
          <?php
          $result_opt = $obj->read_all("DISTINCT id,name,abrv", "comp_reg", "comp_nature IN (1,4)");
          // $sql_opt = "SELECT DISTINCT id,name,abrv from comp_reg WHERE comp_nature=1 ORDER BY name ASC"; ?>
          <label>Parent/Head Units (if any)</label>
          <select id="p_org" name="p_org" class="form-control searchable">
              <?php 
              // $result_opt = mysqli_query($con, $sql_opt);
              $options = "";
              while ($row_opt = mysqli_fetch_assoc($result_opt)) {
                  $comp_id = $row_opt["id"];
                  $code = $row_opt["abrv"];
                  $name_opt = $row_opt["name"];
                  $options .= "<OPTION value='$comp_id' " . ($comp_id == $parent_comp ? 'selected' : '') . ">" . $name_opt . ' (' . $code . ')';
              }
              ?>
              <option value="">Select Parent/Head Units (if any)</option>
              <?php echo $options; ?>
              </option>
          </select>
        </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Abrivaiton of Company * </label>
        <input placeholder="Abrivaiton of Company  *" name="abrv" class="form-control valid" type="text" id="abrv" readonly value="<?php echo $abrv; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Contact Person *</label>
        <input placeholder="Contact Person *" name="contactPerson" class="form-control valid" type="text" id="contactPerson" required='' value="<?php echo $contactPerson; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Payment Terms </label>
        <select name="payment_terms" id="payment_terms" class="form-control" required>
          <option selected value="<?php echo $payment_terms; ?>"><?php echo $payment_terms == 0 ? 'Pay Now' : $payment_terms . ' Days'; ?></option>
          <option value="">Select Payment Terms</option>
          <option value="0">Pay Now</option>
          <option value="7">7 Days</option>
          <option value="14">14 Days</option>
          <option value="21">21 Days</option>
          <option value="28">28 Days</option>
          <option value="30">30 Days</option>
          <option value="35">35 Days</option>
          <option value="42">42 Days</option>
          <option value="49">49 Days</option>
          <option value="56">56 Days</option>
          <option value="63">63 Days</option>
          <option value="63">63 Days</option>
          <option value="70">70 Days</option>
          <option value="77">77 Days</option>
          <option value="84">84 Days</option>
          <option value="91">91 Days</option>
          <option value="98">98 Days</option>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Branch or Department</label>
        <input placeholder="Branch or Department" name="bod" type="text" id="bod" class="form-control" value="<?php echo $bod; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Company Type</label>
        <select name="type_id" id="type_id" class="form-control" required>
          <?php
          $get_types = $obj->read_all("comp_type.id,comp_type.title,company_types.title as group_name", "comp_type,company_types", "comp_type.company_type_id=company_types.id ORDER BY comp_type.title ASC");?>
          <option value="" disabled>Select Company Type</option>
          <?php while ($row_opt = $get_types->fetch_assoc()) {
            $selected_type = $row['type_id'] == $row_opt['id'] ? 'selected' : '';
            echo "<option value='" . $row_opt['id'] . "' ".$selected_type.">" . $row_opt['title'] . " (" . $row_opt['group_name'] . ")</option>";
          }
          ?>
        </select>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Company Registration Number </label>
        <input name="crn" type="text" id="crn" class="form-control" placeholder="Company Registration Number" value="<?php echo $crn; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>VAT Number *</label>
        <input name="vn" type="text" id="vn" class="form-control" placeholder="VAT Number *" value="<?php echo $vn; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Contact No 1 * </label>
        <input name="contactNo1" type="text" id="contactNo1" required='' class="form-control" placeholder="Contact No 1 *" value="<?php echo $contactNo1; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Contact No 2 </label>
        <input placeholder="Contact No 2" name="contactNo2" type="text" id="contactNo2" class="form-control" value="<?php echo $contactNo2; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Company Fax No # </label>
        <input placeholder="Company Fax No" name="contactNo3" type="text" id="contactNo3" class="form-control" value="<?php echo $contactNo3; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Company Website</label>
        <input name="web" type="text" id="web" class="form-control" placeholder="Company Website" value="<?php echo $web; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Email Address * </label>
        <input name="email" type="text" id="email" class="form-control" required='' placeholder="Email Address *" value="<?php echo $email; ?>" />
      </div>
      <?php 
        if(in_array($row['comp_nature'],[1,4])){
      ?>
      <div class="form-group col-md-3 col-sm-6">
      <label>Credit Limit * </label>
        <input name="credit_limit" type="number" id="credit_limit" class="form-control" required='' placeholder="Credit Limit in £" value="<?php echo $credit_limit; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
      <label>Mileage included in Hourly Cost</label>
        <input name="mileage" type="text" id="mileage" class="form-control" required='' placeholder="Enter Mileage for Hourly Cost Package" value="<?php echo $mileage; ?>" />
      </div>
      
      <div class="form-group col-md-3 col-sm-6">
      <label>Travel Time included in Hourly Cost</label>
        <input name="travel_time" type="text" id="travel_time" class="form-control" required='' placeholder="Enter Travel Time for Hourly Cost Package" value="<?php echo $travel_time; ?>" />
      </div>
      <?php
        }
      ?>
      <div class="bg-info col-xs-12 form-group">
        <h4>Invoicing Address (Team or Unit Address)</h4>
      </div>
      <div class="form-group col-md-3">
        <label>Select a country *</label><br>
        <?php
        $country_array = array(
          "Afghanistan" => "Afghanistan (افغانستان)", "Aland Islands" => "Aland Islands (Åland)", "Albania" => "Albania (Shqipëria)", "Algeria" => "Algeria (الجزائر)", "American Samoa" => "American Samoa (American Samoa)", "Andorra" => "Andorra (Andorra)", "Angola" => "Angola (Angola)", "Anguilla" => "Anguilla (Anguilla)", "Antarctica" => "Antarctica (Antarctica)", "Antigua And Barbuda" => "Antigua And Barbuda (Antigua and Barbuda)", "Argentina" => "Argentina (Argentina)", "Armenia" => "Armenia (Հայաստան)", "Aruba" => "Aruba (Aruba)", "Australia" => "Australia (Australia)", "Austria" => "Austria (Österreich)", "Azerbaijan" => "Azerbaijan (Azərbaycan)", "Bahamas The" => "Bahamas The (Bahamas)", "Bahrain" => "Bahrain (‏البحرين)", "Bangladesh" => "Bangladesh (Bangladesh)", "Barbados" => "Barbados (Barbados)", "Belarus" => "Belarus (Белару́сь)", "Belgium" => "Belgium (België)", "Belize" => "Belize (Belize)", "Benin" => "Benin (Bénin)", "Bermuda" => "Bermuda (Bermuda)", "Bhutan" => "Bhutan (ʼbrug-yul)", "Bolivia" => "Bolivia (Bolivia)", "Bonaire, Sint Eustatius and Saba" => "Bonaire, Sint Eustatius and Saba (Caribisch Nederland)", "Bosnia and Herzegovina" => "Bosnia and Herzegovina (Bosna i Hercegovina)", "Botswana" => "Botswana (Botswana)", "Bouvet Island" => "Bouvet Island (Bouvetøya)", "Brazil" => "Brazil (Brasil)", "British Indian Ocean Territory" => "British Indian Ocean Territory (British Indian Ocean Territory)", "Brunei" => "Brunei (Negara Brunei Darussalam)", "Bulgaria" => "Bulgaria (България)", "Burkina Faso" => "Burkina Faso (Burkina Faso)", "Burundi" => "Burundi (Burundi)", "Cambodia" => "Cambodia (Kâmpŭchéa)", "Cameroon" => "Cameroon (Cameroon)", "Canada" => "Canada (Canada)", "Cape Verde" => "Cape Verde (Cabo Verde)", "Cayman Islands" => "Cayman Islands (Cayman Islands)", "Central African Republic" => "Central African Republic (Ködörösêse tî Bêafrîka)", "Chad" => "Chad (Tchad)", "Chile" => "Chile (Chile)", "China" => "China (中国)", "Christmas Island" => "Christmas Island (Christmas Island)", "Cocos (Keeling) Islands" => "Cocos (Keeling) Islands (Cocos (Keeling) Islands)", "Colombia" => "Colombia (Colombia)", "Comoros" => "Comoros (Komori)", "Congo" => "Congo (République du Congo)", "Congo The Democratic Republic Of The" => "Congo The Democratic Republic Of The (République démocratique du Congo)", "Cook Islands" => "Cook Islands (Cook Islands)", "Costa Rica" => "Costa Rica (Costa Rica)", "Cote D'Ivoire (Ivory Coast)" => "Cote D'Ivoire (Ivory Coast) ()", "Croatia (Hrvatska)" => "Croatia (Hrvatska) (Hrvatska)", "Cuba" => "Cuba (Cuba)", "Curaçao" => "Curaçao (Curaçao)", "Cyprus" => "Cyprus (Κύπρος)", "Czech Republic" => "Czech Republic (Česká republika)", "Denmark" => "Denmark (Danmark)", "Djibouti" => "Djibouti (Djibouti)", "Dominica" => "Dominica (Dominica)", "Dominican Republic" => "Dominican Republic (República Dominicana)", "East Timor" => "East Timor (Timor-Leste)", "Ecuador" => "Ecuador (Ecuador)", "Egypt" => "Egypt (مصر‎)", "El Salvador" => "El Salvador (El Salvador)", "Equatorial Guinea" => "Equatorial Guinea (Guinea Ecuatorial)", "Eritrea" => "Eritrea (ኤርትራ)", "Estonia" => "Estonia (Eesti)", "Ethiopia" => "Ethiopia (ኢትዮጵያ)", "Falkland Islands" => "Falkland Islands (Falkland Islands)", "Faroe Islands" => "Faroe Islands (Føroyar)", "Fiji Islands" => "Fiji Islands (Fiji)", "Finland" => "Finland (Suomi)", "France" => "France (France)", "French Guiana" => "French Guiana (Guyane française)", "French Polynesia" => "French Polynesia (Polynésie française)", "French Southern Territories" => "French Southern Territories (Territoire des Terres australes et antarctiques fr)", "Gabon" => "Gabon (Gabon)", "Gambia The" => "Gambia The (Gambia)", "Georgia" => "Georgia (საქართველო)", "Germany" => "Germany (Deutschland)", "Ghana" => "Ghana (Ghana)", "Gibraltar" => "Gibraltar (Gibraltar)", "Greece" => "Greece (Ελλάδα)", "Greenland" => "Greenland (Kalaallit Nunaat)", "Grenada" => "Grenada (Grenada)", "Guadeloupe" => "Guadeloupe (Guadeloupe)", "Guam" => "Guam (Guam)", "Guatemala" => "Guatemala (Guatemala)", "Guernsey and Alderney" => "Guernsey and Alderney (Guernsey)", "Guinea" => "Guinea (Guinée)", "Guinea-Bissau" => "Guinea-Bissau (Guiné-Bissau)", "Guyana" => "Guyana (Guyana)", "Haiti" => "Haiti (Haïti)", "Heard Island and McDonald Islands" => "Heard Island and McDonald Islands (Heard Island and McDonald Islands)", "Honduras" => "Honduras (Honduras)", "Hong Kong S.A.R." => "Hong Kong S.A.R. (香港)", "Hungary" => "Hungary (Magyarország)", "Iceland" => "Iceland (Ísland)", "India" => "India (भारत)", "Indonesia" => "Indonesia (Indonesia)", "Iran" => "Iran (ایران)", "Iraq" => "Iraq (العراق)", "Ireland" => "Ireland (Éire)", "Israel" => "Israel (יִשְׂרָאֵל)", "Italy" => "Italy (Italia)", "Jamaica" => "Jamaica (Jamaica)", "Japan" => "Japan (日本)", "Jersey" => "Jersey (Jersey)", "Jordan" => "Jordan (الأردن)", "Kazakhstan" => "Kazakhstan (Қазақстан)", "Kenya" => "Kenya (Kenya)", "Kiribati" => "Kiribati (Kiribati)", "Korea North" => "Korea North (북한)", "Korea South" => "Korea South (대한민국)", "Kosovo" => "Kosovo (Republika e Kosovës)", "Kuwait" => "Kuwait (الكويت)", "Kyrgyzstan" => "Kyrgyzstan (Кыргызстан)", "Laos" => "Laos (ສປປລາວ)", "Latvia" => "Latvia (Latvija)", "Lebanon" => "Lebanon (لبنان)", "Lesotho" => "Lesotho (Lesotho)", "Liberia" => "Liberia (Liberia)", "Libya" => "Libya (‏ليبيا)", "Liechtenstein" => "Liechtenstein (Liechtenstein)", "Lithuania" => "Lithuania (Lietuva)", "Luxembourg" => "Luxembourg (Luxembourg)", "Macau S.A.R." => "Macau S.A.R. (澳門)", "Macedonia" => "Macedonia (Северна Македонија)", "Madagascar" => "Madagascar (Madagasikara)", "Malawi" => "Malawi (Malawi)", "Malaysia" => "Malaysia (Malaysia)", "Maldives" => "Maldives (Maldives)", "Mali" => "Mali (Mali)", "Malta" => "Malta (Malta)", "Man (Isle of)" => "Man (Isle of) (Isle of Man)", "Marshall Islands" => "Marshall Islands (M̧ajeļ)", "Martinique" => "Martinique (Martinique)", "Mauritania" => "Mauritania (موريتانيا)", "Mauritius" => "Mauritius (Maurice)", "Mayotte" => "Mayotte (Mayotte)", "Mexico" => "Mexico (México)", "Micronesia" => "Micronesia (Micronesia)", "Moldova" => "Moldova (Moldova)", "Monaco" => "Monaco (Monaco)", "Mongolia" => "Mongolia (Монгол улс)", "Montenegro" => "Montenegro (Црна Гора)", "Montserrat" => "Montserrat (Montserrat)", "Morocco" => "Morocco (المغرب)", "Mozambique" => "Mozambique (Moçambique)", "Myanmar" => "Myanmar (မြန်မာ)", "Namibia" => "Namibia (Namibia)", "Nauru" => "Nauru (Nauru)", "Nepal" => "Nepal (नपल)", "Netherlands The" => "Netherlands The (Nederland)", "New Caledonia" => "New Caledonia (Nouvelle-Calédonie)", "New Zealand" => "New Zealand (New Zealand)", "Nicaragua" => "Nicaragua (Nicaragua)", "Niger" => "Niger (Niger)", "Nigeria" => "Nigeria (Nigeria)", "Niue" => "Niue (Niuē)", "Norfolk Island" => "Norfolk Island (Norfolk Island)", "Northern Mariana Islands" => "Northern Mariana Islands (Northern Mariana Islands)", "Norway" => "Norway (Norge)", "Oman" => "Oman (عمان)", "Pakistan" => "Pakistan (پاکستان)", "Palau" => "Palau (Palau)", "Palestinian Territory Occupied" => "Palestinian Territory Occupied (فلسطين)", "Panama" => "Panama (Panamá)", "Papua new Guinea" => "Papua new Guinea (Papua Niugini)", "Paraguay" => "Paraguay (Paraguay)", "Peru" => "Peru (Perú)", "Philippines" => "Philippines (Pilipinas)", "Pitcairn Island" => "Pitcairn Island (Pitcairn Islands)", "Poland" => "Poland (Polska)", "Portugal" => "Portugal (Portugal)", "Puerto Rico" => "Puerto Rico (Puerto Rico)", "Qatar" => "Qatar (قطر)", "Reunion" => "Reunion (La Réunion)", "Romania" => "Romania (România)", "Russia" => "Russia (Россия)", "Rwanda" => "Rwanda (Rwanda)", "Saint Helena" => "Saint Helena (Saint Helena)", "Saint Kitts And Nevis" => "Saint Kitts And Nevis (Saint Kitts and Nevis)", "Saint Lucia" => "Saint Lucia (Saint Lucia)", "Saint Pierre and Miquelon" => "Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "Saint Vincent And The Grenadines" => "Saint Vincent And The Grenadines (Saint Vincent and the Grenadines)", "Saint-Barthelemy" => "Saint-Barthelemy (Saint-Barthélemy)", "Saint-Martin (French part)" => "Saint-Martin (French part) (Saint-Martin)", "Samoa" => "Samoa (Samoa)", "San Marino" => "San Marino (San Marino)", "Sao Tome and Principe" => "Sao Tome and Principe (São Tomé e Príncipe)", "Saudi Arabia" => "Saudi Arabia (العربية السعودية)", "Senegal" => "Senegal (Sénégal)", "Serbia" => "Serbia (Србија)", "Seychelles" => "Seychelles (Seychelles)", "Sierra Leone" => "Sierra Leone (Sierra Leone)", "Singapore" => "Singapore (Singapore)", "Sint Maarten (Dutch part)" => "Sint Maarten (Dutch part) (Sint Maarten)", "Slovakia" => "Slovakia (Slovensko)", "Slovenia" => "Slovenia (Slovenija)", "Solomon Islands" => "Solomon Islands (Solomon Islands)", "Somalia" => "Somalia (Soomaaliya)", "South Africa" => "South Africa (South Africa)", "South Georgia" => "South Georgia (South Georgia)", "South Sudan" => "South Sudan (South Sudan)", "Spain" => "Spain (España)", "Sri Lanka" => "Sri Lanka (śrī laṃkāva)", "Sudan" => "Sudan (السودان)", "Suriname" => "Suriname (Suriname)", "Svalbard And Jan Mayen Islands" => "Svalbard And Jan Mayen Islands (Svalbard og Jan Mayen)", "Swaziland" => "Swaziland (Swaziland)", "Sweden" => "Sweden (Sverige)", "Switzerland" => "Switzerland (Schweiz)", "Syria" => "Syria (سوريا)", "Taiwan" => "Taiwan (臺灣)", "Tajikistan" => "Tajikistan (Тоҷикистон)", "Tanzania" => "Tanzania (Tanzania)", "Thailand" => "Thailand (ประเทศไทย)", "Togo" => "Togo (Togo)", "Tokelau" => "Tokelau (Tokelau)", "Tonga" => "Tonga (Tonga)", "Trinidad And Tobago" => "Trinidad And Tobago (Trinidad and Tobago)", "Tunisia" => "Tunisia (تونس)", "Turkey" => "Turkey (Türkiye)", "Turkmenistan" => "Turkmenistan (Türkmenistan)", "Turks And Caicos Islands" => "Turks And Caicos Islands (Turks and Caicos Islands)", "Tuvalu" => "Tuvalu (Tuvalu)", "Uganda" => "Uganda (Uganda)", "Ukraine" => "Ukraine (Україна)", "United Arab Emirates" => "United Arab Emirates (دولة الإمارات العربية المتحدة)", "United Kingdom" => "United Kingdom (United Kingdom)", "United States" => "United States (United States)", "United States Minor Outlying Islands" => "United States Minor Outlying Islands (United States Minor Outlying Islands)", "Uruguay" => "Uruguay (Uruguay)", "Uzbekistan" => "Uzbekistan (O‘zbekiston)", "Vanuatu" => "Vanuatu (Vanuatu)", "Vatican City State (Holy See)" => "Vatican City State (Holy See) (Vaticano)", "Venezuela" => "Venezuela (Venezuela)",
          "Vietnam" => "Vietnam (Việt Nam)", "Virgin Islands (British)" => "Virgin Islands (British) (British Virgin Islands)", "Virgin Islands (US)" => "Virgin Islands (US) (United States Virgin Islands)", "Wallis And Futuna Islands" => "Wallis And Futuna Islands (Wallis et Futuna)", "Western Sahara" => "Western Sahara (الصحراء الغربية)", "Yemen" => "Yemen (اليَمَن)", "Zambia" => "Zambia (Zambia)", "Zimbabwe" => "Zimbabwe (Zimbabwe)"
        );
        $select_countries = "<select onchange='get_cities(this)' name='selected_country' id='selected_country' class='form-control multi_class mt'>
      <option value='" . $country . "' selected>" . $country . "</option>
      <option value='' disabled>--- Select a country ---</option>";
        foreach ($country_array as $key => $val) {
          $select_countries .= "<option value='" . $key . "'>" . $val . "</option>";
        }
        $select_countries .= "<select>";
        echo $select_countries; ?>
      </div>
      <div class="form-group col-md-3 append_cities">
        <label>Select a city *</label>
        <select onchange='other_city(this)' name='selected_city' id='selected_city' class='form-control mt' required>
          <?php if ($city) { ?>
            <option value="<?php echo $city; ?>" selected><?php echo $city; ?></option>
          <?php }
          if (isset($country)) {
            $ch = curl_init();
            $postData = [
              "country" => $country
            ];
            curl_setopt($ch, CURLOPT_URL, "https://countriesnow.space/api/v0.1/countries/cities");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            $cities_array = json_decode(curl_exec($ch));
            $cities_array = $cities_array->data;
            $select_cities = "<option value='' disabled>--- Select a city ---</option>";
            if (count($cities_array) > 0) {
              foreach ($cities_array as $key => $val) {
                $select_cities .= "<option value='" . $val . "'>" . $val . "</option>";
              }
              $select_cities .= "<option value='Not in List'>Not in List</option>";
            } else {
              $select_cities .= "<option value='Not in List'>No City Found</option>";
            }
            echo $select_cities;
          } ?>
        </select>
      </div>
      <div class="form-group col-md-3 div_other_city_field hidden">
        <label>Enter City Name *</label>
        <input name="city" type="text" class="form-control mt other_city_field hidden" value="<?php echo $city; ?>" placeholder="Enter City Name" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Post Code </label>
        <input name="postCode" type="text" class="form-control" placeholder="Post Code" value="<?php echo $postCode; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="row1">Building Number / Name </label>
        <input name="buildingName" type="text" class="form-control" placeholder="Building Number / Name" value="<?php echo $buildingName; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 1 </label>
        <input name="line1" type="text" required='' class="form-control" placeholder="Address Line 1" value="<?php echo $line1; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 2</label>
        <input name="line2" type="text" class="form-control" placeholder="Address Line 2" value="<?php echo $line2; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 3 </label>
        <input name="streetRoad" type="text" class="form-control" placeholder="Address Line 3" value="<?php echo $streetRoad; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Invoice Email</label>
        <input name="invEmail" type="text" id="invEmail" class="form-control" placeholder="Invoicing Email Address" value="<?php echo $invEmail; ?>" />
      </div>
      </p>
      <!-- <p>
                  <label class="optional">Invoice Address 
           	  </label>
                  <input name="invAddrs" type="text" value="<?php echo $invAddrs; ?>" />
              <?php //if(isset($_POST['submit'])){$c17=$_POST['invAddrs'];$obj->editFun($table,$edit_id,'invAddrs',$c17);} 
              ?>
    </p>-->
      <div class="bg-info col-xs-12 form-group">
        <h4>Trading Address (Team or Unit Address)</h4>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="row1">Authorised Person Name</label>
        <input class="form-control valid" name="taupn" type="text" id="taupn" placeholder="Authorised Person Name" id="taupn" value="<?php echo $taupn; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Position in the Company</label>
        <input name="tpitc" type="text" id="tpitc" class="form-control" placeholder="Position in the Company" value="<?php echo $tpitc; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Select a city *</label>
        <select onchange="other_tcity(this)" name="selected_tcity" id="selected_tcity" class="form-control">
          <?php if ($tcity) { ?>
            <option value="<?php echo $tcity; ?>" selected><?php echo $tcity; ?></option>
          <?php }
          if (isset($country)) {
            echo $select_cities;
          } ?>
        </select>
      </div>
      <div class="form-group col-md-3 div_other_tcity_field hidden">
        <label class="optional">Enter City Name</label>
        <input name="tcity" type="text" class="form-control mt other_tcity_field hidden" value="<?php echo $tcity; ?>" placeholder="Enter City Name" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Post Code</label>
        <input name="tpostCode" type="text" id="tpostCode" class="form-control" placeholder="Post Code" value="<?php echo $tpostCode; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="row1">Building Number / Name</label>
        <input name="tbuildingName" type="text" id="tbuildingName" class="form-control" placeholder="Building Number / Name" value="<?php echo $tbuildingName; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 1</label>
        <input class="form-control" name="tline1" type="text" id="tline1" placeholder="Address Line 1" value="<?php echo $tline1; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 2</label>
        <input class="form-control" name="tline2" type="text" id="tline2" placeholder="Address Line 2" value="<?php echo $tline2; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Address Line 3</label>
        <input class="form-control" name="tstreetRoad" type="text" id="tstreetRoad" placeholder="Address Line 3" value="<?php echo $tstreetRoad; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Contact Name</label>
        <input name="tcn" type="text" id="tcn" class="form-control valid" placeholder="Contact Name" value="<?php echo $tcn; ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label class="optional">Registration Form Filled</label>
        <input name="rff" type="date" id="rff" class="form-control" placeholder="Registration Form Filled" value="<?php echo $rff; ?>" />
      </div>
      <div class="form-group col-sm-6">
        <textarea placeholder="Notes for Company ..." class="form-control" name="note" rows="3"><?php echo $note; ?></textarea>
      </div>
      <div class="bg-info col-xs-12 form-group">
        <h4>Extra Attributes For Company</h4>
      </div>
      <div class="form-group col-md-3 col-sm-4">
        <label class="checkbox-inline">
          <input onchange="if(this.checked) {$('#div_po_email').removeClass('hidden');}else{$('#div_po_email').addClass('hidden');}" <?php echo $po_req == 1 ? 'checked' : ''; ?> type="checkbox" id="po_req" name="po_req" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Purchase Order ?</b>
        </label>
      </div>
      <div class="form-group col-md-3 col-sm-4 <?php echo $po_req == 1 ? '' : 'hidden'; ?>" id="div_po_email">
        <input name="po_email" type="text" id="po_email" class="form-control" placeholder="Enter Purchase Order Email" value="<?php if ($po_req == 1) {
                                                                                                                                echo $po_email;
                                                                                                                              } ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-4">
        <label class="checkbox-inline">
          <input onchange="if(this.checked) {$('#div_admin_rate').removeClass('hidden');}else{$('#div_admin_rate').addClass('hidden');}" type="checkbox" id="admin_ch" name="admin_ch" value="1" data-toggle="toggle" data-on="Yes" data-off="No" <?php if ($admin_ch == 1) {
                                                                                                                                                                                                                                                    echo "checked";
                                                                                                                                                                                                                                                  } ?>> <b>Admin Charge ?</b></label>
      </div>
      <div class="form-group col-md-3 col-sm-4 <?php if ($admin_ch == 0) {
                                                  echo "hidden";
                                                } ?>" id="div_admin_rate">
        <input name="admin_rate" type="text" id="admin_rate" class="form-control" placeholder="Rate For Admin Charge" value="<?php if ($admin_ch == 0) {
                                                                                                                                echo 0;
                                                                                                                              } else {
                                                                                                                                echo $admin_rate;
                                                                                                                              } ?>" />
      </div>
      <div class="form-group col-md-3 col-sm-4">
        <label class="checkbox-inline">
          <input type="checkbox" id="tr_time" name="tr_time" value="1" data-toggle="toggle" data-on="Yes" data-off="No" <?php if ($tr_time == 1) {
                                                                                                                          echo "checked";
                                                                                                                        } ?>> <b>Travel Time ?</b></label>
      </div>
      <div class="form-group col-md-3 col-sm-4">
        <label class="checkbox-inline">
          <input type="checkbox" id="interp_time" name="interp_time" value="1" data-toggle="toggle" data-on="Yes" data-off="No" <?php if ($interp_time == 1) {
                                                                                                                                  echo "checked";
                                                                                                                                } ?>> <b>Interpreting Time ?</b></label>
      </div>
      <div class="form-group col-md-3 col-sm-4">
        <label class="checkbox-inline">
          <input type="checkbox" id="wait_time" name="wait_time" value="1" data-toggle="toggle" data-on="Yes" data-off="No" <?php if ($wait_time == 1) {
                                                                                                                              echo "checked";
                                                                                                                            } ?>> <b>Waiting Time ?</b></label>
      </div>
      <div class="form-group col-md-4 col-sm-4">
		<label class="checkbox-inline">
			<input type="checkbox" id="remote_unit_in_hours" name="remote_unit_in_hours" value="0" data-toggle="toggle" data-on="Yes" data-off="No" <?php if ($remote_unit_in_hours==1 ) { echo "checked"; } ?>> <b>Show Remote unit in hours ?</b></label>
	</div>
    </form>
	
	<?php } else { ?>
	
	<!-- ================== Creating Duplicate Company ==================== -->
	
	<form action="" method="post" id="frm_duplicate_comp" class="register" enctype="multipart/form-data">
		<input type="hidden" name="MM_validator" id="MM_validator" value="0">
	<div style="background: #8c8c86;padding: 6px;position: fixed;z-index: 999999999999;width: 100%;color: white;"> <b>
          <h4 style="display: inline-block;">Company Registration <span class="hidden-xs">Details</span> (Create Duplicate)</h4>
        </b>
		<button class="btn btn-success pull-right hide" style="border-color: #000000;color: black;text-transform: uppercase;margin: 7px 21px;font-size: 16px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="button" name="btn_submit_form" id="btn_submit_form">CREATE DUPLICATE &raquo;</button>
		
		<button class="btn btn-warning pull-right" style="border-color: #000000;color: black;text-transform: uppercase;margin: 7px 21px;font-size: 16px;font-weight: bold;box-shadow: 2px 2px 2px #c5c5a3;" type="button" name="btn_check_duplicate" id="btn_check_duplicate">CHECK DUPLICATE &raquo;</button>
	</div>
	<br>
	<br>
	<br>
	<br>
	<div class="form-group col-md-3 col-sm-6">
		<label>Company Name *</label>
		<input placeholder="Company Name *" name="name" class="form-control" type="text" required='' id="name" onchange="uniqueFun(this.value,'comp_reg','name',$(this).attr('id') );" onBlur="uniqueFun($('#abrv').val(),'comp_reg','abrv',$('#abrv').attr('id') );" oninput="short_name();" onfocusout="short_name();" value="<?php echo $name; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<?php
          $result_opt = $obj->read_all("DISTINCT id,name,abrv", "comp_reg", "comp_nature IN (1,4)");
          // $sql_opt = "SELECT DISTINCT id,name,abrv from comp_reg WHERE comp_nature=1 ORDER BY name ASC"; ?>
			<label>Parent/Head Units (if any)</label>
			<select id="p_org" name="p_org" class="form-control searchable">
				<?php 
              // $result_opt = mysqli_query($con, $sql_opt);
              $options = "";
              while ($row_opt = mysqli_fetch_assoc($result_opt)) {
                  $comp_id = $row_opt["id"];
                  $code = $row_opt["abrv"];
                  $name_opt = $row_opt["name"];
                  $options .= "<OPTION value='$comp_id' " . ($comp_id == $parent_comp ? 'selected' : '') . ">" . $name_opt . ' (' . $code . ')';
              }
              ?>
					<option value="">Select Parent/Head Units (if any)</option>
					<?php echo $options; ?>
						</option>
			</select>
	</div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Abrivaiton of Company * </label>
		<input placeholder="Abrivaiton of Company  *" name="abrv" class="form-control valid" type="text" id="abrv" readonly value="<?php echo $abrv; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Contact Person *</label>
		<input placeholder="Contact Person *" name="contactPerson" class="form-control valid" type="text" id="contactPerson" required='' value="<?php echo $contactPerson; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Payment Terms </label>
		<select name="payment_terms" id="payment_terms" class="form-control" required>
			<option selected value="<?php echo $payment_terms; ?>">
				<?php echo $payment_terms == 0 ? 'Pay Now' : $payment_terms . ' Days'; ?>
			</option>
			<option value="">Select Payment Terms</option>
			<option value="0">Pay Now</option>
			<option value="7">7 Days</option>
			<option value="14">14 Days</option>
			<option value="21">21 Days</option>
			<option value="28">28 Days</option>
			<option value="30">30 Days</option>
			<option value="35">35 Days</option>
			<option value="42">42 Days</option>
			<option value="49">49 Days</option>
			<option value="56">56 Days</option>
			<option value="63">63 Days</option>
			<option value="63">63 Days</option>
			<option value="70">70 Days</option>
			<option value="77">77 Days</option>
			<option value="84">84 Days</option>
			<option value="91">91 Days</option>
			<option value="98">98 Days</option>
		</select>
	</div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Branch or Department</label>
		<input placeholder="Branch or Department" name="bod" type="text" id="bod" class="form-control" value="<?php echo $bod; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Company Type</label>
		<select name="type_id" id="type_id" class="form-control" required>
			<?php
          $get_types = $obj->read_all("comp_type.id,comp_type.title,company_types.title as group_name", "comp_type,company_types", "comp_type.company_type_id=company_types.id ORDER BY comp_type.title ASC");?>
				<option value="" disabled>Select Company Type</option>
				<?php while ($row_opt = $get_types->fetch_assoc()) {
            $selected_type = $row['type_id'] == $row_opt['id'] ? 'selected' : '';
            echo "<option value='" . $row_opt['id'] . "' ".$selected_type.">" . $row_opt['title'] . " (" . $row_opt['group_name'] . ")</option>";
          }
          ?>
		</select>
	</div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Company Registration Number </label>
		<input name="crn" type="text" id="crn" class="form-control" placeholder="Company Registration Number" value="<?php echo $crn; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label>VAT Number *</label>
		<input name="vn" type="text" id="vn" class="form-control" placeholder="VAT Number *" value="<?php echo $vn; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Contact No 1 * </label>
		<input name="contactNo1" type="text" id="contactNo1" required='' class="form-control" placeholder="Contact No 1 *" value="<?php echo $contactNo1; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Contact No 2 </label>
		<input placeholder="Contact No 2" name="contactNo2" type="text" id="contactNo2" class="form-control" value="<?php echo $contactNo2; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Company Fax No # </label>
		<input placeholder="Company Fax No" name="contactNo3" type="text" id="contactNo3" class="form-control" value="<?php echo $contactNo3; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Company Website</label>
		<input name="web" type="text" id="web" class="form-control" placeholder="Company Website" value="<?php echo $web; ?>" /> </div>
    <div class="form-group col-md-3 col-sm-6">
    <label>Credit Limit * </label>
      <input name="credit_limit" type="number" id="credit_limit" class="form-control" required='' placeholder="Credit Limit in £" value="<?php echo $credit_limit; ?>" />
    </div>
    <div class="form-group col-md-3 col-sm-6">
    <label>Mileage included in Hourly Cost</label>
      <input name="mileage" type="text" id="mileage" class="form-control" required='' placeholder="Enter Mileage for Hourly Cost Package" value="<?php echo $mileage; ?>" />
    </div>
    <div class="form-group col-md-3 col-sm-6">
    <label>Travel Time included in Hourly Cost</label>
      <input name="travel_time" type="text" id="travel_time" class="form-control" required='' placeholder="Enter Travel Time for Hourly Cost Package" value="<?php echo $travel_time; ?>" />
    </div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Email Address * </label>
		<input name="email" type="text" id="email" class="form-control" required='' placeholder="Email Address *" value="<?php echo $email; ?>" /> </div>
	<div class="bg-info col-xs-12 form-group">
		<h4>Invoicing Address (Team or Unit Address)</h4> </div>
	<div class="form-group col-md-3">
		<label>Select a country *</label>
		<br>
		<?php
        $country_array = array(
          "Afghanistan" => "Afghanistan (افغانستان)", "Aland Islands" => "Aland Islands (Åland)", "Albania" => "Albania (Shqipëria)", "Algeria" => "Algeria (الجزائر)", "American Samoa" => "American Samoa (American Samoa)", "Andorra" => "Andorra (Andorra)", "Angola" => "Angola (Angola)", "Anguilla" => "Anguilla (Anguilla)", "Antarctica" => "Antarctica (Antarctica)", "Antigua And Barbuda" => "Antigua And Barbuda (Antigua and Barbuda)", "Argentina" => "Argentina (Argentina)", "Armenia" => "Armenia (Հայաստան)", "Aruba" => "Aruba (Aruba)", "Australia" => "Australia (Australia)", "Austria" => "Austria (Österreich)", "Azerbaijan" => "Azerbaijan (Azərbaycan)", "Bahamas The" => "Bahamas The (Bahamas)", "Bahrain" => "Bahrain (‏البحرين)", "Bangladesh" => "Bangladesh (Bangladesh)", "Barbados" => "Barbados (Barbados)", "Belarus" => "Belarus (Белару́сь)", "Belgium" => "Belgium (België)", "Belize" => "Belize (Belize)", "Benin" => "Benin (Bénin)", "Bermuda" => "Bermuda (Bermuda)", "Bhutan" => "Bhutan (ʼbrug-yul)", "Bolivia" => "Bolivia (Bolivia)", "Bonaire, Sint Eustatius and Saba" => "Bonaire, Sint Eustatius and Saba (Caribisch Nederland)", "Bosnia and Herzegovina" => "Bosnia and Herzegovina (Bosna i Hercegovina)", "Botswana" => "Botswana (Botswana)", "Bouvet Island" => "Bouvet Island (Bouvetøya)", "Brazil" => "Brazil (Brasil)", "British Indian Ocean Territory" => "British Indian Ocean Territory (British Indian Ocean Territory)", "Brunei" => "Brunei (Negara Brunei Darussalam)", "Bulgaria" => "Bulgaria (България)", "Burkina Faso" => "Burkina Faso (Burkina Faso)", "Burundi" => "Burundi (Burundi)", "Cambodia" => "Cambodia (Kâmpŭchéa)", "Cameroon" => "Cameroon (Cameroon)", "Canada" => "Canada (Canada)", "Cape Verde" => "Cape Verde (Cabo Verde)", "Cayman Islands" => "Cayman Islands (Cayman Islands)", "Central African Republic" => "Central African Republic (Ködörösêse tî Bêafrîka)", "Chad" => "Chad (Tchad)", "Chile" => "Chile (Chile)", "China" => "China (中国)", "Christmas Island" => "Christmas Island (Christmas Island)", "Cocos (Keeling) Islands" => "Cocos (Keeling) Islands (Cocos (Keeling) Islands)", "Colombia" => "Colombia (Colombia)", "Comoros" => "Comoros (Komori)", "Congo" => "Congo (République du Congo)", "Congo The Democratic Republic Of The" => "Congo The Democratic Republic Of The (République démocratique du Congo)", "Cook Islands" => "Cook Islands (Cook Islands)", "Costa Rica" => "Costa Rica (Costa Rica)", "Cote D'Ivoire (Ivory Coast)" => "Cote D'Ivoire (Ivory Coast) ()", "Croatia (Hrvatska)" => "Croatia (Hrvatska) (Hrvatska)", "Cuba" => "Cuba (Cuba)", "Curaçao" => "Curaçao (Curaçao)", "Cyprus" => "Cyprus (Κύπρος)", "Czech Republic" => "Czech Republic (Česká republika)", "Denmark" => "Denmark (Danmark)", "Djibouti" => "Djibouti (Djibouti)", "Dominica" => "Dominica (Dominica)", "Dominican Republic" => "Dominican Republic (República Dominicana)", "East Timor" => "East Timor (Timor-Leste)", "Ecuador" => "Ecuador (Ecuador)", "Egypt" => "Egypt (مصر‎)", "El Salvador" => "El Salvador (El Salvador)", "Equatorial Guinea" => "Equatorial Guinea (Guinea Ecuatorial)", "Eritrea" => "Eritrea (ኤርትራ)", "Estonia" => "Estonia (Eesti)", "Ethiopia" => "Ethiopia (ኢትዮጵያ)", "Falkland Islands" => "Falkland Islands (Falkland Islands)", "Faroe Islands" => "Faroe Islands (Føroyar)", "Fiji Islands" => "Fiji Islands (Fiji)", "Finland" => "Finland (Suomi)", "France" => "France (France)", "French Guiana" => "French Guiana (Guyane française)", "French Polynesia" => "French Polynesia (Polynésie française)", "French Southern Territories" => "French Southern Territories (Territoire des Terres australes et antarctiques fr)", "Gabon" => "Gabon (Gabon)", "Gambia The" => "Gambia The (Gambia)", "Georgia" => "Georgia (საქართველო)", "Germany" => "Germany (Deutschland)", "Ghana" => "Ghana (Ghana)", "Gibraltar" => "Gibraltar (Gibraltar)", "Greece" => "Greece (Ελλάδα)", "Greenland" => "Greenland (Kalaallit Nunaat)", "Grenada" => "Grenada (Grenada)", "Guadeloupe" => "Guadeloupe (Guadeloupe)", "Guam" => "Guam (Guam)", "Guatemala" => "Guatemala (Guatemala)", "Guernsey and Alderney" => "Guernsey and Alderney (Guernsey)", "Guinea" => "Guinea (Guinée)", "Guinea-Bissau" => "Guinea-Bissau (Guiné-Bissau)", "Guyana" => "Guyana (Guyana)", "Haiti" => "Haiti (Haïti)", "Heard Island and McDonald Islands" => "Heard Island and McDonald Islands (Heard Island and McDonald Islands)", "Honduras" => "Honduras (Honduras)", "Hong Kong S.A.R." => "Hong Kong S.A.R. (香港)", "Hungary" => "Hungary (Magyarország)", "Iceland" => "Iceland (Ísland)", "India" => "India (भारत)", "Indonesia" => "Indonesia (Indonesia)", "Iran" => "Iran (ایران)", "Iraq" => "Iraq (العراق)", "Ireland" => "Ireland (Éire)", "Israel" => "Israel (יִשְׂרָאֵל)", "Italy" => "Italy (Italia)", "Jamaica" => "Jamaica (Jamaica)", "Japan" => "Japan (日本)", "Jersey" => "Jersey (Jersey)", "Jordan" => "Jordan (الأردن)", "Kazakhstan" => "Kazakhstan (Қазақстан)", "Kenya" => "Kenya (Kenya)", "Kiribati" => "Kiribati (Kiribati)", "Korea North" => "Korea North (북한)", "Korea South" => "Korea South (대한민국)", "Kosovo" => "Kosovo (Republika e Kosovës)", "Kuwait" => "Kuwait (الكويت)", "Kyrgyzstan" => "Kyrgyzstan (Кыргызстан)", "Laos" => "Laos (ສປປລາວ)", "Latvia" => "Latvia (Latvija)", "Lebanon" => "Lebanon (لبنان)", "Lesotho" => "Lesotho (Lesotho)", "Liberia" => "Liberia (Liberia)", "Libya" => "Libya (‏ليبيا)", "Liechtenstein" => "Liechtenstein (Liechtenstein)", "Lithuania" => "Lithuania (Lietuva)", "Luxembourg" => "Luxembourg (Luxembourg)", "Macau S.A.R." => "Macau S.A.R. (澳門)", "Macedonia" => "Macedonia (Северна Македонија)", "Madagascar" => "Madagascar (Madagasikara)", "Malawi" => "Malawi (Malawi)", "Malaysia" => "Malaysia (Malaysia)", "Maldives" => "Maldives (Maldives)", "Mali" => "Mali (Mali)", "Malta" => "Malta (Malta)", "Man (Isle of)" => "Man (Isle of) (Isle of Man)", "Marshall Islands" => "Marshall Islands (M̧ajeļ)", "Martinique" => "Martinique (Martinique)", "Mauritania" => "Mauritania (موريتانيا)", "Mauritius" => "Mauritius (Maurice)", "Mayotte" => "Mayotte (Mayotte)", "Mexico" => "Mexico (México)", "Micronesia" => "Micronesia (Micronesia)", "Moldova" => "Moldova (Moldova)", "Monaco" => "Monaco (Monaco)", "Mongolia" => "Mongolia (Монгол улс)", "Montenegro" => "Montenegro (Црна Гора)", "Montserrat" => "Montserrat (Montserrat)", "Morocco" => "Morocco (المغرب)", "Mozambique" => "Mozambique (Moçambique)", "Myanmar" => "Myanmar (မြန်မာ)", "Namibia" => "Namibia (Namibia)", "Nauru" => "Nauru (Nauru)", "Nepal" => "Nepal (नपल)", "Netherlands The" => "Netherlands The (Nederland)", "New Caledonia" => "New Caledonia (Nouvelle-Calédonie)", "New Zealand" => "New Zealand (New Zealand)", "Nicaragua" => "Nicaragua (Nicaragua)", "Niger" => "Niger (Niger)", "Nigeria" => "Nigeria (Nigeria)", "Niue" => "Niue (Niuē)", "Norfolk Island" => "Norfolk Island (Norfolk Island)", "Northern Mariana Islands" => "Northern Mariana Islands (Northern Mariana Islands)", "Norway" => "Norway (Norge)", "Oman" => "Oman (عمان)", "Pakistan" => "Pakistan (پاکستان)", "Palau" => "Palau (Palau)", "Palestinian Territory Occupied" => "Palestinian Territory Occupied (فلسطين)", "Panama" => "Panama (Panamá)", "Papua new Guinea" => "Papua new Guinea (Papua Niugini)", "Paraguay" => "Paraguay (Paraguay)", "Peru" => "Peru (Perú)", "Philippines" => "Philippines (Pilipinas)", "Pitcairn Island" => "Pitcairn Island (Pitcairn Islands)", "Poland" => "Poland (Polska)", "Portugal" => "Portugal (Portugal)", "Puerto Rico" => "Puerto Rico (Puerto Rico)", "Qatar" => "Qatar (قطر)", "Reunion" => "Reunion (La Réunion)", "Romania" => "Romania (România)", "Russia" => "Russia (Россия)", "Rwanda" => "Rwanda (Rwanda)", "Saint Helena" => "Saint Helena (Saint Helena)", "Saint Kitts And Nevis" => "Saint Kitts And Nevis (Saint Kitts and Nevis)", "Saint Lucia" => "Saint Lucia (Saint Lucia)", "Saint Pierre and Miquelon" => "Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "Saint Vincent And The Grenadines" => "Saint Vincent And The Grenadines (Saint Vincent and the Grenadines)", "Saint-Barthelemy" => "Saint-Barthelemy (Saint-Barthélemy)", "Saint-Martin (French part)" => "Saint-Martin (French part) (Saint-Martin)", "Samoa" => "Samoa (Samoa)", "San Marino" => "San Marino (San Marino)", "Sao Tome and Principe" => "Sao Tome and Principe (São Tomé e Príncipe)", "Saudi Arabia" => "Saudi Arabia (العربية السعودية)", "Senegal" => "Senegal (Sénégal)", "Serbia" => "Serbia (Србија)", "Seychelles" => "Seychelles (Seychelles)", "Sierra Leone" => "Sierra Leone (Sierra Leone)", "Singapore" => "Singapore (Singapore)", "Sint Maarten (Dutch part)" => "Sint Maarten (Dutch part) (Sint Maarten)", "Slovakia" => "Slovakia (Slovensko)", "Slovenia" => "Slovenia (Slovenija)", "Solomon Islands" => "Solomon Islands (Solomon Islands)", "Somalia" => "Somalia (Soomaaliya)", "South Africa" => "South Africa (South Africa)", "South Georgia" => "South Georgia (South Georgia)", "South Sudan" => "South Sudan (South Sudan)", "Spain" => "Spain (España)", "Sri Lanka" => "Sri Lanka (śrī laṃkāva)", "Sudan" => "Sudan (السودان)", "Suriname" => "Suriname (Suriname)", "Svalbard And Jan Mayen Islands" => "Svalbard And Jan Mayen Islands (Svalbard og Jan Mayen)", "Swaziland" => "Swaziland (Swaziland)", "Sweden" => "Sweden (Sverige)", "Switzerland" => "Switzerland (Schweiz)", "Syria" => "Syria (سوريا)", "Taiwan" => "Taiwan (臺灣)", "Tajikistan" => "Tajikistan (Тоҷикистон)", "Tanzania" => "Tanzania (Tanzania)", "Thailand" => "Thailand (ประเทศไทย)", "Togo" => "Togo (Togo)", "Tokelau" => "Tokelau (Tokelau)", "Tonga" => "Tonga (Tonga)", "Trinidad And Tobago" => "Trinidad And Tobago (Trinidad and Tobago)", "Tunisia" => "Tunisia (تونس)", "Turkey" => "Turkey (Türkiye)", "Turkmenistan" => "Turkmenistan (Türkmenistan)", "Turks And Caicos Islands" => "Turks And Caicos Islands (Turks and Caicos Islands)", "Tuvalu" => "Tuvalu (Tuvalu)", "Uganda" => "Uganda (Uganda)", "Ukraine" => "Ukraine (Україна)", "United Arab Emirates" => "United Arab Emirates (دولة الإمارات العربية المتحدة)", "United Kingdom" => "United Kingdom (United Kingdom)", "United States" => "United States (United States)", "United States Minor Outlying Islands" => "United States Minor Outlying Islands (United States Minor Outlying Islands)", "Uruguay" => "Uruguay (Uruguay)", "Uzbekistan" => "Uzbekistan (O‘zbekiston)", "Vanuatu" => "Vanuatu (Vanuatu)", "Vatican City State (Holy See)" => "Vatican City State (Holy See) (Vaticano)", "Venezuela" => "Venezuela (Venezuela)",
          "Vietnam" => "Vietnam (Việt Nam)", "Virgin Islands (British)" => "Virgin Islands (British) (British Virgin Islands)", "Virgin Islands (US)" => "Virgin Islands (US) (United States Virgin Islands)", "Wallis And Futuna Islands" => "Wallis And Futuna Islands (Wallis et Futuna)", "Western Sahara" => "Western Sahara (الصحراء الغربية)", "Yemen" => "Yemen (اليَمَن)", "Zambia" => "Zambia (Zambia)", "Zimbabwe" => "Zimbabwe (Zimbabwe)"
        );
        $select_countries = "<select onchange='get_cities(this)' name='selected_country' id='selected_country' class='form-control multi_class mt'>
      <option value='" . $country . "' selected>" . $country . "</option>
      <option value='' disabled>--- Select a country ---</option>";
        foreach ($country_array as $key => $val) {
          $select_countries .= "<option value='" . $key . "'>" . $val . "</option>";
        }
        $select_countries .= "<select>";
        echo $select_countries; ?>
	</div>
	<div class="form-group col-md-3 append_cities">
		<label>Select a city *</label>
		<select onchange='other_city(this)' name='selected_city' id='selected_city' class='form-control mt' required>
			<?php if ($city) { ?>
				<option value="<?php echo $city; ?>" selected>
					<?php echo $city; ?>
				</option>
				<?php }
          if (isset($country)) {
            $ch = curl_init();
            $postData = [
              "country" => $country
            ];
            curl_setopt($ch, CURLOPT_URL, "https://countriesnow.space/api/v0.1/countries/cities");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            $cities_array = json_decode(curl_exec($ch));
            $cities_array = $cities_array->data;
            $select_cities = "<option value='' disabled>--- Select a city ---</option>";
            if (count($cities_array) > 0) {
              foreach ($cities_array as $key => $val) {
                $select_cities .= "<option value='" . $val . "'>" . $val . "</option>";
              }
              $select_cities .= "<option value='Not in List'>Not in List</option>";
            } else {
              $select_cities .= "<option value='Not in List'>No City Found</option>";
            }
            echo $select_cities;
          } ?>
		</select>
	</div>
	<div class="form-group col-md-3 div_other_city_field hidden">
		<label>Enter City Name *</label>
		<input name="city" type="text" class="form-control mt other_city_field hidden" value="<?php echo $city; ?>" placeholder="Enter City Name" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Post Code </label>
		<input name="postCode" type="text" class="form-control" placeholder="Post Code" value="<?php echo $postCode; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="row1">Building Number / Name </label>
		<input name="buildingName" type="text" class="form-control" placeholder="Building Number / Name" value="<?php echo $buildingName; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Address Line 1 </label>
		<input name="line1" type="text" required='' class="form-control" placeholder="Address Line 1" value="<?php echo $line1; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Address Line 2</label>
		<input name="line2" type="text" class="form-control" placeholder="Address Line 2" value="<?php echo $line2; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Address Line 3 </label>
		<input name="streetRoad" type="text" class="form-control" placeholder="Address Line 3" value="<?php echo $streetRoad; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Invoice Email</label>
		<input name="invEmail" type="text" id="invEmail" class="form-control" placeholder="Invoicing Email Address" value="<?php echo $invEmail; ?>" /> </div>
	</p>
	<!-- <p>
                  <label class="optional">Invoice Address 
           	  </label>
                  <input name="invAddrs" type="text" value="<?php echo $invAddrs; ?>" />
              <?php //if(isset($_POST['submit'])){$c17=$_POST['invAddrs'];$obj->editFun($table,$edit_id,'invAddrs',$c17);} 
              ?>
    </p>-->
	<div class="bg-info col-xs-12 form-group">
		<h4>Trading Address (Team or Unit Address)</h4> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="row1">Authorised Person Name</label>
		<input class="form-control valid" name="taupn" type="text" id="taupn" placeholder="Authorised Person Name" id="taupn" value="<?php echo $taupn; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Position in the Company</label>
		<input name="tpitc" type="text" id="tpitc" class="form-control" placeholder="Position in the Company" value="<?php echo $tpitc; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label>Select a city *</label>
		<select onchange="other_tcity(this)" name="selected_tcity" id="selected_tcity" class="form-control">
			<?php if ($tcity) { ?>
				<option value="<?php echo $tcity; ?>" selected>
					<?php echo $tcity; ?>
				</option>
				<?php }
          if (isset($country)) {
            echo $select_cities;
          } ?>
		</select>
	</div>
	<div class="form-group col-md-3 div_other_tcity_field hidden">
		<label class="optional">Enter City Name</label>
		<input name="tcity" type="text" class="form-control mt other_tcity_field hidden" value="<?php echo $tcity; ?>" placeholder="Enter City Name" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Post Code</label>
		<input name="tpostCode" type="text" id="tpostCode" class="form-control" placeholder="Post Code" value="<?php echo $tpostCode; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="row1">Building Number / Name</label>
		<input name="tbuildingName" type="text" id="tbuildingName" class="form-control" placeholder="Building Number / Name" value="<?php echo $tbuildingName; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Address Line 1</label>
		<input class="form-control" name="tline1" type="text" id="tline1" placeholder="Address Line 1" value="<?php echo $tline1; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Address Line 2</label>
		<input class="form-control" name="tline2" type="text" id="tline2" placeholder="Address Line 2" value="<?php echo $tline2; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Address Line 3</label>
		<input class="form-control" name="tstreetRoad" type="text" id="tstreetRoad" placeholder="Address Line 3" value="<?php echo $tstreetRoad; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Contact Name</label>
		<input name="tcn" type="text" id="tcn" class="form-control valid" placeholder="Contact Name" value="<?php echo $tcn; ?>" /> </div>
	<div class="form-group col-md-3 col-sm-6">
		<label class="optional">Registration Form Filled</label>
		<input name="rff" type="date" id="rff" class="form-control" placeholder="Registration Form Filled" value="<?php echo $rff; ?>" /> </div>
	<div class="form-group col-sm-6">
		<textarea placeholder="Notes for Company ..." class="form-control" name="note" rows="3"><?php echo $note; ?></textarea>
	</div>
	<div class="bg-info col-xs-12 form-group">
		<h4>Extra Attributes For Company</h4> </div>
	<div class="form-group col-md-3 col-sm-4">
		<label class="checkbox-inline">
			<input onchange="if(this.checked) {$('#div_po_email').removeClass('hidden');}else{$('#div_po_email').addClass('hidden');}" <?php echo $po_req==1 ? 'checked' : ''; ?> type="checkbox" id="po_req" name="po_req" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Purchase Order ?</b> </label>
	</div>
	<div class="form-group col-md-3 col-sm-4 <?php echo $po_req == 1 ? '' : 'hidden'; ?>" id="div_po_email">
		<input name="po_email" type="text" id="po_email" class="form-control" placeholder="Enter Purchase Order Email" value="<?php if ($po_req == 1) {
                                                                                                                                echo $po_email;
                                                                                                                              } ?>" /> </div>
	<div class="form-group col-md-3 col-sm-4">
		<label class="checkbox-inline">
			<input onchange="if(this.checked) {$('#div_admin_rate').removeClass('hidden');}else{$('#div_admin_rate').addClass('hidden');}" type="checkbox" id="admin_ch" name="admin_ch" value="1" data-toggle="toggle" data-on="Yes" data-off="No" <?php if ($admin_ch==1 ) { echo "checked"; } ?>> <b>Admin Charge ?</b></label>
	</div>
	<div class="form-group col-md-3 col-sm-4 <?php if ($admin_ch == 0) {
                                                  echo " hidden ";
                                                } ?>" id="div_admin_rate">
		<input name="admin_rate" type="text" id="admin_rate" class="form-control" placeholder="Rate For Admin Charge" value="<?php if ($admin_ch == 0) {
                                                                                                                                echo 0;
                                                                                                                              } else {
                                                                                                                                echo $admin_rate;
                                                                                                                              } ?>" /> </div>
	<div class="form-group col-md-3 col-sm-4">
		<label class="checkbox-inline">
			<input type="checkbox" id="tr_time" name="tr_time" value="1" data-toggle="toggle" data-on="Yes" data-off="No" <?php if ($tr_time==1 ) { echo "checked"; } ?>> <b>Travel Time ?</b></label>
	</div>
	<div class="form-group col-md-3 col-sm-4">
		<label class="checkbox-inline">
			<input type="checkbox" id="interp_time" name="interp_time" value="1" data-toggle="toggle" data-on="Yes" data-off="No" <?php if ($interp_time==1 ) { echo "checked"; } ?>> <b>Interpreting Time ?</b></label>
	</div>
	<div class="form-group col-md-3 col-sm-4">
		<label class="checkbox-inline">
			<input type="checkbox" id="wait_time" name="wait_time" value="1" data-toggle="toggle" data-on="Yes" data-off="No" <?php if ($wait_time==1 ) { echo "checked"; } ?>> <b>Waiting Time ?</b></label>
	</div>
  <div class="form-group col-md-4 col-sm-4">
		<label class="checkbox-inline">
			<input type="checkbox" id="remote_unit_in_hours" name="remote_unit_in_hours" value="0" data-toggle="toggle" data-on="Yes" data-off="No" <?php if ($remote_unit_in_hours==1 ) { echo "checked"; } ?>> <b>Show Remote unit in hours?</b></label>
	</div>
</form>
	
	
	<div class="modal" id="process_modal" data-backdrop="static">
		<div class="modal-dialog modal-lg" style="width: 85%;">
		<div class="modal-content" style="padding: 15px 0;">
			<div class="modal-body process_modal_body">
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
		</div>
	</div>
	
	
	<?php } // end if URL get Action ?>
	
	
  </div>
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
  <?php
  if (isset($_POST['submit'])) {
    echo "<script>alert('Company record updated successfully.');</script>"; ?>
    <script>
      window.onunload = refreshParent;

      function refreshParent() {
        window.opener.location.reload();
      }
      window.close();
    </script>
  <?php } ?>
  <script>
    //  $(function() {
    //   $('.searchable').multiselect({
    //       includeSelectAllOption: true,
    //       numberDisplayed: 1,
    //       enableFiltering: true,
    //       enableCaseInsensitiveFiltering: true
    //   });
    // });
    $(".valid").bind('keypress paste', function(e) {
      var regex = new RegExp(/[a-z A-Z 0-9 ()]/);
      var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
      if (!regex.test(str)) {
        e.preventDefault();
        return false;
      }
    });

    function other_city(elem) {
      var selected_city = $(elem).val();
      if (selected_city != 'Not in List') {
        $('.other_city_field').val(selected_city);
      }
      if (selected_city == 'Not in List') {
        $('.other_city_field').val('');
        $(elem).removeAttr("required");
        $('.div_other_city_field,.other_city_field').removeClass('hidden');
        $('.other_city_field').attr('required', "required");
        $('.other_city_field').focus();
      } else {
        $(elem).attr('required', "required");
        $('.div_other_city_field,.other_city_field').addClass('hidden');
        $('.other_city_field').removeAttr("required");
        $('#selected_city').focus();
      }
    }

    function get_cities(elem) {
      $('.div_other_city_field,.other_city_field,.div_other_tcity_field,.other_tcity_field').addClass('hidden');
      $('.other_city_field,.other_tcity_field').val("");
      var country_name = $(elem).val();
      if (country_name) {
        $.ajax({
          url: 'ajax_add_interp_data.php',
          method: 'post',
          dataType: 'json',
          data: {
            country_name: country_name,
            type: 'get_cities_of_country'
          },
          success: function(data) {
            if (data['cities']) {
              $('.append_cities').removeClass('hidden');
              $('.append_cities').html(data['cities']);
              $("#selected_tcity").html($("#selected_city").html());
            } else {
              $('.append_cities').addClass('hidden');
              alert("Something went wrong. Try again!");
            }
          },
          error: function(xhr) {
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
          }
        });
      }
    }

    function other_tcity(elem) {
      var selected_city = $(elem).val();
      if (selected_city != 'Not in List') {
        $('.other_tcity_field').val(selected_city);
      }
      if (selected_city == 'Not in List') {
        $('.other_tcity_field').val('');
        $(elem).removeAttr("required");
        $('.div_other_tcity_field,.other_tcity_field').removeClass('hidden');
        $('.other_tcity_field').attr('required', "required");
        $('.other_tcity_field').focus();
      } else {
        $(elem).attr('required', "required");
        $('.div_other_tcity_field,.other_tcity_field').addClass('hidden');
        $('.other_tcity_field').removeAttr("required");
        $('#selected_tcity').focus();
      }
    }
	
	function short_name() {
      var arrValues = $('#name').val().split(' ');
      var short_name = '';
      // Loop over each value in the array.
      $.each(arrValues, function(intIndex, objValue) {
        if (short_name.length < 7) {
          short_name += objValue.substring(0, 1);
        }
      })
      $('#abrv').val(short_name.toUpperCase());
    }
	
	
	$(document).on('click','#btn_check_duplicate',function(e){
		var abrv = $('#abrv').val();
		if(abrv){
			$.ajax({
				url: "ajax_add_interp_data.php",
				type: 'GET',
				data: {
					action: 'check_duplicate_company',
					abrv: abrv
				},
				dataType: 'json',
				beforeSend: function(xhr) {
					$("#btn_check_duplicate").text('Please wait...').attr('disabled', true);
				},
				success: function(json_data) {
					if (json_data['matches'] > 0) {
						$("#process_modal").modal('show');
						$('.process_modal_body').html(json_data['body']);
						$('#btn_check_duplicate').removeClass('hide');
						$('#MM_validator').val(0);
						$('#btn_submit_form').addClass('hide').attr('type', 'button');
						$("#btn_check_duplicate").text('CHECK DUPLICATE').attr('disabled', false);
					} else {
						alert('No Duplicates Found! Proceed to Confirm Job.');
						$('#MM_validator').val(1);
						$('#btn_submit_form').removeClass('hide').attr('type', 'submit');
						$('#btn_check_duplicate').addClass('hide');
						$("#btn_check_duplicate").text('CHECK DUPLICATE').attr('disabled', true);
					}
				},
				error: function(xhr, status, error) {
					console.error("Error fetching data:", error);
				}
			});

			
		}else{
			alert("Please fill source language, Assignment date and time to check possible duplicates!");
		}	
	});
	
	$(document).on('click', '#btn_submit_form', function(e){
		$('#btn_submit_form').text('Please wait...').attr('disabled', true);
		$('#frm_duplicate_comp').submit();
		return false;
	});
	
	
	
$(document).on('click','#proceed_bk', function(){
	$('#btn_confirm').removeClass('hidden');
});
$(document).on("click", ".result p.click", function() {
	var element = $(this);
	element.parents(".search-box").find('input[type="text"]').val(element.text());
	element.parent(".result").empty();
	element.parents('div').prev('.confirm_element').show();
});
	
	
	
  </script>
</body>

</html>