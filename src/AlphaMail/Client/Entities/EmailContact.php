<?php

    namespace AlphaMail\Client\Entities;

	class EmailContact
	{
	    public $id, $email, $name;
	    
	    public function __construct($name, $email, $id = null)
	    {
            if($id != null){
                $this->id = $id;
            }
	        $this->email = $email;
	        $this->name = $name;
	    }
	}

?>