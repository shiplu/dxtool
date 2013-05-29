<?php

require '../DataExtractor.php';
require '../WebGet.php';

$url = 'http://newyork.craigslist.org/search/jjj?addFour=part-time';

$w = new WebGet();

// using cache to prevent repetitive download
$w->setup_cache(3600, '/tmp');


$content = $w->requestContent($url);
$dx = new DataExtractor($content); 

// the text part we want to match must be in the first subpattern
$dx->p= '|</small>\s*<a[^>]+>([^<]+)<|a';
print_r($dx->extractArray());
?>
