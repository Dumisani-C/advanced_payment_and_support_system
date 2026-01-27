import { useEffect, useState } from 'react';
import { getTenants, createTenant, updateTenant, getAvailableApartments } from '../lib/api';
import type { User, Apartment } from '../types';
import { Phone, Home, Search, Users, Plus, Edit2, Check, Loader2, MoreHorizontal, Download, Filter } from 'lucide-react';
import Modal from '../components/Modal';

export default function Tenants() {
  const [tenants, setTenants] = useState<User[]>([]);
  const [apartments, setApartments] = useState<Apartment[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [editingTenant, setEditingTenant] = useState<User | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({
    firstname: '',
    middlename: '',
    lastname: '',
    email: '',
    contact: '',
    apartment_id: '',
  });

  const fetchData = async () => {
    try {
      const [tenantsRes, aptsRes] = await Promise.all([
        getTenants(),
        getAvailableApartments()
      ]);
      setTenants(tenantsRes.data);
      setApartments(aptsRes.data);
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
    setEditingTenant(null);
    setFormData({ firstname: '', middlename: '', lastname: '', email: '', contact: '', apartment_id: '' });
    setShowModal(true);
  };

  const openEditModal = (tenant: User) => {
    setEditingTenant(tenant);
    setFormData({
      firstname: tenant.firstname || '',
      middlename: tenant.middlename || '',
      lastname: tenant.lastname || '',
      email: tenant.email || '',
      contact: tenant.contact || '',
      apartment_id: tenant.apartment_id?.toString() || '',
    });
    setShowModal(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    try {
      if (editingTenant) {
        await updateTenant(editingTenant.id, formData);
      } else {
        await createTenant(formData);
      }
      setShowModal(false);
      fetchData();
    } catch (error) {
      console.error('Failed to save tenant:', error);
      alert('Failed to save tenant. Please try again.');
    } finally {
      setSaving(false);
    }
  };

  const filteredTenants = tenants.filter((tenant) => {
    const fullName = `${tenant.firstname} ${tenant.middlename} ${tenant.lastname}`.toLowerCase();
    return fullName.includes(search.toLowerCase()) || tenant.email?.toLowerCase().includes(search.toLowerCase());
  });

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="text-center">
          <div className="w-10 h-10 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mx-auto" />
          <p className="mt-4 text-slate-500 text-sm">Loading tenants...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-slate-900">Tenants</h1>
          <p className="text-slate-500 text-sm mt-1">{tenants.length} registered tenants</p>
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
            Add Tenant
          </button>
        </div>
      </div>

      {/* Filters & Search */}
      <div className="flex flex-col sm:flex-row gap-3">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
          <input
            type="text"
            placeholder="Search by name or email..."
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

      {/* Tenants Table */}
      <div className="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="bg-slate-50 border-b border-slate-200">
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Tenant</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Contact</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Property</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Rent</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Status</th>
                <th className="text-right text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filteredTenants.length > 0 ? (
                filteredTenants.map((tenant) => (
                  <tr key={tenant.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-5 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-sm font-semibold">
                          {tenant.firstname?.[0]}{tenant.lastname?.[0]}
                        </div>
                        <div>
                          <p className="text-sm font-medium text-slate-900">{tenant.firstname} {tenant.lastname}</p>
                          <p className="text-xs text-slate-500">{tenant.email}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-5 py-4">
                      <div className="flex items-center gap-2 text-sm text-slate-600">
                        <Phone className="w-4 h-4 text-slate-400" />
                        {tenant.contact || '—'}
                      </div>
                    </td>
                    <td className="px-5 py-4">
                      {tenant.apartment_no ? (
                        <span className="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded-md">
                          <Home className="w-3 h-3" />
                          Unit #{tenant.apartment_no}
                        </span>
                      ) : (
                        <span className="text-sm text-slate-400">Not assigned</span>
                      )}
                    </td>
                    <td className="px-5 py-4">
                      {tenant.price ? (
                        <span className="text-sm font-semibold text-slate-900">K{Number(tenant.price).toLocaleString()}</span>
                      ) : (
                        <span className="text-sm text-slate-400">—</span>
                      )}
                    </td>
                    <td className="px-5 py-4">
                      <span className="inline-flex items-center px-2 py-1 text-xs font-medium bg-emerald-50 text-emerald-700 rounded-full">
                        Active
                      </span>
                    </td>
                    <td className="px-5 py-4 text-right">
                      <button
                        onClick={() => openEditModal(tenant)}
                        className="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                      >
                        <Edit2 className="w-4 h-4" />
                      </button>
                      <button className="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <MoreHorizontal className="w-4 h-4" />
                      </button>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={6} className="px-5 py-12 text-center">
                    <div className="flex flex-col items-center">
                      <div className="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                        <Users className="w-6 h-6 text-slate-400" />
                      </div>
                      <p className="text-sm font-medium text-slate-900">{search ? 'No tenants match your search' : 'No tenants yet'}</p>
                      <p className="text-sm text-slate-500 mt-1">Add your first tenant to get started</p>
                    </div>
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Add/Edit Tenant Modal */}
      <Modal isOpen={showModal} onClose={() => setShowModal(false)} title={editingTenant ? 'Edit Tenant' : 'Add New Tenant'}>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1.5">First Name *</label>
              <input
                type="text"
                required
                value={formData.firstname}
                onChange={(e) => setFormData({ ...formData, firstname: e.target.value })}
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1.5">Last Name *</label>
              <input
                type="text"
                required
                value={formData.lastname}
                onChange={(e) => setFormData({ ...formData, lastname: e.target.value })}
                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
              />
            </div>
          </div>
          
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Middle Name</label>
            <input
              type="text"
              value={formData.middlename}
              onChange={(e) => setFormData({ ...formData, middlename: e.target.value })}
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Email *</label>
            <input
              type="email"
              required
              value={formData.email}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
            <input
              type="tel"
              value={formData.contact}
              onChange={(e) => setFormData({ ...formData, contact: e.target.value })}
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Assign Property</label>
            <select
              value={formData.apartment_id}
              onChange={(e) => setFormData({ ...formData, apartment_id: e.target.value })}
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
            >
              <option value="">Select property...</option>
              {apartments.map((apt) => (
                <option key={apt.id} value={apt.id}>
                  Unit #{apt.apartment_no} - K{Number(apt.price).toLocaleString()}/mo
                </option>
              ))}
            </select>
          </div>

          <div className="flex gap-3 pt-4">
            <button
              type="button"
              onClick={() => setShowModal(false)}
              className="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium"
            >
              Cancel
            </button>
            <button
              type="submit"
              disabled={saving}
              className="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 flex items-center justify-center gap-2 text-sm"
            >
              {saving ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin" />
                  Saving...
                </>
              ) : (
                <>
                  <Check className="w-4 h-4" />
                  {editingTenant ? 'Update' : 'Add Tenant'}
                </>
              )}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}
