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

$search_1 = SafeVar::GetVar('search_1', '');
$search_2 = SafeVar::GetVar('search_2', '');
$search_3 = SafeVar::GetVar('search_3', '');
$multi    = SafeVar::GetVar('multi', '');


include 'db.php';

// if (empty($search_2)) {
//     $search_2 = date("Y-m-d");
// }
// if (empty($search_3)) {
//     $search_3 = date("Y-m-d");
// }
if (!empty($search_2) && empty($search_3)) {
    $search_3 = date("Y-m-d");
}
if($multi==1){
    $mult_ext_interp = "and (interpreter.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=interpreter.multInvoicNo and mult_inv.status='') OR (interpreter.multInv_flag=0 AND interpreter.invoiceNo<>'' AND  (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0) ))";
    $mult_ext_telep = "and (telephone.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=telephone.multInvoicNo and mult_inv.status='') OR (telephone.multInv_flag=0 AND telephone.invoiceNo<>'' and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) AND telephone.total_charges_comp > 0) ))";
    $mult_ext_trans = "and (translation.multInv_flag=1 AND (SELECT id from mult_inv WHERE m_inv=translation.multInvoicNo and mult_inv.status='') OR (translation.multInv_flag=0 AND translation.invoiceNo<>'' and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp > 0)))";
}else{
    $mult_ext_interp = "and ((interpreter.multInv_flag=0 AND interpreter.invoiceNo<>'' AND  (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) AND interpreter.total_charges_comp >0) ))";
    $mult_ext_telep = "and ((telephone.multInv_flag=0 AND telephone.invoiceNo<>'' and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) AND telephone.total_charges_comp > 0) ))";
    $mult_ext_trans = "and ((translation.multInv_flag=0 AND translation.invoiceNo<>'' and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) AND translation.total_charges_comp > 0)))";
}
if(!empty($search_1)){
    $arr = explode(',', $search_1);
    // $_words = implode("' OR orgName like '", $arr);
    $_words = "'".implode("','", $arr)."'";
}else{
    $_words = "";
}

$page = (int) (!isset($_GET["page"]) ? 1 : $_GET["page"]);
$limit = 50;
$startpoint = ($page * $limit) - $limit;

?>
<!doctype html>
<html lang="en">

<head>
    <title>Pending Invoices (All)</title>
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>

<?php include "incmultiselfiles.php";?>


<script type="text/javascript">
    $(function () {
        $('#search_1').multiselect({
            includeSelectAllOption: true
        });
    });



    function myFunction() {
        var x = $('#search_1').val();

        if (!x) {
            x = "<?php echo $search_1; ?>";
        }
        var y = document.getElementById("search_2").value;
        if (!y) {
            y = "<?php echo $search_2; ?>";
        }
        var z = document.getElementById("search_3").value;
        if (!z) {
            z = "<?php echo $search_3; ?>";
        }
        if(document.getElementById("multi_invoice").checked){
            var multi = '&multi=1';
        }else{
            var multi = '&multi=0';
        }
        var strLoc = '<?php echo basename(__FILE__); ?>' + '?search_1=' + x + '&search_2=' + y + '&search_3=' + z +multi;
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
                        <div class="label label-primary"><a href="<?php echo basename(__FILE__);?>">Pending Invoices
                                Report</a></div>
                    </h2>
                </center>
                <div class="col-md-10 col-md-offset-1"><br>
                    <div class="form-group col-md-2 col-sm-3">
                        <select id="search_1" name="search_1" multiple="multiple" class="form-control">

                            <?php
            //first combo options (companies)
            $sql_opt = "SELECT name,abrv FROM comp_reg where deleted_flag=0 ORDER BY name ASC";
            $result_opt = mysqli_query($con, $sql_opt);
            $options = "";
            while ($row_opt = mysqli_fetch_array($result_opt)) {
                $code = $row_opt["abrv"];
                $name_opt = $row_opt["name"];
                $options .= "<OPTION value=" . rawurlencode($code) . ">" . $name_opt . "</option>";
            }
            ?>
                            <option value="" disabled>--Select Organization--</option>
                            <?php echo $options; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2 col-sm-3">
                        <input type="date" name="search_2" id="search_2" placeholder='' class="form-control"
                            onChange="myFunction()" value="<?php echo $search_2; ?>" />
                    </div>
                    <div class="form-group col-md-2 col-sm-3">
                        <input type="date" name="search_3" id="search_3" placeholder='' class="form-control"
                            onChange="myFunction()" value="<?php echo $search_3; ?>" />
                    </div>
                    <div class="form-group col-md-2 col-sm-3">
                    <label title="Include Multi Invoice Single Entries in the Report" class="text-danger" >
                        <input <?=(isset($multi) && $multi!=0 && $multi!='')?'checked':''?> type="checkbox" id="multi_invoice" name="multi_invoice"> Include Multi Invoice
                    </label>
                    </div>
                    <div class="form-group col-md-1 col-sm-4">
                        <a href="javascript:void(0)" title="Click to Get Report" onclick="myFunction()"><span
                                class="btn btn-sm btn-primary">Get Report</span></a>
                    </div>
                    <div class="form-group col-md-2 col-sm-3">
                        <a href="reports_lsuk/excel/<?php echo basename(__FILE__); ?>?search_1=<?php echo $search_1; ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&multi=<?php echo $multi; ?>"
                            title="Download Excel Report"><span class="btn btn-sm btn-success">Export To Excel</span></a>
                    </div>
                </div>  
            </header>

            <div class="tab_container">
                <div id="tab1" class="tab_content" align="center">
                    <?php
                    // $query = "SELECT interpreter_reg.name,'Interpreter' as tble FROM
                    //     interpreter,interpreter_reg where interpreter.intrpName = interpreter_reg.id and interpreter.commit=1 and interpreter.deleted_flag = 0 
                    //     and interpreter.order_cancel_flag=0  
                    //     and (round(interpreter.rAmount,2) < round((interpreter.total_charges_comp+(interpreter.total_charges_comp*interpreter.cur_vat)),2) or interpreter.total_charges_comp =0) 
                    //     and (interpreter.orgName IN ($_words)) and interpreter.assignDate between '$search_2' and '$search_3'
                    //     union all
                    //     SELECT interpreter_reg.name ,'Telephone' as tble FROM 
                    //     telephone,interpreter_reg where telephone.intrpName = interpreter_reg.id and telephone.commit=1 and telephone.deleted_flag = 0 
                    //     and telephone.order_cancel_flag=0 
                    //     and (round(telephone.rAmount,2) < round((telephone.total_charges_comp+(telephone.total_charges_comp*telephone.cur_vat)),2) or telephone.total_charges_comp =0) 
                    //     and (interpreter.orgName IN ($_words)) and telephone.assignDate between '$search_2' and '$search_3'
                    //     union all
                    //     SELECT interpreter_reg.name ,'Translation' as tble FROM 
                    //     translation,interpreter_reg where translation.intrpName = interpreter_reg.id and translation.deleted_flag = 0 
                    //     and translation.order_cancel_flag=0
                    //     and (round(translation.rAmount,2) < round((translation.total_charges_comp+(translation.total_charges_comp*translation.cur_vat)),2) or translation.total_charges_comp =0) 
                    //     and (telephone.orgName IN ($_words)) and translation.asignDate between '$search_2' and '$search_3' LIMIT {$limit}";
                    $query="SELECT interpreter_reg.name,'Interpreter' as tble FROM
                    interpreter,interpreter_reg where interpreter.intrpName = interpreter_reg.id $mult_ext_interp and interpreter.deleted_flag = 0 
                    and interpreter.order_cancel_flag=0 ".(!empty($_words)?" and (interpreter.orgName IN ($_words)) ":"")." ".(!empty($search_2)?"and interpreter.assignDate between '$search_2' and '$search_3'":"")."
                    union all
                    SELECT interpreter_reg.name ,'Telephone' as tble FROM 
                    telephone,interpreter_reg where telephone.intrpName = interpreter_reg.id $mult_ext_telep and telephone.deleted_flag = 0 
                    and telephone.order_cancel_flag=0 
                    ".(!empty($_words)?" and (telephone.orgName IN ($_words)) ":"")."
                    ".(!empty($search_2)?"and telephone.assignDate between '$search_2' and '$search_3'":"")."
                    union all
                    SELECT interpreter_reg.name ,'Translation' as tble FROM 
                    translation,interpreter_reg where translation.intrpName = interpreter_reg.id $mult_ext_trans and translation.deleted_flag = 0 
                    and translation.order_cancel_flag=0
                    ".(!empty($_words)?" and (translation.orgName IN ($_words)) ":"")."
                    ".(!empty($search_2)?"and translation.asignDate between '$search_2' and '$search_3'":"")."
                     LIMIT {$limit}";
                    ?>
                    <div class="col-md-12"><?php echo pagination($con, $table, $query, $limit, $page); ?></div>
                    <iframe id="myFrame" class="col-xs-10 col-xs-offset-1" height="1000px"
                        src="reports_lsuk/pdf/<?php echo basename(__FILE__); ?>?search_1=<?php echo rawurlencode($search_1); ?>&search_2=<?php echo $search_2; ?>&search_3=<?php echo $search_3; ?>&multi=<?php echo $multi; ?>&startpoint=<?php echo $startpoint ?>&page=<?php echo $page ?>&limit=<?php echo $limit ?> "
                        ;></iframe>
                </div><!-- end of #tab1 -->

            </div><!-- end of .tab_container -->

            </article><!-- end of content manager article -->
            <!-- end of messages article -->

            <div class="clear"></div>
    </section>
</body>


</html>