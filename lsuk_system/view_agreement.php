<?php if (isset($_GET['view_id'])) {
    if (session_id() == '' || !isset($_SESSION)) {
        session_start();
    }
    include 'db.php';
    include 'class.php';
    $allowed_type_idz = "186";
    //Check if user has current action allowed
    if ($_SESSION['is_root'] == 0) {
        $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
        if (empty($get_page_access)) {
            die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Interpreter Documents</u> action!<br>Kindly contact admin for further process.</h2></center>");
        }
    }
    $view_id = $_GET['view_id'];
    $query = "SELECT name,reg_date FROM interpreter_reg where id=" . $view_id;
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $name = $row['name'];
    $reg_date = $row['reg_date'];
    $get_agreement = $acttObj->read_specific("em_format", "email_format", "id=41")["em_format"];
    $data   = ["[INTERPRETER_NAME]", "[SIGNED_DATED]"];
    $to_replace  = ["$name", "$reg_date"];
    $get_agreement = str_replace($data, $to_replace, $get_agreement);
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Interpreter Agreement Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
</head>

<body style="padding:12px;">
    <h4 class="text-center"> Agreement for <b><?php echo ucwords($name); ?></b></h4>
    <div class="col-md-12" style="border:1px solid grey;padding:18px;"><?php echo $get_agreement; ?></div>
    <br><br>
</body>

</html>