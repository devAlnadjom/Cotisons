<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

import SummaryCard from '@/components/Dashboard/SummaryCard.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import type { BreadcrumbItem, SharedData } from '@/types'

type Participant = {
    id: number | string
    user_id?: number | string | null
    user?: { id: number | string; name: string } | null
    statut?: string | null
    is_admin?: boolean
}

type CotisationShape = {
    id: number | string
    montant: number | string
    date_versement?: string | null
    user?: { id: number | string; name: string } | null
    participant_id?: number | string | null
}

type GroupPermissions = {
    can_add_cotisation?: boolean
    can_invite?: boolean
}

interface GroupShape {
    id: number
    name: string
    description?: string | null
    periodicity?: string | null
    creator?: { id: number; name: string } | null
    participants?: Participant[]
    activeParticipants?: Participant[]
    cotisations?: CotisationShape[]
    permissions?: GroupPermissions
}

const page = usePage<SharedData & { group: GroupShape }>()
const group = computed(() => page.props.group as GroupShape)

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Groupes', href: route('groups.index') },
    { title: group.value?.name ?? 'Groupe', href: route('groups.show', group.value?.id ?? 0) },
])

function formatAmount(value: number | string | null) {
    if (value === null || value === undefined || value === '') return '—'
    const num = Number(value) || 0
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(num)
}

const periodicityLabel = computed(() => {
    switch (group.value?.periodicity) {
        case 'weekly':
            return 'Contribution hebdomadaire'
        case 'bi-weekly':
            return 'Contribution toutes les deux semaines'
        case 'monthly':
            return 'Contribution mensuelle'
        default:
            return 'Périodicité non définie'
    }
})

const participants = computed<Participant[]>(() => group.value.participants ?? [])
const activeParticipants = computed<Participant[]>(() => {
    if (group.value.activeParticipants) {
        return group.value.activeParticipants
    }

    return participants.value.filter((participant) => (participant.statut ?? '').toLowerCase() === 'actif')
})
const participantsCount = computed(() => participants.value.length)
const hasActiveParticipants = computed(() => activeParticipants.value.length > 0)

const cotisations = computed<CotisationShape[]>(() => group.value.cotisations ?? [])
const cotisationsCount = computed(() => cotisations.value.length)
const lastCotisationAt = computed(() =>
    cotisations.value.length ? cotisations.value[0].date_versement ?? 'En attente' : '—',
)

const permissions = computed<GroupPermissions>(() => group.value.permissions ?? {})
const canInvite = computed(() => permissions.value.can_invite ?? false)
const canAddCotisation = computed(() => permissions.value.can_add_cotisation ?? false)

const showInviteModal = ref(false)
const inviteForm = useForm({ email: '' })

const openInvite = () => {
    if (!canInvite.value) return
    inviteForm.reset()
    showInviteModal.value = true
}

const sendInvite = () => {
    inviteForm.post(route('groups.invite', group.value.id), {
        onSuccess: () => {
            showInviteModal.value = false
        },
    })
}

const showCotisationModal = ref(false)
const cotisationForm = useForm({
    participant_id: '',
    montant: '',
    date_versement: '',
})

const openCotisation = () => {
    if (!canAddCotisation.value) return
    cotisationForm.reset()
    if (activeParticipants.value.length) {
        cotisationForm.participant_id = String(activeParticipants.value[0].id)
    }
    cotisationForm.date_versement = new Date().toISOString().slice(0, 10)
    showCotisationModal.value = true
}

const submitCotisation = () => {
    cotisationForm.post(route('groups.cotisations.store', group.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showCotisationModal.value = false
        },
    })
}

function initials(name: string | undefined | null) {
    if (!name) return '—'
    return name
        .split(' ')
        .map((segment) => segment[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`${group.name} — Groupe`" />

        <div class="space-y-8 p-6">
            <section class="overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-6 py-8 text-white shadow-xl">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl space-y-3">
                        <p class="text-sm font-semibold uppercase tracking-wide text-white/70">Groupe</p>
                        <h1 class="text-3xl font-semibold md:text-4xl">{{ group.name }}</h1>
                        <p class="text-sm text-white/80 md:text-base">
                            {{ group.description ?? 'Ce groupe n’a pas encore de description détaillée.' }}
                        </p>
                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-wide text-white/70">
                            <span class="rounded-full border border-white/20 px-3 py-1 text-white">
                                {{ periodicityLabel }}
                            </span>
                            <span v-if="group.creator" class="rounded-full border border-white/20 px-3 py-1 text-white/80">
                                Créé par {{ group.creator.name }}
                            </span>
                        </div>
                    </div>
                    <div v-if="canInvite || canAddCotisation" class="flex flex-col items-start gap-3 sm:flex-row">
                        <Button
                            v-if="canInvite"
                            variant="secondary"
                            class="rounded-full border border-white/50 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20"
                            @click="openInvite"
                        >
                            Inviter un membre
                        </Button>
                        <Button
                            v-if="canAddCotisation"
                            class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-blue-700 shadow-md hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-70"
                            :disabled="!hasActiveParticipants"
                            @click="openCotisation"
                        >
                            Ajouter une cotisation
                        </Button>
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Aperçu du groupe</h2>
                    <span class="text-sm text-slate-500 dark:text-slate-400">Suivi des membres et des contributions</span>
                </div>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <SummaryCard title="Participants" :value="participantsCount" subtitle="Membres inscrits" />
                    <SummaryCard title="Cotisations" :value="cotisationsCount" subtitle="Cotisations enregistrées" />
                    <SummaryCard title="Dernière contribution" :value="lastCotisationAt" subtitle="Date de la dernière entrée" />
                </div>
            </section>

            <div class="grid gap-6 xl:grid-cols-[2fr,1fr]">
                <div class="space-y-6">
                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Cotisations récentes</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Les contributions s’affichent par ordre chronologique, la plus récente en premier.
                        </p>

                        <div class="mt-6">
                            <ul v-if="cotisations.length" class="space-y-3">
                                <li
                                    v-for="cotisation in cotisations"
                                    :key="cotisation.id"
                                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-900/60"
                                >
                                    <div class="flex flex-wrap items-start justify-between gap-4">
                                        <div>
                                            <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                                {{ formatAmount(cotisation.montant) }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ cotisation.date_versement ?? 'Date non renseignée' }}
                                            </p>
                                        </div>
                                        <div class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-medium uppercase tracking-wide text-blue-600 dark:border-blue-400/30 dark:bg-blue-400/10 dark:text-blue-200">
                                            Cotisation
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400">
                                        <span>Par {{ cotisation.user?.name ?? 'Utilisateur inconnu' }}</span>
                                        <span class="font-medium text-slate-700 dark:text-slate-200">Référence #{{ cotisation.id }}</span>
                                    </div>
                                </li>
                            </ul>
                            <div
                                v-else
                                class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500 dark:border-slate-700 dark:bg-slate-800/40"
                            >
                                <p class="font-semibold text-slate-600 dark:text-slate-300">Aucune cotisation pour le moment.</p>
                                <p class="mt-2 text-sm">Ajoutez votre première contribution pour lancer l’activité du groupe.</p>
                                <Button
                                    v-if="canAddCotisation"
                                    class="mt-4 rounded-full px-5 py-2"
                                    variant="secondary"
                                    :disabled="!hasActiveParticipants"
                                    @click="openCotisation"
                                >
                                    Ajouter une cotisation
                                </Button>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Informations supplémentaires</h3>
                        <dl class="mt-4 space-y-4 text-sm text-slate-600 dark:text-slate-300">
                            <div class="flex items-start justify-between gap-4">
                                <dt class="font-semibold text-slate-700 dark:text-slate-200">Identifiant du groupe</dt>
                                <dd>#{{ group.id }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-4">
                                <dt class="font-semibold text-slate-700 dark:text-slate-200">Périodicité</dt>
                                <dd>{{ periodicityLabel }}</dd>
                            </div>
                            <div v-if="group.creator" class="flex items-start justify-between gap-4">
                                <dt class="font-semibold text-slate-700 dark:text-slate-200">Responsable</dt>
                                <dd>{{ group.creator.name }}</dd>
                            </div>
                        </dl>
                    </section>
                </div>

                <aside class="space-y-6">
                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Participants</h3>
                            <Button
                                v-if="canInvite"
                                variant="outline"
                                size="sm"
                                class="rounded-full px-4"
                                @click="openInvite"
                            >
                                + Inviter
                            </Button>
                        </div>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            {{ participantsCount }} membre<span v-if="participantsCount > 1">s</span> participent à ce groupe.
                        </p>

                        <ul class="mt-6 space-y-3">
                            <li
                                v-for="participant in group.participants"
                                :key="participant.id"
                                class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm dark:border-slate-700 dark:bg-slate-900/50"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700 dark:bg-blue-500/20 dark:text-blue-200">
                                        {{ initials(participant.user?.name) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                            {{ participant.user?.name ?? '—' }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ participant.statut ?? 'Statut inconnu' }}
                                        </p>
                                    </div>
                                </div>
                                <span
                                    v-if="participant.is_admin"
                                    class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-medium uppercase tracking-wide text-blue-600 dark:border-blue-400/30 dark:bg-blue-400/10 dark:text-blue-200"
                                >
                                    Admin
                                </span>
                            </li>
                        </ul>

                        <div v-if="!participantsCount" class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-800/40">
                            Aucun participant pour l’instant. Invitez vos premiers membres pour démarrer.
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </AppLayout>

    <transition name="fade">
        <div v-if="showInviteModal" class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/60 backdrop-blur">
            <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Inviter un participant</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Envoyez une invitation par email pour rejoindre ce groupe.</p>

                <div class="mt-5 space-y-2">
                    <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Adresse email</label>
                    <input
                        v-model="inviteForm.email"
                        type="email"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-400"
                        placeholder="email@exemple.com"
                    />
                    <p v-if="inviteForm.errors.email" class="text-xs text-red-500">{{ inviteForm.errors.email }}</p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <Button variant="ghost" class="rounded-full px-4" @click="showInviteModal = false">Annuler</Button>
                    <Button class="rounded-full px-5" :disabled="inviteForm.processing" @click="sendInvite">Envoyer</Button>
                </div>
            </div>
        </div>
    </transition>

    <transition name="fade">
        <div v-if="showCotisationModal" class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-900/60 backdrop-blur">
            <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-700 dark:bg-slate-900">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Ajouter une cotisation</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Renseignez le montant et la date de contribution.</p>

                <div class="mt-5 space-y-2">
                    <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Participant</label>
                    <select
                        v-model="cotisationForm.participant_id"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-70 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-400"
                        :disabled="!hasActiveParticipants"
                    >
                        <option value="" disabled>Sélectionnez un participant actif</option>
                        <option
                            v-for="participant in activeParticipants"
                            :key="participant.id"
                            :value="participant.id"
                        >
                            {{ participant.user?.name ?? '—' }}
                        </option>
                    </select>
                    <p v-if="cotisationForm.errors.participant_id" class="text-xs text-red-500">{{ cotisationForm.errors.participant_id }}</p>
                </div>

                <div class="mt-5 space-y-2">
                    <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Montant</label>
                    <input
                        v-model="cotisationForm.montant"
                        type="number"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-400"
                        placeholder="100.00"
                    />
                    <p v-if="cotisationForm.errors.montant" class="text-xs text-red-500">{{ cotisationForm.errors.montant }}</p>
                </div>

                <div class="mt-4 space-y-2">
                    <label class="text-sm font-medium text-slate-600 dark:text-slate-300">Date de versement (optionnel)</label>
                    <input
                        v-model="cotisationForm.date_versement"
                        type="date"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-400"
                    />
                    <p v-if="cotisationForm.errors.date_versement" class="text-xs text-red-500">{{ cotisationForm.errors.date_versement }}</p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <Button variant="ghost" class="rounded-full px-4" @click="showCotisationModal = false">Annuler</Button>
                    <Button class="rounded-full px-5" :disabled="cotisationForm.processing" @click="submitCotisation">Créer</Button>
                </div>
            </div>
        </div>
    </transition>
</template>
