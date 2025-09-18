<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'secure.php'; ?>
<?php include 'source/db.php';
include 'source/class.php';
$name = @$_GET['name'];
$gender = @$_GET['gender'];
$city = @$_GET['city'];
$intrpName = $_SESSION['web_userId']; ?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
    <?php include 'source/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap.min.css" />
    <style>
        select.input-sm {
            line-height: 22px;
        }

        .glyphicon {
            color: #fff;
        }

        input,
        textarea,
        select {
            -webkit-appearance: button;
        }

        .dataTables_wrapper .row {
            margin: 0px !important;
        }
    </style>
</head>

<body class="boxed">
    <div id="wrap">
        <?php include 'source/top_nav.php'; ?>
        <section id="page-title">
            <div class="container clearfix">
                <h1>Active Jobs List</h1>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="interp_profile.php">Home</a> &rsaquo;</li>
                    </ul>
                </nav>
            </div>
        </section>

        <section>
            <center>
                <section style="overflow-x:auto;">
                    <?php $q_jobs = $acttObj->read_all("*", "(SELECT 'interpreter' as 'type' ,null as connected_by,null as mark_join_time,null as hostedBy,interpreter.id,interpreter.source,interpreter.target, interpreter.assignDate, interpreter.assignTime,comp_reg.name,'' as comunic  FROM interpreter,comp_reg", "interpreter.orgName = comp_reg.abrv and interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.orderCancelatoin=0 and interpreter.intrpName = '$intrpName' and interpreter.hoursWorkd = 0
                        UNION
                        SELECT 'telephone' as 'type' ,telephone.connected_by,telephone.mark_join_time,telephone.hostedBy,telephone.id,telephone.source,telephone.target, telephone.assignDate, telephone.assignTime,comp_reg.name,comunic  FROM telephone,comp_reg 
                        WHERE telephone.orgName = comp_reg.abrv and telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.orderCancelatoin=0 and telephone.intrpName = '$intrpName' and 
                        telephone.hoursWorkd = 0
                        UNION
                        SELECT 'translation' as 'type',null as connected_by,null as mark_join_time,null as hostedBy,translation.id,translation.source,translation.target, translation.asignDate as 'assignDate', '00:00:00' as 'assignTime',comp_reg.name,'' as comunic  FROM translation,comp_reg 
                        WHERE translation.orgName = comp_reg.abrv and translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.orderCancelatoin=0 and translation.intrpName = '$intrpName' and translation.numberUnit = 0) as grp  order by assignDate");
                    if ($q_jobs->num_rows > 0) { ?>
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary">
                                <tr>
                                    <th>Source Language</th>
                                    <th>Target Language</th>
                                    <th>Company Name</th>
                                    <th>Assignment Date</th>
                                    <th>Assignment Time</th>
                                    <th>Job Type</th>
                                    <th width="21%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $q_jobs->fetch_assoc()) {
                                    if ($row['type'] == 'interpreter') {
                                        $get_url = 'interp_hours.php';
                                    } else if ($row['type'] == 'telephone') {
                                        $get_url = 'telep_hours.php';
                                        $get_channel = $acttObj->read_specific("c_title,c_image", "comunic_types", "c_id=" . $row['comunic']);
                                        $communication_type = empty($row['comunic']) || $row['comunic'] == 11 ? " Telephone" : " " . $get_channel['c_title'];
                                        $channel_img = file_exists('lsuk_system/images/comunic_types/' . $get_channel['c_image']) ? '<img src="lsuk_system/images/comunic_types/' . $get_channel['c_image'] . '" width="30" style="display: inline-block;"/>' : '';
                                    } else {
                                        $get_url = 'trans_hours.php';
                                    } ?>
                                    <tr title='<?php if ($row['type'] != 'translation' && date('Y-m-d H:i', strtotime($row['assignDate'] . ' ' . $row['assignTime'])) > date('Y-m-d H:i') || $row['type'] == 'translation' && date('Y-m-d', strtotime($row['assignDate'])) > date('Y-m-d')) {
                                                    echo "This assignment has not finished yet !";
                                                } ?>'>
                                        <td><?php echo $row['source']; ?></td>
                                        <td><?php echo $row['target']; ?></td>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $misc->dated($row['assignDate']); ?></td>
                                        <td><?php echo $row['assignTime']; ?></td>
                                        <td>
                                            <?php if ($row['type'] == 'interpreter') {
                                                echo '<h4><span class="label label-success">Face To Face</span></h4>';
                                            } else if ($row['type'] == 'telephone') {
                                                echo $channel_img . " <h4 style='display:inline-block'>" . $communication_type . "</h4>";
                                            } else {
                                                echo '<h4><span class="label label-warning">Translation</span></h4>';
                                            } ?></span>
                                        </td>
                                        <td><a class="btn btn-primary btn-sm" <?php if ($row['type'] != 'translation' && date('Y-m-d H:i', strtotime($row['assignDate'] . ' ' . $row['assignTime'])) <= date('Y-m-d H:i') || $row['type'] == 'translation' && date('Y-m-d', strtotime($row['assignDate'])) <= date('Y-m-d')) { ?>href="<?php echo $get_url ?>?update_id=<?php echo $row['id']; ?>" <?php } else { ?>href="javascript:void(0)" <?php } ?> data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?= ($row['type'] != 'translation' && date('Y-m-d H:i', strtotime($row['assignDate'] . ' ' . $row['assignTime'])) <= date('Y-m-d H:i') || $row['type'] == 'translation' && date('Y-m-d', strtotime($row['assignDate'])) <= date('Y-m-d')) ? "Upload Timesheet hours" : "Wait until assignment has finished!"; ?>">Update Hours</a>
                                            <a class="btn btn-success btn-sm" onclick="popupwindow('lsuk_system/reports_lsuk/pdf/new_timesheet.php?update_id=<?php echo $row['id']; ?>&table=<?php echo $row['type']; ?>&down', 'title', 1000, 1000);" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Download Timesheet">Timesheet</a>
                                            <a class="btn btn-warning btn-sm" onclick="popupwindow('lsuk_system/reports_lsuk/pdf/new_timesheet.php?update_id=<?php echo $row['id']; ?>&table=<?php echo $row['type']; ?>&emailto=<?php echo $_SESSION['email']; ?>', 'title', 1000, 1000);" href="javascript:void(0)" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Email Timesheet">Email</a>
                                            <?php
                                            $assignDateTime = strtotime($row['assignDate'] . ' ' . $row['assignTime']);
                                            $currentTime = time() + (5 * 60); // Allow marking 5 minutes before start
                                            $allowedTime = $assignDateTime;
                                            if (
                                                $row['type'] === 'telephone' &&
                                                $row['hostedBy'] == 2
                                            ) {
                                                if (empty($row['mark_join_time'])) {
                                                    $disabled = ( $currentTime <=$allowedTime) ? 'disabled' : '';
                                                    echo '<div class="div_join_time">
                                                            <button type="button"'.$disabled.' style="margin: 6px 9px 1px 2px;" data-content="Save your Join Time" data-toggle="popover" data-trigger="hover" data-placement="top" class="m-1 btn btn-sm btn-warning mark-join-time" data-job-id="' . $row['id'] . '">
                                                                Call Started
                                                            </button>
                                                        </div>';
                                                } elseif (!empty($row['mark_join_time'])) {
                                                    $name = $acttObj->read_specific("name", "interpreter_reg", "id=" . $row['connected_by'])['name'];
                                                    echo '<div class="div_join_time">
                                                               <small title="session started  by '.$name.'" class="text-success"><b>By:' .$name . '<i class="fa fa-check-circle text-success"></i><br>Session Started :' . $row['mark_join_time'] . '</b></small>
                                                            </div>';
                                                }
                                            }
                                            ?>

                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <div class="alert alert-warning text-center h3 col-sm-6 col-sm-offset-3">Sorry ! There are no active jobs yet.
                                    <br><br><a class="btn btn-info" href="interp_profile.php"><i class="glyphicon glyphicon-home"></i> Go to Profile Page</a>
                                </div>
                            <?php } ?>
                            </tbody>

                        </table>
                </section>
            </center>
            <hr>

            <!-- begin clients -->
            <?php //include'source/our_client.php'; 
            ?>
            <!-- end clients -->
        </section>
        <!-- end content -->

        <!-- begin footer -->
        <?php include 'source/footer.php'; ?>
        <!-- end footer -->
    </div>
    <script src="lsuk_system/js/jquery-1.11.3.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                drawCallback: function() {
                    $('[data-toggle="popover"]').popover({
                        html: true
                    });
                    $('[data-toggle="tooltip"]').tooltip();
                },
                "bSort": false
            });
        });
    </script>
    <script>
$(document).ready(function() {
    $('.mark-join-time').on('click', function() {
        if (!confirm("Are you sure you want to mark the join time?")) return;

        var btn = $(this);
        var jobId = btn.data('job-id');

        $.ajax({
            url: 'lsuk_system/ajax_add_interp_data.php',
            type: 'POST',
            dataType: 'json',
            data: { mark_join_time: jobId },
            success: function(data) {
                console.log(data);
                if (data.status == 1) {
                    btn.closest('.div_join_time').html(data.message);
                } else {
                    alert(data.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
});
</script>

</body>

</html>