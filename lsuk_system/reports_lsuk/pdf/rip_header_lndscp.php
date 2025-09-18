<?php 
$PDF_HEADER_LOGO = "logo.png";//any image file. check correct path.
$PDF_HEADER_LOGO_WIDTH = "30";
$PDF_HEADER_TITLE = "                                                                     Language Services UK Limited";

$PDF_HEADER_STRING = "\n\n                                      Translation | Interpreting | Transcription | Cross-Cultural Training & Development\n";

$pdf->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE, $PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
?>