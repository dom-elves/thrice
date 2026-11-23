<script setup lang="ts">
import { computed } from 'vue';
import Card from '@/components/game/Card.vue';

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    cards: {
        type: Object,
        required: true,
    },
});
</script>
<template>
    <div
        class="flex flex-row justify-center rounded p-2"
        :class="`row-${props.name}`"
    >
        <Card
            style="margin: 0px 5px"
            v-for="(card, index) in cards"
            :name="card"
            :invisible="['turn', 'river'].includes(index)"
            :key="index"
        />
    </div>
</template>
<style>
.hole {
    background-color: white;
    height: 140px;
    width: 100px;
    border-radius: var(--radius-lg);
    border: 4px solid green;
    margin: 0px 5px;
    display: flex;
    justify-content: center;
    align-items: center;
}

@media (width <= 384px) {
    .hole {
        height: 56px;
        width: 40px;
    }
}
</style>
