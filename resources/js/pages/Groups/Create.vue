<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
   name: null,
   description: null,
   periodicity: null,
});

const submit = () => {
    form.post(route('groups.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Creation de groupe" />

            
            <div class="flex lg:w-2/3 h-full flex-1 flex-col gap-4 rounded-xl p-4 mx-auto">
                <HeadingSmall title="Nouveau groupe" description="Renseignez les informations du nouveau groupe" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">Nom du groupe</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" required placeholder="Nom du groupe" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.description"
                            required
                            autocomplete="username"
                            placeholder="Description de votre groupe" 
                        />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>
                    <div class="grid gap-2">
                        <h3 class="mb-5 text-lg font-medium text-gray-900 dark:text-white">How much do you expect to use each month?</h3>
                        <ul class="grid w-full gap-6 md:grid-cols-3">
                            <li>
                                <input type="radio" id="periodicity-weekly" name="periodity" v-model="form.periodicity" value="weekly" class="hidden peer" required />
                                <label for="periodicity-weekly" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-gray-500 peer-checked:border-gray-600 dark:peer-checked:border-gray-600 peer-checked:text-gray-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">                           
                                    <div class="block">
                                        <div class="w-full  font-semibold">Hebdomadaire</div>
                                        <div class="w-full text-sm">Chaque semaine</div>
                                    </div>
                                    <svg class="w-5 h-5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </label>
                            </li>
                            <li>
                                <input type="radio" id="periodicity-bi-weekly" name="periodity" v-model="form.periodicity" value="bi-weekly" class="hidden peer" required />
                                <label for="periodicity-bi-weekly" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-gray-500 peer-checked:border-gray-600 dark:peer-checked:border-gray-600 peer-checked:text-gray-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">                           
                                    <div class="block">
                                        <div class="w-full font-semibold">Bi-Hebdomadaire</div>
                                        <div class="w-full text-sm">Chaque 2 semaine</div>
                                    </div>
                                    <svg class="w-5 h-5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </label>
                            </li>
                            <li>
                                <input type="radio" id="periodicity-monthly" name="periodity" v-model="form.periodicity" value="monthly" class="hidden peer" required />
                                <label for="periodicity-monthly" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-gray-500 peer-checked:border-gray-600 dark:peer-checked:border-gray-600 peer-checked:text-gray-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">                           
                                    <div class="block">
                                        <div class="w-full  font-semibold">Mensuel</div>
                                        <div class="w-full text-sm">Chaque Mois</div>
                                    </div>
                                    <svg class="w-5 h-5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                    </svg>
                                </label>
                            </li>
                           
                        </ul>

                    </div>


                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Passer à l'étape suivante</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <!-- <pre>
                {{ form }}
            </pre> -->

    </AppLayout>
</template>
