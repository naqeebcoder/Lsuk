<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$table = 'receivable';
$allowed_type_idz = "109";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Edit Receivable</u> action!<br>Kindly contact admin for further process.</h2></center>");
  }
}
$edit_id = @$_GET['edit_id'];
$query = "SELECT receivable.*,receivable_types.title FROM receivable,receivable_types WHERE receivable.receivable_id=receivable_types.id  and receivable.id=$edit_id";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>Edit Receivable</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />

  <?php include 'ajax_uniq_fun.php'; ?>
  <script src="js/jquery-1.5.2.min.js" type="text/javascript"></script>
  <script>
    $(document).ready(function() {

      $("#receivable_id").change(function() {
        var receivable_id = $(this).find("option:selected").text();
        var loans = receivable_id.trim();

        $("#loadFiless").val(loans);

        if (loans == 'Loans') {

          $("#loan").show();
        } else {
          $("#loan").hide();
        }

      });
    });
  </script>
</head>

<body>
  <div class="container">
    <form action="process.php" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()" enctype="multipart/form-data">
      <h1>Update Receivable Details</h1>

      <?php
      if (isset($_SESSION['success']) and $_SESSION['success'] != '') {
      ?>
        <div class="alert alert-success col-md-6">
          <?php echo $_SESSION['success']; ?>
        </div>
      <?php }
      unset($_SESSION['success']); ?>

      <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
      <!--<p><center>
    <img id="output" src="images/default.png" title="Kindly add Expense slip picture(if any)" name="output" class="img-thumbnail img-responsive" style="max-width: 140px;max-height: 140px;min-width: 140px;min-height: 140px;" /><p></p>
    <input type="file" name="interpreterphoto" value="images/default.png" accept="image/*" onchange="loadFile(event)" id="interpreterphoto" style='width: 25%;float: none;'></center>
    </p>-->
      <input type="hidden" name="loadFiless" id="loadFiless" value="<?php echo $row['title']; ?>">
      <div class="form-group col-sm-6">
        <label> Given By * </label>
        <input name="given_by" type="text" class="form-control" placeholder='' value="<?php echo $row['given_by']; ?>" required='' id="given_by" />
      </div>


      <div class="form-group col-sm-6">
        <label>Received Date * </label>
        <input class="form-control" name="received_date" value="<?php echo $row['received_date']; ?>" type="date" placeholder='' required='' id="received_date" />
      </div>
      <div class="form-group col-sm-4">
        <label>Receivable Type * </label>

        <select class="form-control" name="receivable_id" id="receivable_id" required>
          <?php
          $sql_opt = "SELECT id,title FROM receivable_types ORDER BY title ASC";
          $result_opt = mysqli_query($con, $sql_opt);
          $options = "";

          while ($row_opt = mysqli_fetch_array($result_opt)) {
            $select = '';
            $exp_id = $row_opt["id"];
            $name_opt = $row_opt["title"];
            if ($exp_id == $row['receivable_id']) {
              $select = " selected";
            }
            $options .= "<OPTION value='$exp_id' $select>" . $name_opt;
          } ?>
          <option value="">Select Expense Type</option>
          <?php echo $options; ?>
          </option>
        </select>
      </div>

      <div class="form-group col-sm-4">
        <label>Amount * </label>
        <input class="form-control" value="<?php echo $row['amount']; ?>" name="amount" type="text" placeholder='' required='' id="amount" />
      </div>

      <div class="form-group col-sm-4">
        <label>Attachment </label>
        <input class="form-control" name="file" type="file" id="attachment" />

        <input type="hidden" value="<?php echo $row['attachment']; ?>" name="attachment">

      </div>

      <div class="form-group col-sm-12">
        <textarea class="form-control" name="details" rows="3" placeholder='Write Receivable details here ...' id="details"><?php echo $row['details']; ?></textarea>
      </div>


      <div id="loan" <?php if ($row['title'] != 'Loans') { ?> style="display: none" <?php } ?>>
        <div class="form-group col-sm-6 col-xs-6">
          <label>Terms</label>
          <input class="form-control" name="terms" type="number" placeholder='' required='' value="<?php echo $row['terms']; ?>" id="terms" />
        </div>
        <div class="form-group col-sm-6 col-xs-6">
          <label>Installments</label>
          <input class="form-control" name="installments" type="number" placeholder='' required='' id="installments" value="<?php echo $row['installments']; ?>" />
        </div>
      </div>
      <input type="hidden" value="1" name="add_receivable">

      <div class="form-group col-sm-4 col-xs-12"><br>
        <button class="btn btn-primary" type="submit" name="submit">Update Expense <i class="glyphicon glyphicon-refresh"></i></button>
      </div>
    </form>
  </div>
</body>

</html>