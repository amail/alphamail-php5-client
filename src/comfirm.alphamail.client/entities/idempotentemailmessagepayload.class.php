<?php

    /*
    The MIT License

    Copyright (c) Robin Orheden, 2013 <http://amail.io/>
    
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
	
	class IdempotentEmailMessagePayload
	{
        /**
		 *  Identifies this message operation. Remember that a message queued with this ID can only be sent once.
		 */
	    public $operation_id = "";
	    
		public $project_id = 0, $receiver_id = 0;
	
		public $receiver_email = "", $receiver_name = "";
		public $sender_email = "", $sender_name = "";
		
		public $body = "";
		
		protected function __construct(){}
		
        public static function create()
        {
            return new EmailMessagePayload();
        }
        
        public function setOperationId($operation_id)
        {
            $this->operation_id = $operation_id;
            return $this;
        }
        
        public function setProjectId($id)
        {
            $this->project_id = $id;
            return $this;
        }
        
        public function setSender(EmailContact $contact)
        {
            $this->sender_name = $contact->name;
            $this->sender_email = $contact->email;
            return $this;
        }
        
        public function setReceiver(EmailContact $contact)
        {
            $this->receiver_name = $contact->name;
            $this->receiver_email = $contact->email;
            return $this;
        }
        
        public function setReceiverId($id)
        {
            $this->receiver_id = $id;
            return $this;
        }
        
        public function setBodyObject($body)
        {
            $this->body = json_encode($body);
            return $this;
        }
	}

?>