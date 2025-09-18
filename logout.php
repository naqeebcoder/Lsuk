<?php session_start();
include "db.php";
include "source/class.php";
$role=isset($_GET['r'])?$_GET['r']:'int';
if($role=="op"){
    $logout_path="cust_login.php?op";    
}else{
    $logout_path=$role=='int' || !isset($role)?"login.php":"cust_login.php";
}
/*if(!empty($_SESSION['device_id'])){
    $acttObj->delete("int_tokens","device_id='".$_SESSION['device_id']."'");
}*/
$_SESSION = array();
/*if(!empty($_COOKIE['device_id'])){
    setcookie("device_id", "", time() - 3600);
}*/
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
echo '<script type="text/javascript">window.location="'.$logout_path.'";</script>'; 
?>