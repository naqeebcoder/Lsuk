<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php include 'db.php';include 'class.php';$search_1=@$_GET['search_1']; $search_2=@$_GET['search_2']; $search_3=@$_GET['search_3']; if(empty($search_2)){$search_2= date("Y-m-d");}if(empty($search_3)){$search_3= date("Y-m-d");}	?>
<!doctype html>
<html lang="en">
<!--............................................For Multi-Selection.......................................................................-->    <?php include 'header.php'; ?>      

<?php 
include "incmultiselfiles.php";
?>

<script type="text/javascript">	

$(function() 
{
	$('#search_1').multiselect({includeSelectAllOption: true});
	$('#idcompgrps').multiselect({includeSelectAllOption: true});
});

function FindChildByValue(arrElem,strFind) {	

var i,nLen=arrElem.length;
var elemChild;

for (i=0;i<nLen;i++)
{
	elemChild=arrElem[i];
	if (elemChild.value && elemChild.value==strFind)
		return i;
}
return -1;
}

function GetChildFrom(arrElem,nFrom) 
{	
var i,nLen=arrElem.length;
var elemChild;

var strCos="";
for (i=nFrom;i<nLen;i++)
{
	elemChild=arrElem[i];
	if (!elemChild.dataset["abrv"])
		break;

	if (strCos!="")
		strCos+=",";
	strCos+=elemChild.dataset["abrv"];
}
return strCos;
}

function CompanyGrpChange(elemSel) {	

var opts=elemSel.options;
var childs=elemSel.children;
//var nLen=childs.length;

var arrGrps=$(elemSel).val();
if (arrGrps==null)
	return;

//var arrGrps=strSel.split(",");
var i,nCount=arrGrps.length;
var strOrgGrp;
var nPos;

var strAllComp="";
for (i=0;i<nCount;i++)
{
	strOrgGrp=arrGrps[i];
	nPos=FindChildByValue(childs,strOrgGrp);
	if (nPos>=0)
	{
		if (strAllComp!="")
			strAllComp+=",";
		strAllComp+=GetChildFrom(childs,nPos+1);
	}
}


//alert("CompanyGrpChange:"+strAllComp);

 var x=strAllComp;
 var y = document.getElementById("search_2").value;
 if(!y){
	y="<?php echo $search_2; ?>";
 }
 
 var z = document.getElementById("search_3").value;
 if(!z)
 {
	 z="<?php echo $search_3; ?>";
 }
 
 window.location.href="<?php echo basename(__FILE__);?>" + '?search_1=' + x + '&search_2=' + y + '&search_3=' + z;
 window.location.assign('<?php echo basename(__FILE__);?>?search_1='+ x +'&search_2='+ y + '&search_3=' + z);
}

function myFunction() {	
	 var x = $('#search_1').val();if(!x){x="<?php echo $search_1; ?>";}
	 var y = document.getElementById("search_2").value;if(!y){y="<?php echo $search_2; ?>";}
	 var z = document.getElementById("search_3").value;if(!z){z="<?php echo $search_3; ?>";}
	 window.location.href="<?php echo basename(__FILE__);?>" + '?search_1=' + x + '&search_2=' + y + '&search_3=' + z;
	 
window.location.assign('<?php echo basename(__FILE__);?>?search_1='+ x +'&search_2='+ y + '&search_3=' + z);}
window.addEventListener('click', function(e){  
if ($('#search_1').val() != null) {if (document.getElementById('search_1').contains(e.target)){console.log('inside');} else{myFunction();}}});

</script>
<!--................................//\\//\\//\\//\\//\\........................................................................................-->
<body>    
<?php include 'horz_nav.php'; ?>
	<!-- end of secondary bar -->
	<?php include 'nav.php'; ?>
<!-- end of sidebar -->
	
	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_full">
		<header><h3 class="tabs_involved" style="width:200px;"><a href="<?php echo basename(__FILE__);?>">General Report</a></h3>
        <div align="center" style=" width:60%; margin-left:400px;margin-top:10px;">

		<span>
			<select onchange='CompanyGrpChange(this);' id='idcompgrps' multiple="multiple">
					<?php include 'multiselectcompgrp.php'; ?>
			</select>

		</span>
		
        <select id="search_1" name="search_1" multiple="multiple">
		     <?php 			
$sql_opt="SELECT name,abrv FROM comp_reg ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $name_opt=$row_opt["name"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
					<?php echo $options; ?>
                    </option>
	        </select>
		    |
        <input type="date" name="search_2" id="search_2" placeholder='' style="border-radius: 5px;" onChange="myFunction()" value="<?php echo $search_2; ?>"/> |
        <input type="date" name="search_3" id="search_3" placeholder='' style="border-radius: 5px;" onChange="myFunction()" value="<?php echo $search_3; ?>" />
         <a href="reports_lsuk/excel/<?php echo basename(__FILE__);?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>" title="Download Excel Report">Excel</a>
        
        
        
        </div>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content" align="center">
			                
			<iframe height="1000px" width="950px" src="reports_lsuk/pdf/<?php echo basename(__FILE__);?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>" ></iframe>

		  </div><!-- end of #tab1 -->
			
			
			
		</div><!-- end of .tab_container -->
		
		</article><!-- end of content manager article --><!-- end of messages article -->
		
    <div class="clear"></div>
		
		<!-- end of post new article -->
		
		<div class="spacer"></div>
	</section>


</body>

</html>