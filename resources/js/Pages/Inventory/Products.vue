<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import { PlusIcon, TrashIcon, CubeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ products: Object, categories: Array });

const showModal = ref(false);
const showDeleteModal = ref(false);
const editingProduct = ref(null);
const productToDelete = ref(null);

const form = useForm({
    id: null, category_id: '', sku: '', barcode: '', name: '',
    description: '', stock_quantity: 0, min_stock: 10,
    purchase_price: 0, selling_price: 0, is_active: true,
});
const deleteForm = useForm({});

const openCreate = () => { editingProduct.value = null; form.reset(); showModal.value = true; };
const openEdit = (p) => {
    editingProduct.value = p;
    Object.assign(form, { id: p.id, category_id: p.category_id, sku: p.sku, barcode: p.barcode, name: p.name, description: p.description, stock_quantity: p.stock_quantity, min_stock: p.min_stock, purchase_price: p.purchase_price, selling_price: p.selling_price, is_active: p.is_active });
    showModal.value = true;
};
const closeModal = () => { showModal.value = false; form.reset(); };
const submit = () => {
    if (editingProduct.value) {
        form.put(route('inventory.products.update', form.id), { onSuccess: closeModal });
    } else {
        form.post(route('inventory.products.store'), { onSuccess: closeModal });
    }
};
const confirmDelete = (p) => { productToDelete.value = p; showDeleteModal.value = true; };
const deleteProduct = () => deleteForm.delete(route('inventory.products.destroy', productToDelete.value.id), { onSuccess: () => { showDeleteModal.value = false; } });

const fmt = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);

const inputClass = 'w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 outline-none transition focus:border-indigo-400 focus:bg-white';
</script>

<template>
    <SidebarLayout>
        <template #title>Manajemen Produk</template>

        <!-- Section Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Daftar Produk</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola semua produk yang dijual di toko Anda</p>
            </div>
            <button @click="openCreate" class="flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                <PlusIcon class="h-4 w-4" />
                Tambah Produk
            </button>
        </div>

        <!-- Products Table -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        <th class="px-5 py-3.5 text-left">Produk</th>
                        <th class="px-4 py-3.5 text-left">Kategori</th>
                        <th class="px-4 py-3.5 text-left">SKU</th>
                        <th class="px-4 py-3.5 text-right">Stok</th>
                        <th class="px-4 py-3.5 text-right">Harga Jual</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="p in products.data" :key="p.id" class="group hover:bg-indigo-50/40 transition">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ p.name }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">{{ p.category?.name }}</span>
                        </td>
                        <td class="px-4 py-3"><span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-md">{{ p.sku }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <span :class="p.stock_quantity <= p.min_stock ? 'text-red-600 font-bold' : 'text-gray-700'">{{ p.stock_quantity }}</span>
                            <span v-if="p.stock_quantity <= p.min_stock" class="ml-1 text-xs text-red-400">⚠</span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800">{{ fmt(p.selling_price) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span :class="p.is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-gray-100 text-gray-500 ring-1 ring-gray-200'"
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                <span class="h-1.5 w-1.5 rounded-full" :class="p.is_active ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                                {{ p.is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition">
                                <button @click="openEdit(p)" class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 transition">Edit</button>
                                <button @click="confirmDelete(p)" class="rounded-lg border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold text-red-600 hover:bg-red-100 transition">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!products.data.length">
                        <td colspan="7" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <CubeIcon class="h-10 w-10" />
                                <span class="text-sm">Belum ada produk</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="flex items-center gap-1 border-t border-gray-100 px-5 py-3">
                <Link v-for="link in products.links" :key="link.label" :href="link.url ?? '#'" v-html="link.label"
                    :class="[
                        'rounded-lg px-3 py-1.5 text-xs font-medium transition',
                        link.active ? 'bg-indigo-600 text-white shadow-sm' : link.url ? 'text-gray-500 hover:bg-gray-100' : 'cursor-default text-gray-300'
                    ]" />
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="mb-5 text-base font-semibold text-gray-900">{{ editingProduct ? 'Edit Produk' : 'Tambah Produk Baru' }}</h2>
                <form @submit.prevent="submit" class="grid grid-cols-2 gap-4 space-y-0">
                    <div class="col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Produk</label>
                        <input v-model="form.name" type="text" :class="inputClass" placeholder="Contoh: Laptop Dell XPS 13" required />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Kategori</label>
                        <select v-model="form.category_id" :class="inputClass" required>
                            <option value="">Pilih Kategori</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                        <InputError :message="form.errors.category_id" class="mt-1" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">SKU / Kode Produk</label>
                        <input v-model="form.sku" type="text" :class="inputClass" placeholder="Contoh: PROD-001" required />
                        <InputError :message="form.errors.sku" class="mt-1" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Barcode <span class="text-gray-400">(opsional)</span></label>
                        <input v-model="form.barcode" type="text" :class="inputClass" placeholder="Scan atau masukkan barcode" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Stok Minimum</label>
                        <input v-model="form.min_stock" type="number" min="0" :class="inputClass" required />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Harga Beli</label>
                        <input v-model="form.purchase_price" type="number" min="0" :class="inputClass" required />
                        <InputError :message="form.errors.purchase_price" class="mt-1" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Harga Jual</label>
                        <input v-model="form.selling_price" type="number" min="0" :class="inputClass" required />
                        <InputError :message="form.errors.selling_price" class="mt-1" />
                    </div>
                    <div class="col-span-2 flex justify-end gap-2 border-t border-gray-100 pt-4">
                        <button type="button" @click="closeModal" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                        <button type="submit" :disabled="form.processing" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50 transition">
                            {{ editingProduct ? 'Update Produk' : 'Simpan Produk' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <div class="mb-4 flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                        <TrashIcon class="h-5 w-5 text-red-600" />
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900">Hapus Produk?</h2>
                        <p class="mt-1 text-sm text-gray-500">Yakin ingin menghapus <span class="font-medium text-gray-700">"{{ productToDelete?.name }}"</span>? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button @click="showDeleteModal = false" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                    <button @click="deleteProduct" :disabled="deleteForm.processing" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50 transition">Hapus Produk</button>
                </div>
            </div>
        </Modal>
    </SidebarLayout>
</template>
