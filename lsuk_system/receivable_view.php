<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$table = 'receivable';
$allowed_type_idz = "108";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Receivable</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$view_id= @$_GET['view_id'];

$query="SELECT receivable.*,receivable_types.title FROM receivable,receivable_types WHERE receivable.receivable_id=receivable_types.id  and receivable.id=$view_id";
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>View Receivable</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <style type="text/css">
    .lable_new{width: 100%;
    font-size: 14px;
    font-weight: normal;
    background: #eee;
    padding: 5px 10px;
    border-radius: 5px;}
  </style>
</head>
<body>
    <div class="container">
  <form action="" method="post" class="register" id="signup_form" name="signup_form"  enctype="multipart/form-data">
  <h1 style="margin-bottom: 30px">View Receivable Details</h1>
   
   <?php if(!empty($row['given_by'])){?>   
    <div class="form-group col-sm-6" >
      <label> Given By </label>
       
      <label class="lable_new"><?php echo $row['given_by'];?></label>
    </div>
  <?php } ?>

    <?php if(!empty($row['received_date'])){?>  
   
    <div class="form-group col-sm-6">
      <label>Received Date  </label>
      
      <label class="lable_new"><?php echo $row['received_date'];?></label>
    </div>
    <?php } ?>

    <?php if(!empty($row['receivable_id'])){?>  
    <div class="form-group col-sm-4">
      <label>Receivable Type  </label>
      
      
       <?php      
        $sql_opt="SELECT id,title FROM receivable_types ORDER BY title ASC";
        $result_opt=mysqli_query($con,$sql_opt);
        $options="";
       $name_opt="";
        while ($row_opt=mysqli_fetch_array($result_opt)) {
             $select='';
            $exp_id=$row_opt["id"];
            
            
            if($exp_id==$row['receivable_id']){
              $select = "selected";
              $name_opt=$row_opt["title"];
            }
            }?>
            <label class="lable_new"><?php echo $name_opt;?></label>
          
    </div>
    <?php } ?>

      <?php if(!empty($row['amount'])){?>  

    <div class="form-group col-sm-4">
      <label>Amount  </label>
      <label class="lable_new"><?php echo $row['amount'];?> </label>
    </div>
    <?php } ?>


      <?php if(!empty($row['attachment'])){?>  
    <div class="form-group col-sm-4">

            <?php 
      if($row['attachment']!=''){      
      ?>

      <label>Attachment  </label>
       <label class="lable_new"><a href="file_folder/receivales/<?php echo $row['attachment']?>"><?php echo $row['amount'];?> </label>
    <?php } ?>

    </div>

    <?php } ?>

      <?php if(!empty($row['details'])){?>  
    <div class="form-group col-sm-12">
       <label>Details  </label>
     <label class="lable_new"><?php echo $row['details'];?></label>
    </div>
     <?php } ?>


    <div id="loan" <?php if($row['title']!='Loans'){?>  style="display: none" <?php }?>>
    
      <?php if(!empty($row['terms'])){?>  

    <div class="form-group col-sm-6 col-xs-6">
      <label>Terms</label>
       
       <label class="lable_new"><?php echo $row['terms'];?></label>
    </div>
     <?php } ?>

      <?php if(!empty($row['installments'])){?>  

    <div class="form-group col-sm-6 col-xs-6">
      <label>Installments</label>
  
      <label class="lable_new"><?php echo $row['installments'];?></label>
    </div>
     <?php } ?>
  </div>
   
</form>
</div>
</body>
</html>