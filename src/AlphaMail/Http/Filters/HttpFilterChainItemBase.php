<?php

    namespace AlphaMail\Http\Filters;

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