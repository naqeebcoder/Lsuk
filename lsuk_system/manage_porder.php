<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Manage Purchase Orders</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style>.multiselect {min-width: 250px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}</style>
</head>
<body>
<?php include'db.php'; include'class.php';
if(isset($_POST['btn_add_porder'])){
    $table="comp_reg";
    $sup_parents=implode(',',$_POST['sup_parents']);
    $selector=$_POST['selector'];
    if($selector=='parent'){
        $all_abrv=$acttObj->query_extra("GROUP_CONCAT(child_comp) as all_idz","child_companies","parent_comp IN ($sup_parents)","set SESSION group_concat_max_len=10000");
        $items=$all_abrv['all_idz'];
    }else{
        $items=implode(',',$_POST['selected_companies']);
    }
    $counter=0;$done=0;
    $comp_id=explode(',',$items);
        while($counter<count($comp_id)){
            $chk_exist = $acttObj->read_specific("po_req","$table","id=".$comp_id[$counter]);
            if($chk_exist['po_req']=='0'){
                $acttObj->editFun($table,$comp_id[$counter],'po_req','1');
                $acttObj->editFun($table,$comp_id[$counter],'po_email',$_POST['po_email']);
                if($counter==count($comp_id)-1){$done=1;}
            }else{
                $acttObj->editFun($table,$comp_id[$counter],'po_email',$_POST['po_email']);
                $done=1;
            }
            $counter++;
        }
        if($done==1){
            echo "<script>alert('Purchase Order record updated for selected companies.');</script>";
        }
}
?>
<div class="container text-center">
<h4>Select companies from dropdown to use Purchase Orders</h4><br/>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="row" enctype="multipart/form-data">
<?php if($_SESSION['prv']=='Management'){ ?>
        <div class="form-group col-md-2 col-sm-3">
                 <select id="selector" onchange="changable()" class="form-control" name="selector">
                     <option value='individual'>Individual</option>
                     <option value='parent'>Parent</option>
			    </select>
        </div>
          <div style="display:none;" id="div_parent" class="form-group col-md-4 col-sm-6">
                 <select id='sup_parents' name="sup_parents[]" multiple="multiple" class="form-control multi_class">
					<?php include 'multiselectcompgrp.php'; ?>
			</select>
          </div>
          <div style="display:flex;" id="div_single" class="form-group col-md-4 col-md-6">
                <select class="multi_class" id="individual_companies" name="selected_companies[]"  multiple="multiple">
                        <?php $result_opt=$acttObj->read_all("distinct comp_reg.id,comp_reg.name,comp_reg.abrv","comp_reg","(status!='Company Seized trading in' OR status!='Company Blacklisted') and deleted_flag=0 and po_req=0 and id NOT IN (select child_comp from child_companies) and id NOT IN (select sup_child_comp from parent_companies) ORDER BY comp_reg.name ASC");
                        while($row_opt=mysqli_fetch_assoc($result_opt)){ ?>
                        <option value="<?php echo $row_opt['id']; ?>"><?php echo $row_opt['name']; ?></option>
                        <?php } ?>
                    </select>
            </div>
          <div class="form-group col-md-4 col-sm-7">
                 <input type="text" class="form-control po_email" name="po_email" placeholder="Enter purchase order email"/>
          </div>
          
          <div class="form-group col-md-2 col-sm-2">
                 <button type="submit" class="btn btn-primary" name="btn_add_porder"><i class="fa fa-check-circle"></i> Submit</button>
          </div>
        <?php } ?>
</form>
<br><br>
    <h4>Companies list using Purchase Orders </h4>
           
    <table class="table table-bordered table-hover">
        <thead class="bg-info">
            <tr>
                <td>S.No</td>
                <td>Company</td>
                <td>PO# Email</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody id="append_childs">
    <?php $result = $acttObj->read_all("distinct comp_reg.id,comp_reg.name,comp_reg.abrv,comp_reg.po_email","comp_reg","(status!='Company Seized trading in' OR status!='Company Blacklisted') and deleted_flag=0 and po_req=1 ORDER BY comp_reg.name ASC");
    if(mysqli_num_rows($result)==0){
        echo '<tr><td align="centre"><h3 class="text-center"><span class="label label-danger">There are no companies in this list!</span></h3></td></tr>';
    }else{
        $td_counter=1;
        while($row = mysqli_fetch_assoc($result)){ ?>
                  <tr>
                  <td align="left"><?php echo $td_counter++; ?> </td>
                  <td align="left"><?php echo $row['name']; ?> </td>
                  <td align="left"><?php echo $row['po_email']; ?> </td>
                  <td align="left"> 
                    <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ) { ?>
                      <a title="Click to remove Purchase order" href="javascript:void(0)" id="<?php echo $row['id']; ?>" onclick="if(confirm('Are you sure to remove from list?')){remove_porder(this);}">
                        <i class="glyphicon glyphicon-trash"></i>
                      </a>
                      <?php 
                    } ?>
                  </td>
                </tr>
            <?php } 
        } ?>
    </tbody>
  </table><br><br><br>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
    $(function() {
	    $('.multi_class').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
        $('.table').DataTable({
        'iDisplayLength': 50
        });
    });
    function changable(){
        var value=document.getElementById("selector").value;
        if(value=='parent'){
            $('#div_single').css('display','none');
            $('#div_parent').css('display','flex');
        }else{
            $('#div_single').css('display','flex');
            $('#div_parent').css('display','none');
        }
    }
    function remove_porder(elem){
    var remove_comp_id=elem.id;
        $.ajax({
            url:'ajax_add_comp_data.php',
            method:'post',
            data:{remove_comp_id:remove_comp_id,action:"remove_porder"},
            success:function(response){
            //$('#append_childs').html(data);
                if(response=="1"){
                    window.location.reload();
                }else{
                    alert('Failed to remove from list!');
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
  $('.po_email').keyup(function() {
  this.value = this.value.replace(/\s/g,'');
  });
  $('.po_email').change(function(){
      valid_email(this);
  });
</script>
</body>
</html>