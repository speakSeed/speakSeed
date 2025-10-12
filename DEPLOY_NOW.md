# 🚀 Deploy SpeakSeed to Production - COMPLETE GUIDE

## ✅ Everything is Ready!

All configuration files are created and committed to GitHub.
You're ready to deploy in **10 minutes**!

---

## 🎯 OPTION 1: Railway (Backend + DB) + Vercel (Frontend) - RECOMMENDED

### Why This Option?
- ✅ **100% FREE** for your app
- ✅ Backend on Railway with PostgreSQL
- ✅ Frontend on Vercel (super fast!)
- ✅ Permanent URLs
- ✅ Automatic deployments from GitHub

---

## 📦 Part A: Deploy Backend to Railway

### Step 1: Create Railway Account

1. Open your browser and go to: **https://railway.app**
2. Click **"Start a New Project"**
3. Sign in with **GitHub** (easiest!)

### Step 2: Create New Project from GitHub

1. Click **"Deploy from GitHub repo"**
2. **Authorize Railway** to access your GitHub
3. Select **"speakSeed/speakSeed"** repository
4. Railway will ask which service to deploy first

### Step 3: Deploy Backend

1. Click **"Add a service"** → **"GitHub Repo"**
2. Select your repo: **speakSeed/speakSeed**
3. **Root Directory**: `/backend`
4. Click **"Deploy"**

Railway automatically:
- ✅ Detects Laravel
- ✅ Installs PHP 8.2
- ✅ Runs composer install
- ✅ Uses your Procfile

### Step 4: Add PostgreSQL Database

1. In your Railway project, click **"+ New"**
2. Select **"Database"** → **"Add PostgreSQL"**
3. Railway creates the database automatically!
4. **Important**: Click on your backend service
5. Go to **Variables** tab
6. Railway automatically adds:
   - `DATABASE_URL`
   - `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD`

### Step 5: Add Environment Variables

In your backend service **Variables** tab, add these:

```
APP_NAME=SpeakSeed
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YTUniTdQf3cUzTt6ONwr52tU6ukuPB9FItdqMNTCErI=

DB_CONNECTION=pgsql
DB_HOST=${{PGHOST}}
DB_PORT=${{PGPORT}}
DB_DATABASE=${{PGDATABASE}}
DB_USERNAME=${{PGUSER}}
DB_PASSWORD=${{PGPASSWORD}}

CACHE_DRIVER=file
SESSION_DRIVER=cookie
LOG_CHANNEL=stack
```

**Note**: Railway auto-fills the `${{PGHOST}}` variables!

### Step 6: Get Your Backend URL

1. Click on your backend service
2. Go to **Settings** tab
3. Scroll to **Domains**
4. Click **"Generate Domain"**
5. You'll get: `https://your-backend-production.up.railway.app`
6. **COPY THIS URL** - you need it for frontend!

### Step 7: Run Database Migrations

1. In your backend service, go to **Deployments** tab
2. Click the latest deployment
3. Click **"View Logs"**
4. You should see migrations running automatically!

If not, you can run manually:
- Go to your backend service
- Click **"..." menu** → **"Run Command"**
- Type: `php artisan migrate --force`
- Type: `php artisan db:seed --class=WordSeeder`

---

## 🌐 Part B: Deploy Frontend to Vercel

### Step 1: Install Vercel CLI (if not installed)

```bash
npm install -g vercel
```

### Step 2: Deploy Frontend

```bash
cd /Users/shutenchik/projects/vocabulary/frontend

# Create production environment file
echo "VITE_API_URL=https://YOUR-BACKEND-URL.up.railway.app/api" > .env.production

# Replace YOUR-BACKEND-URL with the Railway URL from Step 6!

# Login to Vercel
vercel login

# Deploy
vercel --prod
```

Vercel will ask:
- "Set up and deploy?"  → **YES**
- "Which scope?" → Select your account
- "Link to existing project?" → **NO**
- "Project name?" → **speakseed-frontend**
- "Directory?" → **frontend** (if it asks)
- "Override settings?" → **NO**

### Step 3: Add Environment Variable in Vercel Dashboard

1. Go to **https://vercel.com/dashboard**
2. Click your **speakseed-frontend** project
3. Go to **Settings** → **Environment Variables**
4. Add:
   - **Name**: `VITE_API_URL`
   - **Value**: `https://YOUR-BACKEND-URL.up.railway.app/api`
   - **Environment**: Production
5. Click **Save**
6. Go to **Deployments** tab
7. Click **"..."** on latest → **"Redeploy"**

### Step 4: Get Your Frontend URL

After deployment completes:
- Vercel shows your URL: `https://speakseed-frontend.vercel.app`
- **THIS IS YOUR PERMANENT URL!**

---

## 🎉 DONE! Your App is LIVE!

### Your Permanent URLs:

- **Frontend**: `https://speakseed-frontend.vercel.app`
- **Backend**: `https://your-backend-production.up.railway.app`

### Share with Friends:

Send them the **frontend URL**!

---

## 🎯 OPTION 2: Everything on Railway (Simpler)

If you want everything in one place:

### Deploy Backend (same as above)

### Deploy Frontend on Railway:

1. In Railway project, click **"+ New"**
2. **"GitHub Repo"** → Select your repo
3. **Root Directory**: `/frontend`
4. Add environment variable:
   - `VITE_API_URL=https://your-backend.up.railway.app/api`
5. Railway builds and deploys!
6. Generate domain for frontend service

**Result**: Both on Railway, one dashboard!

---

## 🔧 After Deployment

### To Update Your App:

Just push to GitHub:
```bash
git add .
git commit -m "Update features"
git push origin main
```

Both Railway and Vercel will auto-deploy! 🎉

### Monitor Your App:

- **Railway**: https://railway.app/dashboard
- **Vercel**: https://vercel.com/dashboard

### Logs:

- **Railway**: Click service → View Logs
- **Vercel**: Click deployment → View Logs

---

## 💰 Costs:

- **Railway**: $5/month FREE credit (your app costs ~$2-3/month)
- **Vercel**: 100% FREE for your usage
- **Total**: ~$0-3/month (first month FREE)

---

## 🆘 Troubleshooting:

### Backend not connecting to database?
- Check Variables tab has all DB variables
- Redeploy the backend service

### Frontend can't reach backend?
- Check VITE_API_URL is correct
- Check backend URL ends with `/api`
- Redeploy frontend after changing variable

### Migrations not running?
- Go to Railway backend service
- "..." menu → "Run Command"
- Type: `php artisan migrate --force`

---

## 📞 Need Help?

- Railway Docs: https://docs.railway.app
- Vercel Docs: https://vercel.com/docs
- Your code is on GitHub: https://github.com/speakSeed/speakSeed

---

## ✅ Checklist:

Backend:
- [ ] Create Railway account
- [ ] Deploy backend from GitHub
- [ ] Add PostgreSQL database
- [ ] Add environment variables
- [ ] Generate backend domain
- [ ] Run migrations

Frontend:
- [ ] Create .env.production with backend URL
- [ ] Deploy to Vercel
- [ ] Add VITE_API_URL variable
- [ ] Get frontend URL

Final:
- [ ] Test frontend URL
- [ ] Share with friends! 🎉

---

**Start here**: https://railway.app 🚀

