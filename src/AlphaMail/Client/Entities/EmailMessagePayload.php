<?php

    namespace AlphaMail\Client\Entities;
	
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