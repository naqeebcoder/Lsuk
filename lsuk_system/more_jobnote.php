  <?php include'db.php'; include'class.php';$table=$_GET['table']; if(isset($_POST['yes'])){$edit_id= $acttObj->get_id('s_note');}?>
  <link rel="stylesheet" type="text/css" href="css/default.css"/>
  <link rel="stylesheet" type="text/css" href="css/layout.css" />
   <script type="text/javascript">
function MM_openBrWindow(theURL,winName,features) {
  window.open(theURL,winName,features);}</script>
<title>Booking Amendment</title>
<br /><br /><br />
<div align="center">
  <span style="font-weight:bold; color:#09F;">Record ID: <?php echo $prm_id=$_GET['edit_id']; ?></span><br /><br />
<form action="" method="post">
<input name="s_note" type="text" id="s_note" value="<?php echo @$_GET['snote']; ?>" size="50" />
<br/><br/>
Are you sure you want to amending  this Job Note&nbsp;&nbsp;<input type="submit" name="yes" value="Yes" />&nbsp;&nbsp;<input type="submit" name="no" value="No" />
</form>
</div>
<?php
if(isset($_POST['yes'])){ $s_note=$_POST['s_note']; $acttObj->editFun('s_note',$edit_id,'snote',$_POST['s_note']);$acttObj->editFun('s_note',$edit_id,'prm_id',$prm_id);$acttObj->editFun('s_note',$edit_id,'tble',$table);$acttObj->editFun($table,$prm_id,'snote',$s_note);
?><script> window.onunload = refreshParent;function refreshParent() {window.opener.location.reload();}</script><?php }?>

<fieldset class="row1">
  <legend>Job Note(s)
     </legend>
            
     <table width="100%">
      <?php 
	   $query="SELECT * FROM s_note where prm_id=$prm_id";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>
  <tr>
    <td align="left"><?php echo $row['snote']; ?> </td>
    <td align="left"><?php echo $row['submit_by']; ?></td>
    <td align="left"><?php echo $misc->dated($row['dated']); ?></td>
    <td align="left"> <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=s_note','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><img src="images/icn_trash.png" title="Trash" height="14" width="16" /></a></td>
    </tr>
    <?php } ?>
  </table>
           
</fieldset>

   