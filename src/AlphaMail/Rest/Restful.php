<?php

    namespace AlphaMail\Rest;

    use AlphaMail\Http\HttpRequest;

    use AlphaMail\Http\Types\HttpMethod;
    use AlphaMail\Http\Entities\HttpHeaderCollection;
    use AlphaMail\Http\Entities\HttpHeaderItem;

    use AlphaMail\Http\Filters\JsonDeserializerChainItem;
    use AlphaMail\Http\Filters\HttpBasicAuthenticationFilterChainItem;

	class Restful
	{
		private $_request = null;
		
		public function __construct()
		{
			$this->_request = new HttpRequest();
		    $this->_request->addInboundFilter(new JsonDeserializerChainItem());
		}
		
		public function setBasicAuthentication($username, $password)
		{
		    $this->_request->addOutboundFilter(new HttpBasicAuthenticationFilterChainItem($username, $password));
		}
		
		public function get($url)
		{
			return $this->createRequest(HttpMethod::GET, $url);
		}
		
		public function put($url, $body)
		{
			return $this->createRequest(HttpMethod::PUT, $url, $body);
		}
		
		public function post($url, $body)
		{
			return $this->createRequest(HttpMethod::POST, $url, $body);
		}
		
		public function delete($url)
		{
			return $this->createRequest(HttpMethod::DELETE, $url);
		}

		public function head($url)
		{
			return $this->createRequest(HttpMethod::HEAD, $url);
		}

		public function options($url)
		{
			return $this->createRequest(HttpMethod::OPTIONS, $url);
		}

		private function createRequest($method, $url, $body = null)
		{
			return $this->_request->create($method, $url, $this->getHeaderCollection(), $body);
		}
		
		private function getHeaderCollection()
		{
		    $headers = new HttpHeaderCollection();
            $headers->add(new HttpHeaderItem("Content-Type", "application/json"));
		    $headers->add(new HttpHeaderItem("Accept-Encoding", "gzip, deflate, chunked"));
		    return $headers;
		}
	}

?>