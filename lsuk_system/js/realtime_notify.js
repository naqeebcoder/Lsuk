$(document).ready(function(){
    $("body").append('<div class="div_notification" style="position: fixed;z-index: 99999;bottom: 20px;right: 10px;"></div>');
    var notificationSound = new Audio('img/lsuk_notify.mp3');

    function startAjaxCall() {
        setInterval(function() {
            // if ($('.div_notification').find('.notification_modal').length < 5) {
                $.ajax({
                url: 'ajax_add_interp_data.php',
                type: 'POST',
                dataType: 'json',
                data: {get_notifications : 1},
                success: function(data) {
                    if (data) {
                        if (data['status'] == 1) {
                            notificationSound.play();
                            $('.div_notification').append('<div class="modal notification_modal fade notification_modal_'+data['div_id']+'" tabindex="-1" data-backdrop="static" aria-labelledby="notification_modal_label" style="position: relative;padding: 0px !important;">'+
                                '<div class="modal-dialog modal-sm" style="margin: auto;width: 380px;">'+
                                '<div class="modal-content">'+
                                    '<div class="modal-header bg-success" style="padding: 8px;">'+
                                    '<b class="modal-title">New Notification From LSUK</b>'+
                                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>'+
                                    '</div>'+
                                    '<div class="modal-body" style="padding: 8px;">'+data['body']+'<br><i class="text-muted text-danger">'+data['assigned_date']+'</i></div>'+
                                '</div>'+
                                '</div>'+
                            '</div>');
                            $('.notification_modal_'+data['div_id']).modal('show');
                        }
                    }
                },
                error: function() {
                    console.log("Error fetching data!");
                }
                });
            // }
        }, 1200000);
    }

    startAjaxCall();

});