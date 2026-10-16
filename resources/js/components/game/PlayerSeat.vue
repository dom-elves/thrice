<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import type { GameUser, PageProps } from '@/interfaces';

const page = usePage<PageProps>();
const game = page.props.game;
const gameUser = page.props.gameUser;

onMounted(() => {
    console.log('here', usePage().props.gameUser);
});

function sit() {
    router.post(`/game/${game.code}/sit`);
}

function stand() {
    router.post(`/game/${game.code}/stand`);
}

</script>
<template>
    <div>
        <button
            v-if="gameUser.in_game"
            @click="sit"
            class="m-4 rounded border border-1 bg-green-300 p-4"
        >
            sit down
        </button>
        <button
            v-if="!gameUser.in_game"
            @click="stand"
            class="m-4 rounded border border-1 bg-blue-300 p-4"
        >
            stand up
        </button>
    </div>
</template>
