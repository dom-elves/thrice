<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { useEchoPresence } from '@laravel/echo-vue';
import { computed, onMounted } from 'vue';
import Board from '@/components/game/Board.vue';
import Hand from '@/components/game/Hand.vue';
import Seat from '@/components/game/Seat.vue';
import type { DealBoard, PageProps, Sit, Stand } from '@/interfaces';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

const page = usePage<PageProps>();
const game = page.props.game;
const seats = computed(() => page.props.seats);
// const board = computed(() => page.props.board);

useEchoPresence(
    `App.Models.Game.${game.id}`,
    ['.player.sit', '.player.stand', '.game.board'],
    (event: Sit | Stand | DealBoard) => {
        if ('message' in event) {
            router.reload({
                only: ['seats'],
            });
        }

        if ('board' in event) {
            router.reload({
                only: ['gameUser'],
            });
        }
    },
);

function leaveGame() {
    router.post(`/game/${game.code}/leave`);
}

onMounted(() => {
    console.log(page.props);
});
</script>
<template>
    <AuthenticatedLayout>
        <h1 class="text-2xl">{{ game.name }}</h1>
        <div class="flex flex-col">
            <!-- <div class="grid grid-cols-4 text-center">
                <div class="bg-blue-100 p-2">col1</div>
                <div class="col-span-2 bg-blue-100 p-2">col2</div>
                <div class="bg-blue-100 p-2">
                    <Board />
                </div>
            </div> -->
            <div class="flex flex-row">
                <div class="flex flex-col">
                    <Seat
                        v-for="(seat, position) in seats"
                        :key="position"
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
                <div class="flex flex-col">
                    <Board />
                    <Hand
                        v-if="page.props.gameUser && page.props.gameUser.in_game"    
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
