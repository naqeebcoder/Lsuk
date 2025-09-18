<?php
$row = $obj->read_specific("interpreter_reg.*", "interpreter_reg,mileage_enquiry", " interpreter_reg.id=mileage_enquiry.interp_id AND mileage_enquiry.status=1 AND mileage_enquiry.av_changed_by=0 AND mileage_enquiry.order_id=" . $assign_id);
if(!empty($row)){
  ?><legend>
  <h2>Assign Interpreter</h2>
</legend>
  <?php
  $name=$row['name']; 
  $email=$row['email'];
  $email2=$row['email2'];
  $rph=$row['rph'];
  $contactNo=$row['contactNo'];
  $contactNo2=$row['contactNo2'];
  $interp=$row['interp'];
  $telep=$row['telep'];
  $trans=$row['trans'];
  $gender=$row['gender'];
  $address=$row['address'];
  $dated=$row['dated'];
  $buildingName=@$row['buildingName'];
  $id=$row['id'];
  $city=$row['city'];
  $mndy=$row['mndy'];
  $mndy_time=$row['mndy_time'];
  $mndy_to=$row['mndy_to'];
  $tsdy=$row['tsdy'];
  $tsdy_time=$row['tsdy_time'];
  $tsdy_to=$row['tsdy_to'];
  $wdnsdy=$row['wdnsdy'];
  $wdnsdy_time=$row['wdnsdy_time'];
  $wdnsdy_to=$row['wdnsdy_to'];
  $thsdy=$row['thsdy'];
  $thsdy_time=$row['thsdy_time'];
  $thsdy_to=$row['thsdy_to'];
  $frdy=$row['frdy'];
  $frdy_time=$row['frdy_time'];
  $frdy_to=$row['frdy_to'];
  $stdy=$row['stdy'];
  $stdy_time=$row['stdy_time'];
  $stdy_to=$row['stdy_to'];
  $sndy=$row['sndy'];
  $sndy_time=$row['sndy_time'];
  $sndy_to=$row['sndy_to'];
  $week_remarks=$row['week_remarks'];
  $interp_code=$row['code'];
  $dob=$row['dob']; 
  $line1=$row['line1'];  
  $ni=$row['ni'];
?>
<table width="100%" class="table table-bordered">
<tr>
  <th width="200" align="left">Name</th>
  <td width="250"><?php echo $name; ?></td>
  <th width="200" align="left">Language</th>
  <td width="250"><?php echo $srcLang; ?></td>
</tr>
<tr>
  <th width="200" align="left">Email-1</th>
  <td width="250"><?php echo $email; ?></td>
  <th align="left">Email-2</th>
  <td><?php echo $email2; ?></td>
  </tr>
<tr>
  <th align="left">Contact No.1</th>
  <td><?php echo $contactNo; ?></td>
  <th align="left">Contact No.2</th>
  <td><?php echo $contactNo2; ?></td>
</tr>
</table>
        </fieldset>
        <fieldset class="row1">
          <legend>Other Information</legend>
          <table width="100%" class="table table-bordered">
            <tr>
              <th width="200" align="left">Interpreter</th>
              <td width="250"><?php echo $interp; ?></td>
              <th width="200" align="left">Gender</th>
              <td width="250"><?php echo $gender; ?></td>
            </tr>
            <tr>
              <th width="200" align="left">Telephone Interpreter</th>
              <td width="250"><?php echo $telep; ?></td>
              <th width="200" align="left">City</th>
              <td width="250"><?php echo $city; ?></td>
            </tr>
            <tr>
              <th width="200" align="left">Translation</th>
              <td width="250"><?php echo $trans; ?></td>
              <th width="200" align="left"><span class="optional">Address</span></th>
              <td width="250"><?php echo $address; ?></td>
            </tr>
          </table>
         
        </fieldset>
        
        <fieldset class="row1">
          <legend>Availability Information </legend>
          <table width="100%" class="table table-bordered">
            <tr>
              <th width="200" align="left">Monday</th>
              <td width="250"><?php echo $mndy; ?></td>
              <th width="200" align="left">Tuesday</th>
              <td width="250"><?php echo $tsdy; ?></td>
            </tr>
            <tr>
              <th width="200" align="left">Wednesday</th>
              <td width="250"><?php echo $wdnsdy; ?></td>
              <th width="200" align="left">Thursday</th>
              <td width="250"><?php echo $thsdy; ?></td>
            </tr>
            <tr>
              <th width="200" align="left">Friday</th>
              <td width="250"><?php echo $frdy; ?></td>
              <th width="200" align="left">Saturday</th>
              <td width="250"><?php echo $stdy; ?></td>
            </tr>
            <tr>
              <th align="left">Sunday</th>
              <td><?php echo $sndy; ?></td>
              <th align="left">Remarks</th>
              <td style="color:#F00"><?php echo $week_remarks; ?></td>
            </tr>
          </table>

   <hr>
     <div>
      <fieldset class="row1">


   <legend>Skills can interpret
   </legend>
          
   <table width="30%" class="table table-bordered">
    <?php 
    $table='interp_skill';
    $code='id-'.$interp_id;
    $row = $obj->read_specific("*", "interp_skill", "code='" . $code . "'");?>
      <tr>
      <td align="left"><?php echo $row['skill']; ?> </td>
      </tr>
   </table>
    <p>
    <h1 style="color:#F00"> 
      <label class="optional" style="color:#069; font-size:15px"> Calculated Stars: 
      </label>
      
    <?php
     $row = $obj->read_specific("(sum(punctuality) + sum(appearance) + sum(professionalism) + 
     sum(confidentiality) + sum(impartiality) + sum(accuracy) + sum(rapport) + 
     sum(communication)) as sm,COUNT(interp_assess.id) as diviser", "interp_assess,interpreter_reg", "interp_assess.interpName=interpreter_reg.code AND interp_assess.interpName='" . $code . "'");
      $diviser=$row['diviser'];
      if($diviser<=0)
      {
        $diviser=1;
      } 
      $assess_num=$row['sm']*100/($diviser*120); 

      if($assess_num<0)
    {
      echo 'Negative Feedback';
    }
    if($assess_num>=0 && $assess_num<=5)
    {
      echo 'No Feedback Received';
    }
    if($assess_num>6 && $assess_num<=20)
    {
      echo '* ';
    }
    if($assess_num>20 && $assess_num<=40)
    {
      echo '** ';
    }
    if($assess_num>40 && $assess_num<=60)
    {
      echo '*** ';
    }
    if($assess_num>60 && $assess_num<=80)
    {
      echo '**** ';
    }
    if($assess_num>80 && $assess_num<=100)
    {
      echo '***** ';
    }
    
  ?>

   </h1>
   </p>
   </fieldset>

    <?php 
      $bGotAll=true;
      if(empty($dob) || $dob== '0000-00-00' || empty($buildingName) || 
        empty($buildingName) || empty($city) || empty($line1) || empty($ni))
      { 
        $bGotAll=false;
      }
    ?>

    <fieldset class="row1">
        <legend><?php echo $bGotAll?"All Available Information":"Missing Information";?> </legend>
    </fieldset>

        
</div>
</fieldset>
<?php
}

