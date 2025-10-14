#!/bin/bash

# Vocabulary App - Railway Deployment Script
# This script helps set up deployment on Railway

set -e

echo "🚀 Vocabulary App - Railway Deployment Helper"
echo "=============================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Railway CLI is installed
if ! command -v railway &> /dev/null; then
    echo -e "${RED}❌ Railway CLI not found${NC}"
    echo ""
    echo "Install it with:"
    echo "  npm i -g @railway/cli"
    echo ""
    echo "Or use the Railway web dashboard instead:"
    echo "  https://railway.app"
    exit 1
fi

echo -e "${GREEN}✓ Railway CLI found${NC}"
echo ""

# Check if logged in
if ! railway whoami &> /dev/null; then
    echo -e "${YELLOW}⚠ Not logged in to Railway${NC}"
    echo "Please login first:"
    railway login
fi

echo -e "${GREEN}✓ Logged in to Railway${NC}"
echo ""

# Ask for project setup
echo "Do you want to:"
echo "1. Create a new Railway project"
echo "2. Link to existing project"
read -p "Enter choice (1 or 2): " choice

if [ "$choice" == "1" ]; then
    echo ""
    echo "Creating new Railway project..."
    railway init
    echo -e "${GREEN}✓ Project created${NC}"
elif [ "$choice" == "2" ]; then
    echo ""
    echo "Linking to existing project..."
    railway link
    echo -e "${GREEN}✓ Project linked${NC}"
else
    echo -e "${RED}Invalid choice${NC}"
    exit 1
fi

echo ""
echo "📦 Next steps:"
echo ""
echo "1. Add PostgreSQL database:"
echo "   - Go to https://railway.app"
echo "   - Open your project"
echo "   - Click 'New' → 'Database' → 'PostgreSQL'"
echo ""
echo "2. Deploy backend:"
echo "   - Click 'New' → 'GitHub Repo'"
echo "   - Select your vocabulary repository"
echo "   - Set root directory to 'backend'"
echo "   - Set start command to: php artisan serve --host=0.0.0.0 --port=\$PORT"
echo ""
echo "3. Set environment variables in Railway dashboard:"
echo "   APP_NAME=\"Vocabulary API\""
echo "   APP_ENV=production"
echo "   APP_KEY=base64:YOUR_KEY"
echo "   APP_DEBUG=false"
echo "   DB_CONNECTION=pgsql"
echo "   DB_HOST=\${{Postgres.PGHOST}}"
echo "   DB_PORT=\${{Postgres.PGPORT}}"
echo "   DB_DATABASE=\${{Postgres.PGDATABASE}}"
echo "   DB_USERNAME=\${{Postgres.PGUSER}}"
echo "   DB_PASSWORD=\${{Postgres.PGPASSWORD}}"
echo ""
echo "4. Generate APP_KEY:"
echo "   railway run php artisan key:generate --show"
echo ""
echo "5. Run migrations:"
echo "   railway run php artisan migrate --force"
echo "   railway run php artisan db:seed --force"
echo ""
echo "6. Get your backend URL and deploy frontend to Vercel"
echo ""
echo -e "${GREEN}✓ Railway setup complete!${NC}"
echo ""
echo "📚 For more details, see QUICK_DEPLOY.md"

