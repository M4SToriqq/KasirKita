<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LockClosedIcon, EyeIcon, EyeSlashIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const showPassword = ref(false);

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout :is-loading="form.processing" loading-message="Mengkonfirmasi Password...">

        <Head title="Konfirmasi Password" />

        <!-- Heading -->
        <div class="mb-6 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50">
                <ShieldCheckIcon class="h-6 w-6 text-indigo-600" />
            </div>
            <h1 class="text-xl font-semibold text-gray-900">Konfirmasi Password</h1>
            <p class="mt-1 text-sm text-gray-500">Ini adalah area aman. Masukkan password Anda untuk melanjutkan.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">

            <!-- Password -->
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <LockClosedIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                        placeholder="••••••••" required autofocus autocomplete="current-password"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <EyeSlashIcon v-if="showPassword" class="h-4 w-4" />
                        <EyeIcon v-else class="h-4 w-4" />
                    </button>
                </div>
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <PrimaryButton variant="indigo" size="lg" :disabled="form.processing" :block="true">
                {{ form.processing ? 'Memverifikasi...' : 'Konfirmasi' }}
            </PrimaryButton>

        </form>
    </GuestLayout>
</template>
