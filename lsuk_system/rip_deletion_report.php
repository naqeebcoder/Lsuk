<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php if (session_id() == '' || !isset($_SESSION)) {session_start();}?>

<?php
include 'class.php';
include 'function.php';

//$search_1=@$_GET['search_1'];
//$search_2=@$_GET['search_2'];
//$search_3=@$_GET['search_3'];

$search_2 = SafeVar::GetVar('search_2', '');
$search_3 = SafeVar::GetVar('search_3', '');


include 'db.php';

if (empty($search_2)) {
    $search_2 = date("Y-m-d");
}
if (empty($search_3)) {
    $search_3 = date("Y-m-d");
}

$arr = explode(',', $search_1);

$_words = implode("' OR orgName like '", $arr);

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;

?>
<!doctype html>
<html lang="en">

<head>
    <title>Deletion Report </title>
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>

<?php include "incmultiselfiles.php";?>


<script type="text/javascript">
    // $(function () {
    //     $('#search_1').multiselect({
    //         includeSelectAllOption: true
    //     });
    // });



    function myFunction() {
        
        var y = document.getElementById("search_2").value;
        if (!y) {
            y = "<?php echo $search_2; ?>";
        }
        var z = document.getElementById("search_3").value;
        if (!z) {
            z = "<?php echo $search_3; ?>";
        }
        
        var strLoc = '<?php echo basename(__FILE__); ?>' + '?search_2=' + y + '&search_3=' + z;
        window.location.assign(strLoc);
    }

    // window.addEventListener('click', function(e) {
    //     if ($('#search_1').val() != null) {
    //         if (document.getElementById('search_1').contains(e.target)) {
    //             console.log('inside');
    //         } else {
    //             myFunction();
    //         }
    //     }
    // });
</script>
<!--................................//\\//\\//\\//\\//\\........................................................................................-->

<body>
    <?php include 'nav2.php';?>
    <!-- end of sidebar -->
    <style>
        .tablesorter thead tr {
            background: none;
        }
    </style>
    <section class="container-fluid" style="overflow-x:auto">
        <div class="col-md-12">
            <header>
                <center>
                    <h2 class="col-md-4 col-md-offset-4 text-center">
                        <div class="label label-primary"><a href="<?php echo basename(__FILE__);?>">Deleted Jobs Report (Home Screen)</a></div>
                    </h2>
                </center>
                <div class="col-md-10 col-md-offset-1"><br>
                    <div class="form-group col-md-2 col-sm-3">
                        <input type="date" name="search_2" id="search_2" placeholder='' class="form-control"
                            onChange="myFunction()" value="<?php echo $search_2; ?>" />
                    </div>
                    <div class="form-group col-md-2 col-sm-3">
                        <input type="date" name="search_3" id="search_3" placeholder='' class="form-control"
                            onChange="myFunction()" value="<?php echo $search_3; ?>" />
                    </div>
                    <div class="form-group col-md-1 col-sm-4">
                        <a href="javascript:void(0)" title="Click to Get Report" onclick="myFunction()"><span
                                class="btn btn-sm btn-primary">Get Report</span></a>
                    </div>
                    <!-- <div class="form-group col-md-2 col-sm-3">
                        <a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&multi=<?php echo $multi; ?>"
                            title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel</span></a>
                    </div> -->
                </div>  
            </header>

            <div class="tab_container">
                <div id="tab1" class="tab_content" align="center">
                    <?php
                    // $query = "SELECT interpreter_reg.name,'Interpreter' as tble FROM
                    //     interpreter,interpreter_reg where interpreter.intrpName = interpreter_reg.id and interpreter.commit=1 and interpreter.deleted_flag = 0 
                    //     and interpreter.order_cancel_flag=0  
                    //     and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) 
                    //     and (interpreter.orgName like '%$_words%') and interpreter.assignDate between '$search_2' and '$search_3'
                    //     union all
                    //     SELECT interpreter_reg.name ,'Telephone' as tble FROM 
                    //     telephone,interpreter_reg where telephone.intrpName = interpreter_reg.id and telephone.commit=1 and telephone.deleted_flag = 0 
                    //     and telephone.order_cancel_flag=0 
                    //     and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) 
                    //     and (telephone.orgName like '%$_words%') and telephone.assignDate between '$search_2' and '$search_3'
                    //     union all
                    //     SELECT interpreter_reg.name ,'Translation' as tble FROM 
                    //     translation,interpreter_reg where translation.intrpName = interpreter_reg.id and translation.deleted_flag = 0 
                    //     and translation.order_cancel_flag=0
                    //     and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) 
                    //     and (translation.orgName like '%$_words%') and translation.asignDate between '$search_2' and '$search_3' LIMIT {$limit}";
                    $query="SELECT interpreter.id,interpreter.nameRef,interpreter.orgName,interpreter.orgRef,interpreter.invoiceNo,interpreter.assignDate,interpreter.source,'F2F' as job_type FROM interpreter WHERE interpreter.assignDate between '$search_2' and '$search_3' AND interpreter.is_shifted = 0 and interpreter.deleted_flag = 1 AND interpreter.intrpName=''
                    UNION ALL
                    SELECT telephone.id,telephone.nameRef,telephone.orgName,telephone.orgRef,telephone.invoiceNo,telephone.assignDate,telephone.source,'Telephone' as job_type FROM telephone WHERE telephone.assignDate between '$search_2' and '$search_3' AND telephone.is_shifted = 0 and telephone.deleted_flag = 1 AND telephone.intrpName=''
                    UNION ALL 
                    SELECT translation.id,translation.nameRef,translation.orgName,translation.orgRef,translation.invoiceNo,translation.asignDate as assignDate,translation.source,'Translation' as job_type FROM translation WHERE translation.asignDate between '$search_2' and '$search_3' AND translation.deleted_flag = 1 AND translation.intrpName='' LIMIT {$limit}";
                    ?>
                    <div class="col-md-12"><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
                    <iframe id="myFrame" class="col-xs-10 col-xs-offset-1" height="1000px"
                        src="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&startpoint=<?php echo $startpoint ?>&page=<?php echo $page ?>&limit=<?php echo $limit ?> "
                        ;></iframe>
                </div><!-- end of #tab1 -->

            </div><!-- end of .tab_container -->

            </article><!-- end of content manager article -->
            <!-- end of messages article -->

            <div class="clear"></div>
    </section>
</body>


</html>