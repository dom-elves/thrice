<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoNotification } from '@laravel/echo-vue';
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

interface GameUser {
    id: number;
    name: string;
}

interface PageProps {
    [key: string]: unknown;
    game: {
        id: number;
        name: string;
        hands: number;
    };
    user: object;
}

const page = usePage<PageProps>();
const game = page.props.game;
const activePlayers = ref<GameUser[]>([]);
// this will need to eventually be bound to redis
const playerReady = ref(false);

// function playHand() {
//     router.post('/play-hand', { game_id: game.id });
// }

useEchoNotification(`App.Models.Game.${game.id}`, (notification: any) => {
    console.log('hit', notification);
    activePlayers.value.push(notification.gameUser.user.name);

    setTimeout( () => {
        router.get('/dashboard')
    }, 3000);
});

function ready() {
    try {
        router.post(`/game/${game.id}/ready`);
    } catch (error) {
        console.log(error);
    } finally {
        playerReady.value = true;
    }
}

function leaveGame() {
    console.log(game.id);
    router.get(`/leave-game/${game.id}`);
}

onMounted(() => {
    console.log(page.props);
});
</script>
<template>
    <AuthenticatedLayout>
        <div class="flex flex-col">
            <h1 class="text-2xl">{{ game.name }}</h1>
            <div class="grid grid-cols-4 text-center">
                <div class="p-2 bg-blue-100">col1</div>
                <div class="p-2 bg-blue-100 col-span-2">col2</div>
                <div class="p-2 bg-blue-100">
                    <p>active players</p>
                    <p v-for="activePlayer in activePlayers" :key="activePlayer">
                        {{ activePlayer }}
                    </p>
                </div>
            </div>
            <div>
                <button
                    @click="ready"
                    class="m-4 rounded border border-1 p-4 bg-green-100"
                    :disabled="playerReady"
                >
                    start game
                </button>
                <button @click="leaveGame" class="m-4 rounded border border-1 p-4 bg-red-300">
                    leave game
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
