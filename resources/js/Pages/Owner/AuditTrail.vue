<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { XCircleIcon, ArrowPathIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ voidedTransactions: Array, stockAdjustments: Array, filters: Object });
const form = useForm({ date_from: props.filters.date_from, date_to: props.filters.date_to });
const filter = () => form.get(route('owner.audit-trail'));
const fmt = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
const fmtDate = (d) => new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
</script>

<template>
    <SidebarLayout>
        <template #title>Audit Trail</template>

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

        <!-- Voided Transactions -->
        <div class="mb-5 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 border-b border-gray-100 bg-red-50 px-5 py-3.5">
                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100">
                    <XCircleIcon class="h-4 w-4 text-red-600" />
                </div>
                <h3 class="font-semibold text-red-700 text-sm">Transaksi Dibatalkan (VOID)</h3>
                <span class="ml-auto rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-600">{{ voidedTransactions.length }}</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        <th class="px-5 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Kasir</th>
                        <th class="px-4 py-3 text-left">Alasan</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-5 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="t in voidedTransactions" :key="t.id" class="hover:bg-red-50/40 transition">
                        <td class="px-5 py-3"><span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-md">{{ t.invoice_number }}</span></td>
                        <td class="px-4 py-3 text-gray-600">{{ t.user?.name }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ t.notes }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ fmtDate(t.created_at) }}</td>
                        <td class="px-5 py-3 text-right font-medium text-gray-800">{{ fmt(t.total_price) }}</td>
                    </tr>
                    <tr v-if="!voidedTransactions.length">
                        <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-300">Tidak ada transaksi void</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Stock Adjustments -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 border-b border-gray-100 bg-amber-50 px-5 py-3.5">
                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-100">
                    <ArrowPathIcon class="h-4 w-4 text-amber-600" />
                </div>
                <h3 class="font-semibold text-amber-700 text-sm">Penyesuaian Stok</h3>
                <span class="ml-auto rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-600">{{ stockAdjustments.length }}</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        <th class="px-5 py-3 text-left">Produk</th>
                        <th class="px-4 py-3 text-left">Oleh</th>
                        <th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3 text-right">Sebelum</th>
                        <th class="px-4 py-3 text-right">Sesudah</th>
                        <th class="px-4 py-3 text-left">Catatan</th>
                        <th class="px-5 py-3 text-left">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="s in stockAdjustments" :key="s.id" class="hover:bg-amber-50/40 transition">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ s.product?.name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ s.user?.name }}</td>
                        <td class="px-4 py-3 text-right">
                            <span :class="s.quantity > 0 ? 'text-emerald-600' : 'text-red-500'" class="font-semibold">
                                {{ s.quantity > 0 ? '+' : '' }}{{ s.quantity }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ s.stock_before }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-800">{{ s.stock_after }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ s.notes }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ fmtDate(s.created_at) }}</td>
                    </tr>
                    <tr v-if="!stockAdjustments.length">
                        <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-300">Tidak ada penyesuaian stok</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </SidebarLayout>
</template>
