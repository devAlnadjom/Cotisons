<script setup lang="ts">
import { Sparkles } from 'lucide-vue-next'
</script>

<template>
    <div class="flex items-center gap-3">
        <div class="flex aspect-square size-9 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-500 to-sky-500 text-white shadow-lg">
            <Sparkles class="h-5 w-5" />
        </div>
        <div class="grid flex-1 text-left leading-tight">
            <span class="truncate text-base font-semibold text-sidebar-foreground">CotizApp</span>
            <span class="truncate text-xs text-sidebar-foreground/60">Simplifiez vos cotisations</span>
        </div>
    </div>
</template>
