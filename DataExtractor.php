<?php
/**
 * Extract data as object or array from plain text or html
 * @author  shiplu<shiplu.net@gmail.com>
 * @package PHP-Utils
 */
class DataExtractor{
	/**
	 * Saves patterns
	 *
	 * @var array
	 */
	private $__varpattern=array();
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
	 * @param string $content content to be parsed
	 */
	public function setContent($content){
		$this->__content = $content;
	}
	/**
	 * creates DataExtractor instance
	 *
	 * @param string $content content to be parsed
	 */
	public function __construct($content){
		$this->__varpattern = array();
		$data = array();
		$this->setContent($content);
	}
	public function __destruct(){
		$this->setContent(null);
	}
	/**
	 * Saves pattern
	 *
	 * @param string $name name of the attribute/data
	 * @param $patternvalue pattern on the attribute/data
	 */
	public function __set($name, $patternvalue){
		$this->__varpattern[$name]=$patternvalue;
	}
	public function init($content){
		$this->__varpattern = array();
		$data = array();
		$this->setContent($content);
	}
	/**
	 * Extracts an array according to given content
	 *
	 * @param string $content content to be parsed
	 * @param array $patterns patterns of the attributes.
	 * @return array desired object as an array.
	 */
	public function extractArrayFromContentByPattern($content, $patterns){
		$this->setContent($content);
		$this->__varpattern = $patterns;
		return  $this->extractArray();
	}
	/**
	 * Extracts an object according to given content
	 *
	 * @param string $content content to be parsed
	 * @param array $patterns patterns of the attributes.
	 * @return object desired object.
	 */
	public function extractObjectFromContentByPattern($content, $patterns){
		$this->setContent($content);
		$this->__varpattern = $patterns;
		return json_decode(json_encode($this->extractArray()));
	}
	/**
	 * Simply extracts the object
	 *
	 * @return object desired object.
	 */
	public function extractObject(){
			return json_decode(json_encode($this->extractArray()));
	}
	/**
	 * Simply extracts object as an array
	 *
	 * @return array desired object as an array.
	 */
	public function extractArray(){
		$ret = array();
		$this->setContent(trim($this->__content));
		if(!empty($this->__content)):
			foreach($this->__varpattern as $name => $re):
				$match = array();
				if(($nre = preg_replace('/(a)([imsxeADSUXu]*)$/','\2',$re))==$re):
					if(MYDEBUG==1){echo "Name: $name, RE: $re\n";}
					preg_match($re , $this->__content, $match);
				else:
					if(MYDEBUG==1){echo "Name: $name, RE: $nre\n";}
					preg_match_all($nre , $this->__content, $match);
				endif;
				if(MYDEBUG==1){print_r($match);}
				if(count($match)>1):
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

