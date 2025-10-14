# Deployment Configuration Guide

## Environment Variables Setup

### Backend Environment Variables

#### Required Variables (All Platforms)

```bash
# Application
APP_NAME="Vocabulary API"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-backend-url.com

# Logging
LOG_CHANNEL=stderr
LOG_LEVEL=error

# Database (adjust based on your platform)
DB_CONNECTION=pgsql
DB_HOST=your-database-host
DB_PORT=5432
DB_DATABASE=vocabulary
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Session & Cache
SESSION_DRIVER=cookie
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# CORS (IMPORTANT: Update with your frontend URL)
CORS_ALLOWED_ORIGINS=https://your-frontend-url.com
SANCTUM_STATEFUL_DOMAINS=your-frontend-url.com
```

#### Platform-Specific Database Configuration

**Railway:**

```bash
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}
```

**Render:**

```bash
# Use the internal database URL from your Render PostgreSQL instance
DB_HOST=<from-internal-url>
DB_PORT=5432
DB_DATABASE=<database-name>
DB_USERNAME=<username>
DB_PASSWORD=<password>
```

**DigitalOcean:**

```bash
DB_HOST=${db.HOSTNAME}
DB_PORT=${db.PORT}
DB_DATABASE=${db.DATABASE}
DB_USERNAME=${db.USERNAME}
DB_PASSWORD=${db.PASSWORD}
DB_SSL_MODE=require
```

**AWS RDS:**

```bash
DB_HOST=your-rds-endpoint.rds.amazonaws.com
DB_PORT=5432
DB_DATABASE=vocabulary
DB_USERNAME=postgres
DB_PASSWORD=your-secure-password
```

---

### Frontend Environment Variables

#### Required Variables

```bash
# API URL (IMPORTANT: Update with your backend URL)
VITE_API_URL=https://your-backend-url.com

# Optional
VITE_APP_NAME="Vocabulary Trainer"
VITE_APP_VERSION="1.0.0"
```

---

## Step-by-Step Configuration

### Step 1: Generate APP_KEY

You need to generate a secure application key for Laravel.

**Option A: Locally**

```bash
cd backend
php artisan key:generate --show
```

**Option B: On Platform (after initial deployment)**

- Railway: Open Shell → `php artisan key:generate --show`
- Render: Connect Shell → `php artisan key:generate --show`
- AWS EB: `eb ssh` → `php artisan key:generate --show`

Copy the output (starts with `base64:`) and use it for `APP_KEY`.

---

### Step 2: Configure Database

#### For Railway:

1. Create PostgreSQL database in Railway
2. Use variable references: `${{Postgres.PGHOST}}`, etc.
3. Railway automatically links them

#### For Render:

1. Create PostgreSQL database
2. Go to database → Info
3. Copy "Internal Database URL"
4. Parse it: `postgres://user:password@host:port/database`
5. Set each variable separately

#### For DigitalOcean:

1. Create Managed Database
2. Use DigitalOcean variable references: `${db.HOSTNAME}`, etc.
3. Or manually copy credentials

#### For AWS:

1. Create RDS PostgreSQL instance
2. Copy endpoint from RDS console
3. Set credentials manually

---

### Step 3: Configure CORS

**CRITICAL**: Your frontend URL must be allowed to call your API.

Update these variables with your ACTUAL frontend URL:

```bash
CORS_ALLOWED_ORIGINS=https://your-frontend.vercel.app
SANCTUM_STATEFUL_DOMAINS=your-frontend.vercel.app
```

**Multiple origins** (separate with commas):

```bash
CORS_ALLOWED_ORIGINS=https://app.example.com,https://www.example.com
```

---

### Step 4: Run Migrations

After deployment, run database migrations:

**Railway:**

```bash
# In Railway Shell
php artisan migrate --force
php artisan db:seed --force
```

**Render:**

```bash
# In Render Shell
php artisan migrate --force
php artisan db:seed --force
```

**DigitalOcean:**

```bash
# Use App Platform Console or API
php artisan migrate --force
php artisan db:seed --force
```

**AWS Elastic Beanstalk:**

```bash
eb ssh
cd /var/app/current
php artisan migrate --force
php artisan db:seed --force
```

**VPS:**

```bash
ssh user@your-server
cd /var/www/vocabulary/backend
php artisan migrate --force
php artisan db:seed --force
```

---

## Platform-Specific Build Commands

### Backend Build Commands

**Railway:**

```bash
# Build Command: (automatic with PHP runtime)
composer install --no-dev --optimize-autoloader

# Start Command:
php artisan serve --host=0.0.0.0 --port=$PORT
```

**Render:**

```bash
# Build Command:
composer install --no-dev --optimize-autoloader

# Start Command:
php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

**DigitalOcean:**

```bash
# Build Command:
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run Command:
php artisan migrate --force && heroku-php-apache2 public/
```

**AWS Elastic Beanstalk:**

```bash
# Use .platform/hooks/postdeploy/migrate.sh:
#!/bin/bash
cd /var/app/current
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

---

### Frontend Build Commands

**Vercel:**

```bash
# Build Command: npm run build
# Output Directory: dist
# Install Command: npm install
```

**Netlify:**

```bash
# Build Command: npm run build
# Publish Directory: dist
# Production branch: main
```

**DigitalOcean:**

```bash
# Build Command: npm run build
# Output Directory: dist
```

---

## Health Check Endpoints

Add these to your deployment configuration for health checks:

### Backend Health Check

**URL**: `/api/diagnostic`
**Expected Response**: 200 OK with JSON

### Database Health Check

**URL**: `/api/health/database`
**Expected Response**: 200 OK if database connected

---

## SSL/HTTPS Configuration

### Automatic (Recommended)

- **Railway**: Automatic SSL with custom domains
- **Vercel**: Automatic SSL
- **Render**: Automatic SSL
- **DigitalOcean**: Automatic SSL with App Platform

### Manual (VPS)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Generate certificate
sudo certbot --nginx -d yourdomain.com -d api.yourdomain.com

# Auto-renewal is configured automatically
# Test renewal:
sudo certbot renew --dry-run
```

---

## Firewall Configuration (VPS Only)

```bash
# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow SSH (IMPORTANT: Do this first!)
sudo ufw allow 22/tcp

# Allow PostgreSQL (if external access needed)
sudo ufw allow 5432/tcp

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

---

## Performance Optimization

### Backend Optimization

```bash
# Cache configuration (run after deployment)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize composer autoloader
composer install --optimize-autoloader --no-dev

# Enable OPcache (add to php.ini)
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
```

### Frontend Optimization

```bash
# Already configured in vite.config.ts
# - Code splitting
# - Tree shaking
# - Minification
# - Asset optimization
```

---

## Monitoring & Logging

### Backend Logs

**Railway/Render/DigitalOcean:**

- View logs in platform dashboard
- Logs are automatically collected

**AWS:**

```bash
# View logs
eb logs

# Tail logs
eb logs --tail
```

**VPS:**

```bash
# Laravel logs
tail -f /var/www/vocabulary/backend/storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

### Frontend Logs

**Vercel:**

- Dashboard → Project → Logs
- Real-time function logs
- Build logs

**Netlify:**

- Dashboard → Site → Deploys → Deploy logs
- Function logs (if using)

---

## Backup Strategy

### Database Backups

**Railway:**

- Automatic daily backups (included)
- Manual backup: Download from dashboard

**Render:**

- Automatic daily backups (paid plans)
- Manual backup:
  ```bash
  pg_dump -h host -U user -d database > backup.sql
  ```

**DigitalOcean:**

- Configure automated backups in database settings
- Daily backups with 7-day retention (included)

**AWS RDS:**

- Enable automated backups in RDS console
- Set retention period (1-35 days)
- Manual snapshots available

**VPS:**

```bash
# Create backup script
nano ~/backup.sh

#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/backups"
mkdir -p $BACKUP_DIR

# Database backup
pg_dump -U vocabulary_user vocabulary > $BACKUP_DIR/db_backup_$DATE.sql

# Compress
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +7 -delete

# Make executable
chmod +x ~/backup.sh

# Add to crontab (daily at 2 AM)
crontab -e
0 2 * * * /home/vocabulary/backup.sh
```

---

## Rollback Strategy

### Railway/Render/Vercel

- Use platform dashboard to rollback to previous deployment
- Railway: Deployments → Click previous deploy → Redeploy
- Render: Deploys → Click previous deploy → Manual Deploy
- Vercel: Deployments → Click previous deploy → Promote to Production

### AWS

```bash
eb deploy --version previous-version-label
```

### VPS

```bash
cd /var/www/vocabulary
git log --oneline  # Find commit to rollback to
git reset --hard COMMIT_HASH
./deploy.sh
```

---

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Database credentials secure and not exposed
- [ ] CORS properly configured (not `*`)
- [ ] HTTPS/SSL enabled
- [ ] Firewall configured (if VPS)
- [ ] Database backups automated
- [ ] Rate limiting enabled
- [ ] Security headers configured
- [ ] File permissions correct (775 for storage, 755 for others)
- [ ] `.env` file not committed to git
- [ ] API keys stored in environment variables
- [ ] Database SSL enabled (if supported)
- [ ] Regular security updates applied

---

## Testing Deployment

### Backend Tests

```bash
# Test API endpoint
curl https://your-backend-url.com/api/diagnostic

# Should return:
{
  "status": "ok",
  "message": "API is working",
  "timestamp": "2024-..."
}

# Test database connection
curl https://your-backend-url.com/api/words

# Test CORS (from browser console on frontend)
fetch('https://your-backend-url.com/api/words')
  .then(r => r.json())
  .then(console.log)
```

### Frontend Tests

```bash
# Build locally first
cd frontend
npm run build
npm run preview

# Test in browser
# Check console for errors
# Test all main features
```

---

## Troubleshooting Common Issues

### Issue: 500 Internal Server Error

**Check:**

1. Laravel logs
2. `APP_KEY` is set
3. Database connection working
4. File permissions correct

**Fix:**

```bash
php artisan config:clear
php artisan cache:clear
chmod -R 775 storage bootstrap/cache
```

### Issue: CORS Errors

**Check:**

1. `CORS_ALLOWED_ORIGINS` includes your frontend URL
2. Frontend is using correct API URL
3. No trailing slashes in URLs

**Fix:**

```bash
# Update backend .env
CORS_ALLOWED_ORIGINS=https://your-exact-frontend-url.com
```

### Issue: Database Connection Failed

**Check:**

1. Database credentials correct
2. Database is running
3. Database accepts connections from your server
4. SSL mode correct for your database

**Fix:**

```bash
# Test connection
php artisan tinker
DB::connection()->getPdo();
```

### Issue: Frontend Shows Blank Page

**Check:**

1. Browser console for errors
2. `VITE_API_URL` is correct
3. Build completed successfully
4. Routing configured for SPA

**Fix:**

```bash
# Rebuild
npm run build

# Check dist/index.html exists
ls -la dist/
```

---

## Post-Deployment Checklist

- [ ] Backend deployed and accessible
- [ ] Database created and migrations run
- [ ] Frontend deployed and accessible
- [ ] Environment variables set correctly
- [ ] CORS configured properly
- [ ] SSL/HTTPS working
- [ ] API endpoints responding correctly
- [ ] Frontend can communicate with backend
- [ ] Database backups configured
- [ ] Monitoring/logging set up
- [ ] Domain configured (if custom domain)
- [ ] Documentation updated
- [ ] Team notified of new URLs

---

## Useful Commands Reference

### Laravel Artisan Commands

```bash
php artisan migrate --force        # Run migrations
php artisan migrate:fresh --force  # Fresh migration (WARNING: deletes data)
php artisan db:seed --force       # Seed database
php artisan config:cache          # Cache configuration
php artisan config:clear          # Clear config cache
php artisan route:cache           # Cache routes
php artisan route:clear           # Clear route cache
php artisan cache:clear           # Clear app cache
php artisan view:clear            # Clear view cache
php artisan key:generate          # Generate APP_KEY
php artisan tinker                # Interactive console
```

### Git Commands

```bash
git status                         # Check status
git add .                          # Stage all changes
git commit -m "message"            # Commit changes
git push origin main               # Push to GitHub
git pull origin main               # Pull latest changes
git log --oneline                  # View commit history
git reset --hard HEAD              # Discard local changes
```

### Platform CLIs

```bash
# Railway
railway login
railway link
railway up
railway logs
railway run <command>

# Vercel
vercel login
vercel link
vercel
vercel logs
vercel env add

# AWS
eb init
eb create
eb deploy
eb logs
eb ssh

# DigitalOcean
doctl auth init
doctl apps list
doctl apps create-deployment
```

---

Good luck with your deployment! 🚀
