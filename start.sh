#!/bin/bash

# VocabMaster Startup Script
# This script helps you quickly start both frontend and backend

set -e

echo "🎓 VocabMaster - Vocabulary Training Application"
echo "================================================"
echo ""

# Check if Docker is available
if command -v docker &> /dev/null && command -v docker-compose &> /dev/null; then
    echo "✓ Docker detected"
    USE_DOCKER=true
else
    echo "⚠ Docker not found. Using local installation."
    USE_DOCKER=false
fi

# Function to start backend
start_backend() {
    echo ""
    echo "🔧 Starting Backend..."
    cd backend

    if [ "$USE_DOCKER" = true ]; then
        echo "Using Docker..."
        
        if [ ! -f ".env" ]; then
            echo "Creating .env file..."
            cp .env.example .env
        fi

        docker-compose up -d
        
        echo "Installing dependencies..."
        docker-compose exec -T app composer install || true
        
        echo "Generating application key..."
        docker-compose exec -T app php artisan key:generate || true
        
        echo "Running migrations..."
        docker-compose exec -T app php artisan migrate || true
        
        echo ""
        echo "✓ Backend started on http://localhost:8000"
        echo "  Database: PostgreSQL on port 5432"
    else
        echo "Using local PHP..."
        
        if ! command -v php &> /dev/null; then
            echo "❌ PHP not found. Please install PHP 8.2+"
            exit 1
        fi

        if ! command -v composer &> /dev/null; then
            echo "❌ Composer not found. Please install Composer"
            exit 1
        fi

        if [ ! -f ".env" ]; then
            echo "Creating .env file..."
            cp .env.example .env
            php artisan key:generate
        fi

        if [ ! -d "vendor" ]; then
            echo "Installing dependencies..."
            composer install
        fi

        echo "Starting Laravel development server..."
        php artisan serve &
        BACKEND_PID=$!
        echo "✓ Backend started on http://localhost:8000 (PID: $BACKEND_PID)"
    fi

    cd ..
}

# Function to start frontend
start_frontend() {
    echo ""
    echo "🎨 Starting Frontend..."
    cd frontend

    if ! command -v node &> /dev/null; then
        echo "❌ Node.js not found. Please install Node.js 18+"
        exit 1
    fi

    if ! command -v npm &> /dev/null; then
        echo "❌ npm not found. Please install npm"
        exit 1
    fi

    if [ ! -f ".env" ]; then
        echo "Creating .env file..."
        echo "VITE_API_URL=http://localhost:8000/api" > .env
        echo "VITE_APP_NAME=VocabMaster" >> .env
    fi

    if [ ! -d "node_modules" ]; then
        echo "Installing dependencies..."
        npm install
    fi

    echo "Starting Vite development server..."
    npm run dev &
    FRONTEND_PID=$!
    echo "✓ Frontend started on http://localhost:5173 (PID: $FRONTEND_PID)"

    cd ..
}

# Main execution
echo "Starting services..."
start_backend
start_frontend

echo ""
echo "================================================"
echo "✓ All services started successfully!"
echo ""
echo "📱 Frontend: http://localhost:5173"
echo "🔌 Backend API: http://localhost:8000/api"
echo ""
echo "Press Ctrl+C to stop all services"
echo "================================================"
echo ""

# Wait for user interrupt
trap 'echo ""; echo "Stopping services..."; kill $FRONTEND_PID 2>/dev/null; [ "$USE_DOCKER" = true ] && cd backend && docker-compose down; echo "✓ Services stopped"; exit 0' INT

# Keep script running
wait

