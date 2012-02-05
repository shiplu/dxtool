<?php

require '../DataExtractor.php';
require '../WebGet.php';

$url = 'http://newyork.craigslist.org/search/jjj?addFour=part-time';

$w = new WebGet();

// using cache to prevent repetitive download
$w->useCache = true;
$w->cacheLocation = '/tmp';
$w->cacheMaxAge = 3600;


$content = $w->requestContent($url);
$dx = new DataExtractor($content); 

// the text part we want to match must be in the first subpattern
$dx->p= '|<p>(.*?)(?=</p><p>)|a';
print_r($dx->extractArray());
?>
