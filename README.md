# ARKRentals - Advanced Tenant Payment & Support System

A modern property management system with integrated payment processing for Malawian markets.

## Features

### 🏠 Property Management
- Apartment listings with photos
- Tenant management
- Landlord dashboard

### 💳 Payment Processing
- **Stripe Integration** - Card payments (Visa/Mastercard)
- **Airtel Money** - Mobile money payments
- **TNM Mpamba** - Mobile money payments  
- **Bank Transfers** - 8 Malawian banks supported

### 📊 Dashboard & Reports
- Real-time payment tracking
- Revenue analytics
- Exportable reports

### 💬 Communication
- In-app messaging system
- Email notifications (PHPMailer)

## Tech Stack

### Backend
- PHP 8+ with REST API
- MySQL Database
- Stripe PHP SDK

### Frontend
- React 18 + TypeScript
- TailwindCSS
- Vite build tool

## Quick Start

### Prerequisites
- XAMPP (PHP 8+, MySQL)
- Node.js 18+
- Git

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Dumisani-C/advanced_payment_and_support_system.git
   cd advanced_payment_and_support_system
   ```

2. **Setup Database**
   ```bash
   # Create database in phpMyAdmin: ark_apartment
   # Import: database/ark_apartment.sql
   ```

3. **Configure Environment**
   ```bash
   cp api/.env.example api/.env
   # Edit api/.env with your API keys
   ```

4. **Start Backend**
   ```bash
   cd api
   C:\xampp\php\php.exe -S localhost:8000
   ```

5. **Start Frontend**
   ```bash
   cd frontend
   npm install
   npm run dev
   ```

6. **Access Application**
   - Frontend: http://localhost:3001
   - API: http://localhost:8000

## Payment Configuration

### Stripe
Get your API keys from [Stripe Dashboard](https://dashboard.stripe.com/apikeys)

### Airtel Money
Register at [Airtel Africa Developer Portal](https://developers.airtel.africa)

### TNM Mpamba
Contact TNM for merchant API access

## Folder Structure
```
├── api/                    # PHP REST API
│   ├── config/            # Database & payment configs
│   ├── models/            # Data models
│   └── migrations/        # SQL migrations
├── frontend/              # React application
│   ├── src/
│   │   ├── components/    # Reusable components
│   │   ├── pages/         # Page components
│   │   └── lib/           # API client
└── database/              # SQL dumps
```

## License
MIT


