<?php

    namespace AlphaMail\Client\Entities;

	class ServiceResponse
	{
		public $error_code, $message, $result;

		public function __construct($error_code, $message, $result)
		{
			$this->error_code = $error_code;
			$this->message = $message;
			$this->result = $result;
		}
	}
	
?>