<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { UserIcon, EnvelopeIcon, LockClosedIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout :is-loading="form.processing" loading-message="Membuat Akun...">

        <Head title="Daftar" />

        <!-- Heading -->
        <div class="mb-6 text-center">
            <h1 class="text-xl font-semibold text-gray-900">Buat Akun</h1>
            <p class="mt-1 text-sm text-gray-500">Daftarkan akun baru Anda</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">

            <!-- Nama -->
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Nama</label>
                <div class="relative">
                    <UserIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="name" v-model="form.name" type="text" required autofocus autocomplete="name"
                        placeholder="Nama lengkap"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                </div>
                <InputError class="mt-1.5" :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                <div class="relative">
                    <EnvelopeIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="email" v-model="form.email" type="email" required autocomplete="username"
                        placeholder="nama@email.com"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                </div>
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <LockClosedIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" required
                        autocomplete="new-password" placeholder="••••••••"
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
                        :type="showConfirmPassword ? 'text' : 'password'" required autocomplete="new-password"
                        placeholder="••••••••"
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
                {{ form.processing ? 'Mendaftarkan...' : 'Daftar' }}
            </PrimaryButton>

            <p class="text-center text-sm text-gray-500">
                Sudah punya akun?
                <Link :href="route('login')" class="font-medium text-indigo-600 hover:text-indigo-700">
                    Masuk
                </Link>
            </p>

        </form>
    </GuestLayout>
</template>
