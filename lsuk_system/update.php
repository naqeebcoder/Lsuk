<article class="module width_quarter" style="width:90%">
			<header><h3>Reminder</h3></header>
			<div class="message_list">
				<div class="module_content">
                      <?php $userId=$_SESSION['userId']; $table='updates';
	   $query="SELECT $table.*, login.name as userName FROM $table 
	   INNER JOIN login ON $table.name=login.id";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>  
					<div class="message"><p><?php echo $row['news']; ?></p>
					<p><strong><?php echo ucwords($row['userName']); ?></strong> &nbsp;&nbsp;(<?php echo $row['dated']; ?>)<span style="margin-left:30px;">
                   <?php if($_SESSION['prv']=='Management'){?> 
                    <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><input type="image" src="images/icn_trash.png" title="Trash"></a><?php } ?></span></p></div>
                    <?php } ?>
				</div>
			</div>
			<footer>
            <?php include'db.php'; if(isset($_POST['send'])){$data=$_POST['news'];$dated=date("Y-m-d");$query="INSERT INTO updates VALUES ('','$data',$userId,'$dated')";	
			if (!mysqli_query($con,$query)) {return die('Error: ' . mysqli_error($con)); }}?>
				<form class="post_message" method="post" action="">
					<input type="text" placeholder="Message" name="news" />

					<input type="submit" class="btn_post_message" value="" name="send"/>
				</form>
			</footer>
		</article>