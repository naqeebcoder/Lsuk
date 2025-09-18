<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php 
	if(session_id() == '' || !isset($_SESSION))
	{
		session_start();
	} 
?> 

<?php 
	include 'db.php';
	include 'class.php';
	include_once ('function.php');

	/*$assignDate=@$_GET['assignDate']; 
	$interp=@$_GET['interp']; 
	$org=@$_GET['org']; 
	$job=@$_GET['job'];
	$our=@$_GET['our'];
	$ur=@$_GET['ur'];
	$inov=@$_GET['inov'];*/
		
	$assignDate=SafeVar::GetVar('assignDate',''); 
	$interp=SafeVar::GetVar('interp',''); 
	$org=SafeVar::GetVar('org',''); 
	$job=SafeVar::GetVar('job',''); 
	$our=SafeVar::GetVar('our',''); 
	$ur=SafeVar::GetVar('ur',''); 
	$inov=SafeVar::GetVar('inov',''); 

	$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
    $limit = 50;
	$startpoint = ($page * $limit) - $limit;	
?>

<?php 
  include "joblistintercol.php"
?>

<!doctype html>
<html lang="en">
<head>
<title>INTERPRETER BOOKING LIST</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<?php include 'header.php'; ?>
<body>
    <script>
function myFunction() 
{
	 var o = document.getElementById("inov").value;if(!o){o="<?php echo $inov; ?>";}
	 var p = document.getElementById("our").value;if(!p){p="<?php echo $our; ?>";}
	 var q = document.getElementById("ur").value;if(!q){q="<?php echo $ur; ?>";}
	 var w = document.getElementById("assignDate").value;if(!w){w="<?php echo $assignDate; ?>";}
	 var x = document.getElementById("interp").value;if(!x){x="<?php echo $interp; ?>";}
	 var y = document.getElementById("org").value;if(!y){y="<?php echo $org; ?>";}
	 var z = document.getElementById("job").value;if(!z){z="<?php echo $job; ?>";}
	 window.location.href="interp_list.php" + '?interp=' + x + '&org=' + y + '&job=' + z + 
	 	'&assignDate=' + w + '&our=' + p + '&ur=' + q + '&inov=' + o;
}
</script>
<?php include 'nav2.php';?>
<!-- end of sidebar -->
	<style>.tablesorter thead tr {background: none;}</style>
<section class="container-fluid" style="overflow-x:auto">
<div class="col-md-12">
		<header>
		    <center><a href="<?php echo basename(__FILE__);?>"><h2 class="col-md-3 col-md-offset-4 text-center"><span class="label label-primary">INTERPRETER BOOKING LIST</span></h2></a></center>
   <div class="col-md-12"><br>
             <div class="form-group col-md-1 col-sm-4">
          <input type="text" name="inov" id="inov" class="form-control" placeholder="Invoice #" onChange="myFunction()" value="<?php echo $inov; ?>"/> 
          </div>
	        <div class="form-group col-md-1 col-sm-4">
          <input type="text" name="our" id="our" class="form-control" placeholder="Our Ref" onChange="myFunction()" value="<?php echo $our; ?>"/> 
          </div>
	        <div class="form-group col-md-1 col-sm-4">
          <input type="text" name="ur" id="ur" class="form-control" placeholder="Your Ref" onChange="myFunction()" value="<?php echo $ur; ?>"/> 
          </div>
	        <div class="form-group col-md-2 col-sm-4">
          
		    <select id="interp" onChange="myFunction()" name="interp" class="form-control">
			  <?php 			
			  

$sql_words="";
if (isset($_words))
	$sql_words=" and (orgName like '%$_words%')";
			  
							
$sql_opt="SELECT distinct interpreter_reg.name FROM interpreter
	   JOIN interpreter_reg ON interpreter.intrpName=interpreter_reg.id
	   where  interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 and  
	   interpreter.assignDate like '$assignDate%' and interpreter.multInv_flag=0 and 
	   interpreter.commit=0 and source like '$job%' $sql_words and 
	   interpreter.nameRef like '%$our%' and interpreter.orgRef like '%$ur%' and 
	   interpreter.invoiceNo like '%$inov%'
		 ORDER BY name ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";

while ($row_opt=mysqli_fetch_array($result_opt)) {

	/*$code=$row_opt["name"];
	$name_opt=$row_opt["name"];
	$city_opt=$row_opt["city"];
	$gender=$row_opt["gender"];*/

	$code=SafeVar::Get($row_opt,"name","");
	$name_opt=SafeVar::Get($row_opt,"name","");
	$city_opt=SafeVar::Get($row_opt,"city","");
	$gender=SafeVar::Get($row_opt,"gender","");

	$options.="<OPTION value='$code'>".$name_opt.' ('. $gender.')'.' ('. $city_opt.')';
}
?>
		      <?php if(!empty($interp)){ ?>
		      <option><?php echo $interp; ?></option>
		      <?php } else{?>
		      <option value="">--Select Interpreter--</option>
		      <?php } ?>
		      <?php echo $options; ?>
		      </option>
	        </select> 
          </div>
	        <div class="form-group col-md-2 col-sm-4">
  <select id="org" name="org" onChange="myFunction()" class="form-control">
    <?php 			
		$sql_opt="SELECT distinct comp_reg.name,comp_reg.abrv FROM comp_reg
		JOIN interpreter ON interpreter.orgName=comp_reg.abrv
		where interpreter.multInv_flag=0 and interpreter.commit=0 and 
		(status <> 'Company Seized trading in this name or Company closed' or status <> 'Company Blacklisted')
		 ORDER BY comp_reg.name ASC";
		 
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["abrv"];
    $name_opt=$row_opt["name"];
    $options.="<OPTION value='$code'>".$name_opt.' ('.$code.')';}
?>
    <?php if(!empty($org)){ ?>
    <option><?php echo $org; ?></option>
    <?php } else{?>
    <option value="">--Select Company--</option>
    <?php } ?>
    <?php echo $options; ?>
    </option>
  </select> 
          </div>
	        <div class="form-group col-md-2 col-sm-4">
  <select name="job" id="job" onChange="myFunction()" class="form-control">
    <?php 			
$sql_opt="SELECT distinct lang FROM lang
JOIN interpreter ON interpreter.source=lang.lang
where interpreter.multInv_flag=0 and interpreter.commit=0
 ORDER BY lang ASC";
$result_opt=mysqli_query($con,$sql_opt);
$options="";
while ($row_opt=mysqli_fetch_array($result_opt)) {
    $code=$row_opt["lang"];
    $name_opt=$row_opt["lang"];
    $options.="<OPTION value='$code'>".$name_opt;}
?>
    <?php if(!empty($job)){ ?>
    <option><?php echo $job; ?></option>
    <?php } else{?>
    <option value="">--Select Language--</option>
    <?php } ?>
    <?php echo $options; ?>
    </option>
  </select> 
          </div>
	        <div class="form-group col-md-2 col-sm-4">
  <input type="date" name="assignDate" id="assignDate" placeholder='' class="form-control" onChange="myFunction()" value="<?php echo $assignDate; ?>"/> 
          </div>
		</header>
		

		<div class="tab_container">
			<div id="tab1" class="tab_content">
			<table class="tablesorter table table-bordered table-hover" cellspacing="0" cellpadding="0">
			<thead class="bg-primary"> 
				<tr>
				  <th>Interpreter</th>
				  <th>Source Lang</th>
				  <th>Assign-Date</th>
				  <th>Assign-Time</th>
    				<th>Company Name</th> 
    				<th>Contact Name</th>
    				<th>Enterd By</th> 
    				<th>Allocated By</th>
                    <th>Intrp Hrz</th> 
                    <th>comp Hrz</th> 
                    <th>Booking Type</th> 
    				<th width="320" align="center">Actions</th> 
				</tr> 
			</thead> 
			<tbody style="font-size:11px;"> 
			<?php $arr = explode(',', $org);
			$_words = implode("' OR orgName like '", $arr);
			$arr_intrp = explode(',', $interp);
			$_words_intrp = implode("' OR name like '", $arr_intrp); ?>  
      <?php $table='interpreter';$counter=0;
	  
	  //$strSelectCols=CodeHere::GetSelectCols();

	  $strSelIs=JobListInterCol::GetSelectCols($table);
	  switch($_SESSION['prv'])
	  { 
		case 'Management':
		
	  //$query="SELECT $table.*,interpreter_reg.name,interpreter_reg.id as interpid FROM $table
	  $query="SELECT ".$strSelIs." FROM $table 
	  JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
	   where  $table.deleted_flag=0 and $table.order_cancel_flag=0 and  
	   		$table.assignDate like '$assignDate%' and $table.multInv_flag=0 and 
		   $table.commit=0 and source like '$job%' and interpreter_reg.name like '%$interp%' and 
		   $table.orgName like '%$org%' and $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and 
		   $table.invoiceNo like '%$inov%'
	   	order by concat(assignDate,assignTime) LIMIT {$startpoint} , {$limit}";
	
		break; 

		case 'Finance':
		
		$query="SELECT ".$strSelIs." FROM $table 
		JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
	   where  $table.deleted_flag=0 and $table.order_cancel_flag=0 and  
	   $table.assignDate like '$assignDate%' and  $table.multInv_flag=0  and $table.commit=0  and
	    source like '$job%' and interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' and 
		$table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
	   order by concat(assignDate,assignTime) LIMIT {$startpoint} , {$limit}";	
		break; 

		case 'Operator':
		
		$query="SELECT ".$strSelIs." FROM $table 
		JOIN interpreter_reg ON $table.intrpName=interpreter_reg.id
		   where  $table.deleted_flag=0 and $table.order_cancel_flag=0 and orderCancelatoin=0 and 
		   $table.hoursWorkd=0 and $table.assignDate like '$assignDate%' and  
		   $table.multInv_flag=0  and $table.commit=0  and source like '$job%' and 
		   interpreter_reg.name like '%$interp%' and $table.orgName like '%$org%' and 
		   $table.nameRef like '%$our%' and $table.orgRef like '%$ur%' and $table.invoiceNo like '%$inov%'
		   order by concat(assignDate,assignTime) LIMIT {$startpoint} , {$limit}";
		break;  
	  }
	  

	  $result = mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result)){$counter++;?>            
				<tr title="<?php echo "Tracking ID:". $row['id']; ?>">

        		  <?php JobListInterCol::GetColTDHoursWorkd($row); ?>
				
				  <td><?php echo $row['source']; ?></td>
				  <td><?php echo $misc->dated($row['assignDate']); ?></td>
				  <td><?php echo $row['assignTime']; ?></td>
					   
				  <td><?php if($row['C_hoursWorkd']==0){ ?>
					<span style="color:#F00" title="Comp Hours: <?php echo $row['C_hoursWorkd']; ?>">
					<?php echo $row['orgName']; ?></span><?php }else{ echo $row['orgName']; }?></td>  
					   
					<td><?php echo $row['orgContact']; ?></td>
					
    				<td><?php echo $row['submited'].'('.$misc->dated($row['dated']).')'; ?></td> 
    				<td><?php echo $row['aloct_by'].'('.$misc->dated($row['aloct_date']).')'; ?></td> 
    				<td><?php echo $row['hrsubmited'].'('.$misc->dated($row['interp_hr_date']).')'; ?></td> 
    				<td><?php echo $row['comp_hrsubmited'].'('.$misc->dated($row['comp_hr_date']).')'; ?></td> 
    				<td><?php echo $row['bookinType']; ?></td>  
    				<td align="center">
<a href="#" onClick="popupwindow('order_view.php?view_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>', 'title', 1000, 1000);"><input type="image" src="images/icn_new_article.png" title="View Order"></a>

<a href="#" onClick="MM_openBrWindow('interp_edit.php?edit_id=<?php echo $row['id']; ?>&duplicate=<?php echo 'yes'; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><input type="image" src="images/commit.png" title="Create Duplicate"></a>
    				  <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Operator'){?>
                      
                    <?php if($_SESSION['prv']=='Management'){?>
<a href="#" onClick="MM_openBrWindow('interp_edit.php?edit_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><input type="image" src="images/icn_edit.png" title="Edit"></a>
<?php } ?>


                    
                    <a href="#" onClick="popupwindow('email_emend.php?email_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&col=intrpName','_blank', 600, 420)"><input type="image" src="images/icn_jump_back.png" title="Amend Enter"></a>
<?php }if($_SESSION['prv']=='Management'){?>

<a href="#" onClick="MM_openBrWindow('del_trash.php?del_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=520,height=350,left=600,top=200')"><input type="image" src="images/icn_trash.png" title="Trash"></a> 
                    <?php }if($_SESSION['prv']=='Management'  || $_SESSION['prv']!='Finance'){ ?>
					
<a href="#" onClick="MM_openBrWindow('update_expenses2.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><input type="image" src="images/Update.png" title="Update Expanses"></a>

 <a href="#" class="clsActionJobNote" onclick="MM_openBrWindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo $table; ?>
 	&orgName=<?php echo $row['orgName']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')">
	 <input data-jobid="<?php echo $row['id']; ?>" type="image" src="images/post_message.png" title="Job Note" 
	 	width="17" height="17"></a> 

<?php }if( $_SESSION['prv']=='Finance'){ ?>
<a href="#" onClick="MM_openBrWindow('update_expenses2.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><input type="image" src="images/Update.png" title="Update Expanses"></a>
 <a href="#" class="clsActionJobNote" onclick="MM_openBrWindow('jobnote.php?fid=<?php echo $row['id']; ?>&table=<?php echo $table; ?>
 	&orgName=<?php echo $row['orgName']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')">
	 <input data-jobid="<?php echo $row['id']; ?>" type="image" src="images/post_message.png" title="Job Note" 
	 	width="17" height="17"></a> 
                    <?php }
                    //if(($row['hoursWorkd']!=0)  && $_SESSION['prv']=='Finance'){ ?>
<!--<a href="#" onClick="MM_openBrWindow('update_expenses2.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=650')"><input type="image" src="images/Update.png" title="Update Expanses"></a> -->

                    <?php // }
                    if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance'){?>
<a href="#" onClick="MM_openBrWindow('invoice.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><input type="image" src="images/invoice.png" title="Invoice"></a>

<a href="#" onClick="MM_openBrWindow('comp_interp_credit_note.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><input type="image" src="images/icn_settings.png".png" title="Make Credit Note"></a>

<?php if($row['credit_note']){ ?>
<a href="#" onClick="MM_openBrWindow('credit_interp.php?invoice_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><input type="image" src="images/icn_categories.png" title="Credit Note"></a>
                    <?php }}if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance'  || $_SESSION['prv']=='Operator'){?>
     <?php if($row['orderCancelatoin']==0){ ?>
<a href="#" onClick="MM_openBrWindow('email_cancel.php?email_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=520,height=350,left=600,top=200')"><input type="image" src="images/top_icon.png" title="Order Cancelation"></a>
  <?php }else{ ?>
<a href="#" onClick="MM_openBrWindow('email_resume.php?email_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=520,height=350,left=600,top=200')"><input type="image" src="images/icn_alert_error.png" title="Order Canceled"></a>
  
  <?php }} ?>
                    <?php if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Finance' ){?>
<a href="#" onClick="MM_openBrWindow('co_update_expanses.php?update_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><input type="image" src="images/company-icon.jpg" title="Comp Update Expanses"></a>

<a href="#" onClick="MM_openBrWindow('purch_update.php?purch_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&orgName=<?php echo $row['orgName']; ?>&porder=<?php echo $row['porder']; ?>','_blank','scrollbars=yes,resizable=yes,width=520,height=350,left=600,top=200')"><input type="image" src="images/icn_tags.png" title="Update Purchase Order #"></a>

<a href="#" onClick="MM_openBrWindow('job_cancelation.php?job_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>&orgName=<?php echo $row['orgName']; ?>','_blank','scrollbars=yes,resizable=yes,width=520,height=350,left=600,top=200')"><input type="image" src="images/icn_logout.png" title="Job Cancelation"></a>

<a href="#" onClick="MM_openBrWindow('comp_earning.php?view_id=<?php echo $row['id']; ?>','_blank','scrollbars=yes,resizable=yes,width=900,height=800,left=450,top=10')"><input type="image" src="images/earning.png" title="Earning">
</a>                   

<a href="interp_list_edited.php?view_id=<?php echo $row['id']; ?>"><input type="image" src="images/feedback.png" title="Edited List"></a>
<?php if($row['time_sheet']){ ?>
                     <a href="#" onClick="MM_openBrWindow('timesheet_view.php?t_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')"><input type="image" src="images/images.jpg" title="View Time Sheet"></a>
                     <?php } ?>

<?php $job_counter=$acttObj->read_specific('count(id) as counter','job_files','status=1 and tbl="'.$table.'" and order_id='.$row['id']);
if($job_counter['counter']>0){ ?>
                     <a href="javascript:void(0)" onClick="MM_openBrWindow('extra_file_view.php?order_id=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')" title="View Extra Files"><i class="fa fa-plus fa-2x"></i></a>
                     <?php } ?>

 <?php } ?>
 <?php
if ($_SESSION['prv']=='Management' && $row['jobDisp'] == 1) {?>
					<a href="#" onClick="MM_openBrWindow('../no_of_applicants.php?tracking=<?php echo $row['id']; ?>&table=<?php echo $table; ?>','_blank','scrollbars=yes,resizable=yes,width=1200,height=900,left=200,top=10')">
						<input type="image" src="images/aplcnts.png" title="<?php echo $acttObj->unique_data('bid','count(*)','job',$row['id']); ?> Applicants"></a>
					<?php
}?>
                    
                    </td> 
				</tr> 
                <?php } ?>
                </tbody></table>                
			<div><?php echo pagination($con,$table,$query,$limit,$page);?></div>
		  </div>
	</section>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

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
</body>
</html>