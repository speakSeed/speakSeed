<template>
  <div
    class="min-h-screen bg-gradient-to-br from-blue-50 to-cyan-100 dark:from-gray-900 dark:to-gray-800 py-8 px-4"
  >
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <button
          @click="goBack"
          class="flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
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
          <div
            class="text-2xl font-bold text-primary-600 dark:text-primary-400"
          >
            {{ score }} / {{ questions.length }}
          </div>
        </div>
      </div>

      <!-- Loading -->
      <LoadingSpinner v-if="loading" message="Preparing quiz..." fullScreen />

      <!-- No Words State -->
      <div
        v-else-if="!questions.length && !completed"
        class="text-center py-16"
      >
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
          No words to quiz
        </h2>
        <p class="text-gray-500 mb-8">
          Add some words to your vocabulary first!
        </p>
        <router-link
          to="/"
          class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg inline-block"
        >
          Add Words
        </router-link>
      </div>

      <!-- Quiz Question -->
      <div v-else-if="!completed && currentQuestion" class="animate-fade-in">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
          <!-- Progress -->
          <div class="mb-6">
            <ProgressBar
              :current="currentQuestionIndex + 1"
              :total="questions.length"
              :label="`Question ${currentQuestionIndex + 1} of ${
                questions.length
              }`"
            />
          </div>

          <!-- Question -->
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
              What word matches this definition?
            </h2>
            <p class="text-xl text-gray-700 dark:text-gray-300 leading-relaxed">
              {{ currentQuestion.question }}
            </p>
          </div>

          <!-- Options -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button
              v-for="(option, index) in currentQuestion.options"
              :key="index"
              @click="selectAnswer(option)"
              :disabled="answered"
              :class="getOptionClass(option)"
              class="p-6 rounded-xl font-semibold text-lg transition-all transform hover:scale-105 disabled:cursor-not-allowed"
            >
              {{ option }}
            </button>
          </div>

          <!-- Feedback -->
          <div
            v-if="answered"
            class="mt-6 p-4 rounded-xl"
            :class="
              isCorrect
                ? 'bg-green-100 dark:bg-green-900'
                : 'bg-red-100 dark:bg-red-900'
            "
          >
            <p
              class="font-semibold"
              :class="
                isCorrect
                  ? 'text-green-800 dark:text-green-200'
                  : 'text-red-800 dark:text-red-200'
              "
            >
              {{ isCorrect ? "✓ Correct!" : "✗ Incorrect" }}
              {{
                !isCorrect
                  ? `The correct answer is: ${currentQuestion.correct_answer}`
                  : ""
              }}
            </p>
          </div>

          <!-- Next Button -->
          <button
            v-if="answered"
            @click="nextQuestion"
            class="mt-6 w-full px-6 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold"
          >
            {{
              currentQuestionIndex < questions.length - 1
                ? "Next Question →"
                : "Finish Quiz"
            }}
          </button>
        </div>
      </div>

      <!-- Completion -->
      <div v-else-if="completed" class="text-center py-12 animate-fade-in">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12">
          <div class="text-6xl mb-4">{{ getScoreEmoji() }}</div>
          <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Quiz Complete!
          </h2>
          <p class="text-2xl text-gray-600 dark:text-gray-400 mb-8">
            Your Score:
            <span class="font-bold text-primary-600"
              >{{ score }} / {{ questions.length }}</span
            >
            <span class="text-lg block mt-2"
              >({{ Math.round((score / questions.length) * 100) }}%)</span
            >
          </p>

          <div class="flex gap-4 justify-center">
            <button
              @click="restartQuiz"
              class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold"
            >
              Try Again
            </button>
            <router-link
              to="/vocabulary"
              class="px-8 py-4 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-semibold"
            >
              Back to Dashboard
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
const selectedAnswer = ref(null);
const score = ref(0);
const completed = ref(false);
const startTime = ref(null);

const currentQuestion = computed(
  () => questions.value[currentQuestionIndex.value]
);
const isCorrect = computed(
  () => selectedAnswer.value === currentQuestion.value?.correct_answer
);

const loadQuiz = async () => {
  loading.value = true;
  try {
    const sessionId = storage.getSessionId();
    const data = await quizApi.getQuiz(sessionId, "quiz", 10);
    questions.value = data.questions;
    startTime.value = Date.now();
  } catch (error) {
    console.error("Error loading quiz:", error);
  } finally {
    loading.value = false;
  }
};

const selectAnswer = async (option) => {
  if (answered.value) return;

  answered.value = true;
  selectedAnswer.value = option;

  if (isCorrect.value) {
    score.value++;
  }

  // Update word review status
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
    selectedAnswer.value = null;
    startTime.value = Date.now();
  } else {
    finishQuiz();
  }
};

const finishQuiz = async () => {
  completed.value = true;
  await progressStore.updateProgress(true, false);
};

const restartQuiz = async () => {
  currentQuestionIndex.value = 0;
  answered.value = false;
  selectedAnswer.value = null;
  score.value = 0;
  completed.value = false;
  await loadQuiz();
};

const getOptionClass = (option) => {
  if (!answered.value) {
    return "bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white";
  }

  if (option === currentQuestion.value.correct_answer) {
    return "bg-green-500 text-white";
  }

  if (option === selectedAnswer.value && !isCorrect.value) {
    return "bg-red-500 text-white";
  }

  return "bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400";
};

const getScoreEmoji = () => {
  const percentage = (score.value / questions.value.length) * 100;
  if (percentage === 100) return "🎉";
  if (percentage >= 80) return "🌟";
  if (percentage >= 60) return "👍";
  if (percentage >= 40) return "💪";
  return "📚";
};

const goBack = () => {
  router.push("/vocabulary");
};

onMounted(async () => {
  await loadQuiz();
});
</script>
