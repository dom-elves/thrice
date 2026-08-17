<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const page = usePage();

const user = page.props.auth.user;

const logout = () => {
    router.post('/logout')
}

onMounted(() => {
    console.log(user);
});

</script>



<template>
    <div>
        gz auth {{ user.name }}
    </div>
    <button
        @click="logout"
        class="button mx-2 p-2 border-1 rounded border-[#1b1b18] bg-transparent text-[#1b1b18] hover:bg-[#1b1b18] hover:text-white dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#EDEDEC] dark:hover:text-[#1b1b18]"
    >
        Log Out
    </button>
</template>
