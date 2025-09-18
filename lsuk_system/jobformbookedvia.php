<script type="text/javascript">
  function OnBookedViaChg() {
    var elemSel = document.getElementById("bookedVia");
    var strBookedType = elemSel.options[elemSel.selectedIndex].value;

    strLab = "";
    switch (strBookedType) {
      case "tel":
        strLab = "Tel No:";
        break;
      case "fax":
        strLab = "Tel No:";
        break;
      case "Email":
        strLab = "Email address";
        break;
      case "its":
        strLab = "Name:";
        break;
      case "onp":
        strLab = "Name:";
        break;
      case "ip":
        strLab = "Name:";
        break;
      case "ip2":
        strLab = "Name:";
        break;
      case "Web":
        strLab = "Name:";
        break;
    }
    var elemLab = document.getElementById("labnamedbooking");
    elemLab.innerHTML = strLab;
  }
</script>
<div class="form-group col-md-4 col-sm-6">
  <label class="optional">Booked Via </label>
  <select class="form-control" onchange="return OnBookedViaChg();" id="bookedVia" name="bookedVia">
    <?php if (isset($bookedVia)) { ?>
      <option><?php echo $bookedVia; ?></option>
    <?php } ?>
    <option value="0">--Select--</option>
    <?php if (isset($wantinpersonal)) { ?><option value="ip">In-person</option><?php }; ?>
    <option value="tel">Telephone</option>
    <option value="fax">Fax</option>
    <option value="Email">Email</option>
    <option value="its">Interpreter Timesheet</option>
    <option value="onp">Online Portal</option>
    <option value="Web">Website</option>
    <?php if (isset($wantinpersonal)) { ?><option value="ip2">Inperson</option><?php }; ?>
  </select>
  <?php if (isset($_POST['submit'])) {
    $c22 = $_POST['bookedVia'];
    $acttObj->editFun($table, $edit_id, 'bookedVia', $c22);
  }?>
</div>
<div class="form-group col-md-4 col-sm-6">
  <label>Booked Date *</label>
  <input onchange="OnDateChgAjax();" type="date" name="bookedDate" id="bookedDate" required='Booked Date' class="form-control" value="<?php echo isset($dbs_bookeddate) ? $dbs_bookeddate : '' ?>" />
</div>
<div class="form-group col-md-4 col-sm-6">
  <label>Booked Time *</label>
  <input onchange="OnTimeChgAjax();" type="time" name="bookedTime" id="bookedTime" required='Booked Time' step="300" class="form-control" value="<?php echo isset($dbs_bookedtime) ? $dbs_bookedtime : '' ?>" />
</div>
<div class="form-group col-md-4 col-sm-6">
  <label id="labnamedbooking">Booking Name</label>
  <input class="form-control" id="namedbooking" name="namedbooking" type="text" required='booking' value="<?php echo isset($dbs_bookednamed) ? $dbs_bookednamed : '' ?>" />
</div>
<?php
if (isset($_POST['submit'])) {
  $oFieldVal = $_POST['bookedDate'];
  $acttObj->editFun($table, $edit_id, 'bookeddate', $oFieldVal);
  $oFieldVal = $_POST['bookedTime'];
  $acttObj->editFun($table, $edit_id, 'bookedtime', $oFieldVal);
  $oFieldVal = $_POST['namedbooking'];
  $acttObj->editFun($table, $edit_id, 'namedbooked', $oFieldVal);
}
?>