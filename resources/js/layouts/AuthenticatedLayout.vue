<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const page = usePage();
const user = page.props.auth.user;

const logout = () => {
    router.post('/logout');
};

onMounted(() => {
    console.log('auth user', user);
});
</script>
<template>
    <div>
        <nav>
            <ul>
                <li>auth layout</li>
                <li><a href="/dashboard">dashboard</a></li>
                <li>
                    <button
                        @click="logout"
                        class="button mx-2 rounded border-1 border-[#1b1b18] bg-transparent p-2 text-[#1b1b18] hover:bg-[#1b1b18] hover:text-white dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#EDEDEC] dark:hover:text-[#1b1b18]"
                    >
                        Log Out
                    </button>
                </li>
            </ul>
        </nav>
        <main
            class="flex h-screen w-full flex-col items-center justify-center bg-red-100"
        >
            <slot />
        </main>
    </div>
</template>
