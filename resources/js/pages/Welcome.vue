<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import { ArrowRight, CheckCircle2, Mail, ShieldCheck, Sparkles, Users2, Wallet, Zap } from 'lucide-vue-next'
import type { SharedData, User } from '@/types'

const page = usePage<SharedData>()
const user = (page.props.auth?.user as User) ?? null

const features = [
    {
        icon: Users2,
        title: 'Groupes intelligents',
        description: 'Organisez vos tontines, caisses et projets associatifs en quelques minutes avec un tableau de bord clair.',
    },
    {
        icon: Wallet,
        title: 'Suivi des cotisations',
        description: 'Capturez chaque versement, ajoutez des preuves et suivez l’historique de manière transparente.',
    },
    {
        icon: ShieldCheck,
        title: 'Accès sécurisé',
        description: 'Définissez des rôles et contrôlez les autorisations pour protéger vos groupes et leurs données.',
    },
]

const steps = [
    {
        label: 'Invitez vos proches',
        detail: 'Un simple email suffit pour embarquer toute votre communauté.',
    },
    {
        label: 'Fixez les règles du jeu',
        detail: 'Montant, périodicité, participants… c’est vous qui décidez.',
    },
    {
        label: 'Collectez en toute sérénité',
        detail: 'Chaque contribution est tracée et visible par les membres autorisés.',
    },
]
</script>

<template>
    <Head title="CotizApp — Gérez vos cotisations sans stress">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="flex min-h-screen flex-col bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white">
        <header class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/80 backdrop-blur">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-5">
                <Link href="/" class="inline-flex items-center gap-2 text-lg font-semibold tracking-tight">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-500/10 text-indigo-300">
                        <Sparkles class="h-5 w-5" />
                    </span>
                    <span>CotizApp</span>
                </Link>

                <nav class="hidden items-center gap-8 text-sm font-medium text-slate-300 md:flex">
                    <a href="#features" class="transition hover:text-white">Fonctionnalités</a>
                    <a href="#workflow" class="transition hover:text-white">Comment ça marche</a>
                    <a href="#testimonials" class="transition hover:text-white">Témoignages</a>
                </nav>

                <div class="hidden items-center gap-3 md:flex">
                    <Link
                        v-if="user"
                        :href="route('dashboard')"
                        class="inline-flex items-center rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-indigo-400 hover:bg-indigo-500/10"
                    >
                        Accéder au tableau de bord
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-semibold text-slate-200 transition hover:text-white"
                        >
                            Connexion
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center gap-2 rounded-full bg-indigo-500 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-400"
                        >
                            Commencer
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <section class="relative overflow-hidden">
                <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(99,102,241,0.25),_transparent_60%)]"></div>
                <div class="mx-auto grid w-full max-w-6xl gap-12 px-6 py-24 md:grid-cols-[1.2fr,0.8fr] md:items-center">
                    <div class="space-y-8">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">
                            <Zap class="h-4 w-4 text-indigo-300" />
                            La façon moderne de gérer vos tontines
                        </div>
                        <h1 class="text-4xl font-semibold leading-tight tracking-tight md:text-5xl">
                            Rassemblez, organisez et suivez vos cotisations en toute transparence.
                        </h1>
                        <p class="max-w-xl text-base text-slate-300 md:text-lg">
                            CotizApp centralise vos groupes, invitations et versements pour fluidifier la gestion
                            financière de votre cercle. Une seule plateforme pour aligner tout le monde, sans tableur ni confusion.
                        </p>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <Link
                                :href="route('register')"
                                class="inline-flex items-center justify-center gap-2 rounded-full bg-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-400"
                            >
                                Créer un groupe gratuitement
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                            <a
                                href="#features"
                                class="inline-flex items-center justify-center gap-2 rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-slate-200 transition hover:border-indigo-400 hover:text-white"
                            >
                                Explorer les fonctionnalités
                            </a>
                        </div>
                        <div class="flex flex-wrap items-center gap-6 text-sm text-slate-300">
                            <div class="flex items-center gap-2">
                                <CheckCircle2 class="h-4 w-4 text-emerald-400" />
                                <span>Accès sécurisé</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <CheckCircle2 class="h-4 w-4 text-emerald-400" />
                                <span>Notifications par email</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <CheckCircle2 class="h-4 w-4 text-emerald-400" />
                                <span>Statistiques en temps réel</span>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-0 -m-10 rounded-3xl bg-gradient-to-br from-indigo-500/30 via-purple-500/20 to-transparent blur-3xl"></div>
                        <div class="relative rounded-3xl border border-white/10 bg-white/5 p-8 shadow-xl backdrop-blur">
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <Users2 class="h-10 w-10 rounded-full bg-indigo-500/10 p-2 text-indigo-300" />
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-slate-400">Groupes actifs</p>
                                        <p class="text-2xl font-semibold text-white">150+</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 text-sm text-slate-200">
                                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                        <p class="text-xs uppercase tracking-wide text-slate-400">Cotisations suivies</p>
                                        <p class="mt-2 text-lg font-semibold text-white">12k+</p>
                                    </div>
                                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                        <p class="text-xs uppercase tracking-wide text-slate-400">Invitations envoyées</p>
                                        <p class="mt-2 text-lg font-semibold text-white">5.6k</p>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-300">
                                        <Mail class="h-4 w-4 text-indigo-300" />
                                        Invitation en moins de 30 secondes
                                    </p>
                                    <p class="mt-2 text-sm text-slate-300">
                                        Invitez par email et suivez qui a accepté, directement depuis votre tableau de bord.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="border-t border-white/5 bg-slate-950/60 py-20">
                <div class="mx-auto flex w-full max-w-5xl flex-col gap-12 px-6">
                    <div class="text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Fonctionnalités clés</p>
                        <h2 class="mt-4 text-3xl font-semibold tracking-tight md:text-4xl">Tout ce qu’il faut pour orchestrer vos cotisations</h2>
                        <p class="mt-4 text-base text-slate-300 md:text-lg">
                            CotizApp regroupe tous les outils essentiels pour éviter les oublis, simplifier la communication et garder la vision globale de vos finances communes.
                        </p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-3">
                        <article
                            v-for="feature in features"
                            :key="feature.title"
                            class="group rounded-3xl border border-white/10 bg-white/5 p-6 transition hover:-translate-y-1 hover:border-indigo-400 hover:bg-white/10"
                        >
                            <component
                                :is="feature.icon"
                                class="mb-4 h-10 w-10 rounded-full bg-indigo-500/10 p-2 text-indigo-300 transition group-hover:bg-indigo-500/20"
                            />
                            <h3 class="text-xl font-semibold text-white">{{ feature.title }}</h3>
                            <p class="mt-3 text-sm text-slate-300">{{ feature.description }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="workflow" class="border-t border-white/5 bg-gradient-to-b from-slate-950/70 via-slate-900/40 to-slate-950/70 py-20">
                <div class="mx-auto flex w-full max-w-5xl flex-col gap-12 px-6 md:flex-row md:items-center">
                    <div class="flex-1 space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">En 3 étapes</p>
                        <h2 class="text-3xl font-semibold tracking-tight text-white md:text-4xl">Un processus fluide pensé pour votre communauté</h2>
                        <p class="text-base text-slate-300">
                            Invitez, paramétrez, collectez. CotizApp s’occupe du reste : rappels automatiques, suivi des participants, et visibilité totale sur les fonds.
                        </p>
                    </div>
                    <div class="flex-1 space-y-6">
                        <div
                            v-for="(step, index) in steps"
                            :key="step.label"
                            class="relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-6 transition hover:border-indigo-400 hover:bg-white/10"
                        >
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500/15 text-indigo-300">
                                    {{ index + 1 }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white">{{ step.label }}</h3>
                                    <p class="mt-2 text-sm text-slate-300">{{ step.detail }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="testimonials" class="border-t border-white/5 bg-slate-950/60 py-20">
                <div class="mx-auto flex w-full max-w-5xl flex-col gap-8 px-6 text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">Ils nous font confiance</p>
                    <h2 class="text-3xl font-semibold tracking-tight md:text-4xl">Créateurs de tontines, associations, clubs &amp; familles</h2>
                    <p class="max-w-3xl mx-auto text-base text-slate-300">
                        « CotizApp nous a permis de centraliser les contributions de notre association en moins d’une semaine. Chaque membre sait exactement où nous en sommes. »
                    </p>
                    <div class="flex items-center justify-center gap-2 text-sm text-slate-400">
                        <span class="font-semibold text-white">Mariam K.</span>
                        <span>•</span>
                        <span>Trésorière — Asso Solidarité</span>
                    </div>
                </div>
            </section>

            <section class="border-t border-white/10 bg-gradient-to-r from-indigo-500/20 via-purple-500/20 to-indigo-500/20 py-16">
                <div class="mx-auto flex w-full max-w-5xl flex-col items-center gap-6 px-6 text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-200">Prêt à vous lancer ?</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-white md:text-4xl">Créez votre premier groupe dès aujourd’hui</h2>
                    <p class="max-w-2xl text-base text-slate-200 md:text-lg">
                        CotizApp est gratuit pour commencer. Vous pouvez inviter vos membres, planifier les contributions et suivre vos finances partagées en quelques clics.
                    </p>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-indigo-600 transition hover:bg-slate-100"
                        >
                            Démarrer gratuitement
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center justify-center gap-2 rounded-full border border-white/40 px-6 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10"
                        >
                            Se connecter
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 bg-slate-950 py-10">
            <div class="mx-auto flex w-full max-w-6xl flex-col items-center justify-between gap-4 px-6 text-sm text-slate-400 md:flex-row">
                <div class="flex items-center gap-2 text-slate-300">
                    <Sparkles class="h-4 w-4 text-indigo-300" />
                    <span>&copy; {{ new Date().getFullYear() }} CotizApp. Tous droits réservés.</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="#features" class="transition hover:text-white">Fonctionnalités</a>
                    <a href="#workflow" class="transition hover:text-white">Processus</a>
                    <a href="#testimonials" class="transition hover:text-white">Témoignages</a>
                </div>
            </div>
        </footer>
    </div>
</template>
