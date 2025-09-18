<?PHP
include 'db.php';
include 'class.php';
$query = mysqli_query($con,"SELECT DISTINCT comp FROM expence WHERE comp!='' AND comp!='N/A'");
while($row = mysqli_fetch_assoc($query)){
    $comp = mysqli_real_escape_string($con,$row['comp']) ;
    $exp_vat_no = mysqli_fetch_assoc(mysqli_query($con,"SELECT exp_vat_no FROM expence WHERE comp='".$row['comp']."' and exp_vat_no!='' and exp_vat_no!='N/A' and exp_vat_no!='NA' and exp_vat_no!='NP'"))['exp_vat_no']; 
    if($exp_vat_no!=''){
        echo $comp."<br>$exp_vat_no<br>";
            $q2 = mysqli_query($con, "INSERT INTO sup_reg(sp_name,tax_reg,uk_citizen_vatNum) VALUES('{$comp}',1,'{$exp_vat_no}')");

    }else{
        echo $comp."<br>|Empty|<br>";
            $q2 = mysqli_query($con, "INSERT INTO sup_reg(sp_name) VALUES('{$comp}')");

    }

}