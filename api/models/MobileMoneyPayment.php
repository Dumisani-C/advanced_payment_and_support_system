<?php
/**
 * Mobile Money Payment Model
 * Handles Airtel Money and TNM Mpamba payments for Malawi
 */

require_once __DIR__ . '/../config/mobile_money.php';

class MobileMoneyPayment {
    private $db;
    private $table = 'payments';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Initiate Airtel Money payment
     */
    public function initiateAirtelPayment($amount, $phone, $tenant_id, $apartment_no, $description = 'Rent Payment') {
        // Generate unique transaction reference
        $reference = 'ARK-AM-' . time() . '-' . rand(1000, 9999);
        
        // Format phone number (ensure it starts with country code)
        $phone = $this->formatPhoneNumber($phone, '265');
        
        // First, get access token from Airtel
        $accessToken = $this->getAirtelAccessToken();
        
        if (!$accessToken) {
            throw new Exception('Failed to authenticate with Airtel Money');
        }

        // Prepare payment request
        $paymentData = [
            'reference' => $reference,
            'subscriber' => [
                'country' => AIRTEL_COUNTRY,
                'currency' => AIRTEL_CURRENCY,
                'msisdn' => $phone
            ],
            'transaction' => [
                'amount' => $amount,
                'country' => AIRTEL_COUNTRY,
                'currency' => AIRTEL_CURRENCY,
                'id' => $reference
            ]
        ];

        // Make API request to Airtel Money
        $response = $this->makeAirtelRequest('/merchant/v1/payments/', $paymentData, $accessToken);

        if ($response && isset($response['status']) && $response['status']['success']) {
            // Store pending payment in database
            $this->storePendingPayment([
                'tenant_id' => $tenant_id,
                'apartment_no' => $apartment_no,
                'amount' => $amount,
                'reference' => $reference,
                'provider' => 'airtel_money',
                'phone' => $phone,
                'description' => $description
            ]);

            return [
                'success' => true,
                'reference' => $reference,
                'message' => 'Payment initiated. Please check your phone to approve the transaction.',
                'provider' => 'airtel_money'
            ];
        }

        throw new Exception($response['status']['message'] ?? 'Failed to initiate Airtel Money payment');
    }

    /**
     * Initiate TNM Mpamba payment
     */
    public function initiateMpambaPayment($amount, $phone, $tenant_id, $apartment_no, $description = 'Rent Payment') {
        // Generate unique transaction reference
        $reference = 'ARK-MP-' . time() . '-' . rand(1000, 9999);
        
        // Format phone number
        $phone = $this->formatPhoneNumber($phone, '265');

        // Prepare payment request for TNM Mpamba
        $paymentData = [
            'merchant_id' => TNM_MERCHANT_ID,
            'reference' => $reference,
            'amount' => $amount,
            'currency' => 'MWK',
            'phone_number' => $phone,
            'callback_url' => PAYMENT_CALLBACK_URL,
            'description' => $description
        ];

        // Make API request to TNM Mpamba
        $response = $this->makeTnmRequest('/api/v1/payment/initiate', $paymentData);

        if ($response && isset($response['success']) && $response['success']) {
            // Store pending payment in database
            $this->storePendingPayment([
                'tenant_id' => $tenant_id,
                'apartment_no' => $apartment_no,
                'amount' => $amount,
                'reference' => $reference,
                'provider' => 'tnm_mpamba',
                'phone' => $phone,
                'description' => $description
            ]);

            return [
                'success' => true,
                'reference' => $reference,
                'message' => 'Payment initiated. Please check your phone to approve the transaction.',
                'provider' => 'tnm_mpamba'
            ];
        }

        throw new Exception($response['message'] ?? 'Failed to initiate TNM Mpamba payment');
    }

    /**
     * Create a bank transfer payment record
     */
    public function initiateBankTransfer($amount, $tenant_id, $apartment_no, $bank_code, $description = 'Rent Payment') {
        $reference = 'ARK-BT-' . time() . '-' . rand(1000, 9999);
        
        $banks = json_decode(SUPPORTED_BANKS, true);
        $selectedBank = null;
        foreach ($banks as $bank) {
            if ($bank['code'] === $bank_code) {
                $selectedBank = $bank;
                break;
            }
        }

        if (!$selectedBank) {
            throw new Exception('Invalid bank selected');
        }

        // Store pending bank transfer
        $this->storePendingPayment([
            'tenant_id' => $tenant_id,
            'apartment_no' => $apartment_no,
            'amount' => $amount,
            'reference' => $reference,
            'provider' => 'bank_transfer',
            'phone' => null,
            'description' => $description,
            'bank_code' => $bank_code
        ]);

        return [
            'success' => true,
            'reference' => $reference,
            'provider' => 'bank_transfer',
            'bank_details' => [
                'bank_name' => BUSINESS_BANK_NAME,
                'account_name' => BUSINESS_ACCOUNT_NAME,
                'account_number' => BUSINESS_ACCOUNT_NUMBER,
                'branch' => BUSINESS_BRANCH,
                'reference' => $reference,
                'amount' => $amount
            ],
            'message' => 'Please transfer to the account below and use the reference number'
        ];
    }

    /**
     * Handle payment callback from mobile money providers
     */
    public function handleCallback($provider, $data) {
        $reference = $data['reference'] ?? $data['transaction_id'] ?? null;
        
        if (!$reference) {
            throw new Exception('Invalid callback: missing reference');
        }

        // Find the pending payment
        $query = "SELECT * FROM {$this->table} WHERE stripe_payment_id = :reference AND payment_status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['reference' => $reference]);
        $payment = $stmt->fetch();

        if (!$payment) {
            throw new Exception('Payment not found');
        }

        // Determine status based on callback data
        $status = 'failed';
        if ($provider === 'airtel_money') {
            if (isset($data['transaction']['status']) && $data['transaction']['status'] === 'TS') {
                $status = 'completed';
            }
        } elseif ($provider === 'tnm_mpamba') {
            if (isset($data['status']) && $data['status'] === 'SUCCESS') {
                $status = 'completed';
            }
        }

        // Update payment status
        $updateQuery = "UPDATE {$this->table} SET payment_status = :status WHERE id = :id";
        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->execute(['status' => $status, 'id' => $payment['id']]);

        return ['success' => true, 'status' => $status];
    }

    /**
     * Confirm bank transfer manually (admin action)
     */
    public function confirmBankTransfer($reference, $admin_notes = '') {
        $query = "UPDATE {$this->table} SET payment_status = 'completed' WHERE stripe_payment_id = :reference AND payment_method = 'bank_transfer'";
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute(['reference' => $reference]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('Payment not found or already confirmed');
        }

        return ['success' => true, 'message' => 'Bank transfer confirmed'];
    }

    /**
     * Get supported banks list
     */
    public function getSupportedBanks() {
        return json_decode(SUPPORTED_BANKS, true);
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($reference) {
        $query = "SELECT payment_status, payment_method, amount, date_created FROM {$this->table} WHERE stripe_payment_id = :reference";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['reference' => $reference]);
        $payment = $stmt->fetch();

        if (!$payment) {
            throw new Exception('Payment not found');
        }

        return $payment;
    }

    /**
     * Store pending payment in database
     */
    private function storePendingPayment($data) {
        $invoice = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
        
        $query = "INSERT INTO {$this->table} 
                  (tenant_id, apartment_no, amount, invoice, payment_status, stripe_payment_id, payment_method, date_created) 
                  VALUES (:tenant_id, :apartment_no, :amount, :invoice, 'pending', :reference, :provider, NOW())";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'tenant_id' => $data['tenant_id'],
            'apartment_no' => $data['apartment_no'],
            'amount' => $data['amount'],
            'invoice' => $invoice,
            'reference' => $data['reference'],
            'provider' => $data['provider']
        ]);
    }

    /**
     * Get Airtel Money access token
     */
    private function getAirtelAccessToken() {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => AIRTEL_API_URL . '/auth/oauth2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'client_id' => AIRTEL_CLIENT_ID,
                'client_secret' => AIRTEL_CLIENT_SECRET,
                'grant_type' => 'client_credentials'
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }

    /**
     * Make Airtel API request
     */
    private function makeAirtelRequest($endpoint, $data, $accessToken) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => AIRTEL_API_URL . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
                'X-Country: ' . AIRTEL_COUNTRY,
                'X-Currency: ' . AIRTEL_CURRENCY
            ]
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Make TNM API request
     */
    private function makeTnmRequest($endpoint, $data) {
        $ch = curl_init();
        
        // Generate signature for TNM API
        $signature = hash_hmac('sha256', json_encode($data), TNM_API_SECRET);
        
        curl_setopt_array($ch, [
            CURLOPT_URL => TNM_API_URL . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-API-Key: ' . TNM_API_KEY,
                'X-Signature: ' . $signature
            ]
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Format phone number with country code
     */
    private function formatPhoneNumber($phone, $countryCode) {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove leading zero if present
        if (substr($phone, 0, 1) === '0') {
            $phone = substr($phone, 1);
        }
        
        // Add country code if not present
        if (substr($phone, 0, 3) !== $countryCode) {
            $phone = $countryCode . $phone;
        }
        
        return $phone;
    }
}
