<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Material Design for Bootstrap</title>
  <!-- MDB icon -->
  <link rel="icon" href="img/mdb-favicon.ico" type="image/x-icon">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
  <!-- Google Fonts Roboto -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- Material Design Bootstrap -->
  <link rel="stylesheet" href="css/mdb.min.css">
  <!-- Your custom styles (optional) -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body style="height:2000px;">
    
    
<div class="modal fade"  id="myModalView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg modal-notify modal-danger" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <p class="heading lead"><b>Corona Safety Document</b></p>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
            </div>
            <div class="modal-body">
              <div class="text-center">
                <iframe id="frame" style="display:none" src="" frameborder='2' width="100%" height="500px"></iframe> 
              </div>
            </div>
            <div class="modal-footer justify-content-center">
              <a type="button" class="btn btn-outline-danger waves-effect" data-dismiss="modal">close Document</a>
            </div>
          </div>
          </div>
        </div>
<button type="button" class="btn btn-danger animated pulse infinite z-depth-5 btn-xs" style="position: absolute;bottom: 12px;right: 12px;" data-toggle="modal" data-target="#myModal">
  Corona Safety
</button>
<div class="modal fade"  id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-side modal-bottom-right modal-notify modal-danger" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <p class="heading lead"><b>Together we can stop Coronavirus Spread</b></p>
            </div>
            <div class="modal-body">
              <div class="text-center">
                <img src="../../images/corona.jpg" class="img img-responsive" width="80"/>
                <p class="ppp">We are ensuring to continue to provide our service provision as normal however to minimise contact and prevent the spread we are</p>
<style>.li li{font-size: 12px;}.ppp{font-size:13px;font-weight:bold;}</style>
<ul class="li">
  <li>Advising our interpreters that in all cases of health concerns they must follow PHE instructions and inform LSUK.</li>
  <li>Filtering the list of our available face to face interpreters. We can continue to provide this service if this cannot be avoided (postponed or replaced with telephone interpreting).</li>
  <li>Able to offer telephone interpreting, video conferencing and over the skype interpreting. These can be accessed remotely and on any device.</li>
</ul>
<p class="ppp">Our service can be booked as normal. LSUK can be reached online (www.lsuk.org), via email (info@lsuk.org) and over the phone (01173290610)</p>
<p class="red-text"><b><i class="fa fa-arrow-down animated bounce infinite"></i> Download safety document <i class="fa fa-arrow-down animated bounce infinite"></i></b></p>
<?php include '../db.php';$options_q=mysqli_query($con,'select * from corona_safety where status=1 ORDER BY lang ASC');?>
<select class="form-control" onchange="doc_load(this.value)">
  <option value="" disabled selected>Choose your language</option>
    <?php while($row_options=mysqli_fetch_assoc($options_q)){ ?>
  <option value="<?php echo $row_options['doc']; ?>"><?php echo $row_options['lang']; ?></option>
  <?php } ?>
</select>
<script>
function doc_load(id){
    $('#myModal').modal('hide');
    $('#myModalView').modal('show');
    $('#frame').css('display','inline');
    $('#frame').attr('src','../../file_folder/corona_safety/'+id);
}
</script>
              </div>
            </div>

            <div class="modal-footer justify-content-center">
              <a type="button" class="btn btn-outline-danger waves-effect" data-dismiss="modal">close</a>
            </div>
          </div>
          </div>
        </div>
  <!-- End your project here-->

  <!-- jQuery -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="js/popper.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="js/mdb.min.js"></script>

</body>
</html>
