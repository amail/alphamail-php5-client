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

	class HttpPacketParserException extends Exception {}

	interface IHttpPacketParser
	{
		function parse($raw_packet);
	}

	class HttpPacketParser implements IHttpPacketParser
	{
		const HTTP_SEPERATOR = "\r\n";
		const HTTP_ALTERNATIVE_SEPERATOR = "\n";
		
		private $_seperator = self::HTTP_SEPERATOR;
		
		public function __construct() {}
		
		public function parse($raw_packet)
		{
			// Init vars
			$package = (object)null;
			
			// Seperate header from body, and append parsed data to object
			$content = $this->separateHeaderFromBody($raw_packet);
			$package->head = $this->parseHeader($content['header']);
			$package->result = $content['body'];
			
			// Return parsed
			return $package;
		}
		
		private function separateHeaderFromBody($raw_packet)
		{
			// Identify seperator. Some servers actually use \n instead of the RFC compliant \r\n.
			$this->_separator = strpos($raw_packet, $this->_seperator) === false
				? self::HTTP_ALTERNATIVE_SEPERATOR : self::HTTP_SEPERATOR;
			
			// Seperate and combine array with assoc keys
			return @array_combine(array("header", "body"),
				explode(str_repeat($this->_separator, 2), $raw_packet, 2));
		}
		
		private function parseHeader($raw_header)
		{
			$result = (object)null;
			$parsed_header = array();
			$raw_header = explode($this->_separator, $raw_header);
			
			// Extract response status from header and perform minor error handling
			if(($status_header = @array_combine(array("protocol", "code", "message"), explode(" ", $raw_header[0], 3))) === false)
				throw new HttpPacketParserException('Unable to parse header. Header response status incorrect or not well formated.');
			
			// Remove status head from header flow
			unset($raw_header[0]);
			
			// Loop-through and parse header vars
			foreach($raw_header as $header)
			{
				// Validate header
				if(strpos($header, ": ") === false)
				{
					throw new HttpPacketParserException('Unable to parse header variable. Variable not correctly formatted.');
				}
				else
				{
					// Extract key and value
					list($key, $value) = explode(": ", $header, 2);
					
					// Convert key to lowercase
					$key = strtolower($key);
					
					// Check whether we should set or append data to key(append data if key is already set).
					@$parsed_header[$key] = (($parsed_header[$key] == null) ? $value : array_merge((array) $parsed_header[$key], (array) $value));
				}
			}

			// Append status and headers to result
			$result->status = (object)null;
            $result->status->code = $status_header["code"];
			$result->status->version = $status_header["protocol"];
            $result->status->message = $status_header["message"];
			$result->headers = $parsed_header;
			
			return $result;
		}
		
		public function __toString()
		{
			return $this->Parse();
		}
	}
	
?>