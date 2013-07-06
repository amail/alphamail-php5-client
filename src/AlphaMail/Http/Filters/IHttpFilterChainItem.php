<?php

    namespace AlphaMail\Http\Filters;

    interface IHttpFilterChainItem
    {
        function setChild(IHttpFilterChainItem $item);
        function apply($source);
    }

?>