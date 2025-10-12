# Quick Start Guide

## 🚀 Launch Application

### Using the Startup Script (Recommended)

```bash
cd /Users/shutenchik/projects/vocabulary
./startup.sh
```

This will automatically:

- Switch to Node.js v23.1.0
- Start PostgreSQL
- Run Laravel backend on `http://127.0.0.1:8000`
- Start Vue frontend on `http://localhost:5173`

### Manual Launch

**Backend:**

```bash
cd backend
php artisan serve --host=127.0.0.1 --port=8000
```

**Frontend:**

```bash
cd frontend
npm run dev
```

## 🌐 Access Points

- **Frontend**: http://localhost:5173
- **Backend API**: http://127.0.0.1:8000/api
- **API Health Check**: http://127.0.0.1:8000/api/health

## 📚 Key Endpoints

### Words

- `GET /api/words/level/{level}?per_page=20` - Get words by level (A1, A2, B1, B2, C1, C2)
- `GET /api/words/random?level={level}` - Get random word
- `GET /api/words/{id}` - Get specific word
- `POST /api/words/fetch` - Fetch word from external API

### User Words

- `GET /api/user-words` - Get all saved words
- `POST /api/user-words` - Add word to learning list
- `PUT /api/user-words/{id}` - Update review status
- `DELETE /api/user-words/{id}` - Remove word
- `GET /api/user-words/review` - Get words due for review

### Progress

- `GET /api/progress` - Get user progress and statistics
- `POST /api/progress` - Update progress

## 🎯 Development Commands

### TypeScript

```bash
cd frontend

# Type check without building
npm run type-check

# Build with type checking
npm run build

# Development mode
npm run dev
```

### Backend

```bash
cd backend

# Database
php artisan migrate:fresh --seed

# Clear cache
php artisan cache:clear
php artisan config:clear

# Create new migration
php artisan make:migration create_table_name

# Create new model
php artisan make:model ModelName

# Create new controller
php artisan make:controller ControllerName
```

## 📁 Project Structure

```
vocabulary/
├── backend/              # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/  # API controllers
│   │   │   └── Resources/    # API resources (camelCase conversion)
│   │   ├── Models/           # Eloquent models
│   │   └── Services/         # Business logic
│   ├── database/
│   │   ├── migrations/       # Database migrations
│   │   └── seeders/          # Database seeders
│   └── routes/
│       └── api.php           # API routes
│
└── frontend/            # Vue 3 + TypeScript
    ├── src/
    │   ├── components/      # Reusable Vue components
    │   ├── views/           # Page components
    │   ├── stores/          # Pinia stores (typed)
    │   ├── services/        # API services (typed)
    │   ├── router/          # Vue Router (typed)
    │   ├── types/           # TypeScript type definitions
    │   └── utils/           # Utility functions (typed)
    └── public/              # Static assets
```

## 🔑 Environment Variables

### Backend (`.env`)

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=vocabulary_db
DB_USERNAME=postgres
DB_PASSWORD=password

UNSPLASH_ACCESS_KEY=your_key_here
PEXELS_API_KEY=your_key_here
```

### Frontend (`.env`)

```env
VITE_API_URL=http://127.0.0.1:8000/api
```

## 🧪 Testing

### Backend

```bash
cd backend
php artisan test
```

### Frontend

```bash
cd frontend
npm run type-check  # TypeScript type checking
npm run lint        # ESLint
```

## 🐛 Troubleshooting

### Backend Issues

```bash
# Check if backend is running
curl http://127.0.0.1:8000/api/health

# View logs
tail -f /tmp/laravel.log
tail -f storage/logs/laravel.log

# Restart backend
pkill -f "php artisan serve"
php artisan serve --host=127.0.0.1 --port=8000
```

### Frontend Issues

```bash
# Check if frontend is running
curl http://localhost:5173

# View logs
tail -f /tmp/vite.log

# Restart frontend
pkill -f "vite"
cd frontend && npm run dev
```

### Database Issues

```bash
# Reset database
cd backend
php artisan migrate:fresh --seed

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### TypeScript Errors

```bash
# Check for type errors
cd frontend
npm run type-check

# Common fixes:
# 1. Clear node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# 2. Restart TypeScript server in VSCode
# CMD+Shift+P → "TypeScript: Restart TS Server"
```

## 📖 Documentation

- `README.md` - Project overview
- `TYPESCRIPT_MIGRATION.md` - TypeScript migration guide
- `LATEST_UPDATES.md` - Recent changes and improvements
- `FRONTEND_FIXES.md` - Frontend bug fixes
- `CAMELCASE_MIGRATION.md` - API camelCase conversion
- `API_INTEGRATION.md` - External API integration guide
- `PROJECT_SUMMARY.md` - Complete project summary

## 💡 Tips

1. **Session ID**: The frontend automatically generates a session ID and stores it in localStorage. This is used to track user progress without authentication.

2. **Mock Data**: The database is seeded with sample words in different levels. You can add more words through the API or seeder.

3. **Type Safety**: All API responses are now typed. Use the types from `frontend/src/types/index.ts` in your code.

4. **Hot Reload**: Both backend and frontend support hot reloading. Changes are reflected automatically.

5. **Browser DevTools**: Use Vue DevTools extension for debugging Vue components and Pinia stores.

## 🎓 Learning Resources

### TypeScript

- [TypeScript Handbook](https://www.typescriptlang.org/docs/handbook/)
- [Vue 3 + TypeScript](https://vuejs.org/guide/typescript/overview.html)

### Vue 3

- [Vue 3 Documentation](https://vuejs.org/)
- [Pinia Documentation](https://pinia.vuejs.org/)

### Laravel

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel API Resources](https://laravel.com/docs/eloquent-resources)

---

**Ready to start coding? Run `./startup.sh` and visit http://localhost:5173** 🚀
