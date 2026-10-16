<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue';
import type { Game } from '@/interfaces';

// as games is an optional prop, it only loads on component mount
const games = computed<Game[]>(
    () => (usePage().props.games as Game[] | undefined) ?? [],
);

function load() {
    router.reload({
        only: ['games'],
        onSuccess: (data) => {
            console.log(data);
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
}

onMounted(() => {
    load();
});
</script>

<template>
    <div>
        <div v-if="games.length >= 1">
            <table class="w-full">
                <tbody>
                    <tr>
                        <th>Name</th>
                        <th></th>
                        <th>Players</th>
                        <th>Started</th>
                        <th>Hands</th>
                    </tr>
                    <tr v-for="game in games" :key="game.id">
                        <td>
                            <a :href="`/game/${game.code}`">{{ game.name }}</a>
                        </td>
                        <td>
                            <svg
                                v-if="game.private"
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 100 100"
                                fill="none"
                                stroke="black"
                                stroke-width="7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    x="20"
                                    y="42"
                                    width="60"
                                    height="48"
                                    rx="5"
                                    fill="black"
                                />
                                <path d="M32 42 V30 A18 18 0 0 1 68 30 V42" />
                                <circle
                                    cx="50"
                                    cy="61"
                                    r="4"
                                    fill="white"
                                    stroke="none"
                                />
                                <path d="M50 65 V75" stroke="white" />
                            </svg>
                        </td>
                        <td>{{ game.game_users.length }}</td>
                        <td>{{ game.started }}</td>
                        <td>{{ game.hands }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p>No games to display!</p>
        </div>
    </div>
</template>
<style>
table,
th,
td {
    border: 1px solid black;
    padding: 2px;
}
</style>
