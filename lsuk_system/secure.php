<?php error_reporting(0);if(session_id() == '' || !isset($_SESSION)){session_start();}
class Secure{
	static public function IsLocal(){
		$bIsLocal=false;
		if ($_SERVER['SERVER_NAME']=="localhost")	
			$bIsLocal=true;
		return $bIsLocal;
	}

	static public function CheckRef(){
		if (!isset($_SESSION['UserName']) || $_SESSION['UserName']==""){
			//http://localhost:88/mylangcdb/lsuk_system/
		
			$bCheckOk=true;
			if (!isset($_SERVER["HTTP_REFERER"]))
				$bCheckOk=false;
			else{
				$strRef=$_SERVER["HTTP_REFERER"];
			}
		
			if (!isset($_SERVER["HTTP_HOST"]))
				$bCheckOk=false;
			else
			{
				//$strOrigin=$_SERVER["HTTP_ORIGIN"];
				$strOrigin=@$_SERVER["REQUEST_SCHEME"];
				$strOrigin.="https://".@$_SERVER["HTTP_HOST"];
			}
		
			if ($bCheckOk==true){
				$len=strlen($strOrigin);
				$found=substr_compare($strRef,$strOrigin,0,$len);
				if ($found!=0)
				{
					$bCheckOk=false;
				}
			}
		
			if (/*!Secure::IsLocal() &&*/ $bCheckOk==false){
				//header("Location: http://example.com/myOtherPage.php");
				header("Location: /lsuk_system/");
				die();	
				//	exit;
			}
		}
	}
}


