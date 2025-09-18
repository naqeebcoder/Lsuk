<?php
function callTMS($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $combined = curl_exec ($ch);
    curl_close ($ch);
    return $combined;
}
$rodtype=1;
$odtypes = array("interpreter","telephone","translation");
$table=$odtypes[$rodtype-1];
$tms = "https://lsuk.org/lsuk_system/reports_lsuk/pdf/new_timesheet.php?update_id=11166&table=$table&down&emailto=fahadsoftech47@gmail.com&send_sms=1&cron_tms=1";
echo $tms;
$gen_tm = callTMS($tms);
echo $gen_tm;