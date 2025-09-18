<title>Testing dates</title>
<?php
set_time_limit(300);
include'db.php'; 
include'class.php'; 
date_default_timezone_set('Europe/London'); ?>

<form class="sky-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <input name="interp" id="interp" type="text"/>
    <input type="submit" name="submit" class="button" value="Submit"/>
    </form>
    <script>document.getElementById('interp').focus();</script>
<?php if(isset($_POST['submit'])){
    $interp=$_POST['interp'];
    $dated_salary_query=$acttObj->read_all("*","interp_salary", "interp='$interp'");
    while($dated_salary_q=mysqli_fetch_assoc($dated_salary_query)){
        $invoice=$dated_salary_q['invoice'];
        $frm=$dated_salary_q['frm'];
        $todate=$dated_salary_q['todate'];
        $dated=$dated_salary_q['dated'];
        $query_jobs="SELECT interpreter.id FROM interpreter,invoice where interpreter.invoiceNo=invoice.invoiceNo AND interpreter.deleted_flag = 0 and 
        interpreter.order_cancel_flag=0 and interpreter.intrpName=$interp and interpreter.intrp_salary_comit = 1 and interpreter.assignDate 
        BETWEEN(select frm from interp_salary WHERE invoice='$invoice')AND(select todate from interp_salary WHERE invoice='$invoice')
        UNION ALL
        SELECT telephone.id FROM telephone ,invoice where telephone.invoiceNo=invoice.invoiceNo AND telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and 
        telephone.intrpName=$interp and telephone.intrp_salary_comit = 1 and telephone.assignDate BETWEEN(select frm from interp_salary 
        WHERE invoice='$invoice')AND(select todate from interp_salary WHERE invoice='$invoice')
        UNION ALL
        SELECT translation.id FROM translation,invoice WHERE translation.invoiceNo=invoice.invoiceNo AND translation.deleted_flag = 0 and translation.order_cancel_flag=0 and 
        translation.intrpName=$interp and translation.intrp_salary_comit = 1 and translation.asignDate BETWEEN(select frm from interp_salary 
        WHERE invoice='$invoice')AND(select todate from interp_salary WHERE invoice='$invoice')";
        $result_jobs=mysqli_query($con,$query_jobs);
        while($row=mysqli_fetch_assoc($result_jobs)){
            $upd_query1="update interpreter set interpreter.paid_date='$dated' WHERE interpreter.deleted_flag = 0 and interpreter.order_cancel_flag=0 and interpreter.intrpName=$interp 
            and interpreter.intrp_salary_comit = 1 and interpreter.assignDate BETWEEN('$frm')AND('$todate')";
            mysqli_query($con,$upd_query1);
            $upd_query2="update telephone set telephone.paid_date='$dated' WHERE telephone.deleted_flag = 0 and telephone.order_cancel_flag=0 and telephone.intrpName=$interp 
            and telephone.intrp_salary_comit = 1 and telephone.assignDate BETWEEN('$frm')AND('$todate')";
            mysqli_query($con,$upd_query2);
            $upd_query3="update translation set translation.paid_date='$dated' WHERE translation.deleted_flag = 0 and translation.order_cancel_flag=0 and translation.intrpName=$interp and 
            translation.intrp_salary_comit = 1 and translation.asignDate BETWEEN('$frm')AND('$todate')";
            mysqli_query($con,$upd_query3);
        }
    }
} ?>