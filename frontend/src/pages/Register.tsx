import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { register } from '../lib/api';
import { Building2, Mail, Lock, Phone, ArrowRight, Home, Briefcase, Loader2, Star, Shield, Check, Zap } from 'lucide-react';

export default function Register() {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [userType, setUserType] = useState<'tenant' | 'landlord'>('tenant');
  const [formData, setFormData] = useState({
    firstname: '',
    lastname: '',
    email: '',
    password: '',
    confirmPassword: '',
    contact: '',
  });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    if (formData.password !== formData.confirmPassword) {
      setError('Passwords do not match');
      return;
    }

    if (formData.password.length < 6) {
      setError('Password must be at least 6 characters');
      return;
    }

    setLoading(true);
    try {
      const response = await register({
        firstname: formData.firstname,
        lastname: formData.lastname,
        email: formData.email,
        password: formData.password,
        contact: formData.contact,
        user_type: userType,
      });

      if (response.data.success) {
        localStorage.setItem('user', JSON.stringify(response.data.user));
        localStorage.setItem('token', response.data.token);
        
        // Redirect based on user type
        if (response.data.user.user_type === '3') {
          navigate('/landlord');
        } else {
          navigate('/browse');
        }
      }
    } catch (err: any) {
      setError(err.response?.data?.error || 'Registration failed. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 flex">
      {/* Left Panel - Branding */}
      <div className="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 p-12 flex-col justify-between relative overflow-hidden">
        {/* Background Pattern */}
        <div className="absolute inset-0 opacity-10">
          <div className="absolute inset-0" style={{
            backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
          }} />
        </div>
        
        <div className="relative z-10">
          <Link to="/browse" className="flex items-center gap-2">
            <div className="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
              <Building2 className="w-5 h-5 text-white" />
            </div>
            <span className="text-xl font-semibold text-white">ARK<span className="font-normal text-indigo-200">Rentals</span></span>
          </Link>
        </div>

        <div className="relative z-10 space-y-8">
          <div>
            <h2 className="text-4xl font-bold text-white mb-4 leading-tight">
              Start your rental
              <br />
              <span className="text-indigo-200">journey today</span>
            </h2>
            <p className="text-lg text-indigo-100 leading-relaxed max-w-md">
              Join thousands of users who trust ARKRentals for finding homes and managing properties.
            </p>
          </div>

          {/* Features */}
          <div className="space-y-4">
            {[
              { icon: Shield, text: 'Secure & verified listings' },
              { icon: Zap, text: 'Instant communication' },
              { icon: Star, text: '24/7 customer support' },
            ].map((item, i) => (
              <div key={i} className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                  <item.icon className="w-4 h-4 text-white" />
                </div>
                <span className="text-indigo-100">{item.text}</span>
              </div>
            ))}
          </div>

          {/* Testimonial */}
          <div className="p-4 rounded-xl bg-white/10 border border-white/20">
            <p className="text-indigo-100 italic mb-3">"ARKRentals made finding my apartment so easy. The platform is intuitive and the support team is amazing."</p>
            <div className="flex items-center gap-2">
              <div className="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white text-sm font-medium">JM</div>
              <div>
                <p className="text-white text-sm font-medium">John Mwale</p>
                <p className="text-indigo-200 text-xs">Tenant since 2023</p>
              </div>
            </div>
          </div>
        </div>

        <div className="relative z-10 text-indigo-200 text-sm">
          © 2024 ARKRentals. All rights reserved.
        </div>
      </div>

      {/* Right Panel - Registration Form */}
      <div className="flex-1 flex items-center justify-center p-6 overflow-y-auto">
        <div className="w-full max-w-md py-8">
          {/* Mobile Logo */}
          <div className="lg:hidden flex items-center justify-center gap-2 mb-8">
            <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center">
              <Building2 className="w-5 h-5 text-white" />
            </div>
            <span className="text-xl font-semibold text-slate-900">ARK<span className="text-indigo-600">Rentals</span></span>
          </div>

          <div className="text-center mb-8">
            <h2 className="text-2xl font-bold text-slate-900 mb-2">Create your account</h2>
            <p className="text-slate-500">Get started with ARKRentals for free</p>
          </div>

          {/* User Type Selection */}
          <div className="grid grid-cols-2 gap-3 mb-6">
            <button
              type="button"
              onClick={() => setUserType('tenant')}
              className={`relative p-4 rounded-xl border-2 transition-all ${
                userType === 'tenant'
                  ? 'border-indigo-500 bg-indigo-50'
                  : 'border-slate-200 hover:border-slate-300 bg-white'
              }`}
            >
              {userType === 'tenant' && (
                <div className="absolute top-2 right-2">
                  <Check className="w-4 h-4 text-indigo-600" />
                </div>
              )}
              <Home className={`w-6 h-6 mx-auto mb-2 ${userType === 'tenant' ? 'text-indigo-600' : 'text-slate-400'}`} />
              <p className={`font-medium text-sm ${userType === 'tenant' ? 'text-indigo-700' : 'text-slate-700'}`}>Tenant</p>
              <p className="text-xs text-slate-500 mt-0.5">Find a home</p>
            </button>
            <button
              type="button"
              onClick={() => setUserType('landlord')}
              className={`relative p-4 rounded-xl border-2 transition-all ${
                userType === 'landlord'
                  ? 'border-indigo-500 bg-indigo-50'
                  : 'border-slate-200 hover:border-slate-300 bg-white'
              }`}
            >
              {userType === 'landlord' && (
                <div className="absolute top-2 right-2">
                  <Check className="w-4 h-4 text-indigo-600" />
                </div>
              )}
              <Briefcase className={`w-6 h-6 mx-auto mb-2 ${userType === 'landlord' ? 'text-indigo-600' : 'text-slate-400'}`} />
              <p className={`font-medium text-sm ${userType === 'landlord' ? 'text-indigo-700' : 'text-slate-700'}`}>Landlord</p>
              <p className="text-xs text-slate-500 mt-0.5">List properties</p>
            </button>
          </div>

          {error && (
            <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm flex items-center gap-2">
              <div className="w-2 h-2 bg-red-500 rounded-full" />
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-1.5">First name</label>
                <input
                  type="text"
                  required
                  value={formData.firstname}
                  onChange={(e) => setFormData({ ...formData, firstname: e.target.value })}
                  className="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                  placeholder="John"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-1.5">Last name</label>
                <input
                  type="text"
                  required
                  value={formData.lastname}
                  onChange={(e) => setFormData({ ...formData, lastname: e.target.value })}
                  className="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                  placeholder="Doe"
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
              <div className="relative">
                <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                <input
                  type="email"
                  required
                  value={formData.email}
                  onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                  className="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                  placeholder="you@example.com"
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1.5">Phone <span className="text-slate-400 font-normal">(optional)</span></label>
              <div className="relative">
                <Phone className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                <input
                  type="tel"
                  value={formData.contact}
                  onChange={(e) => setFormData({ ...formData, contact: e.target.value })}
                  className="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                  placeholder="+265 999 123 456"
                />
              </div>
            </div>

            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                <div className="relative">
                  <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                  <input
                    type="password"
                    required
                    value={formData.password}
                    onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                    className="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                    placeholder="••••••••"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-1.5">Confirm</label>
                <input
                  type="password"
                  required
                  value={formData.confirmPassword}
                  onChange={(e) => setFormData({ ...formData, confirmPassword: e.target.value })}
                  className="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                  placeholder="••••••••"
                />
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full flex items-center justify-center gap-2 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 mt-2"
            >
              {loading ? (
                <>
                  <Loader2 className="w-5 h-5 animate-spin" />
                  Creating account...
                </>
              ) : (
                <>
                  Create account
                  <ArrowRight className="w-4 h-4" />
                </>
              )}
            </button>
          </form>

          <p className="text-center text-slate-500 mt-6 text-sm">
            By signing up, you agree to our{' '}
            <a href="#" className="text-indigo-600 hover:text-indigo-700">Terms</a>
            {' '}and{' '}
            <a href="#" className="text-indigo-600 hover:text-indigo-700">Privacy Policy</a>
          </p>

          <p className="text-center text-slate-500 mt-4">
            Already have an account?{' '}
            <Link to="/login" className="text-indigo-600 hover:text-indigo-700 font-medium">
              Sign in
            </Link>
          </p>

          <div className="mt-4 text-center">
            <Link to="/browse" className="text-sm text-slate-500 hover:text-indigo-600 transition-colors">
              Browse listings without an account →
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
