<!DOCTYPE html> 
<?php

define('DAY', 24 * 60 * 60);

require("definitions/defs.database.php");
require("classes/class.dataobject.php");
require("classes/class.database.php");

$incoming = Database::get("SELECT * FROM items ORDER BY fDateTime DESC");

?>
<html> 
	<head> 
		<title>Page Title</title> 
		<link href='http://fonts.googleapis.com/css?family=Droid+Serif:regular,italic,bold&subset=latin' rel='stylesheet' type='text/css'> 
		<link rel="stylesheet" type="text/css" href="css/incoming.css" media="screen" /> 
		<meta charset="utf-8"  /> 
	</head> 
	
	<body> 
	 <div id="header"> 
		<h1><span>Your Site</span> Incoming</h1> 
	</div> 
	
	<div id="sidebar"> 
		<p>Your welcome message</p> 
		<p><a href="feed/">Get an RSS feed</a> | <a href="mailto:youraddress@site.com">Suggest a link</a></p> 
	</div> 
	<div id="items">
			
		<?php
		
		for($i = 0; $i < count($incoming); $i++)
		{
		
			$item = $incoming[$i];
		?>
		<div class="item">
			<a href="<?php echo $item->getLink() ?>"> 
				<span class="title"><?php echo stripslashes($item->getTitle()) ?></span> 
				<span class="description"><?php echo stripslashes($item->getDescription()) ?></span> 
				<span class="date"><?php echo date("jS F, Y \a\\t g:ia", strtotime($item->getDateTime())) ?></span>
				<?php
				
				if(strtotime($item->getDateTime()) > time() - DAY)
				{
				?>
				<span class="new"></span>
				<?php
				}
				
				?>
			</a> 
		</div>
		<?
		}
		
		?>
		<div class="clear"></div> 
		<div id="footer"> 
		Made by Paul Lewis. Download a copy of Incoming from <a href="https://github.com/paullewis/Incoming">Github</a>.
		</div> 
	</div> 
	
	<!-- Analytics --> 
	<script type="text/javascript"> 
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script> 
	<script type="text/javascript"> 
	try {
	var pageTracker = _gat._getTracker("UA-XXXXXXXX-X");
	pageTracker._trackPageview();
	} catch(err) {}</script> 
	 
	</body> 
</html>