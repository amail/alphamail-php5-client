<?php

    namespace AlphaMail\Http\Filters;

    use AlphaMail\Http\Entities\HttpHeaderItem;

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