<?php $sql_opt="SELECT DISTINCT comp_reg.id,comp_reg.name as parent_name,child_companies.child_comp as child_id,(SELECT comp_reg.abrv from comp_reg WHERE child_companies.child_comp=comp_reg.id) as child_name FROM child_companies,comp_reg WHERE child_companies.parent_comp=comp_reg.id and child_companies.parent_comp NOT IN (select parent_companies.sup_parent_comp from parent_companies ) ORDER BY child_companies.parent_comp";
$result_opt=mysqli_query($con,$sql_opt);
$options="";

$strOrgGrp="";
while ($row_opt=mysqli_fetch_array($result_opt)) 
{
	$parent_id=$row_opt["id"];
	$parent_name=$row_opt["parent_name"];
	$child_name=$row_opt["child_name"];
	$child_id=$row_opt["child_id"];


  if ($parent_name<>$strOrgGrp)
  {
    if ($strOrgGrp!="")
      $options.="</optgroup>";

    $options.="<option value='$parent_id'>".$parent_name."</option>";
    $strOrgGrp=$parent_name;
  }
  $options.="<optgroup data-abrv='$child_id' label='$child_name'></optgroup>";
}
$options.="</optgroup>";

?>

<?php echo $options; ?>
                  
