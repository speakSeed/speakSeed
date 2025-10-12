# 🚂 Deploy SpeakSeed to Railway.app

## Quick Setup (5 minutes)

### Step 1: Install Railway CLI

```bash
npm install -g @railway/cli
```

### Step 2: Login to Railway

```bash
railway login
```

This opens your browser - sign up with GitHub (free!)

### Step 3: Initialize Project

```bash
cd /Users/shutenchik/projects/vocabulary
railway init
```

Choose: "Create new project"
Name it: "speakseed"

### Step 4: Add PostgreSQL Database

```bash
railway add -d postgres
```

Railway automatically creates and links the database!

### Step 5: Deploy Backend

```bash
cd backend
railway up
```

Railway automatically:
- ✅ Detects it's Laravel
- ✅ Runs `composer install`
- ✅ Runs migrations
- ✅ Sets up environment

### Step 6: Get Backend URL

```bash
railway domain
```

You'll get a URL like: `https://your-backend.up.railway.app`

### Step 7: Deploy Frontend

```bash
cd ../frontend

# Update .env with your backend URL
echo "VITE_API_URL=https://your-backend.up.railway.app/api" > .env.production

# Build
npm run build

# Deploy (create a new service)
railway up
```

### Step 8: Get Frontend URL

```bash
railway domain
```

You'll get: `https://your-frontend.up.railway.app`

## 🎉 Done!

Share this URL with your friends: `https://your-frontend.up.railway.app`

### Advantages:
- ✅ Always online (24/7)
- ✅ Fast global CDN
- ✅ Automatic HTTPS
- ✅ Custom domain support
- ✅ Easy updates: just `railway up`
- ✅ FREE for your app size

### To Update:
```bash
git push
railway up
```

---

## Alternative: Deploy Each Service Separately

### Backend on Railway:
```bash
cd backend
railway init
railway add -d postgres
railway up
```

### Frontend on Vercel:
```bash
cd frontend
vercel --prod
```

Your backend runs on Railway, frontend on Vercel!

---

## Need Help?

Railway Docs: https://docs.railway.app/

