<?php

    namespace AlphaMail\Http\Filters;

    use AlphaMail\Http\Filters\Exceptions\HttpChunkDecoderException;

    class HttpChunkFilterChainItem extends HttpFilterChainItemBase implements IHttpInboundFilterChainItem
    {
        const CHUNK_SEPARATOR = "\r\n";
        
        public function apply($source)
        {
            // Detect chunked package
            if(isset($source->result) && (@$source->head->headers['transfer-encoding'] == "chunked"))
            {
                // Init vars
                $data = $source->result;
                $data_buffer = null;
                $size = 1;
                
                // Loop-through data until all chunks are extracted
                while($size > 0)
                {
                    // Locate offset and perform minor error handling
                    if(($offset = strpos($data, self::CHUNK_SEPARATOR)) === false)
                        throw new HttpChunkDecoderException("Could not find a valid offset.");
                    
                    // Extract chunk size in hex, then convert to dec
                    $size = hexdec(substr($data, 0, $offset));
                    
                    // Extract, reduct and build data + buffer
                    $data_buffer .= substr($data, ($offset+2), $size);
                    $data = ltrim(substr($data, ($size+$offset+2)));
                }
                
                // Assign filtered data to source
                $source->result = $data_buffer;
            }
            
            // Break chain and return data, or pass onto next.
            return parent::apply($source);
        }
    }

?>