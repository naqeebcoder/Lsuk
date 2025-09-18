<?php
//php mailer library
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Posts Management</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style>
        .multiselect {
            min-width: 250px;
        }

        .multiselect-container {
            max-height: 400px;
            overflow-y: auto;
            max-width: 380px;
        }
    </style>
</head>

<body>
    <?php include 'db.php';
    include 'class.php';
    include_once('function.php');
    $table = 'post_format';
    if (isset($_POST['btninsert'])) {
        $append = '';
        $get_idz = '';
        $selector = $_POST['selector'];
        $gender = $_POST['gender'];
        $typeBased = $_POST['typeBased'];
        $bookinRef = $_POST['bookinRef'];
        $em_type = $_POST['type'];
        $document_file = $_FILES['doc'];
        $selected_cities = implode(',', array_filter(array_map('trim', $_POST['selected_cities']), fn($city) => !empty($city)));
        $selected_langs = implode(',', array_filter(array_map('trim', $_POST['selected_langs']), fn($lang) => !empty($lang)));
        $format = mysqli_escape_string($con, $_POST['em_format']);
        $dated = date('Y-m-d');
        $by = $_SESSION['UserName'];
        if (!empty($document_file['name']) && !empty($document_file['tmp_name']))
            $documentName = $acttObj->upload_file_custom("post_formate", $document_file['name'], $document_file['type'], $document_file['tmp_name']);

        $queryupd = "INSERT INTO post_format (em_format, em_date, em_insertedby, em_type, gender, type, document, bookinRef, status, filter_cities, filter_languages) 
      VALUES ('$format', '$dated', '$by', '$em_type', '$gender', '$typeBased', '$documentName', '$bookinRef', 'Active', '$selected_cities', '$selected_langs')";
        if (mysqli_query($con, $queryupd)) {

            $insert_post_id = mysqli_insert_id($con);
            $get_mail_format = $acttObj->unique_data('email_format', 'em_format', 'id', '8');
            $query_emails = "SELECT DISTINCT ir.id, ir.email
            FROM interpreter_reg ir
            LEFT JOIN interp_lang il ON ir.code = il.code
            JOIN post_format pf ON 
                (TRIM(pf.gender) = 'Both' OR TRIM(pf.gender) = TRIM(ir.gender))
                AND (pf.bookinRef IS NOT NULL)
                AND (
                    (pf.filter_cities IS NULL OR pf.filter_cities = '') 
                    OR FIND_IN_SET(TRIM(ir.city), TRIM(pf.filter_cities)) > 0
                )
                AND (
                    (pf.filter_languages IS NULL OR pf.filter_languages = '') 
                    OR FIND_IN_SET(TRIM(il.lang), TRIM(pf.filter_languages)) > 0
                )
                AND (
                    (TRIM(pf.type) = 'interp' AND TRIM(ir.interp) = 'Yes') 
                    OR (TRIM(pf.type) = 'telep' AND TRIM(ir.telep) = 'Yes') 
                    OR (TRIM(pf.type) = 'trans' AND TRIM(ir.trans) = 'Yes') 
                    OR (TRIM(pf.type) = 'all' AND (
                        TRIM(ir.interp) = 'Yes' OR TRIM(ir.telep) = 'Yes' OR TRIM(ir.trans) = 'Yes'
                    ))
                )
                WHERE pf.id = $insert_post_id;
            ";
            $res_emails = mysqli_query($con, $query_emails);
            $from_email = "info@lsuk.org";
            $subject = $_POST['type'];
            $sub_title = 'LSUK has posted a new document. Please check LSUK messages at your profile.';
            $type_key = "nd";
            $app_int_ids = array();
            $message = $get_mail_format;
            while ($row_emails = mysqli_fetch_assoc($res_emails)) {
                $int_id = $row_emails['id'];
                //Send notification on APP
                $array_tokens = explode(',', $acttObj->read_specific("GROUP_CONCAT( DISTINCT token) as tokens", "int_tokens", "int_id=" . $int_id)['tokens']);
                if (!empty($array_tokens)) {
                    array_push($app_int_ids, $int_id);
                    foreach ($array_tokens as $token) {
                        if (!empty($token)) {
                            $acttObj->notify($token, "📋 " . $subject, $sub_title, array("type_key" => $type_key));
                        }
                    }
                }
                if ($append != '') {
                    $get_idz .= ',' . $int_id;
                }
                $check_id = $acttObj->read_specific('id', 'notify_new_doc', 'interpreter_id=' . $int_id)['id'];
                if (empty($check_id)) {
                    $acttObj->insert('notify_new_doc', array("interpreter_id" => $int_id, "status" => '0', "new_notification" => '0'));
                } else {
                    $existing_notification = $acttObj->read_specific("new_notification", "notify_new_doc", "interpreter_id=" . $int_id)['new_notification'];
                    $acttObj->update('notify_new_doc', array("status" => '0', "new_notification" => $existing_notification + 1), array("interpreter_id" => $int_id));
                }

                $acttObj->insert(
                    'cron_emails',
                    array(
                        // "order_id" => $edit_id,
                        // "order_type" => $order_type,
                        "user_id" => $int_id,
                        "send_from" => 'info@lsuk.org',
                        "send_password" => 'xtxwzcvtdbjpftdj',
                        "send_to" => $row_emails['email'],
                        "subject" => $subject,
                        "template_type" => 8,
                        "template_body" => mysqli_real_escape_string($con, $message),
                        "created_date" => date("Y-m-d H:i:s")
                    )
                );
            }
            //SAVE app notification
            $int_distinct_ids = implode(',', array_unique($app_int_ids));
            $acttObj->insert('app_notifications', array("title" => $subject, "sub_title" => $sub_title, "dated" => date('Y-m-d'), "int_ids" => $int_distinct_ids, "read_ids" => $int_distinct_ids, "type_key" => $type_key));
            if ($append != '') {
                $array_insert = array('post_id' => $insert_post_id, 'cities' => implode(",", $_POST['selected_cities']), 'languages' => implode(",", $_POST['selected_langs']), 'interpreters' => ltrim($get_idz, ","));
            } else {
                $array_insert = array('post_id' => $insert_post_id, 'cities' => '', 'languages' => '', 'interpreters' => '');
            }
            $acttObj->insert('notify_new_doc_data', $array_insert);
            echo '<script>alert("New post uploaded successfully !");</script>';
            echo '<script>window.location.href="post_format.php";</script>';
        } else {
            echo '<script>alert("Failed to upload new post !");</script>';
        }
    }
    if (isset($_POST['btnupdate'])) {
        $id = mysqli_escape_string($con, $_POST['id']);
        $type = mysqli_escape_string($con, $_POST['type']);
        $gender = mysqli_escape_string($con, $_POST['gender']);
        $typeBased = mysqli_escape_string($con, $_POST['typeBased']);
        $format = $_POST['em_format'];
        $bookinRef = mysqli_escape_string($con, $_POST['bookinRef']);
        $document_file = $_FILES['doc'];
        $dated = date('Y-m-d');
        $by = $_SESSION['UserName'];
        if (isset($_FILES['doc']) && $_FILES['doc']['error'] == 0) {
            $documentName = $acttObj->upload_file_custom("post_formate", $_FILES['doc']['name'], $_FILES['doc']['type'], $_FILES['doc']['tmp_name']);
            $queryupd = "UPDATE post_format SET 
                        em_format='$format',
                        em_date='$dated',
                        em_insertedby='$by',
                        em_type='$type',
                        gender='$gender',
                        type='$typeBased',
                        bookinRef='$bookinRef',
                        document='$documentName' 
                    WHERE id=$id";
        } else {
            $queryupd = "UPDATE post_format SET 
                        em_format='$format',
                        em_date='$dated',
                        em_insertedby='$by',
                        em_type='$type',
                        gender='$gender',
                        type='$typeBased',
                        bookinRef='$bookinRef' 
                    WHERE id=$id";
        }
        if (mysqli_query($con, $queryupd)) {
            echo '<script>alert("Post Successfully updated !");</script>';
            echo '<script>window.location.href="post_format.php";</script>';
        } else {
            echo '<script>alert("Failed to update this Post !");</script>';
        }
    }
    if (isset($_GET['activate_id']) && !empty($_GET['activate_id'])) {
        $id = mysqli_escape_string($con, $_GET['activate_id']);
        $by = $_SESSION['UserName'];
        $dated = date('Y-m-d');
        $queryupd = "update post_format set status='Active',em_insertedby='$by',em_date='$dated' where id=" . $id;
        if (mysqli_query($con, $queryupd)) {
            echo '<script>alert("Post Successfully Activated !");
      window.location.href="post_format.php";</script>';
        } else {
            echo '<script>alert("Failed to activate this Post !");</script>';
        }
    }
    if (isset($_GET['del_id']) && !empty($_GET['del_id'])) {
        $id = mysqli_escape_string($con, $_GET['del_id']);
        $by = $_SESSION['UserName'];
        $dated = date('Y-m-d');
        $queryupd = "update post_format set status='Not Active',em_insertedby='$by',em_date='$dated' where id=" . $id;
        if (mysqli_query($con, $queryupd)) {
            echo '<script>alert("Post Successfully De-activated !");
      window.location.href="post_format.php";</script>';
        } else {
            echo '<script>alert("Failed to De-activate this Post !");</script>';
        }
    }

    include 'nav2.php';
    ?>
    <!-- end of sidebar -->
    <section class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <center><a href="post_format.php" style="padding: 12px;" class="alert-link h4 bg-info">Posts Management</a></center>
            </div>
        </div><br>
        <?php if (isset($_GET['add_post']) || isset($_GET['edit_id'])) { ?>
            <div class="col-md-6">
                <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" class="register" enctype="multipart/form-data">
                    <?php if (isset($_GET['edit_id'])) {
                        $queryget = "SELECT * FROM $table where id=" . $_GET['edit_id'];
                        $resultget = mysqli_query($con, $queryget);
                        $rowget = mysqli_fetch_assoc($resultget);
                    } ?>
                    <div class="form-group col-sm-6">
                        <select id="selector" onchange="changable()" class="form-control" name="selector">
                            <option value='all'>All Cities & Languages</option>
                            <option value='sc'>Specific Cities</option>
                            <option value='sl'>Specific Languages</option>
                            <option value='co'>Custom Option</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <select id="gender" class="form-control" name="gender">
                            <option disabled>Select Gender</option>

                            <option value="male" <?php if ($rowget['gender'] == 'male') echo 'selected'; ?>>Male</option>
                            <option value="female" <?php if ($rowget['gender'] == 'female') echo 'selected'; ?>>Female</option>
                            <option value="both" <?php if ($rowget['gender'] == 'both') echo 'selected'; ?>>Both</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <select id="typeBased" class="form-control" name="typeBased">
                            <option disabled>Select Type Based Invites</option>
                            <option value="interp" <?php if ($rowget['type'] == 'interp') echo 'selected'; ?>>Interpreter</option>
                            <option value="telep" <?php if ($rowget['type']  == 'telep') echo 'selected'; ?>>Telephone</option>
                            <option value="trans" <?php if ($rowget['type']  == 'trans') echo 'selected'; ?>>Translator</option>
                            <option value="all" <?php if ($rowget['type']  == 'all') echo 'selected'; ?>>All</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" name="bookinRef" value="<?php echo $rowget['bookinRef']; ?>" placeholder="Booking Reference" class="form-control" />
                    </div>
                    <div class="form-group col-sm-12">
                        <input type="file" name="doc" value="" class="form-control" />
                    </div>
                    <div class="form-group col-sm-12">
                        <div style="display:contents" id="div_for_all" class="form-group col-sm-6">
                            <label class="h4">All Cities & Languages Selected !</label>
                        </div>
                        <div style="display:none" id="div_lang" class="form-group col-sm-6">
                            <select class="multi_class" id="selected_langs" name="selected_langs[]" multiple="multiple">
                                <?php $res_lang = $acttObj->read_all("DISTINCT lang", "lang ORDER by lang ASC", NULL);
                                while ($row_lang = mysqli_fetch_assoc($res_lang)) { ?>
                                    <option value="<?php echo $row_lang['lang']; ?>"><?php echo $row_lang['lang']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div style="display:none" id="div_city" class="form-group col-sm-6">
                            <select class="multi_class" id="selected_cities" name="selected_cities[]" multiple="multiple">
                                <?php $res_city = $acttObj->read_all("DISTINCT interpreter_reg.city", "interpreter_reg", 'deleted_flag=0 ORDER BY interpreter_reg.city ASC');
                                while ($row_city = mysqli_fetch_assoc($res_city)) { ?>
                                    <option value="<?php echo $row_city['city']; ?>"><?php echo $row_city['city']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <input type="hidden" name="id" value="<?php if (isset($_GET['edit_id'])) {
                                                                    echo $_GET['edit_id'];
                                                                } ?>" />
                        <input type="text" name="type" value="<?php if (isset($_GET['edit_id'])) {
                                                                    echo $rowget['em_type'];
                                                                } ?>" placeholder="Enter title for post" required="required" class="form-control" />
                    </div>
                    <div class="form-group col-sm-12">
                        <textarea name="em_format" id="mytextarea" cols="51" rows="4">
                     <?php if (isset($_GET['edit_id'])) {
                            echo $rowget['em_format'];
                        } ?>
        			</textarea>
                    </div>
                    <div class="form-group col-sm-12">
                        <?php if (isset($_GET['edit_id'])) { ?>
                            <button class="btn btn-info" type="submit" name="btnupdate">Update Now &raquo;</button>
                            <a class="btn btn-warning" href="post_format.php">Close <i class="glyphicon glyphicon-remove-circle"></i></a>
                        <?php } else { ?>
                            <button class="btn btn-primary" type="submit" name="btninsert">Add New Post &raquo;</button>
                            <a class="btn btn-warning" href="post_format.php">Close <i class="glyphicon glyphicon-remove-circle"></i></a>
                        <?php } ?>
                    </div>
                </form>
            </div>
        <?php } else { ?>
            <div class="col-sm-11"><a class="btn btn-primary pull-right" href="post_format.php?add_post">Add New Post <i class="glyphicon glyphicon-plus"></i></a></div>
        <?php } ?>
        <div class="<?php if (isset($_GET['add_post']) || isset($_GET['edit_id'])) {
                        echo 'col-md-6';
                    } else {
                        echo 'col-md-12';
                    } ?>">
            <table class="table table-striped table-hover">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">Email Title</th>
                        <?php if (!isset($_GET['add_post']) && !isset($_GET['edit_id'])) { ?>
                            <th scope="col">Submited By</th>
                        <?php } ?>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    $query = "SELECT * FROM $table ORDER BY em_date DESC";
                    $result = mysqli_query($con, $query);
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr <?php if ($row['status'] != "Active") { ?>style="background: #ff00004d;" title="This post is De Activated!" <?php } ?> <?php if (isset($_GET['edit_id']) && $_GET['edit_id'] == $row['id']) { ?> style='background-color:#1b631b47;' <?php } ?>>
                            <td align="left"><?php if (isset($_GET['edit_id']) && $_GET['edit_id'] == $row['id']) {
                                                    echo '<span class="label label-success" style="font-size:100%;">' . $row['em_type'] . '</span>';
                                                } else {
                                                    echo $row['em_type'];
                                                } ?></td>
                            <?php if (!isset($_GET['add_post']) && !isset($_GET['edit_id'])) { ?>
                                <td align="left text-muted"><?php echo ucwords($row['em_insertedby']); ?> on <span class="badge"><?php echo $row['em_date']; ?></span> </td>
                            <?php } ?>
                            <td align="left">
                                <a href="post_format.php?edit_id=<?php echo $row['id']; ?>" class="btn btn-default btn-xs" title="Edit Post"><i class="fa fa-edit"></i></a>
                                <?php if ($row['status'] == "Active") { ?>
                                    <a class="btn btn-danger btn-xs" href="post_format.php?del_id=<?php echo $row['id']; ?>" style="margin-left:20px;" title="De-activate Post"><i class="fa fa-close"></i>
                                    </a>
                                <?php } else { ?>
                                    <a class="btn btn-success btn-xs" href="post_format.php?activate_id=<?php echo $row['id']; ?>" style="margin-left:20px;" title="Activate Post"><i class="fa fa-refresh"></i>
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        </div>
        <script src="js/jquery-1.11.3.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
        <?php if (isset($_GET['add_post']) || isset($_GET['edit_id'])) { ?>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.9.2/ckeditor.js" integrity="sha512-OF6VwfoBrM/wE3gt0I/lTh1ElROdq3etwAquhEm2YI45Um4ird+0ZFX1IwuBDBRufdXBuYoBb0mqXrmUA2VnOA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

            <script>
                // Replace the <textarea id="editor1"> with a CKEditor 4
                // instance, using default configuration.
                CKEDITOR.replace('mytextarea', {
                    height: '350',
                    minHeight: '350',
                    maxHeight: '350'
                });
            </script>
            <!--script src="https://cdn.tiny.cloud/1/1cuurlhdv50ndxckpjk52wu6i868lluhxe90y7xesmawusin/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script-->
            <script type="text/javascript">
                /*tinymce.init({
                    selector: "#mytextarea",
                    height: 400,
                    plugins: 'print preview   searchreplace autolink autosave save directionality  visualblocks visualchars fullscreen image link media  template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount   imagetools textpattern noneditable help  ',
                    toolbar: 'undo redo | link image | code',
                    image_title: true,
                    automatic_uploads: true,
                    file_picker_types: 'image media',
                    file_picker_callback: function(cb, value, meta) {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');
                        input.onchange = function() {
                            var file = this.files[0];
                            var reader = new FileReader();
                            reader.onload = function() {
                                var id = 'blobid' + (new Date()).getTime();
                                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                                var base64 = reader.result.split(',')[1];
                                var blobInfo = blobCache.create(id, file, base64);
                                blobCache.add(blobInfo);
                                cb(blobInfo.blobUri(), {
                                    title: file.name
                                });
                            };
                            reader.readAsDataURL(file);
                        };
                        input.click();
                    }
                });*/

                $(function() {
                    $('.multi_class').multiselect({
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true
                    });
                });

                function changable() {
                    var value = document.getElementById("selector").value;
                    if (value == 'all') {
                        $('#div_for_all').css('display', 'contents');
                        $('#div_lang').css('display', 'none');
                        $('#div_city').css('display', 'none');
                    } else if (value == 'sc') {
                        $('#div_for_all').css('display', 'none');
                        $('#div_lang').css('display', 'none');
                        $('#div_city').css('display', 'contents');
                    } else if (value == 'sl') {
                        $('#div_for_all').css('display', 'none');
                        $('#div_lang').css('display', 'contents');
                        $('#div_city').css('display', 'none');
                    } else {
                        $('#div_for_all').css('display', 'none');
                        $('#div_lang').css('display', 'contents');
                        $('#div_city').css('display', 'contents');
                    }
                }
            </script>
        <?php } ?>
        <script>
            $(document).ready(function() {
                $('.table').DataTable({
                    "order": [
                        [2, 'desc']
                    ],
                    "columnDefs": [{
                        "targets": 2,
                        "type": "date"
                    }]
                });
            });
        </script>
</body>

</html>