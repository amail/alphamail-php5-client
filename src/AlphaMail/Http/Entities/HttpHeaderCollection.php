<?php

	namespace AlphaMail\Http\Entities;

	use \Exception;
	
	class HttpHeaderCollection
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
		
		public function add(HttpHeaderItem $item)
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
		
		public function set(HttpHeaderItem $item)
		{
			$this->_items[strtolower($item->getName())] = $item;
		}
		
		public function remove($key)
		{
			unset($this->_items[strtolower($key)]);
		}
	}

?>