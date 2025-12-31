<?php
// Error handling and logging configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Custom error handler
function handleError($errno, $errstr, $errfile, $errline) {
    $error_message = date('Y-m-d H:i:s') . " - Error: [$errno] $errstr in $errfile on line $errline\n";
    
    // Log to file
    error_log($error_message, 3, __DIR__ . '/logs/errors.log');
    
    // In development, show errors
    if ($_SERVER['ENVIRONMENT'] !== 'production') {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>";
        echo "<strong>Error:</strong> $errstr in $errfile on line $errline";
        echo "</div>";
    }
    
    return true;
}

// Custom exception handler
function handleException($exception) {
    $error_message = date('Y-m-d H:i:s') . " - Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    
    // Log to file
    error_log($error_message, 3, __DIR__ . '/logs/errors.log');
    
    // In development, show exceptions
    if ($_SERVER['ENVIRONMENT'] !== 'production') {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>";
        echo "<strong>Exception:</strong> " . $exception->getMessage();
        echo " in " . $exception->getFile() . " on line " . $exception->getLine();
        echo "</div>";
    }
}

// Set error handlers
set_error_handler('handleError');
set_exception_handler('handleException');

// Create logs directory if it doesn't exist
if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Log function for debugging
function logMessage($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$level] $message\n";
    error_log($log_message, 3, __DIR__ . '/logs/app.log');
}
?>