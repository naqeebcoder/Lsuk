<?php session_start();
include 'db.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>LSUK | Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <style>
        body {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
        }
    </style>
</head>

<body style="background: #f2f2f2;">
    <div class="container">
        <?php if (isset($_POST['login'])) {
            $msg = '';
            include '../source/class.php';
            $UserNam = mysqli_real_escape_string($con, $_POST['loginEmail']);
            $Pswrd = mysqli_real_escape_string($con, $_POST['loginPass']);
            if ($UserNam && $Pswrd) {
                $query = "SELECT login.*,rolenamed.is_root FROM login,rolenamed 
				where login.prv=rolenamed.named AND login.email='" . $UserNam . "' AND BINARY login.pass='" . $Pswrd . "'";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_array($result);
                $UserName = $row['name'];
                $pasport = $row['pasport'];
                $id = $row['id'];
                $prv = $row['prv'];
                $email = $row['email'];
                $Temp = $row['Temp'];
                $user_status = $row['user_status'];
            }
            if (empty($id)) {
                $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=1');
                $msg = '<span class="alert alert-danger col-md-12 text-center"><b>' . $get_msg_db['message'] . '</b></span><br><br>';
            }

            if (!empty($id)) {
                if ($user_status == 1) {
                    $_SESSION['is_root'] = $row['is_root'];
                    //Get allowed routes
                    if ($row['is_root'] == 0) {
                        $get_routes = $acttObj->read_specific("GROUP_CONCAT(route_permissions.perm_id) as routes", "route_permissions,rolenamed", "route_permissions.role_id=rolenamed.id AND rolenamed.named='" . $prv . "'")['routes'];
                        $get_allowed_routes = !empty($get_routes) ? explode(",", $get_routes) : array();
                        if ($get_allowed_routes) {
                            $_SESSION['allowed_routes'] = $get_allowed_routes;
                        }
                    }
                    $token = $_POST['token'];
                    $_SESSION['UserName'] = $UserName;
                    $_SESSION['pasport'] = $pasport;
                    $_SESSION['userId'] = $id;
                    $_SESSION['prv'] = $prv;
                    $_SESSION['email'] = $email;
                    $_SESSION['Temp'] = $Temp;
                    $_SESSION['token'] = $token;
                    $_SESSION['user_status'] = $user_status;
                    // $acttObj->insert("daily_logs", array("action_id" => 41, "user_id" => $id, "details" => "Logged into System " ));
                    $msg = '<span class="col-md-12 text-center">Greetings <b>' . ucwords($UserName) . '.</b> We are redirecting you ...</span><br><br>';
                    $acttObj->update("login", array("token" => $token), array("id" => $id)); ?>
                    <!-- welcome Modal -->
                    <div class="modal fade" id="modal_welcome" role="dialog" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header bg-success">
                                    <h4 class="modal-title text-center">WELCOME TO LSUK</h4>
                                </div>
                                <div class="modal-body">
                                    <h4><?php if (isset($msg) && !empty($msg)) {
                                            echo $msg;
                                        } ?></h4>
                                    <img class="img img-responsive col-xs-offset-5" src="images/redirect.gif" width="60" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- welcome Modal Ends Here-->
                    <script type="text/javascript">
                        $("#modal_welcome").modal();
                        setTimeout(function() {
                            window.location = "home.php";
                        }, 2500);
                    </script>
        <?php } else {
                    $get_msg_db = $acttObj->read_specific('message', 'auto_replies', 'id=2');
                    $msg = '<span class="alert alert-danger col-md-12 text-center"><b>' . $get_msg_db['message'] . '</b></span><br><br>';
                }
            }
        } ?>
        <div class="col-md-8 col-md-offset-2" style="background: white;box-shadow: 0 0 16px 1px #d6d8d9;">
            <div class="col-md-12">
                <h2 class="text-center">Language Services UK Limited (LSUK)</h2>
                <hr />
            </div>
            <div class="col-md-8 col-md-offset-2">
                <?php if (isset($msg) && !empty($msg)) {
                    echo $msg;
                } ?><br />
            </div>
            <div class="col-md-8 col-md-offset-2">
                <form id="login" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="text" name="loginEmail" id="loginEmail" value="" placeholder='Enter your Email' class="form-control" required />
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="password" name="loginPass" id="loginPass" placeholder="Enter your Password" required class="form-control" />
                            <div class="input-group-btn">
                                <button id="shower" onclick="$('#loginPass').prop('type','text');$(this).hide();$('#hider').show();" class="btn btn-default" type="button">
                                    <i class="glyphicon glyphicon-eye-open" title="Show Password"></i>
                                </button>
                                <button id="hider" onclick="$('#loginPass').prop('type','password');$(this).hide();$('#shower').show();" class="btn btn-default" type="button" style="display:none;">
                                    <i class="glyphicon glyphicon-eye-close" title="Hide Password"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group"><br>
                            <input type="hidden" name="token" id="token" />
                            <input type="submit" class="btn btn-primary col-md-4" name="login" value="Log in" />
                            <input type="reset" class="btn btn-warning col-md-4 col-md-offset-1" value="Clear" />
                        </div>
                        <div class="form-group col-md-12 row">
                            <br>
                            <strong><a class="text-danger" href="javascript:void(0)" onclick="alert('Contact LSUK at Ph: 01173290610 ');" rel="submenu">Forgot Your Password</a></strong>
                        </div>
                </form>
            </div>
            <div class="col-md-8 col-md-offset-2">
                <hr />
                <p class="text-center">A Product of <b>LSUK</b>, Copy Right 2020-21</p><br />
            </div>
        </div>
    </div>
</body>
<script src="https://www.gstatic.com/firebasejs/7.16.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.16.1/firebase-messaging.js"></script>
<script>
    var config = {
        apiKey: "AIzaSyCUsl-EHHeA4HvBDRyOXdyCQSMNmUiRlPc",
        authDomain: "lsuk-1530684014975.firebaseapp.com",
        projectId: "lsuk-1530684014975",
        storageBucket: "lsuk-1530684014975.appspot.com",
        messagingSenderId: "62740450561",
        appId: "1:62740450561:web:40eadc0959b6be3a881f00"
    };
    firebase.initializeApp(config);
    const messaging = firebase.messaging();
    messaging
        .requestPermission()
        .then(function() {
            //console.log("Notification permission granted.");
            // get the token in the form of promise
            return messaging.getToken()
        })
        .then(function(token) {
            $("#token").val(token);
        })
        .catch(function(err) {
            console.log("Unable to get permission to notify.", err);
        });
</script>

</html>