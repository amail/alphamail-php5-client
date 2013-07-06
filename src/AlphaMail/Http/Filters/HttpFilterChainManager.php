<?php

    namespace AlphaMail\Http\Filters;

    class HttpFilterChainManager
    {
        private $_base_item = null;
        private $_current_item = null;
        
        public function __construct()
        {
            $this->_items = array();
        }
    
        public function add(IHttpFilterChainItem $item)
        {
            if($this->_base_item == null){
                $this->_base_item = $item;
            }

            if($this->_current_item != null)
                $this->_current_item->setChild($item);
            
            return ($this->_current_item = $item);
        }
        
        public function execute($source)
        {
            return ($this->_base_item == null ? $source : $this->_base_item->apply($source));
        }
    }

?>