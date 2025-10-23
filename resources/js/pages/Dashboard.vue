<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link } from '@inertiajs/vue3'
import SummaryCard from '@/components/Dashboard/SummaryCard.vue'
import GroupsList from '@/components/Dashboard/GroupsList.vue'
import CotisationsList from '@/components/Dashboard/CotisationsList.vue'


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
]

const { created = [], joined = [], summary = {}, recent_cotisations = [] } = defineProps({
    created: { type: Array, required: true },
    joined: { type: Array, required: true },
    summary: { type: Object, required: false },
    recent_cotisations: { type: Array, required: false },
})
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8 p-6">
            <!-- Hero -->
            <section class="overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-6 py-8 text-white shadow-xl">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-wide text-white/70">Tableau de bord</p>
                        <h1 class="mt-2 text-3xl font-semibold md:text-4xl">Organisez vos cotisations en un clin d'œil</h1>
                        <p class="mt-3 text-sm text-white/80 md:text-base">
                            Visualisez vos groupes, suivez les dernières contributions et invitez vos proches à rejoindre vos projets communs.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <Link
                            :href="route('groups.create')"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-blue-700 shadow-md transition hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600"
                        >
                            <span class="text-lg leading-none">+</span>
                            Nouveau groupe
                        </Link>
                        <Link
                            :href="route('groups.index')"
                            class="inline-flex items-center justify-center gap-2 rounded-full border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600"
                        >
                            Gérer les groupes
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Summary -->
            <section>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Aperçu rapide</h2>
                    <span class="text-sm text-slate-500 dark:text-slate-400">Vos indicateurs clés</span>
                </div>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <SummaryCard title="Groupes" :value="summary.groups_count ?? created.length" subtitle="Groupes que vous avez créés" />
                    <SummaryCard title="Participants" :value="summary.participants_count ?? 0" subtitle="Participants au total" />
                    <SummaryCard title="Invitations en attente" :value="summary.pending_invitations ?? 0" subtitle="Invitations envoyées" />
                </div>
            </section>

            <!-- Main content -->
            <div class="grid gap-6 xl:grid-cols-[2fr,1fr]">
                <!-- Left: groups -->
                <div class="space-y-6">
                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
                                    Vos groupes ({{ created.length }})
                                </h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Retrouvez les groupes que vous avez créés et accédez-y rapidement.
                                </p>
                            </div>
                            <Link
                                :href="route('groups.create')"
                                class="inline-flex items-center justify-center gap-2 rounded-full border border-blue-200 px-4 py-2 text-sm font-semibold text-blue-600 transition hover:border-blue-500 hover:text-blue-700 dark:border-blue-500/40 dark:text-blue-300"
                            >
                                Créer un groupe
                            </Link>
                        </div>

                        <div class="mt-6">
                            <template v-if="created.length">
                                <GroupsList :groups="created" />
                            </template>
                            <div v-else class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500 dark:border-slate-700 dark:bg-slate-800/40">
                                <p class="font-semibold text-slate-600 dark:text-slate-300">Vous n'avez pas encore de groupes.</p>
                                <p class="mt-2 text-sm">
                                    <Link :href="route('groups.create')" class="text-blue-600 underline hover:text-blue-700 dark:text-blue-300">Créez-en un</Link>
                                    pour démarrer vos cotisations.
                                </p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">
                                    Groupes rejoints ({{ joined.length }})
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Accédez aux groupes auxquels vous participez avec d'autres membres.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <template v-if="joined.length">
                                <GroupsList :groups="joined" />
                            </template>
                            <div v-else class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500 dark:border-slate-700 dark:bg-slate-800/40">
                                <p class="font-semibold text-slate-600 dark:text-slate-300">Vous n'êtes membre d'aucun groupe.</p>
                                <p class="mt-2 text-sm">Acceptez une invitation pour apparaître ici.</p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Right: recent cotisations -->
                <aside class="space-y-6">
                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Cotisations récentes</h3>
                            <span class="text-xs uppercase tracking-wide text-slate-400">Activité</span>
                        </div>
                        <div class="mt-6">
                            <div v-if="recent_cotisations.length">
                                <CotisationsList :cotisations="recent_cotisations" />
                            </div>
                            <div v-else class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-800/40">
                                Aucune cotisation récente.
                            </div>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>
