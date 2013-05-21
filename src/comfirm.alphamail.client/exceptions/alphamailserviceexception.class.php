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

    class AlphaMailServiceException extends Exception
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