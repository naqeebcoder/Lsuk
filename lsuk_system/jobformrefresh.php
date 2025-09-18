<script type="text/javascript" >

window.onload = function () 
  {
    var str=window.location.href;
    var strQVar="";
    var nPos=str.indexOf("?");
    if (nPos>=0)
    {
      //abrv
      //orgname
      //refreshedid
      var strRight=str.substr(nPos+1);
      var arrNamVals=strRight.split("&");
      var strNameVal=arrNamVals[0];
      var arr=strNameVal.split("=");
      strQVar=arr[1];
    }
    
    //alert("url is:"+strQVar);

    /*$('#orgName option').each(function() {
      if($(this).val() == strQVar) {
        $(this).prop("selected", true);
      }
    });*/

    $('#orgName').val(strQVar);
    //GetOrganizationFields();
    $('#orgName').change();
  }

</script>

