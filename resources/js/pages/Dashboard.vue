<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { onMounted } from 'vue';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
const props = defineProps({
    created : { type: Object, required: true },
    joined : { type: Object, required: true },
});

onMounted(() => {
    // console.log(props.created);
})
const handleInviteParticipant = (e : Event) => {
    e.preventDefault();
    // console.log(e);
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="p-4 flex flex-col gap-4">
                    <div class="flex items-center justify-between max-w-xg">
                        <h3 class="mb-4 text-xl font-semibold"> Vos Groups ({{ created?.length || 0 }})</h3>

                        <Link :href="route('groups.create')" class="flex items-center gap-2" v-if="created?.length">
                            <span class="text-sm font-medium text-gray-900 dark:text-white btn">Ajouter un groupe</span>
                        </Link>
                    </div>
                    
                
                    <ul class="w-full divide-y divide-gray-200 dark:divide-gray-700" v-if="created?.length">
                        <li class="py-3 sm:py-4" v-for="group in created">
                            <div class="grid grid-cols-3 gap-2">
                                <p class="text-sm font-semibold text-gray-900 truncate dark:text-white">
                                   {{ group.name }}
                                </p>
                                <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                   {{ group.description.slice(0, 20) }}...
                                </p>
                                <div class="flex justify-between text-sm items-center  font-semibold">
                                    <span v-if="group.participants_count > 1" title="Participants">
                                        {{ group.participants_count }} Participants
                                    </span>
                                    <span v-else title="Participant">
                                        <Link :href="route('groups.show', group.id)" title="Inviter Participant" 
                                            class="btn-link">
                                                Inviter <span class="sr-only">Participant" </span>
                                            </Link>
                                    </span>

                                    <Link :href="route('groups.show', group.id)" class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white btn">Détails</span>
                                    </Link>
                                     
                                </div>
                            </div>
                        </li>
                        <!-- <li class="py-3 sm:py-4">
                            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                <div class="shrink-0">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                    Bonnie Green
                                    </p>
                                    <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                    email@flowbite.com
                                    </p>
                                </div>
                                <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                    $3467
                                </div>
                            </div>
                        </li> -->
                    </ul>

                    <div v-else class="flex items-center  justify-center h-96">
                        <div class="flex flex-col items-center">
                            <h3 class="mb-4 text-lg font-semibold">Vous n'avez pas de groupes</h3>
                            <Link :href="route('groups.create')" class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white btn">Ajouter un groupe</span>
                            </Link>
                        </div>
                    </div>
                </div>
               

            </div>
        </div>

        <Dialog>
            <DialogTrigger as-child>
                <Button variant="outline">
                Edit Profile
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                <DialogTitle>Edit profile</DialogTitle>
                <DialogDescription>
                    Make changes to your profile here. Click save when you're done.
                </DialogDescription>
                </DialogHeader>

                <form id="dialogForm" @submit="handleInviteParticipant($event)">
                
                </form>

                <DialogFooter>
                <Button type="submit" form="dialogForm">
                    Save changes
                </Button>
                </DialogFooter>
            </DialogContent>
            </Dialog>
    </AppLayout>
</template>
