<?php $selectFields="distinct interpreter_reg.*";
$source_lang_req=$srcLang;
$target_lang_req=$obj->read_specific("target",$table,"id=".$assign_id)['target'];
$dbs_required=isset($dbs_checked) && !empty($dbs_checked) && $dbs_checked==0?'AND interpreter_reg.dbs_checked=0':'';
if(!isset($gender) || $gender=='' || $gender=='No Preference'){
        $put_gender="";
    }else{
        $put_gender="AND interpreter_reg.gender='$gender'";
    }
    if($source_lang_req== $target_lang_req){
    $put_lang="";$query_style='0';
}else if($source_lang_req!='English' && $target_lang_req!='English'){
    $put_lang="";$query_style='1';
}else if($source_lang_req=='English' && $target_lang_req!='English'){
    $put_lang="interp_lang.lang='$target_lang_req' and interp_lang.level<3 ";$query_style='2';
}else if($source_lang_req!='English' && $target_lang_req=='English'){
    $put_lang="interp_lang.lang='$source_lang_req' and interp_lang.level<3 ";$query_style='2';
}else{
	$put_lang="";$query_style='3';
}
$dayName   = strtolower(date('l', strtotime($assignDate))); // friday
$dayFlag   = "interpreter_reg.$dayName";         // e.g. interpreter_reg.friday
$startCol  = "interpreter_reg.{$dayName}_time";  // e.g. interpreter_reg.friday_time
$endCol    = "interpreter_reg.{$dayName}_to";    // e.g. interpreter_reg.friday_to

$workingExpr = "IF(
    ($dayFlag = 'Yes' OR $dayFlag = '')
    AND $startCol IS NOT NULL
    AND $endCol IS NOT NULL
    AND (
        ($startCol = '00:00:00' AND $endCol = '00:00:00')
        OR (
            TIME('$assignTime') >= $startCol
            AND ADDTIME(TIME('$assignTime'), SEC_TO_TIME({$assignDur}*60)) <= $endCol
        )
    ),
    TRUE,
    FALSE
) AS is_inside_working_hours";
if ($table == 'interpreter' || $table == 'telephone')
    $selectFields .= ', '.$workingExpr;
// 
if($query_style=='0'){
	$sql_opt=$obj->read_all("$selectFields", "interpreter_reg,interp_lang", "interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('".$source_lang_req."') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=1 and 
    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND '$assignDate' NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) $dbs_required $put_gender AND interpreter_reg.$chek_col='Yes' AND interp_lang.type = '$chek_col' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 ".(!empty($JobCity)?" ORDER BY interpreter_reg.city <> '$JobCity' ASC,interpreter_reg.city ":" ORDER BY interpreter_reg.name ASC "));
}else if($query_style=='1'){
	$sql_opt=$obj->read_all("$selectFields", "interpreter_reg,interp_lang", "interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('".$source_lang_req."','".$target_lang_req."') and interp_lang.level<3 and interp_lang.code=interpreter_reg.code)=2 and 
    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND '$assignDate' NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) $dbs_required $put_gender AND interpreter_reg.$chek_col='Yes' AND interp_lang.type = '$chek_col' and interp_lang.level<3 AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 ".(!empty($JobCity)?" ORDER BY interpreter_reg.city <> '$JobCity' ASC,interpreter_reg.city ":" ORDER BY interpreter_reg.name ASC "));
}else if($query_style=='2'){
	$sql_opt=$obj->read_all("$selectFields", "interpreter_reg,interp_lang", "interpreter_reg.code=interp_lang.code AND $put_lang and 
    interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND '$assignDate' NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) $dbs_required $put_gender AND interpreter_reg.$chek_col='Yes' AND interp_lang.type = '$chek_col' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 ".(!empty($JobCity)?" ORDER BY interpreter_reg.city <> '$JobCity' ASC,interpreter_reg.city ":" ORDER BY interpreter_reg.name ASC "));

}else{
	$sql_opt=$obj->read_all("$selectFields", "interpreter_reg", "interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND '$assignDate' NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) $dbs_required $put_gender AND interpreter_reg.$chek_col='Yes' AND interp_lang.type = '$chek_col' AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 ".(!empty($JobCity)?" ORDER BY interpreter_reg.city <> '$JobCity' ASC,interpreter_reg.city ":" ORDER BY interpreter_reg.name ASC "));
}

// logic for blacklisted interpreter
// 1. Normalize inputs
$orgNameForJob = $get_job_details['orgName'];
$serviceUserInput = strtolower(trim($get_job_details['inchPerson']));
$referenceInput = strtolower(trim($get_job_details['orgRef']));

// 2. Get company ID from comp_reg using abrv (case-insensitive)
$companyRow = $obj->read_specific('id', 'comp_reg', "abrv = '$orgNameForJob'");
$companyId = $companyRow ? (int)$companyRow['id'] : 0;

// 3. Get all related company IDs (parent + children)
$relatedIds = [$companyId];

// Add parent if this is a child
$parentRow = $obj->read_specific('parent_comp', 'subsidiaries', "child_comp = $companyId");
if ($parentRow && !empty($parentRow['parent_comp'])) {
    $relatedIds[] = (int)$parentRow['parent_comp'];
}

// Add children if this is a parent
$res = $obj->read_all('child_comp', 'subsidiaries', "parent_comp = $companyId");
while ($r = mysqli_fetch_assoc($res)) {
    $relatedIds[] = (int)$r['child_comp'];
}

$relatedIds = array_unique($relatedIds);
while ($r = mysqli_fetch_assoc($res)) {
    $relatedIds[] = (int)$r['child_comp'];
}
$relatedIdsStr = implode(',', $relatedIds);

// 4. Get all relevant blacklist entries for related companies
$blacklisted_map = [];

$blacklistResult = $obj->read_all('*', 'interp_blacklist',
    "deleted_flag = 0 AND (
        (block_by_type = 'parent' AND block_by_id IN ($relatedIdsStr)) OR
        (block_by_type = 'child' AND block_by_id = $companyId)
    )"
);

while ($row = mysqli_fetch_assoc($blacklistResult)) {
    $interpId = $row['interpName'];
    $scope = $row['block_scope'];
    $block = false;

    $rowUser = strtolower(trim($row['service_user_id']));
    $rowRef = strtolower(trim($row['reference_id']));

    if ($scope === 'all') {
        $block = true;
    } elseif ($scope === 'specific_user' && strpos($rowUser, $serviceUserInput) !== false) {
        $block = true;
    } elseif ($scope === 'specific_reference' && strpos($rowRef, $referenceInput) !== false) {
        $block = true;
    }

    if ($block) {
        $blacklisted_map[$interpId][] = [
            'by' => $row['block_by_type'],
            'scope' => $scope,
            'match' => $scope === 'all' ? 'all' : ($scope === 'specific_user' ? $serviceUserInput : $referenceInput),
            'source_id' => $row['block_by_id']
        ];
    }
}
?>