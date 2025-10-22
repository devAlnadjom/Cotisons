<script setup lang="ts">
import { defineProps } from 'vue'
const { cotisations = [] } = defineProps({ cotisations: { type: Array, default: () => [] }})
</script>

<template>
  <div>
    <h4 class="mb-2 text-lg font-semibold">Recent Cotisations</h4>
    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
      <li v-for="c in cotisations" :key="c.id" class="py-3">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ c.group?.name ?? '—' }}</p>
            <p class="text-sm text-gray-500">{{ c.amount }} — {{ c.date_versement ?? 'Pending' }}</p>
          </div>
          <div class="text-sm text-gray-600">{{ c.author?.name ?? '—' }}</div>
        </div>
      </li>
    </ul>
  </div>
</template>
