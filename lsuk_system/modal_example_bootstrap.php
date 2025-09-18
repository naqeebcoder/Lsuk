<?php if(isset($_POST['id'])){
session_start();
include'db.php'; 
include'class.php';
$view_id=$_POST['id'];
$table='interpreter_reg';
$query="SELECT * FROM $table where id=$view_id";			
$result = mysqli_query($con,$query);
$row = mysqli_fetch_array($result);
$name=$row['name'];$email=$row['email'];$contactNo=$row['contactNo'];
$rph=$row['rph'];$interp=$row['interp'];$telep=$row['telep'];$trans=$row['trans'];$gender=$row['gender'];
$city=$row['city'];$address=$row['address'];$interp_code=$row['code'];$applicationForm=$row['applicationForm'];
$agreement=$row['agreement'];$crbDbs=$row['crbDbs'];$identityDocument=$row['identityDocument'];
$nin=$row['nin'];$cv=$row['cv'];$dps=$row['dps'];$anyOther=$row['anyOther'];$anyCertificate=$row['anyCertificate'];
$rpm=$row['rpm'];$rpu=$row['rpu'];$ni=$row['ni'];$buildingName=$row['buildingName'];$line1=$row['line1'];$line2=$row['line2'];$line3=$row['line3'];
$postCode=$row['postCode'];$bnakName=$row['bnakName'];$acName=$row['acName'];$acntCode=$row['acntCode'];$acNo=$row['acNo'];$dob=$row['dob'];
$reg_date=$row['reg_date'];$interp=$row['interp'];$telep=$row['telep'];$trans=$row['trans'];
?>
<style>.b{color: #fff;}a:link, a:visited {color: #337ab7;}</style>
<h2> Linguist's  Information for <span class="label label-primary b"><?php echo ucwords($name); ?></span></h2>
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#profile_details"><i class="fa fa-user"></i> Profile Details</a></li>
  <li><a data-toggle="tab" href="#int_documents"><i class="fa fa-briefcase"></i> Interpreter Documents</a></li>
  <?php if($_SESSION['prv']=="Management"){ ?><li><a data-toggle="tab" href="#feed_record"> <b>* Feedback Record *</b></a></li><?php } ?>
</ul>

<div class="tab-content">
  <div id="profile_details" class="tab-pane fade in active">
      <br>
    <table class="table table-bordered">
        <tbody>
            <tr class="bg-primary"><td colspan="4">Personal Details</td></tr>
            <tr>
                <td><img class="img img-responsive img-thumbnail" src="file_folder/interp_photo/<?php echo $row['interp_pix']==''?'no_img.jpg':$row['interp_pix']; ?>" class="img-responsive" width="150" height="150" alt="Profile Picture" title="Profile Picture for <?php echo $row['name']; ?>"></td>
                <td colspan="3">
                     <ul class="list-group">
                         <style>.a{color: #000;}</style>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Date of Birth
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $misc->dated($row['dob']); ?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Contact Number
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $contactNo; ?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Email Address
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $email; ?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        National Insurance #
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $ni; ?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Registered Since
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $misc->dated($row['dated']); ?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        Subscribe Status
                        <h3 style="display: inline;"><span class="label a pull-right"><?php echo $row['subscribe']=='1'?'<span class="label label-success">Subscribed <i class="fa fa-check-circle"></i></span>':'<span class="label label-danger">Unsubscribed <i class="fa fa-remove"></i></span>'; ?></span></h3>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center text-center">
                        <i class="fa fa-map-marker"></i> <?php echo $buildingName.' '.$line1.' '.$line2.' '.$line3.' '.$city.' '.$postCode; ?>
                      </li>
                </ul>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered">
        <tbody>
            <tr class="bg-primary"><td colspan="4">Work Details</td></tr>
            <tr>
            <tr>
            <td width="200" align="left">Interpreter</td>
            <td width="200" align="left"><?php echo $interp=='Yes'?'<span class="label label-success">Yes <i class="fa fa-check-circle"></i></span>':'<span class="label label-danger">No <i class="fa fa-remove"></i></span>'; ?></td>
            <td width="200" align="left">Rate per Hour</td>
            <td width="200" align="left"><?php echo $rph; ?></td>
          </tr>
                    <tr>
            <td width="200" align="left">Telephone</td>
            <td width="200" align="left"><?php echo $telep=='Yes'?'<span class="label label-success">Yes <i class="fa fa-check-circle"></i></span>':'<span class="label label-danger">No <i class="fa fa-remove"></i></span>'; ?></td>
            <td width="200" align="left">Rate per Minute</td>
            <td width="200" align="left"><?php echo $rpm; ?></td>
          </tr>
            <tr>
            <td align="left">Translation</td>
            <td align="left"><?php echo $trans=='Yes'?'<span class="label label-success">Yes <i class="fa fa-check-circle"></i></span>':'<span class="label label-danger">No <i class="fa fa-remove"></i></span>'; ?></td>
            <td align="left">Rate per Unit</td>
            <td align="left"><?php echo $rpu; ?></td>
          </tr>
            <tr class="bg-primary"><td colspan="4">Bank Details</td></tr>
            <tr>
            <td align="left">Bank Name</td>
            <td align="left"><?php echo $bnakName?:'- - -';?></td>
            <td align="left">Account Name</td>
            <td align="left"><?php echo $acName?:'- - -'; ?></td>
          </tr>
            <tr>
            <td align="left">Account Sort Code</td>
            <td align="left"><?php echo $acntCode?:'- - -'; ?></td>
            <td align="left">Account Number</td>
            <td align="left"><?php echo $acNo?:'- - -'; ?></td>
          </tr>
          <style>.badge {font-size: 13px;color: #050505;margin-top: 3px;padding: 7px;background-color: #e5e2e2;}</style>
            <tr class="bg-primary"><td colspan="4">Interpreting Languages & Skills</td></tr>
            <tr>
            <td colspan="2" align="left">Interpreting Languages</td>
            <td colspan="2" align="left">
            <?php $query_lang="SELECT lang FROM interp_lang where code='$interp_code'";
			$result_lang = mysqli_query($con,$query_lang);
			if(mysqli_num_rows($result_lang)==0){
                echo '<span class="badge badge-primary">No Languages Currently!</span>';
            }else{
                    while($row_lang = mysqli_fetch_array($result_lang)){  echo '<span class="badge badge-primary">'.$row_lang['lang'].'</span>&nbsp; &nbsp;';}
            }?>
         </td>
          </tr>
            <tr>
            <td colspan="2" align="left">Interpreting Skills</td>
            <td colspan="2" align="left">
            <?php $query_exp="SELECT skill FROM interp_skill where code='$interp_code'";
			$result_exp = mysqli_query($con,$query_exp);
			if(mysqli_num_rows($result_exp)==0){
                echo '<span class="badge badge-primary">No Skills Currently!</span>';
            }else{
                    while($row_exp = mysqli_fetch_array($result_exp)){  echo '<span class="badge badge-primary">'.$row_exp['skill'].'</span>&nbsp; &nbsp;';}
            }?>
         </td>
          </tr>
        </tbody>
    </table>
  </div>
  <div id="int_documents" class="tab-pane fade">
    <br>
    ==============
  </div>
  <?php if($_SESSION['prv']=='Management'){?>
  <div id="feed_record" class="tab-pane fade">
      <br>
    <table class="table table-bordered table-hover">
        <thead class="bg-primary"> 
				<tr>
				  <th>Organization</th>
				  <th>Feedback By</th>
				  <th>Positive Remarks</th> 
    			  <th>Negative Remarks</th> 
   				  <th>Submitted By</th> 
   				  <th>Dated</th> 
    			  <?php if($_SESSION['prv']=='Management'){?><th width="230" align="center">Actions</th> <?php } ?>
				</tr> 
			</thead>
        <tbody>
            <?php $query="SELECT * from interp_assess where interp_assess.interpName='$interp_code'";	
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){ ?>
    			<tr title="Feedback for Invoice No: <?php echo $row['order_id']==0?'Nil':$row['order_id']; ?>"> 
    				<td><?php echo $row['orgName']; ?></td> 
       				<td><?php echo $row['p_feedbackby']; ?></td> 
       				<td><?php echo $row['p_reason']; ?></td> 
        			<td><?php echo $row['n_reason']; ?></td> 
        			<td><?php echo $row['submittedBy']; ?></td> 
                    <td><?php echo $row['dated']; ?></td>  				
        			<td align="center">
                <?php if($_SESSION['prv']=='Management'){?>
                    <a href="javascript:void(0)" onClick="MM_openBrWindow('interp_assessment_edit.php?edit_id=<?php echo $row['id']; ?>&code_qs=<?php echo $row['interpName']; ?>&name=<?php echo $name; ?>','_blank','scrollbars=yes,resizable=yes,width=800,height=800,left=432,top=160')"><input type="image" src="images/icn_edit.png" title="Edit"></a>
                    <a href="javascript:void(0)" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=600,height=300,left=540,top=283')"><input type="image" src="images/icn_trash.png" title="Trash"></a> 
                <?php } ?>
                    </td> 
    			</tr>
            <?php } ?>
        </tbody>
    </table>
  </div>
  <?php } ?>
</div>

     
</div>
</fieldset>
<?php } ?>