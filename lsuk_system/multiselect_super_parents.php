<?php $sup_sql_opt="SELECT DISTINCT comp_reg.id,comp_reg.name as sup_parent_name,parent_companies.sup_child_comp as sup_child_id,(SELECT comp_reg.name from comp_reg WHERE parent_companies.sup_child_comp=comp_reg.id) as sup_child_name FROM parent_companies,comp_reg WHERE parent_companies.sup_parent_comp=comp_reg.id";
$sup_result_opt=mysqli_query($con,$sup_sql_opt);
$sup_options="";

$sup_strOrgGrp="";
while ($sup_row_opt=mysqli_fetch_array($sup_result_opt)) 
{
	$sup_parent_id=$sup_row_opt["id"];
	$sup_parent_name=$sup_row_opt["sup_parent_name"];
	$sup_child_name=$sup_row_opt["sup_child_name"];
	$sup_child_id=$sup_row_opt["sup_child_id"];


  if ($sup_parent_name<>$sup_strOrgGrp)
  {
    if ($sup_strOrgGrp!="")
      $sup_options.="</optgroup>";

    $sup_options.="<option value='$sup_parent_id'>".$sup_parent_name."</option>";
    $sup_strOrgGrp=$sup_parent_name;
  }
  $sup_options.="<optgroup data-abrv='$sup_child_id' label='$sup_child_name'></optgroup>";
}
$sup_options.="</optgroup>";
echo $sup_options; ?>
                  
