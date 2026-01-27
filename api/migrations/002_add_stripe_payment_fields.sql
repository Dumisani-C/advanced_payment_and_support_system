-- Migration: Add Stripe payment fields to payments table
-- Run this migration to support secure Stripe payments

-- Add new columns for Stripe integration
ALTER TABLE payments 
ADD COLUMN IF NOT EXISTS payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'completed' AFTER invoice,
ADD COLUMN IF NOT EXISTS stripe_payment_id VARCHAR(255) NULL AFTER payment_status,
ADD COLUMN IF NOT EXISTS receipt_url VARCHAR(500) NULL AFTER stripe_payment_id,
ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'manual' AFTER receipt_url;

-- Add index for faster lookups
CREATE INDEX IF NOT EXISTS idx_stripe_payment_id ON payments(stripe_payment_id);
CREATE INDEX IF NOT EXISTS idx_payment_status ON payments(payment_status);

-- Update existing payments to have 'completed' status and 'manual' payment method
UPDATE payments SET payment_status = 'completed', payment_method = 'manual' WHERE payment_status IS NULL;
