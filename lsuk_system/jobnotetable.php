<?php
$table = 'jobnotes';

// Show shifted job notes only if not translation
if ($tbl != "translation") {
  if ($tbl == "interpreter") {
    $get_shifted_from = $obj->read_specific("shifted_from", "global_reference_no", "is_shifted=1 AND table_type=2 AND shifted_to=" . $fid)['shifted_from'];
    $old_notes_table = "telephone";
    $old_notes_label = "Telephone";
  } else {
    $get_shifted_from = $obj->read_specific("shifted_from", "global_reference_no", "is_shifted=1 AND table_type=1 AND shifted_to=" . $fid)['shifted_from'];
    $old_notes_table = "interpreter";
    $old_notes_label = "Face To Face";
  }
  if (!empty($get_shifted_from)) {
  $get_shifted_job_notes = $obj->read_all("*", $table, "tbl='$old_notes_table' and fid=$get_shifted_from and deleted_flag=0 order by time desc");
  if ($get_shifted_job_notes->num_rows > 0) {
  ?>
    <div class="panel-group">
      <div class="panel panel-danger">
        <div class="panel-heading">
          <h4 class="panel-title">
            <button type="button" class="btn btn-danger" data-toggle="collapse" href="#collapse1"><b>View Shifted Job Notes</b></button>
          </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse">
          <div class="panel-body">
            <table style="font-size: 14px;" class="table table-bordered">
              <tr class="bg-danger">
                <th align="left">Shifted Old Job Note</th>
                <th width="140" align="left">Note For</th>
                <th align="left" width="22%">Submitted By / Time</th>
              </tr>
              <?php
              $rateChangeNotes = [];
              $normalNotes = [];
              while ($row_old = $get_shifted_job_notes->fetch_assoc()) {
                  // Check if 'jobNote' contains the hidden span with class 'rate_change'
                  if (preg_match('/<span.*class=["\']rate_change["\'].*>.*<\/span>/', $row_old['jobNote'])) {
                      $rateChangeNotes[] = $row_old;
                     
                  } else {
                      $normalNotes[] = $row_old;
                  }
              }

              $allNotes = array_merge($rateChangeNotes, $normalNotes);

              foreach ($allNotes as $row_old) {
                  $notesreader_old = is_numeric($row_old['notesread']) ? ucwords($obj->read_specific("name", "login", "id=" . $row_old['notesread'])['name']) : $row_old['notesread'];
                  ?>
                  <tr <?= $row_old['readcount'] == 0 ? "class='bg-success'" : "" ?>>
                      <td align="left"><?php echo $row_old['jobNote']; ?> </td>
                      <td align="left"><?php echo $notesreader_old ?: "For All"; ?> </td>
                      <td align="left"><?php echo ucwords($row_old['submitted']) . "<br><small>" . $row_old['time'] . "</small>"; ?> </td>
                  </tr>
              <?php } ?>


            </table>
          </div>
          <div class="panel-footer text-danger">These job notes were fetched from the shifted <?=$old_notes_label?> copy of this current <?=$array_job_types[$tbl]?> job</div>
        </div>
      </div>
    </div>
  <?php }
  }
}
// End shifted job notes
$result = $obj->read_all(
              "*",
              $table,
              "tbl='$tbl' AND fid=$fid AND deleted_flag=0 
              ORDER BY 
                  jobNote REGEXP '<span[^>]*class=[\"'']?rate_change[\"'']?[^>]*>.*</span>' DESC,
                  time DESC"
          );
if ($result->num_rows == 0) {
  echo "<h1 class='text-center'><span class='label label-danger'>There are no job notes currently !</span></h1>";
} else { ?>
  <table style="font-size: 14px;" data-countis="<?php echo $nCountIs; ?>" class="table table-bordered">
    <tr class="bg-primary">
      <th align="left">Job Note </th>
      <th width="140" align="left">Note For </th>
      <th align="left" width="22%">Submitted By / Time</th>
      <th align="left" width="12%"> Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) {
      $notesreader = is_numeric($row['notesread']) ? ucwords($obj->read_specific("name", "login", "id=" . $row['notesread'])['name']) : $row['notesread'];
    ?>
      <tr <?= $row['readcount'] == 0 ? "class='bg-success'" : "" ?>>
        <td align="left"><?php echo $row['jobNote']; ?> </td>
        <td align="left"><?php echo $notesreader ?: "For All"; ?> </td>
        <?php $strRowId = $row['id']; ?>
        <td align="left"><?php echo ucwords($row['submitted']) . "<br><small>" . $row['time'] . "</small>"; ?> </td>
        <td align="left">
          <?php if (!is_numeric($row['notesread']) || (is_numeric($row['notesread']) && $row['notesread'] == $_SESSION['userId']) || $row['submitted'] == $_SESSION['UserName'] || $_SESSION['is_root'] == 1) { ?>
            <a class="btn btn-danger btn-sm" href="javascript:void(0)" onclick="if(confirm('Are you sure to delete?')){delete_job_note(<?= $row['id'] ?>);}">
              <i class="glyphicon glyphicon-trash"></i>
            </a>
            <a class="btn btn-info btn-sm" href="javascript:void(0)" onclick="DoReadNote(<?= $fid . ',' . $tbl . ',' . $strRowId . ',' . $row['readcount']; ?>)">
              <i class="glyphicon glyphicon-refresh"></i>
            </a>
          <?php } ?>
        </td>
      </tr>

    <?php } ?>
  </table>

<?php }

// Fetch deleted job notes
$deletedNotes = $obj->read_all(
    "*",
    $table,
    "tbl='$tbl' AND fid=$fid AND deleted_flag=1 
    ORDER BY 
        jobNote REGEXP '<span[^>]*class=[\"'']?rate_change[\"'']?[^>]*>.*</span>' DESC,
        time DESC"
);
if($_SESSION['is_root'] == 1){
  if ($deletedNotes->num_rows > 0) { ?>
      <h3 class="text-center text-danger" style="margin-top:30px;">
          Deleted Job Notes
      </h3>
      <table style="font-size: 14px;" class="table table-bordered table-striped">
          <tr class="bg-danger">
              <th align="left">Job Note</th>
              <th width="140" align="left">Note For</th>
              <th align="left" width="22%">Submitted By / Time</th>
              <th align="left" width="22%">Deleted By</th>
          </tr>
          <?php while ($row = $deletedNotes->fetch_assoc()) {
            // Get deleted by name from login table
              $deletedByName = !empty($row['deleted_by']) && is_numeric($row['deleted_by'])
                  ? ucwords($obj->read_specific("name", "login", "id=" . $row['deleted_by'])['name'])
                  : "Unknown";
              $notesreader = is_numeric($row['notesread']) 
                  ? ucwords($obj->read_specific("name", "login", "id=" . $row['notesread'])['name']) 
                  : $row['notesread'];
          ?>
              <tr class="bg-light text-muted">
                  <td align="left"><?php echo $row['jobNote']; ?> </td>
                  <td align="left"><?php echo $notesreader ?: "For All"; ?> </td>
                  <td align="left"><?php echo ucwords($row['submitted']) . "<br><small>" . $row['time'] . "</small>"; ?> </td>
                  <td align="left"><small><?php echo $deletedByName; ?></small></td>
              </tr>
          <?php } ?>
      </table>
  <?php } ?>



<?php } ?>