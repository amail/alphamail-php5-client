<?php

    class Integration {
        public $id;
        public $name;
        public $metadata;
        public $updated;
        public $created;
    }

    class ConnectIntegrationTokenRequest {
        public $token_id;
    }

    class CreateIntegrationRequest {
        public $name;
        public $metadata;
    }

?>