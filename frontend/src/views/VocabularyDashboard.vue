<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 px-4">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
          My Vocabulary
        </h1>
        <router-link
          to="/"
          class="px-4 py-2 text-primary-600 hover:text-primary-700 dark:text-primary-400 flex items-center gap-2"
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
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
          Back to Levels
        </router-link>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && totalSavedWords === 0" class="text-center py-16">
        <div class="text-gray-400 mb-4">
          <svg
            class="w-24 h-24 mx-auto"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
            />
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-600 dark:text-gray-400 mb-4">
          No words saved yet
        </h2>
        <p class="text-gray-500 dark:text-gray-500 mb-8">
          Start adding words from the training sessions to build your vocabulary
        </p>
        <router-link
          to="/"
          class="inline-block px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold"
        >
          Start Learning
        </router-link>
      </div>

      <!-- Dashboard Content -->
      <div v-else>
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                  Total Words
                </p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">
                  {{ totalSavedWords }}
                </p>
              </div>
              <div
                class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center"
              >
                <svg
                  class="w-6 h-6 text-blue-600 dark:text-blue-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                  />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                  Learning
                </p>
                <p
                  class="text-3xl font-bold text-yellow-600 dark:text-yellow-400"
                >
                  {{ learningWords.length }}
                </p>
              </div>
              <div
                class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center"
              >
                <svg
                  class="w-6 h-6 text-yellow-600 dark:text-yellow-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 10V3L4 14h7v7l9-11h-7z"
                  />
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                  Mastered
                </p>
                <p
                  class="text-3xl font-bold text-green-600 dark:text-green-400"
                >
                  {{ masteredWords.length }}
                </p>
              </div>
              <div
                class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center"
              >
                <svg
                  class="w-6 h-6 text-green-600 dark:text-green-400"
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
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">New</p>
                <p
                  class="text-3xl font-bold text-purple-600 dark:text-purple-400"
                >
                  {{ newWords.length }}
                </p>
              </div>
              <div
                class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center"
              >
                <svg
                  class="w-6 h-6 text-purple-600 dark:text-purple-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"
                  />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Learning Modes -->
        <div class="mb-8">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Practice Modes
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <router-link
              to="/vocabulary/quiz"
              class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:scale-105"
            >
              <div
                class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"
              >
                <svg
                  class="w-8 h-8 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <h3
                class="text-xl font-semibold text-gray-900 dark:text-white mb-2"
              >
                Quiz
              </h3>
              <p class="text-gray-600 dark:text-gray-400 text-sm">
                Multiple choice questions to test your knowledge
              </p>
            </router-link>

            <router-link
              to="/vocabulary/images"
              class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:scale-105"
            >
              <div
                class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"
              >
                <svg
                  class="w-8 h-8 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
              </div>
              <h3
                class="text-xl font-semibold text-gray-900 dark:text-white mb-2"
              >
                Images
              </h3>
              <p class="text-gray-600 dark:text-gray-400 text-sm">
                Match words with corresponding images
              </p>
            </router-link>

            <router-link
              to="/vocabulary/listening"
              class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:scale-105"
            >
              <div
                class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"
              >
                <svg
                  class="w-8 h-8 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"
                  />
                </svg>
              </div>
              <h3
                class="text-xl font-semibold text-gray-900 dark:text-white mb-2"
              >
                Listening
              </h3>
              <p class="text-gray-600 dark:text-gray-400 text-sm">
                Type what you hear to improve pronunciation
              </p>
            </router-link>

            <router-link
              to="/vocabulary/writing"
              class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:scale-105"
            >
              <div
                class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"
              >
                <svg
                  class="w-8 h-8 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                  />
                </svg>
              </div>
              <h3
                class="text-xl font-semibold text-gray-900 dark:text-white mb-2"
              >
                Writing
              </h3>
              <p class="text-gray-600 dark:text-gray-400 text-sm">
                Practice spelling from definitions and images
              </p>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useUserWordsStore } from "../stores/userWords";

const userWordsStore = useUserWordsStore();

const loading = computed(() => userWordsStore.loading);
const totalSavedWords = computed(() => userWordsStore.totalSavedWords);
const newWords = computed(() => userWordsStore.newWords);
const learningWords = computed(() => userWordsStore.learningWords);
const masteredWords = computed(() => userWordsStore.masteredWords);

onMounted(async () => {
  await userWordsStore.fetchUserWords();
});
</script>
