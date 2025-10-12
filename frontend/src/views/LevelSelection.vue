<template>
  <div
    class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900 py-16 px-4 relative overflow-hidden"
  >
    <!-- Animated background elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div
        class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300 dark:bg-purple-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-30 animate-blob"
      ></div>
      <div
        class="absolute -bottom-40 -left-40 w-80 h-80 bg-pink-300 dark:bg-pink-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-30 animate-blob animation-delay-2000"
      ></div>
      <div
        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-indigo-300 dark:bg-indigo-900 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl opacity-30 animate-blob animation-delay-4000"
      ></div>
    </div>

    <div class="max-w-7xl mx-auto relative z-10">
      <!-- Header -->
      <div class="text-center mb-16 animate-fade-in">
        <div class="inline-flex items-center justify-center mb-6">
          <div
            class="w-20 h-1 bg-gradient-to-r from-transparent via-purple-500 to-transparent"
          ></div>
          <span
            class="mx-4 text-sm font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider"
            >English Mastery</span
          >
          <div
            class="w-20 h-1 bg-gradient-to-r from-transparent via-purple-500 to-transparent"
          ></div>
        </div>
        <h1 class="text-6xl md:text-7xl font-black mb-6 leading-tight">
          <span
            class="bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 bg-clip-text text-transparent"
          >
            Choose Your Level
          </span>
        </h1>
        <p
          class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto"
        >
          Select your current proficiency level and start your learning journey
        </p>
      </div>

      <!-- Level Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
        <div
          v-for="(level, index) in levels"
          :key="level.code"
          @click="level.available ? selectLevel(level.code) : null"
          :class="[
            'level-card group relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-3xl shadow-xl p-8 transform transition-all duration-500 animate-slide-up overflow-hidden border-2',
            level.available
              ? 'cursor-pointer hover:scale-105 hover:shadow-2xl hover:-translate-y-2 border-transparent hover:border-purple-300 dark:hover:border-purple-700'
              : 'opacity-50 cursor-not-allowed border-gray-200 dark:border-gray-700',
          ]"
          :style="{ animationDelay: `${index * 100}ms` }"
        >
          <!-- Gradient overlay on hover -->
          <div
            v-if="level.available"
            class="absolute inset-0 bg-gradient-to-br from-purple-500/10 via-pink-500/10 to-indigo-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
          ></div>

          <!-- Not Available Badge -->
          <div
            v-if="!level.available"
            class="absolute top-4 right-4 px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded-full text-xs font-semibold text-gray-600 dark:text-gray-400"
          >
            Coming Soon
          </div>

          <div class="relative z-10">
            <div class="flex justify-between items-start mb-6">
              <div class="flex items-center space-x-3">
                <div
                  :class="[
                    'w-16 h-16 rounded-2xl flex items-center justify-center font-black text-2xl shadow-lg transform transition-transform duration-300',
                    level.available
                      ? 'group-hover:scale-110 group-hover:rotate-3'
                      : '',
                    `bg-gradient-to-br ${level.gradientClass}`,
                  ]"
                >
                  <span class="text-white">{{ level.code }}</span>
                </div>
                <div>
                  <LevelBadge :level="level.code" />
                </div>
              </div>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
              {{ level.name }}
            </h3>

            <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
              {{ level.description }}
            </p>

            <!-- Progress indicator if available -->
            <div
              v-if="level.available && levelProgress[level.code]"
              class="mt-4"
            >
              <ProgressBar
                :current="levelProgress[level.code].mastered || 0"
                :total="levelProgress[level.code].total || 1"
                :label="`${
                  levelProgress[level.code].mastered || 0
                } words mastered`"
                color="success"
              />
            </div>

            <!-- Start button -->
            <div
              v-if="level.available"
              class="mt-6 flex items-center text-purple-600 dark:text-purple-400 font-semibold group-hover:translate-x-2 transition-transform duration-300"
            >
              <span>Start Learning</span>
              <svg
                class="w-5 h-5 ml-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 7l5 5m0 0l-5 5m5-5H6"
                />
              </svg>
            </div>
          </div>

          <div
            class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400"
          >
            <svg
              class="w-4 h-4 mr-2"
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
            Start Training →
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in">
        <div
          class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center"
        >
          <div
            class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2"
          >
            {{ totalWords }}
          </div>
          <div class="text-gray-600 dark:text-gray-400">Words Learned</div>
        </div>

        <div
          class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center"
        >
          <div
            class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2"
          >
            {{ currentStreak }}
          </div>
          <div class="text-gray-600 dark:text-gray-400">Day Streak</div>
        </div>

        <div
          class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 text-center"
        >
          <div
            class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-2"
          >
            {{ Math.round(accuracyPercentage) }}%
          </div>
          <div class="text-gray-600 dark:text-gray-400">Accuracy</div>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="mt-12 flex justify-center gap-4">
        <router-link
          to="/vocabulary"
          class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-2"
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
          My Vocabulary
        </router-link>

        <router-link
          to="/progress"
          class="px-8 py-4 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-semibold transition-colors flex items-center gap-2"
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
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
            />
          </svg>
          View Progress
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useProgressStore } from "../stores/progress";
import LevelBadge from "../components/LevelBadge.vue";
import ProgressBar from "../components/ProgressBar.vue";

const router = useRouter();
const progressStore = useProgressStore();

const levels = [
  {
    code: "A1",
    name: "Elementary",
    description: "Basic words and phrases for everyday situations (20 words)",
    colorClass: "text-green-600 dark:text-green-400",
    gradientClass: "from-green-400 to-emerald-600",
    available: true,
  },
  {
    code: "A2",
    name: "Pre-Intermediate",
    description: "Common expressions and routine tasks (20 words)",
    colorClass: "text-blue-600 dark:text-blue-400",
    gradientClass: "from-blue-400 to-cyan-600",
    available: true,
  },
  {
    code: "B1",
    name: "Intermediate",
    description: "Work, school, leisure topics and situations (20 words)",
    colorClass: "text-yellow-600 dark:text-yellow-400",
    gradientClass: "from-yellow-400 to-orange-600",
    available: true,
  },
  {
    code: "B2",
    name: "Upper-Intermediate",
    description: "Complex texts and abstract topics (Coming soon)",
    colorClass: "text-orange-600 dark:text-orange-400",
    gradientClass: "from-orange-400 to-red-600",
    available: false,
  },
  {
    code: "C1",
    name: "Advanced",
    description: "Demanding texts and implicit meanings (Coming soon)",
    colorClass: "text-red-600 dark:text-red-400",
    gradientClass: "from-red-400 to-pink-600",
    available: false,
  },
  {
    code: "C2",
    name: "Proficient",
    description: "Master level with ease and precision (Coming soon)",
    colorClass: "text-purple-600 dark:text-purple-400",
    gradientClass: "from-purple-400 to-indigo-600",
    available: false,
  },
];

const levelProgress = computed(() => progressStore.levelProgress || {});
const totalWords = computed(() => progressStore.totalWords || 0);
const currentStreak = computed(() => progressStore.currentStreak || 0);
const accuracyPercentage = computed(
  () => progressStore.accuracyPercentage || 0
);

const selectLevel = (level) => {
  router.push({ name: "WordTraining", params: { level } });
};

onMounted(async () => {
  try {
    await progressStore.fetchProgress();
  } catch (error) {
    console.error("Error loading progress:", error);
    // Continue anyway - levels will still be visible
  }
});
</script>

<style scoped>
.level-card {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.level-card:hover {
  box-shadow: 0 25px 50px -12px rgba(147, 51, 234, 0.25),
    0 10px 25px -5px rgba(236, 72, 153, 0.15);
}

@keyframes blob {
  0%,
  100% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.8s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(40px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-up {
  animation: slideUp 0.6s ease-out backwards;
}
</style>
