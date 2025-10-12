<template>
  <div
    class="min-h-screen bg-gradient-to-br from-orange-50 to-amber-100 dark:from-gray-900 dark:to-gray-800 py-8 px-4"
  >
    <div class="max-w-3xl mx-auto">
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
          <div class="text-2xl font-bold text-orange-600">
            {{ score }} / {{ questions.length }}
          </div>
        </div>
      </div>

      <!-- Loading -->
      <LoadingSpinner
        v-if="loading"
        message="Preparing writing exercise..."
        fullScreen
      />

      <!-- Question -->
      <div v-else-if="!completed && currentQuestion" class="animate-fade-in">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
          <ProgressBar
            :current="currentQuestionIndex + 1"
            :total="questions.length"
            :label="`Word ${currentQuestionIndex + 1} of ${questions.length}`"
            color="warning"
          />
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
          <h2
            class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8"
          >
            Write the word that matches this description
          </h2>

          <!-- Image -->
          <div
            v-if="currentQuestion.image_url"
            class="mb-6 rounded-xl overflow-hidden"
          >
            <img
              :src="currentQuestion.image_url"
              alt="Visual hint"
              class="w-full h-64 object-cover"
              @error="handleImageError"
            />
          </div>

          <!-- Definition -->
          <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
            <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
              {{ currentQuestion.definition }}
            </p>
          </div>

          <!-- Example -->
          <div
            v-if="currentQuestion.example_sentence"
            class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-xl"
          >
            <p
              class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1"
            >
              Example:
            </p>
            <p class="text-gray-700 dark:text-gray-300 italic">
              "{{ currentQuestion.example_sentence }}"
            </p>
          </div>

          <!-- Hint -->
          <div
            v-if="showHint"
            class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900 rounded-xl"
          >
            <p
              class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-1"
            >
              Hint:
            </p>
            <p
              class="text-2xl font-mono text-yellow-700 dark:text-yellow-300 tracking-wider"
            >
              {{ currentQuestion.hint }}
            </p>
          </div>

          <!-- Input -->
          <div class="mb-6">
            <input
              v-model="userAnswer"
              @keyup.enter="submitAnswer"
              :disabled="answered"
              type="text"
              placeholder="Type the word here..."
              class="w-full px-6 py-4 text-xl text-center border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-orange-500 focus:outline-none dark:bg-gray-700 dark:text-white disabled:bg-gray-100 dark:disabled:bg-gray-800"
              autofocus
            />
          </div>

          <!-- Feedback -->
          <div
            v-if="answered"
            class="mb-6 p-4 rounded-xl"
            :class="
              isCorrect
                ? 'bg-green-100 dark:bg-green-900'
                : 'bg-red-100 dark:bg-red-900'
            "
          >
            <p
              class="font-semibold mb-2"
              :class="
                isCorrect
                  ? 'text-green-800 dark:text-green-200'
                  : 'text-red-800 dark:text-red-200'
              "
            >
              {{ isCorrect ? "✓ Correct!" : "✗ Incorrect" }}
            </p>
            <p class="text-gray-700 dark:text-gray-300">
              The answer was:
              <span class="font-bold">{{
                currentQuestion.correct_answer
              }}</span>
            </p>
          </div>

          <!-- Actions -->
          <div class="flex gap-4">
            <button
              v-if="!answered && !showHint"
              @click="showHint = true"
              class="px-6 py-4 border-2 border-orange-300 dark:border-orange-700 text-orange-600 dark:text-orange-400 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900 flex items-center gap-2"
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
                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                />
              </svg>
              Show Hint
            </button>

            <button
              v-if="!answered"
              @click="submitAnswer"
              :disabled="!userAnswer.trim()"
              class="flex-1 px-6 py-4 bg-orange-600 hover:bg-orange-700 disabled:bg-gray-400 text-white rounded-lg font-semibold disabled:cursor-not-allowed"
            >
              Submit Answer
            </button>

            <button
              v-else
              @click="nextQuestion"
              class="flex-1 px-6 py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold"
            >
              {{
                currentQuestionIndex < questions.length - 1
                  ? "Next →"
                  : "Finish"
              }}
            </button>
          </div>
        </div>
      </div>

      <!-- Completion -->
      <div v-else-if="completed" class="text-center py-12 animate-fade-in">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12">
          <div class="text-6xl mb-4">✍️</div>
          <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Excellent Work!
          </h2>
          <p class="text-2xl text-gray-600 dark:text-gray-400 mb-8">
            Score:
            <span class="font-bold text-orange-600"
              >{{ score }} / {{ questions.length }}</span
            >
          </p>
          <div class="flex gap-4 justify-center">
            <button
              @click="restartQuiz"
              class="px-8 py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-semibold"
            >
              Practice Again
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
const userAnswer = ref("");
const showHint = ref(false);
const score = ref(0);
const completed = ref(false);
const startTime = ref(null);

const currentQuestion = computed(
  () => questions.value[currentQuestionIndex.value]
);
const isCorrect = computed(() => {
  if (!userAnswer.value || !currentQuestion.value) return false;
  return (
    userAnswer.value.trim().toLowerCase() ===
    currentQuestion.value.correct_answer.toLowerCase()
  );
});

const loadQuiz = async () => {
  loading.value = true;
  try {
    const sessionId = storage.getSessionId();
    const data = await quizApi.getQuiz(sessionId, "writing", 10);
    questions.value = data.questions;
    startTime.value = Date.now();
  } catch (error) {
    console.error("Error loading writing quiz:", error);
  } finally {
    loading.value = false;
  }
};

const submitAnswer = async () => {
  if (!userAnswer.value.trim() || answered.value) return;

  answered.value = true;

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
    userAnswer.value = "";
    showHint.value = false;
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
  userAnswer.value = "";
  showHint.value = false;
  score.value = 0;
  completed.value = false;
  await loadQuiz();
};

const handleImageError = (event) => {
  event.target.src =
    "https://via.placeholder.com/400x300/F97316/FFFFFF?text=Image";
};

const goBack = () => router.push("/vocabulary");

onMounted(async () => {
  await loadQuiz();
});
</script>
