# Railway Deployment - Quick Setup Guide

## Your Repository Info

- **Repository**: speakSeed/speakSeed
- **GitHub URL**: https://github.com/speakSeed/speakSeed

---

## Method 1: Web Dashboard (Recommended)

### Step 1: Grant Railway Access to Your Repository

1. Go to: https://github.com/settings/installations
2. Find **"Railway"** in the list
3. Click **"Configure"**
4. Under **"Repository access"**:
   - ✅ Select **"All repositories"**, OR
   - ✅ Select **"Only select repositories"** → Add `speakSeed/speakSeed`
5. Click **"Save"**

### Step 2: Deploy on Railway

1. Go to: https://railway.app
2. Click **"New Project"**
3. Select **"Deploy from GitHub repo"**
4. Search for: **`speakSeed`** (just the repo name, not the full URL)
5. Select **`speakSeed/speakSeed`**
6. Railway will start deploying

---

## Method 2: Using Railway CLI (Alternative)

If the web interface doesn't work, use the CLI:

### Install Railway CLI

```bash
npm install -g @railway/cli
```

### Login to Railway

```bash
railway login
```

### Deploy Your Project

```bash
# Navigate to your project
cd /Users/shutenchik/projects/vocabulary

# Initialize Railway project
railway init

# Link to Railway
railway link

# Deploy
railway up
```

---

## After Deployment: Configure Your Services

### 1. Add PostgreSQL Database

1. In Railway dashboard, click **"New"**
2. Select **"Database"** → **"PostgreSQL"**
3. Database will be created with automatic credentials

### 2. Configure Backend Service

1. Click on your backend service
2. Go to **"Settings"**:

   - **Root Directory**: `backend`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

3. Go to **"Variables"** and add:

```bash
APP_NAME="Vocabulary API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

SESSION_DRIVER=cookie
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
CORS_ALLOWED_ORIGINS=
```

### 3. Generate APP_KEY

1. After backend deploys, click **"Open Shell"** (terminal icon)
2. Run:

```bash
php artisan key:generate --show
```

3. Copy the output (starts with `base64:`)
4. Go to **"Variables"** → Update `APP_KEY` with this value

### 4. Get Backend URL

1. Go to **"Settings"**
2. Under **"Public Networking"**, click **"Generate Domain"**
3. Copy your backend URL (e.g., `https://speakseed-backend-production.up.railway.app`)
4. Update these variables:
   - `APP_URL`: Your backend URL
   - `CORS_ALLOWED_ORIGINS`: Your frontend URL (add later)

### 5. Run Database Migrations

1. Click **"Open Shell"** again
2. Run:

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 6. Test Backend

Visit: `https://your-backend-url.railway.app/api/diagnostic`

You should see JSON response confirming API is working.

---

## Deploy Frontend to Vercel

### 1. Go to Vercel

1. Visit: https://vercel.com
2. Click **"New Project"**
3. Import your GitHub repository: `speakSeed/speakSeed`

### 2. Configure Project

- **Framework Preset**: Vite
- **Root Directory**: `frontend`
- **Build Command**: `npm run build`
- **Output Directory**: `dist`

### 3. Add Environment Variable

- **Name**: `VITE_API_URL`
- **Value**: Your Railway backend URL (from Step 4 above)
- **Environments**: Select all (Production, Preview, Development)

### 4. Deploy

Click **"Deploy"** and wait 1-2 minutes.

### 5. Update Backend CORS

1. Go back to Railway
2. Click your backend service → **"Variables"**
3. Update `CORS_ALLOWED_ORIGINS` with your Vercel URL
4. Save (backend will auto-redeploy)

---

## Troubleshooting

### Can't Find Repository on Railway?

**Issue**: "No repositories found"

**Solution**:

1. Check GitHub permissions: https://github.com/settings/installations
2. Make sure Railway has access to `speakSeed/speakSeed`
3. Try searching for just `speakSeed` (not the full URL)
4. Refresh the Railway page
5. Try disconnecting and reconnecting GitHub in Railway settings

### Railway Shows Wrong Directory?

If Railway deploys the wrong directory:

1. Click service → **"Settings"**
2. Set **"Root Directory"** to `backend`
3. Set **"Start Command"** to: `php artisan serve --host=0.0.0.0 --port=$PORT`
4. Redeploy

### Backend Won't Start?

Check these:

1. `APP_KEY` is set (generate with `php artisan key:generate --show`)
2. Database variables are correct
3. Start command is correct
4. Check logs in **"Deployments"** tab

### Database Connection Failed?

1. Make sure PostgreSQL service is running
2. Verify variables use Railway references: `${{Postgres.PGHOST}}`
3. Check both services are in the same project
4. Railway automatically links them

---

## Quick Reference

### Railway Dashboard

- **Project**: https://railway.app/project/your-project-id
- **Logs**: Service → Deployments → Click deployment
- **Shell**: Service → Terminal icon at top
- **Variables**: Service → Variables tab

### Useful Commands (in Railway Shell)

```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Clear caches
php artisan config:clear
php artisan cache:clear

# View routes
php artisan route:list

# Check environment
php artisan env
```

---

## Cost Estimate

- **Railway Starter**: $5/month

  - Backend API
  - PostgreSQL database
  - Automatic deployments
  - HTTPS included

- **Vercel Hobby**: FREE
  - Frontend hosting
  - Global CDN
  - Automatic deployments
  - HTTPS included

**Total**: $5/month

---

## Support Links

- Railway Docs: https://docs.railway.app
- Railway Discord: https://discord.gg/railway
- GitHub Railway App: https://github.com/apps/railway-app
- Vercel Docs: https://vercel.com/docs

---

## Next Steps After Deployment

1. ✅ Set up monitoring (Sentry, LogRocket)
2. ✅ Configure automatic backups
3. ✅ Add custom domain (optional)
4. ✅ Set up CI/CD with GitHub Actions
5. ✅ Add error tracking
6. ✅ Performance monitoring

---

Good luck! 🚀
