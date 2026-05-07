<style scoped>
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 500;
        letter-spacing: 0.04em;
        color: #fff;
        background: #1a1a1a;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.18);
        outline: none;
    }

    /* Subtle top-shine overlay */
    .btn-primary::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.07) 0%, transparent 60%);
        pointer-events: none;
    }

    .btn-primary:hover {
        background: #2d2d2d;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.22);
        transform: translateY(-1px);
    }

    .btn-primary:active {
        background: #111;
        transform: scale(0.98);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.18);
    }

    .btn-primary:focus-visible {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.45);
    }

    .btn-primary:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Indigo variant */
    .btn-indigo {
        background: #4f46e5;
        box-shadow: 0 1px 2px rgba(79, 70, 229, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .btn-indigo:hover {
        background: #4338ca;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .btn-indigo:active {
        background: #3730a3;
    }

    /* Sizes */
    .btn-sm {
        padding: 6px 14px;
        font-size: 12px;
        border-radius: 6px;
    }

    .btn-lg {
        padding: 13px 28px;
        font-size: 14px;
        border-radius: 10px;
    }
</style>

<template>
    <button :disabled="disabled" :class="[
        'btn-primary',
        variant === 'indigo' ? 'btn-indigo' : '',
        size === 'sm' ? 'btn-sm' : '',
        size === 'lg' ? 'btn-lg' : '',
    ]">
        <slot name="icon-left" />
        <slot />
        <slot name="icon-right" />
    </button>
</template>

<script setup>
    defineProps({
        variant: { type: String, default: 'dark' }, // 'dark' | 'indigo'
        size: { type: String, default: 'md' },   // 'sm' | 'md' | 'lg'
        disabled: { type: Boolean, default: false },
    })
</script>
