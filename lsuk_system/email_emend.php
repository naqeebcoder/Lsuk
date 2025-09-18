<?php if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
include 'actions.php';
$allowed_type_idz = "118";
//Check if user has current action allowed
if ($_SESSION['is_root'] == 0) {
    $get_page_access = $obj->read_specific("id", "action_permissions", "user_id=" . $_SESSION['userId'] . " AND action_id IN (" . $allowed_type_idz . ")")['id'];
    if (empty($get_page_access)) {
        die("<center><h2 class='text-center text-danger'>You do not have access to <u>Amend Order</u> action for jobs!<br>Kindly contact admin for further process.</h2></center>");
    }
}
$table = trim($_GET['table']);
$job_id = $_GET['email_id'];
//getting amending options from table
$amend_options = $obj->read_all("*", "amend_options", "status=1");
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Job Amendment Form</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/bootstrap.css">
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
</head>

<body>
    <div align="center">
        <?php if ($_SESSION['returned_message']) {
            echo $_SESSION['returned_message'];
            unset($_SESSION['returned_message']);
            die();
            exit();
        } ?>
        <h3>Record ID: <span class="label label-primary"><?= $job_id; ?></span></h3>
        <form action="process/email_emend.php" method="post">
            <input type="hidden" name="job_id" value="<?= $job_id ?>" />
            <input type="hidden" name="table" value="<?= $table ?>" />
            <input type="hidden" name="redirect_url" value='<?= 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" ?>' />
            <div class="container">
                <center>
                    
                    
                        <div class="form-group col-sm-6 col-sm-offset-3">
                            <label>Requested By</label>
                            <select id="amend_filter" class="form-control" onchange="filter_amend_options()">
                                <option value="">-- Select --</option>
                                <option value="cl">Client</option>
                                <option value="ls">LSUK</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-6 col-sm-offset-3" id="amend_div" style="display:none;">
                            <label>Reason of Amendment</label>
                            <select name="amend_id" id="amend_id" class="form-control" required onchange="toggle_interpreter_deduction(this)">
                                <option value="" selected disabled>Select a reason</option>
                                <?php while ($row_amend_opt = $amend_options->fetch_assoc()) {
                                    $effect = $row_amend_opt['effect'] == '1' ? "Client Chargeable" : "Client Non Chargeable";
                                    $effect_interp = $row_amend_opt['effect_interp'] == '1' ? "Affect Interpreter" : "No effect on interpreter";
                                    $effect_cls = ($row_amend_opt['effect'] == '1' || $row_amend_opt['effect_interp'] == '1') ? "class='text-danger'" : "class='text-success'";
                                    $data_deduction = ($row_amend_opt['effect'] == '0' && $row_amend_opt['effect_interp'] == '1') ? '1' : $row_amend_opt['effect'];
                                    $amend_for = $row_amend_opt['amend_for']; // 'cs' or 'ls'
                                    echo "<option data-deduction='$data_deduction' data-for='$amend_for' title='$effect' effect-interp='$effect_interp' $effect_cls value='" . $row_amend_opt['id'] . "'>" . $row_amend_opt['value'] . "</option>";
                                } ?>
                            </select>
                        </div>


                   
                    <div class="form-group col-sm-12">
                        <br>
                        <label>Remarks of Amendment</label>
                        <textarea name="amend_details" rows="3" placeholder="Enter Details (if any)" id="amend_details" class="form-control" required="required"></textarea>
                    </div>
                    <?php if ($table == 'translation') { ?>
                        <!-- <div class="form-group col-sm-6">
                            <label class="checkbox-inline">
                                <input type="checkbox" id="affect_int" name="affect_int" value="1" data-toggle="toggle" data-on="Yes" data-off="No"> <b>Mark affect on interpreter's profile for this job?</b>
                            </label>
                        </div> -->
                    <?php } ?>
                    <div class="div_deduction hidden">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td colspan="4">
                                        <form action="process/money_requests.php" autocomplete="off" method="post" enctype="multipart/form-data">
                                            <!-- <div class="form-group">
                                                <label class="btn btn-success">
                                                    <input type="radio" name="status" value="2" onchange="toggle_update_request(this)" /> Add Penalty Deduction
                                                </label>
                                                <label class="btn btn-danger">
                                                    <input type="radio" name="status" value="3" onchange="toggle_update_request(this)" /> Skip Penalty Deduction
                                                </label>
                                            </div> -->
                                            <div class="form-group">
                                                <label class="btn btn-success">
                                                    <input type="radio" name="status" value="2" onchange="toggle_update_request(this)" /> Add Penalty Deduction
                                                </label>
                                                <label class="btn btn-danger">
                                                    <input type="radio" name="status" value="3" onchange="toggle_update_request(this)" /> Skip Penalty Deduction
                                                </label>
                                            </div>
                                            <div class="div_accept hidden">
                                                <div class="form-group col-xs-4">
                                                    <label>Select Deduction Type</label>
                                                    <select class="form-control" name="type_id">
                                                        <?php $get_types = $obj->read_all("*", "loan_dropdowns", "is_payable=1 AND deleted_flag=0 ORDER BY title ASC");
                                                        while ($row_type = $get_types->fetch_assoc()) {
                                                            $selected_type = $row_type['id'] == 3 ? "selected style='color:red'" : "";
                                                            echo "<option $selected_type value='" . $row_type['id'] . "'>" . $row_type['title'] . "</option>";
                                                        } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-xs-4">
                                                    <label>Deduction Amount</label>
                                                    <input style="width: 80%;" class="form-control" type="number" name="given_amount" id="given_amount" placeholder="Amount ..." oninput="calculate_duration()" />
                                                </div>
                                                <div class="form-group col-xs-4">
                                                    <label>Select Payable Month</label>
                                                    <input min="<?= date('Y-m') ?>" value="<?= date('Y-m') ?>" class="form-control" type="month" name="payable_date" id="payable_date" />
                                                </div>
                                                <div class="form-group col-xs-4">
                                                    <label style="font-size: 15px;">Deduction Percentage</label>
                                                    <select class="form-control" id="percentage" name="percentage" onchange="calculate_duration()">
                                                        <option value="">-Select percentage-</option>
                                                        <option value="5">5 Percent</option>
                                                        <option value="10">10 Percent</option>
                                                        <option value="15">15 Percent</option>
                                                        <option value="20">20 Percent</option>
                                                        <option value="25">25 Percent</option>
                                                        <option value="30">30 Percent</option>
                                                        <option value="35">35 Percent</option>
                                                        <option value="40">40 Percent</option>
                                                        <option value="45">45 Percent</option>
                                                        <option value="50">50 Percent</option>
                                                        <option value="100"  selected style="color:red">FULL AMOUNT AT ONCE</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-xs-4">
                                                    <label>Total Payable Installments</label>
                                                    <input type="hidden" name="duration" id="duration" />
                                                    <br>
                                                    <h3 style="display: inline;"><span class="label label-info text_duration">No Calculations</span></h3>
                                                </div>
                                                <div class="form-group col-xs-4">
                                                    <label>Per Installment Amount</label>
                                                    <br>
                                                    <h3 style="display: inline;"><span class="label label-info text_installment_amount">No Calculations</span></h3>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-6 text-left" id="div_notify">
                            <label class="checkbox">
                                <b>I want to notify the interpreter?</b> <input type="checkbox" id="email_int" name="email_int" value="1" data-toggle="toggle" data-on="Yes" data-off="No" checked>
                            </label>
                            <small class='text-danger'><i>If you do not want to notify the Interpreter then select No</i></small>
                        </div>
                        <div class="form-group col-xs-6 text-left" id="div_notify">
                            <label class="checkbox">
                                <b>I want to notify the client?</b> <input type="checkbox" id="email_cl" name="email_cl" value="1" data-toggle="toggle" data-on="Yes" data-off="No" checked>
                            </label>
                            <small class='text-danger'><i>If you do not want to notify the Client then select No</i></small>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <h4>Are you sure you want to <span class="text-danger"><b>AMEND</b></span> this booking ?</h4>
                    </div>
                    <div class="form-group col-sm-6 col-sm-offset-3">
                        <input type="submit" name="yes" class="btn btn-primary" name="yes" value="Yes" />&nbsp;&nbsp;<input type="button" onclick="window.close();" class="btn btn-warning" name="no" value="No" />
                    </div>
                </center>
            </div>
        </form>
    </div>
   
</body>
<script>
   function toggle_interpreter_deduction(element) {
    $('input:radio[name="status"]').removeAttr("checked");
    $('.div_accept').addClass("hidden");
    var effect = $(element).find("option:selected").attr("title");
    var effectInterp = $(element).find("option:selected").attr("effect-interp");
    var amend = 'yes';
    var jobID = <?php echo $job_id; ?>;
    var tablee = "<?php echo $table; ?>";

    // Show loading spinner while waiting for the AJAX response
    // $(".loading-spinner").removeClass("hidden");

    if ($(element).find("option:selected").attr("data-deduction") == 1) {
        $.ajax({
            url: 'ajax_add_interp_data.php',
            method: 'post',
            dataType: 'json',
            data: {
                amend: amend,
                effect: effect,
                effectInterp: effectInterp,
                jobID: jobID,
                table: tablee
            },
            success: function(response) {
                $(".div_deduction").removeClass("hidden");
                if(response['amount']) {
                    var amount_field = document.getElementById("given_amount");
                    amount_field.value = response['amount'];
                    $("#duration").val(1);
                    $(".text_installment_amount").text(response['amount']);
                    $(".text_duration").text(1);
                }
                if (effectInterp == " Affect Interpreter") {
                    $("label.btn-success").html('<input type="radio" name="status" value="2" onchange="toggle_update_request(this)" /> Add Penalty Deduction to Interpreter');
                    $("label.btn-danger").html('<input type="radio" name="status" value="3" onchange="toggle_update_request(this)" /> Skip Penalty Deduction to Interpreter');

                    $("input[name='status'][value='2']").prop("checked", true);
                    $(".div_accept").removeClass("hidden");
                    
                } else if (effect == "Client Chargeable") {
                    $("label.btn-success").html('<input type="radio" name="status" value="4" onchange="toggle_update_request(this)" /> Add Penalty Deduction to Client');
                    $("label.btn-danger").html('<input type="radio" name="status" value="3" onchange="toggle_update_request(this)" /> Skip Penalty Deduction to Client');

                    $("input[name='status'][value='4']").prop("checked", true);
                    $(".div_accept").removeClass("hidden");
                } else {
                    
                    $("label.btn-success").html('<input type="radio" name="status" value="0" onchange="toggle_update_request(this)" /> Add Penalty Deduction');
                    $("label.btn-danger").html('<input type="radio" name="status" value="3" onchange="toggle_update_request(this)" /> Skip Penalty Deduction');

                    $("input[name='status'][value='2']").prop("checked", false);
                    $(".div_accept").addClass("hidden");
                }
            },
            error: function(xhr) {
                
            }
        });
    } else {
        $(".div_deduction").addClass("hidden");
    }
}
   
    function toggle_update_request(element) {
        var checked_value = $(element).val();
        if (checked_value == 2) {
            $('.div_accept').removeClass("hidden");
            $("#percentage").attr("required", "required");
        } else {
            $('.div_accept').addClass("hidden");
        }
    }

    function calculate_duration() {
        var percentage = $("#percentage").val() ? $("#percentage").val() : 100;
        var loan_amount = $("#given_amount").val();
        var total_installments = !isNaN(Math.round(loan_amount / ((loan_amount * (percentage / 100))))) ? Math.round(loan_amount / ((loan_amount * (percentage / 100)))) : 0;
        var installment_amount = !isNaN(Math.round(loan_amount / total_installments)) ? Math.round(loan_amount / total_installments) : 0;
        $(".text_duration").text(total_installments);
        $("#duration").val(total_installments);
        $(".text_installment_amount").text(installment_amount);
    }
</script>


</html>
<script>
    window.onunload = refreshParent;

    function refreshParent() {
        window.opener.location.reload();
    }
</script>
<script>
function filter_amend_options() {
    const filter = document.getElementById('amend_filter').value;
    const amendDiv = document.getElementById('amend_div');
    const amendSelect = document.getElementById('amend_id');
    const options = amendSelect.querySelectorAll('option');

    if (!filter) {
        amendDiv.style.display = 'none';
        amendSelect.value = '';
        return;
    }

    amendDiv.style.display = 'block';

    options.forEach(opt => {
        const amendFor = opt.getAttribute('data-for');
        if (!amendFor || amendFor === filter) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });

    amendSelect.value = '';
}
</script>

