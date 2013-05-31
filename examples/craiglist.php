<?php

require '../AbstractExtractor.php';
require '../HTMLXPathExtractor.php';
require '../WebGet.php';

$url = 'http://newyork.craigslist.org/search/jjj?addFour=part-time';

$w = new WebGet();

// using cache to prevent repetitive download
$w->setup_cache(3600, '/tmp');


$content = $w->requestContent($url);
$xpe = new HTMLXPathExtractor($content); 

// the text part we want to match must be in the first subpattern
$xpe->p= '//p[@class="row"]/span[@class="pl"]/a/text()';
print_r($xpe->extract());
?>
