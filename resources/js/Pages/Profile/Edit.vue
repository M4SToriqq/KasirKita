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

        <div class="mx-auto max-w-3xl space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" class="max-w-xl" />
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-8">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </SidebarLayout>
</template>
