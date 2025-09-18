<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == "" || !isset($_SESSION)) {
  session_start();
}
include "db.php";
include_once "function.php";
include "class.php";
$name = @$_GET["name"];
$srchgender = @$_GET["srchgender"];
$city = @$_GET["city"];
$lang = @$_GET["lang"];
if ($lang == "all") {
  $lang = "";
}

$active = @$_GET["active"];
$act_id = @$_GET["act_id"];
$tp = @$_GET["tp"];
$put_delete = $tp == "tr" ? "deleted_flag=1" : "deleted_flag=0";
$srcdbs_checked = @$_GET["srcdbs_checked"];
$array_tp = ["tr" => "Trashed", "ac" => "Active", "da" => "De-Activated", "nr" => "Newly Registered",];
$class = $tp == "tr" ? "alert-danger" : "alert-info";
$page_title = $array_tp[$tp] == "Active" ? "" : $array_tp[$tp];
$page = (int)(!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = $page * $limit - $limit;
$table = "interpreter_reg";
if (isset($_GET["tp"])) {
  if ($_GET["tp"] == "ac") {
    $put_active = "and $table.active=0 and $table";
  }
  if ($_GET["tp"] == "da") {
    $put_active = "and $table.active=1 and $table";
  }
  if ($_GET["tp"] == "nr") {
    $put_active = "and $table.is_temp=1 and $table";
  }
} else {
  $put_active = "and $table.active=0";
}
?>
<!doctype html>
<html lang="en">

<head>
  <title><?php echo $page_title; ?> Interpreters List</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<script>
  function myFunction() {
    var x = document.getElementById("name").value;
    if (!x) {
      x = "<?php echo $name; ?>";
    }
    var y = document.getElementById("srchgender").value;
    if (!y) {
      y = "<?php echo $srchgender; ?>";
    }
    var z = document.getElementById("city").value;
    if (!z) {
      z = "<?php echo $city; ?>";
    }
    var p = document.getElementById("lang").value;
    if (!p) {
      p = "<?php echo $lang; ?>";
    }
    var q = document.getElementById("srcdbs_checked").value;
    if (!q) {
      q = "<?php echo $srcdbs_checked; ?>";
    }
    var tp = document.getElementById("tp").value;
    if (!tp) {
      tp = "<?php echo $tp; ?>";
    }
    window.location.href = "registered_adhoc_interps.php" + '?name=' + x + '&srchgender=' + y + '&city=' + z + '&lang=' + p + '&srcdbs_checked=' + q + '&tp=' + tp;
  }
</script>

<?php include "header.php"; ?>

<body>
  <?php
  include "nav2.php";
  error_reporting(E_ALL);
  ?>
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
        <?php if ($_SESSION["prv"] == "Management" || $_SESSION["userId"] == 21) { ?>
          <div class="form-group col-md-2 col-sm-4">
            <select id="tp" onChange="myFunction()" name="tp" class="form-control">
              <?php if (!empty($tp)) { ?>
                <option value="<?php echo $array_tp[$tp]; ?>" selected><?php echo $array_tp[$tp]; ?></option>
              <?php
              } ?>
              <option value="" disabled <?php if (empty($tp)) {
                                          echo "selected";
                                        } ?>>Filter by Type</option>
              <option value="ac">Active</option>
              <option value="nr">Newly Registered</option>
              <?php if ($_SESSION["prv"] == "Management") { ?>
                <option value="da">De-Activated</option>
                <option value="tr">Trashed</option>
              <?php
              } ?>
            </select>
          <?php
        } else { ?>
            <input type="hidden" value='ac' id='tp' />
          <?php
        } ?>
          </div>
          <?php if ($_SESSION["prv"] == "Management") { ?>
            <div class="form-group col-md-1 col-sm-4">
              <a id="btn_export" href="reports_lsuk/excel/<?php echo basename(__FILE__) . "?name=" . $name . "&srchgender=" . $srchgender . "&city=" . $city . "&lang=" . $lang . "&srcdbs_checked=" . $srcdbs_checked . "&tp=" . $tp; ?>" title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel <i class="glyphicon glyphicon-download"></i></span></a>
            </div>
          <?php
          } ?>
          <div class="col-md-12">
            <div class="form-group col-md-3 col-sm-4">
              <select id="name" onChange="myFunction()" name="name" class="form-control">
                <?php
                $sql_opt = "SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city FROM interpreter_reg WHERE $put_delete $put_active AND interpreter_reg.isAdhoc = '1' ORDER BY interpreter_reg.name ASC";
                $result_opt = mysqli_query($con, $sql_opt);
                $options = "";
                while ($row_opt = mysqli_fetch_array($result_opt)) {
                  $code = $row_opt["name"];
                  $name_opt = $row_opt["name"];
                  $city_opt = $row_opt["city"];
                  $gender = $row_opt["gender"];
                  $options .= "<option value='$code'>" . $name_opt . " (" . $gender . ")" . " (" . $city_opt . ")</option>";
                }
                ?>
                <?php if (!empty($name)) { ?>
                  <option><?php echo $name; ?></option>
                <?php
                } else { ?>
                  <option value="">-- Interpreter --</option>
                <?php
                } ?>
                <?php echo $options; ?>
              </select>
            </div>
            <div class="form-group col-md-2 col-sm-4">
              <select name="srcdbs_checked" id="srcdbs_checked" onChange="myFunction()" class="form-control">
                <?php if ($srcdbs_checked != "") { ?>
                  <option value="<?php echo $srcdbs_checked; ?>"><?php if ($srcdbs_checked == 0) {
                                                                    echo "Yes";
                                                                  } else {
                                                                    echo "No";
                                                                  } ?></option>
                <?php
                } else { ?>
                  <option value="" selected> Select DBS </option>
                <?php
                } ?>
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
                <?php
                } ?>
                <?php echo $options; ?>
              </select>
            </div>
            <div class="form-group col-md-2 col-sm-4">
              <select name="srchgender" id="srchgender" onChange="myFunction()" class="form-control">
                <?php if (!empty($srchgender)) { ?>
                  <option><?php echo $srchgender; ?></option>
                <?php
                } else { ?>
                  <option value="">Select Gender</option>
                <?php
                } ?>
                <option>Male</option>
                <option>Female</option>
              </select>
            </div>
            <div class="form-group col-md-2 col-sm-4">
              <select name="city" id="city" onChange="myFunction()" class="form-control">
                <?php if (!empty($city)) { ?>
                  <option><?php echo $city; ?></option>
                <?php
                } else { ?>
                  <option value="">Select City</option>
                <?php
                } ?>
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

      </header>


      <div>
        <div>
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
              </tr>
            </thead>
            <tbody>
              <?php
              if ($_SESSION["prv"] == "Management" || $_SESSION["prv"] == "Finance") {
                if ($adLang = "adLang" && $lang == "") {
                  $query = "SELECT distinct $table.* FROM $table 		
	   	  where $table.$put_delete and $table.dbs_checked like '$srcdbs_checked%' and  name like '$name%' and 
         gender like '$srchgender%' 	  
         and  interpreter_reg.isAdhoc = '1' and city like '$city%' $put_active	  
        LIMIT {$startpoint} , {$limit}";
                } else {
                  $query = "SELECT distinct $table.* FROM $table
		    JOIN interp_lang ON interpreter_reg.code=interp_lang.code	 
	   	  where $table.$put_delete and $table.dbs_checked like '$srcdbs_checked%'  and  
         interp_lang.lang like '$lang%' and name like '$name%' 	  
         and  interpreter_reg.isAdhoc = '1' and gender like '$srchgender%' and city like '$city%' $put_active group by email  LIMIT {$startpoint} , {$limit}";
                }
              }
              if ($_SESSION["prv"] == "Operator") {
                if ($adLang = "adLang" && $lang == "") {
                  $query = "SELECT distinct $table.* FROM $table 		
        where $table.$put_delete and $table.dbs_checked like '$srcdbs_checked%' $put_active and  name like '$name%' and gender like '$srchgender%' 	  
         and  interpreter_reg.isAdhoc = '1' and city like '$city%'	  
        LIMIT {$startpoint} , {$limit}";
                } else {
                  $query = "SELECT distinct $table.* FROM $table JOIN interp_lang ON interpreter_reg.code=interp_lang.code	 
        where $table.$put_delete and $table.dbs_checked like '$srcdbs_checked%' $put_active and  interp_lang.lang like '$lang%' and name like '$name%' and 
        gender  like '$srchgender%' 	  
         and  interpreter_reg.isAdhoc = '1' and city like '$city%'	  
        group by email  LIMIT {$startpoint} , {$limit}";
                }
              }
              // echo $query;exit;
              $result = mysqli_query($con, $query);
              // echo "<pre>"; count(mysqli_fetch_array($result)); die;
              while ($row = mysqli_fetch_array($result)) {

                $missing_doc = $acttObj->read_specific("CONCAT(CASE WHEN (applicationForm='') THEN '<b class=\"label label-danger\">* Application Form</b><br>' ELSE '' END ,
        CASE WHEN (agreement='') THEN '<b class=\"label label-danger\">* Agreement Document</b><br>' ELSE '' END,
        CASE WHEN (crbDbs='') THEN '<b class=\"label label-danger\">* DBS Document</b><br>' ELSE '' END,
        CASE WHEN (ni='') THEN '<b class=\"label label-danger\">* National Insurance Document</b><br>' ELSE '' END,
        CASE WHEN (identityDocument='') THEN '<b class=\"label label-danger\">* Identity Document</b><br>' ELSE '' END,
        CASE WHEN (acNo='') THEN '<b class=\"label label-danger\">* Bank Details</b><br>' ELSE '' END

       -- CASE WHEN (int_qualification='') THEN '<b class=\"label label-danger\">* Qualification Document</b><br>' ELSE '' END,
       -- CASE WHEN (work_evid_file='') THEN '<b class=\"label label-danger\">* Work Evidence Document</b><br>' ELSE '' END,
      --  CASE WHEN (crbDbs ='') THEN '<b class=\"label label-danger\">* DBS Document</b><br>' ELSE '' END,
      --  CASE WHEN (applicationForm ='') THEN '<b class=\"label label-danger\">Application Form</b><br>' ELSE '' END,
      --  CASE WHEN (agreement ='') THEN '<b class=\"label label-danger\">Agreement Document</b><br>' ELSE '' END,
      --  CASE WHEN (cpd = 'Not Provided') THEN '<b class=\"label label-danger\">CPD Document</b><br>' ELSE '' END
        ) as missed", "interpreter_reg", "id=" . $row["id"])["missed"]; // echo "<pre>";print_r($missing_doc);echo "</pre>"; // $missing_doc = $acttObj->read_specific('applicationForm , agreement, crbDbs, ni, identityDocument, acNo', 'interpreter_reg', 'id='.$row['id']);
                $dob = $row["dob"];
                $buildingName = $row["buildingName"];
                $line1 = $row["line1"];
                $city = $row["city"];
                $ni = $row["ni"];
              ?>

                <tr <?php if ($row["is_temp"] == 1) { ?>title="This interpreter is registered by Temporary Role. Kindly confirm to process." style="background-color:#cbda78;" <?php
                                                                                                                              } ?> <?php if ($row["active"] == 1) { ?> class="bg-danger tr_data" title="<?php echo ucwords($row["name"]); ?> is De Activated. Click on row to see actions" <?php
                                                                                                                            } else { ?> title="Click on row to see actions" class="tr_data" <?php
                                                                                                                            } ?>>
                  <td id="emtpyclr">
                    <?php if (empty($dob) || $dob == "0000-00-00" || empty($buildingName) || empty($buildingName) || empty($city) || empty($line1) || empty($ni)) { ?>
                      <span style="color:#F00"><?php echo ucwords($row["name"]); ?></span><?php
                                                                                        } else {
                                                                                          echo $row["name"];
                                                                                        } ?>
                  </td>
                  <td><?php echo $row["gender"]; ?></td>
                  <?php
                  /*<td><?php echo $row['interp']; ?></td>
    				<td><?php echo $row['telep']; ?></td> 
            <td><?php echo $row['trans']; ?></td> */
                  ?>

                  <td><?php echo $row["city"] == "Not in List" ? '<span style="color:red"><b>Not in List</b></span>' : $row["city"]; ?></td>
                  <td><?php echo $row["contactNo"] ? "<span class='label label-primary'>" . $row["contactNo"] . "</span>" : ""; ?></td>
                  <td><?php
                      echo $row["contactNo2"];
                      echo $row["other_number"] ? "<span class='label label-info'>" . $row["other_number"] . "</span>" : "";
                      ?></td>
                  <td><?php echo $row["email"]; ?></td>
                  <td><?php
                      if ($row["availability_option"] == 1) {
                        echo $row["is_marked"] == 1 ? "<span class='label label-success' style='margin:1px;' title='Available today'><i class='fa fa-check'></i></span>" : "<span class='label label-warning' style='margin:1px;' title='Not Available'><i class='fa fa-exclamation'></i></span>";
                      }
                      if (strlen($row["contactNo"]) == 0 || strlen($row["contactNo"]) > 11) {
                        echo "<span class='label label-warning'>Number not valid</span><br>";
                      }
                      if (!empty($missing_doc)) {
                        echo $missing_doc;
                      }

                      // if(!empty($missing_doc)):
                      //     if(strlen($missing_doc['applicationForm']) == 0):
                      //       echo "<span class='label label-danger'> *. Application Form</span> ";
                      //     endif;
                      //     if(strlen($missing_doc['agreement']) == 0):
                      //       echo "<span class='label label-danger'> *. Agreement Document</span> ";
                      //     endif;
                      //     if(strlen($missing_doc['crbDbs']) == 0):
                      //       echo "<span class='label label-danger'> *. DBS Document</span> ";
                      //     endif;
                      //     if(strlen($missing_doc['ni']) == 0):
                      //       echo "<span class='label label-danger'> *. National Insurance Document</span> ";
                      //     endif;
                      //     if(strlen($missing_doc['identityDocument']) == 0):
                      //       echo "<span class='label label-danger'> *. Identity Document</span> ";
                      //     endif;
                      //     if(strlen($missing_doc['acNo']) == 0):
                      //       echo "<span class='label label-danger'> *. Bank Details</span> ";
                      //     endif;
                      // else:
                      // endif;
                      // print_r($missing_doc);

                      ?></td>
                </tr>
                <tr class="div_actions" style="display:none">
                  <td colspan="9" style="padding: 0px !important;">
                    <?php if ($tp == "tr") { ?>
                      <?php if ($_SESSION["prv"] == "Management") { ?>
                        <a title="Restore Interpreter" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-green w3-border w3-border-blue" onClick="popupwindow('trash_restore.php?del_id=<?php echo $row["id"]; ?>&table=<?php echo $table; ?>','Restore record interpreter',520,350)"><i class="fa fa-refresh"></i></a>

                        <a title="Delete Interpreter" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-red w3-border w3-border-black" onClick="popupwindow('del.php?del_id=<?php echo $row["id"]; ?>&table=<?php echo $table; ?>','Delete record interpreter',520,350)"><i class="fa fa-trash"></i></a>
                      <?php
                      } ?>
                    <?php
                    } else { ?>
                      <a title="View Details" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" onclick="popupwindow('full_view_interpreter.php?view_id=<?php echo $row["id"]; ?>', 'View profile of interpreter', 1100, 900);"><i class="fa fa-eye"></i></a>
                      <?php
                      if ($row["is_temp"] == 1 && $_SESSION["Temp"] == 0) { ?>
                        <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-yellow w3-border w3-border-blue" title="Confirm This Account First" onClick="popupwindow('confirm_record.php?id=<?php echo $row["id"]; ?>&table=<?php echo $table; ?>', 'Confirm interpreter account', 520,350);">
                          <i class="fa fa-check-circle"></i></a>
                      <?php
                      }
                      if ($acttObj->read_specific("count(*) as counter", "int_references", "int_id=" . $row["id"])["counter"] > 0) { ?>
                        <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-green w3-border w3-border-blue" title="View references records" onClick="view_reference(<?php echo $row["id"]; ?>);">
                          <i class="fa fa-search"></i></a>
                      <?php
                      }
                      ?>
                      <a title="Edit" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" onClick="popupwindow('interp_reg_edit.php?edit_id=<?php echo $row["id"]; ?>', 'Edit interpreter record', 1100, 910);"><i class="fa fa-pencil"></i></a>
                      <?php if ($row["is_temp"] == 0) { ?>
                        <a title="Week Schedule" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" onClick="MM_openBrWindow('interp_reg_schedul.php?edit_id=<?php echo $row["id"]; ?>&name=<?php echo $row["name"]; ?>&table=<?php echo $table; ?>','Attendance schedule interpreter','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')">
                          <i class="fa fa-briefcase"></i>
                        </a>
                      <?php
                      } ?>
                      <?php if ($_SESSION["prv"] == "Management" || $_SESSION["prv"] == "Finance") { ?>
                        <a title="Trash Record" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" onclick="popupwindow('del_trash.php?del_id=<?php echo $row["id"]; ?>&table=<?php echo $table; ?>','Delete interpreter record', 500,350);"><i class="fa fa-trash"></i></a>
                      <?php
                      } ?>
                      <?php if ($row["is_temp"] == 0) {
                        if ($_SESSION["prv"] == "Management" || $_SESSION["prv"] == "Finance") { ?>
                          <!--<a title="Salary Slip" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-green" onClick="MM_openBrWindow('salary_query.php?salary_id=<?php echo $row["id"]; ?>','Salary interpreter','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><i class="fa fa-file-powerpoint-o"></i></a>-->

                          <a title="Paid Salaries Record" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" href="reg_interp_salary_list.php?interp=<?php echo $row["id"]; ?>"><i class="fa fa-money"></i></a>

                          <!--<a title="Undo Salary" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-red" onClick="MM_openBrWindow('salary_query_paid.php?salary_id=<?php echo $row["id"]; ?>','Paid salaries interpreter','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><i class="fa fa-undo"></i></a>-->

                          <?php
                          if ($_SESSION["prv"] == "Operator" && $row["active"] == 0) { ?>
                            <a href="reg_interp_list.php?active=<?php echo $row["active"]; ?>&&act_id=<?php echo $row["id"]; ?>" title="Status">
                              <?php
                              $status = $row["active"];
                              if ($status == 0) {
                                echo '<span title="Click for De-activate" style=" color:green;background: green;width: 15px;height: 20px;-moz-border-radius: 50px; -webkit-border-radius: 50px;border-radius: 50px;">On</span>';
                              } else {
                                echo '<span title="Click for Activate" style=" color:red;background: red;width: 15px;height: 20px;-moz-border-radius: 50px;	-webkit-border-radius: 50px;border-radius: 50px;">On</span>';
                              }
                              ?>
                            </a>
                          <?php
                          }
                          if ($_SESSION["prv"] == "Management" || $_SESSION["userId"] == 41) { ?>
                            <a href="javascript:void(0)" title="Change Interpreter's Password" class="w3-button w3-small w3-circle w3-white w3-border w3-border-green" onClick="MM_openBrWindow('change_pswrd_interp.php?ref_frn_key=<?php echo $row["id"]; ?>&name=<?php echo $row["name"]; ?>','Update password interpreter','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')">
                              <i class="fa fa-lock"></i>
                            </a>
                            <a data-toggle="tooltip" data-placement="top" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" title="Edited List" onClick="popupwindow('reg_interp_list_edited.php?view_id=<?php echo $row["id"]; ?>','Edited history interpreter',900,800)">
                              <i class="fa fa-list-alt"></i></a>
                        <?php
                          }
                        } ?>

                        <a title="Assessment" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-yellow" onClick="MM_openBrWindow('interp_assessment.php?edit_id=<?php echo $row["id"]; ?>&code_qs=<?php echo $row["code"]; ?>&name=<?php echo $row["name"]; ?>','Interpreter assessment','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><i class="fa fa-star"></i></a>

                        <a title="Language Assessment" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue" onClick="MM_openBrWindow('lang_update.php?interpreter_id=<?php echo $row["id"]; ?>&name=<?php echo $row["name"]; ?>','Update languages interpreter','scrollbars=yes,resizable=yes,width=950,height=800,left=450,top=50')"><i class="fa fa-language"></i></a>

                        <a title="Blacklist" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-red w3-border w3-border-black" onclick="popupwindow('interp_reg_blacklist.php?edit_id=<?php echo $row["id"]; ?>&code_qs=<?php echo $row["code"]; ?>&name=<?php echo $row["name"]; ?>','Blacklist interpreter',820,550)""><i class=" fa fa-ban"></i></a>
                    <?php
                      }
                    } ?>
                  </td>
                </tr>
              <?php
              }
              ?>
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
  <script src="js/jquery-1.11.3.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
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
  </script>
</body>

</html>