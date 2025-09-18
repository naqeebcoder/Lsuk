<!DOCTYPE html>
<html>

<head>
    <title>CMS</title>
</head>

<body>

<?php

function IsRunLocal()
{
    $bIsLocal=false;
    if ($_SERVER['SERVER_NAME']=="localhost")	
        $bIsLocal=true;
    return $bIsLocal;
}

?>

<div>
<iframe width=800 height=600 id="idMyIframe" src="<?php echo (IsRunLocal()?"http://localhost:50139/WebSite/myshopplatform/PhilsCMSPage.aspx?tab=":"http://classyscript.com/classyscript/PhilsCMSPage.aspx?tab=") .$_GET["url"] ?>" width="0" height="0"></iframe>
</div>

<button onclick="window.opener.location.reload()">Refresh Page</button>

</body>
</html>
