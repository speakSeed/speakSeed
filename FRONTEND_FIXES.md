# Frontend Error Fixes

## Issues Resolved

### 1. ✅ TypeError: Cannot read properties of undefined (reading 'length')

**Location:** `VocabularyDashboard.vue:166`

**Error:** `newWords.length` was throwing an error because the `newWords` getter didn't exist in the store.

**Root Cause:**

- The `userWords` store was missing the `newWords` getter
- The store was also missing the `totalSavedWords` getter (alias for `totalWords`)
- Component was trying to access getters that didn't exist

### 2. ✅ TypeError: Cannot read properties of null (reading 'component')

**Location:** Route navigation errors

**Root Cause:**

- Stores were not properly handling Laravel API Resource responses
- API responses are wrapped in `{ data: [...] }` or `{ data: {...} }` format
- Stores were expecting raw arrays/objects directly
- This caused null/undefined values when navigating between routes

### 3. ✅ snake_case vs camelCase Issues

**Root Cause:**

- Backend now returns camelCase (`wordId`, `userId`, etc.)
- Frontend stores were still using snake_case (`word_id`, `user_id`)
- This caused data mismatches and undefined property access

---

## Fixes Applied

### A. `userWords.js` Store Updates

#### 1. Added Missing Getters

```javascript
// Added these getters:
totalSavedWords: (state) => state.userWords.length,  // Alias for compatibility
newWords: (state) => state.userWords.filter((uw) => uw.status === "new"),
```

#### 2. Fixed camelCase Support

```javascript
// Updated to support both formats during transition:
wordIds: (state) => state.userWords.map((uw) => uw.wordId || uw.word_id),

isWordSaved: (state) => (wordId) => {
  return state.userWords.some((uw) => (uw.wordId || uw.word_id) === wordId);
},

getUserWordByWordId(wordId) {
  return this.userWords.find((uw) => (uw.wordId || uw.word_id) === wordId);
},
```

#### 3. Fixed API Response Handling

```javascript
// All methods now handle Laravel Resource format { data: [...] }

async fetchUserWords() {
  const response = await userWordApi.getAll();
  // Handle Laravel Resource format { data: [...] }
  const words = response.data || response;
  this.userWords = Array.isArray(words) ? words : [];
}

async addWord(wordId) {
  const response = await userWordApi.addWord(wordId);
  // Handle Laravel Resource format { data: {...} }
  const userWord = response.data || response;
  this.userWords.push(userWord);
}
```

#### 4. Updated updateReview Method Signature

```javascript
// Old: updateReview(userWordId, quality)
// New: updateReview(userWordId, correct, timeSpent = 0, attempts = 1)

async updateReview(userWordId, correct, timeSpent = 0, attempts = 1) {
  const response = await userWordApi.updateReview(userWordId, correct, timeSpent, attempts);
  const updatedWord = response.data || response;
  // ... rest of logic
}
```

#### 5. Improved Error Handling

```javascript
// Don't throw errors in fetchUserWords - let component handle empty state
async fetchUserWords() {
  try {
    // ...
  } catch (error) {
    this.error = error.message;
    console.error("Error fetching user words:", error);
    this.userWords = [];
    // Don't throw - let the component handle empty state gracefully
  }
}
```

### B. `words.js` Store Updates

#### 1. Fixed API Response Handling

```javascript
// All fetch methods now handle Laravel Resource format

async fetchRandomWord(level) {
  const response = await wordApi.getRandom(targetLevel);
  // Handle Laravel Resource format { data: {...} }
  const word = response.data || response;
  this.currentWord = word;
}

async fetchWord(id) {
  const response = await wordApi.getWord(id);
  const word = response.data || response;
  this.currentWord = word;
}

async fetchAndStoreWord(wordText, level) {
  const response = await wordApi.fetchAndStore(wordText, level);
  const word = response.data || response;
  this.currentWord = word;
  this.words.push(word);
}
```

---

## API Response Format

### Laravel Resource Collections

```json
{
  "data": [
    { "id": 1, "word": "hello", "wordId": 5, ... },
    { "id": 2, "word": "world", "wordId": 6, ... }
  ]
}
```

### Laravel Resource Single Item

```json
{
  "data": {
    "id": 1,
    "word": "hello",
    "wordId": 5,
    "audioUrl": "https://...",
    "imageUrl": "https://...",
    ...
  }
}
```

### Handling Both Formats

```javascript
// This pattern handles both wrapped and unwrapped responses:
const data = response.data || response;

// For collections:
const items = Array.isArray(data) ? data : [];

// For single items:
const item = data;
```

---

## Testing Checklist

✅ Navigate to home page → Select level → No errors  
✅ Navigate to Vocabulary Dashboard → Stats display correctly  
✅ Add words to vocabulary → Words save properly  
✅ Navigate between routes → No null/undefined errors  
✅ Empty state handling → Graceful display when no words saved  
✅ camelCase data → All API responses use camelCase  
✅ snake_case compatibility → Old references still work during transition

---

## Prevention Tips

### 1. Always Handle API Responses

```javascript
// ❌ Bad - assumes direct data
const words = await api.getWords();

// ✅ Good - handles wrapper
const response = await api.getWords();
const words = response.data || response;
```

### 2. Always Provide Fallbacks

```javascript
// ❌ Bad - can cause "cannot read property" errors
const count = words.length;

// ✅ Good - safe with fallback
const count = (words || []).length;
const count = words?.length || 0;
```

### 3. Support Both Naming Conventions During Transition

```javascript
// ✅ Good - works with both formats
const id = item.wordId || item.word_id;
```

### 4. Don't Throw Errors for Expected Empty States

```javascript
// ❌ Bad - breaks UI on empty state
if (!words.length) throw new Error("No words found");

// ✅ Good - handle gracefully
this.words = words || [];
// Let component display empty state UI
```

---

## Files Modified

1. ✅ `frontend/src/stores/userWords.js` - Complete rewrite of API handling
2. ✅ `frontend/src/stores/words.js` - Fixed API response handling
3. ✅ `frontend/src/views/VocabularyDashboard.vue` - No changes needed (fixed by store updates)

---

## Next Steps

### If More Errors Occur:

1. **Check Browser Console** for exact error location
2. **Verify API Response** in Network tab (should be camelCase)
3. **Check Store Methods** are using `response.data || response` pattern
4. **Add Null Safety** with optional chaining (`?.`) and fallbacks (`|| []`)

### For New Features:

1. **Always wrap API calls** with `response.data || response`
2. **Use optional chaining** for nested properties: `user?.profile?.name`
3. **Provide fallbacks** for arrays and objects: `items || []`, `data || {}`
4. **Test navigation** between all routes
5. **Test empty states** (no data scenarios)

---

## Summary

All frontend errors have been resolved by:

1. ✅ Adding missing store getters (`newWords`, `totalSavedWords`)
2. ✅ Fixing API response handling to work with Laravel Resources
3. ✅ Supporting both camelCase and snake_case during transition
4. ✅ Improving error handling and null safety
5. ✅ Updating method signatures to match backend changes

The application should now work smoothly without any `TypeError` errors when navigating between pages or loading data! 🎉
