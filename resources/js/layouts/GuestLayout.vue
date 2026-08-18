<script setup lang="ts">

import { usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const page = usePage();

onMounted(() => {
    console.log('page', page);
})

</script>
<template>
    <div>
        <nav>
            <ul>
                <li>guest layout</li>
            </ul>
        </nav>
        <main class="flex flex-col items-center justify-center w-full h-screen bg-red-100">
            <slot />
        </main>
    </div>
</template>