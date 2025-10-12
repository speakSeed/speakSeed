# Free Dictionary API Integration Guide

## Overview

This application uses the **Free Dictionary API** (dictionaryapi.dev) to fetch real word data including definitions, pronunciations, phonetics, and example sentences.

**Best part: No API key required! 🎉**

## API Endpoint

```
https://api.dictionaryapi.dev/api/v2/entries/en/{word}
```

## Features Implemented

### 1. Aggressive Caching (7 Days)

- **Cache Duration**: 7 days (604,800 seconds)
- **Purpose**: Minimize API calls and improve performance
- **Result**: After first fetch, subsequent requests use cached data
- **Storage**: Laravel Cache (configurable: database, Redis, Memcached)

### 2. Rate Limiting

- **Limit**: 100 requests per minute per IP
- **Protection**: Prevents API abuse and ensures fair usage
- **Fallback**: Returns cached data or null if limit exceeded

### 3. Difficulty Calculation

Words are automatically assigned difficulty levels (1-5) based on length:

- **1**: ≤ 4 letters (e.g., "cat", "book")
- **2**: 5-6 letters (e.g., "house", "travel")
- **3**: 7-8 letters (e.g., "computer", "beautiful")
- **4**: 9-10 letters (e.g., "technology", "restaurant")
- **5**: 11+ letters (e.g., "quintessential", "entrepreneurship")

### 4. Hybrid Approach

**Database Seeding**:

```bash
php artisan migrate:fresh
php artisan db:seed --class=WordSeeder
```

- Seeds ~120 words (20 per level: A1, A2, B1, B2, C1, C2)
- First run: Fetches from API
- Subsequent runs: Uses 7-day cache (instant!)

**On-Demand Fetching**:

```bash
GET /api/words/fetch-random/{level}
```

- Dynamically fetches new words when needed
- Expands database beyond initial seed
- Automatically caches results

## Configuration

### No API Key Needed!

Unlike commercial APIs, Free Dictionary API doesn't require authentication:

```php
// DictionaryApiService.php
private const DICTIONARY_API_URL = 'https://api.dictionaryapi.dev/api/v2/entries/en/';
private const CACHE_TTL = 604800; // 7 days
private const RATE_LIMIT_MAX = 100; // per minute
```

### Cache Configuration

Update `config/cache.php` for production:

```php
// For better performance, use Redis or Memcached
'default' => env('CACHE_DRIVER', 'redis'),
```

## Usage Examples

### 1. Seed Database with Real Data

```bash
# Clear database and seed with real API data
php artisan migrate:fresh
php artisan db:seed

# Output:
# Starting word seeding with Free Dictionary API...
# Note: First run fetches from API, subsequent runs use 7-day cache.
#
# Processing level A1...
# → Fetching: hello
# ✓ Created: hello (difficulty: 2)
# → Fetching: goodbye
# ✓ Created: goodbye (difficulty: 3)
# ...
# 🎉 Word seeding completed! Total words: 120
```

### 2. Fetch Random Word for Level

**Request**:

```bash
curl "http://localhost:8000/api/words/fetch-random/A2?session_id=test"
```

**Response**:

```json
{
  "data": {
    "id": 15,
    "word": "beautiful",
    "level": "A2",
    "difficulty": 3,
    "definition": "Pleasing the senses or mind aesthetically",
    "phonetic": "ˈbjuːtɪfəl",
    "audioUrl": "https://api.dictionaryapi.dev/media/pronunciations/en/beautiful-uk.mp3",
    "imageUrl": "https://images.unsplash.com/photo-...",
    "exampleSentence": "What a beautiful sunset!",
    "meanings": [
      {
        "partOfSpeech": "adjective",
        "definitions": [
          {
            "definition": "Pleasing the senses or mind aesthetically"
          }
        ]
      }
    ],
    "synonyms": ["lovely", "pretty", "gorgeous"]
  }
}
```

### 3. Get Random Word from Existing Database

**Request**:

```bash
curl "http://localhost:8000/api/words/random?level=B1&session_id=test"
```

**Response**: Same format as above, but from cached database

## API Response Structure

The Free Dictionary API returns comprehensive data:

```json
[
  {
    "word": "example",
    "phonetic": "ɪɡˈzɑːmpl",
    "phonetics": [
      {
        "text": "ɪɡˈzɑːmpl",
        "audio": "https://api.dictionaryapi.dev/media/pronunciations/en/example-uk.mp3"
      }
    ],
    "meanings": [
      {
        "partOfSpeech": "noun",
        "definitions": [
          {
            "definition": "A thing characteristic of its kind or illustrating a general rule",
            "example": "it's a good example of how European action can produce results",
            "synonyms": ["specimen", "sample", "exemplar"]
          }
        ]
      }
    ]
  }
]
```

## Troubleshooting

### Issue: Word Not Found

**Symptom**: API returns 404 or no data
**Solution**:

- API may not have all words (especially uncommon ones)
- Seeder skips words that can't be fetched
- Check logs: `storage/logs/laravel.log`

### Issue: Rate Limit Exceeded

**Symptom**: "Rate limit exceeded" warning in logs
**Solution**:

- Increase rate limit constant
- Use Redis for distributed rate limiting
- Check if multiple users hitting API simultaneously

### Issue: Slow First Seeding

**Symptom**: `php artisan db:seed` takes several minutes
**Solution**:

- **Expected behavior** - first run fetches from API
- ~120 words × 0.2 seconds = ~24 seconds
- Subsequent runs are instant (7-day cache)
- Consider seeding during deployment, not on demand

### Issue: Missing Audio

**Symptom**: `audioUrl` is null
**Solution**:

- Not all words have audio in Free Dictionary API
- Frontend should handle gracefully
- Fallback: Use Web Speech API for text-to-speech

## Performance Optimization

### 1. Use Redis Cache (Production)

```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 2. Pre-warm Cache

```bash
# Seed database to populate cache
php artisan db:seed --class=WordSeeder

# Cache is now warm for 7 days
# All API calls return instantly
```

### 3. Queue Word Fetching (Optional)

For large-scale deployments:

```php
// Dispatch job to fetch word
dispatch(new FetchWordJob($word, $level));
```

## Advantages Over Commercial APIs

### Free Dictionary API ✅

- **Cost**: Free (no API key, unlimited requests)
- **Setup**: Zero configuration needed
- **Data Quality**: Community-driven, comprehensive
- **Audio**: Includes pronunciation files
- **Maintenance**: No billing or subscription management

### Merriam-Webster ❌

- **Cost**: Paid (1,000 requests/day free, then paid)
- **Setup**: Requires API key registration
- **Data Quality**: Professional, curated
- **Audio**: Professional recordings
- **Maintenance**: Manage API keys and quotas

## Best Practices

### 1. Always Check for Null

```php
$wordData = $dictionaryService->fetchWordData($word);

if (!$wordData) {
    // Handle missing data
    return response()->json(['error' => 'Word not found'], 404);
}
```

### 2. Monitor Cache Hit Rate

```php
// Add to monitoring
Cache::getStore()->getRedis()->info('stats');
```

### 3. Graceful Degradation

```php
// Fallback to existing words if API fails
if (!$newWord) {
    return Word::where('level', $level)->inRandomOrder()->first();
}
```

### 4. Log API Failures

```php
Log::error("Dictionary API error for '{$word}': " . $e->getMessage());
```

## Maintenance

### Clear Cache (if needed)

```bash
# Clear all cache
php artisan cache:clear

# Clear specific word
php artisan tinker
>>> Cache::forget('word_data_example');
```

### Update Cache TTL

```php
// In DictionaryApiService.php
private const CACHE_TTL = 1209600; // 14 days instead of 7
```

### Monitor API Health

```bash
# Test API directly
curl "https://api.dictionaryapi.dev/api/v2/entries/en/test"

# Should return word data, not error
```

## Testing

### Unit Tests

```bash
php artisan test --filter DictionaryApiServiceTest
```

### Manual Testing

```bash
# Test word fetching
php artisan tinker
>>> $service = app(\App\Services\DictionaryApiService::class);
>>> $data = $service->fetchWordData('example');
>>> dd($data);

# Test caching
>>> $data1 = $service->fetchWordData('test'); // API call
>>> $data2 = $service->fetchWordData('test'); // Cached (instant!)
```

## Migration from Mock Data

### Before (Mock Data)

```php
// QuickWordSeeder.php - Hardcoded definitions
'word' => 'hello',
'definition' => 'Used as a greeting',
'phonetic' => 'həˈloʊ',
```

### After (Real API Data)

```php
// WordSeeder.php - Fetches from API
$wordData = $dictionaryService->fetchWordData('hello');
// Returns real dictionary data with pronunciations, examples, synonyms
```

### Benefits

- ✅ Real, accurate definitions
- ✅ Professional phonetic transcriptions
- ✅ Native audio pronunciations
- ✅ Example sentences in context
- ✅ Synonyms and related words
- ✅ Multiple meanings and parts of speech

## Support & Resources

- **API Documentation**: https://dictionaryapi.dev/
- **GitHub**: https://github.com/meetDeveloper/freeDictionaryAPI
- **Status**: https://status.dictionaryapi.dev/
- **Report Issues**: https://github.com/meetDeveloper/freeDictionaryAPI/issues

## Summary

✅ **No API key required**  
✅ **Aggressive 7-day caching**  
✅ **Rate limiting protection**  
✅ **Automatic difficulty calculation**  
✅ **Hybrid seeding + on-demand fetching**  
✅ **Professional word data**  
✅ **Zero cost, unlimited usage**

---

**Status**: ✅ Fully Integrated  
**API Provider**: Free Dictionary API (dictionaryapi.dev)  
**Cache Strategy**: 7-day TTL with Redis/Memcached support  
**Rate Limiting**: 100 requests/minute per IP  
**Date**: October 12, 2025
