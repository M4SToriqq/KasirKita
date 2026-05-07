<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ClipboardDocumentListIcon, CubeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ stockLogs: Object, filters: Object });
const form = useForm({ type: props.filters?.type ?? '', product_id: props.filters?.product_id ?? '' });
const filter = () => form.get(route('inventory.stock-logs'));
const fmtDate = (d) => new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

const typeStyle = {
    IN:         'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
    OUT:        'bg-red-50 text-red-600 ring-1 ring-red-200',
    ADJUSTMENT: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    VOID:       'bg-gray-100 text-gray-500 ring-1 ring-gray-200',
};
</script>

<template>
    <SidebarLayout>
        <template #title>Riwayat Stok</template>

        <!-- Filter -->
        <div class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-400">Tipe</label>
                <select v-model="form.type" class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:bg-white transition">
                    <option value="">Semua</option>
                    <option value="IN">IN</option>
                    <option value="OUT">OUT</option>
                    <option value="ADJUSTMENT">ADJUSTMENT</option>
                    <option value="VOID">VOID</option>
                </select>
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
                        <th class="px-4 py-3.5 text-center">Tipe</th>
                        <th class="px-4 py-3.5 text-right">Qty</th>
                        <th class="px-4 py-3.5 text-right">Sebelum</th>
                        <th class="px-4 py-3.5 text-right">Sesudah</th>
                        <th class="px-4 py-3.5 text-left">Oleh</th>
                        <th class="px-4 py-3.5 text-left">Catatan</th>
                        <th class="px-5 py-3.5 text-left">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="log in stockLogs.data" :key="log.id" class="hover:bg-indigo-50/40 transition">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ log.product?.name }}</td>
                        <td class="px-4 py-3 text-center">
                            <span :class="typeStyle[log.type]" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">{{ log.type }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800">{{ log.quantity }}</td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ log.stock_before }}</td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ log.stock_after }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ log.user?.name }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ log.notes }}</td>
                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ fmtDate(log.created_at) }}</td>
                    </tr>
                    <tr v-if="!stockLogs.data.length">
                        <td colspan="8" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-300">
                                <ClipboardDocumentListIcon class="h-10 w-10" />
                                <span class="text-sm">Tidak ada data</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="flex items-center gap-1 border-t border-gray-100 px-5 py-3">
                <Link v-for="link in stockLogs.links" :key="link.label" :href="link.url ?? '#'" v-html="link.label"
                    :class="[
                        'rounded-lg px-3 py-1.5 text-xs font-medium transition',
                        link.active ? 'bg-indigo-600 text-white shadow-sm' : link.url ? 'text-gray-500 hover:bg-gray-100' : 'cursor-default text-gray-300'
                    ]" />
            </div>
        </div>
    </SidebarLayout>
</template>
