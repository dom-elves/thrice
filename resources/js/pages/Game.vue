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
}

const page = usePage<PageProps>();
const game = page.props.game;

function playHand() {
    router.post('/play-hand', { game_id: game.id });
}

function leaveGame() {
    console.log(game.id);
    router.get(`/leave-game/${game.id}`);
}

useEchoNotification(`App.Models.Game.${game.id}`, (notification: any) => {
    console.log('hit', notification.gameUser);
});

onMounted(() => {
    console.log(page.props);
});
</script>
<template>
    <AuthenticatedLayout>
        <p>i am the game {{ game.name }}</p>
        <p>we are on hand {{ game.hands }}</p>
        <button @click="playHand" class="m-4 rounded border border-1 p-4">
            play a hand
        </button>

        <button @click="leaveGame" class="m-4 rounded border border-1 p-4">
            leave game
        </button>
    </AuthenticatedLayout>
</template>
