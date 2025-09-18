<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Assign Interpreter</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>
        <script> function openInParent(url) {  window.opener.location.href = url; }</script>

</head>
<body>    

        <form action="" method="post" class="register">
        
          <h1>Make a Report</h1>
          <fieldset class="row1">
            <legend>Select Dates
          </legend>

<p><label>From Date *</label>
<input type="date" name="fdate" id="fdate" />
<label>To Date *</label>
<input type="date" name="tdate" id="tdate"  onchange="myFunction()" />
</p></fieldset>
<p style="display:none" id="demo"></p>
<br /><br />

          
          <div><button class="button" type="submit" name="submit" onclick="openInParent('reports/test.php?id=<?php echo @$_POST['fdate']; ?>');">Submit &raquo;</button></div>
         
        </form>
</body>
</html>




