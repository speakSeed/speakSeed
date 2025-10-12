<template>
  <div class="progress-bar-container">
    <div class="flex justify-between items-center mb-2">
      <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ label }}
      </span>
      <span
        class="text-sm font-semibold text-primary-600 dark:text-primary-400"
      >
        {{ percentage }}%
      </span>
    </div>

    <div
      class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden"
    >
      <div
        class="progress-fill h-full rounded-full transition-all duration-500 ease-out"
        :class="fillColorClass"
        :style="{ width: `${percentage}%` }"
      >
        <div class="progress-shimmer h-full"></div>
      </div>
    </div>

    <div
      v-if="showDetails"
      class="flex justify-between mt-1 text-xs text-gray-500 dark:text-gray-400"
    >
      <span>{{ current }} / {{ total }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  current: {
    type: Number,
    required: true,
  },
  total: {
    type: Number,
    required: true,
  },
  label: {
    type: String,
    default: "Progress",
  },
  showDetails: {
    type: Boolean,
    default: true,
  },
  color: {
    type: String,
    default: "primary",
    validator: (value) =>
      ["primary", "success", "warning", "danger"].includes(value),
  },
});

const percentage = computed(() => {
  if (props.total === 0) return 0;
  return Math.min(100, Math.round((props.current / props.total) * 100));
});

const fillColorClass = computed(() => {
  const colors = {
    primary: "bg-primary-600",
    success: "bg-green-600",
    warning: "bg-yellow-600",
    danger: "bg-red-600",
  };
  return colors[props.color] || colors.primary;
});
</script>

<style scoped>
.progress-shimmer {
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.3),
    transparent
  );
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}
</style>
