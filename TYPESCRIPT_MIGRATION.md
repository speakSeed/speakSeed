# TypeScript Migration Guide

## Overview

The Vocabulary Training project has been successfully migrated from JavaScript to TypeScript. This document outlines the changes made and how to work with the TypeScript codebase.

## What Was Changed

### 1. **Dependencies Installed**

```json
{
  "devDependencies": {
    "typescript": "^5.9.3",
    "@types/node": "^24.7.2",
    "@vue/tsconfig": "^0.8.1",
    "vue-tsc": "^3.1.1"
  }
}
```

### 2. **Configuration Files Created**

#### `tsconfig.json`

Main TypeScript configuration with strict type checking enabled:

- Target: ES2020
- Module: ESNext
- Strict mode enabled
- Path mappings: `@/*` → `./src/*`

#### `tsconfig.node.json`

Configuration for Node.js specific files (vite.config, etc.)

#### `src/env.d.ts`

Type declarations for:

- Vue components (`.vue` files)
- Vite environment variables
- `import.meta.env` typings

### 3. **Files Migrated**

#### Core Files

- ✅ `src/main.js` → `src/main.ts`
- ✅ `vite.config.js` → `vite.config.ts`
- ✅ `index.html` (updated script reference)

#### Type Definitions

- ✅ `src/types/index.ts` - Complete type definitions for:
  - `Word`, `UserWord`, `UserProgress`
  - `EnglishLevel`, `WordStatus`
  - API response types
  - Store state types
  - Quiz types

#### Services (with Full Type Annotations)

- ✅ `src/services/api.ts`
  - Typed API methods
  - Proper return types for all endpoints
  - Request/response type safety

#### Pinia Stores (with Type Safety)

- ✅ `src/stores/words.ts`
- ✅ `src/stores/userWords.ts`
- ✅ `src/stores/progress.ts`
- All state, getters, and actions properly typed

#### Router

- ✅ `src/router/index.ts`
  - Typed routes with `RouteRecordRaw`
  - Extended `RouteMeta` interface

#### Utilities

- ✅ `src/utils/localStorage.ts` - Type-safe local storage operations
- ✅ `src/utils/speechSynthesis.ts` - Typed speech synthesis interface

### 4. **Package.json Scripts Updated**

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vue-tsc && vite build", // Type check before build
    "type-check": "vue-tsc --noEmit", // Manual type checking
    "preview": "vite preview",
    "lint": "eslint . --ext .vue,.js,.jsx,.cjs,.mjs,.ts,.tsx --fix"
  }
}
```

## Type Definitions Reference

### Core Types

#### Word

```typescript
interface Word {
  id: number;
  word: string;
  level: string;
  difficulty: number;
  definition: string;
  phonetic: string | null;
  audioUrl: string | null;
  imageUrl: string | null;
  exampleSentence: string | null;
  meanings: Meaning[];
  synonyms: string[];
  createdAt: string;
  updatedAt: string;
}
```

#### UserWord

```typescript
interface UserWord {
  id: number;
  sessionId: string;
  wordId: number;
  status: WordStatus;
  addedAt: string | null;
  nextReviewDate: string | null;
  reviewCount: number;
  easeFactor: number;
  incorrectCount: number;
  interval: number;
  correctCount: number;
  createdAt: string;
  updatedAt: string;
  word?: Word;
}

type WordStatus = "new" | "learning" | "mastered";
```

#### EnglishLevel

```typescript
type EnglishLevel = "A1" | "A2" | "B1" | "B2" | "C1" | "C2";
```

### Store State Types

```typescript
interface WordsState {
  currentWord: Word | null;
  words: Word[];
  selectedLevel: EnglishLevel | null;
  loading: boolean;
  error: string | null;
}

interface UserWordsState {
  userWords: UserWord[];
  wordsForReview: UserWord[];
  loading: boolean;
  error: string | null;
}

interface ProgressState {
  progress: UserProgress | null;
  statistics: ProgressStatistics | null;
  loading: boolean;
  error: string | null;
}
```

## Usage Examples

### 1. Using Stores with TypeScript

```typescript
import { useWordsStore } from "@/stores/words";
import type { Word, EnglishLevel } from "@/types";

// In a Vue component
const wordsStore = useWordsStore();

// Type-safe actions
async function loadWords(level: EnglishLevel) {
  try {
    await wordsStore.fetchWordsByLevel(level, 20);
    // wordsStore.words is typed as Word[]
    console.log(wordsStore.words);
  } catch (error: any) {
    console.error(error.message);
  }
}
```

### 2. Using API Services

```typescript
import { wordApi } from "@/services/api";
import type { Word, ApiResponse } from "@/types";

// API calls are fully typed
async function getRandomWord(level: EnglishLevel) {
  const response: ApiResponse<Word> = await wordApi.getRandom(level);
  const word: Word = response.data;
  return word;
}
```

### 3. Vue Components with TypeScript

```vue
<script setup lang="ts">
import { ref, computed } from "vue";
import type { Word, EnglishLevel } from "@/types";

// Typed reactive state
const currentWord = ref<Word | null>(null);
const selectedLevel = ref<EnglishLevel>("A1");

// Typed computed property
const wordCount = computed((): number => {
  return words.value.length;
});

// Typed function
async function fetchWord(id: number): Promise<void> {
  try {
    const response = await wordApi.getWord(id);
    currentWord.value = response.data;
  } catch (error: any) {
    console.error("Error fetching word:", error.message);
  }
}
</script>
```

## Development Commands

### Type Checking

```bash
# Check types without building
npm run type-check

# This will show TypeScript errors without compiling
```

### Building

```bash
# Build with type checking
npm run build

# The build will fail if there are TypeScript errors
```

### Development

```bash
# Run dev server (no type checking, faster)
npm run dev

# Type errors will show in your IDE but won't stop the dev server
```

## Benefits of TypeScript Migration

### 1. **Type Safety**

- Catch errors at compile-time instead of runtime
- Autocomplete and IntelliSense in IDEs
- Refactoring is safer and easier

### 2. **Better Documentation**

- Types serve as inline documentation
- Function signatures are self-documenting
- API contracts are explicit

### 3. **Improved Developer Experience**

- Better IDE support
- Fewer runtime errors
- Easier onboarding for new developers

### 4. **Maintainability**

- Changes to types propagate through the codebase
- Breaking changes are caught immediately
- Code is more self-explanatory

## Common Patterns

### Handling API Responses

```typescript
// API responses are wrapped in ApiResponse<T>
const response: ApiResponse<Word> = await wordApi.getWord(1);
const word: Word = response.data;

// For collections
const response: ApiResponse<UserWord[]> = await userWordApi.getAll();
const userWords: UserWord[] = response.data || [];
```

### Error Handling

```typescript
try {
  await someAsyncOperation();
} catch (error: any) {
  // Use 'any' for caught errors to access properties
  console.error(error.message);
  // Or type guard for specific error types
  if (error instanceof Error) {
    console.error(error.message);
  }
}
```

### Optional Properties

```typescript
// Use optional chaining and nullish coalescing
const audioUrl = word.audioUrl ?? "default-audio.mp3";
const definition = word?.definition || "No definition available";
```

## Migration Checklist

- [x] Install TypeScript and related dependencies
- [x] Create `tsconfig.json` and `tsconfig.node.json`
- [x] Create type definitions in `src/types/index.ts`
- [x] Migrate core files (`main.ts`, `vite.config.ts`)
- [x] Migrate API service with types
- [x] Migrate Pinia stores with types
- [x] Migrate router with types
- [x] Migrate utility files with types
- [x] Update `package.json` scripts
- [x] Test compilation and dev server
- [ ] Convert Vue components to `<script setup lang="ts">` (gradual migration)
- [ ] Add type checks to CI/CD pipeline

## Vue Components Migration

Vue components can be migrated gradually. To use TypeScript in a component:

```vue
<script setup lang="ts">
import { ref } from "vue";
import type { Word } from "@/types";

// Your TypeScript code here
const word = ref<Word | null>(null);
</script>
```

Components without `lang="ts"` will continue to work as JavaScript.

## Troubleshooting

### Issue: "Cannot find module '@/types'"

**Solution**: Make sure `tsconfig.json` has the correct path mapping:

```json
{
  "compilerOptions": {
    "baseUrl": ".",
    "paths": {
      "@/*": ["./src/*"]
    }
  }
}
```

### Issue: "Type errors in node_modules"

**Solution**: Enable `skipLibCheck` in `tsconfig.json` (already enabled)

### Issue: "Vite doesn't recognize .ts files"

**Solution**: Ensure `vite.config.ts` exists and imports use `.ts` extensions where needed

## Next Steps

1. **Gradual Component Migration**: Convert Vue components to use `<script setup lang="ts">` one by one
2. **Stricter Types**: Remove `any` types and replace with more specific types
3. **Add JSDoc Comments**: Enhance type definitions with documentation
4. **CI/CD Integration**: Add `npm run type-check` to your CI pipeline
5. **E2E Type Safety**: Ensure backend API matches frontend types

## Resources

- [TypeScript Documentation](https://www.typescriptlang.org/docs/)
- [Vue 3 TypeScript Guide](https://vuejs.org/guide/typescript/overview.html)
- [Pinia TypeScript Support](https://pinia.vuejs.org/core-concepts/)
- [Vite TypeScript Guide](https://vitejs.dev/guide/features.html#typescript)

---

**Migration Status**: ✅ Complete
**Date**: October 12, 2025
**TypeScript Version**: 5.9.3
