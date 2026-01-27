import { useEffect, useState } from 'react';
import { getPayments, createPayment, getTenants } from '../lib/api';
import type { Payment, User } from '../types';
import { CreditCard, Search, TrendingUp, Plus, Check, Loader2, Download, Filter, ArrowUpRight, Shield, Zap, Smartphone, Building2 } from 'lucide-react';
import Modal from '../components/Modal';
import StripePaymentForm from '../components/StripePaymentForm';
import MobilePaymentForm from '../components/MobilePaymentForm';

export default function Payments() {
  const [payments, setPayments] = useState<Payment[]>([]);
  const [tenants, setTenants] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [paymentMode, setPaymentMode] = useState<'card' | 'mobile' | 'manual'>('mobile');
  const [selectedTenant, setSelectedTenant] = useState<User | null>(null);
  const [showStripeModal, setShowStripeModal] = useState(false);
  const [showMobileModal, setShowMobileModal] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({
    tenant_id: '',
    amount: '',
    invoice: '',
  });

  const fetchData = async () => {
    try {
      const [paymentsRes, tenantsRes] = await Promise.all([
        getPayments(),
        getTenants()
      ]);
      setPayments(paymentsRes.data);
      setTenants(tenantsRes.data);
    } catch (error) {
      console.error('Failed to fetch data:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const openAddModal = () => {
    setFormData({ tenant_id: '', amount: '', invoice: `INV-${Date.now()}` });
    setPaymentMode('mobile');
    setSelectedTenant(null);
    setShowStripeModal(false);
    setShowMobileModal(false);
    setShowModal(true);
  };

  const handleTenantSelect = (tenantId: string) => {
    setFormData({ ...formData, tenant_id: tenantId });
    const tenant = tenants.find(t => t.id.toString() === tenantId);
    setSelectedTenant(tenant || null);
    if (tenant?.price) {
      setFormData(prev => ({ ...prev, tenant_id: tenantId, amount: String(tenant.price) }));
    }
  };

  const handleStripeSuccess = (receiptUrl?: string) => {
    setShowStripeModal(false);
    setShowModal(false);
    fetchData();
    if (receiptUrl) {
      window.open(receiptUrl, '_blank');
    }
  };

  const handleMobileSuccess = () => {
    setShowMobileModal(false);
    setShowModal(false);
    fetchData();
  };

  const proceedToPayment = () => {
    if (!selectedTenant || !formData.amount) {
      alert('Please select a tenant and enter an amount');
      return;
    }
    if (paymentMode === 'card') {
      setShowStripeModal(true);
    } else if (paymentMode === 'mobile') {
      setShowMobileModal(true);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    try {
      await createPayment(formData);
      setShowModal(false);
      fetchData();
    } catch (error) {
      console.error('Failed to record payment:', error);
      alert('Failed to record payment. Please try again.');
    } finally {
      setSaving(false);
    }
  };

  const filteredPayments = payments.filter((payment) =>
    payment.tenant_name?.toLowerCase().includes(search.toLowerCase()) ||
    payment.invoice?.toLowerCase().includes(search.toLowerCase()) ||
    payment.apartment_no?.toString().includes(search)
  );

  const totalAmount = filteredPayments.reduce((sum, p) => sum + Number(p.amount), 0);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="text-center">
          <div className="w-10 h-10 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mx-auto" />
          <p className="mt-4 text-slate-500 text-sm">Loading payments...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-slate-900">Payments</h1>
          <p className="text-slate-500 text-sm mt-1">{payments.length} transactions recorded</p>
        </div>
        <div className="flex items-center gap-3">
          <button className="flex items-center gap-2 px-3 py-2 text-sm text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
            <Download className="w-4 h-4" />
            Export
          </button>
          <button
            onClick={openAddModal}
            className="flex items-center gap-2 px-3 py-2 text-sm text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors"
          >
            <Plus className="w-4 h-4" />
            Record Payment
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div className="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-xl p-5 text-white">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-indigo-100 text-sm">Total Collected</p>
              <p className="text-3xl font-bold mt-1">K{totalAmount.toLocaleString()}</p>
            </div>
            <div className="p-2 bg-white/20 rounded-lg">
              <CreditCard className="w-6 h-6" />
            </div>
          </div>
          <div className="flex items-center gap-1 mt-3 text-sm text-indigo-100">
            <ArrowUpRight className="w-4 h-4" />
            <span>+18% from last month</span>
          </div>
        </div>
        <div className="bg-white rounded-xl p-5 border border-slate-200">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-slate-500">Transactions</p>
              <p className="text-2xl font-semibold text-slate-900 mt-1">{payments.length}</p>
            </div>
            <div className="p-2 bg-emerald-50 rounded-lg">
              <TrendingUp className="w-5 h-5 text-emerald-600" />
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl p-5 border border-slate-200">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-slate-500">Avg. Payment</p>
              <p className="text-2xl font-semibold text-slate-900 mt-1">
                K{payments.length ? Math.round(totalAmount / payments.length).toLocaleString() : 0}
              </p>
            </div>
            <div className="p-2 bg-amber-50 rounded-lg">
              <CreditCard className="w-5 h-5 text-amber-600" />
            </div>
          </div>
        </div>
      </div>

      {/* Filters & Search */}
      <div className="flex flex-col sm:flex-row gap-3">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
          <input
            type="text"
            placeholder="Search by tenant, invoice, or unit..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pl-9 pr-4 py-2 text-sm bg-white border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
          />
        </div>
        <button className="flex items-center gap-2 px-3 py-2 text-sm text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50">
          <Filter className="w-4 h-4" />
          Filter
        </button>
      </div>

      {/* Payments Table */}
      <div className="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200">
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Tenant</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Invoice</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Property</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Date</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Status</th>
                <th className="text-right text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Amount</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filteredPayments.length > 0 ? (
                filteredPayments.map((payment) => (
                  <tr key={payment.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-5 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-xs font-semibold">
                          {payment.tenant_name?.charAt(0) || '?'}
                        </div>
                        <span className="text-sm font-medium text-slate-900">{payment.tenant_name || 'Unknown'}</span>
                      </div>
                    </td>
                    <td className="px-5 py-4">
                      <span className="text-sm text-slate-600 font-mono">{payment.invoice || `INV-${payment.id}`}</span>
                    </td>
                    <td className="px-5 py-4">
                      <span className="text-sm text-slate-600">Unit #{payment.apartment_no}</span>
                    </td>
                    <td className="px-5 py-4">
                      <span className="text-sm text-slate-500">
                        {new Date(payment.date_created).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                      </span>
                    </td>
                    <td className="px-5 py-4">
                      <span className="inline-flex items-center px-2 py-1 text-xs font-medium bg-emerald-50 text-emerald-700 rounded-full">
                        Completed
                      </span>
                    </td>
                    <td className="px-5 py-4 text-right">
                      <span className="text-sm font-semibold text-slate-900">K{Number(payment.amount).toLocaleString()}</span>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={6} className="px-5 py-12 text-center">
                    <div className="flex flex-col items-center">
                      <div className="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                        <CreditCard className="w-6 h-6 text-slate-400" />
                      </div>
                      <p className="text-sm font-medium text-slate-900">{search ? 'No payments match your search' : 'No payments yet'}</p>
                      <p className="text-sm text-slate-500 mt-1">Record your first payment to get started</p>
                    </div>
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Record Payment Modal */}
      <Modal 
        isOpen={showModal} 
        onClose={() => { setShowModal(false); setShowStripeModal(false); setShowMobileModal(false); }} 
        title={showStripeModal ? "Card Payment" : showMobileModal ? "Mobile Money / Bank" : "Record Payment"}
      >
        {showStripeModal && selectedTenant ? (
          <StripePaymentForm
            amount={Number(formData.amount)}
            tenantId={selectedTenant.id}
            apartmentNo={selectedTenant.apartment_no || ''}
            tenantName={`${selectedTenant.firstname} ${selectedTenant.lastname}`}
            onSuccess={handleStripeSuccess}
            onCancel={() => setShowStripeModal(false)}
          />
        ) : showMobileModal && selectedTenant ? (
          <MobilePaymentForm
            amount={Number(formData.amount)}
            tenantId={selectedTenant.id}
            apartmentNo={selectedTenant.apartment_no || ''}
            tenantName={`${selectedTenant.firstname} ${selectedTenant.lastname}`}
            onSuccess={handleMobileSuccess}
            onCancel={() => setShowMobileModal(false)}
          />
        ) : (
          <div className="space-y-4">
            {/* Payment Mode Selection */}
            <div className="grid grid-cols-3 gap-2">
              <button
                type="button"
                onClick={() => setPaymentMode('mobile')}
                className={`p-3 rounded-lg border-2 text-center transition-all ${
                  paymentMode === 'mobile'
                    ? 'border-red-500 bg-red-50'
                    : 'border-slate-200 hover:border-slate-300'
                }`}
              >
                <Smartphone className={`w-5 h-5 mx-auto mb-1 ${paymentMode === 'mobile' ? 'text-red-600' : 'text-slate-400'}`} />
                <p className={`text-xs font-medium ${paymentMode === 'mobile' ? 'text-red-700' : 'text-slate-700'}`}>
                  Mobile Money
                </p>
                <p className="text-[10px] text-slate-500">Airtel/Mpamba/Bank</p>
              </button>
              <button
                type="button"
                onClick={() => setPaymentMode('card')}
                className={`p-3 rounded-lg border-2 text-center transition-all ${
                  paymentMode === 'card'
                    ? 'border-indigo-600 bg-indigo-50'
                    : 'border-slate-200 hover:border-slate-300'
                }`}
              >
                <CreditCard className={`w-5 h-5 mx-auto mb-1 ${paymentMode === 'card' ? 'text-indigo-600' : 'text-slate-400'}`} />
                <p className={`text-xs font-medium ${paymentMode === 'card' ? 'text-indigo-700' : 'text-slate-700'}`}>
                  Card Payment
                </p>
                <p className="text-[10px] text-slate-500">Visa/Mastercard</p>
              </button>
              <button
                type="button"
                onClick={() => setPaymentMode('manual')}
                className={`p-3 rounded-lg border-2 text-center transition-all ${
                  paymentMode === 'manual'
                    ? 'border-emerald-600 bg-emerald-50'
                    : 'border-slate-200 hover:border-slate-300'
                }`}
              >
                <Zap className={`w-5 h-5 mx-auto mb-1 ${paymentMode === 'manual' ? 'text-emerald-600' : 'text-slate-400'}`} />
                <p className={`text-xs font-medium ${paymentMode === 'manual' ? 'text-emerald-700' : 'text-slate-700'}`}>
                  Manual Record
                </p>
                <p className="text-[10px] text-slate-500">Cash received</p>
              </button>
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1.5">Tenant *</label>
              <select
                required
                value={formData.tenant_id}
                onChange={(e) => handleTenantSelect(e.target.value)}
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
              >
                <option value="">Select tenant...</option>
                {tenants.map((tenant) => (
                  <option key={tenant.id} value={tenant.id}>
                    {tenant.firstname} {tenant.lastname} {tenant.apartment_no ? `(Unit #${tenant.apartment_no})` : ''}
                  </option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1.5">Amount (MWK) *</label>
              <input
                type="number"
                required
                min="1"
                value={formData.amount}
                onChange={(e) => setFormData({ ...formData, amount: e.target.value })}
                placeholder="e.g., 150000"
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
              />
            </div>

            {paymentMode === 'manual' && (
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-1.5">Invoice Number</label>
                <input
                  type="text"
                  value={formData.invoice}
                  onChange={(e) => setFormData({ ...formData, invoice: e.target.value })}
                  className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
                />
              </div>
            )}

            {paymentMode === 'mobile' && (
              <div className="p-3 bg-red-50 rounded-lg border border-red-200">
                <div className="flex items-center gap-2">
                  <Smartphone className="w-4 h-4 text-red-600" />
                  <p className="text-sm font-medium text-red-800">Local Payment Methods</p>
                </div>
                <p className="text-xs text-red-600 mt-1">Airtel Money, TNM Mpamba, or Bank Transfer</p>
              </div>
            )}

            {paymentMode === 'card' && (
              <div className="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                <div className="flex items-center gap-2">
                  <Shield className="w-4 h-4 text-indigo-600" />
                  <p className="text-sm font-medium text-indigo-800">Secure card payment</p>
                </div>
                <p className="text-xs text-indigo-600 mt-1">Protected by 256-bit SSL encryption. Powered by Stripe.</p>
              </div>
            )}

            <div className="flex gap-3 pt-4">
              <button
                type="button"
                onClick={() => setShowModal(false)}
                className="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium"
              >
                Cancel
              </button>
              {paymentMode === 'manual' ? (
                <button
                  type="button"
                  onClick={handleSubmit}
                  disabled={saving || !formData.tenant_id || !formData.amount}
                  className="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 flex items-center justify-center gap-2 text-sm"
                >
                  {saving ? (
                    <>
                      <Loader2 className="w-4 h-4 animate-spin" />
                      Recording...
                    </>
                  ) : (
                    <>
                      <Check className="w-4 h-4" />
                      Record Payment
                    </>
                  )}
                </button>
              ) : (
                <button
                  type="button"
                  onClick={proceedToPayment}
                  disabled={!selectedTenant || !formData.amount}
                  className={`flex-1 px-4 py-2.5 text-white rounded-lg font-medium transition-colors disabled:opacity-50 flex items-center justify-center gap-2 text-sm ${
                    paymentMode === 'mobile' ? 'bg-red-600 hover:bg-red-700' : 'bg-indigo-600 hover:bg-indigo-700'
                  }`}
                >
                  {paymentMode === 'mobile' ? (
                    <>
                      <Smartphone className="w-4 h-4" />
                      Continue
                    </>
                  ) : (
                    <>
                      <CreditCard className="w-4 h-4" />
                      Continue
                    </>
                  )}
                </button>
              )}
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
