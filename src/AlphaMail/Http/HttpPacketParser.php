<?php

	namespace AlphaMail\Http;

	use AlphaMail\Http\Exceptions\HttpPacketParserException;

	class HttpPacketParser
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