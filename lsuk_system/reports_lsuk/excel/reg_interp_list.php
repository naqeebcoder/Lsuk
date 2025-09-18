<?php include '../../db.php';
include_once('../../class.php');
$table = 'interpreter_reg';
$name = @$_GET['name'];
$interp_email = @$_GET['interp_email'];
$InterpPhoneNumber = @$_GET['InterpPhoneNumber'];
$srchgender = @$_GET['srchgender'];
$city = @$_GET['city'];
$lang = @$_GET['lang'];
if ($lang == "all") {
    $lang = "";
}
$tp = @$_GET['tp'];
$put_delete = $tp == 'tr' ? 'deleted_flag=1' : 'deleted_flag=0';
$srcdbs_checked = @$_GET['srcdbs_checked'];
if (isset($_GET['tp'])) {
    if ($_GET['tp'] == 'ac') {
        $put_active = "and $table.active=0";
    }
    if ($_GET['tp'] == 'da') {
        $put_active = "and $table.active=1";
    }
    if ($_GET['tp'] == 'nr') {
        $put_active = "and $table.is_temp=1";
    }
} else {
    $put_active = "and $table.active=0";
}
if (isset($_GET['tp'])) {
    if ($_GET['tp'] == 'ac') {
        $put_active = "and $table.active=0";
    }
    if ($_GET['tp'] == 'da') {
        $put_active = "and $table.active=1";
    }
    if ($_GET['tp'] == 'nr') {
        $put_active = "and $table.is_temp=1";
    }
} else {
    $put_active = "and $table.active=0";
}
$missing_docs = @$_GET['missing_docs'];
if (isset($missing_docs)) {
    $put_missing_docs = "missing_docs=1";
} else {
    $put_missing_docs = "missing_docs=0";
}
if (isset($missing_docs)) {
    $append_missing_docs = " AND ((interpreter_reg.agreement='') OR
    (interpreter_reg.crbDbs='' AND interpreter_reg.interp='Yes') OR (interpreter_reg.ni='') OR 
    (interpreter_reg.identityDocument='' AND interpreter_reg.uk_citizen=1) OR 
    (interpreter_reg.work_evid_file='' AND interpreter_reg.uk_citizen=0) OR 
    (interpreter_reg.acNo=''))";
}
if ($name) {
    $append_name = " and name like '$name%'";
}
if ($interp_email) {
    $append_email = " and email like '$interp_email%'";
}
if ($InterpPhoneNumber) {
    $append_contact = " and contactNo like '$InterpPhoneNumber%'";
}
if ($srchgender) {
    $append_gender = " and gender = '$srchgender'";
}
if (isset($isAdhoc)) {
    $append_isAdhoc = " and isAdhoc='$isAdhoc'";
}
if ($city) {
    $append_city = " and city like '$city%'";
}
if ($srcdbs_checked) {
    $append_dbs = " and $table.dbs_checked like '$srcdbs_checked%'";
}
if ($_SESSION['is_root'] == 1 || $_SESSION['prv'] == 'Finance') {
    if ($adLang = 'adLang' && $lang == '') {
        $query = "SELECT distinct $table.* FROM $table 		
      where $table.$put_delete $append_missing_docs $append_dbs $append_name $append_email $append_contact $append_gender $append_city $append_isAdhoc $put_active";
    } else {
        $query = "SELECT distinct $table.* FROM $table
      JOIN interp_lang ON interpreter_reg.code=interp_lang.code	 
      where $table.$put_delete and interp_lang.lang like '$lang%' 
      $append_missing_docs $append_dbs $append_name $append_email $append_contact $append_gender $append_city $put_active group by email";
    }
}

if ($_SESSION['is_root'] == 0 && $_SESSION['prv'] != 'Finance') {
    if ($adLang = 'adLang' && $lang == '') {
        $query = "SELECT distinct $table.* FROM $table where $table.$put_delete $append_missing_docs $append_dbs $append_name $append_email $append_contact $append_gender $append_city $put_active";
    } else {
        $query = "SELECT distinct $table.* FROM $table JOIN interp_lang ON interpreter_reg.code=interp_lang.code	 
      where $table.$put_delete $append_missing_docs $append_dbs $append_name $append_email $append_contact $append_gender $append_city $put_active and interp_lang.lang like '$lang%' group by email";
    }
}
$result = mysqli_query($con, $query);
$htmlTable = '<style>
table {border-collapse: collapse; width:670px;}
th {border: 1px solid #999; padding: 0.5rem;text-align: left;background-color:#039; color:#FFF; font-weight:bold;}
td {border: 1px solid #999; padding: 0.5rem;text-align: left;}
</style>
<div>
<h2 style="text-align:center;background:grey">List of Interpreters</h2>
<p align="right"> Report Date: ' . $misc->sys_date() . '</p>
</div>
<table>
    <thead>
        <tr>
            <th style="background-color:#039;color:#FFF;">Name</th>
            <th style="background-color:#039;color:#FFF;">Contact No</th>
            <th style="background-color:#039;color:#FFF;">Email</th>
            <th style="background-color:#039;color:#FFF;">City</th>
            <th style="background-color:#039;color:#FFF;">Gender</th>
            <th style="background-color:#039;color:#FFF;">Account Type</th>
            </tr>
        </thead>
    <tbody>';
while ($row = $result->fetch_assoc()) {
    $htmlTable .= '<tr>
        <td>' . ucwords($row["name"]) . '</td>
        <td>' . $row["contactNo"] . '</td>
        <td>' . $row["email"] . '</td>
        <td>' . $row["city"] . '</td>
        <td>' . $row["gender"] . '</td>
        <td>' . ($row["isAdhoc"] == '1' ? 'Adhoc Interpreter' : 'Normal Interpreter') . '</td>
    </tr>';
}
$htmlTable .= '</tbody></table>';
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=registered_interpreters_" . time() . ".xls");
echo $htmlTable;
