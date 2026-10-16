<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';

onMounted(() => {
    console.log('here', usePage().props.gameUser);
});
</script>
<template>
    <div></div>
</template>
