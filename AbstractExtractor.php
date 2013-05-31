<?php

/**
 * Extract data as object or array from plain text or html using XPath
 * @author  shiplu<shiplu.net@gmail.com>
 * @package PHP-Utils
 */
abstract class AbstractExtractor {

    /**
     * Store the definitions of the data
     * @var array
     */
    protected $definitions;

    /**
     * Extracted raw data
     *
     * @var mixed
     */
    protected $data;

    /**
     * Input content to be parsed.
     *
     * @var string
     */
    protected $content;

    /**
     * sets the content
     *
     * @param string $content content to be parsed (optional)
     */
    protected function setContent($content="") {
        $this->content = $content;
    }

    /**
     * getst the content
     * @return string content
     */
    protected function getContent(){
        return $this->content;
    }
    
    /**
     * Returns all the definitions
     * @var array
     */
    protected function getDefinitions(){
        return $this->definitions;
    }
    
    /**
     * creates DataExtractor instance
     *
     * @param string $content content to be parsed
     */
    public function __construct($content) {
        $this->init($content);
    }

    /**
     * Saves pattern
     *
     * @param string $name name of the attribute/data
     * @param $patternvalue pattern on the attribute/data
     */
    public function __set($name, $value) {
        $this->definitions[$name] = $value;
    }

    /**
     * Initialize DataExtractor 
     * @param string $content content to initialize to
     */
    public function init($content) {
        $this->definitions = array();
        $this->data = array();
        $this->setContent($content);
    }

    /**
     * Gets saved data from cache
     * @return mixed the saved data
     */
    public function getCachedData(){
        return $this->data;
    }

    /**
     * Save the data
     * @param mixed $data. It can be array or object
     * @return mixed the data which is saved.
     */
    protected function saveData($data){
        $this->data = $data;
        return $this->getCachedData();
    }
    /**
     * Simply extracts 
     *
     * @return array desired object as an array.
     * @abstract 
     */
    abstract public function extract();

}

?>
