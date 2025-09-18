<?php 
//When review is submitted
if(isset($_POST['order_id']) && isset($_POST['interpName']))
{
include'db.php'; 
include'class.php'; 
$table='interp_assess';
$interpName=$_POST['interpName'];
$name=$_POST['namee']; 
$edit_id= $acttObj->get_id($table);
$c111=$_POST['orgName']; $acttObj->editFun($table,$edit_id,'orgName',$c111);
$c112=$_POST['get_feedback'];
$acttObj->editFun($table,$edit_id,'get_feedback',$c112);
$c113=$_POST['punctuality'];
$acttObj->editFun($table,$edit_id,'punctuality',$c113);
$c114=$_POST['appearance']; 
$acttObj->editFun($table,$edit_id,'appearance',$c114);
$c115=$_POST['professionalism']; 
$acttObj->editFun($table,$edit_id,'professionalism',$c115);
$c116=$_POST['confidentiality']; 
$acttObj->editFun($table,$edit_id,'confidentiality',$c116);
$c117=$_POST['impartiality']; $acttObj->editFun($table,$edit_id,'impartiality',$c117);
$c118=$_POST['accuracy']; $acttObj->editFun($table,$edit_id,'accuracy',$c118);
$c119=$_POST['rapport']; $acttObj->editFun($table,$edit_id,'rapport',$c119);
$c120=$_POST['communication']; $acttObj->editFun($table,$edit_id,'communication',$c120);
$c121=$_POST['p_feedbackby'];
$acttObj->editFun($table,$edit_id,'p_feedbackby',$c121);
$c122=$_POST['p_reason'];
$acttObj->editFun($table,$edit_id,'p_reason',$c122);
$c123=$_POST['n_reason'];
$acttObj->editFun($table,$edit_id,'n_reason',$c123);
$c124=$_POST['order_id'];
$acttObj->editFun($table,$edit_id,'order_id',$c124);
$c125=$_POST['UserName'];
$acttObj->editFun($table,$edit_id,'submittedBy',$c125);
$acttObj->editFun($table,$edit_id,'table_name',"interpreter");
if($acttObj->editFun($table,$edit_id,'interpName',$interpName)){echo 'good';}
}
?>