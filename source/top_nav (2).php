
 <header id="header" class="container clearfix">
    	<!-- begin logo -->
        <h1 id="logo"><a href="index.php"><img src="images/logo.png" alt="LSUK" height="31" width="135"></a></h1>
        <!-- end logo -->
        
        <!-- begin navigation wrapper -->
        <div class="nav-wrap clearfix">

        
        <!-- begin search form -->
        <form id="search-form" action="#" method="get">
            <input id="s" type="text" name="s" placeholder="Search &hellip;" style="display: none;">
            <input id="search-submit" type="submit" name="search-submit" value="Search">
        </form>
        <!-- end search form -->

        <!-- begin navigation -->
       
<nav> <a id="resp-menu" class="responsive-menu" href="#"><i class="fa fa-reorder"></i> Menu</a>
  <ul class="menu">
    <li><a class="homer" href="index.php">Home</a></li>
    <li><a  href="#">LSUK</a>
      <ul class="sub-menu">
       					<li><a  href="about_us.php">About Us</a>
                       	<li><a href="mission.php">Our Mission</a></li>
                        <li><a href="executive.php">CEO's Message</a></li>
                        <li><a href="role.php">Interpreter Role</a></li>
                        <li><a href="quality.php">Quality Assurance</a></li>
                        <li><a href="memberships.php">Memberships</a></li>
                        <li><a href="feedback.php">Your Feedback</a></li>
                        <li><a href="pricing.php">Pricing</a></li>
                        <li><a href="source/pdf_dox/terms&conditions.pdf">Terms & Conditions</a></li>
                        <li><a href="blog.php">Blog</a></li>
                        
                         <!--   <ul>
                                <li><a href="#">Interpreter III-1</a></li>
                                <li><a href="#">Interpreter III-2</a>
                                    <ul>
                                        <li><a href="#">Interpreter III-2 (1)</a></li>
                                        <li><a href="#">Interpreter III-2 (2)</a></li>
                                        <li><a href="#">Interpreter III-2 (3)</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Drop-Down Example</a></li>
                            </ul>-->
                        
                    </ul>
    </li>
   
    <li><a  href="#">Interpreting</a>
       <ul id="sub-menu" class="ddsubmenustyle">
                        <li><a href="face_to_face.php">Face to Face</a></li>
                        <li><a href="Over_Skype.php">Telephone or Over The  Skype</a></li>
                        <li><a href="voice_over.php">Voice Over</a></li>
                        <li><a href="order_interpreter_prem.php">Book Face to Face Interpreter</a></li>
                        <li><a href="order_telephone_prem.php">Book Telephone Interpreter</a></li>
                    </ul>
    </li>
    <li><a  href="#">Translation</a>
                   <ul class="sub-menu">
                        <li><a href="doc_translation.php">Document Translation </a></li>
                        <li><a href="proofreading.php">Proofreading</a></li>
                        <li><a href="transcription_translation.php">Transcription and  Translation</a></li>
                        <li><a href="order_translation_prem.php">Book Online</a></li>
                    </ul>
    </li>
    <li><a  href="#">Services</a>
                    <ul class="sub-menu">
                    <?php if(empty($_SESSION['UserName'])){?>
                    	<li><a href="cust_login.php" rel="submenu">Customer Login</a>
                        <?php }else{ ?>
                    	<li><a href="logout.php" rel="submenu">Customer Logout</a>
                        <?php } ?>
                    	<li><a href="languages.php" rel="submenu">Languages</a>
                        <li><a href="public_sectors.php">Public Sectors</a></li>
                        <li><a href="corporate_sector.php">Corporate Sector</a></li>
                        <li><a href="legal_sector.php">Legal Sector</a></li>
                        <li><a href="health_sector.php">Health Sector</a></li>
                        <li><a href="education_sector.php">Education Sector</a></li>
                        <li><a href="advice_support.php">Advice and Support</a></li>
                        <li><a href="technical_manufacturing_sectors.php">Technical &amp;  Manufacturing Sectors</a></li>
                    </ul>
    </li>
     <li><a  href="#">Jobs</a>
                 <ul class="sub-menu">
                 <?php if(empty($_SESSION['web_UserName'])){ ?>
                    <li><a href="login.php">Interpreter Login</a></li>
           <?php } else{?>
           <li><a href="change_pass.php">Change Password</a></li>           
           
                    <li><a href="logout.php">Logout</a></li>
           <?php } ?>
		   
		   
		     <?php if(!empty($_SESSION['web_UserName'])){ ?>
                              
                    <li><a href="interp_profile.php">Profile</a></li>  
               <?php if(@$_SESSION['email']=="imran@lsuk.org"){ ?>  <li><a href="job_posting.php">Post a Staff Job</a></li><?php  }?>
                <!-- <li><a href="interp_registration.php">Interpreter Reg</a></li> -->                   
                 	<li><a href="jobs.php?val=interpreter">Interpreting – Face  to  Face </a></li>
                    <li><a href="jobs.php?val=telephone">Interpreting  - Telephone</a></li>
                    <li><a href="jobs_trans.php?val=translation">Translation</a></li>                 
                    <li><a href="time_sheet_interp.php">Make your Time Sheet (Face to Face)</a></li>                   
                    <li><a href="time_sheet_telep.php">Make your Time Sheet (Telephone)</a></li>                   
                    <li><a href="time_sheet_trans.php">Make your Time Sheet (Translation)</a></li><?php } ?>
                    <li><a href="jobs_staff.php?val=staff">Office Staff  Vacancies</a></li>   
                  </ul>
                </li>                
           
    <li><a href="contact_us.php">Contact</a></li>
    
  </ul>
</nav>

        <!-- end navigation -->
        </div>
        <!-- end navigation wrapper -->
    </header>