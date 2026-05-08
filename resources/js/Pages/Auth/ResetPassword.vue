<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { EnvelopeIcon, LockClosedIcon, EyeIcon, EyeSlashIcon, KeyIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout :is-loading="form.processing" loading-message="Mereset Password...">

        <Head title="Reset Password" />

        <!-- Heading -->
        <div class="mb-6 text-center">
            <h1 class="text-xl font-semibold text-gray-900">Reset Password</h1>
            <p class="mt-1 text-sm text-gray-500">Buat password baru untuk akun Anda.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">

            <!-- Email -->
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                <div class="relative">
                    <EnvelopeIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="email" v-model="form.email" type="email" required autofocus autocomplete="username"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                </div>
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <!-- Password Baru -->
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password Baru</label>
                <div class="relative">
                    <LockClosedIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                        placeholder="••••••••" required autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <EyeSlashIcon v-if="showPassword" class="h-4 w-4" />
                        <EyeIcon v-else class="h-4 w-4" />
                    </button>
                </div>
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">Konfirmasi
                    Password</label>
                <div class="relative">
                    <LockClosedIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="password_confirmation" v-model="form.password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'" placeholder="••••••••" required
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                    <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <EyeSlashIcon v-if="showConfirmPassword" class="h-4 w-4" />
                        <EyeIcon v-else class="h-4 w-4" />
                    </button>
                </div>
                <InputError class="mt-1.5" :message="form.errors.password_confirmation" />
            </div>

            <PrimaryButton variant="indigo" size="lg" :disabled="form.processing" :block="true">
                {{ form.processing ? 'Memproses...' : 'Reset Password' }}
            </PrimaryButton>

        </form>
    </GuestLayout>
</template>
