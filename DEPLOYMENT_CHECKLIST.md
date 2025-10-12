# 🚀 SpeakSeed Deployment Checklist

## ✅ What's Already Done

1. ✅ **Git Repository**: Pushed to `git@github.com:speakSeed/speakSeed.git`
2. ✅ **Frontend Build**: TypeScript errors fixed, builds successfully
3. ✅ **Backend Configuration**: `vercel.json` and `api/index.php` created
4. ✅ **CI/CD Workflows**: GitHub Actions configured for both frontend and backend
5. ✅ **Documentation**: Comprehensive deployment tutorial created

## 📋 Next Steps (Your To-Do)

### Step 1: Create Vercel Account & Projects
1. Go to https://vercel.com and sign up/login with GitHub
2. Create **TWO** projects:
   - **Frontend Project**: Import `speakSeed/speakSeed` repository, select `frontend` as root directory
   - **Backend Project**: Import `speakSeed/speakSeed` repository, select `backend` as root directory

### Step 2: Configure GitHub Secrets
Go to your GitHub repository → Settings → Secrets and variables → Actions

Add these secrets:
- `VERCEL_TOKEN`: Get from https://vercel.com/account/tokens
- `VITE_API_URL`: Your backend URL (e.g., `https://your-backend.vercel.app/api`)

### Step 3: Configure Vercel Environment Variables

#### Frontend (Vercel Dashboard)
- `VITE_API_URL`: `https://your-backend.vercel.app/api`

#### Backend (Vercel Dashboard)
```
APP_NAME=SpeakSeed
APP_ENV=production
APP_KEY=[Generate with: php artisan key:generate --show]
APP_DEBUG=false
APP_URL=https://your-backend.vercel.app

DB_CONNECTION=pgsql
DB_HOST=[Your PostgreSQL host]
DB_PORT=5432
DB_DATABASE=[Your database name]
DB_USERNAME=[Your database username]
DB_PASSWORD=[Your database password]

CACHE_DRIVER=array
SESSION_DRIVER=cookie
LOG_CHANNEL=stderr
SESSION_SECURE_COOKIE=true
```

### Step 4: Database Setup

**Option A: Vercel Postgres (Recommended)**
1. In your backend project on Vercel, go to Storage tab
2. Create a Postgres database
3. Copy connection details to environment variables
4. Run migrations: `vercel exec php artisan migrate --seed`

**Option B: External PostgreSQL**
1. Use Supabase, Railway, or any PostgreSQL provider
2. Add connection details to Vercel environment variables
3. Run migrations manually or through CI/CD

### Step 5: Deploy!

Push any change to trigger deployment:
```bash
git commit --allow-empty -m "Trigger deployment"
git push origin main
```

Or trigger manually in Vercel dashboard.

### Step 6: Verify Deployment

1. Check GitHub Actions tab for workflow status
2. Check Vercel deployments for both projects
3. Visit your frontend URL
4. Test API connectivity
5. Add a word and test all features

## 🔑 Where to Find API Keys

| Key | Location |
|-----|----------|
| **Vercel Token** | https://vercel.com/account/tokens |
| **Vercel Project IDs** | Project Settings → General → Project ID |
| **Laravel APP_KEY** | Run: `php artisan key:generate --show` |
| **PostgreSQL Credentials** | Vercel Storage tab or your database provider |
| **GitHub Secrets** | Repository Settings → Secrets and variables → Actions |

## 📖 Detailed Instructions

See `PRODUCTION_DEPLOYMENT_TUTORIAL.md` for step-by-step instructions with screenshots and troubleshooting.

## 🆘 Common Issues

### Issue 1: CI/CD Workflow Fails
- Check GitHub Actions logs for errors
- Ensure all secrets are added correctly
- Verify Vercel CLI has correct permissions

### Issue 2: Backend Returns 500 Error
- Check Vercel function logs
- Verify all environment variables are set
- Ensure database connection works
- Run migrations: `vercel exec php artisan migrate`

### Issue 3: Frontend Can't Connect to Backend
- Verify `VITE_API_URL` points to correct backend URL
- Check CORS configuration in Laravel
- Ensure backend is deployed and accessible

## 🎉 Success Indicators

- ✅ GitHub Actions show green checkmarks
- ✅ Frontend deployed to Vercel URL
- ✅ Backend deployed to Vercel URL
- ✅ Can add words and see them in "My Dictionary"
- ✅ All quiz modes work correctly
- ✅ Progress tracking updates properly

## 📞 Need Help?

Refer to:
1. `PRODUCTION_DEPLOYMENT_TUTORIAL.md` - Comprehensive guide
2. `DEPLOYMENT_GUIDE.md` - Alternative deployment options
3. GitHub Actions logs - Check for CI/CD errors
4. Vercel function logs - Check for backend errors

