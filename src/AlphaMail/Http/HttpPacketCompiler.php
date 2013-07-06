<?php

	namespace AlphaMail\Http;

	use \InvalidArgumentException;

	use AlphaMail\Http\Entities\HttpHeaderItem;
	use AlphaMail\Http\Entities\HttpHeaderCollection;

	use AlphaMail\Http\Types\HttpProtocol;
	use AlphaMail\Http\Types\HttpMethod;

	use AlphaMail\Http\Exceptions\HttpPacketCompileException;

	class HttpPacketCompiler
	{
		const HTTP_SEPERATOR = "\r\n";
		const HTTP_DOUBLE_SEPERATOR = "\r\n\r\n";
		
		protected $_protocol_version, $_method = null, $_url = null;
		protected $_headers = null, $_body = null;
		
		public function __construct($method = HttpMethod::GET, $url, HttpHeaderCollection $headers = null, $body = null)
		{
			$this->setProtocolVersion(HttpProtocol::HTTP_1_1);
			$this->setHeaders($headers == null ? new HttpHeaderCollection() : $headers);
			$this->setMethod($method);
			$this->setBody($body);
			$this->setUrl($url);
		}
		
		public function setMethod($method)
		{
			$headers = $this->getHeaders();
			
			if($method == null)
				$method = HttpMethod::GET;
			
			switch($method)
			{
				case HttpMethod::GET:
					break;
					
				case HttpMethod::PUT:
					break;
					
				case HttpMethod::POST:
					if(!$headers->contains("Content-Type"))
						$this->setContentType('application/x-www-form-urlencoded');
					break;
				
				case HttpMethod::DELETE:
					break;
				
				default:
					throw new InvalidArgumentException("Invalid method '" . $method . "'");
					break;
			}
			
			$this->_method = $method;
		}
		
		public function getMethod()
		{
			return $this->_method;
		}
		
		public function setBody($value)
		{
			if(is_array($value) && $this->getMethod() == HttpMethod::POST)
			$value = http_build_query($value);
			$this->_body = $value;
		}
		public function getBody()
		{
			return $this->_body;
		}
		
		public function setUrl($value)
		{
			// Perform minor url correction(if neccessary) and parse url
			$url = parse_url((strpos($value, "://") === false ? "http://{$value}" : $value));
			
			// Set url defaults if they have been left out
			if(!isset($url['scheme'])) $url['scheme'] = 'http';
			if(!isset($url['path'])) $url['path'] = '/';
			
			// Correct parsed url further
			if(!isset($url['host']))
			{
				// If host couldent be parsed, this must mean that there is no
				// seperation between host and path. ex www.test.com(path left out).
				$url['host'] = str_replace("/", "", $url['path']);
				$url['path'] = '/';
			}
			
			// Set host header
			$this->_headers->set(new HttpHeaderItem("Host",
				$url['host'] . (string)(@$url["port"] == null ? "" : ":" . $url['port'])));
			
			// Store parsed url
			$this->_url = $url;
		}
		
		public function getProtocolVersion()
		{
			return $this->_protocol_version;
		}
		
		public function setProtocolVersion($value)
		{
			$this->_protocol_version = ($value == HttpProtocol::HTTP_1_0
				? $value : HttpProtocol::HTTP_1_1);
		}
		
		public function getHeaders()
		{
			return $this->_headers;
		}
		
		public function setHeaders(HttpHeaderCollection $value)
		{
			$this->setDefaultHeaders($value);
			$this->_headers = $value;
		}
		
		public function getContentType()
		{
			return $this->_headers->get('Content-Type')->getValue();
		}
		
		public function setContentType($value)
		{
			$this->_headers->set(new HttpHeaderItem("Content-Type", $value));
		}
		
		public function getUrl($component = null)
		{
			return $this->_url[$component];
		}
		
		public function compile()
		{
			return $this->compileHeader() . $this->getBody();
		}
		
		private function compileHeader()
		{
			// Set url, append query if available
			$query = @$this->getUrl("query");
			$url = $this->getUrl("path") . ($query == null ? null : "?{$query}");
			
			// Set data length
			if(($length = strlen($this->getBody())) > 0)
				$this->_headers->set(new HttpHeaderItem("Content-Length", $length));
			
			// Sort and build header
			$headers = $this->convertHeadersToAssocArray($this->_headers);
			arsort($headers);
			
			$header = $this->getMethod() . " {$url} " . $this->getProtocolVersion() . self::HTTP_SEPERATOR;
			$header .= $this->mergeAssocArray(": ", self::HTTP_SEPERATOR, $headers);
			
			// Return generated header
			return $header . self::HTTP_DOUBLE_SEPERATOR;
		}
		
		private function convertHeadersToAssocArray(HttpHeaderCollection $headers)
		{
			$result = array();
			
			foreach($headers->getItems() as $item)
				$result[$item->getName()] = $item->getValue();
			
			return $result;
		}
		
		private function mergeAssocArray($key_seperator, $row_seperator, $array)
		{
			// Initialize empty array
			$key_seperated_array = array();
			
			// Loop-through and merge keys and values with delimiter
			foreach($array as $key => $value)
				$key_seperated_array[] = $key . $key_seperator . $value; 
			
			// Imploded array with row seperator, then return
			return implode($row_seperator, $key_seperated_array); 
		}
		
		private function setDefaultHeaders(HttpHeaderCollection $headers)
		{
			$headers->set(new HttpHeaderItem("Connection", "close"));
		}
		
		public function __toString()
		{
			return $this->compile();
		}
	}
	
?>