<script setup lang="ts">
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


