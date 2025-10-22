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
        <div class="p-6">
            <!-- Summary -->
            <div class="grid gap-4 sm:grid-cols-3 mb-6">
                <SummaryCard title="Groups" :value="summary.groups_count ?? created.length" subtitle="Groups you created" />
                <SummaryCard title="Participants" :value="summary.participants_count ?? 0" subtitle="Total participants" />
                <SummaryCard title="Pending" :value="summary.pending_invitations ?? 0" subtitle="Invitations" />
            </div>

            <!-- Main content -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left: groups -->
                <div class="lg:col-span-2 space-y-6">
                    <section class="rounded-lg border bg-white p-4 shadow-sm dark:bg-gray-900">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold">Vos groups ({{ created.length }})</h2>
                            <Link :href="route('groups.create')" class="text-sm font-medium text-blue-600">+ Ajouter</Link>
                        </div>

                        <div class="space-y-4">
                            <div v-if="created.length">
                                <GroupsList :groups="created" />
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">Vous n'avez pas de groupes. <Link :href="route('groups.create')" class="text-blue-600">Créez-en un</Link>.</div>
                        </div>
                    </section>

                    <section class="rounded-lg border bg-white p-4 shadow-sm dark:bg-gray-900">
                        <h3 class="mb-3 text-lg font-semibold">Groupes joint</h3>
                        <div v-if="joined.length">
                            <GroupsList :groups="joined" />
                        </div>
                        <div v-else class="text-gray-500">Vous n'êtes membre d'aucun groupe.</div>
                    </section>
                </div>

                <!-- Right: recent cotisations -->
                <aside class="space-y-6">
                    <section class="rounded-lg border bg-white p-4 shadow-sm dark:bg-gray-900">
                        <h3 class="mb-3 text-lg font-semibold">Recent Cotisations</h3>
                        <div v-if="recent_cotisations.length">
                            <CotisationsList :cotisations="recent_cotisations" />
                        </div>
                        <div v-else class="text-gray-500">No recent cotisations.</div>
                    </section>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>
