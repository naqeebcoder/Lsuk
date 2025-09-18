<?php 
//##actionprinted

$hasemailed=$row['sentemail'];
$toemailed=isset($row['email'])?$row['email']:"";
$hasprinted=$row['printed'];
$printedby=isset($row['printedby'])?$row['printedby']:"";


    //echo "<td title='$toemailed'>yes</p></td>";
    echo "<td title='$toemailed'><div style='width:60px;overflow-wrap: break-word;'>$toemailed</div></td>";

echo "<td title='$printedby'>$printedby</td>";

?>					  


