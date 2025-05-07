<?php

if (!function_exists('logMessage')) {
    function logMessage($message, $type = 'info')
    {
        switch ($type) {
            case 'success':
                $color = "\033[32m"; // Green
                break;
            case 'error':
                $color = "\033[31m"; // Red
                $stream = STDERR;
                break;
            case 'warning':
                $color = "\033[33m"; // Yellow
                break;
            default:
                $color = "\033[36m"; // Cyan
                break;
        }

        $stream = $stream ?? STDOUT;
        
        return fwrite($stream, $color . $message . "\033[0m\n");
    }
}