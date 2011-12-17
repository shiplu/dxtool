<?php

require 'DataExtractor.php';
require 'WebGet.php';

$google_new_feed = 'http://news.google.com/news?pz=1&cf=all&ned=in&hl=en&output=rss';

$w = new WebGet();
$content = $w->requestContent($google_new_feed);
$dx = new DataExtractor($content); 
$dx->titles = '|title>([^<]+)</title|a';
$dx->rsstitle = '|title>([^<]+)</title|';
$data = $dx->extractArray();

print_r($data);

?>