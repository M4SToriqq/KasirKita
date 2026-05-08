<script setup>
    import SidebarLayout from '@/Layouts/SidebarLayout.vue';
    import DeleteUserForm from './Partials/DeleteUserForm.vue';
    import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
    import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
    import { Head } from '@inertiajs/vue3';

    defineProps({
        mustVerifyEmail: {
            type: Boolean,
        },
        status: {
            type: String,
        },
    });
</script>

<template>
    <Head title="Profile" />
    <SidebarLayout>
        <template #title>Profil Saya</template>
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <UpdatePasswordForm />
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <DeleteUserForm />
            </div>
        </div>
    </SidebarLayout>
</template>
