<?php
define("URL", "https://lsuk.org");
define("FROM_NAME", "LSUK");
define("FROM_ADD", 'info@lsuk.org');

error_reporting(0);
date_default_timezone_set('Europe/London');
class actionsClass
{
	const SERVER = 'localhost';
	const USERNAME = 'ufkjpzdso5twq';
	const PASSWORD = '@S0fT3chM41n';
	const DATABASE = 'db5lngybwijlq8';
	const URL = 'https://lsuk.org';
	public $con;
	function __construct()
	{
		$this->con = new mysqli(self::SERVER, self::USERNAME, self::PASSWORD, self::DATABASE);
		if ($this->con->connect_errno) {
			echo "Failed to connect to MdySQL: " . $this->con->connect_error;
			exit();
		}
	}
	public function delete($table, $where)
	{
		$query = "DELETE FROM $table WHERE $where";
		if (!$this->con->query($query)) {
			return 0;
		} else {
			return 1;
		}
	}
	public function read_all($col, $table, $where = NULL)
	{
		$sql = "SELECT " . $col . " FROM " . $table;
		if ($where != null) {
			$sql .= ' WHERE ' . $where;
		}
		$result = $this->con->query($sql);
		return $result;
	}
	public function read_all_c($col, $table, $where = NULL)
	{
		$sql = "SELECT " . $col . " FROM " . $table;
		if ($where != null) {
			$sql .= ' WHERE ' . $where;
		}
		print_r($sql);
	}
	public function read_specific($col, $table, $where = NULL)
	{
		$sql = "SELECT " . $col . " FROM " . $table;
		if ($where != null) {
			$sql .= ' WHERE ' . $where;
		}
		$result = $this->con->query($sql);
		$row = $result->fetch_assoc();
		return $row;
	}
	public function read_specific_c($col, $table, $where = NULL)
	{
		$sql = "SELECT " . $col . " FROM " . $table;
		if ($where != null) {
			$sql .= ' WHERE ' . $where;
		}
		print_r($sql);
	}
	public function insert($table, $data, $return_id = false)
	{
		$key = array_keys($data);
		$val = array_values($data);
		$sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
			. "VALUES ('" . implode("', '", $val) . "')";
		$result = $this->con->query($sql);
		if ($return_id) {
            return $this->con->insert_id;
        } else {
            return $result;
        }
		return $result;
	}
	public function insert_c($table, $data, $return_id = false)
	{
		$key = array_keys($data);
		$val = array_values($data);
		$sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
			. "VALUES ('" . implode("', '", $val) . "')";
		print_r($sql);
	}
	function update($table, $array, $where)
	{
		$data = "";
		foreach ($array as $key => $value) {
			$data .= is_null($value) ? "$key=NULL," : "$key='" . $this->con->real_escape_string($value) . "',";
		}
		$data = rtrim($data, ",");
		$query = "UPDATE " . $table . " SET " . $data;
		if (!empty($where)) {
			$query .= " WHERE " . $where;
		}
		return $this->con->query($query);
	}

	function update_c($table, $array, $where)
	{
		$data = "";
		foreach ($array as $key => $value) {
			$data .= is_null($value) ? "$key=NULL," : "$key='" . $this->con->real_escape_string($value) . "',";
		}
		$data = rtrim($data, ",");
		$query = "UPDATE " . $table . " SET " . $data;
		if (!empty($where)) {
			$query .= " WHERE " . $where;
		}
		print_r($query);
	}
	public function get_id($table)
	{
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
	public function editFun($table, $edit_id, $col, $data)
	{
		$escape_date = $this->con->real_escape_string($data);
		$query = "update $table set $col='$escape_date' where id=$edit_id";
		if (!$this->con->query($query)) {
			die('Error: ' . htmlspecialchars($this->con->connect_error));
		} else {
			return "success";
		}
	}
	public function unique_data($table, $req, $col, $data)
	{
		$query = "SELECT $req AS val FROM $table where $col='$data'";
		$result = $this->con->query($query);
		while ($row = $result->fetch_assoc()) {
			return $row['val'];
		}
	}
	public function unique_dataAnd($table, $req, $col, $data, $col2, $data2)
	{
		$query = "SELECT $req AS val FROM $table where $col='$data' and $col2='$data2'";
		$result = $this->con->query($query);
		while ($row = $result->fetch_assoc()) {
			return $row['val'];
		}
	}
	public function new_old_table($new_table, $old_table, $id)
	{
		$query = "INSERT INTO $new_table SELECT * FROM $old_table WHERE id = $id";
		return $this->con->query($query);
	}

	// public function notify($token, $title, $body, $extra_data)
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
	// 	$data_array = json_encode($data_array);
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
    //     // $full_data = json_encode(array(
    //     //     "token" => $token,
    //     //     "notification" => array("title" => $title, "body" => $body, "hashCode" => 123456),
    //     //     "data" => $data_array,
    //     // ));
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_exec($ch);
	// 	// $response = curl_exec($ch);
    //     curl_close($ch);
    //     // echo $response;
    // }

	public function notify_test($token, $title, $body, $extra_data)
    {   
        include 'token_api.php';
        $ch = curl_init("https://fcm.googleapis.com/v1/projects/lsuk-1530684014975/messages:send");
        $header = array(
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        $rand_id = round(microtime(true));
        $data_array = array("click_action" => "FLUTTER_NOTIFICATION_CLICK", "status" => "done", "rand_id" => $rand_id);
        if ($extra_data) {
            $data_array = array_merge($data_array, $extra_data);
        }
		$data_array = json_encode($data_array);
        $full_data = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $title,
                    "body" => $body
				],
				'data' => [
                    "customData" => $data_array
                ]
            ]
        ];
        $full_data = json_encode($full_data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
		// $response = curl_exec($ch);
        curl_close($ch);
        // echo $response;
    }

	// public function notify($token, $title, $body, $extra_data)
	// {
	// 	$ch = curl_init("https://fcm.googleapis.com/fcm/send");
	// 	$header = array(
	// 		'Content-Type: application/json',
	// 		"Authorization: key=AAAADpufTQE:APA91bHCXUrX_WLqzqqOVoViBKWMNtXFTOf_3dExAhBNkyaXFcNDtKIfi2F-vqXiRfJBKzk52oUfZKI-ZJRqR9QCH7wQ8ppSJkhptPChZH-qyZ3Lwu82ORR86-7G_b_qtZj56WXuZQdw"
	// 	);
	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// 	curl_setopt($ch, CURLOPT_POST, 1);
	// 	$rand_id = round(microtime(true));
	// 	$data_array = array("click_action" => "FLUTTER_NOTIFICATION_CLICK", "status" => "done", "rand_id" => $rand_id);
	// 	if ($extra_data) {
	// 		$data_array = array_merge($data_array, $extra_data);
	// 	}
	// 	$full_data = json_encode(array(
	// 		"to" => $token,
	// 		"notification" => array("title" => $title, "body" => $body, "hashCode" => 123456),
	// 		"data" => $data_array,
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
	public function notification($token, $title, $text, $full_data)
	{
		$ch = curl_init("https://fcm.googleapis.com/fcm/send");
		$header = array(
			'Content-Type: application/json',
			"Authorization: key=AAAADpufTQE:APA91bHCXUrX_WLqzqqOVoViBKWMNtXFTOf_3dExAhBNkyaXFcNDtKIfi2F-vqXiRfJBKzk52oUfZKI-ZJRqR9QCH7wQ8ppSJkhptPChZH-qyZ3Lwu82ORR86-7G_b_qtZj56WXuZQdw"
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		$rand_id = round(microtime(true));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		curl_close($ch);
	}
	public function driver_notification($token, $title, $text, $full_data)
	{
		$ch = curl_init("https://fcm.googleapis.com/fcm/send");
		$header = array(
			'Content-Type: application/json',
			"Authorization: key=AAAADpufTQE:APA91bHCXUrX_WLqzqqOVoViBKWMNtXFTOf_3dExAhBNkyaXFcNDtKIfi2F-vqXiRfJBKzk52oUfZKI-ZJRqR9QCH7wQ8ppSJkhptPChZH-qyZ3Lwu82ORR86-7G_b_qtZj56WXuZQdw"
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		$rand_id = round(microtime(true));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		curl_close($ch);
	}
	function __destruct()
	{
		if ($this->con) {
			$this->con->close();
		}
	}

	/**
	 * Log changes done by Admin users into database
	 * @param mixed $old_values - Old values of a record
	 * @param mixed $new_values - New values of a record
	 * @param integer $record_id - primary ID
	 * @param string $table_name
	 * @param string $action - create,update,delete etc
	 * @param string $admin_user_id
	 * @param string $admin_user_name - optional
	 * @return string $action_source e.g Edit Job, Assign Interpreter, name of performing operation
     * @return string $action_by e.g Type of user performing action [1:LSUK Staff,2:Client Portal,3:Interpreter Portal]
	 */
	public function log_changes($old_values = array(), $new_values = array(), $record_id, $table_name, $action = 'update', $admin_user_id, $admin_user_name, $action_source = '', $action_by = 1)
	{
		try {
			if ($action == 'update') {
				if (!is_array($old_values) && !is_array($new_values)) {
					$old_values = json_decode($old_values, true);
					$new_values = json_decode($new_values, true);
					foreach ($old_values as $key => $value) {
						if ($old_values[$key] == $new_values[$key]) {
							unset($old_values[$key]);
							unset($new_values[$key]);
						} else {
							$old_values[$key] = $this->con->escape_string($old_values[$key]);
							$new_values[$key] = $this->con->escape_string($new_values[$key]);
						}
					}
					if (count($old_values) > 0 || count($new_values) > 0) {
						$field_name = ucwords(str_replace("_", " ", $action_source));
						self::insert("audit_logs", array(
							'field_name' => $field_name,
							'old_value' => count($old_values) > 0 ? json_encode($old_values) : NULL,
							'new_value' => count($new_values) > 0 ? json_encode($new_values) : NULL,
							'table_name' => $table_name,
							'record_id' => $record_id,
							'action' => $action,
							'user_id' => $admin_user_id,
							'user_name' => $admin_user_name,
							'created_date' => date("Y-m-d H:i:s"),
							'ip_address' => $_SERVER['REMOTE_ADDR'],
							'action_source' => $action_source,
                            'action_by' => $action_by
						));
					}
				} else {
					foreach ($old_values as $key => $value) {
						if ($old_values[$key] != $new_values[$key]) {
							self::insert("audit_logs", array(
								'field_name' => $key,
								'old_value' => $old_values[$key] ? $this->con->escape_string($old_values[$key]) : NULL,
								'new_value' => $new_values[$key] ? $this->con->escape_string($new_values[$key]) : NULL,
								'table_name' => $table_name,
								'record_id' => $record_id,
								'action' => $action,
								'user_id' => $admin_user_id,
								'user_name' => $admin_user_name,
								'created_date' => date("Y-m-d H:i:s"),
								'ip_address' => $_SERVER['REMOTE_ADDR'],
								'action_source' => $action_source,
                                'action_by' => $action_by
							));
						}
					}
				}
			} else {
				self::insert("audit_logs", array(
					'field_name' => ($action == 'create' ? 'New Row' : NULL),
					'new_value' => ($action == 'create' ? json_encode($new_values) : NULL),
					'table_name' => $table_name,
					'record_id' => $record_id,
					'action' => $action,
					'user_id' => $admin_user_id,
					'user_name' => $admin_user_name,
					'created_date' => date("Y-m-d H:i:s"),
					'ip_address' => $_SERVER['REMOTE_ADDR'],
					'action_source' => $action_source,
                    'action_by' => $action_by
				));
			}
			return TRUE;
		} catch (Exception $e) {
			return FALSE;
		}
	}

	public function generate_reference($table_type = 1, $update_table_name = "", $update_table_id = "", $created_date = "")
	{
		$created_date = empty($created_date) ? date('Y-m-d H:i:s') : $created_date;
        $new_reference_no = self::read_specific("MAX(id) as new_id", "global_reference_no")['new_id'] + 1;
		// $new_reference_no = str_pad($new_reference_no, 4, "0", STR_PAD_LEFT);
		self::insert("global_reference_no", array("reference_no" => $new_reference_no, "table_type" => $table_type, "created_date" => $created_date), true);
		if (!empty($update_table_name)) {
			self::update($update_table_name, array("reference_no" => $new_reference_no), "id=" . $update_table_id);
		}
		return $new_reference_no;
	}

	public function upload_file($folder, $name, $type, $tmp, $picName)
    {
        $allowedExts = array("gif", "jpeg", "jpg", "png", "pdf", "doc", "docx", "rtf", "odt", "txt", "tiff", "bmp", "xls", "xlsx");
        $temp = explode(".", $name);
        $extension = strtolower(end($temp));
        if (in_array($extension, $allowedExts)) {
            if (file_exists($folder . "/" . $name)) {
                echo ('Picture already existed!');
            } else {
                $parts = explode(".", $name);
                $extension = end($parts);
                move_uploaded_file($tmp, $folder . "/" . $name);
                $PicPath = $folder . "/" . $tmp;
                rename($folder . "/" . $name, $folder . "/" . $picName . '.' . $extension);
                return $Pix = $picName . '.' . $extension;
            }
        }
    }
}
$acttObj=$obj = new actionsClass;

class miscClass
{
	public function IsDatedNull($val)
	{
		if ($val == '1001-01-01' || $val == "30-11--0001") {
			return true;
		}
		return false;
	}
	public function dated($val)
	{
		if ($val == '1001-01-01' || $val == "30-11--0001") {
			return 'Not yet fixed!';
		} else {
			return $dated = date_format(date_create($val), 'd-m-Y');
		}
	}
	public function sys_date()
	{
		return $dated = date_format(date_create(date("Y-m-d")), 'd-m-Y');
	}
	public function sys_date_db()
	{
		return $dated = date("Y-m-d");
	}
	public function sys_datetime_db()
	{
		return $dated = date("Y-m-d H:i:s");
	}
	public function add_in_date($dat, $dys)
	{
		return date('Y-m-d', strtotime($dat . $dys . ' days'));
	}
	public function round_fun($val)
	{
		return round($val, 2);
	}
	public function numberFormat_fun($val)
	{
		return number_format($val, 2);
	}
	function round_quarter($num, $parts)
	{
		$res = $num * $parts;
		$res = ceil($res);
		return $res / $parts;
	}
	//Calculate client incremented hours/minutes based on last param:return_type = hour || minutes
	function calculate_client_hours($input_hours, $input_increment, $return_type = 'hour')
	{
		$jump_value = array('15' => '0.25', '30' => '0.50', '45' => '0.75', '60' => '1');
		$jump_value_multiple = array('0.25' => '1', '0.50' => '2', '0.75' => '3', '1' => '4');
		$increment_quarter = $input_increment / 60;
		$calculated_hours = $return_type == 'hour' ? $input_hours : round($input_hours / 60, 2);
		$array_input = explode(".", number_format($input_hours, 2));
		$floated_input = $array_input[1] / 100;
		$ceiled_value = ((floor($input_hours * 4) / 60) * 60) / 4;
		if ($floated_input == 0) {
			$calculated_hours = $return_type == 'hour' ? $input_hours : ($input_hours / 60) * 4;
		} else {
			if ($ceiled_value < ($array_input[0] + $jump_value[$input_increment])) {
				$calculated_hours = $array_input[0] + $jump_value[$input_increment];
			} else {
				if ($ceiled_value == ($array_input[0] + $jump_value[$input_increment])) {
					if ($input_hours > ($array_input[0] + $jump_value[$input_increment])) {
						$calculated_hours = $ceiled_value + $jump_value[$input_increment];
					} else {
						$calculated_hours = $input_hours;
					}
				} else {
					if (strpos($floated_input / $jump_value[$input_increment], '.') > -1) {
						$calculated_hours = $ceiled_value + $jump_value[$input_increment];
					} else {
						$calculated_hours = $input_hours;
					}
				}
			}
		}
		$calculated_hours = $return_type == 'hour' ? $calculated_hours : $calculated_hours * 60;
		return str_replace(",", "", number_format($calculated_hours, 2));
	}

	function time_elapsed_string($datetime, $full = false)
	{
		$now = new DateTime();
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$minute_diff = $diff->i;
		$hour_diff = $diff->h;
		$day_diff = $diff->d;
		$month_diff = $diff->m;
		$year_diff = $diff->y;

		if ($year_diff > 0) {
			$result = ($year_diff == 1) ? '1 year ago' : "$year_diff years ago";
		} elseif ($month_diff > 0) {
			$result = ($month_diff == 1) ? '1 month ago' : "$month_diff months ago";
		} elseif ($day_diff > 0) {
			$result = ($day_diff == 1) ? '1 day ago' : "$day_diff days ago";
		} elseif ($hour_diff > 0) {
			$result = ($hour_diff == 1) ? '1 hour ago' : "$hour_diff hours ago";
		} elseif ($minute_diff > 0) {
			$result = ($minute_diff == 1) ? '1 minute ago' : "$minute_diff minutes ago";
		} else {
			$result = 'just now';
		}

		return $result;
	}
}
$misc = new miscClass;
