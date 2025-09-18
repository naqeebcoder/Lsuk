<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
function strpos_arr($haystack, $needle) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $what) {
        if(($pos = strpos($haystack, $what))!==false) return 1;
    }
    return false;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Manage Purchase Orders</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style>.multiselect {min-width: 250px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}
.btn_action{padding:8px;}</style>
</head>
<body>
<?php include'db.php'; include'class.php';?>
<div class="container text-center">
<h3>Manage companies emails</h3>
<?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance'){ ?>
<br><br>
    <h4>Companies list using Purchase Orders </h4>  
    <table class="table table-bordered table-hover">
        <thead class="bg-primary">
            <th>S.No</th>
            <th width="20%">Company</th>
            <th>Email</th>
            <th>Invoice Email</th>
            <th>Purchase Order Email</th>
            <th>Action</th>
        </thead>
        <tbody id="append_childs">
    <?php $result = $acttObj->read_all("id,name,abrv,email,invEmail,po_email,po_req","comp_reg","(status!='Company Seized trading in' OR status!='Company Blacklisted') and deleted_flag=0 ORDER BY comp_reg.name ASC");
    if($result->num_rows==0){
        echo '<tr><td align="centre"><h3 class="text-center"><span class="label label-danger">There are no companies in this list!</span></h3></td></tr>';
    }else{
        $td_counter=1;
        while($row = $result->fetch_assoc())
                {
                  ?>
                  <tr>
                  <td align="left"><?php echo $td_counter++;?> </td>
                  <td align="left" class="small"><?php echo $row['name']." (".$row['abrv'].")";?> </td>
                  <td align="left"><input <?php if(strpos_arr($row['email'], array("Enter",strtolower("NA"),strtolower("onfirmation"),"ourEmail","---","Booking","Person","Not","enter",strtolower("Test"))) || strlen($row['email'])<=5){ ?>style="background: #ff000030;"<?php } ?> type="text" value="<?php echo $row['email']; ?>" class="form-control email"></td>
                  <td align="left"><input <?php if(strpos_arr($row['invEmail'], array("Enter",strtolower("NA"),strtolower("onfirmation"),"ourEmail","---","Booking","Person","Not","enter",strtolower("Test"))) || strlen($row['invEmail'])<=5){ ?>style="background: #ff000030;"<?php } ?> type="text" value="<?php echo $row['invEmail']; ?>" class="form-control invEmail"></td>
                  <td align="center"><?php if($row['po_req']==1){ ?><input <?php if(strpos_arr($row['po_email'], array("Enter",strtolower("NA"),strtolower("onfirmation"),"ourEmail","---","Booking","Person","Not","enter",strtolower("Test"))) || strlen($row['po_email'])<=5){ ?>style="background: #ff000030;"<?php } ?> type="text" value="<?php echo $row['po_email']; ?>" class="form-control po_email"><?php }else{ ?><small class="text-danger">PO not required!</small><input type="hidden" value="" class="po_email"><?php } ?></td>
                  <td align="left"> 
                    <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ) { ?>
                      <a class="btn btn-primary btn_action" title="Click to update this record" href="javascript:void(0)" id="<?php echo $row['id']; ?>" onclick="update_comp_emails(this)">
                        <i class="glyphicon glyphicon-refresh"></i>
                      </a>
                      <?php 
                    } ?>
                  </td>
                </tr>
                <?php 
                } 
    }?>
    </tbody>
  </table><br><br><br>
  <?php } ?>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script>
function update_comp_emails($elem){
var update_comp_id=$($elem).attr('id');
var email=$($elem).parents('td').parents('tr').find('.email').val();
var invEmail=$($elem).parents('td').parents('tr').find('.invEmail').val();
var po_email=$($elem).parents('td').parents('tr').find('.po_email').val();
$.ajax({
    url:'ajax_add_comp_data.php',
    method:'post',
    dataType:'json',
    data:{update_comp_id:update_comp_id,email:email,invEmail:invEmail,po_email:po_email,type:"update_comp_emails"},
    success:function(data){
        if(data['result']==1){
            $($elem).removeClass("btn-primary");
            $($elem).addClass("btn-success");
            $($elem).html("<i class='glyphicon glyphicon-ok'></i>");
        }
    }, error: function(xhr){
        alert("An error occured: " + xhr.status + " " + xhr.statusText);
    }
});
}
  function valid_email(element) {
    var expr = /^([\w-\.']+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

    if (!expr.test($(element).val())) {
        alert('Kindly enter a valid email!');
        $(element).focus();
    }
  }
  $('.email,.invEmail,.po_email').keyup(function() {
  this.value = this.value.replace(/\s/g,'');
  });
  $('.email,.invEmail,.po_email').change(function(){
      valid_email(this);
  });
</script>
</body>
</html>