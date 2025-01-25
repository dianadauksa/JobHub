<?php 

namespace App\Controllers;

class ErrorController {

    /**
     * Display the 404 error page
     * 
     * @param string $message The error message to display
     * @return void
     */
    public static function notFound($message = 'Page not found'): void {
        http_response_code(404);
        loadView('error', [
            'status' => '404',
            'message' => $message
        ]);
    }

    /**
     * Display the 403 error page
     * 
     * @param string $message The error message to display
     * @return void
     */
    public static function forbidden($message = 'You are not authorized to access this page'): void {
        http_response_code(403);
        loadView('error', [
            'status' => '403',
            'message' => $message
        ]);
    }
}