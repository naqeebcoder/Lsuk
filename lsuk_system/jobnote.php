<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'actions.php';
$allowed_type_idz = "13,27,40,73,85,125,176";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Job Notes</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$table = 'jobnotes';
$orgName = @$_GET['orgName'];
$tbl = @$_GET['table'];
$fid = @$_GET['fid'];
$rate = null;

if ($tbl && $fid) {
    $job = $obj->read_specific("intrpName", $tbl, "id=$fid");
    $interpreter_id = $job['intrpName'];
     $interp_not_found = false;
    if ($interpreter_id) {
        $column = $tbl == 'telephone' ? 'rpm' : ($tbl == 'translation' ? 'rpu' : 'rph');
        $rate_data = $obj->read_specific($column, "interpreter_reg", "id=$interpreter_id");
        $rate = $rate_data[$column];
    }else{
      $interp_not_found = true;
    }
}
$array_job_types = array("interpreter" => "Face To Face", "telephone" => "Telephone");
//Soft delete job note
if (isset($_GET['del_id'])) {
  $obj->update($table, array("deleted_flag" => 1, "deleted_by" => $_SESSION['userId']), 'id=' . $_GET['del_id']);
}
if (isset($_POST['submit'])) {
    $jobNote = trim($_POST['jobNote']);

    // Check if rate was changed
    if (isset($_POST['change_rate_checkbox'])) {
        $oldRate = $_POST['oldrate'];
        $newRate = $_POST['interpreter_rate'];

        if ($oldRate !== $newRate) {
            $rateNote = "<span class='rate_change' style='display:none' data-old='$oldRate' data-new='$newRate'></span>";
            $jobNote .= $rateNote;
        }
    }

    $obj->insert($table, array(
        "dated" => date("Y-m-d"),
        'jobNote' => $obj->con->real_escape_string($jobNote),
        'notesread' => $_POST['notedfor'],
        'tbl' => $tbl,
        'fid' => $fid,
        "time" => date("Y-m-d H:i:s"),
        "submitted" => $_SESSION['UserName']
    ));
    ?>
    <script>
        alert('Job Note Successfully Submitted!');
        window.onunload = refreshParent;
    </script>
<?php
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Job Notes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <style>
    .multiselect {
      min-width: 350px;
    }

    .multiselect-container {
      max-height: 400px;
      overflow-y: auto;
      max-width: 380px;
    }
  </style>
  <?php include 'ajax_uniq_fun.php'; ?>
  <script type="text/javascript">
    function refreshParent() {
      window.opener.location.reload();
    }

    function popupwindow(url, title, w, h) {
      var left = (screen.width / 2) - (w / 2);
      var top = (screen.height / 2) - (h / 2);
      return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
    }

    function delete_job_note(job_note_id) {
      window.location.href = window.location.href + "&del_id=" + job_note_id;
    }

    function DoReadNote(strJobId, strJobTbl, strfid, nCountIs) {
      //alert("DoReadNote("+strfid+") here");

      var // postData = compData; // Data which you may pass.
        formURL = 'ajaxJobNote.php'; // Write callback script url here

      $.ajax({
        url: formURL,
        type: "POST",
        data: {
          jobid: strJobId,
          jobtbl: strJobTbl,
          strfidis: strfid,
          counted: nCountIs,
          colName: "test"
        },
        success: function(strData, textStatus, jqXHR) {
          if (strData) {
            var elemDiv = document.getElementById("notescontainer")
            elemDiv.innerHTML = strData;

            var jq = $(elemDiv).find("table");
            if (jq.length > 0) {
              var elemTab = jq[0];
            }

          } else {
            alert("no data OK")
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert("Something wrong with Jquery");
        }
      });
    }
  </script>
</head>

<body>
  <?php date_default_timezone_set('Europe/London'); ?>
  <form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()">
    <h3 class="text-center"><?= $array_job_types[$tbl]?> Job Notes for <span class="label label-primary"> <?php echo $orgName; ?></span></h3>
    <div class="form-group col-sm-6 col-sm-offset-3">
      <label class="control-label" for="email">Note Remarks*</label>
      <textarea class="form-control" rows="4" name="jobNote" type="text" placeholder='' required='' id="jobNote"></textarea>
    </div>
    <div class="form-group col-sm-6 col-sm-offset-3">
        <label>
            <input type="checkbox" id="change_rate_checkbox" name="change_rate_checkbox"> Prioritize this Note
        </label>
    </div>
    <div class="form-group col-sm-6 col-sm-offset-3">
      <label class="control-label" for="notedfor">Note For <span class="text-danger"><small>(Do not select any user if job note is for ALL)</small></span></label><br>
      <select id="notedfor" name="notedfor" class="form-control searchable">
        <option value="">Select a User</option>
        <?php $get_users = $obj->read_all("*", "login", "user_status=1 ORDER by name ASC");
        if ($get_users->num_rows > 0) {
          while ($row_users = $get_users->fetch_assoc()) { ?>
            <option value="<?= $row_users['id'] ?>"><?= ucwords($row_users['name']) ?></option>
        <?php }
        } ?>
      </select>
    </div>

    <!-- <div class="form-group col-sm-6 col-sm-offset-3" id="rate_input_wrapper" style="display: none;">
        <label>Interpreter Rate (<span id="rate_label"></span>)</label>
        <input type="number" name="interpreter_rate" id="interpreter_rate" class="form-control" value="<?= $rate ?>">
    </div> -->
    <div class="form-group col-sm-6 col-sm-offset-3">
      <input type="hidden" name="oldrate" value="<?= $rate ?>">
      <button type="submit" name="submit" class="btn btn-primary" onclick="return formSubmit(); return false">Submit &raquo;</button> &nbsp;&nbsp;&nbsp;<button type="reset" name="reset" class="btn btn-warning">Clear All</button>
    </div>
    <fieldset class="col-sm-12">
      <center>
        <h3>All Notes for this job</h3>
      </center>

      <div id="notescontainer">

        <?php
        $nCountIs = 0;
        include("jobnotetable.php");
        ?>
        <br><br><br>
      </div>
    </fieldset>

  </form>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
<script>
  $(function() {
    $('.searchable').multiselect({
      includeSelectAllOption: true,
      numberDisplayed: 1,
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true
    });
  });
</script>
<script>
    // document.getElementById('change_rate_checkbox').addEventListener('change', function () {
    //     const wrapper = document.getElementById('rate_input_wrapper');
    //     const input = document.getElementById('interpreter_rate');
    //     if (this.checked) {
    //         wrapper.style.display = 'block';
    //         input.setAttribute('required', 'required');
    //     } else {
    //         wrapper.style.display = 'none';
    //         input.removeAttribute('required');
    //     }
    // });

    // Set the rate label based on job type
    const jobType = "<?= $tbl ?>";
    const labelMap = {
        telephone: 'Rate Per Minute',
        translation: 'Rate Per Unit',
        interpreter: 'Rate Per Hour'
    };
    document.getElementById('rate_label').textContent = labelMap[jobType] || '';
</script>
<script>
document.querySelectorAll('td').forEach(function(td) {
    const regex = /<span[^>]*class=["']?rate_change["']?[^>]*>.*?<\/span>/i;
    if (regex.test(td.innerHTML)) {
        const badge = document.createElement('span');
        badge.textContent = 'Prioritized';
        badge.className = 'badge badge-danger';
        badge.style.float = 'right';
        badge.style.background = 'red';
        td.appendChild(badge);
    }
});
</script>
</html>