<?php include'source/db.php';session_start();
include 'source/class.php';
$table='interpreter_reg';$interp_code=$_SESSION['interp_code'];
$query="SELECT * FROM interpreter_reg where code='$interp_code'";
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$interp_id=$row['id'];
$interp_pix=$row['interp_pix']?:"profile.png";
$image_path='lsuk_system/file_folder/interp_photo/'.$interp_pix;
if(isset($_POST['submit']) && !empty($_POST['profile_photo_data'])){
  $image_parts = explode(";base64,", $_POST['profile_photo_data']);
  $image_type_aux = explode("image/", $image_parts[0]);
  $image_type = strtolower($image_type_aux[1]);
  $allowed_types = array('png','jpeg','jpg'); 
  if(in_array($image_type, $allowed_types)){
  $image_base64 = base64_decode($image_parts[1]);
  $photo_name = round(microtime(true)).'.'.$image_type;
  error_reporting(0);
  if(!empty($image_base64)){
    $old_file='lsuk_system/file_folder/interp_photo/'.$interp_pix;
    if(file_exists($old_file) && $interp_pix!="profile.png"){
      unlink($old_file);
    }
    file_put_contents("lsuk_system/file_folder/interp_photo/".$photo_name, $image_base64);
    $acttObj->editFun($table,$interp_id,'interp_pix',$photo_name);
    $acttObj->editFun($table,$interp_id,'pic_updated',1);?>
    <script>
      window.opener.location.reload(true);
      window.close();
    </script>
<?php }
  }else{ ?>
    <script>
      alert("Only jpg,png,jpeg file extensions are allowed to upload!");
      window.close();
    </script>
<?php }
} ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<style>
      .cropit-preview {
        background-color: #f8f8f8;
        background-size: cover;
        border: 1px solid #ccc;
        border-radius: 3px;
        margin-top: 7px;
        width: 250px;
        height: 250px;
      }

      .cropit-preview-image-container {
        cursor: move;
      }

      .image-size-label {
        margin-top: 10px;
      }

      input {
        display: block;
      }

      

      #result {
        margin-top: 10px;
        width: 900px;
      }

      #result-data {
        display: block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        word-wrap: break-word;
      }
    </style>
<div align="center">
  <form action="" method="post" enctype="multipart/form-data">
      <div class="col-md-12 alert alert-danger text-left" role="alert">
      <b>Note: </b>Please upload or take a camera facing photo.
      <br>Don't cover your face with mask or anything else. Images in sunglasses aren't acceptable too.
      <br>Use a square or passport size photo with clear background.
      <br>Please make sure that it's only you in the photo and you are facing the camera. The maximum size of this photo needs to be <b><u>1MB.</u></b>
      <br>Allowed file types: jpg | png | jpeg
      </div>
      <label style="margin-top:-12px;" for="profile_photo">Upload Profile Photo <i title="Select a clear square photo of yourself facing towards camera" class="fa fa-question-circle"></i></label>
      <div class="image-editor col-md-8">
        <input style="width:270px" type="file" name="profile_photo" id="profile_photo" class="form-control cropit-image-input" accept="image/*" required/>
        <div class="cropit-preview"></div>
        <label class="image-size-label" for="ranger">Resize image</label>
        <input id="ranger" type="range" class="cropit-image-zoom-input" style="width:50%;height: 40px;">
        <input type="hidden" name="profile_photo_data" class="profile_photo_data" />
        <button class="btn btn-primary" type="submit" name="submit">Update Now &raquo;</button>
      </div>
    </form>
		
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script src="js/jquery.cropit.js"></script>
<script>
/*function max_upload($element){
	if($element.files[0].size > 1572864){
		alert("File is too big ! Upload upto 1.5 MB file");
		$element.value = "";
	}else{
		return "1";
	}
}
var _URL = window.URL || window.webkitURL;
$("#profile_photo").change(function(e) {
	if(max_upload(this)=='1'){
		var file, img;
		var output = document.getElementById('output');
		if ((file = this.files[0])) {
			img = new Image();
			img.onload = function() {
				if(this.width!=this.height){
					alert("You must upload a square passport size photo like 200x200 or 400x400");
					$("#profile_photo").val('');
				}else{
					output.src = _URL.createObjectURL(file);
				}
			};
			img.onerror = function() {
				alert("Uploaded file not a valid photo !");
				$("#profile_photo").val('');
			};
			img.src = _URL.createObjectURL(file);
		}
	}
});*/
$(function() {
  $('.image-editor').cropit({
    imageState: {
      src: "<?php echo $image_path; ?>",
    },
  });

  $('form').submit(function() {
    // Move cropped image data to hidden input
    var imageData = $('.image-editor').cropit('export');
    $('.profile_photo_data').val(imageData);
    // Print HTTP request params
    var formValue = $(this).serialize();

  });
});
</script>