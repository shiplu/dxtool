<?php


/**
 * Creats Extractor
 *
 * @author shiplu
 */
class ExtractorFactory {
    private static $classMap = array(
        'regex' => 'RegExExtractor',
        'reg' => 'RegExExtractor',
        'html-xpath' => 'HTMLXPathExtractor',
        'htmlxpath' => 'HTMLXPathExtractor'
    );
    /**
     * Create an Extractor
     * @param string $name Extractor name. It can be 'regex', 'reg', 'html-xpath' or 'htmlxpath'
     * @param string $content content to be used for initializing extractor
     * @return AbstractExtractor Extractor instance
     */
    public static function create($name, $content){
        if(!class_exists('AbstractExtractor')){
            require dirname(__FILE__).DIRECTORY_SEPARATOR.'AbstractExtractor.php';
        }
        // if the class does not exist search for it and include it
        if(!class_exists(self::$classMap[$name])){
            $fn = dirname(__FILE__).DIRECTORY_SEPARATOR.self::$classMap[$name].'.php';
            if(file_exists($fn)){
                require $fn;
            }else{
                throw new Exception("$name Extractor not found", 1);
            }
            
        }
        $class_name = self::$classMap[$name];
        return new $class_name($content);
    }
}

?>
