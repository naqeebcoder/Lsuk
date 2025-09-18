<?php

$postdata = file_get_contents("php://input");

$strXml="<xml>".$postdata."</xml>";

include "dopagesfunc.php";

//$virtpath="dopages.xml";

$doc = new DOMDocument();
//$doc->loadHTMLFile($filename);
//$doc->load($virtpath);
$doc->loadXml($strXml);

$elements = $doc->getElementsByTagName('page');
$elemPage=$elements[0];

//GenPages($elemPages);

GenPage($elemPage);

echo "ok";

?>

