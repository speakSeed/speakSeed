# 🚀 Quick Deploy Guide (Railway + Vercel)

This is the **fastest and easiest** way to deploy your vocabulary app to production in ~15 minutes.

## What You'll Get

- ✅ Backend API deployed on Railway
- ✅ Frontend deployed on Vercel
- ✅ PostgreSQL database on Railway
- ✅ Automatic deployments from GitHub
- ✅ HTTPS enabled automatically
- ✅ Cost: ~$5-10/month

---

## Step 1: Prerequisites (2 minutes)

1. **GitHub Account**: Make sure your code is on GitHub

   ```bash
   git add .
   git commit -m "Ready for deployment"
   git push origin main
   ```

2. **Create Accounts** (free):
   - Railway: https://railway.app
   - Vercel: https://vercel.com

---

## Step 2: Deploy Database (2 minutes)

1. Go to https://railway.app
2. Click "Login" → Sign in with GitHub
3. Click "New Project"
4. Select "Provision PostgreSQL"
5. Click the database → "Connect" tab → Copy connection details (you'll need these)

**Note**: Keep this tab open, you'll need to reference the database variables.

---

## Step 3: Deploy Backend (5 minutes)

### 3.1: Create Backend Service

1. In Railway, click "New" → "GitHub Repo"
2. Select your vocabulary repository
3. Railway will create a service

### 3.2: Configure Backend

1. Click the backend service
2. Go to "Settings" tab:
   - **Root Directory**: `backend`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`
   - Save changes

### 3.3: Add Environment Variables

1. Go to "Variables" tab
2. Click "RAW Editor"
3. Paste the following (update values):

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
LOG_LEVEL=error

CORS_ALLOWED_ORIGINS=
```

4. Click "Save"

### 3.4: Generate APP_KEY

1. In Railway, click on your backend service
2. Click "Deploy" button (at top right)
3. Once deployed, click "Open Shell" (terminal icon)
4. Run: `php artisan key:generate --show`
5. Copy the output (starts with `base64:`)
6. Go back to "Variables" → Update `APP_KEY` with this value
7. Click "Save"

### 3.5: Get Backend URL

1. Go to "Settings" tab
2. Click "Generate Domain" under "Public Networking"
3. Copy your backend URL (e.g., `https://vocabulary-api-production.up.railway.app`)
4. Update `APP_URL` variable with this URL
5. Save

### 3.6: Run Migrations

1. Click "Open Shell" again
2. Run these commands:
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```
3. You should see "Migration completed successfully"

### 3.7: Test Backend

Visit: `https://your-backend-url.railway.app/api/diagnostic`

You should see a JSON response with your API status.

---

## Step 4: Deploy Frontend (5 minutes)

### 4.1: Deploy to Vercel

1. Go to https://vercel.com
2. Click "New Project"
3. Import your GitHub repository
4. Configure:
   - **Framework Preset**: Vite
   - **Root Directory**: `frontend`
   - **Build Command**: `npm run build`
   - **Output Directory**: `dist`

### 4.2: Add Environment Variable

1. In Vercel project settings
2. Go to "Environment Variables"
3. Add:
   - **Key**: `VITE_API_URL`
   - **Value**: Your Railway backend URL (from Step 3.5)
   - **Environments**: Check all (Production, Preview, Development)
4. Click "Save"

### 4.3: Deploy

1. Click "Deploy"
2. Wait 1-2 minutes
3. Click "Visit" to see your live app!

### 4.4: Get Frontend URL

Copy your Vercel URL (e.g., `https://vocabulary-app.vercel.app`)

---

## Step 5: Update CORS (2 minutes)

Your frontend needs permission to call your backend API.

1. Go back to Railway dashboard
2. Click your backend service
3. Go to "Variables"
4. Update `CORS_ALLOWED_ORIGINS` with your Vercel URL:
   ```
   CORS_ALLOWED_ORIGINS=https://vocabulary-app.vercel.app
   ```
5. Save

Your backend will automatically redeploy.

---

## Step 6: Test Everything (1 minute)

1. Visit your Vercel frontend URL
2. Try to:
   - View the word list
   - Add a new word
   - Practice with flashcards
   - Check progress dashboard

If everything works, **congratulations!** 🎉 Your app is live!

---

## 🔧 Troubleshooting

### Frontend shows "API Error"

**Check 1**: Verify API URL

```bash
# In frontend .env.production or Vercel env vars
VITE_API_URL=https://your-correct-backend.railway.app
```

**Check 2**: Check backend is running

- Visit `https://your-backend.railway.app/api/diagnostic`
- Should return JSON, not an error

**Check 3**: Check CORS

- Go to Railway → Backend → Variables
- Make sure `CORS_ALLOWED_ORIGINS` has your Vercel URL

### Database Connection Failed

1. Go to Railway → PostgreSQL service
2. Check "Variables" tab
3. Make sure backend service can reference it with `${{Postgres.PGHOST}}`

### Migration Errors

```bash
# In Railway Shell
php artisan migrate:fresh --force
php artisan db:seed --force
```

### Backend Won't Start

Check logs in Railway:

1. Click backend service
2. Go to "Deployments" tab
3. Click latest deployment
4. Check logs for errors

Common fixes:

- Make sure `APP_KEY` is set
- Verify database credentials
- Check PHP version compatibility

---

## 📊 Monitoring Your App

### Railway Dashboard

- View logs: Backend service → Deployments → Click deployment
- Monitor resources: Backend service → Metrics
- Database size: PostgreSQL service → Metrics

### Vercel Dashboard

- View analytics: Your project → Analytics
- Check deployments: Your project → Deployments
- View logs: Deployment → Build Logs

---

## 💰 Cost Breakdown

### Railway (Backend + Database)

- **Trial**: $5 free credit
- **Starter Plan**: $5/month
  - Includes: 512MB RAM, shared CPU, 1GB storage
  - PostgreSQL: Included

### Vercel (Frontend)

- **Hobby Plan**: FREE
  - 100GB bandwidth/month
  - Automatic HTTPS
  - Global CDN

**Total**: ~$5-10/month

---

## 🔄 Automatic Deployments

Both Railway and Vercel will automatically deploy when you push to GitHub!

```bash
# Make changes to your code
git add .
git commit -m "Update feature X"
git push origin main

# Wait 1-2 minutes...
# ✅ Automatically deployed!
```

---

## 🚀 Next Steps

1. **Custom Domain** (Optional):
   - Railway: Settings → Custom Domain
   - Vercel: Settings → Domains
2. **Set Up Monitoring**:

   - Add Sentry for error tracking
   - Set up uptime monitoring (UptimeRobot)

3. **Performance**:

   - Enable Redis for caching on Railway
   - Set up CDN for images

4. **Backups**:
   - Railway PostgreSQL has automatic daily backups
   - Configure retention in database settings

---

## 📞 Need Help?

- Railway Docs: https://docs.railway.app
- Vercel Docs: https://vercel.com/docs
- Laravel Deployment: https://laravel.com/docs/deployment
- Vue.js Deployment: https://vuejs.org/guide/best-practices/production-deployment.html

---

## ✅ Success Checklist

- [ ] Backend deployed on Railway
- [ ] PostgreSQL database created
- [ ] Backend environment variables set
- [ ] APP_KEY generated
- [ ] Migrations run successfully
- [ ] Backend API accessible
- [ ] Frontend deployed on Vercel
- [ ] Frontend environment variables set
- [ ] CORS configured
- [ ] App working end-to-end
- [ ] Custom domain configured (optional)

**Your app is now LIVE!** 🎉🚀

Share your app URL and start getting users!
