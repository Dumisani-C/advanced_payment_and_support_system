export interface User {
  id: number;
  name: string;
  username: string;
  firstname: string;
  middlename: string;
  lastname: string;
  email: string;
  contact: string;
  user_type: '1' | '2' | '3'; // 1=admin, 2=tenant, 3=landlord
  status: 'Online' | 'Offline';
  apartment_id?: number;
  apartment_no?: string;
  date_in?: string;
  price?: number;
  category_name?: string;
}

export interface Message {
  id: number;
  sender_id: number;
  receiver_id: number;
  apartment_id?: number;
  message: string;
  is_read: number;
  date_created: string;
  sender_name?: string;
  receiver_name?: string;
  apartment_no?: string;
}

export interface Conversation {
  other_user_id: number;
  other_user_name: string;
  other_user_type: string;
  last_message_time: string;
  unread_count: number;
  last_message: string;
}

export interface ApartmentPhoto {
  id: number;
  apartment_id: number;
  filename: string;
  is_primary: number;
  url?: string;
}

export interface Payment {
  id: number;
  tenant_id: number;
  apartment_no: string;
  amount: number;
  invoice: string;
  date_created: string;
  tenant_name?: string;
  tenant_email?: string;
}

export interface Apartment {
  id: number;
  apartment_no: string;
  category_id: number;
  description: string;
  price: number;
  category_name?: string;
  is_occupied?: number;
  tenant_name?: string;
}

export interface Category {
  id: number;
  name: string;
}

export interface DashboardStats {
  total_tenants: number;
  total_apartments: number;
  occupied_apartments: number;
  monthly_revenue: number;
  total_revenue: number;
}

export interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
}
