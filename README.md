# 🎓 VocabMaster - Vocabulary Training Application

A full-stack web application for learning English vocabulary with interactive training modes, spaced repetition algorithm, and progress tracking.

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

## ✨ Features

- 📚 **6 CEFR Levels** - From Elementary (A1) to Proficient (C2)
- 🎯 **Interactive Word Training** - Images, audio pronunciation, and definitions
- 🧠 **4 Learning Modes**:
  - 📝 Quiz (Multiple choice)
  - 🖼️ Image Matching
  - 🎧 Listening Practice
  - ✍️ Writing Practice
- 🔄 **Spaced Repetition** - SM-2 algorithm for optimal learning
- 📊 **Progress Tracking** - Statistics, streaks, and accuracy metrics
- 🌙 **Dark Mode** - Eye-friendly interface
- 📱 **Responsive Design** - Works on all devices
- 💾 **Local Storage** - Continue where you left off

## 🏗️ Architecture

### Frontend

- **Framework:** Vue 3 (Composition API)
- **Build Tool:** Vite
- **State Management:** Pinia
- **Routing:** Vue Router
- **Styling:** Tailwind CSS
- **Deployment:** Netlify/Vercel

### Backend

- **Framework:** Laravel 11
- **Database:** PostgreSQL
- **API:** RESTful
- **External APIs:** Free Dictionary API, Unsplash/Pexels
- **Deployment:** Traditional hosting (shared/VPS)

## 🚀 Quick Start

### Prerequisites

- **Frontend:** Node.js 18+, npm
- **Backend:** PHP 8.2+, Composer, PostgreSQL 13+
- **Optional:** Docker & Docker Compose

### Option 1: Docker (Recommended)

1. **Clone the repository:**

```bash
git clone <your-repo-url>
cd vocabulary
```

2. **Start backend with Docker:**

```bash
cd backend
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed  # Optional: seed sample words
```

Backend API: `http://localhost:8000`

3. **Start frontend:**

```bash
cd ../frontend
npm install
npm run dev
```

Frontend: `http://localhost:5173`

### Option 2: Local Installation

#### Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=vocabulary
# DB_USERNAME=postgres
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate
php artisan db:seed  # Optional

# Start server
php artisan serve
```

#### Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Configure environment
cp .env.example .env
# Edit .env and set VITE_API_URL=http://localhost:8000/api

# Start development server
npm run dev
```

## 📖 Usage

1. **Select Your Level** - Choose from A1 (Elementary) to C2 (Proficient)
2. **Train with Words** - Learn new vocabulary with images and audio
3. **Add to Vocabulary** - Save words you want to practice
4. **Practice with Different Modes**:
   - Take quizzes to test knowledge
   - Match images with words
   - Listen and type what you hear
   - Write words from definitions
5. **Track Progress** - View statistics, streaks, and improvement

## 📁 Project Structure

```
vocabulary/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/migrations/
│   ├── routes/api.php
│   └── README.md
├── frontend/                # Vue 3 Application
│   ├── src/
│   │   ├── components/
│   │   ├── views/
│   │   ├── stores/
│   │   ├── services/
│   │   └── router/
│   └── README.md
├── .github/workflows/       # CI/CD pipelines
└── README.md
```

## 🔧 Configuration

### Backend Environment Variables

```env
APP_URL=http://localhost:8000
DB_CONNECTION=pgsql
DB_DATABASE=vocabulary
FRONTEND_URL=http://localhost:5173

# Optional: For better images
UNSPLASH_ACCESS_KEY=your_key
PEXELS_API_KEY=your_key
```

### Frontend Environment Variables

```env
VITE_API_URL=http://localhost:8000/api
VITE_APP_NAME=VocabMaster
```

## 🚢 Deployment

### Frontend (Netlify/Vercel)

**Netlify:**

1. Connect GitHub repository
2. Build settings:
   - Build command: `npm run build`
   - Publish directory: `frontend/dist`
3. Set environment variable: `VITE_API_URL`

**Vercel:**

1. Import GitHub repository
2. Framework preset: Vite
3. Root directory: `frontend`
4. Set environment variable: `VITE_API_URL`

### Backend (Traditional Hosting)

1. Upload files to server
2. Configure `.env` for production
3. Run deployment commands:

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan migrate --force
```

4. Set proper permissions on `storage/` and `bootstrap/cache/`
5. Point web server to `public/` directory

## 🧪 Testing

### Backend

```bash
cd backend
php artisan test
```

### Frontend

```bash
cd frontend
npm run test  # If tests are configured
```

## 📊 API Documentation

### Main Endpoints

- `GET /api/words/level/{level}` - Get words by level
- `POST /api/user-words` - Save word for learning
- `GET /api/user-words/{sessionId}` - Get user's saved words
- `PUT /api/user-words/{id}` - Update word review status
- `GET /api/progress/{sessionId}` - Get progress statistics
- `GET /api/quiz/{sessionId}/{mode}` - Get quiz questions

See `backend/README.md` for complete API documentation.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👏 Acknowledgments

- [Free Dictionary API](https://dictionaryapi.dev/) for word definitions
- [Unsplash](https://unsplash.com/) and [Pexels](https://www.pexels.com/) for images
- [SuperMemo 2 Algorithm](https://www.supermemo.com/en/archives1990-2015/english/ol/sm2) for spaced repetition

## 📧 Contact

For questions or support, please open an issue on GitHub.

---

**Happy Learning! 📚✨**
