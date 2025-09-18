<?php if(session_id() == '' || !isset($_SESSION)){session_start();} ?> 
<!DOCTYPE HTML>
<!--[if IE 8]> <html class="ie8 no-js"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js"> <!--<![endif]-->
<head>
<?php include'source/header.php'; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="boxed">
<!-- begin container -->
<div id="wrap">
	<!-- begin header -->
<?php include'source/top_nav.php'; ?>
    <!-- end header -->
	
    <!-- begin page title -->
<section id="page-title">
    	<div class="container clearfix">
            <h1>Memberships</h1>
          <nav id="breadcrumbs">
               <ul>
                    <li><a href="index.php">Home</a> &rsaquo;</li>
                    <li><a href="<?php echo basename($_SERVER['HTTP_REFERER']);?>"><?php echo ucwords(basename($_SERVER['HTTP_REFERER'], '.php'));?></a> &rsaquo;</li>
                    <li><?php echo ucwords(basename($_SERVER['REQUEST_URI'], '.php'));?></li>
            </ul>
          </nav>
        </div>
    </section>
    <!-- begin page title -->
    
    <!-- begin content -->
    <section id="content" class="container clearfix">
    	<!-- begin our company -->
 
    <!-- begin content -->
    <section id="content" class="container clearfix">
       


<table width="100%" border="1">
  <tr>
    <td><p>Language Services UK  limited in a full member of the Association of Translation Companies <a href="http://www.atc.org.uk/" title="read more..">ATC</a> , a professional organisation for language  services providers.
The ATC is an industry association serving  United Kingdom translation/interpreting companies and agencies.  It was founded in 1976, making it &quot;the  world's longest established professional body looking after the interests of  translation companies&nbsp;and sets out professional code of conduct for the  member companies to follow.</p></td>
    <td><a href="http://www.atc.org.uk/" title="read more.."><img src="images/client-logos/atc.png" height="62" width="112"></a></td>
  </tr>
    <tr>
    <td><p>Language Services UK  limited in a Corporate member of the Institue of Translation and interpreting <a href="http://www.iti.org.uk/" title="read more..">ITI</a>. The Institute of Translation & Interpreting (ITI), founded in 1986, is the UK's only dedicated association for practising translation and interpreting. ITI focus is on to promote the highest standards in the translation and interpreting professions.
</p></td>
    <td><a href="http://www.iti.org.uk/" title="read more.."><img src="images/client-logos/iti.png" height="62" width="62"></a></td>
  </tr>
</table>

<div>
  <div> </div>
</div>
    </section>
    <!-- end content -->  
    
        <hr>
        
     	<!-- begin clients -->
       <?php include'source/our_client.php'; ?>
        <!-- end clients -->   
    </section>
    <!-- end content -->  
    
    <!-- begin footer -->
	<?php include'source/footer.php'; ?>
	<!-- end footer -->  
</div>
<!-- end container -->
</body>
</html>