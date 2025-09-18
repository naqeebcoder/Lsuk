<?php //include "secure.php";
//Secure::CheckRef();
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
        while ($row = mysqli_fetch_assoc($result)) {
            $flag = $row['flag'];
        }
        if ($flag != 0) {
            echo "(" . $comp . ') is already in use!';
        }
    }
    /*check unique on insertion page*/
    else {
        $query = "SELECT count(*) as flag FROM $table where $colName='$comp'";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $flag = $row['flag'];
        }
        if ($flag != 0) {
            echo "(" . $comp . ') is already in use!';
        }
    }
}

//.................................................................................///\\\\////\\\\///\\\//\\...............................
class loginClass{
    public function SignIn($UserNam, $Pswrd){
        include 'db.php';
        $query = "SELECT count(*) num,id, name, prv FROM login where  email='$UserNam' AND pass='$Pswrd'";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {$flag = $row['num'];
            $UserName = $row['name'];
            $id = $row['id'];
            $prv = $row['prv'];}
        if ($flag == 0) {return 1;}
        if ($flag == 1) {
            if (session_id() == '' || !isset($_SESSION)) {session_start();}
            $_SESSION['UserName'] = $UserName;
            $_SESSION['userId'] = $id;
            $_SESSION['prv'] = $prv;
            echo '<script type="text/javascript">' . "\n";
            echo 'window.location="home.php";';
            echo '</script>';
        }else{
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

    public function UpdateInvoiceNo($invoiceNo, $table, $edit_id)
    {
        $new_nmbr = $this->unique_data('invoice', 'id', 'invoiceNo', $invoiceNo);
        $invoiceexists = true;
        if ($new_nmbr == null) {
            $new_nmbr = $this->get_id('invoice');
            $newid = $new_nmbr;

            $invoiceexists = false;
            //$new_nmbr="0";
        }
        $new_nmbr = str_pad($new_nmbr, 5, "0", STR_PAD_LEFT);
        $invoice_ful = date("my").$new_nmbr;
        if ($invoiceexists) {
            $this->editFun('invoice', $new_nmbr, 'invoiceNo', $invoice_ful);
        } else {
            $this->editFun('invoice', $newid, 'invoiceNo', $invoice_ful);
        }

        $this->editFun($table, $edit_id, 'invoiceNo', $invoice_ful);
    }

    public function get_id($table)
    {
        $dated = date("Y-m-d");include 'db.php';
        $query = "INSERT INTO $table (id,dated) VALUES (NULL,'$dated')";
        if (!mysqli_query($con, $query)) {return die('Error: ' . mysqli_error($con));}
        $query = "SELECT MAX(id) AS lastId FROM $table";
        $result = mysqli_query($con, $query);while ($row = mysqli_fetch_assoc($result)) {return $lastId = $row['lastId'];}
    }

    public function max_id($table)
    {include 'db.php';
        $query = "SELECT MAX(id) AS lastId FROM $table";
        $result = mysqli_query($con, $query);while ($row = mysqli_fetch_assoc($result)) {return $lastId = $row['lastId'];}}

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
                move_uploaded_file($tmp, "file_folder/" . $folder . "/" . $name);
                $PicPath = "file_folder/" . $folder . "/" . $tmp;
                rename("file_folder/" . $folder . "/" . $name, "file_folder/" . $folder . "/" . $picName . '.' . $extension);
                return $Pix = $picName . '.' . $extension;
            }
        }
    }
    // Upload files by waqar
    public function upload_files($folder, $name, $type, $tmp, $picName)
    {
        $allowedExts = array("gif", "jpeg", "jpg","png","pdf", "doc", "docx","rtf","odt","txt","tiff","bmp","xls","xlsx");
        $temp = explode(".", $name);
        $extension = strtolower(end($temp));
        if (in_array($extension, $allowedExts)) {
            if (file_exists($folder . "/" . $name)) {
                echo ('Picture already existed!');
            } else {
                $extension = end(explode(".", $name));
                move_uploaded_file($tmp, $folder . "/" . $name);
                $PicPath = $folder . "/" . $tmp;
                rename($folder . "/" . $name,$folder . "/" . $picName . '.' . $extension);
                return $Pix = $picName . '.' . $extension;
            }
        }
    }

//.........................................................................Action...........................................
    public function new_old_table($new_table, $old_table, $id)
    {
        $dated = date("Y-m-d");include 'db.php';
        $query = "INSERT INTO $new_table SELECT * FROM $old_table WHERE id = $id";
        if (!mysqli_query($con, $query)) {
            return die('Error: ' . mysqli_error($con));
        }
    }
    //test by waqar
    public function new_old_table2($new_table, $old_table, $id)
    {
        $dated = date("Y-m-d");include 'db.php';
        $query = "INSERT INTO $new_table SELECT * FROM $old_table WHERE id = $id";
        //if (!mysqli_query($con, $query)) {
            return mysqli_query($con, $query);
            //echo $query;
        //}
    }
    public function editFunNowDateTime($table, $edit_id, $col)
    {
        //$data=date("Y-m-d H:i:s");
        date_default_timezone_set('Europe/London');
        $data = date("Y-m-d H:i:s");
        $this->editFun($table, $edit_id, $col, $data);
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
    {include 'db.php';
    $escape_date=mysqli_escape_string($con,$data);
        $query = "update $table set $col='$escape_date' where id=$edit_id";
        if (!mysqli_query($con, $query)) {die('Error: ' . mysqli_error($con));} else {return "Successful!";}}
    public function editFun_comp($table, $col, $data, $comp_col, $comp_data)
    {include 'db.php';
    $escape_date=mysqli_escape_string($con,$data);
        $query = "update $table set $col='$escape_date' where $comp_col='$comp_data'";
        if (!mysqli_query($con, $query)) {die('Error: ' . mysqli_error($con));} else {return "Successful!";}}

    public function delFun($table, $del_id)
    {include 'db.php';
        $query = "delete from $table where id=$del_id";
        if (!mysqli_query($con, $query)) {die('Error: ' . mysqli_error($con));} else {return "Successful!";}}

    public function del_comp($table, $colm, $compare)
    {include 'db.php';
        $query = "delete from $table where $colm='$compare'";
        if (!mysqli_query($con, $query)) {die('Error: ' . mysqli_error($con));} else {return "Successful!";}}

    public function uniqueFun($table, $col, $comp)
    {include 'db.php';
        $query = "SELECT count(*) as flag FROM $table where $col='$comp'";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {$flag = $row['flag'];}
        if ($flag == 0) {return 0;} else {return 1;}}

    public function unique_value($table, $col, $id)
    {include 'db.php';
        $query = "SELECT $col AS val FROM $table where id=$id";
        $result = mysqli_query($con, $query);while ($row = mysqli_fetch_assoc($result)) {return $row['val'];}}

    public function unique_data($table, $req, $col, $data)
    {include 'db.php';
        $query = "SELECT $req AS val FROM $table where $col='$data'";
        $result = mysqli_query($con, $query);while ($row = mysqli_fetch_assoc($result)) {return $row['val'];}}

    public function unique_dataAnd($table, $req, $col, $data, $col2, $data2)
    {
        include 'db.php';
        $query = "SELECT $req AS val FROM $table where $col='$data' and $col2='$data2'";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            return $row['val'];
        }
    }
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
    //READ all columns method goes here...
	public function read_all_c($col,$table,$where) {
	    include 'db.php';
		$sql= "SELECT ".$col." FROM ". $table;
		if($where != null){
			$sql .= ' WHERE '.$where;
		}
		print_r($sql);
	}
	//READ specific columns method goes here...
	public function query_extra($col,$table,$where,$extra) {
	    include 'db.php';
		$sql= "SELECT ".$col." FROM ". $table;
		if($where != null){
			$sql .= ' WHERE '.$where;
		}
		mysqli_query($con, $extra);
		$result = mysqli_query($con, $sql);
		$row = mysqli_fetch_assoc($result);
        return $row;
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
	//For echo purpose
	public function read_specific_c($col,$table,$where) {
	    include 'db.php';
		$sql= "SELECT ".$col." FROM ". $table;
		if($where != null){
			$sql .= ' WHERE '.$where;
		}
		print_r($sql);
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
			public function update_c($table, $data, $parameters) {
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
				print_r($sql);
			}
      public function delete($table, $where){
        include 'db.php';
        $query = "DELETE FROM $table WHERE $where";
        if (!mysqli_query($con, $query)) {return 0;}else{return 1;}
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
            // public function notify($token, $title, $body, $extra_data){   
            //     include '../../token_api.php';
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
            //     $full_data = [
            //         "message" => [
            //             "token" => $token,
            //             "notification" => [
            //                 "title" => $title,
            //                 "body" => $body
            //             ]
            //         ]
            //     ];
            //     $full_data = json_encode($full_data);
            //     curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
            //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //     curl_exec($ch);
            //     curl_close($ch);
            // }
			public function notify($token,$title,$body,$extra_data){
				$ch = curl_init("https://fcm.googleapis.com/fcm/send");
				$header=array('Content-Type: application/json',
				"Authorization: key=AAAADpufTQE:APA91bHCXUrX_WLqzqqOVoViBKWMNtXFTOf_3dExAhBNkyaXFcNDtKIfi2F-vqXiRfJBKzk52oUfZKI-ZJRqR9QCH7wQ8ppSJkhptPChZH-qyZ3Lwu82ORR86-7G_b_qtZj56WXuZQdw");
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt($ch, CURLOPT_POST, 1);
				$rand_id=round(microtime(true));
				$data_array=array("click_action"=>"FLUTTER_NOTIFICATION_CLICK","status"=>"done","rand_id"=>$rand_id);
				if($extra_data){
					$data_array=array_merge($data_array,$extra_data);
				}
				$full_data = json_encode(array(
					"to" => $token,
					"notification"=>array("title"=>$title,"body"=>$body),
					"data"=>$data_array,
				));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $full_data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_exec($ch);
				curl_close($ch);
			}
}
$acttObj = new actionClass;
// if($acttObj->read_specific("status","maintenance","id=1")["status"]=="ON"){
//     echo "<script>window.location.href='maintenance.php';</script>";
// }
class stats{
    public function mainStat($table)
    {include 'db.php';
        $query = "SELECT count(*) as num FROM $table
			JOIN invoice ON $table.invoiceNo=invoice.invoiceNo";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {$num = $row['num'];}
        return $num;}

}$statsObj = new stats();
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
    public function date_time($val)
    {
        if ($val == '1001-01-01 00:00:00' || $val == "30-11--0001 00:00:00") {
            return 'Not yet fixed!';
        } else {
            return $date_time = date_format(date_create($val), 'd-m-Y h:i:s');
        }
    }
    public function ftime($time, $f)
    {
        if (gettype($time) == 'string') {
            $time = strtotime($time);
        }
        return ($f == 24) ? date("hA", $time) : date("h:iA", $time);
    }
    public function sys_date()
    {return $dated = date_format(date_create(date("Y-m-d")), 'd-m-Y');}
    public function sys_date_db()
    {return $dated = date("Y-m-d");}
    public function sys_datetime_db()
    {return $dated = date("Y-m-d H:i:s");}
    public function add_in_date($dat, $dys)
    {return date('Y-m-d', strtotime($dat . $dys . ' days'));}
    public function add_datetime($table, $edit_id)
    {include 'db.php';

        //$data=addslashes($data);
        $query = "update $table set edited_date=NOW() where id=$edit_id";
        if (!mysqli_query($con, $query)) {die('Error: ' . mysqli_error($con));} else {return "Successful!";}}
    public function round_fun($val)
    {return round($val, 2);}
    public function numberFormat_fun($val)
    {return number_format($val, 2);}
}$misc = new miscClass;

class SqlUtils
{
    public static function CmpHash($strSql, $EmpActive, $strWith)
    {
        /*$len=strlen($EmpActive);
        $found=substr_compare($strSql,$EmpActive,0,$len);
        if ($found>=0)
        {
        //replace
        }*/
        return str_replace($EmpActive, $strWith, $strSql);
    }

    public static function ModfiySql($strSql)
    {
        $EmpActive = "##emp_active##";
        $strSql = SqlUtils::CmpHash($strSql, $EmpActive, "and active=0 ");

        return $strSql;
    }
}

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
    public static function SendEmailReply($to_add, $subject, $message, $headers)
    {
    }

    public static function SendEmailFrom($to_add, $subject, $message, $from_add)
    {
        $headers = "From: $from_add \r\n";
        $headers .= "Reply-To: info@lsuk.org \r\n";
        $headers .= "Return-Path: info@lsuk.org \r\n";
        $headers .= "X-Mailer: PHP \r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $strSubject = file_get_contents("mailsubprefixrule.txt");
        if (isset($strSubject) && $strSubject != false) {
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

    public static function AddHtmlFieldsDB($filename, $mapDB)
    {
        $doc = new DOMDocument();
        $doc->loadHTMLFile($filename);

        $elements = $doc->getElementsByTagName('input');
        if (!is_null($elements)) {
            //$input=$elements[0];
            //$input->setAttribute("placeholder","VERY Over Credit Alert");
            //$abc=$input->getAttribute("placeholder");

            foreach ($elements as $element) {

                //echo "<br/>". $element->nodeName. ": ";

                $dbcol = $element->getAttribute("data-dbcol");
                if (isset($dbcol) && isset($mapDB) && isset($mapDB[$dbcol])) {
                    //$element->setAttribute("value","_".$mapDB[$dbcol]."_ah");
                    $element->setAttribute("value", $mapDB[$dbcol]);
                }

                //$element->nodeValue="um";

                //$nodes = $element->childNodes;
                //$alltxt="";
                //foreach ($nodes as $node) {
                //    echo $node->nodeValue. "\n";
                //$alltxt.=$node->nodeValue.",";
                //}
            }
        }
        echo $doc->saveHTML();
        return $doc;
    }

    public static function ModifyHtmlDB($filename, $table, $edit_id)
    {
        $doc = new DOMDocument();
        $doc->loadHTMLFile($filename);

        $elements = $doc->getElementsByTagName('input');
        if (!is_null($elements)) {

            foreach ($elements as $element) {
                $dbName = $element->getAttribute("name");
                if ($dbName != null && isset($_POST[$dbName])) {
                    $dbcol = $element->getAttribute("data-dbcol");
                    if (isset($dbcol)) {
                        $valPosted = $_POST[$dbName];

                        $element->setAttribute("value", $valPosted);

                        //$um=$acttObj;
                        self::editFun($table, $edit_id, $dbName, $valPosted);
                    }
                }
            }
        }
        echo $doc->saveHTML();
        return $doc;
    }

    public static function editFun($table, $edit_id, $col, $data)
    {
        include 'db.php';
        $escape_date=mysqli_escape_string($con,$data);
        $query = "update $table set $col='$escape_date' where id=$edit_id";
        if (!mysqli_query($con, $query)) {
            die('Error: ' . mysqli_error($con));
        } else {
            return "Successful!";
        }
    }

    public static function XsltHtml($filename, $filedata)
    {
        $doc = new DOMDocument();
        //$doc->loadHTMLFile($filename);
        $doc->load($filename);

        $docData = new DOMDocument();
        $docData->load($filedata);

        $proc = new XSLTProcessor();
        $proc->importStylesheet($docData);
        //$proc->setParameter(null,"","");

        //$newdom=$proc->transformToXml($doc);
        $newdom = $proc->transformToDoc($doc);

        print $newdom->saveHTML();

        //http://php.net/manual/en/domdocument.loadhtmlfile.php

        //echo $doc->saveHTML();
        //return $doc;
    }

    public static function ModifyVarTags($elements, $map)
    {
        //$elements = $doc->getElementsByTagName('input');
        if (is_null($elements)) {
            return;
        }

        //new
        $list2 = new DOMNodeList();
        //$len=$elements.length;
        //$len=$elements.length();
        $one = $elements[0];
        $arrElems = array();
        foreach ($elements as $element) {
            array_push($arrElems, $element);

            //$list2->appendChild($element);

            //$arrElems[] = $element;

        }

        //$len=length($arrElems);
        //$len=$arrElems.length;

        foreach ($arrElems as $element) {
            //$element->tagName="span";
            //$dbName=$element->getAttribute("name");

            //$newnode=$element.
            //$elemNew = $element->ownerDocument->createElement('span', 'My span inner');

            $php = $element->nodeValue;
            $replaceWith = trim($php);
            $replaceWith = rtrim($replaceWith, ';');

            if (strpos($replaceWith, ' ') > 0) {
                list($a, $b) = explode(' ', $replaceWith);
                if ($a == "echo") {
                    if (substr($b, 0, 1) == "@") {
                        $b = substr($b, 1);
                    }

                    if (substr($b, 0, 1) == "$") {
                        $b = substr($b, 1);
                    }

                    $replaceWith = $b;
                    if (isset($map[$b])) {
                        $replaceWith = $map[$b];
                    } else {
                        $replaceWith = $replaceWith;
                    }

                    //$strEcho="var is ".$b;
                } else {
                    $replaceWith = $a . " " . $b;
                }

            } else {
                $replaceWith = $replaceWith;
            }

            $elemNew = $element->ownerDocument->createElement('span', $replaceWith);
            $parent = $element->parentNode;
            $old = $parent->replaceChild($elemNew, $element);
        }
    }
}

class OrgOutput
{
    public static function WriteTR(&$nowcompany, &$runcompany, &$tbl)
    {
        if ($nowcompany != $runcompany) {
            $tbl .= <<<EOD
		  <tr>
		  <td></td>
		  </tr>
		  <tr>
			<th style="width:190px;">$nowcompany</th>
		  </tr>
EOD;
            $runcompany = $nowcompany;
        }
    }
}

class EmailPlus
{
    public static function SendEmail($from_mail, $from_name, $replyto, $subject, $message, $filename, $content)
    {
        //https://stackoverflow.com/questions/12301358/send-attachments-with-php-mail
        //$file = $path.$filename;
        ///lsuk_system/reports_lsuk/pdf/timesheet.php?update_id=7532&table=interpreter
        //$content = file_get_contents($file);
        $content = chunk_split(base64_encode($content));
        $uid = md5(uniqid(time()));
        $name = basename($filename);

        // carriage return type (RFC)
        $eol = "\r\n";

        $mailto = $replyto;

        // header
        $header = "From: " . $from_name . " <" . $from_mail . ">\r\n";
        $header .= "Reply-To: " . $replyto . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/alternative; boundary=\"" . $uid . "\"\r\n\r\n";

        // message & attachment
        $nmessage = "--" . $uid . "\r\n";
        $nmessage .= "Content-type:text/html; charset=iso-8859-1\r\n";
        $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $nmessage .= $message . "\r\n\r\n";
        $nmessage .= "--" . $uid . "\r\n";
        $nmessage .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\r\n";
        $nmessage .= "Content-Transfer-Encoding: base64\r\n";
        $nmessage .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
        $nmessage .= $content . "\r\n\r\n";
        $nmessage .= "--" . $uid . "--";

        if (SafeVar::IsDebug() == true) {
            return true;
        }

        if (mail($mailto, $subject, $nmessage, $header)) {
            return true; // Or do something here
        } else {
            return false;
        }
    }
}