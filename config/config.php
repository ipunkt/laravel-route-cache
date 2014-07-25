<?php
/**
 * Created by PhpStorm.
 * User: bastian
 * Date: 25.07.14
 * Time: 09:39
 */

return [
    'timeout' => 0, // 0 - means forever
    'send-not-modified-status' => true,
    'cache-static-content' => true,
    'cachebuster' => 'renew-cache',
    'infoheader' => 'X-Served-From',
];