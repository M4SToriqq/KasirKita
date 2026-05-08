<script setup>
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { UserIcon, EnvelopeIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Informasi Profil</h2>
            <p class="mt-1 text-sm text-gray-500">Perbarui informasi profil dan alamat email akun Anda.</p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-5">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Nama</label>
                <div class="relative">
                    <UserIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="name" v-model="form.name" type="text" required autofocus autocomplete="name"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                </div>
                <InputError class="mt-1.5" :message="form.errors.name" />
            </div>
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                <div class="relative">
                    <EnvelopeIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="email" v-model="form.email" type="email" required autocomplete="username"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                </div>
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>
            <div v-if="mustVerifyEmail && user.email_verified_at === null"
                class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700 ring-1 ring-amber-200">
                Email Anda belum diverifikasi.
                <Link :href="route('verification.send')" method="post" as="button"
                    class="ml-1 font-medium underline hover:text-amber-900">
                    Kirim ulang email verifikasi.
                </Link>
                <div v-show="status === 'verification-link-sent'" class="mt-2 font-medium text-green-600">
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-1">
                <PrimaryButton variant="indigo" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </PrimaryButton>
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="flex items-center gap-1 text-sm text-emerald-600">
                        <CheckCircleIcon class="h-4 w-4" />
                        Tersimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
