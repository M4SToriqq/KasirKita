<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link :href="href"
        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 outline-none transition hover:bg-gray-50 hover:text-gray-900 focus:bg-gray-50">
        <slot />
    </Link>
</template>
