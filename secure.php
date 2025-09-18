<?php if(!isset($_SESSION['web_userId'])){
    echo '<script>window.location.href="login.php";</script>';
} ?>