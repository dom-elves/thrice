<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

const page = usePage();

const user = page.props.auth.user;

onMounted(() => {
    console.log(user);
});
</script>

<template>
    <AuthenticatedLayout>
        <div>gz auth {{ user.name }}</div>
    </AuthenticatedLayout>
</template>
