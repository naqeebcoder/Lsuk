<?php
// DB connection
include 'db.php';
include 'class.php';
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $from = $_POST['from_date'] . ' ' . $_POST['from_time'] . ':00';
    $to = $_POST['to_date'] . ' ' . $_POST['to_time'] . ':00';
    $desc = $_POST['description'];
    $template = $_POST['invite_template'];
    $venue = $_POST['venue'];
    $type = $_POST['event_type'];

    $userId = $_SESSION['userId'];
    $userName = $_SESSION['UserName'];
    $userJson = json_encode(['id' => $userId, 'name' => $userName]);

    if ($_POST['id']) {
        $stmt = $con->prepare("
            UPDATE events 
            SET title = ?, from_date = ?, to_date = ?, description = ?, invite_template = ?, event_type = ?, venue = ?, updated_by = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("ssssssssi", $title, $from, $to, $desc, $template, $type, $venue, $userJson, $_POST['id']);
    } else {
        $stmt = $con->prepare("
            INSERT INTO events (title, from_date, to_date, description, invite_template, event_type, venue, created_by, create_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("ssssssss", $title, $from, $to, $desc, $template, $type, $venue, $userJson);
    }
    $stmt->execute();
    header("Location: events_list.php");
    exit;
}

$event = ['title'=>'', 'from_date'=>'', 'to_date'=>'', 'description'=>'', 'invite_template'=>'', 'event_type'=>'', 'venue'=>''];
if ($id) {
    $res = $acttObj->read_all("events.*","events","id=$id");
    $event = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $id ? 'Edit Event' : 'Add Event' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.9.2/ckeditor.js" integrity="sha512-OF6VwfoBrM/wE3gt0I/lTh1ElROdq3etwAquhEm2YI45Um4ird+0ZFX1IwuBDBRufdXBuYoBb0mqXrmUA2VnOA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body class="container mt-5">

    <h2><?= $id ? 'Edit' : 'Add New' ?> Event</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="mb-3"><label>Title</label><input class="form-control" name="title" value="<?= htmlspecialchars($event['title']) ?>" required></div>
        <?php
        // Split datetime into date and time
        $from_date = $event['from_date'] ? date('Y-m-d', strtotime($event['from_date'])) : date('Y-m-d');
        $from_time = date('H:i', strtotime($event['from_date']));
        $to_date = $event['to_date'] ? date('Y-m-d', strtotime($event['to_date'])) : date('Y-m-d');
        $to_time = date('H:i', strtotime($event['to_date']));
        ?>

        <div class="mb-3 row">
            <div class="col">
                <label>From Date</label>
                <input type="date" class="form-control" name="from_date" value="<?= $from_date ?>" required>
            </div>
            <div class="col">
                <label>From Time</label>
                <input type="time" class="form-control" name="from_time" value="<?= $from_time ?>" required>
            </div>
        </div>

        <div class="mb-3 row">
            <div class="col">
                <label>To Date</label>
                <input type="date" class="form-control" name="to_date" value="<?= $to_date ?>" required>
            </div>
            <div class="col">
                <label>To Time</label>
                <input type="time" class="form-control" name="to_time" value="<?= $to_time ?>" required>
            </div>
        </div>
        <div class="mb-3"><label>Venue</label><input class="form-control" name="venue" value="<?= htmlspecialchars($event['venue']) ?>"></div>
        <div class="mb-3">
            <label>Event Type</label>
            <select class="form-control" name="event_type" required>
                <option value="onsite" <?= $event['event_type'] === 'on-site' ? 'selected' : '' ?>>Onsite</option>
                <option value="remote" <?= $event['event_type'] === 'remote' ? 'selected' : '' ?>>Remote</option>
            </select>
        </div>

        <div class="mb-3"><label>Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($event['description']) ?></textarea>
            <script>CKEDITOR.replace('description');</script>
        </div>

        <div class="mb-3"><label>Email Template</label>
        <div class="alert alert-secondary">
            <strong>Available Placeholders:</strong>
            <ul class="mb-0">
                <li><code>[LINK]</code> – Invitation link</li>
                <li><code>[INTERPRETER_NAME]</code> – Interpreter's full name</li>
                <li><code>[START_DATE]</code> – Event start date (e.g., 2025-06-25)</li>
                <li><code>[START_TIME]</code> – Event start time (e.g., 14:30)</li>
                <li><code>[END_DATE]</code> – Event end date (e.g., 2025-06-25)</li>
                <li><code>[END_TIME]</code> – Event end time (e.g., 16:00)</li>
                <li><code>[EVENT_NAME]</code> – Title of the event</li>
                <li><code>[EVENT_TYPE]</code> – Event type (e.g., Remote or Onsite)</li>
                <li><code>[VENUE]</code> – Event venue or location</li>
            </ul>
        </div>
            <textarea name="invite_template" class="form-control"><?= htmlspecialchars($event['invite_template']) ?></textarea>
            <script>CKEDITOR.replace('invite_template');</script>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="events.php" class="btn btn-secondary">Cancel</a>
    </form>

</body>
</html>
