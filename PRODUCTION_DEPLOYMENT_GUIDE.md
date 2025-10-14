# Production Deployment Guide for Vocabulary App

## Overview

Your app consists of:

- **Backend**: Laravel 11 (PHP 8.2) API with PostgreSQL database
- **Frontend**: Vue 3 + TypeScript + Vite SPA
- **Architecture**: Separate backend API and frontend client

---

## 🚀 Deployment Options Comparison

| Platform                    | Backend       | Frontend | Database | Cost       | Difficulty | Best For                     |
| --------------------------- | ------------- | -------- | -------- | ---------- | ---------- | ---------------------------- |
| **Railway**                 | ✅            | ✅       | ✅       | $5-20/mo   | ⭐⭐       | Full-stack apps, easy setup  |
| **Render**                  | ✅            | ✅       | ✅       | $7-25/mo   | ⭐⭐       | Modern deployment, free tier |
| **DigitalOcean**            | ✅            | ✅       | ✅       | $12-50/mo  | ⭐⭐⭐     | Full control, scalable       |
| **AWS (Elastic Beanstalk)** | ✅            | ✅       | ✅       | $20-100/mo | ⭐⭐⭐⭐   | Enterprise, highly scalable  |
| **Vercel + Supabase**       | ⚠️ Serverless | ✅       | ✅       | $0-20/mo   | ⭐⭐       | JAMstack, serverless         |
| **Netlify + Heroku**        | ✅            | ✅       | ✅       | $7-16/mo   | ⭐⭐       | Split hosting                |
| **VPS (Linode/Vultr)**      | ✅            | ✅       | ✅       | $5-20/mo   | ⭐⭐⭐⭐   | Full control, manual setup   |
| **Docker + Any Cloud**      | ✅            | ✅       | ✅       | Varies     | ⭐⭐⭐     | Containerized, portable      |

---

## 📋 Option 1: Railway (Recommended - Easiest)

Railway is perfect for full-stack apps with automatic deployment from GitHub.

### Prerequisites

- GitHub account
- Railway account (https://railway.app)

### Step 1: Prepare Your Repository

```bash
# Make sure your code is committed
git add .
git commit -m "Prepare for deployment"
git push origin main
```

### Step 2: Create Railway Project

1. Go to https://railway.app and sign in with GitHub
2. Click "New Project" → "Deploy from GitHub repo"
3. Select your vocabulary repository

### Step 3: Deploy Backend

1. In Railway dashboard, click "New"
2. Select "Database" → "PostgreSQL"
3. Note the database credentials (will be auto-provided)
4. Click "New" → "GitHub Repo" → Select your backend directory
5. Add environment variables:

```bash
APP_NAME="Vocabulary API"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-backend.railway.app

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

SESSION_DRIVER=cookie
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

CORS_ALLOWED_ORIGINS=https://your-frontend.vercel.app
```

6. Configure build settings:

   - **Root Directory**: `/backend`
   - **Build Command**: `composer install --no-dev --optimize-autoloader`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

7. Deploy and get your backend URL

### Step 4: Deploy Frontend on Vercel

1. Go to https://vercel.com and sign in with GitHub
2. Click "New Project" → Select your repository
3. Configure project:

   - **Root Directory**: `frontend`
   - **Framework Preset**: Vite
   - **Build Command**: `npm run build`
   - **Output Directory**: `dist`

4. Add environment variable:

```bash
VITE_API_URL=https://your-backend.railway.app
```

5. Deploy!

### Step 5: Run Migrations

In Railway dashboard:

1. Go to your backend service
2. Click "Variables" → Add `RUN_MIGRATIONS=true`
3. Or use Railway CLI:

```bash
railway run php artisan migrate --force
railway run php artisan db:seed
```

### Cost: ~$5-10/month

- PostgreSQL: $5/month
- Backend: Free tier or $5/month
- Frontend on Vercel: Free

---

## 📋 Option 2: Render (Great Free Tier)

Render offers excellent free tier and is very easy to use.

### Step 1: Create Render Account

Sign up at https://render.com

### Step 2: Deploy PostgreSQL Database

1. Click "New" → "PostgreSQL"
2. Name: `vocabulary-db`
3. Plan: Free or Starter ($7/mo)
4. Create Database
5. Note the Internal/External Database URL

### Step 3: Deploy Backend

1. Click "New" → "Web Service"
2. Connect your GitHub repository
3. Configure:

   - **Name**: `vocabulary-api`
   - **Root Directory**: `backend`
   - **Environment**: PHP
   - **Build Command**:

   ```bash
   composer install --no-dev --optimize-autoloader
   ```

   - **Start Command**:

   ```bash
   php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
   ```

   - **Plan**: Free or Starter ($7/mo)

4. Add environment variables (from Render dashboard):

```bash
APP_NAME="Vocabulary API"
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_KEY
APP_DEBUG=false
APP_URL=https://vocabulary-api.onrender.com

DB_CONNECTION=pgsql
DB_HOST=<from_database_internal_url>
DB_PORT=5432
DB_DATABASE=<from_database>
DB_USERNAME=<from_database>
DB_PASSWORD=<from_database>

SESSION_DRIVER=cookie
CACHE_DRIVER=file
```

5. Deploy!

### Step 4: Deploy Frontend

1. Click "New" → "Static Site"
2. Connect your repository
3. Configure:

   - **Name**: `vocabulary-app`
   - **Root Directory**: `frontend`
   - **Build Command**: `npm run build`
   - **Publish Directory**: `dist`

4. Add environment variable:

```bash
VITE_API_URL=https://vocabulary-api.onrender.com
```

5. Add redirect rule (create `frontend/_redirects` file):

```
/*  /index.html  200
```

6. Deploy!

### Cost:

- **Free Tier**: $0 (with limitations: backend sleeps after 15 min inactivity)
- **Paid**: ~$7-14/month for always-on services

---

## 📋 Option 3: DigitalOcean App Platform

Professional platform with great developer experience.

### Step 1: Create DigitalOcean Account

Sign up at https://www.digitalocean.com

### Step 2: Create Managed Database

1. Go to "Databases" → "Create Database Cluster"
2. Choose PostgreSQL 15
3. Select plan (Basic $15/mo or Starter $12/mo)
4. Select region close to your users
5. Create and note connection details

### Step 3: Create App

1. Go to "Apps" → "Create App"
2. Connect GitHub repository
3. DigitalOcean will auto-detect both frontend and backend

### Step 4: Configure Backend Component

- **Type**: Web Service
- **Source Directory**: `/backend`
- **Build Command**:

```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- **Run Command**:

```bash
php artisan migrate --force && heroku-php-apache2 public/
```

- **Environment Variables**:

```bash
APP_NAME="Vocabulary API"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=${APP_URL}

DB_CONNECTION=pgsql
DB_HOST=${db.HOSTNAME}
DB_PORT=${db.PORT}
DB_DATABASE=${db.DATABASE}
DB_USERNAME=${db.USERNAME}
DB_PASSWORD=${db.PASSWORD}
DB_SSL_MODE=require

SESSION_DRIVER=cookie
CACHE_DRIVER=file
```

### Step 5: Configure Frontend Component

- **Type**: Static Site
- **Source Directory**: `/frontend`
- **Build Command**: `npm run build`
- **Output Directory**: `dist`

- **Environment Variables**:

```bash
VITE_API_URL=${backend.PUBLIC_URL}
```

### Step 6: Deploy

Click "Create Resources" and deploy!

### Cost: ~$17-50/month

- Database: $12-15/month
- Backend: $5/month
- Frontend: Free (static)

---

## 📋 Option 4: AWS (Advanced - Most Scalable)

Best for enterprise applications requiring maximum scalability.

### Prerequisites

- AWS Account
- AWS CLI installed
- EB CLI installed: `pip install awsebcli`

### Step 1: Set Up RDS Database

1. Go to AWS Console → RDS
2. Create PostgreSQL database
3. Choose instance size (db.t3.micro for start)
4. Enable public access (for initial setup)
5. Note endpoint, username, password

### Step 2: Deploy Backend with Elastic Beanstalk

```bash
cd backend

# Initialize EB
eb init -p php-8.2 vocabulary-api --region us-east-1

# Create environment
eb create vocabulary-api-prod

# Set environment variables
eb setenv \
  APP_NAME="Vocabulary API" \
  APP_ENV=production \
  APP_KEY=base64:YOUR_KEY \
  APP_DEBUG=false \
  DB_CONNECTION=pgsql \
  DB_HOST=your-rds-endpoint.rds.amazonaws.com \
  DB_PORT=5432 \
  DB_DATABASE=vocabulary \
  DB_USERNAME=postgres \
  DB_PASSWORD=yourpassword

# Deploy
eb deploy
```

### Step 3: Run Migrations

```bash
eb ssh
cd /var/app/current
php artisan migrate --force
exit
```

### Step 4: Deploy Frontend to S3 + CloudFront

```bash
cd frontend

# Build frontend
npm run build

# Create S3 bucket
aws s3 mb s3://vocabulary-app-frontend

# Configure bucket for static website
aws s3 website s3://vocabulary-app-frontend \
  --index-document index.html \
  --error-document index.html

# Upload files
aws s3 sync dist/ s3://vocabulary-app-frontend --delete

# Make public
aws s3api put-bucket-policy --bucket vocabulary-app-frontend \
  --policy '{
    "Version": "2012-10-17",
    "Statement": [{
      "Sid": "PublicReadGetObject",
      "Effect": "Allow",
      "Principal": "*",
      "Action": "s3:GetObject",
      "Resource": "arn:aws:s3:::vocabulary-app-frontend/*"
    }]
  }'
```

### Step 5: Set Up CloudFront (Optional but Recommended)

1. Go to CloudFront in AWS Console
2. Create distribution
3. Origin: Your S3 bucket
4. Default root object: `index.html`
5. Custom error response: 404 → /index.html (200)

### Cost: ~$20-100/month

- RDS: $15-50/month
- Elastic Beanstalk: $15-50/month
- S3 + CloudFront: $1-10/month

---

## 📋 Option 5: VPS (DigitalOcean/Linode/Vultr)

Full control with manual configuration. Best if you want complete customization.

### Step 1: Create VPS

1. Create account on DigitalOcean, Linode, or Vultr
2. Create Droplet/Instance:
   - Ubuntu 22.04 LTS
   - At least 2GB RAM
   - Select region

### Step 2: Initial Server Setup

```bash
# SSH into server
ssh root@your_server_ip

# Update system
apt update && apt upgrade -y

# Create sudo user
adduser vocabulary
usermod -aG sudo vocabulary
su - vocabulary
```

### Step 3: Install Dependencies

```bash
# Install Nginx
sudo apt install nginx -y

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2-fpm php8.2-cli php8.2-pgsql php8.2-mbstring \
  php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip -y

# Install PostgreSQL
sudo apt install postgresql postgresql-contrib -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js & npm
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y
```

### Step 4: Set Up Database

```bash
sudo -u postgres psql

CREATE DATABASE vocabulary;
CREATE USER vocabulary_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE vocabulary TO vocabulary_user;
\q
```

### Step 5: Deploy Backend

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/yourusername/vocabulary.git
cd vocabulary/backend

# Install dependencies
sudo composer install --no-dev --optimize-autoloader

# Set permissions
sudo chown -R www-data:www-data /var/www/vocabulary
sudo chmod -R 755 /var/www/vocabulary/backend/storage
sudo chmod -R 755 /var/www/vocabulary/backend/bootstrap/cache

# Create .env file
sudo nano .env
# Add all environment variables

# Generate key
sudo php artisan key:generate

# Run migrations
sudo php artisan migrate --force
sudo php artisan db:seed

# Cache config
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

### Step 6: Configure Nginx for Backend

```bash
sudo nano /etc/nginx/sites-available/vocabulary-api
```

Add:

```nginx
server {
    listen 80;
    server_name api.yourdomain.com;
    root /var/www/vocabulary/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/vocabulary-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Step 7: Deploy Frontend

```bash
cd /var/www/vocabulary/frontend

# Update API URL in .env or vite config
echo "VITE_API_URL=https://api.yourdomain.com" > .env.production

# Install dependencies and build
npm install
npm run build

# Create Nginx config for frontend
sudo nano /etc/nginx/sites-available/vocabulary-frontend
```

Add:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/vocabulary/frontend/dist;

    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/vocabulary-frontend /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Step 8: Set Up SSL with Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
sudo certbot --nginx -d api.yourdomain.com
```

### Step 9: Set Up Automatic Deployment (Optional)

```bash
# Install webhook or set up GitHub Actions
# Or use simple pull script:
cd /var/www/vocabulary
sudo nano deploy.sh
```

Add:

```bash
#!/bin/bash
cd /var/www/vocabulary

# Pull latest code
git pull origin main

# Update backend
cd backend
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update frontend
cd ../frontend
npm install
npm run build

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx

echo "Deployment complete!"
```

```bash
sudo chmod +x deploy.sh
```

### Cost: $5-20/month for VPS

---

## 📋 Option 6: Docker Deployment (Portable)

Deploy your containerized app anywhere.

### Step 1: Update docker-compose.yml for Production

Create `docker-compose.prod.yml`:

```yaml
version: "3.8"

services:
  app:
    build:
      context: ./backend
      dockerfile: Dockerfile
    container_name: vocabulary-api
    restart: always
    working_dir: /var/www
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_HOST=db
      - DB_DATABASE=vocabulary
      - DB_USERNAME=postgres
      - DB_PASSWORD=${DB_PASSWORD}
    volumes:
      - ./backend:/var/www
      - ./backend/storage:/var/www/storage
    networks:
      - vocabulary-network
    depends_on:
      - db

  nginx:
    image: nginx:alpine
    container_name: vocabulary-nginx
    restart: always
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./backend:/var/www
      - ./backend/nginx.conf:/etc/nginx/conf.d/default.conf
      - ./frontend/dist:/usr/share/nginx/html
      - ./nginx/ssl:/etc/nginx/ssl
    networks:
      - vocabulary-network
    depends_on:
      - app

  db:
    image: postgres:15-alpine
    container_name: vocabulary-db
    restart: always
    environment:
      POSTGRES_DB: vocabulary
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    ports:
      - "5432:5432"
    volumes:
      - dbdata:/var/lib/postgresql/data
    networks:
      - vocabulary-network

networks:
  vocabulary-network:
    driver: bridge

volumes:
  dbdata:
```

### Step 2: Deploy on Any Cloud with Docker

```bash
# On your server (after Docker is installed)
git clone your-repo
cd vocabulary

# Set environment variables
echo "DB_PASSWORD=your_secure_password" > .env

# Build and start
docker-compose -f docker-compose.prod.yml up -d

# Run migrations
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed
```

---

## 🔧 Pre-Deployment Checklist

### Backend

- [ ] Set `APP_DEBUG=false` in production
- [ ] Generate strong `APP_KEY`
- [ ] Configure CORS properly
- [ ] Set up proper database credentials
- [ ] Enable caching (config, routes, views)
- [ ] Set up error logging
- [ ] Configure session driver (database or redis for multiple servers)
- [ ] Set up queue driver if using queues
- [ ] Test all API endpoints

### Frontend

- [ ] Update API URL to production backend
- [ ] Build for production (`npm run build`)
- [ ] Test build locally (`npm run preview`)
- [ ] Configure SPA redirects
- [ ] Set up proper error handling
- [ ] Enable compression
- [ ] Set cache headers for assets

### Database

- [ ] Create production database
- [ ] Run migrations
- [ ] Seed initial data if needed
- [ ] Set up automated backups
- [ ] Configure connection pooling

### Security

- [ ] Set up SSL/HTTPS
- [ ] Configure firewall rules
- [ ] Use environment variables for secrets
- [ ] Enable rate limiting
- [ ] Set up security headers
- [ ] Configure CORS properly
- [ ] Validate all user inputs
- [ ] Use prepared statements (Laravel does this by default)

### Monitoring

- [ ] Set up error tracking (Sentry, Bugsnag)
- [ ] Configure logging
- [ ] Set up uptime monitoring
- [ ] Configure performance monitoring
- [ ] Set up alerts for critical errors

---

## 🎯 Recommended Deployment Strategy

### For Beginners:

**Railway + Vercel** (Easiest, ~$5-10/mo)

### For Small Projects:

**Render** (Free tier or $7-14/mo, very easy)

### For Professional Projects:

**DigitalOcean App Platform** ($17-50/mo, managed)

### For Enterprise:

**AWS Elastic Beanstalk + RDS + CloudFront** ($20-100+/mo, most scalable)

### For Full Control:

**VPS with manual setup** ($5-20/mo, requires DevOps knowledge)

---

## 🚨 Common Issues & Solutions

### Issue: 500 Server Error

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Common fixes:
php artisan config:clear
php artisan cache:clear
chmod -R 755 storage bootstrap/cache
```

### Issue: Database Connection Failed

- Check database credentials in .env
- Verify database host (use internal hostname in production)
- Check if database port is open
- Verify SSL settings if required

### Issue: CORS Errors

Update `backend/config/cors.php`:

```php
'allowed_origins' => [
    env('FRONTEND_URL', 'http://localhost:5173'),
],
```

### Issue: Assets Not Loading

- Check VITE_API_URL in frontend
- Verify Nginx/Apache configuration
- Check build output directory
- Verify file permissions

---

## 📞 Support

If you encounter issues:

1. Check platform-specific documentation
2. Review error logs
3. Search Stack Overflow
4. Check Laravel documentation
5. Review Vue.js deployment guide

---

## 📝 Next Steps After Deployment

1. **Set up monitoring**: Use services like Sentry, LogRocket
2. **Configure backups**: Automate database backups
3. **Set up CI/CD**: Use GitHub Actions for automated deployments
4. **Performance optimization**: Enable Redis for caching
5. **CDN**: Use CloudFlare or similar for static assets
6. **Scaling**: Configure load balancers if needed

---

Good luck with your deployment! 🚀
