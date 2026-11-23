<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoPresence } from '@laravel/echo-vue';
import { computed, onMounted } from 'vue';
import Board from '@/components/game/Board.vue';
import Hand from '@/components/game/Hand.vue';
import Seat from '@/components/game/Seat.vue';
import type { DealBoard, PageProps, Sit, Stand, Game } from '@/interfaces';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

const page = usePage<PageProps>();
const game = computed<Game>(() => page.props.game);
const gameUser = computed(() => page.props.gameUser);
const seats = computed(() => page.props.seats);
const seated = computed(() =>
    seats.value.some((user) => user.user_id == gameUser.value?.user_id),
);

useEchoPresence(
    `App.Models.Game.${game.value.id}`,
    ['.player.sit', '.player.stand', '.game.board'],
    (event: Sit | Stand | DealBoard) => {
        if ('message' in event) {
            router.reload({
                only: ['seats'],
            });
        }

        if ('board' in event) {
            router.reload({
                only: ['board', 'gameUser', 'gameUserCards', 'game'],
            });
        }
    },
);

function leaveGame() {
    router.post(`/game/${game.value.code}/leave`);
}

onMounted(() => {
    console.log('seats', seats.value);
});
</script>
<template>
    <AuthenticatedLayout>
        <h1 class="text-2xl">{{ game.name }}</h1>
        <div class="grid grid-cols-5">
            <div class="flex flex-col p-2">
                <Seat
                    v-for="(seat, position) in seats"
                    :key="position"
                    :seat="seat"
                    :position="position"
                />
                <button
                    v-if="!seated"
                    @click="leaveGame"
                    class="m-4 rounded border border-1 bg-red-300 p-4"
                >
                    leave game
                </button>
            </div>
            <div class="col-span-3 flex flex-col">
                <Board />
                <Hand v-if="seated && game.started" />
            </div>
            <div class="p-2">this is where messages/status etc will go</div>
        </div>
    </AuthenticatedLayout>
</template>
