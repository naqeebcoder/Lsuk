
function GetActionList()
{
    var ancs=$("td.ajaxactions");
    //var elemAncs=ancs[0];
    var elemAnc=ancs[0];
    
    var nodes=elemAnc.children;
    var nCount=nodes.length;

	//var ancInps=$("td.ajaxactions a");
	//var i,nCount=ancInps.length;
	//var elemJQ,ancInp;

    //var strJobIds="";

    var arrUrls=[];
    var elem;
    var strUrlIs;
	for (i=0;i<nCount;i++)
	{
        elem=nodes[i];

        strUrlIs=GetActionUrl(elem);
        if (strUrlIs!=null)
            arrUrls.push(strUrlIs);
	}
    var strUrls=arrUrls.join(",");
    GetActionListSend(strUrls);
}

function GetActionUrl(elem)
{
    var strHref,elemChild;
    var pos,pos2,strFuncUrl;

    //strHref=elem.href;
        //strHref=elem.attributes["href"].value;
        strHref=elem.getAttribute("href");

        if (strHref!="#")
        {
            pos=strHref.indexOf("?");
            if (pos>=0)
                strHref=strHref.substring(0,pos);

            return strHref;
        }
        else
        {
            elemChild=elem.children[0];

            //onclick="MM_openBrWindow('telep_view.php?view_id=1544&amp;table=telephone','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">

            strHref=elem.getAttribute("onclick");
            if (strHref==null)
                return null;

            //$pos=strpos($strHref,"(",0);
            pos=strHref.indexOf("(");
            if (pos<0)
            return null;
            pos2=strHref.indexOf("?",pos);
            if (pos2<0)
                return null;

            strFuncUrl=strHref.substring(pos+2,pos2);
            return strFuncUrl;

            //arrUrls.push(strFuncUrl);
        }
        return null;
}

function GetActionListDone(mapUrls)
{
    var ancs=$("td.ajaxactions");

    var strUrlIs;
    var nRow,nRows=ancs.length;
    var nodes,nCol,nCols;
    for (nRow=0;nRow<nRows;nRow++)
    {
        elemAnc=ancs[nRow];

        nodes=elemAnc.children;
        nCols=nodes.length;

        for (nCol=0;nCol<nCols;nCol++)
    	{
            elem=nodes[nCol];
            strUrlIs=GetActionUrl(elem);
            if (strUrlIs!=null)
            {
                if (mapUrls[strUrlIs]!=1)
                {
                    elem.style.display="none";
                }
            }
        }
        //elemAnc.style.display="block";
        //table-cell
        //elemAnc.style.display="inherit";
        //elemAnc.style.display="table-cell";
        elemAnc.className="";
    }
}

function GetActionListSend(strUrls) 
{
    //alert("DoReadNote("+strJobIds+") here");

    formURL = 'ajaxgetactions.php';
    //    formURL = 'ajaxListJobNotes.php';

		
	var strJobTbl="telephone";
	var nCountIs="123";

    $.ajax({
        url : formURL,
        type: "POST",
        data: {urls:strUrls,jobtbl:strJobTbl,
			counted:nCountIs ,colName: "test"},
        success:function(strData, textStatus, jqXHR)
        {
          if(strData)
          {
			var mapUrls=JSON.parse(strData);
            //alert("OK got strData: after");

			GetActionListDone(mapUrls);
          }
          else
          { 
            alert("no data OK")
          }
        },
        error: function(jqXHR, textStatus, errorThrown)
        { 
          alert("GetActionListDone()- Something wrong with Jquery");
        }
    });
}
