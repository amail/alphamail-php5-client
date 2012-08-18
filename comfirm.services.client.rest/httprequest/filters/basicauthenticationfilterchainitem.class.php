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

    class HttpBasicAuthenticationFilterChainItem extends HttpFilterChainItemBase implements IHttpOutboundFilterChainItem
    {
        private $_username = null, $_password = null;

        public function __construct($username, $password)
        {
            $this->_username = $username;
            $this->_password = $password;
        }
        
        public function apply($source)
        { 
            // Apply authorization header
            $source->getHeaders()->set(new HttpHeaderItem("Authorization", "Basic " .
                base64_encode("{$this->_username}:{$this->_password}")));
            
            // Break chain and return data, or pass onto next.
            return parent::apply($source);
        }
    }

?>