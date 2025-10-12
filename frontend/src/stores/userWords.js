import { defineStore } from "pinia";
import { userWordApi } from "../services/api";

export const useUserWordsStore = defineStore("userWords", {
  state: () => ({
    userWords: [],
    wordsForReview: [],
    loading: false,
    error: null,
  }),

  getters: {
    totalWords: (state) => state.userWords.length,

    totalSavedWords: (state) => state.userWords.length, // Alias for compatibility

    newWords: (state) => {
      return state.userWords.filter((uw) => uw.status === "new");
    },

    learningWords: (state) => {
      return state.userWords.filter((uw) => uw.status === "learning");
    },

    masteredWords: (state) => {
      return state.userWords.filter((uw) => uw.status === "mastered");
    },

    wordIds: (state) => {
      return state.userWords.map((uw) => uw.wordId || uw.word_id); // Support both formats
    },

    isWordSaved: (state) => (wordId) => {
      return state.userWords.some((uw) => (uw.wordId || uw.word_id) === wordId);
    },

    reviewDueCount: (state) => state.wordsForReview.length,
  },

  actions: {
    async fetchUserWords() {
      this.loading = true;
      this.error = null;

      try {
        const response = await userWordApi.getAll();
        // Handle Laravel Resource format { data: [...] }
        const words = response.data || response;
        this.userWords = Array.isArray(words) ? words : [];
        return this.userWords;
      } catch (error) {
        this.error = error.message;
        console.error("Error fetching user words:", error);
        this.userWords = [];
        // Don't throw - let the component handle empty state gracefully
      } finally {
        this.loading = false;
      }
    },

    async fetchWordsForReview() {
      this.loading = true;
      this.error = null;

      try {
        const response = await userWordApi.getForReview();
        // Handle Laravel Resource format { data: [...] }
        const words = response.data || response;
        this.wordsForReview = Array.isArray(words) ? words : [];
        return this.wordsForReview;
      } catch (error) {
        this.error = error.message;
        console.error("Error fetching words for review:", error);
        this.wordsForReview = [];
      } finally {
        this.loading = false;
      }
    },

    async addWord(wordId) {
      // Check if word already exists
      if (this.isWordSaved(wordId)) {
        console.log("Word already saved");
        return;
      }

      this.loading = true;
      this.error = null;

      try {
        const response = await userWordApi.addWord(wordId);
        // Handle Laravel Resource format { data: {...} }
        const userWord = response.data || response;
        this.userWords.push(userWord);
        return userWord;
      } catch (error) {
        this.error = error.message;
        console.error("Error adding word:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async removeWord(userWordId) {
      this.loading = true;
      this.error = null;

      try {
        await userWordApi.removeWord(userWordId);
        this.userWords = this.userWords.filter((uw) => uw.id !== userWordId);
        this.wordsForReview = this.wordsForReview.filter(
          (uw) => uw.id !== userWordId
        );
      } catch (error) {
        this.error = error.message;
        console.error("Error removing word:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateReview(userWordId, correct, timeSpent = 0, attempts = 1) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userWordApi.updateReview(
          userWordId,
          correct,
          timeSpent,
          attempts
        );
        // Handle Laravel Resource format { data: {...} }
        const updatedWord = response.data || response;

        // Update in local state
        const index = this.userWords.findIndex((uw) => uw.id === userWordId);
        if (index !== -1) {
          this.userWords[index] = updatedWord;
        }

        // Remove from review list if exists
        this.wordsForReview = this.wordsForReview.filter(
          (uw) => uw.id !== userWordId
        );

        return updatedWord;
      } catch (error) {
        this.error = error.message;
        console.error("Error updating review:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    clearError() {
      this.error = null;
    },

    // Helper to get user word by word ID
    getUserWordByWordId(wordId) {
      return this.userWords.find((uw) => (uw.wordId || uw.word_id) === wordId);
    },
  },
});
