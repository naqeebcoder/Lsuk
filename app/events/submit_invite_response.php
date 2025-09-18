<?php
require '../db.php';

$event_id       = (int)$_POST['event_id'];
$interpreter_id = (int)$_POST['interpreter_id'];
$reply          = (int)$_POST['response']; // 1 = Attend, 2 = Not Attend
$remarks        = mysqli_real_escape_string($con, $_POST['remarks']);

if ($event_id && $interpreter_id && in_array($reply, [1, 2])) {
    $now = date("Y-m-d H:i:s");

    $update = "
        UPDATE cpd_events 
        SET reply = $reply, attend_type = 0, remarks = '$remarks', updated_date = '$now'
        WHERE event_id = $event_id AND interpreter_id = $interpreter_id
    ";

    if (mysqli_query($con, $update)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => mysqli_error($con)]);
    }
}else{
  echo json_encode(['success' => false]);
}
