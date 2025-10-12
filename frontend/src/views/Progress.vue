<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 px-4">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
          Your Progress
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

      <!-- Loading -->
      <LoadingSpinner v-if="loading" message="Loading progress..." />

      <div v-else>
        <!-- Main Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div
            @click="showAllWordsModal"
            class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white cursor-pointer hover:shadow-2xl hover:scale-105 transition-all duration-300 group"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform"
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
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                  />
                </svg>
              </div>
              <svg
                class="w-5 h-5 opacity-70 group-hover:opacity-100 transition-opacity"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </div>
            <div class="text-4xl font-bold mb-2">{{ totalWords }}</div>
            <div class="text-blue-100">
              Total Words <span class="text-xs">(Click to view)</span>
            </div>
          </div>

          <div
            @click="showMasteredWordsModal"
            class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white cursor-pointer hover:shadow-2xl hover:scale-105 transition-all duration-300 group"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform"
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
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <svg
                class="w-5 h-5 opacity-70 group-hover:opacity-100 transition-opacity"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </div>
            <div class="text-4xl font-bold mb-2">{{ masteredWords }}</div>
            <div class="text-green-100">
              Mastered Words <span class="text-xs">(Click to view)</span>
            </div>
          </div>

          <div
            class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center"
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
                    d="M13 10V3L4 14h7v7l9-11h-7z"
                  />
                </svg>
              </div>
            </div>
            <div class="text-4xl font-bold mb-2">{{ currentStreak }}</div>
            <div class="text-orange-100">Day Streak</div>
            <div class="text-sm mt-1 text-orange-100">
              Longest: {{ longestStreak }} days
            </div>
          </div>

          <div
            class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center"
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
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                  />
                </svg>
              </div>
            </div>
            <div class="text-4xl font-bold mb-2">
              {{ Math.round(accuracyPercentage) }}%
            </div>
            <div class="text-purple-100">Accuracy Rate</div>
          </div>
        </div>

        <!-- Activity Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-3 mb-4">
              <div
                class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5 text-blue-600 dark:text-blue-400"
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
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Quizzes
              </h3>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
              {{ totalQuizzes }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
              Completed
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-3 mb-4">
              <div
                class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5 text-green-600 dark:text-green-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                  />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Reviews
              </h3>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
              {{ totalReviews }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
              Completed
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center gap-3 mb-4">
              <div
                class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5 text-orange-600 dark:text-orange-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Due Today
              </h3>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
              {{ dueForReview }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
              Words to review
            </div>
          </div>
        </div>

        <!-- Words by Status -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-8">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Learning Status
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-gray-600 dark:text-gray-400">New Words</span>
                <span class="font-bold text-purple-600 dark:text-purple-400">{{
                  wordsByStatus.new || 0
                }}</span>
              </div>
              <ProgressBar
                :current="wordsByStatus.new || 0"
                :total="totalWords || 1"
                color="primary"
                :show-details="false"
              />
            </div>

            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-gray-600 dark:text-gray-400">Learning</span>
                <span class="font-bold text-yellow-600 dark:text-yellow-400">{{
                  wordsByStatus.learning || 0
                }}</span>
              </div>
              <ProgressBar
                :current="wordsByStatus.learning || 0"
                :total="totalWords || 1"
                color="warning"
                :show-details="false"
              />
            </div>

            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-gray-600 dark:text-gray-400">Mastered</span>
                <span class="font-bold text-green-600 dark:text-green-400">{{
                  wordsByStatus.mastered || 0
                }}</span>
              </div>
              <ProgressBar
                :current="wordsByStatus.mastered || 0"
                :total="totalWords || 1"
                color="success"
                :show-details="false"
              />
            </div>
          </div>
        </div>

        <!-- Progress by Level -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Progress by Level
          </h2>
          <div v-if="hasAnyLevelProgress" class="space-y-4">
            <div v-for="level in activeLevels" :key="level">
              <div class="flex items-center gap-4">
                <LevelBadge :level="level" />
                <div class="flex-1">
                  <ProgressBar
                    :current="getLevelProgress(level).mastered"
                    :total="getLevelProgress(level).total"
                    :label="`${getLevelProgress(level).mastered} / ${
                      getLevelProgress(level).total
                    } mastered`"
                    color="success"
                  />
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-12">
            <svg
              class="w-16 h-16 mx-auto text-gray-400 mb-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            <p class="text-gray-600 dark:text-gray-400 text-lg mb-2">
              No progress yet
            </p>
            <p class="text-gray-500 dark:text-gray-500 text-sm">
              Start learning words to see your progress by level
            </p>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex gap-4 justify-center">
          <router-link
            to="/vocabulary"
            class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold flex items-center gap-2"
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
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
              />
            </svg>
            Practice Now
          </router-link>
        </div>
      </div>
    </div>

    <!-- Words Modal -->
    <transition name="modal-fade">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm"
        @click="closeModal"
      >
        <div
          class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden"
          @click.stop
        >
          <!-- Modal Header -->
          <div
            class="bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-6 flex items-center justify-between"
          >
            <h2 class="text-2xl font-bold text-white">{{ modalTitle }}</h2>
            <button
              @click="closeModal"
              class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl flex items-center justify-center transition-all"
            >
              <svg
                class="w-6 h-6 text-white"
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

          <!-- Modal Body -->
          <div class="p-8 overflow-y-auto max-h-[calc(80vh-120px)]">
            <div v-if="modalWords.length === 0" class="text-center py-12">
              <svg
                class="w-16 h-16 mx-auto text-gray-400 mb-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <p class="text-gray-500 dark:text-gray-400 text-lg">
                No words to display yet
              </p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div
                v-for="word in modalWords"
                :key="word.id"
                class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 hover:shadow-lg transition-shadow group cursor-pointer"
                @click="goToVocabulary"
              >
                <div class="flex items-start justify-between mb-2">
                  <h3
                    class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors"
                  >
                    {{ word.word.word }}
                  </h3>
                  <span
                    :class="[
                      'px-2 py-1 rounded-lg text-xs font-semibold',
                      word.status === 'mastered'
                        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                        : word.status === 'learning'
                        ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300'
                        : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
                    ]"
                  >
                    {{ word.status }}
                  </span>
                </div>
                <p
                  class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2"
                >
                  {{ word.word.definition }}
                </p>
                <div class="mt-2 flex items-center gap-2">
                  <span
                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300"
                  >
                    {{ word.word.level }}
                  </span>
                  <span class="text-xs text-gray-500 dark:text-gray-400">
                    • {{ word.reviewCount }} reviews
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div
            class="bg-gray-50 dark:bg-gray-900 px-8 py-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700"
          >
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Total:
              <span class="font-semibold">{{ modalWords.length }}</span> word(s)
            </p>
            <button
              @click="closeModal"
              class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg font-semibold transition-all"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { useProgressStore } from "../stores/progress";
import { useUserWordsStore } from "../stores/userWords";
import LevelBadge from "../components/LevelBadge.vue";
import ProgressBar from "../components/ProgressBar.vue";
import LoadingSpinner from "../components/LoadingSpinner.vue";

const router = useRouter();
const progressStore = useProgressStore();
const userWordsStore = useUserWordsStore();

const loading = computed(() => progressStore.loading);
const totalWords = computed(() => progressStore.totalWords);
const masteredWords = computed(() => progressStore.masteredWords);
const currentStreak = computed(() => progressStore.currentStreak);
const longestStreak = computed(() => progressStore.longestStreak);
const accuracyPercentage = computed(() => progressStore.accuracyPercentage);
const totalQuizzes = computed(() => progressStore.totalQuizzes);
const totalReviews = computed(() => progressStore.totalReviews);
const wordsByStatus = computed(() => progressStore.wordsByStatus);
const dueForReview = computed(() => progressStore.dueForReview);
const levelProgress = computed(() => progressStore.levelProgress);

// Modal state
const showModal = ref(false);
const modalTitle = ref("");
const modalWords = ref([]);

// Computed for active levels (levels with words)
const activeLevels = computed(() => {
  const allLevels = ["A1", "A2", "B1", "B2", "C1", "C2"];
  return allLevels.filter((level) => {
    const progress = levelProgress.value[level];
    return progress && progress.total > 0;
  });
});

const hasAnyLevelProgress = computed(() => activeLevels.value.length > 0);

const getLevelProgress = (level) => {
  const progress = levelProgress.value[level];
  if (!progress) {
    return { total: 0, mastered: 0 };
  }
  return progress;
};

const showAllWordsModal = async () => {
  modalTitle.value = "All Your Words";
  await userWordsStore.fetchUserWords();
  modalWords.value = userWordsStore.userWords || [];
  console.log("All words:", modalWords.value.length);
  showModal.value = true;
};

const showMasteredWordsModal = async () => {
  modalTitle.value = "Mastered Words";
  await userWordsStore.fetchUserWords();
  modalWords.value = (userWordsStore.userWords || []).filter(
    (word) => word.status === "mastered"
  );
  console.log("Mastered words:", modalWords.value.length);
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  modalWords.value = [];
};

const goToVocabulary = () => {
  closeModal();
  router.push("/vocabulary");
};

onMounted(async () => {
  await progressStore.fetchProgress();
});
</script>

<style scoped>
/* Modal animations */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-active .bg-white,
.modal-fade-leave-active .bg-white {
  transition: transform 0.3s ease;
}

.modal-fade-enter-from .bg-white {
  transform: scale(0.9);
}

.modal-fade-leave-to .bg-white {
  transform: scale(0.9);
}

/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
