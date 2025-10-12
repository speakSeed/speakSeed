# CamelCase Migration Guide

## Overview

All API responses have been updated to use **camelCase** format instead of snake_case to align with JavaScript/Vue naming conventions.

## Changes Made

### Backend Changes

#### 1. Created API Resources

Three new API Resource classes were created to transform model data to camelCase:

- `app/Http/Resources/WordResource.php`
- `app/Http/Resources/UserWordResource.php`
- `app/Http/Resources/UserProgressResource.php`

#### 2. Updated Controllers

All controllers now return API Resources instead of raw JSON:

**WordController:**

```php
// Before
return response()->json($word);

// After
return new WordResource($word);
```

**UserWordController:**

```php
// Before
return response()->json($words);

// After
return UserWordResource::collection($words);
```

**ProgressController:**

```php
// Before
return response()->json([
    'progress' => $progress,
    'words_by_status' => [...]
]);

// After
return response()->json([
    'progress' => new UserProgressResource($progress),
    'wordsByStatus' => [...]
]);
```

### Frontend Changes

#### Updated Progress Store

The progress store (`frontend/src/stores/progress.js`) was updated to:

1. Expect camelCase keys from the backend
2. Use camelCase internally for consistency

**Changed Keys:**

- `words_by_status` → `wordsByStatus`
- `words_by_level` → `wordsByLevel`
- `due_for_review` → `dueForReview`
- `current_streak` → `currentStreak`
- `longest_streak` → `longestStreak`
- `total_words` → `totalWords`
- `mastered_words` → `masteredWords`

## API Response Examples

### Word Endpoint

**Before (snake_case):**

```json
{
  "id": 1,
  "word": "hello",
  "audio_url": "https://...",
  "image_url": "https://...",
  "example_sentence": "Hello, how are you?",
  "created_at": "2025-10-12T00:00:00Z"
}
```

**After (camelCase):**

```json
{
  "data": {
    "id": 1,
    "word": "hello",
    "audioUrl": "https://...",
    "imageUrl": "https://...",
    "exampleSentence": "Hello, how are you?",
    "createdAt": "2025-10-12T00:00:00+00:00"
  }
}
```

### Progress Endpoint

**Before (snake_case):**

```json
{
  "progress": {
    "current_streak": 5,
    "total_words": 20
  },
  "words_by_status": {
    "learning": 10
  }
}
```

**After (camelCase):**

```json
{
  "progress": {
    "currentStreak": 5,
    "totalWords": 20
  },
  "wordsByStatus": {
    "learning": 10
  }
}
```

## Resource Wrapping

Note that single resources are now wrapped in a `data` object (Laravel default):

```json
{
    "data": {
        "id": 1,
        "word": "hello",
        ...
    }
}
```

Collections remain unwrapped:

```json
{
    "data": [
        { "id": 1, ... },
        { "id": 2, ... }
    ]
}
```

## Testing

Test the updated API:

```bash
# Test Word endpoint
curl "http://127.0.0.1:8000/api/words/random?level=A1&session_id=test" | jq .

# Test Progress endpoint
curl "http://127.0.0.1:8000/api/progress?session_id=test" | jq .

# Test User Words endpoint
curl "http://127.0.0.1:8000/api/user-words?session_id=test" | jq .
```

## Benefits

1. **Consistency:** All JavaScript/Vue code uses camelCase
2. **No manual transformation:** Resources handle the conversion
3. **Type safety:** Easier to work with in TypeScript if needed
4. **Industry standard:** camelCase is the JavaScript convention

## Migration Checklist

- [x] Create API Resources for all models
- [x] Update all controllers to use resources
- [x] Update frontend stores to expect camelCase
- [x] Test all API endpoints
- [x] Update documentation

## Future Considerations

If you add new models or endpoints:

1. Create an API Resource: `php artisan make:resource YourModelResource`
2. Map snake_case fields to camelCase in the `toArray()` method
3. Return the resource from your controller
4. Update frontend stores/services to use camelCase

## Notes

- Dates are now in ISO 8601 format with timezone
- Nested relationships (e.g., `word` in `UserWord`) are also transformed
- Error responses remain as-is (Laravel standard format)
