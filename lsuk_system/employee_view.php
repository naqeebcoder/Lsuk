<?php if(isset($_GET['view_id'])){
if(session_id() == '' || !isset($_SESSION)){session_start();}
include 'db.php';
include_once ('function.php');
include'class.php';
$view_id=$_GET['view_id'];
$table='emp';
$query="SELECT * FROM $table where id=$view_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$name=$row['name'];$passp=$row['passp'];$ni=$row['ni'];$pr=$row['pr'];$driving=$row['driving'];$desig=$row['desig'];$jt=$row['jt'];$phs=$row['phs'];$lss=$row['lss'];$duty=$row['duty'];$remrks=$row['remrks'];$contact=$row['contact'];$email=$row['email'];$buildingName=$row['buildNo'];$line1=$row['line1'];$line2=$row['line2'];$city=$row['city'];$pcode=$row['pcode'];$gender=$row['gender'];$elgible=$row['elgible']; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>View Employee Details</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>.b{color: #fff;}a:link, a:visited {color: #337ab7;}.rm:hover{color: #fff;background-color: #ef3737;cursor:pointer;}</style>
</head>
<body>
    <div class="col-md-12">
<h2 class="text-center"> View Details for <span class="label label-primary b"><?php echo ucwords($name); ?></span></h2>
<table class="table table-bordered">
        <tbody>
            <tr class="bg-primary text-center"><td colspan="4"><b>Personal Details</b></td></tr>
            <tr>
                <td width="40%"><img class="img img-responsive img-thumbnail" src="file_folder/interp_photo/<?php echo $row['interp_pix']==''?'no_img.jpg':$row['interp_pix']; ?>" class="img-responsive" alt="Profile Picture" title="Profile Picture for <?php echo $row['name']; ?>"><center class="h3"><?php echo $gender; ?></center></td>
                <td colspan="3">
                     <ul class="list-group">
                         <style>.a{color: #000;}</style>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Email Address
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $email ;?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Contact Number
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $contact ;?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Passport/NIC #*
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $passp ;?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        National insurance #
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $ni?:'- - -'; ?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Payroll #
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $pr?:'- - -'; ?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Driving Licence #
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $driving?:'Not Provided'; ?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Uk Work Eligibility
                        <h3 style="display: inline;"><span class="label a pull-right"><?php if($elgible==0){ echo '<span class="label label-success">Yes</span>';}else{echo '<span class="label label-danger">No</span>';} ;?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center text-center h5">
                        <i class="fa fa-map-marker"></i> <?php echo $buildingName.' '.$line1.' '.$line2.' '.$city.' '.$pcode; ?>
                      </li>
                </ul>
                </td>
            </tr>
            <tr class="bg-primary text-center"><td colspan="4"><b>Job Description</b></td></tr>
            <tr>
            <td width="200" align="left">Designation</td>
            <td width="200" align="left"><?php echo $desig; ?></td>
            <td width="200" align="left">Job Type</td>
            <td width="200" align="left"><?php echo $jt; ?></td>
          </tr>
            <tr>
            <td width="200" align="left">Per Hour Salary</td>
            <td width="200" align="left"><?php echo $phs; ?></td>
            <td width="200" align="left">Lump-Sum Salary</td>
            <td width="200" align="left"><?php echo $lss?:'---'; ?></td>
          </tr>
    </tbody>
</table>

<div class="col-sm-12" style="margin-bottom: 20px;">
            <div class="col-sm-6">
                <h4>Duties / Assignment</h4>
                <textarea class="form-control" rows="7"><?php echo $duty?:'Not yet !' ;?></textarea>
              </div>
              <div class="col-sm-6">
                <h4>Extra Employee Notes</h4>
                    <textarea class="form-control" rows="7"><?php echo $remrks?:'No Extra Notes !' ;?></textarea>
              </div>
</div>
</body>
</html>
<?php } ?>