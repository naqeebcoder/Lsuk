<?php //if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<?php //include'source/db.php'; include'source/class.php';$table='feedback'; if(isset($_POST['submit']) && !empty($_POST['interp']) && !empty($_POST['source']) && !empty($_POST['date'])){$edit_id= $acttObj->get_id($table);}?>

<!DOCTYPE HTML>
<!--<html class="no-js">
<head>
<meta name="robots" content="noindex,nofollow">
<?php //include'source/header.php'; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    .search_text{
        width: 300px;
        position: relative;
        display: inline-block;
        font-size: 14px;
    }
    .search_text{
        height: 32px;
        padding: 5px 10px;
        border: 1px solid #CCCCCC;
        font-size: 14px;
    }
    .result{
        position: absolute;
        z-index: 1000;
        left: 41.5%;
        width: 11.6% !important;
        background: white;
        max-height: 240px;
        overflow-y: auto;
    }
    .search_text, .result{
        width: 100%;
        box-sizing: border-box;
    }
    /* Formatting result items */
    .result p{
        margin: 0;
        padding: 7px 10px;
        border: 1px solid #CCCCCC;
        border-top: none;
        cursor: pointer;
    }
    .result p:hover{
        background: #f2f2f2;
    }
    </style>
</head>
<body class="boxed">
<div id="wrap">
<?php //include'source/top_nav.php'; ?>
  <section id="page-title">
    	<div class="container clearfix">
            <h1>Your Feedback</h1>
          <nav id="breadcrumbs">
               <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>
                    <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']);?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php'));?></a> &rsaquo;</li>
                    <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php'));?></li>
            </ul>
          </nav>
        </div>
    </section>
    <section id="content" class="container clearfix">
    <section id="content" class="container clearfix">Your feedback is crucial in providing is the process of obtaining a customer's opinion about a business, product or service. It imparts us with insight so that we can then use to improve our business operations, products and/or overall customer experience. LSUK interpreting  and translation service values feedback from all of its users of the service. Please use the form below to send your thoughts to thethis the senior management.
    <div class="space20"></div>
     <form  class="content-form" method="post" action="#">
    <table class="gen-table">
                    <caption>
                        <input type="text" id="client_name" name="client_name" style="height:37px; width:200px; color:#000D00" required='' placeholder='Enter Your Name' />
                        <input class="search_input" name="interpreter_name" id="interpreter_name" type="text" style="height:37px; width:200px; color:#000D00" autocomplete="off" placeholder="Type Interperter Name" />
                        <div class="result"></div>
                        <input type="hidden" name="interpreter_id" id="interpreter_id" required=''/>
                <select name="source" id="source" style="height:50px; width:200px; color:#000D00" required=''>
                                <?php 			
                $sql_opt="SELECT distinct lang FROM lang ORDER BY lang ASC";
                $result_opt=mysqli_query($con,$sql_opt);
                $options="";
                while ($row_opt=mysqli_fetch_array($result_opt)) {
                    $code=$row_opt["lang"];
                    $name_opt=$row_opt["lang"];
                    $options.="<option value='$code'>".$name_opt."</option>";}
                ?>
                <option>Please Select Language</option>
                <?php echo $options; ?>
              </select>
<input type="date" name="date" id="date" style="height:35px; width:200px; color:#000D00" required=''>
                    </caption>
                    <thead>
                        <tr>
                        	<th style="text-align:left">Boundary</th>
                            <th>Poor</th>
                            <th>Fair</th>
                            <th>Satisfactory</th>
                            <th>Good</th>
                            <th>Excellent</th>
                        </tr>
                    </thead>
                    
                      <tbody>
                    
                        <tr>
                        	<td style="text-align:left">Job Knowledge</td>
                            <td><input type="radio" name="poor" id="poor" value="1" ></td>
                            <td><input type="radio" name="poor" id="poor" value="2" ></td>
                            <td><input type="radio" name="poor" id="poor" value="3" ></td>
                            <th><input type="radio" name="poor" id="poor" value="4" ></th>
                            <td><input type="radio" name="poor" id="poor" value="5" ></td>
                        </tr>                   
                        <tr>
                        	<td style="text-align:left">Work Quality</td>
                            <td><input type="radio" name="quality" id="quality" value="1" ></td>
                            <td><input type="radio" name="quality" id="quality" value="2" ></td>
                            <td><input type="radio" name="quality" id="quality" value="3" ></td>
                            <th><input type="radio" name="quality" id="quality" value="4" ></th>
                            <td><input type="radio" name="quality" id="quality" value="5" ></td>
                        </tr>
                   
                        <tr>
                        	<td style="text-align:left">Attendance/Punctuality</td>
                            <td><input type="radio" name="punctuality" id="punctuality" value="1" ></td>
                            <td><input type="radio" name="punctuality" id="punctuality" value="2" ></td>
                            <td><input type="radio" name="punctuality" id="punctuality" value="3" ></td>
                            <th><input type="radio" name="punctuality" id="punctuality" value="4" ></th>
                            <td><input type="radio" name="punctuality" id="punctuality" value="5" ></td>
                        </tr>
                   
                        <tr>
                        	<td style="text-align:left">Initiative</td>
                            <td><input type="radio" name="initiative" id="initiative" value="1" ></td>
                            <td><input type="radio" name="initiative" id="initiative" value="2" ></td>
                            <td><input type="radio" name="initiative" id="initiative" value="3" ></td>
                            <th><input type="radio" name="initiative" id="initiative" value="4" ></th>
                            <td><input type="radio" name="initiative" id="initiative" value="5" ></td>
                        </tr>
                   
                        <tr>
                        	<td style="text-align:left">Communication/Listening Skills</td>
                            <td><input type="radio" name="listening" id="listening" value="1" ></td>
                            <td><input type="radio" name="listening" id="listening" value="2" ></td>
                            <td><input type="radio" name="listening" id="listening" value="3" ></td>
                            <th><input type="radio" name="listening" id="listening" value="4" ></th>
                            <td><input type="radio" name="listening" id="listening" value="5" ></td>
                        </tr>
                   
                        <tr>
                        	<td style="text-align:left">Dependability</td>
                            <td><input type="radio" name="dependability" id="dependability" value="1" ></td>
                            <td><input type="radio" name="dependability" id="dependability" value="2" ></td>
                            <td><input type="radio" name="dependability" id="dependability" value="3" ></td>
                            <th><input type="radio" name="dependability" id="dependability" value="4" ></th>
                            <td><input type="radio" name="dependability" id="dependability" value="5" ></td>
                        </tr>
                        <tr>
                          <td style="text-align:right; vertical-align:middle;">Enter your Comments: </td>
                          <td colspan="5"><textarea style="width: auto;height: auto;" name="coment" class="required" id="coment"cols="150" rows="5"></textarea></td>
                        </tr>
				    </tbody>
                  
                </table>
                <input id="submit" class="button" type="submit" name="submit" value="Submit" />
    </form>
    </section>
        <hr>
    </section>
	<?php //include'source/footer.php'; ?>
<?php /*if(isset($_POST['submit'])){
    if(!empty($_POST['interpreter_id']) && !empty($_POST['source']) && !empty($_POST['date']) && !empty($_POST['client_name'])){
        $data=$_POST['client_name']; 
        $acttObj->editFun($table,$edit_id,'client_name',$data);
        $data=$_POST['interpreter_id']; 
        $acttObj->editFun($table,$edit_id,'interp',$data);
        $data=$_POST['date']; 
        $acttObj->editFun($table,$edit_id,'date',$data);
        $data=$_POST['source']; 
        $acttObj->editFun($table,$edit_id,'source',$data);
        $data=@$_POST['poor']; 
        $acttObj->editFun($table,$edit_id,'poor',$data);
        $data=@$_POST['quality']; 
        $acttObj->editFun($table,$edit_id,'quality',$data);
        $data=@$_POST['punctuality']; 
        $acttObj->editFun($table,$edit_id,'punctuality',$data);
        $data=@$_POST['initiative']; 
        $acttObj->editFun($table,$edit_id,'initiative',$data);
        $data=@$_POST['listening']; 
        $acttObj->editFun($table,$edit_id,'listening',$data);
        $data=@$_POST['dependability']; 
        $acttObj->editFun($table,$edit_id,'dependability',$data);
        $data=@$_POST['coment']; $acttObj->editFun($table,$edit_id,'coment',$data);
        echo "<script>alert('Your Feedback has been successfully received!');</script>";
    }else{
        echo "<script>alert('Please fill all the fields!');</script>";
    }
}*/ ?>
</div>
</body>
<script src="source/jquery-2.1.3.min.js"></script>
<script>
    $(document).ready(function(){
    $('.search_input').on("keyup input", function(){
        if($(this).val().length>=3){
            var interpreter_value = $(this).val();
            var resultDropdown = $(this).siblings(".result");
            if(interpreter_value.length){
                $.get("ajax_client_portal.php", {feedback_interpreter_name: interpreter_value}).done(function(data){
                    // Display the returned data in browser
                    resultDropdown.html(data);
                });
            }else{
                resultDropdown.empty();
            }
        }
    });
    // Set search input value on click of result item
    $(document).on("click", ".result p.click", function(){
        $(".search_input").val($(this).text());
        $("#interpreter_id").val($(this).attr('id'));
        $(".result").empty();
    });
});
</script>
</html>