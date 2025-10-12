<template>
  <div class="flex items-center justify-center" :class="containerClass">
    <div class="relative">
      <div
        class="spinner border-4 border-gray-200 dark:border-gray-700 rounded-full"
        :class="sizeClass"
      ></div>
      <div
        class="spinner-border absolute top-0 left-0 border-4 border-transparent border-t-primary-600 rounded-full animate-spin"
        :class="sizeClass"
      ></div>
    </div>
    <p
      v-if="message"
      class="ml-4 text-gray-600 dark:text-gray-400"
      :class="textSizeClass"
    >
      {{ message }}
    </p>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  size: {
    type: String,
    default: "md",
    validator: (value) => ["sm", "md", "lg"].includes(value),
  },
  message: {
    type: String,
    default: "",
  },
  fullScreen: {
    type: Boolean,
    default: false,
  },
});

const sizeClass = computed(() => {
  const sizes = {
    sm: "w-6 h-6",
    md: "w-12 h-12",
    lg: "w-16 h-16",
  };
  return sizes[props.size];
});

const textSizeClass = computed(() => {
  const sizes = {
    sm: "text-sm",
    md: "text-base",
    lg: "text-lg",
  };
  return sizes[props.size];
});

const containerClass = computed(() => {
  return props.fullScreen ? "min-h-screen" : "py-8";
});
</script>

<style scoped>
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
