<?php

namespace Framework;

class Validation {
    /**
     * Validate a string
     * 
     * @param string $value
     * @param int $min  
     * @param int $max
     * @return bool
     */
    public static function string($value, $min = 1, $max = INF): bool {
        if (is_string($value)) {
            $value = trim($value);

            return strlen($value) >= $min && strlen($value) <= $max;
        }

        return false;
    }

    /**
     * Validate an email
     * 
     * @param string $value
     * @return mixed
     */
    public static function email($value): bool {
        $value = trim($value);

        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Match a value agains another value
     * 
     * @param string $value1
     * @param string $value2
     * @return bool
     */
    public static function match($value1, $value2): bool {
        return trim($value1) === trim($value2);
    }
}