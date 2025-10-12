<template>
  <div
    class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-100 dark:from-gray-900 dark:to-gray-800 py-8 px-4"
  >
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <button
          @click="goBack"
          class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
        >
          <svg
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"
            />
          </svg>
          Back
        </button>

        <div class="flex items-center gap-4">
          <LevelBadge :level="currentLevel" />
          <span class="text-sm text-gray-600 dark:text-gray-400">
            <span class="font-semibold text-purple-600 dark:text-purple-400">{{
              availableWords.length
            }}</span>
            new words available
          </span>
        </div>
      </div>

      <!-- Loading State -->
      <LoadingSpinner v-if="loading" message="Loading word..." />

      <!-- Error State / All Words Saved State -->
      <div
        v-else-if="error"
        class="text-center py-12 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8"
      >
        <!-- Success state when all words are saved -->
        <div
          v-if="error.includes('added to your dictionary')"
          class="text-green-600 dark:text-green-400 mb-4"
        >
          <svg
            class="w-20 h-20 mx-auto mb-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
            🎉 Congratulations!
          </h3>
          <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
            {{ error }}
          </p>
          <div class="flex gap-4 justify-center">
            <router-link
              to="/"
              class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg font-semibold transition-all"
            >
              Choose Another Level
            </router-link>
            <router-link
              to="/vocabulary"
              class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-semibold transition-colors"
            >
              My Dictionary
            </router-link>
          </div>
        </div>

        <!-- Error state for other errors -->
        <div v-else>
          <div class="text-red-600 dark:text-red-400 mb-4">
            <svg
              class="w-16 h-16 mx-auto"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <p class="text-xl text-gray-600 dark:text-gray-400 mb-4">
            {{ error }}
          </p>
          <button
            @click="loadAvailableWords"
            class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors"
          >
            Try Again
          </button>
        </div>
      </div>

      <!-- Word Card -->
      <WordCard
        v-else-if="currentWord"
        :word="currentWord"
        :is-saved="isCurrentWordSaved"
        @next="nextWord"
        @add="addWord"
      />

      <!-- No words message -->
      <div
        v-else
        class="text-center py-12 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8"
      >
        <p class="text-xl text-gray-600 dark:text-gray-400">
          No words available for this level.
        </p>
      </div>

      <!-- Navigation Hint -->
      <div
        v-if="currentWord && !loading"
        class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400"
      >
        <p>
          Press
          <kbd
            class="px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded shadow-sm"
            >→</kbd
          >
          for next word or
          <kbd
            class="px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded shadow-sm"
            >Space</kbd
          >
          to add
        </p>
      </div>

      <!-- Success notification -->
      <div
        v-if="showSaveNotification"
        class="fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg animate-fade-in"
      >
        ✓ Word saved successfully!
      </div>

      <!-- Error notification -->
      <div
        v-if="saveError"
        class="fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg animate-fade-in max-w-md"
      >
        <div class="flex items-start gap-3">
          <svg
            class="w-6 h-6 flex-shrink-0"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <div>
            <p class="font-semibold">Error saving word</p>
            <p class="text-sm mt-1">{{ saveError }}</p>
          </div>
          <button @click="saveError = null" class="ml-auto">
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useWordsStore } from "../stores/words";
import { useUserWordsStore } from "../stores/userWords";
import { wordApi } from "../services/api";
import WordCard from "../components/WordCard.vue";
import LevelBadge from "../components/LevelBadge.vue";
import LoadingSpinner from "../components/LoadingSpinner.vue";
import type { EnglishLevel, Word } from "../types";

const route = useRoute();
const router = useRouter();
const wordsStore = useWordsStore();
const userWordsStore = useUserWordsStore();

const currentLevel = ref<EnglishLevel>(route.params.level as EnglishLevel);
const wordsSeenCount = ref(0);
const showSaveNotification = ref(false);
const saveError = ref<string | null>(null);
const availableWords = ref<Word[]>([]);
const currentWordIndex = ref(0);

const loading = computed(() => wordsStore.loading);
const error = computed(() => wordsStore.error);
const currentWord = computed(() => wordsStore.currentWord);

const isCurrentWordSaved = computed(() => {
  if (!currentWord.value) return false;
  return userWordsStore.isWordSaved(currentWord.value.id);
});

// Get list of saved word IDs for filtering
const savedWordIds = computed(() => {
  // Safety check: ensure userWords exists and is an array
  if (!userWordsStore.userWords || !Array.isArray(userWordsStore.userWords)) {
    return new Set<number>();
  }
  return new Set(userWordsStore.userWords.map((w: any) => w.wordId));
});

// Filter out words that are already saved
const getUnsavedWords = (words: Word[]): Word[] => {
  return words.filter((word) => !savedWordIds.value.has(word.id));
};

// Load a batch of words and filter out saved ones (UNLIMITED WORDS - fetches up to 100 per level)
const loadAvailableWords = async () => {
  try {
    wordsStore.loading = true;
    wordsStore.error = null;

    const response = await wordApi.getByLevel(currentLevel.value, 100); // Fetch up to 100 words!
    const allWords = response.data || [];

    // Safety check: ensure allWords is an array
    if (!Array.isArray(allWords)) {
      throw new Error("Invalid response format from API");
    }

    // Filter out already saved words
    availableWords.value = getUnsavedWords(allWords);
    currentWordIndex.value = 0;

    if (availableWords.value.length === 0) {
      wordsStore.error =
        "🎉 Amazing! You've added all 100 words in this level to your dictionary! Choose another level or review your words.";
      wordsStore.currentWord = null;
    } else {
      // Shuffle the available words
      availableWords.value = availableWords.value.sort(
        () => Math.random() - 0.5
      );
      wordsStore.currentWord = availableWords.value[0];
    }
  } catch (err: any) {
    console.error("Failed to load words:", err);
    wordsStore.error = err.message || "Failed to load words. Please try again.";
    availableWords.value = [];
    wordsStore.currentWord = null;
  } finally {
    wordsStore.loading = false;
  }
};

const loadRandomWord = async () => {
  try {
    // If we have available words, use them
    if (availableWords.value.length > 0) {
      currentWordIndex.value =
        (currentWordIndex.value + 1) % availableWords.value.length;
      wordsStore.currentWord = availableWords.value[currentWordIndex.value];
      wordsSeenCount.value++;
    } else {
      // Reload available words if we've run out
      await loadAvailableWords();
      if (availableWords.value.length > 0) {
        wordsSeenCount.value++;
      }
    }
  } catch (err: any) {
    console.error("Failed to load word:", err);
  }
};

const nextWord = async () => {
  await loadRandomWord();
};

const addWord = async () => {
  if (!currentWord.value || isCurrentWordSaved.value) return;

  try {
    await userWordsStore.addWord(currentWord.value.id);

    // Remove the word from available words since it's now saved
    const currentWordId = currentWord.value.id;
    availableWords.value = availableWords.value.filter(
      (w) => w.id !== currentWordId
    );

    // Show success notification
    showSaveNotification.value = true;
    saveError.value = null;
    setTimeout(() => {
      showSaveNotification.value = false;
    }, 2000);

    // Auto-advance to next word after short delay
    setTimeout(async () => {
      await nextWord();
    }, 800);
  } catch (err: any) {
    console.error("Failed to save word:", err);

    // Show error notification
    saveError.value =
      userWordsStore.error || "Failed to save word. Please try again.";
    setTimeout(() => {
      saveError.value = null;
    }, 5000);
  }
};

const goBack = () => {
  router.push("/");
};

// Keyboard shortcuts
const handleKeyPress = (event: KeyboardEvent) => {
  if (loading.value) return;

  if (event.key === "ArrowRight") {
    nextWord();
  } else if (event.key === " " && !isCurrentWordSaved.value) {
    event.preventDefault();
    addWord();
  }
};

onMounted(async () => {
  try {
    // Set the level in the store
    wordsStore.setLevel(currentLevel.value);

    // Fetch user's saved words first (with error handling)
    try {
      await userWordsStore.fetchUserWords();
    } catch (err) {
      console.warn("Could not fetch user words, continuing anyway:", err);
      // Continue even if user words fail to load
    }

    // Load available words (filtered to exclude saved ones)
    await loadAvailableWords();

    // Add keyboard listener
    window.addEventListener("keydown", handleKeyPress);
  } catch (err: any) {
    console.error("Error during initialization:", err);
    wordsStore.error = "Failed to initialize. Please refresh the page.";
  }
});

onUnmounted(() => {
  window.removeEventListener("keydown", handleKeyPress);
  wordsStore.clearCurrentWord();
});
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateX(100px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

kbd {
  font-family: ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas,
    "Liberation Mono", monospace;
  font-size: 0.875rem;
}
</style>
