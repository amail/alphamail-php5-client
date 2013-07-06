<?php

    namespace AlphaMail\Client\Exceptions;

    use \Exception;

    class ServiceException extends Exception
    {
        public $http_status;
        public $response;
        public $inner_exception;
        
        public function __construct($message, $http_status = null, $response = null, $inner_exception = null)
        {
            parent::__construct($message, $http_status != null ? $http_status->code : null);
            $this->response = $response;
            $this->http_status = $http_status;
            $this->inner_exception = $inner_exception;
        }

        public function getErrorCode(){
            return $this->response == null ? null : $this->response->error_code;
        }
    }

?>