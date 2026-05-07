<script setup>
    import SidebarLayout from '@/Layouts/SidebarLayout.vue';
    import { ref } from 'vue';
    import { Link, useForm } from '@inertiajs/vue3';
    import Modal from '@/Components/Modal.vue';
    import InputError from '@/Components/InputError.vue';
    import { ExclamationTriangleIcon, ClipboardDocumentListIcon, XCircleIcon, BanknotesIcon, CreditCardIcon } from '@heroicons/vue/24/outline';

    const props = defineProps({ transactions: Object, filters: Object });

    const showVoidModal = ref(false);
    const selectedTransaction = ref(null);
    const voidForm = useForm({ notes: '' });

    const openVoid = (t) => { selectedTransaction.value = t; showVoidModal.value = true; };
    const submitVoid = () => voidForm.post(route('kasir.transactions.void', selectedTransaction.value.id), {
        onSuccess: () => { showVoidModal.value = false; voidForm.reset(); }
    });

    const fmt = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
    const fmtDate = (d) => new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
</script>

<template>
    <SidebarLayout>
        <template #title>Riwayat Transaksi</template>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-400">
                            <th class="px-5 py-3.5 text-left">Invoice</th>
                            <th class="px-4 py-3.5 text-left">Metode</th>
                            <th class="px-4 py-3.5 text-left">Status</th>
                            <th class="px-4 py-3.5 text-left">Waktu</th>
                            <th class="px-4 py-3.5 text-right">Total</th>
                            <th class="px-4 py-3.5 text-right">Kembalian</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="t in transactions.data" :key="t.id" class="group transition hover:bg-indigo-50/40">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded-md">{{ t.invoice_number }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span :class="t.payment_method === 'CASH' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-blue-50 text-blue-700 ring-1 ring-blue-200'" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                    <BanknotesIcon v-if="t.payment_method === 'CASH'" class="h-3.5 w-3.5" />
                                    <CreditCardIcon v-else class="h-3.5 w-3.5" />
                                    {{ t.payment_method }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span :class="t.status === 'COMPLETED' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-red-50 text-red-600 ring-1 ring-red-200'" class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                    <span class="h-1.5 w-1.5 rounded-full" :class="t.status === 'COMPLETED' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                    {{ t.status === 'COMPLETED' ? 'Selesai' : 'Dibatalkan' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-500">{{ fmtDate(t.created_at) }}</td>
                            <td class="px-4 py-3.5 text-right font-semibold text-gray-800">{{ fmt(t.total_price) }}</td>
                            <td class="px-4 py-3.5 text-right text-gray-500">{{ fmt(t.change) }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <div v-if="t.status === 'COMPLETED'" class="relative group/tooltip">
                                    <button @click="openVoid(t)" class="flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 opacity-0 transition group-hover:opacity-100 hover:bg-red-100 hover:border-red-300">
                                        <XCircleIcon class="h-3.5 w-3.5" />
                                        Void
                                    </button>
                                    <!-- Tooltip -->
                                    <div class="pointer-events-none absolute right-0 top-full mt-2 w-48 rounded-lg border border-gray-200 bg-white p-2 text-xs text-gray-600 shadow-lg opacity-0 transition-opacity group-hover/tooltip:opacity-100 z-10">
                                        <p class="font-semibold text-gray-800 mb-1">Batalkan Transaksi</p>
                                        <p class="text-[11px] leading-relaxed">Gunakan jika ada kesalahan atau customer membatalkan pesanan. Stok akan dikembalikan.</p>
                                    </div>
                                </div>
                                <span v-else class="text-xs text-gray-400 italic">Dibatalkan</span>
                            </td>
                        </tr>
                        <tr v-if="!transactions.data.length">
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-300">
                                    <ClipboardDocumentListIcon class="h-10 w-10" />
                                    <span class="text-sm">Belum ada transaksi</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="flex items-center gap-1 border-t border-gray-100 px-5 py-3">
                <Link v-for="link in transactions.links" :key="link.label" :href="link.url ?? '#'" v-html="link.label" :class="['rounded-lg px-3 py-1.5 text-xs font-medium transition', link.active ? 'bg-indigo-600 text-white shadow-sm' : link.url ? 'text-gray-500 hover:bg-gray-100' : 'cursor-default text-gray-300']"/>
            </div>
        </div>

        <!-- Void Modal -->
        <Modal :show="showVoidModal" @close="showVoidModal = false">
            <div class="p-6">
                <div class="mb-5 flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100">
                        <ExclamationTriangleIcon class="h-6 w-6 text-red-600" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-gray-900">Batalkan Transaksi (Void)</h2>
                        <p class="mt-1 text-sm text-gray-600">Anda akan membatalkan transaksi yang sudah selesai</p>
                    </div>
                </div>

                <!-- Transaction Info -->
                <div class="mb-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Detail Transaksi</span>
                        <span class="font-mono text-xs bg-white text-gray-700 px-2 py-1 rounded border border-gray-200">{{ selectedTransaction?.invoice_number }}</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Pembayaran</span>
                            <span class="font-semibold text-gray-900">{{ fmt(selectedTransaction?.total_price) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Metode Pembayaran</span>
                            <span class="font-medium text-gray-700">{{ selectedTransaction?.payment_method }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Waktu Transaksi</span>
                            <span class="text-gray-700">{{ fmtDate(selectedTransaction?.created_at) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Warning Box -->
                <div class="mb-5 rounded-xl border-2 border-amber-200 bg-amber-50 p-4">
                    <div class="flex gap-3">
                        <svg class="h-5 w-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-amber-900">Yang Akan Terjadi:</h3>
                            <ul class="mt-2 space-y-1 text-xs text-amber-800">
                                <li class="flex items-start gap-2">
                                    <span class="mt-0.5 text-amber-600">•</span>
                                    <span>Stok produk akan <strong>dikembalikan</strong> ke inventory</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="mt-0.5 text-amber-600">•</span>
                                    <span>Status transaksi berubah menjadi <strong>DIBATALKAN</strong></span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="mt-0.5 text-amber-600">•</span>
                                    <span>Owner dapat melihat pembatalan ini di <strong>Audit Trail</strong></span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="mt-0.5 text-amber-600">•</span>
                                    <span>Tindakan ini <strong>TIDAK DAPAT DIBATALKAN</strong></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Reason Input -->
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Alasan Pembatalan <span class="text-red-500">*</span></label>
                    <textarea v-model="voidForm.notes" rows="3" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-100" placeholder="Contoh: Customer membatalkan pesanan / Salah input produk / Kesalahan pembayaran" required></textarea>
                    <InputError :message="voidForm.errors.notes" class="mt-1" />
                    <p class="mt-1.5 text-xs text-gray-500">Alasan ini akan dicatat untuk audit dan pelaporan</p>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end gap-3">
                    <button @click="showVoidModal = false" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</button>
                    <button @click="submitVoid" :disabled="voidForm.processing || !voidForm.notes" class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-200 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none">
                        <span v-if="voidForm.processing" class="flex items-center gap-2">
                            <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                            Memproses...
                        </span>
                        <span v-else class="flex items-center gap-2">
                            <ExclamationTriangleIcon class="h-4 w-4" />
                            Ya, Batalkan Transaksi
                        </span>
                    </button>
                </div>
            </div>
        </Modal>
    </SidebarLayout>
</template>
