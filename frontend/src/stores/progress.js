import { defineStore } from "pinia";
import { progressApi } from "../services/api";

export const useProgressStore = defineStore("progress", {
  state: () => ({
    progress: null,
    statistics: null,
    loading: false,
    error: null,
  }),

  getters: {
    currentLevel: (state) => state.progress?.level || null,
    totalWords: (state) => state.progress?.totalWords || 0,
    masteredWords: (state) => state.progress?.masteredWords || 0,
    currentStreak: (state) => state.progress?.currentStreak || 0,
    longestStreak: (state) => state.progress?.longestStreak || 0,
    lastActivity: (state) => state.progress?.lastActivity || null,
    totalQuizzes: (state) => state.progress?.totalQuizzes || 0,
    totalReviews: (state) => state.progress?.totalReviews || 0,

    completionPercentage: (state) => {
      if (!state.progress || state.progress.totalWords === 0) return 0;
      return Math.round(
        (state.progress.masteredWords / state.progress.totalWords) * 100
      );
    },

    learningWords: (state) => {
      if (!state.progress) return 0;
      return state.progress.totalWords - state.progress.masteredWords;
    },

    wordsByStatus: (state) => {
      if (!state.statistics) {
        return { new: 0, learning: 0, mastered: 0 };
      }
      return {
        new:
          state.statistics.total_words -
          state.statistics.learning_words -
          state.statistics.mastered_words,
        learning: state.statistics.learning_words || 0,
        mastered: state.statistics.mastered_words || 0,
      };
    },

    dueForReview: (state) => {
      return state.statistics?.due_for_review || 0;
    },

    statsByLevel: (state) => {
      if (!state.statistics || !state.statistics.by_level) return [];
      return state.statistics.by_level;
    },

    // Additional getters for LevelSelection component
    levelProgress: (state) => {
      if (!state.statistics || !state.statistics.by_level) return {};
      const progress = {};
      state.statistics.by_level.forEach((item) => {
        progress[item.level] = {
          total: item.total || 0,
          mastered: item.mastered || 0,
        };
      });
      return progress;
    },

    accuracyPercentage: (state) => {
      if (!state.statistics) return 0;
      const total = state.statistics.total_words || 0;
      const mastered = state.statistics.mastered_words || 0;
      if (total === 0) return 0;
      return (mastered / total) * 100;
    },
  },

  actions: {
    async fetchProgress() {
      this.loading = true;
      this.error = null;

      try {
        const response = await progressApi.get();

        // Backend returns: { progress, wordsByStatus, wordsByLevel, dueForReview }
        this.progress = response.progress;

        // Transform backend response to match frontend structure
        this.statistics = {
          total_words:
            (response.wordsByStatus?.new || 0) +
            (response.wordsByStatus?.learning || 0) +
            (response.wordsByStatus?.mastered || 0),
          learning_words: response.wordsByStatus?.learning || 0,
          mastered_words: response.wordsByStatus?.mastered || 0,
          due_for_review: response.dueForReview || 0,
          by_level: [], // Will be populated from wordsByLevel if needed
        };

        return response;
      } catch (error) {
        this.error = error.message;
        console.error("Error fetching progress:", error);

        // Set default values so the UI doesn't break
        this.progress = {
          currentStreak: 0,
          longestStreak: 0,
          totalWords: 0,
          masteredWords: 0,
        };
        this.statistics = {
          total_words: 0,
          learning_words: 0,
          mastered_words: 0,
          due_for_review: 0,
          by_level: [],
        };
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateProgress(level = null) {
      this.loading = true;
      this.error = null;

      try {
        const progress = await progressApi.update(level);
        this.progress = progress;
        return progress;
      } catch (error) {
        this.error = error.message;
        console.error("Error updating progress:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchStatistics() {
      this.loading = true;
      this.error = null;

      try {
        const stats = await progressApi.getStatistics();
        this.statistics = stats;
        this.progress = stats.progress;
        return stats;
      } catch (error) {
        this.error = error.message;
        console.error("Error fetching statistics:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    setLevel(level) {
      if (this.progress) {
        this.progress.level = level;
      }
      return this.updateProgress(level);
    },

    clearError() {
      this.error = null;
    },

    // Helper to format last activity date
    getFormattedLastActivity() {
      if (!this.lastActivity) return "Never";

      const date = new Date(this.lastActivity);
      const now = new Date();
      const diffTime = Math.abs(now - date);
      const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

      if (diffDays === 0) return "Today";
      if (diffDays === 1) return "Yesterday";
      if (diffDays < 7) return `${diffDays} days ago`;
      if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
      return `${Math.floor(diffDays / 30)} months ago`;
    },
  },
});
