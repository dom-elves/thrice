<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue';

interface Game {
    id: number;
    name: string;
    started: boolean;
    hands: number;
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
        <table>
            <tr>
                <th>Name</th>
                <th>Players</th>
                <th>Started</th>
                <th>Hands</th>
            </tr>
            <tr v-for="game in games" :key="game.id">
                <td>{{ game.name }}</td>
                <td>0, for now</td>
                <td>{{ game.started }}</td>
                <td>{{ game.hands }}</td>
            </tr>
        </table>
    </div>
</template>
<style>
table, th, td {
  border:1px solid black;
}
</style>