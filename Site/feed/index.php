<?php
header("Content-Type:text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";

// Definitions
require("../definitions/defs.feed.php");
require("../definitions/defs.database.php");
require("../definitions/defs.regexps.php");

// Classes
require("../classes/class.dataobject.php");
require("../classes/class.database.php");
require("../classes/class.app.helper.php");

$arrIncoming = Database::get("SELECT * FROM items ORDER by fDateTime DESC");
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"> 
 
<channel> 
	<title><?php echo FEED_TITLE ?></title> 
	<atom:link href="<?php echo FEED_URL ?>" rel="self" type="application/rss+xml" /> 
	<link><?php echo FEED_URL ?></link> 
	<description><?php echo FEED_DESCRIPTION ?></description> 
	<lastBuildDate><?php echo date("r", strtotime($arrIncoming[0]->getDateTime())) ?></lastBuildDate> 
	<language>en</language> 
	<generator>Aerotwist Incoming</generator> 
	
	<?php 
	for($i = 0; $i < count($arrIncoming); $i++)
	{
		$objIncoming = $arrIncoming[$i];
	?>	
		<item> 
		<title><![CDATA[<?php echo stripslashes($objIncoming->getTitle()); ?>]]></title> 
		<link><?php echo $objIncoming->getLink(); ?></link> 
		<pubDate><?php echo date("r", strtotime($objIncoming->getDateTime()))?></pubDate> 
		<category><![CDATA[Links]]></category> 
 
		<guid isPermaLink="true"><?php echo $objIncoming->getLink() ?></guid> 
		<description><![CDATA[<?php stripslashes($objIncoming->getDescription()) ?>]]></description>
		</item>
	<?php 
	}
	?>
</channel>
</rss>