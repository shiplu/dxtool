<?php

/**
 * Extract data as object or array from plain text or html
 * @author  shiplu<shiplu.net@gmail.com>
 * @package PHP-Utils
 */
class RegExExtractor extends AbstractExtractor{
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
     * Simply extracts object as an array
     *
     * @return array desired object as an array.
     */
    public function extract() {
        $ret = array();
        $cont = $this->getContent();
        if (!empty($cont)):
            foreach ($this->getDefinitions() as $name => $re):
                $match = array();
                if (($nre = preg_replace('/(a)([imsxeADSUXu]*)$/', '\2', $re)) == $re):
                    preg_match($re, $cont, $match);
                else:
                    preg_match_all($nre, $cont, $match);
                endif;
                if (count($match) > 1):
                    $ret[$name] = end($match);
                else:
                    $ret[$name] = "";
                endif;
            endforeach;
        endif;
        return $this->saveData($ret);
    }

}

?>
