<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { CurrencyDollarIcon, ClipboardDocumentListIcon, ArrowTrendingUpIcon, CubeIcon, StarIcon, ClockIcon, ChevronRightIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    summary: Object,
    salesByPayment: Array,
    todaySales: Number,
    todayTransactions: Number,
    lowStockProducts: Number,
    recentTransactions: Array,
    topProducts: Array,
    filters: Object,
});

const realtimeData = ref({
    summary: props.summary,
    todaySales: props.todaySales,
    todayTransactions: props.todayTransactions,
    lowStockProducts: props.lowStockProducts,
    recentTransactions: props.recentTransactions,
});

let pollingInterval = null;

const fetchRealtimeData = async () => {
    try {
        const res = await fetch(route('owner.realtime-data', { date_from: props.filters.dateFrom, date_to: props.filters.dateTo }));
        const data = await res.json();
        realtimeData.value = data;
    } catch (e) {
        console.error('Realtime fetch error:', e);
    }
};

onMounted(() => {
    pollingInterval = setInterval(fetchRealtimeData, 5000);
});

onUnmounted(() => {
    if (pollingInterval) clearInterval(pollingInterval);
});

const fmt = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);

const exportSales = () => {
    window.location.href = route('owner.export-sales', { date_from: props.filters.dateFrom, date_to: props.filters.dateTo });
};
</script>

<template>
    <SidebarLayout>
        <template #title>
            <div class="flex items-center justify-between">
                <span>Dashboard Owner</span>
                <button @click="exportSales" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    <ArrowDownTrayIcon class="h-4 w-4" />
                    Export CSV
                </button>
            </div>
        </template>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 mb-5">
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Penjualan</div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                        <CurrencyDollarIcon class="h-5 w-5 text-indigo-600" />
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ fmt(realtimeData.summary.totalSales) }}</div>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Transaksi</div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50">
                        <ClipboardDocumentListIcon class="h-5 w-5 text-blue-600" />
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ realtimeData.summary.totalTransactions }}</div>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Profit</div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50">
                        <ArrowTrendingUpIcon class="h-5 w-5 text-emerald-600" />
                    </div>
                </div>
                <div class="text-2xl font-bold text-emerald-600">{{ fmt(realtimeData.summary.totalProfit) }}</div>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Item Terjual</div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50">
                        <CubeIcon class="h-5 w-5 text-amber-600" />
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ realtimeData.summary.totalItemsSold }}</div>
            </div>
        </div>

        <!-- Today & Low Stock -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 mb-5">
            <div class="rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-5 text-white shadow-lg shadow-indigo-200">
                <div class="text-xs font-semibold uppercase tracking-wide text-indigo-200 mb-3">Penjualan Hari Ini</div>
                <div class="text-2xl font-bold">{{ fmt(realtimeData.todaySales) }}</div>
                <div class="text-sm text-indigo-200 mt-1">{{ realtimeData.todayTransactions }} transaksi</div>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">Penjualan per Metode</div>
                <div v-for="item in salesByPayment" :key="item.payment_method" class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                    <span class="text-sm text-gray-600 flex items-center gap-1.5">
                        <span>{{ item.payment_method === 'CASH' ? '💵' : '📱' }}</span>
                        {{ item.payment_method }}
                    </span>
                    <span class="text-sm font-semibold text-gray-800">{{ fmt(item.total) }}</span>
                </div>
            </div>
            <div :class="realtimeData.lowStockProducts > 0 ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white'" class="rounded-2xl border p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">Stok Menipis</div>
                <div :class="realtimeData.lowStockProducts > 0 ? 'text-red-600' : 'text-gray-900'" class="text-2xl font-bold">{{ realtimeData.lowStockProducts }} produk</div>
                <Link :href="route('owner.audit-trail')" class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-700">
                    Lihat detail <ChevronRightIcon class="h-3 w-3" />
                </Link>
            </div>
        </div>

        <!-- Top Products & Recent Transactions -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 border-b border-gray-100 px-5 py-3.5">
                    <StarIcon class="h-5 w-5 text-amber-500" />
                    <h3 class="font-semibold text-gray-800 text-sm">Produk Terlaris</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            <th class="px-5 py-3 text-left">Produk</th>
                            <th class="px-4 py-3 text-right">Terjual</th>
                            <th class="px-5 py-3 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="p in topProducts" :key="p.product_id" class="hover:bg-indigo-50/40 transition">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ p.product_name }}</td>
                            <td class="px-4 py-3 text-right text-gray-500">{{ p.total_sold }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800">{{ fmt(p.total_revenue) }}</td>
                        </tr>
                        <tr v-if="!topProducts?.length">
                            <td colspan="3" class="px-5 py-8 text-center text-gray-300 text-sm">Belum ada data</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 border-b border-gray-100 px-5 py-3.5">
                    <ClockIcon class="h-5 w-5 text-indigo-500" />
                    <h3 class="font-semibold text-gray-800 text-sm">Transaksi Terbaru</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            <th class="px-5 py-3 text-left">Invoice</th>
                            <th class="px-4 py-3 text-left">Kasir</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="t in realtimeData.recentTransactions" :key="t.id" class="hover:bg-indigo-50/40 transition">
                            <td class="px-5 py-3"><span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-md">{{ t.invoice_number }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ t.user?.name }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-800">{{ fmt(t.total_price) }}</td>
                        </tr>
                        <tr v-if="!recentTransactions?.length">
                            <td colspan="3" class="px-5 py-8 text-center text-gray-300 text-sm">Belum ada transaksi</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </SidebarLayout>
</template>
