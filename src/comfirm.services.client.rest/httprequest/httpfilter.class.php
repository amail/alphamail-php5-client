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

    interface IHttpFilterChainItem
    {
        function setChild(IHttpFilterChainItem $item);
        function apply($source);
    }

    interface IHttpInboundFilterChainItem extends IHttpFilterChainItem
    {
    }

    interface IHttpOutboundFilterChainItem extends IHttpFilterChainItem
    {
    }
    
    interface IHttpFilterChainManager
    {
        function add(IHttpFilterChainItem $item);
        function execute($source);
    }
    
    class HttpFilterChainManager implements IHttpFilterChainManager
    {
        private $_current_item = null;
        
        public function __construct()
        {
            $this->_items = array();
        }
    
        public function add(IHttpFilterChainItem $item)
        {
            if($this->_current_item != null)
                $item->setChild($this->_current_item);
            
            return ($this->_current_item = $item);
        }
        
        public function execute($source)
        {
            return ($this->_current_item == null ? $source : $this->_current_item->apply($source));
        }
    }

    abstract class HttpFilterChainItemBase implements IHttpFilterChainItem
    {
        private $_child_item = null;
        
        public function setChild(IHttpFilterChainItem $item)
        {
            $this->_child_item = $item;
        }
        
        public function apply($source)
        {
            // Break chain and return data, or pass onto next.
            return ($this->_child_item == null ? $source : $this->_child_item->apply($source));
        }
    }

?>