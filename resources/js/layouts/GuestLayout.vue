<script setup lang="ts">

import { onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

onMounted(() => {
    console.log('page', page);
})

</script>
<template>
    <main>
        <nav>
            <ul>
                <li>guest layout</li>
                <li><a href="/forgot-password">forgot password</a></li>
            </ul>
        </nav>
        <slot />
    </main>
</template>