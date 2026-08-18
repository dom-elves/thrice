<script setup lang="ts">
import { usePage, Link } from '@inertiajs/vue3';
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
        <div class="m-4">gz auth {{ user.name }}</div>
        <div>
            <Link
                href="/create"
                class="button mx-2 rounded border-1 border-[#1b1b18] bg-transparent p-2 text-[#1b1b18] hover:bg-[#1b1b18] hover:text-white dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#EDEDEC] dark:hover:text-[#1b1b18]"
            >
                Create
            </Link>
            <Link
                href="/join"
                class="button mx-2 rounded border-1 border-[#1b1b18] bg-transparent p-2 text-[#1b1b18] hover:bg-[#1b1b18] hover:text-white dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#EDEDEC] dark:hover:text-[#1b1b18]"
            >
                Join
            </Link>
        </div>
    </AuthenticatedLayout>
</template>
