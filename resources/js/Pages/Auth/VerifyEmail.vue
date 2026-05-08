<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EnvelopeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    status: { type: String },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout :is-loading="form.processing" loading-message="Mengirim Email...">

        <Head title="Verifikasi Email" />

        <!-- Heading -->
        <div class="mb-6 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50">
                <EnvelopeIcon class="h-6 w-6 text-indigo-600" />
            </div>
            <h1 class="text-xl font-semibold text-gray-900">Verifikasi Email</h1>
            <p class="mt-1 text-sm text-gray-500">
                Terima kasih telah mendaftar! Silakan verifikasi email Anda dengan mengklik link yang telah kami
                kirimkan. Jika tidak menerima email, kami akan mengirimkan yang baru.
            </p>
        </div>

        <!-- Success -->
        <div v-if="verificationLinkSent"
            class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
            Link verifikasi baru telah dikirim ke alamat email Anda.
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <PrimaryButton variant="indigo" size="lg" :disabled="form.processing" :block="true">
                {{ form.processing ? 'Mengirim...' : 'Kirim Ulang Email Verifikasi' }}
            </PrimaryButton>

            <div class="text-center">
                <Link :href="route('logout')" method="post" as="button"
                    class="text-sm text-gray-500 hover:text-gray-700 underline outline-none transition">
                    Keluar
                </Link>
            </div>
        </form>

    </GuestLayout>
</template>
