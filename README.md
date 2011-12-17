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
    require 'DataExtractor.php';

    // initialize webget lib
    $wget = new WebGet();

    // fetch the content
    $content = $wget->requestContent("http://some.domain.com/folder/file.ext");

    // initialize DataExtractor lib
    $dx = new DataExtractor($content);

    // setup search pattern
    $dx->a_pattern_name = '/regular-expression/';
    $dx->another_pattern_name = '/another-regular-expression/';

    // extract all the data
    $data = $dx->extractArray();

    print_r($data);
    ?>