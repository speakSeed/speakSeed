import axios from "axios";
import type {
  Word,
  UserWord,
  UserProgress,
  ApiResponse,
  PaginatedResponse,
  EnglishLevel,
} from "../types";

const API_URL = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000/api";

const api = axios.create({
  baseURL: API_URL,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  timeout: 30000,
});

// Generate or retrieve session ID
const getSessionId = (): string => {
  let sessionId = localStorage.getItem("sessionId");
  if (!sessionId) {
    sessionId =
      "session_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);
    localStorage.setItem("sessionId", sessionId);
  }
  return sessionId;
};

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const sessionId = getSessionId();

    // Add session_id to query params for GET requests
    if (config.method === "get") {
      config.params = {
        ...config.params,
        session_id: sessionId,
      };
    } else {
      // Add session_id to request body for other methods
      if (!config.data) {
        config.data = {};
      }
      if (typeof config.data === "object") {
        config.data.session_id = sessionId;
      }
    }

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => response.data,
  (error) => {
    const message =
      error.response?.data?.message ||
      error.response?.data?.error ||
      error.message ||
      "An error occurred";
    console.error("API Error:", message);
    return Promise.reject(error);
  }
);

// API endpoints
export const wordApi = {
  getByLevel(
    level: EnglishLevel,
    perPage = 10
  ): Promise<PaginatedResponse<Word>> {
    return api.get(`/words/level/${level}`, { params: { per_page: perPage } });
  },

  getRandom(level: EnglishLevel): Promise<ApiResponse<Word>> {
    return api.get("/words/random", { params: { level } });
  },

  getWord(id: number): Promise<ApiResponse<Word>> {
    return api.get(`/words/${id}`);
  },

  fetchAndStore(word: string, level: EnglishLevel): Promise<ApiResponse<Word>> {
    return api.post("/words/fetch", { word, level });
  },
};

export const userWordApi = {
  getAll(): Promise<ApiResponse<UserWord[]>> {
    return api.get("/user-words");
  },

  getForReview(): Promise<ApiResponse<UserWord[]>> {
    return api.get("/user-words/review");
  },

  addWord(wordId: number): Promise<ApiResponse<UserWord>> {
    return api.post("/user-words", { word_id: wordId });
  },

  removeWord(id: number): Promise<{ message: string }> {
    return api.delete(`/user-words/${id}`);
  },

  updateReview(
    id: number,
    correct: boolean,
    timeSpent = 0,
    attempts = 1
  ): Promise<ApiResponse<UserWord>> {
    return api.put(`/user-words/${id}`, {
      correct,
      time_spent: timeSpent,
      attempts,
    });
  },
};

export const progressApi = {
  get(): Promise<{
    progress: UserProgress;
    wordsByStatus: { new: number; learning: number; mastered: number };
    wordsByLevel: Record<string, number>;
    dueForReview: number;
  }> {
    return api.get("/progress");
  },

  update(
    level: EnglishLevel | null = null
  ): Promise<ApiResponse<UserProgress>> {
    const data = level ? { level } : {};
    return api.post("/progress", data);
  },

  getStatistics(): Promise<any> {
    return api.get("/progress/statistics");
  },
};

export const quizApi = {
  generate(mode = "definition", count = 10): Promise<any> {
    return api.post("/quiz/generate", { mode, count });
  },

  // Alias for generate - used by quiz components
  getQuiz(_sessionId: string, mode = "definition", count = 10): Promise<any> {
    return api.post("/quiz/generate", { mode, count });
  },

  submit(answers: any): Promise<any> {
    return api.post("/quiz/submit", { answers });
  },
};

export { getSessionId };
export default api;
