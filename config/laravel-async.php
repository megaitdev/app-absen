<?php

return [
    /*
     * The default value which is the path to the PHP binary should work for CLI usage.
     * However, If you want to use it in web, You should set the path to the PHP binary because the default value will
     * be the path to the web server's PHP binary like php-fpm.
     */
    'php_path' => env('LARAVEL_ASYNC_PHP_PATH'),
    'default' => [
        'timeout' => 7200, // Set the timeout to 30 seconds (or whatever you prefer)
    ],
];
