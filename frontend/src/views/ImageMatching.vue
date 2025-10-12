<template>
  <div
    class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 dark:from-gray-900 dark:to-gray-800 py-8 px-4"
  >
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <button
          @click="goBack"
          class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-300"
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
        <div class="text-right">
          <div class="text-sm text-gray-600 dark:text-gray-400">Score</div>
          <div class="text-2xl font-bold text-green-600">
            {{ score }} / {{ questions.length }}
          </div>
        </div>
      </div>

      <!-- Loading -->
      <LoadingSpinner v-if="loading" message="Loading images..." fullScreen />

      <!-- Question -->
      <div v-else-if="!completed && currentQuestion" class="animate-fade-in">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
          <ProgressBar
            :current="currentQuestionIndex + 1"
            :total="questions.length"
            :label="`Image ${currentQuestionIndex + 1} of ${questions.length}`"
            color="success"
          />
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
          <h2
            class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-8"
          >
            Select the image for:
            <span class="text-green-600 dark:text-green-400">{{
              currentQuestion.word
            }}</span>
          </h2>

          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <button
              v-for="(image, index) in currentQuestion.images"
              :key="index"
              @click="selectImage(image)"
              :disabled="answered"
              :class="getImageClass(image)"
              class="relative rounded-xl overflow-hidden aspect-square transition-all transform hover:scale-105 disabled:cursor-not-allowed"
            >
              <img
                :src="image.url"
                :alt="image.word"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
              <div
                v-if="answered && image.url === currentQuestion.correct_answer"
                class="absolute inset-0 bg-green-500 bg-opacity-50 flex items-center justify-center"
              >
                <svg
                  class="w-16 h-16 text-white"
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
              </div>
              <div
                v-else-if="
                  answered && selectedImage?.url === image.url && !isCorrect
                "
                class="absolute inset-0 bg-red-500 bg-opacity-50 flex items-center justify-center"
              >
                <svg
                  class="w-16 h-16 text-white"
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
              </div>
            </button>
          </div>

          <button
            v-if="answered"
            @click="nextQuestion"
            class="mt-8 w-full px-6 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold"
          >
            {{
              currentQuestionIndex < questions.length - 1 ? "Next →" : "Finish"
            }}
          </button>
        </div>
      </div>

      <!-- Completion -->
      <div v-else-if="completed" class="text-center py-12 animate-fade-in">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12">
          <div class="text-6xl mb-4">{{ getScoreEmoji() }}</div>
          <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Complete!
          </h2>
          <p class="text-2xl text-gray-600 dark:text-gray-400 mb-8">
            Score:
            <span class="font-bold text-green-600"
              >{{ score }} / {{ questions.length }}</span
            >
          </p>
          <div class="flex gap-4 justify-center">
            <button
              @click="restartQuiz"
              class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold"
            >
              Try Again
            </button>
            <router-link
              to="/vocabulary"
              class="px-8 py-4 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-semibold"
            >
              Back
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { quizApi } from "../services/api";
import { useUserWordsStore } from "../stores/userWords";
import { useProgressStore } from "../stores/progress";
import { storage } from "../utils/localStorage";
import ProgressBar from "../components/ProgressBar.vue";
import LoadingSpinner from "../components/LoadingSpinner.vue";

const router = useRouter();
const userWordsStore = useUserWordsStore();
const progressStore = useProgressStore();

const loading = ref(false);
const questions = ref([]);
const currentQuestionIndex = ref(0);
const answered = ref(false);
const selectedImage = ref(null);
const score = ref(0);
const completed = ref(false);
const startTime = ref(null);

const currentQuestion = computed(
  () => questions.value[currentQuestionIndex.value]
);
const isCorrect = computed(
  () => selectedImage.value?.url === currentQuestion.value?.correct_answer
);

const loadQuiz = async () => {
  loading.value = true;
  try {
    const sessionId = storage.getSessionId();
    const data = await quizApi.getQuiz(sessionId, "images", 10);
    questions.value = data.questions;
    startTime.value = Date.now();
  } catch (error) {
    console.error("Error loading images quiz:", error);
  } finally {
    loading.value = false;
  }
};

const selectImage = async (image) => {
  if (answered.value) return;

  answered.value = true;
  selectedImage.value = image;

  if (isCorrect.value) {
    score.value++;
  }

  const timeSpent = Math.floor((Date.now() - startTime.value) / 1000);
  await userWordsStore.updateReview(
    currentQuestion.value.id,
    isCorrect.value,
    timeSpent,
    1
  );
};

const nextQuestion = () => {
  if (currentQuestionIndex.value < questions.value.length - 1) {
    currentQuestionIndex.value++;
    answered.value = false;
    selectedImage.value = null;
    startTime.value = Date.now();
  } else {
    finishQuiz();
  }
};

const finishQuiz = async () => {
  completed.value = true;
  await progressStore.updateProgress(false, true);
};

const restartQuiz = async () => {
  currentQuestionIndex.value = 0;
  answered.value = false;
  selectedImage.value = null;
  score.value = 0;
  completed.value = false;
  await loadQuiz();
};

const getImageClass = (image) => {
  if (!answered.value) {
    return "border-4 border-gray-200 dark:border-gray-700 hover:border-green-400";
  }
  if (image.url === currentQuestion.value.correct_answer) {
    return "border-4 border-green-500";
  }
  if (selectedImage.value?.url === image.url && !isCorrect.value) {
    return "border-4 border-red-500";
  }
  return "border-4 border-gray-300 dark:border-gray-600 opacity-60";
};

const getScoreEmoji = () => {
  const percentage = (score.value / questions.value.length) * 100;
  if (percentage === 100) return "🎉";
  if (percentage >= 80) return "🌟";
  if (percentage >= 60) return "👍";
  return "📚";
};

const handleImageError = (event) => {
  event.target.src =
    "https://via.placeholder.com/400x300/10B981/FFFFFF?text=Image";
};

const goBack = () => router.push("/vocabulary");

onMounted(async () => {
  await loadQuiz();
});
</script>
