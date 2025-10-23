<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

import SummaryCard from '@/components/Dashboard/SummaryCard.vue'
import GroupsList from '@/components/Dashboard/GroupsList.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem, SharedData } from '@/types'

type GroupItem = {
  id: number | string
  name: string
  description?: string | null
  periodicity?: string | null
  participants_count?: number
  last_cotisation_at?: string | null
  balance?: number | string | null
}

interface PageProps extends SharedData {
  created: GroupItem[]
  joined: GroupItem[]
}

const page = usePage<PageProps>()
const props = computed(() => page.props)

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  { title: 'Tableau de bord', href: route('dashboard') },
  { title: 'Mes groupes', href: route('groups.list') },
])

const createdGroups = computed(() => props.value.created ?? [])
const joinedGroups = computed(() => props.value.joined ?? [])

const totalGroups = computed(() => createdGroups.value.length + joinedGroups.value.length)
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Mes groupes" />

    <div class="space-y-8 p-6">
      <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Mes groupes</h1>
          <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Retrouvez les groupes que vous administrez et ceux auxquels vous participez.
          </p>
        </div>
        <Link
          :href="route('groups.create')"
          class="inline-flex items-center justify-center rounded-full bg-indigo-500 px-5 py-2 text-sm font-semibold text-white shadow-md transition hover:bg-indigo-400"
        >
          Créer un groupe
        </Link>
      </header>

      <section>
        <div class="grid gap-4 md:grid-cols-3">
          <SummaryCard title="Total" :value="totalGroups" subtitle="Groupes suivis" />
          <SummaryCard title="Créés" :value="createdGroups.length" subtitle="Groupes dont vous êtes créateur" />
          <SummaryCard title="Rejoints" :value="joinedGroups.length" subtitle="Groupes auxquels vous participez" />
        </div>
      </section>

      <section class="space-y-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Groupes créés</h2>
            <span class="text-sm text-slate-500 dark:text-slate-400">{{ createdGroups.length }} groupe(s)</span>
          </div>

          <div class="mt-6">
            <template v-if="createdGroups.length">
              <GroupsList :groups="createdGroups" />
            </template>
            <div
              v-else
              class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500 dark:border-slate-700 dark:bg-slate-800/40"
            >
              Vous n'avez pas encore créé de groupe.
              <Link :href="route('groups.create')" class="ml-1 text-blue-600 underline hover:text-blue-700 dark:text-blue-300">Créez-en un</Link>
              pour commencer.
            </div>
          </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Groupes rejoints</h2>
            <span class="text-sm text-slate-500 dark:text-slate-400">{{ joinedGroups.length }} groupe(s)</span>
          </div>

          <div class="mt-6">
            <template v-if="joinedGroups.length">
              <GroupsList :groups="joinedGroups" />
            </template>
            <div
              v-else
              class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500 dark:border-slate-700 dark:bg-slate-800/40"
            >
              Vous n'avez pas encore rejoint de groupe. Acceptez une invitation pour apparaître ici.
            </div>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>
