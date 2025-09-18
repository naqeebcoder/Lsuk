<?php
// include "userhaspage.php";
// SysPermiss::UserHasPage(__FILE__);
?>
<?php 
if(session_id() == '' || !isset($_SESSION))
{
	session_start();
} 
?> 

<?php 
include '../../db.php';
include_once('../../class.php');
// include_once ('function.php');
$get_actions = explode(",", $acttObj->read_specific("GROUP_CONCAT(action_permissions.action_id) as actions", "action_permissions,route_actions", "action_permissions.action_id=route_actions.id AND route_actions.route_id=132 AND action_permissions.user_id=" . $_SESSION['userId'])['actions']);
$action_receive_payment = $_SESSION['is_root'] == 1 || in_array(74, $get_actions);
$action_receive_partial_payment = $_SESSION['is_root'] == 1 || in_array(75, $get_actions);
//$from_date=@$_GET['from_date'];
//$to_date=@$_GET['to_date']; 
//$interp=@$_GET['interp']; 

$from_date=SafeVar::GetVar('from_date','');
$to_date=SafeVar::GetVar('to_date','');
$interp=SafeVar::GetVar('interp','');

//$org=@$_GET['org']; 
//$inov=@$_GET['inov'];

$org=SafeVar::GetVar('org','');
$inov=SafeVar::GetVar('inov','');

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;	
$strSqlFilt2="";
if (!empty($org)){ $strSqlFilt2=" and comp_abrv like '$org%' ";}
if (!empty($inov)){ $strSqlFilt2.=" and mult_inv.m_inv like '%$inov%' ";}
$query="select * from mult_inv where comp_name!='' $strSqlFilt2 ORDER BY dated DESC";
$htmlTable = "";
$htmlTable = "<table> 
			<thead> 
				<tr>
				  	<th>Invoice #</th>
    				<th>Company Name</th> 
    				<th>Amount</th> 
					<th>Paid Amount</th>
					<th>Remaining Amount</th>  
    				<th>Payment Status</th> 
					<th>Paid date</th> 
    				<th>From Date</th> 
                    <th>To Date</th> 
                    <th>Due Date</th> 
				</tr> 
			</thead> 
			<tbody> ";
?>
			<?php $result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){
				$rAmount = (!empty($row['rAmount']) && $row['rAmount']>0)?$row['rAmount']:0;
				$htmlTable .= "<tr>
				  <td>".$row['m_inv']."</td>
   					<td>".$row['comp_name']."</td> 
    				<td>".$row['mult_amount']."</td>";
					// $row_part=$acttObj->read_specific('sum(amount) as amount','partial_amounts',' status="1" and tbl="mult_inv" and order_id="'.$row['m_inv'].'"');
					$htmlTable .= "<td>".$rAmount."</td>";
					$htmlTable .= "<td>".(round($row['mult_amount'],2)-round($rAmount,2))."</td>";
					$htmlTable .= ($row['status'])?"<td style='color:#066; font-weight:bold'>".$row['status']."</td>":"<td style='color:#F00; font-weight:bold'>Pending</td>";

					$htmlTable .="<td>".(($row['paid_date']!='1001-01-01' && $row['paid_date']!='0000-00-00')?$row['paid_date']:"Date Not Found")."</td>";
					$htmlTable .= "<td>".$row['from_date']."</td> 
    				<td>".$row['to_date']."</td> 
    				<td>".$row['due_date']."</td>
				</tr> ";
				 }
$htmlTable .= "</tbody></table>";
list($a, $b) = explode('.', basename(__FILE__));
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=" . $a . ".xls");
echo $htmlTable;
				 
				?>