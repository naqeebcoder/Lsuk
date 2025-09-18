<?php
		/* Check if the user has not visited yet this website  
		(or not accepted the cookies usage) */
		if(!isset($_COOKIE['infoCookies'])) 
		{	
            echo '<div id="cookies"><div class="container-fluid" >
                <div class="row">
                    <div class="col-lg-10 col-md-10 col-sm-12">
                    <div class="pt-2">
                    We and our partners use cookies in order to enable essential services and functionality on our site, to collect data on how visitors interact with our site and for personalization of content and ads. By clicking “Accept all cookies”, you agree to the use of cookies by all of the websites listed in our Cookie Policy. By clicking on the Reject button or by closing this banner, you accept only the strictly necessary cookies and no analytics or targeting ones. To learn more about our use of cookies, please visit our Cookie Policy. You can manage your cookies preferences at any time in the Cookie Settings tool on our site.</div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-12">
                    <a onClick="hideCookie();" class="cookieLinks" href=#>Reject</a>
                    <a onClick="hideCookie();" class="cookieLinks btn btn-primary" href=#>Accept</a>
                    </div>
                </div>
            </div></div>';
		}
	
	?>
<style> @media screen and (max-width:425px){
    #logo_img{position: absolute;
    margin: 4px 0px 0px -35px;
    }
    }
    @media screen and (min-width:426px) and (max-width:1024px){
    #logo_img{
    margin: 0px 55px;
    position: absolute;
    }
    }
    @media screen and (min-width:1025px){
    #logo_img{
    margin: 0px 106px;
    position: absolute;
    }
    }
    #header{
        margin-bottom: 0px;
    }
    .menu li a {padding: 20px 11px;}
    body.boxed {
    background-color: transparent;
    background-image: none;
    background-repeat: unset;
    background-position: unset;
}
.boxed #wrap {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    box-shadow: none;
    overflow: hidden;
    background-color: transparent;
}
/* //.container {
//    width: 1000px;
//} */
.text-center{text-align: center;}
::-webkit-scrollbar {
    width: 14px;
}::-webkit-scrollbar-thumb {
    background: #337ab7;
    border: 1px solid #fff;
}::-webkit-scrollbar-track {
    box-shadow: inset 0 0 3px grey;
    background: #ffffff61;
}
#cookies{
		background-color:#393636;
		position:fixed;
        padding: 2rem 3rem;
        bottom: 0;
        left: 0;
		width:100vw;
		height:7rem;
		opacity:0.9;
		z-index:999;
		color:#FFFFFF;
		padding-top:5px;
		padding-bottom:10px;
		/* text-align:center; */
        font-size: 1rem;
		}
	
	.cookieLinks{
		color:#FFFFFF;
	}
	.cookieLinks:hover{
		color:#000000;
	}
    </style>

 <header id="header" class="container-fluid clearfix text-center">
    	<!-- begin logo
        <span id="logo">
            <a href="index.php"><img id="logo_img" src="images/logo_lsuk.png" alt="LSUK" height="60" width="60"></a>
            </span>
         end logo -->
        <div class="nav-wrap clearfix">
<nav class="navbar" style="-webkit-box-shadow: none;">
<a id="resp-menu" class="btn hidden-md hidden-lg" href="javascript:void(0)"><b><i class="fa fa-reorder"></i> Menu</b></a>
  <ul class="menu">
    <li><a class="homer" href="index.php">Home</a></li>
    <li><a  href="javascript:void(0)">About LSUK</a>
      <ul class="sub-menu">
       					<li><a  href="about_us.php">About Us</a>
                        <li><a href="javascript:void(0)">Industries</a>
                        <ul style=" margin-left:60px;" >
                        <li><a href="public_sectors.php">Public Sectors</a></li>
                        <li><a href="corporate_sector.php">Corporate Sector</a></li>
                        <li><a href="legal_sector.php">Legal Sector</a></li>
                        <li><a href="health_sector.php">Health Sector</a></li>
                        <li><a href="education_sector.php">Education Sector</a></li>                        
                        <li><a href="technical_manufacturing_sectors.php">Technical &amp;  Manufacturing Sectors</a></li>
                        </ul></li>
                        <li><a href="quality.php">Quality Assurance</a></li>
                        <li><a href="executive.php">CEO's Message</a></li>
                       	<li><a href="mission.php">Our Mission</a></li>

                        <li><a href="CarbonReductionPlan-LSUK.pdf" target="_blank">Carbon Reduction Plan</a></li>
                        <li><a href="ModernSlaveryAndHumanTraffickingStatement-LSUK.pdf" target="_blank">Modern Slavery and Human Trafficking Statement</a></li>

                        <!--<li><a href="javascript:void(0)">Our Costs</a></li>-->
                        <li><a href="source/pdf_dox/terms&amp;conditions.pdf">Terms &amp; Conditions</a></li>
                        <li><a href="source/pdf"></a></li>
                    </ul>
    </li>
   
   
    <li><a  href="javascript:void(0)">Services</a>
                    <ul class="sub-menu">
                     <li><a  href="javascript:void(0)">Interpreting</a>
       <ul style=" margin-left:60px;" >
                        <li><a href="<?php if(isset($_SESSION['cust_UserName'])){echo 'customer_area_slct_comp.php?interp=order_interpreter_prem.php';}else{echo 'face_to_face.php';} ?>">Book Face to Face Interpreter</a></li>
                        <li><a href="<?php if(isset($_SESSION['cust_UserName'])){echo 'customer_area_slct_comp.php?interp=order_telephone_prem.php';}else{echo 'Over_Skype.php';} ?>">Book Telephone Interpreter</a></li>
                        <li><a href="<?php if(isset($_SESSION['cust_UserName'])){echo 'customer_area_slct_comp.php?interp=order_telephone_prem.php';}else{echo 'voice_over.php';} ?>">Book Voice Over Interpreter</a></li>
                    </ul>
    </li>
    <li><a  href="javascript:void(0)">Translation</a>
                   <ul style=" margin-left:60px;" >
                        <li><a href="doc_translation.php">Document Translation </a></li>
                        <li><a href="proofreading.php">Proofreading</a></li>
                        <li><a href="transcription_translation.php">Transcription and  Translation</a></li>
                        
                        <li><a href="<?php if(isset($_SESSION['cust_UserName'])){echo 'customer_area_slct_comp.php?interp=order_translation_prem.php';}else{echo 'order_translation.php';} ?>">Book Online</a></li>
                    </ul></li>
                    
                    	<li><a href="languages.php" rel="submenu">Languages</a></li>
  </ul></li>
  
    </li>
    <?php if(!empty($_SESSION['web_UserName']) || !empty($_SESSION['cust_UserName'])){ ?>
    <li><a style="font-size: 22px;text-shadow: -1px 0px 7px #337ab7;" href="<?php echo isset($_SESSION['cust_UserName'])?'customer_area.php':'interp_profile.php';?>"><?php echo isset($_SESSION['cust_UserName'])?'My Dashboard':'My Profile';?></a></li>
    <?php }else{ ?>
    <li><a  href="javascript:void(0)"><i class="fa fa-lock"></i> My Portal</a>
                 <ul class="sub-menu">
                        <?php if(empty($_SESSION['cust_UserName'])){?>
                    	<li><a href="cust_login.php" rel="submenu">Client Login</a>
                        <?php }else{ ?>
                    	<li><a href="logout.php" rel="submenu">Customer Logout</a>                
                    <li><a href="customer_area.php">Secured Online Booking Order Forms</a></li>     
                        <?php } ?>
                    <li><a href="login.php">Interpreter Login</a></li>            
                        <li><a href="javascript:void(0)">Current Vacancies</a>
                        <ul style=" margin-left:60px;" >
                        <li><a href="jobs_staff.php?val=staff">Office Staff  Vacancies</a></li>   
                        <li><a href="javascript:void(0)">interpreter Vacancies</a></li>
                        </ul>
                        </li>                        
                        
                         </li>
                  </ul>
    </li>          
    <?php } ?>   
    <li><a  href="javascript:void(0)"><i class="fa fa-lock"></i> Request For Service</a>
                 <ul class="sub-menu">
                        <li><a href="order_interpreter.php">Face To Face Interpreter</a></li>
                        <li><a href="order_telephone.php">Telephone Interpreter</a></li>  
                        <li><a href="order_translation.php">Document Translation</a></li>
                  </ul>
    </li>    
    <!-- <li><a href="blog.php">Blog</a></li>
    <li><a href="gallery.php">Gallery</a></li> -->
    <li><a href="contact_us.php">Contact</a></li>
    <li><a href="javascript:void(0)">Join LSUK</a>
        <ul>
        <li><a href="interp_reg.php">Register as Interpreter</a></li>   
        <li><a href="javascript:void(0)">Register as Company</a></li>
        </ul>
    </li>
    <li><a href="https://ipqualifications.lsuk.org/register"><b style="color: #ff3547 !important;text-shadow: #bdca27 -3px -1px 4px;font-size: 17px;">Trainings</b></a></li>
    
  </ul>
</nav>

        <!-- end navigation -->
        </div>
        <!-- end navigation wrapper -->
    </header>
    <script type=text/javascript>
		function hideCookie(){
				/* Create the expiry date (today + 1 year) */
				var CookieDate = new Date;
				CookieDate.setFullYear(CookieDate.getFullYear( ) +1);
				
				/* Set the cookie (acceptance of cookies usage) */
				document.cookie = 'infoCookies=true; expires=' + CookieDate.toGMTString( ) + ';';

				/* When "OK" clicked, hides this popup */
				document.getElementById("cookies").style.display = "none";		
		}	
	</script>