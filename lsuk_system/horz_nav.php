	<section id="secondary_bar">
		
<div class="user">
			<p><?php if(!empty($_SESSION['UserName'])){echo ucwords(@$_SESSION['UserName']);echo ' ('.$_SESSION['prv'].')';}else{echo '<script type="text/javascript">' . "\n"; 	
			echo 'window.location="index.php";'; echo '</script>';} ?> <!--(<a href="#">3 Messages</a>)--></p>
			<!-- <a class="logout_user" href="#" title="Logout">Logout</a> -->
		</div>
		<div class="breadcrumbs_container">
			<article class="breadcrumbs"><a href="home.php">Home</a><div class="breadcrumb_divider"></div><a href="chat.php">Reminder</a> <div class="breadcrumb_divider"></div><a href="html">New Portal</a><div class="breadcrumb_divider"></div><a href="test_rip.php">New Report Design</a><div class="breadcrumb_divider"></div><a href="test_ino.php">New Invoice Design</a></article>
		</div>
	</section>