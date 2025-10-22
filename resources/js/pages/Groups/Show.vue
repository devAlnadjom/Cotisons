<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import type { SharedData } from '@/types';

interface GroupShape {
  id: number;
  name: string;
  description?: string;
  periodicity?: string;
  creator?: { id: number; name: string } | null;
  participants?: Array<any>;
  cotisations?: Array<any>;
}

const page = usePage<SharedData>();
const group = page.props.group as GroupShape;

function formatAmount(v: number | string | null) {
  if (v === null || v === undefined) return '—';
  const num = Number(v) || 0;
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(num);
}

const showInviteModal = ref(false);
const inviteForm = useForm({ email: '' });

const openInvite = () => {
  inviteForm.reset();
  showInviteModal.value = true;
};

const sendInvite = () => {
  inviteForm.post(route('groups.invite', group.id), {
    onSuccess: () => {
      showInviteModal.value = false;
      // optionally: reload participants or show a toast
    },
  });
};

// Cotisation modal
const showCotisationModal = ref(false);
const cotisationForm = useForm({ montant: '', date_versement: '' });

const openCotisation = () => {
  cotisationForm.reset();
  showCotisationModal.value = true;
};

const submitCotisation = () => {
  cotisationForm.post(route('groups.cotisations.store', group.id), {
    onSuccess: () => {
      showCotisationModal.value = false;
      // optionally: refresh the page or participants
    }
  });
};

const participantsCount = computed(() => group.participants ? group.participants.length : 0);
const cotisationsCount = computed(() => group.cotisations ? group.cotisations.length : 0);
const lastCotisationAt = computed(() => (group.cotisations && group.cotisations.length) ? group.cotisations[0].date_versement : null);

function initials(name: string | undefined | null) {
  if (!name) return '—';
  return name.split(' ').map(s => s[0]).join('').slice(0,2).toUpperCase();
}
</script>

<template>
  <AppLayout>
    <Head title="Groupe — Détail" />

    <div class="flex lg:w-2/3 h-full flex-1 flex-col gap-4 rounded-xl p-4 mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-2xl font-bold">{{ group.name }}</h1>
          <p class="text-xs text-gray-500">Créé par: {{ group.creator?.name ?? '—' }}</p>
        </div>
        <div class="text-sm text-gray-500">Périodicité: {{ group.periodicity }}</div>
      </div>

      <!-- summary cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-white rounded shadow-sm text-center">
          <div class="text-xs text-gray-500">Participants</div>
          <div class="text-2xl font-bold">{{ participantsCount }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow-sm text-center">
          <div class="text-xs text-gray-500">Cotisations</div>
          <div class="text-2xl font-bold">{{ cotisationsCount }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow-sm text-center">
          <div class="text-xs text-gray-500">Dernière cotisation</div>
          <div class="text-sm text-gray-700">{{ lastCotisationAt ?? '—' }}</div>
        </div>
      </div>

      <!-- description card -->
      <div class="mb-6">
        <div class="rounded-lg bg-white ">
          <h3 class="font-semibold mb-2">Description</h3>
          <p class="text-sm text-gray-600">{{ group.description ?? 'Aucune description fournie.' }}</p>
        </div>
      </div>

      <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
          <h3 class="font-semibold mb-3">Cotisations récentes</h3>
          <ul class="space-y-3">
            <li v-if="!group.cotisations || group.cotisations.length === 0" class="p-6 bg-white rounded shadow-sm text-center text-gray-500">
              <div class="mb-2">Aucune cotisation pour le moment.</div>
              <Button variant="outline" size="sm" @click="openCotisation">Ajouter une cotisation</Button>
            </li>
            <li v-else v-for="c in group.cotisations" :key="c.id" class="p-4 bg-white rounded shadow-sm">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <div class="font-medium">{{ formatAmount(c.montant) }}</div>
                  <div class="text-sm text-gray-500">{{ c.date_versement ?? 'En attente' }}</div>
                </div>
                <div class="text-sm text-gray-600">Par: {{ c.user?.name ?? '—' }}</div>
              </div>
            </li>
          </ul>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
          <div class="flex items-center justify-between gap-2 mb-2">
            <h3 class="font-semibold mb-3">Participants</h3>
            <Button variant="outline" size="sm" @click="openInvite">+ Inviter</Button>
          </div>
          <ul class="space-y-2 border-t border-gray-200 pt-2">
            <li v-for="p in group.participants" :key="p.id" class="p-3 flex items-center justify-between">
              <div>
                <div class="font-medium">{{ p.user?.name ?? '—' }}</div>
                <div class="text-xs text-gray-500">{{ p.statut }}</div>
              </div>
              <div class="text-sm text-gray-600">{{ p.is_admin ? 'Admin' : '' }}</div>
            </li>
          </ul>
        </div>
      </section>
    </div>
  </AppLayout>

  <!-- Invite modal -->
  <transition name="fade">
    <div v-if="showInviteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Inviter un participant</h3>

        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">Adresse email</label>
          <input v-model="inviteForm.email" type="email" class="w-full border rounded px-3 py-2" placeholder="email@exemple.com" />
          <p v-if="inviteForm.errors.email" class="text-xs text-red-600 mt-1">{{ inviteForm.errors.email }}</p>
        </div>

        <div class="flex justify-end gap-3">
          <button type="button" @click="showInviteModal = false" class="px-4 py-2">Annuler</button>
          <button type="button" @click="sendInvite" class="px-4 py-2 bg-blue-600 text-white rounded">Envoyer</button>
        </div>
      </div>
    </div>
  </transition>

  <!-- Cotisation modal -->
  <transition name="fade">
    <div v-if="showCotisationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Ajouter une cotisation</h3>

        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">Montant</label>
          <input v-model="cotisationForm.montant" type="number" class="w-full border rounded px-3 py-2" placeholder="100.00" />
          <p v-if="cotisationForm.errors.montant" class="text-xs text-red-600 mt-1">{{ cotisationForm.errors.montant }}</p>
        </div>
        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">Date de versement (optionnel)</label>
          <input v-model="cotisationForm.date_versement" type="date" class="w-full border rounded px-3 py-2" />
          <p v-if="cotisationForm.errors.date_versement" class="text-xs text-red-600 mt-1">{{ cotisationForm.errors.date_versement }}</p>
        </div>

        <div class="flex justify-end gap-3">
          <button type="button" @click="showCotisationModal = false" class="px-4 py-2">Annuler</button>
          <button type="button" @click="submitCotisation" class="px-4 py-2 bg-blue-600 text-white rounded">Créer</button>
        </div>
      </div>
    </div>
  </transition>
</template>

<!-- consolidated script above -->
