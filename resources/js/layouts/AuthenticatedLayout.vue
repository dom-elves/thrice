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
    <div>
        <nav>
            <ul>
                <li>auth layout</li>
                <li><a href="/dashboard">dashboard</a></li>
            </ul>
        </nav>
        <main class="flex flex-col items-center justify-center w-full h-screen bg-red-100">
            <slot />
        </main>
    </div>
</template>