<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include "source/db.php";
include "source/class.php";
if (empty($_SESSION['cust_UserName'])) {
    echo '<script type="text/javascript">' . "\n";
    echo 'window.location="index.php";';
    echo '</script>';
} ?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
    <?php include 'source/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="js/jquery-1.8.2.min.js" type="text/javascript"></script>
    <script src="js/jquery.jcarousel.min.js" type="text/javascript"></script>
    <script src="js/custom.js" type="text/javascript"></script>
    <style>
        .box_item {
            padding: 20px;
            text-align: center;
            border-radius: 4px;
            margin: 12px;
            border: 1px solid #bcbcbc;
            box-shadow: 1px 1px 7px 1px #00000061;
            -webkit-transition: .3s all ease;
            -o-transition: .3s all ease;
            transition: .7s all ease;
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
        }

        .box_item:hover {
            background: #f5f6f7;
            box-shadow: 1px 1px 7px 0px #00339940;
            /* transition: transform 1s;
        transform: rotateX(180deg); */
        }

        .box_item .filter {
            filter: grayscale(80%) drop-shadow(8px 8px 10px #c4c1c1);
        }

        .box_item:hover .filter {
            filter: grayscale(50%) drop-shadow(8px 8px 10px #4d70b14f);
        }

        #breadcrumbs {
            line-height: 30px;
        }
    </style>
</head>

<body class="boxed">
    <div id="wrap">
        <?php include 'source/top_nav.php';
        $get_child_companies = $acttObj->read_specific("*", "child_companies", "parent_comp=" . $_SESSION['company_id']);
        if (isset($_SESSION['operator'])) {
            $get_operator = $acttObj->read_specific("company_operators.id,company_operators.name,company_login.company_id,company_login.orgName,company_operators.email,company_login.comp_type", "company_operators,company_login", "company_operators.company_id=company_login.id and company_operators.id=" . $_SESSION['operator']);
        }
        if ($_SESSION['comp_type'] == 3) {
            if (!empty($get_child_companies['id'])) {
                $url_f2f = "customer_area_slct_comp.php?interp=order_f2f_multi.php";
                $url_tp = "customer_area_slct_comp.php?interp=order_tp_multi.php";
                $url_tr = "customer_area_slct_comp.php?interp=order_tr_multi.php";
            } else {
                $url_f2f = "order_f2f_multi.php?company_id=" . base64_encode($_SESSION['company_id']);
                $url_tp = "order_tp_multi.php?company_id=" . base64_encode($_SESSION['company_id']);
                $url_tr = "order_tr_multi.php?company_id=" . base64_encode($_SESSION['company_id']);
            }
        } else {
            $url_f2f = "customer_area_slct_comp.php?interp=order_f2f_multi.php";
            $url_tp = "customer_area_slct_comp.php?interp=order_tp_multi.php";
            $url_tr = "customer_area_slct_comp.php?interp=order_tr_multi.php";
        }
        ?>
        <section id="page-title">
            <div class="container clearfix">
                <?php if (isset($_SESSION['operator'])) { ?>
                    <h1>Client's Area <a href="logout.php?r=op" class="btn btn-warning" title="Click here to logout">LOG OUT</a></h1><br><br>
                <?php } else { ?>
                    <h1>Client's Area <a href="logout.php?r=comp" class="btn btn-warning" title="Click here to logout">LOG OUT</a></h1><br><br>
                <?php } ?>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="index.php">Home</a> &rsaquo;</li>
                        <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']); ?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php')); ?></a> &rsaquo;</li>
                        <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php')); ?></li>
                    </ul>
                </nav>
            </div>
        </section>
        <div class="container-fluid">
            <div class="col-md-11 col-md-offset-1">
                <?php if ($_SESSION['cust_UserName'] == 'imran@lsuk.org') { ?>
                    <a href="cust_reg.php">
                        <div class="col-md-5 col-lg-3 box_item">
                            <img src="images/client_area/user.png" align="middle" class="img-responsive col-xs-offset-4 filter" width="99">
                            <h3>Regsitration</h3>
                            <p>Add New User to Client Portal</p>
                        </div>
                    </a>
                    <a href="cust_reg_list.php">
                        <div class="col-md-5 col-lg-3 box_item">
                            <img src="images/client_area/user_list.png" align="middle" class="img-responsive col-xs-offset-4 filter" width="99">
                            <h3>Users list</h3>
                            <p>Manage Records of Registered Users</p>
                        </div>
                    </a>
                <?php }
                if ($_SESSION['cust_UserName']) { ?>
                    <a href="<?php echo $url_f2f; ?>">
                        <div class="col-md-5 col-lg-3 box_item">
                            <img alt="Face to face request at LSUK" src="images/client_area/f2f.png" align="middle" class="img-responsive col-xs-offset-4 filter" width="106">
                            <h3>face to face</h3>
                            <p>Order for Face To Face Interpreter</p>
                        </div>
                    </a>
                    <a href="<?php echo $url_tp; ?>">
                        <div class="col-md-5 col-lg-3 box_item">
                            <img alt="Telephone request at LSUK" src="images/client_area/telep.png" align="middle" class="img-responsive col-xs-offset-4 filter" width="100">
                            <h3>telephone</h3>
                            <p>Request for Telephone Interpreter</p>
                        </div>
                    </a>
                    <a href="<?php echo $url_tr; ?>">
                        <div class="col-md-5 col-lg-3 box_item">
                            <img alt="Translation request at LSUK" src="images/client_area/translation.png" align="middle" class="img-responsive col-xs-offset-4 filter" width="100">
                            <h3>translation</h3>
                            <p>Request for Document Translation</p>
                        </div>
                    </a>
                    <a href="client_orders.php">
                        <div class="col-md-5 col-lg-3 box_item">
                            <i class="fa fa-newspaper-o fa-5x h3 filter"></i>
                            <h3>Manage Orders</h3>
                            <p>Manage Order History at LSUK</p>
                        </div>
                    </a>
                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <a href="customer_user_add_comp_list.php">
                            <div class="col-md-5 col-lg-3 box_item">
                                <i class="fa fa-users fa-5x h3 filter"></i>
                                <h3>Manage <?php if ($_SESSION['role'] == 'admin') {
                                                echo 'Companies';
                                            } else {
                                                echo 'Staff';
                                            } ?></h3>
                                <p>Control Users To Access Portal</p>
                            </div>
                        </a>
                    <?php } elseif (!isset($_SESSION['operator']) && ($_SESSION['comp_nature'] == 1)) { ?>
                        <a href="customer_user_add_comp_list.php">
                            <div class="col-md-5 col-lg-3 box_item">
                                <i class="fa fa-users fa-5x h3 filter"></i>
                                <h3>Manage <?php if ($_SESSION['role'] == 'admin') {
                                                echo 'Companies';
                                            } else {
                                                echo 'Staff';
                                            } ?></h3>
                                <p>Control Users To Access Portal</p>
                            </div>
                        </a>
                    <?php } elseif (!isset($_SESSION['operator']) && ($_SESSION['comp_nature'] == 4 || $_SESSION['comp_nature'] == 3)) { ?>
                        <a href="manage_company_operators.php?id=<?php echo $_SESSION['company_login_id']; ?>&action=manage">
                            <div class="col-md-5 col-lg-3 box_item">
                                <i class="fa fa-users fa-5x h3 filter"></i>
                                <h3>Manage <?php if ($_SESSION['role'] == 'admin') {
                                                echo 'Companies';
                                            } else {
                                                echo 'Staff';
                                            } ?></h3>
                                <p>Control Users To Access Portal</p>
                            </div>
                        </a>
                    <?php }

                    if (!isset($_SESSION['operator'])) {
                        $semi = "\"'\"";
                        $logged_id = $_SESSION['cust_userId'];
                        $logged_company_id = $acttObj->read_specific("id", "comp_reg", "abrv='" . $_SESSION['cust_UserName'] . "'")['id'];
                        if ($_SESSION['comp_nature'] == 1) {
                            $all_cz = $acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,comp_reg.abrv,$semi) as all_cz", "comp_reg,subsidiaries", "comp_reg.id=subsidiaries.child_comp AND subsidiaries.parent_comp=$logged_company_id", "set SESSION group_concat_max_len=10000")['all_cz'];
                            $append_string = "orgName IN (" . $all_cz . ")";
                        } else if ($_SESSION['comp_type'] == 2) {
                            $data1 = $acttObj->read_specific("GROUP_CONCAT(comp_reg.id) as data1", "comp_reg", "id IN (" . $logged_company_id . ")");
                            $all_abrv = $acttObj->query_extra("GROUP_CONCAT(comp_reg.abrv) as all_abrv", "comp_reg", "id IN (" . $data1['data1'] . ")", "set SESSION group_concat_max_len=10000");
                            $all_cz = $acttObj->query_extra("DISTINCT GROUP_CONCAT($semi,(SELECT comp_reg.abrv from comp_reg WHERE id=child_companies.child_comp),$semi) as all_cz", "child_companies", "child_companies.parent_comp IN (" . $data1['data1'] . ")", "set SESSION group_concat_max_len=10000")['all_cz'];
                            $append_string = "orgName IN (" . $all_cz . ")";
                        } else {
                            $all_cz = "'" . $_SESSION['cust_UserName'] . "'";
                            $append_string = "orgName=" . $all_cz;
                        }
                        $p_jobs = 0;
                        $p_jobs = $acttObj->read_specific("SUM(pend_job) as pending_jobs", "(SELECT COUNT(interpreter.id) as pend_job  FROM interpreter,comp_reg", "interpreter.orgName = comp_reg.abrv AND interpreter.approve_portal_mngt=0 AND interpreter.commit=0 and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.orderCancelatoin=0 and interpreter.intrp_salary_comit=0 and interpreter.is_temp=0 and interpreter.$append_string AND interpreter.assignDate>=CURRENT_DATE() 
                            UNION 
                            SELECT COUNT(telephone.id) as pend_job  FROM telephone,comp_reg 
                            WHERE telephone.orgName = comp_reg.abrv AND telephone.approve_portal_mngt=0 and telephone.commit=0 and telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.orderCancelatoin=0 and telephone.intrp_salary_comit=0 and telephone.is_temp=0 and telephone.$append_string AND telephone.assignDate>=CURRENT_DATE() 
                            UNION 
                            SELECT COUNT(translation.id) as pend_job  FROM translation,comp_reg 
                            WHERE translation.orgName = comp_reg.abrv AND translation.approve_portal_mngt=0 and translation.commit=0 and translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.orderCancelatoin=0 and translation.intrp_salary_comit=0 and translation.is_temp=0 and translation.$append_string AND translation.asignDate>=CURRENT_DATE()) as grp  ")['pending_jobs'];
                    ?>
                        <a href="pending_orders.php">
                            <div class="col-md-5 col-lg-3 box_item">
                                <i class="fa fa-hourglass-half fa-5x h3 filter"></i>
                                <h3>Pending Approval<?php echo $p_jobs ? '(<span class="text-danger">' . $p_jobs . '</span>)' : ''; ?></h3>
                                <p>Yet to be approved by Company Manager</p>
                            </div>
                        </a>
                    <?php } ?>
                    <!--<a href="customer_account.php">
                            <div class="col-md-5 col-lg-3 box_item">
                                <i class="fa fa-lock fa-5x h3 filter"></i>
                                <h3>Update Profile</h3>
                                <p>Manage Personal Account</p>
                            </div>
                        </a>-->
                <?php } ?>
            </div>
            <div class="col-md-12">
                <hr>
                <?php //include'source/our_client.php'; 
                ?>
            </div>
        </div>
        <?php include 'source/footer.php'; ?>
    </div>
</body>

</html>