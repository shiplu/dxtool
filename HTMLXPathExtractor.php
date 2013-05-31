<?php

/**
 * Extract data as object or array from plain text or html using XPath
 * @author  shiplu<shiplu.net@gmail.com>
 * @package PHP-Utils
 */
class HTMLXPathExtractor extends AbstractExtractor{

    private $xpath;

    /**
     * Initialize DataExtractor 
     * @param string $content content to initialize to
     */
    public function init($content) {
        $this->definitions = array();
        $this->data = array();
        $this->setContent($content);
        
        $dom = new DOMDocument();

        // suppress the errors for invalid html
        libxml_use_internal_errors(true);

        $dom->loadHTML($content);

        $this->xpath = new DOMXPath($dom);
    }
    
    public function extract() {
        $ret = array();
        foreach($this->getDefinitions() as $name => $expr){
            $nodes = $this->xpath->query($expr);
            if ($nodes){
                $values = array();
                foreach($nodes as $node){
                    $values[] = $node->nodeValue;
                }
                $ret[$name] = $values;
            }
        }
        return $this->saveData($ret);
    }
}

?>
