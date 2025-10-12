// Word types
export interface Word {
  id: number;
  word: string;
  level: string;
  difficulty: number;
  definition: string;
  phonetic: string | null;
  audioUrl: string | null;
  imageUrl: string | null;
  exampleSentence: string | null;
  meanings: Meaning[];
  synonyms: string[];
  createdAt: string;
  updatedAt: string;
}

export interface Meaning {
  partOfSpeech: string;
  definitions: Definition[];
  synonyms: string[];
  antonyms: string[];
}

export interface Definition {
  definition: string;
  synonyms: string[];
  antonyms: string[];
  example?: string;
}

// User Word types
export interface UserWord {
  id: number;
  sessionId: string;
  wordId: number;
  status: WordStatus;
  addedAt: string | null;
  nextReviewDate: string | null;
  reviewCount: number;
  easeFactor: number;
  incorrectCount: number;
  interval: number;
  correctCount: number;
  createdAt: string;
  updatedAt: string;
  word?: Word;
}

export type WordStatus = "new" | "learning" | "mastered";

// Progress types
export interface UserProgress {
  id: number;
  sessionId: string;
  totalWords: number;
  masteredWords: number;
  currentStreak: number;
  longestStreak: number;
  accuracyPercentage: number;
  totalQuizzes: number;
  totalReviews: number;
  levelProgress: LevelProgress;
  lastActivity: string | null;
  createdAt: string;
  updatedAt: string;
}

export interface LevelProgress {
  [key: string]: {
    total: number;
    mastered: number;
  };
}

export interface ProgressStatistics {
  total_words: number;
  learning_words: number;
  mastered_words: number;
  due_for_review: number;
  by_level: LevelStat[];
}

export interface LevelStat {
  level: string;
  total: number;
  mastered: number;
}

// API Response types
export interface ApiResponse<T> {
  data: T;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page?: number;
  last_page?: number;
  per_page?: number;
  total?: number;
}

// Level types
export type EnglishLevel = "A1" | "A2" | "B1" | "B2" | "C1" | "C2";

export interface LevelInfo {
  id: EnglishLevel;
  name: string;
  description: string;
  colorClass: string;
}

// Quiz types
export interface QuizQuestion {
  id: number;
  word: Word;
  type: QuizType;
  question: string;
  options: string[];
  correctAnswer: string;
}

export type QuizType = "definition" | "image" | "listening" | "writing";

// Store State types
export interface WordsState {
  currentWord: Word | null;
  words: Word[];
  selectedLevel: EnglishLevel | null;
  loading: boolean;
  error: string | null;
}

export interface UserWordsState {
  userWords: UserWord[];
  wordsForReview: UserWord[];
  loading: boolean;
  error: string | null;
}

export interface ProgressState {
  progress: UserProgress | null;
  statistics: ProgressStatistics | null;
  loading: boolean;
  error: string | null;
}
