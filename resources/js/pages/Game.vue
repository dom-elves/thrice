<script setup lang="ts">
import { usePage, Form } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { useEchoNotification } from '@laravel/echo-vue';

interface PageProps {
    [key: string]: unknown;
    game: {
        id: number;
        name: string;
    };
    // inviteLink: {
    //     token: string;
    // }
}

const page = usePage<PageProps>();
const game = page.props.game;
// const inviteLink = page.props.inviteLink;

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
