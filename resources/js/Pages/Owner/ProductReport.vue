<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { CubeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ products: Array, filters: Object });
const form = useForm({ date_from: props.filters.date_from, date_to: props.filters.date_to });
const filter = () => form.get(route('owner.product-report'));
const fmt = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
</script>

<template>
    <SidebarLayout>
        <template #title>Laporan Produk</template>

        <!-- Filter -->
        <div class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-400">Dari</label>
                <input v-model="form.date_from" type="date" class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:bg-white transition" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-400">Sampai</label>
                <input v-model="form.date_to" type="date" class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:bg-white transition" />
            </div>
            <button @click="filter" class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                Filter
            </button>
        </div>

        <!-- Table -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        <th class="px-5 py-3.5 text-left">Produk</th>
                        <th class="px-4 py-3.5 text-left">Kategori</th>
                        <th class="px-4 py-3.5 text-right">Stok</th>
                        <th class="px-4 py-3.5 text-right">Terjual</th>
                        <th class="px-4 py-3.5 text-right">Revenue</th>
                        <th class="px-5 py-3.5 text-right">Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="p in products" :key="p.id" class="hover:bg-indigo-50/40 transition">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ p.name }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">{{ p.category?.name }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span :class="p.stock_quantity <= p.min_stock ? 'text-red-600 font-bold' : 'text-gray-700'">{{ p.stock_quantity }}</span>
                            <span v-if="p.stock_quantity <= p.min_stock" class="ml-1 text-xs text-red-400">⚠</span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ p.sold_qty }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800">{{ fmt(p.revenue) }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-emerald-600">{{ fmt(p.profit) }}</td>
                    </tr>
                    <tr v-if="!products.length">
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <CubeIcon class="h-10 w-10" />
                                <span class="text-sm">Tidak ada data</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </SidebarLayout>
</template>
