<?php
// DB connection

include 'db.php';
include 'class.php';
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
$events = $acttObj->read_all("events.*","events","1=1");
?>

<!doctype html>
<html lang="en">
<head>
<title>CPD Events</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css" />
<style>html,body{background:none !important;}
.badge-counter{border-radius: 0px!important;margin: -9px -9px!important;font-size: 10px;float: left;}
.pagination>.active>a{
    background:#337ab7;
}</style>
</head>
<?php include 'header.php';?>
<body>
<?php include 'nav2.php';?>
<section class="container-fluid" style="overflow-x:auto">
    <div class="d-flex justify-content-between mb-3">
        <h2>All Events</h2>
        <a href="javascript:void(0);" onclick="popupwindow('edit_event.php', 'Add New Event', 1250, 730);" class="btn btn-primary" style="color:white !important">Add New Event</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th><th>Title</th><th>From</th><th>To</th><th>Venue</th><th>Last Modified</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $events->fetch_assoc()) {
            // Try updated_by first
            $byData = json_decode($row['updated_by'], true);
            $name = $byData['name'] ?? null;
            $datetime = $row['updated_at'] ?? null;

            // Fallback to created_by if no update info
            if (!$name || !$datetime) {
                $byData = json_decode($row['created_by'], true);
                $name = $byData['name'] ?? '—';
                $datetime = $row['create_at'] ?? '—';
            }
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['from_date'] ?></td>
                <td><?= $row['to_date'] ?></td>
                <td><?= htmlspecialchars($row['venue']) ?></td>
                <td><?= htmlspecialchars($name) ?><br><small><?= $datetime ?></small></td>
                <td style="color:white !important">
                    <a style="color:white !important" href="javascript:void(0);" onclick="popupwindow('edit_event.php?id=<?= $row['id'] ?>', 'Edit Event', 1250, 730);" class="btn btn-sm btn-warning">Edit</a>

                    <a style="color:white !important" href="javascript:void(0);" onclick="popupwindow('invite_event_form.php?event_id=<?= $row['id'] ?>', 'Invite Event', 1250, 730);" class="btn btn-sm btn-primary">Invite</a>

                    <a style="color:white !important" href="events.php?event_id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Responses</a>
                </td>
            </tr>
        <?php } ?>

        </tbody>
    </table>
	</section>
</body>
</html>
