<template>
  <div
    class="level-badge inline-flex items-center justify-center px-4 py-2 rounded-full font-semibold text-sm"
    :class="badgeClasses"
  >
    {{ level }}
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  level: {
    type: String,
    required: true,
    validator: (value) => ["A1", "A2", "B1", "B2", "C1", "C2"].includes(value),
  },
});

const badgeClasses = computed(() => {
  const classes = {
    A1: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
    A2: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
    B1: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
    B2: "bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200",
    C1: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
    C2: "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200",
  };
  return classes[props.level] || classes["A1"];
});
</script>
