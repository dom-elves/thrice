<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const page = usePage();

onMounted(() => {
    console.log('page', page);
});
</script>
<template>
    <div>
        <nav>
            <ul>
                <li>guest layout</li>
            </ul>
        </nav>
        <main
            class="flex h-screen w-full flex-col items-center justify-center bg-red-100"
        >
            <slot />
        </main>
    </div>
</template>
