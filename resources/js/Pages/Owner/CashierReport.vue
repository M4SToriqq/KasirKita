<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { UserGroupIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ cashiers: Array, filters: Object });
const form = useForm({ date_from: props.filters.date_from, date_to: props.filters.date_to });
const filter = () => form.get(route('owner.cashier-report'));
const fmt = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
</script>

<template>
    <SidebarLayout>
        <template #title>Laporan Kasir</template>

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
                        <th class="px-5 py-3.5 text-left">Nama Kasir</th>
                        <th class="px-4 py-3.5 text-right">Total Transaksi</th>
                        <th class="px-5 py-3.5 text-right">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="c in cashiers" :key="c.user_id" class="hover:bg-indigo-50/40 transition">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-600">
                                    {{ c.cashier_name?.charAt(0).toUpperCase() }}
                                </div>
                                <span class="font-medium text-gray-800">{{ c.cashier_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ c.total_transactions }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">{{ fmt(c.total_sales) }}</td>
                    </tr>
                    <tr v-if="!cashiers.length">
                        <td colspan="3" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <UserGroupIcon class="h-10 w-10" />
                                <span class="text-sm">Tidak ada data</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </SidebarLayout>
</template>
