# updateWord → updateReview Method Name Fix ✅

## Problem

All quiz-related components were calling `userWordsStore.updateWord()`, which doesn't exist:

```
Uncaught (in promise) TypeError: userWordsStore.updateWord is not a function
```

## Root Cause

**Mismatch between component calls and store method name:**

**Components were calling**:

```typescript
await userWordsStore.updateWord(id, correct, timeSpent, attempts);
```

**Store actually has**:

```typescript
// In src/stores/userWords.ts
async updateReview(
  userWordId: number,
  correct: boolean,
  timeSpent = 0,
  attempts = 1
): Promise<UserWord>
```

## Solution

**Changed method name in all quiz components from `updateWord` → `updateReview`**

### Files Fixed

#### 1. `Quiz.vue` ✅

```typescript
// Before
await userWordsStore.updateWord(
  currentQuestion.value.id,
  isCorrect.value,
  timeSpent,
  1
);

// After
await userWordsStore.updateReview(
  currentQuestion.value.id,
  isCorrect.value,
  timeSpent,
  1
);
```

#### 2. `Listening.vue` ✅

```typescript
// Same change as above
await userWordsStore.updateReview(...)
```

#### 3. `Writing.vue` ✅

```typescript
// Same change as above
await userWordsStore.updateReview(...)
```

#### 4. `ImageMatching.vue` ✅

```typescript
// Same change as above
await userWordsStore.updateReview(...)
```

## What This Method Does

The `updateReview()` method:

1. **Updates word review status** based on user performance
2. **Calculates next review date** using spaced repetition algorithm
3. **Tracks statistics**:
   - Review count
   - Correct count
   - Incorrect count
   - Easiness factor
   - Interval days
4. **Updates word status**: `new` → `learning` → `mastered`

## Method Signature

```typescript
async updateReview(
  userWordId: number,      // ID of the user_word record
  correct: boolean,         // Was the answer correct?
  timeSpent = 0,           // Time in seconds
  attempts = 1             // Number of attempts
): Promise<UserWord>
```

## Affected Functionality

All quiz modes now properly track learning progress:

- ✅ **Quiz Mode**: Multiple choice questions
- ✅ **Listening Mode**: Audio recognition
- ✅ **Writing Mode**: Spelling practice
- ✅ **Image Matching Mode**: Visual association

## Spaced Repetition Integration

When `updateReview()` is called:

1. **Correct answer** (quality ≥ 3):

   - Increases interval (1 day → 6 days → 15 days → ...)
   - Updates easiness factor
   - Increments correct count
   - May change status to `mastered`

2. **Incorrect answer** (quality < 3):
   - Resets interval to 1 day
   - Decreases easiness factor
   - Increments incorrect count
   - Changes status back to `learning`

## Testing

### Test Quiz Progress Tracking

1. Add 3-5 words to learn
2. Go to any quiz page
3. Answer questions (mix correct and incorrect)
4. Check `/vocabulary` dashboard
5. **Expected**:
   - Word statuses updated
   - Next review dates set
   - Progress statistics reflect your performance

### Verify All Quiz Modes

```
✓ http://localhost:5173/vocabulary/quiz
✓ http://localhost:5173/vocabulary/listening
✓ http://localhost:5173/vocabulary/writing
✓ http://localhost:5173/vocabulary/images
```

**Expected**: All work without console errors

## Backend API Call

When `updateReview()` is called, it makes this API request:

```typescript
PUT /api/user-words/{id}
{
  "correct": true,
  "time_spent": 15,
  "attempts": 1
}
```

**Response**:

```json
{
  "data": {
    "id": 1,
    "status": "learning",
    "nextReviewDate": "2025-10-18T00:00:00+00:00",
    "reviewCount": 3,
    "easeFactor": 2.6,
    "interval": 6,
    "correctCount": 2,
    "incorrectCount": 1
  }
}
```

## Related Systems

This fix ensures proper integration with:

- ✅ Spaced Repetition Algorithm (SM-2)
- ✅ Progress Tracking
- ✅ User Statistics
- ✅ Learning Analytics
- ✅ Review Scheduling

## Files Modified

- ✅ `frontend/src/views/Quiz.vue`
- ✅ `frontend/src/views/Listening.vue`
- ✅ `frontend/src/views/Writing.vue`
- ✅ `frontend/src/views/ImageMatching.vue`

## No Changes Needed

- ✅ `frontend/src/stores/userWords.ts` - Already has correct method name
- ✅ Backend API - Already working correctly

---

**Status**: ✅ Fixed  
**Impact**: Quiz progress tracking now functional  
**Action**: Refresh browser and test quiz modes  
**Date**: October 12, 2025
