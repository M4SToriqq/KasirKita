<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { CubeIcon, TagIcon, ExclamationTriangleIcon, ClipboardDocumentListIcon, ChevronRightIcon, PlusIcon } from '@heroicons/vue/24/outline';

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

        <!-- Section Header -->
        <div class="mb-6 rounded-2xl border border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50 p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Ringkasan Manajemen Stok</h2>
            <p class="mt-1 text-sm text-gray-500">Monitor status stok produk dan aktivitas inventory secara real-time</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mb-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Total Produk</div>
                        <div class="text-3xl font-bold text-gray-900">{{ realtimeData.totalProducts }}</div>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50">
                        <CubeIcon class="h-6 w-6 text-indigo-600" />
                    </div>
                </div>
                <p class="text-xs text-gray-500">Jumlah produk dalam sistem</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Total Kategori</div>
                        <div class="text-3xl font-bold text-gray-900">{{ realtimeData.totalCategories }}</div>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50">
                        <TagIcon class="h-6 w-6 text-blue-600" />
                    </div>
                </div>
                <p class="text-xs text-gray-500">Kategori produk tersedia</p>
            </div>

            <div :class="realtimeData.lowStockCount > 0 ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white'" class="rounded-2xl border p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">Stok Menipis</div>
                        <div :class="realtimeData.lowStockCount > 0 ? 'text-red-600' : 'text-gray-900'" class="text-3xl font-bold">
                            {{ realtimeData.lowStockCount }}
                        </div>
                    </div>
                    <div :class="realtimeData.lowStockCount > 0 ? 'bg-red-100' : 'bg-gray-50'" class="flex h-12 w-12 items-center justify-center rounded-xl">
                        <ExclamationTriangleIcon :class="realtimeData.lowStockCount > 0 ? 'text-red-600' : 'text-gray-400'" class="h-6 w-6" />
                    </div>
                </div>
                <p :class="realtimeData.lowStockCount > 0 ? 'text-red-500' : 'text-gray-500'" class="text-xs">
                    {{ realtimeData.lowStockCount > 0 ? 'Segera lakukan stok masuk' : 'Semua stok dalam kondisi aman' }}
                </p>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div v-if="realtimeData.lowStockProducts.length > 0" class="mb-6">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="flex items-center gap-2.5 border-b border-gray-100 bg-red-50 px-6 py-4">
                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-red-100">
                        <ExclamationTriangleIcon class="h-4 w-4 text-red-600" />
                    </div>
                    <h3 class="font-semibold text-red-700 text-sm">Produk dengan Stok di Bawah Minimum</h3>
                    <span class="ml-auto rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-600">{{ realtimeData.lowStockProducts.length }} produk</span>
                </div>
                <div class="divide-y divide-gray-50">
                    <div v-for="p in realtimeData.lowStockProducts.slice(0, 5)" :key="p.id" class="flex items-center justify-between px-6 py-3 hover:bg-red-50/50 transition">
                        <div>
                            <p class="font-medium text-gray-900">{{ p.name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ p.category?.name }}</p>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-sm font-bold text-red-600">{{ p.stock_quantity }}</p>
                                <p class="text-xs text-gray-500">dari {{ p.min_stock }} minimum</p>
                            </div>
                            <Link :href="route('inventory.stock-in')" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-600 hover:bg-emerald-100 transition border border-emerald-200">
                                <PlusIcon class="h-3.5 w-3.5" />
                                Stok Masuk
                            </Link>
                        </div>
                    </div>
                </div>
                <Link v-if="realtimeData.lowStockProducts.length > 5" :href="route('inventory.low-stock')" class="flex items-center justify-center gap-2 w-full px-6 py-3 text-sm font-medium text-indigo-600 hover:bg-indigo-50 border-t border-gray-100 transition">
                    Lihat Semua <ChevronRightIcon class="h-4 w-4" />
                </Link>
            </div>
        </div>

        <!-- Recent Stock Logs -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                <h3 class="font-semibold text-gray-900">Aktivitas Stok Terakhir</h3>
            </div>
            <div class="divide-y divide-gray-50">
                <div v-for="log in realtimeData.recentStockLogs.slice(0, 8)" :key="log.id" class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-4">
                        <div :class="typeStyle[log.type]" class="flex items-center px-3 py-1.5 rounded-full text-xs font-semibold">
                            {{ log.type }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ log.product?.name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ fmtDate(log.created_at) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 text-right">
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ log.quantity }}</p>
                            <p class="text-xs text-gray-500">{{ log.stock_before }} → {{ log.stock_after }}</p>
                        </div>
                    </div>
                </div>
                <div v-if="!realtimeData.recentStockLogs.length" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2 text-gray-300">
                        <ClipboardDocumentListIcon class="h-10 w-10" />
                        <span class="text-sm">Belum ada aktivitas</span>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>
