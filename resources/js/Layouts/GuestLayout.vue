<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    isLoading: {
        type: Boolean,
        default: false,
    },
    loadingMessage: {
        type: String,
        default: 'Memproses...',
    },
});
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 px-4 py-6">
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm ring-1 ring-gray-200">
            <div class="mb-6 flex flex-col items-center">
                <Link href="/">
                    <ApplicationLogo class="h-16 w-16" />
                </Link>
            </div>
            <slot />
        </div>
    </div>
</template>
