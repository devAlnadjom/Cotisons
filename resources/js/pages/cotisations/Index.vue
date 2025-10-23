<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

import SummaryCard from '@/components/Dashboard/SummaryCard.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import type { BreadcrumbItem, SharedData } from '@/types'

interface CotisationItem {
  id: number | string
  montant: number | string
  date_versement?: string | null
  created_at?: string | null
  group?: { id: number | string; name: string } | null
  author?: { id: number | string; name: string } | null
}

interface PageProps extends SharedData {
  cotisations: {
    data: CotisationItem[]
    links: Array<{ url: string | null; label: string; active: boolean }>
    meta?: Record<string, unknown>
  }
  metrics: {
    total: number
    total_amount: number
    pending: number
  }
}

const page = usePage<PageProps>()
const props = computed(() => page.props)

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Tableau de bord', href: route('dashboard') },
  { title: 'Mes cotisations', href: route('cotisations.index') },
])

function formatCurrency(value: number | string) {
  const amount = Number(value) || 0
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount)
}

function formatDate(date?: string | null) {
  if (!date) return 'En attente'
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(new Date(date))
}

const cotisations = computed(() => props.value.cotisations.data)
const links = computed(() => props.value.cotisations.links)
const metrics = computed(() => props.value.metrics)

const changePage = (url: string | null) => {
  if (!url) return
  router.visit(url, {
    preserveScroll: true,
    preserveState: true,
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Mes cotisations" />

    <div class="space-y-8 p-6">
      <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Mes cotisations</h1>
          <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Toutes vos contributions, tous groupes confondus. Gardez un œil sur votre participation.
          </p>
        </div>
        <Link
          :href="route('groups.index')"
          class="inline-flex items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-blue-400 hover:text-blue-600 dark:border-slate-700 dark:text-slate-300"
        >
          Voir mes groupes
        </Link>
      </header>

      <section>
        <div class="grid gap-4 md:grid-cols-3">
          <SummaryCard title="Cotisations versées" :value="metrics.total" subtitle="Nombre total" />
          <SummaryCard title="Montant cumulé" :value="formatCurrency(metrics.total_amount)" subtitle="Sur tous les groupes" />
          <SummaryCard title="En attente" :value="metrics.pending" subtitle="Sans date de versement" />
        </div>
      </section>

      <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <header class="flex items-center justify-between">
          <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Historique des cotisations</h2>
          <span class="text-sm text-slate-500 dark:text-slate-400">{{ cotisations.length }} entrée(s) listée(s)</span>
        </header>

        <div class="mt-6 overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead>
              <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                <th class="px-4 py-3">Groupe</th>
                <th class="px-4 py-3">Montant</th>
                <th class="px-4 py-3">Date de versement</th>
                <th class="px-4 py-3">Enregistrée par</th>
                <th class="px-4 py-3">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm dark:divide-slate-800">
              <tr v-if="!cotisations.length">
                <td colspan="5" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">
                  Aucune cotisation enregistrée pour le moment.
                </td>
              </tr>
              <tr
                v-for="cotisation in cotisations"
                :key="cotisation.id"
                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
              >
                <td class="px-4 py-4">
                  <div class="flex items-center gap-2">
                    <span class="font-semibold text-slate-900 dark:text-slate-100">
                      {{ cotisation.group?.name ?? 'Groupe inconnu' }}
                    </span>
                  </div>
                </td>
                <td class="px-4 py-4 font-semibold text-slate-900 dark:text-slate-100">
                  {{ formatCurrency(cotisation.montant) }}
                </td>
                <td class="px-4 py-4 text-slate-500 dark:text-slate-400">
                  {{ formatDate(cotisation.date_versement) }}
                </td>
                <td class="px-4 py-4 text-slate-500 dark:text-slate-400">
                  {{ cotisation.author?.name ?? '—' }}
                </td>
                <td class="px-4 py-4">
                  <Link
                    v-if="cotisation.group"
                    :href="route('groups.show', cotisation.group.id)"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 transition hover:text-blue-700 dark:text-blue-300"
                  >
                    Voir le groupe
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <footer v-if="links.length > 1" class="mt-6 flex items-center justify-between">
          <Button
            variant="ghost"
            class="rounded-full px-4"
            :disabled="!links[0].url"
            @click="changePage(links[0].url)"
          >
            Précédent
          </Button>
          <div class="flex items-center gap-2">
            <button
              v-for="(link, index) in links.slice(1, links.length - 1)"
              :key="link.label + index"
              type="button"
              class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold transition"
              :class="link.active ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800'"
              @click="changePage(link.url)"
            >
              <span v-html="link.label"></span>
            </button>
          </div>
          <Button
            variant="ghost"
            class="rounded-full px-4"
            :disabled="!links[links.length - 1].url"
            @click="changePage(links[links.length - 1].url)"
          >
            Suivant
          </Button>
        </footer>
      </section>
    </div>
  </AppLayout>
</template>
