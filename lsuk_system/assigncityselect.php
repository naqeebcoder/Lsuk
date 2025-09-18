<?php 			

$sql_opt="SELECT city FROM cities ORDER BY city ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";

while ($row_opt=mysqli_fetch_array($result_opt)) 
{
	$code=$row_opt["city"];
	$name_opt=$row_opt["city"];
    $options.="<OPTION value='$code'>".$name_opt;
}

?>

<?php if(!empty($city))
{ ?>
  <option><?php echo $city; ?></option>
  <?php 
} 
else
{?>
  <option>Bristol</option>
  <?php 
} ?>
<?php echo $options; ?>
</option>                      
</select>
                  
