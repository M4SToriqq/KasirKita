<script setup>
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon, LockClosedIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const showCurrent = ref(false);
const showNew = ref(false);
const showConfirm = ref(false);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Perbarui Password</h2>
            <p class="mt-1 text-sm text-gray-500">Pastikan akun Anda menggunakan password yang panjang dan acak untuk
                tetap aman.</p>
        </header>

        <form @submit.prevent="updatePassword" class="space-y-5">

            <!-- Password Saat Ini -->
            <div>
                <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700">Password Saat
                    Ini</label>
                <div class="relative">
                    <LockClosedIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="current_password" ref="currentPasswordInput" v-model="form.current_password"
                        :type="showCurrent ? 'text' : 'password'" autocomplete="current-password" placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                    <button type="button" @click="showCurrent = !showCurrent"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <EyeSlashIcon v-if="showCurrent" class="h-4 w-4" />
                        <EyeIcon v-else class="h-4 w-4" />
                    </button>
                </div>
                <InputError :message="form.errors.current_password" class="mt-1.5" />
            </div>

            <!-- Password Baru -->
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password Baru</label>
                <div class="relative">
                    <LockClosedIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="password" ref="passwordInput" v-model="form.password"
                        :type="showNew ? 'text' : 'password'" autocomplete="new-password" placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                    <button type="button" @click="showNew = !showNew"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <EyeSlashIcon v-if="showNew" class="h-4 w-4" />
                        <EyeIcon v-else class="h-4 w-4" />
                    </button>
                </div>
                <InputError :message="form.errors.password" class="mt-1.5" />
            </div>
            <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">Konfirmasi
                    Password</label>
                <div class="relative">
                    <LockClosedIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input id="password_confirmation" v-model="form.password_confirmation"
                        :type="showConfirm ? 'text' : 'password'" autocomplete="new-password" placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100" />
                    <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <EyeSlashIcon v-if="showConfirm" class="h-4 w-4" />
                        <EyeIcon v-else class="h-4 w-4" />
                    </button>
                </div>
                <InputError :message="form.errors.password_confirmation" class="mt-1.5" />
            </div>

            <div class="flex items-center gap-4 pt-1">
                <PrimaryButton variant="indigo" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Password' }}
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
