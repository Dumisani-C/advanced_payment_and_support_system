<?php
/**
 * Stripe Payment Handler
 * Handles secure payment processing via Stripe
 */

require_once __DIR__ . '/../config/stripe.php';

class StripePayment {
    private $conn;
    private $table = 'payments';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create a Stripe Payment Intent
     * This is the first step in processing a payment
     */
    public function createPaymentIntent($amount, $tenant_id, $apartment_no, $description = 'Rent Payment') {
        // Convert amount to cents (Stripe requires amounts in smallest currency unit)
        $amountInCents = (int)($amount * 100);
        
        // Initialize cURL for Stripe API call
        $ch = curl_init();
        
        $postData = http_build_query([
            'amount' => $amountInCents,
            'currency' => STRIPE_CURRENCY,
            'automatic_payment_methods[enabled]' => 'true',
            'metadata[tenant_id]' => $tenant_id,
            'metadata[apartment_no]' => $apartment_no,
            'metadata[app]' => STRIPE_APP_NAME,
            'description' => $description . ' - Unit #' . $apartment_no
        ]);
        
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.stripe.com/v1/payment_intents',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . STRIPE_SECRET_KEY,
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if ($httpCode !== 200) {
            throw new Exception($result['error']['message'] ?? 'Failed to create payment intent');
        }
        
        // Store pending payment in database
        $this->storePendingPayment($tenant_id, $apartment_no, $amount, $result['id']);
        
        return [
            'clientSecret' => $result['client_secret'],
            'paymentIntentId' => $result['id'],
            'amount' => $amount,
            'currency' => STRIPE_CURRENCY
        ];
    }
    
    /**
     * Store pending payment record
     */
    private function storePendingPayment($tenant_id, $apartment_no, $amount, $stripe_payment_id) {
        $query = "INSERT INTO " . $this->table . " 
                  (tenant_id, apartment_no, amount, invoice, payment_status, stripe_payment_id, date_created) 
                  VALUES (:tenant_id, :apartment_no, :amount, :invoice, 'pending', :stripe_payment_id, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        $invoice = 'INV-' . time() . '-' . rand(1000, 9999);
        
        $stmt->bindParam(':tenant_id', $tenant_id);
        $stmt->bindParam(':apartment_no', $apartment_no);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':invoice', $invoice);
        $stmt->bindParam(':stripe_payment_id', $stripe_payment_id);
        
        return $stmt->execute();
    }
    
    /**
     * Confirm payment was successful
     */
    public function confirmPayment($paymentIntentId) {
        // Retrieve payment intent from Stripe
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.stripe.com/v1/payment_intents/' . $paymentIntentId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . STRIPE_SECRET_KEY
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if ($httpCode !== 200) {
            throw new Exception('Failed to verify payment');
        }
        
        if ($result['status'] === 'succeeded') {
            // Update payment status in database
            $this->updatePaymentStatus($paymentIntentId, 'completed', $result['charges']['data'][0]['receipt_url'] ?? null);
            return [
                'success' => true,
                'status' => 'completed',
                'receiptUrl' => $result['charges']['data'][0]['receipt_url'] ?? null,
                'amount' => $result['amount'] / 100
            ];
        }
        
        return [
            'success' => false,
            'status' => $result['status']
        ];
    }
    
    /**
     * Update payment status in database
     */
    private function updatePaymentStatus($stripe_payment_id, $status, $receipt_url = null) {
        $query = "UPDATE " . $this->table . " 
                  SET payment_status = :status, receipt_url = :receipt_url 
                  WHERE stripe_payment_id = :stripe_payment_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':receipt_url', $receipt_url);
        $stmt->bindParam(':stripe_payment_id', $stripe_payment_id);
        
        return $stmt->execute();
    }
    
    /**
     * Get Stripe publishable key for frontend
     */
    public function getPublishableKey() {
        return STRIPE_PUBLISHABLE_KEY;
    }
    
    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook($payload, $sigHeader) {
        // Verify webhook signature
        $timestamp = null;
        $signatures = [];
        
        foreach (explode(',', $sigHeader) as $part) {
            $pair = explode('=', $part, 2);
            if ($pair[0] === 't') {
                $timestamp = $pair[1];
            } elseif ($pair[0] === 'v1') {
                $signatures[] = $pair[1];
            }
        }
        
        $signedPayload = $timestamp . '.' . $payload;
        $expectedSignature = hash_hmac('sha256', $signedPayload, STRIPE_WEBHOOK_SECRET);
        
        if (!in_array($expectedSignature, $signatures)) {
            throw new Exception('Invalid webhook signature');
        }
        
        $event = json_decode($payload, true);
        
        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event['data']['object'];
                $this->updatePaymentStatus($paymentIntent['id'], 'completed');
                break;
                
            case 'payment_intent.payment_failed':
                $paymentIntent = $event['data']['object'];
                $this->updatePaymentStatus($paymentIntent['id'], 'failed');
                break;
        }
        
        return true;
    }
    
    /**
     * Get payment history for a tenant
     */
    public function getPaymentHistory($tenant_id) {
        $query = "SELECT p.*, 
                         CONCAT(u.firstname, ' ', u.lastname) as tenant_name
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.tenant_id = u.id
                  WHERE p.tenant_id = :tenant_id
                  ORDER BY p.date_created DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tenant_id', $tenant_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Get payment by Stripe payment intent ID
     */
    public function getByStripeId($stripe_payment_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE stripe_payment_id = :stripe_payment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stripe_payment_id', $stripe_payment_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
