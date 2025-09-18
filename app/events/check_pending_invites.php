<?php
require '../db.php'; // include DB connection

$interpreter_id = isset($_GET['interpreter_id']) ? (int)$_GET['interpreter_id'] : 0;

if ($interpreter_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid interpreter ID']);
    exit;
}

$query = "
    SELECT e.id, e.title,e.description, e.from_date, e.to_date, e.venue
    FROM cpd_events ce
    JOIN events e ON ce.event_id = e.id
    WHERE ce.interpreter_id = $interpreter_id
      AND ce.reply = 0
      AND e.from_date > NOW()
    ORDER BY e.from_date ASC LIMIT 1
";

$result = mysqli_query($con, $query);

$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

header('Content-Type: application/json');
echo json_encode($events);
