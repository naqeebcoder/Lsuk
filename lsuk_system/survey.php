<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if(session_id() == '' || !isset($_SESSION)){session_start();}
include 'db.php';
include_once ('function.php');
include 'class.php';
$table="survey";
$msg = '';
// error_reporting(E_ALL);
$query_emails="SELECT DISTINCT interpreter_reg.name, interpreter_reg.email, interpreter_reg.id FROM interpreter_reg,interp_lang WHERE (CASE 
        WHEN (actnow='Active' and (actnow_time='1001-01-01' AND actnow_to='1001-01-01') AND active='0') THEN 'ready'
        WHEN (actnow='Active' and (CURRENT_DATE() BETWEEN actnow_time AND actnow_to) AND active='0') THEN 'ready'
        WHEN (actnow='Inactive' and (actnow_time='1001-01-01' AND actnow_to='1001-01-01') AND (active='0' OR active='1')) THEN 'not ready'
        WHEN (actnow='Inactive' and (CURRENT_DATE() NOT BETWEEN actnow_time AND actnow_to) AND active='0') THEN 'ready'
        ELSE 'not ready' END)='ready' AND interpreter_reg.interp='Yes' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0";
$res_emails=mysqli_query($con,$query_emails);
if(isset($_POST['submit'])){
    $ids = $_POST['selected_interpreters'];
    if(!empty($ids)){
        $ids = implode(',',$ids);
        $sql = "SELECT name,email FROM interpreter_reg WHERE id IN ($ids)";
        $interpreters = mysqli_query($con,$sql);
        while($interpreter = mysqli_fetch_assoc($interpreters)){
            $name = $interpreter['name'];
            $email = $interpreter['email'];
              $sql = "INSERT INTO $table (name,email) VALUES ('$name' , '$email')";
              mysqli_query($con,$sql);
        }
        $msg = 'Survey will be broadcasted in a while';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<title>BroadCast Survey</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>.ri{margin-top: 7px;}
.ri .label{font-size:100%;padding: .5em 0.6em 0.5em;}
.checkbox-inline+.checkbox-inline, .radio-inline+.radio-inline {
    margin-top: 4px;}
    .multiselect {min-width: 300px;}.multiselect-container {max-height: 400px;overflow-y: auto;max-width: 380px;}.p40{padding:40px!important;}
#ajax_loader {visibility: hidden;background-color: #ffffff;
position: absolute;width: 7%;
height: 12%;left: 44%;top: 10%;
overflow: hidden;z-index: 9999;border-radius: 100%;
}
#div_specific .btn-group .dropdown-menu{
    top:unset;bottom:100%;
}
/* Formatting search box */
.search-box{
    width: 300px;
    position: relative;
    display: inline-block;
    font-size: 14px;
}
.search-box input[type="text"]{
    height: 32px;
    padding: 5px 10px;
    border: 1px solid #CCCCCC;
    font-size: 14px;
}
.result{
    position: absolute;
    z-index: 1;
    top: 100%;
    width: 90% !important;
    background: white;
    max-height: 246px;
    overflow-y: auto;
}
.search-box input[type="text"], .result{
    width: 100%;
    box-sizing: border-box;
}
/* Formatting result items */
.result p{
    margin: 0;
    padding: 7px 10px;
    border: 1px solid #CCCCCC;
    border-top: none;
    cursor: pointer;
}
.result p:hover{
    background: #f2f2f2;
}
</style>
</head>
<?php include 'header.php';?>
<body>
<?php include 'nav2.php';?>
<style>.tablesorter thead tr {background: none;}</style>
<section class="container-fluid">
    <div style='background-color:green;color:white;font-weight:bold'><?php echo $msg; ?></div>
    <form method='post'>
<div class="row">
<div class="form-group col-md-3 col-sm-6">
                  <label class="optional">Select Interpreters</label>
                  <select class="multi_class" id="selected_interpreters" name="selected_interpreters[]"  multiple="multiple">
                <?php while($row_emails=mysqli_fetch_assoc($res_emails)){ ?>
                  <option value='<?php echo $row_emails['id'] ?>'><?php echo $row_emails['name'] ?></option>
                <?php } ?>
                  </select>
            </div>
    </div>
    <div class='row'>
        <div class='col-md-12'>
            <input type='submit' class='btn btn-info' name='submit' value='Submit'/>
        </div>
    </div>
                </form>
	</section>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"type="text/javascript"></script>
<script>
    $(function() {
      $('.multi_class , #selected_interpreters').multiselect({includeSelectAllOption: true,numberDisplayed: 1,enableFiltering: true,enableCaseInsensitiveFiltering: true});
    });
</script>
</body>
</html>