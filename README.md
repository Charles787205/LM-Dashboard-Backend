# 📊 LM Dashboard Backend

A modern, feature-rich Laravel API for logistics and delivery management with comprehensive dashboard, real-time tracking, and advanced reporting capabilities.

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="License">
</p>

---

## ✨ Features

### 📱 Dashboard & Analytics
- **Interactive Dashboard** - Real-time insights and metrics visualization
- **Dashboard Comments** - Collaborative feedback and annotations on dashboard metrics
- **Advanced Reporting** - Generate comprehensive reports on deliveries, trips, and operations

### 🚚 Delivery Management
- **Successful Deliveries Tracking** - Complete delivery success monitoring and analytics
- **Failed Deliveries Management** - Track, analyze, and manage failed delivery attempts with detailed logging
- **Trip Management** - Organize and monitor delivery trips with comprehensive data

### 🏢 Hub & Operations
- **Hub Management** - Manage multiple distribution hubs and operational centers
- **Hub Comments** - Internal communication and notes on hub operations
- **Client Management** - Full client/customer information and relationship management
- **Attendance System** - Employee attendance tracking and reporting

### 🔐 Authentication & Authorization
- **OAuth 2.0 Authentication** - Secure API access via Laravel Passport
- **Social Authentication** - Google Sign-In integration with Laravel Socialite
- **Role-Based Access Control** - Fine-grained permissions system with Access management
- **Event Logging** - Comprehensive audit trail of all system activities

### 🛠️ Technical Features
- **RESTful API** - Clean, modern API architecture
- **Database Migrations** - Version-controlled schema management
- **Background Jobs** - Queue system for asynchronous processing
- **Testing Suite** - PHPUnit testing framework
- **Real-time Updates** - Event broadcasting capabilities

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & npm
- SQLite or MySQL database

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd lm_dashboard_backend
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database migration**
```bash
php artisan migrate
php artisan db:seed
```

5. **Generate Passport keys**
```bash
php artisan passport:install
```

6. **Build frontend assets**
```bash
npm run build
```

### Development

Start the development environment:
```bash
npm run dev
```

This runs:
- Laravel development server
- Job queue worker
- Application logs monitoring
- Vite hot module replacement

---

## 📁 Project Structure

```
app/
├── Http/Controllers/        # API Controllers
├── Models/                  # Eloquent Models (User, Client, Hub, Trip, etc.)
├── Enums/                   # Access roles, event types, positions
└── Providers/               # Service providers

database/
├── migrations/              # Schema migrations
├── factories/               # Model factories for testing
└── seeders/                 # Database seeders

routes/
├── api.php                  # API routes
├── web.php                  # Web routes
└── console.php              # Console commands

tests/                        # Test suite (Feature & Unit)
```

---

## 🗂️ Core Models

| Model | Purpose |
|-------|---------|
| **User** | System users with OAuth authentication |
| **Client** | Customer/client information management |
| **Hub** | Distribution hubs and operational centers |
| **Trip** | Delivery trip tracking and management |
| **Attendance** | Employee attendance records |
| **Report** | Business intelligence and reporting |
| **FailedDeliveries** | Failed delivery tracking and analysis |
| **SuccessfulDeliveries** | Successful delivery records |
| **Access** | Role-based access control |
| **EventLog** | Complete audit trail |

---

## 🔌 API Endpoints

The API provides RESTful endpoints for all major operations:
- `/api/users` - User management
- `/api/clients` - Client operations
- `/api/hubs` - Hub management
- `/api/trips` - Trip tracking
- `/api/deliveries/failed` - Failed deliveries
- `/api/deliveries/successful` - Successful deliveries
- `/api/dashboard` - Dashboard data
- `/api/reports` - Report generation
- `/api/attendance` - Attendance tracking

All endpoints require authentication via Bearer token (Passport OAuth2).

---

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

Or with coverage:
```bash
php artisan test --coverage
```

---

## 📦 Dependencies

### Core
- **Laravel 12.0** - Web framework
- **Laravel Passport** - OAuth2 authentication
- **Laravel Socialite** - Social authentication (Google)
- **Vite** - Frontend build tool

### Development
- **PHPUnit** - Testing framework
- **Laravel Pint** - Code style fixing
- **Faker** - Seeding and testing data generation
- **Mockery** - Mocking library

---

## 🔒 Security

- All API endpoints require authentication
- Input validation on all requests
- CORS enabled for specified domains (see `config/cors.php`)
- Request rate limiting
- CSRF protection
- Complete audit logging via EventLog

---

## 📝 Environment Variables

Key configuration variables in `.env`:
```
APP_NAME=LM Dashboard
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=lm_dashboard
DB_USERNAME=root
DB_PASSWORD=

PASSPORT_PERSONAL_ACCESS_CLIENT_ID=
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
```

---

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

## 🤝 Support

For issues, questions, or suggestions, please create an issue in the repository or contact the development team.
