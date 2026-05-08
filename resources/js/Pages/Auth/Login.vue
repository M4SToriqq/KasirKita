<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon, EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout :is-loading="form.processing" loading-message="Memproses Login...">

        <Head title="Masuk" />

        <!-- Logo & heading -->
        <div class="mb-8 text-center">
            <h1 class="text-xl font-semibold text-gray-900">Selamat datang</h1>
            <p class="mt-1 text-sm text-gray-500">Masuk ke akun POS Anda</p>
        </div>

        <div v-if="status"
            class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Email -->
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

            <!-- Password -->
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <LockClosedIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                        placeholder="••••••••" required autocomplete="current-password"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <EyeSlashIcon v-if="showPassword" class="h-4 w-4" />
                        <EyeIcon v-else class="h-4 w-4" />
                    </button>
                </div>
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <!-- Remember & Forgot -->
            <div class="flex items-center justify-between">
                <label class="flex cursor-pointer items-center gap-2">
                    <input type="checkbox" v-model="form.remember" class="h-4 w-4 rounded accent-indigo-600" />
                    <span class="text-sm text-gray-600">Ingat saya</span>
                </label>
                <Link v-if="canResetPassword" :href="route('password.request')"
                    class="text-sm text-indigo-600 hover:text-indigo-700">
                    Lupa password?
                </Link>
            </div>

            <!-- Submit -->
            <PrimaryButton variant="indigo" size="lg" :disabled="form.processing" :block="true">
                {{ form.processing ? 'Memproses...' : 'Masuk' }}
            </PrimaryButton>
        </form>
    </GuestLayout>
</template>
