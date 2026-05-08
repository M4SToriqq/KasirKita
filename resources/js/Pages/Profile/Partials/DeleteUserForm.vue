<script setup>
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ExclamationTriangleIcon, EyeIcon, EyeSlashIcon, LockClosedIcon } from '@heroicons/vue/24/outline';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const showPassword = ref(false);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Hapus Akun</h2>
            <p class="mt-1 text-sm text-gray-500">
                Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Sebelum menghapus
                akun, silakan unduh data atau informasi yang ingin Anda simpan.
            </p>
        </header>

        <PrimaryButton variant="red" @click="confirmUserDeletion">
            <template #icon-left>
                <ExclamationTriangleIcon class="h-4 w-4" />
            </template>
            Hapus Akun
        </PrimaryButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <div class="mb-4 flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-50">
                        <ExclamationTriangleIcon class="h-5 w-5 text-red-600" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Hapus akun Anda?</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Semua data akan dihapus secara permanen. Masukkan password Anda untuk mengkonfirmasi.
                        </p>
                    </div>
                </div>

                <div class="mt-5">
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <LockClosedIcon class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input id="password" ref="passwordInput" v-model="form.password"
                            :type="showPassword ? 'text' : 'password'" placeholder="••••••••" @keyup.enter="deleteUser"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-10 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-red-400 focus:bg-white focus:ring-2 focus:ring-red-100" />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <EyeSlashIcon v-if="showPassword" class="h-4 w-4" />
                            <EyeIcon v-else class="h-4 w-4" />
                        </button>
                    </div>
                    <InputError :message="form.errors.password" class="mt-1.5" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <PrimaryButton variant="dark" @click="closeModal">
                        Batal
                    </PrimaryButton>
                    <PrimaryButton variant="red" :disabled="form.processing" @click="deleteUser">
                        {{ form.processing ? 'Menghapus...' : 'Ya, Hapus Akun' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
