<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){session_start();}
include 'db.php';
include'class.php';
$id=$_GET['id']?:1;
$res=$acttObj->read_specific("*","timesheet_policy","id=".$id);
$type=$res['type'];
$html=$res['html'];
if(isset($_POST['btn_edit_policy'])){
		$policy_id=$_POST['policy_id'];
		$html=mysqli_escape_string($con,$_POST['html']);
    if($acttObj->update('timesheet_policy',array('html'=>$html),array('id'=>$policy_id))){
      	$msg='<div class="alert alert-success alert-dismissible col-md-6 col-md-offset-3">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
      <strong> Success <i class="glyphicon glyphicon-ok-sign"></i></strong> '.$type.' policy has been updated successfully. 
      </div>';
    	}else{ echo $acttObj->error();
		   $msg='<div class="alert alert-danger alert-dismissible col-md-4 col-md-offset-4">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times; </a>
      <strong> Error <i class="glyphicon glyphicon-exclamation"></i> !</strong> Failed to update this policy. Try again later! 
      </div>';
    	}
} ?>
<!doctype html>
<html lang="en">
<head>
  <title>Timesheet policy management</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <style>.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {color: #fbfbfb;background-color: #337ab7;}</style>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
    function popupwindow(url, title, w, h) {
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
    }
  var pageURL = '<?php echo basename(__FILE__);?>';
	function changer(element){
	window.location.href="timesheet_policy.php?id="+$(element).val();}</script>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
        <div class="col-sm-12"><br>
            <h3 class="txt-pr text-center">Update contents for <?php echo $type; ?> timesheet policy</h3><br>
            <div class="col-md-12"><?php if(isset($msg) && !empty($msg)){echo $msg;} ?></div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$id;?>" autocomplete="off" method="post" novalidate="novalidate" enctype="multipart/form-data" class="row">
                <div class="form-group col-md-3 col-sm-6">
                    <label>Select Page to Edit</label>
                    <select name="type" class="form-control" onchange="changer(this);">
                        <option value="<?php echo $id; ?>"><?php echo $type.' Policy'; ?></option>
                        <option disabled>--- Select Type ---</option>
                <?php //get all pages links
                $q_pages=$acttObj->read_all("*","timesheet_policy","id!=".$id);
                while($res=$q_pages->fetch_assoc()){
                $policy_id=$res['id'];
                $type=$res['type'];
                echo "<option value='".$policy_id."'>".$type." Policy</option>";
                } ?>
                    </select>
                </div>
                <div class="form-group col-sm-12">
                    <input type="hidden" name="policy_id" value="<?php echo $id; ?>">
                    <textarea name="html" id="html" cols="51" rows="4"><?php echo $html; ?>
                    </textarea>
                </div>
                <div class="form-group col-sm-12">
                    <button name="btn_edit_policy" type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-upload"></i>&nbsp;
                                Update Policy
                        </button>
                        <a href="timesheet_policy.php"><button class="btn btn-warning" type="button" name="btncancel" >Cancel <i class="glyphicon glyphicon-remove-circle"></i></button></a>
                </div>
            </form>
			</div>
		</div>
    </div>
</body>
<script src="https://cdn.rawgit.com/davidstutz/bootstrap-multiselect/master/dist/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script src="https://cdn.tiny.cloud/1/1cuurlhdv50ndxckpjk52wu6i868lluhxe90y7xesmawusin/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script type="text/javascript">
tinymce.init({
  selector: "#html",
  height:   300,
  plugins: 'print preview   searchreplace autolink autosave save directionality  visualblocks visualchars fullscreen image link media  template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount   imagetools textpattern noneditable help  ',
  toolbar: 'undo redo | link image | code',
  image_title: true,
  automatic_uploads: true,
  file_picker_types: 'image media',
  file_picker_callback: function (cb, value, meta) {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.onchange = function () {
    var file = this.files[0];
    var reader = new FileReader();
    reader.onload = function () {
    var id = 'blobid' + (new Date()).getTime();
        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
        var base64 = reader.result.split(',')[1];
        var blobInfo = blobCache.create(id, file, base64);
        blobCache.add(blobInfo);
        cb(blobInfo.blobUri(), { title: file.name });
      };
      reader.readAsDataURL(file);
    };
    input.click();
  }
});
</script>
</html>