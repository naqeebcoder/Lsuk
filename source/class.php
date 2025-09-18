<?php error_reporting(0);
//.................................................................................///\\\\////\\\\///\\\//\\...............................
$table = isset($_POST['table']) ? $_POST['table'] : '';
$colName = isset($_POST['colName']) ? $_POST['colName'] : '';
$comp = isset($_POST['comp']) ? $_POST['comp'] : '';
$eFlag = isset($_POST['eFlag']) ? $_POST['eFlag'] : '';
$rowID = isset($_POST['rowID']) ? $_POST['rowID'] : '';
if (!empty($table) && !empty($colName) && !empty($comp)) {
    include 'db.php';
    /*check unique on edit page*/
    if ($eFlag == 'editFlag') {
        $query = "SELECT count(*) as flag FROM $table where $colName='$comp' and id<>$rowID";
        $result = mysqli_query($con, $query);
        while ($row = $result->fetch_assoc()) {
            $flag = $row['flag'];
        }
        if ($flag != 0) {
            echo "(" . $comp . ') is already in use!';
        }
    }
    /*check unique on insertion page*/ else {
        $query = "SELECT count(*) as flag FROM $table where $colName='$comp'";
        $result = mysqli_query($con, $query);
        while ($row = $result->fetch_assoc()) {
            $flag = $row['flag'];
        }
        if ($flag != 0) {
            echo "(" . $comp . ') is already in use!';
        }
    }
}
//.................................................................................///\\\\////\\\\///\\\//\\...............................
class loginClass
{
    public function SignIn($UserNam, $Pswrd)
    {
        include 'db.php';
        $query = "SELECT count(*) num,id, name, prv FROM login where  email='$UserNam' AND pass='$Pswrd'";
        $result = mysqli_query($con, $query);
        while ($row = $result->fetch_assoc()) {
            $flag = $row['num'];
            $UserName = $row['name'];
            $id = $row['id'];
            $prv = $row['prv'];
        }
        if ($flag == 0) {
            return 1;
        }
        if ($flag == 1) {
            if (session_id() == '' || !isset($_SESSION)) {
                session_start();
            }
            $_SESSION['UserName'] = $UserName;
            $_SESSION['userId'] = $id;
            $_SESSION['prv'] = $prv;
            echo '<script type="text/javascript">' . "\n";
            echo 'window.location="home.php";';
            echo '</script>';
        } else {
            return 0;
        }
    }
}
$logObj = new loginClass();
class actionClass
{
    /*public function meta(){include'db.php';
    $sql="SELECT * FROM cust";
    if ($result=mysqli_query($con,$sql)){
    while ($fieldinfo=mysqli_fetch_field($result)){$cols=$fieldinfo->name;}
    mysqli_free_result($result);}}*/

    public function get_id($table)
    {
        $dated = date("Y-m-d");
        include 'db.php';
        $query = "INSERT INTO $table (id,dated) VALUES (NULL,'$dated')";
        if (!mysqli_query($con, $query)) {
            return die('Error: ' . mysqli_error($con));
        }
        $query = "SELECT MAX(id) AS lastId FROM $table";
        $result = mysqli_query($con, $query);
        while ($row = $result->fetch_assoc()) {
            return $lastId = $row['lastId'];
        }
    }

    public function max_id($table)
    {
        include 'db.php';
        $query = "SELECT MAX(id) AS lastId FROM $table";
        $result = mysqli_query($con, $query);
        while ($row = $result->fetch_assoc()) {
            return $lastId = $row['lastId'];
        }
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

    //.........................................................................Action...........................................
    public function new_old_table($new_table, $old_table, $id)
    {
        $dated = date("Y-m-d");
        include 'db.php';
        $query = "INSERT INTO $new_table SELECT * FROM $old_table WHERE id = $id";
        if (!mysqli_query($con, $query)) {
            return die('Error: ' . mysqli_error($con));
        }
    }

    public function editFunTimeAsMins($table, $edit_id, $col, $data)
    {
        //$data:
        //01:25
        list($a, $b) = explode(':', $data);
        $data = $a * 60 + $b;

        include 'db.php';

        $query = "update $table set $col='$data' where id=$edit_id";
        if (!mysqli_query($con, $query)) {
            die('Error: ' . mysqli_error($con));
        } else {
            return "Successful!";
        }
    }

    public function editFun($table, $edit_id, $col, $data)
    {
        include 'db.php';
        $escape_date = mysqli_escape_string($con, $data);
        $query = "update $table set $col='$escape_date' where id=$edit_id";
        if (!mysqli_query($con, $query)) {
            die('Error: ' . htmlspecialchars(mysqli_error($con)));
        } else {
            return "Successful!";
        }
    }

    public function editFunDate($table, $edit_id, $col, $data)
    {
        include 'db.php';
        $strDateAs = TestCode::GetDateAsUS($data);
        $query = "update $table set $col='$strDateAs' where id=$edit_id";
        if (!mysqli_query($con, $query)) {
            die('Error: ' . mysqli_error($con));
        } else {
            return "Successful!";
        }
    }

    public function delFun($table, $del_id)
    {
        include 'db.php';
        $query = "delete from $table where id=$del_id";
        if (!mysqli_query($con, $query)) {
            die('Error: ' . mysqli_error($con));
        } else {
            return "Successful!";
        }
    }

    public function del_comp($table, $colm, $compare)
    {
        include 'db.php';
        $query = "delete from $table where $colm='$compare'";
        if (!mysqli_query($con, $query)) {
            die('Error: ' . mysqli_error($con));
        } else {
            return "Successful!";
        }
    }

    public function uniqueFun($table, $col, $comp)
    {
        include 'db.php';
        $query = "SELECT count(*) as flag FROM $table where $col='$comp'";
        $result = mysqli_query($con, $query);
        while ($row = $result->fetch_assoc()) {
            $flag = $row['flag'];
        }
        if ($flag == 0) {
            return 0;
        } else {
            return 1;
        }
    }

    public function unique_value($table, $col, $id)
    {
        include 'db.php';
        $query = "SELECT $col AS val FROM $table where id=$id";
        $result = mysqli_query($con, $query);
        while ($row = $result->fetch_assoc()) {
            return $row['val'];
        }
    }

    public function unique_data($table, $req, $col, $data)
    {
        include 'db.php';
        $query = "SELECT $req AS val FROM $table where $col='$data'";
        $result = mysqli_query($con, $query);
        while ($row = $result->fetch_assoc()) {
            return $row['val'];
        }
    }
    public function unique_dataAnd($table, $req, $col, $data, $col2, $data2)
    {
        include 'db.php';
        $query = "SELECT $req AS val FROM $table where $col='$data' and $col2='$data2'";
        $result = mysqli_query($con, $query);
        while ($row = $result->fetch_assoc()) {
            return $row['val'];
        }
    }
    //READ all columns method goes here...
    public function read_all($col, $table, $where = NULL)
    {
        include 'db.php';
        $sql = "SELECT " . $col . " FROM " . $table;
        if ($where != null) {
            $sql .= ' WHERE ' . $where;
        }
        $result = mysqli_query($con, $sql);
        return $result;
    }
    //READ all columns method goes here...
    public function read_all_c($col, $table, $where = NULL)
    {
        include 'db.php';
        $sql = "SELECT " . $col . " FROM " . $table;
        if ($where != null) {
            $sql .= ' WHERE ' . $where;
        }
        print_r($sql);
    }
    //READ specific columns method goes here...
    public function query_extra($col, $table, $where = NULL, $extra)
    {
        include 'db.php';
        $sql = "SELECT " . $col . " FROM " . $table;
        if ($where != null) {
            $sql .= ' WHERE ' . $where;
        }
        mysqli_query($con, $extra);
        $result = mysqli_query($con, $sql);
        $row = $result->fetch_assoc();
        return $row;
    }
    //READ specific columns method goes here...
    public function read_specific($col, $table, $where = NULL)
    {
        include 'db.php';
        $sql = "SELECT " . $col . " FROM " . $table;
        if ($where != null) {
            $sql .= ' WHERE ' . $where;
        }
        $result = mysqli_query($con, $sql);
        $row = $result->fetch_assoc();
        return $row;
    }
    //For echo purpose
    public function read_specific_c($col, $table, $where = NULL)
    {
        include 'db.php';
        $sql = "SELECT " . $col . " FROM " . $table;
        if ($where != null) {
            $sql .= ' WHERE ' . $where;
        }
        print_r($sql);
    }

    //INSERT RECORD method goes here...
    public function insert($table, $data, $return_id = false)
    {
        include 'db.php';
        $key = array_keys($data);
        $val = array_values($data);
        $sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
            . "VALUES ('" . implode("', '", $val) . "')";
        $result = mysqli_query($con, $sql);
        if ($return_id) {
            return mysqli_insert_id($con);
        } else {
            return $result;
        }
    }
    public function insert_c($table, $data, $return_id = false)
    {
        include 'db.php';
        $key = array_keys($data);
        $val = array_values($data);
        $sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
            . "VALUES ('" . implode("', '", $val) . "')";
        print_r($sql);
    }
    //UPDATE RECORD method goes here...
    public function update($table, $data, $parameters)
    {
        include 'db.php';
        $cols = array();
        $cols2 = array();
        foreach ($data as $key => $val) {
            $cols[] = "$key = '$val'";
        }
        if (is_array($parameters)) {
            foreach ($parameters as $key2 => $val2) {
                $cols2[] = "$key2 = '$val2'";
            }
            if (count($parameters) > 1) {
                $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE " . implode(' and ', $cols2);
            } else {
                $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE " . $key2 . '=' . $val2;
            }
        } else {
            $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE " . $parameters;
        }
        $result = mysqli_query($con, $sql);
        return $result;
    }
    public function update_c($table, $data, $parameters)
    {
        include 'db.php';
        $cols = array();
        $cols2 = array();
        foreach ($data as $key => $val) {
            $cols[] = "$key = '$val'";
        }
        if (is_array($parameters)) {
            foreach ($parameters as $key2 => $val2) {
                $cols2[] = "$key2 = '$val2'";
            }
            if (count($parameters) > 1) {
                $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE " . implode(' and ', $cols2);
            } else {
                $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE " . $key2 . '=' . $val2;
            }
        } else {
            $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE " . $parameters;
        }
        print_r($sql);
        exit;
    }
    public function update_custom($table, $data, $where)
    {
        include 'db.php';
        $cols = array();
        foreach ($data as $key => $val) {
            $cols[] = "$key = '$val'";
        }
        if (!empty($where)) {
            $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE " . $where;
        } else {
            $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE 1";
        }
        $result = mysqli_query($con, $sql);
        return $result;
    }
    public function delete($table, $where)
    {
        include 'db.php';
        $query = "DELETE FROM $table WHERE $where";
        if (!mysqli_query($con, $query)) {
            return 0;
        } else {
            return 1;
        }
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
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
            include 'db.php';
            if ($action == 'update') {
                if (!is_array($old_values) && !is_array($new_values)) {
                    $old_values = json_decode($old_values, true);
                    $new_values = json_decode($new_values, true);
                    foreach ($old_values as $key => $value) {
                        if ($old_values[$key] == $new_values[$key]) {
                            unset($old_values[$key]);
                            unset($new_values[$key]);
                        } else {
                            $old_values[$key] = mysqli_escape_string($con, $old_values[$key]);
                            $new_values[$key] = mysqli_escape_string($con, $new_values[$key]);
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
                                'old_value' => $old_values[$key] ? mysqli_escape_string($con, $old_values[$key]) : NULL,
                                'new_value' => $new_values[$key] ? mysqli_escape_string($con, $new_values[$key]) : NULL,
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
}
$acttObj = new actionClass;
// if($acttObj->read_specific("status","maintenance","id=1")["status"]=="ON"){
//     echo "<script>window.location.href='../lsuk_system/maintenance.php';</script>";
// }
class stats
{
    public function mainStat($table)
    {
        include 'db.php';
        $query = "SELECT count(*) as num FROM $table
			JOIN invoice ON $table.invoiceNo=invoice.invoiceNo";
        $result = mysqli_query($con, $query);
        while ($row = $result->fetch_assoc()) {
            $num = $row['num'];
        }
        return $num;
    }
}
$statsObj = new stats();
class miscClass
{
    public function dated($val)
    {
        return $dated = date_format(date_create($val), 'd-m-Y');
    }
    public function timeFormat($val)
    {
        return $dated = date_format(date_create($val), 'H:i');
    }
    public function sys_datetime_db()
    {
        return $dated = date("Y-m-d H:i:s");
    }
    public function date_time($val)
    {
        return $date_time = date_format(date_create($val), 'd-m-Y H:i:s');
    }
    public function round_fun($val)
    {
        return round($val, 2);
    }
    public function numberFormat_fun($val)
    {
        return number_format($val, 2);
    }
}
$misc = new miscClass;

class SafeVar
{
    public static function Get($map, $named, $default)
    {
        if (!isset($map[$named])) {
            return $default;
        }

        return $map[$named];
    }
    public static function GetVar($named, $default)
    {
        //try
        //{
        //$job=@$_GET['job'];
        if (!isset($_GET[$named])) {
            return $default;
        }

        return @$_GET[$named];
        //}
        /*catch ()
    {
    return $default;
    }*/
    }

    public static function IsLocal()
    {
        $bIsLocal = false;
        if ($_SERVER['SERVER_NAME'] == "localhost") {
            $bIsLocal = true;
        }

        return $bIsLocal;
    }

    public static function IsDebug()
    {
        //return false;
        //return true;
        return SafeVar::IsLocal();
    }
}

class TestCode
{
    public static function SendEmailFrom($to_add, $subject, $message, $from_add)
    {
        $headers = "From: $from_add \r\n";
        $headers .= "Reply-To: info@lsuk.org \r\n";
        $headers .= "Return-Path: info@lsuk.org \r\n";
        $headers .= "X-Mailer: PHP \r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $strSubject = file_get_contents("lsuk_system/mailsubprefixrule.txt");
        if (isset($strSubject)) {
            $subject = $strSubject . $subject;
        }

        return TestCode::SendEmail($to_add, $subject, $message, $headers);
    }

    public static function SendEmail($to_add, $subject, $message, $headers)
    {
        if (SafeVar::IsDebug() == true) {
            return true;
        }

        $bOk = mail($to_add, $subject, $message, $headers);
        return $bOk;
    }
    public static function GetDateAsUS($val)
    {
        return $dated = date_format(date_create($val), 'd-m-Y');
    }

    public static function LoadHtml($filename)
    {
        $doc = new DOMDocument();
        $doc->loadHTMLFile($filename);

        //http://php.net/manual/en/domdocument.loadhtmlfile.php

        echo $doc->saveHTML();
        return $doc;
    }
}
