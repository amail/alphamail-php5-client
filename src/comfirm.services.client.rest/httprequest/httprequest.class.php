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

	include_once("httptypes.class.php");
	include_once("httpheadercollection.class.php");
	include_once("httppacketcompiler.class.php");
	include_once("httppacketparser.class.php");
	include_once("httpsocket.class.php");
	include_once("httpfilter.class.php");

	// Include core filters
    include_once("filters/httpgzipfilterchainitem.class.php");
    include_once("filters/httpchunkfilterchainitem.class.php");
    include_once("filters/basicauthenticationfilterchainitem.class.php");
    include_once("filters/jsondeserializerchainitem.class.php");

	interface IHttpRequest
	{
        function addInboundFilter(IHttpInboundFilterChainItem $item);
	    function addOutboundFilter(IHttpOutboundFilterChainItem $item);
		function create($method, $url, IHttpHeaderCollection $headers, $body);
	}

	class HttpRequest
	{
		private $_parser = null;
	    private $_inbound_filter_manager = null, $_outbound_filter_manager = null;

		public function __construct()
		{
			$this->_parser = new HttpPacketParser();
    		$this->_outbound_filter_manager = new HttpFilterChainManager();
			$inbound_manager = new HttpFilterChainManager();
			
			// Add default inbound filters
			$inbound_manager->add(new HttpGzipFilterChainItem());
			$inbound_manager->add(new HttpChunkFilterChainItem());
			
			$this->_inbound_filter_manager = $inbound_manager;
		}
		
		function addOutboundFilter(IHttpOutboundFilterChainItem $item)
		{
    		$this->_outbound_filter_manager->add($item);
		}
		
		function addInboundFilter(IHttpInboundFilterChainItem $item)
		{
    		$this->_inbound_filter_manager->add($item);
		}

		public function create($method, $url, IHttpHeaderCollection $headers = null, $body = null)
		{
			$result = null;
			$parsed_url = $this->parseUrl($url);
			$socket = new HttpSocket($parsed_url['host'], $this->resolvePort($parsed_url));

			try
			{
				$socket->open();
				
				if($socket->isConnected())
				{
					$compiler = $this->_outbound_filter_manager->execute(
					    new HttpPacketCompiler($method, $url, $headers, $body));

					$result = $socket->writeAndRead($compiler->compile());

                    $socket->close();
				}
    		}
			catch(Exception $ex)
			{
				$socket->close();
				throw $ex;
			}

			return $this->_inbound_filter_manager->execute(
			    $this->_parser->parse($result));
		}

		private function parseUrl($url)
		{
			return parse_url((strpos($url, "://") === false ? "http://{$url}" : $url));
		}

		private function resolvePort($url)
		{
			return (@$url['port'] == null ? (@$url['scheme'] == 'https' ?
			    HttpSocket::SSL_PORT : HttpSocket::REGULAR_PORT) : $url['port']);
		}
	}

?>