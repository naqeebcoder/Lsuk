<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
?>
<?php include'db.php'; include'class.php';$salary_id= $_GET['salary_id'];?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <title>Assign Interpreter</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="stylesheet" type="text/css" href="css/default.css"/>

</head>
<body>    

        <form action="paid_salary.php" method="get" class="register">
        
          <h1>Remittance Advice Note</h1>
          <fieldset class="row1">
            <legend>Calculate Salary</legend>
 <p><label>From Date</label>
<input type="date" id="fdate" name="fdate" required/>

<label>To Date</label>
<input type="date" id="tdate" name="tdate" required/></p>
</fieldset>
<div><button class="button" type="submit" name="submit" value="<?php echo $salary_id;  ?>">Submit &raquo;</button></div>