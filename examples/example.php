<?php

require '../AbstractExtractor.php';
require '../RegExExtractor.php';
require '../HTMLXPathExtractor.php';
require '../WebGet.php';

$google_new_feed = 'http://news.google.com/news?pz=1&cf=all&ned=in&hl=en&output=rss';

$w = new WebGet();

// using cache to prevent repetitive download
$w->setup_cache(3600, '/tmp');



$content = $w->requestContent($google_new_feed);
$dx = new RegExExtractor($content); 

// the text part we want to match must be in the first subpattern
$dx->titles = '|title>([^<]+)</title|a';
$dx->rsstitle = '|title>([^<]+)</title|';
print_r($dx->extract());


// use built in anchor text pattern 
$dx->init($w->requestContent("http://shiplu.mokadd.im"));
$dx->anchor_texts = RegExExtractor::PATTERN_HTML_ANCHOR_TEXT_ALL;
$dx->anchor_urls = RegExExtractor::PATTERN_HTML_ANCHOR_URL_ALL;

print_r($dx->extract());

// do not use any extractor. Just request the content
$content = $w->requestContent('https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&incslude_rts=0&screen_name=microsoft&count=200&exclude_replies=1&contributor_details=0');

print_r(json_decode($content, true));


// Get list of files of this project from github

$xp = new HTMLXPathExtractor($w->requestContent("https://github.com/shiplu/dxtool"));
$xp->content = '//div[@id="slider"]//tr/td[@class="content"]/a/text()';
$xp->age = '//div[@id="slider"]//tr/td[@class="age"]/time/text()';
$xp->message = '//div[@id="slider"]//tr/td[@class="message"]/a[1]/text()';
print_r($xp->extract());

?>
