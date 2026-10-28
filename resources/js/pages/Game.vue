<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoPresence } from '@laravel/echo-vue';
import { onMounted, ref } from 'vue';
import PlayerSeat from '@/components/game/PlayerSeat.vue';
import type { GameUser, PageProps, Sit, Stand, User } from '@/interfaces';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

const page = usePage<PageProps>();
const game = page.props.game;
const activePlayers = ref<GameUser[]>([]);

const { channel } = useEchoPresence(
    `App.Models.Game.${game.id}`,
    ['.player.sit', '.player.stand'],
    (event: Sit | Stand) => {
        console.log('e', event);
    },
);

channel()
    .here(() => {
        console.log('anyone here?');
    })
    .joining((user: User) => {
        console.log('join', user);
    })
    .leaving((user: User) => {
        console.log('leave', user);
    })
    .error((error: unknown) => {
        console.error('e', error);
    });

function leaveGame() {
    router.post(`/game/${game.code}/leave`);
}

onMounted(() => {
    // console.log('g', game);
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
                <PlayerSeat />
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
