<?php if (isset($_GET['id'])) {
    if (session_id() == '' || !isset($_SESSION)) {
        session_start();
    }
    include 'db.php';
    include 'class.php';
    $allowed_type_idz = "42";
    //Check if user has current action allowed
    if ($_SESSION['is_root'] == 0) {
        $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
        if (empty($get_page_access)) {
            die("<center><h2 class='text-center text-danger'>You do not have access to <u>Edit Interpreter Profile</u> action!<br>Kindly contact admin for further process.</h2></center>");
        }
    }
    $id = $_GET['id'];
    $table = 'interpreter_reg';
    if (isset($_POST["btn_upload"])) {
        $image_parts = explode(";base64,", $_POST['cropped_image']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $profile_photo_name = round(microtime(true)) . '.' . $image_type;
        $targetPath = "file_folder/interp_photo/" . $profile_photo_name;
        $directory_profile_photo = "file_folder/interp_photo/";
        if (!empty($image_base64)) {
            $old_profile_photo = $_POST['old_profile_photo'];
            if ($old_profile_photo != "") {
                if (unlink($directory_profile_photo . $old_profile_photo)) {
                    file_put_contents($targetPath, $image_base64);
                }
            } else {
                file_put_contents($targetPath, $image_base64);
            }
            if ($acttObj->update("$table", array("interp_pix" => $profile_photo_name), array("id" => $id))) {
                $acttObj->editFun($table, $id, 'pic_updated', 1);
                echo '<script>function refreshParent(){window.opener.location.reload();}
            alert("Photo has been updated successfully. Thank you");window.onunload = refreshParent;window.close();</script>';
            } else {
                echo '<script>alert("Failed to update interpreter photo. Try again!");window.history.back(-1);</script>';
            }
        }
    }
    $row = $acttObj->read_specific("*", "$table", "id=" . $id);
?>
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <title>Interpreter Details</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>

    <body>
        <div class="col-md-12">
            <h2 class="text-center"> Update Photo for <span class="label label-primary b"><?php echo ucwords($row['name']); ?></span></h2>
            <br>
            <form action="" method="post" enctype="multipart/form-data">
                <img id="cropbox" class="img img-responsive img-thumbnail" src="file_folder/interp_photo/<?php echo $row['interp_pix'] == '' ? 'profile.png' : $row['interp_pix']; ?>" alt="Profile Picture" title="Profile Picture for <?php echo $row['name']; ?>">
                <button style="margin-top:2px;" type="button" id="crop" class="btn btn-sm btn-info">Crop Photo</button>
                <div class="col-md-12 hidden div_preview_cropped" style="margin-top: 2px;border: 1px solid lightgrey;">
                    <h4>Cropped Photo Preview</h4>
                    <img src="#" id="cropped_img">
                    <span title="Reset Photo" onclick="$('.div_preview_cropped').addClass('hidden');" class="fa fa-remove text-danger" style="cursor:pointer;border: 2px solid;border-radius: 100%;padding: 2px 4px;"></span>
                    <input type="hidden" name="cropped_image" id="cropped_image">
                    <br><br><br>
                    <input name="old_profile_photo" type="hidden" value="<?php echo $row['interp_pix']; ?>" />
                    <button type="submit" name="btn_upload" title="Save Cropped Photo" onclick="return confirm('Are you sure to save this photo?');" class="btn btn-success"><i class="fa fa-check"></i> Save Now</button>
                    <br><br><br>
                </div>
            </form>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="css/jquery.Jcrop.min.css" type="text/css" />
        <script src="js/jquery.Jcrop.min.js"></script>
    </body>
    <script type="text/javascript">
        $(document).ready(function() {
            var size;
            $('#cropbox').Jcrop({
                aspectRatio: 1,
                onSelect: function(c) {
                    size = {
                        x: c.x,
                        y: c.y,
                        w: c.w,
                        h: c.h
                    };
                    $("#crop").css("visibility", "visible");
                }
            });
            $("#crop").click(function() {
                var img = $("#cropbox").attr('src');
                $(".div_preview_cropped").removeClass('hidden');
                $("#cropped_img").attr('src', 'image-crop.php?x=' + size.x + '&y=' + size.y + '&w=' + size.w + '&h=' + size.h + '&img=' + img);
                toDataUrl($('#cropped_img').attr('src'), function(myBase64) {
                    $('.new_image').attr('src', myBase64);
                    $('#cropped_image').val(myBase64);
                });
            });
        });

        function toDataUrl(url, callback) {
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var reader = new FileReader();
                reader.onloadend = function() {
                    callback(reader.result);
                }
                reader.readAsDataURL(xhr.response);
            };
            xhr.open('GET', url);
            xhr.responseType = 'blob';
            xhr.send();
        }
    </script>

    </html>
<?php } ?>