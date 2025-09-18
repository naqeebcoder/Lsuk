<?php 

class MenuPermiss
{

    static public function ItemHdrUrlPage($urlpage,$strNamed)
    {
        return MenuPermiss::ItemHdrUrlAttribs($urlpage,$strNamed,null,null);
    }

    static public function ItemUrlPage($urlpage,$strNamed)
    {
        return MenuPermiss::ItemUrlAttribs($urlpage,$strNamed,null,null);
    }

    static public function ItemUrlPageSpan($urlpage,$strNamed)
    {
        return MenuPermiss::ItemUrlSpanAttribs($urlpage,$strNamed,null,null);
    }

    static public function ItemUrlOpen($urlpage,$strNamed,$wdt,$hgt)
    {
        return MenuPermiss::ItemOpenAttribs($urlpage,$strNamed,$wdt,$hgt,null,null);
    }

    
    static public function CheckUrlDb($urlpage)
    {
        global $con;
        
        if(session_id() == '' || !isset($_SESSION))
        {
            session_start();
            
        } 
        if (!isset($con))
        {
            include 'db.php'; 
        }

        //$url=basename($urlpage);
        $quest=strrpos($urlpage,"?");
        if ($quest!=false)
            $urlfile=substr($urlpage,0,$quest);
        else    
            $urlfile=$urlpage;

        $allowed=MenuPermiss::CheckUrl($urlfile);
        return $allowed;
    }

    //
    static private function ItemUrlSpanAttribs($urlpage,$strTip,$strAAttribs,$strSpanAttribs)
    {
        return MenuPermiss::ItemUrlAttribs($urlpage,$strTip,$strAAttribs,$strSpanAttribs);
    }

    //$strAAttribs,$strSpanAttribs are optional
    static private function ItemUrlAttribs($urlpage,$strTip,$strAAttribs,$strSpanAttribs)
    {
        $allowed=MenuPermiss::CheckUrlDb($urlpage);
        if ($allowed==false)
            return;

        if (!isset($strAAttribs))
            $strAAttribs="";

        if (!isset($strSpanAttribs))
            $strSpanAttribs="";
        
        //<li><a href="interp_list.php">Interpreters List</a></li>

        $htm='<li><a href="'.$urlpage.'">'.$strTip.'</a></li>';

        echo $htm;
    }

    //$strAAttribs,$strSpanAttribs are optional
    static private function ItemHdrUrlAttribs($urlpage,$strTip,$strAAttribs,$strSpanAttribs)
    {
        $allowed=MenuPermiss::CheckUrlDb($urlpage);
        if ($allowed==false)
            return;

        if (!isset($strAAttribs))
            $strAAttribs="";

        if (!isset($strSpanAttribs))
            $strSpanAttribs="";
        
        $htm='<li><a href="#">'.$strTip.'</a>';
        echo $htm;
    }

    static public function CheckUrl($url)
	{
        global $con;

        $url=basename($url);

        $userid=$_SESSION['userId'];
        if ($userid<20)
            return true;
	
		$query="
		select count(*) as exist
		from userrole
		 JOIN rolepermis ON userrole.roleid=rolepermis.roleid
		where userrole.userid=$userid and phppage='$url'";
	
		$result = mysqli_query($con, $query); 
        $row = mysqli_fetch_assoc($result);
        if ($row["exist"]>0)
           return true;

        return false;
    }
	
    static private function IsOldPriv($prv)
    {
        //return true;

        if($_SESSION['prv']==$prv)
            return true;

        return false;
    }

    static public function HasPriv($prv)
    {        
        if(session_id() == '' || !isset($_SESSION))
        {
            session_start();
        } 

        $userid=$_SESSION['userId'];
	
        if($_SESSION['prv']==$prv)
            return true;

        return false;
    }

    static private function ItemOpenAttribs($urlpage,$strTip,$wdt,$hgt,$strAAttribs,$strSpanAttribs)
    {
        $allowed=MenuPermiss::CheckUrlDb($urlpage);
        if ($allowed==false)
            return;

        if (!isset($strAAttribs))
            $strAAttribs="";

        if (!isset($strSpanAttribs))
            $strSpanAttribs="";
        
        //<li><a href="#" onClick="MM_openBrWindow('invoice_query.php',
        //'_blank','scrollbars=yes,resizable=yes,width=900,height=650')">Invoices Registered</a></li>

        $htm='<li><a href="#" onClick="MM_openBrWindow(\''.
            $urlpage.'\',\'_blank\',\'scrollbars=yes,resizable=yes,left=450,top=10,width='.$wdt.',height='.$hgt.
            '\')"'.$strAAttribs.'>'.$strSpanAttribs.$strTip.'</a></li>';

        echo $htm;
    }

    static public function ItemUrlPageImg($urlpage,$strNamed,$img)
    {
        //ItemUrlPageImg
        return MenuPermiss::ItemUrlPageImgAttribs($urlpage,$strNamed,$img,null,null);
    }

    static private function ItemUrlPageImgAttribs($urlpage,$strTip,$img,$strAAttribs,$strSpanAttribs)
    {
        $allowed=MenuPermiss::CheckUrlDb($urlpage);
        if ($allowed==false)
            return;

        if (!isset($strAAttribs))
            $strAAttribs="";

        if (!isset($strSpanAttribs))
            $strSpanAttribs="";
        
        //<li><a href="logout.php"><img src="images/arrow.gif" style="margin-left:-15px;" />Sign out</a></li>

        $htm='<li><a href="'.$urlpage.'"><img src="images/'.$img.'" style="margin-left:-15px" />'.$strTip.'</a></li>';

        echo $htm;
    }
    
}

?>
