Requirement
===========

* php5
* php5-curl extension
* php5-json extension (already included with php5)


Features
========

* Extract Data from any http resource
* Use simple regular expression to extract data
* Hassle free http transaction
* Supports cookie (via curl)
* Can cache http response


Usage
=====

    <?php 
    // include libraries. 
    require 'WebGet.php';
    require 'ExtractorFactory.php';
    

    // initialize webget lib
    $wget = new WebGet();

    // fetch the content
    $content = $wget->requestContent("http://some.domain.com/folder/file.ext");

    // initialize regular expression powered extractor
    $dx = ExtractorFactory::create('regex', $content);

    // setup search pattern
    $dx->a_pattern_name = '/regular-expression/';
    $dx->another_pattern_name = '/another-regular-expression/';

    // extract all the data
    $data = $dx->extract();

    print_r($data);


    // initialize xpath powered extractor
    $dx = ExtractorFactory::create('html-xpath', $content);
    
    // search pattern
    $dx->data_name = '//some/xpath';
    $dx->other_name = '//other/xpath/expression';

    // extract
    $data = $dx->extract();

    print_r($data);
    ?>
    
Also check the example.php file.