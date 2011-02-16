<?php

// get our data connection ready
require("../definitions/defs.twitter.php");
require("../definitions/defs.bitly.php");
require("../definitions/defs.database.php");
require("../classes/class.dataobject.php");
require("../classes/class.database.php");

// collect the posted data
$title		 	= isset($_POST["title"]) 		? stripslashes($_POST["title"]) : "No Title";
$description 	= isset($_POST["description"]) 	? stripslashes($_POST["description"]) : "";
$link 		 	= isset($_POST["link"]) 		? $_POST["link"] : "Link";
$twitterTitle	= $title;
$originalLink	= $link;

// we need to bit.ly the link
$bitlyLink	 	= file_get_contents("http://api.bit.ly/v3/shorten?domain=".BITLY_DOMAIN."&login=".BITLY_LOGIN."&apiKey=".BITLY_API_KEY."&longUrl=".urlencode($link)."&format=json");
$objLink	 	= json_decode($bitlyLink);
$link 		 	= $objLink->data->url;

if(!is_null($link))
	$link = $originalLink;

// push to the database
Database::set("INSERT INTO items SET fTitle = ?, fDescription = ?, fDateTime = NOW(), fLink = ?", array($title, $description, $link));

// if we are pushing to Twitter
if(isset($_POST["twitter"]) && $_POST["twitter"] == 1)
{
	// very basic check for safety
	if(file_exists('Epi/EpiCurl.php'))
	{
		include 'Epi/EpiCurl.php';
		include 'Epi/EpiOAuth.php';
		include 'Epi/EpiTwitter.php';
		$consumer_key 		= TWITTER_CONSUMER_KEY;
		$consumer_secret 	= TWITTER_CONSUMER_SECRET;
		$token 				= TWITTER_TOKEN;
		$secret				= TWITTER_SECRET;
		$twitterObj 		= new EpiTwitter($consumer_key, $consumer_secret, $token, $secret);
		$twitterObjUnAuth 	= new EpiTwitter($consumer_key, $consumer_secret);

		// now do the push
		$twitterObj->post('/statuses/update.json', array('status' => TWITTER_PREAMBLE.stripslashes($twitterTitle).' - '.$link));
	}
}

?>