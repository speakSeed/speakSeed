#!/bin/bash

echo "🚀 Starting Vocabulary Training Application..."
echo ""

# Switch to Node 23
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
nvm use 23.1.0

# Add PostgreSQL to PATH
export PATH="/opt/homebrew/opt/postgresql@16/bin:$PATH"

# Start PostgreSQL if not running
if ! pg_isready -q; then
    echo "📦 Starting PostgreSQL..."
    brew services start postgresql@16
    sleep 2
fi

# Check database
if ! psql -lqt | cut -d \| -f 1 | grep -qw vocabulary_db; then
    echo "📊 Creating database..."
    createdb vocabulary_db
fi

# Backend Setup
echo "🔧 Setting up Backend..."
cd backend

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "📦 Installing PHP dependencies..."
    composer install --no-interaction
fi

# Check if .env exists and has APP_KEY
if [ ! -f ".env" ] || ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi

# Run migrations if needed
echo "🗄️  Running migrations..."
php artisan migrate --force

# Seed database if words table is empty
WORD_COUNT=$(psql -d vocabulary_db -t -c "SELECT COUNT(*) FROM words;" | tr -d '[:space:]')
if [ "$WORD_COUNT" -eq "0" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed
fi

# Start backend server
echo "🚀 Starting Laravel backend on http://127.0.0.1:8000..."
php artisan serve --host=127.0.0.1 --port=8000 > /tmp/laravel.log 2>&1 &
BACKEND_PID=$!
sleep 3

# Frontend Setup
echo "🎨 Setting up Frontend..."
cd ../frontend

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "📦 Installing npm dependencies..."
    npm install
fi

# Start frontend server
echo "🚀 Starting Vue frontend..."
npm run dev > /tmp/vite.log 2>&1 &
FRONTEND_PID=$!
sleep 5

echo ""
echo "✅ Application Started Successfully!"
echo ""
echo "📍 Backend API: http://127.0.0.1:8000"
echo "📍 Frontend UI: http://localhost:5173"
echo ""
echo "🔍 Test Backend: curl http://127.0.0.1:8000/api/words/random?level=A1&session_id=test"
echo ""
echo "📝 Logs:"
echo "   Backend: tail -f /tmp/laravel.log"
echo "   Frontend: tail -f /tmp/vite.log"
echo ""
echo "🛑 To stop: pkill -f 'php artisan serve' && pkill -f 'vite'"
echo ""

# Test backend
sleep 2
echo "🧪 Testing backend API..."
curl -s "http://127.0.0.1:8000/api/words/random?level=A1&session_id=test123" | head -20

echo ""
echo "🎉 Ready to use! Open http://localhost:5173 in your browser"

