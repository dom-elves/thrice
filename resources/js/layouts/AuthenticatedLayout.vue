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
        <nav>
            <ul>
                <li>auth layout</li>
                <li><a href="/dashboard">dashboard</a></li>
            </ul>
        </nav>
        <slot />
    </main>
</template>