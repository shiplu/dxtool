<?php

/**
 * Extract data as object or array from plain text or html
 * @author  shiplu<shiplu.net@gmail.com>
 * @package PHP-Utils
 */
class DataExtractor {
    /**
     * Some common pattern
     * Thanks to,
     * http://regexlib.com
     */
    const PATTERN_EMAIL = '#([\w\-_\.]+@([\w\-]+\.)+[\w\-]+)#';
    const PATTERN_EMAIL_ALL = '#([\w\-_\.]+@([\w\-]+\.)+[\w\-]+)#a';
    const PATTERN_URL = '#((http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?)#';
    const PATTERN_URL_ALL = '#((http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-\@?^=%&/~\+#])?)#a';
    const PATTERN_NUMBER = '#(-?[\d,]*(\.\d+)?|\([\d,]*(\.\d+)?\))#';
    const PATTERN_NUMBER_ALL = '#(-?[\d,]*(\.\d+)?|\([\d,]*(\.\d+)?\))#';

    // Author: Jay Gann 
    const PATTERN_PHONE_US = '|((1?(-?\d{3})-?)?(\d{3})(-?\d{4}))|';
    const PATTERN_PHONE_US_ALL = '|((1?(-?\d{3})-?)?(\d{3})(-?\d{4}))|a';

    // Author: Jay Solomon
    const PATTERN_ZIP_US = '~(^\d{5}$)|(^\d{5}-\d{4}$)~';
    const PATTERN_ZIP_US_ALL = '~(^\d{5}$)|(^\d{5}-\d{4}$)~a';
    
    // Author: W. D.
    const PATTERN_ZIP_CA = '~([a-eghj-npr-tvxy][0-9][abceghj-npr-tv-z] {0,1}[0-9][abceghj-npr-tv-z][0-9])~i';
    const PATTERN_ZIP_CA_ALL = '~([a-eghj-npr-tvxy][0-9][abceghj-npr-tv-z] {0,1}[0-9][abceghj-npr-tv-z][0-9])~ia';

    const PATTERN_PHONE_COMMON = '~(\+?[\d\- ]{6,15})~';
    const PATTERN_PHONE_COMMON_ALL = '~(\+?[\d\- ]{6,15})~a';
    
    const PATTERN_HTML_ANCHOR_TEXT = '#<a [^>]+>([^<]+)</a>#i';
    const PATTERN_HTML_ANCHOR_TEXT_ALL = '#<a [^>]+>([^<]+)</a>#ia';
    const PATTERN_HTML_ANCHOR_URL = '#<a [^>]*(?=href=")href="(https?[^"]+|ftps?[^"]+)"#i';
    const PATTERN_HTML_ANCHOR_URL_ALL = '#<a [^>]*(?=href)href="(https?[^"]+|ftps?[^"]+)"#ia';
    
    
    
    /**
     * Enables debugging. If enabled lot of information will be printed. 
     * @var boolean 
     */
    public $enableDebug = false;

    /**
     * Saves patterns
     *
     * @var array
     */
    private $__varpattern = array();

    /**
     * Extracted raw data
     *
     * @var mixed
     */
    public $data;

    /**
     * Input content to be parsed.
     *
     * @var string
     */
    private $__content;

    /**
     * sets the content
     *
     * @param string $content content to be parsed (optional)
     */
    public function setContent($content="") {
        $this->__content = $content;
    }

    /**
     * creates DataExtractor instance
     *
     * @param string $content content to be parsed
     */
    public function __construct($content) {
        $this->init($content);
    }

    public function __destruct() {
        $this->setContent(null);
    }

    /**
     * Saves pattern
     *
     * @param string $name name of the attribute/data
     * @param $patternvalue pattern on the attribute/data
     */
    public function __set($name, $patternvalue) {
        $this->__varpattern[$name] = $patternvalue;
    }

    /**
     * Initialize DataExtractor 
     * @param string $content content to initialize to
     */
    public function init($content) {
        $content = trim($content);
        // removing any new line characters. For data extraction 
        // these are not necessary.
        $content = preg_replace("|[\r\n]+|", "", $content);
        $this->__varpattern = array();
        $this->data = array();
        $this->setContent($content);
    }

    /**
     * Extracts an array according to given content
     *
     * @param string $content content to be parsed
     * @param array $patterns patterns of the attributes.
     * @return array desired object as an array.
     */
    public function extractArrayFromContentByPattern($content, $patterns) {
        $this->setContent($content);
        $this->__varpattern = $patterns;
        return $this->extractArray();
    }

    /**
     * Extracts an object according to given content
     *
     * @param string $content content to be parsed
     * @param array $patterns patterns of the attributes.
     * @return object desired object.
     */
    public function extractObjectFromContentByPattern($content, $patterns) {
        $this->setContent($content);
        $this->__varpattern = $patterns;
        return json_decode(json_encode($this->extractArray()));
    }

    /**
     * Simply extracts the object
     *
     * @return object desired object.
     */
    public function extractObject() {
        return json_decode(json_encode($this->extractArray()));
    }

    /**
     * Simply extracts object as an array
     *
     * @return array desired object as an array.
     */
    public function extractArray() {
        $ret = array();
        if (!empty($this->__content)):
            foreach ($this->__varpattern as $name => $re):
                $match = array();
                if (($nre = preg_replace('/(a)([imsxeADSUXu]*)$/', '\2', $re)) == $re):
                    if ($this->enableDebug) {
                        echo "Name: $name, RE: $re\n";
                    }
                    preg_match($re, $this->__content, $match);
                else:
                    if ($this->enableDebug) {
                        echo "Name: $name, RE: $nre\n";
                    }
                    preg_match_all($nre, $this->__content, $match);
                endif;
                if ($this->enableDebug) {
                    print_r($match);
                }
                if (count($match) > 1):
                    $ret[$name] = end($match);
                else:
                    $ret[$name] = "";
                endif;
            endforeach;
        endif;
        return $ret;
    }

}

?>
