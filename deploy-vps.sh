#!/bin/bash

# Vocabulary App - VPS Deployment Script
# This script automates deployment on a fresh Ubuntu VPS

set -e

echo "🚀 Vocabulary App - VPS Deployment Script"
echo "=========================================="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Please run as root (use sudo)"
    exit 1
fi

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Configuration
read -p "Enter your domain (e.g., example.com): " DOMAIN
read -p "Enter API subdomain (e.g., api): " API_SUBDOMAIN
read -p "Enter database password: " -s DB_PASSWORD
echo ""
read -p "Enter your GitHub repository URL: " REPO_URL

API_DOMAIN="$API_SUBDOMAIN.$DOMAIN"

echo ""
echo "Configuration:"
echo "  Frontend: https://$DOMAIN"
echo "  Backend: https://$API_DOMAIN"
echo "  Repository: $REPO_URL"
echo ""
read -p "Is this correct? (y/n): " confirm

if [ "$confirm" != "y" ]; then
    echo "Aborted"
    exit 1
fi

echo ""
echo "🔧 Starting installation..."

# Update system
echo "📦 Updating system packages..."
apt update && apt upgrade -y

# Install Nginx
echo "📦 Installing Nginx..."
apt install nginx -y

# Install PHP 8.2
echo "📦 Installing PHP 8.2..."
add-apt-repository ppa:ondrej/php -y
apt update
apt install php8.2-fpm php8.2-cli php8.2-pgsql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd -y

# Install PostgreSQL
echo "📦 Installing PostgreSQL..."
apt install postgresql postgresql-contrib -y

# Install Composer
echo "📦 Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install Node.js
echo "📦 Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install nodejs -y

# Install Git
echo "📦 Installing Git..."
apt install git -y

# Configure PostgreSQL
echo "🗄️  Configuring database..."
sudo -u postgres psql <<EOF
CREATE DATABASE vocabulary;
CREATE USER vocabulary_user WITH PASSWORD '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON DATABASE vocabulary TO vocabulary_user;
\q
EOF

# Clone repository
echo "📥 Cloning repository..."
cd /var/www
git clone $REPO_URL vocabulary
cd vocabulary

# Setup Backend
echo "⚙️  Setting up backend..."
cd backend

composer install --no-dev --optimize-autoloader

# Create .env file
cat > .env <<EOF
APP_NAME="Vocabulary API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://$API_DOMAIN

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=vocabulary
DB_USERNAME=vocabulary_user
DB_PASSWORD=$DB_PASSWORD

SESSION_DRIVER=cookie
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

CORS_ALLOWED_ORIGINS=https://$DOMAIN
EOF

# Generate APP_KEY
php artisan key:generate

# Set permissions
chown -R www-data:www-data /var/www/vocabulary
chmod -R 755 /var/www/vocabulary/backend/storage
chmod -R 755 /var/www/vocabulary/backend/bootstrap/cache

# Run migrations
php artisan migrate --force
php artisan db:seed --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Setup Frontend
echo "⚙️  Setting up frontend..."
cd /var/www/vocabulary/frontend

# Create production env
echo "VITE_API_URL=https://$API_DOMAIN" > .env.production

# Build frontend
npm install
npm run build

# Configure Nginx for Backend
echo "🌐 Configuring Nginx for backend..."
cat > /etc/nginx/sites-available/vocabulary-api <<EOF
server {
    listen 80;
    server_name $API_DOMAIN;
    root /var/www/vocabulary/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Configure Nginx for Frontend
echo "🌐 Configuring Nginx for frontend..."
cat > /etc/nginx/sites-available/vocabulary-frontend <<EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root /var/www/vocabulary/frontend/dist;

    index index.html;

    location / {
        try_files \$uri \$uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF

# Enable sites
ln -s /etc/nginx/sites-available/vocabulary-api /etc/nginx/sites-enabled/
ln -s /etc/nginx/sites-available/vocabulary-frontend /etc/nginx/sites-enabled/

# Remove default site
rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
nginx -t

# Restart services
systemctl restart php8.2-fpm
systemctl restart nginx

# Install SSL certificates
echo "🔒 Installing SSL certificates..."
apt install certbot python3-certbot-nginx -y

# Request certificates
certbot --nginx -d $DOMAIN -d www.$DOMAIN -d $API_DOMAIN --non-interactive --agree-tos --register-unsafely-without-email

# Create deployment script
echo "📝 Creating deployment script..."
cat > /var/www/vocabulary/deploy.sh <<'EOF'
#!/bin/bash
cd /var/www/vocabulary

echo "🔄 Pulling latest changes..."
git pull origin main

echo "📦 Updating backend..."
cd backend
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "📦 Updating frontend..."
cd ../frontend
npm install
npm run build

echo "🔄 Restarting services..."
systemctl restart php8.2-fpm
systemctl reload nginx

echo "✅ Deployment complete!"
EOF

chmod +x /var/www/vocabulary/deploy.sh

# Create backup script
echo "📝 Creating backup script..."
mkdir -p /home/backups

cat > /home/backups/backup.sh <<EOF
#!/bin/bash
DATE=\$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/backups"

# Database backup
pg_dump -U vocabulary_user vocabulary > \$BACKUP_DIR/db_backup_\$DATE.sql
gzip \$BACKUP_DIR/db_backup_\$DATE.sql

# Keep only last 7 days
find \$BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +7 -delete

echo "Backup completed: db_backup_\$DATE.sql.gz"
EOF

chmod +x /home/backups/backup.sh

# Add to crontab
(crontab -l 2>/dev/null; echo "0 2 * * * /home/backups/backup.sh") | crontab -

echo ""
echo -e "${GREEN}✅ Deployment complete!${NC}"
echo ""
echo "🌐 Your app is now available at:"
echo "   Frontend: https://$DOMAIN"
echo "   Backend:  https://$API_DOMAIN"
echo ""
echo "📝 Important files:"
echo "   Backend env: /var/www/vocabulary/backend/.env"
echo "   Deploy script: /var/www/vocabulary/deploy.sh"
echo "   Backup script: /home/backups/backup.sh"
echo ""
echo "🔄 To deploy updates:"
echo "   sudo /var/www/vocabulary/deploy.sh"
echo ""
echo "📊 To check logs:"
echo "   Backend: tail -f /var/www/vocabulary/backend/storage/logs/laravel.log"
echo "   Nginx: tail -f /var/log/nginx/error.log"
echo ""
echo "💾 Backups run daily at 2 AM to /home/backups/"
echo ""
echo -e "${YELLOW}⚠️  Don't forget to:${NC}"
echo "   1. Point your DNS A records to this server's IP"
echo "   2. Test the app thoroughly"
echo "   3. Set up monitoring"
echo ""

