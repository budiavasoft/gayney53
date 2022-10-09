<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//140604 for Cache
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<jdoc:include type="head" />

<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/gaineyrancha/css/template.css" type="text/css" />
<!--[if lte IE 6]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ieonly.css" rel="stylesheet" type="text/css" />
<![endif]-->
<?php if($this->direction == 'rtl') : ?>
	<link href="<?php echo $this->baseurl ?>/templates/gaineyrancha/css/template_rtl.css" rel="stylesheet" type="text/css" />
<?php endif; ?>

</head>
<body>
	<div class="wrapper">
    <div class="bg-shadow">
	<div class="header">
    <div class="header-top">
    	<div class="logo_container" align="center">
        	<a href="#">
            	<img src="<?php echo $this->baseurl ?>/templates/gaineyrancha/images/logo.jpg" alt="Gainey Rancha" width="305" height="85" />
            </a>
            <div style="text-align:center;">
            	7720 Gainey Ranch Road &bull; Scottsdale, Arizona &bull; 85258-1601 <br />
            	(480)951-0321
           	</div>
        </div>
        <div class="serach_container">
        	<jdoc:include type="modules" name="top" />
        </div>
    </div>
    </div>
    <div class="nav">
    	<jdoc:include type="modules" name="user1" />
    	<!-- <ul class="mainmenu">
        	<li><a href="#">Main</a></li>
            <li><a href="#">About GRCA</a></li>
            <li><a href="#">Bulletins</a></li>
            <li><a href="#">Events</a></li>
            <li><a href="#">Classifieds</a></li>
            <li><a href="#">Documents/Form</a></li>
            <li><a href="#">Contact Us</a></li>
        </ul> -->
    </div>
    <?php
		$user = & JFactory::getUser();
		if ($user->username != "")
		{
	?>
    <div class="log">
    	<jdoc:include type="modules" name="login" />
    </div>
    <div class="content" style="padding:0;">
    	<jdoc:include type="message" />
    	<jdoc:include type="component" />
   	<?php
		}
		else{
	?>
    <div class="content">
    	<jdoc:include type="message" />
    	<jdoc:include type="component" />
        <jdoc:include type="modules" name="login" />
     <?php
	 	}
	 ?>
       <!--<div class="form-login">
        <div style="padding:50px 0; float:left;">
        	<h1>Login Area</h1>
           	<img src="images/lock.png" style="float:left; width:78px; margin:10px 15px 0 0;" />
            <div class="form">
            	<form>
                	<label>Username</label>
                    <input type="text" name="username" />
                    
                    <label>Password</label>
                    <input type="password" name="password" />
                   	<ul>
                    	<li><a href="#">Forgot Password</a></li>
                        <li>|</li>
                        <li><a href="#">Register</a></li>
                    </ul>
                    
                    
                    <input type="submit" value="Login" />            
                    </form>
              
            </div>
        </div>
        </div>
        
         
        <div class="form-login">
        <div style="padding:30px 0; float:left;">
        	<h1>Login Area</h1>
           	<img src="<?php //echo $this->baseurl ?>/templates/gaineyrancha/images/lock.png" style="float:left; width:78px; margin:0px 15px 0 0;" />
            <div class="form">
        <jdoc:include type="modules" name="login" />
         </div>
        </div>
        </div>-->
    </div>
    <div class="footer">
    	<div class="footer-text">
        	Gainey Ranch Community Association &copy; Copyright 2012. All Rights Reserved.
        </div>
    </div>
    </div>
    </div>
</body>
</html>