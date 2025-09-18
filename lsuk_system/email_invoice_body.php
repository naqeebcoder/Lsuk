<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}

include 'actions.php';
$table = 'email_format';
if (isset($_POST['btnupdate'])) {
  $format = trim($_POST['em_format']);
  $dated = date('Y-m-d');
  $user_id = $_SESSION['userId'];
  $user_name = $_SESSION['UserName'];
  $old_values = $obj->read_specific("em_format as 'Email Body',em_insertedby as 'Updated By'", "$table", "id=" . $_GET['edit_id']);
  $new_values = array("em_format" => $format, "em_date" => $dated, "em_insertedby" => $user_name);
  $done = $obj->update("$table", $new_values, "id=" . $_GET['edit_id']);
  if ($done) {
    $new_values = array("Email Body" => $format, "Updated By" => $user_name);
    $obj->log_changes($old_values, $new_values, $_GET['edit_id'], $table, "update", $user_id, $user_name);
    // echo '<script>alert("Format Successfully updated!");
    //   window.location.href="email_invoice_body.php";</script>';
  } else {
    // echo '<script>alert("Sorry, There is some error!");</script>';
  }
}

// New Insertion - Email format
if (isset($_POST['btnSubmit']) && $_POST['is_validated'] == 1) {
  $format = trim($_POST['em_format']);
  $em_type = trim($_POST['em_type']);
  $dated = date('Y-m-d');
  $user_id = $_SESSION['userId'];
  $user_name = $_SESSION['UserName'];

  $new_values = array("em_format" => $format, "em_date" => $dated, "em_insertedby" => $user_name, "em_type" => $em_type);
  $result = $obj->insert($table, $new_values, true);
  if ($result) {
    $new_values = array("Email Type" => $em_type, "Email Body" => $format, "Inserted By" => $user_name);
    $obj->log_changes('', $new_values, $result, $table, "create", $user_id, $user_name);

    echo '<script>alert("Format Successfully Submitted!");';
    echo 'window.location.href="email_invoice_body.php";</script>';
  } else {
    // echo '<script>alert("Sorry, There is some error!");</script>';
  }
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Email Management</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
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

    html,
    body {
      background: none !important;
    }

    .modal {
      overflow-y: auto !important;
    }

    .efm_wrapper {
      padding: 15px;
      margin-bottom: 40px;
    }
  </style>
  <script type="text/javascript">
    function MM_openBrWindow(theURL, winName, features) { //v2.0
      window.open(theURL, winName, features);
    }
  </script>
</head>

<body>
  <?php include 'nav2.php'; ?>
  <!-- end of sidebar -->
  <section class="container-fluid">
    <div class="row">
      <div class="efm_wrapper">
        <div class="col-sm-6 text-left">
          <a href="email_invoice_body.php" style="padding: 12px;" class="alert-link h4 bg-info">Email Formats Management</a>
        </div>

        <?php if (!isset($_GET['action']) && !isset($_GET['edit_id'])) { ?>
          <div class="col-sm-6 text-right">
            <a href="?action=new" class="btn btn-info">
              <i class="fa fa-plus"></i> New Email Format
            </a>
          </div>
        <?php } ?>
      </div>
    </div>

    <div class="<?php if (isset($_GET['edit_id']) || isset($_GET['action'])) {
                  echo 'col-md-6';
                } else {
                  echo 'col-md-12';
                } ?>">
      <table class="table table-striped table-hover">
        <thead class="bg-info">
          <tr>
            <th scope="col">Email Title</th>
            <?php if (!isset($_GET['edit_id']) && !isset($_GET['action'])) { ?>
              <th scope="col">Submited By</th>
            <?php } ?>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1;
          $get_formats = $obj->read_all("*", $table, "1");
          while ($row = $get_formats->fetch_assoc()) { ?>
            <tr <?php if (isset($_GET['edit_id']) && $_GET['edit_id'] == $row['id']) { ?> style='background-color:#1b631b47;' <?php } ?>>
              <td align="left">
                <?php if (isset($_GET['edit_id']) && $_GET['edit_id'] == $row['id']) {
                  echo '<span class="label label-success" style="font-size:100%;">' . $row['em_type'] . '</span>';
                } else {
                  echo $row['em_type'];
                } ?>
              </td>
              <?php if (!isset($_GET['edit_id']) && !isset($_GET['action'])) { ?>
                <td align="left text-muted"><?php echo ucwords($row['em_insertedby']); ?> on <span class="badge"><?php echo $row['em_date']; ?></span> </td>
              <?php } ?>
              <td align="left">
                <a class="btn btn-default btn-xs" href="email_invoice_body.php?edit_id=<?php echo $row['id']; ?>" style="color:#F00;"><i title="Edit Format" class="fa fa-edit text-info"></i></a>
                <a class="btn btn-xs btn-warning" data-record-id="<?= $row['id'] ?>" onclick="view_log_changes(this)" href="javascript:void(0)" title="View Log Edited History">Edited History</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <?php if (isset($_GET['edit_id'])) { ?>
      <div class="col-md-6">
        <form action="" method="post" enctype="multipart/form-data">
          <?php if (isset($_GET['edit_id'])) {
            $rowget = $obj->read_specific("*", $table, "id=" . $_GET['edit_id']);
          } ?>
          <center><br>
            <h4 style="padding:9px;" class="col-sm-12 bg-success text-danger"><?php echo $rowget['em_type']; ?></h4>
          </center>
          <div class="form-group col-sm-12">
            <textarea name="em_format" style="padding: 5px;" id="mytextarea" cols="51" rows="5"><?php echo $rowget['em_format']; ?>
			    </textarea>
          </div>
          <div class="form-group col-sm-12">
            <button class="btn btn-primary" type="submit" name="btnupdate">Update Now &raquo;</button>
            <a class="btn btn-warning" href="email_invoice_body.php">Close <i class="glyphicon glyphicon-remove-circle"></i></a>
          </div>
        </form>
      </div>
    <?php } ?>

    <?php if (isset($_GET['action']) && $_GET['action'] == 'new') { ?>
      <div class="col-md-6">
        <form action="" method="post" enctype="multipart/form-data">
          <center><br>
            <h4 style="padding:9px;" class="col-sm-12 bg-success text-danger">New Email Format</h4>
          </center>
          <div class="form-group col-sm-12">
            <input type="text" name="em_type" id="em_type" placeholder="Email Type*" class="form-control" required>
          </div>
          <div class="form-group col-sm-12">
            <textarea name="em_format" style="padding: 5px;" id="mytextarea" class="mytextarea" cols="51" rows="5"></textarea>
          </div>
          <div class="form-group col-sm-12">
            <button class="btn btn-success" type="submit" name="btnSubmit" id="btnSubmit">Submit Now &raquo;</button>
            <input type="hidden" id="is_validated" name="is_validated" value="0">
            <a class="btn btn-warning" href="email_invoice_body.php">Close <i class="glyphicon glyphicon-remove-circle"></i></a>
          </div>
        </form>
      </div>
    <?php } ?>

    <!--Ajax processing modal-->
    <div class="modal" id="process_modal" data-backdrop="static">
      <div class="modal-dialog modal-lg" style="width: 85%;">
        <div class="modal-content">
          <div class="modal-body process_modal_attach">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
<?php if (isset($_GET['edit_id']) || isset($_GET['action'])) { ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.9.2/ckeditor.js" integrity="sha512-OF6VwfoBrM/wE3gt0I/lTh1ElROdq3etwAquhEm2YI45Um4ird+0ZFX1IwuBDBRufdXBuYoBb0mqXrmUA2VnOA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
    // Replace the <textarea id="editor1"> with a CKEditor 4
    // instance, using default configuration.
    CKEDITOR.replace('mytextarea', {
      height: '310',
      minHeight: '310',
      maxHeight: '310'
    });
  </script>

  <!--script src="https://cdn.tiny.cloud/1/1cuurlhdv50ndxckpjk52wu6i868lluhxe90y7xesmawusin/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script type="text/javascript">
      tinymce.init({
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
            reader.readAsDataURL(	);
          };
          input.click();
        },
		setup : function(editor) {
			editor.on('init', function() {
				const editorBody = editor.getBody();
				if (!editorBody) {
					console.error('Editor body not available');
					return;
				}

				const isDisabled = document.getElementById(editor.id)?.hasAttribute('disabled');
				
				// Applying for staging server - editor is not working using cloudflare/domain restriction
				if (isDisabled) { 
					editorBody.setAttribute('contenteditable', true); // Reverse condition - Readonly or editable
				} else {
					editorBody.setAttribute('contenteditable', true); // Reverse condition - Readonly or Not editable
				}
			});
		}
      });
	  
    </script-->
<?php } ?>
<script>
  $(document).ready(function() {
    $('.table').DataTable();

    $('#btnSubmit').click(function() {
      if ($('#em_type').val() == '') {
        $('#em_type').css('border-color', '#ff000069');
        return false;
      } else {
        $('#em_type').css('border-color', '');
      }

      $('#is_validated').val(1); // This will validate the form and submit
    });

    $('#DataTables_Table_0_filter').addClass('text-right');

  });

  function view_log_changes(element) {
    var table_name = "email_format";
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
          table_name_label: "Email Body Formats",
          record_label: "Email Format",
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

</html>