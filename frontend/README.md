# Vocabulary Training Frontend

Vue 3 frontend application for vocabulary learning with interactive modes and spaced repetition.

## Features

- 🎯 6 CEFR levels (A1-C2)
- 📚 Word training with images and audio
- 🧠 4 learning modes: Quiz, Image Matching, Listening, Writing
- 📊 Progress tracking with statistics
- 🔄 Spaced repetition algorithm
- 🌙 Dark mode support
- 📱 Responsive design
- 💾 Local storage for offline capability

## Tech Stack

- **Framework:** Vue 3 (Composition API)
- **Build Tool:** Vite
- **State Management:** Pinia
- **Routing:** Vue Router
- **Styling:** Tailwind CSS
- **HTTP Client:** Axios
- **Charts:** Chart.js

## Prerequisites

- Node.js 18+ and npm
- Backend API running (see backend/README.md)

## Installation

1. **Install dependencies:**

```bash
npm install
```

2. **Configure environment:**

```bash
# Create .env file
cp .env.example .env

# Edit .env and set your API URL
VITE_API_URL=http://localhost:8000/api
```

3. **Start development server:**

```bash
npm run dev
```

The app will be available at `http://localhost:5173`

## Development

### Project Structure

```
src/
├── assets/           # Images and styles
├── components/       # Reusable components
│   ├── WordCard.vue
│   ├── LevelBadge.vue
│   ├── ProgressBar.vue
│   └── LoadingSpinner.vue
├── views/            # Page components
│   ├── LevelSelection.vue
│   ├── WordTraining.vue
│   ├── VocabularyDashboard.vue
│   ├── Quiz.vue
│   ├── ImageMatching.vue
│   ├── Listening.vue
│   ├── Writing.vue
│   └── Progress.vue
├── stores/           # Pinia stores
│   ├── words.js
│   ├── userWords.js
│   └── progress.js
├── services/         # API services
│   └── api.js
├── utils/            # Helper functions
│   ├── localStorage.js
│   └── speechSynthesis.js
├── router/           # Vue Router
│   └── index.js
├── App.vue           # Root component
└── main.js           # Entry point
```

### Available Scripts

```bash
# Development server with hot reload
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview

# Lint and fix files
npm run lint
```

## Building for Production

```bash
# Build optimized production bundle
npm run build

# The dist/ folder will contain the production-ready files
```

## Deployment

### Netlify

1. Connect your GitHub repository to Netlify
2. Set build settings:
   - Build command: `npm run build`
   - Publish directory: `dist`
3. Set environment variables in Netlify dashboard:
   - `VITE_API_URL`: Your production API URL

Or use the included `netlify.toml` configuration.

### Vercel

1. Import your project to Vercel
2. Vercel will auto-detect Vite configuration
3. Set environment variables:
   - `VITE_API_URL`: Your production API URL

Or use the included `vercel.json` configuration.

### Manual Deployment

1. Build the project:

```bash
npm run build
```

2. Upload the `dist/` folder to your web server

3. Configure your server to serve `index.html` for all routes (SPA)

## Features Guide

### Level Selection

Choose from 6 English proficiency levels (A1-C2) to start learning vocabulary appropriate for your skill level.

### Word Training

- View word with image, definition, and example
- Listen to pronunciation using Web Speech API
- Spell out words letter-by-letter
- Add words to your learning list

### Learning Modes

**Quiz Mode:** Multiple choice questions to test word recognition

**Image Matching:** Match words with corresponding images

**Listening Practice:** Type what you hear to improve listening skills

**Writing Practice:** Write words from definitions and visual cues

### Progress Tracking

- Total words learned
- Mastery progress
- Daily streaks
- Accuracy percentage
- Progress by level

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Opera 76+

**Note:** Web Speech API is required for audio features and may not be available in all browsers.

## Troubleshooting

### API Connection Issues

Ensure the backend is running and the `VITE_API_URL` is correctly set in `.env`

### Build Errors

```bash
# Clear node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

### Speech Synthesis Not Working

Check browser compatibility and ensure HTTPS (required for some browsers)

## License

MIT License
