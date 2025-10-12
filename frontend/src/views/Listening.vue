<template>
  <div
    class="min-h-screen bg-gradient-to-br from-purple-50 to-violet-100 dark:from-gray-900 dark:to-gray-800 py-8 px-4"
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
          <div class="text-2xl font-bold text-purple-600">
            {{ score }} / {{ questions.length }}
          </div>
        </div>
      </div>

      <!-- Loading -->
      <LoadingSpinner
        v-if="loading"
        message="Preparing listening exercise..."
        fullScreen
      />

      <!-- Question -->
      <div v-else-if="!completed && currentQuestion" class="animate-fade-in">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
          <ProgressBar
            :current="currentQuestionIndex + 1"
            :total="questions.length"
            :label="`Word ${currentQuestionIndex + 1} of ${questions.length}`"
            color="primary"
          />
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
          <h2
            class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8"
          >
            Listen and type what you hear
          </h2>

          <!-- Audio Player -->
          <div class="flex justify-center mb-8">
            <button
              @click="playAudio"
              class="w-24 h-24 bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition-all"
            >
              <svg
                class="w-12 h-12 text-white"
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
            </button>
          </div>

          <!-- Phonetic hint -->
          <div
            v-if="currentQuestion.phonetic"
            class="text-center mb-6 text-gray-500 dark:text-gray-400"
          >
            {{ currentQuestion.phonetic }}
          </div>

          <!-- Input -->
          <div class="mb-6">
            <input
              v-model="userAnswer"
              @keyup.enter="submitAnswer"
              :disabled="answered"
              type="text"
              placeholder="Type what you hear..."
              class="w-full px-6 py-4 text-xl text-center border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:outline-none dark:bg-gray-700 dark:text-white disabled:bg-gray-100 dark:disabled:bg-gray-800"
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
                : 'bg-yellow-100 dark:bg-yellow-900'
            "
          >
            <p
              class="font-semibold mb-2"
              :class="
                isCorrect
                  ? 'text-green-800 dark:text-green-200'
                  : 'text-yellow-800 dark:text-yellow-200'
              "
            >
              {{ isCorrect ? "✓ Perfect!" : "~ Close!" }}
            </p>
            <p class="text-gray-700 dark:text-gray-300">
              Correct answer:
              <span class="font-bold">{{
                currentQuestion.correct_answer
              }}</span>
            </p>
          </div>

          <!-- Actions -->
          <div class="flex gap-4">
            <button
              v-if="!answered"
              @click="submitAnswer"
              :disabled="!userAnswer.trim()"
              class="flex-1 px-6 py-4 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white rounded-lg font-semibold disabled:cursor-not-allowed"
            >
              Submit Answer
            </button>
            <button
              v-else
              @click="nextQuestion"
              class="flex-1 px-6 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold"
            >
              {{
                currentQuestionIndex < questions.length - 1
                  ? "Next →"
                  : "Finish"
              }}
            </button>
          </div>

          <!-- Replay button -->
          <button
            @click="playAudio"
            class="mt-4 w-full px-6 py-3 border-2 border-purple-300 dark:border-purple-700 text-purple-600 dark:text-purple-400 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900 flex items-center justify-center gap-2"
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
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
              />
            </svg>
            Replay
          </button>
        </div>
      </div>

      <!-- Completion -->
      <div v-else-if="completed" class="text-center py-12 animate-fade-in">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12">
          <div class="text-6xl mb-4">🎧</div>
          <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Well Done!
          </h2>
          <p class="text-2xl text-gray-600 dark:text-gray-400 mb-8">
            Score:
            <span class="font-bold text-purple-600"
              >{{ score }} / {{ questions.length }}</span
            >
          </p>
          <div class="flex gap-4 justify-center">
            <button
              @click="restartQuiz"
              class="px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold"
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
import { speech } from "../utils/speechSynthesis";
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
    const data = await quizApi.getQuiz(sessionId, "listening", 10);
    questions.value = data.questions;
    startTime.value = Date.now();
    // Auto-play first word
    setTimeout(() => playAudio(), 500);
  } catch (error) {
    console.error("Error loading listening quiz:", error);
  } finally {
    loading.value = false;
  }
};

const playAudio = () => {
  if (currentQuestion.value?.word) {
    speech.speak(currentQuestion.value.word);
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
    startTime.value = Date.now();
    setTimeout(() => playAudio(), 300);
  } else {
    finishQuiz();
  }
};

const finishQuiz = async () => {
  completed.value = true;
  speech.stop();
  await progressStore.updateProgress(false, true);
};

const restartQuiz = async () => {
  currentQuestionIndex.value = 0;
  answered.value = false;
  userAnswer.value = "";
  score.value = 0;
  completed.value = false;
  await loadQuiz();
};

const goBack = () => {
  speech.stop();
  router.push("/vocabulary");
};

onMounted(async () => {
  await loadQuiz();
});
</script>
