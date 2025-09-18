<script src="../js/jquery.min.for.unique.js" type="text/javascript"></script>
<script type="text/javascript" >function uniqueFun(compData, tab, colName,txtBoxId,editFlag,rowID){
$(document).ready(function() {  
        var postData = compData; // Data which you may pass.
        var formURL = 'source/class.php'; // Write callback script url here
        $.ajax({
        url : formURL,
        type: "POST",
        data: {table: tab, colName: colName, comp: postData, eFlag: editFlag, rowID: rowID},
        success:function(data, textStatus, jqXHR)
        {if(data){alert(data); $('#'+txtBoxId).val("");}
		else{ $("[name='submit']").show();}},
        error: function(jqXHR, textStatus, errorThrown){ alert("Something wrong with Jquery");}
        });});}
</script>

