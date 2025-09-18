<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$table = 'expence';

$allowed_type_idz = "245";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
  $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
  if (empty($get_page_access)) {
    die("<center><h2 class='text-center text-danger'>You do not have access to <u>Edit Company Expense</u> action!<br>Kindly contact admin for further process.</h2></center>");
  }
}


$view_id = @$_GET['v_id'];
$query = "SELECT *, expence_list.title as expense_type FROM $table
LEFT JOIN expence_list ON expence_list.id = $table.type_id 
WHERE prepayment_id = $view_id";
$result = mysqli_query($con, $query);
//$row = mysqli_fetch_array($result);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <title>View Prepayment History</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <link rel="stylesheet" type="text/css" href="css/util.css" />
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
</head>

<body>
  <div class="container">

    <h1 class="m-b-30">Prepayment History (Track# <?php echo $view_id; ?>)</h1>

    <div class="row">
      <table class="table table-bordered table-striped table-hover">
        <thead class="thead-dark">
          <tr class="bg-primary">
            <th>Track#</th>
            <th>Voucher</th>
            <th>Expense Type</th>
            <th>Supplier</th>
            <th>Net Amount</th>
            <th>VAT</th>
            <th>Non VAT</th>
            <th>Total Amount</th>
            <th>Inv. Ref</th>
            <th>Dated</th>
            <th>By</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            // Initialize totals
            $total_netamount = 0;
            $total_vat = 0;
            $total_nonvat = 0;
            $total_amoun = 0;

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $total_netamount += $row['netamount'];
                    $total_vat += $row['vat'];
                    $total_nonvat += $row['nonvat'];
                    $total_amoun += $row['amoun'];

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['invoice_no']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['voucher']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['expense_type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['comp']) . "</td>";
                    echo "<td>" . number_format($row['netamount']) . "</td>";
                    echo "<td>" . number_format($row['vat']) . "</td>";
                    echo "<td>" . number_format($row['nonvat']) . "</td>";
                    echo "<td>" . number_format($row['amoun']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['inv_ref_num']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['dated']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['posted_by']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11' class='text-center'>No records found for this Prepayment ID.</td></tr>";
            }
          ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="4" class="text-right"><strong>Grand Total:</strong></th>
            <th><strong><?php echo number_format($total_netamount); ?></strong></th>
            <th><strong><?php echo number_format($total_vat); ?></strong></th>
            <th><strong><?php echo number_format($total_nonvat); ?></strong></th>
            <th><strong><?php echo number_format($total_amoun); ?></strong></th>
            <th colspan="3"></th>
          </tr>
        </tfoot>
      </table>


    </div>
  </div>
</body>

</html>