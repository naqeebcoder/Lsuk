<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
if (empty($search_2)) {
  $search_2 = date("Y-m-d");
}
if (empty($search_3)) {
  $search_3 = date("Y-m-d");
}  ?>
<!doctype html>
<html lang="en">

<head>
  <title>OVERALL GENERAL REPORT</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <?php include "incmultiselfiles.php";
  ?>
  <script type="text/javascript">
    $(function() {
      $('#search_1').multiselect({
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Select Company'
      });
    });


    function myFunction_date() {
      var x = $('#search_1').val();
      var dt1 = $('#search_2').val();
      var dt2 = $('#search_3').val();
      if (Date.parse(dt1) && Date.parse(dt2) && x) {
        window.location.assign('<?php echo basename(__FILE__); ?>?search_1=' + x + '&search_2=' + dt1 + '&search_3=' + dt2);
      } else {
        alert('Kindly select Company & Date Range first ! Thank you');
      }
    }
  </script>

<body>
  <?php include 'nav2.php'; ?>
  <section class="container-fluid" style="overflow-x:auto">
    <div class="col-md-12">
      <header>
        <center>
          <h2 class="col-md-4 col-md-offset-4 text-center">
            <div class="alert bg-primary h4"><a style="color:white" href="<?php echo basename(__FILE__); ?>">OVERALL GENERAL REPORT</a></div>
          </h2>
        </center>
        <div class="col-md-10 col-md-offset-2"><br>
          <div class="form-group col-md-2 col-sm-4">
            <select id="search_1" name="search_1" multiple="multiple" class="form-control">
              <?php
              $sql_opt = "SELECT name,id,abrv FROM comp_reg ORDER BY name ASC";
              $result_opt = mysqli_query($con, $sql_opt);
              $options = "";
              while ($row_opt = mysqli_fetch_array($result_opt)) {
                $abrv = $row_opt["abrv"];
                $name_opt = $row_opt["name"];
                $options .= "<option value='$abrv'>" . $name_opt . "</option>";
              }
              ?>
              <?php echo $options; ?>
            </select>
          </div>
          <div class="form-group col-md-2 col-sm-4">
            <input type="date" id="search_2" name="search_2" class="form-control" <?php if (isset($_GET['search_2']) && !empty($_GET['search_2'])) {
                                                                                    echo 'value="' . $_GET['search_2'] . '"';
                                                                                  } ?> />
          </div>
          <div class="form-group col-md-2 col-sm-4">
            <input type="date" id="search_3" name="search_3" class="form-control" <?php if (isset($_GET['search_3']) && !empty($_GET['search_3'])) {
                                                                                    echo 'value="' . $_GET['search_3'] . '"';
                                                                                  } ?> />
          </div>
          <div class="form-group col-md-1 col-sm-4">
            <a href="javascript:void(0)" title="Click to Get Report" onclick="myFunction_date()"><span class="btn btn-sm btn-primary">Get Report</span></a>
          </div>
          <div class="form-group col-md-1 col-sm-4">
            <a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>" title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel</span></a>
          </div>
        </div>
      </header>


      <div class="tab_container">
        <div id="tab1" class="tab_content" align="center">

          <iframe class="col-xs-10 col-xs-offset-1" height="1000px" src="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>"></iframe>

        </div>

      </div>
      </article>
      <div class="clear"></div>

      <div class="spacer"></div>
  </section>
</body>

</html>