<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include 'class.php';include_once ('function.php');$assignDate=@$_GET['assignDate']; $interp=@$_GET['interp']; $org=@$_GET['org']; $job=@$_GET['job'];$our=@$_GET['our'];$ur=@$_GET['ur'];$inov=@$_GET['inov']; $counter=0;$x=0; $source_num=0;?>
<!--............................................For Multi-Selection.......................................................................-->          

<?php 
include "incmultiselfiles.php";
?>

<script type="text/javascript">	
$(function() {$('#org').multiselect({includeSelectAllOption: true});});
function myFunction() {	

	 	var y = $('#org').val();if(!y){y="<?php echo $org; ?>";}
		
window.location.assign('<?php echo basename(__FILE__);?>?org='+ y);}
window.addEventListener('click', function(e){   
if ($('#org').val() != null) {if (document.getElementById('org').contains(e.target)){console.log('inside');}else{myFunction();}} });
</script>
<!--................................//\\//\\//\\//\\//\\........................................................................................-->

<select id="org" name="org" multiple="multiple" title="Organization">
        <?php 			
$sql_opt="SELECT distinct comp_reg.name,comp_reg.abrv FROM comp_reg
JOIN interpreter ON interpreter.orgName=comp_reg.abrv
where interpreter.multInv_flag=0 and interpreter.commit=0 and (status <> 'Company Seized trading in this name or Company closed' or status <> 'Company Blacklisted')
 ORDER BY comp_reg.name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $status=$row_opt["status"];
    $name_opt=$row_opt["name"];
    $options.="<OPTION value='$code'>".$name_opt. '<span style="color:#F00;">('.$status.')</span>';}
?>       
        <?php echo $options; ?>
        </option>
      </select> |
      <?php $arr = explode(',', $org);$_words = implode("' OR orgName like '", $arr);?>
            

<?php $arr = explode(',', $org);$_words = implode("' OR orgName = '", $arr);$arr_intrp = explode(',', $interp);$_words_intrp = implode("' OR name like '", $arr_intrp);

$table='interpreter';?>
 
     
     <table width="100%" border="1">
  <tr>
    <th>Source_Lang</th>
  <?php   foreach($arr as $orgName){?>
    <th><?php echo $orgName; ?></th>
    <?php $counter++;} ?>
  </tr>
  <?php $query="SELECT distinct ($table.source) FROM $table					
	   			where (orgName = '$_words') order by $table.source";
	   $result = mysqli_query($con, $query); $result = mysqli_query($con, $query);while($row = mysqli_fetch_assoc($result)){ $lang=$row['source']; ?>
   <tr><td><?php echo $lang; ?></td>
 <?php foreach($arr as $orgName){$orgName=$orgName;?>
  
   <?php	 $x=$counter;$u=0; while($x>$u){ 
  $query_inner="SELECT count(source) as source_num FROM $table
  				INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo
	   			where orgName = '$orgName' and source='$lang'";
	   $result_inner = mysqli_query($con, $query_inner);while($row_inner = mysqli_fetch_assoc($result_inner)){?>
    
    <td><?php echo $row_inner["source_num"]; //$source_num=$row_inner["source_num"] + $source_num; ?></td>
    
   <?php	 $u++;}break;} } ?>
	 </tr>
     
<?php $x++;} ?>
 
 
 <tr>
 
<td>Total</td>
 <?php  foreach($arr as $orgName){$orgName=$orgName;?>
	  
  
   <?php	 
  $query_total_inner="SELECT count(source) as source_num FROM $table
						INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo  
	   					where orgName = '$orgName'";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){?>
    
    <td><?php echo $row_total_inner["source_num"]; //$source_num=$row_inner["source_num"] + $source_num; ?></td>
    
   <?php	 }} ?>
	 </tr>
     
 
 
 <tr>
 
<td>Total Cost</td>
 <?php foreach($arr as $orgName){$orgName=$orgName;?>
	  
  
   <?php	 
  $query_total_inner="SELECT sum($table.total_charges_comp) as total_charges_comp FROM $table
						INNER JOIN invoice ON interpreter.invoiceNo=invoice.invoiceNo  
	   					where orgName = '$orgName'";
	   $result_total_inner = mysqli_query($con, $query_total_inner);while($row_total_inner = mysqli_fetch_assoc($result_total_inner)){?>
    
    <td><?php echo round ($row_total_inner["total_charges_comp"]* 0.2 + $row_total_inner["total_charges_comp"], 2); //$source_num=$row_inner["source_num"] + $source_num; ?></td>
    
   <?php	 }} ?>
	 </tr>
     
</table>
<?php echo 'Oganization(s): ';for($i=0;$i<$counter;$i++){echo @$arr[$i].', ';} echo '<br/>Interpreter(s): '; for($i=0;$i<$counter;$i++){echo @$arr_intrp[$i].', ';} ?>