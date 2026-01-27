<?php
/**
 * Mobile Money Configuration for Malawi
 * Airtel Money and TNM Mpamba integration settings
 * 
 * IMPORTANT: Replace with actual API credentials from providers
 */

// Airtel Money API Configuration
define('AIRTEL_API_URL', 'https://openapiuat.airtel.africa'); // Use production URL in live
define('AIRTEL_CLIENT_ID', 'YOUR_AIRTEL_CLIENT_ID');
define('AIRTEL_CLIENT_SECRET', 'YOUR_AIRTEL_CLIENT_SECRET');
define('AIRTEL_COUNTRY', 'MW'); // Malawi
define('AIRTEL_CURRENCY', 'MWK');

// TNM Mpamba API Configuration
define('TNM_API_URL', 'https://api.tnm.co.mw'); // TNM API endpoint
define('TNM_API_KEY', 'YOUR_TNM_API_KEY');
define('TNM_API_SECRET', 'YOUR_TNM_API_SECRET');
define('TNM_MERCHANT_ID', 'YOUR_TNM_MERCHANT_ID');

// Payment callback URL (your server endpoint for payment notifications)
define('PAYMENT_CALLBACK_URL', 'https://yourdomain.com/api/mobile-payment/callback');

// Supported banks for bank transfers
define('SUPPORTED_BANKS', json_encode([
    ['code' => 'NBM', 'name' => 'National Bank of Malawi', 'swift' => 'ABORWMWX'],
    ['code' => 'STD', 'name' => 'Standard Bank Malawi', 'swift' => 'SBICMWMX'],
    ['code' => 'FMB', 'name' => 'First Merchant Bank', 'swift' => 'FABORWMW'],
    ['code' => 'NBS', 'name' => 'NBS Bank', 'swift' => 'NBSKMWMX'],
    ['code' => 'CDH', 'name' => 'CDH Investment Bank', 'swift' => 'CDIBMWMX'],
    ['code' => 'FDH', 'name' => 'FDH Bank', 'swift' => 'FABORWMF'],
    ['code' => 'ECO', 'name' => 'Ecobank Malawi', 'swift' => 'ABORWMWE'],
    ['code' => 'MYB', 'name' => 'MyBucks Banking', 'swift' => 'MYBKMWMX'],
]));

// Business account details for bank transfers
define('BUSINESS_BANK_NAME', 'National Bank of Malawi');
define('BUSINESS_ACCOUNT_NAME', 'ARKRentals Ltd');
define('BUSINESS_ACCOUNT_NUMBER', '1234567890'); // Replace with actual account
define('BUSINESS_BRANCH', 'Lilongwe Main Branch');
