<script setup>
    import SidebarLayout from '@/Layouts/SidebarLayout.vue';
    import { ref, computed, onMounted } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import { MagnifyingGlassIcon, ShoppingCartIcon, CheckCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline';

    const props = defineProps({ categories: Array });

    const searchQuery = ref('');
    const searchResults = ref([]);
    const cart = ref([]);
    const isSearching = ref(false);
    const selectedPaymentMethod = ref('CASH');
    const amountPaid = ref(0);
    const showReceipt = ref(false);
    const lastTransaction = ref(null);
    const selectedCategory = ref(null);

    const filteredProducts = computed(() => {
        if (!selectedCategory.value) return [];
        const cat = props.categories.find(c => c.id === selectedCategory.value);
        return cat?.products || [];
    });

    onMounted(() => {
        if (props.categories?.length > 0) {
            selectedCategory.value = props.categories[0].id;
        }
    });

    const searchProduct = async () => {
        if (searchQuery.value.length < 2) { searchResults.value = []; return; }
        isSearching.value = true;
        try {
            const res = await fetch(route('kasir.search-product', { q: searchQuery.value }));
            const results = await res.json();
            searchResults.value = results;
            
            // Auto-add jika hasil search cuma 1 produk (untuk barcode scanner)
            if (results.length === 1 && results[0].stock_quantity > 0) {
                setTimeout(() => addToCart(results[0]), 100);
            }
        } catch (e) { console.error(e); }
        finally { isSearching.value = false; }
    };

    const addToCart = (product) => {
        const existing = cart.value.find(i => i.product_id === product.id);
        if (existing) { if (existing.quantity < product.stock_quantity) existing.quantity++; }
        else if (product.stock_quantity > 0) {
            cart.value.push({ product_id: product.id, name: product.name, price: product.selling_price, quantity: 1, stock: product.stock_quantity });
        }
        searchQuery.value = '';
        searchResults.value = [];
    };

    const removeFromCart = (i) => cart.value.splice(i, 1);
    const updateQty = (i, d) => {
        const item = cart.value[i];
        const n = item.quantity + d;
        if (n >= 1 && n <= item.stock) item.quantity = n;
    };

    const subtotal = computed(() => cart.value.reduce((s, i) => s + i.price * i.quantity, 0));
    const change = computed(() => Math.max(0, amountPaid.value - subtotal.value));
    const canSubmit = computed(() => {
        if (selectedPaymentMethod.value === 'MIDTRANS') return cart.value.length > 0;
        return cart.value.length > 0 && amountPaid.value >= subtotal.value;
    });

    const submitTransaction = () => {
        if (!canSubmit.value) return;

        if (selectedPaymentMethod.value === 'MIDTRANS') {
            processMidtrans();
        } else {
            const form = useForm({
                items: cart.value,
                total_price: subtotal.value,
                total_paid: amountPaid.value,
                change: change.value,
                payment_method: selectedPaymentMethod.value,
            });
            form.post(route('kasir.transactions.store'), {
                onSuccess: (page) => {
                    lastTransaction.value = page.props.receipt;
                    showReceipt.value = true;
                    cart.value = [];
                    amountPaid.value = 0;
                },
            });
        }
    };

    const processMidtrans = async () => {
        try {
            const res = await fetch(route('kasir.midtrans.token'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ items: cart.value, total_price: subtotal.value }),
            });

            if (!res.ok) {
                const errorData = await res.json();
                alert('Error: ' + (errorData.error || 'Gagal membuat token pembayaran'));
                console.error('Midtrans error:', errorData);
                return;
            }

            const data = await res.json();

            if (!data.snap_token) {
                alert('Error: Token pembayaran tidak ditemukan. Pastikan kredensial Midtrans sudah benar.');
                console.error('No snap_token in response:', data);
                return;
            }

            window.snap.pay(data.snap_token, {
                onSuccess: async (result) => {
                    const storeRes = await fetch(route('kasir.midtrans.store'), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ items: cart.value, total_price: subtotal.value, invoice_number: data.invoice_number }),
                    });
                    const storeData = await storeRes.json();
                    lastTransaction.value = storeData.transaction;
                    showReceipt.value = true;
                    cart.value = [];
                    amountPaid.value = 0;
                },
                onPending: (result) => { console.log('pending', result); },
                onError: (result) => { alert('Pembayaran gagal'); },
                onClose: () => { console.log('closed'); },
            });
        } catch (e) {
            console.error('Error:', e);
            alert('Terjadi kesalahan: ' + e.message);
        }
    };

    const fmt = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
    const quickAmounts = [10000, 20000, 50000, 100000];
    const printReceipt = () => window.print();
</script>

<template>
    <SidebarLayout>
        <template #title>Kasir / POS</template>

        <div class="flex h-[calc(100vh-8rem)] gap-5">
            <!-- Left: Product Grid & Search -->
            <div class="flex w-2/3 flex-col gap-4">
                <!-- Search Bar -->
                <div class="relative">
                    <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm ring-1 ring-transparent focus-within:border-indigo-400 focus-within:ring-indigo-100 transition-all">
                        <MagnifyingGlassIcon class="h-5 w-5 shrink-0 text-gray-400" />
                        <input v-model="searchQuery" type="text" placeholder="Cari produk atau scan barcode..." class="w-full bg-transparent text-base text-gray-800 placeholder-gray-400 outline-none" autocomplete="off" @input="searchProduct"/>
                        <span v-if="isSearching" class="h-4 w-4 animate-spin rounded-full border-2 border-indigo-500 border-t-transparent shrink-0"></span>
                    </div>
                    <!-- Search Results Dropdown -->
                    <div v-if="searchQuery.length >= 2 && !isSearching" class="absolute left-0 right-0 z-20 mt-2 rounded-xl border border-gray-100 bg-white shadow-xl">
                        <div v-if="searchResults.length === 0" class="flex flex-col items-center justify-center py-8 px-4 text-gray-400">
                            <svg class="h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-medium">Produk tidak ditemukan</p>
                            <p class="text-xs mt-1">Coba kata kunci lain atau scan barcode</p>
                        </div>
                        <button v-else v-for="p in searchResults" :key="p.id" @click="addToCart(p)" :disabled="p.stock_quantity === 0" class="flex w-full items-center justify-between px-4 py-3 text-left transition hover:bg-indigo-50 disabled:opacity-40 border-b border-gray-50 last:border-0">
                            <div>
                                <div class="font-semibold text-gray-800">{{ p.name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">SKU: {{ p.sku }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-indigo-600">{{ fmt(p.selling_price) }}</div>
                                <div :class="p.stock_quantity > 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-500 bg-red-50'" class="mt-0.5 inline-block rounded-full px-2 py-0.5 text-xs font-medium">
                                    Stok: {{ p.stock_quantity }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-1 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm flex flex-col">
                    <!-- Category Tabs -->
                    <div class="border-b border-gray-100 px-4 py-2.5 overflow-x-auto scrollbar-hide">
                        <div class="flex gap-2">
                            <button v-for="cat in categories" :key="cat.id" @click="selectedCategory = cat.id" :class="selectedCategory === cat.id ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200'" class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition whitespace-nowrap">
                                <span>{{ cat.name }}</span>
                                <span :class="selectedCategory === cat.id ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600'" class="rounded-full px-1.5 py-0.5 text-[10px] font-bold">
                                    {{ cat.products?.length || 0 }}
                                </span>
                            </button>
                        </div>
                    </div>
                    <!-- Products Grid -->
                    <div class="flex-1 overflow-auto p-4">
                        <div v-if="!selectedCategory" class="flex h-full items-center justify-center text-gray-400">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <p class="text-sm">Pilih kategori untuk melihat produk</p>
                            </div>
                        </div>
                        <div v-else-if="filteredProducts.length === 0" class="flex h-full items-center justify-center text-gray-400">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-sm">Tidak ada produk di kategori ini</p>
                            </div>
                        </div>
                        <div v-else class="grid grid-cols-3 gap-3">
                            <button v-for="product in filteredProducts" :key="product.id" @click="addToCart(product)" :disabled="product.stock_quantity === 0" class="group relative rounded-xl border-2 p-4 text-left transition" :class="product.stock_quantity > 0 ? 'border-gray-200 bg-white hover:border-indigo-400 hover:shadow-lg' : 'border-gray-100 bg-gray-50 opacity-50 cursor-not-allowed'">
                                <div class="mb-2 flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-800 text-sm line-clamp-2 group-hover:text-indigo-600 transition">{{ product.name }}</div>
                                        <div class="mt-1 text-xs text-gray-400">{{ product.sku }}</div>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-end justify-between">
                                    <div class="text-lg font-bold text-indigo-600">{{ fmt(product.selling_price) }}</div>
                                    <div :class="product.stock_quantity > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500'" class="rounded-full px-2 py-0.5 text-xs font-medium">
                                        {{ product.stock_quantity }}
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Payment -->
            <div class="flex w-1/3 flex-col gap-4">
                <!-- Total -->
                <div class="rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-6 text-center shadow-lg shadow-indigo-200">
                    <div class="text-sm font-medium text-indigo-200 uppercase tracking-widest">Total Pembayaran</div>
                    <div class="mt-1 text-4xl font-extrabold text-white tracking-tight">{{ fmt(subtotal) }}</div>
                    <div class="mt-2 text-xs text-indigo-300">{{ cart.length }} produk · {{ cart.reduce((s,i)=>s+i.quantity,0) }} pcs</div>
                </div>
                <!-- Payment Method -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Metode Pembayaran</div>
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="selectedPaymentMethod = 'CASH'" :class="selectedPaymentMethod === 'CASH' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-2 ring-indigo-400' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'" class="flex flex-col items-center gap-2 rounded-xl border py-4 font-semibold transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 0 0-10 0v2M5 9h14l1 12H4L5 9z"/></svg>
                            <span class="text-sm">Tunai</span>
                        </button>
                        <button @click="selectedPaymentMethod = 'MIDTRANS'" :class="selectedPaymentMethod === 'MIDTRANS' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 ring-2 ring-indigo-400' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'" class="flex flex-col items-center gap-2 rounded-xl border py-4 font-semibold transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span class="text-sm">Non-Tunai</span>
                        </button>
                    </div>
                </div>
                <!-- Amount Paid -->
                <div v-if="selectedPaymentMethod !== 'MIDTRANS'" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Uang Diterima</div>
                    <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 focus-within:border-indigo-400 focus-within:bg-white transition">
                        <span class="text-sm font-medium text-gray-400">Rp</span>
                        <input v-model="amountPaid" id="amountPaid" type="number" class="w-full bg-transparent text-xl font-bold text-gray-800 outline-none" placeholder="0"/>
                    </div>
                    <div class="mt-2 grid grid-cols-4 gap-1.5">
                        <button v-for="a in quickAmounts" :key="a" @click="amountPaid = a" class="rounded-lg border border-gray-200 bg-white py-2 text-xs font-medium text-gray-600 transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">{{ fmt(a) }}</button>
                    </div>
                    <div v-if="amountPaid > 0" class="mt-3 rounded-lg px-4 py-3" :class="change >= 0 ? 'bg-emerald-50' : 'bg-red-50'">
                        <div class="text-xs font-medium" :class="change >= 0 ? 'text-emerald-600' : 'text-red-500'">Kembalian</div>
                        <div class="text-2xl font-extrabold" :class="change >= 0 ? 'text-emerald-600' : 'text-red-500'">{{ fmt(change) }}</div>
                    </div>
                </div>
                <!-- Pay Button -->
                <button @click="submitTransaction" :disabled="!canSubmit" class="flex w-full items-center justify-center gap-2 rounded-xl py-4 text-lg font-bold transition-all" :class="canSubmit ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 active:scale-[0.98]' : 'cursor-not-allowed bg-gray-100 text-gray-400'">
                    <CheckCircleIcon class="h-5 w-5" />
                    Proses Pembayaran
                </button>
            </div>
        </div>

        <!-- Receipt Modal -->
        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" leave-active-class="transition duration-150" leave-to-class="opacity-0">
            <div v-if="showReceipt" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0 scale-95" leave-to-class="opacity-0 scale-95">
                    <div v-if="showReceipt" class="w-full max-w-sm rounded-2xl bg-white shadow-2xl overflow-hidden print-receipt">
                        <!-- Logo & Store Info -->
                        <div class="px-6 pt-6 pb-3 text-center border-b border-dashed border-gray-300">
                            <img src="/icons/KasirKita.svg" alt="KasirKita Logo" class="h-16 mx-auto mb-2" />
                            <div class="text-2xl font-bold text-gray-800">KasirKita</div>
                            <div class="text-xs text-gray-500 mt-1">Jl. Contoh No. 123, Jakarta</div>
                            <div class="text-xs text-gray-500">Telp: 021-12345678</div>
                        </div>
                        <!-- Header -->
                        <div class="px-6 py-5 text-center print-header" style="background: white; border-bottom: 2px dashed #e5e7eb;">
                            <div class="text-lg font-bold text-gray-800">Transaksi Berhasil!</div>
                            <div class="mt-1 text-sm text-gray-600">{{ lastTransaction?.invoice_number }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ new Date().toLocaleString('id-ID') }}</div>
                        </div>
                        <!-- Items -->
                        <div class="px-6 py-4 print-items">
                            <div class="max-h-40 overflow-auto space-y-2 print-no-scroll">
                                <div v-for="item in lastTransaction?.details" :key="item.id" class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ item.product?.name || 'Produk' }} <span class="text-gray-400">×{{ item.quantity }}</span></span>
                                    <span class="font-medium text-gray-800">{{ fmt(item.subtotal) }}</span>
                                </div>
                            </div>
                            <div class="mt-4 space-y-2 border-t border-dashed border-gray-200 pt-4 text-sm print-total">
                                <div class="flex justify-between font-bold text-base text-gray-900">
                                    <span>Total</span><span>{{ fmt(Number(lastTransaction?.total_price) || 0) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Dibayar</span><span>{{ fmt(Number(lastTransaction?.total_paid) || 0) }}</span>
                                </div>
                                <div class="flex justify-between font-semibold text-emerald-600">
                                    <span>Kembalian</span><span>{{ fmt(Number(lastTransaction?.change) || 0) }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- Footer -->
                        <div class="px-6 py-3 text-center text-xs text-gray-500 print-footer">
                            <p class="font-semibold">Terima kasih atas kunjungan Anda</p>
                            <p class="mt-1">Barang yang sudah dibeli tidak dapat dikembalikan</p>
                            <p class="mt-2 text-gray-400">www.kasirkita.com</p>
                        </div>
                        <!-- Actions -->
                        <div class="flex gap-3 border-t border-gray-100 px-6 py-4 print-hide">
                            <button @click="printReceipt" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 flex items-center justify-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Cetak
                            </button>
                            <button @click="showReceipt = false" class="flex-1 rounded-xl bg-indigo-600 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 flex items-center justify-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Selesai
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </SidebarLayout>
</template>

<style>
/* Hide scrollbar for category tabs */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

@media print {
    /* Hide everything except receipt */
    body * {
        visibility: hidden;
    }

    /* Show only the receipt modal content */
    .print-receipt, .print-receipt * {
        visibility: visible;
    }

    .print-receipt {
        position: fixed !important;
        left: 50% !important;
        top: 0 !important;
        transform: translateX(-50%) !important;
        width: 80mm;
        max-width: 80mm;
        background: white;
        padding: 5mm;
        box-shadow: none !important;
        border-radius: 0 !important;
        margin: 0 auto;
        font-family: 'Courier New', monospace;
        font-size: 10pt;
        line-height: 1.3;
    }

    /* Remove modal overlay and animations */
    .fixed.inset-0 {
        position: static !important;
        background: white !important;
        backdrop-filter: none !important;
    }

    /* Hide buttons and success icon on print */
    .print-hide {
        display: none !important;
    }

    /* Hide the green success header background on print */
    .print-header {
        background: white !important;
        color: black !important;
        padding: 8px 0 !important;
        border-bottom: 2px dashed #000;
    }

    .print-header * {
        color: black !important;
    }

    .print-header .rounded-full {
        display: none !important;
    }

    /* Optimize text for thermal printer */
    @page {
        size: 80mm auto;
        margin: 0;
    }

    /* Items table */
    .print-items {
        border-bottom: 1px dashed #000;
        padding-bottom: 8px;
        margin-bottom: 8px;
    }

    /* Remove scrollbar and show all items on print */
    .print-no-scroll {
        max-height: none !important;
        overflow: visible !important;
    }

    /* Total section */
    .print-total {
        border-top: 2px solid #000 !important;
        padding-top: 8px;
        font-weight: bold;
        font-size: 13pt;
    }

    /* Footer */
    .print-footer {
        text-align: center;
        margin-top: 12px;
        padding-top: 8px;
        border-top: 1px dashed #000;
        font-size: 9pt;
    }

    /* Store info at top - ensure logo is visible */
    .print-receipt > div:first-child {
        border-bottom: 2px dashed #000;
        padding-bottom: 8px;
        margin-bottom: 8px;
    }

    .print-receipt > div:first-child img {
        display: block !important;
        visibility: visible !important;
        max-height: 40mm;
    }
}
</style>
