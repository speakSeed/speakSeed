import { defineStore } from "pinia";
import { wordApi } from "../services/api";

export const useWordsStore = defineStore("words", {
  state: () => ({
    currentWord: null,
    words: [],
    selectedLevel: localStorage.getItem("selectedLevel") || null,
    loading: false,
    error: null,
  }),

  getters: {
    hasCurrentWord: (state) => !!state.currentWord,
    wordsByLevel: (state) => (level) => {
      return state.words.filter((word) => word.level === level);
    },
  },

  actions: {
    setLevel(level) {
      this.selectedLevel = level;
      localStorage.setItem("selectedLevel", level);
    },

    async fetchWordsByLevel(level, perPage = 20) {
      this.loading = true;
      this.error = null;

      try {
        const response = await wordApi.getByLevel(level, perPage);
        this.words = response.data || [];
        return this.words;
      } catch (error) {
        this.error = error.message;
        console.error("Error fetching words:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchRandomWord(level = null) {
      const targetLevel = level || this.selectedLevel;

      if (!targetLevel) {
        throw new Error("No level selected");
      }

      this.loading = true;
      this.error = null;

      try {
        const response = await wordApi.getRandom(targetLevel);
        // Handle Laravel Resource format { data: {...} }
        const word = response.data || response;
        this.currentWord = word;
        return word;
      } catch (error) {
        this.error = error.message;
        console.error("Error fetching random word:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchWord(id) {
      this.loading = true;
      this.error = null;

      try {
        const response = await wordApi.getWord(id);
        // Handle Laravel Resource format { data: {...} }
        const word = response.data || response;
        this.currentWord = word;
        return word;
      } catch (error) {
        this.error = error.message;
        console.error("Error fetching word:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchAndStoreWord(wordText, level) {
      this.loading = true;
      this.error = null;

      try {
        const response = await wordApi.fetchAndStore(wordText, level);
        // Handle Laravel Resource format { data: {...} }
        const word = response.data || response;
        this.currentWord = word;
        this.words.push(word);
        return word;
      } catch (error) {
        this.error = error.message;
        console.error("Error fetching and storing word:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    clearCurrentWord() {
      this.currentWord = null;
    },

    clearError() {
      this.error = null;
    },
  },
});
