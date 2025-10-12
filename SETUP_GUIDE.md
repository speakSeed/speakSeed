# 🚀 VocabMaster Setup Guide

This guide will help you get VocabMaster running on your local machine.

## Prerequisites Checklist

Before you begin, make sure you have:

### Option 1: Docker Setup (Easiest)

- [ ] Docker Desktop installed
- [ ] Docker Compose installed
- [ ] Node.js 18+ and npm installed
- [ ] 4GB+ free disk space

### Option 2: Local Setup

- [ ] PHP 8.2 or higher
- [ ] Composer
- [ ] PostgreSQL 13 or higher
- [ ] Node.js 18+ and npm
- [ ] 2GB+ free disk space

## Quick Start (5 minutes)

### Using the Automated Script

The easiest way to start both frontend and backend:

```bash
./start.sh
```

This script will:

1. Detect if Docker is available
2. Start the backend (with Docker or locally)
3. Start the frontend
4. Open your browser automatically

### Manual Setup

#### Step 1: Backend Setup

**With Docker:**

```bash
cd backend
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed  # Optional: adds sample words
```

**Without Docker:**

```bash
cd backend

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure PostgreSQL
# Edit .env and set your database credentials:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=vocabulary
# DB_USERNAME=postgres
# DB_PASSWORD=your_password

# Create database
createdb vocabulary  # Or use pgAdmin

# Run migrations
php artisan migrate
php artisan db:seed  # Optional

# Start server
php artisan serve
```

Backend will run on: **http://localhost:8000**

#### Step 2: Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Configure environment (if not exists)
echo "VITE_API_URL=http://localhost:8000/api" > .env
echo "VITE_APP_NAME=VocabMaster" >> .env

# Start development server
npm run dev
```

Frontend will run on: **http://localhost:5173**

#### Step 3: Access the Application

Open your browser and go to: **http://localhost:5173**

## Initial Configuration

### 1. Database Seeding (Recommended)

To populate the database with sample words:

```bash
cd backend
php artisan db:seed
# Or with Docker:
docker-compose exec app php artisan db:seed
```

This will add ~120 sample words across all levels (A1-C2).

### 2. Optional: Configure External APIs

For better image quality, you can add API keys to `backend/.env`:

**Unsplash (Free):**

1. Sign up at https://unsplash.com/developers
2. Create a new application
3. Add to `.env`: `UNSPLASH_ACCESS_KEY=your_key_here`

**Pexels (Free):**

1. Sign up at https://www.pexels.com/api/
2. Get your API key
3. Add to `.env`: `PEXELS_API_KEY=your_key_here`

## Troubleshooting

### Backend Issues

**Problem:** "Class not found" errors

```bash
cd backend
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
```

**Problem:** Database connection failed

- Check PostgreSQL is running
- Verify credentials in `.env`
- Ensure database exists: `createdb vocabulary`

**Problem:** Port 8000 already in use

```bash
php artisan serve --port=8001
# Update frontend .env: VITE_API_URL=http://localhost:8001/api
```

### Frontend Issues

**Problem:** "Failed to fetch" or CORS errors

- Ensure backend is running
- Check `VITE_API_URL` in `frontend/.env`
- Verify CORS settings in `backend/config/cors.php`

**Problem:** Port 5173 already in use

```bash
npm run dev -- --port 5174
```

**Problem:** Module not found errors

```bash
rm -rf node_modules package-lock.json
npm install
```

### Docker Issues

**Problem:** Containers won't start

```bash
docker-compose down
docker-compose up -d --force-recreate
```

**Problem:** Permission errors

```bash
sudo chown -R $USER:$USER backend/storage backend/bootstrap/cache
```

## Verification Steps

After setup, verify everything works:

1. **Backend Health Check:**

   - Visit: http://localhost:8000/api/health
   - Should return: `{"status":"ok","timestamp":"..."}`

2. **Frontend:**

   - Visit: http://localhost:5173
   - You should see the level selection page

3. **Database:**
   - Try selecting a level (e.g., A1)
   - You should see words if you ran the seeder

## Development Workflow

### Making Changes

**Backend:**

1. Edit PHP files in `backend/app/`
2. Changes are reflected immediately
3. If you modify routes or config, run:
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

**Frontend:**

1. Edit Vue files in `frontend/src/`
2. Vite will hot-reload automatically
3. Check browser console for errors

### Running Tests

**Backend:**

```bash
cd backend
php artisan test
```

**Frontend:**

```bash
cd frontend
npm run test  # If configured
```

## Production Deployment

See the main [README.md](README.md) for detailed deployment instructions for:

- Frontend: Netlify/Vercel
- Backend: Traditional hosting

## Getting Help

If you encounter issues:

1. Check this guide's troubleshooting section
2. Review logs:
   - Backend: `backend/storage/logs/laravel.log`
   - Frontend: Browser console
3. Check the GitHub Issues page
4. Ensure all prerequisites are installed

## Next Steps

Once setup is complete:

1. Explore the application
2. Try different learning modes
3. Check the progress dashboard
4. Customize the code to your needs
5. Consider adding more words via the seeder

Happy learning! 🎓
