<?php
if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
// include 'secure.php';
if(isset($_GET['print_from_list']) && isset($_GET['id'])){
  $interp_code = $_GET['id'];
  $query = "SELECT * FROM interpreter_reg where id=$interp_code";
}else{
  $interp_code = $_SESSION['interp_code'];
  $query = "SELECT * FROM interpreter_reg where code='$interp_code'";
}
include 'source/db.php';
include 'source/class.php';
$level_array = array("1" => "Native", "2" => "Fluent", "3" => "Intermediate", "4" => "Basic");
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);
$interp_id = $row['id'];
// $user_id = $_SESSION['web_userId'];
// $user_name = $_SESSION['web_UserName'];
$picture = $row['interp_pix'] ? $row['interp_pix'] : 'profile.png';
$photo_path = "lsuk_system/file_folder/interp_photo/" . $picture;
if (!file_exists($photo_path)) {
  $photo_path = "lsuk_system/file_folder/interp_photo/profile.png";
}
?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
  <meta name="google-site-verification" content="FD3pfiOXrr6D1lGvNWqseAJrL1PMPj1nguqXAd5mFkY" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <script src="prefixfree.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>LSUK Limited the name to trust for Certified Translation,Professional Interpreter and Translator Bristol</title>
  <meta name="robots" content="index, follow" />
  <link rel="canonical" href="http://www.lsuk.org" />
  <link href="style.css" type="text/css" rel="stylesheet" id="main-style">
  <link href="css/responsive.css" type="text/css" rel="stylesheet">
  <link href="images/favicon.ico" type="image/x-icon" rel="shortcut icon">
  <meta http-equiv="Content-Language" content="en" />
  <meta name="description" content="Professional Interpreting and Certified Document Translation only a click away" />
  <meta name="keywords" content="Professional Interprete and translator" ,"Interpreting","Court Interpreter","Medical Interpreter","Certified Document Translation","Technical Translation","Audio and Video Transcription","Telephone Interpreting","Sign Language Interpreter","BSL Interpreter","Translation Company","Interepting Service Bristol","Professional Intereprter Bristol","Bath","Cardiff", "Newport" ,"Gloucester","Swindon","Somerset","Plymouth","Exeter">
  <meta property="og:title" content="Certified Translation and Professional Interpreter" />
  <meta property="og:description" content="Language Services UK is leading translation service provider that has and will provide certifed translator and professional Interpreter each time you would want to communicate with global markets and audiences. we will meet your expectations" />
  <meta property="fb:app_id" content="" />
  <meta property="og:image" content="http://www.lsuk.org/images/logo.png" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="http://www.lsuk.org" />
  <meta property="og:site_name" content="lsuk.org" />
  <!-- begin JS -->
  <script src="js/jquery.jcarousel.min.js" type="text/javascript"></script>


  <script type="text/javascript">
    function popupwindow(url, title, w, h) {
      var left = (screen.width / 2) - (w / 2);
      var top = (screen.height / 2) - (h / 2);
      return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
    }

    function MM_openBrWindow(theURL, winName, features) {
      window.open(theURL, winName, features);
    }
  </script>
  <style>
    #home{
        width: 20.315rem;height:12.756rem;
      }
      .card_image{
        width: inherit;position: absolute;top: 0;left: 0;
      }
      .img_and_name{
        margin: 0 auto;width: 4rem;position: relative;top: 3.5rem;right: 1.6rem;
      }
      .prf_pic{
        border-radius: 5rem;width:100%;
      }
      .description{
        position: relative;top: 4.3rem;right: 3rem;width: 100%;text-align: center;
      }
      .intrpName{
        font-size: 0.8rem;line-height: 1.5rem;color: #3d519b;line-height: 1rem;
      }
    @media print {
      body {
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact; 
        print-color-adjust: exact;
      }
      #home{
        width: 20.315rem;height:12.756rem;
      }
      .card_image{
        width: inherit;position: absolute;top: 0;left: 0;
      }
      .img_and_name{
        margin: 0 auto;width: 4rem;position: relative;top: 3.5rem;right: 6.7rem;
      }
      .prf_pic{
        border-radius: 5rem;width:100%;
      }
      .description{
        position: relative;top: 0.5rem;left: 1.5rem;width: 100%;text-align: center;
      }
      .intrpName{
        font-size: 0.8rem;line-height: 1.5rem;color: #3d519b;line-height: 1rem;
      }
      button{
        display: none !important;
      }
    }
  </style>
</head>

<body class="boxed">

            <button onclick="window.print();" type="button" class="btn btn-primary"><i class="fa fa-print"></i> Print Card</button>
              <!-- <div id="home" class="col-md-12 tab-pane fade in active" style="background-image: url(images/interpreter/card22.png);height: 30rem;background-repeat: no-repeat;background-size: contain;background-position-x: center;"> -->
              <div id="home" class="col-md-12 tab-pane fade in active" >
                <img src="images/interpreter/card22.png" alt="card_layout" class="card_image" >
                <div class="row">
                    <div class="col-md-6">
                        <div class="img_and_name" >
                            <img src="<?php echo $photo_path; ?>" alt="Profile_Picture" class="prf_pic text-center" >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="description" >
                            <p class="intrpName" ><?php echo strtoupper($row['name']); ?></p>
                        </div>
                    </div>
                </div>
</body>
</html>