<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoNotification } from '@laravel/echo-vue';
import { useEcho } from '@laravel/echo-vue';
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import Echo from 'laravel-echo';

interface GameUser {
    id: number;
    name: string;
    hand: unknown[];
}

interface HandDealt {
    [key: string]: unknown;
}

interface PageProps {
    [key: string]: unknown;
    game: {
        id: number;
        name: string;
        hands: number;
        code: string;
    };
    user: object;
    gameUser: GameUser;
}

const page = usePage<PageProps>();
const game = page.props.game;
const gameUser = page.props.gameUser;
const activePlayers = ref<GameUser[]>([]);

// function playHand() {
//     router.post('/play-hand', { game_id: game.id });
// }

useEchoNotification(`App.Models.Game.${game.id}`, (notification: any) => {
    console.log('g', notification);
    activePlayers.value.push(notification.gameUser.user.name);
});

useEcho(
    `App.Models.GameUser.${gameUser.id}`,
    '.hand.dealt',
    (event: HandDealt) => {
        console.log('hand dealt', event);
    },
);

function leaveGame() {
    router.get(`/game/${game.code}/leave`);
}

onMounted(() => {
    console.log('g', game);
});
</script>
<template>
    <AuthenticatedLayout>
        <div class="flex flex-col">
            <h1 class="text-2xl">{{ game.name }}</h1>
            <div class="grid grid-cols-4 text-center">
                <div class="bg-blue-100 p-2">col1</div>
                <div class="col-span-2 bg-blue-100 p-2">col2</div>
                <div class="bg-blue-100 p-2">
                    <p>active players</p>
                    <p
                        v-for="activePlayer in activePlayers"
                        :key="activePlayer.id"
                    >
                        {{ activePlayer }}
                    </p>
                </div>
            </div>
            <div>
                <button
                    @click="leaveGame"
                    class="m-4 rounded border border-1 bg-red-300 p-4"
                >
                    leave game
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
