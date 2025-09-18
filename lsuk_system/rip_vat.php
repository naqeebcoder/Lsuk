<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
$search_1 = @$_GET['search_1'];
$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
if (empty($search_2)) {
    $search_2 = date("Y-m-d");
}
if (empty($search_3)) {
    $search_3 = date("Y-m-d");
} ?>
<!doctype html>
<html lang="en">

<head>
    <title>Company VAT Report</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style>
        .lbl {
            vertical-align: text-top;
        }

        .multiselect {
            min-width: 190px;
        }

        .multiselect-container {
            max-height: 400px;
            overflow-y: auto;
            max-width: 380px;
        }

        .tab_container {
            min-height: 700px;
            ]
    </style>
</head>

<body>
    <?php include "incmultiselfiles.php"; ?>
    <script type="text/javascript">
        $(function() {
            $('#search_1').multiselect({
                includeSelectAllOption: true,
                numberDisplayed: 1,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                nonSelectedText: 'Interpreter Vat Number'
            });
        });

        function myFunction_date() {
            var x = $('#search_1').val();
            var y = $('#search_2').val();
            var z = $('#search_3').val();
            window.location.assign('<?php echo basename(__FILE__); ?>?search_1=' + x + '&search_2=' + y + '&search_3=' + z);

        }
    </script>
    <?php include 'nav2.php'; ?>

    <section class="container-fluid" style="overflow-x:auto">
        <div class="col-md-12">
            <header>
                <center><a href="<?php echo basename(__FILE__); ?>">
                        <h2 class="col-md-4 col-md-offset-4 text-center"><span class="label label-primary">Company VAT Report</span></h2>
                    </a>
                </center>
                <div class="col-sm-11 col-sm-offset-1"><br>
                    <div class="form-group col-sm-3">
                        <select id="search_1" name="search_1" class="form-control" multiple="multiple">
                            <?php
                            $sql_opt = "select DISTINCT vat_no_int from (SELECT distinct interpreter.vat_no_int as vat_no_int FROM interpreter WHERE interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and interpreter.vat_no_int!=''
UNION ALL
SELECT distinct telephone.vat_no_int as vat_no_int FROM telephone WHERE telephone.deleted_flag=0 and telephone.order_cancel_flag=0 and telephone.vat_no_int!=''
UNION ALL
SELECT distinct translation.vat_no_int as vat_no_int FROM translation WHERE translation.deleted_flag=0 and translation.order_cancel_flag=0 and translation.vat_no_int!='') as grp_vat_no";
                            $result_opt = mysqli_query($con, $sql_opt);
                            $options = "";
                            while ($row_opt = mysqli_fetch_array($result_opt)) {
                                $vat_no_int = $row_opt["vat_no_int"];
                                $options .= "<OPTION >" . $vat_no_int;
                            }
                            ?>
                            <?php echo $options; ?>
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <input placeholder="Start Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="search_2" id="search_2" class="form-control" value="<?php echo isset($_GET['search_2']) && !empty($_GET['search_2']) ? $_GET['search_2'] : ''; ?>" />
                    </div>
                    <div class="form-group col-sm-2">
                        <input placeholder="End Date" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="search_3" id="search_3" class="form-control" value="<?php echo isset($_GET['search_3']) && !empty($_GET['search_3']) ? $_GET['search_3'] : ''; ?>" />
                    </div>
                    <div class="form-group col-md-1 col-sm-2">
                        <a href="javascript:void(0)" title="Click to Get Report" onclick="myFunction_date()"><span class="btn btn-sm btn-primary">Get Report</span></a>
                    </div>
                    <div class="form-group col-md-1 col-sm-2">
                        <a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>" title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel</span></a>
                    </div>
                </div>
            </header>


            <div class="tab_container">
                <div id="tab1" class="tab_content" align="center">

                    <iframe class="col-xs-10 col-xs-offset-1" height="1000px" src="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>"></iframe>

                </div><!-- end of #tab1 -->



            </div><!-- end of .tab_container -->

            </article><!-- end of content manager article --><!-- end of messages article -->

            <div class="clear"></div>

            <!-- end of post new article -->

            <div class="spacer"></div>
    </section>


</body>

</html>