<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'

import SummaryCard from '@/components/Dashboard/SummaryCard.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import type { BreadcrumbItem, SharedData } from '@/types'

interface PaymentItem {
  id: number | string
  montant: number | string
  date_paiement?: string | null
  motif?: string | null
  group?: { id: number | string; name: string } | null
  recipient?: { id: number | string; name: string } | null
  author?: { id: number | string; name: string } | null
}

interface PageProps extends SharedData {
  payments: {
    data: PaymentItem[]
    links: Array<{ url: string | null; label: string; active: boolean }>
  }
  metrics: {
    total: number
    total_amount_sent: number
    total_amount_received: number
  }
}

const page = usePage<PageProps>()
const props = computed(() => page.props)

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Tableau de bord', href: route('dashboard') },
  { title: 'Mes paiements', href: route('payments.index') },
])

const formatAmount = (value: number | string) => {
  const amount = Number(value) || 0
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount)
}

const formatDate = (date?: string | null) => {
  if (!date) return 'Non renseignée'
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(new Date(date))
}

const payments = computed(() => props.value.payments.data)
const links = computed(() => props.value.payments.links)
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
    <Head title="Mes paiements" />

    <div class="space-y-8 p-6">
      <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Mes paiements</h1>
          <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Visualisez les paiements effectués ou reçus au sein de vos groupes.
          </p>
        </div>
      </header>

      <section>
        <div class="grid gap-4 md:grid-cols-3">
          <SummaryCard title="Paiements effectués" :value="metrics.total" subtitle="Nombre total envoyés" />
          <SummaryCard title="Montant versé" :value="formatAmount(metrics.total_amount_sent)" subtitle="Somme envoyée" />
          <SummaryCard title="Montant reçu" :value="formatAmount(metrics.total_amount_received)" subtitle="Somme perçue" />
        </div>
      </section>

      <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <header class="flex items-center justify-between">
          <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Historique</h2>
          <span class="text-sm text-slate-500 dark:text-slate-400">{{ payments.length }} entrée(s)</span>
        </header>

        <div class="mt-6 overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead>
              <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                <th class="px-4 py-3">Groupe</th>
                <th class="px-4 py-3">Montant</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Destinataire</th>
                <th class="px-4 py-3">Enregistré par</th>
                <th class="px-4 py-3">Motif</th>
                <th class="px-4 py-3">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm dark:divide-slate-800">
              <tr v-if="!payments.length">
                <td colspan="7" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">
                  Aucun paiement pour le moment.
                </td>
              </tr>
              <tr
                v-for="payment in payments"
                :key="payment.id"
                class="transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
              >
                <td class="px-4 py-4">
                  <span class="font-semibold text-slate-900 dark:text-slate-100">
                    {{ payment.group?.name ?? 'Groupe inconnu' }}
                  </span>
                </td>
                <td class="px-4 py-4 font-semibold text-slate-900 dark:text-slate-100">
                  {{ formatAmount(payment.montant) }}
                </td>
                <td class="px-4 py-4 text-slate-500 dark:text-slate-400">
                  {{ formatDate(payment.date_paiement) }}
                </td>
                <td class="px-4 py-4 text-slate-500 dark:text-slate-400">
                  {{ payment.recipient?.name ?? '—' }}
                </td>
                <td class="px-4 py-4 text-slate-500 dark:text-slate-400">
                  {{ payment.author?.name ?? '—' }}
                </td>
                <td class="px-4 py-4 text-slate-500 dark:text-slate-400">
                  {{ payment.motif ?? '—' }}
                </td>
                <td class="px-4 py-4">
                  <Link
                    v-if="payment.group"
                    :href="route('groups.show', payment.group.id)"
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
