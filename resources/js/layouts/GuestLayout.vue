<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const page = usePage();

onMounted(() => {
    console.log('page', page);
});
</script>
<template>
    <div class="flex flex-col min-h-screen">
        <nav>
            <ul>
                <li>guest layout</li>
            </ul>
        </nav>
        <main
            class="flex w-full flex-1 flex-col items-center justify-center bg-red-100"
        >
            <slot />
        </main>
    </div>
</template>
