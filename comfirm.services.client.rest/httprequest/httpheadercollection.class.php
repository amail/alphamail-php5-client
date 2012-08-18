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

	interface IHttpHeaderItem
	{
		function getName();
		function setName($value);
		
		function getValue();
		function setValue($value);
	}

	interface IHttpHeaderCollection
	{
		// Retrieves a list of all added HttpHeaderItem's
		function getItems();
	
		// Retrieves a specific header by key
		function get($key);
		
		// Adds a HttpHeaderItem to the collection
		function add(IHttpHeaderItem $item);
		
		function remove($key);
		
		// Checks whether a specific key exist
		function contains($key);
		
		// Forces a specific key to be set
		function set(IHttpHeaderItem $item);
	}
	
	class HttpHeaderCollection implements IHttpHeaderCollection
	{
		private $_items = null;
	
		public function __construct()
		{
			$this->_items = array();
		}
		
		public function getItems()
		{
			return $this->_items;
		}
		
		public function get($key)
		{
			return $this->_items[strtolower($key)];
		}
		
		public function add(IHttpHeaderItem $item)
		{
			if($this->contains($item->getName()))
				throw new Exception("Header with name '" . $item->getName() . "' already exist");
			else
				$this->set($item);
		}
		
		public function contains($key)
		{
			return array_key_exists(strtolower($key), $this->_items);
		}
		
		public function set(IHttpHeaderItem $item)
		{
			$this->_items[strtolower($item->getName())] = $item;
		}
		
		public function remove($key)
		{
			unset($this->_items[strtolower($key)]);
		}
	}
	
	class HttpHeaderItem implements IHttpHeaderItem
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