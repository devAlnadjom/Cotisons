<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

type Group = {
  id: number | string
  name: string
  description?: string | null
  participants_count?: number
  members_count?: number
  participants?: unknown[]
  balance?: number | string | null
}

const props = withDefaults(
    defineProps<{
        groups?: Group[]
    }>(),
    {
        groups: () => [],
    },
)

const participantCount = (group: Group) =>
    group.participants_count ??
    group.members_count ??
    (Array.isArray(group.participants) ? group.participants.length : 0)

const formatCurrency = (value?: number | string | null) => {
    if (value === null || value === undefined || value === '') return '—'
    const amount = Number(value) || 0
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount)
}
</script>

<template>
    <ul class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <li
            v-for="group in props.groups"
            :key="group.id"
        >
            <Link
                :href="route('groups.show', group.id)"
                class="group block h-full rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-500 hover:shadow-lg dark:border-slate-700 dark:bg-slate-900"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100">
                            {{ group.name }}
                        </p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            {{ group.description || 'Aucune description pour ce groupe.' }}
                        </p>
                    </div>
                    <span class="shrink-0 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-medium uppercase tracking-wide text-blue-700 group-hover:border-blue-200 group-hover:bg-blue-100 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-200">
                        Voir
                    </span>
                </div>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm">
                    <div class="font-medium text-slate-600 dark:text-slate-300">
                        {{ participantCount(group) }} participant<span v-if="participantCount(group) > 1">s</span>
                    </div>
                    <div class="rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-200">
                        Solde {{ formatCurrency(group.balance) }}
                    </div>
                </div>
                <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-blue-600 transition group-hover:gap-2 dark:text-blue-300">
                    <span>Découvrir</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </Link>
        </li>
    </ul>
</template>
