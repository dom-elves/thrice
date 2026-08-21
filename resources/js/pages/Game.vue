<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoNotification } from '@laravel/echo-vue';
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

interface PageProps {
    [key: string]: unknown;
    game: {
        id: number;
        name: string;
        hands: number;
    };
    // inviteLink: {
    //     token: string;
    // }
}

const page = usePage<PageProps>();
const game = page.props.game;
// const inviteLink = page.props.inviteLink;

function playHand() {
    router.post('/play-hand', { game_id: game.id });
}

useEchoNotification(`App.Models.Game.${game.id}`, (notification) => {
    console.log('hit', notification);
});

onMounted(() => {});
</script>
<template>
    <AuthenticatedLayout>
        <p>i am the game {{ game.name }}</p>
        <p>we are on hand {{ game.hands }}</p>
        <button @click="playHand" class="border border-1 rounded m-4 p-4">play a hand</button>    
    </AuthenticatedLayout>
</template>
