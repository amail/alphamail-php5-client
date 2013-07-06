<?php

    namespace AlphaMail\Http\Filters;

    use Alphamail\Http\Filters\Exceptions\JsonDeserializerException;
    
    class JsonDeserializerChainItem extends HttpFilterChainItemBase implements IHttpInboundFilterChainItem
    {        
        public function apply($source)
        {
            $segments = (array)explode(";", $source->head->headers["content-type"], 2);

            // Decode result if content type is set to JSON
            if($segments[0] == "application/json")
            {
                $source->result = json_decode($source->result);
            }
            
            // Break chain and return data, or pass onto next.
            return parent::apply($source);
        }
    }

?>