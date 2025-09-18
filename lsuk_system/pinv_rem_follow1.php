<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    require 'phpmailer/vendor/autoload.php';
    $mail = new PHPMailer(true);
    include 'db.php';
    include 'class_new.php';
    function callCURL($url) {
        // echo "called";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $combined = curl_exec ($ch);
        curl_close ($ch);
        return $combined;
    }
    $get_pinv = $obj_new->read_all("id,name,invEmail,abrv","comp_reg"," comp_nature IN (1,4) AND deleted_flag=0");

    $r_pinv = mysqli_num_rows($get_pinv);
    if($r_pinv>0){
        while($row = mysqli_fetch_assoc($get_pinv)){
            $url = 'https://lsuk.org/lsuk_system/reports_lsuk/excel/pinv_gen.php?p_org='.$row['id'];
            $gen_files = callCURL($url);
        }
}

    ?>
    