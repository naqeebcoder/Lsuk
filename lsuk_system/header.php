	<meta charset="utf-8" />
	<title>LSUK.ORG</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" type="text/css" href="css1/grey.css" />
	<link rel="stylesheet" type="text/css" href="css1/pagination.css" />
	<link rel="icon" type="image/png" href="img/logo.png">
	<script type="text/javascript">
	    function MM_openBrWindow(theURL, winName, features) {
	        window.open(theURL, winName, features);
	    }

	    function popupwindow(url, title, w, h) {
	        var left = (screen.width / 2) - (w / 2);
	        var top = (screen.height / 2) - (h / 2);
	        return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
	    }
	</script>
	<link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />
	<script src="js/jquery-1.5.2.min.js" type="text/javascript"></script>
	<script src="js/hideshow.js" type="text/javascript"></script>
	<script src="js/jquery.tablesorter.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/jquery.equalHeight.js"></script>
	<script type="text/javascript">
	    $(function() {
	        try {
	            $('.column').equalHeight();
	        } catch (e) {}

	        /*var count_time = 0;var interval_active;
	        //var uat = getCookie("uat");
	        function timerHandler() {
	        count_time++;
	        console.log(count_time);
	        }
	        function startTimer() {
	        interval_active = setInterval(timerHandler, 1000);
	        }
	        function stopTimer() {
	        clearInterval(interval_active);
	        }
	        $(window).on("mouseover mouseleave", function(e) {
	            var prevType = $(this).data("prevType");
	            if (prevType != e.type) {//reduce double fire issues
	                switch (e.type) {
	                    case "mouseover":
	                        startTimer();
	                        // if (uat != "") {
	                        //     alert("Welcome again " + uat);
	                        // } else {
	                        //     uat = prompt("Please enter your name:", "");
	                        //     if (uat != "" && uat != null) {
	                        //     setCookie("username", uat, 365);
	                        //     }
	                        // }
	                        // console.log("Active");
	                        break;
	                    case "mouseleave":
	                        stopTimer();
	                        // console.log("Not Active");
	                        break;
	                }
	            }
	            $(this).data("prevType", e.type);
	        });*/

	    });
	</script>
	<style>
	    ::-webkit-scrollbar {
	        width: 14px;
	    }

	    ::-webkit-scrollbar-thumb {
	        background: #337ab7;
	        border: 1px solid #fff;
	    }

	    ::-webkit-scrollbar-track {
	        box-shadow: inset 0 0 3px grey;
	        background: #ffffff61;
	    }
	</style>
	<?php error_reporting(0); ?>
	</head>