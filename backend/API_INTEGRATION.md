# API Integration Guide

## Dictionary API

We use the **Free Dictionary API** for word definitions, pronunciations, and examples.

- **URL:** `https://api.dictionaryapi.dev/api/v2/entries/en/{word}`
- **Cost:** Free, no API key required
- **Rate Limit:** Reasonable (no strict limits)
- **Features:**
  - Word definitions
  - Phonetic transcriptions
  - Audio pronunciations
  - Example sentences
  - Parts of speech
  - Synonyms and antonyms

### Alternative: Merriam-Webster API

If you want higher quality dictionary data, you can use [Merriam-Webster's Collegiate Dictionary API](https://dictionaryapi.com/products/api-collegiate-dictionary):

1. **Register:** Get a free API key at https://dictionaryapi.com/
2. **Set API key:** Add to `.env`: `MERRIAM_WEBSTER_API_KEY=your_key_here`
3. **Update Service:** Modify `DictionaryApiService.php` to use Merriam-Webster

## Image Sources

We use multiple image sources with fallback mechanism:

### 1. Unsplash (Primary) ✅ Recommended

- **URL:** `https://api.unsplash.com/search/photos`
- **Cost:** Free tier: 50 requests/hour
- **Quality:** High-quality, professional photos
- **Setup:**
  1. Register at https://unsplash.com/developers
  2. Create an app
  3. Add to `.env`: `UNSPLASH_ACCESS_KEY=your_key_here`

**Pros:**

- Beautiful, high-quality images
- Free tier is generous
- No watermarks
- Commercial use allowed

**Cons:**

- Rate limits on free tier
- Requires API key

### 2. Pexels (Secondary)

- **URL:** `https://api.pexels.com/v1/search`
- **Cost:** Free, unlimited requests
- **Quality:** Good quality, diverse collection
- **Setup:**
  1. Register at https://www.pexels.com/api/
  2. Get API key
  3. Add to `.env`: `PEXELS_API_KEY=your_key_here`

**Pros:**

- Unlimited requests
- Free forever
- Good quality images
- Easy attribution

**Cons:**

- Smaller library than Unsplash
- Images require attribution

### 3. Placeholder (Fallback)

- **URL:** `https://via.placeholder.com/400x300/{color}/{text}`
- **Cost:** Free, no limits
- **Quality:** Simple colored placeholders with text

Used when both Unsplash and Pexels fail or no API keys are set.

### 4. Unsplash Source (No API Key Required) ⚡ Fastest Setup

If you don't want to register for API keys, use Unsplash Source:

```php
// In ImageApiService.php
public function fetchImage(string $query): string
{
    return "https://source.unsplash.com/400x300/?" . urlencode($query);
}
```

**Pros:**

- No API key required
- Fast and simple
- Good quality
- No rate limits (for small projects)

**Cons:**

- Random images (cannot control which image is returned)
- No API to get specific image details
- May return same image multiple times

### 5. Pixabay (Alternative)

- **URL:** `https://pixabay.com/api/`
- **Cost:** Free, 5000 requests/hour
- **Setup:**
  1. Register at https://pixabay.com/api/docs/
  2. Get API key
  3. Add to `.env`: `PIXABAY_API_KEY=your_key_here`

**Pros:**

- High rate limits
- Large library
- Free for commercial use
- No attribution required

## Current Implementation

Our `ImageApiService` uses this fallback hierarchy:

1. **Unsplash** (if API key is set)
2. **Pexels** (if API key is set)
3. **Placeholder** (always works)

All images are cached for 7 days to reduce API calls.

## Recommended Setup

### For Development:

Use **Unsplash Source** (no API key):

```env
# No keys needed!
```

Update `QuickWordSeeder.php` to use:

```php
'image_url' => "https://source.unsplash.com/400x300/?" . urlencode($wordData['image_query'])
```

### For Production:

Use **Unsplash API** with caching:

```env
UNSPLASH_ACCESS_KEY=your_unsplash_key_here
```

Or use **Pexels** for unlimited requests:

```env
PEXELS_API_KEY=your_pexels_key_here
```

## Testing Image Sources

Test Unsplash Source (no API key):

```bash
curl "https://source.unsplash.com/400x300/?cat"
```

Test Unsplash API (requires key):

```bash
curl -H "Authorization: Client-ID YOUR_KEY" \
  "https://api.unsplash.com/search/photos?query=cat&per_page=1"
```

Test Pexels API (requires key):

```bash
curl -H "Authorization: YOUR_KEY" \
  "https://api.pexels.com/v1/search?query=cat&per_page=1"
```

## Image Query Optimization

For better image results, use descriptive queries in the seeder:

- ❌ Bad: `cat`
- ✅ Good: `cute cat sitting`

- ❌ Bad: `happy`
- ✅ Good: `happy person smiling`

- ❌ Bad: `technology`
- ✅ Good: `modern technology computers`

## Rate Limiting Best Practices

1. **Cache everything:** Our service caches images for 7 days
2. **Seed during off-hours:** Run seeder when you're not actively developing
3. **Use rate limiting:** Add delays in seeder (`usleep(500000)` = 0.5 seconds)
4. **Monitor usage:** Check your API dashboard regularly

## Cost Comparison

| Service         | Free Tier | Paid Plans         | Best For                    |
| --------------- | --------- | ------------------ | --------------------------- |
| Unsplash Source | Unlimited | N/A                | Development, small projects |
| Unsplash API    | 50/hour   | $49/mo (5000/hour) | Production apps             |
| Pexels          | Unlimited | N/A                | Free unlimited usage        |
| Pixabay         | 5000/hour | N/A                | High-traffic apps           |
| Placeholder     | Unlimited | N/A                | Fallback only               |

## Troubleshooting

### No images showing:

1. Check `.env` has correct API keys
2. Check internet connection
3. Clear Laravel cache: `php artisan cache:clear`
4. Check API key permissions on provider website
5. Check rate limits haven't been exceeded

### Slow image loading:

1. Enable caching (already implemented)
2. Use CDN for served images
3. Optimize image sizes
4. Use lazy loading in frontend

### Wrong images for words:

1. Improve search queries in seeder
2. Use more specific image descriptions
3. Consider manual curation for important words

## Future Improvements

1. **Image CDN:** Upload fetched images to your own CDN
2. **Manual curation:** Allow admin to select/upload specific images
3. **AI image generation:** Use DALL-E or Stable Diffusion for unique images
4. **Multiple images:** Store 3-5 images per word, rotate randomly
5. **Image quality ratings:** Let users vote on best images
