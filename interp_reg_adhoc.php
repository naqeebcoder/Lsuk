<?php 
include 'db.php'; 
include 'class.php'; 

if(isset($_POST) && !empty($_POST)){
    try{
        $data = [
          'name' => $_POST['fname'],
          'surname' => $_POST['sname'],
          'email' => $_POST['email'],
          'language' => @$_POST['language'],
          'source_language' => $_POST['source_language'],
          'lang_level' => @$_POST['level'],
          'address' => $_POST['address'],
          'city' => $_POST['city'],
          'postCode' => $_POST['post_code'],
          'address' => $_POST['address'],
          'landline' => $_POST['landline'],
          'mobile' => $_POST['mobile'],
          'dob' => $_POST['dob'],
          'is_nrpsi' => isset($_POST['nrpis_itiyes']) ? 1 : 0,
          'nrpsi_number' => @$_POST['registeration_number'],
          'sbmtd_by' => $_SESSION['userId'],
          'dated' => date('Y-m-d '),
          'isAdhoc' => 1,
          'interp' => isset($_POST['interp']) ? 'yes' : 'no',
          'telep' => isset($_POST['telep']) ? 'yes' : 'no',
          'trans' =>  isset($_POST['trans']) ? 'yes' : 'no',
          'gender' => $_POST['gender'],
        ];
        // print_r($data); die;
        extract($data);
    
        $sql = 'INSERT INTO interpreter_reg SET name = "'.$name.'",dob ="'.$dob.'" ,reg_date ="'.$dated.'" , email = "'.$email.'" ,contactNo = "'.$mobile.'", contactNo2 = "'.$landline.'", interp ="'.$interp.'",telep ="'.$telep.'",trans="'.$trans.'", gender="'.$gender.'",is_nrpsi="'.$is_nrpsi.'", city="'.$city.'", address="'.$address.'",postCode="'.$postCode.'",dated="'.$dated.'",isAdhoc=1, extra_data="null"';
        
        $result = mysqli_query($con, $sql);
        $last_id = mysqli_insert_id($con);
        //check $language is array or not
        if(is_array($language)){
            for($i = 0; $i <count($language); $i++){
                $sql = 'INSERT INTO interp_lang SET dated= "'.$dated.'", code = "id-'.$last_id.'",lang ="'.$language[$i].'", level ="'.$lang_level[$i].'"';
                $result = mysqli_query($con, $sql);
            }
        }else{
            $sql = 'INSERT INTO interp_lang SET dated= "'.$dated.'", code = "id-'.$last_id.'",lang ="'.$source_language.'", level = 1';
            $result = mysqli_query($con, $sql);
        }
    
        echo json_encode(['status' => 1, 'message' => 'Successfully registered']);
    }catch(Exception $e){
        echo json_encode(['status' => 0, 'message' => $e->getMessage()]);

    }
    die;
  //   echo "<script>alert('New Ad interpreter successfully registered. Thank you');window.onunload = refreshParent;</script>";
  }
if(session_id() == '' || !isset($_SESSION)){session_start();}
// language list for dropdown
$sql_opt = "SELECT lang FROM lang ORDER BY lang ASC";
$result_opt = mysqli_query($con, $sql_opt);
$languageOptions = "";
while ($row_opt = mysqli_fetch_assoc($result_opt)) {
    $code = $row_opt["lang"];
    $name_opt = $row_opt["lang"];
    $languageOptions .= "<option value='$code'>" . $name_opt. "</option>";
}


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>Interpreter Registration Form</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <?php include'ajax_uniq_fun.php'; ?>
    <style>
    .multiselect {min-width: 230px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}.multiselect-native-select{display:block;}.multiselect-container li.active label.radio,.multiselect-container li.active label.checkbox{color:white;}
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
        <h1>Adhoc Interpreter Registration Form</h1>
        </div>
    </div>
  <form method="post" action="" enctype="multipart/form-data" id="submit-form">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Register AdHoc Interpreter
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Are you a BSL Interpreter?</label>
                        <label for="">
                            Yes <input type="radio" class="bsl"  name="bsl" value="1" />
                        </label>
                        <label for="">
                            No <input type="radio"  class="bsl" name="bsl" value="0" />
                        </label>
                        <input type="text" name="nrcpd_no" id="nrcpd_no" class="form-control" placeholder="NRCPD Number.." style="display: none;">
                    </div>
                    <div class="form-group" id="nrpsi_div" style="display: none;">
                        <label for="">Are You a NRPSI ITIYES?</label>
                        <label for="">
                            Yes <input type="radio" name="nrpis_itiyes" class="nrpis_itiyes" value="1" />
                        </label>
                        <label for="">
                            No <input type="radio" name="nrpis_itiyes"class="nrpis_itiyes"  value="0" />
                        </label>
                        <input type="text" name="registeration_no" id="registeration_no" class="form-control" placeholder="Registeration Number.." style="display: none;">

                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary continue" type="button" disabled>Conitinue</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-primary main-form" style="display: none;">
        <div class="panel-heading">
            Applicant Information
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">
                            First Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="fname" onblur="check_existing(this.value)" id="fname" placeholder="First Name" required />
                    </div>  
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">Surname</label>
                        <input type="text" class="form-control" name="sname" id="sname" placeholder="Surname" required />
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">
                            Gender <span class="text-danger">*</span>
                        </label>
                        <select name="gender" id="gender" class="form-control" required>
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Not Speciefied">Not Specified</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="">
                            Date of Birth <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" name="dob" id="dob" placeholder="Date of Birth" required />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">
                            Address <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" name="address" id="address" placeholder="Address" required></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">City</label>
                        <input type="text" class="form-control" name="city" id="city" placeholder="City" required/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    
                    <div class="form-group">
                        <label for="">Post Code</label>
                        <input type="text" class="form-control" name="post_code" id="post_code" placeholder="Post Code" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">Mobile Number</label>
                        <input type="text" class="form-control" name="mobile" id="mobile" onblur="check_existing(this.value)" placeholder="Mobile Number" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">LandLine</label>
                        <input type="text" class="form-control" name="landline" id="landline" placeholder="LandLine" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" onblur="check_existing()" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Mode of Job</label> <br>
                        <label for="">
                            Face To Face Interpreting
                            <input type="checkbox" value="yes" name="interp" class="f2f"  checked />
                        </label>
                        <label for="">
                            Telephone Interpreting
                            <input type="checkbox" value="yes" name="telep" class="tp"  />
                        </label>
                        <label for="">
                            Translation
                            <input type="checkbox" value="yes" name="trans" class="tr"  />
                        </label>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Select Source Language</label>
                        <select name="source_language" id="source_language" onchange="upd_langs(this)"  required='' class="form-control multi_class">
                            <option selected>English</option>
                                <option disabled value="">Select Target Language</option>
                                <?php echo $languageOptions; ?>
                            </option>
                        </select>
                        <div class="selected-languages">

                        </div>
                    </div>
                </div>
                
            </div>
            <div class="row">
                <div class="col-offset-sm-10 col-sm-2">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </div>
            </div>
        
    </div>
  </form>
</div>

<!-- Modal to display record -->
<div class="modal modal-info fade col-md-8 col-md-offset-2"  data-toggle="modal" data-target=".bs-example-modal-lg" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div class="modal-dialog" role="document" style="width:auto;">
    <div class="modal-content">
        <div class="modal-header bg-default bg-light-ltr">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Record Details</h4>
        </div>
        <div class="modal-body" id="view_modal_data" style="overflow-x:auto;">
            <table class="table table-bordered">
            <tr>
                <td><input readonly type="text" class="form-control" id="selected_language" style='width:165px;'></td>
                <td><select class='form-control' id='selected_level' style='width:165px;' required>
                <option value='1' selected>Native</option>
                <option value='2'>Fluent</option>
                <option value='3'>Intermediate</option>
                <option value='4'>Basic</option>
                </select></td>
                <td><button type='button' class='btn btn-success btn-sm' onclick='add_language()'>Add</button>
                    <button type='button' class='btn btn-danger btn-sm' onclick='cancel_language()'>Cancel</button>
                </td>
            </tr>
            </table>
        </div>
        <div class="modal-footer bg-default">
        <button type="button" class="btn  btn-primary pull-right" data-dismiss="modal"> Close</button>
        </div>
    </div>
    </div>
</div>
<!--End of modal-->
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
    function upd_langs(elem){
        $('#view_modal_data').find('#selected_language').val(elem.value);
        $('#view_modal').modal("show");
    }
    function add_language(){
        var lang_selected=$('#source_language').val();
        var level_selected=$('#selected_level').val();
        var html = '<span for="" class="label label-info remove-selected-lang" title="Click to remove language" style="cursor:pointer;">'+lang_selected+'</span>  ';
        html += '<input type="hidden" name="language[]"  id="lang_'+lang_selected+'"  value="'+lang_selected+'">';
        html += '<input type="hidden" name="level[]"   id="level_'+lang_selected+'" value="'+level_selected+'">';
        $('.selected-languages').append(html);
        $('#source_language option:selected').remove();
        cancel_language();
    }

    function cancel_language(){
        $("#language_selector").prop("selectedIndex", 0).change();
        $('#selected_level').prop("selectedIndex", 0).change();
        $('#view_modal').modal("hide");
    }

$('body').on('click','.remove-selected-lang',function(){
    var elm = $(this);
    var langID = elm.text();
    $('#lang_'+langID).remove();
    $('#level_'+langID).remove();
    $(this).remove();
});
$('body').on('click','.bsl',function(){
    var check = $(this).val();
    if(check == '1'){
        $('#nrcpd_no').css('display','block').attr('required',true);
        $('#nrpsi_div').css('display','none');
        $('#registeration_no').attr('required',false);
        $('.continue').attr('disabled',false);
        continueCheck = false;
    }else{
        $('#nrcpd_no').css('display','none').attr('required',false);
        $('#nrpsi_div').css('display','block');
        $('.continue').attr('disabled',true);
        $('.main-form').css('display','none');
        continueCheck = true;

    }
});

$('body').on('focus','#nrcpd_no',function(){
    var nrcpd_no = $(this).val();
    if(nrcpd_no != "")
    {
        $('.continue').css('disabled',true);
    }
});

$('body').on('click','.continue',function(){
    var nrcpd_no = $('#nrcpd_no').val();
    var registeration_no = $('#registeration_no').val();
  $('.main-form').css('display','block');

});

$('body').on('click','.nrpis_itiyes',function(){
    var check = $(this).val();
    if(check == '1'){
        $('#nrpsi_div').css('display','block').attr('required',true);
        $('.continue').attr('disabled',false);
        $('.main-form').css('display','none');

        $('#registeration_no').attr('required',true).css('display','block');
    }else{
        $('#nrpsi_div').css('display','non').attr('required',false);
        $('.continue').attr('disabled',true);
        $('.main-form').css('display','none');

        $('#registeration_no').attr('required',false).css('display','none');

    }
});

$('body').on('submit','#submit-form', function(e){
    var check_languages = $('#source_language').val();
    if(check_languages == ""){
        alert('Please select atleast one language');
        e.preventDefault();
    }
   e.preventDefault();
    $.ajax({
        url: 'interp_reg_adhoc.php',
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(data){
            var data = $.parseJSON(data);
            // console.log(data);
            if(data.status == 1){
                alert('Successfully registered');
                window.close();
            }
            if(data.status == 0)
            {
                alert(data.message);
            }
            
        }
    });
});

////////////////////////////////////////////////////
$(function() {
  $('.multi_class').multiselect({buttonWidth: '100px',includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
});
$(".valid").bind('keypress paste',function (e) {
  var regex = new RegExp(/[a-z A-Z 0-9 ()]/);
  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
  if (!regex.test(str)) {
    e.preventDefault();
    return false;
  }
});
function check_fields(){
    var name=$("#fname").val();
    var dob=$("#dob").val();
    // if(!name){
    //     $("#fname").focus();
    // }else if(!dob){
    //     $("#dob").focus();
    // }else{}
}
function check_existing($elem=null){
var nm=$("#fname").val();
var dob=$("#dob").val();
var em=$('#email').val();
// alert(nm+dob+em); return;
if(nm && dob && em){
    $.ajax({
        url:'ajax_add_interp_data.php',
        method:'post',
        dataType:'json',
        data:{'em':em,'nm':nm,'dob':dob,'action':'check_em'},
        success:function(data){
            if(data['status']=="exist" && data['is_temp']=="1"){
                alert(data['msg']);
            }else if(data['status']=="exist" && data['is_temp']=="0"){
                alert(data['msg']);
                $("#email").val("");$("#email").focus();
            }else if(data['status']=="same_exist"){
                alert(data['msg']);
                $("#name").val("");$("#dob").val("");$("#email").val("");$("#name").focus();
            }
    }, error: function(xhr){
        alert("An error occured: " + xhr.status + " " + xhr.statusText);
    }
    });
  }
}
function refreshParent(){
  window.opener.location.reload();
}
    function other_city(elem){
			var selected_city=$(elem).val();
      if (selected_city!='Not in List'){
        $('.other_city_field').val(selected_city);
      }
			if (selected_city=='Not in List'){
          $('.other_city_field').val('');
          $(elem).removeAttr("required");
          $('.div_other_city_field,.other_city_field').removeClass('hidden');
          $('.other_city_field').attr('required',"required");
          $('.other_city_field').focus();
      } else {
          $(elem).attr('required',"required");
          $('.div_other_city_field,.other_city_field').addClass('hidden');
          $('.other_city_field').removeAttr("required");
          $('#selected_city').focus();
      }
		}
    function get_cities(elem){
      $('.div_other_city_field,.other_city_field').addClass('hidden');
      $('.other_city_field').val("");
      var country_name=$(elem).val();
      if(country_name){
        $.ajax({
            url:'ajax_add_interp_data.php',
            method:'post',
            dataType:'json',
            data:{country_name:country_name,type:'get_cities_of_country'},
            success:function(data){
                if(data['cities']){
                    $('.append_cities').removeClass('hidden');
                    $('.append_cities').html(data['cities']);
                    //$("#selected_city").multiselect('rebuild');
                }else{
                  $('.append_cities').addClass('hidden');
                  alert("Something went wrong. Try again!");
                }
        }, error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        }
        });
      }
    }
</script>
</body>
</html>