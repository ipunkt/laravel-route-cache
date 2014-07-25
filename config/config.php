<?php
return [
    /**
     * How long shloud the ETag and Content be cached
     * 0 - means forever
     * any integer is in minutes
     */
    'timeout' => 0,
    
    /**
     * if set to false, there would be no checks for the etag
     */
    'send-not-modified-status' => true,
    
    /**
     * if set to false, the content itself would not be cached
     */
    'cache-static-content' => true,
    
    /**
     * You can add this string as GET-parameter to your URL to remove the cache for that URL
     */
    'cachebuster' => 'renew-cache',
    
    /**
     * set to false to remove the Header with the info for cached or controller content
     * change to any string to set the header-key
     */
    'infoheader' => 'X-Served-From',
];
