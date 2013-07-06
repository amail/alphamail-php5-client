<?php

    namespace AlphaMail\Http;

    /**
     * Represents a socket used for client-server communication
     *
     * @category    Communication
     * @author      Comfirm Development Team <techsupport@comfirm.se>
     */
    interface IHttpSocket
    {
        /**
         * Opens the socket
         */
        function open($host = null, $port = null);
        
        /**
         * Closes the socket
         */
        function close();
        
        /**
         * Reads data from the socket
         *
         * @return  string
         */
        function read();
        
        /**
         * Writes data to the socket
         */
        function write($data);
        
        /**
         * Returns a status of whether this socket is connected or not
         */
        function isConnected();

        /**
         * Gets the host this socket is bound to
         */
        function getHost();
        
        /**
         * Sets the host this socket is bound to
         */
        function setHost($host);
        
        /**
         * Gets the port this socket is bound to
         */
        function getPort();
        
        /**
         * Sets the port this socket is bound to
         */
        function setPort($port);
    }

?>