<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
$org = @$_GET['org'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
// if(empty($search_2)){$search_2= date("Y-m-d");}if(empty($search_3)){$search_3= date("Y-m-d");}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Booking Report</title>
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>

<body>
    <script>
        function myFunction() {

            var y = document.getElementById("search_2").value;
            if (!y) {
                y = "<?php echo $search_2; ?>";
            }
            var z = document.getElementById("search_3").value;
            if (!z) {
                z = "<?php echo $search_3; ?>";
            }
            var x = document.getElementById("org").value;
            if (!x) {
                x = "";
            }
            window.location.href = "<?php echo basename(__FILE__); ?>" + '?search_2=' + y + '&search_3=' + z + '&org=' +
                x;

        }

        function myFunction2() {
            var append_url = "<?php echo basename(__FILE__) . " ? 1 "; ?>";
            var org = $("#org").val();
            if (org) {
                append_url += '&org=' + org;
            }
            window.location.href = append_url;
        }
    </script>
    <?php include 'nav2.php'; ?>

    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <header>
                    <div class="row">
                        <center><a href="<?php echo basename(__FILE__); ?>">
                                <h2 class="col-md-4 col-md-offset-4 text-center"><span class="label label-primary">Next
                                        Day
                                        Bookings Report</span></h2>
                            </a></center>
                    </div>
                    <div class="row">
                        <div class="col-md-12"><br>
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group " style="margin-bottom:0 !important">
                                    <label for="search_2">From:</label>
                                    <input type="date" name="search_2" id="search_2" class="form-control"
                                        value="<?php echo $search_2; ?>" />
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group " style="margin-bottom:0 !important">
                                    <label for="search_3">To:</label>
                                    <input type="date" name="search_3" id="search_3" class="form-control"
                                        value="<?php echo $search_3; ?>" />
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group " style="margin-bottom:0 !important;margin-top:1.8rem;">
                                    <select id="org" name="org" class="form-control searchable">
                                        <?php
                                            $sql_opt = "SELECT name,abrv FROM comp_reg where name LIKE 'VHS%' AND deleted_flag = 0 ORDER BY name ASC";
                                            $result_opt = mysqli_query($con, $sql_opt);
                                            $options = "";
                                            while ($row_opt = mysqli_fetch_array($result_opt)) {
                                                $code = $row_opt["abrv"];
                                                $name_opt = $row_opt["name"];
                                                $options .= "<OPTION value='$code'>" . $name_opt . ' (' . $code . ')';
                                            }
                                            ?>
                                        <?php if (!empty($org)) { ?>
                                        <option><?php echo $org; ?></option>
                                        <?php } else { ?>
                                        <option value="">Select Organization</option>
                                        <?php } ?>
                                        <?php echo $options; ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="btns_action" style="margin-top:1.5rem;width:100%">
                        <div class="col-md-12 col-sm-12">
                            <div class="col-md-2 col-sm-2">
                                <div class="form-group " style="margin-bottom:0 !important">
                                    <a href="javascript:void(0)" title="Click to Get Report"
                                        onclick="myFunction()"><span class="btn btn-sm btn-primary">Get
                                            Report</span></a>
                                </div>
                            </div>
                            <?php
                            if (isset($org) && !empty($org)) {
                                $p_org = $org;
                            } else {
                                $p_org = "";
                            }

                            if ((isset($search_2) && !empty($search_2)) && (isset($search_3) && !empty($search_3))) {
                                $ex_srch2 = $search_2;
                                $ex_srch3 = $search_3;
                            } else {
                                $ex_srch2 = "";
                                $ex_srch3 = "";
                            }
                            ?>
                            <div class="col-md-2 col-sm-2 ">
                                <div class="form-group ">
                                    <a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?search_2=<?php echo $ex_srch2; ?>&search_3=<?php echo $ex_srch3; ?>&org=<?php echo $p_org; ?>"
                                        title="Download Excel Report"><span class="btn btn-sm btn-success">Export To
                                            Excel</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>


                </header>
            </div>
        </div>

        <div class="tab_container">
            <!-- <div id="tab1" class="tab_content" align="center">
                    <iframe id="myFrame" class="col-xs-10 col-xs-offset-1" style="height:135rem"
                        src="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>"></iframe>
                </div> -->
            <?php
                if (isset($org) && !empty($org)) {
                    $append_orgName_int = " and interpreter.orgName = '" . $org . "'";
                    $append_orgName_tp = " and telephone.orgName = '" . $org . "'";
                    $append_orgName_tr = " and translation.orgName = '" . $org . "'";
                    $append_orgName_all = " and orgName = '" . $org . "'";
                } else {
                    $append_orgName_int = " and comp_reg.name LIKE 'VHS%' ";
                    $append_orgName_tp = " and comp_reg.name LIKE 'VHS%' ";
                    $append_orgName_tr = " and comp_reg.name LIKE 'VHS%' ";
                    $append_orgName_all = " and comp_reg.name LIKE 'VHS%' ";
                }
                if ((isset($search_2) && !empty($search_2)) && (isset($search_3) && !empty($search_3))) {
                    $append_dates = " assignDate BETWEEN '$search_2' AND '$search_3' ";
                } else {
                    $append_dates = " DATE(assignDate)=DATE(NOW() + INTERVAL 1 DAY) ";
                }

                $bkr = "SELECT * from (SELECT 'Face 2 Face' as type,'none' as communication_type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,interpreter.source,interpreter.target,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.dated FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_orgName_int AND interpreter.multInv_flag=0 AND interpreter.deleted_flag=0  and interpreter.order_cancel_flag=0 and interpreter.commit=0  and interpreter.orderCancelatoin=0  
                    UNION ALL SELECT 'Telephone' as type,(SELECT comunic_types.c_title from comunic_types WHERE comunic_types.c_id=telephone.comunic) as communication_type,telephone.id,telephone.intrpName,telephone.orgName,telephone.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.dated FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_orgName_tp AND telephone.multInv_flag=0 AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.commit=0 and telephone.orderCancelatoin=0
                    UNION ALL SELECT 'Translation' as type,'none' as communication_type,translation.id,translation.intrpName,translation.orgName,translation.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,translation.source,translation.target,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.dated FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_orgName_tr AND translation.multInv_flag=0 AND translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.commit=0  and translation.orderCancelatoin=0 ) as grp WHERE $append_dates ORDER BY assignDate ASC";

                // $bkr = "SELECT * from (SELECT 'Face 2 Face' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,interpreter.source,interpreter.target,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.dated FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_orgName_int AND interpreter.multInv_flag=0 AND interpreter.deleted_flag=0  and interpreter.order_cancel_flag=0 and interpreter.commit=0  and interpreter.orderCancelatoin=0  
                // UNION ALL SELECT 'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.dated FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_orgName_tp AND telephone.multInv_flag=0 AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.commit=0 and telephone.orderCancelatoin=0
                // UNION ALL SELECT 'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,translation.source,translation.target,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.dated FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_orgName_tr AND translation.multInv_flag=0 AND translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.commit=0  and translation.orderCancelatoin=0 ) as grp WHERE DATE(assignDate)=DATE(NOW() + INTERVAL 1 DAY) ORDER BY CONCAT(assignDate,' ',assignTime)";

                // $bkr = "SELECT * from (SELECT 'Face 2 Face' as type,interpreter.id,interpreter.intrpName,interpreter.orgName,interpreter.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,interpreter.source,interpreter.target,interpreter.assignDate,interpreter.assignTime,interpreter.orgContact,interpreter.dated FROM interpreter,interpreter_reg,comp_reg WHERE interpreter.intrpName=interpreter_reg.id AND interpreter.orgName=comp_reg.abrv $append_orgName_int AND interpreter.multInv_flag=0 AND interpreter.deleted_flag=0  and interpreter.order_cancel_flag=0 and interpreter.commit=0  and interpreter.orderCancelatoin=0 
                // UNION ALL SELECT 'Telephone' as type,telephone.id,telephone.intrpName,telephone.orgName,telephone.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,telephone.source,telephone.target,telephone.assignDate,telephone.assignTime,telephone.orgContact,telephone.dated FROM telephone,interpreter_reg,comp_reg WHERE telephone.intrpName=interpreter_reg.id AND telephone.orgName=comp_reg.abrv $append_orgName_tp AND telephone.multInv_flag=0 AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.commit=0 and telephone.orderCancelatoin=0
                // UNION ALL SELECT 'Translation' as type,translation.id,translation.intrpName,translation.orgName,translation.orgRef,interpreter_reg.name as int_name,interpreter_reg.email as int_email,interpreter_reg.id as int_id,interpreter_reg.contactNo as int_cont,translation.source,translation.target,translation.asignDate as assignDate,'00:00:00' as assignTime,translation.orgContact,translation.dated FROM translation,interpreter_reg,comp_reg WHERE translation.intrpName=interpreter_reg.id AND translation.orgName=comp_reg.abrv $append_orgName_tr AND translation.multInv_flag=0 AND translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.commit=0  and translation.orderCancelatoin=0 ) as grp ORDER BY CONCAT(assignDate,' ',assignTime) LIMIT 10";
                // $bkr = "select id from interpreter LIMIT 10";
                ?>
            <table id="bk_table" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Interp Name - Contact</th>
                        <th scope="col">Type</th>
                        <th scope="col">Source- Target</th>
                        <th scope="col">Assignment Date and Time</th>
                        <th scope="col">Organization</th>
                        <th scope="col">Client Name</th>
                        <th scope="col">Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $ex_bkr = mysqli_query($con, $bkr);
                        while ($bk_row = mysqli_fetch_assoc($ex_bkr)) {
                            echo "<tr><td>" . $bk_row['int_name'] . "<br>" . $bk_row['int_cont'] . "<br>" . $bk_row['int_email'] . "</td><td>" .($bk_row['communication_type']!="none"?"Remote - ".$bk_row['communication_type']:$bk_row['type']). "</td><td>" . $bk_row['source'] . " to " . $bk_row['target'] . "</td><td>" . $bk_row['assignDate'] . " " . $bk_row['assignTime'] . "</td><td>" . $bk_row['orgName'] . "</td><td>" . $bk_row['orgContact'] . "</td><td>" . $bk_row['orgRef'] . "</td></tr>";
                        }
                        ?>
                </tbody>
            </table>


        </div><!-- end of .tab_container -->

        </article><!-- end of content manager article -->
        <!-- end of messages article -->

        <div class="clear"></div>

        <!-- end of post new article -->

        <div class="spacer"></div>
    </section>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"
    rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"
    type="text/javascript"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script>
    $(function () {
        var dtToday = new Date();

        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();
        if (month < 10)
            month = '0' + month.toString();
        if (day < 10)
            day = '0' + day.toString();

        var maxDate = year + '-' + month + '-' + day;

        // or instead:
        // var maxDate = dtToday.toISOString().substr(0, 10);

        // alert(maxDate);
        // $('#search_2').attr('min', maxDate);
        // $('#search_3').attr('min', maxDate);

    });
    $(function () {
        $('.searchable').multiselect({
            includeSelectAllOption: true,
            numberDisplayed: 1,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true
        });
    });

    function MM_openBrWindow(theURL, winName, features) {
        window.open(theURL, winName, features);
    }
    $(document).ready(function () {
        $('#bk_table').dataTable({
            order: [
                [3, 'asc']
            ],
        });
    });
</script>

</html>