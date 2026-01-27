import { useEffect, useState } from 'react';
import { getApartments, createApartment, updateApartment, deleteApartment, getCategories } from '../lib/api';
import type { Apartment } from '../types';
import { Building2, Search, Plus, Edit2, Trash2, Check, Loader2, MoreHorizontal, Download, Filter, Home } from 'lucide-react';
import Modal from '../components/Modal';

interface Category {
  id: number;
  name: string;
}

export default function Apartments() {
  const [apartments, setApartments] = useState<Apartment[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [editingApartment, setEditingApartment] = useState<Apartment | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({
    apartment_no: '',
    description: '',
    price: '',
    category_id: '',
  });

  const fetchData = async () => {
    try {
      const [aptsRes, catsRes] = await Promise.all([
        getApartments(),
        getCategories()
      ]);
      setApartments(aptsRes.data);
      setCategories(catsRes.data || []);
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
    setEditingApartment(null);
    setFormData({ apartment_no: '', description: '', price: '', category_id: '' });
    setShowModal(true);
  };

  const openEditModal = (apt: Apartment) => {
    setEditingApartment(apt);
    setFormData({
      apartment_no: apt.apartment_no || '',
      description: apt.description || '',
      price: apt.price?.toString() || '',
      category_id: apt.category_id?.toString() || '',
    });
    setShowModal(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    try {
      if (editingApartment) {
        await updateApartment(editingApartment.id, formData);
      } else {
        await createApartment(formData);
      }
      setShowModal(false);
      fetchData();
    } catch (error) {
      console.error('Failed to save apartment:', error);
      alert('Failed to save apartment. Please try again.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (apt: Apartment) => {
    if (!confirm(`Delete Unit #${apt.apartment_no}? This cannot be undone.`)) return;
    try {
      await deleteApartment(apt.id);
      fetchData();
    } catch (error) {
      console.error('Failed to delete apartment:', error);
      alert('Failed to delete apartment.');
    }
  };

  const filteredApartments = apartments.filter((apt) =>
    apt.apartment_no.toLowerCase().includes(search.toLowerCase()) ||
    apt.category_name?.toLowerCase().includes(search.toLowerCase())
  );

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="text-center">
          <div className="w-10 h-10 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mx-auto" />
          <p className="mt-4 text-slate-500 text-sm">Loading properties...</p>
        </div>
      </div>
    );
  }

  const occupiedCount = apartments.filter(a => a.is_occupied).length;
  const availableCount = apartments.length - occupiedCount;

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-slate-900">Properties</h1>
          <p className="text-slate-500 text-sm mt-1">{apartments.length} total properties</p>
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
            Add Property
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div className="bg-white rounded-xl p-4 border border-slate-200">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-slate-500">Total Properties</p>
              <p className="text-2xl font-semibold text-slate-900 mt-1">{apartments.length}</p>
            </div>
            <div className="p-2 bg-indigo-50 rounded-lg">
              <Building2 className="w-5 h-5 text-indigo-600" />
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl p-4 border border-slate-200">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-slate-500">Available</p>
              <p className="text-2xl font-semibold text-emerald-600 mt-1">{availableCount}</p>
            </div>
            <div className="p-2 bg-emerald-50 rounded-lg">
              <Home className="w-5 h-5 text-emerald-600" />
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl p-4 border border-slate-200">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-slate-500">Occupied</p>
              <p className="text-2xl font-semibold text-amber-600 mt-1">{occupiedCount}</p>
            </div>
            <div className="p-2 bg-amber-50 rounded-lg">
              <Building2 className="w-5 h-5 text-amber-600" />
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
            placeholder="Search by unit number or category..."
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

      {/* Properties Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        {filteredApartments.length > 0 ? (
          filteredApartments.map((apartment) => (
            <div
              key={apartment.id}
              className="bg-white rounded-xl border border-slate-200 overflow-hidden hover:border-slate-300 hover:shadow-md transition-all"
            >
              {/* Header */}
              <div className="p-4 border-b border-slate-100">
                <div className="flex items-start justify-between">
                  <div>
                    <div className="flex items-center gap-2">
                      <h3 className="font-semibold text-slate-900">Unit #{apartment.apartment_no}</h3>
                      <span className={`px-2 py-0.5 text-xs font-medium rounded-full ${
                        apartment.is_occupied
                          ? 'bg-amber-50 text-amber-700'
                          : 'bg-emerald-50 text-emerald-700'
                      }`}>
                        {apartment.is_occupied ? 'Occupied' : 'Available'}
                      </span>
                    </div>
                    <p className="text-sm text-slate-500 mt-0.5">{apartment.category_name || 'Standard'}</p>
                  </div>
                  <button className="p-1 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded">
                    <MoreHorizontal className="w-4 h-4" />
                  </button>
                </div>
              </div>

              {/* Content */}
              <div className="p-4">
                <p className="text-sm text-slate-500 line-clamp-2 mb-4">
                  {apartment.description || 'Modern property with excellent amenities.'}
                </p>

                <div className="flex items-center justify-between mb-4">
                  <div>
                    <p className="text-xs text-slate-400">Monthly Rent</p>
                    <p className="text-xl font-semibold text-slate-900">K{Number(apartment.price).toLocaleString()}</p>
                  </div>
                </div>

                {/* Tenant info */}
                {apartment.tenant_name && (
                  <div className="flex items-center gap-2 p-2 bg-slate-50 rounded-lg mb-4">
                    <div className="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-xs font-semibold">
                      {apartment.tenant_name.charAt(0)}
                    </div>
                    <div>
                      <p className="text-xs text-slate-400">Tenant</p>
                      <p className="text-sm font-medium text-slate-700">{apartment.tenant_name}</p>
                    </div>
                  </div>
                )}

                {/* Actions */}
                <div className="flex gap-2">
                  <button
                    onClick={() => openEditModal(apartment)}
                    className="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors"
                  >
                    <Edit2 className="w-4 h-4" />
                    Edit
                  </button>
                  {!apartment.is_occupied && (
                    <button
                      onClick={() => handleDelete(apartment)}
                      className="flex items-center justify-center gap-1.5 px-3 py-2 text-sm text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  )}
                </div>
              </div>
            </div>
          ))
        ) : (
          <div className="col-span-full">
            <div className="flex flex-col items-center justify-center py-16 bg-white rounded-xl border border-slate-200">
              <div className="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                <Building2 className="w-6 h-6 text-slate-400" />
              </div>
              <p className="text-sm font-medium text-slate-900">{search ? 'No properties match your search' : 'No properties yet'}</p>
              <p className="text-sm text-slate-500 mt-1">Add your first property to get started</p>
            </div>
          </div>
        )}
      </div>

      {/* Add/Edit Property Modal */}
      <Modal isOpen={showModal} onClose={() => setShowModal(false)} title={editingApartment ? 'Edit Property' : 'Add New Property'}>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Unit Number *</label>
            <input
              type="text"
              required
              value={formData.apartment_no}
              onChange={(e) => setFormData({ ...formData, apartment_no: e.target.value })}
              placeholder="e.g., A101"
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Category</label>
            <select
              value={formData.category_id}
              onChange={(e) => setFormData({ ...formData, category_id: e.target.value })}
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
            >
              <option value="">Select category...</option>
              {categories.map((cat) => (
                <option key={cat.id} value={cat.id}>{cat.name}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Monthly Rent (MWK) *</label>
            <input
              type="number"
              required
              min="0"
              value={formData.price}
              onChange={(e) => setFormData({ ...formData, price: e.target.value })}
              placeholder="e.g., 150000"
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
            <textarea
              value={formData.description}
              onChange={(e) => setFormData({ ...formData, description: e.target.value })}
              rows={3}
              placeholder="Describe the property..."
              className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 resize-none text-sm"
            />
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
                  {editingApartment ? 'Update' : 'Add Property'}
                </>
              )}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}
