<?php include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == "" || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include_once('class.php');
include_once 'function.php';


// $selectedCompany = @$_GET['company_list'];
// $fromDate = @$_GET['from_date'] = date('Y-m-01',strtotime($_GET['from_date']));
// $toDate = @$_GET['to_date'] = date('Y-m-t',strtotime($_GET['to_date']));
// // company list dropdown
// $deleted_flag='deleted_flag = 0';
// $order_cancel_flag='order_cancel_flag = 0';
// $multInv_flag='multInv_flag=0';
// $title='';$class='label-primary';
// $companySql = $sql_opt = "SELECT DISTINCT name,abrv from (SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,interpreter.porder,interpreter.multInv_flag,interpreter.commit,interpreter.total_charges_comp,interpreter.rAmount,interpreter.orgName,interpreter.deleted_flag,interpreter.order_cancel_flag FROM comp_reg,interpreter WHERE interpreter.orgName=comp_reg.abrv AND interpreter.$deleted_flag and interpreter.$order_cancel_flag AND interpreter.$multInv_flag and interpreter.commit=0 and interpreter.assignDate like '$assignDate%' 
// UNION SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,telephone.porder,telephone.multInv_flag,telephone.commit,telephone.total_charges_comp,telephone.rAmount,telephone.orgName,telephone.deleted_flag,telephone.order_cancel_flag FROM comp_reg,telephone WHERE telephone.orgName=comp_reg.abrv AND telephone.$deleted_flag and telephone.$order_cancel_flag AND telephone.$multInv_flag and telephone.commit=0 and telephone.assignDate like '$assignDate%' and telephone.assignTime like '$aT%'  
// UNION SELECT DISTINCT comp_reg.name,comp_reg.abrv,comp_reg.po_req,translation.porder,translation.multInv_flag,translation.commit,translation.total_charges_comp,translation.rAmount,translation.orgName,translation.deleted_flag,translation.order_cancel_flag FROM comp_reg,translation WHERE translation.orgName=comp_reg.abrv AND translation.$deleted_flag and translation.$order_cancel_flag AND translation.$multInv_flag and translation.commit=0 and translation.asignDate like '$assignDate%'  ) as grp 
// ORDER BY name ASC";
// $companylist_opt = mysqli_query($con, $companySql); 
// $companylist_opt_options = "";
// while ($row_opt = mysqli_fetch_array($companylist_opt)) {
//     $code = $row_opt["abrv"];
//     $name_opt = $row_opt["name"];
//     $selected = (in_array($code, $selectedCompany)) ? "selected" : "";
//     $companylist_opt_options .= "<OPTION value='$code' $selected>" . $name_opt . ' (' . $code . ')';
// }
// company list dropdown ends
function get_number($string)
{
    $number = '';
    for ($i = strlen($string) - 1; $i >= 0; $i--) {
        if (is_numeric($string[$i])) {
            $number = $string[$i] . $number;
        } else {
            break;
        }
    }
    return $number;
}

function getAlhpabitcsFromStrings($str)
{
    $numbers = preg_replace('/[^0-9]/', '', $str);
    $letters = preg_replace('/[^a-zA-Z]/', '', $str);
    return ['numbers' => $numbers, 'letters' => $letters];
}


$record = [];
$totalMale = 0;
$totalFemale = 0;
$total = 0;
$total_languages = 0;
$languages = [];
$interp_ids = [];
if (isset($_GET) && !empty($_GET)) :
    $postCodeFrom = trim(@$_GET['postCodeFrom']);
    $postCodeTo = trim(@$_GET['postCodeTo']);
    $getRangeFrom = getAlhpabitcsFromStrings($postCodeFrom);
    $getRangeTo = getAlhpabitcsFromStrings($postCodeTo);
    // echo  $getRangeTo['numbers'];die;
    $rangString = "";
    $check = 0;
    for ($i = (int) $getRangeFrom['numbers']; $i <= (int) $getRangeTo['numbers']; $i++) :
        if ($check == 0) :
            $rangString .= "postCode LIKE '%" . $getRangeFrom['letters'] . $getRangeFrom['numbers']++ . " %' ";
        else :
            $rangString .= " OR postCode LIKE '%" . $getRangeFrom['letters'] . $getRangeFrom['numbers']++ . " %' ";
        endif;
        $check++;
    endfor;
    // echo $rangString; die;
    $postCode = "BS";
    // $acttObj->read_all_c('id,name,gender,postCode','interpreter_reg'," $rangString AND actnow='Active' AND deleted_flag = 0 "); die;
    $interp = $acttObj->read_all('id,name,gender,postCode', 'interpreter_reg', " $rangString AND actnow='Active' AND deleted_flag = 0 ");
    $record = mysqli_fetch_all($interp, MYSQLI_ASSOC);
    // echo "<pre>";print_r($record); die;
    $total = count($record);
    foreach ($record as $key => $value) :
        $getInterpLangues = $acttObj->read_all('lang,level,type', 'interp_lang', '  code = "id-' . $value['id'] . '" AND lang != "" ');
        $langs = mysqli_fetch_all($getInterpLangues, MYSQLI_ASSOC);
        array_push($interp_ids, $value['id']);
        // $total_languages += count($langs);
        $record[$key]['languages'] = $langs;
        if ($record[$key]['gender'] == "Male") :
            $totalMale++;
        else :
            $totalFemale++;
        endif;

    endforeach;

    $languagesWhere = "";
    $inc = 1;
    $interp_ids_where = "";
    foreach ($interp_ids as $id) :
        if ($inc == count($interp_ids)) :
            $languagesWhere .= "'id-" . $id . "'";
            $interp_ids_where .= $id;
        else :
            $languagesWhere .= "'id-" . $id . "',";
            $interp_ids_where .= $id . ",";

        endif;
    endforeach;
    $languagesWhere = rtrim($languagesWhere, ',');
    $interp_ids_where = rtrim($interp_ids_where, ',');

    $languages1 = $acttObj->read_all('id,lang', 'interp_lang', " code IN ($languagesWhere) AND lang !=''  GROUP BY lang");
    $languagesGetArr = mysqli_fetch_all($languages1, MYSQLI_ASSOC);
    foreach ($languagesGetArr as $key => $value) :
        $getLangSqlForTotalFemale = "SELECT * FROM interp_lang as il LEFT JOIN interpreter_reg as interp ON CONCAT('id-',interp.id) = il.code WHERE il.lang='" . $value['lang'] . "' AND interp.id IN(" . $interp_ids_where . ") AND gender = 'Female'";
        $getlanGTotalFemale = mysqli_query($con, $getLangSqlForTotalFemale);
        $getlanGTotalFemale = mysqli_num_rows($getlanGTotalFemale);

        $getLangSqlForTotalmale = "SELECT * FROM interp_lang as il LEFT JOIN interpreter_reg as interp ON CONCAT('id-',interp.id) = il.code WHERE il.lang='" . $value['lang'] . "' AND interp.id IN(" . $interp_ids_where . ") AND gender = 'Male'";
        $getlanGTotalmale = mysqli_query($con, $getLangSqlForTotalmale);
        $getlanGTotalmale = mysqli_num_rows($getlanGTotalmale);

        $languagesGetArr[$key]['total'] =  $getlanGTotalFemale + $getlanGTotalmale;
        $languagesGetArr[$key]['totalMale'] =  $getlanGTotalmale;
        $languagesGetArr[$key]['totalFemale'] =  $getlanGTotalFemale;




    endforeach;


// echo "<pre>"; print_r($languagesGetArr); echo "</pre>"; die;
endif;


?>
<!doctype html>
<html lang="en">

<head>
    <title>Interpreter list - <?= date('Y-m-d H:i:s') ?></title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <style>
        html,
        body {
            background: white !important;
        }

        .action_buttons .w3-button {
            padding: 7px 11px;
        }

        .dropdown_actions2 .dropdown-menu {
            left: auto;
            right: 0;
        }

        .action_buttons .fa,
        .action_buttons2 .fa {
            font-size: 16px;
        }

        .w3-ul li {
            border-bottom: none;
        }

        .dropdown_actions a,
        .dropdown_actions2 a {
            padding: 2px 4px !important
        }

        .dropdown_actions .dropdown-menu {
            width: max-content;
            padding: 7px 7px 0px 2px;
            bottom: -4px !important;
            top: auto;
            right: 64px !important;
            left: auto;
        }

        .dropdown_actions,
        .dropdown_actions2 {
            display: inline-block;
        }

        .lbl {
            border-radius: 0px !important;
            margin: -9px -8px !important;
            font-size: 12px;
            bottom: 0;
            right: 0;
            position: absolute;
        }

        .p3 {
            padding: 3px;
        }

        .w3-ul li {
            margin: -6px -25px;
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
        }

        .w3-small {
            padding: 1px 5px !important;
            margin-top: -6px !important;
        }

        .badge-counter {
            border-radius: 0px !important;
            margin: -9px -9px !important;
            font-size: 10px;
            float: left;
        }

        .tablesorter thead tr {
            background: none;
        }

        .mt15 {
            margin-top: 15px;
        }

        .w3-hoverable tbody tr:hover {
            background-color: #2196f30d !important;
        }

        .is_temp {
            background-color: #cbda78;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<?php include 'header.php'; ?>

<body>
    <?php include 'nav2.php'; ?>

    <div class="container-fluid">

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-graph"></i>
                            Interpreter Report
                        </h3>
                    </div>
                    <div class="panel-body">
                        <form action="" method="get" class="form">
                            <div class="row">
                                <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Select Company</label>
                                    <select name="company_list[]" id="company-list" class="form-control" multiple>
                                        <option value="">Select Company</option>
                                        <?php //echo $companylist_opt_options; 
                                        ?>
                                    </select>
                                </div>
                            </div> -->
                                <!-- <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="">From Date</label>
                                    <input type="date" name="from_date" class="form-control"  placeholder="Select From Date">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="">To Date</label>
                                    <input type="date" name="to_date" class="form-control"  placeholder="Select To Date">
                                </div>
                            </div> -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Postal Code Range From</label>
                                        <input type="text" name="postCodeFrom" id="postCode" class="form-control" value="<?= $_GET['postCodeFrom'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Postal Code Range To</label>
                                        <input type="text" name="postCodeTo" id="postCode" class="form-control" value="<?= $_GET['postCodeTo'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($record)) : ?>
            <div class="row">

            </div>
            <div class="row">
                <div class="col-sm-9">
                    <div class="panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <i class="fa fa-table"></i>
                                Interpreter List
                            </h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table1 table-hover">
                                <thead>
                                    <th>#</th>
                                    <th><strong>Postal Code</strong></th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Languages</th>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($record as $key => $value) : ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= $value['postCode'] ?></td>
                                            <td><?= $value['name'] ?></td>
                                            <td><span class="label label-primary"><?= $value['gender'] ?></span></td>
                                            <?php
                                            $languages = array_column($value['languages'], 'lang');
                                            $languages = array_unique($languages);
                                            $languages = implode(', ', $languages);
                                            ?>
                                            <td><?= $languages ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel-primary">
                        <div class="panel-heading">Graph</div>
                        <div class="panel-body">
                            <canvas id="pie-chart-1"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="panel-primary">
                        <div class="panel-heading">Languages</div>
                        <div class="panel-body">
                            <table class="table table2 table-hover">
                                <thead>
                                    <th>#</th>
                                    <th>Language</th>
                                    <th>Total Male</th>
                                    <th>Total Female</th>
                                    <th>Total</th>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($languagesGetArr as $key => $value) : ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= $value['lang'] ?></td>
                                            <td><?= $value['totalMale'] ?></td>
                                            <td><?= $value['totalFemale'] ?></td>
                                            <td><?= $value['total'] ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="panel-primary">
                        <div class="panel-heading">Graph</div>
                        <div class="panel-body">
                            <canvas id="bar-chart-2"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

    <script>
        $('.table1').DataTable({
            "order": false,
            "pageLength": 12,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
        });
        $('.table2').DataTable({
            "order": false,
            "pageLength": 12,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        var ctx = document.getElementById('pie-chart-1').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Total', 'Male', 'Female', 'Languages'],
                datasets: [{
                    data: [<?= $total ?>, <?= $totalMale ?>, <?= $totalFemale ?>, <?= count(array_column($languagesGetArr, 'lang')) ?>],
                    backgroundColor: ['orange', '#36A2EB', '#FFBAD2', 'red']
                }]
            },
            options: {
                responsive: true,
                labels: {
                    display: true,
                    fontSize: 20,
                    fontColor: 'Red',
                }
            }
        });

        var ctx = document.getElementById("bar-chart-2").getContext("2d");

        var mybarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php
                            $languages = array_column($languagesGetArr, 'lang');
                            foreach ($languages as $key => $value) {
                                echo "'" . $value . "',";
                            }
                            ?>],
                datasets: [{
                        label: 'Total',
                        backgroundColor: "#FF6600",
                        data: [<?php
                                foreach ($languagesGetArr as $key => $value) {
                                    echo $value['total'] . ",";
                                }

                                ?>]
                    },
                    {
                        label: 'Male',
                        backgroundColor: "#C7E1BA",
                        data: [<?php
                                foreach ($languagesGetArr as $key => $value) {
                                    echo $value['totalMale'] . ",";
                                }

                                ?>]
                    },
                    {
                        label: 'Female',
                        backgroundColor: "#EC799A",
                        data: [<?php
                                foreach ($languagesGetArr as $key => $value) {
                                    echo $value['totalFemale'] . ",";
                                };
                                ?>]
                    }
                ]
            },

            options: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        fontColor: "#71748d",
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
</body>

</html>