import { defineStore } from "pinia";
import { userWordApi } from "../services/api";
import type { UserWord, UserWordsState } from "../types";

export const useUserWordsStore = defineStore("userWords", {
  state: (): UserWordsState => ({
    userWords: [],
    wordsForReview: [],
    loading: false,
    error: null,
  }),

  getters: {
    totalWords: (state): number => state.userWords.length,

    totalSavedWords: (state): number => state.userWords.length, // Alias for compatibility

    newWords: (state): UserWord[] => {
      return state.userWords.filter((uw) => uw.status === "new");
    },

    learningWords: (state): UserWord[] => {
      return state.userWords.filter((uw) => uw.status === "learning");
    },

    masteredWords: (state): UserWord[] => {
      return state.userWords.filter((uw) => uw.status === "mastered");
    },

    wordIds: (state): number[] => {
      return state.userWords.map((uw) => uw.wordId || (uw as any).word_id); // Support both formats
    },

    isWordSaved:
      (state) =>
      (wordId: number): boolean => {
        return state.userWords.some(
          (uw) => (uw.wordId || (uw as any).word_id) === wordId
        );
      },

    reviewDueCount: (state): number => state.wordsForReview.length,
  },

  actions: {
    // Fetch all user-saved words
    async fetchUserWords(): Promise<UserWord[]> {
      this.loading = true;
      this.error = null;

      try {
        const response = await userWordApi.getAll();
        // Handle Laravel Resource format { data: [...] }
        const words = response.data || (response as any);
        this.userWords = Array.isArray(words) ? words : [];
        return this.userWords;
      } catch (error: any) {
        this.error = error.message;
        console.error("Error fetching user words:", error);
        this.userWords = [];
        // Don't throw - let the component handle empty state gracefully
        return [];
      } finally {
        this.loading = false;
      }
    },

    // Fetch words that are due for review
    async fetchWordsForReview(): Promise<UserWord[]> {
      this.loading = true;
      this.error = null;

      try {
        const response = await userWordApi.getForReview();
        // Handle Laravel Resource format { data: [...] }
        const words = response.data || (response as any);
        this.wordsForReview = Array.isArray(words) ? words : [];
        return this.wordsForReview;
      } catch (error: any) {
        this.error = error.message;
        console.error("Error fetching words for review:", error);
        this.wordsForReview = [];
        return [];
      } finally {
        this.loading = false;
      }
    },

    // Add a word to the user's learning list
    async addWord(wordId: number): Promise<UserWord | void> {
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
        const userWord = response.data || (response as any);
        this.userWords.push(userWord);
        return userWord;
      } catch (error: any) {
        // Check for invalid word ID error (422)
        if (error.response?.status === 422) {
          this.error =
            "This word is no longer available. Please refresh the page to get new words.";
        } else {
          this.error = error.message;
        }
        console.error("Error adding word:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // Remove a word from the user's learning list
    async removeWord(userWordId: number): Promise<void> {
      this.loading = true;
      this.error = null;

      try {
        await userWordApi.removeWord(userWordId);
        this.userWords = this.userWords.filter((uw) => uw.id !== userWordId);
        this.wordsForReview = this.wordsForReview.filter(
          (uw) => uw.id !== userWordId
        );
      } catch (error: any) {
        this.error = error.message;
        console.error("Error removing word:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // Update the review status of a word (spaced repetition)
    async updateReview(
      userWordId: number,
      correct: boolean,
      timeSpent = 0,
      attempts = 1
    ): Promise<UserWord> {
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
        const updatedWord = response.data || (response as any);

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
      } catch (error: any) {
        this.error = error.message;
        console.error("Error updating review:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    clearError(): void {
      this.error = null;
    },

    // Helper to get user word by word ID
    getUserWordByWordId(wordId: number): UserWord | undefined {
      return this.userWords.find(
        (uw) => (uw.wordId || (uw as any).word_id) === wordId
      );
    },
  },
});
