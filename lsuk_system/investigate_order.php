<?php
include "userhaspage.php";
SysPermiss::UserHasPage(__FILE__);
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
?>
<html lang="en">

<head>
    <title>Investigate an Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <h3 class="text-primary text-center"><b>Investigate an Order History</b></h3>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="investigate_order_type">Select Order Type</label>
                <select class="form-control" id="investigate_order_type">
                    <option value="1">Face to Face</option>
                    <option value="2">Telephone</option>
                    <option value="3">Translation</option>
                </select>
            </div>
            <div class="form-group col-sm-4">
                <label for="investigate_order_id">Enter Order ID</label>
                <input type="text" placeholder="Enter Order ID ..." class="form-control" name="investigate_order_id" id="investigate_order_id">
            </div>
            <div class="form-group col-sm-2">
                <br><button style="margin-top: 4px;" type="button" class="btn btn-primary" onclick="investigate_order(this)">Search History</button>
            </div>
        </div>
        <div class="row investigate_order_attach"></div>
    </div>
</body>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    function investigate_order(element) {
        var investigate_order_id = $("#investigate_order_id").val();
        var investigate_order_type = $("#investigate_order_type").val();
        var table_name_array = {
            "interpreter": "Face To Face Order",
            "telephone": "Telephone Order",
            "translation": "Translation Order"
        };
        if (investigate_order_id && investigate_order_type) {
            $('.investigate_order_attach').html("<center><i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i> <i class='fa fa-circle fa-2x'></i><br><h3>Loading ...<br><br>Please Wait !!!</h3></center>");
            $.ajax({
                url: 'process/investigate_order.php',
                method: 'post',
                dataType: 'json',
                data: {
                    investigate_order_id: investigate_order_id,
                    investigate_order_type: investigate_order_type,
                    table_name: table_name_array[investigate_order_type],
                    investigate_order: 1
                },
                success: function(data) {
                    if (data['status'] == 1) {
                        $('.investigate_order_attach').html(data['body']);
                    } else {
                        $('.investigate_order_attach').html("<div class='col-md-10 col-md-offset-1 alert alert-danger'>Cannot load requested response. Please try again!</div>");
                    }
                },
                error: function(data) {
                    $('.investigate_order_attach').html("<div class='col-md-10 col-md-offset-1 alert alert-danger'>Error: Please select valid Order ID and Order Type for order history details or refresh the page! Thank you</div>");
                }
            });
        } else {
            $('.investigate_order_attach').html("<div class='col-md-10 col-md-offset-1 alert alert-danger'>Error: Please select valid Order ID for history details or refresh the page! Thank you</div>");
            $("#investigate_order_id").focus();
        }
    }
</script>

</html>