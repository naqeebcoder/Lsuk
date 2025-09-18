<?php include'db.php'; include'class.php'; $invoice_No=$_GET['invoice_No'];if(isset($_POST['submit'])){$table=' interp_paid';$edit_id= $acttObj->get_id($table);}?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Order Form</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
    </head>
<body>    
<form action="" method="post" class="register">
  <h1>Update Amount Status to be Paid to Interpreter</h1>
  <fieldset class="row1">
    <legend>Amount Details
          </legend>
    <p>
      <label>Amount Received *
                  </label>
                  <input name="rAmount" type="number" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" id="rAmount" required=''/>
                  <!--
                  <label class="obinfo">* obligatory fields
                  </label>-->
                  
  
       <label>Date *
         </label>
               <input name="dated" type="date" style="border:1px solid #CCC" value="" required='' />
               <?php if(isset($_POST['submit'])){$c1=$_POST['rAmount']; $acttObj->editFun($table,$edit_id,'pAmount',$c1);} ?>
              <?php if(isset($_POST['submit'])){$c3=$_POST['dated'];$acttObj->editFun($table,$edit_id,'dated',$c3);
			  $acttObj->editFun($table,$edit_id,'invoiceNo',$invoice_No);} ?>
            </p>
          </fieldset>
          <fieldset class="row1">
     <legend>Amount Paid to interpret (Invoice No: <span style="color:#F00"><?php echo $invoice_No;?></span>)
     </legend>
            
     <table width="30%" border="1">
      <?php $table='interp_paid';
	   $query="SELECT * FROM $table where invoiceNo='$invoice_No'";			
			$result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){?>
  <tr>
    <td align="left"><?php echo $row['dated']; ?> </td>
    <td align="left"><?php echo $row['pAmount']; ?> </td>
    <td align="left"> <a href="#" onClick="MM_openBrWindow('del.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=400,height=200')"><img src="images/icn_trash.png" title="Trash" height="14" width="16" /></a></td>
    </tr>
    <?php } ?>
  </table>
           
     </fieldset>
  <div><button class="button" type="submit" name="submit">Submit &raquo;</button></div>
</form>
</body>
</html>
<?php
if(isset($_POST['submit'])){echo "<script>alert('Successful!');</script>";}

?>

<script>
  window.onunload = refreshParent;
 function refreshParent() {
    window.opener.location.reload();
	
}</script>





