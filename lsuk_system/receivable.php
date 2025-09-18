<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){
	session_start();
}
include'db.php';
include'class.php';  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>Add New Receivable</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />

<?php include'ajax_uniq_fun.php'; ?>
<script src="js/jquery-1.5.2.min.js" type="text/javascript"></script>
<script>
 
$(document).ready(function() {  

 $("#receivable_id").change( function(){
  var receivable_id= $(this).find("option:selected").text();
  var loans= receivable_id.trim();
  $("#loanvalue").val(loans);
  if(loans=='Loans'){
   
    $("#loan").show();
  }else{
    $("#loan").hide();
  }

  });
});
</script>
</head>
<body>
    <div class="container">
<?php 
        if(isset($_POST['submit'])){
         
        $table='receivable';
        $amount=$_POST['amount']?:0;  
        $details=$_POST['details']; 
        $given_by=$_POST['given_by']; 
        $received_by=$_SESSION['userId']; 
        $received_date=$_POST['received_date']; 
        $terms=$_POST['terms']; 
        $installments=$_POST['installments']; 
        $receivable_id=$_POST['receivable_id']; 

        if(trim($_POST['loanvalue'])=='Loans'){
           $balance=$_POST['amount'];

        }else{
           $balance=0;

        }


        $lastid = $acttObj->insert($table,array('amount'=>$amount,
                                   'details'=>$details,
                                   'given_by'=>$given_by,
                                   'received_by'=>$received_by,
                                   'received_date'=>$received_date,
                                   'terms'=>$terms,
                                   'balance'=>$balance,
                                   'receivable_id'=>$receivable_id, 
                                   'attachment'=>'' 
                                    ));
        

        if(isset($_FILES["file"]["name"]) and $_FILES["file"]["name"]!=''){

        $picName=$acttObj->upload_file("receivable",$_FILES["file"]["name"],$_FILES["file"]["type"],$_FILES["file"]["tmp_name"],$_FILES["file"]['name']);


          //$acttObj->editFun($table,$edit_id,'interp_pix',$picName);

          $acttObj->editFun($table,$lastid,'attachment',$_FILES["file"]['name']);

        $attachment=$_FILES["file"]["name"];
          }else{
            $attachment = '';
          }


       // $acttObj->new_old_table('hist_'.$table,$table,$edit_id); ?>
         
         <script>
            alert('New Receivable has been added successfuly.');
            var loadFile = function (event) {
            var output = document.getElementById('output');
            location.reload();
            //output.src = URL.createObjectURL(event.target.files[0]);
            };
           
            function refreshParent(){
              window.opener.location.reload();
            }
        </script>

        <?php } ?>
<form action="" method="post" class="register" id="signup_form" name="signup_form" onsubmit="return formSubmit()" enctype="multipart/form-data">
  <h1>Enter Receivable Details</h1>
    <input type="hidden" name="loanvalue" id="loanvalue" value="0">
    <!--<p><center>
    <img id="output" src="images/default.png" title="Kindly add Expense slip picture(if any)" name="output" class="img-thumbnail img-responsive" style="max-width: 140px;max-height: 140px;min-width: 140px;min-height: 140px;" /><p></p>
    <input type="file" name="interpreterphoto" value="images/default.png" accept="image/*" onchange="loadFile(event)" id="interpreterphoto" style='width: 25%;float: none;'></center>
    </p>-->
    <div class="form-group col-sm-6">
      <label> Given By * </label>
      <input name="given_by" type="text" class="form-control" placeholder='' required='' id="given_by"/>
    </div>
   
    <div class="form-group col-sm-6">
      <label>Received Date * </label>
      <input class="form-control" name="received_date" type="date" placeholder='' required='' id="received_date" />
    </div>
    <div class="form-group col-sm-4">
      <label>Receivable Type * </label>

      <select class="form-control" name="receivable_id" id="receivable_id" required>
       <?php 			
        $sql_opt="SELECT id,title FROM receivable_types ORDER BY title ASC";
        $result_opt=mysqli_query($con,$sql_opt);
        $options="";
        while ($row_opt=mysqli_fetch_array($result_opt)) {
            $exp_id=$row_opt["id"];
            $name_opt=$row_opt["title"];
            $options.="<OPTION value='$exp_id'>".$name_opt;}?>
                    <option value="">Select Expense Type</option>
                    <?php echo $options; ?>
                    </option>
                  </select>
    </div>
    <div class="form-group col-sm-4">
      <label>Amount * </label>
      <input class="form-control" name="amount" type="text" placeholder='' required='' id="amount" />
    </div>
    <div class="form-group col-sm-4">
      <label>Attachment  </label>
      <input class="form-control" name="file" type="file" id="attachment" />
    </div>
    <div class="form-group col-sm-12">
      <textarea class="form-control" name="details" rows="3"  placeholder='Write Receivable details here ...' id="details" ></textarea>
    </div>
    <div id="loan" style="display: none">
    <div class="form-group col-sm-6 col-xs-6">
      <label>Terms</label>
      <input class="form-control" name="terms" type="number"  placeholder='' required='' id="terms"  value="0"/>
    </div>
    <div class="form-group col-sm-6 col-xs-6">
      <label>Installments</label>
      <input class="form-control" name="installments" type="number"  placeholder='' required='' id="installments"  value="0"/>
    </div>
  </div>
   
    <div class="form-group col-sm-4 col-xs-12"><br>
    <button class="btn btn-primary" type="submit" name="submit" onclick="return formSubmit(); return false">Submit Receivable <i class="glyphicon glyphicon-ok-sign"></i></button>
  </div>
</form>
</div>
</body>
</html>