<label class="optional">Building Number / Name</label>
    <input class="form-control" name="inchNo" id="inchNo" type="text" placeholder='' readonly="readonly"/>
    <?php 
    if(isset($_POST['submit']))
    {
      $c14=$_POST['inchNo'];
      $acttObj->editFun($table,$edit_id,'inchNo',$c14);
    } 
    ?>
</div>
<div class="form-group col-md-4 col-sm-6">
<label class="optional">Address Line 1</label>
    <input class="form-control" name="line1" id="line1" type="text" placeholder='' readonly="readonly"/>
<?php 
if(isset($_POST['submit']))
{
  $c14=$_POST['line1'];
  $acttObj->editFun($table,$edit_id,'line1',$c14);} 
?>
</div>
            <div class="form-group col-md-4 col-sm-6">
<label class="optional">Address Line 2</label>
    <input class="form-control" name="line2" id="line2" type="text" placeholder='' readonly="readonly"/>
<?php 
if(isset($_POST['submit']))
{
  $c14=$_POST['line2'];
  $acttObj->editFun($table,$edit_id,'line2',$c14);
} 
?>
</div>
            <div class="form-group col-md-4 col-sm-6">
<label class="optional">Address Line 3</label>
    <input class="form-control" name="inchRoad" id="inchRoad" type="text" placeholder='' readonly="readonly"/>
<?php 
if(isset($_POST['submit']))
{
  $c15=$_POST['inchRoad'];
  $acttObj->editFun($table,$edit_id,'inchRoad',$c15);} 
?>
</div>
            <div class="form-group col-md-4 col-sm-6">
<label class="optional">City</label>
<select class="form-control" name="inchCity" id="inchCity" readonly="readonly">
<?php 			
$sql_opt="SELECT city FROM cities ORDER BY city ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) 
{
  $code=$row_opt["city"];
  $name_opt=$row_opt["city"];
  $options.="<OPTION value='$code'>".$name_opt;}
?>		   		      
  <option value="">--Select City--</option>

    <?php echo $options; ?>
    </option>                 
  </select>
<?php 
if(isset($_POST['submit']))
{
  $c16=$_POST['inchCity'];
  $acttObj->editFun($table,$edit_id,'inchCity',$c16);
} 
?>
</div>
            <div class="form-group col-md-4 col-sm-6">
  <label class="optional">Post Code </label>
  <input class="form-control" name="inchPcode" id="inchPcode" readonly="readonly"  type="text" />
<?php 
if(isset($_POST['submit']))
{
  $c17=$_POST['inchPcode'];
  $acttObj->editFun($table,$edit_id,'inchPcode',$c17);
} 
?>