<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue';
import type { PageProps } from '@/interfaces';

const page = usePage<PageProps>();
const game = page.props.game;
const gameUser = computed(() => page.props.gameUser);

onMounted(() => {
    console.log('here', page.props.gameUser);
});

// somehow use once props for these maybe
function sit() {
    router.post(`/game/${game.code}/sit`, {
        gameUserId: gameUser.value?.id ?? null,
        only: ['gameUser'],
    });
}

function stand() {
    router.post(`/game/${game.code}/stand`, {
        gameUserId: gameUser.value?.id ?? null,
        only: ['gameUser'],
    });
}
</script>
<template>
    <div>
        <button
            v-if="gameUser?.in_game"
            @click="stand"
            class="m-4 rounded border border-1 bg-blue-300 p-4"
        >
            stand up
        </button>
        <button
            v-else
            @click="sit"
            class="m-4 rounded border border-1 bg-green-300 p-4"
        >
            sit down
        </button>
    </div>
</template>
