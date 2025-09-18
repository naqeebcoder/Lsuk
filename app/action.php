<?php define("SERVER", "localhost");
define("USERNAME", "ufkjpzdso5twq");
define("PASSWORD", "@S0fT3chM41n");
define("DATABASE", "db5lngybwijlq8");
define("URL", "https://lsuk.org");
define("FROM_NAME","LSUK");
define("FROM_ADD",'info@lsuk.org');
error_reporting(0);
date_default_timezone_set('Europe/London');
class actionClass{
	public $con;
	function __construct(){
		$this->con = new mysqli(SERVER,USERNAME,PASSWORD,DATABASE);
		if ($this->con->connect_errno) {
		  echo "Failed to connect to MySQL: " . $this->con->connect_error;
		  exit();
		}
	}
    public function delete($table, $where){
        $query = "DELETE FROM $table WHERE $where";
        if (!$this->con->query($query)) {
			return 0;
		}else{return 1;}
    }
	public function read_all($col,$table,$where){
		$sql= "SELECT ".$col." FROM ". $table;
		if($where != null){
			$sql .= ' WHERE '.$where;
		}
		$result = $this->con->query($sql);
        return $result;
	}
	public function read_all_c($col,$table,$where){
		$sql= "SELECT ".$col." FROM ". $table;
		if($where != null){
			$sql .= ' WHERE '.$where;
		}
		print_r($sql);
	}
	public function read_specific($col,$table,$where){
		$sql= "SELECT ".$col." FROM ". $table;
		if($where != null){
			$sql .= ' WHERE '.$where;
		}
		$result = $this->con->query($sql);
		$row = $result->fetch_assoc();
		return $row;
	}
	public function read_specific_c($col,$table,$where){
		$sql= "SELECT ".$col." FROM ". $table;
		if($where != null){
			$sql .= ' WHERE '.$where;
		}
		print_r($sql);
	}
	public function insert($table, $data){
		$key = array_keys($data);
		$val = array_values($data);
		$sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
			. "VALUES ('" . implode("', '", $val) . "')"; 
		$result = $this->con->query($sql);
		return $result;
	}
	public function insert_c($table, $data){
		$key = array_keys($data);
		$val = array_values($data);
		$sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
			. "VALUES ('" . implode("', '", $val) . "')"; 
		print_r($sql);
	}
	function update($table,$array,$where){
        $data="";
		foreach ($array as $key => $value) {
			$data.= "$key='".$this->con->real_escape_string($value)."',";
		}
		$data=rtrim($data,",");
		$query="UPDATE ".$table." SET ".$data;
		if(!empty($where)){
			$query.=" WHERE ".$where;
		}
		return $this->con->query($query);
	}
	
	function update_c($table,$array,$where){
        $data="";
		foreach ($array as $key => $value) {
			$data.= "$key='".$this->con->real_escape_string($value)."',";
		}
		$data=rtrim($data,",");
		$query="UPDATE ".$table." SET ".$data;
		if(!empty($where)){
			$query.=" WHERE ".$where;
		}
		print_r($query);
	}
    public function get_id($table){
        $dated = date("Y-m-d");
        $query = "INSERT INTO $table (id,dated) VALUES (NULL,'$dated')";
        if (!$this->con->query($query)) {
            return die('Error: ' . $this->con->connect_error);
        }
        $query = "SELECT MAX(id) AS lastId FROM $table";
        $result = $this->con->query($query);
        while ($row = $result->fetch_assoc()) {
            return $lastId = $row['lastId'];
        }
    }
    public function editFun($table, $edit_id, $col, $data){
        $escape_date=$this->con->real_escape_string($data);
        $query = "update $table set $col='$escape_date' where id=$edit_id";
        if(!$this->con->query($query)){
            die('Error: ' . htmlspecialchars($this->con->connect_error));
        }else{
            return "success";
        }
    }
    public function unique_data($table, $req, $col, $data){
        $query = "SELECT $req AS val FROM $table where $col='$data'";
        $result = $this->con->query($query);
        while ($row = $result->fetch_assoc()){
            return $row['val'];
        }
    }
    public function unique_dataAnd($table,$req,$col,$data,$col2,$data2){
		$query="SELECT $req AS val FROM $table where $col='$data' and $col2='$data2'";			
		$result = $this->con->query($query);
		while($row =$result->fetch_assoc()){
			return $row['val'];
		}
	}
	// public function notify($token, $title, $body, $extra_data){  
	// 	include '../lsuk_system/token_api.php';
	// 	$ch = curl_init("https://fcm.googleapis.com/v1/projects/lsuk-1530684014975/messages:send");
	// 	$header = array(
	// 		'Content-Type: application/json',
	// 		"Authorization: Bearer $accessToken"
	// 	);
	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// 	curl_setopt($ch, CURLOPT_POST, 1);
	// 	$rand_id = round(microtime(true));
	// 	$data_array = array("click_action" => "FLUTTER_NOTIFICATION_CLICK", "status" => "done", "rand_id" => $rand_id);
	// 	if ($extra_data) {
	// 		$data_array = array_merge($data_array, $extra_data);
	// 	}
	// 	$full_data = [
	// 		"message" => [
	// 			"token" => $token,
	// 			"notification" => [
	// 				"title" => $title,
	// 				"body" => $body
	// 			]
	// 		]
	// 	];
	// 	$full_data = json_encode($full_data);
	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 	curl_exec($ch);
	// 	curl_close($ch);
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
	// 		"notification"=>array("title"=>$title,"body"=>$body,"hashCode"=>123456),
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



	public function notification($token,$title,$text,$full_data){
		$ch = curl_init("https://fcm.googleapis.com/fcm/send");
		$header=array('Content-Type: application/json',
            "Authorization: key=AAAADpufTQE:APA91bHCXUrX_WLqzqqOVoViBKWMNtXFTOf_3dExAhBNkyaXFcNDtKIfi2F-vqXiRfJBKzk52oUfZKI-ZJRqR9QCH7wQ8ppSJkhptPChZH-qyZ3Lwu82ORR86-7G_b_qtZj56WXuZQdw");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_POST, 1);
		$rand_id=round(microtime(true));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		curl_close($ch);
	}
	public function driver_notification($token,$title,$text,$full_data){
		$ch = curl_init("https://fcm.googleapis.com/fcm/send");
		$header=array('Content-Type: application/json',
            "Authorization: key=AAAADpufTQE:APA91bHCXUrX_WLqzqqOVoViBKWMNtXFTOf_3dExAhBNkyaXFcNDtKIfi2F-vqXiRfJBKzk52oUfZKI-ZJRqR9QCH7wQ8ppSJkhptPChZH-qyZ3Lwu82ORR86-7G_b_qtZj56WXuZQdw");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_POST, 1);
		$rand_id=round(microtime(true));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		curl_close($ch);
	}
    
    // public function send_sms($number,$message){
    //     $to = str_replace("+92","0",$number);
    //     $username = "sabzishop";
    //     $password = "987654";
    //     $sender = "ALERTS";
    //     $url="http://161.97.70.24/smswebpk/api/send?username=".$username."&password=".$password."&mask=".$sender."&mobile=".urlencode($to)."&message=".urlencode($message."\r\nRegards,\r\nSabzi Shop")."&language=E";
	// 	$ch = curl_init($url);
    //     curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	// 	curl_setopt($ch, CURLOPT_POST, 1);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 	$response=curl_exec($ch);
	// 	curl_close($ch);
    //     return $response;
    // }
	public function upload_file($folder, $name, $type, $tmp, $picName)
    {
        $allowedExts = array("gif", "jpeg", "jpg","png","pdf", "doc", "docx","rtf","odt","txt","tiff","bmp","xls","xlsx");
        $temp = explode(".", $name);
        $extension = strtolower(end($temp));
        if (in_array($extension, $allowedExts)) {
            if (file_exists("file_folder/" . $folder . "/" . $name)) {
                echo ('Picture already existed!');
            } else {
                $extension = end(explode(".", $name));
                move_uploaded_file($tmp, "../lsuk_system/file_folder/" . $folder . "/" . $name);
                $PicPath = "file_folder/" . $folder . "/" . $tmp;
                rename("../lsuk_system/file_folder/" . $folder . "/" . $name, "../lsuk_system/file_folder/" . $folder . "/" . $picName . '.' . $extension);
                return $Pix = $picName . '.' . $extension;
            }
        }
    }
	function __destruct()
	{
		if ($this->con) {
			$this->con->close();
		}
	}
}
$obj = new actionClass;

class miscClass{
    public function IsDatedNull($val){
        if ($val == '1001-01-01' || $val == "30-11--0001") {
            return true;
        }
        return false;
    }
    public function dated($val){
        if ($val == '1001-01-01' || $val == "30-11--0001") {
            return 'Not yet fixed!';
        }else{
            return $dated = date_format(date_create($val), 'd-m-Y');
        }
    }
    public function sys_date(){
        return $dated = date_format(date_create(date("Y-m-d")), 'd-m-Y');
    }
    public function sys_date_db(){
        return $dated = date("Y-m-d");
    }
    public function sys_datetime_db(){
        return $dated = date("Y-m-d H:i:s");
    }
    public function add_in_date($dat, $dys){
        return date('Y-m-d', strtotime($dat . $dys . ' days'));
    }
    public function round_fun($val){
        return round($val, 2);
    }
    public function numberFormat_fun($val){
        return number_format($val, 2);
    }
    function round_quarter($num,$parts) {
        $res = $num * $parts;
        $res = ceil($res);
        return $res /$parts;
    }
}
$misc = new miscClass;