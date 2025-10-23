<script setup lang="ts">
type Cotisation = {
  id: number | string
  amount: string | number
  date_versement?: string | null
  group?: { name?: string | null }
  author?: { name?: string | null }
}

const props = withDefaults(
    defineProps<{
        cotisations?: Cotisation[]
    }>(),
    {
        cotisations: () => [],
    },
)
</script>

<template>
    <ul class="space-y-3">
        <li
            v-for="cotisation in props.cotisations"
            :key="cotisation.id"
            class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-900/60"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                        {{ cotisation.group?.name ?? 'Groupe inconnu' }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        {{ cotisation.date_versement ? `Versé le ${cotisation.date_versement}` : 'Versement en attente' }}
                    </p>
                </div>
                <div class="shrink-0 text-base font-semibold text-blue-600 dark:text-blue-300">
                    {{ cotisation.amount }}
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span>Initiée par {{ cotisation.author?.name ?? '—' }}</span>
                <span class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-[11px] font-medium uppercase tracking-wide text-blue-600 dark:border-blue-400/30 dark:bg-blue-400/10 dark:text-blue-200">
                    Cotisation
                </span>
            </div>
        </li>
    </ul>
</template>
