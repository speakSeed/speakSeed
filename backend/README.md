# Vocabulary Training API Backend

Laravel REST API backend for the Vocabulary Training Application.

## Features

- RESTful API for vocabulary management
- External Dictionary API integration
- Image API integration (Unsplash/Pexels)
- Spaced Repetition Algorithm (SM-2)
- Progress tracking and statistics
- Quiz generation (multiple modes)
- PostgreSQL database

## Requirements

- PHP 8.2 or higher
- Composer
- PostgreSQL 13 or higher
- Extensions: pdo_pgsql, mbstring, openssl, tokenizer, xml, ctype, json

## Installation

### Using Composer (Local Development)

1. Install dependencies:

```bash
composer install
```

2. Copy environment file:

```bash
cp .env.example .env
```

3. Generate application key:

```bash
php artisan key:generate
```

4. Configure database in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=vocabulary
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

5. Run migrations:

```bash
php artisan migrate
```

6. Seed the database (optional):

```bash
php artisan db:seed
```

7. Start development server:

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

### Using Docker (Recommended)

1. Start Docker containers:

```bash
docker-compose up -d
```

2. Install dependencies:

```bash
docker-compose exec app composer install
```

3. Generate key and run migrations:

```bash
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

## API Endpoints

### Words

- `GET /api/words/level/{level}` - Get words by level (A1-C2)
- `GET /api/words/random` - Get random words
- `GET /api/words/{id}` - Get single word
- `POST /api/words/fetch` - Fetch word from external API

### User Words

- `GET /api/user-words/{sessionId}` - Get user's saved words
- `GET /api/user-words/{sessionId}/due` - Get words due for review
- `POST /api/user-words` - Save word for learning
- `PUT /api/user-words/{id}` - Update word review
- `DELETE /api/user-words/{id}` - Remove word

### Progress

- `GET /api/progress/{sessionId}` - Get progress statistics
- `POST /api/progress` - Update progress

### Quiz

- `GET /api/quiz/{sessionId}/{mode}` - Get quiz questions
  - Modes: `quiz`, `images`, `listening`, `writing`

### Health Check

- `GET /api/health` - API health check

## Environment Variables

```env
# External APIs (Optional)
UNSPLASH_ACCESS_KEY=your_key_here
PEXELS_API_KEY=your_key_here

# CORS Configuration
FRONTEND_URL=http://localhost:5173
```

## Testing

Run tests:

```bash
php artisan test
```

Run specific test:

```bash
php artisan test --filter=WordControllerTest
```

## Deployment

### Production Checklist

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Configure PostgreSQL connection
3. Run optimization commands:

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. Run migrations:

```bash
php artisan migrate --force
```

5. Set up proper permissions:

```bash
chmod -R 755 storage bootstrap/cache
```

### Using GitHub Actions

The project includes CI/CD workflows for automated deployment. See `.github/workflows/` for configuration.

## API Documentation

Detailed API documentation is available in `docs/api.md`.

## License

MIT License
