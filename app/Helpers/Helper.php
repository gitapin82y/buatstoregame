<?php

// app/Helpers/Helper.php
namespace App\Helpers;

class Helper
{
    /**
     * Generate a unique invoice number
     *
     * @param string $prefix
     * @param int $userId
     * @return string
     */
    public static function generateInvoiceNumber(string $prefix, int $userId): string
    {
        return $prefix . time() . $userId;
    }
    
    /**
     * Format currency
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    public static function formatCurrency(float $amount, string $currency = 'Rp'): string
    {
        return $currency . ' ' . number_format($amount, 0, ',', '.');
    }
    
    /**
     * Check if a string is a valid URL
     *
     * @param string $url
     * @return bool
     */
    public static function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Format phone number
     *
     * @param string $phone
     * @return string
     */
    public static function formatPhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ensure it starts with '62'
        if (substr($phone, 0, 2) !== '62') {
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            } else {
                $phone = '62' . $phone;
            }
        }
        
        return $phone;
    }
    
    /**
     * Truncate text
     *
     * @param string $text
     * @param int $length
     * @param string $append
     * @return string
     */
    public static function truncate(string $text, int $length = 100, string $append = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $append;
    }
    
    /**
     * Get user-friendly date difference
     *
     * @param string $date
     * @return string
     */
    public static function dateDiff(string $date): string
    {
        $now = now();
        $date = \Carbon\Carbon::parse($date);
        
        $diffInDays = $date->diffInDays($now);
        
        if ($diffInDays === 0) {
            if ($date->isPast()) {
                $diffInHours = $date->diffInHours($now);
                
                if ($diffInHours === 0) {
                    $diffInMinutes = $date->diffInMinutes($now);
                    return $diffInMinutes . ' menit yang lalu';
                }
                
                return $diffInHours . ' jam yang lalu';
            } else {
                $diffInHours = $now->diffInHours($date);
                
                if ($diffInHours === 0) {
                    $diffInMinutes = $now->diffInMinutes($date);
                    return $diffInMinutes . ' menit lagi';
                }
                
                return $diffInHours . ' jam lagi';
            }
        } elseif ($diffInDays < 7) {
            if ($date->isPast()) {
                return $diffInDays . ' hari yang lalu';
            } else {
                return $diffInDays . ' hari lagi';
            }
        }

        // If more than 7 days, show the date
        return $date->format('d M Y');
    }
}