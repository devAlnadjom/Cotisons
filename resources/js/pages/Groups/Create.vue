<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'

import HeadingSmall from '@/components/HeadingSmall.vue'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'

interface Props {
    mustVerifyEmail: boolean
    status?: string
}

defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Groupes', href: '/groups' },
    { title: 'Nouveau', href: route('groups.create') },
]

const periodicities = [
    {
        id: 'weekly',
        title: 'Hebdomadaire',
        description: 'Chaque semaine',
    },
    {
        id: 'bi-weekly',
        title: 'Bi-hebdomadaire',
        description: 'Toutes les deux semaines',
    },
    {
        id: 'monthly',
        title: 'Mensuel',
        description: 'Chaque mois',
    },
]

const form = useForm({
    name: '',
    description: '',
    periodicity: '',
})

const submit = () => {
    form.post(route('groups.store'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Création de groupe" />

        <div class="space-y-8 p-6">
            <section class="overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-6 py-8 text-white shadow-xl">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-wide text-white/70">Nouveau groupe</p>
                        <h1 class="mt-2 text-3xl font-semibold md:text-4xl">Rassemblez votre communauté</h1>
                        <p class="mt-3 text-sm text-white/80 md:text-base">
                            Créez un espace de cotisation en définissant ses objectifs, ses règles et son rythme de participation.
                        </p>
                    </div>
                    <Link
                        :href="route('groups.index')"
                        class="inline-flex items-center justify-center rounded-full border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600"
                    >
                        Retour à vos groupes
                    </Link>
                </div>
            </section>

            <div class="mx-auto w-full max-w-4xl space-y-8">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                    <HeadingSmall title="Informations du groupe" description="Partagez aux membres pourquoi ce groupe existe et comment il fonctionnera." />

                    <form @submit.prevent="submit" class="mt-8 space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">Nom du groupe</Label>
                            <Input id="name" v-model="form.name" required placeholder="Ex: Tontine famille, Projet voyage..." />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 transition focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-blue-400"
                                placeholder="Décrivez l'objectif du groupe, les règles, et toute information utile aux membres."
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Fréquence de contribution</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    Sélectionnez la périodicité qui correspond à la cadence prévue pour vos cotisations.
                                </p>
                            </div>

                            <ul class="grid gap-4 sm:grid-cols-3">
                                <li
                                    v-for="option in periodicities"
                                    :key="option.id"
                                >
                                    <input
                                        :id="`periodicity-${option.id}`"
                                        v-model="form.periodicity"
                                        type="radio"
                                        name="periodicity"
                                        :value="option.id"
                                        class="peer sr-only"
                                        required
                                    />
                                    <label
                                        :for="`periodicity-${option.id}`"
                                        class="group flex h-full flex-col justify-between rounded-2xl border border-slate-200 bg-slate-50 p-5 text-left text-sm font-medium text-slate-600 transition hover:-translate-y-0.5 hover:border-blue-400 hover:bg-white hover:text-blue-600 peer-checked:border-blue-600 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-md dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-blue-500 dark:hover:text-blue-300 dark:peer-checked:border-blue-400 dark:peer-checked:bg-slate-900 dark:peer-checked:text-blue-200"
                                    >
                                        <span class="text-base font-semibold">{{ option.title }}</span>
                                        <span class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                            {{ option.description }}
                                        </span>
                                        <span class="mt-4 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-blue-600 opacity-0 transition group-hover:opacity-100 peer-checked:opacity-100 dark:text-blue-300">
                                            Sélectionné
                                        </span>
                                    </label>
                                </li>
                            </ul>
                            <InputError class="mt-2" :message="form.errors.periodicity" />
                        </div>

                        <div class="flex flex-wrap items-center gap-4">
                            <Button :disabled="form.processing" class="rounded-full px-6 py-3">
                                Passer à l'étape suivante
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p v-show="form.recentlySuccessful" class="text-sm text-slate-500 dark:text-slate-400">Enregistré.</p>
                            </Transition>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
