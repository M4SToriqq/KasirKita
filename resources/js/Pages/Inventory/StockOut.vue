<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { ArrowUpTrayIcon, CheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ products: Array });

const form = useForm({ product_id: '', quantity: 1, notes: '' });
const submit = () => form.post(route('inventory.stock-out.store'), { onSuccess: () => form.reset() });

const inputClass = 'w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-800 outline-none transition focus:border-indigo-400 focus:bg-white';
</script>

<template>
    <SidebarLayout>
        <template #title>Stok Keluar / Penyesuaian</template>

        <div class="max-w-lg">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50">
                        <ArrowUpTrayIcon class="h-5 w-5 text-red-600" />
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Kurangi Stok</div>
                        <div class="text-xs text-gray-400">Catat pengurangan atau penyesuaian stok</div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Produk</label>
                        <select v-model="form.product_id" :class="inputClass" required>
                            <option value="">Pilih Produk</option>
                            <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} (Stok: {{ p.stock_quantity }})</option>
                        </select>
                        <InputError :message="form.errors.product_id" class="mt-1" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Jumlah</label>
                        <input v-model="form.quantity" type="number" min="1" :class="inputClass" required />
                        <InputError :message="form.errors.quantity" class="mt-1" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Catatan / Alasan <span class="text-red-500">*</span></label>
                        <textarea v-model="form.notes" rows="3" :class="inputClass" placeholder="Contoh: Barang rusak, kadaluarsa, hilang, dll" required />
                        <InputError :message="form.errors.notes" class="mt-1" />
                    </div>
                    <button type="submit" :disabled="form.processing"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 disabled:opacity-50 active:scale-[0.98]">
                        <CheckIcon class="h-4 w-4" />
                        Simpan Stok Keluar
                    </button>
                </form>
            </div>
        </div>
    </SidebarLayout>
</template>
