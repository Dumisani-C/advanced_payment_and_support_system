import { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { login as apiLogin } from '../lib/api';
import { Loader2, Mail, Lock, ArrowRight, Building2, Shield, Star, Zap, Check } from 'lucide-react';

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [mounted, setMounted] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    setMounted(true);
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const response = await apiLogin(email, password);
      if (response.data.success) {
        login(response.data.user, response.data.token);
        navigate('/dashboard');
      } else {
        setError(response.data.error || 'Login failed');
      }
    } catch (err: any) {
      setError(err.response?.data?.error || 'Invalid credentials');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 flex">
      {/* Left Panel - Branding */}
      <div className={`hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 p-12 flex-col justify-between relative overflow-hidden transition-all duration-700 ${mounted ? 'opacity-100' : 'opacity-0'}`}>
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
              Manage your rentals
              <br />
              <span className="text-indigo-200">with ease</span>
            </h2>
            <p className="text-lg text-indigo-100 leading-relaxed max-w-md">
              Access your dashboard to manage payments, communicate with landlords, and track your rental history.
            </p>
          </div>

          {/* Features */}
          <div className="space-y-4">
            {[
              { icon: Shield, text: 'Secure payment processing' },
              { icon: Zap, text: 'Real-time notifications' },
              { icon: Star, text: 'Verified property listings' },
            ].map((item, i) => (
              <div key={i} className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                  <item.icon className="w-4 h-4 text-white" />
                </div>
                <span className="text-indigo-100">{item.text}</span>
              </div>
            ))}
          </div>
        </div>

        <div className="relative z-10 text-indigo-200 text-sm">
          © 2024 ARKRentals. All rights reserved.
        </div>
      </div>

      {/* Right Panel - Login Form */}
      <div className={`flex-1 flex items-center justify-center p-6 transition-all duration-700 delay-200 ${mounted ? 'opacity-100' : 'opacity-0'}`}>
        <div className="w-full max-w-md">
          {/* Mobile Logo */}
          <div className="lg:hidden flex items-center justify-center gap-2 mb-8">
            <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center">
              <Building2 className="w-5 h-5 text-white" />
            </div>
            <span className="text-xl font-semibold text-slate-900">ARK<span className="text-indigo-600">Rentals</span></span>
          </div>

          <div className="text-center mb-8">
            <h2 className="text-2xl font-bold text-slate-900 mb-2">Welcome back</h2>
            <p className="text-slate-500">Sign in to your account to continue</p>
          </div>

          {/* Login Form */}
          <form onSubmit={handleSubmit} className="space-y-5">
            {error && (
              <div className="flex items-center gap-3 p-3 rounded-lg bg-red-50 border border-red-200">
                <div className="w-2 h-2 rounded-full bg-red-500" />
                <span className="text-sm text-red-600">{error}</span>
              </div>
            )}

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
              <div className="relative">
                <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                <input
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="you@example.com"
                  className="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                  required
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
              <div className="relative">
                <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                <input
                  type="password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="••••••••"
                  className="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                  required
                />
              </div>
            </div>

            <div className="flex items-center justify-between text-sm">
              <label className="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" className="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                <span className="text-slate-600">Remember me</span>
              </label>
              <a href="#" className="text-indigo-600 hover:text-indigo-700 font-medium">Forgot password?</a>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full flex items-center justify-center gap-2 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50"
            >
              {loading ? (
                <>
                  <Loader2 className="w-5 h-5 animate-spin" />
                  <span>Signing in...</span>
                </>
              ) : (
                <>
                  <span>Sign in</span>
                  <ArrowRight className="w-4 h-4" />
                </>
              )}
            </button>
          </form>

          {/* Demo Credentials */}
          <div className="mt-6 p-4 rounded-lg bg-slate-100 border border-slate-200">
            <div className="flex items-start gap-3">
              <Check className="w-5 h-5 text-indigo-600 mt-0.5" />
              <div>
                <p className="text-sm font-medium text-slate-700 mb-1">Demo Account</p>
                <p className="text-xs text-slate-500">
                  Email: <span className="text-slate-700 font-medium">apiembewe@gmail.com</span>
                </p>
                <p className="text-xs text-slate-500">
                  Password: <span className="text-slate-700 font-medium">12345678</span>
                </p>
              </div>
            </div>
          </div>

          <p className="text-center text-slate-500 mt-6">
            Don't have an account?{' '}
            <Link to="/register" className="text-indigo-600 hover:text-indigo-700 font-medium">
              Sign up free
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
