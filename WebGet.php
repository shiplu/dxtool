<?php
class WebGet{
	/**
	 * Enables debugging. If enabled lot of information will be printed. 
	 * @var boolean 
	 */
	public $enableDebug=false;
	/**
	 * The name of the outgoing network interface to use. This can be an interface name, an IP address or a host name 
	 * @var string
	 */
	public $interface="";
	/**
	 * Path of the file where cookies will writen and retrieved from. The file must be writable
	 * @var string
	 */
	public $cookieFile="";
	/**
	 * Content caching will be enabled if set to true
	 * @var boolean 
	 */
	public $useCache=false;
	/**
	 * Maximum age of cache before its renewd
	 * @var int age of cache in seconds
	 */
	public $cacheMaxAge=10800;
	/**
	 * location of the cache files to be stored 
	 * @var string path of the directory 
	 */
	public $cacheLocation='';
	/**
	 * Whether compressed output will be decompressed
	 * @var boolean 
	 */
	public $decompressOutput = true;
	/**
	 * First header line
	 * @var string first line
	 */
	public $responseStatusLine="";
	/**
	 * Response code. eg 200
	 * @var int code 
	 */
	public $responseStatusCode=0;
	/**
	 * Response headers in array
	 * @var array array of headers 
	 */
	public $responseHeaders=array();
	/**
	 * Last url that was requested
	 * @var string  requested url 
	 */
	public $cachedUrl="";
	/**
	 * Any proxy server that will be used 
	 * @var string ip address of hostname/domain name of proxy server
	 */
	public $proxyServer="";
	/**
	 * Port number of proxy server
	 * @var int proxy server port
	 */
	public $proxyPort="";
	/**
	 * Username for proxy server authentication
	 */
	public $proxyUser="";
	/**
	 * Password for proxy server authentication
	 */
	public $proxyPassword="";
	
	/**
     * Last requested content is cached here
     * @var string
     */
	public   $cachedContent="";
	/**
	* @var string true to convert the downloaded data to unicode utf-8 encoding.
	*/
	public $convertToUtf8 = false;
	/**
         * associative array of default http headers that
         * will be passed with each request
         * @var array
         */
	public   $defaultHeaders=array(
		"User-agent" =>"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16",
		"Accept" =>"text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8",
		"Accept-language" =>"en-us,en;q=0.7,bn;q=0.3",
		"Accept-charset" =>"ISO-8859-1,utf-8;q=0.7,*;q=0.7"
	);
	/**
         * Denotes the format of the header would be an array of header lines.
         */
	const  HEADER_FORMAT_LINESARRAY=1;
	/**
         * Denotes the format of the header would be a string seperated by EOL
         */
	const  HEADER_FORMAT_STRING=2;
	public function __construct(){
	}
	public function __destruct(){
	}
	/**
         * Build headers from array on headers
         *
         * @param array $headers an associated array of headers. Key is the name of header and  value is the  content of the header
         * @param  const $format either HEADER_FORMAT_LINESARRAY or  HEADER_FORMAT_STRING
         * @return mixed  on success return  the header built. otherwise false. use  idendity operator to check it.
         * @see  PluginBase::HEADER_FORMAT_LINESARRAY
         * @see PluginBase::HEADER_FORMAT_STRING
         */
	protected   function buildHeaders	($headers,$format=1){
		if($format == self::HEADER_FORMAT_LINESARRAY && is_array($headers)){
			$res = array();
			foreach($headers as $hn => $hv){
				$res[]="$hn: $hv";
			}
			return  $res;
		}elseif ($format == self::HEADER_FORMAT_STRING  && is_string($headers)){
			$res = preg_split('#[\r\n]+#',$headers,-1,PREG_SPLIT_NO_EMPTY);
			return $res;
		}else{
			return false;
		}
	}
	/**
         * Request content from an url
         *
         * @param string $url url to request. must be in a standard format
         * @param array $getvars associative array of get variables
         * @param array $postvars associative array of post variables
         * @param array $headers associated array of headers. Key is the name of header and  value is the  content of the header
         * @return string content of the response
         */
	public   function requestContent($url, $getvars=array(),$postvars=array(), $headers=array()){
		$cache_file = $this->cacheLocation.DIRECTORY_SEPARATOR.strtoupper(sha1(serialize($url).serialize($getvars).serialize($postvars).serialize($headers))).'.html';
		// If the cache contains the content. use the content	
		if($this->useCache){
			if(file_exists($cache_file) && 0!=filesize($cache_file)){
				//Checking if cache is expired
				if((mktime()-filemtime($cache_file))<$this->cacheMaxAge){
					$this->cachedContent = file_get_contents($cache_file);
					return $this->cachedContent;
				}
			}
		}
		$this->cachedUrl = $url;
		$accept_encoding = false;
		$this->responseHeaders=array();
		$this->responseStatusCode=0;
		$this->responseStatusLine="";
		/// Checking if Accept-encoding header is set by user
		foreach ($headers as $hn => $hv) {
			if(trim(strtoupper($hn))=='ACCEPT-ENCODING'){
				$accept_encoding = true;
			}
		}
		
		/// If no accept-encoding header is set by user, a default one will be set
		if($accept_encoding==false){
			$headers['Accept-Encoding']='gzip, deflate';
		}
	
		//parsing the url component
		$u = parse_url($url);
		// url scheme (protocol) and host name is a must
		// If not found return right now.
		if(!isset($url['scheme']) || !isset($url['host'])){
			return false;
		}
		// if path is set it will be used
		if(isset($u['path'])){
			// If query string is found it will be used
			if(isset($u['query']))	{
				$url = "{$u['scheme']}://{$u['host']}{$u['path']}?{$u['query']}";
			}else {
				// query stirng not found. so wont be used.
				$url = "{$u['scheme']}://{$u['host']}{$u['path']}";
			}
		}else{
			// Path is not found. so not using.
			$url = "{$u['scheme']}://{$u['host']}";
		}
		// only process get variables if its provided valid
		if(is_array($getvars)&&count($getvars)>0){
			//If both get variables and query string is passed then
			//get varables are appended to the query string
			if(isset($u['query'])&&strlen($u['query'])>0){
				$url="$url&".http_build_query($getvars);
			}else{
				$url="$url?".http_build_query($getvars);
			}
		}

		// Staring curl transaction
		if($this->enableDebug==1){echo "--> $url\n";}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'saveHeaders'));

		// Handling source IP/host names
		if(!is_null($this->interface) && !empty($this->interface)){
			curl_setopt($ch, CURLOPT_INTERFACE, $this->interface);
		}

		// handling proxy here
		if(!empty($this->proxyServer) && !empty($this->proxyPort)){
			//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1); 
			curl_setopt($ch, CURLOPT_PROXY, $this->proxyServer. ":" . $this->proxyPort); 
			if(!empty($this->proxyUser) && !empty($this->proxyPassword)){
			
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyUser . ":" . $this->proxyPassword); 
			}
		}
		// Setting cookie functionality
		if(!empty($this->cookieFile) && file_exists($this->cookieFile) && is_writeable($this->cookieFile) ){
			curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
		}
		if(is_array($postvars)&&count($postvars)>0){
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			http_build_query($postvars)
			);
		}elseif (is_string($postvars)&&strlen($postvars)>0){
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			$postvars
			);
		}
		if(is_array($headers)&&count($headers)>0){
			curl_setopt($ch, CURLOPT_HTTPHEADER,
			$this->buildHeaders($headers, self::HEADER_FORMAT_LINESARRAY)
			);
		}
		
		$data = curl_exec($ch);
		
		
		/// Decompress output if neccessary.
		if($this->decompressOutput==true && isset($this->responseHeaders['CONTENT-ENCODING'])){
			switch (strtolower($this->responseHeaders['CONTENT-ENCODING'])){
				case 'gzip':
					/// Decrompress gzip
					$data = $this->gzdecode($data);
					break;
				case 'deflate';
					/// Decompress deflate encoding
					$data = gzinflate($data);
					break;
				default:
					break;
			}
		}
		
		
		/// Converting to utf8 if neccessary.
		if($this->convertToUtf8==true):
			$this->cachedContent = utf8_encode($data);
		else:
			$this->cachedContent = $data;
		endif;
		
		if(curl_errno($ch)>0){
			trigger_error("Curl Error: ". curl_error($ch));
			print_r(curl_getinfo($ch));
		}
		curl_close($ch);
		
		//Write downloaded data to cache
		if($this->useCache){
			file_put_contents($cache_file, $this->cachedContent);
		}
		
		return $this->cachedContent;
	}
	protected  function saveHeaders($ch, $header){
		/// Saving http response headers
		if(preg_match('#HTTP/(?P<version>[\d\.]+)\s+(?P<code>\d+)#',$header, $m)){
			if(isset($m['code'])){
				$this->responseStatusCode = $m['code'];
				$this->responseStatusLine = $m[0];
			}
		}else if(preg_match('#^(?P<name>[^:]+):\s*(?P<value>.*)#',$header, $m)){
			if(isset($m['name']) && isset($m['value'])){
				$this->responseHeaders[strtoupper(trim($m['name']))]=trim($m['value']);
			}
		}
		
		return strlen($header);
	}
	protected function gzdecode($data){
		if (function_exists('gzdecode')){
			return gzdecode($data);
		}
		$flags = ord(substr($data, 3, 1));
		$headerlen = 10;
		$extralen = 0;
		$filenamelen = 0;
		if ($flags & 4) {
		    $extralen = unpack('v' ,substr($data, 10, 2));
		    $extralen = $extralen[1];
		    $headerlen += 2 + $extralen;
		}
		if ($flags & 8) // Filename
		    $headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 16) // Comment
		    $headerlen = strpos($data, chr(0), $headerlen) + 1;
		if ($flags & 2) // CRC at end of file
		    $headerlen += 2;
		$unpacked = gzinflate(substr($data, $headerlen));
		if ($unpacked === FALSE)
		      $unpacked = $data;
		return $unpacked;
	}
}
?>

