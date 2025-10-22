<script setup lang="ts">
import { defineProps } from 'vue'
const { groups = [] } = defineProps({ groups: { type: Array, default: () => [] }})
</script>

<template>
  <div>
    <h4 class="mb-2 text-lg font-semibold">Recent Groups</h4>
    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
      <li v-for="g in groups" :key="g.id" class="py-3">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">{{ g.name }}</p>
            <p class="text-sm text-gray-500">{{ g.description }}</p>
          </div>
          <div class="text-sm text-gray-600">{{ g.participants_count }} participants</div>
        </div>
      </li>
    </ul>
  </div>
</template>
