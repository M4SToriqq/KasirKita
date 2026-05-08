<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 outline-none transition hover:bg-gray-50 hover:border-gray-300 focus:ring-2 focus:ring-indigo-100 disabled:opacity-40">
        <slot />
    </button>
</template>
