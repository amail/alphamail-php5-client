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

	include_once("httprequest/httprequest.class.php");

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
			return $this->_request->create(HttpMethodType::GET, $url, $this->getHeaderCollection(), null);
		}
		
		public function put($url, $body)
		{
			return $this->_request->create(HttpMethodType::PUT, $url, $this->getHeaderCollection(), $body);
		}
		
		public function post($url, $body)
		{
			$x = $this->_request->create(HttpMethodType::POST, $url, $this->getHeaderCollection(), $body);
			return $x;
		}
		
		public function delete($url)
		{
			return $this->_request->create(HttpMethodType::DELETE, $url, $this->getHeaderCollection(), null);
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