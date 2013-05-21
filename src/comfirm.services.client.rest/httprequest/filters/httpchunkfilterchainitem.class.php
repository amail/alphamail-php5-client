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

    class HttpChunkDecoderException extends Exception {}
    
    class HttpChunkFilterChainItem extends HttpFilterChainItemBase implements IHttpInboundFilterChainItem
    {
        const CHUNK_SEPARATOR = "\r\n";
        
        public function apply($source)
        {
            // Detect chunked package
            if((@$source->head->headers['transfer-encoding'] == "chunked"))
            {
                // Init vars
                $data = $source->content;
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