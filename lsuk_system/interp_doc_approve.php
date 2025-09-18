<?php
// Sample JSON from DB
session_start();
include 'actions.php';
$table = 'interpreter_reg';
$intrp_id= $_GET['edit_id'];
if (isset($_POST['submit'])) {
    $json = $_POST['pending_approvals'] ?? '[]';
    $decoded = json_decode($json, true);
    if ($decoded === null) {
        die("Invalid JSON received");
    }

    $arr = [];
    $columnsToUpdate = [];

    foreach ($decoded as &$change) {
        foreach ($change['changed_fields'] as $field => $values) {
            if ($change['action'] == 'approved') {
                // Save the new value to DB
                $columnsToUpdate[$field] = $values['new'];
            } elseif (true) {
                // Reset to old value
                $columnsToUpdate[$field] = $values['old'];
            }
            // If still pending → do nothing to the real column
        }
    }

    // Save back JSON log with updated actions/reasons
    $arr['pending_approvals'] = json_encode($decoded, JSON_PRETTY_PRINT);
    // Merge history + column updates
    $finalUpdate = array_merge($arr, $columnsToUpdate);

    // Perform update
    $obj->update($table, $finalUpdate, "id=" . $intrp_id);
    ?>
  <script>
    window.onload = function() {
        window.opener.location.reload(); // refresh parent
        window.close(); // close child
    };
  </script>
<?php
}

$row = $obj->read_specific('*',$table,"id=".$intrp_id);
$json = $row['pending_approvals'] ?? '[]';
$logs = json_decode($json, true);

// Sample JSON from DB
$json  = $logs = $row['pending_approvals'] ?? '[]';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Document Verification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <style>
        .old-value { color: #d9534f;font-size: 11px;}
        .new-value { color: #5cb85c;font-size: 11px; }
        .d-none{ display:none;}
    </style>
</head>
<body class="container">

    <h2>Interpreter Document Verification <b style="color:red"><?= $row['name']." (#".$row['id'].")" ?></b></h2>

    <form id="verificationForm" method="post" action="">
        <input type="hidden" name="pending_approvals" id="pending_approvals">

        <table class="table table-bordered table-striped" id="logTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Changed Fields</th>
                    <th>Status</th>
                    <th>Action By</th>
                    <th>Time</th>
                    <th width='200 px'>Reason</th>
                    <th>Controls</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
    </form>

<script>
let logs = <?php echo $json; ?>;  // existing JSON from PHP
function renderTable() {
    const tbody = document.querySelector("#logTable tbody");
    tbody.innerHTML = "";

    logs.forEach((entry, i) => {
        let row = document.createElement("tr");

        // Map DB keys to readable labels
        const fieldLabels = {
            id_doc_file: "ID Document File",
            id_doc_expiry_date: "ID Document Expiry Date",
            id_doc_issue_date: "ID Document Issue Date",
            work_evid_file: "Work Evidence File",
            right_to_work_no: "Right to Work Number",
            work_evid_issue_date: "Work Evidence Issue Date",
            work_evid_expiry_date: "Work Evidence Expiry Date",
        };

        // Changed fields
        let fields = "";
        for (let field in entry.changed_fields) {
            let vals = entry.changed_fields[field];
            let label = fieldLabels[field] ?? field; // fallback to raw if not mapped

            if (field === "id_doc_file" || field === "work_evid_file") {
                let baseUrl = window.location.origin + "/lsuk_system/file_folder/issue_expiry_docs/";
                let oldFile = vals.old ? `<a href="${baseUrl + vals.old}" target="_blank" class="text-danger">👁 Old</a>` : '';
                let newFile = vals.new ? `<a href="${baseUrl + vals.new}" target="_blank" class="text-success">👁 New</a>` : '';

                fields += `<strong>${label}:</strong><br> 
                            <span class="old-value">${oldFile}</span> → 
                            <span class="new-value">${newFile}</span><br>`;
            } else {
                fields += `<strong>${label}:</strong><br>
                            <span class="old-value">${formatDate(vals.old)}</span> → 
                            <span class="new-value">${formatDate(vals.new)}</span><br>`;
            }
        }

        // Status label
        let labelClass = entry.action === "approved" ? "label-success" :
                        entry.action === "rejected" ? "label-danger" :
                        "label-warning";

        row.innerHTML = `
            <td>${i+1}</td>
            <td>${fields}</td>
            <td><span class="label ${labelClass}">${entry.action.charAt(0).toUpperCase() + entry.action.slice(1)}</span></td>
            <td>${entry.action_by ?? '-'}</td>
            <td>${entry.action_time ?? '-'}</td>
            `;

            if (entry.action === "pending") {
              row.innerHTML += `<td><input type="text" class="form-control input-sm reason-input" 
                    data-index="${i}" value="${entry.reason ?? ''}"></td>`;
            }else{
             row.innerHTML += `<td><div>${entry.reason ?? ''}</div></td>`;
            }
            if (entry.action === "pending") {
            row.innerHTML += `<td>
                <button type="button" class="btn btn-success btn-sm" onclick="approve(${i})">Approve</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="reject(${i})">Reject</button>
                <button type="button" class="btn btn-default btn-sm d-none" onclick="resetStatus(${i})">Reset</button>
            </td>`;
            
            }else {
                row.innerHTML += `<td></td>`;
            }
        tbody.appendChild(row);
    });

}
function formatDate(val) {
    if (!val) return '';
    // match YYYY-MM-DD
    let match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(val);
    if (match) {
        return `${match[3]}-${match[2]}-${match[1]}`; // DD-MM-YYYY
    }
    return val; // return as is if not a date
}

let currentUser = "<?php echo $_SESSION['UserName']; ?>";  // action_by name from session

function approve(index) {
    logs[index].action = "approved";
    logs[index].reason = document.querySelector(`.reason-input[data-index='${index}']`).value.trim();
    logs[index].action_by = currentUser;
    logs[index].action_time = new Date().toISOString();
    renderTable();
}

function reject(index) {
    let reason = document.querySelector(`.reason-input[data-index='${index}']`).value.trim();
    if (!reason) {
        alert("Reason is required for rejection.");
        return;
    }

    logs[index].action = "rejected";
    logs[index].reason = reason;
    logs[index].action_by = currentUser;
    logs[index].action_time = new Date().toISOString();
    renderTable();
}

function resetStatus(index) {
    logs[index].action = "pending";
    logs[index].reason = null;
    renderTable();
}

// Before submitting, inject JSON into hidden input
document.getElementById("verificationForm").addEventListener("submit", function(e) { 
    const a = document.getElementById("pending_approvals").value = JSON.stringify(logs);
    console.log(a);
});

// Initial render
renderTable();
</script>

</body>
</html>
