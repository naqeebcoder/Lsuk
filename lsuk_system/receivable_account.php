<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
	session_start();
}
include 'db.php';
include 'class.php';

$search_2 = SafeVar::GetVar('search_2', '');
$search_3 = SafeVar::GetVar('search_3', '');

if (!empty($search_2) && empty($search_3)) {
    $search_3 = date("Y-m-d");
}

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;
//Access actions
// $get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=24 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
// $action_view_expense = $_SESSION['is_root'] == 1 || in_array(94, $get_actions);
// $action_edit_expense = $_SESSION['is_root'] == 1 || in_array(95, $get_actions);
// $action_delete_expense = $_SESSION['is_root'] == 1 || in_array(96, $get_actions);
// $action_restore_expense = $_SESSION['is_root'] == 1 || in_array(97, $get_actions);
// $action_expense_history = $_SESSION['is_root'] == 1 || in_array(98, $get_actions);
// $action_dropdown_filter = $_SESSION['is_root'] == 1 || in_array(99, $get_actions);

include_once('function.php');
$search2 = $search3 = '';
?>
<!doctype html>
<html lang="en">

<head>
    <title><?php echo $page_title; ?> Receivable Account</title>
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style>
        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th {
            padding: 4px !important;
            cursor: pointer;
        }

        html,
        body {
            background: #fff !important;
        }

        .div_actions {
            position: absolute;
            margin-top: -48px;
            background: #ffffff;
            border: 1px solid lightgrey;
        }

        .alert {
            padding: 6px;
        }

        .div_actions .fa {
            font-size: 14px;
        }

        .w3-btn,
        .w3-button {
            padding: 8px 10px !important;
        }

        .multiselect-container {
            height: 25rem;
            overflow: scroll;
        }

        div.btn-group,
        .btn-group button {
            width: 100% !important;
        }
    </style>
    <script type="text/javascript">
    $(function () {
        $('#search_1').multiselect({
            includeSelectAllOption: true
        });
    });



    function myFunction() {

        var y = document.getElementById("search_2").value;
        if (!y) {
            y = "<?php echo $search_2; ?>";
        }
        var z = document.getElementById("search_3").value;
        if (!z) {
            z = "<?php echo $search_3; ?>";
        }
        var strLoc = '<?php echo basename(__FILE__); ?>' + '?search_2=' + y + '&search_3=' + z;
        window.location.assign(strLoc);
    }

    // window.addEventListener('click', function(e) {
    //     if ($('#search_1').val() != null) {
    //         if (document.getElementById('search_1').contains(e.target)) {
    //             console.log('inside');
    //         } else {
    //             myFunction();
    //         }
    //     }
    // });
</script>
</head>
<?php include 'header.php'; ?>

<body>
    <?php include 'nav2.php'; ?>
    <!-- end of sidebar -->
    <style>
        .tablesorter thead tr {
            background: none;
        }
    </style>
    <section class="container-fluid">
        <div class="col-md-12">
            <header>
                <div class="row">
                    <center><a href="<?php echo basename(__FILE__); ?>">
                            <h2 class="col-md-4 col-md-offset-4 text-center" style="margin-bottom: 2rem;"><span class="label label-primary">Receivable Account</span></h2>
                        </a></center>
                </div>
                <div class="col-md-10 col-md-offset-1"><br>
                    <div class="form-group col-md-2 col-sm-3">
                        <input type="date" name="search_2" id="search_2" placeholder='' class="form-control"
                            onChange="myFunction()" value="<?php echo $search_2; ?>" />
                    </div>
                    <div class="form-group col-md-2 col-sm-3">
                        <input type="date" name="search_3" id="search_3" placeholder='' class="form-control"
                            onChange="myFunction()" value="<?php echo $search_3; ?>" />
                    </div>
                    <div class="form-group col-md-1 col-sm-4">
                        <a href="javascript:void(0)" title="Click to Get Report" onclick="myFunction()"><span
                                class="btn btn-sm btn-primary">Get Report</span></a>
                    </div>
                </div>
            </header>
            <div>
                <div>
                <?php
                    $query="SELECT id,voucher,dated,company,description,credit,debit,balance,deleted_flag FROM
                    account_receivable where deleted_flag=0 ".(!empty($search_2)?" AND dated BETWEEN '$search_2' AND '$search_3' ":"")." 
                     LIMIT {$limit}";
                    ?>
                <div class="col-md-12"><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
                <iframe id="myFrame" class="col-xs-12 " height="1600px"
                        src="reports_lsuk/pdf/rip_<?php echo basename(__FILE__); ?>?search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&startpoint=<?php echo $startpoint ?>&page=<?php echo $page ?>&limit=<?php echo $limit ?> "
                        ;></iframe>
                    <div>
                        <!-- <?php echo pagination($con, $table, $query, $limit, $page); ?> -->
                    </div>
                </div>
    </section>
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"
        rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"
        type="text/javascript"></script>
</body>

</html>