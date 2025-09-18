<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'class.php';
include 'db.php';
$allowed_type_idz = "186";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Interpreter Documents</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>View Interpreter Document</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<?php
$col = $_GET['col'];
$v_id = $_GET['v_id'];
$col_file = $_GET['col'] . '_file';
$col_no = $_GET['col'] . '_no';
$col_issue_date = $_GET['col'] . '_issue_date';
$col_expiry_date = $_GET['col'] . '_expiry_date';
$text = $_GET['text'];
$name = $name = $acttObj->unique_data('interpreter_reg', 'name', 'id', $v_id);
if ($col == 'applicationForm' || $col == 'agreement' || $col == 'dps' || $col == 'int_qualification' || $col == 'anyOther') {
    if ($col == 'anyOther') {
        $col_file = 'anyOther';
    }
    if ($col == 'dps') {
        $col_file = 'dps';
    }
    if ($col == 'int_qualification') {
        $col_file = 'int_qualification';
    }
    $row_t = $acttObj->read_specific("$col_file", "interpreter_reg", "id=" . $v_id);
    $url_t = "file_folder/$col/" . $row_t[$col_file];
} else if ($col == 'nin') {
    $row_t = $acttObj->read_specific("ni,nin", "interpreter_reg", "id=" . $v_id);
    $url_t = "file_folder/nin/" . $row_t['nin'];
} else {
    if ($col == "work_evid") {
        $row_t = $acttObj->read_specific("$col_file,$col_issue_date,$col_expiry_date", "interpreter_reg", "id=" . $v_id);
    } else {
        $row_t = $acttObj->read_specific("$col_file,$col_no,$col_issue_date,$col_expiry_date", "interpreter_reg", "id=" . $v_id);
    }
    $url_t = "file_folder/issue_expiry_docs/" . $row_t[$col_file];
}
?>

<body>
    <div align="center" class="col-md-12"><br>
        <h1><span class="label label-primary"><?php echo $text; ?></span> for : <?php echo ucwords($name); ?></span></h1><br />
        <ul class="list-group col-sm-6 col-sm-offset-3">
            <style>
                .a {
                    color: #000;
                }
            </style>
            <?php if ($col == 'applicationForm' || $col == 'agreement' || $col == 'dps' || $col == 'int_qualification' || $col == 'anyOther') { ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo $text; ?>
                </li>
            <?php } else if ($col == 'nin') { ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    National Insurance Number
                    <h3 style="display: inline;"><span class="label a pull-right"><?php echo $row_t['ni']; ?></span></h3>
                </li>
                <?php } else {
                if ($col != "work_evid") { ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Number
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $row_t[$col_no]; ?></span></h3>
                    </li>
                <?php } ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Issue Date
                    <h3 style="display: inline;"><span class="label a pull-right"><?php echo $row_t[$col_issue_date]; ?></span></h3>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Expiry Date
                    <h3 style="display: inline;"><span class="label a pull-right"><?php echo $row_t[$col_expiry_date]; ?></span></h3>
                </li>
            <?php } ?>
        </ul>
        <?php
        if (!empty(strpos($url_t, "default.png"))) {
            echo '<div class="col-md-12">
            <center><h3><i>This document was previously uploaded as Hard Copy or Somewhere else.<br>Document Uploaded Copy was skipped at LSUK System!</i></h3></center>
        </div>';
        } else {
            $extensions = array("jpg", "jpeg", "png", "bmp", "webp");
            if (in_array(strtolower(end(explode(".", $url_t))), $extensions)) { ?>
                <div class="col-md-12">
                    <a href="<?php echo $url_t; ?>" target="_blank" title="Click to full view">
                        <div class="col-md-6" style="background: url(<?php echo $url_t; ?>);background-size: cover;background-repeat: no-repeat;height: 80%;width: 75%;"></div>
                    </a>
                </div>
                <br><br><br>
            <?php } else { ?>
                <iframe src="<?php echo $url_t; ?>" frameborder='2' width="100%" height="100%"></iframe>
        <?php }
        } ?>
    </div>
</body>

</html>