import { useState, useEffect } from 'react';
import { 
  Smartphone, Building2, CheckCircle, AlertCircle, Loader2, 
  Copy, Phone, Banknote, Clock, ArrowRight
} from 'lucide-react';
import { 
  initiateAirtelPayment, 
  initiateMpambaPayment, 
  initiateBankTransfer,
  getSupportedBanks,
  checkPaymentStatus
} from '../lib/api';

interface Bank {
  code: string;
  name: string;
  swift: string;
}

interface PaymentFormProps {
  amount: number;
  tenantId: number;
  apartmentNo: string;
  tenantName: string;
  onSuccess: () => void;
  onCancel: () => void;
}

type PaymentProvider = 'airtel' | 'mpamba' | 'bank';

export default function MobilePaymentForm({
  amount,
  tenantId,
  apartmentNo,
  tenantName,
  onSuccess,
  onCancel
}: PaymentFormProps) {
  const [provider, setProvider] = useState<PaymentProvider>('airtel');
  const [phone, setPhone] = useState('');
  const [selectedBank, setSelectedBank] = useState('');
  const [banks, setBanks] = useState<Bank[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState(false);
  const [paymentReference, setPaymentReference] = useState<string | null>(null);
  const [bankDetails, setBankDetails] = useState<any>(null);
  const [checkingStatus, setCheckingStatus] = useState(false);

  useEffect(() => {
    loadBanks();
  }, []);

  const loadBanks = async () => {
    try {
      const res = await getSupportedBanks();
      setBanks(res.data);
    } catch (err) {
      console.error('Failed to load banks:', err);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      let result;
      
      if (provider === 'airtel') {
        if (!phone) {
          throw new Error('Please enter your Airtel Money phone number');
        }
        result = await initiateAirtelPayment({
          amount,
          phone,
          tenant_id: tenantId,
          apartment_no: apartmentNo,
          description: `Rent Payment - ${tenantName}`
        });
      } else if (provider === 'mpamba') {
        if (!phone) {
          throw new Error('Please enter your TNM Mpamba phone number');
        }
        result = await initiateMpambaPayment({
          amount,
          phone,
          tenant_id: tenantId,
          apartment_no: apartmentNo,
          description: `Rent Payment - ${tenantName}`
        });
      } else if (provider === 'bank') {
        if (!selectedBank) {
          throw new Error('Please select your bank');
        }
        result = await initiateBankTransfer({
          amount,
          tenant_id: tenantId,
          apartment_no: apartmentNo,
          bank_code: selectedBank,
          description: `Rent Payment - ${tenantName}`
        });
      }

      if (result?.data?.success) {
        setPaymentReference(result.data.reference);
        if (result.data.bank_details) {
          setBankDetails(result.data.bank_details);
        }
        setSuccess(true);
      }
    } catch (err: any) {
      setError(err.response?.data?.error || err.message || 'Payment failed');
    } finally {
      setLoading(false);
    }
  };

  const handleCheckStatus = async () => {
    if (!paymentReference) return;
    
    setCheckingStatus(true);
    try {
      const res = await checkPaymentStatus(paymentReference);
      if (res.data.payment_status === 'completed') {
        onSuccess();
      } else {
        setError('Payment is still pending. Please complete the transaction on your phone.');
      }
    } catch (err: any) {
      setError(err.response?.data?.error || 'Failed to check status');
    } finally {
      setCheckingStatus(false);
    }
  };

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
  };

  if (success && provider !== 'bank') {
    return (
      <div className="space-y-6">
        <div className="text-center py-6">
          <div className="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Clock className="w-8 h-8 text-amber-600" />
          </div>
          <h3 className="text-xl font-semibold text-slate-900 mb-2">Payment Initiated!</h3>
          <p className="text-slate-500">
            Check your phone and approve the payment of <strong>K{amount.toLocaleString()}</strong>
          </p>
        </div>

        <div className="bg-slate-50 rounded-lg p-4 border border-slate-200">
          <div className="flex items-center justify-between mb-2">
            <span className="text-sm text-slate-500">Reference</span>
            <div className="flex items-center gap-2">
              <code className="text-sm font-mono text-slate-900">{paymentReference}</code>
              <button 
                onClick={() => copyToClipboard(paymentReference || '')}
                className="p-1 hover:bg-slate-200 rounded"
              >
                <Copy className="w-4 h-4 text-slate-400" />
              </button>
            </div>
          </div>
          <div className="flex items-center justify-between">
            <span className="text-sm text-slate-500">Provider</span>
            <span className="text-sm font-medium text-slate-900 capitalize">
              {provider === 'airtel' ? 'Airtel Money' : 'TNM Mpamba'}
            </span>
          </div>
        </div>

        {error && (
          <div className="flex items-start gap-3 p-4 bg-amber-50 rounded-lg border border-amber-200">
            <AlertCircle className="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
            <p className="text-sm text-amber-700">{error}</p>
          </div>
        )}

        <div className="flex gap-3">
          <button
            onClick={onCancel}
            className="flex-1 px-4 py-3 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium"
          >
            Close
          </button>
          <button
            onClick={handleCheckStatus}
            disabled={checkingStatus}
            className="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 flex items-center justify-center gap-2 text-sm"
          >
            {checkingStatus ? (
              <>
                <Loader2 className="w-4 h-4 animate-spin" />
                Checking...
              </>
            ) : (
              <>
                <CheckCircle className="w-4 h-4" />
                I've Paid
              </>
            )}
          </button>
        </div>
      </div>
    );
  }

  if (success && provider === 'bank' && bankDetails) {
    return (
      <div className="space-y-6">
        <div className="text-center py-4">
          <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Building2 className="w-8 h-8 text-blue-600" />
          </div>
          <h3 className="text-xl font-semibold text-slate-900 mb-2">Bank Transfer Details</h3>
          <p className="text-slate-500">Transfer the amount to the account below</p>
        </div>

        <div className="bg-slate-50 rounded-lg p-4 border border-slate-200 space-y-3">
          <div className="flex items-center justify-between">
            <span className="text-sm text-slate-500">Bank</span>
            <span className="text-sm font-medium text-slate-900">{bankDetails.bank_name}</span>
          </div>
          <div className="flex items-center justify-between">
            <span className="text-sm text-slate-500">Account Name</span>
            <span className="text-sm font-medium text-slate-900">{bankDetails.account_name}</span>
          </div>
          <div className="flex items-center justify-between">
            <span className="text-sm text-slate-500">Account Number</span>
            <div className="flex items-center gap-2">
              <span className="text-sm font-mono font-medium text-slate-900">{bankDetails.account_number}</span>
              <button 
                onClick={() => copyToClipboard(bankDetails.account_number)}
                className="p-1 hover:bg-slate-200 rounded"
              >
                <Copy className="w-4 h-4 text-slate-400" />
              </button>
            </div>
          </div>
          <div className="flex items-center justify-between">
            <span className="text-sm text-slate-500">Branch</span>
            <span className="text-sm font-medium text-slate-900">{bankDetails.branch}</span>
          </div>
          <div className="flex items-center justify-between pt-3 border-t border-slate-200">
            <span className="text-sm text-slate-500">Reference (Important!)</span>
            <div className="flex items-center gap-2">
              <code className="text-sm font-mono font-bold text-indigo-600">{bankDetails.reference}</code>
              <button 
                onClick={() => copyToClipboard(bankDetails.reference)}
                className="p-1 hover:bg-slate-200 rounded"
              >
                <Copy className="w-4 h-4 text-slate-400" />
              </button>
            </div>
          </div>
          <div className="flex items-center justify-between">
            <span className="text-sm font-medium text-slate-700">Amount</span>
            <span className="text-lg font-bold text-slate-900">K{bankDetails.amount.toLocaleString()}</span>
          </div>
        </div>

        <div className="p-3 bg-amber-50 rounded-lg border border-amber-200">
          <p className="text-sm text-amber-700">
            <strong>Important:</strong> Use the reference number above when making your transfer. Your payment will be confirmed within 24 hours.
          </p>
        </div>

        <button
          onClick={onCancel}
          className="w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors text-sm"
        >
          Done
        </button>
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

      {/* Payment Provider Selection */}
      <div>
        <label className="block text-sm font-medium text-slate-700 mb-3">Select Payment Method</label>
        <div className="grid grid-cols-3 gap-3">
          <button
            type="button"
            onClick={() => setProvider('airtel')}
            className={`p-3 rounded-lg border-2 text-center transition-all ${
              provider === 'airtel'
                ? 'border-red-500 bg-red-50'
                : 'border-slate-200 hover:border-slate-300'
            }`}
          >
            <div className="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-2">
              <Smartphone className="w-5 h-5 text-white" />
            </div>
            <p className={`text-xs font-medium ${provider === 'airtel' ? 'text-red-700' : 'text-slate-700'}`}>
              Airtel Money
            </p>
          </button>
          
          <button
            type="button"
            onClick={() => setProvider('mpamba')}
            className={`p-3 rounded-lg border-2 text-center transition-all ${
              provider === 'mpamba'
                ? 'border-yellow-500 bg-yellow-50'
                : 'border-slate-200 hover:border-slate-300'
            }`}
          >
            <div className="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-2">
              <Phone className="w-5 h-5 text-white" />
            </div>
            <p className={`text-xs font-medium ${provider === 'mpamba' ? 'text-yellow-700' : 'text-slate-700'}`}>
              TNM Mpamba
            </p>
          </button>
          
          <button
            type="button"
            onClick={() => setProvider('bank')}
            className={`p-3 rounded-lg border-2 text-center transition-all ${
              provider === 'bank'
                ? 'border-blue-500 bg-blue-50'
                : 'border-slate-200 hover:border-slate-300'
            }`}
          >
            <div className="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-2">
              <Building2 className="w-5 h-5 text-white" />
            </div>
            <p className={`text-xs font-medium ${provider === 'bank' ? 'text-blue-700' : 'text-slate-700'}`}>
              Bank Transfer
            </p>
          </button>
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

      {/* Payment Form */}
      <form onSubmit={handleSubmit} className="space-y-4">
        {(provider === 'airtel' || provider === 'mpamba') && (
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">
              <Phone className="w-4 h-4 inline mr-1" />
              Phone Number
            </label>
            <input
              type="tel"
              value={phone}
              onChange={(e) => setPhone(e.target.value)}
              placeholder={provider === 'airtel' ? '099XXXXXXX' : '088XXXXXXX'}
              className="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
              required
            />
            <p className="text-xs text-slate-500 mt-1">
              {provider === 'airtel' 
                ? 'Enter your Airtel Money registered number' 
                : 'Enter your TNM Mpamba registered number'}
            </p>
          </div>
        )}

        {provider === 'bank' && (
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">
              <Building2 className="w-4 h-4 inline mr-1" />
              Select Your Bank
            </label>
            <select
              value={selectedBank}
              onChange={(e) => setSelectedBank(e.target.value)}
              className="w-full px-3 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
              required
            >
              <option value="">Choose a bank...</option>
              {banks.map((bank) => (
                <option key={bank.code} value={bank.code}>
                  {bank.name}
                </option>
              ))}
            </select>
          </div>
        )}

        <div className="flex gap-3 pt-4">
          <button
            type="button"
            onClick={onCancel}
            disabled={loading}
            className="flex-1 px-4 py-3 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium disabled:opacity-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            disabled={loading}
            className="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 flex items-center justify-center gap-2 text-sm"
          >
            {loading ? (
              <>
                <Loader2 className="w-4 h-4 animate-spin" />
                Processing...
              </>
            ) : (
              <>
                {provider === 'bank' ? (
                  <>
                    <Banknote className="w-4 h-4" />
                    Get Bank Details
                  </>
                ) : (
                  <>
                    <ArrowRight className="w-4 h-4" />
                    Pay K{amount.toLocaleString()}
                  </>
                )}
              </>
            )}
          </button>
        </div>
      </form>
    </div>
  );
}
