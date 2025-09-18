<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'db.php';
include 'class.php';
if ($_SESSION['is_root'] == 1) {
    $managment = 1;
} else {
    $managment = 0;
}
$allowed_type_idz = "14,79,91,158,172,182";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $acttObj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Update Purchase Order</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Update Purchase Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style>
        .b {
            color: #fff;
        }

        a:link,
        a:visited {
            color: #337ab7;
        }

        .multiselect {
            min-width: 370px;
        }

        .multiselect-container {
            max-height: 320px;
            overflow-y: auto;
            max-width: 380px;
        }

        .glyphicon-remove-circle {
            font-size: 20px;
        }
    </style>
</head>

<body>
    <?php
    $orgName = @$_GET['orgName'];
    $porder = @$_GET['porder'];
    $porder_check = empty($porder) ? "" : "and porder='$porder'";
    $purch_id = @$_GET['purch_id'];
    $table = @$_GET['table'];
    if (isset($_GET['remove_purch'])) {
        if ($_GET['remove_purch'] == 1) {
            $acttObj->update($table, array("porder" => ''), array("id" => $purch_id));
            echo "<script>window.close();</script>";
        }
    }
    if ($table == 'interpreter') {
        $alltotal_charges_comp = $acttObj->read_specific("(total_charges_comp*cur_vat)+total_charges_comp+C_otherexpns as alltotal_charges_comp", "$table", "id=" . $purch_id)['alltotal_charges_comp'];
    } else {
        $alltotal_charges_comp = $acttObj->read_specific("(total_charges_comp*cur_vat)+total_charges_comp as alltotal_charges_comp", "$table", "id=" . $purch_id)['alltotal_charges_comp'];
    }
    $final_sum = round($alltotal_charges_comp, 2); ?>
    <script>
        function refreshParent() {
            window.opener.location.reload();
        }

        function myFunction() {
            var x = document.getElementById("orgName").value;
            if (!x) {
                x = "<?php echo $orgName; ?>";
            }
            var y = document.getElementById("porder").value;
            if (!y) {
                y = "<?php echo $porder; ?>";
            }
            var z = "<?php echo $purch_id; ?>";
            var p = "<?php echo $table; ?>";
            window.location.href = "<?php echo basename(__FILE__); ?>" +
                '?orgName=' + x + '&porder=' + y + '&purch_id=' + z + '&table=' + p;
        }
    </script>
    <?php
    //valid orgname? 
    $query = "SELECT abrv FROM comp_reg where abrv='$orgName' and po_req=1";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $valid_orgName = $row['abrv'];

    //$rem_credit='';
    if ($valid_orgName) {
        //PO rem_credit for org and po no.
        $query_2 = "SELECT porder, company as orgName,balance,dated from porder_details 
    where expired=0 and company='$valid_orgName' and balance > 0.01 and balance>=$final_sum $porder_check ORDER BY dated DESC";
        $result_2 = mysqli_query($con, $query_2);
        while ($row_2 = mysqli_fetch_assoc($result_2)) {
            $rem_credit = @$row_2['balance'];
            $valid_orgName = $row_2['orgName'];
        }
        if (isset($_POST['yes'])) {
            $custom_po_validation = "";
            $new_po = '';
            $new_po = $_POST['porder'];
            $custom_po = (isset($_POST['custom_po']) && $_POST['custom_po'] != '') ? $_POST['custom_po'] : '';
            if (!empty($custom_po)) {
                $custom_po_validation = $acttObj->read_specific("id", "comp_credit", " porder='$custom_po' ")["id"];
                if (!empty($custom_po_validation)) {
                    $new_po = $_POST['custom_po'];
                }
            }
            if (!empty($new_po) && (empty($custom_po) || !empty($custom_po_validation))) {
                $array_types = array("interpreter" => "f2f", "telephone" => "tp", "translation" => "tr");
                // $data=$_POST['porder'];
                $acttObj->editFun($table, $purch_id, 'porder', $new_po);
                $data = $_POST['rem_credit'];
                $acttObj->editFun($table, $purch_id, 'rem_credit', $data);
                if (!empty($table) && !empty($purch_id)) {
                    $po_requested_ids = $acttObj->read_specific("GROUP_CONCAT(id) as po_requested", "po_requested", "order_id=" . $purch_id . " and order_type='" . $array_types[$table] . "'")["po_requested"];
                    if (!empty($po_requested_ids)) {
                        $acttObj->delete("po_requested", "id IN (" . $po_requested_ids . ")");
                    }
                }
                if ($managment == 0) {
                    echo '<script>window.onunload = refreshParent;</script>';
                }
                echo "<script>window.close();</script>";
                $acttObj->insert("daily_logs", array("action_id" => 15, "user_id" => $_SESSION['userId'], "details" => strtoupper($array_types[$table]) . " Job ID: " . $purch_id));

                $acttObj->update("mult_inv_items", array("po_order=>$new_po"), array("main_job_id => $purch_id"));
            } else {
                echo '<script>alert("Custom Purchase Order is not Valid");</script>';
            }
        }
        if (isset($_POST['no'])) {
            echo "<script>window.close();</script>";
        } ?>
        <div align="center" class="container">
            <h1>Record ID: <span class="label label-danger"><?php echo @$_GET['purch_id']; ?></span></h1><br />
            <form action="" method="post" class="col-md-6">
                <p class="text-center text-danger"><b>NOTE :</b> Total Invoice Amount for this job is : <?php echo '<b>' . $final_sum . '</b>'; ?></p>
                <div class="form-group">
                    <label>Organization</label><br />
                    <select id="orgName" name="orgName" onChange="myFunction()" required class="form-control multi_class">
                        <?php
                        //list Organisations that have PO
                        $sql_opt = "SELECT distinct name,abrv,status 
    FROM comp_reg where comp_reg.po_req=1 ORDER BY comp_reg.name ASC";
                        $result_opt = mysqli_query($con, $sql_opt);
                        $options = "";
                        while ($row_opt = mysqli_fetch_assoc($result_opt)) {
                            $code = $row_opt["abrv"];
                            $status = $row_opt["status"];
                            $name_opt = $row_opt["name"];
                            if ($orgName == $code) {
                                $fulname = $name_opt;
                                $abrivation = $code;
                            }
                            $options .= "<option value='$code'>" . $name_opt . '<span style="color:#F00;">(' . $status . ')</span></option>';
                        } ?>
                        <option value="<?php echo $abrivation; ?>"><?php echo $fulname; ?></option>
                        <option value="" disabled>Select Company</option>
                        <?php echo $options; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Purchase Order # <br><input type="checkbox" id="all_porders" name="all_porders"> <span style="font-weight:400;">Show 0 Balance Purchase orders</span></label>
                    <br />
                    <select id="porder" name="porder" onChange="myFunction()" class="form-control multi_class">
                        <?php
                        // $sql_opt="SELECT porder From porder_details where expired=0 and company='$orgName' and balance > 0.01 and balance>=$final_sum";
                        $sql_opt = "SELECT porder From porder_details where company='$orgName'";
                        $get_current_po = 0;
                        $get_current_po = $acttObj->read_specific("porder", "$table", " id=$purch_id ")['porder'];
                        $result_opt = mysqli_query($con, $sql_opt);
                        $options = "";
                        $cur_remaining = "";
                        while ($row_opt = mysqli_fetch_assoc($result_opt)) {
                            $code = trim($row_opt["porder"]);
                            $name_opt = $row_opt["porder"];
                            $sum_amount = $acttObj->read_specific(" MAX(credit) as po_balance ", "comp_credit", " porder='$name_opt' AND deleted_flag=0 ")['po_balance'];
                            $porder_inv = $acttObj->read_specific(" SUM(total_cost) as used_credit ", "(SELECT COUNT(interpreter.id) as num_inv,round(IFNULL(sum(interpreter.total_charges_comp),0)+ IFNULL(sum(interpreter.total_charges_comp * interpreter.cur_vat),0) +IFNULL(sum(interpreter.C_otherexpns),0),2) as total_cost FROM interpreter where interpreter.porder='$name_opt' AND interpreter.deleted_flag=0 and interpreter.order_cancel_flag=0 AND interpreter.commit=1 and interpreter.invoic_date!='1001-01-01'  UNION ALL SELECT COUNT(telephone.id) as num_inv,round(IFNULL(sum(telephone.total_charges_comp),0)+ IFNULL(sum(telephone.total_charges_comp * telephone.cur_vat),0),2) as total_cost FROM telephone WHERE telephone.porder='$name_opt' AND telephone.deleted_flag=0 and telephone.order_cancel_flag=0 AND telephone.commit=1 and telephone.invoic_date!='1001-01-01' UNION ALL SELECT COUNT(translation.id) as num_inv,round(IFNULL(sum(translation.total_charges_comp),0)+ IFNULL(sum(translation.total_charges_comp * translation.cur_vat),0),2) as total_cost FROM translation ", "translation.porder='$name_opt' AND translation.deleted_flag=0 and translation.order_cancel_flag=0 AND translation.commit=1 and translation.invoic_date!='1001-01-01') as grp")['used_credit'];
                            $remaining = 0;
                            $remaining = round($sum_amount - $porder_inv, 2);
                            if (($porder == "" && $code == $get_current_po) || ($porder <> "" && $porder == $code)) {
                                $cur_remaining = $remaining;
                            }
                            $options .= "<option value='$code' id='" . $code . "_" . $remaining . "' class='porders_num " . ($remaining <= 1 ? 'hide zero_balance' : '') . "' " . ($porder == "" && $code == $get_current_po ? 'selected' : (($porder == $code) ? 'selected' : '')) . ">" . $name_opt . " (Remaining: $remaining)</option>";
                        } ?>
                        <option value="">Select Purchase Order #</option>
                        <?php echo $options; ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="button" class="btn btn-primary" name="remove_purch" id="remove_purch" value="Remove Purchase Order" />
                </div>
                <div class="form-group">
                    <?php if (!empty($porder)) {
                        $porder_for = $acttObj->read_specific("porder_for", "porder_details", "porder='" . $porder . "'")['porder_for'];
                        if (!empty($porder_for)) {
                            $get_references = $acttObj->read_all("reference", "comp_ref", "id IN (" . $porder_for . ")");
                            echo "Company Reference list to use this Purchase Order:";
                            echo "<h4>";
                            while ($row_ref = $get_references->fetch_assoc()) {
                                echo "<label class='label label-primary'>" . $row_ref["reference"] . "</label> ";
                            }
                            echo "</h4>";
                        } else {
                            echo "<h4><label class='label label-warning'>No company reference assigned to this Purchase Order!</label></h4>";
                        }
                        echo "Current Reference For Order:<br><h4><label class='label label-info'>" . $acttObj->read_specific("comp_ref.reference", "comp_ref,$table", "comp_ref.id=$table.reference_id AND $table.id='" . $purch_id . "'")['reference'] . "</label></h4>";
                    } ?>
                </div>
                <div class="form-group">
                    <label>Remaining Credit</label><br />
                    <input style="width:80%;" type="text" name="rem_credit" id="rem_credit" value="<?php echo $cur_remaining; ?>" required readonly="readonly" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Add Custom Purchase Order # <br> <small style="color:#a94442;">NOTE: Not to fill if the required Purchase order is found above</small></label><br />
                    <input style="width:80%;" type="text" name="custom_po" id="custom_po" class="form-control" />
                </div>
                <div class="form-group">
                    <h4>Are you sure to <span class="text-danger"><b>amend</b></span> this booking Purchase Order ?</h4>
                    <input type="submit" class="btn btn-primary" name="yes" value="Yes >" />&nbsp;&nbsp;
                    <input type="submit" class="btn btn-warning" name="no" value="No" />
                </div>
            </form>
        </div>
    <?php
    } else { ?><h3 align="center">Purchase Orders are not enabled for this Company!</h3>
    <?php } ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script>
        $(function() {
            $('.multi_class').multiselect({
                includeSelectAllOption: true,
                numberDisplayed: 1,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true
            });
        });
        $(document).ready(function() {
            $(document).on('change', '#all_porders', function() {
                if ($(this).is(":checked")) {
                    $('.zero_balance').removeClass('hide');
                } else if ($(this).is(":not(:checked)")) {
                    $('.zero_balance').addClass('hide');
                }
            });
            $(document).on('change', '#porder', function() {
                var new_po = $(this).val();
                console.log(new_po);
                // $('#rem_credit').val(new_po);
            });
            $(document).on('click', '#remove_purch', function() {
                var purch_id = "<?php echo $purch_id; ?>";
                var table = "<?php echo $table; ?>";
                var orgName = "<?php echo $orgName; ?>";
                window.location.href = "https://lsuk.org/lsuk_system/purch_update.php?purch_id=" + purch_id + "&table=" + table + "&orgName=" + orgName + "&remove_purch=1";
            });
        });
    </script>