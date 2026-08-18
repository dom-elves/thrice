<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

onMounted(() =>{

});

</script>



<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] justify-center p-8 dark:bg-[#0a0a0a]"
    >
        <h1 class="text-8xl p-6">thrice</h1>
        <div>
            <Link href="/login" class="button mx-2 p-2 border-1 rounded border-[#1b1b18] bg-transparent text-[#1b1b18] hover:bg-[#1b1b18] hover:text-white dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#EDEDEC] dark:hover:text-[#1b1b18]">
                Log in
            </Link>
            <Link href="/register" class="button mx-2 p-2 border-1 rounded border-[#1b1b18] bg-transparent text-[#1b1b18] hover:bg-[#1b1b18] hover:text-white dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#EDEDEC] dark:hover:text-[#1b1b18]">
                Register
            </Link>
        </div>
    </div>
</template>
