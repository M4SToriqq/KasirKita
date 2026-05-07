<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ExclamationTriangleIcon, CheckCircleIcon, PlusIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ products: Array });
const fmt = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
</script>

<template>
    <SidebarLayout>
        <template #title>⚠️ Stok Menipis</template>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 border-b border-gray-100 bg-red-50 px-5 py-3.5">
                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100">
                    <ExclamationTriangleIcon class="h-4 w-4 text-red-600" />
                </div>
                <h3 class="font-semibold text-red-700 text-sm">Produk dengan Stok di Bawah Minimum</h3>
                <span class="ml-auto rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-600">{{ products.length }} produk</span>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        <th class="px-5 py-3.5 text-left">Produk</th>
                        <th class="px-4 py-3.5 text-left">Kategori</th>
                        <th class="px-4 py-3.5 text-left">SKU</th>
                        <th class="px-4 py-3.5 text-right">Stok Saat Ini</th>
                        <th class="px-4 py-3.5 text-right">Stok Minimum</th>
                        <th class="px-4 py-3.5 text-right">Harga Beli</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="p in products" :key="p.id" class="hover:bg-red-50/40 transition">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ p.name }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">{{ p.category?.name }}</span>
                        </td>
                        <td class="px-4 py-3"><span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-md">{{ p.sku }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <span class="inline-flex items-center gap-1 text-red-600 font-bold">
                                <ExclamationTriangleIcon class="h-4 w-4" />
                                {{ p.stock_quantity }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ p.min_stock }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ fmt(p.purchase_price) }}</td>
                        <td class="px-5 py-3 text-right">
                            <Link :href="route('inventory.stock-in')" class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600 transition hover:bg-emerald-100">
                                <PlusIcon class="h-3 w-3" />
                                Stok Masuk
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="!products.length">
                        <td colspan="7" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-emerald-600">
                                <CheckCircleIcon class="h-10 w-10" />
                                <span class="text-sm font-medium">✓ Semua stok dalam kondisi aman</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </SidebarLayout>
</template>
