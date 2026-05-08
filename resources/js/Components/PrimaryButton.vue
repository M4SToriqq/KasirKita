<style scoped>
.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
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
    width: fit-content;
}

.btn-primary::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
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
    box-shadow: 0 1px 3px rgba(79, 70, 229, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.12);
}

.btn-indigo:hover {
    background: #4338ca;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.12);
    transform: translateY(-1px);
}

.btn-indigo:active {
    background: #3730a3;
    transform: scale(0.98);
}

/* Emerald variant */
.btn-emerald {
    background: #059669;
    box-shadow: 0 1px 3px rgba(5, 150, 105, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.12);
}

.btn-emerald:hover {
    background: #047857;
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.12);
    transform: translateY(-1px);
}

.btn-emerald:active {
    background: #065f46;
    transform: scale(0.98);
}

/* Red variant */
.btn-red {
    background: #dc2626;
    box-shadow: 0 1px 3px rgba(220, 38, 38, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.12);
}

.btn-red:hover {
    background: #b91c1c;
    box-shadow: 0 4px 14px rgba(220, 38, 38, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.12);
    transform: translateY(-1px);
}

.btn-red:active {
    background: #991b1b;
    transform: scale(0.98);
}

/* Sizes */
.btn-sm {
    padding: 6px 14px;
    font-size: 12px;
    border-radius: 6px;
}

.btn-lg {
    padding: 12px 28px;
    font-size: 14px;
    border-radius: 10px;
}

.btn-full {
    width: 100%;
}
</style>

<template>
    <button :disabled="disabled" :class="[
        'btn-primary',
        variant === 'indigo' ? 'btn-indigo' : '',
        variant === 'emerald' ? 'btn-emerald' : '',
        variant === 'red' ? 'btn-red' : '',
        size === 'sm' ? 'btn-sm' : '',
        size === 'lg' ? 'btn-lg' : '',
        block ? 'btn-full' : '',
    ]">
        <slot name="icon-left" />
        <slot />
        <slot name="icon-right" />
    </button>
</template>

<script setup>
defineProps({
    variant: { type: String, default: 'dark' },
    size: { type: String, default: 'md' },
    disabled: { type: Boolean, default: false },
    block: { type: Boolean, default: false },
})
</script>
