# OnTheSpot - Quick Start Guide

Get OnTheSpot running locally in 5 minutes!

## Prerequisites

- Docker & Docker Compose installed
- OR: PHP 8.2+, Node.js 18+, PostgreSQL 15+

## Quick Start with Docker (Recommended)

### 1. Clone & Configure

```bash
# Clone the repository
git clone <repository-url>
cd onthespot

# Set up environment files
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

# Edit backend/.env with your database password
nano backend/.env
# Change DB_PASSWORD to something secure
```

### 2. Start the Application

```bash
# Build and start all services
docker-compose up -d

# Wait for services to be ready (30 seconds)

# Generate application key
docker-compose exec backend php artisan key:generate

# Run database migrations
docker-compose exec backend php artisan migrate

# Seed initial data
docker-compose exec backend php artisan db:seed --class=CategorySeeder
```

### 3. Access the Application

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000/api
- **Database**: localhost:5432

### 4. Create Your First Account

1. Open http://localhost:3000
2. Click "Sign Up"
3. Choose account type:
   - **Consumer**: Browse and review brands
   - **Business**: Manage your brand and inventory
4. Complete registration

### 5. (Business Accounts) Create Your Brand

1. Login and go to "My Brands"
2. Click "+ Add New Brand"
3. Fill in your brand information
4. Click "Manage" to add products/menu items

## Quick Start Without Docker

### 1. Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Create database
createdb onthespot

# Run migrations
php artisan migrate
php artisan db:seed --class=CategorySeeder

# Start server
php artisan serve
```

### 2. Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev
```

### 3. Access

- Frontend: http://localhost:3000
- Backend: http://localhost:8000

## Default Test Data

After seeding, you'll have these categories:
- Fashion 👗
- Restaurant 🍽️
- Cafe ☕
- Beauty & Spa 💅

## Common Commands

```bash
# View logs
docker-compose logs -f

# Stop all services
docker-compose down

# Restart services
docker-compose restart

# Access database
docker-compose exec postgres psql -U postgres -d onthespot

# Run migrations
docker-compose exec backend php artisan migrate

# Clear caches
docker-compose exec backend php artisan cache:clear
docker-compose exec backend php artisan config:clear
```

## Next Steps

1. **Configure Google Maps** (optional):
   - Get API key from Google Cloud Console
   - Add to `backend/.env`: `GOOGLE_MAPS_API_KEY=your-key`
   - Restart backend: `docker-compose restart backend`

2. **Explore Features**:
   - Search and browse brands
   - Create a business account
   - Add your brand and inventory
   - Leave reviews

3. **Customize**:
   - Update branding in `frontend/src/components/Navbar.vue`
   - Modify colors in `frontend/tailwind.config.js`
   - Add more categories in database seeder

## Troubleshooting

### Port Already in Use

```bash
# Change ports in docker-compose.yml
# Frontend: "3001:3000" instead of "3000:3000"
# Backend: "8001:8000" instead of "8000:8000"
```

### Database Connection Error

```bash
# Check if postgres container is running
docker-compose ps

# Restart postgres
docker-compose restart postgres
```

### Frontend Can't Reach Backend

```bash
# Check VITE_API_URL in frontend/.env
# Should be: http://localhost:8000/api

# Check CORS in backend/config/cors.php
```

## Need Help?

- Read the full [README.md](./README.md)
- Check [DEPLOYMENT.md](./docs/DEPLOYMENT.md) for production setup
- Review API documentation: http://localhost:8000/api

---

**Happy Building! 🚀**
