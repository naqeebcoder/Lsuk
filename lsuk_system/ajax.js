function ParseDateToDateType(strDate) {

  var y = strDate.substr(0, 4);
  var m = strDate.substr(5, 2) - 1;
  var d = strDate.substr(8, 2);
  var dt = new Date(y, m, d);
  return dt;
}

function ParseTimeTotalMins(strTime) {
  var h1 = strTime.substr(0, 2);
  h1 = parseInt(h1);
  var m1 = strTime.substr(3);
  m1 = parseInt(m1);
  var nTotMin = h1 * 60 + m1;
  return nTotMin;
}

function FilterBookType(resp) {
  resp = g_resp;
  if (g_strJobTableIs != "interpreter" && g_strJobTableIs != "telephone") {
    $("#bookinType").html(resp);

    return;
  }

  var elemDate = document.getElementById("bookedDate");
  var elemTime = document.getElementById("bookedTime");
  var strDate = elemDate.value;
  var strTime = elemTime.value;
  var elemAssignTime = document.getElementById("assignTime");
  var strTime2 = elemAssignTime.value;
  var nTotMin2 = ParseTimeTotalMins(strTime2);
  var nFromMin = ParseTimeTotalMins("07:00");
  var nToMin = ParseTimeTotalMins("19:00");
  var strCatIs = "?";

  //1.<48 hours = urgent
  if (strCatIs == "?") {
    var nTotMin = ParseTimeTotalMins(strTime);
    var nMilliMin = 1000 * 60;
    var elemAssignDate = document.getElementById("assignDate");
    var strDate2 = elemAssignDate.value;
    var dt2 = ParseDateToDateType(strDate2);
    var nTotMin2 = ParseTimeTotalMins(strTime2);
    var tm2 = Math.floor(dt2 / nMilliMin) + nTotMin2;
    var dt = ParseDateToDateType(strDate);
    var tm1 = Math.floor(dt / nMilliMin) + nTotMin;
    var dm = tm2 - tm1;
    var mins48Hr = 48 * 60;
    if (dm <= mins48Hr){
      strCatIs = "urgent";
    } else {
      var nDay = dt2.getDay();
      if (nDay != 0 && nDay != 6) {
        var adj = 0;
        var dtFrom = dt;
        dtFrom.setDate(dtFrom.getDate() + 1);
        while (dtFrom < dt2) {
          var nDay = dtFrom.getDay();
          if (nDay == 0 || nDay == 6) adj += mins48Hr / 2;
          dtFrom.setDate(dtFrom.getDate() + 1);
        }
        dm -= adj;
        if (dm <= mins48Hr) strCatIs = "urgent";
      }
    }
  }

  //2.out of hours
  if (strCatIs == "?") {
    if (nTotMin2 < nFromMin || nTotMin2 > nToMin) strCatIs = "outofhours";
  }

  //3.weekend

  if (strCatIs == "?") {
    var nDay = dt2.getDay();
    if (nDay == 0 || nDay == 6) strCatIs = "weekend";
  }

  //2.out of hours
  if (strCatIs == "?") {
    if (nTotMin2 > nFromMin && nTotMin2 < nToMin) strCatIs = "";
  }

  $("#bookinType").html(resp);
  if (strCatIs == "?") strCatIs = "flat";

  var nMatches = FilterBookingType(strCatIs);
  if (nMatches == 0) FilterBookingType("");
}

function OnDateChgAjax() {
  // FilterBookType();
}

function OnTimeChgAjax() {
  // FilterBookType();
}

function FilterBookingType(strCatIs) {
  var elemSel = $("#bookinType")[0];
  var options = elemSel.options;
  var i, nCount = options.length;
  var option;
  var strCatIs2 = "";
  if (strCatIs == "weekend" || strCatIs == "outofhours")
    strCatIs2 = "weoutofhours";
  for (i = 0; i < nCount; i++) {
    option = options[i];
    if (option.value != "all" && option.value != "flat") {
      bDel = true;
      if (option.value == strCatIs || (strCatIs2 != "" && option.value == strCatIs2)) {
        bDel = false; 
      }

      if (bDel) {
        option.style.display = "none";
      }
    }
  }
  $(elemSel).prop("selectedIndex", 0);
  return nCount;
}

//Commented out as we have made new model

// $(document).ready(function () {
//   var elemDate = document.getElementById("assignDate");
//   elemDate.addEventListener("change", OnDateChgAjax);
//   var elemTime = document.getElementById("assignTime");
//   if (elemTime) elemTime.addEventListener("change", OnTimeChgAjax);
//   $("#orgName").change(function () {
//     var orgName_id = $(this).val();
//     if (orgName_id != "") {
//       $.ajax({
//         url: "get-states.php",
//         data: { o_id: orgName_id, jobtab: g_strJobTableIs },
//         type: "POST",
//         success: function (response) {
//           var resp = $.trim(response);
//           $("#bookinType").html(resp);
//           g_resp = resp;
//           FilterBookType();
//         },
//       });
//     } else {
//       $("#bookinType").html("<option value=''>------- Select --------</option>");
//     }
//   });
// });
