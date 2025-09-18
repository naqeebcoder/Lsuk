<?php
class setupSMS{
	const API_KEY = 'bb431aad';
	const API_SECRET = 'ZpugnA5uMnxIx5Xv';
	const SENDER_MASK = 'LSUK';
	const IS_ALLOWED = 1;
	const VONAGE_URL = 'https://rest.nexmo.com/sms/json';

    public function format_phone($number, $country = "United Kingdom", $format_number = 1){
		if ($format_number == 1) {
			if ($country == "United Kingdom") {
				$number = "44" . str_replace(array("-", "+", " "), "", ltrim(ltrim(trim($number),"0"), "44"));
			}
		}
		return $number;
	}
    
	public function send_sms($number, $message_body){
		if (self::IS_ALLOWED == 1) {
			$api_key = self::API_KEY;
			$api_secret = self::API_SECRET;
			$sender_mask = self::SENDER_MASK;
			try {
				$url = self::VONAGE_URL;
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$headers = array("Content-Type: application/x-www-form-urlencoded");
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				$posting_data = "from=" . urlencode($sender_mask) . "&text=" . urlencode($message_body) . "&to=" . urlencode($number) . "&api_key=" . $api_key . "&api_secret=" . $api_secret;
				curl_setopt($curl, CURLOPT_POSTFIELDS, $posting_data);
	
				//for debug only
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	
				$curl_respone = curl_exec($curl);
				curl_close($curl);
				$curl_respone = json_decode($curl_respone);
				if ($curl_respone && $curl_respone->messages && $curl_respone->messages[0]->status==0) {
					$response['status'] = 1;
					$response['message'] = "Message has been sent successfully. Thank you";
				} else {
					$response['status'] = 0;
					$response['message'] = "SMS Failed to send due to number failure!";
				}
			} catch(Exception $e) {
				$response['status'] = 0;
				$response['message'] = "SMS Failed to send due to API failure!";
			}
		} else {
			$response['status'] = 0;
            $response['message'] = "SMS service is blocked yet! Try again later";
		}
		return $response;
    }
}