<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { useEchoNotification } from '@laravel/echo-vue';
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

interface PageProps {
    [key: string]: unknown;
    game: {
        id: number;
        name: string;
    };
}

const page = usePage<PageProps>();
const game = page.props.game;

console.log({
    host: import.meta.env.VITE_REVERB_HOST,
    port: import.meta.env.VITE_REVERB_PORT,
    scheme: import.meta.env.VITE_REVERB_SCHEME,
});

useEchoNotification(`App.Models.Game.${game.id}`, (notification) => {
    console.log('hit', notification);
});

onMounted(() => {});
</script>
<template>
    <AuthenticatedLayout> i am the game {{ game.name }} </AuthenticatedLayout>
</template>
