<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import { PlusIcon, TagIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ categories: Object });

const showModal = ref(false);
const showDeleteModal = ref(false);
const editingCategory = ref(null);
const categoryToDelete = ref(null);

const form = useForm({ id: null, name: '', description: '' });
const deleteForm = useForm({});

const openCreate = () => { editingCategory.value = null; form.reset(); showModal.value = true; };
const openEdit = (c) => { editingCategory.value = c; form.id = c.id; form.name = c.name; form.description = c.description ?? ''; showModal.value = true; };
const closeModal = () => { showModal.value = false; form.reset(); };
const submit = () => {
    if (editingCategory.value) {
        form.put(route('inventory.categories.update', form.id), { onSuccess: closeModal });
    } else {
        form.post(route('inventory.categories.store'), { onSuccess: closeModal });
    }
};
const confirmDelete = (c) => { categoryToDelete.value = c; showDeleteModal.value = true; };
const deleteCategory = () => deleteForm.delete(route('inventory.categories.destroy', categoryToDelete.value.id), { onSuccess: () => { showDeleteModal.value = false; } });

const inputClass = 'w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 outline-none transition focus:border-indigo-400 focus:bg-white';
</script>

<template>
    <SidebarLayout>
        <template #title>Manajemen Kategori</template>

        <!-- Section Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Daftar Kategori Produk</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola kategori untuk mengorganisir produk</p>
            </div>
            <button @click="openCreate" class="flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                <PlusIcon class="h-4 w-4" />
                Tambah Kategori
            </button>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="c in categories.data" :key="c.id" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 flex-1">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 group-hover:bg-indigo-100 transition">
                            <TagIcon class="h-5 w-5 text-indigo-600" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ c.name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ c.products_count ?? 0 }} produk</p>
                        </div>
                    </div>
                </div>
                <p v-if="c.description" class="text-sm text-gray-600 mb-4 pb-4 border-b border-gray-50">{{ c.description }}</p>
                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                    <button @click="openEdit(c)" class="flex-1 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 transition">Edit</button>
                    <button @click="confirmDelete(c)" class="flex-1 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 transition">Hapus</button>
                </div>
            </div>

            <div v-if="!categories.data.length" class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center">
                <div class="flex flex-col items-center gap-2 text-gray-300">
                    <TagIcon class="h-10 w-10" />
                    <span class="text-sm">Belum ada kategori. Buat kategori pertama Anda sekarang!</span>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="mb-5 text-base font-semibold text-gray-900">{{ editingCategory ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input v-model="form.name" type="text" :class="inputClass" placeholder="Contoh: Elektronik, Makanan, dsb" required />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Deskripsi <span class="text-gray-400">(opsional)</span></label>
                        <textarea v-model="form.description" rows="3" :class="inputClass" placeholder="Tambahkan deskripsi untuk kategori ini" />
                    </div>
                    <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                        <button type="button" @click="closeModal" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                        <button type="submit" :disabled="form.processing" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50 transition">
                            {{ editingCategory ? 'Update Kategori' : 'Simpan Kategori' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="mb-3 text-base font-semibold text-gray-900">Hapus Kategori?</h2>
                <p class="mb-5 text-sm text-gray-600">Apakah Anda yakin ingin menghapus kategori "<strong>{{ categoryToDelete?.name }}</strong>"? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex justify-end gap-2">
                    <button @click="showDeleteModal = false" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                    <button @click="deleteCategory" :disabled="deleteForm.processing" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50 transition">Hapus</button>
                </div>
            </div>
        </Modal>
    </SidebarLayout>
</template>
