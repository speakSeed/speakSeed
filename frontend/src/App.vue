<template>
  <div
    id="app"
    class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900 transition-all duration-500"
  >
    <!-- Navigation Bar -->
    <nav
      class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg shadow-xl sticky top-0 z-50 border-b border-purple-100 dark:border-purple-900"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <!-- Logo/Brand -->
          <router-link to="/" class="flex items-center space-x-3 group">
            <div
              class="w-12 h-12 bg-gradient-to-br from-purple-500 via-pink-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 relative overflow-hidden"
            >
              <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
              <svg
                class="w-7 h-7 text-white relative z-10"
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
            <div>
              <span
                class="text-2xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent"
                >SpeakSeed</span
              >
              <p class="text-xs text-gray-500 dark:text-gray-400 -mt-1">
                Master English Words
              </p>
            </div>
          </router-link>

          <!-- Search Bar -->
          <div class="hidden lg:flex flex-1 max-w-md mx-8">
            <div class="relative w-full group">
              <input
                v-model="searchQuery"
                @keyup.enter="searchWord"
                @focus="showSearchSuggestions = true"
                @blur="hideSearchSuggestions"
                type="text"
                placeholder="Search or add a word..."
                class="w-full px-6 py-3 pl-12 pr-24 rounded-2xl bg-gray-50 dark:bg-gray-700/50 border-2 border-gray-200 dark:border-gray-600 focus:border-purple-500 dark:focus:border-purple-400 focus:ring-4 focus:ring-purple-500/20 outline-none transition-all duration-300 text-gray-900 dark:text-white placeholder-gray-400 group-hover:shadow-lg"
              />
              <svg
                class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
              <button
                @click="searchWord"
                :disabled="!searchQuery.trim()"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-1.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 disabled:hover:scale-100"
              >
                Add
              </button>

              <!-- Search Loading/Results -->
              <div
                v-if="searchLoading"
                class="absolute top-full mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-4 border border-purple-100 dark:border-purple-900"
              >
                <div class="flex items-center justify-center space-x-2">
                  <div
                    class="w-2 h-2 bg-purple-600 rounded-full animate-bounce"
                    style="animation-delay: 0ms"
                  ></div>
                  <div
                    class="w-2 h-2 bg-pink-600 rounded-full animate-bounce"
                    style="animation-delay: 150ms"
                  ></div>
                  <div
                    class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce"
                    style="animation-delay: 300ms"
                  ></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Navigation Links -->
          <div class="hidden md:flex items-center space-x-2">
            <router-link
              to="/"
              class="nav-link relative px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300 overflow-hidden group"
            >
              <span
                class="relative z-10 text-gray-700 dark:text-gray-200 group-hover:text-white transition-colors"
                >Levels</span
              >
              <div
                class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"
              ></div>
            </router-link>
            <router-link
              to="/vocabulary"
              class="nav-link relative px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300 overflow-hidden group"
            >
              <span
                class="relative z-10 text-gray-700 dark:text-gray-200 group-hover:text-white transition-colors"
                >My Dictionary</span
              >
              <div
                class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"
              ></div>
            </router-link>
            <router-link
              to="/progress"
              class="nav-link relative px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300 overflow-hidden group"
            >
              <span
                class="relative z-10 text-gray-700 dark:text-gray-200 group-hover:text-white transition-colors"
                >Progress</span
              >
              <div
                class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"
              ></div>
            </router-link>
          </div>

          <!-- Theme Toggle -->
          <button
            @click="toggleTheme"
            class="p-3 rounded-xl bg-gradient-to-br from-purple-100 to-pink-100 dark:from-gray-700 dark:to-gray-600 hover:shadow-lg hover:scale-110 transition-all duration-300"
            aria-label="Toggle theme"
          >
            <svg
              v-if="isDark"
              class="w-5 h-5 text-yellow-500"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
              />
            </svg>
            <svg
              v-else
              class="w-5 h-5 text-purple-600"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
              />
            </svg>
          </button>
        </div>
      </div>
    </nav>

    <!-- Success Toast -->
    <transition name="slide-down">
      <div
        v-if="showSuccessToast"
        class="fixed top-24 left-1/2 transform -translate-x-1/2 z-[60] animate-bounce-in"
      >
        <div
          class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center space-x-3"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 13l4 4L19 7"
            />
          </svg>
          <span class="font-semibold">Word added to your dictionary!</span>
        </div>
      </div>
    </transition>

    <!-- Error Toast -->
    <transition name="slide-down">
      <div
        v-if="showErrorToast"
        class="fixed top-24 left-1/2 transform -translate-x-1/2 z-[60]"
      >
        <div
          class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center space-x-3"
        >
          <svg
            class="w-6 h-6"
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
          <span class="font-semibold">{{ errorMessage }}</span>
        </div>
      </div>
    </transition>

    <!-- Main Content -->
    <main class="relative">
      <router-view v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Footer -->
    <footer
      class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-gray-800 dark:to-gray-900 border-t border-purple-100 dark:border-purple-900 mt-20"
    >
      <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <p
            class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 font-semibold mb-2"
          >
            SpeakSeed
          </p>
          <p class="text-gray-500 dark:text-gray-400 text-sm">
            Master English vocabulary with AI-powered learning • Built with Vue
            3 & Laravel
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { storage } from "./utils/localStorage";
import { wordApi } from "./services/api";
import { useUserWordsStore } from "./stores/userWords";

const router = useRouter();
const userWordsStore = useUserWordsStore();

const isDark = ref(storage.getTheme() === "dark");
const searchQuery = ref("");
const searchLoading = ref(false);
const showSearchSuggestions = ref(false);
const showSuccessToast = ref(false);
const showErrorToast = ref(false);
const errorMessage = ref("");

const toggleTheme = () => {
  isDark.value = !isDark.value;
  storage.setTheme(isDark.value ? "dark" : "light");
};

const hideSearchSuggestions = () => {
  setTimeout(() => {
    showSearchSuggestions.value = false;
  }, 200);
};

const searchWord = async () => {
  const word = searchQuery.value.trim().toLowerCase();

  if (!word) return;

  searchLoading.value = true;

  try {
    // First, try to fetch the word from the API
    const response = await wordApi.fetchAndStore(word, "B1"); // Default level

    if (response.data) {
      // Add the word to user's vocabulary
      await userWordsStore.addWord(response.data.id);

      // Show success message
      showSuccessToast.value = true;
      setTimeout(() => {
        showSuccessToast.value = false;
      }, 3000);

      // Clear search
      searchQuery.value = "";

      // Optionally navigate to vocabulary page
      setTimeout(() => {
        router.push("/vocabulary");
      }, 1500);
    }
  } catch (error: any) {
    console.error("Error adding word:", error);

    // Show error message
    errorMessage.value =
      error.response?.data?.error || "Word not found. Please try another word.";
    showErrorToast.value = true;
    setTimeout(() => {
      showErrorToast.value = false;
    }, 4000);
  } finally {
    searchLoading.value = false;
  }
};

onMounted(() => {
  // Initialize theme
  storage.setTheme(storage.getTheme());
});
</script>

<style>
.nav-link.router-link-active span {
  @apply text-white;
}

.nav-link.router-link-active > div {
  @apply scale-x-100;
}

/* Page transitions */
.page-enter-active {
  animation: slideInUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.page-leave-active {
  animation: fadeOut 0.2s ease-out;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeOut {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
  }
}

/* Toast animations */
.slide-down-enter-active {
  animation: slideDown 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.slide-down-leave-active {
  animation: slideUp 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translate(-50%, -100px);
  }
  to {
    opacity: 1;
    transform: translate(-50%, 0);
  }
}

@keyframes slideUp {
  from {
    opacity: 1;
    transform: translate(-50%, 0);
  }
  to {
    opacity: 0;
    transform: translate(-50%, -50px);
  }
}

/* Bounce in animation */
@keyframes bounceIn {
  0% {
    opacity: 0;
    transform: translate(-50%, -100px) scale(0.3);
  }
  50% {
    opacity: 1;
    transform: translate(-50%, 10px) scale(1.05);
  }
  70% {
    transform: translate(-50%, -5px) scale(0.95);
  }
  100% {
    transform: translate(-50%, 0) scale(1);
  }
}

.animate-bounce-in {
  animation: bounceIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Smooth scroll */
html {
  scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

::-webkit-scrollbar-track {
  background: rgba(156, 163, 175, 0.1);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #9333ea, #ec4899);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #7e22ce, #db2777);
}

/* Dark mode scrollbar */
.dark ::-webkit-scrollbar-track {
  background: rgba(75, 85, 99, 0.3);
}
</style>
