import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import LevelSelection from "../views/LevelSelection.vue";
import WordTraining from "../views/WordTraining.vue";
import VocabularyDashboard from "../views/VocabularyDashboard.vue";
import Quiz from "../views/Quiz.vue";
import ImageMatching from "../views/ImageMatching.vue";
import Listening from "../views/Listening.vue";
import Writing from "../views/Writing.vue";
import Progress from "../views/Progress.vue";

const routes: RouteRecordRaw[] = [
  {
    path: "/",
    name: "LevelSelection",
    component: LevelSelection,
    meta: { title: "Select Level" },
  },
  {
    path: "/training/:level",
    name: "WordTraining",
    component: WordTraining,
    meta: { title: "Word Training" },
  },
  {
    path: "/vocabulary",
    name: "VocabularyDashboard",
    component: VocabularyDashboard,
    meta: { title: "My Vocabulary" },
  },
  {
    path: "/vocabulary/quiz",
    name: "Quiz",
    component: Quiz,
    meta: { title: "Quiz Mode" },
  },
  {
    path: "/vocabulary/images",
    name: "ImageMatching",
    component: ImageMatching,
    meta: { title: "Image Matching" },
  },
  {
    path: "/vocabulary/listening",
    name: "Listening",
    component: Listening,
    meta: { title: "Listening Practice" },
  },
  {
    path: "/vocabulary/writing",
    name: "Writing",
    component: Writing,
    meta: { title: "Writing Practice" },
  },
  {
    path: "/progress",
    name: "Progress",
    component: Progress,
    meta: { title: "Progress Statistics" },
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// Extend route meta type
declare module "vue-router" {
  interface RouteMeta {
    title?: string;
  }
}

// Update page title
router.beforeEach((to, _from, next) => {
  const appName = import.meta.env.VITE_APP_NAME || "Vocabulary Training";
  document.title = to.meta.title ? `${to.meta.title} - ${appName}` : appName;
  next();
});

export default router;
