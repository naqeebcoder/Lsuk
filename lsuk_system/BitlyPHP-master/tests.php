<?php
require_once('bitly.php');

$client_id = 'f0a0a069e777be80d2c338aad4aa6e78986c396b';
$client_secret = '0a6a38d493a4eb92902c8e15bb6cea24d55bdb4c';
$user_access_token = '72a6b27e8849c8ad2590f06ce854a3c99a242b58';
$user_login = 'waqarecp';
$user_api_key = 'R_2ebf36a54e874943ba5c8f830e40fa93'; 

$params = array();
$params['access_token'] = $user_access_token;
$params['longUrl'] = 'https://lsuk.org/jobs2.php?val=interpreter&tracking=11166';
$params['domain'] = 'j.mp';
$results = bitly_get('shorten', $params);
$bit_link=substr($results['data']['url'],7);

//click send test
require_once('../clicksend/vendor/autoload.php');

// Configure HTTP basic authorization: BasicAuth
$config = ClickSend\Configuration::getDefaultConfiguration()
              ->setUsername('imran@lsuk.org')
              ->setPassword('BAFB1B12-89D9-8D9A-1BE5-9FF2165105E9');
$message="Alert!
Dear Interpreter,
LSUK has a related job for you.
Visit below link to bid.
".$bit_link."
First come first serve basis.
GOOD LUCK LSUK
Admin Team";
echo $message;
$apiInstance = new ClickSend\Api\SMSApi(new GuzzleHttp\Client(),$config);
$msg = new \ClickSend\Model\SmsMessage();
$msg->setBody($message); 
$msg->setTo("+923015698197");
$msg->setSource("sdk");

// \ClickSend\Model\SmsMessageCollection | SmsMessageCollection model
$sms_messages = new \ClickSend\Model\SmsMessageCollection(); 
$sms_messages->setMessages([$msg]);

try {
    $result = $apiInstance->smsSendPost($sms_messages);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SMSApi->smsSendPost: ', $e->getMessage(), PHP_EOL;
}

?>