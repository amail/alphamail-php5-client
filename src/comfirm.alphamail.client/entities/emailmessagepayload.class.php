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
	
	class EmailMessagePayload
	{
		public $project_id = 0;

        public $receiver;
        public $sender;
		
		public $payload = null;
		
		protected function __construct(){}
		
        public static function create()
        {
            return new EmailMessagePayload();
        }
        
        public function setProjectId($id)
        {
            $this->project_id = $id;
            return $this;
        }
        
        public function setSender(EmailContact $contact)
        {
            $this->sender = array(
                "email" => $contact->email,
                "name" => $contact->name
            );
            return $this;
        }
        
        public function setReceiver(EmailContact $contact)
        {
            $this->receiver = array(
                "id" => $contact->id,
                "email" => $contact->email,
                "name" => $contact->name
            );
            return $this;
        }
        
        public function setReceiverId($id)
        {
            $this->receiver_id = $id;
            return $this;
        }
        
        public function setBodyObject($body)
        {
            $this->payload = $body;
            return $this;
        }
	}

?>