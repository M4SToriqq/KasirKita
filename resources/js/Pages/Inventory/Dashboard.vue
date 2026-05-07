<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { CubeIcon, TagIcon, ExclamationTriangleIcon, ClipboardDocumentListIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    totalProducts: Number,
    totalCategories: Number,
    lowStockCount: Number,
    lowStockProducts: Array,
    recentStockLogs: Array,
});

const realtimeData = ref({
    totalProducts: props.totalProducts,
    totalCategories: props.totalCategories,
    lowStockCount: props.lowStockCount,
    lowStockProducts: props.lowStockProducts,
    recentStockLogs: props.recentStockLogs,
});

let pollingInterval = null;

const fetchRealtimeData = async () => {
    try {
        const res = await fetch(route('inventory.realtime-data'));
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

const fmtDate = (d) => new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });

const typeStyle = {
    IN:         'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    OUT:        'bg-red-50 text-red-600 ring-1 ring-red-200',
    ADJUSTMENT: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    VOID:       'bg-gray-100 text-gray-500 ring-1 ring-gray-200',
};
</script>

<template>
    <SidebarLayout>
        <template #title>Dashboard Inventory</template>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Produk</div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                        <CubeIcon class="h-5 w-5 text-indigo-600" />
                    </div>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ realtimeData.totalProducts }}</div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total Kategori</div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50">
                        <TagIcon class="h-5 w-5 text-blue-600" />
                    </div>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ realtimeData.totalCategories }}</div>
            </div>
            <div :class="realtimeData.lowStockCount > 0 ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white'" class="rounded-2xl border p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Stok Menipis</div>
                    <div :class="realtimeData.lowStockCount > 0 ? 'bg-red-100' : 'bg-gray-50'" class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <ExclamationTriangleIcon class="h-5 w-5" :class="realtimeData.lowStockCount > 0 ? 'text-red-600' : 'text-gray-400'" />
                    </div>
                </div>
                <div :class="realtimeData.lowStockCount > 0 ? 'text-red-600' : 'text-gray-900'" class="text-3xl font-bold">{{ realtimeData.lowStockCount }}</div>
                <Link :href="route('inventory.low-stock')" class="mt-1 inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-700">
                    Lihat semua <ChevronRightIcon class="h-3 w-3" />
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <!-- Low Stock Alert -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-3.5">
                    <div class="flex items-center gap-2">
                        <ExclamationTriangleIcon class="h-5 w-5 text-red-500" />
                        <h3 class="font-semibold text-gray-800 text-sm">Stok Menipis</h3>
                    </div>
                    <Link :href="route('inventory.low-stock')" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Lihat semua</Link>
                </div>
                <div class="divide-y divide-gray-50">
                    <div v-for="p in realtimeData.lowStockProducts" :key="p.id" class="flex items-center justify-between px-5 py-3 hover:bg-red-50/40 transition">
                        <div>
                            <div class="font-medium text-gray-800 text-sm">{{ p.name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ p.category?.name }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-red-600 font-bold text-sm">{{ p.stock_quantity }}</span>
                            <span class="text-gray-400 text-xs"> / min {{ p.min_stock }}</span>
                        </div>
                    </div>
                    <div v-if="!realtimeData.lowStockProducts.length" class="px-5 py-8 text-center text-sm text-emerald-600 font-medium">
                        ✓ Semua stok aman
                    </div>
                </div>
            </div>

            <!-- Recent Stock Logs -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-3.5">
                    <div class="flex items-center gap-2">
                        <ClipboardDocumentListIcon class="h-5 w-5 text-indigo-500" />
                        <h3 class="font-semibold text-gray-800 text-sm">Aktivitas Stok Terbaru</h3>
                    </div>
                    <Link :href="route('inventory.stock-logs')" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Lihat semua</Link>
                </div>
                <div class="divide-y divide-gray-50">
                    <div v-for="log in realtimeData.recentStockLogs" :key="log.id" class="flex items-center justify-between px-5 py-3 hover:bg-indigo-50/40 transition">
                        <div>
                            <div class="font-medium text-gray-800 text-sm">{{ log.product?.name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ fmtDate(log.created_at) }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span :class="typeStyle[log.type]" class="rounded-full px-2.5 py-0.5 text-xs font-semibold">{{ log.type }}</span>
                            <span class="font-bold text-sm text-gray-800">{{ log.quantity }}</span>
                        </div>
                    </div>
                    <div v-if="!realtimeData.recentStockLogs.length" class="px-5 py-8 text-center text-sm text-gray-300">
                        Belum ada aktivitas
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>
