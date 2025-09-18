<?php if (session_id() == '' || !isset($_SESSION)) {
  session_start();
}
include 'db.php';
include 'class.php';
$table = 'sup_reg';
$allowed_type_idz = "102";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Supplier</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$edit_id = $_GET['edit_id'];
$query = "SELECT * FROM $table where id=$edit_id";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);
$sp_name = $row['sp_name'];
$sp_abrv = $row['sp_abrv'];
$sp_rnum = $row['sp_rnum'];
$sp_code = $row['sp_code'];
$sp_contact = $row['sp_contact'];
$sp_email = $row['sp_email'];
$sp_type = $row['sp_type'];
$sp_web = $row['sp_web'];
$sp_web = $row['sp_web'];
//VAT INFO
$tax_reg = $row['tax_reg'];
$uk_citizen = $row['uk_citizen'];
$uk_citizen_vatNum = $row['uk_citizen_vatNum'];
$country_vat = $row['country_vat'];
$country_vatNum = $row['country_vatNum'];

//ADDRESS
$sp_country = $row['sp_country'];
$sp_city = $row['sp_city'];
$sp_postCode = $row['sp_postCode'];
$sp_buildingName = $row['sp_buildingName'];
$sp_streetRoad = $row['sp_streetRoad'];
$sp_line1 = $row['sp_line1'];
$sp_line2 = $row['sp_line2'];

//BANK DETAILS
$sp_bnkName = $row['sp_bnkName'];
$sp_acName = $row['sp_acName'];
$sp_acNum = $row['sp_acNum'];
$sp_sCode = $row['sp_sCode'];

// CONTACT PERSON DETAILS
$sp_cpName = $row['sp_cpName'];
$sp_cppos = $row['sp_cppos'];
$sp_cpNum = $row['sp_cpNum'];
$sp_cpEmail = $row['sp_cpEmail'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>View Company Record</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <?php include 'ajax_uniq_fun.php'; ?>
</head>

<body>
    <div class="container-fluid">
        <table class="table table-bordered table-striped">
            <caption class="h4 bg-info text-center">Details of <span class="h4 label label-success"><?php echo $abrv; ?></span> (<?php echo $name; ?>)</caption>
            <tr>
                <td width="20%">Supplier Name</td>
                <td width="30%"><?php echo $sp_name; ?></td>
                <td width="20%">Supplier Abbreviation</td>
                <td width="30%"><?php echo $sp_abrv; ?></td>
            </tr>
            <tr>
                <td>Supplier Type</td>
                <td><?php echo $sp_type; ?></td>
                <td>Supplier Registration Number </td>
                <td><?php echo $sp_rnum; ?></td>
            </tr>
            <tr>
                <td>Supplier Contact</td>
                <td><?php echo $sp_contact; ?></td>
                <td>Suppier Email</td>
                <td><?php echo $sp_email; ?></td>
            </tr>
            <tr>
                <td>Supplier Website</td>
                <td><?php echo $sp_web; ?></td>
            </tr>
        </table>
        <table class="table table-bordered table-striped">
            <caption class="h4 bg-info text-center">VAT INFO</caption>
            <tr>
                <td width="20%">Registered for Taxes</td>
                <td width="30%"><?php echo ($tax_reg == 1 ? 'YES' : 'NO'); ?></td>
            </tr>
            <tr>
                <td width="20%">UK Based</td>
                <td width="30%"><?php echo ($uk_citizen == 1 ? 'YES' : 'NO'); ?></td>
                <td width="20%">UK VAT number</td>
                <td width="30%"><?php echo $uk_citizen_vatNum; ?></td>
            </tr>
            <tr>
                <td>Nationality (Other than UK)</td>
                <td><?php echo $country_vat; ?></td>
                <td>Tax Number (Other than UK)</td>
                <td><?php echo $country_vatNum; ?></td>
            </tr>
        </table>
        <table class="table table-bordered table-striped">
            <caption class="h4 bg-info text-center">Supplier Address</caption>
            <tr>
                <td width="20%">Country</td>
                <td width="30%"><?php echo $sp_country; ?></td>
                <td width="20%">City</td>
                <td width="30%"><?php echo $sp_city; ?></td>
            </tr>
            <tr>
                <td>Post Code</td>
                <td><?php echo $sp_postCode; ?></td>
                <td>Building Name / Street </td>
                <td><?php echo $sp_buildingName . " " . $sp_streetRoad; ?></td>
            </tr>
            <tr>
                <td>Address Line 1</td>
                <td><?php echo $sp_line1; ?></td>
                <td>Address Line 2</td>
                <td><?php echo $sp_line2; ?></td>
            </tr>
        </table>
        <table class="table table-bordered table-striped">
            <caption class="h4 bg-info text-center">BANK DETAILS</caption>
            <tr>
                <td width="20%">Bank Name</td>
                <td width="30%"><?php echo $sp_bnkName; ?></td>
                <td width="20%">Account Holder Name</td>
                <td width="30%"><?php echo $sp_acName; ?></td>
            </tr>
            <tr>
                <td>Account Number</td>
                <td><?php echo $sp_acNum; ?></td>
                <td>Sort Code</td>
                <td><?php echo $sp_sCode; ?></td>
            </tr>
        </table>
        <table class="table table-bordered table-striped">
            <caption class="h4 bg-info text-center">Authorization (Contact Person) Details</caption>
            <tr>
                <td width="20%">Name</td>
                <td width="30%"><?php echo $sp_cpName; ?></td>
                <td width="20%">Position in Business (Designation)</td>
                <td width="30%"><?php echo $sp_cppos; ?></td>
            </tr>
            <tr>
                <td>Contact Number</td>
                <td><?php echo $sp_cpNum; ?></td>
                <td>Email</td>
                <td><?php echo $sp_cpEmail; ?></td>
            </tr>
        </table>
        <div>
    </form>
</body>
</html>