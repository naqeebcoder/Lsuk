<?php session_start();
include 'db.php';
error_reporting(0);
class SysPermiss
{
    static public function UserHasPage($url)
	{
        global $con;
        if ($_SESSION['is_root'] == 0) {
            $url=basename($url);
            $userid=$_SESSION['userId'];
            $query="select count(route_permissions.id) as exist from route_permissions,userrole,routes WHERE userrole.roleid=route_permissions.role_id AND route_permissions.perm_id=routes.id AND userrole.userid=$userid and routes.name='$url'";
            $result = mysqli_query($con, $query); 
            $row = mysqli_fetch_assoc($result);

            if ($row["exist"] == 0){
                echo "<script>window.location.href='/lsuk_system/';</script>";
                die();
            }
        }
    }
}
