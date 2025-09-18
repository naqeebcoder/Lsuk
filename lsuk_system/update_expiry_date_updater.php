<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'class.php';
include 'db.php';
$allowed_type_idz = "187";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Edit Interpreter Documents</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
?>
<html xmlns="httdp://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Update Expired Document</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript">
        function MM_openBrWindow(theURL, winName, features) {
            window.open(theURL, winName, features);
        }

        function refreshParent() {
            window.opener.location.reload();
        }
    </script>
</head>

<body>
    <?php
    $table = 'interpreter_reg';
    $view_id = @$_GET['edit_id'];
    $col = @$_GET['col'];
    $text = @$_GET['text'];
    $query = "SELECT * FROM $table where id=$view_id";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $name = $row['name'];
    $email = $row['email'];
    $ni_number_db = $row['ni'];
    $ni_file_db = $row['nin'];
    $col_file = $row[$col . '_file'];
    $col_no = $row[$col . '_no'];
    $col_issue = $row[$col . '_issue_date'];
    $col_expiry = $row[$col . '_expiry_date'];

    if (isset($_POST['yes'])) {
        if (!empty($_POST['document_number']) && !empty($_POST['issue_date']) && !empty($_POST['expiry_date']) || ($col == "work_evid" && !empty($_POST['issue_date']) && !empty($_POST['expiry_date']))) {
            //Column names
            $col_file_name = $col . '_file';
            $col_no_name = $col . '_no';
            $col_issue_name = $col . '_issue_date';
            $col_expiry_name = $col . '_expiry_date';
            //column names ends
            if ($col != "work_evid") {
                $document_number = $_POST['document_number'];
                $acttObj->editFun($table, $view_id, $col_no_name, $document_number);
                $append_name = "";
            } else {
                $append_name = "wef";
            }
            $issue_date = $_POST['issue_date'];
            $acttObj->editFun($table, $view_id, $col_issue_name, $issue_date);
            $expiry_date = $_POST['expiry_date'];
            $acttObj->editFun($table, $view_id, $col_expiry_name, $expiry_date);
            if (isset($_POST['skipper']) && $_POST['skipper'] == "1") {
                if ($col_file != '' && $col_file != 'default.png') {
                    unlink("file_folder/issue_expiry_docs/" . $col_file);
                }
                $acttObj->editFun($table, $view_id, $col_file_name, "default.png");
                if ($col == "dbs" || $col == "id_doc") {
                    if ($col == "dbs") {
                        $col_file_name = "crbDbs";
                    }
                    if ($col == "id_doc") {
                        $col_file_name = "identityDocument";
                    }
                    $acttObj->editFun($table, $view_id, $col_file_name, "Hard Copy");
                }
            } else {
                if ($_FILES["document"]["name"] != NULL) {
                    error_reporting(0);
                    if ($col_file == '') {
                        $picName = $acttObj->upload_file("issue_expiry_docs", $_FILES["document"]["name"], $_FILES["document"]["type"], $_FILES["document"]["tmp_name"], $append_name . round(microtime(true)));
                    } else {
                        if ($col_file != 'default.png') {
                            unlink("file_folder/issue_expiry_docs/" . $col_file);
                        }
                        $picName = $acttObj->upload_file("issue_expiry_docs", $_FILES["document"]["name"], $_FILES["document"]["type"], $_FILES["document"]["tmp_name"], $append_name . round(microtime(true)));
                    }
                    $acttObj->editFun($table, $view_id, $col_file_name, $picName);
                    if ($col == "dbs" || $col == "id_doc") {
                        if ($col == "dbs") {
                            $col_file_name = "crbDbs";
                        }
                        if ($col == "id_doc") {
                            $col_file_name = "identityDocument";
                        }
                        $acttObj->editFun($table, $view_id, $col_file_name, "Soft Copy");
                    }
                    echo "<script>window.onunload = refreshParent;</script>";
                }
            }
            $msg = '<div class="w3-panel w3-green w3-display-container">
          <span onclick="this.parentElement.style.display=`none`"
          class="w3-button w3-large w3-display-topright">&times;</span>
          <h3>Success!</h3>
          <p>This record has been successfully updated.</p>
        </div>';
            echo "<script>window.onunload = refreshParent;</script>";
        } else {
            $msg = '<div class="w3-panel w3-orange w3-display-container">
          <span onclick="this.parentElement.style.display=`none`"
          class="w3-button w3-large w3-display-topright">&times;</span>
          <h3>Note!</h3>
          <p>Kindly Fill all the Fields.</p>
        </div>';
        }
    }
    //Update NI Number
    if (isset($_POST['btn_ni'])) {
        if (!empty($_POST['ni_number'])) {
            $ni_number = $_POST['ni_number'];
            $ni_file = $_POST['ni_file'];
            $acttObj->editFun($table, $view_id, 'ni', $ni_number);
            if (isset($_POST['skipper']) && $_POST['skipper'] == "1") {
                if ($ni_file_db != '' && $ni_file_db != 'default.png') {
                    unlink("file_folder/nin/" . $ni_file_db);
                }
                $acttObj->editFun($table, $view_id, 'nin', "default.png");
            } else {
                if ($_FILES["ni_file"]["name"] != NULL) {
                    error_reporting(0);
                    if ($ni_file_db == '') {
                        $picName = $acttObj->upload_file("nin", $_FILES["ni_file"]["name"], $_FILES["ni_file"]["type"], $_FILES["ni_file"]["tmp_name"], round(microtime(true)));
                        $acttObj->editFun($table, $view_id, 'nin', $picName);
                    } else {
                        if ($ni_file_db != 'default.png') {
                            unlink("file_folder/nin/" . $ni_file_db);
                        }
                        $picName = $acttObj->upload_file("nin", $_FILES["ni_file"]["name"], $_FILES["ni_file"]["type"], $_FILES["ni_file"]["tmp_name"], round(microtime(true)));
                        $acttObj->editFun($table, $view_id, 'nin', $picName);
                    }
                    echo "<script>window.onunload = refreshParent;</script>";
                }
            }
            $msg = '<div class="w3-panel w3-green w3-display-container">
          <span onclick="this.parentElement.style.display=`none`"
          class="w3-button w3-large w3-display-topright">&times;</span>
          <h3>Success!</h3>
          <p>This record has been successfully updated.</p>
        </div>';
            echo "<script>window.onunload = refreshParent;</script>";
        } else {
            $msg = '<div class="w3-panel w3-orange w3-display-container">
          <span onclick="this.parentElement.style.display=`none`"
          class="w3-button w3-large w3-display-topright">&times;</span>
          <h3>Note!</h3>
          <p>Kindly Fill all the Fields.</p>
        </div>';
        }
    }
    //Update Bank Details
    if (isset($_POST['btn_bank'])) {
        if (!empty($_POST['acNo'])) {
            $acttObj->update($table, array('bnakName' => $_POST['bnakName'], 'acName' => $_POST['acName'], 'acntCode' => $_POST['acntCode'], 'acNo' => $_POST['acNo']), array('id' => $view_id));
            $msg = '<div class="w3-panel w3-green w3-display-container">
          <span onclick="this.parentElement.style.display=`none`"
          class="w3-button w3-large w3-display-topright">&times;</span>
          <h3>Success!</h3>
          <p>This record has been successfully updated.</p>
        </div>';
            echo "<script>window.onunload = refreshParent;</script>";
        } else {
            $msg = '<div class="w3-panel w3-orange w3-display-container">
          <span onclick="this.parentElement.style.display=`none`"
          class="w3-button w3-large w3-display-topright">&times;</span>
          <h3>Note!</h3>
          <p>Kindly Fill all the Fields.</p>
        </div>';
        }
    }
    if (isset($_POST['btn_specific_yes'])) {
        $col_file_name = $col . '_file';
        if ($col == 'dps') {
            $col_file_name = 'dps';
            $col_file = $row['dps'];
        }
        if ($col == 'anyOther') {
            $col_file_name = 'anyOther';
            $col_file = $row['anyOther'];
        }
        if ($col == 'int_qualification') {
            $col_file_name = 'int_qualification';
            $col_file = $row['int_qualification'];
        }
        if (isset($_POST['skipper']) && $_POST['skipper'] == "1") {
            if ($col_file != '' && $col_file != 'default.png') {
                unlink("file_folder/$col/" . $col_file);
            }
            $acttObj->editFun($table, $view_id, $col_file_name, "default.png");
            if ($col == "applicationForm" || $col == "agreement") {
                $col_file_name = $col;
                $acttObj->editFun($table, $view_id, $col_file_name, "Hard Copy");
            }
            $msg = '<div class="w3-panel w3-green w3-display-container">
        <span onclick="this.parentElement.style.display=`none`"
        class="w3-button w3-large w3-display-topright">&times;</span>
        <h3>Success!</h3>
        <p>This record has been successfully updated.</p></div>';
            echo "<script>window.onunload = refreshParent;</script>";
        } else {
            if ($_FILES["document"]["name"] != NULL) {
                error_reporting(0);
                if ($col_file == '') {
                    $picName = $acttObj->upload_file("$col", $_FILES["document"]["name"], $_FILES["document"]["type"], $_FILES["document"]["tmp_name"], round(microtime(true)));
                } else {
                    if ($col_file != 'default.png') {
                        unlink("file_folder/$col/" . $col_file);
                    }
                    $picName = $acttObj->upload_file("$col", $_FILES["document"]["name"], $_FILES["document"]["type"], $_FILES["document"]["tmp_name"], round(microtime(true)));
                }
                $acttObj->editFun($table, $view_id, $col_file_name, $picName);
                if ($col == "applicationForm" || $col == "agreement") {
                    $col_file_name = $col;
                    $acttObj->editFun($table, $view_id, $col_file_name, "Soft Copy");
                }
                $msg = '<div class="w3-panel w3-green w3-display-container">
          <span onclick="this.parentElement.style.display=`none`"
          class="w3-button w3-large w3-display-topright">&times;</span>
          <h3>Success!</h3>
          <p>This record has been successfully updated.</p></div>';
                echo "<script>window.onunload = refreshParent;</script>";
            } else {
                $msg = '<div class="w3-panel w3-orange w3-display-container">
          <span onclick="this.parentElement.style.display=`none`"
          class="w3-button w3-large w3-display-topright">&times;</span>
          <h3>Note!</h3>
          <p>Kindly select a file first.</p></div>';
            }
        }
    }
    ?>

    <div align="center"><br>
        <span style="font-weight:bold; color:#09F;">Update <b><?php echo $text; ?></b> for : <?php echo ucwords($name); ?></span><br /><br />
        <?php if (isset($msg) && !empty($msg)) {
            echo $msg;
        } ?>
        <div class="w3-container w3-col m12">
            <?php if ($col == 'applicationForm' || $col == 'agreement' || $col == 'dps' || $col == 'int_qualification' || $col == 'anyOther') {
                if ($col == 'anyOther') {
                    $col_file = $row['anyOther'];
                }
                if ($col == 'dps') {
                    $col_file = $row['dps'];
                }
                if ($col == 'int_qualification') {
                    $col_file = $row['int_qualification'];
                } ?>
                <form action="#" method="post" enctype="multipart/form-data">
                    <table width="50%" border="1" class="w3-table-all w3-card-4">
                        <tr>
                            <td>Upload <?php echo $text; ?> (Scaned / Picture)</td>
                        </tr>
                        <tr>
                            <td><label for="skipper" class="text-danger"><input onchange="skip_document(this)" id="skipper" name="skipper" type="checkbox" value="1" /> Skip (Already uploaded)</label></td>
                        </tr>
                        <tr>
                            <td><input name="document" type="file" <?php if ($col_file == '') { ?>required <?php } ?> placeholder='' id="document" />
                                <?php if ($col_file != '') { ?>
                                    <label class="w3-text-black pull-right">
                                        <?php if ($col_file != '') {
                                            echo $col_file;
                                        } ?> <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $view_id; ?>&col=<?php echo $col; ?>&text=<?php echo $text; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                            <span class="w3-badge w3-large w3-padding w3-blue"><i class="fa fa-eye" title="View <?php echo $text; ?>"></i></span></a>
                                    </label><?php } else { ?>
                                    <label class="w3-text-red w3-small"><b><?php echo $text; ?> not uploaded!</label></b><?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="w3-text-black"><b>Are you sure you want to update this record ?</b></label>
                                <input type="submit" name="btn_specific_yes" class="w3-btn w3-blue" value="Yes ❯" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            <?php } else if ($col == 'nin') {
                $col_file = 'nin'; ?>
                <form action="#" method="post" enctype="multipart/form-data">
                    <table width="50%" border="1" class="w3-table-all w3-card-4">
                        <tr>
                            <td>Upload <?php echo $text; ?> (Scaned / Picture)</td>
                        </tr>
                        <tr>
                            <td><label for="skipper" class="text-danger"><input onchange="skip_document(this)" id="skipper" name="skipper" type="checkbox" value="1" /> Skip (Already uploaded)</label></td>
                        </tr>
                        <tr>
                            <td><input name="ni_file" type="file" <?php if ($ni_file_db == '') { ?>required <?php } ?> placeholder='' id="document" />
                                <?php if ($ni_file_db != '') { ?>
                                    <label class="w3-text-black pull-right">
                                        <?php if ($ni_file_db != '') {
                                            echo 'NI No #';
                                        } ?> <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $view_id; ?>&col=<?php echo $col; ?>&text=<?php echo $text; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                            <span class="w3-badge w3-large w3-padding w3-blue"><i class="fa fa-eye" title="View <?php echo $text; ?>"></i></span></a>
                                    </label><?php } else { ?>
                                    <label class="w3-text-red w3-small"><b><?php echo $text; ?> is not uploaded!</label></b><?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td><input class="w3-input w3-border" required placeholder="Enter <?php echo $text; ?> Number" value="<?php echo $ni_number_db; ?>" type="text" name="ni_number"></td>
                        </tr>
                        <tr>
                            <td>
                                <label class="w3-text-black"><b>Are you sure you want to update this record ?</b></label>
                                <input type="submit" name="btn_ni" class="w3-btn w3-blue" value="Yes ❯" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            <?php } else if ($col == 'acNo') { ?>
                <form action="#" method="post" enctype="multipart/form-data">
                    <table width="50%" border="1" class="w3-table-all w3-card-4">
                        <tr>
                            <td><input class="w3-input w3-border" required placeholder="Bank Name" value="<?php echo $row['bnakName']; ?>" type="text" name="bnakName"></td>
                        </tr>
                        <tr>
                            <td><input class="w3-input w3-border" required placeholder="Account Title" value="<?php echo $row['acName']; ?>" type="text" name="acName"></td>
                        </tr>
                        <tr>
                            <td><input class="w3-input w3-border" required placeholder="Sort Code" value="<?php echo $row['acntCode']; ?>" type="text" name="acntCode"></td>
                        </tr>
                        <tr>
                            <td><input class="w3-input w3-border" required placeholder="Account Number" value="<?php echo $row['acNo']; ?>" type="text" name="acNo"></td>
                        </tr>
                        <tr>
                            <td>
                                <label class="w3-text-black"><b>Are you sure you want to update this record ?</b></label>
                                <input type="submit" name="btn_bank" class="w3-btn w3-blue" value="Yes ❯" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            <?php } else { ?>
                <form action="#" method="post" enctype="multipart/form-data">
                    <table width="50%" border="1" class="w3-table-all w3-card-4">
                        <tr>
                            <td>Upload <?php echo $text; ?> (Scaned / Picture)</td>
                        </tr>
                        <tr>
                            <td><label for="skipper" class="text-danger"><input onchange="skip_document(this)" id="skipper" name="skipper" type="checkbox" value="1" /> Skip (Already uploaded)</label></td>
                        </tr>
                        <tr>
                            <td><input name="document" type="file" <?php if ($col_file == '') { ?>required <?php } ?> placeholder='' id="document" />
                                <?php if ($col_file != '') { ?>
                                    <label class="w3-text-black pull-right">
                                        <?php if ($col_file != '') {
                                            echo $col_file;
                                        } ?> <a href="javascript:void(0)" onClick="MM_openBrWindow('doc_view.php?v_id=<?php echo $view_id; ?>&col=<?php echo $col; ?>&text=<?php echo $text; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
                                            <span class="w3-badge w3-large w3-padding w3-blue"><i class="fa fa-eye" title="View <?php echo $text; ?>"></i></span></a>
                                    </label><?php } else { ?>
                                    <label class="w3-text-red w3-small"><b><?php echo $text; ?> is not uploaded!</label></b><?php } ?>
                            </td>
                        </tr>
                        <?php if ($col != "work_evid") { ?>
                            <tr>
                                <td><input class="w3-input w3-border" required placeholder="Enter <?php echo $text; ?> Number" value="<?php echo $col_no; ?>" type="text" name="document_number"></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td><input class="w3-input w3-border" required type="date" name="issue_date" value="<?php echo $col_issue == '1001-01-01' ? '' : $col_issue; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><input class="w3-input w3-border" required type="date" name="expiry_date" value="<?php echo $col_expiry == '1001-01-01' ? '' : $col_expiry; ?>"></td>
                        </tr>
                        <tr>
                            <td>
                                <label class="w3-text-black"><b>Are you sure you want to update this record ?</b></label>
                                <input type="submit" name="yes" class="w3-btn w3-blue" value="Yes ❯" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            <?php } ?>
        </div>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    function skip_document(element) {
        if ($(element).is(":checked")) {
            $('#document').removeAttr('required');
        } else {
            $('#document').attr('required', 'required');
        }
    }
</script>

</html>