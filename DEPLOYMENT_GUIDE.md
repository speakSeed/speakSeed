# 🚀 SpeakSeed - Production Deployment Guide

Complete guide for deploying the SpeakSeed vocabulary learning app to production with CI/CD.

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Frontend Deployment (Vercel)](#frontend-deployment-vercel)
4. [Backend Deployment Options](#backend-deployment-options)
5. [Environment Variables](#environment-variables)
6. [CI/CD Setup](#cicd-setup)
7. [Post-Deployment](#post-deployment)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Overview

**Architecture:**

```
┌─────────────────┐         ┌─────────────────┐
│  Frontend (Vue) │◄────────┤  Backend (Laravel)│
│  Hosted on     │  REST   │  Hosted on       │
│  Vercel        │  API    │  Your Server     │
└─────────────────┘         └─────────────────┘
        │                           │
        │                           │
        ▼                           ▼
  Static Files              PostgreSQL Database
```

**Deployment Flow:**

1. Push to GitHub → Triggers CI/CD
2. GitHub Actions builds frontend
3. Deploys to Vercel automatically
4. Backend runs on your server

---

## 🛠️ Prerequisites

### 1. **Accounts Needed:**

- ✅ GitHub account (for code repository)
- ✅ Vercel account (for frontend hosting) - https://vercel.com
- ✅ Server for Laravel backend (VPS, DigitalOcean, AWS, etc.)

### 2. **Tools Required:**

- Git
- Node.js 18+
- PHP 8.1+
- Composer
- PostgreSQL 14+

---

## 🌐 Frontend Deployment (Vercel)

### Step 1: Push Code to GitHub

```bash
# Initialize git (if not already done)
cd /Users/shutenchik/projects/vocabulary
git init
git add .
git commit -m "Initial commit: SpeakSeed vocabulary app"

# Create GitHub repository and push
git remote add origin https://github.com/YOUR_USERNAME/speakseed.git
git branch -M main
git push -u origin main
```

### Step 2: Import Project to Vercel

1. **Go to Vercel:** https://vercel.com/new
2. **Import Git Repository:**

   - Click "Add New" → "Project"
   - Select your GitHub repository
   - Click "Import"

3. **Configure Project:**

   ```
   Framework Preset: Vite
   Root Directory: frontend
   Build Command: npm run build
   Output Directory: dist
   Install Command: npm ci
   ```

4. **Add Environment Variables:**

   - Go to "Environment Variables"
   - Add:
     ```
     VITE_API_URL=https://your-backend-domain.com/api
     ```
   - Click "Add"

5. **Deploy:**
   - Click "Deploy"
   - Wait for build to complete (~2 minutes)
   - Your app will be live at: `https://your-project.vercel.app`

### Step 3: Configure Custom Domain (Optional)

1. Go to Project Settings → Domains
2. Add your custom domain
3. Update DNS records as instructed
4. Wait for SSL certificate (~1 hour)

---

## 🖥️ Backend Deployment Options

### Option 1: Deploy on DigitalOcean (Recommended)

**1. Create Droplet:**

```bash
# Minimum specs: 2GB RAM, 1 vCPU, 25GB SSD
# Choose Ubuntu 22.04 LTS
```

**2. SSH into Server:**

```bash
ssh root@your-server-ip
```

**3. Install Dependencies:**

```bash
# Update system
apt update && apt upgrade -y

# Install PHP 8.2
add-apt-repository ppa:ondrej/php
apt update
apt install php8.2 php8.2-fpm php8.2-cli php8.2-common php8.2-mysql \
php8.2-pgsql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl \
php8.2-xml php8.2-bcmath -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install PostgreSQL
apt install postgresql postgresql-contrib -y

# Install Nginx
apt install nginx -y
```

**4. Setup Database:**

```bash
# Switch to postgres user
sudo -u postgres psql

# Create database and user
CREATE DATABASE vocabulary;
CREATE USER vocabulary_user WITH PASSWORD 'your_secure_password';
GRANT ALL PRIVILEGES ON DATABASE vocabulary TO vocabulary_user;
\q
```

**5. Clone and Setup Laravel:**

```bash
# Create web directory
mkdir -p /var/www/vocabulary
cd /var/www/vocabulary

# Clone your repository
git clone https://github.com/YOUR_USERNAME/speakseed.git .

# Install dependencies
cd backend
composer install --optimize-autoloader --no-dev

# Setup environment
cp .env.example .env
php artisan key:generate

# Edit .env file
nano .env
```

**6. Configure .env:**

```env
APP_NAME=SpeakSeed
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://api.yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=vocabulary
DB_USERNAME=vocabulary_user
DB_PASSWORD=your_secure_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

**7. Run Migrations:**

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**8. Configure Nginx:**

```bash
nano /etc/nginx/sites-available/vocabulary
```

**Nginx Configuration:**

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

**9. Enable Site:**

```bash
ln -s /etc/nginx/sites-available/vocabulary /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

**10. Setup SSL with Let's Encrypt:**

```bash
apt install certbot python3-certbot-nginx -y
certbot --nginx -d api.yourdomain.com
```

**11. Set Permissions:**

```bash
chown -R www-data:www-data /var/www/vocabulary
chmod -R 755 /var/www/vocabulary/backend/storage
chmod -R 755 /var/www/vocabulary/backend/bootstrap/cache
```

### Option 2: Deploy on AWS EC2

Similar steps to DigitalOcean, but:

1. Launch EC2 instance (t3.small or larger)
2. Configure security groups (ports 80, 443, 22)
3. Follow same setup steps as above

### Option 3: Deploy on Heroku

```bash
# Install Heroku CLI
curl https://cli-assets.heroku.com/install.sh | sh

# Login and create app
heroku login
heroku create speakseed-api

# Add PostgreSQL addon
heroku addons:create heroku-postgresql:hobby-dev

# Push to Heroku
git push heroku main

# Run migrations
heroku run php artisan migrate --seed
```

---

## 🔐 Environment Variables

### Frontend (Vercel)

Add in Vercel Dashboard → Project → Settings → Environment Variables:

```env
VITE_API_URL=https://api.yourdomain.com/api
```

### Backend (.env file)

```env
# Application
APP_NAME=SpeakSeed
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://api.yourdomain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=vocabulary
DB_USERNAME=vocabulary_user
DB_PASSWORD=your_secure_password

# Cache & Sessions
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# CORS (Allow your frontend domain)
CORS_ALLOWED_ORIGINS=https://your-frontend.vercel.app,https://yourdomain.com
```

---

## 🤖 CI/CD Setup

### Step 1: Get Vercel Tokens

1. **Install Vercel CLI:**

   ```bash
   npm install -g vercel
   ```

2. **Login to Vercel:**

   ```bash
   vercel login
   ```

3. **Link Project:**

   ```bash
   cd frontend
   vercel link
   ```

4. **Get Project Details:**

   ```bash
   # Get your ORG_ID and PROJECT_ID from:
   cat .vercel/project.json
   ```

5. **Create Vercel Token:**
   - Go to: https://vercel.com/account/tokens
   - Click "Create"
   - Name: "GitHub Actions"
   - Copy the token

### Step 2: Add GitHub Secrets

1. **Go to GitHub Repository:**

   - Settings → Secrets and variables → Actions
   - Click "New repository secret"

2. **Add These Secrets:**
   ```
   VERCEL_TOKEN=your_vercel_token
   VERCEL_ORG_ID=your_org_id (from project.json)
   VERCEL_PROJECT_ID=your_project_id (from project.json)
   VITE_API_URL=https://api.yourdomain.com/api
   ```

### Step 3: Enable GitHub Actions

1. Go to repository → Actions
2. Enable workflows
3. Push a commit to trigger deployment

### Step 4: Test CI/CD

```bash
# Make a small change
cd frontend
echo "// Test" >> src/App.vue

# Commit and push
git add .
git commit -m "Test CI/CD deployment"
git push origin main

# Watch deployment at:
# - GitHub Actions: https://github.com/YOUR_USERNAME/speakseed/actions
# - Vercel Dashboard: https://vercel.com/dashboard
```

---

## ✅ Post-Deployment

### 1. Health Check

**Frontend:**

```bash
curl https://your-frontend.vercel.app
# Should return HTML
```

**Backend:**

```bash
curl https://api.yourdomain.com/api/health
# Should return: {"status":"ok","timestamp":"..."}
```

### 2. Test API Connection

Open your frontend app and try:

1. Select a level
2. View words
3. Add a word
4. Check progress

### 3. Monitor Performance

**Vercel Analytics:**

- Go to Project → Analytics
- Enable Web Analytics
- Monitor page views, performance

**Backend Logs:**

```bash
# SSH into server
tail -f /var/www/vocabulary/backend/storage/logs/laravel.log
```

### 4. Setup Backups

**Database Backup Script:**

```bash
#!/bin/bash
# /root/backup_db.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"
mkdir -p $BACKUP_DIR

pg_dump vocabulary > $BACKUP_DIR/vocabulary_$DATE.sql
find $BACKUP_DIR -type f -mtime +7 -delete  # Keep 7 days

# Optional: Upload to S3
# aws s3 cp $BACKUP_DIR/vocabulary_$DATE.sql s3://your-bucket/backups/
```

**Add to Crontab:**

```bash
crontab -e

# Add line:
0 2 * * * /root/backup_db.sh  # Daily at 2 AM
```

---

## 🐛 Troubleshooting

### Frontend Issues

**Issue: Build fails on Vercel**

```
Solution:
1. Check build logs in Vercel dashboard
2. Ensure all dependencies are in package.json
3. Verify Node version (should be 18+)
4. Check VITE_API_URL is set correctly
```

**Issue: API calls fail (CORS errors)**

```
Solution:
1. Check backend .env has correct CORS settings
2. Add your Vercel domain to allowed origins
3. Clear browser cache
4. Check Network tab for actual error
```

**Issue: 404 on refresh**

```
Solution:
Already handled by vercel.json rewrite rules.
If still happening, ensure vercel.json is in frontend directory.
```

### Backend Issues

**Issue: 500 Internal Server Error**

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check Nginx logs
tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

**Issue: Database connection fails**

```bash
# Test PostgreSQL connection
psql -U vocabulary_user -d vocabulary

# Check PostgreSQL is running
systemctl status postgresql

# Verify credentials in .env
```

**Issue: Permissions errors**

```bash
# Reset permissions
cd /var/www/vocabulary/backend
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

### CI/CD Issues

**Issue: GitHub Actions fails**

```
Solution:
1. Check GitHub Actions logs
2. Verify all secrets are set correctly
3. Ensure VERCEL_TOKEN hasn't expired
4. Check vercel.json configuration
```

**Issue: Deployment succeeds but changes not visible**

```
Solution:
1. Clear browser cache
2. Check Vercel deployment URL matches your domain
3. Wait a few minutes for CDN propagation
4. Try incognito mode
```

---

## 📊 Performance Optimization

### Frontend

1. **Enable Vercel Edge Network:**

   - Already enabled by default
   - Uses global CDN

2. **Optimize Images:**

   - Use WebP format
   - Lazy load images
   - Use Vercel Image Optimization

3. **Enable Caching:**
   - Static assets cached automatically
   - API responses cached client-side

### Backend

1. **Enable OPcache:**

   ```bash
   # /etc/php/8.2/fpm/php.ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.max_accelerated_files=10000
   ```

2. **Use Redis for Caching:**

   ```bash
   apt install redis-server
   composer require predis/predis

   # Update .env
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   ```

3. **Queue Processing:**

   ```bash
   # Install Supervisor
   apt install supervisor

   # Configure worker
   nano /etc/supervisor/conf.d/laravel-worker.conf

   [program:laravel-worker]
   command=php /var/www/vocabulary/backend/artisan queue:work
   autostart=true
   autorestart=true
   user=www-data
   ```

---

## 🔒 Security Best Practices

### 1. **SSL/HTTPS:**

- ✅ Frontend: Auto SSL via Vercel
- ✅ Backend: Let's Encrypt SSL

### 2. **Environment Variables:**

- ✅ Never commit .env files
- ✅ Use strong passwords
- ✅ Rotate API keys regularly

### 3. **Firewall:**

```bash
# Enable UFW
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw enable
```

### 4. **Rate Limiting:**

Already configured in Laravel backend:

- API: 100 requests/minute
- Dictionary API: Smart caching

### 5. **Regular Updates:**

```bash
# Update system monthly
apt update && apt upgrade -y

# Update Composer dependencies
composer update

# Update npm dependencies
npm update
```

---

## 📈 Monitoring & Analytics

### 1. **Vercel Analytics:**

- Page views
- Performance metrics
- Error tracking

### 2. **Backend Monitoring:**

- Laravel Telescope (development)
- Error logs
- Database performance

### 3. **Uptime Monitoring:**

Use services like:

- UptimeRobot (free)
- Pingdom
- StatusCake

---

## 🎉 Deployment Checklist

- [ ] Code pushed to GitHub
- [ ] Vercel project created and linked
- [ ] Frontend environment variables set
- [ ] Backend server provisioned
- [ ] Database created and configured
- [ ] Laravel backend deployed
- [ ] Nginx configured with SSL
- [ ] Domain names pointed correctly
- [ ] GitHub Actions secrets added
- [ ] CI/CD pipeline tested
- [ ] Health checks pass
- [ ] API connection works
- [ ] Backups configured
- [ ] Monitoring enabled
- [ ] Documentation updated

---

## 🚀 Your App is Live!

**Frontend:** https://your-project.vercel.app
**Backend API:** https://api.yourdomain.com

**Next Steps:**

1. Share with users
2. Monitor analytics
3. Collect feedback
4. Iterate and improve

---

## 📞 Support

**Common Commands:**

```bash
# Frontend (Local)
npm run dev          # Development
npm run build        # Production build
npm run preview      # Preview build

# Backend (Server)
php artisan migrate  # Run migrations
php artisan cache:clear  # Clear cache
php artisan config:cache  # Cache config
systemctl restart nginx  # Restart Nginx

# Deployment
git push origin main  # Trigger CI/CD
vercel --prod        # Manual deploy
```

---

## 📚 Additional Resources

- **Vercel Docs:** https://vercel.com/docs
- **Laravel Docs:** https://laravel.com/docs
- **Vue.js Docs:** https://vuejs.org/guide/
- **PostgreSQL Docs:** https://www.postgresql.org/docs/

---

**Good luck with your deployment! 🎊**

Your vocabulary learning app is ready to help thousands of users master English! 🚀📚
