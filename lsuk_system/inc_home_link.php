<script>
    var url = window.location.pathname;
    var filename = url.substring(url.lastIndexOf('/') + 1);
</script>
<div class="btn-group" style="margin-bottom: 5px;">
    <?php if ($route_home) { ?>
        <a href="home.php" type="button" class="btn btn-primary fw"><i class="glyphicon glyphicon-user"></i> Interpreter</a>
    <?php }
    if ($route_tp) { ?>
        <a href="tele_index.php" type="button" class="btn btn-default fw"><i class="glyphicon glyphicon-phone"></i> Telephone</a>
    <?php }
    if ($route_tr) { ?>
        <a href="trans_index.php" type="button" class="btn btn-default fw"><i class="glyphicon glyphicon-globe"></i> Translation</a>
    <?php } ?>
</div>
<script type="text/javascript">
    $('.fw').removeClass('btn-primary active');
    $('.fw').addClass('btn-default');
    $('a[href*="' + filename + '"]').addClass('btn-primary active');
    $('a[href*="' + filename + '"]').removeClass('fw');
</script>