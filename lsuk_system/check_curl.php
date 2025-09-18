<?php
function callTMS($url) {
    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, $url);
    // // curl_setopt($ch, CURLOPT_HTTPGET, true);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // $combined = curl_exec ($ch);
    // print_r($combined);
    // curl_close ($ch);
    // return $combined;
    // return file_get_contents($url);
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: PHP\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    return $response;
}
// echo "Hello World";
$tms = "https://lsuk.org/lsuk_system/test_curl.php";
$gen_tm = callTMS($tms);
echo $gen_tm;
// print_r($gen_tm);
// phpinfo();
