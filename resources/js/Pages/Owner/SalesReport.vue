<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ClipboardDocumentListIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ transactions: Array, totalRevenue: Number, totalProfit: Number, filters: Object });
const form = useForm({ date_from: props.filters.date_from, date_to: props.filters.date_to });
const filter = () => form.get(route('owner.sales-report'));
const fmt = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
const fmtDate = (d) => new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
</script>

<template>
    <SidebarLayout>
        <template #title>Laporan Penjualan</template>

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

        <!-- Summary -->
        <div class="mb-5 grid grid-cols-2 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Total Revenue</div>
                <div class="text-2xl font-bold text-gray-900">{{ fmt(totalRevenue) }}</div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Total Profit</div>
                <div class="text-2xl font-bold text-emerald-600">{{ fmt(totalProfit) }}</div>
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        <th class="px-5 py-3.5 text-left">Invoice</th>
                        <th class="px-4 py-3.5 text-left">Kasir</th>
                        <th class="px-4 py-3.5 text-left">Metode</th>
                        <th class="px-4 py-3.5 text-left">Waktu</th>
                        <th class="px-5 py-3.5 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="t in transactions" :key="t.id" class="hover:bg-indigo-50/40 transition">
                        <td class="px-5 py-3"><span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-md">{{ t.invoice_number }}</span></td>
                        <td class="px-4 py-3 text-gray-600">{{ t.user?.name }}</td>
                        <td class="px-4 py-3">
                            <span :class="t.payment_method === 'CASH' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-blue-50 text-blue-700 ring-1 ring-blue-200'"
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                {{ t.payment_method === 'CASH' ? '💵' : '📱' }} {{ t.payment_method }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ fmtDate(t.created_at) }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">{{ fmt(t.total_price) }}</td>
                    </tr>
                    <tr v-if="!transactions.length">
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <ClipboardDocumentListIcon class="h-10 w-10" />
                                <span class="text-sm">Tidak ada data</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </SidebarLayout>
</template>
