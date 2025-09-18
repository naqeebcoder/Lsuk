<?php if(session_id() == '' || !isset($_SESSION)){session_start();}
if(empty($_SESSION['cust_UserName'])){
    echo '<script>window.location="index.php";</script>';
}
include 'source/db.php';
include 'source/class.php';
$id= @$_GET['id'];
$op_id= @$_GET['operator_id'];
$pr_comp="";
$pr_comp=$acttObj->read_specific("subsidiaries.parent_comp as parent_id","subsidiaries,company_login","subsidiaries.child_comp=company_login.company_id AND company_login.id=$id")['parent_id'];

if($_SESSION['company_login_id']!=$id && $_SESSION['role']!='admin' && $_SESSION['company_id']!=$pr_comp){
    // echo "logged id: $id <br> Session comp id: ".$_SESSION['company_id']."<br>Parent id : $pr_comp <br>Role: ".$_SESSION['role'];
    header("Location:customer_area.php");
}
// else{
//     echo "all good";
// }
$table = "company_operators";
$action= @$_GET['action'];
if(isset($_GET['operator_id']) && $action=="delete"){
$acttObj->editFun($table,$_GET['operator_id'],'deleted_flag',1);
}
if(isset($_GET['operator_id']) && $action=="restore"){
$acttObj->editFun($table,$_GET['operator_id'],'deleted_flag',0);
}
if(isset($_POST['btn_edit'])){
    echo $_POST['op_role_edit']." is the value of role";
$acttObj->update("company_operators",array("company_id"=>$id,"name"=>$_POST['name'],"email"=>$_POST['email'],"paswrd"=>$_POST['paswrd'],"temp"=>$_POST['op_role_edit']),array("id"=>$op_id));
?>
<script>
    alert('Account updated Successfuly.');
    window.location.href = "manage_company_operators.php?id=<?php echo $id; ?>&action=manage";
</script>
<?php }
if(isset($_POST['btn_add'])){
$check_existing=$acttObj->read_specific("count(*) as count_operators","company_operators","email='".$_POST['email']."'");
if($check_existing['count_operators']>0){ ?>
<script>
    alert('Same email account already exists!');
    window.location.href = "manage_company_operators.php?id=<?php echo $id; ?>&action=manage";
</script>
<?php }else{
$acttObj->insert("company_operators",array("company_id"=>$id,"name"=>$_POST['name'],"email"=>$_POST['email'],"paswrd"=>$_POST['paswrd'],"temp"=>$_POST['op_role']));?>
<script>
    alert('New account added Successfuly.');
    window.location.href = "manage_company_operators.php?id=<?php echo $id; ?>&action=manage";
</script>
<?php }
}
$row = $acttObj->read_specific("comp_reg.name,company_login.*","company_login,comp_reg","company_login.company_id=comp_reg.id AND company_login.id=".$id); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Manage Company Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
    <script>
        function refreshParent() {
            window.opener.location.reload();
        }
    </script>
</head>

<body>
    <div class="container">
        <?php $ntr = array(1,3,4); ?>
        <?php if(($action=="manage" || $action=="delete" || $action=="restore") && (in_array($_SESSION['comp_nature'],$ntr) || $_SESSION['comp_type']==2 || $_SESSION['role']=='admin')){ ?>
        <br><br>
        <div class="col-md-10 col-md-offset-1">
            <div class="bg-info col-xs-12 form-group">
                <h4><?php echo $row['name']; ?></h4>
            </div>
            <a class="btn btn-primary" href="manage_company_operators.php?id=<?php echo $id; ?>&action=add">Add New
                Operator</a><br><br>
            <table class="table table-bordered table-hover">
                <thead class="bg-primary">
                    <tr>
                        <th width="40%">Operator Name</th>
                        <th>Login Email</th>
                        <th>Account Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $get_operator=$acttObj->read_all("company_operators.id,company_operators.name,company_login.company_id,company_login.orgName,company_operators.email,company_operators.deleted_flag,company_login.comp_type","company_operators,company_login","company_operators.company_id=company_login.id and company_operators.company_id=".$id);
                if($get_operator->num_rows>0){
                while($row_operator = $get_operator->fetch_assoc()){ ?>
                    <tr>
                        <td><?php echo $row_operator['name'];?></td>
                        <td><?php echo $row_operator['email']; ?></td>
                        <td><?php echo $row_operator['deleted_flag']==1?'<span class="label label-danger">Trashed</span>':'<span class="label label-success">Active</span>'; ?>
                        </td>
                        <td>
                            <a class="btn btn-warning btn-sm"
                                href="manage_company_operators.php?id=<?php echo $id; ?>&operator_id=<?php echo $row_operator['id']; ?>&action=edit">Edit</a>
                            <?php if($row_operator['deleted_flag']==0){ ?>
                            <a class="btn btn-danger btn-sm"
                                href="manage_company_operators.php?id=<?php echo $id; ?>&operator_id=<?php echo $row_operator['id']; ?>&action=delete">Delete</a>
                            <?php }else{ ?>
                            <a class="btn btn-success btn-sm"
                                href="manage_company_operators.php?id=<?php echo $id; ?>&operator_id=<?php echo $row_operator['id']; ?>&action=restore">Restore</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php }
                 }else{ ?>
                    <tr>
                        <td align="center" colspan="4">There are no booking operators currently!</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php }
    if($action=="add" && (in_array($_SESSION['comp_nature'],$ntr) || $_SESSION['comp_type']==2 || $_SESSION['role']=='admin')){ ?><br>
        <a class="btn btn-primary" href="manage_company_operators.php?id=<?php echo $id; ?>&action=manage">View All
            Operators</a>
        <form action="" method="post" class="register" id="signup_form" name="signup_form"><br>
            <label class="text-center">Add New company Operator credentials</label><br>
            <div class="bg-info col-xs-12 form-group">
                <h4><?php echo $row['name']; ?></h4>
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label>Operator Name *</label>
                <input name="name" type="text" id="unique" placeholder='Write User Name' required=''
                    class="form-control" />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label>Role</label><br>
                <label  for="op_role1">
                    <input class="form-check-input" type="radio" name="op_role" id="op_role1" value="1" checked>
                    Temporary <small>(Bookings will need manager's approval before being posted)</small>
                </label>
                <label  for="op_role2">
                    <input class="form-check-input" type="radio" name="op_role" id="op_role2" value="0">
                    Permanent <small>(Bookings will be posted directly without manager's approval)</small>
                </label>
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label>Email *</label>
                <input name="email" type="text" id="unique" placeholder='Write Login Email' required=''
                    class="form-control" />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label>Password * </label>
                <input name="paswrd" type="password" id="pass" onchange="form.repass.pattern = this.value;"
                    placeholder='Add a password' required='' class="form-control pass" />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label><input type="checkbox" id="checkbox" /> Show password</label><br><br>
                <button type="submit" name="btn_add" class="btn btn-primary">Submit &raquo;</button>
            </div>
        </form>
        <?php }else{
    if(empty($row['id'])){ ?>
        <center>
            <h3>Coudn't find this record!</h3><button class="btn btn-danger" type="button"
                onclick="window.close();">Close</button>
        </center>
        <?php }else{
     if($action=="edit"){
         $row=$acttObj->read_specific("*","company_operators","id=".$_GET['operator_id']); ?>
        <br><a class="btn btn-primary" href="manage_company_operators.php?id=<?php echo $id; ?>&action=manage">View All
            Operators</a>
        <form action="" method="post" class="register" id="signup_form" name="signup_form"><br>
            <label class="text-center">Update company operator credentials</label><br>
            <div class="bg-info col-xs-12 form-group">
                <h4><?php echo $row['name']; ?></h4>
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label>Operator Name *</label>
                <input name="name" type="text" id="unique" value="<?php echo $row['name']; ?>"
                    placeholder='Write User Name' required='' class="form-control" />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label>Role</label><br>
                <label  for="op_role_edit1">
                    <input class="form-check-input" type="radio" name="op_role_edit" id="op_role_edit1" value="1" <?php echo ($row['temp']==1?'checked':''); ?>>
                    Temporary <small>(Bookings will need manager's approval before being posted)</small>
                </label>
                <label  for="op_role_edit2">
                    <input class="form-check-input" type="radio" name="op_role_edit" id="op_role_edit2" value="0" <?php echo ($row['temp']==0?'checked':''); ?>>
                    Permanent <small>(Bookings will be posted directly without manager's approval)</small>
                </label>
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label>Email *</label>
                <input name="email" type="text" id="unique" value="<?php echo $row['email']; ?>" placeholder=''
                    required='' class="form-control" />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label>Password * </label>
                <input name="paswrd" type="password" id="pass" onchange="form.repass.pattern = this.value;"
                    value="<?php echo $row['paswrd']; ?>" placeholder='' required='' class="form-control pass" />
            </div>
            <div class="form-group col-md-6 col-sm-6">
                <label><input type="checkbox" id="checkbox" /> Show password</label><br><br>
                <button type="submit" name="btn_edit" class="btn btn-primary">Submit &raquo;</button>
            </div>
        </form>
        <?php }
        }
    } ?>
    </div>
</body>
<script>
    $(".valid").bind('keypress paste', function (e) {
        var regex = new RegExp(/[a-z A-Z 0-9 ()]/);
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (!regex.test(str)) {
            e.preventDefault();
            return false;
        }
    });
    var $vp = $('#checkbox');
    $vp.on('click', function () {
        var $target = $('.pass');
        if ($target.attr('type') == "password") {
            $target.attr('type', 'text');
        } else {
            $target.attr('type', 'password');
        }
    });
</script>

</html>