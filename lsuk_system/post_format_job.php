<?php
$interpreter_id = $interp_id; // Replace with dynamic ID
if(isset($_POST['ap_user_id']) && !empty($_POST['ap_user_id'])){
    $interpreter_id  = $_POST['ap_user_id'];
    $con = $obj->con;
}
$query = "SELECT DISTINCT pf.* 
FROM post_format pf 
JOIN interpreter_reg ir ON 1=1
LEFT JOIN interp_lang il ON ir.code = il.code
WHERE 
    (TRIM(pf.gender) = 'Both' OR TRIM(pf.gender) = TRIM(ir.gender)) 
    AND (pf.bookinRef IS NOT NULL)
    AND (
        (pf.filter_cities IS NULL or '') 
        OR pf.filter_cities = '' 
        OR FIND_IN_SET(TRIM(ir.city), TRIM(pf.filter_cities)) > 0
    )
    AND (
        (pf.filter_languages IS NULL or '') 
        OR pf.filter_languages = '' 
        OR FIND_IN_SET(TRIM(il.lang), TRIM(pf.filter_languages)) > 0
    )
    AND (
        (TRIM(pf.type) = 'interp' AND TRIM(ir.interp) = 'Yes') 
        OR (TRIM(pf.type) = 'telep' AND TRIM(ir.telep) = 'Yes') 
        OR (TRIM(pf.type) = 'trans' AND TRIM(ir.trans) = 'Yes') 
        OR (TRIM(pf.type) = 'all' AND (
            TRIM(ir.interp) = 'Yes' OR TRIM(ir.telep) = 'Yes' OR TRIM(ir.trans) = 'Yes'
        ))
    )
    AND ir.id = $interpreter_id
	AND pf.status = 'Active'";

$result = mysqli_query($con, $query);
$jobs = [];

while ($row = mysqli_fetch_assoc($result)) {

    $bookinRef = mysqli_real_escape_string($con, $row['bookinRef']);
    $jobType = mysqli_real_escape_string($con, $row['type']); // Get the type from post_format

    $jobQuery = "";

    if ($jobType == 'trans') {
        $jobQuery = "SELECT 'translation' AS job_type, t.id AS job_id 
                     FROM translation t 
                     WHERE t.nameRef LIKE '%$bookinRef' 
                     LIMIT 1";
    } elseif ($jobType == 'telep') {
        $jobQuery = "SELECT 'telephone' AS job_type, tp.id AS job_id 
                     FROM telephone tp 
                     WHERE tp.nameRef LIKE '%$bookinRef' 
                     LIMIT 1";
    } elseif ($jobType == 'interp') {
        $jobQuery = "SELECT 'interpreter' AS job_type, i.id AS job_id 
                     FROM interpreter i 
                     WHERE i.nameRef LIKE '%$bookinRef' 
                     LIMIT 1";
    } elseif ($jobType == 'all') {
        // UNION ALL for all job types
        $jobQuery = "SELECT 'translation' AS job_type, t.id AS job_id 
                     FROM translation t 
                     WHERE t.nameRef LIKE '%$bookinRef'
                     UNION ALL
                     SELECT 'telephone' AS job_type, tp.id AS job_id 
                     FROM telephone tp 
                     WHERE tp.nameRef LIKE '%$bookinRef'
                     UNION ALL
                     SELECT 'interpreter' AS job_type, i.id AS job_id 
                     FROM interpreter i 
                     WHERE i.nameRef LIKE '%$bookinRef'
                     LIMIT 1";
    }

    if (!empty($jobQuery)) {
        $jobResult = mysqli_query($con, $jobQuery);

        if ($jobResult) {
            while ($jobRow = mysqli_fetch_assoc($jobResult)) {
                $jobs[] = $jobRow;
            }
        }
    }
}


// Print jobs    
$jobsArray = []; // Initialize an array to store job details

foreach ($jobs as $job) {
    $table = $job['job_type']; // Table name
    $jobId = (int) $job['job_id']; // Job ID
    // 1. General Job Validity Checks
    $query = "SELECT * FROM $table 
              WHERE id = $jobId 
              AND deleted_flag = 0 
              AND order_cancel_flag = 0
              AND orderCancelatoin = 0 
              AND jobStatus = 1
              AND (intrpName IS NULL OR intrpName = '')
              AND jobDisp = 1 
              AND is_temp = 0";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 0) {
        continue;
    }

    // 2. Check User's Job Application Status
    $applyCheck = "SELECT bid_type FROM bid 
                   WHERE job = $jobId 
                   AND tabName = '$table' 
                   AND interpreter_id = $interpreter_id";
    $applyResult = mysqli_query($con, $applyCheck);
    
    $bidData = mysqli_fetch_assoc($applyResult);
    $bid_label = "";
    // **Skip job if user already declined it (bid_type = 2)**
    if ($bidData && $bidData['bid_type'] == 2) {
        continue;
    }
    if($bidData && $bidData['bid_type'] == 3){
        $bid_label = "Alternate Availability Given";
    }if($bidData && $bidData['bid_type'] == 1){
        $bid_label = "Applied";
    }

    $jobData = mysqli_fetch_assoc($result);

    // **Duration Formatting**
    $assignDur = $jobData['assignDur'];
    if ($assignDur > 60) {
        $hours = floor($assignDur / 60);
        $mins = $assignDur % 60;
        $durationText = ($mins == 0) ? "$hours Hour(s)" : "$hours Hour(s) $mins Minutes";
    } else {
        $durationText = ($assignDur == 60) ? "1 Hour" : "$assignDur Minutes";
    }
    if ($jobData['job_type'] == 'Translation') {
        $durationText = '---';
    }

    $assignCity = $jobData['assignCity'] ?? 'Nil';
    $postCode = $jobData['postCode'] ?? '';

    if (!empty($postCode)) {
        $assignCity .= " (" . substr($postCode, 0, 3) . ")";
    }

    $jobEntry = [
        "job_type"            => (ucfirst($table) === 'Interpreter' ? 'Face To Face' : ucfirst($table)),
        "company_name"        => $jobData['company_name'] ?? 'TEST',
        "job_id"              => "".$jobId."",
        "job_key"             => $jobData['job_key'] ?? "LSUK/Jul/$jobId",
        "source"              => $jobData['source'],
        "target"              => $jobData['target'],
        "assignDate"          => $misc->dated($jobData['assignDate']),
        "tbl"                 => $table,
        "assignTime"          => substr($jobData['assignTime'], 0, 5) ?? 'Nil',
        "noty"                => "",
        "assignDur"           => $durationText,
        "assignCity"          => $assignCity,
        "document_type"       => $jobData['document_type'] ?? 'no_display',
        "communication_type"  => $jobData['communication_type'] ?? 'no_display',
        "communication_image" => $jobData['communication_image'] ?? 'none',
        "category"            => $jobData['category'] ?? 'General',
        "bid"                 => (int)($bidData['bid_type'] ?? 0),
        "pf"                  => 1
    ];

    // Only add bid_label if it has a value
    if (!empty($bid_label)) {
        $jobEntry["bid_label"] = $bid_label;
    }

    $jobsArray[] = $jobEntry;
}

foreach ($jobs as $job) {
    $table = $job['job_type']; // Table name
    $jobId = (int) $job['job_id']; // Job ID

    // 1. General Job Validity Checks
    $query = "SELECT * FROM $table 
              WHERE id = $jobId 
              AND deleted_flag = 0 
              AND order_cancel_flag = 0
              AND orderCancelatoin = 0 
              AND jobStatus = 1
              AND (intrpName IS NULL OR intrpName = '')
              AND jobDisp = 1 
              AND is_temp = 0";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 0) {
        continue;
    }

    // 2. Check User's Job Application Status
    $applyCheck = "SELECT bid_type FROM bid 
                   WHERE job = $jobId 
                   AND tabName = '$table' 
                   AND interpreter_id = $interpreter_id";
    $applyResult = mysqli_query($con, $applyCheck);
    
    $bidData = mysqli_fetch_assoc($applyResult);

    // **Skip job if user already declined it (bid_type = 2)**
    if ($bidData && $bidData['bid_type'] == 2) {
        continue;
    }

    $jobData = mysqli_fetch_assoc($result);

    // **Format job data into table row**
    $postformat_rows .= "<tr style='background-color:#d9edf7;'>";
    $postformat_rows .= "<td style='text-transform: capitalize;'>" . 
        ($table === 'interpreter' ? 'Face to Face' : $table) . 
    "</td>"; // Job Type
    $postformat_rows .= "<td>{$jobData['source']}</td>"; // Source Language
    $postformat_rows .= "<td>{$jobData['target']}</td>"; // Target Language
    $postformat_rows .= "<td><b>" . (!empty($jobData['assignCity']) ? $jobData['assignCity'] : 'Nil') . "</b></td>"; // City
    $postformat_rows .= "<td>{$misc->dated($jobData['assignDate'])}</td>"; // Assignment Date
    $postformat_rows .= "<td>" . (!empty($jobData['assignTime']) ? $jobData['assignTime'] : 'Nil') . "</td>"; // Assignment Time

    // **Duration Formatting**
    $assignDur = $jobData['assignDur'];
    if ($assignDur > 60) {
        $hours = floor($assignDur / 60);
        $mins = $assignDur % 60;
        $durationText = ($mins == 0) ? "$hours Hour(s)" : "$hours Hour(s) $mins Minutes";
    } else {
        $durationText = ($assignDur == 60) ? "1 Hour" : "$assignDur Minutes";
    }
    $postformat_rows .= "<td>" . ($jobData['job_type'] == 'Translation' ? '---' : $durationText) . "</td>"; // Duration

    // **Action Section**
    $postformat_rows .= "<td>";
    if ($bidData) {
        // User has already bid, show appropriate message
        if ($bidData['bid_type'] == 1) {
            $postformat_rows .= "<span class='btn btn-secondary'>Applied</span>";
        } elseif ($bidData['bid_type'] == 3) {
            $postformat_rows .= "<span class=''>Alternative Availability Given</span>";
        }
    } else {
        // No bid exists, show all options
            $postformat_rows .= "<a class='btn btn-success' onclick='return confirm(\"Are you sure you want to apply on this job?\")' href='jobs.php?val={$table}&tracking={$jobId}&bid_type=1'>Apply</a>
                                <a class='btn btn-info altAvailabilityBtn' 
                                    href='#' 
                                    data-toggle='modal' 
                                    data-target='#altAvailabilityModal' 
                                    data-table='$table'
                                    data-pf='1' 
                                    data-jobid='$jobId'>Alternative Availability</a>
                                <a class='btn btn-danger' onclick='return confirm(\"Are you sure you want to decline this job?\")' href='jobs.php?val={$table}&tracking={$jobId}&bid_type=2'>Decline</a>";

    }
    $postformat_rows .= "</td>";

    $postformat_rows .= "</tr>";
}

// **Convert to JSON if needed**
//$jsonJobs = json_encode($jobsArray, JSON_UNESCAPED_UNICODE);
//echo $jsonJobs; // Output JSON data


if (isset($_POST['pf']) && $_POST['pf'] == 1) {
    require 'db.php'; // Include DB connection if not already included
    // Sanitize and retrieve form inputs
    $jobId = mysqli_real_escape_string($con, $_POST['tracking']);
    $tableName = mysqli_real_escape_string($con, $_POST['val']);
    $message = mysqli_real_escape_string($con, $_POST['alt_message']);
    $alternate_date = mysqli_real_escape_string($con, $_POST['alt_date']);
    $current_date = date("Y-m-d H:i:s");
    // Insert into the `bid` table
    $check_apply = $acttObj->read_specific('id,bid_type,job', 'bid', "job= " . $jobId  . " AND tabName='" . $tableName . "' AND interpreter_id='" . $_SESSION['web_userId'] . "' ");
    if(!$check_apply)
    {
        $query = "INSERT INTO bid (job, tabName, dated, allocated, interpreter_id, bid_type, bid_via, message, alternate_date) 
                VALUES ('$jobId', '$tableName', '$current_date', 0, '$interpreter_id', 3, 1, '$message', '$alternate_date')";
        if (mysqli_query($con, $query)) {
            $msg = "<div class='alert alert-info col-md-6 col-md-offset-3 text-center h4'><b>Alternative Availability submitted successfully!.</b></div>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
        }
    }else{
        $msg = "<div class='alert alert-info col-md-6 col-md-offset-3 text-center h4'><b>Seems like you have already responded to this job.</b></div>";
    }
}
if(!isset($_POST['ap_user_id']) && empty($_POST['ap_user_id'])){
?>

<!-- Modal -->
<div class="modal fade" id="altAvailabilityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Provide Alternative Availability</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="jobs.php">
                    <input type="hidden" name="pf" id="pf" >
                    <input type="hidden" name="tracking" id="modalJobId">
                    <input type="hidden" name="val" id="modalTabName">
                    
                    <div class="form-group">
                        <label for="alt_date">Alternative Date:</label>
                        <input type="datetime-local" name="alt_date" id="alt_date" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="alt_message">Message:</label>
                        <textarea name="alt_message" id="alt_message" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
$(document).on('click', '.altAvailabilityBtn', function() {
    var jobId = $(this).data('jobid');   // Get job ID from clicked button
    var tabName = $(this).data('table'); // Get table name from clicked button
    var pf = $(this).data('pf');
    $('#modalJobId').val(jobId);
    $('#modalTabName').val(tabName);
    $('#pf').val(pf);
});

</script>
<?php
}
?>