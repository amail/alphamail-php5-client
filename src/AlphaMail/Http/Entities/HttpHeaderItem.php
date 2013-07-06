<?php

    namespace AlphaMail\Http\Entities;

    class HttpHeaderItem
    {
        private $_name, $_value;
        
        public function __construct($name, $value)
        {
            $this->setName($name);
            $this->setValue($value);
        }
        
        public function getName()
        {
            return $this->_name;
        }
        
        public function setName($value)
        {
            $this->_name = $value;
        }
        
        public function getValue()
        {
            return $this->_value;
        }
        
        public function setValue($value)
        {
            $this->_value = $value;
        }
    }

?>