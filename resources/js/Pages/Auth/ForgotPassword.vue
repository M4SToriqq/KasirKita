<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { EnvelopeIcon } from '@heroicons/vue/24/outline';

defineProps({
    status: { type: String },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout :is-loading="form.processing" loading-message="Mengirim Link...">

        <Head title="Lupa Password" />

        <div class="mb-6 text-center">
            <h1 class="text-xl font-semibold text-gray-900">Lupa Password?</h1>
            <p class="mt-1 text-sm text-gray-500">Masukkan email Anda, kami akan kirimkan link reset password.</p>
        </div>

        <div v-if="status"
            class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                <div class="relative">
                    <EnvelopeIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="email" v-model="form.email" type="email" placeholder="nama@email.com" required autofocus
                        autocomplete="username"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                </div>
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <PrimaryButton variant="indigo" size="lg" :disabled="form.processing" :block="true">
                {{ form.processing ? 'Mengirim...' : 'Kirim Link Reset Password' }}
            </PrimaryButton>
        </form>
    </GuestLayout>
</template>
