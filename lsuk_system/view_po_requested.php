<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
$allowed_type_idz = "171";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Requested Purchase Order</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Manage Purchase Orders</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>

<body>
    <?php
    $order_id = $_GET['order_id'];
    $get_order_type = $_GET['order_type'];
    if ($get_order_type == "f2f") {
        $order_type = "interpreter";
    } else if ($get_order_type == "tp") {
        $order_type = "telephone";
    } else {
        $order_type = "translation";
    }
    $table = "po_requested";
    if (isset($_POST['btn_submit'])) {
        if ($_POST['inchEmail']) {
            $update_array['inchEmail'] = $_POST['inchEmail'];
            if ($_POST['porder_email']) {
                $update_array['porder_email'] = $_POST['porder_email'];
            }
            $done = $acttObj->update($order_type, $update_array, array("id" => $order_id));
            if ($done) {
                echo "<script>alert('Record successfully updated. Thank you')</script>";
                window . close();
            } else {
                echo "<script>alert('Sorry ! Failed to update this record.')</script>";
            }
        }
    } ?>
    <div class="container text-center">
        <br><br>
        <h4>Update emails for this record ID # <span class="label label-primary"><?php echo $order_id; ?></span></h4>
        <?php $row_comp = $acttObj->read_specific("comp_reg.name,comp_reg.po_req,comp_reg.po_email,$order_type.orgName,$order_type.inchEmail,$order_type.porder_email", "$order_type,comp_reg", "$order_type.orgName=comp_reg.abrv AND $order_type.id=" . $order_id); ?>
        <div class="row">
            <form class="col-md-8 col-md-offset-2" method="POST" action="">
                <table class="table table-bordered table-hover">
                    <tbody>
                        <tr>
                            <td>Company</td>
                            <th align="left" class="small"><?php echo $row_comp['name'] . " (" . $row_comp['orgName'] . ")"; ?> </th>
                        </tr>
                        <tr>
                            <td>Booking Confirmation Email</td>
                            <td align="left" title="Booking Confirmation Email">
                                <?php if (!$row_comp['inchEmail']) { ?>
                                    <p><small><?php echo "Do you want to use <b>" . $row_comp['email'] . "</b> ?"; ?></small></p>
                                <?php } else { ?>
                                    <p><small>Current booking confirmation email</small></p>
                                <?php } ?>
                                <input type="text" name="inchEmail" value="<?php echo $row_comp['inchEmail']; ?>" class="form-control" required>
                            </td>
                        </tr>
                        <tr>
                            <?php if ($row_comp['po_req'] == 1) { ?>
                                <td>Purchase Order Email</td>
                                <td align="left" title="Purchase Order Email">
                                    <?php if (!$row_comp['porder_email']) { ?>
                                        <p><small><?php echo "Do you want to use <b>" . $row_comp['po_email'] . "</b> ?"; ?></small></p>
                                    <?php } else { ?>
                                        <p><small>Current purchase order email</small></p>
                                    <?php } ?>
                                    <input type="text" name="porder_email" value="<?php echo $row_comp['porder_email']; ?>" class="form-control" required>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td align="left" colspan="2">
                                <button type="submit" name="btn_submit" class="btn btn-primary" title="Click to update this record">Update</button>
                            </td>
                        </tr>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div><br><br>
        <h4>List of reminder notifications sent for purchase orders </h4>
        <table class="table table-bordered table-hover">
            <tbody id="append_childs">
                <?php $result = $acttObj->read_all("$table.*,$order_type.nameRef,$order_type.invoiceNo,$order_type.credit_note", "$table,$order_type", "$table.order_id=$order_type.id and $table.order_id=$order_id AND $table.order_type='" . $get_order_type . "' ORDER BY $table.id ASC"); ?>
                <table class="tablesorter table table-bordered" cellspacing="0" cellpadding="0">
                    <thead class="bg-info">
                        <tr>
                            <td>Order ID</td>
                            <td>Order Type</td>
                            <td>Invoice No</td>
                            <td>Reference No</td>
                            <td>PO# sent email</td>
                            <td>Sent Date</td>
                            <td>Notification Type</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows == 0) {
                            echo '<tr>
            		  <td colspan="7"><h4 class="text-danger text-center">Sorry ! There are no records.</h4></td></tr>';
                        } else {
                            $counter = 0;
                            while ($row = $result->fetch_assoc()) {
                                //Invoice number with credit note been made
                                if (!empty($row['credit_note'])) {
                                    $row['invoiceNo'] = $row['invoiceNo'] . "-0" . $acttObj->read_specific("count(*) as counter", "credit_notes", "order_id=" . $row['order_id'] . " and order_type='" . $row['order_type'] . "'")['counter'];
                                }
                                $counter++;
                                $follow_counter = $counter - 1; ?>
                                <tr>
                                    <td><?php echo $row['order_id']; ?></td>
                                    <td><?php if ($row['order_type'] == 'f2f') {
                                            echo "<span class='label label-success'>Face To Face</span>";
                                        } else if ($row['order_type'] == 'tp') {
                                            echo "<span class='label label-info'>Telephone</span>";
                                        } else {
                                            echo "<span class='label label-warning'>Translation</span>";
                                        } ?></td>
                                    <td><?php echo "<span class='label label-primary'>" . $row['invoiceNo'] . "</span>"; ?></td>
                                    <td><?php echo $row['nameRef']; ?></td>
                                    <td><?php echo $row['to_email']; ?></td>
                                    <td><?php echo $row['dated']; ?></td>
                                    <td><?php echo $counter == 1 ? "Initial reminder" : "Follow up reminder " . $follow_counter; ?></td>
                                </tr>
                            <?php } ?>
                    </tbody>
                </table>
    </div>
<?php } ?><br><br><br>
</div>
<script src="js/jquery-1.11.3.min.js"></script>
</body>

</html>