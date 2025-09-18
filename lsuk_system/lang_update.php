<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
$allowed_type_idz = "57";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Language Assessment</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$table = 'lang_update';
$interpreter_id = $_GET['interpreter_id'];
$name = $_GET['name'];
if (isset($_POST['submit'])) {
    $edit_id = $acttObj->get_id($table);
    $acttObj->editFun($table, $edit_id, 'interpreter_id', $interpreter_id);
    $assess_lang = $_POST['assess_lang'];
    $acttObj->editFun($table, $edit_id, 'assess_lang', $assess_lang);
    $assess_by = $_POST['assess_by'];
    $acttObj->editFun($table, $edit_id, 'assess_by', $assess_by);
    $assessor_type = $_POST['assessor_type'];
    $acttObj->editFun($table, $edit_id, 'assessor_type', $assessor_type);
    $assess_date = $_POST['assess_date'];
    $acttObj->editFun($table, $edit_id, 'assess_date', $assess_date);
    $status = $_POST['status'];
    $acttObj->editFun($table, $edit_id, 'status', $status);
    $lang_level = $_POST['lang_level'];
    $acttObj->editFun($table, $edit_id, 'lang_level', $lang_level);
    if ($status == 1) {
        if ($lang_level < 3) {
            $check_id = $acttObj->read_specific("id", "interp_lang", "code='id-" . $interpreter_id . "' AND lang='" . $assess_lang . "' AND level<3")['id'];
            if (empty($check_id)) {
                $acttObj->insert("interp_lang", array("lang" => $assess_lang, "code" => "id-" . $interpreter_id, "dated" => date('Y-m-d'), "level" => $lang_level));
            } else {
                $acttObj->update("interp_lang", array("dated" => date('Y-m-d'), "level" => $lang_level), array("id" => $check_id));
            }
        }
    }
    echo "<script>alert('Record successfully added.');</script>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Interpreter Language Assessment</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <?php include 'ajax_uniq_fun.php'; ?>
    <script type="text/javascript">
        function popupwindow(url, title, w, h) {
            var left = (screen.width / 2) - (w / 2);
            var top = (screen.height / 2) - (h / 2);
            return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
        }
    </script>
</head>

<body>
    <div class="container-fluid">
        <form action="" method="post" enctype="multipart/form-data" onsubmit="return formSubmit()">
            <div class="bg-info col-xs-12 form-group text-center">
                <h4>Language Assessment for <span class="label label-primary"><?php echo $name; ?></span></h4>
            </div>
            <div class="form-group col-md-3 col-sm-4">
                <label>Language Assessment for *</label>
                <select name="assess_lang" id="assess_lang" required='' class="form-control">
                    <?php $result_opt = $acttObj->read_all("DISTINCT lang", "lang", "lang NOT IN (SELECT lang from interp_lang WHERE code='id-$interpreter_id') ORDER BY lang ASC");
                    while ($row_opt = $result_opt->fetch_assoc()) {
                        echo "<option>" . $row_opt["lang"] . "</option>";
                    } ?>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-4">
                <label>Assessment Completed *</label>
                <select name="status" id="status" required='' class="form-control">
                    <option value="">--- Select status ---</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-4">
                <label>Assessment Date *</label>
                <input name="assess_date" type="date" class="form-control" required='' />
            </div>
            <div class="form-group col-md-3 col-sm-4">
                <label>Language Assessment By *</label>
                <input name="assess_by" type="text" class="form-control" required='' />
            </div>
            <div class="form-group col-md-3 col-sm-4">
                <label>Language Assessor Type *</label>
                <select name="assessor_type" id="assessor_type" required='' class="form-control">
                    <option value="1">Native Speaker</option>
                    <option value="2">Language Expert</option>
                    <option value="3">Qualified Assessor</option>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-4">
                <label>Language Speaking Level</label>
                <select name="lang_level" id="lang_level" required='' class="form-control">
                    <option value="1">Native</option>
                    <option value="2">Fluent</option>
                    <option value="3">Intermediate</option>
                    <option value="4">Basic</option>
                </select>
            </div>
            <div class="form-group col-md-3 col-sm-6">
                <button class="btn btn-primary" type="submit" name="submit" onclick="return formSubmit(); return false">Submit &raquo;</button>
            </div>
        </form>
        <div class="row">
            <div class="container-fluid">
                <div class="bg-info col-xs-12 form-group text-center">
                    <h4>Language Assessment Records</h4>
                </div>
                <table class="table table-bordered">
                    <?php $query = $acttObj->read_all("*", $table, "interpreter_id=" . $interpreter_id);
                    if ($query->num_rows > 0) { ?>
                        <thead class="bg-primary">
                            <tr>
                                <td>Language</td>
                                <td>Completed Date</td>
                                <td>Assessment By</td>
                                <td>Status</td>
                                <td>Level</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <?php $assessor_array = array("1" => "Native Speaker", "2" => "Language Expert", "3" => "Qualified Assessor");
                        $level_array = array("1" => "Native", "2" => "Fluent", "3" => "Intermediate", "4" => "Basic");
                        while ($row = $query->fetch_assoc()) { ?>
                            <tr>
                                <td align="left"><?php echo $row['assess_lang']; ?> </td>
                                <td align="left"><?php echo $misc->dated($row['assess_date']); ?> </td>
                                <td align="left"><?php echo $row['assess_by'] . ' (' . $assessor_array[$row['assessor_type']] . ')'; ?> </td>
                                <td align="left"><i><?php echo $row['status'] == 1 ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Not Completed</span>'; ?></i></td>
                                <td align="left"><?php echo $level_array[$row['lang_level']]; ?> </td>
                                <td align="left" width="50">
                                    <a title="Delete this record" href="javascript:void(0)" onClick='popupwindow("del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>","title", 600,400);'>
                                        <i class="fa fa-trash fa-2x text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php }
                    } else {
                        echo "<div class='col-xs-12'><center><br><br><h3 class='text-danger'>There are no language assessment records!</h3></center></div>";
                    } ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>