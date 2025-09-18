<?php if (isset($_POST['edit_idd']) && isset($_POST['code_qss']) && isset($_POST['namee']) && isset($_POST['orgName'])) {
  session_start();
  include 'actions.php';
  $table = 'interp_assess';
  $code_qs = $_POST['code_qss'];
  $order_id = $_POST['order_id'];
  $name = $_POST['namee'];
  $orgName = $_POST['orgName'];
?>
  <style>
    #frm_review td {
      font-size: 12px;
    }
  </style>
  <?php $row_assess = $obj->read_specific("count(*) as check_exist", "interp_assess", "order_id='$order_id' and interpName='$code_qs'");
  if ($row_assess['check_exist'] == 0) { ?>
    <div class="col-md-12">
      <form action="" method="post" class="register" id="frm_review">
        <h1> Interpreter Assessment Form for <span style="color:#F00;"> <?php echo $name; ?></span></h1>
        <fieldset class="row1">
          <legend style="border:none;">Details
          </legend>
          <div>
            <input type='hidden' id="orgName_feedback" value="<?php echo $orgName; ?>" readonly=''>
            <span class="col-sm-6">
              <p><label class="optional">Feedback Method</label>
                <select name="get_feedback" id="get_feedback" required='' class="form-control" style="width: 190px;">
                  <option value="">--Select--</option>
                  <option>Email</option>
                  <option>Timesheet</option>
                  <option>Phone</option>
                  <option>Others</option>
                  <option>Online</option>
                  <option>App</option>
                </select>
              </p>
            </span>
          </div>
          <div><br>
            <table width="100%" align="center" class="table table-hover">
              <tr>
                <td><strong>About</strong></td>
                <td><strong>Poor</strong></td>
                <td><strong>Average</strong></td>
                <td><strong>Fair</strong></td>
                <td><strong>Good</strong></td>
                <td><strong>Excellent</strong></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>Punctuality * </td>
                <td><input type="radio" name="punctuality" id="punctuality" value="-5" required='' /></td>
                <td><input type="radio" name="punctuality" id="punctuality" value="1" required='' /></td>
                <td><input type="radio" name="punctuality" id="punctuality" value="5" required='' /></td>
                <td><input type="radio" name="punctuality" id="punctuality" value="10" required='' /></td>
                <td><input type="radio" name="punctuality" id="punctuality" value="15" required='' /></td>
                <td></td>
              </tr>
              <tr>
                <td>Appearance * </td>
                <td><input type="radio" name="appearance" id="appearance" value="-5" required='' /></td>
                <td><input type="radio" name="appearance" id="appearance" value="1" required='' /></td>
                <td><input type="radio" name="appearance" id="appearance" value="5" required='' /></td>
                <td><input type="radio" name="appearance" id="appearance" value="10" required='' /></td>
                <td><input type="radio" name="appearance" id="appearance" value="15" required='' /></td>
                <td>
                </td>
              </tr>
              <tr>
                <td>Professionalism * </td>
                <td><input type="radio" name="professionalism" id="professionalism" value="-5" required='' /></td>
                <td><input type="radio" name="professionalism" id="professionalism" value="1" required='' /></td>
                <td><input type="radio" name="professionalism" id="professionalism" value="5" required='' /></td>
                <td><input type="radio" name="professionalism" id="professionalism" value="10" required='' /></td>
                <td><input type="radio" name="professionalism" id="professionalism" value="15" required='' /></td>
                <td></td>
              </tr>
              <tr>
                <td>Confidentiality * </td>
                <td><input type="radio" name="confidentiality" id="confidentiality" value="-5" required='' /></td>
                <td><input type="radio" name="confidentiality" id="confidentiality" value="1" required='' /></td>
                <td><input type="radio" name="confidentiality" id="confidentiality" value="5" required='' /></td>
                <td><input type="radio" name="confidentiality" id="confidentiality" value="10" required='' /></td>
                <td><input type="radio" name="confidentiality" id="confidentiality" value="15" required='' /></td>
                <td></td>
              </tr>
              <tr>
                <td>Impartiality * </td>
                <td><input type="radio" name="impartiality" id="impartiality" value="-5" required='' /></td>
                <td><input type="radio" name="impartiality" id="impartiality" value="1" required='' /></td>
                <td><input type="radio" name="impartiality" id="impartiality" value="5" required='' /></td>
                <td><input type="radio" name="impartiality" id="impartiality" value="10" required='' /></td>
                <td><input type="radio" name="impartiality" id="impartiality" value="15" required='' /></td>
                <td></td>
              </tr>
              <tr>
                <td>Accuracy * </td>
                <td><input type="radio" name="accuracy" id="accuracy" value="-5" required='' /></td>
                <td><input type="radio" name="accuracy" id="accuracy" value="1" required='' /></td>
                <td><input type="radio" name="accuracy" id="accuracy" value="5" required='' /></td>
                <td><input type="radio" name="accuracy" id="accuracy" value="10" required='' /></td>
                <td><input type="radio" name="accuracy" id="accuracy" value="15" required='' /></td>
                <td></td>
              </tr>
              <tr>
                <td>Rapport * </td>
                <td><input type="radio" name="rapport" id="rapport" value="-5" required='' /></td>
                <td><input type="radio" name="rapport" id="rapport" value="1" required='' /></td>
                <td><input type="radio" name="rapport" id="rapport" value="5" required='' /></td>
                <td><input type="radio" name="rapport" id="rapport" value="10" required='' /></td>
                <td><input type="radio" name="rapport" id="rapport" value="15" required='' /></td>
                <td></td>
              </tr>
              <tr>
                <td>Communication * </td>
                <td><input type="radio" name="communication" id="communication" value="-5" required='' /></td>
                <td><input type="radio" name="communication" id="communication" value="1" required='' /></td>
                <td><input type="radio" name="communication" id="communication" value="5" required='' /></td>
                <td><input type="radio" name="communication" id="communication" value="10" required='' /></td>
                <td><input type="radio" name="communication" id="communication" value="15" required='' /></td>
                <td></td>
              </tr>

            </table>

          </div>
        </fieldset>

        <fieldset class="row1">
          <legend>Person Giving Feedback</legend>
          <p>
            <label class="optional">Name</label>
            <input type="text" class="form-control" name="p_feedbackby" id="p_feedbackby" required=''>
          </p>
        </fieldset>

        <fieldset class="row1">
          <legend>Positive Feedback</legend>
          <p>
            <label class="optional"> Reason
            </label>
            <textarea class="form-control" class="form-control" name="p_reason" id="p_reason" required=''></textarea>
          </p>
        </fieldset>

        <fieldset class="row1">
          <legend>Negative Feedback</legend>
          <p>
            <label class="optional"> Reason
            </label>
            <textarea class="form-control" name="n_reason" id="n_reason" required=''></textarea>
          </p>
          <p>
          <h1 style="color:#F00"> <label class="optional" style="color:#069; font-size:14px;"> Calculated Stars:</label>
            <?php
            //show assessment rating
            $query = "SELECT (sum(punctuality) + sum(appearance) + sum(professionalism) + 
          sum(confidentiality) + sum(impartiality) + sum(accuracy) + sum(rapport) + 
          sum(communication)) as sm,COUNT(interp_assess.id) as diviser 
        FROM interp_assess
        JOIN interpreter_reg ON interp_assess.interpName=interpreter_reg.code	 
        where interp_assess.interpName='$code_qs'";

            $result = mysqli_query($con, $query);
            while ($row = mysqli_fetch_array($result)) {
              $diviser = $row['diviser'];
              if ($diviser <= 0) {
                $diviser = 1;
              }
              $assess_num = $row['sm'] * 100 / ($diviser * 120);
            }
            //echo $assess_num;
            if ($assess_num < 0) {
              echo 'Negative Feedback';
            }
            if ($assess_num >= 0 && $assess_num <= 5) {
              echo 'No Feedback Received';
            }
            if ($assess_num > 6 && $assess_num <= 20) {
              echo '* ';
            }
            if ($assess_num > 20 && $assess_num <= 40) {
              echo '** ';
            }
            if ($assess_num > 40 && $assess_num <= 60) {
              echo '*** ';
            }
            if ($assess_num > 60 && $assess_num <= 80) {
              echo '**** ';
            }
            if ($assess_num > 80 && $assess_num <= 100) {
              echo '***** ';
            }
            ?>
          </h1>
          </p>
          <div>
            <button class="btn btn-primary" type="button" name="review_submit" id="review_submit">Submit &raquo;</button>
          </div>
        </fieldset>
      </form>
    </div>
  <?php } else { ?>
    <div>
      <h4 class="text-center">Sorry! Feedback already given for this job.<br><br>Thank You</h4>
    </div>
    <script>
      $("#lbl_feedback").hide();
      $("#check_further").before("<label>Do you want to add future job?</label>");
      $("#check_further").empty();
      $("#check_further").append("<option value=''></option>");
      $("#check_further").append("<option value='no_future'>Not for Future Job (on timesheet)</option>");
      $("#check_further").append("<option value='future'>Future Job (on timesheet)</option>");
    </script>
  <?php } ?>
  <script>
    $("#review_submit").click(function() {
      var form_elements = document.getElementById('frm_review').elements;
      var orgName = form_elements['orgName_feedback'].value;
      var get_feedback = form_elements['get_feedback'].value;
      var punctuality = form_elements['punctuality'].value;
      var appearance = form_elements['appearance'].value;
      var professionalism = form_elements['professionalism'].value;
      var confidentiality = form_elements['confidentiality'].value;
      var impartiality = form_elements['impartiality'].value;
      var accuracy = form_elements['accuracy'].value;
      var rapport = form_elements['rapport'].value;
      var communication = form_elements['communication'].value;
      var p_feedbackby = form_elements['p_feedbackby'].value;
      var p_reason = form_elements['p_reason'].value;
      var n_reason = form_elements['n_reason'].value;
      var order_id = '<?php echo $order_id; ?>';
      var UserName = '<?php echo $_SESSION['UserName'] ?>';
      var interpName = '<?php echo $code_qs; ?>';
      if (order_id != '' && orgName != '' && UserName != '' && interpName != '' && get_feedback != '') {
        $.ajax({
          url: "store_review.php",
          type: "POST",
          data: {
            orgName: orgName,
            get_feedback: get_feedback,
            punctuality: punctuality,
            appearance: appearance,
            professionalism: professionalism,
            confidentiality: confidentiality,
            impartiality: impartiality,
            accuracy: accuracy,
            rapport: rapport,
            communication: communication,
            p_feedbackby: p_feedbackby,
            p_reason: p_reason,
            n_reason: n_reason,
            order_id: order_id,
            UserName: UserName,
            interpName: interpName
          },
          success: function(data) {
            if (data == 'good') {
              $('#myModal').modal("hide");
              $("#lbl_feedback").hide();
              $("#check_further").before("<label>Do you want to add future job?</label>");
              $("#check_further").empty();
              $("#check_further").append("<option value=''></option>");
              $("#check_further").append("<option value='no_future'>Not for Future Job (on timesheet)</option>");
              $("#check_further").append("<option value='future'>Future Job (on timesheet)</option>");
            }
          }
        });
      } else {
        alert('Kindly fill the form!');
      }
    });
  </script>
<?php } ?>