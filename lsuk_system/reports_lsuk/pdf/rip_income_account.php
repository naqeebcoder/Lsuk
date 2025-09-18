<?php
include 'db.php';
include 'class.php';

if (!empty($search_2) && empty($search_3)) {
    $search_3 = date("Y-m-d");
}

$search_2 = @$_GET['search_2'];
$search_3 = @$_GET['search_3'];
$mak_page = @$_GET['page'];
$startpoint = @$_GET['startpoint'];
$limit = @$_GET['limit'];

include_once('function.php');
$search2 = $search3 = '';
?>
<!doctype html>
<html lang="en">

<head>
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
        .tablesorter thead tr {
            background: none;
        }
    </style>
</head>

<body>
    <section class="container-fluid">
        <div class="col-md-12">
            <div>
                <div>
                    <table class="table table-bordered table-hover" cellspacing="0" width="100%">
                        <thead class="bg-primary">
                            <tr>
                                <th>Date</th>
                                <th>Voucher</th>
                                <th>Company</th>
                                <th>Description</th>
                                <th>Credit</th>
                                <th>Debit</th>
                                <th>Balance </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $fetch_jobs = $acttObj->read_all("id,voucher,dated,company,description,credit,debit,balance,deleted_flag","account_income"," deleted_flag=0 ".(!empty($search2)?" AND dated BETWEEN '$search2' AND '$search3' ":"")." LIMIT {$startpoint} , {$limit} ");

                            ?>
                            <tr class="tr_data">
                                <?php 
                                $count=1;
                                $balance = 0;
                                // echo "<br>rows: ".mysqli_num_rows($fetch_jobs)."rows";
                                if(mysqli_num_rows($fetch_jobs)>0){
                                    while($row = mysqli_fetch_assoc($fetch_jobs)){
                                        $voucher = $row['voucher'];
                                        $assignDate = $row['dated'];
                                        $company = $row['company'];
                                        $credit = $row['credit'];
                                        $debit = $row['debit'];
                                        $description = $row['description'];
                                        $balance = $row['balance'];
                                        ?>
                                        <tr>
                                            <td><?php echo $assignDate; ?></td>
                                            <td><?php echo $voucher; ?></td>
                                            <td><?php echo $company; ?></td>
                                            <td><?php echo $description; ?></td>
                                            <td><?php echo $credit; ?></td>
                                            <td><?php echo $debit; ?></td>
                                            <td><?php echo $balance; ?></td>
                                        </tr>
                                        <?php

                                        $count++;
                                    }
                                }
                                ?>
                            </tr>

                        </tbody>
                    </table>
                </div>
    </section>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"
        rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"
        type="text/javascript"></script>
</body>

</html>