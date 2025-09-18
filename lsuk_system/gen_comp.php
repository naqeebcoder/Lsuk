<?php
ob_start();
include "userhaspage.php";
?>
<?php 
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 

include 'db.php';
include 'class.php';
include_once ('function.php');
$table='com_reg';
$title=@$_GET['title'];
$search_2=@$_GET['search_2'];
$search_3=@$_GET['search_3'];
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 20;
$startpoint = ($page * $limit) - $limit;
$tp = @$_GET['tp'];
$array_tp=array('a'=>'Active','tr'=>'Trashed');
$page_title=$array_tp[$tp]=='Active'?'':$array_tp[$tp];
$deleted_flag=$tp=='tr'?'deleted_flag = 1':'deleted_flag = 0';
$class=$tp=='tr'?'alert-danger':'alert-info'; ?>
<!doctype html>
<html lang="en">
<head>
<title>Fix Supplier Names</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

<style>.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 4px!important;cursor:pointer;}html, body {background: #fff !important;}.div_actions{position: absolute;margin-top: -48px;background: #ffffff;border: 1px solid lightgrey;}.alert{padding: 6px;}.div_actions .fa {font-size: 14px;}.w3-btn, .w3-button {padding: 8px 10px!important;}</style>
</head>
<script>
function myFunction() {
	 var x = document.getElementById("title").value;if(!x){x="<?php echo $title; ?>";}
	//  var y = document.getElementById("search_2").value;if(!y){y="<?php echo $search_2; ?>";}
	//  var z = document.getElementById("search_3").value;if(!z){z="<?php echo $search_3; ?>";}
	//  var tp = document.getElementById("tp").value;if(!tp){tp="<?php echo $tp; ?>";}
	 window.location.href="fix_sup.php" + '?title=' + encodeURIComponent(x) ;
	 
}
</script>
<?php
if(isset($_GET['submit'])){
    $p_title=$_GET['p_title'];
    $u_title=$_GET['u_title'];
    $s_title=$_GET['s_title'];
    $parent_unit=$_GET['parent_unit']?'1':'0';
    print_r($parent_unit);
    echo "Parent: <br>";
    print_r($p_title);
    echo "<br>Subs: <br>";

    print_r($s_title);
    // die();exit();
    if($parent_unit==0){
        if($p_title!='' && !empty($s_title)){
            $mk_pr = mysqli_query($con,"UPDATE comp_reg_test set comp_nature=1 WHERE id=$p_title");
            $dated = date('Y-m-d H:i');
            $sl_dl = implode(",",$s_title);
            // echo $sl_dl;
            // die();exit();
            $dl_p = mysqli_query($con, "DELETE FROM subsidiaries WHERE parent_comp=$p_title AND child_comp NOT IN ($sl_dl)");
            foreach ($s_title as $st) {
                $chk_exs = $acttObj->read_specific("id","subsidiaries"," parent_comp=$p_title AND child_comp=$st")['id'];
                if(!$chk_exs || empty($chk_exs)){
                    $mk_sb = mysqli_query($con,"INSERT INTO subsidiaries(parent_comp,child_comp,dated) VALUES('{$p_title}','{$st}','{$dated}') ");
                    $mk_sb2 = mysqli_query($con,"UPDATE comp_reg_test set comp_nature=3 WHERE id=$st");
                }
                
            }        
        }
    }elseif($parent_unit==1){
        if($p_title!='' && $u_title!='' && !empty($s_title)){
            $mk_pr = mysqli_query($con,"UPDATE comp_reg_test set comp_nature=1 WHERE id=$p_title");
            $mk_uh = mysqli_query($con,"UPDATE comp_reg_test set comp_nature=2 WHERE id=$u_title");
            $dated = date('Y-m-d H:i');
            $chk_sb = mysqli_query($con, "SELECT * FROM subsidiaries WHERE parent_comp=$p_title");
            if(mysqli_num_rows($chk_sb)>0){
                $mk_uh2 = mysqli_query($con,"UPDATE comp_reg_test set comp_nature=2 WHERE id=$u_title");
                $mk_sb33 = mysqli_query($con,"UPDATE subsidiaries SET unit_head=0 WHERE unit_head='{$u_title}' AND parent_comp='$p_title' ");

                $sl_dl = implode(",",$s_title);
                // foreach ($s_title as $st) {
                //     $chk_exs = $acttObj->read_specific("id","subsidiaries"," parent_comp=$p_title AND child_comp=$st")['id'];
                //     if(!$chk_exs || empty($chk_exs)){
                //         $mk_sb = mysqli_query($con,"INSERT INTO subsidiaries(parent_comp,child_comp,dated) VALUES('{$p_title}','{$st}','{$dated}') ");
                //         $mk_sb2 = mysqli_query($con,"UPDATE comp_reg_test set comp_nature=3 WHERE id=$st");
                //     }
                // }    

                foreach ($s_title as $st) {
                    $chk_exs = $acttObj->read_specific("id","subsidiaries"," parent_comp=$p_title AND child_comp=$st")['id'];
                    if(!$chk_exs || empty($chk_exs)){
                        $mk_sb = mysqli_query($con,"INSERT INTO subsidiaries(parent_comp,unit_head,child_comp,dated) VALUES('{$p_title}','{$u_title}','{$st}','{$dated}') ");
                    }else{
                        $mk_sb = mysqli_query($con,"UPDATE subsidiaries SET unit_head = '$u_title' WHERE parent_comp='$p_title' AND child_comp='$st'");
                    }
                    $mk_sb2 = mysqli_query($con,"UPDATE comp_reg_test set comp_nature=3 WHERE id=$st");
                }   
            }else{
                $mk_uh2 = mysqli_query($con,"UPDATE comp_reg_test set comp_nature=2 WHERE id=$u_title");
                foreach ($s_title as $st) {
                    $mk_sb = mysqli_query($con,"INSERT INTO subsidiaries(parent_comp,unit_head,child_comp,dated) VALUES('{$p_title}','{$u_title}','{$st}','{$dated}') ");
                    $mk_sb2 = mysqli_query($con,"UPDATE comp_reg_test set comp_nature=3 WHERE id=$st");
                }   
            }
            // $dl_p = mysqli_query($con, "DELETE FROM subsidiaries WHERE parent_comp=$p_title");
                 
        }
    }
    
    // $new_sup_name= mysqli_real_escape_string($con, $_GET['new_sup_name']);
    // $qu = mysqli_query($con,'UPDATE expence SET comp="'.$new_sup_name.'" WHERE comp="'.$title.'"');
	// if($qu){
	// 	header('Location:fix_sup.php');
	// }else{
	// 	echo "<h1>Error Updating the Record</h1>";
	// }
    header('Location:gen_comp.php');
}
?>
<?php include 'header.php'; ?>
<body>    
<?php include 'nav2.php';?>
<!-- end of sidebar -->
	<style>.tablesorter thead tr {background: none;}</style>
<section class="container-fluid" >
<div class="col-md-12">
		<header>
		    <div class="alert <?php echo $class; ?> col-md-2">
                <a href="<?php echo basename(__FILE__);?>" class="alert-link"><?php echo $page_title; ?> Parent/Subs Maker</a>
              </div>
</div>
<div class="row">
<form action="" method="GET">

            
         <div class="col-md-8">
             <div class="form-group col-md-3 col-sm-4">
                <label for="p_title">Select Parent Comapny</label>
        <select id="p_title" name="p_title"  class="form-control searchable">
    <?php 			
$sql_opt="SELECT id,name as title  FROM comp_reg_test WHERE deleted_flag=0 AND comp_nature IN (1,4) ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["title"];
    $id=$row_opt["id"];
    $name_opt=$row_opt["title"];
	// $title=urldecode($title);
    $options.='<OPTION value="'.$id.'" '.((isset($_GET['p_title']) && $_GET['p_title']==$id)?'selected':'').'>'.((!empty($name_opt))?$name_opt:'Empty');
}
?>
    
    <option value="">Select Parent Comapny</option>
    <?php echo $options; ?>
    </option>
  </select>
	        </div>
            <div class="div col-md-2">
                <label for="parent_unit">Select the Head Type
            <input onchange="if(this.checked) {$('#unit_head').removeClass('hidden');}else{$('#unit_head').addClass('hidden');}"  type="checkbox" id="parent_unit" name="parent_unit" <?php echo (isset($_GET['u_title'])?'checked':''); ?> value="1" data-toggle="toggle" data-on="Unit Head" data-off="Parent" > </label>
            </div>


                    <div class="form-group col-md-3 col-sm-4 <?php echo (!isset($_GET['u_title'])?'hidden':''); ?> " id="unit_head">
                        <label for="u_title">Select Unit Head</label><br>
                <select id="u_title" name="u_title"  class="form-control searchable">
            <?php 			
        $sql_opt="SELECT id,name as title  FROM comp_reg_test WHERE deleted_flag=0 AND comp_nature IN (2,4) ORDER BY name ASC";
        $result_opt=mysqli_query($con,$sql_opt);
        $options="";
        while ($row_opt=mysqli_fetch_array($result_opt)) {
            $code=$row_opt["title"];
            $id=$row_opt["id"];
            $name_opt=$row_opt["title"];
            // $title=urldecode($title);
            $options.='<OPTION value="'.$id.'" '.((isset($_GET['u_title']) && $_GET['u_title']==$id)?'selected':'').'>'.((!empty($name_opt))?$name_opt:'Empty');
        }
        ?>
            
            <option value="">Select Unit Head</option>
            <?php echo $options; ?>
            </option>
        </select>
        </div>

<?php if(isset($_GET['u_title'])){
    $sb_str = mysqli_fetch_assoc(mysqli_query($con,"SELECT GROUP_CONCAT(child_comp) as sub_ids FROM subsidiaries WHERE unit_head='".$_GET['u_title']."'"))['sub_ids'];
    $sb_q = explode(",",$sb_str);
    ?>
            <div class="form-group col-md-3 col-sm-4">
                <label for="s_title">Select Subsidiaries</label><br>
        <select id="s_title" multiple name="s_title[]"  class="form-control searchable">
    <?php 			
$sql_opt="SELECT id,name as title  FROM comp_reg_test WHERE deleted_flag=0 AND comp_nature IN (3,4) ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["title"];
    $id=$row_opt["id"];
    $name_opt=$row_opt["title"];
	// $title=urldecode($title);
    $options.='<OPTION value="'.$id.'" '.((!empty($sb_q) && in_array($id,$sb_q))?'selected':'').'>'.((!empty($name_opt))?$name_opt:'Empty');
}
?>
    
    <option value="">Select Subsidiaries</option>
    <?php echo $options; ?>
    </option>
  </select>
	        </div>
            <?php }elseif(isset($_GET['p_title'])){
    $sb_str = mysqli_fetch_assoc(mysqli_query($con,"SELECT GROUP_CONCAT(child_comp) as sub_ids FROM subsidiaries WHERE parent_comp='".$_GET['p_title']."'"))['sub_ids'];
    // $sb_str=$acttObj->query_extra("GROUP_CONCAT(child_comp) as sub_ids","subsidiaries"," parent_comp='".$_GET['p_title']."' ","set SESSION group_concat_max_len=10000")['sub_ids'];
    $sb_q = explode(",",$sb_str);
    ?>
            <div class="form-group col-md-3 col-sm-4">
                <label for="s_title">Select Subsidiaries</label><br>
        <select id="s_title" multiple name="s_title[]"  class="form-control searchable">
    <?php 			
$sql_opt="SELECT id,name as title  FROM comp_reg_test WHERE deleted_flag=0 AND comp_nature IN (3,4) ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["title"];
    $id=$row_opt["id"];
    $name_opt=$row_opt["title"];
	// $title=urldecode($title);
    $options.='<OPTION value="'.$id.'" '.((!empty($sb_q) && in_array($id,$sb_q))?'selected':'').'>'.((!empty($name_opt))?$name_opt:'Empty');
}
?>
    
    <option value="">Select Subsidiaries</option>
    <?php echo $options; ?>
    </option>
  </select>
	        </div>
            <?php } ?>
            <!-- <div class="form-group col-md-3 col-sm-4">
                <input type="text" class="form-control" name="new_sup_name" placeholder="Enter new name of this Supplier Here ..">
            </div> -->
            <button type="submit" class="btn btn-primary" value="submit" name="submit">Submit</button>
            </form>
		</header>
		
		             
	<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
	</div>
	</section>
    <?php if(isset($_GET['p_title'])){
        $p_t = $_GET['p_title']; ?>
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="parent_name"><h1> Parent: <?php echo $acttObj->read_specific("name","comp_reg_test"," id=$p_t")['name'];   ?></h1></div>
            </div>
        </div>
    </section>
    
       <?php if(isset($_GET['p_title'])){
        $p_t = $_GET['p_title']; ?> 
    <section>
        <div class="row">
            <div class="col-md-6">
                <div class="subs_name">
                    <h2 class="sub_head" >Subsidiaries: </h2>
                    <div class="sub_items">
                    <?php $si =1; 
                    // $subs_str =  $acttObj->read_specific("comp_reg_test.name as subs_str","subsidiaries,comp_reg_test"," comp_reg_test.id=subsidiaries.child_comp AND subsidiaries.parent_comp=$p_t ");
                    $subs_str = mysqli_query($con,"SELECT comp_reg_test.name  FROM subsidiaries,comp_reg_test where comp_reg_test.id=subsidiaries.child_comp AND subsidiaries.parent_comp=$p_t ");
                    if(mysqli_num_rows($subs_str)>0){
                        while($crow = mysqli_fetch_assoc($subs_str)){
                            echo "<h3> $si) ".$crow['name']." </h3>";
                            $si++;
                        }
                    }
                    
                    ?>
                    </div>
                </div>
            </div>
            <?php if(isset($_GET['u_title'])){
                $un_h = $_GET['u_title'];
              ?>
            <div class="col-md-6">
            <?php }
    if(isset($_GET['u_title'])){
    ?>
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="unit_name"><h2> Unit Head: <?php echo $acttObj->read_specific("name","comp_reg_test"," id=".$_GET['u_title']."")['name'];  ?> </h2></div>
            </div>
        </div>
    </section>
    <?php }else{
        ?>
        <section>
        <div class="row">
            <div class="col-md-12">
                <div class="unit_name"></div>
            </div>
        </div>
    </section>
        <?php
    } ?>
                <div class="units_name">
                    <div class="unit_items">
                        <?php 
                        $si =1; 
                        // $subs_str =  $acttObj->read_specific("GROUP_CONCAT(comp_reg_test.name) as subs_str","subsidiaries,comp_reg_test"," comp_reg_test.id=subsidiaries.child_comp     AND subsidiaries.unit_head=$un_h ")['subs_str'];
                        // if(!empty($subs_str)){
                        //     $subs = explode(",",$subs_str);
                        //     foreach($subs as $sb_items){
                        //         echo "<h3> $si) $sb_items </h3>";
                        //         $si++;
                        //     }
                        // }
                        $subs_str = mysqli_query($con,"SELECT comp_reg_test.name  FROM subsidiaries,comp_reg_test where comp_reg_test.id=subsidiaries.child_comp     AND subsidiaries.unit_head=$un_h ");
                        if(mysqli_num_rows($subs_str)>0){
                            while($crow = mysqli_fetch_assoc($subs_str)){
                                echo "<h3> $si) ".$crow['name']." </h3>";
                                $si++;
                            }
                        }
                        
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>
    <?php } ?>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
    $(function() {
	    $('.searchable').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
    });
$('.tr_data').click(function(event){
    $('.div_actions').css('display','none');
    $(this).next().css('display','block');
  });
  $(document).on('click','.view_attach',function(e){
      e.preventDefault();
      window.open('exp_receipt_view.php?v_id='+this.id, "popupWindow", "width=600,height=600,scrollbars=yes");
    });
    $(document).on('change','#p_title',function(e){
      e.preventDefault();
      var p_id = this.value;
      window.location.href="gen_comp.php" + '?p_title=' + encodeURIComponent(p_id) ;
    //   $('.parent_name').html('<h1>Parent : '+$('#p_title option:selected').text()+'</h1>')
    });
    $(document).on('change','#u_title',function(e){
      e.preventDefault();
      var p_id = '<?php echo $_GET['p_title']; ?>';
      var u_id = this.value;
      window.location.href="gen_comp.php" + '?p_title=' + encodeURIComponent(p_id)+'&u_title='+encodeURIComponent(u_id);
    //   $('.unit_name').html('<h2>Unit Head : '+$('#u_title option:selected').text()+'</h2>')
    });
    $(document).on('change','#s_title',function(e){
      e.preventDefault();
      var sb_list ='';
      sb_list = $('#s_title option:selected');
      if(sb_list){
        $('.sub_head').css({'display':'block'});
        $('.sub_items').html('');
        $(sb_list).each(function(i, obj) {
            //test
            $('.sub_items').append('<h3> '+(i+1) + ') ' + $(this).text()+'</h3>');
            // console.log($(this).text());
        });

    //   $.each(sb_list, function(index, value) {
    //     $('.sub_items').append('<h3> '+(index+1) + ') ' + value+'</h3>');
    //     });
      }else{
        $('.sub_items').html('');
        $('.sub_head').css({'display':'none'});
      }
      
    //   $.each(sb_list, function (i) {
    //     $('.sub_items').html('<h3> '+sb_list[i]+'</h3><br>');
    //  });

    });
</script>
</body>
</html>