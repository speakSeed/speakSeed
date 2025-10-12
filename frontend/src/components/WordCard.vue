<template>
  <div
    v-if="word && word.word"
    class="word-card bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 max-w-2xl mx-auto animate-fade-in"
  >
    <!-- Image -->
    <div
      v-if="showImage && word.imageUrl"
      class="mb-6 rounded-xl overflow-hidden"
    >
      <img
        :src="imageUrl"
        :alt="word.word"
        class="w-full h-64 object-cover"
        @error="handleImageError"
      />
    </div>

    <!-- Fallback: Show word letter as icon if no image -->
    <div
      v-else-if="!imageLoadError && !word.imageUrl"
      class="mb-6 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center h-64"
    >
      <span class="text-9xl font-bold text-white uppercase">
        {{ word.word.charAt(0) }}
      </span>
    </div>

    <!-- Word Title -->
    <div class="text-center mb-6">
      <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-2">
        {{ word.word }}
      </h1>

      <!-- Phonetic -->
      <p v-if="word.phonetic" class="text-xl text-gray-500 dark:text-gray-400">
        {{ word.phonetic }}
      </p>
    </div>

    <!-- Audio Controls -->
    <div class="flex justify-center gap-4 mb-6">
      <!-- Native Audio Player (if API provides audio URL) -->
      <button
        v-if="word.audioUrl"
        @click="playAudio"
        class="flex items-center gap-2 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors"
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
            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
          />
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        <span>Play Audio</span>
      </button>

      <!-- Web Speech API -->
      <button
        @click="speakWord"
        class="flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors"
        :disabled="!isSpeechSupported"
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
            d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"
          />
        </svg>
        <span>Pronounce</span>
      </button>

      <button
        @click="spellWord"
        class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors"
        :disabled="!isSpeechSupported"
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
            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
          />
        </svg>
        <span>Spell Out</span>
      </button>
    </div>

    <!-- Definition -->
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
        Definition:
      </h3>
      <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed">
        {{ word.definition }}
      </p>
    </div>

    <!-- Meanings (detailed definitions) - ALWAYS SHOW -->
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
      <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
        Detailed Meanings:
      </h3>
      <div
        v-if="
          word.meanings && Array.isArray(word.meanings) && word.meanings.length
        "
      >
        <div
          v-for="(meaning, index) in word.meanings.slice(0, 2)"
          :key="index"
          class="mb-4 last:mb-0"
        >
          <span
            class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-semibold mb-2"
          >
            {{ meaning.partOfSpeech }}
          </span>
          <ul
            v-if="meaning.definitions && meaning.definitions.length"
            class="list-disc list-inside space-y-1"
          >
            <li
              v-for="(def, defIndex) in meaning.definitions.slice(0, 2)"
              :key="defIndex"
              class="text-gray-600 dark:text-gray-400 text-sm"
            >
              {{ def.definition }}
              <span
                v-if="def.example"
                class="block ml-6 mt-1 italic text-gray-500 dark:text-gray-500"
              >
                "{{ def.example }}"
              </span>
            </li>
          </ul>
        </div>
      </div>
      <div v-else class="text-gray-500 dark:text-gray-400 text-sm italic">
        No detailed meanings available for this word.
      </div>
    </div>

    <!-- Example Sentence -->
    <div
      v-if="word.exampleSentence"
      class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border-l-4 border-amber-500"
    >
      <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
        Example:
      </h3>
      <p class="text-gray-600 dark:text-gray-400 italic">
        "{{ word.exampleSentence }}"
      </p>
    </div>

    <!-- Synonyms - ONLY SHOW IF NOT EMPTY -->
    <div
      v-if="
        word.synonyms &&
        Array.isArray(word.synonyms) &&
        word.synonyms.length > 0
      "
      class="mb-6"
    >
      <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
        Synonyms:
      </h3>
      <div class="flex flex-wrap gap-2">
        <span
          v-for="synonym in word.synonyms.slice(0, 8)"
          :key="synonym"
          class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm"
        >
          {{ synonym }}
        </span>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-4 mt-8">
      <button
        @click="$emit('next')"
        class="flex-1 px-6 py-4 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-semibold transition-colors"
      >
        Next Word →
      </button>

      <button
        @click="$emit('add')"
        :disabled="isSaved"
        class="flex-1 px-6 py-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg font-semibold transition-colors disabled:cursor-not-allowed"
      >
        {{ isSaved ? "✓ Saved" : "+ Add to Learn" }}
      </button>
    </div>

    <!-- Hidden audio element for native audio playback -->
    <audio ref="audioPlayer" :src="word.audioUrl" class="hidden"></audio>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { speech } from "../utils/speechSynthesis";
import type { Word } from "../types";

const props = defineProps<{
  word: Word;
  isSaved?: boolean;
}>();

defineEmits<{
  (e: "next"): void;
  (e: "add"): void;
}>();

const audioPlayer = ref<HTMLAudioElement | null>(null);
const isSpeechSupported = computed(() => speech.isSupported());
const imageLoadError = ref(false);
const showImage = ref(true);

// Reset image error state when word changes
watch(
  () => props.word.id,
  () => {
    imageLoadError.value = false;
    showImage.value = true;
  }
);

// Computed URL with fallback
const imageUrl = computed(() => {
  if (imageLoadError.value) {
    return ""; // Don't show anything if error
  }
  return props.word.imageUrl || "";
});

const playAudio = () => {
  if (audioPlayer.value) {
    audioPlayer.value.currentTime = 0;
    audioPlayer.value.play();
  }
};

const speakWord = () => {
  speech.speak(props.word.word);
};

const spellWord = () => {
  speech.spellOut(props.word.word);
};

const handleImageError = (event: Event) => {
  const target = event.target as HTMLImageElement;

  // Prevent infinite loop - only try once
  if (!imageLoadError.value) {
    imageLoadError.value = true;
    showImage.value = false;
    console.log(`Failed to load image for word: ${props.word.word}`);
  }

  // Stop the error from propagating
  event.stopPropagation();
};
</script>

<style scoped>
.word-card {
  transition: transform 0.3s ease;
}

.word-card:hover {
  transform: translateY(-4px);
}

@keyframes fade-in {
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
  animation: fade-in 0.5s ease-out;
}
</style>
