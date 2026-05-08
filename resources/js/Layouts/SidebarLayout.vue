<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {
    ChartBarIcon,
    ChartPieIcon,
    CubeIcon,
    TagIcon,
    ClipboardDocumentListIcon,
    ShoppingCartIcon,
    ReceiptPercentIcon,
    MagnifyingGlassIcon,
    UserIcon,
    ChevronLeftIcon,
    ChevronDownIcon,
    UserCircleIcon,
    ArrowRightStartOnRectangleIcon,
} from '@heroicons/vue/24/outline';

const page = usePage();
const user = computed(() => page.props.auth.user);
const role = computed(() => user.value.role);
const sidebarOpen = ref(true);
const userMenuOpen = ref(false);
const openMenus = ref({});

const ownerMenus = [
    { label: 'Dashboard',   route: 'owner.dashboard',   icon: ChartBarIcon },
    {
        label: 'Laporan', icon: ChartPieIcon,
        children: [
            { label: 'Laporan Penjualan', route: 'owner.sales-report' },
            { label: 'Laporan Produk',    route: 'owner.product-report' },
            { label: 'Laporan Kasir',     route: 'owner.cashier-report' },
        ]
    },
    { label: 'Akun Pengguna', route: 'owner.cashiers', icon: UserCircleIcon },
    { label: 'Audit Trail', route: 'owner.audit-trail', icon: MagnifyingGlassIcon },
];

const inventoryMenus = [
    { label: 'Dashboard', route: 'inventory.dashboard',  icon: ChartBarIcon },
    { label: 'Produk',    route: 'inventory.products',   icon: CubeIcon },
    { label: 'Kategori',  route: 'inventory.categories', icon: TagIcon },
    {
        label: 'Stok', icon: ClipboardDocumentListIcon,
        children: [
            { label: 'Stok Masuk',   route: 'inventory.stock-in' },
            { label: 'Stok Keluar',  route: 'inventory.stock-out' },
            { label: 'Riwayat Stok', route: 'inventory.stock-logs' },
            { label: 'Stok Menipis', route: 'inventory.low-stock' },
        ]
    },
];

const kasirMenus = [
    { label: 'Kasir / POS',       route: 'kasir.pos',          icon: ShoppingCartIcon },
    { label: 'Riwayat Transaksi', route: 'kasir.transactions', icon: ReceiptPercentIcon },
];

const menus = computed(() => {
    if (role.value === 'owner')     return ownerMenus;
    if (role.value === 'inventory') return inventoryMenus;
    if (role.value === 'kasir')     return kasirMenus;
    return [];
});

const roleLabel = computed(() => {
    const labels = { owner: 'Owner', inventory: 'Inventory', kasir: 'Kasir' };
    return labels[role.value] ?? role.value;
});

const roleColor = computed(() => {
    const colors = {
        owner: 'bg-amber-500/20 text-amber-400',
        inventory: 'bg-emerald-500/20 text-emerald-400',
        kasir: 'bg-indigo-500/20 text-indigo-400',
    };
    return colors[role.value] ?? 'bg-gray-500/20 text-gray-400';
});

const userInitial = computed(() => user.value.name?.charAt(0).toUpperCase() ?? '?');

function handleToggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value;
    if (!sidebarOpen.value) openMenus.value = {};
}

function toggleSubmenu(label) {
    if (!sidebarOpen.value) return;
    openMenus.value[label] = !openMenus.value[label];
}

function isParentActive(menu) {
    return menu.children?.some(child => route().current(child.route));
}
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-60' : 'w-16'" class="relative flex flex-col bg-white border-r border-gray-200 transition-all duration-300 overflow-hidden flex-shrink-0">
            <!-- Logo -->
            <div class="flex h-16 items-center justify-between px-4 border-b border-gray-100">
                <div v-if="sidebarOpen" class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg">
                        <ApplicationLogo class="h-5 w-5 fill-white" />
                    </div>
                    <span class="text-[15px] font-bold tracking-wide text-gray-800">KasirKita</span>
                </div>
                <div v-else class="mx-auto flex h-8 w-8 items-center justify-center rounded-lg">
                    <ApplicationLogo class="h-5 w-5 fill-white" />
                </div>
                <button v-if="sidebarOpen" @click="handleToggleSidebar" class="flex h-7 w-7 items-center justify-center rounded-md text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                    <ChevronLeftIcon class="h-4 w-4" />
                </button>
            </div>
            <!-- User Info -->
            <div class="flex h-16 items-center border-b border-gray-100 px-3">
                <div v-if="sidebarOpen" class="flex w-full items-center gap-3 rounded-xl bg-gray-50 px-3 py-2">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">
                        {{ userInitial }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-[13px] font-semibold text-gray-800">{{ user.name }}</div>
                        <div class="text-[11px] text-gray-400">{{ roleLabel }}</div>
                    </div>
                </div>
                <div v-else class="flex w-full justify-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">
                        {{ userInitial }}
                    </div>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto px-2.5 py-3 space-y-0.5">
                <p v-if="sidebarOpen" class="mb-2 px-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400">
                    Menu Utama
                </p>
                <template v-for="menu in menus" :key="menu.label">
                    <Link v-if="!menu.children" :href="route(menu.route)" class="group relative flex items-center gap-3 rounded-lg text-[13px] font-medium transition-all duration-150" :class="[sidebarOpen ? 'px-3 py-2' : 'justify-center px-2 py-2', route().current(menu.route) ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800']">
                        <component :is="menu.icon" class="h-[18px] w-[18px] shrink-0" />
                        <span v-if="sidebarOpen" class="truncate">{{ menu.label }}</span>
                        <!-- Tooltip when collapsed -->
                        <div v-if="!sidebarOpen" class="pointer-events-none absolute left-full ml-3 hidden whitespace-nowrap rounded-md bg-gray-800 px-2.5 py-1.5 text-xs text-white shadow-lg group-hover:flex z-50">
                            {{ menu.label }}
                        </div>
                    </Link>
                    <!-- Submenu -->
                    <div v-else>
                        <button @click="toggleSubmenu(menu.label)" class="group relative flex w-full items-center gap-3 rounded-lg text-[13px] font-medium transition-all duration-150" :class="[sidebarOpen ? 'px-3 py-2' : 'justify-center px-2 py-2', isParentActive(menu) || openMenus[menu.label] ? 'bg-indigo-50 text-indigo-700' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800']">
                            <component :is="menu.icon" class="h-[18px] w-[18px] shrink-0" />
                            <span v-if="sidebarOpen" class="flex-1 truncate text-left">{{ menu.label }}</span>
                            <ChevronDownIcon v-if="sidebarOpen" class="h-3.5 w-3.5 shrink-0 text-gray-400 transition-transform duration-200" :class="openMenus[menu.label] ? 'rotate-180' : ''"/>
                        </button>
                        <div class="overflow-hidden transition-all duration-300" :class="openMenus[menu.label] && sidebarOpen ? 'max-h-60 opacity-100' : 'max-h-0 opacity-0'">
                            <div class="ml-3 mt-0.5 border-l border-gray-200 pl-3 space-y-0.5">
                                <Link v-for="child in menu.children" :key="child.route" :href="route(child.route)" class="flex items-center gap-2 rounded-md px-2 py-1.5 text-[12px] font-medium transition-all duration-150" :class="route().current(child.route) ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'">
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full" :class="route().current(child.route) ? 'bg-indigo-500' : 'bg-gray-300'"></span>
                                    {{ child.label }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </nav>

            <!-- Bottom: Toggle + Logout -->
            <div class="border-t border-gray-100 p-2.5 space-y-1">
                <button v-if="!sidebarOpen" @click="handleToggleSidebar" class="flex w-full items-center justify-center rounded-lg py-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                    <ChevronLeftIcon class="h-4 w-4 rotate-180" />
                </button>

                <!-- Profile -->
                <div class="relative">
                    <button @click="userMenuOpen = !userMenuOpen" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-[13px] text-gray-500 transition hover:bg-gray-100 hover:text-gray-800" :class="sidebarOpen ? '' : 'justify-center'">
                        <UserCircleIcon class="h-[18px] w-[18px] shrink-0" />
                        <span v-if="sidebarOpen" class="flex-1 text-left">Akun</span>
                        <ChevronDownIcon v-if="sidebarOpen" class="h-3.5 w-3.5 text-gray-400" />
                    </button>

                    <Transition enter-active-class="transition duration-150" enter-from-class="opacity-0 translate-y-1" leave-active-class="transition duration-100" leave-to-class="opacity-0 translate-y-1">
                        <div v-if="userMenuOpen" class="absolute bottom-full left-0 right-0 mb-2 z-50">
                            <div class="fixed inset-0 z-40" @click="userMenuOpen = false"></div>
                            <div class="relative z-50 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <div class="text-sm font-semibold text-gray-800">{{ user.name }}</div>
                                    <div class="text-xs text-gray-400">{{ user.email }}</div>
                                </div>
                                <Link :href="route('profile.edit')" @click="userMenuOpen = false" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 transition hover:bg-gray-50">
                                    <UserIcon class="h-4 w-4 text-gray-400" /> Profil
                                </Link>
                                <Link :href="route('logout')" method="post" as="button" @click="userMenuOpen = false" class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-600 transition hover:bg-red-50">
                                    <ArrowRightStartOnRectangleIcon class="h-4 w-4" /> Keluar
                                </Link>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex flex-1 flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-6 shadow-sm">
                <h1 class="text-base font-semibold text-gray-800">
                    <slot name="title">Dashboard</slot>
                </h1>
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">
                        {{ userInitial }}
                    </div>
                    <div class="text-sm">
                        <div class="font-medium text-gray-700 leading-tight">{{ user.name }}</div>
                        <div class="text-xs text-gray-400 leading-tight">{{ roleLabel }}</div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
