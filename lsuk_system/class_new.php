<?php date_default_timezone_set('Europe/London');
class class_new{
    //READ all columns method goes here...
	public function read_all($col,$table,$where) {
	    include 'db.php';
		$sql= "SELECT ".$col." FROM ". $table;
		if($where != null){
			$sql .= ' WHERE '.$where;
		}
		$result = mysqli_query($con, $sql);
        return $result;
	}
	//READ specific columns method goes here...
	public function read_specific($col,$table,$where) {
	    include 'db.php';
		$sql= "SELECT ".$col." FROM ". $table;
		if($where != null){
			$sql .= ' WHERE '.$where;
		}
		$result = mysqli_query($con, $sql);
		$row = mysqli_fetch_assoc($result);
        return $row;
	}
	//INSERT RECORD method goes here...
	public function insert($table, $data) {
    include 'db.php';
    $key = array_keys($data);
    $val = array_values($data);
    $sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
    . "VALUES ('" . implode("', '", $val) . "')"; 
    $result = mysqli_query($con, $sql);
    return $result;
	}
	//UPDATE RECORD method goes here...
    public function update($table, $data, $parameters) {
        include 'db.php';
        $cols = array();$cols2 = array();
        foreach($data as $key=>$val) {
            $cols[] = "$key = '$val'";
        }
        foreach($parameters as $key2=>$val2) {
            $cols2[] = "$key2 = '$val2'";
        }
        if(count($parameters)>1){
            $sql= "UPDATE $table SET " . implode(', ', $cols) ." WHERE " . implode(' and ', $cols2) ;
        }else{
            $sql= "UPDATE $table SET " . implode(', ', $cols) ." WHERE " . $key2.'='.$val2;
        }
        $result = mysqli_query($con, $sql);
        return $result;
    }
	public function update_custom($table, $data, $where) {
        include 'db.php';
        $cols = array();
        foreach($data as $key=>$val) {
            $cols[] = "$key = '$val'";
        }
        if(!empty($where)){
            $sql= "UPDATE $table SET " . implode(', ', $cols) ." WHERE " . $where;
        }else{
            $sql= "UPDATE $table SET " . implode(', ', $cols) ." WHERE 1";
        }
        $result = mysqli_query($con, $sql);
        return $result;
	}
    public function delete($table, $where){
        include 'db.php';
        $query = "DELETE FROM $table WHERE $where";
        if (!mysqli_query($con, $query)) {return 0;}else{return 1;}
    }
    public function insert_array($table, $data) {
        include 'db.php';
        $key = array_keys($data);
        $val = array_values($data);
        $sql = "INSERT INTO $table (" . implode(', ', $key) . ") " . "VALUES ('" . implode("', '", $val) . "')"; 
        if (!mysqli_query($con, $sql)) {return 0;}else{return 1;}
    }
    public function notification($token,$title,$text,$full_data){
        $ch = curl_init("https://fcm.googleapis.com/fcm/send");
        $header=array('Content-Type: application/json',
                    "Authorization: key=AAAADpufTQE:APA91bHCXUrX_WLqzqqOVoViBKWMNtXFTOf_3dExAhBNkyaXFcNDtKIfi2F-vqXiRfJBKzk52oUfZKI-ZJRqR9QCH7wQ8ppSJkhptPChZH-qyZ3Lwu82ORR86-7G_b_qtZj56WXuZQdw");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
    }
    //     public function notify($token, $title, $body, $extra_data)
    // {   
    //     include 'token_api.php';
    //     $ch = curl_init("https://fcm.googleapis.com/v1/projects/lsuk-1530684014975/messages:send");
    //     $header = array(
    //         'Content-Type: application/json',
    //         "Authorization: Bearer $accessToken"
    //     );
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     $rand_id = round(microtime(true));
    //     $data_array = array("click_action" => "FLUTTER_NOTIFICATION_CLICK", "status" => "done", "rand_id" => $rand_id);
    //     if ($extra_data) {
    //         $data_array = array_merge($data_array, $extra_data);
    //     }
    //     $data_array = json_encode($data_array);
    //     $full_data = [
    //         "message" => [
    //             "token" => $token,
    //             "notification" => [
    //                 "title" => $title,
    //                 "body" => $body
    //             ],
	// 			'data' => [
    //                 "customData" => $data_array
    //             ]
    //         ]
    //     ];
    //     $full_data = json_encode($full_data);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_exec($ch);
    //     curl_close($ch);
    // }
	// public function notify($token,$title,$body,$extra_data){
	// 	$ch = curl_init("https://fcm.googleapis.com/fcm/send");
	// 	$header=array('Content-Type: application/json',
    //     "Authorization: key=AAAADpufTQE:APA91bHCXUrX_WLqzqqOVoViBKWMNtXFTOf_3dExAhBNkyaXFcNDtKIfi2F-vqXiRfJBKzk52oUfZKI-ZJRqR9QCH7wQ8ppSJkhptPChZH-qyZ3Lwu82ORR86-7G_b_qtZj56WXuZQdw");
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	// 	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	// 	curl_setopt($ch, CURLOPT_POST, 1);
	// 	$rand_id=round(microtime(true));
	// 	$data_array=array("click_action"=>"FLUTTER_NOTIFICATION_CLICK","status"=>"done","rand_id"=>$rand_id);
	// 	if($extra_data){
	// 		$data_array=array_merge($data_array,$extra_data);
	// 	}
	// 	$full_data = json_encode(array(
	// 		"to" => $token,
	// 		"notification"=>array("title"=>$title,"body"=>$body),
	// 		"data"=>$data_array,
	// 	));
	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 	curl_exec($ch);
	// 	curl_close($ch);
	// }
    public function notify($token, $title, $body, $extra_data) {
        $cacheUrl = 'https://lsuk.org/lsuk_system/access_token_cache.json';
        $accessToken = null;

        // Step 1: Try reading existing token
        $cached = json_decode(file_get_contents($cacheUrl), true);
        if ($cached && isset($cached['access_token'], $cached['expires_at']) && $cached['expires_at'] > time()) {
            $accessToken = $cached['access_token'];
        }

        // Step 2: Regenerate if expired/missing
        if (!$accessToken) {
            // Trigger token refresh
            file_get_contents('https://lsuk.org/lsuk_system/token_api.php');

            // Re-fetch the updated token
            $cached = json_decode(file_get_contents($cacheUrl), true);
            $accessToken = $cached['access_token'] ?? null;

            if (!$accessToken) {
                return ['status' => 'error', 'message' => 'Unable to fetch access token'];
            }
        }

        // Step 3: Send FCM notification
        $ch = curl_init("https://fcm.googleapis.com/v1/projects/lsuk-1530684014975/messages:send");
        $header = [
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $rand_id = round(microtime(true));
        $data_array = [
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "status" => "done",
            "rand_id" => $rand_id
        ];
        if ($extra_data) {
            $data_array = array_merge($data_array, $extra_data);
        }
		foreach ($data_array as $key => $value) {
			$data_array[$key] = (string)$value;
		}
        $payload = json_encode([
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $title,
                    "body" => $body
                ],
        	"data" => $data_array
            ]
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        // if ($httpCode === 200) {
        //     return ['status' => 'success', 'response' => json_decode($response, true)];
        // } else {
        //     print_r( ['status' => 'error', 'http_code' => $httpCode, 'error' => $error, 'response' => $response]);
        // }
    }
    public function date_dmy($val){
        if ($val == '1001-01-01' || $val == "0000-00-00") {
            return 'Not yet fixed!';
        } else {
            return $dated = date_format(date_create($val), 'd-m-Y');
        }
    }
}
$obj_new = new class_new;
?>