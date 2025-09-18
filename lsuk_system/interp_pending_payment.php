<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
?>

<?php
include 'db.php';
include 'class.php';
include_once 'function.php';
$assignDate = @$_GET['assignDate'];
$interp = @$_GET['interp'];
$org = @$_GET['org'];
$job = @$_GET['job'];
$our = @$_GET['our'];
$ur = @$_GET['ur'];
$inov = @$_GET['inov'];

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;

?>

<!doctype html>
<html lang="en">
<script>
function myFunction() {
	 var o = document.getElementById("inov").value;if(!o){o="<?php echo $inov; ?>";}
	 var p = document.getElementById("our").value;if(!p){p="<?php echo $our; ?>";}
	 var q = document.getElementById("ur").value;if(!q){q="<?php echo $ur; ?>";}
	 var w = document.getElementById("assignDate").value;if(!w){w="<?php echo $assignDate; ?>";}
	 var x = document.getElementById("interp").value;if(!x){x="<?php echo $interp; ?>";}
	 var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
	 var z = document.getElementById("job").value;if(!z){z="<?php echo $job; ?>";}
	 window.location.href="interp_pending_payment.php" + '?interp=' + x + '&org=' + y + '&job=' + z + '&assignDate=' + w + '&our=' + p+ '&ur=' + q + '&inov=' + o;

}
</script>
<?php include 'header.php';?>
<link rel="stylesheet" href="css/bootstrap.css">
<style>.tablesorter thead tr{background: none !important;}</style>
<body>
<?php include 'horz_nav.php';?>
	<!-- end of secondary bar -->
	<?php include 'nav.php';?>
<!-- end of sidebar -->

	<section id="main" class="column"><!-- end of stats article -->
				<article class="module width_full">
		<header>
		  <h3 class="tabs_involved" style="width:236px;"><a href="<?php echo basename(__FILE__); ?>">Pending Invoices - F2F</a></h3>
		  <div align="right" style=" width:75%; float:right;margin-top:3px;">
          <input type="text" name="inov" id="inov" style="width:80px;height:19px;" placeholder="Invoice #"onChange="myFunction()" value="<?php echo $inov; ?>"/>
          <input type="text" name="our" id="our" style="width:80px;height:19px;" placeholder="Our Ref"onChange="myFunction()"value="<?php echo $our; ?>"/>
          <input type="text" name="ur" id="ur" style="width:80px;height:19px;"placeholder="Your Ref"onChange="myFunction()"value="<?php echo $ur; ?>"/>
		    <select id="interp" onChange="myFunction()" name="interp"style="width:100px;height:25px;">
		      <?php
$sql_opt = "SELECT distinct interpreter_reg.name,interpreter_reg.id,interpreter_reg.gender, interpreter_reg.city FROM interpreter_reg
JOIN interp_lang ON interpreter_reg.code=interp_lang.code
JOIN interpreter ON interpreter.intrpName=interpreter_reg.id
where interpreter.multInv_flag=0 and interpreter.commit=1 and (total_charges_comp > rAmount or total_charges_comp =0) and (orgName like '%$_words%')
 ORDER BY name ASC";
$result_opt = mysqli_query($con, $sql_opt);
$options = "";
while ($row_opt = mysqli_fetch_array($result_opt)) {
    $code = $row_opt["name"];
    $name_opt = $row_opt["name"];
    $city_opt = $row_opt["city"];
    $gender = $row_opt["gender"];
    $options .= "<OPTION value='$code'>" . $name_opt . ' (' . $gender . ')' . ' (' . $city_opt . ')';}
?>
		      <?php if (!empty($interp)) {?>
		      <option><?php echo $interp; ?></option>
		      <?php } else {?>
		      <option value="">--Select Interpreter--</option>
		      <?php }?>
		      <?php echo $options; ?>
		      </option>
	        </select>
		    |
  <select id="org" name="org" onChange="myFunction()" style="width:100px; height:25px;">
    <?php
$sql_opt = "SELECT distinct comp_reg.name,comp_reg.abrv FROM comp_reg
JOIN interpreter ON interpreter.orgName=comp_reg.abrv
where interpreter.multInv_flag=0 and interpreter.commit=1 and (total_charges_comp > rAmount or total_charges_comp =0)
 ORDER BY comp_reg.name ASC";
$result_opt = mysqli_query($con, $sql_opt);
$options = "";
while ($row_opt = mysqli_fetch_array($result_opt)) {
    $code = $row_opt["abrv"];
    $name_opt = $row_opt["name"];
    $options .= "<OPTION value='$code'>" . $name_opt . ' (' . $code . ')';}
?>
    <?php if (!empty($org)) {?>
    <option><?php echo $org; ?></option>
    <?php } else {?>
    <option value="">--Select Company--</option>
    <?php }?>
    <?php echo $options; ?>
    </option>
  </select>
		    |
  <select name="job" id="job" onChange="myFunction()" style="width:100px;height:25px;">
    <?php
$sql_opt = "SELECT distinct lang FROM lang
JOIN interpreter ON interpreter.source=lang.lang
where interpreter.multInv_flag=0 and interpreter.commit=0
 ORDER BY lang ASC";
$result_opt = mysqli_query($con, $sql_opt);
$options = "";
while ($row_opt = mysqli_fetch_array($result_opt)) {
    $code = $row_opt["lang"];
    $name_opt = $row_opt["lang"];
    $options .= "<OPTION value='$code'>" . $name_opt;}
?>
    <?php if (!empty($job)) {?>
    <option><?php echo $job; ?></option>
    <?php } else {?>
    <option value="">--Select Language--</option>
    <?php }?>
    <?php echo $options; ?>
    </option>
  </select>
		    |
  <input type="date" name="assignDate" id="assignDate" placeholder='' style="width:100px;border-radius: 5px;" onChange="myFunction()" value="<?php echo $assignDate; ?>"/>
	      </div>
		</header>
		<div class="tab_container">
			<div id="tab1" class="tab_content">
			 <table class="tablesorter table table-bordered table-hover" cellspacing="0" width="100%">
                        <thead class="bg-primary">
                            <tr>
				  <th>Interpreter</th>
                	<th>Source Lang</th>
                	<th>Assign-Date</th>
                	<th>Assign-Time</th>
    				<th>Company Name</th>
    				<th>Contact Name</th>
                    <th>Entered By</th>
    				<th>Allocated By</th>
                    <th>Intrp Hrz</th>
                    <th>comp Hrz</th>

                    <th width="40">Emailed</th>
                    <th>Printed By</th>

                    <th>Invoice Amount</th>
                    <th>Received Amount</th>
    				<th>Dated</th>
                    <th width="120" align="center">Actions</th>
   				</tr>
			</thead>
			<tbody style="font-size:11px;">
                        <?php $arr = explode(',', $org);
$_words = implode("' OR orgName like '", $arr);
$arr_intrp = explode(',', $interp);
$_words_intrp = implode("' OR name like '", $arr_intrp);?>
      <?php $table = 'interpreter';
$counter = 0;

$query =
    "SELECT $table.* ,interpreter_reg.name,comp_reg.email
		FROM $table
	   		JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
			   left outer JOIN comp_reg ON $table.orgName=comp_reg.abrv

	   		where $table.deleted_flag = 0 and $table.order_cancel_flag=0 and commit=1 and
			   (total_charges_comp > rAmount or total_charges_comp =0) and
			   $table.assignDate like '$assignDate%' and source like '$job%' and
			   interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' and
			   $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and
			   $table.invoiceNo like '%$inov%'
			order by $table.assignDate
			LIMIT {$startpoint} , {$limit}";

$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_array($result)) {
    $counter++;?>
            		<tr>
            		  <td><?php if ($row['hoursWorkd'] == 0) {?>
            		    <span style="color:#F00" title="Interp Hours: <?php echo $row['hoursWorkd']; ?>"><?php echo $row['name']; ?></span>
            		    <?php } else {echo $row['name'];}?></td>
                	<td><?php echo $row['source']; ?></td>
                	<td><?php echo $misc->dated($row['assignDate']); ?></td>
                	<td><?php echo $row['assignTime']; ?></td>
   					<td><?php if ($row['C_hoursWorkd'] == 0) {?><span style="color:#F00" title="Comp Hours: <?php echo $row['C_hoursWorkd']; ?>"><?php echo $row['orgName']; ?></span><?php } else {echo $row['orgName'];}?></td>
    				<td><?php echo $row['orgContact']; ?></td>
    				<td><?php echo $row['submited'] . '(' . $misc->dated($row['dated']) . ')'; ?></td>
    				<td><?php echo $row['aloct_by'] . '(' . $misc->dated($row['aloct_date']) . ')'; ?></td>
    				<td><?php echo $row['hrsubmited'] . '(' . $misc->dated($row['interp_hr_date']) . ')'; ?></td>
    				<td><?php echo $row['comp_hrsubmited'] . '(' . $misc->dated($row['comp_hr_date']) . ')'; ?></td>

<?php
//##actionprinted
    include "viewactionprinted.php";
    ?>

					<?php
//##gotcreditnote

    $totalforvat = $row['total_charges_comp'];
    $vatpay = $totalforvat * $row['cur_vat'];
    $totinvnow = $totalforvat + $vatpay + $row['C_otherexpns'];

    /*$totinvnow2=$row['total_charges_comp']* $row["cur_vat"] +
    $row['total_charges_comp'] + $row['C_otherexpns'] + $row['C_admnchargs'];*/

    $gotcreditnote = false;
    if (isset($row['credit_note']) && $row['credit_note'] != "") {
        $totinvnow = 0;
        $gotcreditnote = true;
    }

    ?>
				<!--	<td><?php echo $new_total; ?></td>-->
					<td><?php echo $totinvnow; ?></td>
    				<td><?php echo $row['rAmount']; ?></td>
                    <td><?php echo $misc->dated($row['rDate']); ?></td>

    				<td align="center"><a href="#" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'title', 1000, 1000);">
    				  <input type="image" src="images/icn_new_article.png" title="View Order">
    				  </a>

    				  <a href="#" onClick="MM_openBrWindow('receive_amount.php?row_id=<?php echo $row['id']; ?>&table=<?php echo $table ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
    				    <input type="image" src="images/Cash.png" title="Payment Received">
   				      </a>
<a href="#" onClick="MM_openBrWindow('invoice.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/invoice.png" title="Invoice"></a>
<?php if ($row['sentemail'] == 1) {
	  ?>
		<a href="#" onClick="MM_openBrWindow('invoice.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/email_sent_icon.jpg" title="Email Sent"></a>
<?php }else{ ?>
<a href="#" onClick="MM_openBrWindow('invoice.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/email_icon.jpg" title="Send Email"></a>
<?php } ?>



<?php
if ($gotcreditnote) {
        ?>
    <a href="#" onClick="MM_openBrWindow('credit_interp.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
		<input style="background-color:red;" type="image" src="images/icn_categories.png" title="Credit Note">
	</a>
	<?php
} else {
        ?>
	<input type="image" src="images/icn_categories.png" title="No Credit Note">
	<?php
}
    ?>

     <a href="#" onClick="MM_openBrWindow('un_commit.php?com_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=500,height=250')">
    				  <input type="image" src="images/icn_jump_back.png" title="Un-commit payment">
    				  </a>

                      <a href="#" onClick="MM_openBrWindow('purch_update.php?purch_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&orgName=<?php echo $row['orgName']; ?>','_blank','scrollbars=yes,resizable=yes,width=500,height=300')"><input type="image" src="images/icn_tags.png" title="Update Purchase Order #"></a>
                      <a href="#" onClick="MM_openBrWindow('comp_earning.php?view_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/earning.png" title="Earning"></a>

					  <a href="#" class="clsActionJobNote" onclick="MM_openBrWindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo $table; ?>
 	&orgName=<?php echo $row['orgName']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')">
	 <input data-jobid="<?php echo $row['id']; ?>" type="image" src="images/post_message.png" title="Job Note"
	 	width="17" height="17"></a>



   				</tr>
                <?php }?>
                </tbody></table>
			<div><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
		  </div><!-- end of #tab1 -->



		</div><!-- end of .tab_container -->

		</article><!-- end of content manager article --><!-- end of messages article -->

    <div class="clear"></div>

		<!-- end of post new article -->

		<div class="spacer"></div>
	</section>


</body>

</html>
<script>

function DoCountNotes(strJobIds)
{
    //alert("DoReadNote("+strJobIds+") here");

    formURL = 'ajaxListJobNotes.php';

	//var strJobId="1";
	//var strfid="1";

	var strJobTbl="interpreter";
	var nCountIs="123";

    $.ajax({
        url : formURL,
        type: "POST",
        data: {jobids:strJobIds,jobtbl:strJobTbl,
			counted:nCountIs ,colName: "test"},
        success:function(strData, textStatus, jqXHR)
        {
          if(strData)
          {
            //alert("OK got strData: before:"+strData);

			var mapjobs=JSON.parse(strData);
            //alert("OK got strData: after");

			DoCountNotesDone(mapjobs);

			//var elemDiv=document.getElementById("notescontainer")
            //elemDiv.innerHTML=strData;

            /*var jq=$(elemDiv).find("table");
            if (jq.length>0)
            {
              var elemTab=jq[0];
              //alert("elemTab:"+elemTab.dataset["countis"]);
            }*/
          }
          else
          {
            alert("no data OK")
          }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
          alert("DoCountNotes()- Something wrong with Jquery");
        }
      });

  }

function DoCountNotesDone(mapjobs)
{
    //alert("modify job note"+ nCount);

	var ancInps=$("a.clsActionJobNote input");
	var i,nCount=ancInps.length;
	var elemJQ,ancInp;

	var strJobId,strReadUn;
	var arrReadUn;

	for (i=0;i<nCount;i++)
	{
		elemJQ=ancInps[i];
		ancInp=elemJQ;

		strJobId=ancInp.dataset['jobid'];

		strReadUn=mapjobs[strJobId];
		arrReadUn=strReadUn.split(",");

		ancInp.title="Job Note(unread:"+arrReadUn[0]+",read:"+arrReadUn[1]+")";
		ancInp.src=arrReadUn[0]==0?"images/post_message.png":"images/post_messagered.png";
	}
}


window.onload = function()
{
	var ancInps=$("a.clsActionJobNote input");
	var i,nCount=ancInps.length;
	var elemJQ,ancInp;

	var strJobIds="";
	for (i=0;i<nCount;i++)
	{
		elemJQ=ancInps[i];
		ancInp=elemJQ;

		if (strJobIds!="")
			strJobIds+=",";
		strJobIds+=ancInp.dataset['jobid'];

		ancInp.title+="("+i+")";
	}

//	 <input data-jobid="<?php echo $row['id']; ?>" type="image" src="images/post_message.png" title="Job Note" width="17" height="17"></a>

	DoCountNotes(strJobIds);
	//alert("onload");
};
</script>
