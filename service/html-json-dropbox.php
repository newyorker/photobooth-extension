<?php

header('Content-Type: application/json');
date_default_timezone_set('America/New_York');

/*
	phpQuery:
	https://github.com/TobiaszCudnik/phpquery

	Dropbox
	https://www.dropbox.com/developers/core/sdks/php
	https://www.dropbox.com/developers/core/start/php
*/

include("/PATH/TO/lib/phpQuery-onefile.php");
require_once "/PATH/TO/lib/Dropbox/autoload.php";

use \Dropbox as dbx;

class PhotoBoothJSON {

    function Run() {
		$data = $this->Extract();
		if (!empty($data)) {
			$data = $this->Publish($data);
		}
	}

	function Extract() {

		$baseUrl = "http://www.newyorker.com/culture/photo-booth/";
		
		$results = array();
		$results['title']   = "Photo Booth";
		$results['link']    = $baseUrl;
		$results['updated'] = date('r');
		$results['items']   = array();

		$pageLimit = 8;

		for ($i=1; $i<=$pageLimit; $i++) {

		/*	This is specific to the New Yorker site where blog index pages have pagination
			Example:
				http://www.newyorker.com/culture/photo-booth/page/1
				http://www.newyorker.com/culture/photo-booth/page/2
				...
				http://www.newyorker.com/culture/photo-booth/page/{page-number}
		*/

			$url = $baseUrl . "page/" . $i;
			phpQuery::newDocumentFileHTML($url);
			$stories = array();
			$storyCount = 0;

		/*
			We're dealing with HTML structure that is unique to pages on newyorker.com
			
			We use "phpQuery" for the heavy lifting in regards to digging through the DOM

			* https://code.google.com/p/phpquery/wiki/Basics
			* https://github.com/TobiaszCudnik/phpquery
			
			You'll need to dig into this depending on your source page.
		*/

			foreach (pq("body")->find('div.posts article') as $article) {

				$article = pq($article);
				
				$figure = $article->find('figure');
				$header = $article->find('header');

				$story['link']			= $figure->find('a')->attr("href");
				$story['headline']		= $figure->find('a')->attr("title");
				$story['author']		= $header->find('h3 span a')->attr("title");
				$story['image']			= $figure->find('img')->attr("src");
				$story['orientation']   = "horizontal";

				$imgClass = $figure->find('img')->attr("class"); // vertical;
				if (strpos($imgClass, 'vertical') !== FALSE) {
					$story['orientation'] = "vertical";
				}

				if (!empty($story['image'])) {
					array_push($results['items'], $story);
				}
			}
		}

		return json_encode($results);
	}

    function Publish($data) {
		$fn = "/LOCAL/PATH/TO/newyorker/photobooth/index.js"; // First, publish to local directory...
        if (is_writable($fn)) {
            if (!$handle = fopen($fn, 'w')) {
                 exit;
            }
            if (fwrite($handle, $data) === FALSE) {
                exit;
            }
            fclose($handle);
			$this->Upload($fn); // ...and then upload it to The Internet Tubes
        }
    }

    function Upload($fn) {
	/*
		"Generate an access token for your own account"
		https://blogs.dropbox.com/developers/2014/05/generate-an-access-token-for-your-own-account/
		
		I publish to a "public" directory in a DropBox account
		https://www.dropbox.com/en/help/4224
		
		WARNING:
		You'll have a hell of a time getting your extension approved by Google 
		if you're using a feed hosted on "dl.dropboxusercontent.com"
		No Really... Not kidding.
	*/
		$unifyToken = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"; /* Add yours here */
		$dbxClient = new dbx\Client($unifyToken, "PHP-Example/1.0");
		$f = fopen($fn, "rb");
		$result = $dbxClient->uploadFile("/public/extension/newyorker/photobooth/index.js", dbx\WriteMode::force(), $f);
		fclose($f);
    }
}

$event = new PhotoBoothJSON;
$response = $event->Run();

?>
