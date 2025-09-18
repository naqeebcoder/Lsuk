<?php
//php mailer library
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include '../source/setup_email.php';
include 'db.php'; // DB connection
$action = $_GET['action'] ?? '';
$data = [];
function send_invite_email($con, $interpreter, $event, $template_html) {
    
    // Replace placeholders
    $email_body = str_replace(
        ['[LINK]', '[INTERPRETER_NAME]', '[START_DATE]', '[START_TIME]', '[END_DATE]', '[END_TIME]', '[EVENT_NAME]', '[EVENT_TYPE]', '[VENUE]'],
        [
            $interpreter['link'],
            $interpreter['name'],
            date('Y-m-d', strtotime($event['from_date'])),
            date('H:i', strtotime($event['from_date'])),
            date('Y-m-d', strtotime($event['to_date'])),
            date('H:i', strtotime($event['to_date'])),
            $event['title'],
            ucfirst($event['event_type']),
            $event['venue']
        ],
        $template_html
    );
    $emailSubject = "You're Invited: " . $event['title'];

    $data = array(
        "order_id"       => 0,
        "order_type"     => "0",
        "user_id"        => 0,
        "user_type"      => 0,
        "send_from"      => setupEmail::INFO_EMAIL,
        "send_password"  => setupEmail::INFO_PASSWORD,
        "send_to"        => $interpreter['email'],
        "subject"        => $emailSubject,
        "template_type"  => 11,
        "template_data"  => "{}",
        "template_body"  => $email_body,
        "created_date"   => date("Y-m-d H:i:s"),
        "status"         =>0
    );

    $escapedValues = array_map(function($v) use ($con) {
        return "'" . mysqli_real_escape_string($con, $v) . "'";
    }, $data);

    $fields = implode(", ", array_keys($data));
    $values = implode(", ", $escapedValues);

    $insertQuery = "INSERT INTO cron_emails ($fields) VALUES ($values)";
    if (!mysqli_query($con, $insertQuery)) {
        echo "Email queue insert failed: " . mysqli_error($con);
        return false;
    }
    return true;
}

if($_POST['typeBased'] && $_POST['gender'] && $_POST['event_id']){    
    $event_id = $_POST['event_id'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $type = $_POST['typeBased'] ?? null;
    $remarks = mysqli_real_escape_string($con, $_POST['remarks'] ?? '');
    $cities = $_POST['selected_cities'] ?? [];
    $langs = $_POST['selected_langs'] ?? [];

    if (!$event_id || !$gender || !$type) {
        die("Missing required fields.");
    }

    // Filter interpreters based on input
    $conditions = [];
    $joins = [];

    $joins[] = "JOIN interp_lang il ON ir.code = il.code";

    $conditions[] = "ir.deleted_flag = 0";
    $conditions[] = "(TRIM('$gender') = 'both' OR TRIM(ir.gender) = TRIM('$gender'))";

    if (!empty($cities)) {
        $escaped_cities = array_map(fn($c) => "'" . mysqli_real_escape_string($con, $c) . "'", $cities);
        $conditions[] = "ir.city IN (" . implode(',', $escaped_cities) . ")";
    }

    if (!empty($langs)) {
        $escaped_langs = array_map(fn($l) => "'" . mysqli_real_escape_string($con, $l) . "'", $langs);
        $conditions[] = "il.lang IN (" . implode(',', $escaped_langs) . ")";
    }

    if ($type === 'interp') {
        $type_condition = "ir.interp = 'Yes'";
    } elseif ($type === 'telep') {
        $type_condition = "ir.telep = 'Yes'";
    } elseif ($type === 'trans') {
        $type_condition = "ir.trans = 'Yes'";
    } elseif ($type === 'all') {
        $type_condition = "(ir.interp = 'Yes' OR ir.telep = 'Yes' OR ir.trans = 'Yes')";
    } else {
        $type_condition = "1=0";
    }
    $conditions[] = $type_condition;

    $query = "
      SELECT DISTINCT 
          ir.id AS interpreter_id,
          ir.email,
          ir.name,
          e.title,
          e.from_date,
          e.to_date,
          e.venue,
          e.event_type,
          e.invite_template
      FROM interpreter_reg ir
      " . implode("\n", $joins) . "
      JOIN events e ON e.id = $event_id
      WHERE  ir.active = 1 AND " . implode(" AND ", $conditions); 
    $result = mysqli_query($con, $query);
    $created = date('Y-m-d H:i:s');
    $added = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $interpreter_id = $row['interpreter_id'];
        $check = mysqli_query($con, "SELECT id, reply FROM cpd_events WHERE event_id = $event_id AND interpreter_id = $interpreter_id LIMIT 1");
        $shouldSend = false;

        if (!mysqli_num_rows($check)) {
            // First time – insert and send
            $insert = "
                INSERT INTO cpd_events (event_id, interpreter_id, created_date, reply, attend_type, remarks, updated_date)
                VALUES ($event_id, $interpreter_id, '$created', 0, 0, '$remarks', '$created')
            ";
            if (mysqli_query($con, $insert)) {
                $shouldSend = true;
            }
        } else {
            $existing = mysqli_fetch_assoc($check);
            if ($existing['reply'] == 0) {
                // Already in DB but hasn't responded – resend
                $shouldSend = true;
            }
        }

        // Send if allowed
        if ($shouldSend) {
            
            send_invite_email($con, [
                'email' => $row['email'],
                'name'  => $row['name'],
                'link'  => 'lsuk.org/cpd_reply.php?event_id=' . urlencode(base64_encode($event_id)) . '&id=' . urlencode(base64_encode($row['interpreter_id']))
            ], [
                'title'       => $row['title'],
                'from_date'   => $row['from_date'],
                'to_date'     => $row['to_date'],
                'venue'       => $row['venue'],
                'event_type'  => $row['event_type']
            ], $row['invite_template']);
        }
        $added++;
    }
    $mail->smtpClose();
    echo "$added interpreter(s) invited successfully.";
    die();
}
if ($action === 'get_languages' && isset($_POST['cities'])) {
    header('Content-Type: application/json');
    $cities = array_map('trim', $_POST['cities']);
    $cities_list = "'" . implode("','", array_map(function($c) use ($con) {
        return mysqli_real_escape_string($con, $c);
    }, $cities)) . "'";
    
    $query = "
        SELECT DISTINCT il.lang 
        FROM interpreter_reg ir
        JOIN interp_lang il ON ir.code = il.code
        WHERE ir.city IN ($cities_list)
    ";
    $res = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row['lang'];
    }
    echo json_encode($data);
    exit;
}

if ($action === 'get_cities' && isset($_POST['langs'])) {
    header('Content-Type: application/json');
    $langs = array_map('trim', $_POST['langs']);
    $langs_list = "'" . implode("','", array_map(function($l) use ($con) {
        return mysqli_real_escape_string($con, $l);
    }, $langs)) . "'";

    $query = "
        SELECT DISTINCT ir.city 
        FROM interpreter_reg ir
        JOIN interp_lang il ON ir.code = il.code
        WHERE il.lang IN ($langs_list)
    ";
    $res = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row['city'];
    }
    echo json_encode($data);
    exit;
}

$event_id = $_GET['event_id'] ?? null;
$event = null;

if ($event_id) {
    $res = mysqli_query($con, "SELECT id, title FROM events WHERE id = $event_id LIMIT 1");
    $event = mysqli_fetch_assoc($res);
}


$langs = mysqli_query($con, "SELECT DISTINCT lang FROM lang ORDER BY lang");
$cities = mysqli_query($con, "SELECT DISTINCT city FROM interpreter_reg WHERE deleted_flag = 0 ORDER BY city");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invite Users to Event</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
    <style>
        .multiselect {
            min-width: 250px;
        }

        .multiselect-container {
            max-height: 400px;
            overflow-y: auto;
            max-width: 380px;
        }
    </style>
</head>
<body class="container mt-5">

<h2>Invite Interpreters to Event</h2>

<form method="post" action="">
    <div class="mb-3">
        <?php if ($event): ?>
            <div class="form-group">
                <label>Event</label>
                <div class="form-control-plaintext alert alert-info"><?= htmlspecialchars($event['title']) ?></div>
                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
            </div>
        <?php else: ?>
            <div class="alert alert-danger">Invalid or missing event ID.</div>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
                <option value="both">Both</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>

        <div class="form-group col-md-6">
            <label>Type</label>
            <select name="typeBased" class="form-control" required>
                <option value="interp">Interpreter</option>
                <option value="telep">Telephone</option>
                <option value="trans">Translator</option>
                <option value="all">All</option>
            </select>
        </div>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="enableFiltering">
        <label class="form-check-label" for="enableFiltering">
            Enable city/language filtering
        </label>
    </div>
     <div class="form-group col-sm-6" id="div_lang" >
        <label>Select Languages</label>
        <select class="multi_class" id="selected_langs" name="selected_langs[]" class="form-control" multiple>
            <?php
            $res_lang = mysqli_query($con, "SELECT DISTINCT lang FROM lang ORDER BY lang ASC");
            while ($row = mysqli_fetch_assoc($res_lang)) {
                echo "<option value='{$row['lang']}'>{$row['lang']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group col-sm-6" id="div_city" >
        <label>Select Cities</label>
        <select class="multi_class" id="selected_cities" name="selected_cities[]" class="form-control" multiple>
            <?php
            $res_city = mysqli_query($con, "SELECT DISTINCT city FROM interpreter_reg WHERE deleted_flag = 0 ORDER BY city ASC");
            while ($row = mysqli_fetch_assoc($res_city)) {
                echo "<option value='{$row['city']}'>{$row['city']}</option>";
            }
            ?>
        </select>
    </div>

    <button type="submit" class="btn btn-success mt-3">Filter & Invite Users</button>
</form>
        <script src="js/jquery-1.11.3.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js" type="text/javascript"></script>
<script>
$(function () {
    function preserveSelections($select, validOptions) {
        const selected = $select.val() || [];
        $select.empty();

        validOptions.forEach(opt => {
            const isSelected = selected.includes(opt);
            $select.append(`<option value="${opt}" ${isSelected ? 'selected' : ''}>${opt}</option>`);
        });

        $select.multiselect('rebuild');
    }
    function isFilteringEnabled() {
        return $('#enableFiltering').is(':checked');
    }

    $('#selected_cities').multiselect({
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        onChange: function () {
            if (!isFilteringEnabled()) return;
            const cities = $('#selected_cities').val();
            if (!cities || cities.length === 0) return;

            $.ajax({
                type: 'POST',
                url: 'invite_event_form.php?action=get_languages',
                data: { cities },
                dataType: 'json',
                success: function (langs) {
                    preserveSelections($('#selected_langs'), langs);
                },
                error: function (xhr) {
                    console.error('Language load failed:', xhr.responseText);
                }
            });
        }
    });

    $('#selected_langs').multiselect({
        includeSelectAllOption: true,
        numberDisplayed: 1,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        onChange: function () {
            if (!isFilteringEnabled()) return;
            const langs = $('#selected_langs').val();
            if (!langs || langs.length === 0) return;

            $.ajax({
                type: 'POST',
                url: 'invite_event_form.php?action=get_cities',
                data: { langs },
                dataType: 'json',
                success: function (cities) {
                    preserveSelections($('#selected_cities'), cities);
                },
                error: function (xhr) {
                    console.error('City load failed:', xhr.responseText);
                }
            });
        }
    });
});

</script>
<script>
$(function() {
                    $('.multi_class').multiselect({
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        enableFiltering: true,
                        enableCaseInsensitiveFiltering: true
                    });
                });
</script>
</body>
</html>
