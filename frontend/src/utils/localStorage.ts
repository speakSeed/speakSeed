import type { UserProgress, EnglishLevel } from "../types";

const STORAGE_KEYS = {
  SESSION_ID: "vocabulary_session_id",
  SAVED_WORDS: "vocabulary_saved_words",
  PROGRESS: "vocabulary_progress",
  THEME: "vocabulary_theme",
  CURRENT_LEVEL: "vocabulary_current_level",
} as const;

type Theme = "light" | "dark";

export const storage = {
  // Session ID
  getSessionId(): string {
    let sessionId = localStorage.getItem(STORAGE_KEYS.SESSION_ID);
    if (!sessionId) {
      sessionId = this.generateSessionId();
      this.setSessionId(sessionId);
    }
    return sessionId;
  },

  setSessionId(sessionId: string): void {
    localStorage.setItem(STORAGE_KEYS.SESSION_ID, sessionId);
  },

  generateSessionId(): string {
    return `session_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
  },

  // Saved words
  getSavedWords(): number[] {
    const words = localStorage.getItem(STORAGE_KEYS.SAVED_WORDS);
    return words ? JSON.parse(words) : [];
  },

  setSavedWords(words: number[]): void {
    localStorage.setItem(STORAGE_KEYS.SAVED_WORDS, JSON.stringify(words));
  },

  addSavedWord(wordId: number): void {
    const words = this.getSavedWords();
    if (!words.includes(wordId)) {
      words.push(wordId);
      this.setSavedWords(words);
    }
  },

  removeSavedWord(wordId: number): void {
    const words = this.getSavedWords();
    const filtered = words.filter((id) => id !== wordId);
    this.setSavedWords(filtered);
  },

  // Progress
  getProgress(): UserProgress | null {
    const progress = localStorage.getItem(STORAGE_KEYS.PROGRESS);
    return progress ? JSON.parse(progress) : null;
  },

  setProgress(progress: UserProgress): void {
    localStorage.setItem(STORAGE_KEYS.PROGRESS, JSON.stringify(progress));
  },

  // Theme
  getTheme(): Theme {
    return (localStorage.getItem(STORAGE_KEYS.THEME) as Theme) || "light";
  },

  setTheme(theme: Theme): void {
    localStorage.setItem(STORAGE_KEYS.THEME, theme);
    if (theme === "dark") {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
  },

  // Current level
  getCurrentLevel(): EnglishLevel | null {
    return localStorage.getItem(
      STORAGE_KEYS.CURRENT_LEVEL
    ) as EnglishLevel | null;
  },

  setCurrentLevel(level: EnglishLevel): void {
    localStorage.setItem(STORAGE_KEYS.CURRENT_LEVEL, level);
  },

  // Clear all
  clearAll(): void {
    Object.values(STORAGE_KEYS).forEach((key) => {
      localStorage.removeItem(key);
    });
  },
};

// Initialize theme on load
storage.setTheme(storage.getTheme());

export default storage;
