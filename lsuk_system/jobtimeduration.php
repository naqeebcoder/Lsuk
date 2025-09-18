<script type="text/javascript">
function dur_finder(){

var datetime=$('#assignDate').val()+' '+$('#assignTime').val();
var duration=$('#assignDur').val();
        $.ajax({
            url:'ajax_add_interp_data.php',
            method:'post',
            data:{'datetime':datetime,'duration':duration,val:'dur_finder'},
            success:function(data){
                $('#assignEndTime').val(data);
        }, error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        }
        });

}
function check_guess_duration()
{
  var assignTime = $('#assignDur').val();
  var guessTime = $('#guess_dur').val();
  //convert assignTime to minutes
  var assignTime_min = assignTime.split(':');
  var assignTime_min = parseInt(assignTime_min[0])*60 + parseInt(assignTime_min[1]);
  //convert guessTime to minutes
  var guessTime_min = guessTime.split(':');
  var guessTime_min = parseInt(guessTime_min[0])*60 + parseInt(guessTime_min[1]);
  //calculate difference
  var diff = assignTime_min - guessTime_min;
  if(diff > 0)
  {
    alert("Expected duration is less than assignment duration");
    $('#guess_dur').val('').focus();
  }

}
</script>
<div class="form-group col-md-3 col-sm-6">
<label>Assignment  Time *
</label>
<input onkeyup="dur_finder();" name="assignTime" id="assignTime" type="time"  step="300" class="form-control time_picker" required='' value="<?php echo isset($assignTime)?$assignTime:''; ?>" />
</div>
<div class="form-group col-md-3 col-sm-6">
<label>Assignment  Duration * (in Minutes)
</label>
<input id="assignDur" onblur="check_guess_duration();"  onkeyup="dur_finder();" name="assignDur" type="text" pattern="[0-9 :]{5}" maxlength="5" class="form-control" value="<?php echo isset($assignDur)?SetValueAsTime($assignDur):''; ?>" required='' placeholder="Hours : Minutes"/>
</div>
<div class="form-group col-md-3 col-sm-6">
<label>Expected Duration * (Hours:Minutes)</label>
<input id="guess_dur" name="guess_dur" type="text" pattern="[0-9 :]{5}" maxlength="5" class="form-control" value="<?php echo isset($guess_dur) && !empty($guess_dur)?SetValueAsTime($guess_dur):SetValueAsTime($assignDur); ?>" required="" placeholder="Hours : Minutes">
</div>
<div class="form-group col-md-4 col-sm-6">
<label>Assignment End Time</label>
<?php 
function SetValueAsTime($data){
  if (!isset($data))
    return "";
  $mins=$data % 60;
  $hours=$data / 60;
  $data=sprintf("%02d:%02d",$hours,$mins);
  return $data;
}
$input_time = date($assignDate.' '.$assignTime);
$newTime = date("m/d/Y H:i",strtotime("+$assignDur minutes", strtotime($input_time))); ?>
<input id="assignEndTime" readonly="readonly" name="assignEndTime" type="text" class="form-control" value="<?php echo $newTime; ?>" />	
</div>
<script>
    $('#assignDur,#guess_dur').keyup(function () {
      var cctlength = $(this).val().length; // get character length
      switch (cctlength) {
        case 2:
        var cctVal = $(this).val();
        var cctNewVal = cctVal + ':';
        $(this).val(cctNewVal);
        break;
        case 5:
        break;
        default:
        break;
      }
    });
$("#assignDur,#guess_dur").bind('keypress paste',function (e) {
  var regex = new RegExp(/[0-9]/);
  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
  if (!regex.test(str)) {
    e.preventDefault();
    return false;
  }
});
</script>