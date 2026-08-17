<script setup lang="ts">

import { usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const page = usePage();
const user = page.props.auth.user;

onMounted(() => {
    console.log('auth user', user);
})

</script>
<template>
    <main>
        <nav>navbar</nav>
        <slot />
    </main>
</template>