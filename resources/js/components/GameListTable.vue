<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue';

interface Game {
    id: number;
    name: string;
}

const games = computed<Game[]>(
    () => (usePage().props.games as Game[] | undefined) ?? [],
);

function load() {
    router.reload({ only: ['games'] });
}

onMounted(() => {
    console.log(games);
    console.log(load());
});
</script>

<template>
    <div>
        <p v-for="game in games" :key="game.id">{{ game.name }}</p>
    </div>
</template>
