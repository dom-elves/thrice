<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Row from '@/components/game/Row.vue';
import type { PageProps } from '@/interfaces.ts';

const page = usePage<PageProps>();
const board = page.props.board;

onMounted(() => {
    console.log('brd', board);
});
</script>
<template>
    <div>
        <p>This is the board</p>
        <div v-for="(cards, row) in board" :key="row">
            <Row :name="row" :cards="cards" />
        </div>
    </div>
</template>
