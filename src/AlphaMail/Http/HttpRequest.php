<?php

    namespace AlphaMail\Http;

    use AlphaMail\Http\Types\HttpMethod;
    use AlphaMail\Http\Types\HttpProtocol;

    use AlphaMail\Http\Entities\HttpHeaderCollection;

    use AlphaMail\Http\Filters\HttpFilterChainManager;
    use AlphaMail\Http\Filters\IHttpOutboundFilterChainItem;
    use AlphaMail\Http\Filters\IHttpInboundFilterChainItem;
    use AlphaMail\Http\Filters\HttpGzipFilterChainItem;
    use AlphaMail\Http\Filters\HttpChunkFilterChainItem;

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

		public function create($method, $url, HttpHeaderCollection $headers = null, $body = null)
		{
			$result = null;
			$parsed_url = $this->parseUrl($url);
			$socket = new RegularHttpSocket($parsed_url['host'], $this->resolvePort($parsed_url));

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
			    RegularHttpSocket::SSL_PORT : RegularHttpSocket::REGULAR_PORT) : $url['port']);
		}
	}

?>