<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
$allowed_type_idz = "58";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>View Company Profile</u> action!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$table = 'comp_reg';
$edit_id = $_GET['edit_id'];

$query = "SELECT * FROM $table where id=$edit_id";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_array($result);

$rowID = $row['id'];
$name = $row['name'];
$contactPerson = $row['contactPerson'];
$abrv = $row['abrv'];
$contactNo1 = $row['contactNo1'];
$contactNo2 = $row['contactNo2'];
$contactNo3 = $row['contactNo3'];
$buildingName = $row['buildingName'];
$line1 = $row['line1'];
$streetRoad = $row['streetRoad'];
$email = $row['email'];
$country = $row['country'];
$city = $row['city'];
$compType = $row['compType'];
$postCode = $row['postCode'];
$note = $row['note'];
$line2 = $row['line2'];
$invEmail = $row['invEmail'];
$invAddrs = $row['invAddrs'];
$bod = $row['bod'];
$crn = $row['crn'];
$vn = $row['vn'];
$web = $row['web'];
$aupn = $row['aupn'];
$pitc = $row['pitc'];
$taupn = $row['taupn'];
$tpitc = $row['tpitc'];
$tbuildingName = $row['tbuildingName'];
$tline1 = $row['tline1'];
$tline2 = $row['tline2'];
$tstreetRoad = $row['tstreetRoad'];
$tcity = $row['tcity'];
$tcn = $row['tcn'];
$tpostCode = $row['tpostCode'];
$rff = $row['rff'];
$payment_terms = $row['payment_terms'];
$po_req = $row['po_req'];
$po_email = $row['po_email'];
$admin_ch = $row['admin_ch'];
$admin_rate = $row['admin_rate'];
$interp_time = $row['interp_time'];
$wait_time = $row['wait_time'];
$tr_time = $row['tr_time']; ?>
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
            <caption class="h4 bg-info text-center">
				Details of <span class="h4 label label-success"><?php echo $abrv; ?></span> (<?php echo $name; ?>)
				<?php 
					// Getting Parent/Head
					$parent_comp = '';
					if($row['comp_nature'] == 3){ // Comp Nature = 3 -- subsidiary
						$parent_comp = $acttObj->read_specific("c.name as parent_head_comp, c.abrv as parent_abrv, s.parent_comp", "subsidiaries s, comp_reg c", "s.child_comp=" . $edit_id." AND s.parent_comp = c.id");
						$parent_head_comp = $parent_comp['parent_head_comp'];
						$parent_head_abrv = $parent_comp['parent_abrv'];
					}
					if($parent_comp){
				?>
					<br>
						<h5><strong>Parent/Head Units:</strong> <?php echo $parent_head_comp .' ('.$parent_head_abrv.')'; ?><h5>
					
					<?php } ?>
			</caption>
            <tr>
                <td width="20%">Contact Person</td>
                <td width="30%"><?php echo $contactPerson; ?></td>
                <td width="20%">Payment Terms</td>
                <td width="30%"><?php echo $payment_terms == 0 ? 'Pay Now' : $payment_terms . ' Days'; ?></td>
            </tr><tr>
                <td width="20%">Branch or Department</td>
                <td width="30%"><?php echo $bod; ?></td>
                <td width="20%">Company Registration Number</td>
                <td width="30%"><?php echo $crn; ?></td>
            </tr>
            <tr>
                <td>Company Type</td>
                <td><?php echo $compType; ?></td>
                <td>VAT Number</td>
                <td><?php echo $vn; ?></td>
            </tr>
			<tr>
                <td>Contact No 1</td>
                <td><?php echo $contactNo1; ?></td>
                <td>Contact No 2</td>
                <td><?php echo $contactNo2; ?></td>
            </tr>
            <tr>
                <td>Company Fax No</td>
                <td><?php echo $contactNo3; ?></td>
                <td>Email Address</td>
                <td><?php echo $email; ?></td>
            </tr>
            <tr>
                <td>Company Website</td>
                <td><?php echo $web; ?></td>
				<td>Registration Form Filled</td>
                <td><?php echo $rff; ?></td>
            </tr>
            <tr>
                <td>Purchase Order Required?</td>
                <td><?php echo $po_req == 1 ? '<span style="font-size: 16px;" class="label label-success pull-right">Yes</span>' : '<span style="font-size: 16px;" class="label label-warning pull-right">No</span>'; ?></td>
				<td>Purchase Order Email</td>
				<td><?php echo $po_email; ?></td>
            </tr>
            <tr>
                <td>Admin Charge ?<?php echo $admin_ch == 1 ? '<span style="font-size: 16px;" class="label label-success pull-right">Yes</span>' : '<span  style="font-size: 16px;" class="label label-warning pull-right">No</span>'; ?> <br>
                    <?php if ($admin_ch == 1) { ?>Admin Charge Rate :<b class="pull-right"><?php echo $admin_rate; ?></b> <?php } ?>
                </td>
                <td>Travel Time ?<?php echo $tr_time == 1 ? '<span style="font-size: 16px;" class="label label-success pull-right">Yes</span>' : '<span  style="font-size: 16px;" class="label label-warning pull-right">No</span>'; ?></td>
                <td>Interpreting Time ?<?php echo $interp_time == 1 ? '<span style="font-size: 16px;" class="label label-success pull-right">Yes</span>' : '<span style="font-size: 16px;" class="label label-warning pull-right">No</span>'; ?></td>
                <td>Waiting Time ?<?php echo $wait_time == 1 ? '<span style="font-size: 16px;" class="label label-success pull-right">Yes</span>' : '<span  style="font-size: 16px;" class="label label-warning pull-right">No</span>'; ?></td>
            </tr>
        </table>
        <table class="table table-bordered table-striped">
            <caption class="h4 bg-info text-center">Invoicing Address (Team or Unit Address)</caption>
            <tr>
                <td width="20%">Building Number / Name</td>
                <td width="30%"><?php echo $buildingName; ?></td>
                <td width="20%">Address Line 1</td>
                <td width="30%"><?php echo $line1; ?></td>
            </tr>
            <tr>
                <td>Address Line 2</td>
                <td><?php echo $line2; ?></td>
                <td>Address Line 3</td>
                <td><?php echo $streetRoad; ?></td>
            </tr>
            <tr>
                <td>City/Country</td>
                <td><?php echo $city . ', '.$country; ?></td>
                <td>Post Code</td>
                <td><?php echo $postCode; ?></td>
            </tr>
            <tr>
                <td>Invoice Email</td>
                <td><?php echo $invEmail; ?></td>
                <td>Invoice Address</td>
                <td><?php echo $invAddrs; ?></td>
            </tr>
        </table>
        <table class="table table-bordered table-striped">
            <caption class="h4 bg-info text-center">Trading Address (Team or Unit Address)</caption>
            <tr>
                <td width="20%">Authorised Person Name</td>
                <td width="30%"><?php echo $taupn; ?></td>
                <td width="20%">Position in the Company</td>
                <td width="30%"><?php echo $tpitc; ?></td>
            </tr>
            <tr>
                <td>Building Number / Name</td>
                <td><?php echo $tbuildingName; ?></td>
                <td>Address Line 1</td>
                <td><?php echo $tline1; ?></td>
            </tr>
            <tr>
                <td>Address Line 2</td>
                <td><?php echo $tline2; ?></td>
                <td>Address Line 3</td>
                <td><?php echo $tstreetRoad; ?></td>
            </tr>
            <tr>
                <td>City</td>
                <td><?php echo $tcity; ?></td>
                <td>Post Code</td>
                <td><?php echo $tpostCode; ?></td>
            </tr>
            <tr>
                <td>Contact Name</td>
                <td><?php echo $tcn; ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4">Company Note:
					<?php echo $note; ?>
				</td>
            </tr>
        </table>
        <div>
            </form>
</body>

</html>