<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$table = 'receivable';
$allowed_type_idz = "112";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Receivable Installments</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$edit_id = $_GET['row_id'];
$edit = $_GET['edit'];
$del = $_GET['del'];
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Receive Partial Amount</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <style>
        .b {
            color: #fff;
        }

        a:link,
        a:visited {
            color: #337ab7;
        }
    </style>
</head>
<script>
    function confirm_delete() {
        var result = confirm("Are you sure to delete this record ?");
        if (result == true) {
            return true;
        } else {
            return false;
        }
    }
</script>

<body>
    <?php
    $query = "SELECT * FROM $table where id=$edit_id";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_array($result);
    $balance = $row['balance'];


    if (isset($edit) && !empty($edit)) {
        $row_partials = $acttObj->read_specific('*', 'loan_repay', 'id=' . $_GET['id']);
        $amountedit = $row_partials['amount'];
        $id = $row_partials['id'];
        $paid_date = $row_partials['paid_date'];
    }

    ?>
    <div class="container">
        <form action="process.php" method="post" class="col-md-12">
            <h3 class="text-center">Receivable Loan Amount</h3>
            <p class="text-center text-danger"><b>NOTE :</b> Total amount for this loan is : <?php echo '<b>' . $balance . '</b>'; ?></p>

            <?php
            if (isset($_SESSION['success']) and $_SESSION['success'] != '') { ?>
                <div class="alert alert-success col-md-6">
                    <?php echo $_SESSION['success']; ?>
                </div>

            <?php }
            unset($_SESSION['success']);
            ?>

            <?php
            if (isset($_SESSION['error']) and $_SESSION['error'] != '') { ?>
                <div class="alert alert-danger col-md-6">
                    <?php echo $_SESSION['error']; ?>
                </div>

            <?php }
            unset($_SESSION['error']);
            ?>

            <span <?php if (isset($edit) && !empty($edit)) {
                        echo ' id="e_display_msg"';
                    } else { ?> id="display_msg" <?php } ?>><?php if (isset($msg) && !empty($msg)) {
                                                                                                                                    echo $msg;
                                                                                                                                } ?></span>

            <div class="form-group col-sm-6">
                <label>Amount Received *</label>
                <input <?php if (isset($edit) && !empty($edit)) {
                            echo 'oninput="e_value_amount()"';
                        } else { ?> oninput="value_amount()" <?php } ?> name="amount" class="form-control" type="text" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="amount" required='' value="<?php if (isset($edit) && !empty($edit)) {
                                                                                                                                                                                                                                                                                echo $amountedit;
                                                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                                                echo '0';
                                                                                                                                                                                                                                                                            } ?>" />
            </div>
            <div class="form-group col-sm-6">
                <label>Paid Date *</label>
                <input name="paid_date" type="date" class="form-control" required='' value="<?php echo !empty($paid_date) && $paid_date != '1001-01-01' ? $paid_date : ''; ?>" />
            </div>



            <div class="form-group col-sm-6">
                <?php if (isset($edit) && !empty($edit)) { ?>
                    <input type="hidden" name="loan_id" value="<?php echo $edit_id; ?>">
                    <input type="hidden" name="ramount" value="<?php echo $balance; ?>">

                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <input type="hidden" name="partial_submit_edit" value="1">
                <?php } else { ?>
                    <input type="hidden" name="partial_submit_add" value="1">
                    <input type="hidden" name="loan_id" value="<?php echo $edit_id; ?>">
                    <input type="hidden" name="ramount" value="<?php echo $balance; ?>">

                <?php } ?>
                <button class="btn btn-primary" type="submit" <?php if (isset($edit) && !empty($edit)) { ?> id="e_btn_submit" name="partial_edit" <?php } else { ?> id="btn_submit" name="partial_submit" <?php } ?>>Submit &raquo;</button>
            </div>
        </form>
        <br>
        <?php $row_part = $acttObj->read_all('*', 'loan_repay', 'loan_id=' . $edit_id);

        if (mysqli_num_rows($row_part) > 0) { ?>
            <table class="table table-bordered table-hover">
                <thead>
                    <th>Amount</th>
                    <th>Paid Date</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <?php while ($row_data = mysqli_fetch_assoc($row_part)) {
                        if ($row_data['tbl'] == 'int') {
                            $tab = 'interpreter';
                        } else if ($row_data['tbl'] == 'tp') {
                            $tab = 'telephone';
                        } else {
                            $tab = 'translation';
                        }
                    ?>
                        <tr <?php if ((isset($_GET['edit']) || isset($_GET['del'])) && ($_GET['id'] == $row_data['id'])) {
                                echo 'class="bg-success"';
                            } ?>>
                            <td><?php echo $row_data['amount']; ?></td>
                            <td><?php echo $row_data['dated']; ?></td>

                            <td><a href="<?php echo basename(__FILE__) . '?row_id=' . $row_data['loan_id'] . '&id=' . $row_data['id'] . '&edit=1'; ?>" title="Edit Record"><input type="image" src="images/icn_edit.png" title="Edit"></a>
                                <a onclick='return confirm_delete();' href="process.php?row_id=<?php echo $row_data['loan_id'] . '&id=' . $row_data['id'] . '&del=1'; ?>" title="Trash Record"><input type="image" src="images/icn_trash.png" title="Trash"></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else {
            echo '<h3 class="text-danger text-center col-sm-12"> <span class="label label-danger">No installments added yet !</span></h3>';
        } ?>
    </div>
</body>
<script>
    function value_amount() {
        var amount_val = document.getElementById('amount');
        var display_msg = document.getElementById('display_msg');
        var btn_submit = document.getElementById('btn_submit');
        if (!(/^[-+]?\d*\.?\d*$/.test(amount_val.value))) {
            btn_submit.disabled = true;
            display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is not a number !</b></div>';
        } else {
            if (amount_val.value > <?php echo $balance; ?>) {
                btn_submit.disabled = false;
                display_msg.innerHTML = '<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4"><b>Entered value is greater  than loan amount <?php echo $final_sum; ?></b></div>';
            } else if (amount_val.value < <?php echo $balance; ?>) {
                btn_submit.disabled = false;
                display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is less than loan amount <?php echo $final_sum; ?></b></div>';
            } else {
                btn_submit.disabled = false;
                display_msg.innerHTML = '';
            }
        }
    }

    function e_value_amount() {
        var e_amount_val = document.getElementById('amount');
        var e_display_msg = document.getElementById('e_display_msg');
        var e_btn_submit = document.getElementById('e_btn_submit');
        if (!(/^[-+]?\d*\.?\d*$/.test(e_amount_val.value))) {
            e_btn_submit.disabled = true;
            e_display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is not a number !</b></div>';
        } else {
            if (e_amount_val.value > <?php echo $balance; ?>) {
                e_btn_submit.disabled = false;
                e_display_msg.innerHTML = '<div class="alert alert-warning col-md-6 col-md-offset-3 text-center h4"><b>Entered value is greater  than loan balance amount <?php echo $final_sum; ?></b></div>';
            } else if (e_amount_val.value < <?php echo $balance; ?>) {
                e_btn_submit.disabled = false;
                e_display_msg.innerHTML = '<div class="alert alert-danger col-md-6 col-md-offset-3 text-center h4"><b>Entered value is less than loan balance amount <?php echo $amount; ?></b></div>';
            } else {
                e_btn_submit.disabled = false;
                e_display_msg.innerHTML = '';
            }
        }
    }
</script>

</html>