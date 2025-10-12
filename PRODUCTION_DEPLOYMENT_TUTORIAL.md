# 🚀 Production Deployment Tutorial - SpeakSeed

## Complete Guide to Deploy Your Vocabulary App to Vercel with CI/CD

This tutorial will guide you through deploying both the frontend (Vue.js) and backend (Laravel) to Vercel with automated CI/CD pipelines using GitHub Actions.

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Required API Keys & Where to Find Them](#required-api-keys--where-to-find-them)
3. [Part 1: Frontend Deployment](#part-1-frontend-deployment)
4. [Part 2: Backend Deployment](#part-2-backend-deployment)
5. [Part 3: Database Setup](#part-3-database-setup)
6. [Part 4: CI/CD Pipeline Setup](#part-4-cicd-pipeline-setup)
7. [Part 5: Environment Variables](#part-5-environment-variables)
8. [Part 6: Testing & Verification](#part-6-testing--verification)
9. [Part 7: Custom Domain (Optional)](#part-7-custom-domain-optional)
10. [Troubleshooting](#troubleshooting)
11. [Maintenance & Updates](#maintenance--updates)

---

## Prerequisites

Before starting, ensure you have:

- ✅ **GitHub Account** - [Sign up here](https://github.com/signup)
- ✅ **Vercel Account** - [Sign up here](https://vercel.com/signup) (free tier available)
- ✅ **Node.js 18+** installed locally
- ✅ **PHP 8.2+** installed locally
- ✅ **Composer** installed locally
- ✅ **Git** installed and configured
- ✅ Your code pushed to a GitHub repository

**Estimated Time:** 45-60 minutes for complete setup

---

## Required API Keys & Where to Find Them

### 1. Vercel Token

**What it is:** Authentication token for Vercel API and CLI

**Where to get it:**

1. Go to [https://vercel.com/account/tokens](https://vercel.com/account/tokens)
2. Click "Create Token"
3. Name it "GitHub Actions" or "CI/CD"
4. Select scope: "Full Account"
5. Click "Create"
6. **Copy the token immediately** (you won't see it again!)

**Looks like:** `v8rWyS...` (random string)

---

### 2. Vercel Organization ID

**What it is:** Your Vercel account/team identifier

**Where to get it:**

```bash
# Install Vercel CLI
npm install -g vercel

# Login
vercel login

# Link your project
cd frontend
vercel link

# View project.json
cat .vercel/project.json
```

**Example output:**

```json
{
  "orgId": "team_abc123xyz",  ← This is your ORG_ID
  "projectId": "prj_def456uvw"
}
```

**Looks like:** `team_abc123xyz` or `user_abc123xyz`

---

### 3. Vercel Project IDs

**What it is:** Unique identifiers for your frontend and backend projects

**Where to get them:**

**For Frontend:**

```bash
cd frontend
vercel link
# Follow prompts:
# - Link to existing project? Y
# - What's your project name? speakseed-frontend

cat .vercel/project.json
# Copy the "projectId" value
```

**For Backend:**

```bash
cd backend
vercel link
# Follow prompts:
# - Link to existing project? Y
# - What's your project name? speakseed-backend

cat .vercel/project.json
# Copy the "projectId" value
```

**Looks like:** `prj_abc123xyz456`

---

### 4. PostgreSQL Database Credentials

**Option A: Vercel Postgres (Recommended)**

1. Go to [Vercel Dashboard](https://vercel.com/dashboard)
2. Select your backend project
3. Go to "Storage" tab
4. Click "Create Database"
5. Choose "Postgres"
6. Select region (closest to your users)
7. Click "Create"
8. Copy all credentials:
   - `POSTGRES_URL`
   - `POSTGRES_HOST`
   - `POSTGRES_DATABASE`
   - `POSTGRES_USER`
   - `POSTGRES_PASSWORD`

**Option B: External Provider (Supabase, Railway, etc.)**

1. Create database on your preferred provider
2. Get connection details from their dashboard

---

### 5. Laravel APP_KEY

**What it is:** Encryption key for Laravel application

**How to generate:**

```bash
cd backend
php artisan key:generate --show
```

**Output:** `base64:rAnDoMkEy123...`

**Copy the entire string including `base64:`**

---

### 6. GitHub Personal Access Token (Optional)

**What it is:** Token for GitHub Actions to access private repos

**Only needed if:** Your repository is private

**Where to get it:**

1. Go to [GitHub Settings > Tokens](https://github.com/settings/tokens)
2. Click "Generate new token" > "Generate new token (classic)"
3. Name: "Vercel CI/CD"
4. Select scopes: `repo` (full control)
5. Click "Generate token"
6. Copy the token

**Looks like:** `ghp_abc123xyz...`

---

## Part 1: Frontend Deployment

### Step 1.1: Push Code to GitHub

```bash
# Initialize git (if not already done)
cd /path/to/vocabulary
git init
git add .
git commit -m "Initial commit: SpeakSeed vocabulary app"

# Create repository on GitHub (via website)
# Then add remote and push:
git remote add origin https://github.com/YOUR_USERNAME/speakseed.git
git branch -M main
git push -u origin main
```

### Step 1.2: Deploy Frontend to Vercel

**Via Vercel Dashboard (Easiest):**

1. Go to [https://vercel.com/new](https://vercel.com/new)
2. Click "Import Project"
3. Select your GitHub repository
4. Configure project:
   ```
   Framework Preset: Vite
   Root Directory: frontend
   Build Command: npm run build
   Output Directory: dist
   Install Command: npm ci
   ```
5. Click "Deploy"
6. Wait 2-3 minutes for first deployment
7. Your frontend is live! 🎉

**Your frontend URL:** `https://speakseed-frontend-xxx.vercel.app`

### Step 1.3: Add Frontend Environment Variables

1. In Vercel Dashboard, go to your frontend project
2. Click "Settings" > "Environment Variables"
3. Add variable:
   ```
   Key: VITE_API_URL
   Value: https://your-backend.vercel.app/api
   ```
   (You'll update this after deploying backend)
4. Click "Save"
5. Redeploy: Go to "Deployments" > click "..." > "Redeploy"

---

## Part 2: Backend Deployment

### Step 2.1: Prepare Laravel for Vercel

The backend requires special configuration for Vercel's serverless environment.

**Files already created:**

- `backend/vercel.json` - Vercel configuration
- `backend/api/index.php` - Serverless entry point

**No additional setup needed!**

### Step 2.2: Deploy Backend to Vercel

**Via Vercel Dashboard:**

1. Go to [https://vercel.com/new](https://vercel.com/new)
2. Click "Import Project"
3. Select your GitHub repository
4. Configure project:
   ```
   Framework Preset: Other
   Root Directory: backend
   Build Command: composer install --optimize-autoloader --no-dev
   Output Directory: (leave empty)
   Install Command: composer install
   ```
5. Click "Deploy"
6. Wait 3-5 minutes for first deployment
7. Your backend is live! 🎉

**Your backend URL:** `https://speakseed-backend-xxx.vercel.app`

### Step 2.3: Add Backend Environment Variables

1. In Vercel Dashboard, go to your backend project
2. Click "Settings" > "Environment Variables"
3. Add these variables one by one:

```env
# Application
APP_NAME=SpeakSeed
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE (from step 5 above)
APP_DEBUG=false
APP_URL=https://your-backend.vercel.app

# Database (from step 4 above)
DB_CONNECTION=pgsql
DB_HOST=your-db-host.vercel-storage.com
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Cache & Session
CACHE_DRIVER=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync

# CORS (Important!)
CORS_ALLOWED_ORIGINS=https://your-frontend.vercel.app
```

4. Click "Save" after each variable
5. Redeploy: Go to "Deployments" > click "..." > "Redeploy"

### Step 2.4: Run Database Migrations

```bash
# Install Vercel CLI if not already installed
npm install -g vercel

# Login
vercel login

# Go to backend directory
cd backend

# Link to your Vercel project
vercel link

# Run migrations on production database
vercel env pull .env.production
php artisan migrate --force --env=production

# Seed database
php artisan db:seed --force --env=production
```

---

## Part 3: Database Setup

### Option A: Using Vercel Postgres

**Already covered in Part 2, Step 2.3!**

Vercel Postgres automatically:

- Backs up your database
- Scales with your app
- Provides connection pooling
- Includes metrics and monitoring

### Option B: Using External Database

**Recommended Providers:**

- **Supabase** (free tier: 500MB)
- **Railway** ($5/month)
- **PlanetScale** (free tier available)

**Setup:**

1. Create database on your provider
2. Get connection credentials
3. Add to Vercel environment variables (Part 2, Step 2.3)
4. Run migrations as shown in Part 2, Step 2.4

---

## Part 4: CI/CD Pipeline Setup

### Step 4.1: Add GitHub Secrets

1. Go to your GitHub repository
2. Click "Settings" > "Secrets and variables" > "Actions"
3. Click "New repository secret"
4. Add these secrets one by one:

| Secret Name                  | Value                | Where to get it                     |
| ---------------------------- | -------------------- | ----------------------------------- |
| `VERCEL_TOKEN`               | Your Vercel token    | Step 1 above                        |
| `VERCEL_ORG_ID`              | Your organization ID | Step 2 above                        |
| `VERCEL_PROJECT_ID_FRONTEND` | Frontend project ID  | Step 3 above                        |
| `VERCEL_PROJECT_ID_BACKEND`  | Backend project ID   | Step 3 above                        |
| `VITE_API_URL`               | Backend API URL      | https://your-backend.vercel.app/api |

**Click "Add secret" after each one.**

### Step 4.2: GitHub Actions Workflows

**Files already created:**

- `.github/workflows/deploy-frontend.yml` - Auto-deploy frontend
- `.github/workflows/deploy-backend.yml` - Auto-deploy backend

**No additional setup needed!**

### Step 4.3: Test CI/CD Pipeline

```bash
# Make a small change
echo "# Test CI/CD" >> README.md

# Commit and push
git add .
git commit -m "Test: CI/CD deployment"
git push origin main

# Watch deployment:
# 1. GitHub: Go to "Actions" tab
# 2. Vercel: Go to your dashboard
```

**Expected results:**

- ✅ GitHub Actions shows green checkmark
- ✅ Vercel shows successful deployment
- ✅ Your app is updated with changes

---

## Part 5: Environment Variables

### Frontend Environment Variables

**File:** `frontend/.env.production` (for local testing)

```env
VITE_API_URL=https://your-backend.vercel.app/api
```

**Vercel Dashboard:**

```
VITE_API_URL=https://speakseed-backend-xxx.vercel.app/api
```

### Backend Environment Variables

**Vercel Dashboard (Complete List):**

```env
# Application
APP_NAME=SpeakSeed
APP_ENV=production
APP_KEY=base64:your_generated_key_here
APP_DEBUG=false
APP_URL=https://your-backend.vercel.app

# Database
DB_CONNECTION=pgsql
DB_HOST=your-db-host.vercel-storage.com
DB_PORT=5432
DB_DATABASE=verceldb
DB_USERNAME=default
DB_PASSWORD=your_password_here

# Cache & Session (Serverless Compatible)
CACHE_DRIVER=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync

# CORS
CORS_ALLOWED_ORIGINS=https://your-frontend.vercel.app,https://speakseed.com

# Optional: External Services
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### How to Update Environment Variables

**Via Vercel Dashboard:**

1. Go to project > Settings > Environment Variables
2. Edit existing or add new variable
3. Click "Save"
4. **Important:** Redeploy for changes to take effect!

**Via Vercel CLI:**

```bash
# Add variable
vercel env add VARIABLE_NAME

# Pull to local
vercel env pull

# List all variables
vercel env ls
```

---

## Part 6: Testing & Verification

### 6.1: Frontend Health Check

```bash
# Check if frontend is live
curl https://your-frontend.vercel.app

# Expected: HTML response with your app
```

**In Browser:**

1. Open https://your-frontend.vercel.app
2. You should see the SpeakSeed homepage
3. Check browser console for errors

### 6.2: Backend Health Check

```bash
# Check API health endpoint
curl https://your-backend.vercel.app/api/health

# Expected response:
# {"status":"ok","timestamp":"2025-10-12T..."}
```

### 6.3: Database Connection Test

```bash
# Check if backend can connect to database
curl https://your-backend.vercel.app/api/words/level/A1?per_page=5

# Expected: JSON response with words
```

### 6.4: Full Integration Test

**Test the complete flow:**

1. **Open your frontend:** https://your-frontend.vercel.app
2. **Select a level** (e.g., A1)
3. **Add a word** to dictionary
4. **Check Progress** page
5. **Try a quiz**

**All features should work!** ✅

### 6.5: Performance Test

```bash
# Test API response time
time curl https://your-backend.vercel.app/api/health

# Should be < 2 seconds
```

---

## Part 7: Custom Domain (Optional)

### 7.1: Add Custom Domain to Frontend

1. Go to Vercel Dashboard > Your frontend project
2. Click "Settings" > "Domains"
3. Enter your domain: `speakseed.com`
4. Click "Add"
5. Follow DNS configuration instructions

**DNS Records (add these to your domain registrar):**

```
Type: A
Name: @
Value: 76.76.21.21

Type: CNAME
Name: www
Value: cname.vercel-dns.com
```

### 7.2: Add Custom Domain to Backend

1. Go to Vercel Dashboard > Your backend project
2. Click "Settings" > "Domains"
3. Enter your subdomain: `api.speakseed.com`
4. Click "Add"
5. Follow DNS configuration instructions

**DNS Record:**

```
Type: CNAME
Name: api
Value: cname.vercel-dns.com
```

### 7.3: Update Environment Variables

**Frontend:**

```env
VITE_API_URL=https://api.speakseed.com/api
```

**Backend:**

```env
APP_URL=https://api.speakseed.com
CORS_ALLOWED_ORIGINS=https://speakseed.com,https://www.speakseed.com
```

**Redeploy both projects after updating!**

---

## Troubleshooting

### Issue 1: Frontend Shows "Network Error"

**Cause:** Backend URL is incorrect or CORS not configured

**Solution:**

1. Check `VITE_API_URL` in Vercel environment variables
2. Verify backend `CORS_ALLOWED_ORIGINS` includes your frontend domain
3. Redeploy both frontend and backend

### Issue 2: Backend Shows 500 Error

**Cause:** Database connection failed or APP_KEY missing

**Solution:**

```bash
# Check logs
vercel logs your-backend-project --follow

# Common fixes:
# 1. Verify all DB_ environment variables are correct
# 2. Ensure APP_KEY is set and includes "base64:" prefix
# 3. Check DB_CONNECTION=pgsql (not postgres)
```

### Issue 3: "Route not found" on Backend

**Cause:** Laravel routing not configured for serverless

**Solution:**

- Ensure `backend/vercel.json` exists
- Check `backend/api/index.php` exists
- Verify routes in `backend/routes/api.php`

### Issue 4: GitHub Actions Fails

**Cause:** Missing secrets or incorrect workflow configuration

**Solution:**

1. Verify all GitHub secrets are added correctly
2. Check workflow file syntax
3. Look at error message in Actions tab
4. Common issue: VERCEL_TOKEN expired (regenerate it)

### Issue 5: Database Migrations Fail

**Cause:** Wrong database credentials or network issue

**Solution:**

```bash
# Test connection locally
php artisan migrate --env=production

# If fails, check:
# 1. DB credentials are correct
# 2. Database exists
# 3. User has proper permissions
```

### Issue 6: Session Not Persisting

**Cause:** Vercel serverless environment doesn't support file sessions

**Solution:**

- Ensure `SESSION_DRIVER=cookie` in backend
- Add `SESSION_DOMAIN=.yourdomain.com` for cross-subdomain sessions

### Issue 7: Slow API Response

**Cause:** Cold start in serverless environment

**Solutions:**

1. Optimize code (reduce dependencies)
2. Use Vercel Pro ($20/month) for faster cold starts
3. Add caching layer (Redis)
4. Optimize database queries

### Issue 8: Images Not Loading

**Cause:** Image URLs from API returning null

**Solution:**

- Check Unsplash API limits (50 requests/hour)
- Images fetch on-demand, so some might fail
- Consider pre-fetching and caching image URLs

---

## Maintenance & Updates

### Deploying Updates

**Automatic (via CI/CD):**

```bash
# Make changes to code
git add .
git commit -m "Update: feature description"
git push origin main

# GitHub Actions automatically deploys!
```

**Manual Deployment:**

```bash
# Frontend
cd frontend
vercel --prod

# Backend
cd backend
vercel --prod
```

### Monitoring

**Vercel Dashboard:**

- View deployment history
- Check error logs
- Monitor performance
- View analytics

**Set up alerts:**

1. Go to Project Settings > Monitoring
2. Enable email notifications for:
   - Deployment failures
   - High error rates
   - Performance issues

### Database Backups

**Vercel Postgres:**

- Automatic daily backups (kept for 7 days)
- View in Vercel Dashboard > Storage > Backups

**Manual Backup:**

```bash
# Export database
pg_dump $DATABASE_URL > backup.sql

# Upload to safe location (S3, Google Drive, etc.)
```

### Updating Dependencies

**Frontend:**

```bash
cd frontend
npm update
npm audit fix
git add package*.json
git commit -m "Update: frontend dependencies"
git push
```

**Backend:**

```bash
cd backend
composer update
git add composer.*
git commit -m "Update: backend dependencies"
git push
```

### Scaling

**When you need more:**

**Vercel Pro ($20/month per user):**

- Faster builds
- Better cold start performance
- Team collaboration
- Advanced analytics

**Database Scaling:**

- Vercel Postgres: Automatic scaling
- External: Upgrade plan as needed

**CDN & Caching:**

- Vercel automatically uses CDN
- Add Redis for backend caching

---

## Cost Breakdown

### Free Tier (Hobby)

**Vercel:**

- Unlimited deployments
- 100GB bandwidth/month
- Serverless functions
- **Cost: $0/month**

**Vercel Postgres:**

- 256 MB storage
- 60 hours compute/month
- **Cost: $0/month**

**Total: $0/month** for moderate traffic

### Paid Tier (When Needed)

**Vercel Pro:**

- Everything in free tier
- Faster performance
- Team features
- **Cost: $20/month**

**Vercel Postgres:**

- 512 MB: $0.45/month
- 1 GB: $0.90/month
- Scales with usage

**Estimated Total: $20-30/month** for production app with good traffic

---

## Quick Reference

### Essential Commands

```bash
# Deploy manually
vercel --prod

# View logs
vercel logs your-project --follow

# Pull environment variables
vercel env pull

# Run migrations
php artisan migrate --force --env=production

# Generate APP_KEY
php artisan key:generate --show

# Clear caches
php artisan config:cache
php artisan route:cache
```

### Important URLs

- **Vercel Dashboard:** https://vercel.com/dashboard
- **GitHub Actions:** https://github.com/YOUR_USERNAME/speakseed/actions
- **Vercel Docs:** https://vercel.com/docs
- **Laravel Docs:** https://laravel.com/docs

### Environment Variables Checklist

Frontend:

- [ ] VITE_API_URL

Backend:

- [ ] APP_NAME
- [ ] APP_ENV
- [ ] APP_KEY
- [ ] APP_DEBUG
- [ ] APP_URL
- [ ] DB_CONNECTION
- [ ] DB_HOST
- [ ] DB_PORT
- [ ] DB_DATABASE
- [ ] DB_USERNAME
- [ ] DB_PASSWORD
- [ ] CACHE_DRIVER
- [ ] SESSION_DRIVER
- [ ] QUEUE_CONNECTION
- [ ] CORS_ALLOWED_ORIGINS

GitHub Secrets:

- [ ] VERCEL_TOKEN
- [ ] VERCEL_ORG_ID
- [ ] VERCEL_PROJECT_ID_FRONTEND
- [ ] VERCEL_PROJECT_ID_BACKEND
- [ ] VITE_API_URL

---

## Success! 🎉

Your SpeakSeed vocabulary app is now deployed to production with automated CI/CD!

**Next Steps:**

1. Share your app with users
2. Monitor performance and errors
3. Collect feedback
4. Iterate and improve

**Your live app:**

- Frontend: https://your-frontend.vercel.app
- Backend: https://your-backend.vercel.app/api
- GitHub: https://github.com/YOUR_USERNAME/speakseed

**Need help?**

- Vercel Support: https://vercel.com/support
- Laravel Community: https://laravel.com/community
- GitHub Issues: Create an issue in your repository

---

## Additional Resources

- [Vercel Documentation](https://vercel.com/docs)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [Vue.js Production Guide](https://vuejs.org/guide/best-practices/production-deployment.html)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)

---

**Congratulations on deploying your app! 🚀**

Your vocabulary learning platform is now helping users master English worldwide! 📚✨
