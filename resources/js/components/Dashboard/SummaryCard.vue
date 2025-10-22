<script setup lang="ts">
import { defineProps } from 'vue'

const { title, value, subtitle = '' } = defineProps({
  title: { type: String, required: true },
  value: { type: [String, Number], required: true },
  subtitle: { type: String, default: '' },
})
</script>

<template>
  <div class="rounded-lg border border-sidebar-border/70 bg-white p-4 shadow-sm dark:bg-gray-900">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ title }}</p>
        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ value }}</p>
        <p v-if="subtitle" class="mt-1 text-xs text-gray-400">{{ subtitle }}</p>
      </div>
    </div>
  </div>
</template>
