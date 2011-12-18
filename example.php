<?php

require 'DataExtractor.php';
require 'WebGet.php';

$google_new_feed = 'http://news.google.com/news?pz=1&cf=all&ned=in&hl=en&output=rss';

$w = new WebGet();

// using cache to prevent repetitive download
$w->useCache = true;
$w->cacheLocation = '/tmp';
$w->cacheMaxAge = 3600;



$content = $w->requestContent($google_new_feed);
$dx = new DataExtractor($content); 

// the text part we want to match must be in the first subpattern
$dx->titles = '|title>([^<]+)</title|a';
$dx->rsstitle = '|title>([^<]+)</title|';
print_r($dx->extractArray());


// use built in anchor text pattern 
$dx->init($w->requestContent("http://shiplu.mokadd.im"));
$dx->anchor_texts = DataExtractor::PATTERN_HTML_ANCHOR_TEXT_ALL;
$dx->anchor_urls = DataExtractor::PATTERN_HTML_ANCHOR_URL_ALL;

print_r($dx->extractArray());

?>