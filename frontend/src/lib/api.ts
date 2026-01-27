import axios from 'axios';

const API_BASE = '/api';

const api = axios.create({
  baseURL: API_BASE,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Auth
export const login = (email: string, password: string) =>
  api.post('/auth/login', { email, password });

export const register = (data: {
  firstname: string;
  lastname: string;
  middlename?: string;
  email: string;
  password: string;
  contact?: string;
  user_type: 'tenant' | 'landlord';
}) => api.post('/auth/register', data);

// Dashboard
export const getDashboardStats = () => api.get('/dashboard');

// Tenants
export const getTenants = () => api.get('/tenants');
export const getTenant = (id: number) => api.get(`/tenants/${id}`);
export const createTenant = (data: any) => api.post('/tenants', data);
export const updateTenant = (id: number, data: any) => api.put(`/tenants/${id}`, data);

// Payments
export const getPayments = () => api.get('/payments');
export const getPayment = (id: number) => api.get(`/payments/${id}`);
export const getTenantPayments = (tenantId: number) => api.get(`/payments?tenant_id=${tenantId}`);
export const createPayment = (data: any) => api.post('/payments', data);

// Apartments
export const getApartments = () => api.get('/apartments');
export const getApartment = (id: number) => api.get(`/apartments/${id}`);
export const getAvailableApartments = () => api.get('/apartments/available');
export const createApartment = (data: any) => api.post('/apartments', data);
export const updateApartment = (id: number, data: any) => api.put(`/apartments/${id}`, data);
export const deleteApartment = (id: number) => api.delete(`/apartments/${id}`);

// Categories
export const getCategories = () => api.get('/categories');

// Public Browse (no auth required)
export const browseApartments = () => api.get('/browse');
export const browseApartment = (id: number) => api.get(`/browse/${id}`);

// Messages
export const getConversations = (userId: number) => api.get(`/messages/conversations?user_id=${userId}`);
export const getConversation = (user1Id: number, user2Id: number) => api.get(`/messages/${user1Id}/${user2Id}`);
export const sendMessage = (data: { sender_id: number; receiver_id: number; apartment_id?: number; message: string }) =>
  api.post('/messages', data);
export const markMessagesRead = (senderId: number, receiverId: number) =>
  api.put('/messages', { sender_id: senderId, receiver_id: receiverId });
export const getUnreadCount = (userId: number) => api.get(`/messages/unread?user_id=${userId}`);

// Photos
export const getApartmentPhotos = (apartmentId: number) => api.get(`/photos/${apartmentId}`);
export const uploadPhoto = (apartmentId: number, file: File, isPrimary = false) => {
  const formData = new FormData();
  formData.append('photo', file);
  formData.append('apartment_id', apartmentId.toString());
  formData.append('is_primary', isPrimary ? '1' : '0');
  return api.post('/photos', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
};
export const deletePhoto = (photoId: number) => api.delete(`/photos/${photoId}`);

// Landlords
export const getLandlords = () => api.get('/landlords');
export const getLandlord = (id: number) => api.get(`/landlords/${id}`);
export const getLandlordProperties = (landlordId: number) => api.get(`/landlords/properties?landlord_id=${landlordId}`);
export const getLandlordIncome = (landlordId: number) => api.get(`/landlords/income?landlord_id=${landlordId}`);

// Stripe Payments
export const getStripeConfig = () => api.get('/stripe/config');
export const createPaymentIntent = (data: { 
  amount: number; 
  tenant_id: number; 
  apartment_no: string;
  description?: string;
}) => api.post('/stripe/create-payment-intent', data);
export const confirmStripePayment = (paymentIntentId: string) => 
  api.post('/stripe/confirm', { paymentIntentId });
export const getPaymentHistory = (tenantId: number) => 
  api.get(`/stripe/history?tenant_id=${tenantId}`);

// Mobile Money Payments (Airtel Money, TNM Mpamba, Bank Transfer)
export const initiateAirtelPayment = (data: {
  amount: number;
  phone: string;
  tenant_id: number;
  apartment_no: string;
  description?: string;
}) => api.post('/mobile-payment/airtel', data);

export const initiateMpambaPayment = (data: {
  amount: number;
  phone: string;
  tenant_id: number;
  apartment_no: string;
  description?: string;
}) => api.post('/mobile-payment/mpamba', data);

export const initiateBankTransfer = (data: {
  amount: number;
  tenant_id: number;
  apartment_no: string;
  bank_code: string;
  description?: string;
}) => api.post('/mobile-payment/bank-transfer', data);

export const getSupportedBanks = () => api.get('/mobile-payment/banks');
export const checkPaymentStatus = (reference: string) => 
  api.get(`/mobile-payment/status?reference=${reference}`);
export const confirmBankTransfer = (reference: string, notes?: string) =>
  api.post('/mobile-payment/confirm-bank', { reference, notes });

export default api;
