import { useEffect, useState } from 'react';
import { getDashboardStats, getPayments } from '../lib/api';
import type { DashboardStats, Payment } from '../types';
import { Users, Building2, CreditCard, TrendingUp, ArrowUpRight, ArrowDownRight, Home, DollarSign, MoreHorizontal, Plus, Download, Filter } from 'lucide-react';
import { Link } from 'react-router-dom';

export default function Dashboard() {
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [recentPayments, setRecentPayments] = useState<Payment[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [statsRes, paymentsRes] = await Promise.all([
          getDashboardStats(),
          getPayments(),
        ]);
        setStats(statsRes.data);
        setRecentPayments(paymentsRes.data.slice(0, 5));
      } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <div className="text-center">
          <div className="w-10 h-10 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mx-auto" />
          <p className="mt-4 text-slate-500 text-sm">Loading dashboard...</p>
        </div>
      </div>
    );
  }

  const statCards = [
    {
      label: 'Total Tenants',
      value: stats?.total_tenants || 0,
      icon: Users,
      change: '+12%',
      trend: 'up',
      color: 'text-indigo-600',
      bgColor: 'bg-indigo-50',
    },
    {
      label: 'Properties',
      value: stats?.total_apartments || 0,
      icon: Building2,
      change: '+3',
      trend: 'up',
      color: 'text-emerald-600',
      bgColor: 'bg-emerald-50',
    },
    {
      label: 'Occupancy Rate',
      value: `${stats?.total_apartments ? Math.round((stats.occupied_apartments / stats.total_apartments) * 100) : 0}%`,
      icon: Home,
      change: '+5%',
      trend: 'up',
      color: 'text-amber-600',
      bgColor: 'bg-amber-50',
    },
    {
      label: 'Revenue',
      value: `K${Number(stats?.monthly_revenue || 0).toLocaleString()}`,
      icon: DollarSign,
      change: '+18%',
      trend: 'up',
      color: 'text-violet-600',
      bgColor: 'bg-violet-50',
    },
  ];

  return (
    <div className="space-y-6">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold text-slate-900">Overview</h1>
          <p className="text-slate-500 text-sm mt-1">Track your property performance and revenue</p>
        </div>
        <div className="flex items-center gap-3">
          <button className="flex items-center gap-2 px-3 py-2 text-sm text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
            <Download className="w-4 h-4" />
            Export
          </button>
          <button className="flex items-center gap-2 px-3 py-2 text-sm text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
            <Plus className="w-4 h-4" />
            Add Property
          </button>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {statCards.map((stat) => {
          const Icon = stat.icon;
          return (
            <div
              key={stat.label}
              className="bg-white rounded-xl p-5 border border-slate-200 hover:border-slate-300 transition-colors"
            >
              <div className="flex items-center justify-between mb-4">
                <div className={`p-2 rounded-lg ${stat.bgColor}`}>
                  <Icon className={`w-5 h-5 ${stat.color}`} />
                </div>
                <button className="p-1 text-slate-400 hover:text-slate-600 rounded">
                  <MoreHorizontal className="w-4 h-4" />
                </button>
              </div>
              <div>
                <p className="text-2xl font-semibold text-slate-900">{stat.value}</p>
                <div className="flex items-center justify-between mt-1">
                  <p className="text-sm text-slate-500">{stat.label}</p>
                  <div className={`flex items-center gap-0.5 text-xs font-medium ${
                    stat.trend === 'up' ? 'text-emerald-600' : 'text-red-600'
                  }`}>
                    {stat.trend === 'up' ? (
                      <ArrowUpRight className="w-3 h-3" />
                    ) : (
                      <ArrowDownRight className="w-3 h-3" />
                    )}
                    {stat.change}
                  </div>
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Main Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Revenue Card */}
        <div className="lg:col-span-2 bg-gradient-to-br from-indigo-600 to-violet-700 rounded-xl p-6 text-white">
          <div className="flex items-center justify-between mb-6">
            <div>
              <p className="text-indigo-100 text-sm font-medium">Total Revenue</p>
              <p className="text-3xl font-bold mt-1">
                K{Number(stats?.total_revenue || 0).toLocaleString()}
              </p>
            </div>
            <div className="flex items-center gap-2 px-3 py-1.5 bg-white/20 rounded-lg text-sm">
              <TrendingUp className="w-4 h-4" />
              +24% this month
            </div>
          </div>
          
          {/* Simple bar chart visualization */}
          <div className="flex items-end gap-2 h-32 mb-4">
            {[40, 65, 45, 80, 55, 90, 70, 85, 60, 95, 75, 88].map((height, i) => (
              <div key={i} className="flex-1 bg-white/20 rounded-t hover:bg-white/30 transition-colors" style={{ height: `${height}%` }} />
            ))}
          </div>
          <div className="flex justify-between text-xs text-indigo-200">
            <span>Jan</span>
            <span>Feb</span>
            <span>Mar</span>
            <span>Apr</span>
            <span>May</span>
            <span>Jun</span>
            <span>Jul</span>
            <span>Aug</span>
            <span>Sep</span>
            <span>Oct</span>
            <span>Nov</span>
            <span>Dec</span>
          </div>
        </div>

        {/* Quick Stats */}
        <div className="bg-white rounded-xl border border-slate-200 p-5">
          <h3 className="font-semibold text-slate-900 mb-4">Property Overview</h3>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                  <Home className="w-5 h-5 text-emerald-600" />
                </div>
                <div>
                  <p className="text-sm font-medium text-slate-900">Occupied</p>
                  <p className="text-xs text-slate-500">Active tenants</p>
                </div>
              </div>
              <p className="text-xl font-semibold text-slate-900">{stats?.occupied_apartments || 0}</p>
            </div>
            <div className="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                  <Building2 className="w-5 h-5 text-amber-600" />
                </div>
                <div>
                  <p className="text-sm font-medium text-slate-900">Available</p>
                  <p className="text-xs text-slate-500">Ready to rent</p>
                </div>
              </div>
              <p className="text-xl font-semibold text-slate-900">{(stats?.total_apartments || 0) - (stats?.occupied_apartments || 0)}</p>
            </div>
            <div className="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <Users className="w-5 h-5 text-indigo-600" />
                </div>
                <div>
                  <p className="text-sm font-medium text-slate-900">Tenants</p>
                  <p className="text-xs text-slate-500">Total registered</p>
                </div>
              </div>
              <p className="text-xl font-semibold text-slate-900">{stats?.total_tenants || 0}</p>
            </div>
          </div>
        </div>
      </div>

      {/* Recent Payments Table */}
      <div className="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div className="flex items-center justify-between p-5 border-b border-slate-200">
          <div>
            <h2 className="font-semibold text-slate-900">Recent Transactions</h2>
            <p className="text-sm text-slate-500 mt-0.5">Latest payment activity</p>
          </div>
          <div className="flex items-center gap-2">
            <button className="flex items-center gap-2 px-3 py-1.5 text-sm text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50">
              <Filter className="w-4 h-4" />
              Filter
            </button>
            <Link to="/payments" className="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
              View all
            </Link>
          </div>
        </div>
        
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="bg-slate-50">
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Tenant</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Property</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Date</th>
                <th className="text-left text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Status</th>
                <th className="text-right text-xs font-medium text-slate-500 uppercase tracking-wider px-5 py-3">Amount</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {recentPayments.length > 0 ? (
                recentPayments.map((payment) => (
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
                  <td colSpan={5} className="px-5 py-12 text-center">
                    <div className="flex flex-col items-center">
                      <div className="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                        <CreditCard className="w-6 h-6 text-slate-400" />
                      </div>
                      <p className="text-sm font-medium text-slate-900">No transactions yet</p>
                      <p className="text-sm text-slate-500 mt-1">Payments will appear here when made</p>
                    </div>
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
