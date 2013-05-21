<?php

    /*
    The MIT License

    Copyright (c) 2011 Comfirm <http://www.comfirm.se/>

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
    */

	class HttpPacketCompileException extends Exception {}

	interface IHttpPacketCompiler
	{
		function compile();
		
		function getContentType();
		function setContentType($value);
		
		function getProtocolVersion();
		function setProtocolVersion($value);
		
		function getUrl();
		function setUrl($value);
		
		function getMethod();
		function setMethod($value);
		
		function getHeaders();
		function setHeaders(IHttpHeaderCollection $value);
		
		function getBody();
		function setBody($value);
	}

	class HttpPacketCompiler implements IHttpPacketCompiler
	{
		const HTTP_SEPERATOR = "\r\n";
		const HTTP_DOUBLE_SEPERATOR = "\r\n\r\n";
		
		protected $_protocol_version, $_method = null, $_url = null;
		protected $_headers = null, $_body = null;
		
		public function __construct($method = HttpMethodType::GET, $url, IHttpHeaderCollection $headers = null, $body = null)
		{
			$this->setProtocolVersion(HttpProtocolVersionType::HTTP_1_1);
			$this->setHeaders($headers == null ? new HttpHeaderCollection() : $headers);
			$this->setMethod($method);
			$this->setBody($body);
			$this->setUrl($url);
		}
		
		public function setMethod($method)
		{
			$headers = $this->getHeaders();
			
			if($method == null)
				$method = HttpMethodType::GET;
			
			switch($method)
			{
				case HttpMethodType::GET:
					break;
					
				case HttpMethodType::PUT:
					break;
					
				case HttpMethodType::POST:
					if(!$headers->contains("Content-Type"))
						$this->setContentType('application/x-www-form-urlencoded');
					break;
				
				case HttpMethodType::DELETE:
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
			if(is_array($value) && $this->getMethod() == HttpMethodType::POST)
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
			$this->_protocol_version = ($value == HttpProtocolVersionType::HTTP_1_0
				? $value : HttpProtocolVersionType::HTTP_1_1);
		}
		
		public function getHeaders()
		{
			return $this->_headers;
		}
		
		public function setHeaders(IHttpHeaderCollection $value)
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
		
		private function convertHeadersToAssocArray(IHttpHeaderCollection $headers)
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
		
		private function setDefaultHeaders(IHttpHeaderCollection $headers)
		{
			$headers->set(new HttpHeaderItem("Connection", "close"));
		}
		
		public function __toString()
		{
			return $this->compile();
		}
	}
	
?>