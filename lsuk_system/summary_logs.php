<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include_once('function.php');
include 'class.php';
$table = "daily_logs";
$get_date_1 = @$_GET['get_date_1'];
$get_date_2 = @$_GET['get_date_2'];
$get_user = @$_GET['get_user'];
if ($get_date_1) {
    $append_get_date = "and DATE($table.dated)='" . $get_date_1 . "'";
} else {
    $yesterday = date('Y-m-d', strtotime("-1 days"));
    $append_get_date = "and DATE($table.dated)='" . $yesterday . "'";
}
if ($get_date_2) {
    $append_get_date = "and DATE($table.dated)='" . $get_date_2 . "'";
} else {
    $today = date('Y-m-d');
    $append_get_date = "and DATE($table.dated)='" . $today . "'";
}
if ($get_date_1 && $get_date_2) {
    $append_get_date = "and DATE($table.dated) BETWEEN ('" . $get_date_1 . "') and ('" . $get_date_2 . "')";
}
if ($get_user) {
    $append_get_user = "and $table.user_id=" . $get_user . "";
    $append_summary_user = "and $table.user_id=" . $get_user . "";
    $get_user_name = $acttObj->read_specific("CONCAT(name,' (',prv,')') as selected_user", "login", "id=" . $get_user)['selected_user'];
}
$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;
$page_count = $startpoint;
?>
<!doctype html>
<html lang="en">

<head>
    <title>Summary Logs</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style>
        html,
        body {
            background: none !important;
        }

        .badge-counter {
            border-radius: 0px !important;
            margin: -9px -9px !important;
            font-size: 10px;
            float: left;
        }

        .pagination>.active>a {
            background: #337ab7;
        }
    </style>
    <script>
        function myFunction() {
            var get_date_1 = document.getElementById("get_date_1").value;
            if (!get_date_1) {
                get_date_1 = "<?php echo $get_date_1; ?>";
            }
            var get_date_2 = document.getElementById("get_date_2").value;
            if (!get_date_2) {
                get_date_2 = "<?php echo $get_date_2; ?>";
            }
            var get_user = document.getElementById("get_user").value;

            if (get_user === 'all') {
                get_user = "";
            } else {
                get_user = get_user;
            }
            window.location.href = "<?php echo basename(__FILE__); ?>" + '?get_date_1=' + get_date_1 + '&get_date_2=' + get_date_2 + '&get_user=' + get_user;
        }
    </script>
</head>
<?php include 'header.php'; ?>

<body>
    <?php include 'nav2.php'; ?>
    <style>
        .tablesorter thead tr {
            background: none;
        }
    </style>
    <section class="container-fluid" style="overflow-x:auto">
        <div class="col-md-12">
            <header>
                <?php $get_dates = $acttObj->read_all("DISTINCT DATE(dated)", "$table", "1");
                $options = "";
                while ($row_dates = $get_dates->fetch_assoc()) {
                    $options .= "<option value='" . $row_dates['DATE(dated)'] . "'>" . $row_dates['DATE(dated)'] . "</option>";
                } ?>
                <div class="form-group col-md-2 col-sm-4 mt15" style="margin-top: 15px;">
                    <input type="date" id="get_date_1" onChange="myFunction()" name="get_date_1" class="form-control" value="<?php echo $get_date_1; ?>" />
                </div>
                <div class="form-group col-md-2 col-sm-4 mt15" style="margin-top: 15px;">
                    <input type="date" id="get_date_2" onChange="myFunction()" name="get_date_2" class="form-control" value="<?php echo $get_date_2; ?>" />
                </div>
                <div class="form-group col-md-2 col-sm-4 mt15" style="margin-top: 15px;">
                    <select id="get_user" onChange="myFunction()" name="get_user" class="form-control">
                        <?php $get_users = $acttObj->read_all("DISTINCT $table.user_id,login.name,login.prv", "$table,login", "$table.user_id=login.id AND login.user_status=1");
                        ?>
                        <option value="" disabled>Filter By User</option>
                        <option value="all">All</option>
                        <?php while ($row_users = $get_users->fetch_assoc()) { ?>
                            <option value="<?php echo $row_users['user_id']; ?>" <?php echo ($row_users['user_id'] == $get_user) ? 'selected' : ''; ?>>
                                <?php echo $row_users['name'] . " (" . $row_users['prv'] . ")"; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-4 col-md-offset-0 col-sm-4 mt15">
                    <h2 class="text-center"><a href="<?php echo basename(__FILE__); ?>"><span class="label label-primary">System Daily Logs</a></span></h2>
                </div>
                <div class="tab_container col-md-12" id="put_data">
                    <?php
                    $result_dates = $acttObj->read_all("DISTINCT DATE(dated) as dates", "$table", "1 $append_get_date $append_get_user");
                    if ($result_dates->num_rows > 0) {
                        while ($row_dates = $result_dates->fetch_assoc()) { ?>
                            <h3><span class="label label-success"><?php echo "Logs of " . $row_dates['dates']; ?></span></h3>
                            <?php
                            $result_summary = $acttObj->read_all("count(*) as counter,user_actions.id,user_actions.summary_name", "$table,user_actions", "daily_logs.action_id=user_actions.id and DATE(daily_logs.dated)='" . $row_dates['dates'] . "' $append_get_user GROUP BY (daily_logs.action_id)");

                            if ($result_summary->num_rows == 0) { ?>
                                <center>
                                    <h4 class="text-danger text-center">Sorry ! There are no summary records currently.</h4>
                                </center>
                            <?php } else { ?>
                                <table class="tablesorter table table-bordered tbl_data" cellspacing="0" cellpadding="0">
                                    <thead class="bg-info">
                                        <tr>
                                            <td width="20%">Number of Records</td>
                                            <td>Action Taken</td>
                                            <td width="20%">Action</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row_summary = $result_summary->fetch_assoc()) {
                                            $action_id = $row_summary['id'];
                                            $to_find = array("F2F", "TP", "TR");
                                            $to_change = array("Face To Face", "Telephone", "Translation"); ?>
                                            <tr>
                                                <td><?php echo $row_summary['counter']; ?></td>
                                                <td><?php echo $row_summary["summary_name"]; ?></td>
                                                <td><a onclick="popupwindow('daily_log.php?get_date_1=<?php echo $get_date_1; ?>&get_date_2=<?php echo $get_date_2 . '&get_user=' . $get_user . '&action_id=' . $action_id; ?>', 'View daily log', 1000, 1000);" href="javascript:void(0)" class="btn btn-primary btn-sm" title="Click to view details of this log">Details</a></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                        <?php }
                        }
                    } else { ?>
                        <center>
                            <h4 class="text-danger text-center">Sorry ! There are no summary records in this range.</h4>
                        </center>
                    <?php } ?>
                </div>
    </section>
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>