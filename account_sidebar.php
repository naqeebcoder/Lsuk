<style>
  .nav-tabs>li {
    min-width: 156px;
    border: 1px solid lightgrey;
  }

  .nav-tabs>li.active>a,
  .nav-tabs>li.active>a:focus,
  .nav-tabs>li.active>a:hover {
    color: #fff;
    background-color: #337ab7;
    border: none;
    border-bottom-color: transparent;
  }

  .nav-tabs>li>a {
    margin-right: 0px;
    border: none;
    border-radius: 0px;
  }

  nav.list-group a img {
    vertical-align: middle;
    display: inline;
  }

  .d-inline-block {
    display: inline-block !important;
  }

  .border {
    border: 2px solid #04346a;
  }

  .p-0 {
    padding: 0px;
  }

  .p-2 {
    padding: 2px;
  }

  .p-5 {
    padding: 5px;
  }

  @keyframes label-blinking {
    0% {
      opacity: 1;
    }

    50% {
      opacity: 0;
    }

    100% {
      opacity: 1;
    }
  }

  .label-blinking {
    font-size: 12px;
    animation: label-blinking 1s infinite;
  }

  .container {
    width: auto;
  }
</style>
<div class="col-sm-4 col-md-3 col-lg-3 col-xl-3">
  <div class="wizard">
    <nav class="list-group list-group-flush">

      <a class="list-group-item" href="javascript:void(0)">
        <div class="d-flex justify-content-between align-items-center">
          <div class="row p-2">
            <div class="col-xs-4">
              <img width="100%" src="<?= $photo_path ?>" alt="LSUK interpreter Profile" class="img-circle border">
            </div>
            <div class="col-md-8 p-0">
              <h5 style="margin-bottom: 0px"><?= ucwords($row['name']) ?> <i class="fa fa-check-circle text-success"></i></h5>
              <span class="text-muted">Registered Since <?= $misc->dated($row['dated']) ?></span>
              <span onclick="popupwindow('profile_pix.php','_blank',950,750)" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Edit Picture</span>
              <span data-toggle="modal" data-target="#myModalLogout" title="Click here to logout" class="btn btn-xs btn-danger"><i class="fa fa-lock"></i> Logout</span>
            </div>
          </div>
        </div>
      </a>
      <a href="interp_profile.php" class="list-group-item">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <img src="images/interpreter/lsuk-profile.png" width="12%" class="mr-1">
            <div class="d-inline-block font-weight-medium text-uppercase"><b>My Profile</b></div>
          </div>
        </div>
      </a>
      <a href="interp_card.php" class="list-group-item">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <img src="images/interpreter/id-card.png" width="12%" class="mr-1">
            <div class="d-inline-block font-weight-medium text-uppercase"><b>LSUK Badge</b></div>
          </div>
        </div>
      </a>
      <a href="interp_docs.php" class="list-group-item">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <img src="images/interpreter/lsuk-documents.png" width="12%" class="mr-1">
            <div class="d-inline-block font-weight-medium text-uppercase"><b>New Documents</b> <?= $check_noty == 0 ? '<span id="noty_badge" class="label label-warning label-blinking alert_img">New</span>' : '' ?></div>
          </div>
        </div>
      </a>
      <a class="list-group-item" href="jobs.php">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <img src="images/interpreter/lsuk-available-jobs.png" width="12%" class="mr-1">
            <div class="d-inline-block font-weight-medium text-uppercase"><b>Available Jobs</b></div>
          </div>
        </div>
      </a>
      <a class="list-group-item" href="time_sheet_interp.php">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <img src="images/interpreter/lsuk-active-jobs.png" width="12%" class="mr-1">
            <div class="d-inline-block font-weight-medium text-uppercase"><b>Allocated Jobs</b></div>
          </div>
        </div>
      </a>
      <a class="list-group-item" href="interp_unpaid_jobs.php">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <img src="images/interpreter/lsuk-unpaid-jobs.png" width="12%" class="mr-1">
            <div class="d-inline-block font-weight-medium text-uppercase"><b>Un-Paid Jobs</b></div>
          </div>
        </div>
      </a>
      <a class="list-group-item" href="salary_list.php">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <img src="images/interpreter/lsuk-salary-slips.png" width="12%" class="mr-1">
            <div class="d-inline-block font-weight-medium text-uppercase"><b>Salary Slips</b></div>
          </div>
        </div>
      </a>
      <a class="list-group-item" href="javascript:void(0)" onclick="popupwindow('interp_reg_schedul.php','Interpreter Availibility Schedule',950,550)">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <img src="images/interpreter/lsuk-availability.png" width="12%" class="mr-1">
            <div class="d-inline-block font-weight-medium text-uppercase"><b>Change Availability</b></div>
          </div>
        </div>
      </a>
      <a class="list-group-item" href="update_password.php">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <img src="images/interpreter/lsuk-password.png" width="12%" class="mr-1">
            <div class="d-inline-block font-weight-medium text-uppercase"><b>Change Password</b></div>
          </div>
        </div>
      </a>
    </nav>
  </div>
</div>
<!-- Logout Modal -->
<div class="modal fade" id="myModalLogout" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">LOGOUT</h4>
      </div>
      <div class="modal-body">
        <h4>Are you sure to logout from LSUK?</h4>
      </div>
      <div class="modal-footer">
        <a href="logout.php" class="btn btn-danger">Yes</a>
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>
<!-- Logout Modal Ends Here-->
<!-- Card Modal -->
<div class="modal fade" id="cardModal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title text-center"><b>PRINT INTERPRETING CARD</b></h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-8 col-md-offset-4">
            <button onclick="print_card('div_card');" class="btn btn-primary pull-left"><i class="fa fa-print"></i> Print Interpreting Card</button><br><br><br>
          </div>
          <div class="div_card col-md-8 col-md-offset-2">
            <style>
              @media print {
                body {
                  margin: 4px !important;
                  display: block;
                  visibility: visible;
                  -webkit-print-color-adjust: exact; /* For Webkit browsers */
                  color-adjust: exact; /* For other browsers */
                }
                .btn_print {
                  display: none;
                }
            }
            </style>
            <div class="panel panel-default" align="center" style="background: #efefef !important;">
              <img style="border: 1px solid #7c7c7c !important;background: white !important;" width="20%" src="images/logo_lsuk.png" class="img-responsive img-circle">
              <div style="border-bottom: 1px solid #dcdada;">
                <h2 class="text-center">Language Services UK Limited</h2>
              </div>
              <div class="panel-body" align="left" style="background: white !important;padding: 5px !important;border: 1px solid #d7d7d7 !important;">
                <div class="col-xs-12">
                    <h3 style="margin: 5px 0px !important;"><?= ucwords($row['name']) ?></h3>
                </div>
                <div class="col-xs-5" style="padding: 0px !important;">
                  <img style="border: 1px solid #c9c9c9 !important;border-radius: 8px !important;" width="100%" src="<?= $photo_path ?>" class="img-responsive">
                </div>
                <div class="col-xs-6 col-xs-offset-1" style="padding: 0 2px !important;">
                  <ul class='list-group' style="min-height: 135px !important;">
                  <?php foreach ($interpreter_languages as $key => $row_lang) {
                    echo "<li style='padding: 8px !important;' class='list-group-item'>" . $row_lang . "</li>";
                  } ?>
                  </ul>
                </div>
              </div>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <td style="background: #efefef !important;"><span>Registration Number #</span></td>
                    <td style="background: #efefef !important;"><?= str_pad($row['id'], 6, "0", STR_PAD_LEFT) ?></td>
                  </tr>
                  <tr>
                    <td style="background: #efefef !important;"><span>Expiry Date</span></td>
                    <td style="background: #efefef !important;">31-12-<?= date('Y') ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Card Modal Ends Here-->
<button type="button" class="btn btn-danger" style="position: fixed;bottom: 12px;right: 12px;z-index:9999;" data-toggle="modal" data-target="#ticket_modal">Having Issues <i class="fa fa-question-circle"></i>
</button>
<!-- Ticket Modal -->
<div class="modal fade" id="ticket_modal" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title">Submit a Ticket</h3>
      </div>
      <div class="modal-body">
        <h4>Share details about your issue in online portal</h4>
        <form action="" method="POST">
          <div class="form-group">
            <input type="text" class="form-control" placeholder="Enter your ticket title" name="title" required>
          </div>
          <div class="form-group">
            <textarea rows="5" class="form-control" placeholder="Enter details of your issue" name="details" required></textarea>
          </div>
          <div class="form-group">
            <button type="submit" name="btn_submit_ticket" class="btn btn-primary">Submit</button>
            <button type="reset" class="btn btn-default">Clear</button>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Ticket Modal Ends Here-->