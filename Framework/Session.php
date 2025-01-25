<?php

namespace Framework;

class Session {
    /**
     * Start the session
     * 
     * @return void
     */
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a value in the session
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a value from the session
     * 
     * @param string $key
     * @return mixed
     */
    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Check if a key exists in the session
     * 
     * @param string $key
     * @return bool
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    /**
     * Clear a key from the session
     * 
     * @param string $key
     * @return void
     */
    public static function clear($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy the session
     * 
     * @return void
     */
    public static function destroy() {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }
}