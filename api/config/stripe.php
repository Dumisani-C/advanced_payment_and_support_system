<?php
/**
 * Stripe Configuration
 * Replace with your actual Stripe API keys
 * 
 * IMPORTANT: Never commit real API keys to version control
 * Use environment variables in production
 */

// Stripe API Keys - Replace these with your actual keys
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: 'sk_test_YOUR_SECRET_KEY'); // Set via environment variable
define('STRIPE_PUBLISHABLE_KEY', getenv('STRIPE_PUBLISHABLE_KEY') ?: 'pk_test_YOUR_PUBLISHABLE_KEY'); // Set via environment variable

// Webhook secret for verifying webhook signatures
define('STRIPE_WEBHOOK_SECRET', 'whsec_XXXXXXXXXXXXXXXXXXXXXXXX'); // Replace with your webhook secret

// Currency setting (MWK for Malawi Kwacha, or use USD for testing)
define('STRIPE_CURRENCY', 'usd'); // Note: Stripe may not support MWK directly, use USD and convert

// Application name for Stripe dashboard
define('STRIPE_APP_NAME', 'ARKRentals');
