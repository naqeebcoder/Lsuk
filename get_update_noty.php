<?php if(isset($_POST['interpreter_id'])){
session_start();
    $interp_id=$_SESSION['web_userId'];
    include 'source/db.php'; 
    include 'source/class.php';
    $query_update="update notify_new_doc set status='1' where interpreter_id='$interp_id'";
    mysqli_query($con,$query_update);
}
?>