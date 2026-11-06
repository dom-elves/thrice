<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoPresence } from '@laravel/echo-vue';
import { computed, onMounted, ref } from 'vue';
import PlayerSeat from '@/components/game/PlayerSeat.vue';
import type { GameUser, PageProps, Sit, Stand } from '@/interfaces';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

const page = usePage<PageProps>();
const game = page.props.game;
const seats = computed(() => page.props.seats);
const activePlayers = ref<GameUser[]>([]);

useEchoPresence(
    `App.Models.Game.${game.id}`,
    ['.player.sit', '.player.stand'],
    (event: Sit | Stand) => {
        console.log('e', event);
        router.reload(
            {
                only: ['seats'],
            },
        );
        console.log('seats reloaded', seats.value);
    },
);


function leaveGame() {
    router.post(`/game/${game.code}/leave`);
}

onMounted(() => {
    console.log('seats', seats.value);
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
                <PlayerSeat
                    v-for="(seat, position) in seats"
                    :seat="seat"
                    :position="position"
                />
                <button
                    v-if="page.props.gameUser && !page.props.gameUser.in_game"
                    @click="leaveGame"
                    class="m-4 rounded border border-1 bg-red-300 p-4"
                >
                    leave game
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
