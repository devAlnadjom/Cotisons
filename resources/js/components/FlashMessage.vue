<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

type FlashType = 'success' | 'error' | 'warning'

const page = usePage<{ flash?: Record<FlashType, string | null | undefined> }>()

const active = computed<{ type: FlashType; message: string } | null>(() => {
    const flash = page.props.flash
    if (!flash) return null

    if (flash.success) {
        return { type: 'success', message: flash.success }
    }

    if (flash.error) {
        return { type: 'error', message: flash.error }
    }

    if (flash.warning) {
        return { type: 'warning', message: flash.warning }
    }

    return null
})

const visible = ref(false)

watch(
    active,
    (value) => {
        visible.value = !!value
    },
    { immediate: true },
)

onMounted(() => {
    visible.value = !!active.value
})

const close = () => {
    visible.value = false
}

const variantClasses = computed(() => {
    if (!active.value) {
        return {
            wrapper: '',
            badge: '',
        }
    }

    switch (active.value.type) {
        case 'error':
            return {
                wrapper: 'border-red-200 bg-red-50 text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200',
                badge: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-200',
            }
        case 'warning':
            return {
                wrapper: 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200',
                badge: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-200',
            }
        default:
            return {
                wrapper: 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200',
                badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200',
            }
    }
})
</script>

<template>
    <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="-translate-y-2 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible && active"
            class="rounded-2xl border px-4 py-3 text-sm shadow-sm"
            :class="variantClasses.wrapper"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex flex-1 gap-3">
                    <span
                        class="inline-flex shrink-0 items-center justify-center rounded-full px-2.5 py-1 text-xs font-semibold uppercase tracking-wide"
                        :class="variantClasses.badge"
                    >
                        {{ active.type === 'success' ? 'Succès' : active.type === 'error' ? 'Erreur' : 'Info' }}
                    </span>
                    <p class="leading-relaxed">
                        {{ active.message }}
                    </p>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-full p-1 text-xs transition hover:bg-black/10 dark:hover:bg-white/10"
                    @click="close"
                >
                    <span class="sr-only">Fermer</span>
                    &#x2715;
                </button>
            </div>
        </div>
    </transition>
</template>
