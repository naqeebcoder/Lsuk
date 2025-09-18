<?php
$get_image=strtolower(end(explode('.',$_GET['img'])));
switch ($get_image) {
  case "jpeg":
    $img_r = imagecreatefromjpeg($_GET['img']);
    break;
  case "jpg":
    $img_r = imagecreatefromjpeg($_GET['img']);
    break;
  default:
    $img_r = imagecreatefrompng($_GET['img']);
}
  
$dst_r = ImageCreateTrueColor( $_GET['w'], $_GET['h'] );

imagecopyresampled($dst_r, $img_r, 0, 0, $_GET['x'], $_GET['y'], $_GET['w'], $_GET['h'], $_GET['w'],$_GET['h']);

header('Content-type: image/jpeg');
switch ($get_image) {
  case "jpeg":
    imagejpeg($dst_r);
    break;
  case "jpg":
    imagejpeg($dst_r);
    break;
  default:
    imagepng($dst_r);
}

exit;
?>