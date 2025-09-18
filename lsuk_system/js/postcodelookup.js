
function FindInSelect(strCity,options) 
{
  var i,nLen=options.length;
  var opt;

  for (i=0;i<nLen;i++)
  {
    opt=options[i];
    if (opt.text==strCity)
      return i;
  }
  return -1;
}

function TrimAddress(obj,nInd) 
{
  var strAddr=obj.addresses[nInd];
  var arrFields=strAddr.split(",");

  var i,len=arrFields.length;
  var strField;
  for (i=0;i<len;i++)
  {
    strField=arrFields[i];
    arrFields[i]=$.trim(strField);
  }
  return arrFields;
}

function JoinArray(arr,nFrom,nTo,strSep)
{
  var i,len=arr.length;
  var str;
  var strNew="";

  for (i=nFrom;i<=nTo;i++)
  {
    str=arr[i];
    if (str=="")
      continue;

    if (strNew!="")
      strNew+=strSep;
    strNew+=str;
  }
  return strNew;
}

function PostCodeListChanged(elemSel)
{
  if (!g_postcodeLooked)
    return;

  var obj=g_postcodeLooked;
  var nSel=elemSel.selectedIndex;
  if (nSel>=obj.addresses.length)
    return;

  //obj.addresses[nSel];
  arrFields=TrimAddress(obj,nSel);
  PostCodeSetFields(arrFields);

}

function PostCodeSetFields(arrFields)
{
  $("#buildingName").val(arrFields[0]);

  var strStreet=JoinArray(arrFields,1,4,",");
  $("#street").val(strStreet);
  //$("#street").text(strStreet);

  var strCity=JoinArray(arrFields,5,6,",\n");
  var strCityFind=JoinArray(arrFields,5,6,", ");

  var jqSelect=$("#assignCity");
  var elemSelect=jqSelect[0];
  var nSel=FindInSelect(strCityFind,elemSelect.options);

  if (nSel>=0)
  {
    jqSelect.val(strCity);
    jqSelect.change();
  }
  else
  {
    //jqSelect.append("<option value='"+strCity+"'>"+strCity+"</option>");
    //$("#theSelectId").prepend("<option value='' selected='selected'></option>");
    jqSelect.prepend("<option value='"+strCity+"'>"+strCity+"</option>");
    jqSelect.val(strCity);
    jqSelect.change();
  }
}

function PostCodeChanged()
{
  DebugHere();
  //alert(" PostCodeChanged blur");
  //var elemPC=document.getElementById("inchPcode");
  var elemPC=document.getElementById("postCode");
  var strCode=elemPC.value;

  $.ajax({			
			type:'GET',
			//url:'http://api.getthedata.com/postcode/'+strCode,
      url:'https://api.getaddress.io/find/'+strCode+'?api-key=JwJX4MXnkEihbeI4wAPTIg14351',

      //url:strUrl,
      //jsonp: "callback",
      //dataType: "jsonp",

      //data:null,
      error:function(ajaxresult)
      {
      },
      success:function(ajaxresult)
      {
        //console.log(ajaxresult);
		DebugHere();
        //alert("before:"+ajaxresult);
		//obj = JSON.parse(ajaxresult);
        //alert("after");
        obj = ajaxresult;
        g_postcodeLooked=obj;
        if (obj.addresses.length<1)
          return;
        var nLen=obj.addresses.length;
        var strAddr;
        var arrFields;
        var strOptions="";
        for (i=0;i<nLen;i++){
          strAddr=obj.addresses[i];
          arrFields=TrimAddress(obj,i);
          strOptions+="<option value='"+i+"'>"+arrFields[0]+"</option>";
        }
        $("#postcode_data").val(obj.latitude+","+obj.longitude);
        $("#postcodelist").html(strOptions);
        arrFields=TrimAddress(obj,0);
        PostCodeSetFields(arrFields);
      }
    });
    
    return false;

}

function EditStreet() 
{
  $("#buildingName").removeAttr("readonly");
  $("#buildingName").focus();
}

