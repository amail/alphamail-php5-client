<?php

    /*
    The MIT License

    Copyright (c) Robin Orheden, 2013 <http://amail.io/>
    
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
    
	class HttpSocketConnectionException extends Exception {}
	
    /**
     * Represents a socket used for client-server communication
     *
     * @category    Communication
     * @author      Comfirm Development Team <techsupport@comfirm.se>
     */
	interface IHttpSocket
	{
		/**
		 * Opens the socket
		 */
		function open($host = null, $port = null);
		
		/**
		 * Closes the socket
		 */
		function close();
		
		/**
		 * Reads data from the socket
		 *
		 * @return  string
		 */
		function read();
		
		/**
		 * Writes data to the socket
		 */
		function write($data);
		
		/**
		 * Returns a status of whether this socket is connected or not
		 */
		function isConnected();

		/**
		 * Gets the host this socket is bound to
		 */
		function getHost();
		
		/**
		 * Sets the host this socket is bound to
		 */
		function setHost($host);
		
		/**
		 * Gets the port this socket is bound to
		 */
		function getPort();
		
		/**
		 * Sets the port this socket is bound to
		 */
		function setPort($port);
	}
	
    /**
     * TCP socket specifically used for HTTP communication
     *
     * @category    Communication
     * @author      Comfirm Development Team <techsupport@comfirm.se>
     */
	class HttpSocket implements IHttpSocket
	{
		const BUFFER_READ_SIZE = 4096; // 4kb
		const REGULAR_PORT = 80;
		const SSL_PORT = 443;
		
		private $_stream = null;
		private $_connected = false;
		private $_host = null, $_port = null;		
		
		public function __construct($host, $port)
		{
			$this->setHost($host);
			$this->setPort($port);
		}
		
		public function getHost()
		{
			return $this->_host;
		}
		
		public function setHost($host)
		{
			$this->_host = $host;
		}
		
		public function getPort()
		{
			return $this->_port;
		}
		
		public function setPort($port)
		{
			if($port == null) $port = self::REGULAR_PORT;
			$this->_port = $port;
		}
		
		public function open($host = null, $port = null)
		{
			$result = false;
			
			if($host == null)
				$host = $this->getHost();
			
			if($port == null)
				$port = $this->getPort();
			
			// Check that OpenSSL is installed
			if($port == self::SSL_PORT && !function_exists("openssl_sign")){
				throw new HttpSocketConnectionException(
				    sprintf("Unable to securely connect to host '%s:%s'. OpenSSL is not installed.", $host, $port));
			}
            
			// Try to establish a connection
			if(($stream = @fsockopen((($port == self::SSL_PORT) ? "ssl://" : null) . $this->getHost(),
				$port, $error_num, $error_str, 5)))
			{
				$result = true;
				$this->_stream = $stream;
			}
			else
			{
				throw new HttpSocketConnectionException(
				    sprintf("Unable to connect to host '%s:%s'", $host, $port));
			}
			
			return ($this->_connected = $result);
		}
		
		public function close()
		{
			@fclose($this->_stream);
		}
		
		public function read()
		{
			$result = false;
				
			// Return false if no connection established
			if($this->isConnected())
			{
				$result = null;
				while(!feof($this->_stream)){
					$result .= @fgets($this->_stream, self::BUFFER_READ_SIZE);
				}
			}else{
				throw new HttpSocketConnectionException(
				    sprintf("Unable to connect to host '%s:%s'", $host, $port));
			}
	
			return $result;
		}
		
		public function write($data)
		{
			$result = false;
			
			if($this->isConnected()){
				$result = @fwrite($this->_stream, $data) === false ? false : true;
			}else{
				throw new HttpSocketConnectionException(
				    sprintf("Unable to connect to host '%s:%s'", $host, $port));
			}
				
			return $result;
		}
		
		public function writeAndRead($data)
		{
			$result = false;
			
			if($this->write($data))
				$result = $this->read();
			
			return $result;
		}
		
		public function isConnected()
		{
			return $this->_stream != null && $this->_connected &&
				stream_get_meta_data($this->_stream) != null;
		}
	}
	
?>