<?php
// Initialize the session.
// If you are using session_name("something"), don't forget it now!
session_start();
// include 'db.php';
// $ud = $_SESSION['userId'];
// $qy = mysqli_query($con, "INSERT INTO daily_logs(action_id,user_id,details) VALUES(42,$ud,'Logged Out From System')");
// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();
echo '<script type="text/javascript">window.location="index.php";</script>'; 
?>