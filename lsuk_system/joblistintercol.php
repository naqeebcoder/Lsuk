<?php 

class JobListInterCol
{
    static public function GetSelectCols($table)
	{
        //
        //return "$table.*,interpreter_reg.name";
        //return $table.".*,interpreter_reg.name,interpreter_reg.id as interpid";
        return "$table.*,interpreter_reg.name,interpreter_reg.id as interpid";
	}
    
    static public function GetColTDHoursWorkd($row)
	{
        JobListInterCol::GetColTD($row,"hoursWorkd","Interp Hours: ");
    }

    static public function GetColTD($row,$strColName,$strTitle)
	{
        /*
        <td><?php if($row['numberUnit']==0){$counter++; ?>  
            <span style="color:#F00" title="Interp Units: <?php echo $row['numberUnit']; ?>">
            <?php echo $row['name']; ?></span>
            <?php }else{ echo $row['name']; }?></td>					

            */

        $anctext=$row['name'];
        $anc="<span style='cursor:pointer;' href=\"#\" 
            onclick=\"MM_openBrWindow('interp_data_view.php?view_id=".$row['interpid'].
            "&table=interpreter_reg','_blank','scrollbars=yes,resizable=yes,width=850,height=700')\">".
            $anctext.
            "</span>";

        if ($row[$strColName]<>0)
        {
            $strHtml=
                "<td>
                <span ".$anc."</span>
                </td>";
            echo $strHtml;
        }
        else
        {
            $strHtml=
                "<td>
                <span style='color:#F00' title='".$strTitle.$row[$strColName]."'>".
                    $anc."</span>
                </td>";
            echo $strHtml;
        }
    }
}

?>

