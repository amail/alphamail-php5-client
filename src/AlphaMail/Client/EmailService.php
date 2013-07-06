<?php

    namespace AlphaMail\Client;

    use AlphaMail\Rest\Restful;

    use AlphaMail\Client\Entities\ServiceResponse;
    use AlphaMail\Client\Entities\EmailContact;
    use AlphaMail\Client\Entities\EmailMessagePayload;

    use AlphaMail\Client\Exceptions\ServiceException;
    use AlphaMail\Client\Exceptions\AuthorizationException;
    use AlphaMail\Client\Exceptions\InternalException;
    use AlphaMail\Client\Exceptions\ValidationException;

    class EmailService
    {
        private $_client = null;
        private $_service_url = null, $_api_token;
        
        public function __construct($token = null)
        {
            $this->_client = new Restful();
            $this->setServiceUrl("http://api.amail.io/v2/");
            if($token != null){
                $this->setApiToken($token);
            }
        }

        public static function create(){
            return new EmailService();
        }
        
        public function setServiceUrl($service_url)
        {
            $this->_service_url = rtrim($service_url, "/");
            return $this;
        }
        
        public function setApiToken($api_token)
        {
            $this->_api_token = $api_token;
            $this->_client->setBasicAuthentication(null, $api_token);
            return $this;
        }
        
        public function queue(EmailMessagePayload $payload)
        {
            $response = null;
            
            try
            {
                $response = $this->_client->post($this->_service_url . "/email/queue", json_encode($payload));
                $response->result = $this->cast($response->result, "AlphaMail\Client\Entities\ServiceResponse");
                $this->handleErrors($response);
            }
            catch(ServiceException $exception)
            {
                throw $exception;
            }
            catch(Exception $exception)
            {
                throw new ServiceException($exception->getMessage(), null, null, $exception);
            }
            
            return $response->result;
        }
        
        private function handleErrors($response)
        {
            switch ($response->head->status->code)
            {
                // Successful requests
                case 202: // Accepted:
                case 201: // Created:
                case 200: // OK:
                    if ($response->result->error_code != 0){
                        throw new InternalException(
                            sprintf("Service returned success while response error code was set (%d)", $response->result->error_code),
                            $response->head->status,
                            $response->result,
                            null
                        );
                    }
                    break;

                // Unauthorized
                case 403: // Forbidden:
                case 401: // Unauthorized:
                    throw new AuthorizationException(
                        $response->result->message,
                        $response->head->status,
                        $response->result,
                        null
                    );

                // Validation error
                case 405: // MethodNotAllowed:
                case 400: // BadRequest:
                    throw new ValidationException(
                        $response->result->message,
                        $response->head->status,
                        $response->result,
                        null
                    );

                // Internal error
                case 500: // InternalServerError
                    throw new InternalException(
                        $response->result->message,
                        $response->head->status,
                        $response->result,
                        null
                    );

                // Unknown
                default:
                    throw new ServiceException(
                        $response->result->message,
                        $response->head->status,
                        $response->result,
                        null
                    );
            }

            return $response;
        }
        
        // Object-casting-hack :)
        private function cast($source, $destination)
        {
            $result = null;
            
            if(@class_exists($destination))
            {
                $serialized = @serialize($source);
                
                $result = @unserialize('O:' . strlen($destination) . ':"' . $destination . '":' .
                    substr($serialized, $serialized[2] + 7));
                
                if($result === false)
                    throw new Exception("Unable to cast object to type '" . $destination . "'");
            }
            else
            {
                return false;
            }
            
            return $result;
        }
    }
    
?>