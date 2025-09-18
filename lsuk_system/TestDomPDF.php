<?php 

/*
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'Courier');
$dompdf = new Dompdf($options);
*/

/*
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->set_option('defaultFont', 'Courier');
*/

//$_dompdf_show_warnings = true;
//$_dompdf_warnings = [];

// include autoloader
require_once 'dompdf/autoload.inc.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;



use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'Courier');
$options->set('isRemoteEnabled', TRUE);
$options->set('debugKeepTemp', TRUE);
$options->set('isHtml5ParserEnabled', true);
//$options->set('chroot', '');
//$dompdf = new Dompdf($options);




// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->set_option('defaultFont', 'Courier');
$dompdf->set_option('isRemoteEnabled', TRUE);

//Go to the dompdf_config.inc.php file and set the variable DOMPDF_ENABLE_REMOTE to TRUE ...


//$dompdf->loadHtml('hello my world');

$html="";

//TestPdf($html);
//TestPdfFile($dompdf);

$href=$_GET['href'];
$isxml=false;
$inxml=$_GET['x'];
if (isset($inxml) && $inxml=="1")
    $isxml=true;

$getcardfiled="";
$filenamed="";
if (isset($_GET['perid']))
{
    $perid=$_GET['perid'];
    //$perid=14685;
    //$perid=7276;
    //$perid=12575;
    //$perid=9649;

    if ($isxml==true)
        $pername="cust_".$perid."_.xml";
    else
        $pername="cust_".$perid."_.pdt";
    $filenamed=$pername;

    $getcardfiled="getcardfiled.aspx?perid=$perid";
}
else
{
    $designid=$_GET['designid'];
    //A_9283_
    $designname="A_".$designid."_";
    $filenamed=$designname;
}

$slash=strrpos($href,'/');
$dir=substr($href,0,$slash+1);
$dirfiles=$dir."files/";

if ($getcardfiled!="")
    $virtpath=MapPath($dir,$getcardfiled);
else
    //$virtpath=$dir."/files/".$filenamed;
    $virtpath=MapPath($dirfiles,$filenamed);

//$xdata=file_get_contents($virtpath);

$doc = new DOMDocument();
//$doc->loadHTMLFile($filename);
$doc->load($virtpath);

$elements = $doc->getElementsByTagName('fonts');
$elemFonts=$elements[0];

$html.="<head>";
if ($elemFonts)
    SetNodeXmlFonts($html,$elemFonts);
$html.="</head>";


$html.="<body>";

$elements = $doc->getElementsByTagName('front');
$elemFront=$elements[0];

//$pdf->AddPage();

//$html="<div style='position:relative;'>";

PrintPdfCard($elemFront,$dirfiles,$html,$dir);

$html.="<body>";

//$html.="</div>";

$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
//$dompdf->setPaper('A4', 'landscape');
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
//$dompdf->stream();
//document.pdf
//0 = preview,1=download
$dompdf->stream("codexworld",array("Attachment"=>0));

function TestPdfFile($dompdf)
{
    $filename="googfontpdf.html";
    $html=file_get_contents($filename);
    
    //$dompdf->loadHTMLFile($filename);
    $dompdf->loadHTML($html);
}

function TestPdf(&$html)
{

    //$html="<div style='width:80px;height:80px;background-image:url(\"./salary-256.png\");background-repeat:no-repeat;'>IMAGE HERE: </div>";

    $html="<div style='width:80px;height:80px;background-image:url(http://localhost:88/mylangcdb/lsuk_system/salary-256.png);background-repeat:no-repeat;'>IMAGE HERE: </div>";
    //http://localhost:88/mylangcdb/lsuk_system/testdompdf.php

    //$dompdf->loadHtmlFile('pdf.html');

    //$html='<img src="./salary-256.png" />';
    $html='<img src="http://localhost:88/mylangcdb/lsuk_system/salary-256.png" />';

    //$filename="salary-256.png";
    //256x256
    $filename="UKflag.jpg";
    //1264x944


    //C:\xampp\htdocs\mylangcdb\lsuk_system\images
    //$url="C:/xampp/htdocs/mylangcdb/lsuk_system/images".$filename;

    $url="images/".$filename;

    $filename="alpha2.gif";
    $url2="reports_lsuk/pdf/images/".$filename;


    //$html='<img src="http://localhost:88/mylangcdb/lsuk_system/salary-256.png" />';


    $html="<div style='position:relative;'>";

    //720 is full width?
    //pix on-screen
    //width=800
    //width:8.7in;height:4in;
    //$html.='<img width=720 style="background-size: 50% 50%;position:absolute;" src="'.$url.'" />';
    //background-size: 50% 50%;
    $html.='<img style="width:7.2in;height:10.7in;position:absolute;" src="'.$url.'" />';

    $html.='<img style="position:absolute;left:20mm;top:21mm;width:28mm;height:20mm;" src="'.$url.'" />';

    $html.="<div style='position:absolute;left:30mm;top:21mm;border:2mm solid red;".
        "background-size: 200mm 200mm;width:200mm;height:460mm;".
        //"background-position:-130mm 0mm;".
        "background-image:url(".$url2.");background-repeat:no-repeat;'>IMAGE on top</div>";
    //$html.="<div style='position:absolute;left:50mm;top:31mm;width:180px;height:180px;background-image:url(".$url.");background-repeat:no-repeat;'>IMAGE HERE: </div>";

    $html.="</div>";
}

function ReScale($oldwdt,$oldtot,$newwdt)
{
    $new=$oldwdt/$oldtot*$newwdt;
    $new=(int)($new*100)/100;
    return $new;
}

function ReScaleWdtNo($oldwdt)
{
    return ReScale($oldwdt,400,7.2);
}

function ReScaleHgtNo($oldhgt)
{
    return ReScale($oldhgt,600,10.7);
}

function ReScaleWdt($oldwdt)
{
    //return ReScale($oldwdt,400,210);
    //return ReScale($oldwdt,400,200);
    //return ReScale($oldwdt,400,190);
    return ReScale($oldwdt,400,7.2)."in";
}

function ReScaleHgt($oldhgt)
{
    //return ReScale($oldhgt,600,270);
    //return ReScale($oldhgt,600,250);
    return ReScale($oldhgt,600,10.7).".in";
}

function MapPath($dir,$pathfile)
{
    $arr=explode('/',$pathfile);
    $len=count($arr);
        
    $imgnamed=$arr[$len-1];
    return $dir.$imgnamed;
}

function PrintPdfCard($elemFront,$dirfiles,&$html,$dir)
{
    $nodes=$elemFront->childNodes;

    $nIndex=0;
    foreach ($elemFront->childNodes as $elem)
    {
        $str=$elem->nodeName;

        if ($str=="image")
            PrintPdfImages($elem,$dirfiles,$html,$nIndex);
        else if ($str=="edit")
            PrintPdfText($elem,$dirfiles,$html,$nIndex);
        else if ($str=="imgbox")
            PrintPdfImgBox($elem,$dirfiles,$html,$nIndex,$dir);

        $nIndex++;
    }
}




function PrintPdfImages($elemImg,$dirfiles,&$html,$nIndex)
{
    if ($elemImg=="")
        return;


    //inset & main image

    //$elements = $elemFront->getElementsByTagName('image');
    //$elemImg=$elements[0];


    
    $wdt=$elemImg->getAttribute("wdt");
    $hgt=$elemImg->getAttribute("hgt");
    $wdt2=ReScaleWdt($wdt);
    $hgt2=ReScaleHgt($hgt);


    $inset=$elemImg->getAttribute("inset");
    $inscale=$elemImg->getAttribute("inscale");
    //0.41
    $inleft=$elemImg->getAttribute("inleft");
    $intop=$elemImg->getAttribute("intop");
        
    $inwdt=$elemImg->getAttribute("inimgwdt");
    $inhgt=$elemImg->getAttribute("inimghgt");
        
    //$inleft2=ReScaleWdt($inleft)*$inscale;
    //$intop2=ReScaleHgt($intop)*$inscale;

    $nfudgeleft=0;
    $nfudgetop=0;

    if ($inset!="")
    {
        $inleft+=$nfudgeleft;
        $intop+=$nfudgetop;
        $inleft2=ReScaleWdt($inleft);
        $intop2=ReScaleHgt($intop);

        //$inwdt2=ReScaleWdt($inwdt);
        //$inhgt2=ReScaleHgt($inhgt);
        $inwdt2=ReScaleWdtNo($inwdt);
        $inwdt2*=$inscale;
        $inwdt2=SetCssUnits($inwdt2);
        $inhgt2=ReScaleHgtNo($inhgt);
        $inhgt2*=$inscale;
        $inhgt2=SetCssUnits($inhgt2);

        $imgpath=MapPath($dirfiles,$inset);
            //$imgpath=MapPath($basecardsfile,$inset);
        

        $html.="<img style='z-index:$nIndex;position:absolute;left:$inleft2;top:$intop2;width:$inwdt2;height:$inhgt2;' src='$imgpath' />";
            
            //$pdf->Image($imgpath, $inleft2, $intop2, $inwdt2, $inhgt2, '', 
            //    '', '', false, 300, '', false, false, 
            //    1, false, false, false);
    }
        
    //image main

    $left=$elemImg->getAttribute("left");
    $top=$elemImg->getAttribute("top");
    $img=$elemImg->getAttribute("img");

    $left2=ReScaleWdt($left);
    $top2=ReScaleHgt($top);

    $imgpath=MapPath($dirfiles,$img);
        //$imgpath=MapPath($basecardsfile,$img);

    $html.="<img style='z-index:$nIndex;position:absolute;left:$left2;top:$top2;width:$wdt2;height:$hgt2;' src='$imgpath' />";
        
        ///$pdf->Image($imgpath, $left2, $top2, $wdt2, $hgt2, '', 
        //    '', '', false, 300, '', false, false, 
        //    1, false, false, false);
}

function PrintPdfText($elemEdit,$dirfiles,&$html,$nIndex)
{
    if ($elemEdit=="")
        return;

    //@text
    //$elements = $elemFront->getElementsByTagName('edit');
    //$elemEdit=$elements[0];

    $wdt=$elemEdit->getAttribute("wdt");
    $hgt=$elemEdit->getAttribute("hgt");
    $left=$elemEdit->getAttribute("left");
    $top=$elemEdit->getAttribute("top");
    $borcolor=$elemEdit->getAttribute("bordercolor");
    $color=$elemEdit->getAttribute("color");
    $txtborder=$elemEdit->getAttribute("txtborder");
    $toplevel=$elemEdit->getAttribute("toplevel");
    $usecolor=$elemEdit->getAttribute("usecolor");

    $text=$elemEdit->getAttribute("text");
    //decode
    //$htmtxt = htmlspecialchars_decode($text);
    $htmtxt = urldecode($text);

    $wdt2=ReScaleWdt($wdt);
    $hgt2=ReScaleHgt($hgt);

    $nfudgeleft=0;
    $nfudgetop=0;
    $left+=$nfudgeleft;
    $top+=$nfudgetop;

    $left2=ReScaleWdt($left);
    $top2=ReScaleHgt($top);

    //$html.="<head>";

    //$html.="<link href='http://localhost:50139/WebSite/myshopplatform/cssfonts.aspx?family=PacificoRegular&v2' rel='stylesheet' type='text/css'>";

    //$html.="<div style='font-family:PacificoRegular'>";
    //$html.="<div style='z-index:1;' >";
    $html.="<div style='z-index:$nIndex;' >";

    /*
    $html.="<style type='text/css'>";
    $html.=
    "@font-face { \r\n
        font-family: PacificoRegular; \r\n
        src: url('fonts/PacificoRegular.eot;'); \r\n
        src: local('PacificoRegular'), url('fonts/PacificoRegular.ttf;');\r\n
        } ";
    $html.="</style>";
    */

    //$html.="</head>";
        
    //$html.="<body>";
    
    //$html.="<div style='font-family:PacificoRegular;position:absolute;left:$left2;top:$top2;width:$wdt2;height:$hgt2;'>$htmtxt</div>";
    //$html.="<div style='font-size:28pt;font-family:PacificoRegular;position:absolute;left:$left2;top:$top2;width:$wdt2;height:$hgt2;'>bigger</div>";
    
    $html.="<div style='z-index:$nIndex;border:2mm solid red;position:absolute;left:$left2;top:$top2;width:$wdt2;height:$hgt2;'>".$htmtxt."</div>";

    //$html.="</body>";
    $html.="</div>";
}

function PrintPdfImgBox($elemImg,$dirfiles,&$html,$nIndex,$dir)
{
    //return;
    if ($elemImg=="")
        return;

    //$html.='<img style="position:absolute;left:20mm;top:21mm;width:20mm;height:20mm;" src="'.$url.'" />';

    $img=$elemImg->getAttribute("img");

    $wdt=$elemImg->getAttribute("wdt");
    $hgt=$elemImg->getAttribute("hgt");
    $left=$elemImg->getAttribute("left");
    $top=$elemImg->getAttribute("top");
    $toplevel=$elemImg->getAttribute("toplevel");
    $scalepc=$elemImg->getAttribute("scale");

    $wdt2=ReScaleWdt($wdt);
    $hgt2=ReScaleHgt($hgt);

    $posx=$elemImg->getAttribute("posx");
    $posy=$elemImg->getAttribute("posy");

    $fposx=(float)$posx;
    $fposy=(float)$posy;

    $posxpc=0;
    $posypc=0;

    //$strBkPos="";
    if (isset($fposx) && !is_nan($fposx) && $fposx!=0 && isset($fposy) && !is_nan($fposy) && $fposy!=0) 
    {
        $posxpc=$posx/$wdt*100.0;
        $posypc=$posy/$hgt*100.0;

        //$strBkPos="background-position:$posxpc% $posypc%;";
    }

    /*
    if (isset($scalepc))
    {
        $wdt2=ReScaleWdtNo($wdt);
        $hgt2=ReScaleHgtNo($hgt);
        $wdt2*=$scalepc/100;
        $hgt2*=$scalepc/100;
        $wdt2=SetCssUnits($wdt2);
        $hgt2=SetCssUnits($hgt2);
    }
    */
 

    /*
    $nfudgeleft=-8;
    $nfudgetop=5;
    $nfudgeleft=0;
    $nfudgetop=0;

    $left+=$nfudgeleft;
    $top+=$nfudgetop;*/

    $left2=ReScaleWdt($left);
    $top2=ReScaleHgt($top);

    $slash=strrpos($img,'/');
    $imgFname=substr($img,$slash+1);

    //$scalepc=100;
    $virtimg=MapPath($dir,"getimgsized.aspx");
    //$virtimg=MapPath($dir,$img);
    $virtimg.="?img=$imgFname&wdtpc=$scalepc&hgtpc=$scalepc&posx=$posxpc&posy=$posypc";

    //$virtimg=$img;

    //$html.="<img style='z-index:$nIndex;position:absolute;left:$left2;top:$top2;width:$wdt2;height:$hgt2;' src='$img' />";
    //$html.="<img style='z-index:$nIndex;position:absolute;left:$left2;top:$top2;width:$wdt2;height:$hgt2;' src='$img' />";


    //$html.="<img style='z-index:$nIndex;position:absolute;left:$left2;top:$top2;width:$wdt2;height:$hgt2;".$strBkPos."' src='$virtimg' />";
    $html.="<img style='z-index:$nIndex;position:absolute;left:$left2;top:$top2;width:$wdt2;height:$hgt2;' src='$virtimg' />";

    //background-position: 50% 50%;
    //background-size: 296mm 296mm;
    //background-origin: content-box;
    //background-clip: padding-box;
    //$html.="<div style='background-size: $wdt2 $hgt2;background-repeat: no-repeat;background-image:url($img);z-index:$nIndex;position:absolute;left:$left2;top:$top2;width:$wdt2;height:$hgt2;'></div>";
    
    //$html.="<div style='background-size: 100% 100%;background-repeat: no-repeat;background-image:url($virtimg);z-index:$nIndex;position:absolute;left:$left2;top:$top2;width:$wdt2;height:$hgt2;'></div>";
}

function IsLocalFontLink()
{
    return false;
    //$bIsLocal=false;
    if ($_SERVER['SERVER_NAME']=="localhost")	
        $bIsLocal=true;
    return $bIsLocal;
}

function SetNodeXmlFonts(&$html,$elemFonts)
{
    //$nodes=$elemFonts->childNodes;
    $bIsLocalFont=IsLocalFontLink();

    foreach ($elemFonts->childNodes as $elem)
    {
        $str=$elem->nodeName;

        if ($str=="font")
            InsertFontLinkBefore($html,$elem, $bIsLocalFont);
    }
}

function InsertFontLinkBefore(&$html, $elem, $bIsLocalFont)
{
    $strName=$elem->getAttribute("name");
    $url="";
    if ($bIsLocalFont==false)
        $url="http://fonts.googleapis.com/css?family=" . $strName;
    else
        $url="http://localhost:55958/WebSite/myshoppers/cssfonts.aspx?family=" . $strName;
        
    $strLink="<link rel='stylesheet' type='text/css' href='".$url."' />\r\n";
    $html.=$strLink;
    //$html.="<link href='http://localhost:50139/WebSite/myshopplatform/cssfonts.aspx?family=PacificoRegular&v2' rel='stylesheet' type='text/css'>";
}

function SetCssUnits($inhgt2)
{
    return $inhgt2."in";
}


?> 


