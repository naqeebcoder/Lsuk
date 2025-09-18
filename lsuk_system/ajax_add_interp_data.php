<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/vendor/autoload.php';
require '../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

if (isset($_POST['id']) && isset($_POST['view_reference'])) {
    include 'db.php';
    include 'class.php';
    $query_ref = $acttObj->read_all("*", "int_references", "int_id=" . $_POST['id']);
    if ($query_ref->num_rows > 0) {
        $counter = 1;
        $tbl = '<table class="table table-bordered">
        <tbody><tr class="bg-primary text-center">
            <td colspan="7"><b>Referee(s) Details</b></td></tr>
            <tr>
            <td>S.No</td><td><b>Name</b></td><td><b>Relationship</b></td>
            <td><b>Company</b></td><td><b>Email</b></td><td><b>Phone</b></td><td><b>Status</b></td>
            <tr>';
        $arr_stars = array("-5" => "Poor", "0" => "Bad", "5" => "Fair", "10" => "Good", "15" => "Excellent");
        while ($row_ref = $query_ref->fetch_assoc()) {
            $append_lateness = $row_ref['lateness'] > 0 ? 'Late at ' . $row_ref['lateness'] . ' ocassions' : "No Lateness";
            $append_missed = $row_ref['missed'] > 0 ? $row_ref['missed'] . ' jobs missed' : "No missed jobs";
            $status = $row_ref['status'] == 0 ? "Not verified" : "Verified";
            $view_ref_btn = $row_ref['status'] != 0 ? '<br><a data-toggle="collapse" data-target="#view_ref_' . $row_ref['id'] . '" title="View Details" href="javascript:void(0)" class="w3-button w3-small w3-circle w3-white w3-border w3-border-blue"><i class="fa fa-eye"></i></a>' : "";
           $tbl .= '<tr id="ref_row_' . $row_ref['id'] . '">
                <td>' . $counter++ . '</td>
                <td class="ref-name">' . $row_ref['name'] . '</td>
                <td class="ref-relation">' . $row_ref['relation'] . '</td>
                <td class="ref-company">' . $row_ref['company'] . '</td>
                <td class="ref-email">' . $row_ref['email'] . '</td>
                <td class="ref-phone">' . $row_ref['phone'] . '</td>
                <td align="center">' . $status . $view_ref_btn . '
                    <br>
                    <button class="btn btn-xs btn-warning edit-ref-btn"
                        data-id="' . $row_ref['id'] . '"
                        data-name="' . htmlspecialchars($row_ref['name']) . '"
                        data-relation="' . htmlspecialchars($row_ref['relation']) . '"
                        data-company="' . htmlspecialchars($row_ref['company']) . '"
                        data-email="' . htmlspecialchars($row_ref['email']) . '"
                        data-phone="' . htmlspecialchars($row_ref['phone']) . '">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                </td>
            </tr>';

            // Inject toggleable edit form row
            $tbl .= '<tr class="edit-form-row" id="edit_row_' . $row_ref['id'] . '" style="display:none;">
                <td colspan="7">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <strong>Edit Reference</strong>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12" id="edit_ref_form_container_' . $row_ref['id'] . '">
                                <form id="edit_ref_form_' . $row_ref['id'] . '" class="edit-ref-form">
                                    <input type="hidden" name="edit_ref_id" value="' . $row_ref['id'] . '">
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Name</label>
                                            <input type="text" name="name" value="" class="form-control edit-name" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Relationship</label>
                                            <input type="text" name="relation" value="" class="form-control edit-relation" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Company</label>
                                            <input type="text" name="company" value="" class="form-control edit-company" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Email</label>
                                            <input type="email" name="email" value="" class="form-control edit-email" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Phone</label>
                                            <input type="text" name="phone" value="" class="form-control edit-phone" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>&nbsp;</label><br>
                                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                </td>
            </tr>
            <tr class="collapse" id="view_ref_' . $row_ref['id'] . '">
                <td colspan="7">
                    <table class="table table-bordered"><tbody>
                        <tr class="bg-info">
                            <td>Submitted By </td>
                            <td colspan="3"><b>' . $row_ref['submit_name'] . '</b><br>' . $row_ref['submit_email'] . '</td>
                            <td>Submitted Date </td>
                            <td colspan="3">' . $row_ref['submit_date'] . '</td>
                        </tr>
                        <tr>
                            <td>Punctuality </td>
                            <td>' . $arr_stars[$row_ref['punctuality']] . '</td>
                            <td>Appearance</td>
                            <td>' . $arr_stars[$row_ref['appearance']] . '</td>
                            <td>Professionalism </td>
                            <td>' . $arr_stars[$row_ref['professionalism']] . '</td>
                            <td>Confidentiality</td>
                            <td>' . $arr_stars[$row_ref['confidentiality']] . '</td>
                        </tr>
                        <tr>
                            <td>Impartiality </td>
                            <td>' . $arr_stars[$row_ref['impartiality']] . '</td>
                            <td>Accuracy</td>
                            <td>' . $arr_stars[$row_ref['accuracy']] . '</td>
                            <td>Rapport </td>
                            <td>' . $arr_stars[$row_ref['rapport']] . '</td>
                            <td>Communication</td>
                            <td>' . $arr_stars[$row_ref['communication']] . '</td>
                        </tr>
                        <tr>
                            <td>Latnesses </td>
                            <td colspan="3">' . $append_lateness . '</td>
                            <td>Missed Jobs</td>
                            <td colspan="3">' . $append_missed . '</td>
                        </tr>
                        <tr>
                            <td>Remarks</td>
                            <td colspan="7">' . $row_ref['remarks'] . '</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>';
        }
        $tbl .= '</tbody></table>';
        echo $tbl;
    } else {
        echo '<center><h3>No references found for this interpreter!</h3></center>';
    }
}

/**  Updating Reference **/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ref_id']) && $_POST['update_ref_id'] == 1) {
    include 'db.php';
    include 'class.php';
    $ref_id = intval($_POST['edit_ref_id']);
    $name = $con->real_escape_string($_POST['name']);
    $relation = $con->real_escape_string($_POST['relation']);
    $company = $con->real_escape_string($_POST['company']);
    $email = $con->real_escape_string($_POST['email']);
    $phone = $con->real_escape_string($_POST['phone']);

    $sql = "UPDATE int_references SET 
                name = '$name',
                relation = '$relation',
                company = '$company',
                email = '$email',
                phone = '$phone'
            WHERE id = $ref_id";

    header('Content-Type: application/json');

    if ($con->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => $con->error
        ]);
    }
} 

if (isset($_POST['addnotes']) && isset($_POST['id']) && isset($_POST['notes'])) {
    include 'db.php';
    include 'class.php';
    $update_array = [];
    $update_array['week_remarks'] = $_POST['notes'];
    $acttObj->update('interpreter_reg', $update_array, array("id" => $_POST['id']));
    echo '1';
}
if (isset($_POST['option']) && isset($_POST['id']) && isset($_POST['update_action_interpreter'])) {
    include 'db.php';
    include 'class.php';
    $update_array = array();
    if ($_POST['update_action_interpreter'] == "specific_agreed") {
        $update_array['specific_agreed'] = $_POST['option'];
    } else {
        $update_array['availability_option'] = $_POST['option'];
    }
    $acttObj->update('interpreter_reg', $update_array, array("id" => $_POST['id']));
    if ($_POST['option'] == 1) {
        echo '1';
    } else {
        echo '0';
    }
}
if (isset($_POST['datetime']) && isset($_POST['duration']) && $_POST['val'] == 'dur_finder') {
    $dt = $_POST['datetime'];
    $dur = $_POST['duration'];
    function get_endtime($datetime, $duration)
    {
        $input_time = date($datetime);
        list($a, $b) = explode(':', $duration);
        $minutes = $a * 60 + $b;
        $newTime = date("d/m/Y H:i", strtotime("+$minutes minutes", strtotime($input_time)));
        return  $newTime;
    }
    echo get_endtime($dt, $dur);
}
if (isset($_POST['action']) && $_POST['action'] == 'job_confirmation' && isset($_POST['type'])) {
    include 'db.php';
    include 'class.php';
    $table = $_POST['type'];
    $source = $_POST['source'];
    $target = $_POST['target'];
    $orgName = $_POST['orgName'];
    $assignDate = $_POST['assignDate'];
    $assignTime = $_POST['assignTime'];
    $orgRef = $_POST['orgRef'];
    $orgContact = $_POST['orgContact'];
    if ($table == 'interpreter') {
        $columns = "nameRef,orgRef,orgContact,source,target,CONCAT(assignDate,' ',assignTime) as assignDate,assignDur,interp_cat,interp_type,assignIssue";
        $where = "source='$source' and target='$target' and assignDate='$assignDate' and assignTime='$assignTime' and orgName='$orgName' and orgContact LIKE '%$orgContact%' and orgRef LIKE '%$orgRef%'";
    } else if ($table == 'telephone') {
        $columns = "nameRef,orgRef,orgContact,source,target,CONCAT(assignDate,' ',assignTime) as assignDate,assignDur,assignIssue,telep_cat,telep_type";
        $where = "source='$source' and target='$target' and assignDate='$assignDate' and assignTime='$assignTime' and orgName='$orgName' and orgContact LIKE '%$orgContact%' and orgRef LIKE '%$orgRef%'";
    } else {
        $columns = "orgRef,orgContact,nameRef,source,target,asignDate as assignDate,docType,transType";
        $where = "source='$source' and target='$target' and asignDate='$assignDate' and orgName='$orgName' and orgContact LIKE '%$orgContact%' and orgRef LIKE '%$orgRef%'";
    }
    $query = $acttObj->read_all("$columns", "$table", "$where");
    if (mysqli_num_rows($query) == 0) {
        echo '';
    } else { ?>
        <table class="table table-bordered">
            <thead class="bg-info">
                <th>Project ID</th>
                <th>Org.Ref</th>
                <th>Org.Contact</th>
                <th>Source</th>
                <th>Target</th>
                <th>Job Date</th>
                <?php if ($table != 'translation') { ?><th>Duration</th><?php } ?>
                <th>Category</th>
                <th>Details</th>
            </thead>
            <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?php echo $row['nameRef']; ?></td>
                    <td><?php echo $row['orgRef']; ?></td>
                    <td><?php echo $row['orgContact']; ?></td>
                    <td><?php echo $row['source']; ?></td>
                    <td><?php echo $row['target']; ?></td>
                    <td><?php echo $row['assignDate']; ?></td>
                    <?php if ($table != 'translation') {
                        if ($row['assignDur'] > 60) {
                            $hours = $row['assignDur'] / 60;
                            if (floor($hours) > 1) {
                                $hr = "hours";
                            } else {
                                $hr = "hour";
                            }
                            $mins = $row['assignDur'] % 60;
                            if ($mins == 00) {
                                $get_dur = sprintf("%2d $hr", $hours);
                            } else {
                                $get_dur = sprintf("%2d $hr %02d minutes", $hours, $mins);
                            }
                        } else if ($row['assignDur'] == 60) {
                            $get_dur = "1 Hour";
                        } else {
                            $get_dur = $row['assignDur'] . " minutes";
                        }
                        echo "<td>" . $get_dur . "</td>";
                    } ?>
                    <?php if ($table == "translation") { ?>
                        <td><?php echo $acttObj->read_specific("tc_title", "trans_cat", "tc_id=" . $row['docType'])['tc_title']; ?></td>
                    <?php } else { ?>
                        <td class="text-danger"><?php echo $table == 'telephone' ? $acttObj->read_specific("tpc_title", "telep_cat", "tpc_id=" . $row['telep_cat'])['tpc_title'] : $acttObj->read_specific("ic_title", "interp_cat", "ic_id=" . $row['interp_cat'])['ic_title'] ?></td>
                    <?php } ?>
                    <?php if ($table == "translation") { ?>
                        <td><?php echo $acttObj->read_specific("CONCAT(GROUP_CONCAT(CONCAT('{',td_title)  SEPARATOR '} '),'}') as td_title", "trans_dropdown", "td_id IN (" . $row['transType'] . ")")['td_title']; ?></td>
                    <?php } else { ?>
                        <td class="text-danger"><?php if ($table == 'telephone') {
                                                    echo $row['telep_cat'] == '11' ? $row['assignIssue'] : $acttObj->read_specific("GROUP_CONCAT(CONCAT(tpt_title)  SEPARATOR ' <b> & </b> ') as tpt_title", "telep_types", "tpt_id IN (" . $row['telep_type'] . ")")['tpt_title'];
                                                } else {
                                                    echo $row['interp_cat'] == '12' ? $row['assignIssue'] : $acttObj->read_specific("GROUP_CONCAT(CONCAT(it_title)  SEPARATOR ' <b> & </b> ') as it_title", "interp_types", "it_id IN (" . $row['interp_type'] . ")")['it_title'];
                                                } ?></td>
                    <?php } ?>
                </tr>
            <?php }
        }
    }
    if (isset($_POST['code']) && isset($_POST['lang']) && isset($_POST['level'])) {
        include 'db.php';
        include 'class.php';
        $table = 'interp_lang';
        $code = $_POST['code'];
        $lang = $_POST['lang'];
        $level = $_POST['level'];
        $dated = date('Y-m-d');
        $mod = $_POST['interpreting_type']; //echo "<pre>"; print_r($_POST); echo "</pre>";
        $dated = date('Y-m-d');
        if (!empty($mod) && count($mod) > 0) {
            foreach ($mod as $key => $value) {
                $data = array(
                    'code' => $code,
                    'lang' => $lang,
                    'level' => $level,
                    'type' => $value,
                    'dated' => $dated
                );
                $existing_id = $acttObj->read_specific('id', 'interp_lang', "code='$code' AND type = '$value' AND lang='$lang'")['id'];
                if (!empty($existing_id)) {
                    $acttObj->update($table, $data, "id=" . $existing_id);
                } else {
                    $acttObj->insert($table, $data);
                }
                $interpreter_data[] = $value;
            }
            $interpreter_update_array = [];
            if (in_array('interp', $interpreter_data)) {
                $interpreter_update_array['interp'] = 'Yes';
            }
            if (in_array('telep', $interpreter_data)) {
                $interpreter_update_array['telep'] = 'Yes';
            }
            if (in_array('trans', $interpreter_data)) {
                $interpreter_update_array['trans'] = 'Yes';
            }
            $acttObj->update("interpreter_reg", $interpreter_update_array, "code='" . $code . "'");
        }
        $langs_q = $acttObj->read_all('DISTINCT id,lang,level,`type`', 'interp_lang', "code='$code' ORDER BY lang ASC");
        if (mysqli_num_rows($langs_q) == 0) {
            echo '<span class="badge badge-primary">No Languages Currently!</span>';
        } else {
            // $level_1=array();
            // $level_2=array();
            $levelz = array("1" => "Native", "2" => "Fluent", "3" => "Intermediate", "4" => "Basic");

            $sql1 = "SELECT DISTINCT lang FROM interp_lang where code='$code'   ORDER BY id ASC";
            $result_1 = mysqli_query($con, $sql1);
            $row_1 = mysqli_fetch_all($result_1);
            foreach ($row_1 as $row_1) : ?>
                <tr>
                    <td>
                        <?php $lang =  $acttObj->read_specific('id,lang,level', 'interp_lang', " lang='" . $row_1[0] . "' AND code = '" . $code . "' AND type ='interp'");
                        $level = $lang['level'];
                        $lang_name = $lang['lang'];
                        $lang_id = $lang['id'];

                        $Level = ($lang_name != "") ? " | " . $levelz[$level] : "";
                        $language =  $lang_name  . $Level;
                        ?>
                        <?php if ($language != "") : ?>
                            <span class="badge badge-primary rm" <?php
                                                                    if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Operator') { ?> title="Double Click to remove this language!" id="<?php echo $lang_id . '|' . $code; ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php  } ?>>
                                <?php echo $language; ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $lang =  $acttObj->read_specific('id,lang,level', 'interp_lang', " lang='" . $row_1[0] . "' AND code = '" . $code . "' AND type ='telep'");
                        $level = $lang['level'];
                        $lang_name = $lang['lang'];
                        $lang_id = $lang['id'];

                        $Level = ($lang_name != "") ? " | " . $levelz[$level] : "";
                        $language =  $lang_name  . $Level;
                        ?>
                        <?php if ($lang != "") : ?>
                            <span class="badge badge-primary rm" <?php
                                                                    if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Operator') { ?> title="Double Click to remove this language!" id="<?php echo $lang_id . '|' . $code; ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php  } ?>>
                                <?php echo $language; ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $lang = $acttObj->read_specific('id,lang,level', 'interp_lang', " lang='" . $row_1[0] . "' AND code = '" . $code . "' AND type ='trans'");
                        $level = $lang['level'];
                        $lang_name = $lang['lang'];
                        $lang_id = $lang['id'];

                        $Level = ($lang_name != "") ? " | " . $levelz[$level] : "";
                        $language = $lang_name  . $Level;

                        ?>
                        <?php if ($language != "") : ?>
                            <span class="badge badge-primary rm" <?php
                                                                    if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Operator') { ?> title="Double Click to remove this language!" id="<?php echo $lang_id . '|' . $code; ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php  } ?>>

                                <?php echo $language ?>
                            </span>
                        <?php endif; ?>
                    </td>

                </tr>
            <?php endforeach; ?>

            <?php
            // while($row_lang = mysqli_fetch_array($langs_q)){
            //     if($row_lang['level']<3){
            //         array_push($level_1,$row_lang);
            //     }else{
            //         array_push($level_2,$row_lang);
            //     }




            // }
            // if(!empty($level_1)){
            //     foreach($level_1 as $key){ 
            //         $mod = "";

            //         switch($key['type']){
            //             case "interp":
            //                 $mod = "<small>(Interpreting)</small>";
            //                 break;
            //             case "telep":
            //                 $mod = "<small>(Telephone)</small>";
            //                 break;
            //             case "trans":
            //                 $mod = "<small>(Translation)</small>";
            //                 break;
            //         }
            ?>
            <span class="badge badge-primary rm" <?php //if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Operator'){ 
                                                    ?> title="Double Click to remove this language!" id="<?php //echo $key['id'].'|'.$code; 
                                                                                                                                                                                    ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php //} 
                                                                                                                                                                                                                                                                                                                ?>><?php //echo $key['lang'].' | '.$levelz[$key['level']].' | '.$mod; 
                                                                                                                                                                                                                                                                                                                            ?></span>
            <?php //}
            //} 
            ?>
            <hr>
            <?php
            // if(!empty($level_2)){
            // foreach($level_2 as $key){ 
            //     $mod = "";

            //     switch($key['type']){
            //         case "interp":
            //             $mod = "<small>(Interpreting)</small>";
            //             break;
            //         case "telep":
            //             $mod = "<small>(Telephone)</small>";
            //             break;
            //         case "trans":
            //             $mod = "<small>(Translation)</small>";
            //             break;
            //     }
            ?>
            <span class="badge badge-primary rm" <?php //if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Operator'){ 
                                                    ?> title="Double Click to remove this language!" id="<?php //echo $key['id'].'|'.$code; 
                                                                                                                                                                                    ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php //} 
                                                                                                                                                                                                                                                                                                                ?>><?php //echo $key['lang'].' | '.$levelz[$key['level']].' | '.$mod; 
                                                                                                                                                                                                                                                                                                                            ?></span>
            <?php //}
            //} 
            ?>
            <?php //}
        }
    }
    if (isset($_POST['remove_lang_id'])) {
        include 'db.php';
        include 'class.php';
        $table = 'interp_lang';
        $arr = explode('|', $_POST['remove_lang_id']);
        $remove_id = $arr[0];
        $code = $arr[1];
        $lang = $_POST['lang'];
        // echo $remove_id; die;
        $data_remove = mysqli_query($con, "DELETE from interp_lang where id=" . $remove_id);
        if ($data_remove) {
            $langs_q = $acttObj->read_all('DISTINCT id,lang,`level`,`type`', 'interp_lang', "code='$code'");
            if ($langs_q->num_rows == 0) {
                $acttObj->update("interpreter_reg", array('interp' => 'No', 'telep' => 'No', 'trans' => 'No'), "code='" . $code . "'");
                echo '<span class="badge badge-primary">No Languages Currently!</span>';
            } else {
                $type_f2f = $acttObj->read_specific('id', 'interp_lang', "code='$code' AND type='interp'")['id'];
                $type_tp = $acttObj->read_specific('id', 'interp_lang', "code='$code' AND type='telep'")['id'];
                $type_tr = $acttObj->read_specific('id', 'interp_lang', "code='$code' AND type='trans'")['id'];
                $interpreter_update_array = [];
                if (empty($type_f2f)) {
                    $interpreter_update_array['interp'] = 'No';
                }
                if (empty($type_tp)) {
                    $interpreter_update_array['telep'] = 'No';
                }
                if (empty($type_tr)) {
                    $interpreter_update_array['trans'] = 'No';
                }
                $acttObj->update("interpreter_reg", $interpreter_update_array, "code='" . $code . "'");
                // $level_1=array();$level_2=array();
                $levelz = array("1" => "Native", "2" => "Fluent", "3" => "Intermediate", "4" => "Basic"); ?>

                <?php

                $sql1 = "SELECT DISTINCT lang FROM interp_lang where code='$code'";
                $result_1 = mysqli_query($con, $sql1);
                $row_1 = mysqli_fetch_all($result_1);
                foreach ($row_1 as $row_1) : ?>
                    <tr>
                        <td>
                            <?php $langRecord =  $acttObj->read_specific('id,lang,level', 'interp_lang', " lang='" . $row_1[0] . "' AND code = '" . $code . "' AND type ='interp'");
                            $level = $langRecord['level'];
                            $lang = $langRecord['lang'];
                            $lang_id = $langRecord['id'];

                            $Level = ($lang != "") ? " | " . $levelz[$level] : "";
                            $language =  $lang  . $Level;
                            ?>
                            <?php if ($language != "") : ?>
                                <span class="badge badge-primary rm" <?php
                                                                        if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Operator') { ?> title="Double Click to remove this language!" id="<?php echo $lang_id . '|' . $code; ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php  } ?>>
                                    <?php echo $language; ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $langRecord =  $acttObj->read_specific('id,lang,level', 'interp_lang', " lang='" . $row_1[0] . "' AND code = '" . $code . "' AND type ='telep'");
                            $level = $langRecord['level'];
                            $lang = $langRecord['lang'];
                            $lang_id = $langRecord['id'];
                            $Level = ($lang != "") ? " | " . $levelz[$level] : "";
                            $language =  $lang  . $Level;
                            ?>
                            <?php if ($lang != "") : ?>
                                <span class="badge badge-primary rm" <?php
                                                                        if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Operator') { ?> title="Double Click to remove this language!" id="<?php echo $lang_id . '|' . $code; ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php  } ?>>
                                    <?php echo $language; ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $langRecord = $acttObj->read_specific('id,lang,level', 'interp_lang', " lang='" . $row_1[0] . "' AND code = '" . $code . "' AND type ='trans'");
                            $level = $langRecord['level'];
                            $lang = $langRecord['lang'];
                            $lang_id = $langRecord['id'];
                            $Level = ($lang != "") ? " | " . $levelz[$level] : "";
                            $language = $lang  . $Level;

                            ?>
                            <?php if ($language != "") : ?>
                                <span class="badge badge-primary rm" <?php
                                                                        if ($_SESSION['prv'] == 'Management' || $_SESSION['prv'] == 'Operator') { ?> title="Double Click to remove this language!" id="<?php echo $lang_id . '|' . $code; ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php  } ?>>

                                    <?php echo $language ?>
                                </span>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
                <?php

                // while($row_lang = mysqli_fetch_array($langs_q)){
                //     if($row_lang['level']<3){
                //         array_push($level_1,$row_lang);
                //     }else{
                //         array_push($level_2,$row_lang);
                //     }
                // }
                // if(!empty($level_1)){
                //     foreach($level_1 as $key){
                //         $mod = "";

                //             switch($key['type']){
                //                 case "interp":
                //                     $mod = "<small>(Interpreting)</small>";
                //                     break;
                //                 case "telep":
                //                     $mod = "<small>(Telephone)</small>";
                //                     break;
                //                 case "trans":
                //                     $mod = "<small>(Translation)</small>";
                //                     break;
                //             }
                ?>
                <span class="badge badge-primary rm" <?php //if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Operator'){ 
                                                        ?> title="Double Click to remove this language!" id="<?php //echo $key['id'].'|'.$code; 
                                                                                                                                                                                        ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php //} 
                                                                                                                                                                                                                                                                                                                    ?>><?php // $key['lang'].' | '.$levelz[$key['level']].' | '.$mod; 
                                                                                                                                                                                                                                                                                                                                ?></span>
                <?php //}
                //} 
                ?>
                <hr>
                <?php
                //if(!empty($level_2)){
                // foreach($level_2 as $key){ 
                //     $mod = "";

                //     switch($key['type']){
                //         case "interp":
                //             $mod = "<small>(Interpreting)</small>";
                //             break;
                //         case "telep":
                //             $mod = "<small>(Telephone)</small>";
                //             break;
                //         case "trans":
                //             $mod = "<small>(Translation)</small>";
                //             break;
                //     }
                ?>
                <span class="badge badge-primary rm" <?php //if($_SESSION['prv']=='Management' || $_SESSION['prv']=='Operator'){ 
                                                        ?> title="Double Click to remove this language!" id="<?php echo $key['id'] . '|' . $code; ?>" ondblclick="if(confirm('Are you sure to remove this language?')){remove_lang(this);}" <?php //} 
                                                                                                                                                                                                                                                                                                                    ?>><?php //echo $key['lang'].' | '.$levelz[$key['level']].' | '.$mod; 
                                                                                                                                                                                                                                                                                                                                ?></span>
                <?php //}
                //} 
                ?>
                <?php }
        }
    }
    // Code for skill
    if (isset($_POST['code']) && isset($_POST['skill'])) {
        include 'db.php';
        include 'class.php';
        $table = 'interp_skill';
        $code = $_POST['code'];
        $skill = $_POST['skill'];
        $dated = date('Y-m-d');
        $data = array('skill' => $skill, 'code' => $code, 'dated' => $dated);
        if ($acttObj->insert($table, $data)) {
            $skills_q = $acttObj->read_all('DISTINCT id,skill', 'interp_skill', "code='$code' ORDER BY skill ASC");
            if (mysqli_num_rows($skills_q) == 0) {
                echo '<span class="badge badge-primary">No Skills Currently!</span>';
            } else {
                while ($row_skills = mysqli_fetch_assoc($skills_q)) { ?>
                    <span class="badge badge-primary rm" <?php if ($_SESSION['prv'] == 'Management') { ?> title="Double Click to remove this Skill!" id="<?php echo $row_skills['id'] . '|' . $code; ?>" ondblclick="if(confirm('Are you sure to remove this language?')){ remove_skill(this);}" <?php } ?>><?php echo $row_skills['skill']; ?></span>&nbsp; &nbsp;
                <?php }
            }
        }
    }
    if (isset($_POST['remove_skill_id'])) {
        include 'db.php';
        include 'class.php';
        $table = 'interp_skill';
        $arr = explode('|', $_POST['remove_skill_id']);
        $remove_id = $arr[0];
        $code = $arr[1];
        $data_remove = mysqli_query($con, "DELETE from interp_skill where id=" . $remove_id);
        if ($data_remove) {
            $skills_q = $acttObj->read_all('DISTINCT id,skill', 'interp_skill', "code='$code' ORDER BY skill ASC");
            if (mysqli_num_rows($skills_q) == 0) {
                echo '<span class="badge badge-primary">No Skills Currently!</span>';
            } else {
                while ($row_skills = mysqli_fetch_assoc($skills_q)) { ?>
                    <span class="badge badge-primary rm" <?php if ($_SESSION['prv'] == 'Management') { ?> title="Double Click to remove this Skill!" id="<?php echo $row_skills['id'] . '|' . $code; ?>" ondblclick="remove_skill(this);" <?php } ?>><?php echo $row_skills['skill']; ?></span>&nbsp; &nbsp;
            <?php }
            }
        }
    }
    //Code for translation types ajax
    if (isset($_POST['tc_id']) && !empty($_POST['tc_id'])) {
        include 'db.php';
        include 'class.php';
        $tc_id = $_POST['tc_id'];
        $q_tt = $acttObj->read_all('tt_id,tt_title', 'trans_types', "tc_id='$tc_id' and tc_id!=8 and tt_status=1 ORDER BY tt_title ASC");
        $q_td = $acttObj->read_all('td_id,td_title', 'vw_translation', "tc_id='$tc_id' and tc_id!=8 ORDER BY td_title ASC");
        $res_tt .= '<label class="control-label">Select Translation Type(s)</label>';
        if ($q_tt->num_rows == 0) {
            $res_tt .= '<select name="trans_detail[]" id="trans_detail" class="form-control">
                <option value="8">Select Translation Type</option>
            </select>';
        } else {
            $res_tt .= '<select name="trans_detail[]"  multiple="multiple" id="trans_detail" class="form-control multi_class" required>';
            while ($row_tt = $q_tt->fetch_assoc()) {
                $res_tt .= '<option value="' . $row_tt['tt_id'] . '">' . utf8_encode($row_tt['tt_title']) . '</option>';
            }
            $res_tt .= '</select>';
        }
        $res_td .= '<label class="control-label">Select Translation Category</label>';
        if ($q_td->num_rows == 0) {
            $res_td .= '<select name="transType[]" id="transType" class="form-control">
                <option value="8">Select Translation Category</option>
            </select>';
        } else {
            $res_td .= '<select name="transType[]"  multiple="multiple" id="transType" required class="form-control multi_class">';
            while ($row_td = $q_td->fetch_assoc()) {
                $res_td .= '<option value="' . $row_td['td_id'] . '">' . utf8_encode($row_td['td_title']) . '</option>';
            }
            $res_td .= '</select>';
        }
        $data[0] = $res_tt;
        $data[1] = $res_td;
        echo json_encode($data);
    }
    //Code for interpreting job types ajax
    if (isset($_POST['ic_id']) && !empty($_POST['ic_id'])) {
        include 'db.php';
        include 'class.php';
        $ic_id = $_POST['ic_id'];
        $res_it = '';
        if ($ic_id != '12') {
            $q_it = $acttObj->read_all('it_id,it_title', 'interp_types', "ic_id=$ic_id and it_status=1 ORDER BY it_title ASC");

            $res_it .= '<label class="control-label">Select Assignment Type(s)</label>';
            if ($q_it->num_rows == 0) {
                $res_it .= '<select name="interp_type[]" id="interp_type" class="form-control">
                <option value="">Select Assignment Type(s)</option>
            </select>';
            } else {
                $res_it .= '<select name="interp_type[]"  multiple="multiple" id="interp_type" class="form-control multi_class" required>';
                while ($row_it = $q_it->fetch_assoc()) {
                    $res_it .= '<option value="' . $row_it['it_id'] . '">' . utf8_encode($row_it['it_title']) . '</option>';
                }
                $res_it .= '</select>';
            }
        } else {
            $res_it = '';
        }
        echo $res_it;
    }
    //Code for telephone job types ajax
    if (isset($_POST['tpc_id']) && !empty($_POST['tpc_id'])) {
        include 'db.php';
        include 'class.php';
        $tpc_id = $_POST['tpc_id'];
        $res_tpt = '';
        if ($tpc_id != '11') {
            $q_tpt = $acttObj->read_all('tpt_id,tpt_title', 'telep_types', "tpc_id=$tpc_id and tpt_status=1 ORDER BY tpt_title ASC");

            $res_tpt .= '<label class="control-label">Select Telephone Details</label>';
            if ($q_tpt->num_rows == 0) {
                $res_tpt .= '<select name="telep_type[]" id="telep_type" class="form-control">
                <option value="">Select Telephone Details</option>
            </select>';
            } else {
                $res_tpt .= '<select name="telep_type[]"  multiple="multiple" id="telep_type" class="form-control multi_class" required>';
                while ($row_tpt = $q_tpt->fetch_assoc()) {
                    $res_tpt .= '<option value="' . $row_tpt['tpt_id'] . '">' . utf8_encode($row_tpt['tpt_title']) . '</option>';
                }
                $res_tpt .= '</select>';
            }
        } else {
            $res_tpt = '';
        }
        echo $res_tpt;
    }
    //Code for audio,video and both types ajax
    if (isset($_POST['telep_checker']) && !empty($_POST['val'])) {
        include 'db.php';
        include 'class.php';
        $val = $_POST['val'];
        $put_var = $val != 'b' ? "and c_cat='$val'" : "";
        $res_tp_checker = '';
        $q_tp_checker = $acttObj->read_all("c_id,c_title,c_image", "comunic_types", "c_status=1 $put_var GROUP BY c_title ORDER BY c_title ASC");
        $res_tp_checker .= '<label class="control-label">Select Communication Type</label>
                <select class="form-control" name="comunic" id="comunic" required="">';
        $res_tp_checker .= '<option value="">Select Type</option>';
        while ($row_tp_checker = $q_tp_checker->fetch_assoc()) {
            $res_tp_checker .= '<option value="' . $row_tp_checker['c_id'] . '">' . utf8_encode($row_tp_checker['c_title']) . '</option>';
        }
        $res_tp_checker .= '</select>';
        echo $res_tp_checker;
    }
    //Code for cancellation drop downs  ajax
    if (isset($_POST['cd_for']) && isset($_POST['lang']) && isset($_POST['cancel']) && $_POST['cancel'] == 'yes') {
        include 'db.php';
        include 'class.php';
        $cd_for = $_POST['cd_for'];
        $lang = $_POST['lang'];
        $cancelled_at = $_POST['cancelled_at'] ? $_POST['cancelled_at'] : date('Y-m-d H:i:s');
        $cancelled_date = date('Y-m-d', strtotime($cancelled_at));
        $cancelled_time = date('H:i:s', strtotime($cancelled_at));
        $assignment_date = date('Y-m-d', strtotime($_POST['assign_date']));
        $assignment_time = date('H:i:s', strtotime($_POST['assign_time']));

        $date1 = new DateTime($cancelled_at);
        $date2 = new DateTime($assignment_date . " " . $assignment_time);
        $diff = $date2->diff($date1);

        $working_days = 0;
        if ($date2 > $date1) {
            list($date2, $date1) = [$date1, $date2];
        }
        while ($date2 < $date1) {
            if ($date2->format("N") < 6) {
                $working_days++;
            }
            $date2->modify('+1 day');
        }
        $diff_hours = ($working_days * 24);
        $hours = $diff_hours;

        $pay_int = 0;
        $past_cancellation_label = $date1 > $date2 ? '<span class="label label-danger">Past Cancellation</span>' : '';
        if ($lang == 'Sign Language' || $lang == 'Sign Language (BSL)') {
            // For BSL:24 hours=24x7:168,48 hours=24x14:336,greater 48 hours=greater then 336
            if ($working_days <= 7) {
                $pay_int = 1;
                $put_cancelled_hours = " AND cancelled_hours=1";
            } else if ($working_days > 7 && $working_days <= 14) {
                $put_cancelled_hours = " AND cancelled_hours=2";
            } else {
                $put_cancelled_hours = " AND cancelled_hours=3";
            }
        } else {
            if ($working_days <= 1) {
                $pay_int = 1;
                $put_cancelled_hours = " AND cancelled_hours=1";
            } else if ($working_days > 1 && $working_days <= 2) {
                $put_cancelled_hours = " AND cancelled_hours=2";
            } else {
                $put_cancelled_hours = " AND cancelled_hours=3";
            }
        }
        $pay_int_text = $pay_int == 1 ? "Interpreter Payable" : "Interpreter Non-payable";
        $pay_int_class = $pay_int == 1 ? "danger" : "success";

        $put_bsl = $lang == 'Sign Language' || $lang == 'Sign Language (BSL)' ? " and is_bsl = 1" : " and is_bsl = 0";
        $put_var = "cd_for='" . $cd_for . "'";
        $response = '<div class="form-group col-xs-6" id="div_cd">';
        $row_dropdown = $acttObj->read_specific("cd_id,cd_title,cd_effect,cd_effect_interp", "cancellation_drops", "$put_var $put_bsl $put_cancelled_hours AND deleted_flag=0 ORDER BY cd_title ASC");

        //$response.='<label class="control-label">Select Cancellation Type '.$past_cancellation_label.'</label>
        $response .= '<label class="control-label">Select Cancellation Type [Days : ' . $working_days . '] ' . $past_cancellation_label . '</label>
        <select onchange="get_reasons(this);" class="form-control" name="cn_t_id" id="cn_t_id" required>';
        $charge_client = $row_dropdown['cd_effect'] == '1' ? "Client Chargeable" : "Client Non-chargeable";
        $charge_interp = '';
        if($row_dropdown['cd_effect_interp'] =='charg'){
            $charge_interp = 'Interpreter Chargeable';
            $pay_int_class = "danger";
        }elseif($row_dropdown['cd_effect_interp'] =='ncharg'){
            $pay_int_class = "success";
            $charge_interp = 'Interpreter Non-Chargeable';
        }elseif($row_dropdown['cd_effect_interp'] =='pay'){
            $pay_int_class = "success";
            $charge_interp = 'Interpreter Payable';
        }elseif($row_dropdown['cd_effect_interp'] =='npay'){
            $pay_int_class = "success";
            $charge_interp = 'Interpreter Non-Payable';
        }
        $charge_client_class = $row_dropdown['cd_effect'] == '1' ? "danger" : "success";
        $response .= '<option value="' . $row_dropdown['cd_id'] . '">' . utf8_encode($row_dropdown['cd_title']) . '</option>';
        $response .= '</select></div>
    <div class="form-group col-xs-3" id="div_cancel_details" style="margin-top: -10px;">
        <button type="button" class="btn btn-block btn-' . $charge_client_class . '">' . $charge_client . '</button>
        <button type="button" class="btn btn-block btn-' . $pay_int_class . '">' . $charge_interp . '</button>
    </div>';

        //Cancellation reasons dropdown    
        $put_var = $cd_for == 'ls' && ($lang == 'Sign Language' || $lang == 'Sign Language (BSL)') ? "1" : "cr_for='" . $cd_for . "'";
        $response .= '<div class="form-group col-xs-5" id="div_reason">';
        $query_reasons = $acttObj->read_all("cr_id,cr_title", "cancel_reasons", "$put_var $put_bsl OR cr_id=15 ORDER BY cr_title ASC");
        $response .= '<label class="control-label">Select Cancellation Reason</label>
        <select onchange="get_buttons(this);" class="form-control" name="cn_r_id" id="cn_r_id" required=""><option selected disabled value="">Select Cancellation Reason</option>';
        while ($row_reasons = $query_reasons->fetch_assoc()) {
            $other_colored = $row_reasons['cr_id'] == 15 ? "style='color:red'" : "";
            $response .= '<option ' . $other_colored . ' value="' . $row_reasons['cr_id'] . '">' . utf8_encode($row_reasons['cr_title']) . '</option>';
        }
        $response .= '</select></div>';
        echo json_encode(["data" => $response, "charge_client" => $row_dropdown['cd_effect'], "pay_int" => $pay_int, "charg_interp" => $row_dropdown['cd_effect_interp']]);
    }
    //Code for amendmend job drop downs  ajax
    if (isset($_POST['jobID']) && isset($_POST['effect']) && isset($_POST['effectInterp']) && $_POST['amend'] == 'yes') {
        include 'db.php';
        include 'class.php';
        $jobID = $_POST['jobID'];
        $effect = $_POST['effect'];
        $effectInterp = $_POST['effectInterp'];
        $amend = $_POST['amend'];
        $table = $_POST['table'];

        $get_job_data = $acttObj->read_specific("source,intrpName", "$table", "id=" . $jobID);
        $chk_booked = $get_job_data['intrpName'];
        if (empty($chk_booked)) {
            $row = $acttObj->read_specific("$table.*,comp_reg.name as orgzName", "$table,comp_reg", "$table.orgName=comp_reg.abrv AND $table.id=" . $jobID);
        } else {
            $row = $acttObj->read_specific("$table.*,interpreter_reg.name,interpreter_reg.email,interpreter_reg.rph,interpreter_reg.rpm,interpreter_reg.rpu,interpreter_reg.contactNo,interpreter_reg.country as interpreter_country,comp_reg.name as orgzName", "$table,interpreter_reg,comp_reg", "$table.intrpName=interpreter_reg.id AND $table.orgName=comp_reg.abrv AND $table.id=" . $jobID);
        }
        $assignDur = $row['assignDur'];
       
        $rph = $row['rph'];
        $rpm = $row['rpm'];
        $rpu = $row['rpu'];
        $interp_charge_amount = 0;


        if ($table == 'telephone') {
            $interp_charge_amount = $assignDur * $rpm;
        }


        if ($table == 'interpreter') {
            $hours = $assignDur / 60;
            $interp_charge_amount = $hours * $rph;
            // if ($assignDur >= 60) {
            // } else {
            //     $interp_charge_amount = $assignDur * $rph;
            // }
        }
        if ($table == 'translation') {
            $assignDur = $row['numberUnit'];
            $interp_charge_amount = $assignDur * $rpu;
        }
       
        $rounded_amount = round($interp_charge_amount);
        echo json_encode(["amount" => $rounded_amount]);
        // echo json_encode(["data" => $response, "charge_client" => $row_dropdown['cd_effect'], "pay_int" => $pay_int, "charg_interp" => $row_dropdown['cd_effect_interp']]);
    }
    //Code for cancellation reasons ajax
    if (isset($_POST['cd_id']) && isset($_POST['reason']) && $_POST['reason'] == 'yes') {
        include 'db.php';
        include 'class.php';
        $cd_id = $_POST['cd_id'];
        $cd_effect = $acttObj->read_specific("cd_effect", "cancellation_drops", "cd_id=" . $cd_id)['cd_effect'];
        if ($cd_effect == 1) {
            $hidden_pay = "<input type='hidden' id='hidden_pay' value='1'/>";
        } else {
            $hidden_pay = "<input type='hidden' id='hidden_pay' value='0'/>";
        }
        if ($cd_id != '6') {
            $cr_for = $acttObj->read_specific("cd_for", "cancellation_drops", "cd_id='$cd_id'")['cd_for'];
            $put_var = $cr_for == 'cl' ? "cr_for='$cr_for' OR cr_for='all'" : "cr_for='ls' OR cr_for='all'";
            $res_cr_chk = '';
            $q_cr_chk = $acttObj->read_all("cr_id,cr_title", "cancel_reasons", "$put_var ORDER BY cr_title ASC");
            $res_cr_chk .= '<label class="control-label">Select Cancellation Reason</label>' . $hidden_pay . '
                <select onchange="get_buttons();" class="form-control" name="cn_r_id" id="cn_r_id" required=""><option selected disabled value="">Select Cancellation Reason</option>';
            while ($row_cr_chk = $q_cr_chk->fetch_assoc()) {
                $res_cr_chk .= '<option value="' . $row_cr_chk['cr_id'] . '">' . utf8_encode($row_cr_chk['cr_title']) . '</option>';
            }
            $res_cr_chk .= '</select>';
            echo $res_cr_chk;
        } else {
            echo '';
        }
    }
    if (isset($_POST['get_specific']) && isset($_POST['get_type'])) {
        include 'db.php';
        $get_type = $_POST['get_type'];
        error_reporting(0);
        if ($get_type == 'interpreter') {
            $chek_col = "interp";
        } else if ($get_type == 'telephone') {
            $chek_col = "telep";
        } else {
            $chek_col = "trans";
        }
        $source_lang_req = $_POST['source'];
        $target_lang_req = $_POST['target'];
        $dbs_checked_req = $_POST['dbs_checked'];
        $dbs_required = isset($dbs_checked_req) && !empty($dbs_checked_req) && $dbs_checked_req == 0 ? 'AND interpreter_reg.dbs_checked=0' : '';
        $gender_req = $_POST['gender'];
        if ($gender_req == '' || $gender_req == 'No Preference') {
            $put_gender = "";
        } else {
            $put_gender = "AND interpreter_reg.gender='$gender_req'";
        }
        if ($source_lang_req == $target_lang_req) {
            $put_lang = "";
            $query_style = '0';
        } else if ($source_lang_req != 'English' && $target_lang_req != 'English') {
            $put_lang = "";
            $query_style = '1';
        } else if ($source_lang_req == 'English' && $target_lang_req != 'English') {
            $put_lang = "interp_lang.lang='$target_lang_req'";
            $query_style = '2';
        } else if ($source_lang_req != 'English' && $target_lang_req == 'English') {
            $put_lang = "interp_lang.lang='$source_lang_req'";
            $query_style = '2';
        } else {
            $put_lang = "";
            $query_style = '3';
        }
        if ($query_style == '0') {
            $query_emails = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('" . $source_lang_req . "') and interp_lang.code=interpreter_reg.code)=1 and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.$chek_col='Yes' $dbs_required $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0";
        } else if ($query_style == '1') {
            $query_emails = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND (SELECT COUNT(DISTINCT interp_lang.lang) FROM interp_lang WHERE interp_lang.lang IN ('" . $source_lang_req . "','" . $target_lang_req . "') and interp_lang.code=interpreter_reg.code)=2 and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.$chek_col='Yes' $dbs_required $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0";
        } else if ($query_style == '2') {
            $query_emails = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg,interp_lang WHERE interpreter_reg.code=interp_lang.code AND $put_lang and 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.$chek_col='Yes' $dbs_required $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.is_temp=0 ORDER BY interpreter_reg.name ASC";
        } else {
            $query_emails = "SELECT DISTINCT interpreter_reg.id,interpreter_reg.name,interpreter_reg.gender,interpreter_reg.city FROM interpreter_reg WHERE 
            interpreter_reg.active='0' AND (interpreter_reg.actnow='Active' OR (interpreter_reg.actnow='Inactive' AND CURRENT_DATE() NOT BETWEEN interpreter_reg.actnow_time AND interpreter_reg.actnow_to)) AND interpreter_reg.$chek_col='Yes' $dbs_required $put_gender AND interpreter_reg.deleted_flag=0 AND interpreter_reg.subscribe=1 AND interpreter_reg.is_temp=0";
        }
        // echo $query_emails;exit;
        $res_emails = mysqli_query($con, $query_emails);
        while ($row_ints = mysqli_fetch_assoc($res_emails)) { ?>
            <option value="<?php echo $row_ints['id']; ?>"><?php echo $row_ints['name'] . ' (' . $row_ints['gender'] . ')' . ' (' . $row_ints['city'] . ')'; ?></option>
        <?php }
    }
    if (isset($_REQUEST["term"]) && isset($_REQUEST["orgName"])) {
        include 'db.php';
        include 'class.php';
        if (!empty($_REQUEST["orgName"])) {
            $append_orgName = "AND company='" . $_REQUEST["orgName"] . "'";
        } else {
            $append_orgName = "";
        }
        if ($_REQUEST["runtime_action"] == "purchase_order") {
            $result = $acttObj->read_all("porder", "porder_details", "porder LIKE '" . $_REQUEST["term"] . "%' " . $append_orgName);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<p class='click'>" . $row["porder"] . "</p>";
                }
            } else {
                echo "<p>No matches found!</p>";
            }
        } else {
            $result = $acttObj->read_all("reference", "comp_ref", "reference LIKE '" . $_REQUEST["term"] . "%' " . $append_orgName);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<p class='click'>" . $row["reference"] . "</p>";
                }
            } else {
                echo "<p>No matches found!</p>";
            }
        }
    }

    if (isset($_POST["nm"]) && isset($_POST["em"]) && isset($_POST["dob"]) && isset($_POST["action"]) && $_POST["action"] == "check_em") {
        include 'db.php';
        include 'class.php';
        $nm = trim($_POST["nm"]);
        $em = trim($_POST["em"]);
        $dob = $_POST["dob"];
        $data = array();
        $check_1 = $acttObj->read_specific("id,is_temp,dated", "interpreter_reg", "email='" . $em . "' AND deleted_flag=0");
        if (!empty($check_1['id'])) {
            $data['status'] = "exist";
            $data['is_temp'] = $check_1['is_temp'];
            if ($check_1['is_temp'] == 1) {
                $data['msg'] = "This account is already in pending for approval registered on " . $check_1['dated'];
            } else {
                $data['msg'] = "This email is already registered. Use a different one!";
            }
        } else {
            $check_2 = $acttObj->read_specific("id,is_temp,dated", "interpreter_reg", "name LIKE '%" . $nm . "' AND dob='" . $dob . "' AND deleted_flag=0");
            if (!empty($check_2['id'])) {
                $data['status'] = "same_exist";
                $data['is_temp'] = $check_2['is_temp'];
                $data['msg'] = "Looks like we have already have a same record!";
            } else {
                $data['status'] = "not_exist";
                $data['is_temp'] = $check_2['is_temp'];
                $data['msg'] = "";
            }
        }
        echo json_encode($data);
    }
    //code to get cities list for a specific country
    if (isset($_POST['country_name']) && $_POST['type'] == "get_cities_of_country") {
        $url = 'https://countriesnow.space/api/v0.1/countries/cities';
        $data = [
            'country' => $_POST['country_name']
        ];
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if ($httpcode === 200) {
            $cities_array = json_decode($response)->data;
            $select_cities = "";
            if (!isset($_POST['without_label'])) {
                $select_cities .= "<label>Select City Name *</label>";
            }
            $select_cities .= "<select onchange='other_city(this)' name='selected_city' id='selected_city' class='form-control mt' required>
                <option value='' disabled selected>--- Select a city ---</option>";
            if (count($cities_array) > 0) {
                foreach ($cities_array as $key => $val) {
                    $select_cities .= "<option value='" . $val . "'>" . $val . "</option>";
                }
                $select_cities .= "<option value='Not in List'>Not in List</option>";
            } else {
                $select_cities .= "<option value='Not in List'>No City Found</option>";
            }
            $select_cities .= "<select>";
            $data['cities'] = $select_cities;
        } else {
            $data['cities'] = "";
        }
        echo json_encode($data);
    
        curl_close($ch);
    }
    
    
    //Find company rates
    if (isset($_POST['find_company_rates'])) {
        include 'actions.php';
        $language_types = array(1 => "Standard Language", 2 => "Rare Language", 3 => "BSL Language");
        if (!empty($_POST['find_order_type'])) {
            $append_order_type = " AND order_type=" . $_POST['find_order_type'];
        }
        if (!empty($_POST['find_company_id'])) {
            $check_parent_company = $obj->read_specific("child_companies.*,comp_type.company_type_id", "child_companies,comp_reg,comp_type", "child_companies.child_comp=comp_reg.id AND comp_reg.type_id=comp_type.id AND child_companies.child_comp=" . $_POST['find_company_id']);
            if (!empty($check_parent_company['parent_comp'])) {
                $_POST['find_company_id'] = $check_parent_company['parent_comp'];
                $_POST['find_company_type'] = $check_parent_company['company_type_id'];
            }
            $append_company_id = " AND company_id=" . $_POST['find_company_id'];
        }
        // Apply company type & order type from rates
        if (!empty($_POST['find_company_type'])) {
            $append_company_type = " AND company_type_id = " . $_POST['find_company_type'];
        }
        // Check assignment time if OOH or not
        $timezone = new DateTimeZone('Europe/London');
        if (!empty($_POST['find_assignment_time'])){//08:00 AM to 05:00 PM
            $assignment_time = new DateTime(date('H:i', strtotime($_POST['find_assignment_time'])), $timezone);
            $start_time = new DateTime('08:00', $timezone);
            $end_time = new DateTime('17:00', $timezone);
            $ooh = 0;$weekend = 0;
            if ($assignment_time < $start_time || $assignment_time > $end_time) {
                $ooh = 1;
                $append_ooh = " AND rate_categories.id IN (10,20,30)";
            }
        }
        //Check if charity clients fixed rates
        if ($_POST['find_company_type'] == 4) {//charity & others client
            $append_non_weekend = " AND rate_categories.id IN (9,19,29)";
        } else {
            // Check assignment date if weekend or not
            if (!empty($_POST['find_assignment_date'])){
                $datetime = new DateTime($_POST['find_assignment_date'], $timezone);
                $day_of_week = (int) $datetime->format('w');
                if ($day_of_week === 6 || $day_of_week === 0) {
                    $weekend = 1;
                    $append_weekend = " AND rate_categories.id IN (3,6,13,16,23,26)";
                    if ($_POST['find_company_type'] == 3) {//Contracted client
                        $append_weekend = " AND rate_categories.id IN (8,18,28)";
                    }
                } else {
                    if ($_POST['find_company_type'] == 3) {//Contracted client
                        $append_non_weekend = " AND rate_categories.id IN (7,17,27)";
                    }
                }
            } else {
                if ($_POST['find_company_type'] == 3) {//Contracted client
                    $append_non_weekend = " AND rate_categories.id IN (7,8,17,18,27,28)";
                }
            }
        }
        //Check if language is BSL | Rare | Standard if not charity clients
        if ($_POST['find_company_type'] != 4) {
            if (!empty($_POST['find_language_type'])) {
                if ($_POST['find_language_type'] == 3) {//BSL Language
                    $append_language_type = " AND rate_categories.is_bsl = 1 AND rate_categories.is_rare = 0";
                } else if ($_POST['find_language_type'] == 2) {//Rare Language
                    $append_language_type = " AND rate_categories.is_rare = 1 AND rate_categories.is_bsl = 0";
                } else {//Standard Language
                    $append_language_type = " AND rate_categories.is_rare = 0 AND rate_categories.is_bsl = 0";
                }
            }
        }
        // Check assignment and booking datetime difference only for public funded & LAA clients
        if ($_POST['find_company_type'] < 3 || !isset($_POST['find_company_type'])) {
            if (!empty($_POST['find_assignment_date'])){
                $find_booked_date = !empty($_POST['find_booked_date']) ? date('Y-m-d', strtotime($_POST['find_booked_date'])) : date('Y-m-d');
                $find_booked_time = !empty($_POST['find_booked_time']) ? date('H:i:s', strtotime($_POST['find_booked_time'])) : date('H:i:s');
                $find_assignment_date = !empty($_POST['find_assignment_date']) ? date('Y-m-d', strtotime($_POST['find_assignment_date'])) : date('Y-m-d');
                $find_assignment_time = !empty($_POST['find_assignment_time']) ? date('H:i:s', strtotime($_POST['find_assignment_time'])) : date('H:i:s');

                $date1 = new DateTime($find_booked_date . " " . $find_booked_time);
                $date2 = new DateTime($find_assignment_date . " " . $find_assignment_time);
                $diff = $date2->diff($date1);

                $working_days = 0;
                if ($date2 > $date1) {
                    list($date2, $date1) = [$date1, $date2];
                }
                while ($date2 < $date1) {
                    if ($date2->format("N") < 6) {
                        $working_days++;
                    }
                    $date2->modify('+1 day');
                }
                if ($working_days > 2) {//2 days for client as standard order
                    $append_duration = " AND rate_categories.id IN (1,4,11,14,21,24)";
                } else {
                    $append_duration = " AND rate_categories.id IN (2,5,12,15,22,25)";
                }
            }
        }

        if ($ooh == 1) {
            $append_company_type .= $append_ooh;
        } else {
            if ($append_non_weekend) {
                $append_company_type .= $append_non_weekend;
            } else {
                if ($weekend == 1) {
                    $append_company_type .= $append_weekend;
                } else {
                    $append_company_type .= $append_duration;
                }
            }
        }
        // Fetch company self rates if they are set else fetch global rates for selected group of company
        if (!empty($_POST['find_company_id'])) {
            $get_group_rates = $obj->read_all("individual_company_rates.*,company_types.title as group_name,rate_categories.is_bsl,rate_categories.is_rare,(CASE WHEN rate_categories.is_bsl = 1 THEN 'BSL' WHEN rate_categories.is_rare = 1 THEN 'Rare' ELSE 'Standard' END) as extra_title, rate_categories.title", "individual_company_rates,rate_categories,company_types", "individual_company_rates.rate_category_id=rate_categories.id AND individual_company_rates.company_type_id = company_types.id ".$append_order_type.$append_company_id.$append_company_type.$append_language_type);
        }
        if (empty($_POST['find_company_id']) || (isset($get_group_rates) && $get_group_rates->num_rows == 0)) {
            $get_group_rates = $obj->read_all("company_rates.*,company_types.title as group_name,rate_categories.is_bsl,rate_categories.is_rare,(CASE WHEN rate_categories.is_bsl = 1 THEN 'BSL' WHEN rate_categories.is_rare = 1 THEN 'Rare' ELSE 'Standard' END) as extra_title, rate_categories.title", "company_rates,rate_categories,company_types", "company_rates.rate_category_id=rate_categories.id AND company_rates.company_type_id = company_types.id ".$append_order_type.$append_company_type.$append_language_type." ORDER BY rate_categories.id ASC");
        }
        $company_rates = array();
        $append_extra_title =  !empty($_POST['find_language_type']) ? " (" . $language_types[$_POST['find_language_type']] . ")" : "";
        while ($row_group_rates = $get_group_rates->fetch_assoc()) {
            if (empty($append_extra_title)) {
                $empty_extra_title = " - " . $row_group_rates['group_name'] . " (" . $row_group_rates['extra_title'] . ")";
                $row_group_rates['title'] = $row_group_rates['title'] . $empty_extra_title;
            } else {
                $row_group_rates['title'] = $row_group_rates['title'] . " - " . $row_group_rates['group_name'] . $append_extra_title;
            }
            $company_rates[] = $row_group_rates;
        }
        $response = array('status' => 0, 'company_rates' => array());
        if (count($company_rates) > 0) {
            $response['status'] = 1;
            $response['company_rates'] = $company_rates;
        }
        echo json_encode($response);
        exit;
    }

    //Find interpreter rates
    if (isset($_POST['find_interpreter_rates'])) {
        include 'actions.php';
        $language_types = array(1 => "Standard Language", 2 => "Rare Language", 3 => "BSL Language");
        if (!empty($_POST['find_order_type'])) {
            $append_order_type = " AND order_type=" . $_POST['find_order_type'];
        }
        if (!empty($_POST['find_interpreter_id'])) {
            $check_assigned_rates = $obj->read_specific("*", "individual_interpreter_rates", "interpreter_id=" . $_POST['find_interpreter_id']);
            if (!empty($check_assigned_rates['rate_group_id'])) {
                $append_group_id = " AND group_id=" . $check_assigned_rates['rate_group_id'];
            } else {
                if ($_POST['find_language_type'] == 3) {// if BSL
                    $append_group_id = " AND group_id=2";//2 is default group for BSL interpreters
                } else {
                    $append_group_id = " AND group_id=1";//1 is default group for Non-BSL interpreters
                }
            }
        }
        // Check assignment time if OOH or not
        $timezone = new DateTimeZone('Europe/London');
        if (!empty($_POST['find_assignment_time'])){//08:00 AM to 05:00 PM
            $assignment_time = new DateTime(date('H:i', strtotime($_POST['find_assignment_time'])), $timezone);
            $start_time = new DateTime('08:00', $timezone);
            $end_time = new DateTime('17:00', $timezone);
            $ooh = 0;$weekend = 0;
            if ($assignment_time < $start_time || $assignment_time > $end_time) {
                $ooh = 1;
                $append_ooh = " AND rate_categories.id IN (10,20)";
            }
        }
        // Check assignment date if weekend or not
        if (!empty($_POST['find_assignment_date'])){
            $datetime = new DateTime($_POST['find_assignment_date'], $timezone);
            $day_of_week = (int) $datetime->format('w');
            if ($day_of_week === 6 || $day_of_week === 0) {
                $weekend = 1;
                $append_weekend = " AND rate_categories.id IN (3,13)";
            }
        }
        //Check if language is BSL | Rare | Standard
        if (!empty($_POST['find_language_type'])) {
            if ($_POST['find_language_type'] == 3) {//BSL Language
                $append_language_type = " AND rate_categories.for_interpreter=1 AND rate_categories.is_bsl = 1 AND rate_categories.is_rare = 0";
            } else {//Standard Language
                $append_language_type = " AND rate_categories.for_interpreter=1 AND rate_categories.is_rare = 0 AND rate_categories.is_bsl = 0";
            }
        }
        // Check assignment and booking datetime difference
        if (!empty($_POST['find_assignment_date'])){
            $find_booked_date = !empty($_POST['find_booked_date']) ? date('Y-m-d', strtotime($_POST['find_booked_date'])) : date('Y-m-d');
            $find_booked_time = !empty($_POST['find_booked_time']) ? date('H:i:s', strtotime($_POST['find_booked_time'])) : date('H:i:s');
            $find_assignment_date = !empty($_POST['find_assignment_date']) ? date('Y-m-d', strtotime($_POST['find_assignment_date'])) : date('Y-m-d');
            $find_assignment_time = !empty($_POST['find_assignment_time']) ? date('H:i:s', strtotime($_POST['find_assignment_time'])) : date('H:i:s');

            $date1 = new DateTime($find_booked_date . " " . $find_booked_time);
            $date2 = new DateTime($find_assignment_date . " " . $find_assignment_time);
            $diff = $date2->diff($date1);

            $working_days = 0;
            if ($date2 > $date1) {
                list($date2, $date1) = [$date1, $date2];
            }
            while ($date2 < $date1) {
                if ($date2->format("N") < 6) {
                    $working_days++;
                }
                $date2->modify('+1 day');
            }
            if ($working_days > 1) {//1 day for interpreter as standard order
                $append_duration = " AND rate_categories.id IN (1,11)";
            } else {
                $append_duration = " AND rate_categories.id IN (2,12)";
            }
        }

        if ($ooh == 1) {
            $append_order_type .= $append_ooh;
        } else if ($weekend == 1) {
            $append_order_type .= $append_weekend;
        } else {
            $append_order_type .= $append_duration;
        }
        // Fetch interpreter self rates if they are set else fetch global rates for selected interpreter
        if (!empty($_POST['find_interpreter_id'])) {
            $get_interpreter_rates = $obj->read_all("individual_interpreter_rates.*,rate_categories.is_bsl,rate_categories.is_rare,(CASE WHEN rate_categories.is_bsl = 1 THEN 'BSL' WHEN rate_categories.is_rare = 1 THEN 'Rare' ELSE 'Standard' END) as extra_title, rate_categories.title", "individual_interpreter_rates,rate_categories", "individual_interpreter_rates.rate_category_id=rate_categories.id ".$append_order_type.$append_group_id.$append_language_type);
        }
        if (empty($_POST['find_interpreter_id']) || (isset($get_interpreter_rates) && $get_interpreter_rates->num_rows == 0)) {
            $get_interpreter_rates = $obj->read_all("interpreter_rates.*,rate_categories.is_bsl,rate_categories.is_rare,(CASE WHEN rate_categories.is_bsl = 1 THEN 'BSL' WHEN rate_categories.is_rare = 1 THEN 'Rare' ELSE 'Standard' END) as extra_title, rate_categories.title", "interpreter_rates,rate_categories", "interpreter_rates.rate_category_id=rate_categories.id ".$append_order_type.$append_language_type." ORDER BY rate_categories.id ASC");
        }
        $interpreter_rates = array();
        $append_extra_title =  !empty($_POST['find_language_type']) ? " (" . $language_types[$_POST['find_language_type']] . ")" : "";
        while ($row_int_rates = $get_interpreter_rates->fetch_assoc()) {
            if (empty($append_extra_title)) {
                $empty_extra_title = " - " . " (" . $row_int_rates['extra_title'] . ")";
                $row_int_rates['title'] = $row_int_rates['title'] . $empty_extra_title;
            } else {
                $row_int_rates['title'] = $row_int_rates['title'] . " - " . $append_extra_title;
            }
            $interpreter_rates[] = $row_int_rates;
        }
        $response = array('status' => 0, 'interpreter_rates' => array());
        if (count($interpreter_rates) > 0) {
            $response['status'] = 1;
            $response['interpreter_rates'] = $interpreter_rates;
        }
        echo json_encode($response);
        exit;
    }
    //Get notifications for all users
    if(isset($_POST['get_notifications']) && isset($_SESSION['userId'])){
        include 'actions.php';
        $order_types_array = array(1 => "Face To Face", 2 => "Telephone", 3 => "Translation");
        $data=array('status' => 0, 'body' => '', 'assigned_date' => '', 'div_id' => '0');
        if (isset($_SESSION)) {
            $user_id = $_SESSION['userId'];
            $get_data=$obj->read_specific("*","assigned_jobs_users","user_id='".$user_id."' AND status=0");
            if(!empty($get_data)){
                $data['status']=1;
                $data['div_id']=time();
                $data['body']="Hello <i>" . $_SESSION['UserName'] . "</i>,<br>You have been assigned a new job to process<br>" . 
                $order_types_array[$get_data['order_type']] . " Job ID: " . $get_data['order_id'] . " assigned on " . $misc->dated($get_data['assigned_date']);
                $data['assigned_date']=$misc->time_elapsed_string($get_data['assigned_date']);
                $obj->update("assigned_jobs_users", array("status" => 1), "user_id=" . $user_id);
            } else {
                //Notify all active users for 3 hours upcoming pending job not yet assigned
                if (!isset($_SESSION['next_notify' . $user_id]) || date('H:i:s') > $_SESSION['next_notify' . $user_id]) {
                    $assignDate = date("Y-m-d");
                    $assignTime = date('H:i:s', strtotime('+3 hour'));
                    $calls_count_f2f = $obj->read_specific("count(*) as counter", "interpreter", "deleted_flag=0 AND order_cancel_flag=0 AND intrpName='' AND assignDate='" . $assignDate ."' AND assignTime<='" . $assignTime ."'")['counter'];
                    $calls_count_tp = $obj->read_specific("count(*) as counter", "telephone", "deleted_flag=0 AND order_cancel_flag=0 AND intrpName='' AND assignDate='" . $assignDate ."' AND assignTime<='" . $assignTime ."'")['counter'];
                    if ($calls_count_f2f > 0 || $calls_count_tp > 0) {
                        $_SESSION['next_notify' . $user_id] = date('H:i:s',time() + 300);
                        $data['status'] = 1;
                        $data['div_id'] = time();
                        $data['body']="Hello <i>" . $_SESSION['UserName'] . "</i>,<br>There are <b class='text-success'>" . $calls_count_f2f . " Face To Face</b> & <b class='text-primary'>" . $calls_count_tp . " Telephone</b> unallocated jobs starting in upcoming 3 hours.<br>" . 
                        "Please process and allocate it to interpreters urgently. Thank you";
                        $data['assigned_date'] = "Just Now";
                    }
                }
            }
        }
        echo json_encode($data);
        exit;
    }
    //Update assigned operator
    if(isset($_POST['update_assigned_operator']) && isset($_SESSION['userId'])){
        include 'actions.php';
        $append_allocation_type = !empty($_POST['assigned_row_id'])?"re-allocate":"allocate";
        $order_types_array = array(1 => "Face To Face", 2 => "Telephone", 3 => "Translation");
        $data=array('message' => 'Failed to ' . $append_allocation_type . ' this job to ' . $_POST['operator_name']);
        if (isset($_SESSION)) {
            if(!empty($_POST['assigned_row_id'])) {
                $obj->update("assigned_jobs_users", array("user_id" => $_POST['operator_id'], "assigned_by" => $_SESSION['userId'], "assigned_date" => date("Y-m-d H:i:s"), "updated_date" => date("Y-m-d H:i:s"), "status" => 0), "id = " . $_POST['assigned_row_id']);
            } else {
                $obj->insert("assigned_jobs_users", array("user_id" => $_POST['operator_id'], "order_type" => $_POST['order_type'], "order_id" => $_POST['job_id'], "assigned_by" => $_SESSION['userId'], "created_date" => date("Y-m-d H:i:s"), "assigned_date" => date("Y-m-d H:i:s")));
                $data['assigned_row_id'] = $obj->con->insert_id;
            }
            $data['message']=$order_types_array[$_POST['order_type']] . " Job ID: " . $_POST['job_id'] . " has been " . $append_allocation_type . "d to " . $_POST['operator_name'] . " successfully";
        }
        echo json_encode($data);
        exit;
    }
    //Get update celebration of a user
    if(isset($_POST['update_celebration']) && !empty($_POST['update_celebration'])){
        include 'actions.php';
        $data = array('status' => 0);
        $done = $obj->update("login", array("celebrate" => 0), "id=" . $_POST['update_celebration']);
        if ($done) {
            $data['status'] = 1;
        }
        echo json_encode($data);
        exit;
    }
    //Connect the call hosted by LSUK 
    if(isset($_POST['connect_the_call']) && !empty($_POST['connect_the_call'])){
        include 'actions.php';
        $data = array('status' => 0, "message" => "");
        $connected_user = $_SESSION['UserName'];
        $connected_date = date("Y-m-d H:i:s");
        $done = $obj->update("telephone", array("connected_by" => $_SESSION['userId'], "connected_date" => $connected_date), "id=" . $_POST['connect_the_call']);
        if ($done) {
            $obj->insert("daily_logs", array("action_id" => 36, "user_id" => $_SESSION['userId'], "details" => "TP Job ID: " . $_POST['connect_the_call']));
            $data['status'] = 1;
            $data['message'] = "<small title='Call connected by $connected_user' class='text-success'><b>" . $connected_user . " <i class='fa fa-check-circle text-success'></i><br>" . $connected_date . "</b></small>";
        }
        echo json_encode($data);
        exit;
    }

    if (isset($_POST['mark_join_time']) && !empty($_POST['mark_join_time'])) {
        include 'actions.php';
        $data = array('status' => 0, "message" => "");
        if (!isset($_SESSION['web_userId']) || empty($_SESSION['web_userId'])) {
                $data['message'] = "Unauthorized access.";
                echo json_encode($data);
                exit;
            }
            $connected_user = $_SESSION['web_UserName'];
            $mark_join_time = date("Y-m-d H:i:s");
            $job_id = $_POST['mark_join_time'];
            // Fetch job assign date and time
            $get_details = $obj->read_specific("assignDate,assignTime", "telephone", "id=" . $job_id);
            if ($get_details) {
                $assignDateTime = strtotime($get_details['assignDate'] . ' ' . $get_details['assignTime']);
                $currentTime = time() + (5 * 60); // Allow marking 5 minutes before start
                $allowedTime = $assignDateTime;
                $data["test"] =  date('Y-m-d H:i:s');
                if ($currentTime >= $allowedTime) {
                    $update = array(
                        "connected_by" => $_SESSION['web_userId'],
                        "mark_join_time" => $mark_join_time
                    );
                    $done = $obj->update("telephone", $update, "id=" . $job_id);
                    if ($done) {
                        $obj->insert("daily_logs", array(
                            "action_id" => 36,
                            "user_id" => $_SESSION['web_userId'],
                            "details" => "TP Job ID: " . $job_id
                        ));
                        $data['status'] = 1;
                        $data['message'] = "<small title='Join Time Marked by $connected_user' class='text-success'>By: $connected_user<b><i class='fa fa-check-circle text-success'></i><br>Session Started: $mark_join_time</b></small>";
                    } else {
                        $data['message'] = "Failed to update join time.";
                    }
                } else {
                    $data['message'] = "You can only mark join time within 5 minutes before the job starts.";
                }
            } else {
                $data['message'] = "Job not found.";
            }
            echo json_encode($data);
            exit;
        }
    //Get non connected calls 
    if(isset($_POST['get_non_connected_calls']) && !empty($_POST['get_non_connected_calls'])){
        include 'actions.php';
        $data = array('calls_count' => 0);
        $assignDate = date("Y-m-d");
        $assignTimeNow = date('H:i:s');
        $assignTime = date('H:i:s', strtotime('+15 minute'));
        $calls_count = $obj->read_specific("count(*) as counter", "telephone", "deleted_flag=0 AND order_cancel_flag=0 AND intrpName!='' AND hostedBy=1 AND connected_by IS NULL AND hoursWorkd=0 AND assignDate='" . $assignDate ."' AND assignTime BETWEEN '" . $assignTimeNow ."' AND '" . $assignTime ."'")['counter'];
        $data['calls_count'] = $calls_count;
        echo json_encode($data);
        exit;
    }
    //Check the job by LSUK operator 
    if(isset($_POST['check_the_job']) && isset($_POST['job_id'])){
        include 'actions.php';
        $order_types_array = array("F2F" => "interpreter", "TP" => "telephone", "TR" => "translation");
        $data = array('status' => 0, "message" => "");
        $checked_user = $_SESSION['UserName'];
        $checked_date = date("Y-m-d H:i:s");
        $show_checked_date = date("d-m-Y");
        $done = $obj->update($order_types_array[$_POST['job_type']], array("checked_by" => $_SESSION['userId'], "checked_date" => $checked_date), "id=" . $_POST['job_id']);
        if ($done) {
            $obj->insert("daily_logs", array("action_id" => 33, "user_id" => $_SESSION['userId'], "details" => $_POST['job_type'] . " Job ID: " . $_POST['job_id']));
            $data['status'] = 1;
            if ($_POST['screen'] == "booking") {
                $data['message'] = '<br><small class="text-primary" title="This job has been checked by ' . $checked_user . " on " . $show_checked_date . '"><b>' . $checked_user . " <i class='fa fa-thumbs-up'></i> <br> " . $show_checked_date . '</b></small>';
            } else {
                $data['message'] = '<small class="w3-border w3-border-black text-primary pull-right" title="This job has been checked by ' . $checked_user . " on " . $show_checked_date . '" style="margin: 8px 10px;padding:2px;"><b>' . $checked_user . " on " . $show_checked_date . '</b></small>';
            }
        }
        echo json_encode($data);
        exit;
    }
    //Populate Duplicate Jobs
    if(isset($_POST['bk_source']) && isset($_POST['bk_assignDate']) && isset($_POST['bk_assignTime']) && isset($_POST['bk_type'])){
        include 'actions.php';
        $tbl="";
        $bk_type = $_POST['bk_type'];
        $bk_source = $_POST['bk_source'];
        $bk_assignDate = $_POST['bk_assignDate'];
        $bk_assignTime = $_POST['bk_assignTime'];
        $count=1;

        $fetch_match = $obj->read_all("'Face To Face' as job_type,interpreter.id,interpreter.source,interpreter.target,interpreter.intrpName,interpreter.assignDate,interpreter.assignTime,interpreter.assignDur,interpreter.orgName,interpreter.orgRef,interpreter.orgContact,interpreter.nameRef,interpreter.submited FROM interpreter where interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 AND interpreter.source='$bk_source' AND interpreter.assignDate='$bk_assignDate' AND interpreter.assignTime='$bk_assignTime' UNION ALL
        SELECT 'Telephone' as job_type,telephone.id,telephone.source,telephone.target,telephone.intrpName,telephone.assignDate,telephone.assignTime,telephone.assignDur,telephone.orgName,telephone.orgRef,telephone.orgContact,telephone.nameRef,telephone.submited","telephone","telephone.deleted_flag=0 and telephone.order_cancel_flag=0 AND telephone.source='$bk_source' AND telephone.assignDate='$bk_assignDate' AND telephone.assignTime='$bk_assignTime' ");

        $tbl .= '<table class="table table-bordered tbl_data" cellspacing="0" cellpadding="0">
        <thead class="bg-primary">
            <tr>
                <td>Sr.No</td>
                <td>Job Type</td>
                <td>Job Ref</td>
                <td>Language</td>
                <td>Assign-Date</td>
                <td>Duration</td>
                <td>Company</td>
                <td>Contact Name</td>
                <td>Submitted By</td>
                <td>Ref Name</td>
                <td>Currently at</td>
            </tr>
        </thead>
        <tbody>';
        if(mysqli_num_rows($fetch_match)>0){
            $tbl.="<h1 class='text-center' style='color:#ff0000;'> Possible Dupicates Found </h1>";
            while($row = mysqli_fetch_assoc($fetch_match)){
                $tbl.="<tr>
                <td>$count</td>
                <td>".$row['job_type']." </td>
                <td>".$row['nameRef']." </td>
                <td>".$row['source']." to ".$row['target']."</td>
                <td>".$row['assignDate']." ".$row['assignTime']."</td>
                <td>".$row['assignDur']." </td>
                <td>".$row['orgName']." </td>
                <td>".$row['orgContact']." </td>
                <td>".$row['submited']." </td>
                <td>".$row['orgRef']." </td>
                <td>".(!empty($row['intrpName'])?"Booking List":"Home Screen")." </td>
                </tr>";
                $count++;
            }
        }else{
            $tbl.="<h1 class='text-center' style='color:#156c00;'> No Duplicates Found </h1>";
            $tbl.="<tr class='text-center'> <td colspan='10'> No matching Records </td></tr>";
        } 
        $tbl.='</tbody>
        </table>';
        $data['body']=$tbl;
        $data['matches']=$count-1;
        echo json_encode($data);
        exit;
    }
    //View text messages for interpreters for specific job
    if(isset($_POST['view_text_messages']) && isset($_SESSION['userId'])){
        include 'actions.php';
        include '../source/setup_sms.php';
        $setupSMS = new setupSMS;
        $order_types_array = array(1 => "Face To Face", 2 => "Telephone", 3 => "Translation");
        $data = array('status' => 0, 'body' => '');
        if (isset($_SESSION)) {
            $contact_no = $setupSMS->format_phone($_POST['contact_no'], $_POST['country']);
            $country = $_POST['country'];
            $data['status'] = 1;
            $user_id = $_SESSION['userId'];
            $array_category = array(1 => "Job Availability", 2 => "Job Assigned", 3 => "Job Cancelled", 4 => "Job Amended", 5 => "1 Day Before Reminder", 6 => "Bided Job Confirmation", 7 => "Same Day Job Reminder");
            $array_message_status = array(0 => "<i title='Message not delivered to interpreter' class='fa fa-2x pull-right fa-remove text-danger'></i>", 1 => "<i title='Message delivered successfully' class='fa fa-2x pull-right fa-check text-success'></i>", 2 => "<i title='Interprerter responded back [RESPONSE_DATE]' class='fa fa-2x pull-right fa-refresh text-success'></i>");
            $data['body'] = "<h3 class='text-center'>Messages to <b>" . $_POST['interpreter_name'] . "</b> for " . $order_types_array[$_POST['job_type']] . " order ID # " . $_POST['job_id'] . "</h3>";
            $get_sent_messages = $obj->read_all("job_messages.*,login.name", "job_messages,login", "job_messages.created_by=login.id AND order_type=" . $_POST['job_type'] . " AND order_id=" . $_POST['job_id'] . " AND interpreter_id=" . $_POST['interpreter_id'] . " ORDER BY id DESC");
            $data['body'] .= '<div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h4 class="panel-title">
                  <button class="btn btn-primary" data-toggle="collapse" href="#collapse1"><b>Send New Message</b></button>
                </h4>
              </div>
              <div id="collapse1" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="form-group">
                        <label>Interpreter Name : <span id="write_interpreter_name">' . $_POST['interpreter_name'] . '</span></label><br>
                        <label>Interpreter Country : <span id="write_interpreter_country">' . $country . '</span></label><br>
                        <label>Contact Number : <input id="write_interpreter_phone" type="text" placeholder="Recipient Contact Number" value="' . $contact_no . '"></label><br>
                    </div>
                    <div class="form-group">
                        <label>Write message details</label><b class="character_count pull-right"></b>
                        <input type="hidden" id="write_order_id" value="' . $_POST['job_id'] . '"/>
                        <input type="hidden" id="write_order_type" value="' . $_POST['job_type'] . '"/>
                        <input type="hidden" id="write_interpreter_id" value="' . $_POST['interpreter_id'] . '"/>
                        <textarea id="message_body" rows="5" class="form-control" placeholder="Write message here ..."></textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" onclick="send_text_message(this)" class="btn btn-success" id="btn_send_text_message">Click To Send</button>
                    </div>
                </div>
                <div class="panel-footer text-danger">Note: Numbers should not contain initial 0 or + sign, Valid <b>12 digits</b> number sample: 447366312487</div>
              </div>
            </div>
          </div>
          <script>
            $(document).ready(function() {
                var textarea = $("#message_body");
                var characterCount = $(".character_count");
                var initialCharCount = textarea.val().length;
                characterCount.text("Characters: " + initialCharCount + "/120");
                textarea.on("input", function() {
                    var inputText = textarea.val();
                    if (inputText.length > 120) {
                        $(".character_count").addClass("text-danger");
                        inputText = inputText.substring(0, 120);
                        textarea.val(inputText);
                    } else {
                        $(".character_count").removeClass("text-danger");
                    }
                    var currentCharCount = inputText.length;
                    characterCount.text("Characters: " + currentCharCount + "/120");
                });
            });
            </script>';
            if ($get_sent_messages->num_rows > 0) {
                $data['body'] .= "<table class='table table-bordered'>
                    <thead><tr class='bg-primary'>
                        <td width='18%'>Category</td>
                        <td width='18%'>Receiver Phone Number</td>
                        <td width='18%'>Status</td>
                        <td>Response</td>
                        <td>Message</td>
                        </tr></thead>";
                while ($row = $get_sent_messages->fetch_assoc()) {
                    $response_date = !is_null($row['response_date']) ? date("d-m-Y H:i:s", strtotime($row['response_date'])) : "";
                    if ($row['status'] == 2) {
                        // $can_do = $row['can_do'] == 1 ? "<small class='label label-success'>Available</small>" : "<small class='label label-danger'>Not Available</small>";
                        if($row['can_do'] == 1){
                        $can_do = "<small class='label label-success'>Available</small><br>";
                        }else if($row['can_do'] == 3){
                        $can_do = "<small class='label label-warning'>Alternatively Available</small><br>";
                        }else{
                        $can_do = "<small class='label label-danger'>Not Available</small><br>";
                        }
                    } else {
                        if ($row['status'] == 0) {
                            $can_do = "<small style='margin-bottom:10px !important;' title='Failed to deliver this SMS to " . $row['sent_to'] . "' class='label label-danger'>SMS Failed</small>";
                        } else {
                            $can_do = is_null($row['response_date']) ? "<small style='margin-bottom:10px !important;' class='label label-warning'>No Response</small><br>" : "";
                        }
                    }
                    $msg_status = str_replace("[RESPONSE_DATE]", $response_date, $array_message_status[$row['status']]);
                    $data['body'] .= "<tr>
                        <td>" . $array_category[$row['message_category']] . "<br><small>Sent By " . ucwords($row['name']) . "</small></td>
                        <td>" . $row['sent_to'] . "<br><small>Sent at " . date("d-m-Y H:i", strtotime($row['created_date'])) . "</small></td>
                        <td>" . $msg_status . $can_do . "<br>" . ($row['action_by'] == 1 ? "<span class='label label-success'>By LSUK Staff</span>" : "<span class='label label-danger'>By Client Portal</span>") . "</td>
                        <td style='font-size: 10px'>" . (!is_null($row['response_date']) ? "Date: " . date("d-m-Y H:i:s", strtotime($row['response_date'])) . "<br>" : "") . ($row['response_message'] ? $row['response_message'] : "No reply") . "</td>
                        <td style='font-size: 10px'>" . nl2br($row['message_body']) . "</td>
                    </tr>";
                }
                
            } else {
                $data['body'] .= "<p class='text-center text-danger'>No messages sent to " . $_POST['interpreter_name'] . " yet for this job!</p>";
            }
        }
        echo json_encode($data);
        exit;
    }
    //View text messages for interpreters for specific job
    if(isset($_POST['view_message_response']) && isset($_SESSION['userId'])){
        include 'actions.php';
        include '../source/setup_sms.php';
        $setupSMS = new setupSMS;
        $order_types_array = array(1 => "Face To Face", 2 => "Telephone", 3 => "Translation");
        $data = array('status' => 0, 'body' => '');
        if (isset($_SESSION)) {
            $contact_no = $setupSMS->format_phone($_POST['contact_no'], $_POST['country']);
            $country = $_POST['country'];
            $data['status'] = 1;
            $user_id = $_SESSION['userId'];
            $array_category = array(1 => "Job Availability", 2 => "Job Assigned", 3 => "Job Cancelled", 4 => "Job Amended", 5 => "1 Day Before Reminder", 6 => "Bided Job Confirmation", 7 => "Same Day Job Reminder");
            $array_message_status = array(0 => "<i title='Message not delivered to interpreter' class='fa fa-2x pull-right fa-remove text-danger'></i>", 1 => "<i title='Message delivered successfully' class='fa fa-2x pull-right fa-check text-success'></i>", 2 => "<i title='Interprerter responded back [RESPONSE_DATE]' class='fa fa-2x pull-right fa-refresh text-success'></i>");
            $data['body'] = "<h3 class='text-center'>Messages to <b>" . $_POST['interpreter_name'] . "</b> for " . $order_types_array[$_POST['job_type']] . " order ID # " . $_POST['job_id'] . "</h3>";
            $get_sent_messages = $obj->read_all("job_messages.*,login.name", "job_messages,login", "job_messages.created_by=login.id AND order_type=" . $_POST['job_type'] . " AND order_id=" . $_POST['job_id'] . " AND interpreter_id=" . $_POST['interpreter_id'] . " ORDER BY id DESC");
            $data['body'] .= '<div class="panel-group">
            <div class="panel panel-default">
              <div id="collapse1" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="form-group">
                        <label>Interpreter Name : <span id="write_interpreter_name">' . $_POST['interpreter_name'] . '</span></label><br>
                        <label>Interpreter Country : <span id="write_interpreter_country">' . $country . '</span></label><br>
                        <label>Contact Number : <input id="write_interpreter_phone" type="text" placeholder="Recipient Contact Number" value="' . $contact_no . '"></label><br>
                    </div>
                    <div class="form-group">
                        <label>Write message details</label><b class="character_count pull-right"></b>
                        <input type="hidden" id="write_order_id" value="' . $_POST['job_id'] . '"/>
                        <input type="hidden" id="write_order_type" value="' . $_POST['job_type'] . '"/>
                        <input type="hidden" id="write_interpreter_id" value="' . $_POST['interpreter_id'] . '"/>
                        <textarea id="message_body" rows="5" class="form-control" placeholder="Write message here ..."></textarea>
                    </div>
                    <div class="form-group">
                        <button type="button" onclick="send_text_message(this)" class="btn btn-success" id="btn_send_text_message">Click To Send</button>
                    </div>
                </div>
                <div class="panel-footer text-danger">Note: Numbers should not contain initial 0 or + sign, Valid <b>12 digits</b> number sample: 447366312487</div>
              </div>
            </div>
          </div>
          <script>
            $(document).ready(function() {
                var textarea = $("#message_body");
                var characterCount = $(".character_count");
                var initialCharCount = textarea.val().length;
                characterCount.text("Characters: " + initialCharCount + "/120");
                textarea.on("input", function() {
                    var inputText = textarea.val();
                    if (inputText.length > 120) {
                        $(".character_count").addClass("text-danger");
                        inputText = inputText.substring(0, 120);
                        textarea.val(inputText);
                    } else {
                        $(".character_count").removeClass("text-danger");
                    }
                    var currentCharCount = inputText.length;
                    characterCount.text("Characters: " + currentCharCount + "/120");
                });
            });
            </script>';
            if ($get_sent_messages->num_rows > 0) {
                $data['body'] .= "<table class='table table-bordered'>
                    <thead><tr class='bg-primary'>
                        <td>Message</td>
                        <td>Response</td>
                        </tr></thead>";
                while ($row = $get_sent_messages->fetch_assoc()) {
                    $response_date = !is_null($row['response_date']) ? date("d/m/Y H:i", strtotime($row['response_date'])) : "";
                    if ($row['status'] == 2) {
                        $can_do = $row['can_do'] == 1 ? "<small class='label label-success'>Available</small>" : "<small class='label label-danger'>Not Available</small>";
                    } else {
                        if ($row['status'] == 0) {
                            $can_do = "<small title='Failed to deliver this SMS to " . $row['sent_to'] . "' class='label label-danger'>SMS Failed</small>";
                        } else {
                            $can_do = is_null($row['response_date']) ? "<small class='label label-warning'>No Response</small>" : "";
                        }
                    }
                    $msg_status = str_replace("[RESPONSE_DATE]", $response_date, $array_message_status[$row['status']]);
                    $data['body'] .= "<tr>
                        
                        
                        <td style='font-size: 10px'>" . nl2br($row['message_body']) . "</td>
                        <td style='font-size: 10px'>" . (!is_null($row['response_date']) ? "Date: " . date("d/m/Y H:i", strtotime($row['response_date'])) . "<br>" : "") . ($row['response_message'] ? $row['response_message'] : "No reply") . "</td>
                        
                    </tr>";
                }
                
            } else {
                $data['body'] .= "<p class='text-center text-danger'>No messages sent to " . $_POST['interpreter_name'] . " yet for this job!</p>";
            }
        }
        echo json_encode($data);
        exit;
    }
    //View log changes for a table's specific record/row
    if(isset($_POST['view_log_changes']) && isset($_POST['record_id'])  && isset($_POST['table_name']) && isset($_SESSION['userId'])){
        include 'actions.php';
        $data = array('status' => 0, 'body' => '');
        $actions_array = array("update" => "<small class='text-primary'>Updated <i class='fa fa-edit'></i></small>", "create" => "<small class='text-success'>Created <i class='fa fa-plus'></i></small>", "delete" => "<small class='text-danger'>Deleted <i class='fa fa-trash'></i></small>");
        if (isset($_SESSION)) {
            $data['status'] = 1;
            $data['body'] = "<h3 class='text-center'>Log History for <b>" . $_POST['table_name_label'] . "</b> " . $_POST['record_label'] . " ID # " . $_POST['record_id'] . "</h3>";
            $get_logs = $obj->read_all("*, DATE(created_date) as date_group", "audit_logs", "table_name='" . $_POST['table_name'] . "' AND record_id=" . $_POST['record_id'] . " ORDER BY id DESC");
            if ($get_logs->num_rows > 0) {
                while ($row = $get_logs->fetch_assoc()) {
                    $data_row[$row['date_group']][] = $row;
                }
                $data['body'] .= '<div class="panel-group">
                    <div class="panel panel-default">';
                    foreach ($data_row as $date => $records) {
                        $data['body'] .= '
                        <div class="panel-heading">
                            <h4 class="panel-title">
                            <button class="btn btn-block btn-primary" data-toggle="collapse" href="#collapse_' . $date . '"><b>Edited Logs History For Date: ' . $misc->dated($date) . '</b></button>
                            </h4>
                        </div>
                        <div id="collapse_' . $date . '" class="panel-collapse collapse">
                            <div class="panel-body table-responsive">
                                <table class="table table-bordered table-history">
                                    <thead><tr class="bg-info">
                                        <td width="13%">Field</td>
                                        <td width="5%">Action</td>
                                        <td width="12%">User/Date-Time</td>
                                        <td width="35%">Old Value</td>
                                        <td width="35%">New Value</td>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                foreach ($records as $record) {
                                    if (strpos($record['field_name'], "Rate ID") !== false || strpos($record['field_name'], "Rate Data") !== false) {
                                        continue;
                                    }
                                    $data['body'] .= "<tr>
                                        <td>" . $record['field_name'] . "<br>" . ($record['action_by'] == 1 ? "<span class='label label-success'>By LSUK Staff</span>" : "<span class='label label-danger'>By Client Portal</span>") . "</td>
                                        <td>" . $actions_array[$record['action']] . "<br><small class='text-muted'>" . $record['ip_address'] . "</small></td>
                                        <td>" . $record['user_name'] . " (#{$record['user_id']})<br><small>" . date("d-m-Y H:i:s", strtotime($record['created_date'])) . "</small></td>
                                        <td><div style='word-wrap: break-word;max-width: 450px;'><small>" . ($record['old_value'] != strip_tags($record['old_value']) ? str_replace("<p>&nbsp;</p>", "", $record['old_value']) : $record['old_value']) . "</small></div></td>
                                        <td><div style='word-wrap: break-word;max-width: 450px;'><small class='text-success'>" . ($record['new_value'] != strip_tags($record['new_value']) ? str_replace("<p>&nbsp;</p>", "", $record['new_value']) : $record['new_value']) . "</small></div></td>
                                    </tr>";
                                }
                            $data['body'] .= '</tbody>
                                </table>
                            </div>
                        </div>';
                    }
                    $data['body'] .= '</div>
                </div>';
                
            } else {
                $data['body'] .= "<p class='text-center text-danger'>No log history found for <b>" . $_POST['table_name_label'] . "</b> " . $_POST['record_label'] . " ID # " . $_POST['record_id'] . "</p>";
            }
        }
        echo json_encode($data);
        exit;
    }
    //Populate Duplicate Company
    if (isset($_GET['action']) && $_GET['action'] == 'check_duplicate_company') {
        include 'actions.php';
        $tbl = '';
        $abrv = $_GET['abrv'];
        $count = 1;

        $fetch_match = $obj->read_all("*", "comp_reg", "abrv = '" . $abrv . "'");

        $tbl .= '<table class="table table-bordered tbl_data" cellspacing="0" cellpadding="0">
        <thead class="bg-primary">
            <tr>
                <td>Name</td>
                <td>Company Type</td>
                <td>Phone</td>
                <td>Email</td>
                <td>City</td>
                <td>Country</td>
            </tr>
        </thead>
        <tbody>';
        if (mysqli_num_rows($fetch_match) > 0) {
            $tbl .= "<h1 class='text-center' style='color:#ff0000;'> Possible Dupicate Found </h1>";
            while ($row = mysqli_fetch_assoc($fetch_match)) {
                $tbl .= "<tr>
                <td>" . $row['name'] . " </td>
                <td>" . $row['compType'] . " </td>
                <td>" . $row['contactNo1'] . "</td>
                <td>" . $row['email'] . "</td>
                <td>" . $row['city'] . " </td>
                <td>" . $row['country'] . " </td>
                </tr>";
            }
        } else {
            $tbl .= "<h1 class='text-center' style='color:#156c00;'> No Duplicates Found </h1>";
            $tbl .= "<tr class='text-center'> <td colspan='10'> No matching Records </td></tr>";
        }
        $tbl .= '</tbody>
        </table>';

        $data['body'] = $tbl;
        $data['matches'] = mysqli_num_rows($fetch_match);
        echo json_encode($data);
        exit;
    }
    ?>
    