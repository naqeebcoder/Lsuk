<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'actions.php';
$table = 'interpreter';
$allowed_type_idz = "213";
$array_tables = array(1 => 'interpreter', 2 => 'telephone', 3 => 'translation');
$array_table_labels = array(1 => 'Face To Face', 2 => 'Telephone', 3 => 'Translation');
$table = $array_tables[$_GET['job_type']];
$job_id = @$_GET['job_id'];
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Update expenses</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$row = $obj->read_specific("$table.*,interpreter_reg.name,interpreter_reg.code", "$table,interpreter_reg", "$table.intrpName=interpreter_reg.id and $table.id=$job_id");
$row_lateness = $obj->read_specific("*", "job_late_minutes", "job_id=" . $row['id'] . " AND job_type=" . $_GET['job_type'] . " AND interpreter_id=" . $row['intrpName']);
if ($_GET['job_type'] == 1) {
    $assignment_type = $row['interp_cat'] == 12 ? $row['assignIssue'] : $obj->read_specific("ic_title", "interp_cat", "ic_id=" . $row['interp_cat'])['ic_title'];
}
if ($_GET['job_type'] == 2) {
    $assignment_type = $obj->read_specific("c_title", "comunic_types", "c_id=" . $row['comunic'])['c_title'];
}
if ($_GET['job_type'] == 3) {
    $assignment_type = $obj->read_specific("tc_title", "trans_cat", "tc_id=" . $row['docType'])['tc_title'];
    $row['assignDate'] = $row['asignDate'];
} else {
    $db_assignDur = $row['assignDur'];
    if ($db_assignDur > 60) {
        $hours = $db_assignDur / 60;
        if (floor($hours) > 1) {
            $hr = "hours";
        } else {
            $hr = "hour";
        }
        $mins = $db_assignDur % 60;
        if ($mins == 00) {
            $assignment_duration = sprintf("%2d $hr", $hours);
        } else {
            $assignment_duration = sprintf("%2d $hr %02d minutes", $hours, $mins);
        }
    } else if ($db_assignDur == 60) {
        $assignment_duration = "1 Hour";
    } else {
        $assignment_duration = $db_assignDur . " minutes";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Interpreter Lateness - <?= $array_table_labels[$_GET['job_type']] ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css" rel="stylesheet" type='text/css'>
    <style>
        .cls_danger {
            background: red;
            border: 2px solid black;
            color: white;
            font-weight: bold;
        }

        .nav-tabs>li.active>a,
        .nav-tabs>li.active>a:focus,
        .nav-tabs>li.active>a:hover {
            color: #fff;
            background-color: #337ab7;
            font-weight: bold;
            border: 1px solid #000;
        }

        .nav-tabs>li>a {
            border: 1px solid #c5b7b7;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>

<body class="container-fluid">
    <table class="table table-bordered">
        <tr>
            <th>Job ID #</th>
            <td><?= $row['id'] ?></td>
        </tr>
        <tr>
            <th><?= $_GET['job_type'] == 3 ? 'Document' : 'Assignment' ?> Type</th>
            <td><?= $assignment_type ?></td>
        </tr>
        <tr>
            <th>Assignment Date Time</th>
            <td><?= $row['assignDate'] ? $misc->dated($row['assignDate']) . ' ' . $row['assignTime'] : "---" ?></td>
        </tr>
        <?php if ($_GET['job_type'] != 3) { ?>
            <tr>
                <th>Assignment Duration</th>
                <td><?= $assignment_duration ?></td>
            </tr>
        <?php }
        if ($_GET['job_type'] == 1) { ?>
            <tr>
                <th>Assignment PostCode</th>
                <td><?= $row['postCode'] ?: "Nil" ?></td>
            </tr>
        <?php } ?>
    </table>
    <div class="tab-content">
        <div class="tab-pane fade in active">
            <div class="col-md-12">
                <?php if ($_SESSION['returned_message']) {
                    echo $_SESSION['returned_message'];
                    unset($_SESSION['returned_message']);
                } ?>
                <form action="process/interpreter_lateness.php" method="POST">
                    <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
                    <input type="hidden" name="job_id" value="<?= $job_id ?>" readonly />
                    <input type="hidden" name="job_type" value="<?= $_GET['job_type'] ?>" readonly />
                    <input type="hidden" name="interpreter_id" value="<?= $row['intrpName'] ?>" readonly />
                    <input type="hidden" name="lateness_id" value="<?= $row_lateness['id'] ?>" readonly />
                    <div class="col-xs-12 text-center">
                        <h4><?= $array_table_labels[$_GET['job_type']] ?> - Update Interpreter Lateness For <span style="color:#F00;"><?php echo $row['name'] . ' ( ' . $misc->dated($row['assignDate']) . ' )'; ?></span></h4>
                    </div>
                    <div class="col-xs-12 form-group">
                        <div class="panel-group">
                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <button data-toggle="collapse" href="#collapse_lateness"><b>Lateness Record</b></button><?= $row_lateness['id'] ? ' <label class="btn btn-sm btn-warning pull-right" style="margin-top: -7px;" for="remove_lateness"><input type="checkbox" name="remove_lateness" id="remove_lateness" value="1"> Remove Lateness Record</label>' : '' ?>
                                    </h4>
                                </div>
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td width="13%">
                                                <label>Late Minutes :</label>
                                                <input placeholder="Minutes ..." type="number" class="form-control" name="lateness_minutes" required value="<?= $row_lateness['minutes'] ?: '' ?>" />
                                            </td>
                                            <td width="34%">
                                                <label>How did you get reason of lateness :</label>
                                                <select name="lateness_created_by" class="form-control" required>
                                                    <option <?= empty($row_lateness['created_by']) ? 'selected' : '' ?> value="">--- Select type of lateness reason ---</option>
                                                    <option <?= !is_null($row_lateness['created_by']) && $row_lateness['created_by'] == 0 ? 'selected' : '' ?> value="0">Client LSUK App</option>
                                                    <option <?= !is_null($row_lateness['created_by']) && $row_lateness['created_by'] == 1 ? 'selected' : '' ?> value="1">Interpreter informed LSUK about lateness</option>
                                                    <option <?= !is_null($row_lateness['created_by']) && $row_lateness['created_by'] == 2 ? 'selected' : '' ?> value="2">LSUK phoned interpreter for reason of lateness</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="form-group">
                                                    <label>Write reason of interpreter lateness :</label>
                                                    <textarea required placeholder="Write reason of interpreter lateness ..." rows="3" maxlength="255" class="form-control" name="lateness_reason"><?= $row_lateness['reason'] ?: '' ?></textarea>
                                                    <br>
                                                    <button type="submit" class="btn btn-primary" name="btn_submit_lateness" onclick="return confirm('Are you sure to update this lateness record?')">Submit</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>
</body>

</html>