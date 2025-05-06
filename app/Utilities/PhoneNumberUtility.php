<?php

namespace App\Utilities;

class PhoneNumberUtility
{
    public static function formatForSms($phoneNumber)
    {
        // Remove all non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If starts with 0, replace with +256
        if (strlen($cleaned) > 9 && $cleaned[0] === '0') {
            return '256' . substr($cleaned, 1);
        }
        
        // If starts with 256 without +, add +
        if (strlen($cleaned) > 9 && substr($cleaned, 0, 3) === '256') {
            return '+' . $cleaned;
        }
        
        // If already in international format, return as is
        if (strlen($cleaned) > 9 && $cleaned[0] === '+') {
            return $cleaned;
        }
        
        // Default return (shouldn't happen for valid UG numbers)
        return $phoneNumber;
    }
}