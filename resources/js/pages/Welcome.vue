<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import type { SharedData, User } from '@/types';

const page = usePage<SharedData>();
const user = (page.props.auth && (page.props.auth.user as User)) || null;
</script>

<template>
    <Head title="CotizApp — Bienvenue">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="bg-white text-black font-sans min-h-screen">
        <!-- HEADER -->
        <header class="bg-white shadow fixed w-full z-50">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <Link href="/" class="text-2xl font-bold text-red-600">CotizApp</Link>
                <nav class="space-x-6 hidden md:block">
                    <a href="#features" class="text-black hover:text-red-600">Fonctionnalités</a>
                    <a href="#invite" class="text-black hover:text-red-600">Inviter</a>
                    <a href="#faq" class="text-black hover:text-red-600">Pourquoi nous ?</a>
                    <Link v-if="user" :href="route('dashboard')" class="text-black hover:text-red-600 font-medium">Dashboard</Link>
                    <template v-else>
                        <Link :href="route('login')" class="text-black hover:text-red-600 font-medium">Connexion</Link>
                        <Link :href="route('register')" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 font-medium transition">Créer un compte</Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- HERO -->
        <section class="bg-black text-white pt-32 pb-20 px-6 text-center">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Gérez vos cotisations. Invitez vos amis. Simplifiez tout.</h1>
                <p class="text-lg mb-8">Créez facilement des groupes de cotisation, suivez les paiements, et partagez le tout avec vos proches.</p>
                <div class="flex flex-col md:flex-row justify-center gap-4">
                    <Link :href="route('register')" class="bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-semibold transition">Créer un compte</Link>
                    <a href="#invite" class="border border-white hover:bg-white hover:text-black py-3 px-6 rounded-lg font-semibold transition">Inviter des amis</a>
                </div>
            </div>
        </section>

        <!-- FONCTIONNALITÉS -->
        <section id="features" class="py-20 px-6 bg-white text-black">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-12">Fonctionnalités clés</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-xl font-semibold mb-2 text-red-600">Groupes personnalisés</h3>
                        <p>Créez autant de groupes que vous voulez avec des montants, périodicités et membres différents.</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold mb-2 text-red-600">Suivi des cotisations</h3>
                        <p>Ajoutez des preuves de paiement, suivez l’historique et automatisez les rappels.</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold mb-2 text-red-600">Invitations simples</h3>
                        <p>Invitez vos amis par email. Ils n’ont même pas besoin d’un compte pour commencer.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- INVITATION -->
        <section id="invite" class="py-20 bg-gray-100 text-center px-6">
            <div class="max-w-xl mx-auto">
                <h2 class="text-3xl font-bold mb-6">Invitez vos amis en un clic</h2>
                <p class="mb-8">Utilisez simplement leur adresse email et ils recevront une invitation personnalisée.</p>
                <form class="flex flex-col md:flex-row items-center justify-center gap-4" @submit.prevent>
                    <input type="email" placeholder="Adresse email de votre ami" class="w-full md:w-auto px-4 py-3 rounded border border-gray-300" required />
                    <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700 transition">Envoyer l’invitation</button>
                </form>
            </div>
        </section>

        <!-- POURQUOI NOUS -->
        <section id="faq" class="py-20 px-6 bg-white text-black">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-10">Pourquoi utiliser CotizApp ?</h2>
                <ul class="text-lg space-y-4">
                    <li>✔️ Simple, rapide et accessible sur mobile</li>
                    <li>✔️ Sécurisée avec gestion des accès</li>
                    <li>✔️ Pensée pour les cercles d'amis, familles, tontines ou groupes associatifs</li>
                    <li>✔️ Suivi des paiements et transparence totale</li>
                </ul>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="bg-black text-white py-8 text-center">
            <p class="text-sm">&copy; 2025 CotizApp — Tous droits réservés.</p>
        </footer>
    </div>
  
</template>
