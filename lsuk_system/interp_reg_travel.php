<?php 
  //$ratetravelexpmile=$row['ratetravelexpmile'];
  //$ratetravelworkmile=$row['ratetravelworkmile'];
?>

<p>
<label>Travel Expenses Rate</label>
<input name="ratetravelexpmile" type="text" id="ratetravelexpmile"
      pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" tabindex="8" 
      value="<?php echo isset($ratetravelexpmile)?$ratetravelexpmile:'' ?>"/>

      <?php 
      if(isset($_POST['submit']))
      {
        $c1=$_POST['ratetravelexpmile'];
        $acttObj->editFun($table,$edit_id,'ratetravelexpmile',$c1);
      } 
      ?>

<label> Travel Time Rate</label>
<input name="ratetravelworkmile" type="text" id="ratetravelworkmile"
      pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" tabindex="8"
      value="<?php echo isset($ratetravelworkmile)?$ratetravelworkmile:'' ?>"/>

      <?php 
      if(isset($_POST['submit']))
      {
        $c1=$_POST['ratetravelworkmile'];
        $acttObj->editFun($table,$edit_id,'ratetravelworkmile',$c1);
      } 
      ?>
</p>


