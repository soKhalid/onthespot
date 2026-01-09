# OnTheSpot Deployment Guide

This guide provides step-by-step instructions for deploying OnTheSpot to a production environment.

## Table of Contents
1. [Server Requirements](#server-requirements)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Option 1: Docker Deployment](#option-1-docker-deployment-recommended)
4. [Option 2: Traditional Server Deployment](#option-2-traditional-server-deployment)
5. [SSL Certificate Setup](#ssl-certificate-setup)
6. [Database Seeding](#database-seeding)
7. [Post-Deployment](#post-deployment)
8. [Troubleshooting](#troubleshooting)

---

## Server Requirements

### Minimum Requirements
- **CPU**: 2 cores
- **RAM**: 4GB
- **Storage**: 20GB SSD
- **OS**: Ubuntu 20.04 LTS or later (recommended)

### Software Requirements
- Docker 20.10+ and Docker Compose 2.0+ (for Docker deployment)
- OR:
  - PHP 8.2+
  - PostgreSQL 15+
  - Node.js 18+
  - Nginx 1.18+

### Domain & DNS
- A registered domain name
- DNS configured to point to your server's IP address

---

## Pre-Deployment Checklist

- [ ] Server provisioned and accessible via SSH
- [ ] Domain name configured and DNS propagated
- [ ] SSL certificate obtained (Let's Encrypt recommended)
- [ ] Database credentials prepared
- [ ] Google Maps API key obtained
- [ ] Backup strategy planned

---

## Option 1: Docker Deployment (Recommended)

This is the easiest and most consistent way to deploy OnTheSpot.

### Step 1: Install Docker

```bash
# Update package index
sudo apt update

# Install prerequisites
sudo apt install -y apt-transport-https ca-certificates curl software-properties-common

# Add Docker's official GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Add Docker repository
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Start and enable Docker
sudo systemctl start docker
sudo systemctl enable docker

# Add your user to docker group
sudo usermod -aG docker $USER
```

Log out and back in for the group change to take effect.

### Step 2: Clone the Repository

```bash
cd /var/www
sudo git clone <your-repository-url> onthespot
cd onthespot
sudo chown -R $USER:$USER .
```

### Step 3: Configure Environment Variables

```bash
# Backend configuration
cp backend/.env.example backend/.env
nano backend/.env
```

Update the following values in `backend/.env`:
```env
APP_NAME=OnTheSpot
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_KEY=  # Will be generated later

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=onthespot
DB_USERNAME=postgres
DB_PASSWORD=your-secure-password-here

GOOGLE_MAPS_API_KEY=your-google-maps-api-key
```

```bash
# Frontend configuration
nano frontend/.env
```

Update `frontend/.env`:
```env
VITE_API_URL=https://your-domain.com/api
```

### Step 4: Generate Application Key

```bash
docker-compose run --rm backend php artisan key:generate
```

### Step 5: Build and Start Containers

```bash
# Build the images
docker-compose build

# Start the containers
docker-compose up -d
```

### Step 6: Run Database Migrations

```bash
# Run migrations
docker exec onthespot_backend php artisan migrate --force

# Seed initial data (categories)
docker exec onthespot_backend php artisan db:seed
```

### Step 7: Set Permissions

```bash
docker exec onthespot_backend chown -R www-data:www-data storage bootstrap/cache
docker exec onthespot_backend chmod -R 775 storage bootstrap/cache
```

### Step 8: Configure Nginx for SSL

```bash
# Update nginx configuration with your domain
nano nginx/nginx.conf
```

See [SSL Certificate Setup](#ssl-certificate-setup) section below.

---

## Option 2: Traditional Server Deployment

### Step 1: Install Required Software

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-pgsql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath

# Install PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx
```

### Step 2: Configure PostgreSQL

```bash
# Switch to postgres user
sudo -u postgres psql

# In PostgreSQL shell:
CREATE DATABASE onthespot;
CREATE USER onthespot_user WITH PASSWORD 'your-secure-password';
GRANT ALL PRIVILEGES ON DATABASE onthespot TO onthespot_user;
\q
```

### Step 3: Deploy Backend

```bash
# Clone repository
cd /var/www
sudo git clone <your-repository-url> onthespot
cd onthespot/backend

# Install dependencies
composer install --optimize-autoloader --no-dev

# Set up environment
cp .env.example .env
nano .env  # Edit with your settings

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate --force
php artisan db:seed

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Step 4: Deploy Frontend

```bash
cd /var/www/onthespot/frontend

# Install dependencies
npm install

# Build for production
npm run build

# The built files will be in dist/ directory
```

### Step 5: Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/onthespot
```

Add the following configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/onthespot/frontend/dist;

    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location /api {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location /storage {
        alias /var/www/onthespot/backend/storage/app/public;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/onthespot /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 6: Set Up PHP-FPM as System Service

```bash
# Start PHP-FPM
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm

# For Laravel, you might want to use a process manager like Supervisor
sudo apt install -y supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/onthespot.conf
```

Add:
```ini
[program:onthespot-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/onthespot/backend/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/onthespot/backend/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start onthespot-worker:*
```

---

## SSL Certificate Setup

### Using Let's Encrypt (Recommended)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Certificates will be automatically configured in Nginx
# Auto-renewal is set up automatically
```

### Manual Certificate

If you have a certificate from another provider:

```bash
# Copy certificates to nginx/ssl/
mkdir -p nginx/ssl
cp your-cert.crt nginx/ssl/cert.pem
cp your-key.key nginx/ssl/key.pem

# Update nginx.conf to enable SSL
nano nginx/nginx.conf
# Uncomment the HTTPS server block and update paths
```

---

## Database Seeding

### Seed Initial Categories

```bash
# Docker deployment
docker exec onthespot_backend php artisan db:seed --class=CategorySeeder

# Traditional deployment
cd /var/www/onthespot/backend
php artisan db:seed --class=CategorySeeder
```

Create `backend/database/seeders/CategorySeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Discover local fashion boutiques and clothing stores',
                'icon' => '👗',
                'is_active' => true,
            ],
            [
                'name' => 'Restaurant',
                'slug' => 'restaurant',
                'description' => 'Find the best local restaurants and dining experiences',
                'icon' => '🍽️',
                'is_active' => true,
            ],
            [
                'name' => 'Cafe',
                'slug' => 'cafe',
                'description' => 'Explore cozy cafes and coffee shops',
                'icon' => '☕',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
```

---

## Post-Deployment

### 1. Verify Installation

```bash
# Check if all containers are running (Docker deployment)
docker-compose ps

# Check application logs
docker-compose logs -f backend
docker-compose logs -f frontend

# Test API endpoint
curl https://your-domain.com/api/categories
```

### 2. Create Admin/Test Accounts

Visit `https://your-domain.com/register` and create:
- A consumer account for testing
- A business account for testing brand management

### 3. Set Up Monitoring

Install basic monitoring tools:

```bash
# Install monitoring tools
sudo apt install -y htop iotop nethogs

# Set up log rotation
sudo nano /etc/logrotate.d/onthespot
```

Add:
```
/var/www/onthespot/backend/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 4. Configure Backups

Create backup script:

```bash
sudo nano /usr/local/bin/backup-onthespot.sh
```

```bash
#!/bin/bash
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/onthespot"

mkdir -p $BACKUP_DIR

# Backup database
docker exec onthespot_postgres pg_dump -U postgres onthespot | gzip > $BACKUP_DIR/db_$TIMESTAMP.sql.gz

# Backup uploaded files
tar -czf $BACKUP_DIR/storage_$TIMESTAMP.tar.gz /var/www/onthespot/backend/storage/app

# Keep only last 7 days
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

echo "Backup completed: $TIMESTAMP"
```

```bash
sudo chmod +x /usr/local/bin/backup-onthespot.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-onthespot.sh
```

### 5. Configure Firewall

```bash
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP
sudo ufw allow 443/tcp  # HTTPS
sudo ufw enable
```

### 6. Performance Optimization

```bash
# For Docker deployment
docker exec onthespot_backend php artisan config:cache
docker exec onthespot_backend php artisan route:cache
docker exec onthespot_backend php artisan view:cache

# For traditional deployment
cd /var/www/onthespot/backend
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Troubleshooting

### Issue: Database Connection Failed

**Solution:**
```bash
# Check PostgreSQL is running
docker ps | grep postgres
# or
sudo systemctl status postgresql

# Check credentials in .env file
nano backend/.env

# Test connection
docker exec onthespot_postgres psql -U postgres -d onthespot -c "SELECT 1;"
```

### Issue: Frontend Can't Connect to Backend

**Solution:**
```bash
# Check VITE_API_URL in frontend/.env
nano frontend/.env

# Check CORS settings in backend/config/cors.php
# Ensure frontend URL is in allowed origins

# Check Nginx proxy configuration
sudo nginx -t
sudo systemctl reload nginx
```

### Issue: 500 Internal Server Error

**Solution:**
```bash
# Check Laravel logs
docker exec onthespot_backend tail -f storage/logs/laravel.log

# Check permissions
docker exec onthespot_backend ls -la storage
docker exec onthespot_backend chown -R www-data:www-data storage
```

### Issue: Assets Not Loading

**Solution:**
```bash
# Rebuild frontend
cd frontend
npm run build

# Clear browser cache
# Check network tab in browser DevTools for 404 errors
```

### Issue: Migration Errors

**Solution:**
```bash
# Reset database (⚠️ WARNING: This will delete all data)
docker exec onthespot_backend php artisan migrate:fresh --force

# Or rollback and re-run
docker exec onthespot_backend php artisan migrate:rollback
docker exec onthespot_backend php artisan migrate --force
```

---

## Maintenance Commands

### Update Application

```bash
cd /var/www/onthespot

# Pull latest changes
git pull origin main

# Update backend
docker-compose run --rm backend composer install --optimize-autoloader --no-dev
docker exec onthespot_backend php artisan migrate --force
docker exec onthespot_backend php artisan config:cache

# Update frontend
cd frontend
npm install
npm run build
cd ..

# Restart containers
docker-compose restart
```

### View Logs

```bash
# All logs
docker-compose logs -f

# Backend only
docker-compose logs -f backend

# Database only
docker-compose logs -f postgres
```

### Database Management

```bash
# Access database
docker exec -it onthespot_postgres psql -U postgres -d onthespot

# Backup database
docker exec onthespot_postgres pg_dump -U postgres onthespot > backup.sql

# Restore database
cat backup.sql | docker exec -i onthespot_postgres psql -U postgres -d onthespot
```

---

## Security Checklist

- [ ] SSL certificate installed and working
- [ ] `.env` files have secure passwords
- [ ] `APP_DEBUG=false` in production
- [ ] Database backups configured
- [ ] Firewall rules configured
- [ ] Server SSH configured with key-based authentication
- [ ] Regular security updates scheduled
- [ ] Google Maps API key has domain restrictions
- [ ] File upload limits configured

---

## Support

For issues or questions:
- Check logs: `docker-compose logs`
- Review this guide
- Contact: hello@onthespot.com

---

**Congratulations! Your OnTheSpot application should now be live! 🎉**
