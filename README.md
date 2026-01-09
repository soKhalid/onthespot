# OnTheSpot - User Portal Web Application

OnTheSpot is a comprehensive user portal that connects consumers with local brands and shops across multiple sectors including fashion, dining, and other retail services. The platform provides a standardized, professional online presence for businesses while offering users an easy way to discover, review, and connect with trusted local brands.

## 🌟 Features

### For Consumers
- **Advanced Search & Discovery**: Find brands by category, location, rating, and more
- **Detailed Brand Profiles**: View comprehensive information including inventory, menu items, location, and business hours
- **Review System**: Read and write reviews to help others make informed decisions
- **Favorites**: Save your favorite brands for quick access
- **Category Browsing**: Explore brands by Fashion, Restaurants, Cafes, and more
- **Google Maps Integration**: View brand locations and get directions
- **Delivery Service Integration**: Direct links to Talabat and Deliveroo for restaurants

### For Business Owners
- **Professional Brand Pages**: Standardized, beautiful pages with customizable information
- **Inventory Management**: Easy-to-use interface for managing products/menu items
- **Stock Control**: Track inventory levels for fashion items
- **Multiple Brand Support**: Manage multiple brands from one account
- **Customer Reviews**: See what customers are saying about your business
- **Location Management**: Set your exact location with map integration

## 🏗️ Technical Architecture

### Backend
- **Framework**: Laravel 10 (PHP 8.2)
- **Database**: PostgreSQL
- **Authentication**: Laravel Sanctum (token-based API authentication)
- **API**: RESTful API with comprehensive endpoints

### Frontend
- **Framework**: Vue.js 3 (Composition API)
- **State Management**: Pinia
- **Routing**: Vue Router
- **Styling**: Tailwind CSS
- **Build Tool**: Vite

### Infrastructure
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx (reverse proxy)
- **Deployment**: Production-ready configuration included

## 📦 Project Structure

```
onthespot/
├── backend/                 # Laravel backend
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/ # API controllers
│   │   ├── Models/          # Eloquent models
│   │   └── Policies/        # Authorization policies
│   ├── config/              # Configuration files
│   ├── database/
│   │   ├── migrations/      # Database migrations
│   │   └── seeders/         # Database seeders
│   ├── routes/
│   │   └── api.php          # API routes
│   └── composer.json
│
├── frontend/                # Vue.js frontend
│   ├── src/
│   │   ├── components/      # Reusable components
│   │   ├── views/           # Page components
│   │   ├── router/          # Route definitions
│   │   ├── stores/          # Pinia stores
│   │   ├── services/        # API services
│   │   └── assets/          # Static assets
│   └── package.json
│
├── nginx/                   # Nginx configuration
├── docs/                    # Documentation
├── docker-compose.yml       # Docker orchestration
└── README.md
```

## 🚀 Getting Started (Development)

### Prerequisites
- Docker & Docker Compose (recommended)
- OR: PHP 8.2+, Composer, Node.js 18+, PostgreSQL 15+

### Option 1: Docker (Recommended)

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd onthespot
   ```

2. **Set up environment variables**
   ```bash
   # Backend
   cp backend/.env.example backend/.env
   # Edit backend/.env with your database credentials

   # Frontend
   cp frontend/.env.example frontend/.env
   ```

3. **Start the containers**
   ```bash
   docker-compose up -d
   ```

4. **Run database migrations**
   ```bash
   docker exec onthespot_backend php artisan migrate
   docker exec onthespot_backend php artisan db:seed
   ```

5. **Access the application**
   - Frontend: http://localhost:3000
   - Backend API: http://localhost:8000/api

### Option 2: Manual Setup

#### Backend Setup

1. **Install dependencies**
   ```bash
   cd backend
   composer install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Set up database**
   ```bash
   # Create PostgreSQL database
   createdb onthespot

   # Run migrations
   php artisan migrate
   php artisan db:seed
   ```

4. **Start server**
   ```bash
   php artisan serve
   ```

#### Frontend Setup

1. **Install dependencies**
   ```bash
   cd frontend
   npm install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with API URL
   ```

3. **Start development server**
   ```bash
   npm run dev
   ```

## 📊 Database Schema

### Core Tables
- **users**: Consumer and business user accounts
- **categories**: Brand categories (Fashion, Restaurant, etc.)
- **brands**: Business/brand profiles
- **inventory_items**: Products or menu items
- **reviews**: Customer reviews and ratings
- **favorites**: User favorite brands

## 🔑 API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/me` - Get current user

### Brands
- `GET /api/brands` - List brands (with filters)
- `GET /api/brands/{slug}` - Get brand details
- `POST /api/brands` - Create brand (business only)
- `PUT /api/brands/{id}` - Update brand (owner only)
- `DELETE /api/brands/{id}` - Delete brand (owner only)

### Categories
- `GET /api/categories` - List categories
- `GET /api/categories/{slug}` - Get category with brands

### Inventory
- `GET /api/brands/{id}/inventory` - List brand items
- `POST /api/brands/{id}/inventory` - Add item (owner only)
- `PUT /api/brands/{id}/inventory/{itemId}` - Update item
- `DELETE /api/brands/{id}/inventory/{itemId}` - Delete item

### Reviews
- `GET /api/brands/{id}/reviews` - List reviews
- `POST /api/brands/{id}/reviews` - Create review
- `PUT /api/reviews/{id}` - Update review (author only)
- `DELETE /api/reviews/{id}` - Delete review (author only)

### Favorites
- `GET /api/favorites` - List user favorites
- `POST /api/brands/{id}/favorite` - Toggle favorite

## 🌐 Deployment Guide

See [DEPLOYMENT.md](./docs/DEPLOYMENT.md) for comprehensive deployment instructions including:
- Server requirements
- Production setup
- SSL configuration
- Database backup
- Monitoring setup

## 🔧 Configuration

### Google Maps API
1. Get an API key from [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Maps JavaScript API and Geocoding API
3. Add the key to your `.env` file:
   ```
   GOOGLE_MAPS_API_KEY=your-api-key-here
   ```

### External Delivery Services
Business owners can add direct links to their Talabat and Deliveroo profiles in their brand settings.

## 🧪 Testing

```bash
# Backend tests
cd backend
php artisan test

# Frontend tests
cd frontend
npm run test
```

## 📝 License

This project is proprietary software. All rights reserved.

## 👥 Team

- **Product Manager**: Person
- **Lead Developer**: Person
- **Marketing/Outreach Lead**: Person
- **Legal/Finance**: Person

## 📍 Contact

- **Main Office**: Place
- **Email**: hello@onthespot.com
- **Website**: https://onthespot.com

## 🎯 Roadmap

- [ ] Mobile applications (iOS & Android)
- [ ] Advanced analytics dashboard for businesses
- [ ] Integration with more delivery services
- [ ] Multi-language support
- [ ] Payment gateway integration
- [ ] Booking/reservation system for restaurants
- [ ] AI-powered recommendations

## 🤝 Contributing

This is a private project. For internal team members, please follow our contribution guidelines in [CONTRIBUTING.md](./docs/CONTRIBUTING.md).

---

**Built with ❤️ by the OnTheSpot Team**
