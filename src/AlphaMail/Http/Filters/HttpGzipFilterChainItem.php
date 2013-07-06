<?php

    namespace AlphaMail\Http\Filters;

    use AlphaMail\Http\Filters\Exceptions\HttpGzipException;

    class HttpGzipFilterChainItem extends HttpFilterChainItemBase implements IHttpInboundFilterChainItem
    {
        public function apply($source)
        {
            // Check whether this package is GZIP-encoded
            if(strlen($source->result) > 0 && @$source->head->headers['content-encoding'])
            {
                // Try to decompress gzip deflated package, throw exception on faliure
                if(($uncompressed = @gzinflate(substr($source->result, 10))) === false){
                    throw new HttpGzipException("Could not decompress GZIP-encoded package.");
                }

                $source->result = $uncompressed;
            }

            // Break chain and return data, or pass onto next.
            return parent::apply($source);
        }
    }
    
?>