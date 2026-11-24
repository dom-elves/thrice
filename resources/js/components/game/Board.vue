<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue';
import Row from '@/components/game/Row.vue';
import type { PageProps } from '@/interfaces.ts';

const page = usePage<PageProps>();
const board = computed(() => page.props.board);

onMounted(() => {
    console.log('brd', board);
});
</script>
<template>
    <div class="mb-4">
        <Row
            v-for="(cards, row) in board"
            :key="row"
            :name="row"
            :cards="cards"
        />
    </div>
</template>
