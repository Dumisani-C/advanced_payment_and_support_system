import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { browseApartments } from '../lib/api';
import { Building2, MapPin, Bed, Bath, Search, MessageCircle, Heart, ArrowRight, User, Star, Shield, ChevronRight, Check, Zap } from 'lucide-react';

interface ApartmentListing {
  id: number;
  apartment_no: string;
  description: string;
  price: number;
  category_name: string;
  landlord_name: string;
  landlord_id: number;
  location?: string;
  bedrooms?: number;
  bathrooms?: number;
  primary_photo?: string;
  primary_photo_url?: string;
  photo_count?: number;
}

export default function Browse() {
  const navigate = useNavigate();
  const [apartments, setApartments] = useState<ApartmentListing[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [priceFilter, setPriceFilter] = useState<string>('all');
  const [likedApartments, setLikedApartments] = useState<Set<number>>(new Set());
  const user = JSON.parse(localStorage.getItem('user') || 'null');

  const toggleLike = (id: number) => {
    setLikedApartments(prev => {
      const newSet = new Set(prev);
      if (newSet.has(id)) newSet.delete(id);
      else newSet.add(id);
      return newSet;
    });
  };

  const features = [
    { icon: Shield, title: 'Verified Properties', desc: 'All listings vetted' },
    { icon: Zap, title: 'Instant Booking', desc: 'Quick & easy process' },
    { icon: Star, title: 'Top Rated', desc: '4.9/5 avg rating' },
  ];

  useEffect(() => {
    const fetchApartments = async () => {
      try {
        const response = await browseApartments();
        setApartments(response.data);
      } catch (error) {
        console.error('Failed to fetch apartments:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchApartments();
  }, []);

  const filteredApartments = apartments.filter((apt) => {
    const matchesSearch = 
      apt.apartment_no.toLowerCase().includes(search.toLowerCase()) ||
      apt.category_name?.toLowerCase().includes(search.toLowerCase()) ||
      apt.description?.toLowerCase().includes(search.toLowerCase());
    
    const matchesPrice = 
      priceFilter === 'all' ||
      (priceFilter === 'low' && apt.price < 100000) ||
      (priceFilter === 'mid' && apt.price >= 100000 && apt.price < 200000) ||
      (priceFilter === 'high' && apt.price >= 200000);
    
    return matchesSearch && matchesPrice;
  });

  const handleEnquire = (apt: ApartmentListing) => {
    if (!user) {
      navigate('/login', { state: { from: '/browse', apartmentId: apt.id } });
      return;
    }
    navigate(`/apartment/${apt.id}`);
  };

  return (
    <div className="min-h-screen bg-gradient-to-b from-slate-50 to-white">
      {/* Navigation */}
      <header className="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6">
          <div className="flex items-center justify-between h-16">
            <Link to="/" className="flex items-center gap-2">
              <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center">
                <Building2 className="w-5 h-5 text-white" />
              </div>
              <span className="text-xl font-semibold text-slate-900">ARK<span className="text-indigo-600">Rentals</span></span>
            </Link>
            
            <nav className="hidden md:flex items-center gap-8">
              <a href="#features" className="text-sm text-slate-600 hover:text-slate-900 transition-colors">Features</a>
              <a href="#listings" className="text-sm text-slate-600 hover:text-slate-900 transition-colors">Listings</a>
              <a href="#pricing" className="text-sm text-slate-600 hover:text-slate-900 transition-colors">Pricing</a>
            </nav>

            <div className="flex items-center gap-3">
              {user ? (
                <Link
                  to="/dashboard"
                  className="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
                >
                  Dashboard
                  <ChevronRight className="w-4 h-4" />
                </Link>
              ) : (
                <>
                  <Link to="/login" className="px-4 py-2 text-sm text-slate-600 hover:text-slate-900 font-medium transition-colors">
                    Log in
                  </Link>
                  <Link
                    to="/register"
                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
                  >
                    Start free trial
                  </Link>
                </>
              )}
            </div>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="relative pt-16 pb-20 overflow-hidden">
        {/* Background decoration */}
        <div className="absolute inset-0 -z-10">
          <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-gradient-to-br from-indigo-100 via-violet-50 to-transparent rounded-full blur-3xl opacity-60" />
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6">
          <div className="max-w-3xl mx-auto text-center">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-sm font-medium mb-6">
              <span className="w-2 h-2 rounded-full bg-indigo-500 animate-pulse" />
              Now serving 500+ happy tenants
            </div>
            
            <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold text-slate-900 tracking-tight mb-6">
              Find your perfect
              <span className="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600">
                rental property
              </span>
            </h1>
            
            <p className="text-lg text-slate-600 mb-8 max-w-2xl mx-auto leading-relaxed">
              The modern platform for property rentals. Browse verified listings, connect with landlords, and manage payments—all in one place.
            </p>
            
            {/* Search Bar */}
            <div className="max-w-2xl mx-auto">
              <div className="flex flex-col sm:flex-row gap-3 p-2 bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-200">
                <div className="flex-1 relative">
                  <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                  <input
                    type="text"
                    placeholder="Search by location, unit, or type..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    className="w-full pl-10 pr-4 py-3 bg-slate-50 text-slate-900 placeholder-slate-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:bg-white border border-transparent focus:border-indigo-500 transition-all"
                  />
                </div>
                <select
                  value={priceFilter}
                  onChange={(e) => setPriceFilter(e.target.value)}
                  className="px-4 py-3 bg-slate-50 text-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 border border-transparent focus:border-indigo-500 cursor-pointer transition-all"
                >
                  <option value="all">Any price</option>
                  <option value="low">Under K100k</option>
                  <option value="mid">K100k - K200k</option>
                  <option value="high">K200k+</option>
                </select>
                <button className="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                  <Search className="w-4 h-4" />
                  Search
                </button>
              </div>
            </div>

            {/* Trust indicators */}
            <div className="flex flex-wrap items-center justify-center gap-6 mt-10">
              {features.map((feature, i) => (
                <div key={i} className="flex items-center gap-2 text-sm text-slate-600">
                  <div className="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <feature.icon className="w-4 h-4 text-indigo-600" />
                  </div>
                  <span>{feature.title}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Listings Section */}
      <section id="listings" className="py-16 px-4 sm:px-6">
        <div className="max-w-7xl mx-auto">
          {/* Section Header */}
          <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
              <h2 className="text-2xl font-bold text-slate-900">Available Properties</h2>
              <p className="text-slate-500 mt-1">{filteredApartments.length} listings match your criteria</p>
            </div>
            <div className="flex items-center gap-2">
              <span className="text-sm text-slate-500">Sort by:</span>
              <select className="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <option>Newest first</option>
                <option>Price: Low to High</option>
                <option>Price: High to Low</option>
              </select>
            </div>
          </div>

          {loading ? (
            <div className="flex items-center justify-center py-20">
              <div className="text-center">
                <div className="w-12 h-12 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mx-auto" />
                <p className="text-slate-500 mt-4">Loading properties...</p>
              </div>
            </div>
          ) : filteredApartments.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {filteredApartments.map((apt) => (
                <div
                  key={apt.id}
                  className="group bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg hover:shadow-slate-200/50 hover:border-slate-300 transition-all duration-300"
                >
                  {/* Image */}
                  <div className="relative h-48 bg-slate-100 overflow-hidden">
                    {apt.primary_photo_url ? (
                      <img
                        src={apt.primary_photo_url}
                        alt={`Unit ${apt.apartment_no}`}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                      />
                    ) : (
                      <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                        <Building2 className="w-12 h-12 text-slate-300" />
                      </div>
                    )}
                    
                    {/* Badges */}
                    <div className="absolute top-3 left-3 right-3 flex items-center justify-between">
                      <span className="px-2.5 py-1 bg-white/90 backdrop-blur-sm text-xs font-medium text-slate-700 rounded-md shadow-sm">
                        {apt.category_name || 'Standard'}
                      </span>
                      <button 
                        onClick={() => toggleLike(apt.id)}
                        className={`p-2 rounded-full transition-all ${
                          likedApartments.has(apt.id) 
                            ? 'bg-rose-500 text-white' 
                            : 'bg-white/90 backdrop-blur-sm text-slate-400 hover:text-rose-500'
                        }`}
                      >
                        <Heart className={`w-4 h-4 ${likedApartments.has(apt.id) ? 'fill-current' : ''}`} />
                      </button>
                    </div>

                    {/* Price */}
                    <div className="absolute bottom-3 left-3">
                      <div className="px-3 py-1.5 bg-slate-900/80 backdrop-blur-sm rounded-lg">
                        <span className="text-lg font-bold text-white">K{Number(apt.price).toLocaleString()}</span>
                        <span className="text-slate-300 text-sm">/mo</span>
                      </div>
                    </div>
                  </div>

                  {/* Content */}
                  <div className="p-4">
                    <div className="mb-3">
                      <h3 className="font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors">
                        Unit #{apt.apartment_no}
                      </h3>
                      <div className="flex items-center gap-1 text-slate-500 text-sm mt-1">
                        <MapPin className="w-3.5 h-3.5" />
                        {apt.location || 'Area 3, Lilongwe'}
                      </div>
                    </div>

                    <p className="text-sm text-slate-500 line-clamp-2 mb-4">
                      {apt.description || 'Modern apartment with great amenities and convenient location.'}
                    </p>

                    {/* Features */}
                    <div className="flex items-center gap-4 mb-4 text-sm text-slate-600">
                      <div className="flex items-center gap-1.5">
                        <Bed className="w-4 h-4 text-slate-400" />
                        {apt.bedrooms || 2} beds
                      </div>
                      <div className="flex items-center gap-1.5">
                        <Bath className="w-4 h-4 text-slate-400" />
                        {apt.bathrooms || 1} bath
                      </div>
                    </div>

                    {/* Footer */}
                    <div className="flex items-center justify-between pt-4 border-t border-slate-100">
                      <div className="flex items-center gap-2">
                        <div className="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-xs font-medium">
                          {(apt.landlord_name || 'P')[0]}
                        </div>
                        <div>
                          <p className="text-sm font-medium text-slate-700">{apt.landlord_name || 'Property Manager'}</p>
                          <div className="flex items-center gap-1">
                            <Check className="w-3 h-3 text-emerald-500" />
                            <span className="text-xs text-slate-400">Verified</span>
                          </div>
                        </div>
                      </div>
                      <button
                        onClick={() => handleEnquire(apt)}
                        className="flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors"
                      >
                        <MessageCircle className="w-4 h-4" />
                        Enquire
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-20">
              <div className="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                <Building2 className="w-8 h-8 text-slate-400" />
              </div>
              <h3 className="text-lg font-semibold text-slate-900 mb-1">No properties found</h3>
              <p className="text-slate-500">
                {search ? 'Try adjusting your search criteria' : 'New listings are added regularly'}
              </p>
            </div>
          )}
        </div>
      </section>

      {/* CTA Section */}
      {!user && (
        <section className="py-20 px-4 sm:px-6 bg-gradient-to-br from-indigo-600 to-violet-700">
          <div className="max-w-4xl mx-auto text-center">
            <h2 className="text-3xl sm:text-4xl font-bold text-white mb-4">
              Ready to find your perfect home?
            </h2>
            <p className="text-lg text-indigo-100 mb-8 max-w-2xl mx-auto">
              Join thousands of happy tenants. Create an account to save favorites, message landlords, and manage your rental journey.
            </p>
            <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
              <Link
                to="/register"
                className="flex items-center gap-2 px-6 py-3 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-indigo-50 transition-colors"
              >
                Get started free
                <ArrowRight className="w-4 h-4" />
              </Link>
              <Link
                to="/register?type=landlord"
                className="flex items-center gap-2 px-6 py-3 border-2 border-white/30 text-white rounded-lg font-semibold hover:bg-white/10 transition-colors"
              >
                List your property
              </Link>
            </div>
          </div>
        </section>
      )}

      {/* Footer */}
      <footer className="bg-slate-900 text-slate-400 py-12 px-4 sm:px-6">
        <div className="max-w-7xl mx-auto">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
            <div className="col-span-2 md:col-span-1">
              <div className="flex items-center gap-2 mb-4">
                <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center">
                  <Building2 className="w-4 h-4 text-white" />
                </div>
                <span className="text-lg font-semibold text-white">ARK<span className="text-indigo-400">Rentals</span></span>
              </div>
              <p className="text-sm">The modern platform for property rentals in Lilongwe, Malawi.</p>
            </div>
            <div>
              <h4 className="text-white font-medium mb-3">Product</h4>
              <ul className="space-y-2 text-sm">
                <li><a href="#" className="hover:text-white transition-colors">Features</a></li>
                <li><a href="#" className="hover:text-white transition-colors">Pricing</a></li>
                <li><a href="#" className="hover:text-white transition-colors">Integrations</a></li>
              </ul>
            </div>
            <div>
              <h4 className="text-white font-medium mb-3">Company</h4>
              <ul className="space-y-2 text-sm">
                <li><a href="#" className="hover:text-white transition-colors">About</a></li>
                <li><a href="#" className="hover:text-white transition-colors">Blog</a></li>
                <li><a href="#" className="hover:text-white transition-colors">Careers</a></li>
              </ul>
            </div>
            <div>
              <h4 className="text-white font-medium mb-3">Legal</h4>
              <ul className="space-y-2 text-sm">
                <li><a href="#" className="hover:text-white transition-colors">Privacy</a></li>
                <li><a href="#" className="hover:text-white transition-colors">Terms</a></li>
                <li><a href="#" className="hover:text-white transition-colors">Security</a></li>
              </ul>
            </div>
          </div>
          <div className="pt-8 border-t border-slate-800 text-sm text-center">
            © 2024 ARKRentals. All rights reserved.
          </div>
        </div>
      </footer>
    </div>
  );
}
