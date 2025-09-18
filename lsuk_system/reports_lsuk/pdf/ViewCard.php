<?php

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Test Image
 * @author Me
 * @since Now
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Phil');
$pdf->SetTitle('Greet Cards');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 009', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// -------------------------------------------------------------------

// add a page
$pdf->AddPage();

// set JPEG quality
$pdf->setJPEGQuality(75);

// Image method signature:
// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// Example of Image from data stream ('PHP rules')
$imgdata = base64_decode('iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABlBMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDrEX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==');

// The '@' character is used to indicate that follows an image data stream and not an image file name
$pdf->Image('@'.$imgdata);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// Image example with resizing
$pdf->Image('images/image_demo.jpg', 15, 140, 75, 113, 'JPG', 'http://www.tcpdf.org', '', true, 150, '', false, false, 1, false, false, false);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// test fitbox with all alignment combinations

$horizontal_alignments = array('L', 'C', 'R');
$vertical_alignments = array('T', 'M', 'B');

$x = 15;
$y = 35;
$w = 30;
$h = 30;
// test all combinations of alignments
for ($i = 0; $i < 3; ++$i) {
    $fitbox = $horizontal_alignments[$i].' ';
    $x = 15;
    for ($j = 0; $j < 3; ++$j) {
        $fitbox[1] = $vertical_alignments[$j];
        $pdf->Rect($x, $y, $w, $h, 'F', array(), array(128,255,128));
        $pdf->Image('images/image_demo.jpg', $x, $y, $w, $h, 'JPG', '', '', false, 300, '', false, false, 0, $fitbox, false, false);
        $x += 32; // new column
    }
    $y += 32; // new row
}

$x = 115;
$y = 35;
$w = 25;
$h = 50;

//	public function Image($file, $x='', $y='', $w=0, $h=0, $type='', 
//      $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, 
//      $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array()) {
for ($i = 0; $i < 3; ++$i) {
    $fitbox = $horizontal_alignments[$i].' ';
    $x = 115;
    for ($j = 0; $j < 3; ++$j) {
        $fitbox[1] = $vertical_alignments[$j];
        $pdf->Rect($x, $y, $w, $h, 'F', array(), array(128,255,255));
        $pdf->Image('images/image_demo.jpg', $x, $y, $w, $h, 'JPG', '', '', 
            false, 300, '', false, false, 0, $fitbox, false, false);
        $x += 27; // new column
    }
    $y += 52; // new row
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

//public function Text($x, $y, $txt, $fstroke=false, 
//  $fclip=false, $ffill=true, $border=0, $ln=0, $align='', 
//  $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M', $rtloff=false) {
//	 * @see Cell(), Write(), MultiCell(), WriteHTML(), WriteHTMLCell()

// Stretching, position and alignment example

$pdf->SetXY(110, 200);

$pdf->Rect(60, 190, 45, 45, 'F', array(), array(28,125,125));
//	public function writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='') {
//$pdf->writeHTML("<div>phil here</div>", true, false, false, false, '');
//$pdf->Text(110,200,"<b>bold here</b>phil here");
//$pdf->Write(20,"<b>bold here</b>phil here");
//$pdf->Cell(90,60,"<b>bold here</b>phil here");

//public function writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=false, $reseth=true, $align='', $autopadding=true) {
//    return $this->MultiCell($w, $h, $html, $border, $align, $fill, $ln, $x, $y, $reseth, 0, true, $autopadding, 0, 'T', false);

$pdf->WriteHTMLCell(90,60,60,190,"<b>bold here</b>pds ds ds ds  <br/>sdhil here");

//$pdf->Image('images/image_demo.jpg', '', '', 40, 40, '', '', 'T', 
//    false, 300, '', false, false, 1, false, false, false);

//$pdf->Image('images/image_demo.jpg', 70, 194, 40, 40, '', '', '', 
//    false, 300, '', false, false, 1, false, false, false);

$pdf->Image('images/alpha2.gif', 56, 174, 40, 40, '', '', '', 
    false, 300, '', false, false, 1, false, false, false);
// -------------------------------------------------------------------

//@@@

$pdf->AddPage();


//
//file_get_contents()
//C:\Phil\code\GreetCards2017\CopyDir From EasyCgi\files\A_9283_

//$basecardsweb="C:/Phil/code/GreetCards2017/CopyDir From EasyCgi/";
$basecardsweb="C:/Phil/code/GreetCards2018/GreetCard z18-02_16c EditCardsPrt/WebSite/mooncardshopper/";
$basecardsfile=$basecardsweb."files/";
//$filepath=$basecardsfile."A_9283_";
//$filepath=$basecardsfile."cust_9840_.pdt";
//$filepath=$basecardsfile."cust_9843_.pdt";
//$filepath=$basecardsfile."cust_9845_.pdt";
//$filepath=$basecardsfile."cust_9666_.pdt";
    //see thru
//$filepath=$basecardsfile."cust_9672_.pdt";
//$filepath=$basecardsfile."cust_9673_.pdt";
//$filepath=$basecardsfile."cust_9876_.pdt";
//$filepath=$basecardsfile."cust_9887_.pdt";
//$filepath=$basecardsfile."cust_9649_.pdt";
//$filepath=$basecardsfile."cust_12592_.pdt";
//$filepath=$basecardsfile."cust_12589_.pdt";
//$filepath=$basecardsfile."cust_12582_.pdt";
$filepath=$basecardsfile."cust_12575_.pdt";




$doc = new DOMDocument();
//$doc->loadHTMLFile($filename);
$doc->load($filepath);


//inset image

$elements = $doc->getElementsByTagName('front');
$elemFront=$elements[0];
$elements = $elemFront->getElementsByTagName('image');

$elemImg=$elements[0];
$wdt=$elemImg->getAttribute("wdt");
$hgt=$elemImg->getAttribute("hgt");
$wdt2=ReScaleWdt($wdt);
$hgt2=ReScaleHgt($hgt);


$inset=$elemImg->getAttribute("inset");
$inscale=$elemImg->getAttribute("inscale");
    //0.41
$inleft=$elemImg->getAttribute("inleft");
$intop=$elemImg->getAttribute("intop");
        
$inleft2=ReScaleWdt($inleft)*$inscale;
$intop2=ReScaleHgt($intop)*$inscale;
//$inleft2=ReScaleWdt($inleft);
//$intop2=ReScaleHgt($intop);

//orig:223x226
//$inwdt=$wdt2*$inscale;
//$inhgt=$hgt2*$inscale;
$inwdt=223-50;
$inhgt=226-50;
//$inwdt2=ReScaleWdt($inwdt);
//$inhgt2=ReScaleHgt($inhgt);
$inwdt2=ReScaleWdt($inwdt)*$inscale;
$inhgt2=ReScaleHgt($inhgt)*$inscale;

$inleft2+=24;
$intop2+=16;

$arr=explode('/',$inset);
$len=count($arr);
        
$imgnamed=$arr[$len-1];
$imgpath=$basecardsfile.$imgnamed;
        
$pdf->Image($imgpath, $inleft2, $intop2, $inwdt2, $inhgt2, '', 
            '', '', false, 300, '', false, false, 
            1, false, false, false);
        
    

//image main

$left=$elemImg->getAttribute("left");
$top=$elemImg->getAttribute("top");
$img=$elemImg->getAttribute("img");

$left2=ReScaleWdt($left);
$top2=ReScaleHgt($top);

//http://localhost:1369/WebSite/mooncard3/files/up_9282.png

//list($a,$b)=
$arr=explode('/',$img);

//if (count(array_count_values($ws)) == 1) {
$len=count($arr);
//$len1=array_count_values($arr);

$imgnamed=$arr[$len-1];
$imgpath=$basecardsfile.$imgnamed;


//strpos

//	public function Image($file, $x='', $y='', $w=0, $h=0, $type='', 
//      $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, 
//      $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array()) {

/*$pdf->Image($imgpath, 8, 14, 200, 250, '', 
    '', '', false, 300, '', false, false, 
    1, false, false, false);*/

$pdf->Image($imgpath, $left2, $top2, $wdt2, $hgt2, '', 
    '', '', false, 300, '', false, false, 
    1, false, false, false);


    




    //@text
$elements = $elemFront->getElementsByTagName('edit');
$elemEdit=$elements[0];
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
$left2=ReScaleWdt($left);
$top2=ReScaleHgt($top);

//public function writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=false, $reseth=true, $align='', $autopadding=true) {
//    return $this->MultiCell($w, $h, $html, $border, $align, $fill, $ln, $x, $y, $reseth, 0, true, $autopadding, 0, 'T', false);

$pdf->WriteHTMLCell($wdt2,$hgt2,$left2,$top2,$htmtxt);



//Close and output PDF document
$pdf->Output('example_009.pdf', 'I');

function ReScale($oldwdt,$oldtot,$newwdt)
{
    $new=$oldwdt/$oldtot*$newwdt;
    return $new;
}

function ReScaleWdt($oldwdt)
{
    return ReScale($oldwdt,400,210);
}

function ReScaleHgt($oldhgt)
{
    return ReScale($oldhgt,600,270);
}

//============================================================+
// END OF FILE
//============================================================+
