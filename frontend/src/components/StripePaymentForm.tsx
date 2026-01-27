import { useState, useEffect } from 'react';
import { loadStripe } from '@stripe/stripe-js';
import { Elements, PaymentElement, useStripe, useElements } from '@stripe/react-stripe-js';
import { CreditCard, Lock, CheckCircle, AlertCircle, Loader2, Shield } from 'lucide-react';
import { getStripeConfig, createPaymentIntent, confirmStripePayment } from '../lib/api';

interface PaymentFormProps {
  amount: number;
  tenantId: number;
  apartmentNo: string;
  tenantName: string;
  onSuccess: (receiptUrl?: string) => void;
  onCancel: () => void;
}

// Main wrapper component that loads Stripe
export default function StripePaymentForm(props: PaymentFormProps) {
  const [stripePromise, setStripePromise] = useState<ReturnType<typeof loadStripe> | null>(null);
  const [clientSecret, setClientSecret] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    initializePayment();
  }, []);

  const initializePayment = async () => {
    try {
      // Get Stripe publishable key
      const configRes = await getStripeConfig();
      setStripePromise(loadStripe(configRes.data.publishableKey));

      // Create payment intent
      const intentRes = await createPaymentIntent({
        amount: props.amount,
        tenant_id: props.tenantId,
        apartment_no: props.apartmentNo,
        description: `Rent Payment - ${props.tenantName}`
      });

      setClientSecret(intentRes.data.clientSecret);
      setLoading(false);
    } catch (err: any) {
      setError(err.response?.data?.error || err.message || 'Failed to initialize payment');
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex flex-col items-center justify-center py-12">
        <Loader2 className="w-8 h-8 animate-spin text-indigo-600 mb-4" />
        <p className="text-slate-600">Initializing secure payment...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="space-y-4">
        <div className="flex items-start gap-3 p-4 bg-red-50 rounded-lg border border-red-200">
          <AlertCircle className="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
          <div>
            <p className="text-sm font-medium text-red-800">Payment Initialization Error</p>
            <p className="text-sm text-red-600">{error}</p>
          </div>
        </div>
        <button
          onClick={props.onCancel}
          className="w-full px-4 py-2.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50"
        >
          Go Back
        </button>
      </div>
    );
  }

  if (!stripePromise || !clientSecret) {
    return null;
  }

  const appearance = {
    theme: 'stripe' as const,
    variables: {
      colorPrimary: '#4f46e5',
      colorBackground: '#ffffff',
      colorText: '#1e293b',
      colorDanger: '#ef4444',
      fontFamily: 'system-ui, sans-serif',
      borderRadius: '8px',
    },
  };

  return (
    <Elements stripe={stripePromise} options={{ clientSecret, appearance }}>
      <CheckoutForm {...props} />
    </Elements>
  );
}

// Inner checkout form that uses Stripe hooks
function CheckoutForm({ 
  amount, 
  tenantName,
  apartmentNo,
  onSuccess, 
  onCancel 
}: PaymentFormProps) {
  const stripe = useStripe();
  const elements = useElements();
  const [processing, setProcessing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!stripe || !elements) {
      return;
    }

    setProcessing(true);
    setError(null);

    try {
      const { error: stripeError, paymentIntent } = await stripe.confirmPayment({
        elements,
        confirmParams: {
          return_url: window.location.origin + '/payments?success=true',
        },
        redirect: 'if_required',
      });

      if (stripeError) {
        setError(stripeError.message || 'Payment failed');
        setProcessing(false);
        return;
      }

      if (paymentIntent && paymentIntent.status === 'succeeded') {
        const confirmRes = await confirmStripePayment(paymentIntent.id);
        setSuccess(true);
        setTimeout(() => {
          onSuccess(confirmRes.data.receiptUrl);
        }, 2000);
      }
    } catch (err: any) {
      setError(err.response?.data?.error || err.message || 'Payment failed');
      setProcessing(false);
    }
  };

  if (success) {
    return (
      <div className="flex flex-col items-center justify-center py-12">
        <div className="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-4">
          <CheckCircle className="w-8 h-8 text-emerald-600" />
        </div>
        <h3 className="text-xl font-semibold text-slate-900 mb-2">Payment Successful!</h3>
        <p className="text-slate-500 text-center">
          Your payment of <strong>K{amount.toLocaleString()}</strong> has been processed.
        </p>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Payment Summary */}
      <div className="bg-slate-50 rounded-lg p-4 border border-slate-200">
        <div className="flex items-center justify-between mb-3">
          <span className="text-sm text-slate-500">Tenant</span>
          <span className="text-sm font-medium text-slate-900">{tenantName}</span>
        </div>
        <div className="flex items-center justify-between mb-3">
          <span className="text-sm text-slate-500">Property</span>
          <span className="text-sm font-medium text-slate-900">Unit #{apartmentNo}</span>
        </div>
        <div className="flex items-center justify-between pt-3 border-t border-slate-200">
          <span className="text-sm font-medium text-slate-700">Total Amount</span>
          <span className="text-xl font-bold text-slate-900">K{amount.toLocaleString()}</span>
        </div>
      </div>

      {/* Security Badge */}
      <div className="flex items-center gap-2 p-3 bg-emerald-50 rounded-lg border border-emerald-200">
        <Shield className="w-5 h-5 text-emerald-600" />
        <div>
          <p className="text-sm font-medium text-emerald-800">Secure Payment</p>
          <p className="text-xs text-emerald-600">Your payment is protected by 256-bit SSL encryption</p>
        </div>
      </div>

      {/* Error Message */}
      {error && (
        <div className="flex items-start gap-3 p-4 bg-red-50 rounded-lg border border-red-200">
          <AlertCircle className="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
          <div>
            <p className="text-sm font-medium text-red-800">Payment Error</p>
            <p className="text-sm text-red-600">{error}</p>
          </div>
        </div>
      )}

      {/* Stripe Payment Form */}
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-slate-700 mb-2">
            <CreditCard className="w-4 h-4 inline mr-2" />
            Card Details
          </label>
          <div className="border border-slate-300 rounded-lg p-4 bg-white">
            <PaymentElement />
          </div>
        </div>

        <div className="flex gap-3 pt-4">
          <button
            type="button"
            onClick={onCancel}
            disabled={processing}
            className="flex-1 px-4 py-3 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium disabled:opacity-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            disabled={processing || !stripe || !elements}
            className="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 flex items-center justify-center gap-2 text-sm"
          >
            {processing ? (
              <>
                <Loader2 className="w-4 h-4 animate-spin" />
                Processing...
              </>
            ) : (
              <>
                <Lock className="w-4 h-4" />
                Pay K{amount.toLocaleString()}
              </>
            )}
          </button>
        </div>

        <p className="text-xs text-center text-slate-400">
          By clicking Pay, you agree to our terms of service. Powered by Stripe.
        </p>
      </form>
    </div>
  );
}
